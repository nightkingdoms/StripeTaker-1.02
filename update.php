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

   require_once("core/init.php");

   if (!CheckSession()) { header("Location:login.php"); die(); }

   $headers_appname = 1;
   $license_data = DataOps_ReadFile(STAKER_CORE2);
   $this_version = $StripeTaker_SaveFile_Data['version'];
   $that_version = $license_data['version'];
   settype($this_version, "float");
   settype($that_version, "float");


   switch($_REQUEST['op']) {

      case "UpdateNow":
      Update_Now();
      break;

      default:
      Update_Main();
      break;

   }


function Update_Now() {
   global $StripeTaker_SaveFile_Data, $headers_appname;

   $check_verify = md5(date("Y-m-d" . ""));

   if ($check_verify == $_REQUEST['verify']) {

      Update_MakeBackups();

      if (isset($license_data['update_url']) && $license_data['update_url'] != "") {

         Update_DownloadFile($license_data['update_url']);

      } else {

         Update_DownloadFile();

      }

   } else {

      die("StripeTaker :: Invalid function call.");

   }

}


function Update_MakeBackups() {

   if(file_exists(STAKER_CORE)) { if (!Update_BackupFile("data/", "stcore")) { die("StripeTaker :: Failed to backup stcore."); } }
   if(file_exists(STAKER_CORE2)) { if (!Update_BackupFile("data/", "stcore2")) { die("StripeTaker :: Failed to backup stcore2."); } }
   if(file_exists(STAKER_DATA)) { if (!Update_BackupFile("data/", "stdata")) { die("StripeTaker :: Failed to backup stdata."); } }
   if(file_exists(STAKER_PRODS)) { if (!Update_BackupFile("data/", "stprods")) { die("StripeTaker :: Failed to backup stprods."); } }
   if(file_exists(STAKER_ORDERS)) { if (!Update_BackupFile("data/", "storders")) { die("StripeTaker :: Failed to backup storders."); } }
   if(file_exists(STAKER_CUSTS)) { if (!Update_BackupFile("data/", "stcusts")) { die("StripeTaker :: Failed to backup stcusts."); } }

}


function Update_BackupFile($loc, $file) {

   if(file_exists($loc . $file)) {

      $data = "";

      // get file contents;
      $fhandle = fopen($loc . $file, "rt");

      while(!feof($fhandle)) {

         $data .= fgets($fhandle, 8192);

      }

      fclose($fhandle);


      // output to backup directory;
      $loc .= "backups/";
      $file .= "-" . date("mdY", time());
      $fhandle2 = fopen($loc . $file, "w");
      fwrite($fhandle2, $data);
      fclose($fhandle2);

      if (file_exists($loc . $file)) { return true; } else { return false; }

   } else {

      return false;

   }

}


function Update_Unpack() {
   global $StripeTaker_SaveFile_Data, $headers_appname;

   include("res/pcl/pclzip.lib.php");
   $local_file = "update.zip";

   $archive = new PclZip($local_file);

   // rename setup file to overwrite;
   foreach(glob("setup*.php") as $target) {

      rename($target, "setup.php");

   }

   include("headers.inc.php");

   if ($archive->extract(PCLZIP_OPT_REPLACE_NEWER) != 0) {

      ?><div class="alert alert-success">

      <center><h1><i class="icon-ok-sign icon-large" style="font-size: 1.2em;"></i> Update Is Ready!</h1>

      <p><b>Your files have been updated to the latest version!  One more step to update any database fixes!</b></p>

      <p><button class="btn btn-success btn-large" onClick="javascript:location.href='manage.php?op=Install';">Click here to continue.</button></p></center>

      </div><?php

      unlink($local_file);

   } else {

      ?><div class="alert alert-error">

      <center><h1><i class="icon-warning-sign icon-large" style="font-size: 1.2em;"></i> Update Error</h1>

      <p>The update failed to extract the downloaded package.  You can <a href="javascript:location.href='update.php?';">try again</a> if you believe this to be in error.</p>

      <p><b>If you continue to get this error, please contact us (<b>helpdesk@nightkingdoms.com</b>).</b></p>

      <p><button class="btn btn-large" onClick="javascript:location.href='manage.php?';">Return to Store Management</button></p></center>

      </div><?php

      unlink($local_file);

   }

   include("footers.inc.php");

   // rename setup file back to random string;
   rename("setup.php", "setup" . strtoupper(substr(md5(uniqid(rand())), 0, 6)) . ".php");

}


function Update_DownloadFile($file='none') {
   global $StripeTaker_SaveFile_Data, $headers_appname;

   $local_file = "update.zip";

   if ($file == "none") {

      $update_url = "http://nightkingdoms.com/StripeTaker/update.zip";

   } else {

      $update_url = $file;

   }

   set_time_limit(0); // set no time limit to download large file
   //ini_set('display_errors',true);//Just in case we get some errors, let us know....
 
   $fp = fopen ($local_file, 'w+');//where the file will be saved
   $ch = curl_init($update_url);//Here is the file we are downloading
   curl_setopt($ch, CURLOPT_TIMEOUT, 50);
   curl_setopt($ch, CURLOPT_FILE, $fp);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
   curl_exec($ch);

   if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != "404") {

      Update_Unpack();

   } else {

      include("headers.inc.php");

      ?><div class="alert alert-error">

      <center><h1><i class="icon-warning-sign icon-large" style="font-size: 1.2em;"></i> Update Error</h1>

      <p>The update failed to download.  You can <a href="javascript:location.href='update.php?';">try again</a> if you believe this to be in error.</p>

      <p><b>If you continue to get this error, please contact us (<b>helpdesk@nightkingdoms.com</b>).</b></p>

      <p><button class="btn btn-large" onClick="javascript:location.href='manage.php?';">Return to Store Management</button></p></center>

      </div><?php

      include("footers.inc.php");

   }


   curl_close($ch);
   fclose($fp);

}


function Update_Main() {
   global $StripeTaker_SaveFile_Data, $headers_appname, $license_data, $this_version, $that_version;

      include("headers.inc.php");

      if ($license_data['addon_Updates'] == "Active") {

         if ($this_version < $that_version) {

            ?><center><h1 style="color: #0088ff;"><i class="icon-download icon-large" style="font-size: 1.2em;"></i> Update Available</h1>

            <h3>Your Version: <?php echo $StripeTaker_SaveFile_Data['version']; ?></h3>

            <h3>Current Version: <?php echo $license_data['version']; ?></h3>

            <p><b>Our update process is entirely automated.  A backup of all your data will be created before we modify any files.</b></p>

            <p><b>To start the update process, click the button below:</b></p>

            <p><button class="btn btn-primary btn-large" onClick="javascript:location.href='';">Begin Upgrade</button></p>

            <p><button class="btn btn-large" onClick="javascript:location.href='manage.php?';">Return to Store Management</button></p></center>

            <?php

         } elseif ($this_version == $that_version) {

            ?><center><h1 style="color: #0088ff;"><i class="icon-info-sign icon-large" style="font-size: 1.2em;"></i> Already The Current Version</h1>

            <p>You are already using the most-current version of this program.</p>

            <p><button class="btn btn-large" onClick="javascript:location.href='manage.php?';">Return to Store Management</button></p></center>

            <?php

         }

      } else {

         ?><center><h1 style="color: #0088ff;"><i class="icon-info-sign icon-large" style="font-size: 1.2em;"></i> Upgrades Not Authorized</h1>

         <p>Your service plan does not include updates.  Please log into your account online to add Upgrades to your service.</p>

         <p><button class="btn btn-info btn-large" onClick="location.href='https://secure.x-mirror.com/erp/login.php';">Log In To Your Account</button></p>

         <p><button class="btn btn-large" onClick="javascript:location.href='manage.php?';">Return to Store Management</button></p></center>

         <?php

      }

      include("footers.inc.php");

}

?>
