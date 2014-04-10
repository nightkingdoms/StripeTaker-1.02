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

   date_default_timezone_set("America/Phoenix");

   if (!function_exists('curl_init')) {

      die("StripeTaker :: The CURL PHP extension is required to use this application.");

   }


   if (!function_exists('json_decode')) {

      die("StripeTaker :: The JSON PHP extension is required to use this application.");

   }

   include("core/forms.php");
   if (basename($_SERVER['PHP_SELF']) != "setup.php") { include("core/edi.php"); }
   include("core/sessions.php");
   include("core/dataops.php");
   include("res/php-mobile-detect/Mobile_Detect.php");
   include("core/stripefunc.php");
   include("core/templates.php");

   // determine browser type;
   $browser_detect = new Mobile_Detect();

   if ($browser_detect->isMobile()) { $browser_type = "mobile";

   } elseif ($browser_detect->isTablet()) { $browser_type = "tablet";

   } else { $browser_type = "desktop"; }


// if no installation data, send to setup.php, if not present, rename, if still not present, fail;
if (!file_exists(STAKER_DATA) && basename($_SERVER['PHP_SELF']) != "setup.php") {

   if (!file_exists("setup.php")) {

      foreach(glob("setup*.php") as $target) {

         rename($target, "setup.php");

      }

      if (!file_exists("setup.php")) {

         die("StripeTaker :: You do not have a setup file or configuration data. Without the setup file you cannot setup your installation. Please contact helpdesk@nightkingdoms or support@nightkingdoms.com with your serial number to be provided a copy of the setup file.");

      }

   } else {

      header("Location:setup.php");
      die();

   }

}

   $StripeTaker_SaveFile_Data['version'] = "1.01b";

?>
