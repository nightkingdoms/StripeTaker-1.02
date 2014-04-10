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

$sess_auth_code = md5(date("Y-m-d") . "11365");

function CheckSession() {
   global $sess_auth_code;

   if (isset($_COOKIE['auth'])) {

      $sess_info = Decrypt($_COOKIE['auth']);

      if ($sess_info = $sess_auth_code) {

         AddSession(); // renew the time;

         return true;

      } else {

         return false;

      }

   } else {

      return false;

   }

}


function AddSession() {
   global $sess_auth_code;

   setcookie("auth", $sess_auth_code, time() + 3600);

}


function KillSession() {

   setcookie("auth", "", time() - 3600);

}

?>
