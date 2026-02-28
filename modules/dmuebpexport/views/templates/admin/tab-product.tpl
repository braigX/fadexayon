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

<input type="hidden" name="accounting_loaded" value="1">
{if isset($id_product)}

    <div id="product-accounting" class="panel product-tab">
        <input type="hidden" name="submitted_tabs[]" value="Accounting" />
        <h3>{$title_form|escape:'htmlall':'UTF-8'}</h3>
        <div class="alert alert-info">{$desc|escape:'htmlall':'UTF-8'}</div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="accounting_vat">
                <span class="label-tooltip" data-toggle="tooltip" title="">{$accounting_number|escape:'htmlall':'UTF-8'}</span>
            </label>
            <div class="col-lg-5">
                <input type="text" id="accounting_vat" name="accounting_vat" value="{$accounting_vat|escape:'htmlall':'UTF-8'}" />
            </div>
            <div class="col-lg-9 col-lg-offset-3">
                <div class="help-block">{$default_value_category|escape:'htmlall':'UTF-8'}&nbsp;{$default_accounting_vat|escape:'htmlall':'UTF-8'}</div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="accounting_no_vat">
                <span class="label-tooltip" data-toggle="tooltip" title="">{$accounting_number_no_VAT|escape:'htmlall':'UTF-8'}</span>
            </label>
            <div class="col-lg-5">
                <input type="text" id="accounting_no_vat" name="accounting_no_vat" value="{$accounting_no_vat|escape:'htmlall':'UTF-8'}" />
            </div>
            <div class="col-lg-9 col-lg-offset-3">
                <div class="help-block">{$default_value_category|escape:'htmlall':'UTF-8'}&nbsp;{$default_accounting_no_vat|escape:'htmlall':'UTF-8'}</div>
            </div>
        </div>

        <div class="panel-footer">
            <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {$cancel|escape:'htmlall':'UTF-8'}</a>
            <button type="submit" name="submitAddproduct" class="btn btn-default pull-right"{if !$ps16_old} disabled="disabled"{/if}><i class="process-icon-{if !$ps16_old}loading{else}save{/if}"></i> {$save|escape:'htmlall':'UTF-8'}</button>
            <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"{if !$ps16_old} disabled="disabled"{/if}><i class="process-icon-{if !$ps16_old}loading{else}save{/if}"></i> {$save_and_stay|escape:'htmlall':'UTF-8'}</button>
        </div>
    </div>

{else}

    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {$one_warning|escape:'htmlall':'UTF-8'}
        <ul style="display:block;" id="seeMore">
            <li>{$warning_message|escape:'htmlall':'UTF-8'}</li>
        </ul>
    </div>

{/if}
