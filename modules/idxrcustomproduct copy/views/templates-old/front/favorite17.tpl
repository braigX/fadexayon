{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}


{extends file='page.tpl'}
{block name="page_content"}

<div class='col-md-12'>
    <h3><i class="material-icons">save</i>{l s='Your favourite products' mod='idxrcustomproduct'}</h3>
    <hr />
    <div class="{*card-group*}">
    {foreach from=$favorites item=favorite name=fav_loop}
        <div class="card favoritos" id="idxrcustomproduct_panel_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}">

            <div class="card-header">
                <i class="material-icons">format_list_bulleted</i> {$favorite.product_name|escape:'htmlall':'UTF-8'}
            </div>

            <div class="card-block">
            {foreach from=$favorite.description item=desc}
                <p>{$desc|escape:'htmlall':'UTF-8'}</p>
            {/foreach}
            {foreach from=$favorite.extra_data item=extra}
                <p>{$extra.title}: {$extra.extra}</p>
            {/foreach}
            <hr />

                <div class="clearfix">
                    <button id="idxrcustomproduct_send_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}" class="btn btn-success float-lg-right float-md-right float-sm-right idxrcustomproduct_send" type="submit"><i class="material-icons">local_grocery_store</i> {l s='Add to cart' mod='idxrcustomproduct'}</button>
                    <button id="idxrcustomproduct_view_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}" class="btn btn-default float-lg-right float-md-right float-sm-right idxrcustomproduct_view" type="submit"><i class="material-icons">edit</i> {l s='Preview and modify' mod='idxrcustomproduct'} <i class="fa fa-pencil"></i></button>
                    <button id="idxrcustomproduct_del_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}" class="btn btn-danger float-lg-left float-md-left float-sm-left idxrcustomproduct_del" type="submit"><i class="material-icons">delete</i> {l s='Delete from my favorites' mod='idxrcustomproduct'}</button>            
                    <span class="hidden" id="icp_info_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}" data-productid="{$favorite.id_product|intval}" data-customization="{$favorite.icp_code|escape:'htmlall':'UTF-8'}" data-url="{$favorite.product_url|escape:'htmlall':'UTF-8'}" data-favid="{$favorite.id_fav|escape:'htmlall':'UTF-8'}"></span>
                </div>

            </div>

        </div>
    {/foreach}
    </div>
</div>
    
{/block}