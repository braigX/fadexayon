{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}


<div class="panel col-lg-12">
    <div class="panel-heading"> 
        {l s='Configuration images' mod='idxrcustomproduct'}
    </div>
    <div>
        <a href="{$product_url}" target="_blank" class="btn btn-info" role="button">{l s='Associate images to options' mod='idxrcustomproduct'}</a>
    </div>
    <hr />
    <div id="fileuploader" data-idconfiguration="{$id_configuration}" data-max-size="{$max_image_size}">{l s='Upload' mod='idxrcustomproduct'}</div>
</div>