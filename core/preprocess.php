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

StripeTaker_PreProcessor();

function StripeTaker_PreProcessor() {
   global $StripeTaker_SaveFile_Data;

   if ($StripeTaker_SaveFile_Data['mode'] == "Test") { $show_key = $StripeTaker_SaveFile_Data['key_test_p']; } else { $show_key = $StripeTaker_SaveFile_Data['key_live_p']; }

?>
<!--
   NK StripeTaker (c) 2012 NightKingdoms LLC. All rights reserved.

   -=- Support For This Program -=-
   Web: http://support.nightkingdoms.com
   Email: helpdesk@nightkingdoms.com
   Twitter: @nightkingdoms
   Facebook: http://nk2.us/fb
-->

    <script type="text/javascript" src="https://js.stripe.com/v1/"></script>
    <script type="text/javascript">
       Stripe.setPublishableKey('<?php echo $show_key; ?>');
            function stripeResponseHandler(status, response) {
                if (response.error) {
                    $('.submit-button').removeAttr("disabled");
                    $(".payment-errors").html(response.error.message);
                } else {
                    var form$ = $("#payment-form");
                    var token = response['id'];
                    form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                    form$.get(0).submit();
                }
            }

            $(document).ready(function() {
                $("#payment-form").submit(function(event) {
                    $('.submit-button').attr("disabled", "disabled");

                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                    return false;
                });
            });
        </script>
<?php

}

?>
