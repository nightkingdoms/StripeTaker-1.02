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

function Forms_ReformatData($string) {

   $string = eregi_replace("@@;@@", ";", $string);
   $string = eregi_replace("@@drop@@", "drop", $string);
   $string = eregi_replace("@@replace@@", "replace", $string);
   $string = eregi_replace("@@create@@", "create", $string);
   $string = eregi_replace("@@alter@@", "alter", $string);
   $string = eregi_replace("@@select@@", "select", $string);
   $string = eregi_replace("@@script@@", "script", $string);
   $string = eregi_replace("@@embed@@", "embed", $string);
   $string = stripslashes($string);

   return $string;

}


function Forms_FormatData($string) {

   $string = eregi_replace(";", "@@;@@", $string);
   $string = eregi_replace("drop", "@@drop@@", $string);
   $string = eregi_replace("replace", "@@replace@@", $string);
   $string = eregi_replace("create", "@@create@@", $string);
   $string = eregi_replace("script", "@@script@@", $string);
   $string = eregi_replace("embed", "@@embed@@", $string);
   $string = eregi_replace("alter", "@@alter@@", $string);
   $string = eregi_replace("select", "@@select@@", $string);
   $string = addslashes($string);

   return $string;

}


function Forms_CheckValidEmail($email) {

   $isValid = true;

   $atIndex = strrpos($email, "@");

   if (is_bool($atIndex) && !$atIndex) {

      $isValid = false;

   } else {

      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);

      if ($localLen < 1 || $localLen > 64) {

         // local part length exceeded
         $isValid = false;

      } else if ($domainLen < 1 || $domainLen > 255) {

         // domain part length exceeded
         $isValid = false;

      } else if ($local[0] == '.' || $local[$localLen-1] == '.') {

         // local part starts or ends with '.'
         $isValid = false;

      } else if (preg_match('/\\.\\./', $local)) {

         // local part has two consecutive dots
         $isValid = false;

      } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {

         // character not valid in domain part
         $isValid = false;

      } else if (preg_match('/\\.\\./', $domain)) {

         // domain part has two consecutive dots
         $isValid = false;

      } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {

         // character not valid in local part unless 
         // local part is quoted

         if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {

            $isValid = false;

         }

      } if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {

         // domain not found in DNS
         $isValid = false;

      }

   }

   return $isValid;

}

?>
