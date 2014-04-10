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

   // determine if running as HTTP or HTTPS;
   if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") { $header_protocol = "https"; } else { $header_protocol = "http"; }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php

      if (!isset($StripeTaker_SaveFile_Data['storename']) || $StripeTaker_SaveFile_Data['storename'] == "") {

         $StripeTaker_SaveFile_Data['storename'] = "NK StripeTaker";

      }

      if ($headers_appname == 1) { echo "NK StripeTaker"; } else { echo $StripeTaker_SaveFile_Data['storename']; }

   ?></title>
   <meta name="robots" content="all,index,follow" />
   <meta name="revisit-after" content="3" />
   <meta name="generator" content="StripeTaker <?php echo $StripeTaker_SaveFile_Data['version']; ?>" />

    <script src="js/jquery-1.8.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="css/font-awesome.css" />
    <link href='<?php echo $header_protocol; ?>://fonts.googleapis.com/css?family=Archivo+Black&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <?php if ($header_credit == 1) { include("core/preprocess.php"); } ?>

    <script type='text/javascript'>
    </script>

    <style type="text/css">
      * {-webkit-print-color-adjust: exact;}
      input.error {
         color: #b94a48;
         background-color: #f2dede;
         border: 1px solid #eed3d7;
      }
      select.error {
		   color: #b94a48;
		   background-color: #f2dede;
		   border: 1px solid #eed3d7;
	   }
      .error {
		   color: #b94a48;
		   background-color: #f2dede;
		   border: 1px solid #eed3d7;
	   }
	   .alert-error {
		   color: #b94a48;
		   background-color: #f2dede;
		   border-color: #eed3d7;
	   }
      body {
         padding-top: 45px;
         font-size: 16px;
         <?php if ($header_login == 1) { ?>background: #cccccc;<?php } ?>
      }
      .brand {
         font-family: 'Archivo Black', sans-serif;
      }
      th {
         font-weight: bold;
      }
      .btn {
         font-weight: bold;
      }
      label {
         font-weight: bold;
      }
    </style>

      <?php if(file_exists("headers-custom.php")) { include("headers-custom.php"); } ?>
   </head>

<body>

<div class="container-fluid">

<div class="page-header">
   <div class="row-fluid">
      <div class="span12" style="margin-top: -60px;">


<h1><span class="brand"><?php

   $logo_imgs = glob("img/logo.*");

   if (count($logo_imgs) > 0) {

      
      $show_logo = $logo_imgs[0];

      ?><img src="<?php echo $show_logo; ?>" border="0"><?php

   } else {

      if ($headers_appname == 1) {

         echo "NK StripeTaker";

      } else {

         echo $StripeTaker_SaveFile_Data['storename'];

      }

   }

?></span> 
<?php if (isset($header_pagename) && $browser_type != "desktop") { echo "<br />"; } ?>
<?php if (isset($header_pagename)) { echo "<small><em>$header_pagename</em></small>"; } ?></h1></div>
