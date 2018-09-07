Zen Cart Feedback Company Plug-in
=================================
By David Fairbanks
Version 1.0

This plug-in sends API requests to Feedback Company to invite customers to fill out reviews.

Files included with this Plugin:
================================
    /includes/auto_loaders/config.feedback_company_invite.php
    /includes/classes/observers/class.feedback_company_invite.php
    /includes/classes/FeedbackCompany.php
    /YOUR_ADMIN/includes/auto_loaders/config.feedback_company.php
    /YOUR_ADMIN/includes/extra_datafiles/feedback_company_definitions.php
    /YOUR_ADMIN/includes/init_includes/init_feedback_company.php
    /README.md - this file

Installation:
=============
Upload all included files (except the README.txt) to the required directories on your server.
Be sure not to upload the folders as this will most likely overwrite the contents of the
directories on you server. The 'YOUR_ADMIN' folder is the name of your admin directory on
your server.

After the files are uploaded, log into your Zen Cart admin panel. This will add the appropriate
configuration settings enabling you to enter the authentication keys.
