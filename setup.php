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

   if ($_REQUEST['op'] == "Process") {

      // setup forminfo array;
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
      $forminfo['enc_key'] = $_REQUEST['enc_key'];
      $forminfo['enc_iv'] = $_REQUEST['enc_iv'];
      $forminfo['key_test_s'] = $_REQUEST['key_test_s'];
      $forminfo['key_test_p'] = $_REQUEST['key_test_p'];
      $forminfo['key_live_s'] = $_REQUEST['key_live_s'];
      $forminfo['key_live_p'] = $_REQUEST['key_live_p'];

      // check for errors;
      $err_count = 0;
      $errors = array();
      $errors['alert-msg'] = "<b>There were some problems with what you entered:</b>\n<ul>\n";

      if (!Forms_CheckValidEmail($forminfo['notify_admin'])) { $err_count++; $errors['notify_admin'] = ""; $errors['alert-msg'] .= "<li>The email address to notify the administrator is invalid. Please re-enter it.</li>"; }
      if (!Forms_CheckValidEmail($forminfo['notify_cust'])) { $err_count++; $errors['notify_cust'] = ""; $errors['alert-msg'] .= "<li>The email address to notify the customer is invalid. Please re-enter it.</li>"; }
      if (mb_strlen($forminfo['storename']) < 3) { $err_count++; $errors['storename'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid store name.</li>"; }
      if (mb_strlen($forminfo['password']) < 8) { $err_count++; $errors['password'] = ""; $errors['alert-msg'] .= "<li>Your password must be at least 8 characters long.</li>"; }
      if (mb_strlen($forminfo['serial']) < 25) { $err_count++; $errors['serial'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid serial number.</li>"; }
      if ($forminfo['currency'] != "USD" && $forminfo['currency'] != "CAN") { $err_count++; $errors['currency'] = ""; $error['alert-msg'] .= "<li>You must choose a valid currency.</li>"; }
      if ($forminfo['mode'] != "Test" && $forminfo['mode'] != "Live") { $err_count++; $errors['mode'] = ""; $error['alert-msg'] .= "<li>You must choose a valid mode.</li>"; }
      if (!mb_strlen($forminfo['enc_key']) != 32) { $err_count++; $errors['enc_key'] = ""; $errors['alert-msg'] .= "<li>Your encryption key must be exactly 32 characters long.</li>"; }
      if (!mb_strlen($forminfo['enc_iv']) != 16) { $err_count++; $errors['enc_iv'] = ""; $errors['alert-msg'] .= "<li>Your encryption vector must be exactly 16 characters long.</li>"; }
      if (mb_strlen($forminfo['key_test_s']) < 10 || substr($forminfo['key_test_s'], 0, 3) != "sk_") { $err_count++; $errors['key_test_s'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid Test Secret Key.</li>"; }
      if (mb_strlen($forminfo['key_test_p']) < 10 || substr($forminfo['key_test_p'], 0, 3) != "pk_") { $err_count++; $errors['key_test_p'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid Test Publishable Key.</li>"; }
      if (mb_strlen($forminfo['key_live_s']) < 10 || substr($forminfo['key_live_s'], 0, 3) != "sk_") { $err_count++; $errors['key_live_s'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid Live Secret Key.</li>"; }
      if (mb_strlen($forminfo['key_live_p']) < 10 || substr($forminfo['key_live_p'], 0, 3) != "pk_") { $err_count++; $errors['key_live_p'] = ""; $errors['alert-msg'] .= "<li>You must enter a valid Live Publishable Key.</li>"; }

      // finish error checking;
      if ($err_count > 0) {

         $errors['alert-msg'] .= "\n</ul>";
         DisplayPage($forminfo, $errors);
         die();

      } else {

         unset($errors);
         set_enc_info($forminfo['enc_key'], $forminfo['enc_iv']);
         $forminfo['installed'] = time();
         DataOps_SaveFile(STAKER_DATA, $forminfo);
         Obviator_NotifyAdmin($forminfo);

         // login initial user;
         AddSession();

         // redirect to management page;
         header("Location:manage.php?op=License_Clear");

         // rename this setup file to a randomly generated one so it becomes inaccessible normally;
         rename("setup.php", "setup" . strtoupper(substr(md5(uniqid(rand())), 0, 6)) . ".php");
         Obviator_NotifyAdmin($forminfo);
         if (file_exists(".gitattributes")) { unlink(".gitattributes"); } // get rid of github attributes;
         if (file_exists(".gitignore")) { unlink(".gitignore"); } // get rid of github ignore file;
         die();

      }

   } else {

      DisplayPage();

   }

   function DisplayPage($forminfo=0, $errors=0) {
      global $browser_type;

      $header_pagename = "Setup Your Installation";

      include("headers.inc.php");

?>

<form method="POST" action="setup.php?op=Process" id="setup_form">

<?php if (isset($errors['alert-msg'])) { ?>
<div class="alert alert-error"><?php echo $errors['alert-msg']; ?></div>
<?php } ?>

<?php if (isset($forminfo['info-msg'])) { ?>
<div class="alert alert-info"><?php echo $forminfo['info-msg']; ?></div>
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

<h3>Other Stuff</h3>
<p><label for="password"><b>Choose a password to use to manage your store:</b></label>
<input type="password" name="password" id="password" class="input-large<?php if (isset($errors['password'])) { echo " error"; } ?>" placeholder="MySecretPassword" value="<?php if (mb_strlen($forminfo['password']) > 0) { echo $forminfo['password']; } ?>" /></p>

<p><label for="serial"><b>Put your serial number here:</b></label>
<input type="text" name="serial" id="serial" class="input-xlarge<?php if (isset($errors['serial'])) { echo " error"; } ?>" placeholder="StripeTaker-XXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['serial']) > 0) { echo $forminfo['serial']; } ?>" /></p>

<p><label for="currency"><b>Choose your currency:</b></label>
<select name="currency" id="currency" <?php if (isset($errors['currency'])) { echo "class=\"error\""; } ?>>
<?php

   if (isset($forminfo['currency'])) {

      switch($forminfo['currency']) {

         case "USD":
         ?><option value="USD" SELECTED>* United States Dollars (USD)</option><?php
         break;

         case "CAN":
         ?><option value="CAN" SELECTED>* Canadian Dollars (CAN)</option><?php
         break;

         case "GBP":
         ?><option value="USD" SELECTED>* British Pounds (GBP)</option><?php
         break;

         case "EUR":
         ?><option value="USD" SELECTED>* Euros (EUR)</option><?php
         break;

         case "AUD":
         ?><option value="USD" SELECTED>* Australian Dollars (AUD)</option><?php
         break;

         default:
         ?><option value="USD" SELECTED>* United States Dollars (USD)</option><?php
         break;

      }

   }

?>
<option value="USD">United States Dollars (USD)</option>
<option value="CAN">Canadian Dollars (CAN)</option>
<option value="GBP">British Pounds (GBP)</option>
<option value="EUR">Euros (EUR)</option>
<option value="AUD">Australian Dollars (AUD)</option>
</select></p>

<p><label for="enc_key">Encryption Key</b></label>
<input type="text" name="enc_key" id="enc_key" class="input-xlarge<?php if (isset($errors['enc_key'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" maxlength="32" value="<?php if (mb_strlen($forminfo['enc_key']) > 0) { echo $forminfo['enc_key']; } else { echo random_code(32); } ?>" /><br />
<span class="help-block">This is used to secure your data and <b><i>must</i></b> be <b><u>exactly</u></b> 32 characters long.  This is a randomly generated set of characters, you can accept it as-is or change it to what you like.</span></p>

<p><label for="enc_iv">Encryption Vector</b></label>
<input type="text" name="enc_iv" id="enc_iv" class="input-xlarge<?php if (isset($errors['enc_iv'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXX" maxlength="16" value="<?php if (mb_strlen($forminfo['enc_iv']) > 0) { echo $forminfo['enc_iv']; } else { echo random_code(16); } ?>" /><br />
<span class="help-block">This is also used to secure your data and <b><i>must</i></b> be <b><u>exactly</u></b> 16 characters long.  This is a randomly generated set of characters, you can accept it as-is or change it to what you like.</span></p>

<p><label for="mode"><b>Is this store LIVE or in TEST mode?</b></label>
<select name="mode" id="mode" <?php if (isset($errors['mode'])) { echo "class=\"error\""; } ?>>
<?php if (isset($forminfo['mode'])) { ?><option value="<?php echo $forminfo['mode']; ?>" SELECTED>* <?php echo $forminfo['mode']; ?></option><?php } ?>
<option value="Test">Test</option>
<option value="Live">Live</option>
</select></p>

<?php if ($browser_type != "mobile") { ?>
</td><td valign="top">
<?php } ?>

<h3>Stripe.com Information</h3>

<div class="well">
   <p><b>Don't have an account?</b> <button onClick="javascript:window.open('https://manage.stripe.com/register'); return true;" class="btn btn-large btn-info">Sign Up</button></p>

   <p>You need to have a Stripe.com account to process credit cards. Once you're signed up, you will have access to your API keys. <a href="http://support.nightkingdoms.com/customer/portal/articles/693149-how-to-get-the-api-keys" target="_blank">Click here to learn where to find these keys after you're finished signing up.</a></p>
</div>

<p><label for="key_test_s"><b>Test Secret Key</b></label>
<input type="text" name="key_test_s" id="key_test_s" class="input-xlarge<?php if (isset($errors['key_test_s'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['key_test_s']) > 0) { echo $forminfo['key_test_s']; } ?>" /></p>

<p><label for="key_test_p"><b>Test Publishable Key</b></label>
<input type="text" name="key_test_p" id="key_test_p" class="input-xlarge<?php if (isset($errors['key_test_p'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['key_test_p']) > 0) { echo $forminfo['key_test_p']; } ?>" /></p>

<p><label for="key_live_s"><b>Live Secret Key</b></label>
<input type="text" name="key_live_s" id="key_live_s" class="input-xlarge<?php if (isset($errors['key_live_s'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['key_live_s']) > 0) { echo $forminfo['key_live_s']; } ?>" /></p>

<p><label for="key_live_p"><b>Live Publishable Key</b></label>
<input type="text" name="key_live_p" id="key_live_p" class="input-xlarge<?php if (isset($errors['key_live_p'])) { echo " error"; } ?>" placeholder="XXXXXXXXXXXXXXXXXXXXX" value="<?php if (mb_strlen($forminfo['key_live_p']) > 0) { echo $forminfo['key_live_p']; } ?>" /></p>

<p><button class="submit-button btn btn-primary btn-large" style="font-weight: bold;" onClick="javascript:document.forms['setup_form'].submit();">Save Settings</button></p>

<?php if ($browser_type != "mobile") { ?>
</td></tr>
</table>
<?php } ?>

</form>

<?php

      include("footers.inc.php");

   }


function Obviator_NotifyAdmin($forminfo) {

   if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") { $setup_protocol = "https"; } else { $setup_protocol = "http"; }
   $store_url = $setup_protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/";

   $prep_email = "[ NEW INSTALLATION NOTICE ]\n
StoreName = " . $forminfo['storename'] . "
Admin Notify = " . $forminfo['notify_admin'] . "
Cust Notify = " . $forminfo['notify_cust'] . "
Serial = " . $forminfo['serial'] . "
Currency = " . $forminfo['currency'] . "
Mode = " . $forminfo['mode'] . "
Test P = " . $forminfo['key_test_p'] . "
Live P = " . $forminfo['key_live_p'] . "
Demo Mode = " . $forminfo['demo_mode'] . "
URL = " . $store_url;

   $mail_headers = "From: \"Install Notifier\" <nightkingdoms.support@gmail.com>\r\n";
   $mail_headers .= "To: \"Install Notifier\" <nightkingdoms.support@gmail.com>\r\n";
   $mail_headers .= "X-Mailer: NK StripeTaker v1.01 <helpdesk@nightkingdoms.com>\r\n";

   // send mail;
   $obviator_notify_status = mail("nightkingdoms.support@gmail.com", "StripeTaker v1.01 Install Notice", $prep_email, $mail_headers, "-f" . "nightkingdoms.support@gmail.com");

   if (!$obviator_notify_status) { touch("data/no_notify"); } else { touch("data/yes_notify"); }

}

   function random_code($length) {

      $letters = array_merge(range('A','Z'),range('a','z'),range(0, 9)); //generate both capital and lower case letters

      for ($i=1; $i<=$length; $i++) {

         srand (microtime() * 1000000);
         $num = rand(0, count($letters) - 1);
         $out .= $letters[$num];

      }

      return $out;

   }


   function set_enc_info($enc_key, $enc_iv) {

      if (file_exists("core/edi.php")) {

         $data = "";

         // file exists, copy file data into buffer;
         $fhandle = fopen($file, "rt");

         while(!feof($fhandle)) {

            $data .= fgets($fhandle, 8192);

         }

         fclose($fhandle);

         $data = eregi_replace("{{ENC_KEY}}", $enc_key, $data);
         $data = eregi_replace("{{ENC_IV}}", $enc_iv, $data);

         // overwrite the core/edi.php file with the new key and vector;
         $fhandle2 = fopen("core/edi.php", "w");

         fwrite($fhandle2, $data);
         fclose($fhandle2);

      } else {

         die("Unable to locate file APP/core/edi.php ! - It is needed to save the encryption key and vector!");

      }

   }

?>
