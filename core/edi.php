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

   require_once("res/aes/AES_Encryption.php");
   require_once("res/aes/padCrypt.php");

   define("STAKER_ENC_INT_SECX", "{{ENC_KEY}}"); // key for internal use;
   define("STAKER_ENC_INT_SECY", "{{ENC_IV}}"); // vector for internal use;

   // create encryption objects for internal/external use;
   $StripeTaker_enc_int = new AES_Encryption(STAKER_ENC_INT_SECX, STAKER_ENC_INT_SECY);


function Encrypt($data) {
   global $StripeTaker_enc_int;

   $data = base64_encode($StripeTaker_enc_int->encrypt($data));

   return $data;

}


function Decrypt($data) {
   global $StripeTaker_enc_int;

   $data = $StripeTaker_enc_int->decrypt(base64_decode($data));

   return $data;

}

?>
