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

?>

<p />&nbsp;<br /><p />&nbsp;<br /><p />

   <?php if(file_exists("footers-custom.php")) { include("footers-custom.php"); } ?>

   <?php

      $footer_license_data = DataOps_ReadFile(STAKER_CORE2);

      if ($footer_license_data['addon_BrandingRemoval'] != "Active") {

   ?>
   <footer class="footer" style="border-style: solid; border: 0px; border-top: 1px solid #eeeeee; padding-top: 5px;">
   <a href="http://nightkingdoms.com/StripeTaker/" target="_blank"><img src="img/stripetaker_bug.png" border="0"></a> 
   <a href="http://stripe.com/" target="_blank"><img src="img/stripe-footer.png" border="0"></a>
   </footer>
   <?php } else { ?>

      <footer class="footer" style="border-style: solid; border: 0px; border-top: 1px solid #eeeeee; padding-top: 5px;">
      Copyright © <?php echo date("Y", time()); ?> <?php echo $StripeTaker_SaveFile_Data['storename']; ?>
      </footer>

   <?php } ?>

      </div><!--/span12-->
   </div><!--/row-fluid-->
</div><!--/container-->

<?php echo "<!-- " . date("m-d-Y") . " Copyright (c) " . date("Y") . " NightKingdoms LLC. All rights reserved. -->"; ?>

<?php if (file_exists("analytics.php")) { include("analytics.php"); } ?>

<div style="display: none;" id="finder">
Once upon a time, there was a little boy who lived in the NightKingdoms...
</div>

</body>
</html>
