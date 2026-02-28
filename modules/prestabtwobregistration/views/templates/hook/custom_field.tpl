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
<div class="form-group row ">
    <label class="col-md-3 form-control-label required">{l s='Customer Group' mod='prestabtwobregistration'}</label>
    <div class="col-md-6">
        <select class="form-control form-control-select" name="customer_group" required="required">
            <option value="" disabled="disabled" selected="">{l s='-- please choose --' mod='prestabtwobregistration'}</option>
            {if isset($presta_groups) && $presta_groups}
                {foreach $presta_groups as $gp}
                    <option
                        {if isset($presta_default_group) && $presta_default_group == $gp.id_group}
                        selected="selected"
                        {else if Configuration::get('PRESTA_GROUP_DEFAULT_GROUP') == $gp.id_group}
                        selected="selected"
                        {/if}
                        value="{$gp.id_group|escape:'htmlall':'UTF-8'}">{$gp.name|escape:'htmlall':'UTF-8'}
                    </option>
                {/foreach}
            {/if}
        </select>
        <div style="display:none;" class="alert alert-warning" id="validateInfo">
            {l s='Your account will be validated by admin' mod='prestabtwobregistration'}
        </div>
    </div>
    <div class="col-md-3 form-control-comment"></div>
</div>
