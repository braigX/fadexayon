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

<div class="presta-btob-btns clearfix">
    <div class="presta-button">
        <input
            type="radio"
            id="presta_private"
            name="presta_btob_registration"
            value="0"/>
        <label class="btn btn-default" for="presta_private">{l s='Register as Private' mod='prestabtwobregistration'}</label>
    </div>
    <div class="presta-button">
        <input
            type="radio"
            id="presta_company"
            name="presta_btob_registration"
            value="1"
            checked/>
        <label class="btn btn-default" for="presta_company">{l s='Register as company' mod='prestabtwobregistration'}</label>
    </div>
</div>
<h4>
{$prestaConfigData->personal_data_heading|escape:'htmlall':'UTF-8'}
</h4>


