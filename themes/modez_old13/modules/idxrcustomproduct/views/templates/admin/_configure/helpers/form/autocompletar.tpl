{**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2021 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<fieldset class="form-group idx-autocomplete">
    <div
         class="autocomplete-search {$input.module_name|escape:'htmlall':'UTF-8'}-autocomplete"
         data-formid="{$input.name|escape:'htmlall':'UTF-8'}"
         data-fullname="{$input.name|escape:'htmlall':'UTF-8'}"
         data-function ="select_{$input.name}" 
         data-mappingvalue="id"
         data-mappingname="name"
         data-remoteurl="{$input.urlAjax|escape:'html':'UTF-8'}%QUERY"
         {if isset($input.limit)}data-limit="{$input.limit|intval}"{/if}
         >
        <div class="search search-with-icon">
            <input type="text" id="{$input.name|escape:'htmlall':'UTF-8'}" class="form-control search typeahead {$input.name|escape:'htmlall':'UTF-8'}" placeholder="{l s='Start typing...' mod='idxrcustomproduct'}" autocomplete="off"{if isset($input.elementos) && $input.elementos && isset($input.limit) && $input.limit  !== 0 && $input.limit == count($input.elementos)} disabled{/if}>
        </div>
        {if isset($input.desc)}
        <p class="help-block">{$input.desc|escape:'htmlall':'UTF-8'}</p>
        {/if}
        <ul id="{$input.name|escape:'htmlall':'UTF-8'}-data" class="typeahead-list nostyle col-sm-12 product-list{if !isset($input.elementos) || !$input.elementos || $input.elementos|@count == 0} hidden{/if} {if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}">
            {if $input.elementos}
                {foreach from=$input.elementos item=elemento}
                <li class="media">
                    {if isset($elemento.imagen)}
                    <div class="media-left">
                        <img class="media-object image" src="{$elemento.imagen|escape:'html':'UTF-8'}" />
                    </div>
                    {/if}
                    <div class="media-body media-middle">
                        <span class="label">{$elemento.name|escape:'html':'UTF-8'}{if !empty($elemento.reference)}&nbsp;{l s='(ref: %s)' sprintf=[$elemento.reference] mod='idxrcustomproduct'}{/if}</span>
                        {if !isset($input.bloqueado) || $input.bloqueado == false}
                        {if $input.es17}
                        <i class="material-icons eliminar">clear</i>
                        {else}
                        <i class="icon icon-trash eliminar"></i>
                        {/if}
                        {/if}
                    </div>
                    <input type="hidden" name="{$input.name|escape:'htmlall':'UTF-8'}[data][]" value="{$elemento.id|escape:'html':'UTF-8'}" />
                </li>
                {/foreach}
            {/if}
        </ul>
        <div class="invisible" id="tplcollection-{$input.name|escape:'htmlall':'UTF-8'}">
            {if $input.es17}
            <span class="label">%s</span><i class="material-icons eliminar">clear</i>
            {else}
            <span class="label">%s</span><i class="icon icon-trash eliminar"></i>
            {/if}
        </div>
    </div>
</fieldset>
