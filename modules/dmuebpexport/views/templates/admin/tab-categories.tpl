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

{if $dmuebp_compte_priorite == 1}
<form id="module_form_10" 
    class="defaultForm form-horizontal" action="{$url}" 
    method="post" enctype="multipart/form-data" novalidate="">
        <input type="hidden" name="Configuration6" value="1">
        <input type="hidden" name="form_id" id="form_id" value="{$form_id|escape:'htmlall':'UTF-8'}">
        <div class="panel" id="fieldset_0_4_4">                     
            <div class="panel-heading"><i class="icon-cogs"></i>&nbsp;{$title|escape:'htmlall':'UTF-8'}</div>

            {foreach from=$array_categories item=categorie}
            <div class="form-group recursive-group">
                <div class="spacer spacer_{$categorie.cat.level_depth|escape:'htmlall':'UTF-8'}"></div>
                <label class="control-label col-sm-2 col-lg-2"><strong>{$categorie.cat.name|escape:'htmlall':'UTF-8'}</strong></label>
                <div class="col-sm-2 col-lg-2">
                    <span>{$txt_vat|escape:'htmlall':'UTF-8'}:</span>
                    <input  type="text" 
                            name="DMUEBP_CATEGORY_TTC[{$categorie.cat.id_category|escape:'htmlall':'UTF-8'}]" 
                            id="DMUEBP_CATEGORY_TTC[{$categorie.cat.id_category|escape:'htmlall':'UTF-8'}]" 
                            value="{$categorie.dmuebp_category_ttc_value|escape:'htmlall':'UTF-8'}" 
                            class="input fixed-width-sm">
                </div>  
                <div class="col-sm-2 col-lg-2">
                    <span>{$txt_novat|escape:'htmlall':'UTF-8'}:</span>
                    <input  type="text" 
                            name="DMUEBP_CATEGORY_HT[{$categorie.cat.id_category|escape:'htmlall':'UTF-8'}]" 
                            id="DMUEBP_CATEGORY_HT[{$categorie.cat.id_category|escape:'htmlall':'UTF-8'}]" 
                            value="{$categorie.dmuebp_category_ht_value|escape:'htmlall':'UTF-8'}" 
                            class="input fixed-width-sm">
                </div>
            </div>
            {/foreach}

            <div class="panel-footer">
            <button type="submit" value="1" id="module_form_submit_btn_10" 
            name="Configuration6" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {$txt_update}
            </button>
        </div>
    </div>
</form>
{else}
<div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert">×</button>{$txt_warning|escape:'htmlall':'UTF-8'}
    <ul style="display:block;" id="seeMore">
        <li>{$txt_choose|escape:'htmlall':'UTF-8'}</li>
    </ul>
</div>
{/if}