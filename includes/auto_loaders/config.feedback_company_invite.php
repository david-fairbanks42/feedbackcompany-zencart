<?php
// location: /includes/auto_loaders/config.feedback_company_invite.php

// setup the order web-hook observer
$autoLoadConfig[190][] = array('autoType'=>'class',
                          'loadFile'=>'observers/class.feedback_company_invite.php');
$autoLoadConfig[190][] = array('autoType'=>'classInstantiate',
                          'className'=>'FeedbackCompanyInvite',
                          'objectName'=>'FeedbackCompanyInvite');
