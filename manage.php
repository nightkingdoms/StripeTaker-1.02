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

   if (!CheckSession()) { header("Location:login.php"); die(); }

   $header_pagename = "Manage Your Store";

   switch($_REQUEST['op']) {

      case "Install":
      if (file_exists("install.php")) {
         include("install.php");
         Install_Run();
      }
      break;

      case "Orders":
      include("headers.inc.php");
      Manage_Menu();
      Orders_Main();
      include("footers.inc.php");
      break;

      case "Orders_View":
      $_REQUEST['op'] = "Orders";
      include("headers.inc.php");
      Manage_Menu();
      Orders_View();
      include("footers.inc.php");
      break;

      case "Orders_Invoice":
      $header_pagename = "Invoice";
      include("headers.inc.php");
      Orders_Invoice();
      include("footers.inc.php");
      break;

      case "Orders_Refund":
      $_REQUEST['op'] = "Orders";
      include("headers.inc.php");
      Manage_Menu();
      Orders_Refund();
      include("footers.inc.php");
      break;

      case "Orders_Refund_Process":
      $_REQUEST['op'] = "Orders";
      include("headers.inc.php");
      Manage_Menu();
      Orders_Refund_Process();
      include("footers.inc.php");
      break;

      case "Customers":
      include("headers.inc.php");
      Manage_Menu();
      Customers_Main();
      include("footers.inc.php");
      break;

      case "Customers_View":
      $_REQUEST['op'] = "Customers";
      include("headers.inc.php");
      Manage_Menu();
      Customers_View();
      include("footers.inc.php");
      break;

      case "Settings":
      include("headers.inc.php");
      Manage_Menu();
      Settings_Main();
      include("footers.inc.php");
      break;

      case "Settings_Process":
      Settings_Process();
      break;

      case "Templates":
      include("headers.inc.php");
      Manage_Menu();
      Templates_Main();
      include("footers.inc.php");
      break;

      case "Templates_Edit":
      $_REQUEST['op'] = "Templates";
      include("headers.inc.php");
      Manage_Menu();
      Templates_Edit($_REQUEST['tpl']);
      include("footers.inc.php");
      break;

      case "Templates_Save":
      Templates_Save($_REQUEST['tpl']);
      break;

      case "Products_Add":
      $_REQUEST['op'] = "Products";
      include("headers.inc.php");
      Manage_Menu();
      Products_Add();
      include("footers.inc.php");
      break;

      case "Products_Insert":
      Products_Insert();
      break;

      case "Products_Edit":
      $_REQUEST['op'] = "Products";
      include("headers.inc.php");
      Manage_Menu();
      Products_Edit();
      include("footers.inc.php");
      break;

      case "Products_Save":
      Products_Save();
      break;

      case "Products_Delete":
      $_REQUEST['op'] = "Products";
      include("headers.inc.php");
      Manage_Menu();
      Products_Delete();
      include("footers.inc.php");
      break;

      case "Products_DeleteOK":
      Products_DeleteOK();
      break;

      default:
      $_REQUEST['op'] = "Products";
      include("headers.inc.php");
      Manage_Menu();
      Products_Main();
      include("footers.inc.php");
      break;

   }


function Templates_Main() {

?>

<?php if (isset($_REQUEST['info'])) { ?>
<div class="alert alert-info"><?php echo $_REQUEST['info']; ?></div>
<?php } ?>

<div class="alert alert-info">
<ul><li>All emails include the header (top) and footer (bottom) for each message sent.</li>
<li>Notification templates will be added to the message as they are applicable.</li>
</div>

<h3>Header and Footer Templates</h3>
<div class="thumbnail" style="width: 325px; background: #eeeeee;">
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=header';" class="btn btn-large btn-block">Header</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=footer';" class="btn btn-large btn-block">Footer</button>
</div><br />

<h3>Customer Notification Templates</h3>
<div class="thumbnail" style="width: 325px; background: #eeeeee;">
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=cust_pay_one';" class="btn btn-large btn-block">Payment: One-Time</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=cust_pay_sub';" class="btn btn-large btn-block">Payment: Subscription</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=cust_dl';" class="btn btn-large btn-block">Download Instructions</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=cust_ship';" class="btn btn-large btn-block">Shipment Details</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=cust_refund';" class="btn btn-large btn-block">Payment Refund</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=cust_cancel';" class="btn btn-large btn-block">Cancelled Subscription</button>
</div><br />

<h3>Administrator Notification Emails</h3>
<div class="thumbnail" style="width: 325px; background: #eeeeee;">
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=admin_pay_one';" class="btn btn-large btn-block">Payment: One-Time</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=admin_pay_sub';" class="btn btn-large btn-block">Payment: Subscription</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=admin_dl';" class="btn btn-large btn-block">Downloaded Products</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=admin_ship';" class="btn btn-large btn-block">Shipping Details</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=admin_refund';" class="btn btn-large btn-block">Payment Refund</button>
<button onClick="javascript:location.href='manage.php?op=Templates_Edit&tpl=admin_cancel';" class="btn btn-large btn-block">Cancelled Subscription</button>
</div><br />
<?php

}


function Templates_Edit($template) {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $tpl_file = "tpls/" . $template . ".tpl";

   if (!file_exists($tpl_file)) {

      touch($tpl_file);

   }

      $fhandle = fopen($tpl_file, "rt");

      while(!feof($fhandle)) {

         $data .= fgets($fhandle, 8192);

      }

      fclose($fhandle);

      ?>

      <?php if ($browser_type != "mobile") { ?>
      <table width="85%" border="0">
      <tr><td width="40%" valign="top">
      <?php } ?>

      <form method="POST" action="manage.php?op=Templates_Save&tpl=<?php echo $template; ?>" id="edit_form">
      <h3>Editing Template File: <span class="muted"><?php echo $template; ?></span></h3>

      <p><textarea name="tpl_data" class="input-xxlarge" rows="20" cols="80"><?php echo $data; ?></textarea></p>

      <button class="btn btn-primary btn-large" onClick="javascript:document.forms['edit_form'].submit(); this.disabled=true;">Save Template</button>

      <?php if ($browser_type != "mobile") { ?>
      </td><td width="30%" valign="top">
      <?php } ?>

      <h3>Site Variables</h3>
      <ul>
         <li><b>{store_name}</b> - name of the store</li>
         <li><b>{store_email}</b> - store contact email address</li>
         <li><b>{store_url}</b> - base URL of this store</li>
      </ul>

      <h3>Order Variables</h3>
      <ul>
         <li><b>{order_id}</b> - ID of order</li>
         <li><b>{order_amount}</b> - total amount</li>
         <li><b>{order_currency}</b> - currency of order</li>
         <li><b>{order_fees}</b> - Stripe.com fees</li>
         <li><b>{order_type}</b> - One-Time/Subscription</li>
         <li><b>{order_datetime}</b> - YYYY-MM-DD HH:MM:SS</li>
         <li><b>{order_ip}</b> - customer IP address</li>
         <li><b>{order_agent}</b> - customer browser</li>
         <li><b>{stripe_charge}</b> - Stripe.com charge ID (opt.)</li>
         <li><b>{card_type}</b> - credit card type</li>
         <li><b>{card_finger}</b> - credit card fingerprint</li>
         <li><b>{card_exp}</b> - credit card expiration (MM/YY)</li>
         <li><b>{card_last4}</b> - last 4 of credit card number</li>
      </ul>

      <h3>Refund Variables</h3>
      <ul>
         <li><b>{amount}</b> - total order amount</li>
         <li><b>{refunded}</b> - amount already refunded</li>
         <li><b>{refund}</b> - amount of this refund</li>
         <li><i>All customer variables available as well.</i></li>
      </ul>

      <?php if ($browser_type != "mobile") { ?>
      </td><td width="30%" valign="top">
      <?php } ?>

      <h3>Product Variables</h3>
      <ul>
         <li><b>{product_id}</b> - ID of product</li>
         <li><b>{product_name}</b> - name of product</li>
         <li><b>{product_recurs}</b> - how often charge occurs</li>
         <li><b>{download_url}</b> - product download URL</li>
      </ul>

      <h3>Customer Variables</h3>
      <ul>
         <li><b>{cust_id}</b> - customer ID</li>
         <li><b>{cust_stripe}</b> - Stripe.com customer ID</li>
         <li><b>{cust_name}</b> - customer name</li>
         <li><b>{cust_email}</b> - customer email address</li>
         <li><b>{cust_phone}</b> - customer phone number</li>
         <li><b>{bill_street}</b> - billing street address, line 1</li>
         <li><b>{bill_street2}</b> - billing street address, line 2</li>
         <li><b>{bill_city}</b> - billing city</li>
         <li><b>{bill_state}</b> - billing state/province</li>
         <li><b>{bill_postal}</b> - billing postal code</li>
         <li><b>{bill_country}</b> - billing country</li>
         <li><b>{ship_same}</b> - whether shipping address same as billing</li>
         <li><b>{ship_street}</b> - shipping street address, line 1</li>
         <li><b>{ship_streeet2}</b> - shipping street address, line 2</li>
         <li><b>{ship_city}</b> - shipping city</li>
         <li><b>{ship_state}</b> - shipping state/province</li>
         <li><b>{ship_postal}</b> - shipping postal code</li>
         <li><b>{ship_country}</b> - shipping country</li>
      </ul>

      <?php if ($browser_type != "mobile") { ?>
      </td></tr>
      </table><br />
      <?php } ?>

      <?php

}


function Templates_Save($template) {

   $data = $_REQUEST['tpl_data'];
   $tpl_file = "tpls/" . $template . ".tpl";

   $fhandle = fopen($tpl_file, "w");
   fwrite($fhandle, $data);
   fclose($fhandle);

   header("Location:manage.php?op=Templates&info=<b>Template saved successfully.</b>");
   die();

}


function Settings_Process() {
   global $StripeTaker_SaveFile_Data;

   $forminfo['storename'] = $_REQUEST['storename'];
   $forminfo['notify_admin'] = $_REQUEST['notify_admin'];
   $forminfo['notify_cust'] = $_REQUEST['notify_cust'];
   $forminfo['store_ph'] = $_REQUEST['store_ph'];
   $forminfo['store_addr1'] = $_REQUEST['store_addr1'];
   $forminfo['store_addr2'] = $_REQUEST['store_addr2'];
   $forminfo['store_city'] = $_REQUEST['store_city'];
   $forminfo['store_state'] = $_REQUEST['store_state'];
   $forminfo['store_postal'] = $_REQUEST['store_postal'];
   $forminfo['store_country'] = $_REQUEST['store_country'];
   $forminfo['password'] = $_REQUEST['password'];
   $forminfo['serial'] = $_REQUEST['serial'];
   $forminfo['currency'] = $_REQUEST['currency'];
   $forminfo['mode'] = $_REQUEST['mode'];
   $forminfo['key_test_s'] = $_REQUEST['key_test_s'];
   $forminfo['key_test_p'] = $_REQUEST['key_test_p'];
   $forminfo['key_live_s'] = $_REQUEST['key_live_s'];
   $forminfo['key_live_p'] = $_REQUEST['key_live_p'];
   $logout = false;

   if ($forminfo['password'] != $StripeTaker_SaveFile_Data['password']) { $logout = true; }

   // check for errors;
   $err_count = 0;
   $errors = array();
   $errors['alert-msg'] = "<b>There were some problems with what you entered:</b>\n<ul>\n";

if ($StripeTaker_SaveFile_Data['demo_mode'] == "Yes") { $err_count++; $errors['alert-msg'] .= "<li><b style=\"color: #0000AA;\">You cannot save settings in Demo Mode.</b></li>"; } else {
   if (!Forms_CheckValidEmail($forminfo['notify_admin'])) { $err_count++; $errors['notify_admin'] = ""; $errors['alert-msg'] .= "<li>The email address to notify the administrator is invalid. Please re-enter it.</li>"; }
   if (!Forms_CheckValidEmail($forminfo['notify_cust'])) { $err_count++; $errors['notify_cust'] = ""; $errors['alert-msg'] .= "<li>The email address to notify the customer is invalid. Please re-enter it.</li>"; }
   if (mb_strlen($forminfo['storename']) < 3) { $err_count++; $errors['storename'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid store name.</li>"; }
   if (mb_strlen($forminfo['password']) < 8) { $err_count++; $errors['password'] = ""; $errors['alert-msg'] .= "<li>Your password must be at least 8 characters long.</li>"; }
   if (mb_strlen($forminfo['serial']) < 25) { $err_count++; $errors['serial'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid serial number.</li>"; }
   if ($forminfo['currency'] != "USD" && $forminfo['currency'] != "CAN") { $err_count++; $errors['currency'] = ""; $error['alert-msg'] .= "<li>You must choose a valid currency.</li>"; }
   if ($forminfo['mode'] != "Test" && $forminfo['mode'] != "Live") { $err_count++; $errors['mode'] = ""; $error['alert-msg'] .= "<li>You must choose a valid mode.</li>"; }
   if (mb_strlen($forminfo['key_test_s']) < 10 || substr($forminfo['key_test_s'], 0, 3) != "sk_") { $err_count++; $errors['key_test_s'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid Test Secret Key.</li>"; }
   if (mb_strlen($forminfo['key_test_p']) < 10 || substr($forminfo['key_test_p'], 0, 3) != "pk_") { $err_count++; $errors['key_test_p'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid Test Publishable Key.</li>"; }
   if (mb_strlen($forminfo['key_live_s']) < 10 || substr($forminfo['key_live_s'], 0, 3) != "sk_") { $err_count++; $errors['key_live_s'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid Live Secret Key.</li>"; }
   if (mb_strlen($forminfo['key_live_p']) < 10 || substr($forminfo['key_live_p'], 0, 3) != "pk_") { $err_count++; $errors['key_live_p'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid Live Publishable Key.</li>"; }
}

   // finish error checking;
   if ($err_count > 0) {

      $errors['alert-msg'] .= "\n</ul>";
      include("headers.inc.php");
      Manage_Menu();
      Settings_Form($forminfo, $errors);
      include("footers.inc.php");
      die();

   } else {

      unset($errors);

      if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") { DataOps_SaveFile(STAKER_DATA, $forminfo); }

      if ($logout === true) {

         // login initial user;
         KillSession();

         // redirect to login page;
         header("Location:login.php?info=<b>Your password has changed, please login with the new password.</b>");

      } else {

         // redirect to management page;
         header("Location:manage.php?op=Settings&info=<b>Settings were saved successfully.</b>");

      }

      die();

   }

}


function Settings_Main() {
   global $StripeTaker_SaveFile_Data;

   Settings_Form($StripeTaker_SaveFile_Data);

}


function Settings_Form($forminfo, $errors=0) {
   global $StripeTaker_SaveFile_Data, $browser_type;

?>

   <form method="POST" action="manage.php?op=Settings_Process" id="settings_form">


   <?php if (isset($errors['alert-msg'])) { ?>
   <div class="alert alert-error"><?php echo $errors['alert-msg']; ?></div>
   <?php } ?>

   <?php if (isset($_REQUEST['info'])) { ?>
   <div class="alert alert-info"><?php echo $_REQUEST['info']; ?></div>
   <?php } ?>

   <?php if ($browser_type != "mobile") { ?>
   <table width="85%" border="0">
   <tr><td width="50%" valign="top">
   <?php } ?>

   <h3>Tell Us About This Online Store</h3>
   <p><label for="storename"><b>Store Name:</b></label>
   <input type="text" name="storename" id="storename" class="input-xlarge<?php if (isset($errors['storename'])) { echo " error"; } ?>" placeholder="My Store" value="<?php if (mb_strlen($forminfo['storename']) > 3) { echo $forminfo['storename']; } ?>" /></p>

   <p><label for="notify_admin"><b>What email address do we send administrative notifications to?</b></label>
   <input type="text" name="notify_admin" id="notify_admin" class="input-xlarge<?php if (isset($errors['notify_admin'])) { echo " error"; } ?>" placeholder="admin@mystore.com" value="<?php if (mb_strlen($forminfo['notify_admin']) > 1) { echo $forminfo['notify_admin']; } ?>" /></p>

   <p><label for="notify_cust"><b>What email address do we use for notifying the customer?</b></label>
   <input type="text" name="notify_cust" id="notify_cust" class="input-xlarge<?php if (isset($errors['notify_cust'])) { echo " error"; } ?>" placeholder="help@mystore.com" value="<?php if (mb_strlen($forminfo['notify_cust']) > 1) { echo $forminfo['notify_cust']; } ?>" /></p>

   <h3>For Printable Receipts <small>(Optional)</small></h3>
   <p><label for="store_ph"><b>Phone Number:</b></label>
   <input type="text" name="store_ph" id="store_ph" class="input-medium<?php if (isset($errors['store_ph'])) { echo " error"; } ?>" placeholder="212-000-1234" value="<?php if (mb_strlen($forminfo['store_ph']) > 0) { echo $forminfo['store_ph']; } ?>" /></p>

   <p><label for="store_addr1"><b>Street Address:</b></label>
   <input type="text" name="store_addr1" id="store_addr1" class="input-xlarge<?php if (isset($errors['store_addr1'])) { echo " error"; } ?>" placeholder="123 Happy St" value="<?php if (mb_strlen($forminfo['store_addr1']) > 0) { echo $forminfo['store_addr1']; } ?>" /><br />

   <input type="text" name="store_addr2" id="store_addr2" class="input-xlarge<?php if (isset($errors['store_addr2'])) { echo " error"; } ?>" placeholder="Suite 4321" value="<?php if (mb_strlen($forminfo['store_addr2']) > 0) { echo $forminfo['store_addr2']; } ?>" /></p>

   <p><label for="store_city"><b>City:</b></label>
   <input type="text" name="store_city" id="store_city" class="input-large<?php if (isset($errors['store_city'])) { echo " error"; } ?>" placeholder="Happyville" value="<?php if (mb_strlen($forminfo['store_city']) > 0) { echo $forminfo['store_city']; } ?>" /></p>

   <p><label for="store_state"><b>State/Province:</b></label>
   <input type="text" name="store_state" id="store_state" class="input-large<?php if (isset($errors['store_state'])) { echo " error"; } ?>" placeholder="MyState" value="<?php if (mb_strlen($forminfo['store_state']) > 0) { echo $forminfo['store_state']; } ?>" /></p>

   <p><label for="store_postal"><b>Postal Code:</b></label>
   <input type="text" name="store_postal" id="store_postal" class="input-medium<?php if (isset($errors['store_postal'])) { echo " error"; } ?>" placeholder="12345" value="<?php if (mb_strlen($forminfo['store_postal']) > 0) { echo $forminfo['store_postal']; } ?>" /></p>

   <p><label for="store_country">Country:</label>
   <select name="store_country" id="store_country" class="input-large<?php if (isset($errors['store_country'])) { echo " error"; } ?>"> 
      <option value="<?php echo $forminfo['store_country']; ?>" SELECTED>* <?php echo $forminfo['store_country']; ?></option>
      <option value="United States">United States</option> 
      <option value="Canada">Canada</option> 
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

   <?php if ($browser_type != "mobile") { ?>
   </td><td valign="top">
   <?php } ?>

   <h3>Other Stuff</h3>
   <p><label for="password"><b>Choose a password to use to manage your store:</b></label>
   <?php if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") { ?>
   <input type="password" name="password" id="password" class="input-large<?php if (isset($errors['password'])) { echo " error"; } ?>" placeholder="MySecretPassword" value="<?php if (mb_strlen($forminfo['password']) > 0) { echo $forminfo['password']; } ?>" /></p>
   <?php } else { ?>
   <p id="password"><i>Can't change it while in demo mode!</i></p>
   <?php } ?>

   <p><label for="serial"><b>Put your serial number here:</b></label>
   <?php if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") { ?>
   <input type="text" name="serial" id="serial" class="input-xlarge<?php if (isset($errors['serial'])) { echo " error"; } ?>" placeholder="StripeTaker-XXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['serial']) > 0) { echo $forminfo['serial']; } else { echo "StripeTaker-XXXXXXXXXXXXXXXXXXXX"; } ?>" /></p>
   <?php } else { ?>
   <p id="serial"><i>Can't change it while in demo mode!</i></p>
   <?php } ?>


   <p><label for="currency"><b>Choose your currency:</b></label>
   <select name="currency" id="currency" <?php if (isset($errors['currency'])) { echo "class=\"error\""; } ?>>
   <option value="USD"<?php if ($forminfo['currency'] == "USD") { echo " SELECTED"; } ?>><?php if ($forminfo['currency'] == "USD") { echo "* "; } ?>United States Dollars (USD)</option>
   <option value="CAN"<?php if ($forminfo['currency'] == "CAN") { echo " SELECTED"; } ?>><?php if ($forminfo['currency'] == "CAN") { echo " SELECTED"; } ?>Canadian Dollars (CAN)</option>
   <option value="GBP"<?php if ($forminfo['currency'] == "GBP") { echo " SELECTED"; } ?>><?php if ($forminfo['currency'] == "GBP") { echo " SELECTED"; } ?>British Pound Sterline (GBP)</option>
   <option value="EUR"<?php if ($forminfo['currency'] == "EUR") { echo " SELECTED"; } ?>><?php if ($forminfo['currency'] == "EUR") { echo " SELECTED"; } ?>Euros (EUR)</option>
   <option value="AUD"<?php if ($forminfo['currency'] == "AUD") { echo " SELECTED"; } ?>><?php if ($forminfo['currency'] == "AUD") { echo " SELECTED"; } ?>Australian Dollars (AUD)</option>
   </select></p>

   <p><label for="mode"><b>Is this store LIVE or in TEST mode?</b></label>
   <?php if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") { ?>
   <select name="mode" id="mode" <?php if (isset($errors['mode'])) { echo "class=\"error\""; } ?>>
   <?php if (isset($forminfo['mode'])) { ?><option value="<?php echo $forminfo['mode']; ?>" SELECTED>* <?php echo $forminfo['mode']; ?></option><?php } ?>
   <option value="Test">Test</option>
   <option value="Live">Live</option>
   </select></p>
   <?php } else { ?>
   <p id="mode"><i>We have to stay in <b>Test</b> mode while demo'ing.</i></p>
   <?php } ?>

   <h3>Stripe.com Information</h3>
   <p><label for="key_test_s"><b>Test Secret Key</b></label>
   <?php if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") { ?>
   <input type="text" name="key_test_s" id="key_test_s" class="input-xlarge<?php if (isset($errors['key_test_s'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['key_test_s']) > 0) { echo $forminfo['key_test_s']; } ?>" /></p>
   <?php } else { ?>
   <p id="key_test_s"><i>Can't change this while in demo mode!</i></p>
   <?php } ?>


   <p><label for="key_test_p"><b>Test Publishable Key</b></label>
   <?php if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") { ?>
   <input type="text" name="key_test_p" id="key_test_p" class="input-xlarge<?php if (isset($errors['key_test_p'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['key_test_p']) > 0) { echo $forminfo['key_test_p']; } ?>" /></p>
   <?php } else { ?>
   <p id="key_test_p"><i>Can't change this while in demo mode!</i></p>
   <?php } ?>

   <p><label for="key_live_s"><b>Live Secret Key</b></label>
   <?php if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") { ?>
   <input type="text" name="key_live_s" id="key_live_s" class="input-xlarge<?php if (isset($errors['key_live_s'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['key_live_s']) > 0) { echo $forminfo['key_live_s']; } ?>" /></p>
   <?php } else { ?>
   <p id="key_live_s"><i>Can't change this while in demo mode!</i></p>
   <?php } ?>

   <p><label for="key_live_p"><b>Live Publishable Key</b></label>
   <?php if ($StripeTaker_SaveFile_Data['demo_mode'] != "Yes") { ?>
   <input type="text" name="key_live_p" id="key_live_p" class="input-xlarge<?php if (isset($errors['key_live_p'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['key_live_p']) > 0) { echo $forminfo['key_live_p']; } ?>" /></p>
   <?php } else { ?>
   <p id="key_live_p"><i>Can't change this while in demo mode!</i></p>
   <?php } ?>

   <p><button class="submit-button btn btn-primary btn-large" style="font-weight: bold;" onClick="javascript:document.forms['settings_form'].submit(); this.disabled=true;">Save Settings</button></p>

   <?php if ($browser_type != "mobile") { ?>
   </td></tr>
   </table>
   <?php } ?>

   </form>

<?php

}


function Customers_Main() {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $cust_data = array_reverse(DataOps_ReadFile(STAKER_CUSTS));

   ?><h3>Manage Customers</h3>
   <table width="85%" border="0" class="table table-striped">
      <thead>
         <tr>
            <th>ID #</th>
            <th>Name</th>
            <?php if ($browser_type != "mobile") { ?><th>Email</th><?php } ?>
            <?php if ($browser_type != "mobile") { ?><th>Phone</th><?php } ?>
            <th>Actions</th>
         </tr>
      </thead>

      <tbody>
         <?php

   if (count($cust_data) > 0) {

      foreach ($cust_data as $key => $value) {

         ?>
         <tr>
            <td><?php echo $value['id']; ?></td>
            <td><?php echo $value['name']; ?></td>
            <?php if ($browser_type != "mobile") { ?>
            <td><?php echo $value['email']; ?></td>
            <?php } ?>
            <?php if ($browser_type != "mobile") { ?>
            <td><?php echo $value['phone']; ?></td>
            <?php } ?>
            <td>
               <button class="btn btn-primary" onClick="javascript:location.href='manage.php?op=Customers_View&id=<?php echo $value['id']; ?>';">View</button>
            </td>
         </tr><?php

      }

   } else {

      ?><td colspan="<?php if ($browser_type != "mobile") { echo "5"; } else { echo "3"; } ?>" align="center"><b>No orders found.</b></td><?php

   }

         ?>
      <tbody>
   </table>

   <?php

}


function Customers_View() {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $cust_data = DataOps_Data_Get(STAKER_CUSTS, $_REQUEST['id']);

   if (count($cust_data) > 0) {

      ?><h3>Customer # <?php echo $cust_data['id']; ?></h3>

      <?php if ($browser_type != "mobile") { ?>
      <table width="85%" border="0">
      <tr><td width="50%" valign="top">
      <?php } ?>

      <p><i class="icon-cloud" style="font-weight: bold;"></i> <b style="color: #0088cc;">Stripe.com ID</b><br />
      <?php echo $cust_data['stripe']; ?></p><br />

      <p><i class="icon-phone" style="font-weight: bold;"></i> <b style="color: #0088cc;">Contact Information</b><br />
      Name: <?php echo $cust_data['name']; ?><br />
      Email: <?php echo $cust_data['email']; ?><br />
      Phone: <?php echo $cust_data['phone']; ?></p><br />

      <?php if ($browser_type != "mobile") { ?>
      </td><td valign="top">
      <?php } ?>

      <p><i class="icon-home" style="font-weight: bold;"></i> <b style="color: #0088cc;">Billing Address</b><br />
      <?php echo $cust_data['bill_street']; ?><br />
      <?php if (mb_strlen($cust_data['bill_extra']) > 0) { echo $cust_data['bill_extra'] . "<br />\n"; } ?>
      <?php echo $cust_data['bill_city']; ?>, 
      <?php echo $cust_data['bill_state']; ?>&nbsp; 
      <?php echo $cust_data['bill_postal']; ?><br />
      <?php echo $cust_data['bill_country']; ?></p><br />

      <p><i class="icon-truck" style="font-weight: bold;"></i> <b style="color: #0088cc;">Shipping Address</b><br />
      <?php if ($cust_data['ship_same'] == "Yes") {

         ?><i>Same as billing address.</i></p><br /><?php

      } else {

         if (mb_strlen($cust_data['ship_street']) > 0) {

            ?><?php echo $cust_data['ship_street']; ?><br />
            <?php if (mb_strlen($cust_data['ship_extra']) > 0) { echo $cust_data['ship_extra'] . "<br />\n"; } ?>
            <?php echo $cust_data['ship_city']; ?>, 
            <?php echo $cust_data['ship_state']; ?>&nbsp; 
            <?php echo $cust_data['ship_postal']; ?><br />
            <?php echo $cust_data['country']; ?></p><br /><?php

         } else {

            ?><i>None entered or required.</i><?php

         }

      }

      if ($browser_type != "mobile") {
      ?></td></tr>
      </table><br /><?php
      }


      // order information;
      $order_data = DataOps_ReadFile(STAKER_ORDERS);
      $cust_orders = array();

      foreach($order_data as $key => $value) {

         if (array_search($cust_data['id'], $value)) {

            $cust_orders[] = $value;

         }

      }

      ?><h3>Customer's Orders</h3>

      <table width="85%" border="0" class="table table-striped">
         <thead>
            <th>Order #</th>
            <th>Total Price</th>
            <?php if ($browser_type != "mobile") { ?><th>Fees</th><?php } ?>
            <?php if ($browser_type != "mobile") { ?><th>Ordered On</th><?php } ?>
            <th>Actions</th>
         </thead>
         <tbody>
         <?php

            if (count($cust_orders) > 0) {

               foreach ($cust_orders as $key => $value) {

                  ?><tr>
                     <td><?php echo $value['id']; ?></td>
                     <td>$<?php echo $value['amount']; ?> <?php $StripeTaker_SaveFile_Data['currency']; ?></td>
                     <?php if ($browser_type != "mobile") { ?><td>$<?php echo $value['fees']; ?> <?php $StripeTaker_SaveFile_Data['currency']; ?></td><?php } ?>
                     <?php if ($browser_type != "mobile") { ?><td><?php echo date("Y-m-d H:i:s", $value['ordered']); ?></td><?php } ?>
                     <td>
                        <button class="btn" onClick="javascript:location.href='manage.php?op=Orders_View&id=<?php echo $value['id']; ?>';">View</button>
                     </td>
                  </tr><?php

               }

            } else {

               ?><tr><td colspan="<?php if ($browser_type != "mobile") { echo "5"; } else { echo "3"; } ?>" align="center">No orders found.</td></tr><?php

            }

         ?>
         </tbody>
      </table>

      <?php


      // list all charges for customer;
      $stripe_charges = StripeInterface_ChargeList($cust_data['stripe'], 20);

      ?><h3><i class="icon-cloud icon-large"></i> Stripe.com Charges</h3>

      <table width="85%" border="0" class="table table-striped">
         <thead>
            <th>Charge #</th>
            <th>Amount</th>
            <?php if ($browser_type != "mobile") { ?><th>Charged On</th><?php } ?>
            <th>Actions</th>
         </thead>
         <tbody>
         <?php

            if (count($stripe_charges) > 0) {

               foreach ($stripe_charges as $key => $value) {

                  ?><tr>
                     <td><?php echo $value['id']; ?></td>
                     <td>$<?php echo number_format(($value['amount'] / 100), 2, ".", ""); ?> <?php $StripeTaker_SaveFile_Data['currency']; ?></td>
                     <?php if ($browser_type != "mobile") { ?><td><?php echo date("Y-m-d H:i:s", $value['created']); ?></td><?php } ?>
                     <td>
                        <button class="btn" onClick="javascript:location.href='manage.php?op=Orders_Refund&id=<?php echo $value['id']; ?>&cust=<?php echo $cust_data['id']; ?>';">Refund</button>
                     </td>
                  </tr><?php

               }

            } else {

               ?><tr><td colspan="<?php if ($browser_type != "mobile") { echo "4"; } else { echo "3"; } ?>" align="center">No charges found.</td></tr><?php

            }

         ?>
         </tbody>
      </table>

      <?php

   } else {

      ?><h3>We're Sorry</h3>
      We weren't able to find that customer.<?php

   }

}


function Orders_Refund() {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $charge_data = StripeInterface_ChargeItem($_REQUEST['id']);

   if ($charge_data['status'] != "fail") {

      $amount = number_format(($charge_data['amount'] / 100), 2, ".", "");
      if ($charge_data['amount_refunded'] == "0") { $refunded = "0.00"; } else { $refunded = number_format(($charge_data['amount_refunded'] / 100), 2, ".", ""); }
      $amount_left = number_format(($amount - $refunded), 2, ".", "");

      if (isset($_REQUEST['error'])) { ?>
      <div class="alert alert-error"><?php echo $_REQUEST['error']; ?></div>
      <?php } ?>

      <?php if (isset($_REQUEST['info'])) { ?>
      <div class="alert alert-info"><?php echo $_REQUEST['info']; ?></div>
      <?php } ?>

      <h3><i class="icon-cloud"></i> Stripe.com Charge # <?php echo $charge_data['id']; ?></h3>

      <table width="<?php if ($browser_type != "mobile") { echo "500"; } else { echo "250"; } ?>" border="0" align="center" class="table-striped table-bordered">
         <tr>
            <td <?php if ($browser_type != "mobile") { echo "width=\"33%\""; } ?> align="center">
               <h1>$<?php echo $amount; ?><br />
               <small>Total Invoice</small></h1>
            </td>
         <?php if ($browser_type != "desktop") { ?></tr><tr><?php } ?>
            <td <?php if ($browser_type != "mobile") { echo "width=\"33%\""; } ?> align="center">
               <h1>$<?php echo $refunded; ?><br />
               <small>All Refunded</small></h1>
            </td>
         <?php if ($browser_type != "desktop") { ?></tr><tr><?php } ?>
            <td <?php if ($browser_type != "mobile") { echo "width=\"33%\""; } ?> align="center">
               <h1>$<?php echo $amount_left; ?></br />
               <small>Amount Left</small></h1>
            </td>
         </tr>
      </table><br /><?php

      ?><form method="POST" action="manage.php?op=Orders_Refund_Process&id=<?php echo $_REQUEST['id']; ?>" id="refund_form">
      <input type="hidden" name="cust" value="<?php echo $_REQUEST['cust']; ?>">
      <input type="hidden" name="curr_amt" value="<?php echo $amount; ?>">
      <input type="hidden" name="curr_refund" value="<?php echo $refunded; ?>">
      <table width="<?php if ($browser_type != "mobile") { echo "500"; } else { echo "250"; } ?>" border="0" align="center">
         <tr>
            <td align="center">
               <p class="form-inline"><label for="refund_amt">Amount To Refund:</label>
               <div class="input-prepend input-append"><span class="add-on">$</span>








               <input type="text" name="refund_amt" id="refund_amt" class="input-medium" placeholder="<?php echo $amount_left; ?>" value="<?php echo $amount_left; ?>" />
               <span class="add-on"><?php echo$StripeTaker_SaveFile_Data['currency']; ?></span></div></p><br />
            </td>
         </tr>
         <tr>
            <td align="center">
               <button class="btn btn-danger btn-large" onClick="javascript:document.forms['refund_form'].submit(); this.disabled=true;">Refund</button>
            </td>
         </tr>
      </table>
      </form><br />

      <?php

   } else {

      ?><h3>We're Sorry</h3>
      We weren't able to find the charge you were looking for.<?php

   }   

}


function Orders_Refund_Process() {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $cust_id = $_REQUEST['cust'];
   $curr_amt = $_REQUEST['curr_amt'];
   $curr_amt = number_format($curr_amt, 2, ".", "");
   $curr_refund = $_REQUEST['curr_refund'];
   $curr_refund = number_format($curr_refund, 2, ".", "");
   $curr_diff = $curr_amt - $curr_refund;
   $curr_diff = number_format($curr_diff, 2, ".", "");
   $curr_diff_prep = $curr_diff * 100;
   $this_refund = $_REQUEST['refund_amt'];
   $this_refund = number_format($this_refund, 2, ".", "");
   $this_refund_prep = $this_refund * 100;

   if ($this_refund_prep <= $curr_diff_prep) {

      $stripe_info = StripeInterface_Refund($this_refund, $_REQUEST['id']);

      if ($stripe_info['status'] != "fail") {

         $result = "info=<b>Your refund was successfully processed.</b>";

      } else {

         $result = "error=<b>There was a problem with the request.</b><ul><li>" . $stripe_info['msg'] . "</i></ul>";

      }

   } else {

      $result = "error=<b>The amount you entered was more than is available to refund.</b>";

   }

   header("Location:manage.php?op=Orders_Refund&id=" . $_REQUEST['id'] . "&" . $result);

   $temp_vars['order_id'] = $_REQUEST['id'];
   $temp_vars['amount'] = $curr_amt;
   $temp_vars['refunded'] = $curr_refund;
   $temp_vars['refund'] = $this_refund;

   $cust_data = DataOps_Data_Get(STAKER_CUSTS, $cust_id);
   $temp_vars['id'] = $cust_data['id'];
   $temp_vars['stripe'] = $cust_data['stripe'];
   $temp_vars['name'] = $cust_data['name'];
   $temp_vars['email'] = $cust_data['email'];
   $temp_vars['phone'] = $cust_data['phone'];
   $temp_vars['bill_street'] = $cust_data['bill_street'];
   $temp_vars['bill_street2'] = $cust_data['bill_extra'];
   $temp_vars['bill_city'] = $cust_data['bill_city'];
   $temp_vars['bill_state'] = $cust_data['bill_state'];
   $temp_vars['bill_postal'] = $cust_data['bill_postal'];
   $temp_vars['bill_country'] = $cust_data['bill_country'];
   $temp_vars['ship_same'] = $cust_data['ship_same'];
   $temp_vars['ship_street'] = $cust_data['ship_street'];
   $temp_vars['ship_street2'] = $cust_data['ship_extra'];
   $temp_vars['ship_city'] = $cust_data['ship_city'];
   $temp_vars['ship_state'] = $cust_data['ship_state'];
   $temp_vars['ship_postal'] = $cust_data['ship_postal'];
   $temp_vars['ship_country'] = $cust_data['ship_country'];

   Templater_Refund("cust", $temp_vars);
   Templater_Refund("admin", $temp_vars);
   die();

}


function Orders_Invoice() {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $order_data = DataOps_Data_Get(STAKER_ORDERS, $_REQUEST['id']);
   $cust_data = DataOps_Data_Get(STAKER_CUSTS, $_REQUEST['customer']);

   ?><table width="100%" border="0" class="table table-striped" align="center">
      <tbody>
         <tr>
            <td width="50%">
               <b>Invoiced To</b><br />
               <?php echo $cust_data['name']; ?><br />
               <?php if (mb_strlen($cust_data['company']) > 0) { echo $cust_data['company'] . "<br />\n"; } ?>
               <?php echo $cust_data['bill_street']; ?><br />
               <?php if (mb_strlen($cust_data['bill_extra']) > 0) { echo $cust_data['bill_extra'] . "<br />\n"; } ?>
               <?php echo $cust_data['bill_city']; ?>, <?php echo $cust_data['bill_state']; ?><br />
               <?php echo $cust_data['bill_postal']; ?><br />
               <?php echo $cust_data['bill_country']; ?><br />
               <?php echo $cust_data['phone']; ?>
            </td>
            <td width="50%">
               <b>Pay To</b><br />
               <?php echo $StripeTaker_SaveFile_Data['storename']; ?><br />
               <?php echo $StripeTaker_SaveFile_Data['store_addr1']; ?><br />
               <?php if (mb_strlen($StripeTaker_SaveFile_Data['store_addr2']) > 0) { echo $StripeTaker_SaveFile_Data['store_addr2'] . "<br />"; } ?>
               <?php echo $StripeTaker_SaveFile_Data['store_city']; ?>, <?php echo $StripeTaker_SaveFile_Data['store_state']; ?><br />
               <?php echo $StripeTaker_SaveFile_Data['store_postal']; ?><br />
               <?php echo $StripeTaker_SaveFile_Data['store_country']; ?><br />
               <?php echo $StripeTaker_SaveFile_Data['store_ph']; ?>
            </td>
         </tr>
      </tbody>
   </table><br />

   <table width="100%" border="0" class="table" align="center">
      <tbody>
         <tr>
            <td width="50%">
                  <h4 style="margin-bottom: -2px;">Invoice #<?php echo $order_data['id']; ?></h4>
                  Invoice Date: <?php echo date("m/d/Y H:i", $order_data['ordered']); ?><br />
                  Due Date: <?php echo date("m/d/Y H:i", $order_data['ordered']); ?><p style="padding-bottom: 20px;" />
            </td>
            <td width="50%" style="text-align: center;">
               <h1 class="brand" style="color: #468847; font-size: 3em;">PAID</h1>
            </td>
         </tr>
      </tbody>
   </table>

   <table width="100%" border="0" class="table table-striped table-bordered" align="center">
      <thead>
         <tr>
            <td width="80%"><b>Description</b></td>
            <td width="20%"><b>Amount</b></td>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td><?php echo $order_data['product_name']; ?> (# <?php echo $order_data['product_id']; ?>)</td>
            <td><span class="pull-right">$<?php echo $order_data['amount']; ?> <?php echo $StripeTaker_SaveFile_Data['currency']; ?></span></td>
         </tr>
         <tr>
            <td><span class="pull-right"><b>Total:</b></span></td>
            <td><span class="pull-right"><b>$<?php echo $order_data['amount']; ?> <?php echo $StripeTaker_SaveFile_Data['currency']; ?></span></td>
         </tr>
      </tbody>
   </table>

   <h4>Transactions</h4>
   <table width="100%" border="0" class="table table-striped table-bordered" style="background: #ffffff;" align="center">
      <thead>
         <tr>
            <td width="25%" style="text-align: center;"><b>Transaction Date</b></td>
            <td width="25%" style="text-align: center;"><b>Gateway</b></td>
            <td width="25%" style="text-align: center;"><b>Transaction ID</b></td>
            <td width="25%" style="text-align: center;"><b>Amount</b></td>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td style="text-align: center;"><?php echo date("m/d/Y", $order_data['ordered']); ?></td>
            <td style="text-align: center;">Stripe</td>
            <td style="text-align: center;"><?php if (mb_strlen($order_data['charge']) > 1) { echo $order_data['charge']; } else { echo "-----"; } ?></td>
            <td style="text-align: center;">$<?php echo $order_data['amount']; ?> <?php echo $StripeTaker_SaveFile_Data['currency']; ?></td>
         </tr>
      </tbody>
   </table>

   <?php

}



function Orders_View() {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $order_data = DataOps_Data_Get(STAKER_ORDERS, $_REQUEST['id']);

   if (count($order_data) > 0) {

      ?><h3>Order # <?php echo $order_data['id']; ?></h3>

      <?php if ($browser_type != "mobile") { ?>
      <table border="0" width="85%">
      <tr><td width="50%" valign="top">
      <?php } ?>

      <p><i class="icon-group" style="font-weight: bold;"></i> <b style="color: #0088cc;">Customer</b><br />
      <a href="manage.php?op=Customers_View&id=<?php echo $order_data['customer']; ?>"><?php echo $order_data['customer']; ?></a> (<?php echo $order_data['email']; ?>)</p><br />

      <p><i class="icon-tasks" style="font-weight: bold;"></i> <b style="color: #0088cc;">Product Ordered</b><br />
      <?php echo $order_data['product_name']; ?> (# <a href="manage.php?op=Products_Edit&id=<?php echo $order_data['product_id']; ?>"><?php echo $order_data['product_id']; ?></a>)</p><br />

      <p><i class="icon-credit-card" style="font-weight: bold;"></i><b style="color: #0088cc;"> Credit Card</b><br />
      Card Type: <?php echo $order_data['card_type']; ?><br />
      Expires On: <?php echo $order_data['card_exp']; ?><br />
      Card Number: ************<?php echo $order_data['card_last4']; ?><br />
      Unique ID: <?php echo $order_data['card_finger']; ?></i></p><br />

      <p><i class="icon-money" style="font-weight: bold;"></i><b style="color: #0088cc;"> Total Order</b><br />
      $<?php echo $order_data['amount']; ?> <?php echo $StripeTaker_SaveFile_Data['currency']; ?><br />
      <span class="muted">Fees: $<?php echo $order_data['fees']; ?> <?php echo $StripeTaker_SaveFile_Data['currency']; ?></span></p><br />

      <?php if ($browser_type != "mobile") { ?>
      </td><td valign="top">
      <?php } ?>

      <?php if ($order_data['type'] == "One-Time") { ?>
      <p><i class="icon-cloud" style="font-weight: bold;"></i> <b style="color: #0088cc;"> Stripe.com Charge</b><br />
      <?php echo $order_data['charge']; ?> <button class="btn" onClick="javascript:location.href='manage.php?op=Orders_Refund&id=<?php echo $order_data['charge']; ?>&cust=<?php echo $order_data['customer']; ?>';">Refund</button></p><br />

      <?php } else { ?>

      <p><i class="icon-cloud" style="font-weight: bold;"></i> <b style="color: #0088cc;"> Stripe.com Charges</b><br />
      <button class="btn" onClick="javascript:location.href='manage.php?op=Customers_View&id=<?php echo $order_data['customer']; ?>';">View Customer Charges</button></p><br />
      <?php } ?>

      <?php if ($browser_type != "mobile") { ?>
      <p><i class="icon-print" style="font-weight: bold;"></i><b style="color: #0088cc;"> Printables</b><br />
      <button onClick="javascript:location.href='manage.php?op=Orders_Invoice&id=<?php echo $order_data['id']; ?>';" class="btn btn-large">Print An Invoice</button></p>
      <?php } ?>

      <p><i class="icon-time" style="font-weight: bold;"></i><b style="color: #0088cc;"> Created</b><br />
      <?php echo date("Y-m-d H:i:s", $order_data['ordered']); ?></p><br />

      <p><i class="icon-globe" style="font-weight: bold;"></i><b style="color: #0088cc;"> Customer Fraud Data</b><br />
      IP Address: <i><?php echo $order_data['ip']; ?></i><br />
      Browser ID: <i><?php echo $order_data['agent']; ?></i></br /></p><br />

      <?php if ($browser_type != "mobile") { ?>
      </td></tr>
      </table><br />
      <?php } ?>

      <?php

   } else {

      ?><h3>We're Sorry</h3>
      We weren't able to find the order you were searching for.<?php

   }

}


function Orders_Main() {
   global $StripeTaker_SaveFile_Data, $browser_type;

   $order_data = array_reverse(DataOps_ReadFile(STAKER_ORDERS));

   ?><h3>Manage Orders</h3>
   <table width="85%" border="0" class="table table-striped">
      <thead>
         <tr>
            <th>Order #</th>
            <th>Total Price</th>
            <?php if ($browser_type != "mobile") { ?><th>Fees</th><?php } ?>
            <?php if ($browser_type != "mobile") { ?><th>Ordered On</th><?php } ?>
            <th>Actions</th>
         </tr>
      </thead>

      <tbody>
         <?php

   if (count($order_data) > 0) {

      foreach ($order_data as $key => $value) {

         ?>
         <tr>
            <td><?php echo $value['id']; ?></td>
            <td>$<?php echo $value['amount']; ?> <?php echo $StripeTaker_SaveFile_Data['currency']; ?></td>
            <?php if ($browser_type != "mobile") { ?>
            <td>$<?php echo $value['fees']; ?> <?php echo $StripeTaker_SaveFile_Data['currency']; ?></td>
            <td><?php echo date("Y-m-d H:i:s", $value['ordered']); ?></td>
            <?php } ?>
            <td>
               <button class="btn btn-primary" onClick="javascript:location.href='manage.php?op=Orders_View&id=<?php echo $value['id']; ?>';">View</button>
            </td>
         </tr><?php

      }

   } else {

      ?><td colspan="<?php if ($browser_type != "mobile") { echo "5"; } else { echo "3"; } ?>" align="center"><b>No orders found.</b></td><?php

   }

         ?>
      <tbody>
   </table>

   <?php

}


function Products_Add() {

   Products_Form(0, 0, "insert", "Save Product");

}


function Products_Edit() {

   $forminfo = DataOps_Data_Get(STAKER_PRODS, $_REQUEST['id']);

   Products_Form($forminfo, 0, "save", "Save Product");

}


function Products_Save() {
   global $StripeTaker_SaveFile_Data;

   $forminfo['id'] = $_REQUEST['id'];
   $forminfo['name'] = $_REQUEST['name'];
   $forminfo['desc'] = $_REQUEST['desc'];
   $forminfo['price'] = $_REQUEST['price'];
   $forminfo['recurs'] = $_REQUEST['recurs'];
   $forminfo['recurs_unit'] = $_REQUEST['recurs_unit'];
   $forminfo['recurs_orig'] = $_REQUEST['recurs_orig'];
   $forminfo['ships'] = $_REQUEST['ships'];
   $forminfo['url_download'] = $_REQUEST['url_download'];
   $forminfo['url_return'] = $_REQUEST['url_return'];
   $forminfo['url_notify'] = $_REQUEST['url_notify'];
   $forminfo['storage'] = $StripeTaker_SaveFile_Data['mode'];

   // setup error checking;
   $err_count = 0;
   $errors = array();
   $errors['alert-msg'] = "<b>There was a problem with the information you entered:</b>\n<ul>";

   // check for errors;
   if (mb_strlen($forminfo['name']) < 3) { $err_count++; $errors['name'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid name for this product.</li>"; }
   if (mb_strlen($forminfo['recurs']) < 2) { $err_count++; $errors['recurs'] = ""; $errors['alert-msg'] .= "<li>You must choose whether this product recurs or not.</li>"; }
   if ($forminfo['price'] < 1) { $err_count++; $errors['price'] = ""; $errors['alert-msg'] .= "<li>Price must be at least $1.00.</li>"; }

   // finish error checking;
   if ($err_count > 0) {

      $errors['alert-msg'] .= "\n</ul>";
      include("headers.inc.php");
      Manage_Menu();
      Products_Form($forminfo, $errors, "save", "Save Product");
      include("footers.inc.php");
      die();

   } else {

      if ($forminfo['recurs'] == "Yes") {

         $stripe_info1 = StripeInterface_Plan_Delete($forminfo['id']);
         $stripe_info2 = StripeInterface_Plan_Create($forminfo['price'], $forminfo['recurs_unit'], $forminfo['id'], $forminfo['name']);

      }

      if ($forminfo['recurs'] == "No" && $forminfo['recurs_orig'] == "Yes") {

         $stripe_info = StripeInterface_Plan_Delete($forminfo['id']);

      }

      DataOps_Data_Update(STAKER_PRODS, $forminfo, $forminfo['id']);
      header("Location:manage.php?");
      die();

   }

}



function Products_Delete() {

   $prod_id = $_REQUEST['id'];
   $prod_name = base64_decode($_REQUEST['prod_name']);
   $recurs = $_REQUEST['recurs'];

   ?><p><center><h3>Do you really want to delete <em><?php echo $prod_name; ?></em>?</h3>

   <button class="btn btn-large" onClick="javascript:location.href='manage.php?';">Cancel</button> 
   <button class="btn btn-danger btn-large" onClick="javascript:location.href='manage.php?op=Products_DeleteOK&id=<?php echo $prod_id; ?>&recurs=<?php echo $recurs; ?>';">Delete</button></center></p><?php

}


function Products_DeleteOK() {

   $prod_id = $_REQUEST['id'];

   if ($_REQUEST['recurs'] == "yes") {

      $stripe_info = StripeInterface_Plan_Delete($prod_id);

   }

   DataOps_Data_Delete(STAKER_PRODS, $prod_id);

   header("Location:manage.php?");
   die();

}


function Products_Insert() {
   global $StripeTaker_SaveFile_Data;

   $forminfo['name'] = $_REQUEST['name'];
   $forminfo['desc'] = $_REQUEST['desc'];
   $forminfo['price'] = $_REQUEST['price'];
   $forminfo['recurs'] = $_REQUEST['recurs'];
   $forminfo['recurs_unit'] = $_REQUEST['recurs_unit'];
   $forminfo['ships'] = $_REQUEST['ships'];
   $forminfo['url_download'] = $_REQUEST['url_download'];
   $forminfo['url_return'] = $_REQUEST['url_return'];
   $forminfo['url_notify'] = $_REQUEST['url_notify'];
   $forminfo['storage'] = $StripeTaker_SaveFile_Data['mode'];

   // setup error checking;
   $err_count = 0;
   $errors = array();
   $errors['alert-msg'] = "<b>There was a problem with the information you entered:</b>\n<ul>";

   // check for errors;
   if (mb_strlen($forminfo['name']) < 3) { $err_count++; $errors['name'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid name for this product.</li>"; }
   if (mb_strlen($forminfo['recurs']) < 2) { $err_count++; $errors['recurs'] = ""; $errors['alert-msg'] .= "<li>You must choose whether this product recurs or not.</li>"; }
   if ($forminfo['price'] < 1) { $err_count++; $errors['price'] = ""; $errors['alert-msg'] .= "<li>Price must be at least $1.00.</li>"; }

   // finish error checking;
   if ($err_count > 0) {

      $errors['alert-msg'] .= "\n</ul>";
      include("headers.inc.php");
      Manage_Menu();
      Products_Form($forminfo, $errors, "insert", "Save Product");
      include("footers.inc.php");
      die();

   } else {

      $forminfo['id'] = strtoupper(substr(md5(uniqid(rand())), 0, 6));

      if ($forminfo['recurs'] == "Yes") {

         $stripe_info = StripeInterface_Plan_Create($forminfo['price'], $forminfo['recurs_unit'], $forminfo['id'], $forminfo['name']);

      }

      DataOps_Data_Insert(STAKER_PRODS, $forminfo);
      header("Location:manage.php?");
      die();

   }

}


function Products_Form($forminfo=0, $errors=0, $action=0, $submit=0) {
   global $StripeTaker_SaveFile_Data;

?>

<form method="POST" action="manage.php?op=Products_<?php if ($action) { echo ucfirst($action); } ?>" id="<?php if ($action) { echo $action; } ?>_form">
<?php if (isset($forminfo['id'])) { ?><input type="hidden" name="id" value="<?php echo $forminfo['id']; ?>" /><?php } ?>
<?php if (isset($forminfo['recurs'])) { ?><input type="hidden" name="recurs_orig" value="<?php echo $forminfo['recurs']; ?>" /><?php } ?>

<?php if (isset($errors['alert-msg'])) { ?>
<div class="alert alert-error"><?php echo $errors['alert-msg']; ?></div>
<?php } ?>

<?php if (isset($forminfo['info-msg'])) { ?>
<div class="alert alert-info"><?php echo $forminfo['info-msg']; ?></div>
<?php } ?>

<h3>Manage Product</h3>

<?php

   if ($action == "save") {

      if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") { $access_protocol = "https"; } else { $access_protocol = "http"; }
      $access_url = $access_protocol . "://" . $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']) . "/";
      $access_url .= "order.php?op=View&id=" . $forminfo['id'];

      ?><p><div class="well">
         <label for="access_url">Product Order Page:</label>
         <i class="icon-<?php if ($access_protocol != "https") { echo "unlock"; } else { echo "lock"; } ?>"></i> 
         <input type="text" id="access_url" class="input-xxlarge" value="<?php echo $access_url; ?>">
         <span class="help-block"><small>Use this address for links that your customers can click on to buy your product.</small></span>
      </div></p><?php

   }

?>

<p><label for="name">Product Name:</label>
<input type="text" name="name" id="name" class="input-xlarge <?php if (isset($errors['name'])) { echo " error"; } ?>" placeholder="Product Name" value="<?php echo $forminfo['name']; ?>" /></p>

<p><label for="desc">Description:</label>
<textarea name="desc" id="desc" rows="10" cols="70" class="input-xxlarge <?php if (isset($errors['desc'])) { echo " error"; } ?>" placeholder="Description of your product or service.">
<?php echo $forminfo['desc']; ?>
</textarea></p>

<p class="form-inline"><label for="price">Price:</label>
<div class="input-prepend input-append"><span class="add-on">$</span><input type="text" name="price" id="price" class="input-medium <?php if (isset($errors['price'])) { echo " error"; } ?>" placeholder="1,000,000.00" value="<?php echo $forminfo['price']; ?>" /><span class="add-on"><?php echo$StripeTaker_SaveFile_Data['currency']; ?></span></div></p><br />

<p class="form-inline"><label for="recurs">Does this amount recur?</label>
<select name="recurs" id="recurs" class="input-small <?php if (isset($errors['recurs'])) { echo " error"; } ?>" onChange="javascript:$('#recurs_div').toggle();">
<?php if (mb_strlen($forminfo['recurs'])) { ?><option value="<?php echo $forminfo['recurs']; ?>" SELECTED>* <?php echo $forminfo['recurs']; ?></option><?php } ?>
<option value="No">No</option>
<option value="Yes">Yes</option>
</select></p>

<div id="recurs_div" class="well" <?php if ($forminfo['recurs'] != "Yes") { echo "style=\"display: none;\""; } ?>>
<p class="form-inline"><label for="recurs_unit">How often will this recur?</label>
<select name="recurs_unit" id="recurs_unit" class="input-medium <?php if (isset($errors['recurs_unit'])) { echo " error"; } ?>">
<?php if (mb_strlen($forminfo['recurs_unit'])) { ?><option value="<?php echo $forminfo['recurs_unit']; ?>" SELECTED>* <?php echo $forminfo['recurs_unit']; ?></option><?php } ?>
<option value="Monthly">Monthly</option>
<option value="Yearly">Yearly</option>
</select></p>
</div>

<p class="form-inline"><label for="ships">Requires shipping?</label>
<select name="ships" id="ships" class="input-small <?php if (isset($errors['ships'])) { echo " error"; } ?>">
<?php if (mb_strlen($forminfo['ships'])) { ?><option value="<?php echo $forminfo['ships']; ?>" SELECTED>* <?php echo $forminfo['ships']; ?></option><?php } ?>
<option value="No">No</option>
<option value="Yes">Yes</option>
</select></p>

<p><label for="url_download">Download URL:</label>
<input type="text" name="url_download" id="url_download" class="input-xxlarge <?php if (isset($errors['url_download'])) { echo " error"; } ?>" placeholder="http://mysite.com/stuff.zip" value="<?php echo $forminfo['url_download']; ?>" />
<span class="help-block"><small>Leave blank if there's nothing to download.</small></span></p>

<p><label for="url_return">Return URL:</label>
<input type="text" name="url_return" id="url_return" class="input-xxlarge <?php if (isset($errors['url_return'])) { echo " error"; } ?>" placeholder="http://mysite.com/thankyou.htm" value="<?php echo $forminfo['url_return']; ?>" />
<span class="help-block"><small>Entering a URL here will send the user to that page rather than the order confirmation page.</small></span></p>

<p><label for="url_notify">Notification URL:</label>
<input type="text" name="url_notify" id="url_notify" class="input-xxlarge <?php if (isset($errors['url_notify'])) { echo " error"; } ?>" placeholder="http://mysite.com/thankyou.htm" value="<?php echo $forminfo['url_notify']; ?>" />
<span class="help-block"><small>Entering a URL here will send a POST to the page with ALL variables in JSON format using variable <b>params</b>.</small></span></p>

<?php if (isset($forminfo['storage'])) { ?>
<p class="form-inline"><label for="storage">Is this a live or test product?</label>
<select name="storage" id="storage" class="input-small <?php if (isset($errors['storage'])) { echo " error"; } ?>">
<?php if (mb_strlen($forminfo['storage'])) { ?><option value="<?php echo $forminfo['storage']; ?>" SELECTED>* <?php echo $forminfo['storage']; ?></option><?php } ?>
<option value="Test">Test</option>
<option value="Live">Live</option>
</select></p>
<?php } ?>

<div class="alert alert-info">Saving this product can take a few moments. Please be patient.</div>
<button onClick="javascript:document.forms['<?php if ($action) { echo $action; } ?>_form'].submit(); this.disabled=true;" class="btn btn-primary btn-large"><?php if ($submit) { echo $submit; } else { echo "Help! I'm stuck in a button!"; } ?></button>

</form>


<?php

}


function Products_Main() {
   global $StripeTaker_SaveFile_Data, $browser_type;

      if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") { $access_protocol = "https"; } else { $access_protocol = "http"; }
      $access_url = $access_protocol . "://" . $_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']) . "/";
      $access_url .= "order.php?op=View&id=";

   foreach(glob("sync-*.php") as $target) {

      include($target);
      ?><p><center><button class="btn btn-info btn-large" onClick="location.href='<?php echo $target; ?>?op=Begin';">Sync Your Products with <?php echo $sync_file_name; ?></button></center></p><?php
      unset($sync_file_name);

   }


   ?><h3>Products Listing</h3>

   <table width="100%" class="table table-striped">

      <thead>
         <tr>
            <?php if ($browser_type != "mobile") { ?><th>ID #</th><?php } ?>
            <th>Name</th>
            <th>Price</th>
            <?php if ($browser_type != "mobile") { ?><th>Recurs</th><?php } ?>
            <th>Actions</th>
         </tr>
      </thead>

      <tbody>
<?php

   $data = DataOps_ReadFile(STAKER_PRODS);

   if (count($data) > 0) {

      foreach ($data as $product) {

         ?><tr>
            <?php if ($browser_type != "mobile") { ?><td><?php echo $product['id']; ?></td><?php } ?>
            <td><?php echo $product['name']; ?> <?php if ($product['storage'] == "Test") { echo "&nbsp; <i style=\"color: #6699ff;\" class=\"icon-star\"></i>"; } ?></td>
            <td>$<?php echo $product['price']; ?></td>
            <?php if ($browser_type != "mobile") { ?><td><?php echo $product['recurs']; ?></td><?php } ?>
            <td>
               <button class="btn btn-primary" onClick="javascript:location.href='manage.php?op=Products_Edit&id=<?php echo $product['id']; ?>';"><i class="icon-edit"></i> Edit</button> <?php if ($browser_type == "mobile") { echo "<p style=\"padding-top: 10px;\" />"; } ?>
               <button class="btn btn-danger" onClick="javascript:location.href='manage.php?op=Products_Delete&id=<?php echo $product['id']; ?>&prod_name=<?php echo base64_encode($product['name']); ?><?php if ($product['recurs'] == "Yes") { echo "&recurs=yes"; } ?>';"><i class="icon-trash"></i> Delete</button> <?php if ($browser_type == "mobile") { echo "<p />"; } ?>
               <button class="btn btn-inverse" onClick="javascript:location.href='order.php?op=View&id=<?php echo $product['id']; ?>&override=phone';"><i class="icon-check"></i> Take Order</button> <?php if ($browser_type == "mobile") { echo "<p />"; } ?>
               <button class="btn" onClick="javascript:$('#<?php echo $product['id']; ?>').modal()">Get code</button> <?php if ($browser_type == "mobile") { echo "<p />"; } ?>

               <div class="modal" id="<?php echo $product['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $product['id']; ?>" aria-hidden="true" style="display: none;<?php if ($browser_type != "mobile") { echo " width: 650px;"; } ?>">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                     <h3 id="myModalLabel"><?php echo $product['name']; ?><?php if ($browser_type != "mobile") { ?> - <?php } else { echo "<br /><small>"; } ?>Order Page Options<?php if ($browser_type == "mobile") { echo "</small"; } ?></h3>
                  </div>
                  <div class="modal-body">
                     <h3>Order Link</h3>
                     <p>Copy and paste the address below into a link or button on your site to take your customers to the <?php echo $product['name']; ?> order page.</p>
                     <p><input type="text" id="product_url" class="input-xxlarge" value="<?php echo $access_url . $product['id']; ?>" /></p>

                     <?php if ($browser_type != "mobile") { ?>
                     <h3>Order Button</h3>
                     <p>Copy and paste the code below to create a button on your site for your customers to simply click on to get to the <?php echo $product['name']; ?> order page.</p>
<p><textarea id="product_btn" class="input-xxlarge" rows="4">
<!-- StripeTaker Order button: <?php echo $product['name']; ?> -->
<button onClick="javascript:location.href='<?php echo $access_url . $product['id']; ?>';" style="font-weight: bold;">Order</button>
</textarea></p>

                     <p><i>This button code will produce a button that looks like this:</i> <button onClick="javascript:location.href='<?php echo $access_url . $product['id']; ?>';" style="font-weight: bold;">Order</button></p>
                     <?php } else { ?>
                     <p><i>Order button generator is only available in the desktop and tablet versions.</i></p>
                     <?php } ?>
                  </div>
                  <div class="modal-footer">
                     <button class="btn btn-primary btn-large" data-dismiss="modal" aria-hidden="true">Close</button>
                  </div>
               </div>
            </td>
         </tr><?php

      }

   } else {

      ?><tr><td colspan="<?php if ($browser_type != "mobile") { echo "5"; } else { echo "3"; } ?>" style="text-align: center;"><b>No products found.</b></td></tr><?php

   }

?>
      </tbody>

   </table><p />

   <p><center><button onClick="javascript:location.href='manage.php?op=Products_Add';" class="btn btn-large"><i class="icon-plus-sign"></i> Add A Product</button></center></p> <br />

      <p class="muted pull-right"><i style="color: #6699ff;" class="icon-star icon-large"></i> Denotes test products that will not show in <b>Live mode</b> to customers.</p><?php

}


function Manage_Menu() {
   global $StripeTaker_SaveFile_Data, $browser_type;

      if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") { $current_protocol = "https"; } else { $current_protocol = "http"; }

      $license_data = DataOps_ReadFile(STAKER_CORE2);
      $this_version = $StripeTaker_SaveFile_Data['version'];
      $that_version = $license_data['version'];
      settype($this_version, "float");
      settype($that_version, "float");
      if ($this_version < $that_version) { $update_avail = "Yes"; } else { $update_avail = "No"; }

?>
   <?php if ($current_protocol != "https") { ?>
     <div class="alert alert-info">
         <b style="color: #cc0000;"><i class="icon-minus-sign icon-large"></i> Your web site is not secure!</b><br />
         By not having an SSL certificate in place and/or being in SSL mode (e.g. http<b>s</b>:// in the address), you may be exposing customer data to attack and compromise without end-to-end encryption. <b><button class="btn btn-mini" onClick="javascript:location.href='http://support.nightkingdoms.com/customer/portal/articles/825698-ssl-encryption';">Learn More</button></b>
      </div>
   <?php } ?>

   <?php if ($update_avail == "Yes") { ?>
      <div class="alert alert-success">
         <b><i class="icon-download icon-large" style="font-size: 1.2em;"></i> An Update Is Available! <button class="btn btn-success btn-mini" onClick="javascript:location.href='manage.php?op=License';">more info</button>
      </div>
   <?php } ?>

   <div class="well" <?php if ($browser_type == "desktop") { echo "style=\"height: 35px;\""; } ?>>
   <ul class="nav <?php if ($browser_type == "desktop") { echo "nav-pills"; } else { echo "nav-list"; } ?>">
     <li <?php if ($_REQUEST['op'] == "Products") { echo "class=\"active\""; } ?>><a href="manage.php?op=Products"><i class="icon-tasks icon-large"></i> Products</a></li>
     <li <?php if ($_REQUEST['op'] == "Orders") { echo "class=\"active\""; } ?>><a href="manage.php?op=Orders"><i class="icon-shopping-cart icon-large"></i> Orders</a></li>
     <li <?php if ($_REQUEST['op'] == "Customers") { echo "class=\"active\""; } ?>><a href="manage.php?op=Customers"><i class="icon-group icon-large"></i> Customers</a></li>
     <li <?php if ($_REQUEST['op'] == "Templates") { echo "class=\"active\""; } ?>><a href="manage.php?op=Templates"><i class="icon-envelope icon-large"></i> Email Templates</a></li>
     <li <?php if ($_REQUEST['op'] == "Settings") { echo "class=\"active\""; } ?>><a href="manage.php?op=Settings"><i class="icon-cogs icon-large"></i> Settings</a></li>
     <li><a href="http://support.nightkingdoms.com/customer/portal/topics/318505-nk-stripetaker/articles" target="_blank"><i class="icon-question-sign icon-large"></i> Support</a></li>
     <li><a href="logout.php"><i class="icon-signout icon-large"></i> LogOut</a></li>
   </ul>
   </div>
<?php

}

?>
