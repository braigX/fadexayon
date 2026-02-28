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
<div class="col-12">
    <div class="card" id="dmuebpexport-panel">
        <div class="card-header">
            <i class="icon-euro"></i> {l s='Accounting export with EBP format' mod='dmuebpexport'}
        </div>
        <form id="accounting_customer_form" class="card-body" method="post" onSubmit="return false;" action="{$controller_ebp|escape:'htmlall':'UTF-8'}">
            <div class="form-group row">
                <label class="control-label col-3" for="accounting_customer">
                    <span class="label-tooltip" data-toggle="tooltip" title="">{l s='Account number' mod='dmuebpexport'}</span>
                </label>
                <div class="col-5">
                    <input type="text" id="accounting_customer" name="accounting_customer" class="form-control" placeholder="{$default_accounting_customer|escape:'htmlall':'UTF-8'}" value="{$accounting_customer|escape:'htmlall':'UTF-8'}">
                </div>
                <div class="col-4">
                    <button type="submit" id="submitAccountingCustomer" class="btn btn-primary pull-right">
                        <i class="icon-save"></i>
                        {l s='Save' mod='dmuebpexport'}
                    </button>
                </div>
                <div class="col-9 offset-3">
                    <div class="help-block text-muted">{l s='Default value if empty:' mod='dmuebpexport'}&nbsp;{$default_accounting_customer|escape:'htmlall':'UTF-8'}</div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
var border_color_memory;
$(document).ready(function() {
    $('#submitAccountingCustomer').click(function() {
        accounting_customer = $('#accounting_customer').val();
        if (!border_color_memory) border_color_memory = $('#accounting_customer').css('border-color');
        $('#accounting_customer').css('border-color', '#900').attr('disabled', true).addClass('disabled');
        $(this).addClass('disabled');
        $.ajax({
            type: 'POST',
            url: $('#accounting_customer_form').attr('action'),
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