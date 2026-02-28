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
<div class="col-lg-2">
    <div class="list-group">
        <a href="#default_setting" class="list-group-item presta-active active" data-toggle="tab">
            <i class="icon-wrench"></i>
            {l s='Default Setting' mod='prestabtwobregistration'}
        </a>
        <a href="#general_setting" class="list-group-item presta-active" data-toggle="tab">
            <i class="icon-filter"></i>
            {l s='General Setting' mod='prestabtwobregistration'}
        </a>
    </div>
</div>
<div class="col-lg-10">
    <div class="panel">
        <form method="post" id="prestaManageStorePaymentsForm" class="defaultForm form-horizontal tab-content">
            <input
                type="hidden"
                name="id_presta_btwob_registration_configuration"
                value="{if isset($presta_config) && $presta_config}{$presta_config->id|escape:'htmlall':'UTF-8'}{/if}">
            <div class="tab-pane active" id="default_setting">
                {include file="module:prestabtwobregistration/views/templates/admin/presta_configuration/helpers/form/_partials/default_setting.tpl"}
            </div>
            <div class="tab-pane" id="general_setting">
                 {include file="module:prestabtwobregistration/views/templates/admin/presta_configuration/helpers/form/_partials/general_setting.tpl"}
            </div>
            <div class="panel-footer">
                <button type="submit" value="1" name="prestabtwobregistrationconfigurationbtn" class="btn btn-default  pull-right">
                    <i class="process-icon-save"></i> {l s='Save' mod='prestabtwobregistration'}
                </button>
            </div>
        </form>
    </div>
</div>

