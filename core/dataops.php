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

define("STAKER_CORE", "data/stcore"); // license data (unreadable);
define("STAKER_CORE2", "data/stcore2"); // license data (readable);
define("STAKER_DATA", "data/stdata"); // system data;
define("STAKER_PRODS", "data/stprods"); // products database;
define("STAKER_ORDERS", "data/storders"); // orders database;
define("STAKER_CUSTS", "data/stcusts"); // customers database;


   $StripeTaker_SaveFile_Data = DataOps_ReadFile(STAKER_DATA);

   $StripeTaker_SaveFile_Data['demo_mode'] = "No";


function DataOps_Data_Insert($file, $data) {

   $tmp_data = DataOps_ReadFile($file);
   $tmp_data[] = $data;

   DataOps_SaveFile($file, $tmp_data);

}


function DataOps_Data_Delete($file, $id) {

   $tmp_data = DataOps_ReadFile($file);

   $data_pos = DataOps_Data_Search($file, $id);

   unset($tmp_data[$data_pos]);

   DataOps_SaveFile($file, $tmp_data);

}


function DataOps_Data_Update($file, $data, $id) {

   $tmp_data = DataOps_ReadFile($file);

   $data_pos = DataOps_Data_Search($file, $id);

   unset($tmp_data[$data_pos]);

   $tmp_data[] = $data;

   natcasesort($tmp_data);
   

   DataOps_SaveFile($file, $tmp_data);   

}


function DataOps_Data_Get($file, $param) {

   $data_pos = DataOps_Data_Search($file, $param);

   $tmp_data = DataOps_ReadFile($file);

   return $tmp_data[$data_pos];

}


function DataOps_Data_Search($file, $param) {

   $data = DataOps_ReadFile($file);
   $data_pos = -1;

   foreach ($data as $key => $value) {

      if (array_search($param, $value)) { $data_pos = urlencode($key); }

   }


   if ($data_pos >= 0) {

      return $data_pos;

   } else {

      return false;

   }

}


function DataOps_ReadFile($file) {

   if (file_exists($file)) {

      $data = "";

      // file exists, copy file data into buffer;
      $fhandle = fopen($file, "rt");

      while(!feof($fhandle)) {

         $data .= fgets($fhandle, 8192);

      }

      fclose($fhandle);

      // perform operations to transform data into an array;
      $data = json_decode(Decrypt($data), true);

      return $data;

   } else {

      $no_data = array();
      return $no_data;

   }

}


function DataOps_TestFile($file) {

   $data = DataOps_ReadFile($file);

   print_r($data);

}


function DataOps_SaveFile($file, $data) {

   $fhandle = fopen($file, "w");
   $data = Encrypt(json_encode($data));

   fwrite($fhandle, $data);
   fclose($fhandle);

}


function DataOps_SetupDataFile() {

   $fhandle = fopen(STAKER_DATA, "w");
   fwrite($fhandle, "VQzqFF+24jlF//QTFqpW1Q==");
   fclose($fhandle);

}


function DataOps_SetupProdsFile() {

   $data = array();

   $data = Encrypt(json_encode($data));

   $fhandle = fopen(STAKER_PRODS, "w");
   fwrite($fhandle, $data);
   fclose($fhandle);

}


function DataOps_Outbound($url, $postfields=0) {

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_TIMEOUT, 100);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   if ($postfields != 0) { curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields); }
   curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
   curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
   curl_setopt($ch, CURLOPT_FAILONERROR, 1);
   curl_setopt($ch, CURLOPT_MUTE, 1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
   $data = @curl_exec($ch);
   curl_close($ch);

   return $data;

}

?>
