{*
* Since 2013 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
* @version   Release: 4
*}

{if $customer_account_subscribe_by_loi_active|intval && count($list_of_interest) > 0}
    <div class="pqnp-list-of-interest form-group row ">
        <label class="pqnp-list-of-interest-label col-md-3 form-control-label">
            {l s='Interested in' mod='newsletterpro'}
        </label>
        <div class="col-md-6">
            {foreach $list_of_interest as $item}
                <span class="pqnp-custom-checkbox">
                    <label>
                        <input name="pqnp_list_of_interest[]" type="checkbox" value="{$item.id_newsletter_pro_list_of_interest}" {if $item.checked|intval}checked="checked"{/if}>
                        <span><i class="pqnp-icon-checkbox-checked pqnp-checkbox-checked"></i></span>
                        <span>{$item.name}</span>
                    </label>
                </span>
            {/foreach}
        </div>
    </div>
{/if}

