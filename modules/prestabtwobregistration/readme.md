# Important Note -
1. Add the below code in CustomerForm.php in validate function above of "return parent::validate();".
    Path - classes/form/CustomerForm.php

    if (Module::isEnabled('prestabtwobregistration') && Module::isInstalled('prestabtwobregistration')) {
        $prestaNoError = Hook::exec('actionSubmitAccountBefore', [], null, true);
        if (!$prestaNoError['prestabtwobregistration']) {
            return false;
        }
    }

2. Add the below code in customer-form.tpl
    Path - themes/active_theme_folder/templates/customer/_partials/customer-form.tpl
    Add [enctype="multipart/form-data"] attribute in form tag
    example: <form action="{block name='customer_form_actionurl'}{$action}{/block}" id="customer-form" class="js-customer-form" method="post" enctype="multipart/form-data">
