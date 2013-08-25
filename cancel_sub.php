<?php

/*******************************************\
|            StripeTaker v1.02              |
|         [ Open Source Version ]           |
|     Released under the MIT License.       |
|   See LICENSE.TXT to view the license.    |
|                                           |
|  Copyright © 2012-2013 NightKingdoms LLC  |
|     http://support.nightkingdoms.com      |
|        helpdesk@nightkingdoms.com         |
|                                           |
\*******************************************/

   include("core/init.php");

if (isset($_REQUEST['id'])) {

   $cust_stripe = Forms_FormatData($_REQUEST['id']);
   $custinfo = DataOps_Data_Get(STAKER_CUSTS, $cust_stripe);
   $custinfo['ip'] = $_SERVER['REMOTE_ADDR'];
   $custinfo['agent'] = $_SERVER['HTTP_USER_AGENT'];

   if (count($custinfo) > 0) {

      $stripe_info = StripeInterface_Plan_Cancel($cust_stripe);

      if ($stripe_info['status'] != "fail") {

         include("headers.inc.php");

         ?><div class="alert alert-success"><i class="icon-ok-sign icon-large"></i> Subscription cancelled!</div><p />

         <p>Your subscription has been successfully cancelled.  You should receive a confirmation message at <b><?php echo $custinfo['email']; ?></b> shortly.</p>

         <p>Thanks for doing business with us! Hope to see you again soon!</p><?php

         include("footers.inc.php");

         Templater_CancelSub("cust", $custinfo);
         Templater_CancelSub("admin", $custinfo);

      } else {

         include("headers.inc.php");

         ?><h3>We're Sorry</h3>
         <p>We were not able to automatically cancel the subscription associated with #<?php echo $cust_stripe; ?>.</p>

         <p>Please try again later (it might've been a hiccup) or contact us at <?php echo $StripeTaker_SaveFile_Data['notify_cust']; ?> to have it manually cancelled.</p>

         <p>Thank you!</p><?php

         include("footers.inc.php");

      }

   } else {

      include("headers.inc.php");

      ?><h3>We're Sorry</h3>
      <p>We were not able to find the subscription associated with #<?php echo $cust_stripe; ?> to cancel.</p>

      <p>If you believe this is an error, please contact us at <?php echo $StripeTaker_SaveFile_Data['notify_cust']; ?> to have it manually cancelled.</p>

      <p>Thank you!</p><?php

      include("footers.inc.php");

   }

} else {

   die("Invalid request.");

}

?>
