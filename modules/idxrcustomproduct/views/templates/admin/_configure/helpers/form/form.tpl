{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'component_options'}
        <div class="col-lg-9 col-lg-offset-3">
            <div class="form-group">                                        
                <div class="col-lg-9">                        
                    {if $input.data.type == 'sel_img'}
                        <button class='btn btn-default' id='ajax_add_sel_img_opt'>{l s='Add the option' mod='idxrcustomproduct'}</button>
                    {/if}
                </div>
            </div>
        </div>
    {else if $input.type == 'separator'}
        <div class="col-lg-9 col-lg-offset-3">
            <div class="form-group">                                        
                <div class="col-lg-9">  
                    <br/>
                    <h3>{$input.name|escape:'htmlall':'UTF-8'}</h3>
                </div>
            </div>
        </div>
    {else if $input.type == 'sortable_lists'}
        <div class="sortable_box col-lg-9 col-lg-offset-3">

            <div class="sortable-box-1 col-lg-6 col-md-6 well">
                <span><i class="icon icon-list"></i> {l s='Components list for this configuration' mod='idxrcustomproduct'}</span>
                <hr/>
                <ul id='sortable1' class="sortable">
                    {foreach from=$input.list_selected item=selected}
                        <li data-id_component='{$selected.id_component}' 
                            {if $selected.constraints}
                                data-constraints='{','|implode:$selected.constraints}' class='w_constraint'
                            {/if}
                            >
                            <i class="icon icon-list-alt"></i> 
                            {$selected.name|truncate:53}
                            {if isset($selected.errors)}
                                <i title='{$selected.errors}' class='icon icon-exclamation-circle lineerrors'></i>
                            {/if}
                            {if $selected.taxChange}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-percent" viewBox="0 0 16 16" data-toggle="tooltip" title="{l s='Tax change component, only can use one by configuration' mod='idxrcustomproduct'}">
                            <path d="M13.442 2.558a.625.625 0 0 1 0 .884l-10 10a.625.625 0 1 1-.884-.884l10-10a.625.625 0 0 1 .884 0zM4.5 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zm7 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                            </svg>
                            {/if}
                            {if $selected.constraints}
                                {assign var="first_constraint" value=explode('_',$selected.constraints[0]) nocache}
                                <i title='{l s='combined' mod='idxrcustomproduct'}' class='icon icon-link combined' data_constraint="{$first_constraint[0]}"></i>
                            {/if}
                            <i class="icon icon-plus-square pull-right add_constraints" data-toggle="tooltip" title="{l s='Create condition' mod='idxrcustomproduct'}"></i>
                            <i class="icon icon-pencil-square pull-right edit_component" data-toggle="tooltip" title="{l s='Edit component' mod='idxrcustomproduct'}"></i>
                        </li>
                    {/foreach}
                </ul>
            </div>

            <div class="sortable-box-2 col-lg-6 col-md-6 well">
                <span><i class="icon icon-list"></i> {l s='General list' mod='idxrcustomproduct'}</span>
                <input type="text" placeholder="{l s='Filter' mod='idxrcustomproduct'}" id="js-idxrcustomproduct-componentsearch"/>
                <hr />
                <ul id='sortable2' class="sortable">
                    {foreach from=$input.list_available item=available}
                        <li data-id_component='{$available.id_component}'>
                            <i class="icon icon-list-alt"></i> {$available.name}
                            {if $available.taxChange}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-percent" viewBox="0 0 16 16" data-toggle="tooltip" title="{l s='Tax change component, only can use one by configuration' mod='idxrcustomproduct'}">
                                <path d="M13.442 2.558a.625.625 0 0 1 0 .884l-10 10a.625.625 0 1 1-.884-.884l10-10a.625.625 0 0 1 .884 0zM4.5 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zm7 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                            </svg>
                            {/if}
                            <i class="icon icon-plus-square pull-right add_constraints" title="{l s='Set options' mod='idxrcustomproduct'}"></i>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    {else if $input.type == 'component_icon'}
        <div class="col-lg-9 icon-preview">
            {if $input.component->icon_preview}
                <img height="30" width="30" src="{$module_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$input.component->id_component|intval}.png"/> 
                <button class="btn btn-danger btn-sm js-delete-component-icon" data-component_id='{$input.component->id_component|intval}'  title="{l s='delete icon' mod='idxrcustomproduct'}"><i class="icon icon-trash"></i></button>
            {else}
                {l s='There are not any image uploaded for this component' mod='idxrcustomproduct'}
            {/if}
        </div>
    {else if $input.type == 'vistype_preview'}
        <div class="col-lg-9 col-lg-offset-3">
            <img class="vistype_prev" id="vistype_prev_accordion" src="{$module_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/views/img/acordeon.png" />
            <img class="vistype_prev" id="vistype_prev_full" src="{$module_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/views/img/cascada.png" />
            <img class="vistype_prev" id="vistype_prev_minified" src="{$module_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/views/img/minified.png" />
        </div>            
    {else if $input.type == 'type_preview'}
        <div class="col-lg-9 col-lg-offset-3">
            <img src="{$module_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/views/img/tipo-de-componente.png" />
        </div>
    {elseif $input.type == 'autocompletar'}
        {include file="./autocompletar.tpl"}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

