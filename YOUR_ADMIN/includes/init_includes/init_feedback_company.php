<?php
// location: /__ADMIN__/includes/init_includes/init_feedback_company.php

/**
 * Create the configuration settings
 *
 * @author David Fairbanks <david@makerdave.com>
 * @copyright (c) 2018, Fairbanks Publishing, LLC
 * @version 1.0
 */
$fbc_group_name = 'Feedback Company Review Invite';

// Configuration Values to create or preserve
$fbc_menu_items = array(
    array(
        'key'         => 'FEEDBACK_COMPANY_INVITE_ENABLE',
        'default'     => 'true',
        'title'       => 'Enable Review Invite',
        'description' => 'With the invite disabled, no order information will be sent to Feedback Company to invite customers to submit reviews.',
        'use'         => null,
        'set'         => "zen_cfg_select_option(array('true', 'false'),"
    ),
    array(
        'key'         => 'FEEDBACK_COMPANY_CLIENT_ID',
        'default'     => '',
        'title'       => 'Feedback Company API Client ID',
        'description' => 'The Oauth Client ID provided by Freeback Company',
        'use'         => null,
        'set'         => null
    ),
    array(
        'key'         => 'FEEDBACK_COMPANY_CLIENT_SECRET',
        'default'     => '',
        'title'       => 'Feedback Company API Secret',
        'description' => 'The Oauth Secret ID provided by Freeback Company',
        'use'         => 'zen_cfg_password_display',
        'set'         => null
    ),
    array(
        'key'         => 'FEEDBACK_COMPANY_ACCESS_TOKEN',
        'default'     => '',
        'title'       => 'Feedback Company Access Token',
        'description' => 'The Oauth access token obtained and refreshed automatically',
        'use'         => 'zen_cfg_password_display',
        'set'         => null
    ),
    array(
        'key'         => 'FEEDBACK_COMPANY_TOKEN_EXPIRE',
        'default'     => '',
        'title'       => 'Feedback Company Access Expireation Date',
        'description' => 'Epoch date when the access token will expire next',
        'use'         => null,
        'set'         => null
    ),
    array(
        'key'         => 'FEEDBACK_COMPANY_DELAY_DAYS',
        'default'     => '3',
        'title'       => 'Feedback Company Delay Days',
        'description' => 'Number of days before review invitation will be sent',
        'use'         => null,
        'set'         => null
    ),
    array(
        'key'         => 'FEEDBACK_COMPANY_REMIND_DAYS',
        'default'     => '0',
        'title'       => 'Feedback Company Reminder Days',
        'description' => 'Number of days before invite reminder is sent. "0" will disable the reminder altogether.',
        'use'         => null,
        'set'         => null
    )
);

if(!defined('FEEDBACK_COMPANY_CLIENT_ID')) {
    /* Find max sort order in the configuration group table -- add 2 to this value to create the configuration group ID */
    $sql = "SELECT (MAX(sort_order) + 1) as sort FROM ".TABLE_CONFIGURATION_GROUP;
    $result = $db->Execute($sql);
    $sort = $result->fields['sort'];

    /* Create configuration group */
    $sql = "INSERT INTO ".TABLE_CONFIGURATION_GROUP." (configuration_group_id, configuration_group_title, configuration_group_description, sort_order, visible) VALUES (NULL, '$fbc_group_name', 'Feedback Company Configuration', $sort, '1')";
    $db->Execute($sql);

    /* Find configuration group ID */
    $sql = "SELECT configuration_group_id FROM ".TABLE_CONFIGURATION_GROUP." WHERE configuration_group_title='$fbc_group_name' LIMIT 1";
    $result = $db->Execute($sql);
    $fbc_config_group_id = $result->fields['configuration_group_id'];

    /* insert configuration settings */
    foreach($fbc_menu_items as $fbc_sort => $item) {
        $uf = ($item['use'] === null) ? 'NULL' : "'".str_replace("'", "\\'", $item['use'])."'";
        $sf = ($item['set'] === null) ? 'NULL' : "'".str_replace("'", "\\'", $item['set'])."'";
        $sql = "INSERT INTO ".TABLE_CONFIGURATION." (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES
('{$item['title']}', '{$item['key']}', '{$item['default']}', '{$item['description']}', '$fbc_config_group_id', $fbc_sort, NULL, now(), $uf, $sf)";
        $db->Execute($sql);
    }

    // find next sort order in admin_pages table
    $sql = "SELECT (MAX(sort_order) + 1) as sort FROM ".TABLE_ADMIN_PAGES;
    $result = $db->Execute($sql);
    $admin_page_sort = $result->fields['sort'];

    zen_register_admin_page('configFbc',
                            'BOX_CONFIGURATION_FEEDBACK_COMPANY', 'FILENAME_CONFIGURATION',
                            'gID=' . $fbc_config_group_id, 'configuration', 'Y',
                            $admin_page_sort);
}
