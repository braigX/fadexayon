{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<div class="panel">
    <div class="panel-heading">
        <svg class="bi bi-layers" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M3.188 8L.264 9.559a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882L12.813 8l-1.063.567L14.438 10 8 13.433 1.562 10 4.25 8.567 3.187 8z"/>
            <path fill-rule="evenodd" d="M7.765 1.559a.5.5 0 0 1 .47 0l7.5 4a.5.5 0 0 1 0 .882l-7.5 4a.5.5 0 0 1-.47 0l-7.5-4a.5.5 0 0 1 0-.882l7.5-4zM1.563 6L8 9.433 14.438 6 8 2.567 1.562 6z"/>
        </svg> {l s='Generate component by source' mod='idxrcustomproduct'}
    </div>
    <div class="panel-body">
        <form id="component_clone_form" class="defaultForm form-horizontal" 
              action="{$form_action_url}" method="post" enctype="multipart/form-data" novalidate="">
            <div class="form-group">
                <label class="control-label col-lg-3">
                     {l s='Clone from other component' mod='idxrcustomproduct'}
                </label>
                <div class="col-lg-5">
                    <select name="component_source" class="fixed-width-xxl">
                        {foreach from=$components item=component}
                        <option value="{$component.id_component}">{$component.name}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-lg-4">
                    <button class="btn btn-default" type="sumbit" value="1" name="submitCloneComponent" id="submitCloneComponent">
                        <svg class="bi bi-files" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
                            <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
                        </svg>
                        {l s='Clone' mod='idxrcustomproduct'}
                    </button>
                </div>
            </div>
        </form>
        <hr/>
        {*<form id="component_generate_form" class="defaultForm form-horizontal" 
              action="{$form_action_url}" method="post" enctype="multipart/form-data" novalidate="">
            <div class="form-group">
                <label class="control-label col-lg-3">
                     {l s='Generate from a product' mod='idxrcustomproduct'}
                </label>
                <div class="col-lg-5">
                    <select name="product_source" id="component_product_source" class="fixed-width-xxl">
                        <option value="" selected disabled>{l s='Please select a product' mod='idxrcustomproduct'}</option>
                        {foreach from=$products item=product}
                        <option value="{$product.id_product}">{$product.id_product} {$product.reference} {$product.name}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-lg-4">
                    <button class="btn btn-default" type="sumbit" value="1" name="submitGenerateComponent">
                        <svg class="bi bi-files" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3z"/>
                            <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2v-1a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/>
                        </svg>
                        {l s='Generate' mod='idxrcustomproduct'}
                    </button>
                </div>
            </div>            
        </form>*}
    </div>
</div>