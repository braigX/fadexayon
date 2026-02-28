{if isset($box_mail) && $box_mail}
  <div>
    <div class="box-content box-mail-content">
      <script>

        document.addEventListener("DOMContentLoaded", function(event) {
          $('#contactable').contactable({
              subject: '{$shop.name} Message rapide',
              header: '{l|escape:'javascript' s='Contact Us' d='Modules.Roylevibox.Mail'}',
              url: '{$urls.base_url}modules/roy_levibox/mail.php',
              name: '{l|escape:'javascript' s='Name' d='Modules.Roylevibox.Mail'}',
              email: '{l|escape:'javascript' s='Email' d='Modules.Roylevibox.Mail'}',
              customermail: '{$customer.email}',
              message : '{l|escape:'javascript' s='Message' d='Modules.Roylevibox.Mail'}',
              submit : '{l|escape:'javascript' s='SEND' d='Modules.Roylevibox.Mail'}',
              recievedMsg : '{l|escape:'javascript' s='Thank you for your message' d='Modules.Roylevibox.Mail'}',
              notRecievedMsg : '{l|escape:'javascript' s='Sorry but your message could not be sent, try again later.' d='Modules.Roylevibox.Mail'}',
              footer: '{l|escape:'javascript' s='Please feel free to get in touch, we will answer as soon as possible.' d='Modules.Roylevibox.Mail'}',
              hideOnSubmit: true
            });
        });

      </script>
      <div id="contactable"></div>

      {widget name='ps_socialfollow'}

    </div>
  </div>

{/if}
