<?php

/*******************************************\
|               StripeTaker                 |
|         [ Open Source Version ]           |
|     Released under the MIT License.       |
|   See LICENSE.TXT to view the license.    |
|                                           |
|  Copyright © 2012-2013 NightKingdoms LLC  |
|     http://support.nightkingdoms.com      |
|        helpdesk@nightkingdoms.com         |
|                                           |
\*******************************************/

   if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") { $tplsite_protocol = "https"; } else { $tplsite_protocol = "http"; }
   $store_url = $tplsite_protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/";


function Templater_Refund($who, $vars) {
   global $StripeTaker_SaveFile_Data, $store_url;

   $order_vars = $vars;
   $order_vars['id'] = $vars['order_id'];

   $prep_email = "";

   $prep_email .= Templater_FilterReturn($order_vars, $vars, $who . "_refund");

   // send notification to recipient;
   if ($who == "cust") {

      $mail_headers = "From: \"" . $StripeTaker_SaveFile_Data['storename'] . "\" <" . $StripeTaker_SaveFile_Data['notify_cust'] . ">\r\n";
      $mail_headers .= "To: \"" . $vars['name'] . "\" <" . $vars['email'] . ">\r\n";
      $mail_headers .= "X-Mailer: NK StripeTaker v1.01 <helpdesk@nightkingdoms.com>\r\n";

      // send mail;
      mail($vars['email'], "Refund Confirmation - Order #" . $vars['order_id'], $prep_email, $mail_headers, "-f " . $StripeTaker_SaveFile_Data['notify_cust']);

   } else { // admin;

      $mail_headers = "From: \"" . $StripeTaker_SaveFile_Data['storename'] . "\" <" . $StripeTaker_SaveFile_Data['notify_cust'] . ">\r\n";
      $mail_headers .= "To: \"" . $StripeTaker_SaveFile_Data['storename'] . " Administrator\" <" . $StripeTaker_SaveFile_Data['notify_admin'] . ">\r\n";
      $mail_headers .= "X-Mailer: NK StripeTaker v1.01 <helpdesk@nightkingdoms.com\r\n";

      // send email;
      mail($StripeTaker_SaveFile_Data['notify_admin'], "Refund Issued - Order #" . $vars['order_id'], $prep_email, $mail_headers, "-f " . $StripeTaker_SaveFile_Data['notify_cust']);

   }

}


function Templater_CancelSub($who, $custinfo) {
   global $StripeTaker_SaveFile_Data, $store_url;

   $prep_email = "";

   $prep_email .= Templater_FilterReturn($custinfo, $custinfo, $who . "_cancel");

   // send notification to recipient;
   if ($who == "cust") {

      $mail_headers = "From: \"" . $StripeTaker_SaveFile_Data['storename'] . "\" <" . $StripeTaker_SaveFile_Data['notify_cust'] . ">\r\n";
      $mail_headers .= "To: \"" . $custinfo['name'] . "\" <" . $custinfo['email'] . ">\r\n";
      $mail_headers .= "X-Mailer: NK StripeTaker v1.01 <helpdesk@nightkingdoms.com>\r\n";

      // send mail;
      mail($custinfo['email'], "Confirmation - Subscription Cancelled" . $vars['order_id'], $prep_email, $mail_headers, "-f " . $StripeTaker_SaveFile_Data['notify_cust']);

   } else { // admin;

      $mail_headers = "From: \"" . $StripeTaker_SaveFile_Data['storename'] . "\" <" . $StripeTaker_SaveFile_Data['notify_cust'] . ">\r\n";
      $mail_headers .= "To: \"" . $StripeTaker_SaveFile_Data['storename'] . " Administrator\" <" . $StripeTaker_SaveFile_Data['notify_admin'] . ">\r\n";
      $mail_headers .= "X-Mailer: NK StripeTaker v1.01 <helpdesk@nightkingdoms.com\r\n";

      // send email;
      mail($StripeTaker_SaveFile_Data['notify_admin'], "Subscription Cancelled", $prep_email, $mail_headers, "-f " . $StripeTaker_SaveFile_Data['notify_cust']);

   }

}


function Templater_Notify($who, $orderinfo, $custinfo, $forminfo) {
   global $StripeTaker_SaveFile_Data, $store_url;

   $prep_email = "";

   $prep_email = Templater_FilterReturn($orderinfo, $custinfo, "header") . "\n\n"; // include header;

   if ($forminfo['chargeType'] == "One-Time") { // add payment information - one time payment;

      $prep_email .= Templater_FilterReturn($orderinfo, $custinfo, $who . "_pay_one") . "\n\n";

   } else { // subscription payment;

      $prep_email .= Templater_FilterReturn($orderinfo, $custinfo, $who . "_pay_sub") . "\n\n";

      if ($who == "cust") {

         $prep_email .= "You can cancel your subscription at any time by using the address below:\n\n" . $store_url . "cancel_sub.php?id=" . $custinfo['stripe'] . "\n\n";

      } else {

         $prep_email .= "Provide this URL to your customer to cancel their subscription:\n\n" . $store_url . "cancel_sub.php?id=" . $custinfo['stripe'] . "\n\n";

      }

   }

   if ($forminfo['ships']) { // show shipment informaton;

      $prep_email .= Templater_FilterReturn($orderinfo, $custinfo, $who . "_ship") . "\n\n";

   }

   if (mb_strlen($forminfo['url_download']) > 5) { // show download information;

      $prep_email .= Templater_FilterReturn($orderinfo, $custinfo, $who . "_dl") . "\n\n";

   }

   $prep_email .= Templater_FilterReturn($orderinfo, $custinfo, "footer"); // include footer;


   // send notification to recipient;
   if ($who == "cust") {

      $mail_headers = "From: \"" . $StripeTaker_SaveFile_Data['storename'] . "\" <" . $StripeTaker_SaveFile_Data['notify_cust'] . ">\r\n";
      $mail_headers .= "To: \"" . $custinfo['name'] . "\" <" . $custinfo['email'] . ">\r\n";
      $mail_headers .= "X-Mailer: NK StripeTaker v1.01 <helpdesk@nightkingdoms.com>\r\n";

      // send mail;
      mail($custinfo['email'], "Your Order #" . $orderinfo['id'], $prep_email, $mail_headers, "-f " . $StripeTaker_SaveFile_Data['notify_cust']);

   } else { // admin;

      $mail_headers = "From: \"" . $StripeTaker_SaveFile_Data['storename'] . "\" <" . $StripeTaker_SaveFile_Data['notify_cust'] . ">\r\n";
      $mail_headers .= "To: \"" . $StripeTaker_SaveFile_Data['storename'] . " Administrator\" <" . $StripeTaker_SaveFile_Data['notify_admin'] . ">\r\n";
      $mail_headers .= "X-Mailer: NK StripeTaker v1.01 <helpdesk@nightkingdoms.com\r\n";

      // send email;
      mail($StripeTaker_SaveFile_Data['notify_admin'], "New Order #" . $orderinfo['id'], $prep_email, $mail_headers, "-f " . $StripeTaker_SaveFile_Data['notify_cust']);

   }

}


function Templater_FilterReturn($orderinfo, $custinfo, $file) {
   global $StripeTaker_SaveFile_Data, $store_url;

   if (isset($orderinfo['product_id'])) {

      $prodinfo = DataOps_Data_Get(STAKER_PRODS, $orderinfo['product_id']);

   }

   $tpl_vars['order_id'] = $orderinfo['id'];
   $tpl_vars['product_id'] = $orderinfo['product_id'];
   $tpl_vars['product_name'] = $orderinfo['product_name'];
   $tpl_vars['card_type'] = $orderinfo['card_type'];
   $tpl_vars['card_finger'] = $orderinfo['card_finger'];
   $tpl_vars['card_exp'] = $orderinfo['card_exp'];
   $tpl_vars['card_last4'] = $orderinfo['card_last4'];
   $tpl_vars['order_amount'] = $orderinfo['amount'];
   $tpl_vars['order_currency'] = $StripeTaker_SaveFile_Data['currency'];
   $tpl_vars['order_fees'] = $orderinfo['fees'];
   $tpl_vars['order_type'] = $orderinfo['type'];
   $tpl_vars['stripe_charge'] = $orderinfo['charge'];
   $tpl_vars['order_datetime'] = date("Y-m-d H:i:s", $orderinfo['ordered']);
   $tpl_vars['order_ip'] = $orderinfo['ip'];
   $tpl_vars['order_agent'] = $orderinfo['agent'];

   $tpl_vars['product_recurs'] = strtolower($prodinfo['recurs_unit']);
   $tpl_vars['download_url'] = $prodinfo['url_download'];

   $tpl_vars['cust_id'] = $custinfo['id'];
   $tpl_vars['cust_stripe'] = $custinfo['stripe'];
   $tpl_vars['cust_name'] = $custinfo['name'];
   $tpl_vars['cust_email'] = $custinfo['email'];
   $tpl_vars['cust_phone'] = $custinfo['phone'];
   $tpl_vars['bill_street'] = $custinfo['bill_street'];
   $tpl_vars['bill_street2'] = $custinfo['bill_extra'];
   $tpl_vars['bill_city'] = $custinfo['bill_city'];
   $tpl_vars['bill_state'] = $custinfo['bill_state'];
   $tpl_vars['bill_postal'] = $custinfo['postal'];
   $tpl_vars['bill_country'] = $custinfo['country'];

   $tpl_vars['ship_same'] = $custinfo['ship_same'];
   $tpl_vars['ship_street'] = $custinfo['ship_street'];
   $tpl_vars['ship_street2'] = $custinfo['ship_extra'];
   $tpl_vars['ship_city'] = $custinfo['ship_city'];
   $tpl_vars['ship_state'] = $custinfo['ship_state'];
   $tpl_vars['ship_postal'] = $custinfo['ship_postal'];
   $tpl_vars['ship_country'] = $custinfo['ship_country'];

   // refund vars;
   $tpl_vars['amount'] = $custinfo['amount'];
   $tpl_vars['refunded'] = $custinfo['refunded'];
   $tpl_vars['refund'] = $custinfo['refund'];

   $tpl_vars['store_name'] = $StripeTaker_SaveFile_Data['storename'];
   $tpl_vars['store_email'] = $StripeTaker_SaveFile_Data['notify_cust'];
   $tpl_vars['store_url'] = $store_url;


   // read template file into variable;
   $tpl_file = "tpls/" . $file . ".tpl";

   if (file_exists($tpl_file)) {

      $fhandle = fopen($tpl_file, "rt");

      while(!feof($fhandle)) {

         $data .= fgets($fhandle, 8192);

      }

      fclose($fhandle);

      foreach($tpl_vars as $key => $value) {

         $data = str_replace("{" . $key . "}", $value, $data);

      }

      return $data;

   } else {

      return false;

   }

}

?>
