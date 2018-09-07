<?php
// location: /includes/classes/FeedbackCompany.php

/**
 * Class FeedbackCompany
 *
 * @author David Fairbanks <david@makerdave.com>
 * @copyright (c) 2018, Fairbanks Publishing, LLC
 * @version 1.0
 */
class FeedbackCompany
{
    /**
     * Temporary storage for new access token
     * @var null|string
     */
    private $accessToken = null;

    /**
     * Ensure the auth token is up to date
     * @return boolean
     */
    public function refreshAccessToken()
    {
        if(defined('FEEDBACK_COMPANY_TOKEN_EXPIRE') && FEEDBACK_COMPANY_TOKEN_EXPIRE > time() + 86400) {
            return true;
        }

        if(!defined('FEEDBACK_COMPANY_CLIENT_ID') || !defined('FEEDBACK_COMPANY_CLIENT_SECRET')
            || FEEDBACK_COMPANY_CLIENT_ID == '' || FEEDBACK_COMPANY_CLIENT_SECRET == ''
        ) {
            error_log('FeedbackCompany client secrets are not set');
            return false;
        }

        global $db;

        // GET https://www.feedbackcompany.com/api/v2/oauth2/token
        $params = [
            'client_id' => FEEDBACK_COMPANY_CLIENT_ID,
            'client_secret' => FEEDBACK_COMPANY_CLIENT_SECRET,
            'grant_type' => 'authorization_code'
        ];

        try {
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_URL            => 'https://www.feedbackcompany.com/api/v2/oauth2/token?' . http_build_query($params)
            ]);

            $result = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($result);
        } catch(Exception $e) {
            error_log('Curl exception requesting FeedbackCompany auth token: ' . $e->getMessage());
            return false;
        }

        if(!is_object($data) || !isset($data->error) || !isset($data->access_token)) {
            error_log('Unexpected response getting FeedbackCompany auth token: ' . $result);
            return false;
        }

        if($data->error != false) {
            error_log('Response error getting FeedbackCompany auth token: ' . $result);
            return false;
        }

        $this->accessToken = $data->access_token;
        $expire = DateTime::createFromFormat('M, d Y H:i:s T', $data->expires_on)->getTimestamp();

        //error_log("Updating FeedbackCompany auth token to {$data->access_token} {$data->expires_on} {$expire}");

        try {
            $db->Execute('UPDATE ' . TABLE_CONFIGURATION . " SET `configuration_value` = '{$data->access_token}', `last_modified` = NOW() WHERE `configuration_key` = 'FEEDBACK_COMPANY_ACCESS_TOKEN'");
            $db->Execute('UPDATE ' . TABLE_CONFIGURATION . " SET `configuration_value` = '{$expire}', `last_modified` = NOW() WHERE `configuration_key` = 'FEEDBACK_COMPANY_TOKEN_EXPIRE'");
        } catch(Exception $e) {
            error_log('Database exception updating FeedbackCompany auth token: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Send review invite request to Feedback Company
     *
     * @param int $orderId
     * @param string $email
     * @param string $name
     *
     * @return boolean
     */
    public function inviteReview($orderId, $email, $name='')
    {
        if($orderId === null || !is_numeric($orderId) || !is_string($email) || $email == '') {
            error_log('Invalid parameters for FeedbackCompany::inviteReview');
            return false;
        }

        if($this->accessToken !== null) {
            // continue
        } elseif(!defined('FEEDBACK_COMPANY_ACCESS_TOKEN') || FEEDBACK_COMPANY_ACCESS_TOKEN == '') {
            error_log('FeedbackCompany auth token is not set');
            return false;
        }

        defined('FEEDBACK_COMPANY_DELAY_DAYS') || define('FEEDBACK_COMPANY_DELAY_DAYS', 3);
        defined('FEEDBACK_COMPANY_REMIND_DAYS') || define('FEEDBACK_COMPANY_REMIND_DAYS', 0);

        // POST https://www.feedbackcompany.com/api/v2/orders
        $data = [
            'external_id' => (string)$orderId,
            'customer'    => [
                'email' => $email,
                'name'  => $name
            ],
            'invitation'  => [
                'delay' => ['unit' => 'days', 'amount' => FEEDBACK_COMPANY_DELAY_DAYS]
            ]
        ];

        if(FEEDBACK_COMPANY_REMIND_DAYS > 0) {
            $data['invitation']['reminder'] = ['unit' => 'days', 'amount' => FEEDBACK_COMPANY_REMIND_DAYS];
        }

        $accessToken = ($this->accessToken !== null) ? $this->accessToken : FEEDBACK_COMPANY_ACCESS_TOKEN;

        try {
            $ch = curl_init();
            $json = json_encode($data);
            $jsonLen = strlen($json);

            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_URL            => 'https://www.feedbackcompany.com/api/v2/orders',
                CURLOPT_HTTPHEADER     => [
                    "Authorization: Bearer {$accessToken}",
                    "Content-Type: application/json",
                    "Content-Length: {$jsonLen}"
                ],
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $json
            ));

            // log errors
            /*if(defined('DIR_FS_LOGS')) {
                $logFile = DIR_FS_LOGS . DIRECTORY_SEPARATOR . 'feedback_company.log';
                $fhFlag = false;
                $logFh = @fopen($logFile, 'a');
                if($logFh === false) {
                    @file_put_contents($logFile, '');
                    if($t1 !== false) {
                        $t2 = @chmod($logFile, octdec('0666'));
                        if($t2 === false) {
                            $fhFlag = true;
                        }
                    }
                } else {
                    $fhFlag = true;
                    file_put_contents($logFile, "-------------------------------\n$json\n", FILE_APPEND | LOCK_EX);
                }
                if($fhFlag == true) {
                    curl_setopt($ch, CURLOPT_VERBOSE, true);
                    curl_setopt($ch, CURLOPT_STDERR, $logFh);
                }
            }*/

            $result = curl_exec($ch);

            if (!curl_errno($ch)) {
                $info = curl_getinfo($ch);
                error_log('FeedbackCompany response code of ' . $info['http_code'] . ' for ' . $json);
            } else {
                error_log('Curl error sending FeedbackCompany invite: ' . curl_error($ch));
            }

            curl_close($ch);

            if(isset($logFile))
                file_put_contents($logFile, $result . "\n", FILE_APPEND | LOCK_EX);

        } catch(Exception $e) {
            error_log('Curl exception sending FeedbackCompany invite: ' . $e->getMessage());
            return false;
        }

        return true;
    }
}
