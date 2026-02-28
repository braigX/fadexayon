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
{if isset($multipleValues) && $multipleValues}
    <div class="form-group clearfix presta_select">
        <label class="col-md-3 control-label">{l s='Select Value' mod='prestabtwobregistration'}</label>
        <div class="col-md-6 input-container">
            <div id="customer_gender_id">
                {foreach $multipleValues as $key => $values}
                    <div class="form-check form-check-radio form-radio">
                        <label class="form-check-label">
                            <input
                                {if isset($objCurrentId) && $objCurrentId->id_dependant_value == $values.id_multi_value}
                                    checked="checked"
                                {elseif isset($objCurrentId) && $objCurrentId->id_dependant_value == $values.id_multi_value}
                                    checked="checked"
                                {else if isset($objCurrentId) && $objCurrentId->id_dependant_value === $values.id_multi_value}
                                    checked="checked"
                                {/if}
                                type="radio"
                                name="presta_dependant"
                                class="form-check-input"
                                value="{$values.id_multi_value|escape:'htmlall':'UTF-8'}">
                            <i class="form-check-round"></i>{$values.value|escape:'htmlall':'UTF-8'}
                        </label>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
{/if}
