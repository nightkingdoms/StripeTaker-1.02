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

   require_once("core/init.php");

   switch($_REQUEST['op']) {

      case "View":
      Show_Checkout();
      break;

      case "Process":
      Process_Order();
      break;

      default:
      include("headers.inc.php");
      Show_All();
      include("footers.inc.php");
      break;

   }


function Show_All() {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $all_prods = DataOps_ReadFile(STAKER_PRODS);

   if (count($all_prods) > 0) {

      $prod_count = 0;

      ?><h3>All Available Products</h3>

      <?php
      foreach($all_prods as $key => $value) {

         if ($StripeTaker_SaveFile_Data['mode'] == "Live" && $value['storage'] == "Test") {

            // show nothing -- no test products on live system;

         } else {

            ?><h3 style="color: #0088cc;"><?php echo $value['name']; ?></h3>
            <table width="90%" border="0" class="table table-striped">
            <tr>
               <td width="50%" valign="middle" style="text-align: center;">
                  <b class="muted">Price</b>
               </td>
               <td width="50%" valign="middle" style="text-align: center;">
                  <b class="muted">Recurs</b>
               </td>
            </tr>
            <tr>
               <td width="50%" valign="middle" style="text-align: center;">
                  <h4>$<?php echo $value['price']; ?> <?php echo $StripeTaker_SaveFile_Data['currency']; ?></h4>
               </td>
               <td width="50%" valign="middle" style="text-align: center;">
                  <h4><?php if ($value['recurs'] == "Yes") { echo $value['recurs_unit']; } else { echo "No"; } ?></h4>
               </td>
            </tr>
            <tr>
               <td colspan="2">
                  <?php if (mb_strlen($value['desc']) > 300) { echo substr($value['desc'], 0, 300) . "..."; } else { echo nl2br($value['desc']); } ?>
               </td>
            </tr>
            <tr>
               <td colspan="2" style="text-align: right;" valign="middle">
                  <?php if (mb_strlen($value['url_download']) > 5) { ?>
                  <span class="label label-success">Download</span>
                  <?php } else { ?>
                  <span class="label">Download</span>
                  <?php } ?>

                  <?php if ($value['ships'] == "Yes") { ?>
                  <span class="label label-success">Shipping</span>
                  <?php } else { ?>
                  <span class="label">Shipping</span>
                  <?php } ?>

                  <button class="btn btn-primary btn-large" onClick="location.href='order.php?op=View&id=<?php echo $value['id']; ?>';">Order Now!</button>
               </td>
            </tr>
            <?php

            $prod_count++;

            ?></table><br /><p /><?php

         }

         if ($prod_count == 0) {

            ?><h3>We're Sorry</h3>
            <p>We have no products online right now.</p>

            <p>If you believe this to be an error, please contact us (<b><?php echo $StripeTaker_SaveFile_Data['notify_cust']; ?></b>) about the issue.</p><?php

         }

      }

   } else {

      ?><h3>We're Sorry</h3>
      <p>We have no products to showcase right now.</p>

      <p>If you believe this to be an error, please contact us (<b><?php echo $StripeTaker_SaveFile_Data['notify_cust']; ?></b>) about the issue.</p><?php

   }

}


function Show_Checkout() {
   global $StripeTaker_SaveFile_Data;

   $prod_data = DataOps_Data_Get(STAKER_PRODS, $_REQUEST['id']);

   if ($data_check === false && $_REQUEST['Custom'] != "On") {

      Show_All();

   } elseif ($data_check === false && $_REQUEST['Custom'] == "On") {

      Show_Form($prod_data);

   } else {

      $prod_data['embed'] = Encrypt(json_encode($prod_data));
      Show_Form($prod_data);

   }

}


function Process_Order() {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $forminfo = json_decode(Decrypt($_REQUEST['embed']), true);
   $forminfo['cust_name'] = Forms_FormatData($_REQUEST['cust_name']);
   $forminfo['cust_company'] = Forms_FormatData($_REQUEST['cust_company']);
   $forminfo['cust_email'] = Forms_FormatData($_REQUEST['cust_email']);
   $forminfo['cust_ph'] = preg_replace("/[^0-9]/", "", Forms_FormatData($_REQUEST['cust_ph']));
   $forminfo['bill_street'] = Forms_FormatData($_REQUEST['bill_street']);
   $forminfo['bill_extra'] = Forms_FormatData($_REQUEST['bill_extra']);
   $forminfo['bill_city'] = Forms_FormatData($_REQUEST['bill_city']);
   $forminfo['bill_state'] = Forms_FormatData($_REQUEST['bill_state']);
   $forminfo['bill_postal'] = Forms_FormatData($_REQUEST['bill_postal']);
   $forminfo['bill_country'] = Forms_FormatData($_REQUEST['bill_country']);
   $forminfo['ship_same'] = Forms_FormatData($_REQUEST['ship_same']);
   $forminfo['ship_street'] = Forms_FormatData($_REQUEST['ship_street']);
   $forminfo['ship_extra'] = Forms_FormatData($_REQUEST['ship_extra']);
   $forminfo['ship_city'] = Forms_FormatData($_REQUEST['ship_city']);
   $forminfo['ship_state'] = Forms_FormatData($_REQUEST['ship_state']);
   $forminfo['ship_postal'] = Forms_FormatData($_REQUEST['ship_postal']);
   $forminfo['ship_country'] = Forms_FormatData($_REQUEST['ship_country']);
   $forminfo['stripeToken'] = Forms_FormatData($_REQUEST['stripeToken']);

   // setup for error checking;
   $err_count = 0;
   $errors = array();
   $errors['alert-msg'] = "<b>There were some problems with the information you entered:</b>\n<ul>\n";

   // check for errors in data;
   if ($StripeTaker_SaveFile_Data['mode'] == "Live" && $forminfo['storage'] == "Test") { $err_count++; $errors['alert-msg'] .= "<li>The product you selected is not currently available for sale.  Please notify us (" . $StripeTaker_SaveFile_Data['notify_cust'] . ") if you believe this is incorrect.</li>"; }
   if (mb_strlen($forminfo['cust_name']) < 5) { $err_count++; $errors['cust_name'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid name.</li>"; }
   if (!Forms_CheckValidEmail($forminfo['cust_email'])) { $err_count++; $errors['cust_email'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid email address.</li>"; }
   if (mb_strlen($forminfo['cust_ph']) < 10) { $err_count++; $errors['cust_ph'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid phone number.</li>"; }
   if (mb_strlen($forminfo['bill_street']) < 5) { $err_count++; $errors['bill_street'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid street address.</li>"; }
   if (mb_strlen($forminfo['bill_city']) < 3) { $err_count++; $errors['bill_city'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid city name.</li>"; }
   if (mb_strlen($forminfo['bill_state']) < 3) { $err_count++; $errors['bill_state'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid state/province name.</li>"; }
   if (mb_strlen($forminfo['bill_postal'] ) < 5) { $err_count++; $errors['bill_postal'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid postal code.</li>"; }
   if ($forminfo['ship_same'] == "No" && mb_strlen($forminfo['ship_street']) < 5) { $err_count++; $errors['ship_street'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid shipping street address.</li>"; }
   if ($forminfo['ship_same'] == "No" && mb_strlen($forminfo['ship_city']) < 3) { $err_count++; $errors['ship_city'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid shipping city name.</li>"; }
   if ($forminfo['ship_same'] == "No" && mb_strlen($forminfo['ship_state']) < 3) { $err_count++; $errors['ship_state'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid shipping state/province name.</li>"; }
   if ($forminfo['ship_same'] == "No" && mb_strlen($forminfo['ship_postal']) < 5) { $err_count++; $errors['ship_postal'] = ""; $errors['alert-msg'] .= "<li>Please enter a valid shipping postal code.</li>"; }

   // finalize error checking;
   $errors['alert-msg'] .= "\n</ul>";
   $forminfo['embed'] = Encrypt(json_encode($forminfo));

   // prepare error reporting;
   if ($err_count > 0) {

      Show_Form($forminfo, $errors);
      die();

   } else {

      $forminfo['order_id'] = strtoupper(substr(md5(uniqid(rand())), 0, 10));

      $stripe_cust = StripeInterface_Cust_Create($forminfo['stripeToken'], $forminfo['cust_email'], $forminfo['cust_name']);
      $forminfo['cust_ph'] = substr($forminfo['cust_ph'], 0, 3) . "-" . substr($forminfo['cust_ph'], 3, 3) . "-" . substr($forminfo['cust_ph'], 6, mb_strlen($forminfo['cust_ph']) - 6);

      if ($stripe_cust['status'] != "fail") {

         if ($forminfo['recurs'] == "Yes") {

            $stripe_info = StripeInterface_Plan_Assc($stripe_cust['id'], $forminfo['id']);
            $forminfo['chargeType'] = "Subscription";

         } else {

            $desc_prep = $forminfo['name'] . " # " . $forminfo['id'];

            $stripe_info = StripeInterface_ChargeCust($forminfo['price'], $stripe_cust['id'], $desc_prep);
            $forminfo['chargeType'] = "One-Time";

         }

         if ($forminfo['CustomVars'] == "On") {

            $forminfo['id'] = strtoupper(substr(md5(uniqid(rand())), 0, 10));

            // setup product to be added to the database;
            $prod_info['id'] = $forminfo['id'];
            $prod_info['name'] = $forminfo['name'];
            $prod_info['desc'] = $forminfo['desc'];
            $prod_info['price'] = $forminfo['price'];
            $prod_info['recurs'] = $forminfo['recurs'];
            $prod_info['recurs_unit'] = $forminfo['recurs_unit'];
            $prod_info['recurs_orig'] = $forminfo['recurs_orig'];
            $prod_info['ships'] = $forminfo['ships'];
            $prod_info['url_download'] = $forminfo['url_download'];
            $prod_info['url_return'] = $forminfo['url_return'];
            $prod_info['url_notify'] = $forminfo['url_notify'];
            $prod_info['storage'] = $forminfo['storage'];

            DataOps_Data_Insert(STAKER_PRODS, $prod_info);

         }

         $stripe_cust_info = StripeInterface_Cust_Get($stripe_cust['id']);
         $cust_id = strtoupper(substr(md5(uniqid(rand())), 0, 10));

         $orderinfo['id'] = $forminfo['order_id'];
         $orderinfo['customer'] = $cust_id;
         $orderinfo['email'] = $stripe_cust['email'];
         $orderinfo['product_id'] = $forminfo['id'];
         $orderinfo['product_name'] = $forminfo['name'];
         $orderinfo['card_type'] = $stripe_cust['active_card']['type'];
         $orderinfo['card_finger'] = $stripe_cust['active_card']['fingerprint'];
         $orderinfo['card_exp'] = $stripe_cust['active_card']['exp_month'] . "/" . $stripe_cust['active_card']['exp_year'];
         $orderinfo['card_last4'] = $stripe_cust['active_card']['last4'];
         $orderinfo['amount'] = $forminfo['price'];
         $orderinfo['fees'] = number_format((round(($forminfo['price'] * 0.029), 2) + 0.30), 2, ".", "");
         $orderinfo['type'] = $forminfo['chargeType'];
         $orderinfo['charge'] = $stripe_info['id'];
         $orderinfo['ordered'] = time();
         $orderinfo['ip'] = $_SERVER['REMOTE_ADDR'];
         $orderinfo['agent'] = $_SERVER['HTTP_USER_AGENT'];

         $custinfo['id'] = $cust_id;
         $custinfo['stripe'] = $stripe_cust['id'];
         $custinfo['name'] = $forminfo['cust_name'];
         $custinfo['email'] = $forminfo['cust_email'];
         $custinfo['phone'] = $forminfo['cust_ph'];
         $custinfo['company'] = $forminfo['cust_company'];
         $custinfo['bill_street'] = $forminfo['bill_street'];
         $custinfo['bill_extra'] = $forminfo['bill_extra'];
         $custinfo['bill_city'] = $forminfo['bill_city'];
         $custinfo['bill_state'] = $forminfo['bill_state'];
         $custinfo['bill_postal'] = $forminfo['bill_postal'];
         $custinfo['bill_country'] = $forminfo['bill_country'];
         $custinfo['ship_same'] = $forminfo['ship_same'];
         $custinfo['ship_street'] = $forminfo['ship_street'];
         $custinfo['ship_extra'] = $forminfo['ship_extra'];
         $custinfo['ship_city'] = $forminfo['ship_city'];
         $custinfo['ship_state'] = $forminfo['ship_state'];
         $custinfo['ship_postal'] = $forminfo['ship_postal'];
         $custinfo['ship_country'] = $forminfo['ship_country'];

         if ($custinfo['ship_same'] == "Yes") {

            $custinfo['ship_street'] = $forminfo['bill_street'];
            $custinfo['ship_extra'] = $forminfo['bill_extra'];
            $custinfo['ship_city'] = $forminfo['bill_city'];
            $custinfo['ship_state'] = $forminfo['bill_state'];
            $custinfo['ship_postal'] = $forminfo['bill_postal'];
            $custinfo['ship_country'] = $forminfo['bill_country'];

         }

         // save new data to files;
         DataOps_Data_Insert(STAKER_ORDERS, $orderinfo);
         DataOps_Data_Insert(STAKER_CUSTS, $custinfo);

         // send POST back to notification url, if present;
         if (mb_strlen($forminfo['url_notify'])) {

            $allinfo['customer'] = $custinfo;
            $allinfo['order'] = $orderinfo;
            $allinfo['product'] = $forminfo;
            $send_data['params'] = Encrypt(json_encode($allinfo));
            if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") { $result = DataOps_Outbound($forminfo['url_notify'], $send_data); }

         }

         // send email notifications;

         if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") {

            Templater_Notify("cust", $orderinfo, $custinfo, $forminfo);
            Templater_Notify("admin", $orderinfo, $custinfo, $forminfo);

         }

         if ($forminfo['url_return'] == "Yes" && $_REQUEST['override'] != "phone") {

            // send customer back to return url;
            header("Location:" . $forminfo['url_return']);

         } elseif ($_REQUEST['override'] == "phone") {

            header("Location:manage.php?op=Orders_View&id=" . $orderinfo['id']);

         } else {

         // Show confirmation page;
         include("headers.inc.php");

         ?><div class="alert alert-success"><b><i class="icon-ok-sign icon-large"></i> Order Confirmed!</b></div><br />

         <center><h3>Order # <?php echo $orderinfo['id']; ?></h3></center>

         <table border="0" class="table table-striped table-bordered">
            <tr>
               <td width="5%">
                  1
               </td>
               <td width="65%">
                  <?php echo $forminfo['name']; ?>
               </td>
               <td width="30%">
                  $<?php echo $forminfo['price']; ?> (<?php echo $StripeTaker_SaveFile_Data['currency']; ?>) <?php if ($forminfo['recurs'] == "Yes") { echo " " . strtolower($forminfo['recurs_unit']); } ?>
               </td>
            </tr>
         </table>

         <p>We have successfully received your order! You should receive an email at <b><?php echo $custinfo['email']; ?></b> with all this information as well.</p>

         <p>Please also check your spam folder in case we get trapped in there!  It's dark there and we get scared. <b>:(</b></p>

         <?php if ($forminfo['ships'] == "Yes") { ?>

            <p><b>Your order will ship to:</b></p>

            <p><address>
            <b><?php echo $custinfo['name']; ?></b><br />
            <?php echo $custinfo['ship_street']; ?><br />
            <?php if (mb_strlen($custinfo['ship_extra']) > 2) { echo $custinfo['ship_extra'] . "<br />\n"; } ?>
            <?php echo $custinfo['ship_city']; ?>, <?php echo $custinfo['ship_state']; ?><br />
            <?php echo $custinfo['ship_postal']; ?><br />
            <?php echo $custinfo['ship_country']; ?>
            </address></p>

         <?php } ?>

         <?php if (mb_strlen($forminfo['url_download']) > 5) { ?>

            <p><button onClick="javascript:location.href='<?php echo $forminfo['url_download']; ?>';" class="btn btn-success btn-large">Download Your Product Here</button></p>

         <?php } ?>
         <?php

         include("footers.inc.php");

         }

      } else {

         $errors['alert-msg'] = "<b>We encountered an error while attempting to process your payment.</b>\n<ul>\n<li>" . $stripe_cust['msg'] . "</li>\n</ul>\n\n<!-- " . $stripe_cust['orig'] . " -->\n";
         $forminfo['embed'] = Encrypt(json_encode($forminfo));

         Show_Form($forminfo, $errors);
         die();

      }

   }

}


function Show_Form($forminfo, $errors=0) {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $header_pagename = "Checkout";
   $header_credit = 1;

   if ($_REQUEST['Custom'] == "On") {

      $forminfo['name'] = $_REQUEST['P_Name'];
      $forminfo['desc'] = $_REQUEST['P_Desc'];
      $forminfo['recurs_orig'] = "No";
      $forminfo['url_download'] = $_REQUEST['P_URL_Download'];
      $forminfo['url_return'] = $_REQUEST['P_URL_Return'];
      $forminfo['url_notify'] = $_REQUEST['P_URL_Notify'];
      $forminfo['storage'] = $StripeTaker_SaveFile_Data['mode'];

      if (!isset($_REQUEST['P_Name']) || mbstr_len($_REQUEST['P_Name']) < 3) {

         $forminfo['name'] = "(None Given)";

      }

      if (isset($_REQUEST['P_Price'])) {

         settype($_REQUEST['P_Price'], "integer");

         if ($_REQUEST['P_Price'] > 0.50) {

            $forminfo['price'] = $_REQUEST['P_Price'];

         } else {

            $forminfo['price'] = 1.00;

         }

      } else {

         $forminfo['price'] = 1.00;

      }

      if (isset($_REQUEST['P_Recurs']) && $_REQUEST['P_Recurs'] == "Yes") {

         $forminfo['recurs'] = $_REQUEST['P_Recurs']; } else { $forminfo['recurs'] = "No";

      }

      if (isset($_REQUEST['P_Recurs_Unit']) && $_REQUEST['P_Recurs_Unit'] == "Yearly") {

         $forminfo['recurs_unit'] = $_REQUEST['P_Recurs_Unit']; } else { $forminfo['recurs_unit'] = "Monthly";

      }

      if (isset($_REQUEST['P_Ships']) && $_REQUEST['P_Ships'] == "Yes") {

         $forminfo['ship'] = $_REQUEST['P_Ships'];

      }

      $forminfo['CustomVars'] = "On";

      $forminfo['embed'] = Encrypt(json_encode($forminfo));

   }

   include("headers.inc.php");

   ?><form method="POST" action="order.php?op=Process" id="payment-form">
   <input type="hidden" name="embed" value="<?php echo $forminfo['embed']; ?>">
   <?php if ($_REQUEST['override'] == "phone") { ?><input type="hidden" name="override" value="phone"><?php } ?>

   <?php if (isset($errors['alert-msg'])) { ?>
   <div class="alert alert-error"><?php echo $errors['alert-msg']; ?></div>
   <?php } ?>

   <?php if (isset($forminfo['info-msg'])) { ?>
   <div class="alert alert-info"><?php echo $forminfo['info-msg']; ?></div>
   <?php } ?>

   <h3>Your Order</h3>

   <table width="85%" border="0" class="table table-striped table-bordered">
   <thead>
      <tr>
         <th width="50%">Product Name</th>
         <th>Cost</th>
      </tr>
   </thead>

   <tbody>
      <tr>
         <td><?php echo $forminfo['name']; ?></td>
         <td>
            $<?php echo $forminfo['price']; ?> <?php echo $StripeTaker_SaveFile_Data['currency']; ?>
            <?php if ($forminfo['recurs'] == "Yes" && $forminfo['recurs_unit'] == "Monthly") { echo " every month until cancelled"; } ?>
            <?php if ($forminfo['recurs'] == "Yes" && $forminfo['recurs_unit'] == "Yearly") { echo " every year until cancelled"; } ?>
         </td>
      </tr>
      <tr>
         <td colspan="2">
            <b class="muted">Description</b><p />
            <?php echo nl2br($forminfo['desc']); ?>
         </td>
      </tr>
   </tbody>
   </table>

   <?php if ($browser_type != "mobile") { ?>
   <table width="85%" border="0">
      <tr><td width="50%" valign="top">
   <?php } ?>

      <h3>Your Billing Information <?php if ($browser_type == "mobile") { echo "<br />"; } ?><small>Your Information Is Safe With Us</small></h3>
      <p><label for="cust_name">Your Name:</label>
      <input type="text" name="cust_name" id="cust_name" class="input-large<?php if (isset($errors['cust_name'])) { echo " error"; } ?>" placeholder="John Smith" value="<?php if (isset($forminfo['cust_name'])) { echo $forminfo['cust_name']; } ?>" /></p>

      <p><label for="cust_company">Company:</label>
      <input type="text" name="cust_company" id="cust_company" class="input-large<?php if (isset($errors['cust_company'])) { echo " error"; } ?>" placeholder="Awesome Widget Co." value="<?php if (isset($forminfo['cust_company'])) { echo $forminfo['cust_company']; } ?>" /></p>

      <p><label for="cust_email">Email Address:</label>
      <input type="text" name="cust_email" id="cust_email" class="input-xlarge<?php if (isset($errors['cust_email'])) { echo " error"; } ?>" placeholder="jsmith@widgetco.com" value="<?php if (isset($forminfo['cust_email'])) { echo $forminfo['cust_email']; } ?>" /></p>

      <p><label for="cust_ph">Phone Number:</label>
      <input type="text" name="cust_ph" id="cust_ph" class="input-medium<?php if (isset($errors['cust_ph'])) { echo " error"; } ?>" placeholder="212-000-1234" value="<?php if (isset($forminfo['cust_ph'])) { echo $forminfo['cust_ph']; } ?>" /></p>

      <p><label for="bill_street">Billing Address:</label>
      <input type="text" name="bill_street" id="bill_street" class="input-xlarge<?php if (isset($errors['bill_street'])) { echo " error"; } ?>" placeholder="123 Happy St" value="<?php if (!isset($forminfo['bill_street'])) { echo $forminfo['addr_street']; } else { echo $forminfo['bill_street']; } ?>" /><br />
      <input type="text" name="bill_extra" id="bill_extra" class="input-xlarge" placeholder="Suite 4321" value="<?php if (!isset($forminfo['bill_extra'])) { echo $forminfo['addr_extra']; } else { echo $forminfo['bill_extra']; } ?>" /></p>

      <p><label for="bill_city">City:</label>
      <input type="text" name="bill_city" id="bill_city" class="input-medium<?php if (isset($errors['bill_city'])) { echo " error"; } ?>" placeholder="City" value="<?php if (!isset($forminfo['bill_city'])) { echo $forminfo['addr_city']; } else { echo $forminfo['bill_city']; } ?>" /> 

      <label for="bill_state">State/Province:</label>
      <input type="text" name="bill_state" id="bill_state" class="input-medium<?php if (isset($errors['bill_state'])) { echo " error"; } ?>" placeholder="State" value="<?php if (!isset($forminfo['bill_state'])) { echo $forminfo['addr_state']; } else { echo $forminfo['bill_state']; } ?>" /></p>

      <p><label for="bill_postal">Postal Code:</label>
      <input type="text" name="bill_postal" id="bill_postal" class="input-small<?php if (isset($errors['bill_postal'])) { echo " error"; } ?>" placeholder="Postal Code" value="<?php if (!isset($forminfo['bill_postal'])) { echo $forminfo['addr_postal']; } else { echo $forminfo['bill_postal']; } ?>" /></p>

      <p><label for="bill_country">Country:</label>
      <select name="bill_country" id="bill_country" class="input-large<?php if (isset($errors['bill_country'])) { echo " error"; } ?>"> 
         <option value="United States"<?php if ($StripeTaker_SaveFile_Data['currency'] == "USD") { echo " SELECTED"; } ?>>United States</option> 
         <option value="Canada"<?php if ($StripeTaker_SaveFile_Data['currency'] == "CAN") { echo " SELECTED"; } ?>>Canada</option> 
         <option value="United Kingdom">United Kingdom</option> 
         <option value="Afghanistan">Afghanistan</option> 
         <option value="Albania">Albania</option> 
         <option value="Algeria">Algeria</option> 
         <option value="American Samoa">American Samoa</option> 
         <option value="Andorra">Andorra</option> 
         <option value="Angola">Angola</option> 
         <option value="Anguilla">Anguilla</option> 
         <option value="Antarctica">Antarctica</option> 
         <option value="Antigua and Barbuda">Antigua and Barbuda</option> 
         <option value="Argentina">Argentina</option> 
         <option value="Armenia">Armenia</option> 
         <option value="Aruba">Aruba</option> 
         <option value="Australia">Australia</option> 
         <option value="Austria">Austria</option> 
         <option value="Azerbaijan">Azerbaijan</option> 
         <option value="Bahamas">Bahamas</option> 
         <option value="Bahrain">Bahrain</option> 
         <option value="Bangladesh">Bangladesh</option> 
         <option value="Barbados">Barbados</option> 
         <option value="Belarus">Belarus</option> 
         <option value="Belgium">Belgium</option> 
         <option value="Belize">Belize</option> 
         <option value="Benin">Benin</option> 
         <option value="Bermuda">Bermuda</option> 
         <option value="Bhutan">Bhutan</option> 
         <option value="Bolivia">Bolivia</option> 
         <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option> 
         <option value="Botswana">Botswana</option> 
         <option value="Bouvet Island">Bouvet Island</option> 
         <option value="Brazil">Brazil</option> 
         <option value="British Indian Ocean Territory">British Indian Ocean Territory</option> 
         <option value="Brunei Darussalam">Brunei Darussalam</option> 
         <option value="Bulgaria">Bulgaria</option> 
         <option value="Burkina Faso">Burkina Faso</option> 
         <option value="Burundi">Burundi</option> 
         <option value="Cambodia">Cambodia</option> 
         <option value="Cameroon">Cameroon</option> 
         <option value="Cape Verde">Cape Verde</option> 
         <option value="Cayman Islands">Cayman Islands</option> 
         <option value="Central African Republic">Central African Republic</option> 
         <option value="Chad">Chad</option> 
         <option value="Chile">Chile</option> 
         <option value="China">China</option> 
         <option value="Christmas Island">Christmas Island</option> 
         <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
         <option value="Colombia">Colombia</option> 
         <option value="Comoros">Comoros</option> 
         <option value="Congo">Congo</option> 
         <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
         <option value="Cook Islands">Cook Islands</option> 
         <option value="Costa Rica">Costa Rica</option> 
         <option value="Cote D'ivoire">Cote D'ivoire</option> 
         <option value="Croatia">Croatia</option> 
         <option value="Cuba">Cuba</option> 
         <option value="Cyprus">Cyprus</option> 
         <option value="Czech Republic">Czech Republic</option> 
         <option value="Denmark">Denmark</option> 
         <option value="Djibouti">Djibouti</option> 
         <option value="Dominica">Dominica</option> 
         <option value="Dominican Republic">Dominican Republic</option> 
         <option value="Ecuador">Ecuador</option> 
         <option value="Egypt">Egypt</option> 
         <option value="El Salvador">El Salvador</option> 
         <option value="Equatorial Guinea">Equatorial Guinea</option> 
         <option value="Eritrea">Eritrea</option> 
         <option value="Estonia">Estonia</option> 
         <option value="Ethiopia">Ethiopia</option> 
         <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
         <option value="Faroe Islands">Faroe Islands</option> 
         <option value="Fiji">Fiji</option> 
         <option value="Finland">Finland</option> 
         <option value="France">France</option> 
         <option value="French Guiana">French Guiana</option> 
         <option value="French Polynesia">French Polynesia</option> 
         <option value="French Southern Territories">French Southern Territories</option> 
         <option value="Gabon">Gabon</option> 
         <option value="Gambia">Gambia</option> 
         <option value="Georgia">Georgia</option> 
         <option value="Germany">Germany</option> 
         <option value="Ghana">Ghana</option> 
         <option value="Gibraltar">Gibraltar</option> 
         <option value="Greece">Greece</option> 
         <option value="Greenland">Greenland</option> 
         <option value="Grenada">Grenada</option> 
         <option value="Guadeloupe">Guadeloupe</option> 
         <option value="Guam">Guam</option> 
         <option value="Guatemala">Guatemala</option> 
         <option value="Guinea">Guinea</option> 
         <option value="Guinea-bissau">Guinea-bissau</option> 
         <option value="Guyana">Guyana</option> 
         <option value="Haiti">Haiti</option> 
         <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option> 
         <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option> 
         <option value="Honduras">Honduras</option> 
         <option value="Hong Kong">Hong Kong</option> 
         <option value="Hungary">Hungary</option> 
         <option value="Iceland">Iceland</option> 
         <option value="India">India</option> 
         <option value="Indonesia">Indonesia</option> 
         <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option> 
         <option value="Iraq">Iraq</option> 
         <option value="Ireland">Ireland</option> 
         <option value="Israel">Israel</option> 
         <option value="Italy">Italy</option> 
         <option value="Jamaica">Jamaica</option> 
         <option value="Japan">Japan</option> 
         <option value="Jordan">Jordan</option> 
         <option value="Kazakhstan">Kazakhstan</option> 
         <option value="Kenya">Kenya</option> 
         <option value="Kiribati">Kiribati</option> 
         <option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option> 
         <option value="Korea, Republic of">Korea, Republic of</option> 
         <option value="Kuwait">Kuwait</option> 
         <option value="Kyrgyzstan">Kyrgyzstan</option> 
         <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option> 
         <option value="Latvia">Latvia</option> 
         <option value="Lebanon">Lebanon</option> 
         <option value="Lesotho">Lesotho</option> 
         <option value="Liberia">Liberia</option> 
         <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option> 
         <option value="Liechtenstein">Liechtenstein</option> 
         <option value="Lithuania">Lithuania</option> 
         <option value="Luxembourg">Luxembourg</option> 
         <option value="Macao">Macao</option> 
         <option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option> 
         <option value="Madagascar">Madagascar</option> 
         <option value="Malawi">Malawi</option> 
         <option value="Malaysia">Malaysia</option> 
         <option value="Maldives">Maldives</option> 
         <option value="Mali">Mali</option> 
         <option value="Malta">Malta</option> 
         <option value="Marshall Islands">Marshall Islands</option> 
         <option value="Martinique">Martinique</option> 
         <option value="Mauritania">Mauritania</option> 
         <option value="Mauritius">Mauritius</option> 
         <option value="Mayotte">Mayotte</option> 
         <option value="Mexico">Mexico</option> 
         <option value="Micronesia, Federated States of">Micronesia, Federated States of</option> 
         <option value="Moldova, Republic of">Moldova, Republic of</option> 
         <option value="Monaco">Monaco</option> 
         <option value="Mongolia">Mongolia</option> 
         <option value="Montserrat">Montserrat</option> 
         <option value="Morocco">Morocco</option> 
         <option value="Mozambique">Mozambique</option> 
         <option value="Myanmar">Myanmar</option> 
         <option value="Namibia">Namibia</option> 
         <option value="Nauru">Nauru</option> 
         <option value="Nepal">Nepal</option> 
         <option value="Netherlands">Netherlands</option> 
         <option value="Netherlands Antilles">Netherlands Antilles</option> 
         <option value="New Caledonia">New Caledonia</option> 
         <option value="New Zealand">New Zealand</option> 
         <option value="Nicaragua">Nicaragua</option> 
         <option value="Niger">Niger</option> 
         <option value="Nigeria">Nigeria</option> 
         <option value="Niue">Niue</option> 
         <option value="Norfolk Island">Norfolk Island</option> 
         <option value="Northern Mariana Islands">Northern Mariana Islands</option> 
         <option value="Norway">Norway</option> 
         <option value="Oman">Oman</option> 
         <option value="Pakistan">Pakistan</option> 
         <option value="Palau">Palau</option> 
         <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option> 
         <option value="Panama">Panama</option> 
         <option value="Papua New Guinea">Papua New Guinea</option> 
         <option value="Paraguay">Paraguay</option> 
         <option value="Peru">Peru</option> 
         <option value="Philippines">Philippines</option> 
         <option value="Pitcairn">Pitcairn</option> 
         <option value="Poland">Poland</option> 
         <option value="Portugal">Portugal</option> 
         <option value="Puerto Rico">Puerto Rico</option> 
         <option value="Qatar">Qatar</option> 
         <option value="Reunion">Reunion</option> 
         <option value="Romania">Romania</option> 
         <option value="Russian Federation">Russian Federation</option> 
         <option value="Rwanda">Rwanda</option> 
         <option value="Saint Helena">Saint Helena</option> 
         <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option> 
         <option value="Saint Lucia">Saint Lucia</option> 
         <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option> 
         <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option> 
         <option value="Samoa">Samoa</option> 
         <option value="San Marino">San Marino</option> 
         <option value="Sao Tome and Principe">Sao Tome and Principe</option> 
         <option value="Saudi Arabia">Saudi Arabia</option> 
         <option value="Senegal">Senegal</option> 
         <option value="Serbia and Montenegro">Serbia and Montenegro</option> 
         <option value="Seychelles">Seychelles</option> 
         <option value="Sierra Leone">Sierra Leone</option> 
         <option value="Singapore">Singapore</option> 
         <option value="Slovakia">Slovakia</option> 
         <option value="Slovenia">Slovenia</option> 
         <option value="Solomon Islands">Solomon Islands</option> 
         <option value="Somalia">Somalia</option> 
         <option value="South Africa">South Africa</option> 
         <option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option> 
         <option value="Spain">Spain</option> 
         <option value="Sri Lanka">Sri Lanka</option> 
         <option value="Sudan">Sudan</option> 
         <option value="Suriname">Suriname</option> 
         <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option> 
         <option value="Swaziland">Swaziland</option> 
         <option value="Sweden">Sweden</option> 
         <option value="Switzerland">Switzerland</option> 
         <option value="Syrian Arab Republic">Syrian Arab Republic</option> 
         <option value="Taiwan, Province of China">Taiwan, Province of China</option> 
         <option value="Tajikistan">Tajikistan</option> 
         <option value="Tanzania, United Republic of">Tanzania, United Republic of</option> 
         <option value="Thailand">Thailand</option> 
         <option value="Timor-leste">Timor-leste</option> 
         <option value="Togo">Togo</option> 
         <option value="Tokelau">Tokelau</option> 
         <option value="Tonga">Tonga</option> 
         <option value="Trinidad and Tobago">Trinidad and Tobago</option> 
         <option value="Tunisia">Tunisia</option> 
         <option value="Turkey">Turkey</option> 
         <option value="Turkmenistan">Turkmenistan</option> 
         <option value="Turks and Caicos Islands">Turks and Caicos Islands</option> 
         <option value="Tuvalu">Tuvalu</option> 
         <option value="Uganda">Uganda</option> 
         <option value="Ukraine">Ukraine</option> 
         <option value="United Arab Emirates">United Arab Emirates</option> 
         <option value="United Kingdom">United Kingdom</option> 
         <option value="United States">United States</option> 
         <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option> 
         <option value="Uruguay">Uruguay</option> 
         <option value="Uzbekistan">Uzbekistan</option> 
         <option value="Vanuatu">Vanuatu</option> 
         <option value="Venezuela">Venezuela</option> 
         <option value="Viet Nam">Viet Nam</option> 
         <option value="Virgin Islands, British">Virgin Islands, British</option> 
         <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option> 
         <option value="Wallis and Futuna">Wallis and Futuna</option> 
         <option value="Western Sahara">Western Sahara</option> 
         <option value="Yemen">Yemen</option> 
         <option value="Zambia">Zambia</option> 
         <option value="Zimbabwe">Zimbabwe</option>
      </select></p>

      <?php if ($forminfo['ships'] == "Yes") { ?>
      <p class="form-inline"><label for="ship_same">Billing Address the Same As Shipping?</label>
      <select name="ship_same" id="ship_same" class="input-small <?php if (isset($errors['ship_same'])) { echo " error"; } ?>" onChange="javascript:$('#ship_div').toggle();">
      <option value="No"<?php if ($forminfo['ship_same'] == "No") { echo " SELECTED"; } ?>><?php if ($forminfo['ship_same'] == "No") { echo "* "; } ?>No</option>
      <option value="Yes"<?php if ($forminfo['ship_same'] == "Yes") { echo " SELECTED"; } ?>><?php if ($forminfo['ship_same'] == "Yes") { echo "* "; } ?>Yes</option>
      </select></p>

      <div id="ship_div"<?php if ($forminfo['ship_same'] == "Yes") { echo " style=\"display: none;\""; } ?>">
      <p><label for="ship_street">Shipping Address:</label>
      <input type="text" name="ship_street" id="ship_street" class="input-xlarge<?php if (isset($errors['ship_street'])) { echo " error"; } ?>" placeholder="123 Happy St" value="<?php if (!isset($forminfo['ship_street'])) { echo $forminfo['addr_street']; } else { echo $forminfo['ship_street']; } ?>" /><br />
      <input type="text" name="ship_extra" id="ship_extra" class="input-xlarge" placeholder="Suite 4321" value="<?php if (!isset($forminfo['ship_extra'])) { echo $forminfo['addr_extra']; } else { echo $forminfo['ship_extra']; } ?>" /></p>

      <p><label for="ship_city">City:</label>
      <input type="text" name="ship_city" id="ship_city" class="input-medium<?php if (isset($errors['ship_city'])) { echo " error"; } ?>" placeholder="City" value="<?php if (!isset($forminfo['ship_city'])) { echo $forminfo['addr_city']; } else { echo $forminfo['ship_city']; } ?>" /> 

      <label for="ship_state">State/Province:</label>
      <input type="text" name="ship_state" id="ship_state" class="input-medium<?php if (isset($errors['ship_state'])) { echo " error"; } ?>" placeholder="State" value="<?php if (!isset($forminfo['ship_state'])) { echo $forminfo['addr_state']; } else { echo $forminfo['ship_state']; } ?>" /></p>

      <p><label for="ship_postal">Postal Code:</label>
      <input type="text" name="ship_postal" id="ship_postal" class="input-small<?php if (isset($errors['ship_postal'])) { echo " error"; } ?>" placeholder="Postal Code" value="<?php if (!isset($forminfo['ship_postal'])) { echo $forminfo['ship_postal']; } else { echo $forminfo['ship_postal']; } ?>" /></p>

      <p><label for="ship_country">Country:</label>
      <select name="ship_country" id="ship_country" class="input-large<?php if (isset($errors['ship_country'])) { echo " error"; } ?>"> 
         <option value="United States"<?php if ($StripeTaker_SaveFile_Data['currency'] == "USD") { echo " SELECTED"; } ?>>United States</option> 
         <option value="Canada"<?php if ($StripeTaker_SaveFile_Data['currency'] == "CAN") { echo " SELECTED"; } ?>>Canada</option> 
         <option value="United Kingdom">United Kingdom</option> 
         <option value="Afghanistan">Afghanistan</option> 
         <option value="Albania">Albania</option> 
         <option value="Algeria">Algeria</option> 
         <option value="American Samoa">American Samoa</option> 
         <option value="Andorra">Andorra</option> 
         <option value="Angola">Angola</option> 
         <option value="Anguilla">Anguilla</option> 

         <option value="Antarctica">Antarctica</option> 
         <option value="Antigua and Barbuda">Antigua and Barbuda</option> 
         <option value="Argentina">Argentina</option> 
         <option value="Armenia">Armenia</option> 
         <option value="Aruba">Aruba</option> 
         <option value="Australia">Australia</option> 
         <option value="Austria">Austria</option> 
         <option value="Azerbaijan">Azerbaijan</option> 
         <option value="Bahamas">Bahamas</option> 
         <option value="Bahrain">Bahrain</option> 
         <option value="Bangladesh">Bangladesh</option> 
         <option value="Barbados">Barbados</option> 
         <option value="Belarus">Belarus</option> 
         <option value="Belgium">Belgium</option> 
         <option value="Belize">Belize</option> 
         <option value="Benin">Benin</option> 
         <option value="Bermuda">Bermuda</option> 
         <option value="Bhutan">Bhutan</option> 
         <option value="Bolivia">Bolivia</option> 
         <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option> 
         <option value="Botswana">Botswana</option> 
         <option value="Bouvet Island">Bouvet Island</option> 
         <option value="Brazil">Brazil</option> 
         <option value="British Indian Ocean Territory">British Indian Ocean Territory</option> 
         <option value="Brunei Darussalam">Brunei Darussalam</option> 
         <option value="Bulgaria">Bulgaria</option> 
         <option value="Burkina Faso">Burkina Faso</option> 
         <option value="Burundi">Burundi</option> 
         <option value="Cambodia">Cambodia</option> 
         <option value="Cameroon">Cameroon</option> 
         <option value="Cape Verde">Cape Verde</option> 
         <option value="Cayman Islands">Cayman Islands</option> 
         <option value="Central African Republic">Central African Republic</option> 
         <option value="Chad">Chad</option> 
         <option value="Chile">Chile</option> 
         <option value="China">China</option> 
         <option value="Christmas Island">Christmas Island</option> 
         <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
         <option value="Colombia">Colombia</option> 
         <option value="Comoros">Comoros</option> 
         <option value="Congo">Congo</option> 
         <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
         <option value="Cook Islands">Cook Islands</option> 
         <option value="Costa Rica">Costa Rica</option> 
         <option value="Cote D'ivoire">Cote D'ivoire</option> 
         <option value="Croatia">Croatia</option> 
         <option value="Cuba">Cuba</option> 
         <option value="Cyprus">Cyprus</option> 
         <option value="Czech Republic">Czech Republic</option> 
         <option value="Denmark">Denmark</option> 
         <option value="Djibouti">Djibouti</option> 
         <option value="Dominica">Dominica</option> 
         <option value="Dominican Republic">Dominican Republic</option> 
         <option value="Ecuador">Ecuador</option> 
         <option value="Egypt">Egypt</option> 
         <option value="El Salvador">El Salvador</option> 
         <option value="Equatorial Guinea">Equatorial Guinea</option> 
         <option value="Eritrea">Eritrea</option> 
         <option value="Estonia">Estonia</option> 
         <option value="Ethiopia">Ethiopia</option> 
         <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
         <option value="Faroe Islands">Faroe Islands</option> 
         <option value="Fiji">Fiji</option> 
         <option value="Finland">Finland</option> 
         <option value="France">France</option> 
         <option value="French Guiana">French Guiana</option> 
         <option value="French Polynesia">French Polynesia</option> 
         <option value="French Southern Territories">French Southern Territories</option> 
         <option value="Gabon">Gabon</option> 
         <option value="Gambia">Gambia</option> 
         <option value="Georgia">Georgia</option> 
         <option value="Germany">Germany</option> 
         <option value="Ghana">Ghana</option> 
         <option value="Gibraltar">Gibraltar</option> 
         <option value="Greece">Greece</option> 
         <option value="Greenland">Greenland</option> 
         <option value="Grenada">Grenada</option> 
         <option value="Guadeloupe">Guadeloupe</option> 
         <option value="Guam">Guam</option> 
         <option value="Guatemala">Guatemala</option> 
         <option value="Guinea">Guinea</option> 
         <option value="Guinea-bissau">Guinea-bissau</option> 
         <option value="Guyana">Guyana</option> 
         <option value="Haiti">Haiti</option> 
         <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option> 
         <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option> 
         <option value="Honduras">Honduras</option> 
         <option value="Hong Kong">Hong Kong</option> 
         <option value="Hungary">Hungary</option> 
         <option value="Iceland">Iceland</option> 
         <option value="India">India</option> 
         <option value="Indonesia">Indonesia</option> 
         <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option> 
         <option value="Iraq">Iraq</option> 
         <option value="Ireland">Ireland</option> 
         <option value="Israel">Israel</option> 
         <option value="Italy">Italy</option> 
         <option value="Jamaica">Jamaica</option> 
         <option value="Japan">Japan</option> 
         <option value="Jordan">Jordan</option> 
         <option value="Kazakhstan">Kazakhstan</option> 
         <option value="Kenya">Kenya</option> 
         <option value="Kiribati">Kiribati</option> 
         <option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option> 
         <option value="Korea, Republic of">Korea, Republic of</option> 
         <option value="Kuwait">Kuwait</option> 
         <option value="Kyrgyzstan">Kyrgyzstan</option> 
         <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option> 
         <option value="Latvia">Latvia</option> 
         <option value="Lebanon">Lebanon</option> 
         <option value="Lesotho">Lesotho</option> 
         <option value="Liberia">Liberia</option> 
         <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option> 
         <option value="Liechtenstein">Liechtenstein</option> 
         <option value="Lithuania">Lithuania</option> 
         <option value="Luxembourg">Luxembourg</option> 
         <option value="Macao">Macao</option> 
         <option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option> 
         <option value="Madagascar">Madagascar</option> 
         <option value="Malawi">Malawi</option> 
         <option value="Malaysia">Malaysia</option> 
         <option value="Maldives">Maldives</option> 
         <option value="Mali">Mali</option> 
         <option value="Malta">Malta</option> 
         <option value="Marshall Islands">Marshall Islands</option> 
         <option value="Martinique">Martinique</option> 
         <option value="Mauritania">Mauritania</option> 
         <option value="Mauritius">Mauritius</option> 
         <option value="Mayotte">Mayotte</option> 
         <option value="Mexico">Mexico</option> 
         <option value="Micronesia, Federated States of">Micronesia, Federated States of</option> 
         <option value="Moldova, Republic of">Moldova, Republic of</option> 
         <option value="Monaco">Monaco</option> 
         <option value="Mongolia">Mongolia</option> 
         <option value="Montserrat">Montserrat</option> 
         <option value="Morocco">Morocco</option> 
         <option value="Mozambique">Mozambique</option> 
         <option value="Myanmar">Myanmar</option> 
         <option value="Namibia">Namibia</option> 
         <option value="Nauru">Nauru</option> 
         <option value="Nepal">Nepal</option> 
         <option value="Netherlands">Netherlands</option> 
         <option value="Netherlands Antilles">Netherlands Antilles</option> 
         <option value="New Caledonia">New Caledonia</option> 
         <option value="New Zealand">New Zealand</option> 
         <option value="Nicaragua">Nicaragua</option> 
         <option value="Niger">Niger</option> 
         <option value="Nigeria">Nigeria</option> 
         <option value="Niue">Niue</option> 
         <option value="Norfolk Island">Norfolk Island</option> 
         <option value="Northern Mariana Islands">Northern Mariana Islands</option> 
         <option value="Norway">Norway</option> 
         <option value="Oman">Oman</option> 
         <option value="Pakistan">Pakistan</option> 
         <option value="Palau">Palau</option> 
         <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option> 
         <option value="Panama">Panama</option> 
         <option value="Papua New Guinea">Papua New Guinea</option> 
         <option value="Paraguay">Paraguay</option> 
         <option value="Peru">Peru</option> 
         <option value="Philippines">Philippines</option> 
         <option value="Pitcairn">Pitcairn</option> 
         <option value="Poland">Poland</option> 
         <option value="Portugal">Portugal</option> 
         <option value="Puerto Rico">Puerto Rico</option> 
         <option value="Qatar">Qatar</option> 
         <option value="Reunion">Reunion</option> 
         <option value="Romania">Romania</option> 
         <option value="Russian Federation">Russian Federation</option> 
         <option value="Rwanda">Rwanda</option> 
         <option value="Saint Helena">Saint Helena</option> 
         <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option> 
         <option value="Saint Lucia">Saint Lucia</option> 
         <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option> 
         <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option> 
         <option value="Samoa">Samoa</option> 
         <option value="San Marino">San Marino</option> 
         <option value="Sao Tome and Principe">Sao Tome and Principe</option> 
         <option value="Saudi Arabia">Saudi Arabia</option> 
         <option value="Senegal">Senegal</option> 
         <option value="Serbia and Montenegro">Serbia and Montenegro</option> 
         <option value="Seychelles">Seychelles</option> 
         <option value="Sierra Leone">Sierra Leone</option> 
         <option value="Singapore">Singapore</option> 
         <option value="Slovakia">Slovakia</option> 
         <option value="Slovenia">Slovenia</option> 
         <option value="Solomon Islands">Solomon Islands</option> 
         <option value="Somalia">Somalia</option> 
         <option value="South Africa">South Africa</option> 
         <option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option> 
         <option value="Spain">Spain</option> 
         <option value="Sri Lanka">Sri Lanka</option> 
         <option value="Sudan">Sudan</option> 
         <option value="Suriname">Suriname</option> 
         <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option> 
         <option value="Swaziland">Swaziland</option> 
         <option value="Sweden">Sweden</option> 
         <option value="Switzerland">Switzerland</option> 
         <option value="Syrian Arab Republic">Syrian Arab Republic</option> 
         <option value="Taiwan, Province of China">Taiwan, Province of China</option> 
         <option value="Tajikistan">Tajikistan</option> 
         <option value="Tanzania, United Republic of">Tanzania, United Republic of</option> 
         <option value="Thailand">Thailand</option> 
         <option value="Timor-leste">Timor-leste</option> 
         <option value="Togo">Togo</option> 
         <option value="Tokelau">Tokelau</option> 
         <option value="Tonga">Tonga</option> 
         <option value="Trinidad and Tobago">Trinidad and Tobago</option> 
         <option value="Tunisia">Tunisia</option> 
         <option value="Turkey">Turkey</option> 
         <option value="Turkmenistan">Turkmenistan</option> 
         <option value="Turks and Caicos Islands">Turks and Caicos Islands</option> 
         <option value="Tuvalu">Tuvalu</option> 
         <option value="Uganda">Uganda</option> 
         <option value="Ukraine">Ukraine</option> 
         <option value="United Arab Emirates">United Arab Emirates</option> 
         <option value="United Kingdom">United Kingdom</option> 
         <option value="United States">United States</option> 
         <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option> 
         <option value="Uruguay">Uruguay</option> 
         <option value="Uzbekistan">Uzbekistan</option> 
         <option value="Vanuatu">Vanuatu</option> 
         <option value="Venezuela">Venezuela</option> 
         <option value="Viet Nam">Viet Nam</option> 
         <option value="Virgin Islands, British">Virgin Islands, British</option> 
         <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option> 
         <option value="Wallis and Futuna">Wallis and Futuna</option> 
         <option value="Western Sahara">Western Sahara</option> 
         <option value="Yemen">Yemen</option> 
         <option value="Zambia">Zambia</option> 
         <option value="Zimbabwe">Zimbabwe</option>
      </select></p>
      </div>
      <?php } ?>

      <?php if ($browser_type != "mobile") { ?>
      </td>
      <td valign="top">
      <?php } ?>

      <h3>Payment Information <?php if ($browser_type == "mobile") { echo "<br />"; } ?><small>Credit and Debit Cards Accepted</small></h3>

      <b class="payment-errors alert-error"></b>

      <p><label for="card-number">Card Number:</label>
      <input type="text" id="card-number" autocomplete="off" class="card-number input-large<?php if (isset($errors['card-number'])) { echo " error"; } ?>" placeholder="4200000000000000" value="<?php if ($StripeTaker_SaveFile_Data['mode'] == "Test") { echo "4242424242424242"; } ?>" /></p>

      <p><label for="card-expiry-month">Expiration:</label>
      <select id="card-expiry-month" autocomplete="off" class="card-expiry-month input-mini<?php if (isset($errors['card-expiry-month'])) { echo " error"; } ?>" placeholder="MM">
         <?php if (strtoupper($forminfo['company']) == "TESTMODE") { echo "<option value=\"12\" SELECTED>* 12</option>\n"; } ?>
         <option value="01">01</option>
         <option value="02">02</option>
         <option value="03">03</option>
         <option value="04">04</option>
         <option value="05">05</option>
         <option value="06">06</option>
         <option value="07">07</option>
         <option value="08">08</option>
         <option value="09">09</option>
         <option value="10">10</option>
         <option value="11">11</option>
         <option value="12"<?php if ($StripeTaker_SaveFile_Data['mode'] == "Test") { echo " SELECTED"; } ?>>12</option>
      </select> 

      <select id="card-expiry-year" autocomplete="off" class="card-expiry-year input-small<?php if (isset($errors['card-expiry-year'])) { echo " error"; } ?>" placeholder="YYYY">
         <option value="<?php echo date("Y"); ?>"<?php if ($StripeTaker_SaveFile_Data['mode'] == "Test") { echo " SELECTED"; } ?>><?php echo date("Y"); ?></option>
         <option value="<?php echo date("Y") + 1; ?>"><?php echo date("Y") + 1; ?></option>
         <option value="<?php echo date("Y") + 2; ?>"><?php echo date("Y") + 2; ?></option>
         <option value="<?php echo date("Y") + 3; ?>"><?php echo date("Y") + 3; ?></option>
         <option value="<?php echo date("Y") + 4; ?>"><?php echo date("Y") + 4; ?></option>
         <option value="<?php echo date("Y") + 5; ?>"><?php echo date("Y") + 5; ?></option>
         <option value="<?php echo date("Y") + 6; ?>"><?php echo date("Y") + 6; ?></option>
         <option value="<?php echo date("Y") + 7; ?>"><?php echo date("Y") + 7; ?></option>
         <option value="<?php echo date("Y") + 8; ?>"><?php echo date("Y") + 8; ?></option>
      </select></p>

      <p><label for="card-cvc">Card Verification Number:</label>
      <input type="text" autocomplete="off" class="card-cvc input-mini<?php if (isset($errors['card-cvc'])) { echo " error"; } ?>" placeholder="123" value="<?php if ($StripeTaker_SaveFile_Data['mode'] == "Test") { echo "123"; } ?>" /></p>

      <p><img src="img/cvv.png"></p>

      <p><div class="alert alert-info"><b>Please be patient.</b><br /> It can take up to a minute for the information to process.</div></p>
      <p><input type="submit" class="submit-button btn btn-primary btn-large" style="font-weight: bold;" value="Pay Now &raquo;"></p>

   <?php if ($browser_type != "mobile") { ?>
      </td></tr>
   </table>
   <?php } ?>

   </form><?php

   include("footers.inc.php");

}


function License_Check($licensekey,$localkey="") {
    $whmcsurl = "https://secure.x-mirror.com/erp/";
    $licensing_secret_key = "VGhpcyBpcyBhIHRlc3Qgb2YgdGhlIGVtZXJnZW5jeSBicm9hZGNhc3Qgc3lzdGVtLiBJZiB0aGlzIHdhcyBhIHJlYWwgdGVzdCwgdGhpcyBtZXNzYWdlIHdvdWxkIGJlIGZvbGxvd2VkIGJ5IGluc3RydWN0aW9ucy4="; # Unique value, should match what is set in the product configuration for MD5 Hash Verification
    $check_token = time().md5(mt_rand(1000000000,9999999999).$licensekey);
    $checkdate = date("Ymd"); # Current date
    $usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
    $localkeydays = 15; # How long the local key is valid for in between remote checks
    $allowcheckfaildays = 5; # How many days to allow after local key expiry before blocking access if connection cannot be made
    $localkeyvalid = false;
    if ($localkey) {
        $localkey = str_replace("\n",'',$localkey); # Remove the line breaks
		$localdata = substr($localkey,0,strlen($localkey)-32); # Extract License Data
		$md5hash = substr($localkey,strlen($localkey)-32); # Extract MD5 Hash
        if ($md5hash==md5($localdata.$licensing_secret_key)) {
            $localdata = strrev($localdata); # Reverse the string
    		$md5hash = substr($localdata,0,32); # Extract MD5 Hash
    		$localdata = substr($localdata,32); # Extract License Data
    		$localdata = base64_decode($localdata);
    		$localkeyresults = unserialize($localdata);
            $originalcheckdate = $localkeyresults["checkdate"];
            if ($md5hash==md5($originalcheckdate.$licensing_secret_key)) {
                $localexpiry = date("Ymd",mktime(0,0,0,date("m"),date("d")-$localkeydays,date("Y")));
                if ($originalcheckdate>$localexpiry) {
                    $localkeyvalid = true;
                    $results = $localkeyresults;
                    $validdomains = explode(",",$results["validdomain"]);
                    if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                    $validips = explode(",",$results["validip"]);
                    if (!in_array($usersip, $validips)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                    if ($results["validdirectory"]!=dirname(__FILE__)) {
                        $localkeyvalid = false;
                        $localkeyresults["status"] = "Invalid";
                        $results = array();
                    }
                }
            }
        }
    }
    if (!$localkeyvalid) {
        $postfields["licensekey"] = $licensekey;
        $postfields["domain"] = $_SERVER['SERVER_NAME'];
        $postfields["ip"] = $usersip;
        $postfields["dir"] = dirname(__FILE__);
        if ($check_token) $postfields["check_token"] = $check_token;
        if (function_exists("curl_exec")) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $whmcsurl."modules/servers/licensing/verify.php");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);
        } else {
            $fp = fsockopen($whmcsurl, 80, $errno, $errstr, 5);
	        if ($fp) {
        		$querystring = "";
                foreach ($postfields AS $k=>$v) {
                    $querystring .= "$k=".urlencode($v)."&";
                }
                $header="POST ".$whmcsurl."modules/servers/licensing/verify.php HTTP/1.0\r\n";
        		$header.="Host: ".$whmcsurl."\r\n";
        		$header.="Content-type: application/x-www-form-urlencoded\r\n";
        		$header.="Content-length: ".@strlen($querystring)."\r\n";
        		$header.="Connection: close\r\n\r\n";
        		$header.=$querystring;
        		$data="";
        		@stream_set_timeout($fp, 20);
        		@fputs($fp, $header);
        		$status = @socket_get_status($fp);
        		while (!@feof($fp)&&$status) {
        		    $data .= @fgets($fp, 1024);
        			$status = @socket_get_status($fp);
        		}
        		@fclose ($fp);
            }
        }
        if (!$data) {
            $localexpiry = date("Ymd",mktime(0,0,0,date("m"),date("d")-($localkeydays+$allowcheckfaildays),date("Y")));
            if ($originalcheckdate>$localexpiry) {
                $results = $localkeyresults;
            } else {
                $results["status"] = "Invalid";
                $results["description"] = "Remote Check Failed";
                return $results;
            }
        } else {
            preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
            $results = array();
            foreach ($matches[1] AS $k=>$v) {
                $results[$v] = $matches[2][$k];
            }
        }
        if ($results["md5hash"]) {
            if ($results["md5hash"]!=md5($licensing_secret_key.$check_token)) {
                $results["status"] = "Invalid";
                $results["description"] = "MD5 Checksum Verification Failed";
                return $results;
            }
        }
        if ($results["status"]=="Active") {
            $results["checkdate"] = $checkdate;
            $data_encoded = serialize($results);
            $data_encoded = base64_encode($data_encoded);
            $data_encoded = md5($checkdate.$licensing_secret_key).$data_encoded;
            $data_encoded = strrev($data_encoded);
            $data_encoded = $data_encoded.md5($data_encoded.$licensing_secret_key);
            $data_encoded = wordwrap($data_encoded,80,"\n",true);
            $results["localkey"] = $data_encoded;
        }
        $results["remotecheck"] = true;
    }
    unset($postfields,$data,$matches,$whmcsurl,$licensing_secret_key,$checkdate,$usersip,$localkeydays,$allowcheckfaildays,$md5hash);
    return $results;
}


function License_HandleResults($data) {
   global $StripeTaker_SaveFile_Data;

   // check product id's against list;
   $allowed_products[] = "135";
   $allowed_products[] = "137";

   if (!in_array($data['productid'], $allowed_products)) {

      $data['status'] = "Invalid";

   }

   if ($data['status'] == "Active") {

      if ($data['localkey']) {

         // Save Core1;
         $fhandle = fopen(STAKER_CORE, "w");
         fwrite($fhandle, $data['localkey']);
         fclose($fhandle);

         // Save Core2;
         $local_license_copy = $data;
         unset($local_license_copy['localkey']); // remove gibberish of Core1;

         // parse addons;
         if (isset($data['addons'])) {
            $addons_parse = explode("|", $data['addons']);

            foreach($addons_parse as $i) {

               $addons_tmp[] = explode(";", $i);

            }

            $addons_count = 0;

            foreach($addons_tmp as $j) {

               list($addon1, $addon2) = explode("=", $j[0]);
               $tmp_name = "addon_" . $addon2;
               $tmp_name = preg_replace("/ /","", $tmp_name);
               list($addon1, $addon2) = explode("=", $j[2]);
               $local_license_copy[$tmp_name] = $addon2;

               $addons_count++;

            }

            unset($local_license_copy['addons']);

         }

         // parse custom fields;
         if (isset($data['customfields'])) {
            $custom_parse = explode("|", $data['customfields']);

            foreach($custom_parse as $i) {

               $custom_tmp[] = explode(";", $i);

            }

            $custom_count = 0;

            foreach($custom_tmp as $j) {

               list($custom1, $custom2) = explode("=", $j[0]);
               $local_license_copy[$custom1] = $custom2;

               $custom_count++;

            }

            unset($local_license_copy['customfields']);

         }

         unset($local_license_copy['validdomain']);
         unset($local_license_copy['validip']);
         unset($local_license_copy['validdirectory']);
         unset($local_license_copy['md5hash']);
         unset($local_license_copy['remotecheck']);

         DataOps_SaveFile(STAKER_CORE2, $local_license_copy); // save Core2;

      }

   } else {

      include("headers.inc.php");

      ?>

      <div class="alert alert-info"><center>
         <h2><i class="icon-time icon-large" style="font-size: 1.3em;"></i> We're Currently Doing Maintenance</h2>
         <p>We're sorry, but we're currently doing maintenance on our system. Please bear with us.</p>
         <p>If you see this message for a long period of time, please email us (<?php echo $StripeTaker_SaveFile_Data['notify_cust']; ?>) so we can look into it.</p>
      </center></div>

      <?php

      include("footers.inc.php");
      die();

   }

}


function License_ReadCore1() {

   if (file_exists($file)) {

      // file exists, copy file data into buffer;
      $fhandle = fopen(STAKER_CORE, "rt");

      while(!feof($fhandle)) {

         $data .= fgets($fhandle, 8192);

      }

      fclose($fhandle);

      return $data;

   } else {

      return '';

   }

}

?>
