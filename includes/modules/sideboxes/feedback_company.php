<?php
/**
 * Feedback Company review sidebox
 *
 * @package templateSystem
 * @copyright Copyright 2018 david-fairbanks42 <david@makerdave.com>
 * @copyright Portions Copyright Feedback Company
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if(defined('FEEDBACK_COMPANY_WIDGET_UUID') && FEEDBACK_COMPANY_WIDGET_UUID != '') :
?>

<div style="width:200px;margin-left:auto;margin-right:auto;">
<!-- Feedback Company Widget (start) -->
<script type="text/javascript" id="__fbcw__<?php echo FEEDBACK_COMPANY_WIDGET_UUID ?>">
    "use strict";!function(){
        window.FeedbackCompanyWidgets=window.FeedbackCompanyWidgets||{queue:[],loaders:[
            ]};var options={uuid:"<?php echo FEEDBACK_COMPANY_WIDGET_UUID ?>",version:"1.2.1",prefix:""};if(
            void 0===window.FeedbackCompanyWidget){if(
            window.FeedbackCompanyWidgets.queue.push(options),!document.getElementById(
                "__fbcw_FeedbackCompanyWidget")){var scriptTag=document.createElement("script")
        ;scriptTag.onload=function(){if(window.FeedbackCompanyWidget)for(
            ;0<window.FeedbackCompanyWidgets.queue.length;
        )options=window.FeedbackCompanyWidgets.queue.pop(),
                window.FeedbackCompanyWidgets.loaders.push(
                    new window.FeedbackCompanyWidgetLoader(options))},
            scriptTag.id="__fbcw_FeedbackCompanyWidget",
            scriptTag.src="https://www.feedbackcompany.com/includes/widgets/feedback-company-widget.min.js"
            ,document.body.appendChild(scriptTag)}
        }else window.FeedbackCompanyWidgets.loaders.push(
                new window.FeedbackCompanyWidgetLoader(options))}();
</script>
<!-- Feedback Company Widget (end) -->
</div>

<?php endif; ?>
