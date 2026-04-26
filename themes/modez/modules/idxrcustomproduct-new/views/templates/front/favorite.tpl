{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div class='col-md-12'>
    <h3><i class="icon icon-save fa fa-save"></i> {l s='Your custom products' mod='idxrcustomproduct'}</h3>
    <hr />
    <div class="panel-group">
    {foreach from=$favorites item=favorite name=fav_loop}
        <div class="panel panel-default favoritos" id="idxrcustomproduct_panel_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}">

            <div class="panel-heading"><i class="icon icon-list fa fa-list"></i> {$favorite.product_name|escape:'htmlall':'UTF-8'}</div>
            <div class="panel-body">
                {foreach from=$favorite.description item=desc}
                <p>{$desc|escape:'htmlall':'UTF-8'}</p>
                {/foreach}
                {foreach from=$favorite.extra_data item=extra}
                    <p>{$extra.title}: {$extra.extra}</p>
                {/foreach}
            </div>

            <div class="panel-footer">
                <div class="clearfix">
                    <button id="idxrcustomproduct_send_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}" class="btn btn-success pull-right idxrcustomproduct_send" type="submit"><i class="fa fa-shopping-cart icon icon-shopping-cart"></i> {l s='Add to cart' mod='idxrcustomproduct'}</button>
                    <button id="idxrcustomproduct_view_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}" class="btn btn-default pull-right idxrcustomproduct_view" type="submit"><i class="fa fa-eye"></i><i class="fa fa-pencil icon icon-pencil"></i> {l s='Preview and modify' mod='idxrcustomproduct'}</button>
                    <button id="idxrcustomproduct_del_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}" class="btn btn-danger pull-left idxrcustomproduct_del" type="submit"><i class="fa fa-trash icon icon-trash"></i> {l s='Delete from my favorites' mod='idxrcustomproduct'}</button>            
                    <span class="hidden" id="icp_info_{$smarty.foreach.fav_loop.index|escape:'htmlall':'UTF-8'}" data-productid="{$favorite.id_product|intval}" data-customization="{$favorite.icp_code|escape:'htmlall':'UTF-8'}" data-url="{$favorite.product_url|escape:'htmlall':'UTF-8'}" data-favid="{$favorite.id_fav|escape:'htmlall':'UTF-8'}"></span>
                </div>
            </div>
        </div>
    {/foreach}
    </div>
</div>