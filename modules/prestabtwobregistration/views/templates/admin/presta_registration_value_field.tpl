{**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 *}
<div class="form-group clearfix select_urls">
    <label class="col-md-3 control-label"></label>
    <div class="col-lg-6 select-presta-product">
        {foreach from=$languages item=language}
            {assign var="presta_current_div" value="presta_current_registration_fields_div_`$language.id_lang`"}
            {assign var="name" value="presta_registration_multi_value_field_`$language.id_lang`[]"}
            <div class="presta_registration_main_div {$presta_current_div|escape:'htmlall':'UTF-8'}" {if
                $current_lang->id != $language.id_lang}style="display:none;"{/if}>
                <input
                    type="text"
                    autocomplete="off"
                    id="presta_registration_multi_value_field_{$language.id_lang|escape:'htmlall':'UTF-8'}"
                    name="{$name|escape:'htmlall':'UTF-8'}" class="form-control"
                    value="" />
            </div>
        {/foreach}
    </div>
    {if count($languages) > 1}
    <div class="col-md-3 input-group-btn">
        <button class="btn btn-danger multiple_delete" type="button"><i class="icon icon-trash"></i></button>
    </div>
    {/if}
</div>
