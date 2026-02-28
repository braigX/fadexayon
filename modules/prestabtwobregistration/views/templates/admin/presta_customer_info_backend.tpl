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
{if isset($customerFields) && $customerFields}
    <div class="col">
        <div class="card card-block">
            <h3 class="card-header">
                <i class="material-icons">info</i>
                {l s='Custom Field Data' mod='prestabtwobregistration'}
            </h3>

            <div class="card-body">
                {foreach $customerFields as $field}
                    <div class="row mb-1">
                        <div class="col-4 text-right" style="margin-top: 8px;">
                            {$field.field_title|escape:'htmlall':'UTF-8'} :
                        </div>
                        {if $field['field_type'] === 'message'}
                            <div class="alert alert-{if $field.notice_types == 1}danger{else if $field.notice_types == 2}warning{else if $field.notice_types == 3}info{else if $field.notice_types == 4}success{/if}">
                                <div class="col-4 text-right" style="display: contents;margin-top: 8px;">
                                    {$field.field_value|escape:'htmlall':'UTF-8'}
                                </div>
                            </div>
                        {else if $field['field_type'] === 'file'}
                            <a
                                href="{$field.downloadLink|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-lg">
                                {l s='Download File' mod='prestabtwobregistration'}
                            </a>
                        {else}
                            <div class="col-md-8" style="margin-top: 7px;">
                                {$field.field_value|escape:'htmlall':'UTF-8'}
                            </div>
                        {/if}
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
{/if}
