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

   include("core/init.php");

   if ($_REQUEST['op'] == "Process") {

      if ($StripeTaker_SaveFile_Data['password'] == hash("sha512", $_REQUEST['password'], false))) {

         AddSession();
         header("Location:manage.php");
         die();

      } else {

         $errors['alert-msg'] = "<b>Password invalid.</b>";
         $error['password'] = "";

      }

   }

   $header_login = 1;
   include("headers.inc.php");

?>

<?php if (isset($errors['alert-msg'])) { ?>
<div class="alert alert-error"><?php echo $errors['alert-msg']; ?></div>
<?php } ?>

<?php if (isset($_REQUEST['info'])) {?>
<div class="alert alert-info"><?php echo $_REQUEST['info']; ?></div>
<?php } ?>

<center><div class="thumbnail" style="background: #ffffff; width: 300px; padding-left: 40px; text-align: left;">
<form method="POST" action="login.php?op=Process" id="login_form">

<h3>Store Management</h3>
<p><label for="password">Enter your password to continue:</label>
<input type="password" name="password" id="password" class="input-medium<?php if (isset($errors['password'])) { echo " error"; } ?>" placeholder="MySecret" /></p>
<button class="btn btn-primary btn-large" onClick="javascript:document.forms['login_form'].submit(); this.disabled=true;"><i class="icon-lock"></i>&nbsp; Login</button>
</form>
</div></center>

<?php

   include("footers.inc.php");

?>
