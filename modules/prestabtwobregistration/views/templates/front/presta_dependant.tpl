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
{if isset($dependantFields) && $dependantFields}
    <div class="form-group row">
            <label class="col-md-3 control-label">
                {l s='Select Value' mod='prestabtwobregistration'}
            </label>
        <div class="form-group col-md-6 presta-dependant">
            {foreach $dependantFields as $key => $values}
                {if $values.field_type == 'text'}
                    <input
                        {* {if $key === 0} checked="checked"{/if} *}
                        type="text"
                        name="presta_dependant"
                        class="form-control"
                        value=""
                        class="form-controller"/>
                    {* <label class="form-control container">{$values.is_dependant}</label> *}
                {elseif ($values.field_type == 'dropdown')}
                    <input
                        {* {if $key === 0} checked="checked"{/if} *}
                        type="checkbox"
                        name="presta_dependant"
                        value=""
                        class="form-controller"/>
                    {* <label class="form-control container">{$values.is_dependant}</label> *}
                {/if}
            {/foreach}
        </div>
    </div>
{/if}
