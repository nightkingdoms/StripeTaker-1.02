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

   // Setup file needs;
   include("res/stripe/Stripe.php");

   if ($StripeTaker_SaveFile_Data['mode'] == "Live") {

      $stripe_key = $StripeTaker_SaveFile_Data['key_live_s'];

   } else {

      $stripe_key = $StripeTaker_SaveFile_Data['key_test_s'];

   }

   if (isset($StripeTaker_SaveFile_Data['serial'])) {

      Stripe::setApiKey($stripe_key);

   }


function StripeInterface_ChargeList($cust, $count) {

   $stripeInfo = array("count" => $count, "customer" => $cust);

   try { $stripe_info = Stripe_Charge::all($stripeInfo); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info['data'];

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_ChargeItem($id) {

   try { $stripe_info = Stripe_Charge::retrieve($id); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_Charge($amount, $token, $desc="Order") {
   global $StripeTaker_SaveFile_Data;

   $amount_prep = $amount * 100;
   $currency_prep = strtolower($StripeTaker_SaveFile_Data['currency']);

   $stripeInfo = array("amount" => $amount_prep, "currency" => $currency_prep, "card" => $token, "description" => $desc);

   try { $stripe_info = Stripe_Charge::create($stripeInfo); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_ChargeCust($amount, $cust, $desc="Order") {
   global $StripeTaker_SaveFile_Data;

   $amount_prep = $amount * 100;
   $currency_prep = strtolower($StripeTaker_SaveFile_Data['currency']);

   $stripeInfo = array("amount" => $amount_prep, "currency" => $currency_prep, "customer" => $cust, "description" => $desc);

   try { $stripe_info = Stripe_Charge::create($stripeInfo); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_Cust_Get($cust) {
   global $StripeTaker_SaveFile_Data;

   try { $stripe_info = Stripe_Customer::retrieve($cust); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_Refund($amount, $charge) {
   global $StripeTaker_SaveFile_Data;

   $amount_prep = $amount * 100;

   try { $stripe_charge = Stripe_Charge::retrieve($charge); $stripe_info = $stripe_charge->refund(array("amount" => $amount_prep)); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_Cust_Create($token, $email, $desc) {
   global $StripeTaker_SaveFile_Data;

   $stripeInfo = array("email" => $email, "description" => $desc, "card" => $token);

   try { $stripe_info = Stripe_Customer::create($stripeInfo); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_Plan_Create($amount, $interval, $id, $name) {
   global $StripeTaker_SaveFile_Data;

   $amount_prep = $amount * 100;
   $currency_prep = strtolower($StripeTaker_SaveFile_Data['currency']);
   if ($interval == "Monthly") { $interval_prep = "month"; } elseif ($interval == "Yearly") { $interval_prep = "year"; } else { $interval_prep = "THROW_ERROR"; }

   $stripeInfo = array("amount" => $amount_prep, "interval" => $interval_prep, "name" => $name, "currency" => $currency_prep, "id" => $id, "name" => $name);

   try { $stripe_info = Stripe_Plan::create($stripeInfo); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_Plan_Assc($cust, $plan) {
   global $StripeTaker_SaveFile_Data;

   $stripeInfo = array("prorate" => false, "plan" => $plan);

   try { $stripe_cust = Stripe_Customer::retrieve($cust); $stripe_info = $stripe_cust->updateSubscription($stripeInfo); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_Plan_Cancel($cust) {
   global $StripeTaker_SaveFile_Data;

   try { $stripe_cust = Stripe_Customer::retrieve($cust); $stripe_info = $stripe_cust->cancelSubscription(); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_Plan_Delete($plan) {
   global $StripeTaker_SaveFile_Data;

   try { $stripe_plan = Stripe_Plan::retrieve($plan); $stripe_info = $stripe_plan->delete(); }
   catch (Exception $e) { $stripeError = $e->getJsonBody(); $stripeErrors = StripeInterface_CheckError($stripeError); }

   if (!isset($stripeErrors)) {

      $stripe_info = json_decode($stripe_info, true);
      return $stripe_info;

   } else {

      return $stripeErrors;

   }

}


function StripeInterface_CheckError($stripeError) {

   $error_body = $stripeError['error'];
   $type = $error_body['type'];
   $err = $error_body['code'];

   $err_return['status'] = "fail";

   switch($type) {

      case "card_error":
         switch($err) {

            case "card_declined":
            $err_return['issue'] = "all";
            $err_return['msg'] = "Your credit card has been declined while verifying.";
            break;

            case "incorrect_number":
            $err_return['issue'] = "card-number";
            $err_return['msg'] = "Your credit card number is invalid or incorrect. Please re-enter it.";
            break;

            case "invalid_expiry_month":
            $err_return['issue'] = "card-expiry-month";
            $err_return['msg'] = "Your credit card expiration month is invalid or incorrect. Please re-enter it.";
            break;

            case "invalid_expiry_year":
            $err_return['issue'] = "card-expiry-year";
            $err_return['msg'] = "Your credit card expiration year is invalid or incorrect. Please re-enter it.";
            break;

            case "invalid_cvc":
            $err_return['issue'] = "card-cvc";
            $err_return['msg'] = "Your credit card verification number is invalid or incorrect. Please re-enter it.";
            break;

            case "expired_card":
            $err_return['issue'] = "all";
            $err_return['msg'] = "Your credit card was found to be expired while verifying.";
            break;

            case "missing":
            $err_return['issue'] = "all";
            $err_return['msg'] = "You have missing credit card information.";
            break;

            case "processing_error": // 100
            $err_return['issue'] = "all";
            $err_return['msg'] = "There was an error while processing the request with the gateway. (Error 100)";
            break;

            default: // 200
            $err_return['issue'] = "all";
            $err_return['msg'] = "There was a problem while processing the request with the gateway. (Error 200)";
            break;

         }
      break;

      case "invalid_request_error": // 300
      $err_return['issue'] = "all";
      $err_return['msg'] = "An invalid request was sent while contacting the gateway. (Error 300)";
      break;

      case "api_error": // 400
      $err_return['issue'] = "all";
      $err_return['msg'] = "There was an API issue while contacting the gateway. (Error 400)";
      break;

      default: // 500
      $err_return['issue'] = "all";
      $err_return['msg'] = "There was a problem while verifying your credit card information. Please re-enter it. (Error 500)";
      break;

   }

   $err_return['orig'] = $error_body['message'];

   return $err_return;

}

?>
