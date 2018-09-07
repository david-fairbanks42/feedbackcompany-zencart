<?php
// location: /includes/classes/observers/class.feedback_company_invite.php

/**
 * Class FeedbackCompanyInvite
 *
 * @author David Fairbanks <david@makerdave.com>
 * @copyright (c) 2018, Fairbanks Publishing, LLC
 * @version 1.0
 */
class FeedbackCompanyInvite extends base {

    public function __construct() {
        $this->attach($this, array('NOTIFY_CHECKOUT_PROCESS_AFTER_SEND_ORDER_EMAIL'));
    }

    /**
     * @param object $class The calling class to provide access to it's variables (notifier)
     * @param int $eventId The name of the event that triggered the update
     *      It is quite possible to observe more than one notifier
     * @param array $paramsArray Seems to always be empty
     */
    public function update(&$class, $eventId, $paramsArray) {
        if(!defined('FEEDBACK_COMPANY_INVITE_ENABLE') || FEEDBACK_COMPANY_INVITE_ENABLE == 'false') {
            error_log('FeedbackCompany invite is disabled');
            return;
        }

        if(!defined('FEEDBACK_COMPANY_ACCESS_TOKEN')) {
            error_log('FeedbackCompany invite is not setup');
            return;
        }

        global $order;

        require_once(__DIR__ . '/../FeedbackCompany.php');
        $fbc = new FeedbackCompany();

        if($fbc->refreshAccessToken() !== true)
            return;

        $orderId = (isset($_SESSION['order_number_created']) && is_numeric($_SESSION['order_number_created']))
            ? (int)$_SESSION['order_number_created']
            : ((isset($order->info['orders_id'])) ? (int)$order->info['orders_id'] : 0);

        $email = (isset($order->customer['email_address'])) ? $order->customer['email_address'] : null;

        $name = (isset($order->delivery['name']))
            ? $order->delivery['name']
            : trim($order->delivery['firstname'] . ' ' . $order->delivery['lastname']);

        if($orderId > 0 && $email !== null)
            $fbc->inviteReview($orderId, $email, $name);
    }
}
