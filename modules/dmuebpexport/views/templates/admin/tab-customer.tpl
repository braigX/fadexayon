{**
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from SARL DREAM ME UP
* Use, copy, modification or distribution of this source file without written
* license agreement from the SARL DREAM ME UP is strictly forbidden.
*
*   .--.
*   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
*   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
*   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
*        w w w . d r e a m - m e - u p . f r       '
*
*  @author    Dream me up <prestashop@dream-me-up.fr>
*  @copyright 2007 - 2024 Dream me up
*  @license   All Rights Reserved
*}

<div class="col-lg-12">
    <div class="panel" id="dmuebpexport-panel">
        <div class="panel-heading">
            <i class="icon-euro"></i> {l s='Accounting export with EBP format' mod='dmuebpexport'}
        </div>
        <form id="accounting_customer_form" class="form-horizontal" method="post" onSubmit="return false;">
            <div class="form-group">
                <label class="control-label col-lg-3" for="accounting_customer">
                    <span class="label-tooltip" data-toggle="tooltip" title="">{l s='Account number' mod='dmuebpexport'}</span>
                </label>
                <div class="col-lg-5">
                    <input type="text" id="accounting_customer" name="accounting_customer" placeholder="{$default_accounting_customer|escape:'htmlall':'UTF-8'}" value="{$accounting_customer|escape:'htmlall':'UTF-8'}">
                </div>
                <div class="col-lg-4">
                    <button type="submit" id="submitAccountingCustomer" class="btn btn-default pull-right">
                        <i class="icon-save"></i>
                        {l s='Save' mod='dmuebpexport'}
                    </button>
                </div>
                <div class="col-lg-9 col-lg-offset-3">
                    <div class="help-block">{l s='Default value if empty:' mod='dmuebpexport'}&nbsp;{$default_accounting_customer|escape:'htmlall':'UTF-8'}</div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
var border_color_memory;
$(document).ready(function() {
    if ($('.icon-user').length) {
        $('#dmuebpexport-panel').detach().insertAfter($('.icon-user').closest('.panel'));
    }
    $('#submitAccountingCustomer').click(function() {
        accounting_customer = $('#accounting_customer').val();
        if (!border_color_memory) border_color_memory = $('#accounting_customer').css('border-color');
        $('#accounting_customer').css('border-color', '#900').attr('disabled', true).addClass('disabled');
        $(this).addClass('disabled');
        $.ajax({
            type: 'POST',
            url: document.location,
            async: true,
            cache: false,
            dataType : "json",
            data: {
                ajax : 1,
                action : 'dmueeSetAccountingCustomer',
                accounting_customer : accounting_customer,
            },
            success: function(json)
            {
                $('#accounting_customer').css('border-color', border_color_memory);
                if (json.success) {
                    $('#accounting_customer').val(json.accounting_customer).attr('disabled', false).removeClass('disabled').css('border-color', '#080');
                    $('#submitAccountingCustomer').removeClass('disabled');
                }
            }
        });
    });
});
</script>