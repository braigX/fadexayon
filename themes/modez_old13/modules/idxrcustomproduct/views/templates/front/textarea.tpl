{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<h3><img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png"/> {$step.title|escape:'htmlall':'UTF-8'}</h3>
<div class="panel panel-red" style="border-color: {$step.color|escape:'htmlall':'UTF-8'};">           
    <div class="panel-heading" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};"><span>{$step.description|escape:'htmlall':'UTF-8'}</span></div>                        
    <div class="panel-body">
        <div class="form-group">
            <textarea class="form-control" rows="5" id="textarea_{$step.id_component|intval}"></textarea>
        </div>
    </div><!-- panel-body -->
    <div class="panel-footer">

        <div class="clearfix"> <a id='js_icp_next_opt_{$step.id_component|intval}' data-type="{$step.type|escape:'htmlall':'UTF-8'}" class="js_icp_next_option btn btn-md btn-default pull-right"><i class="fa fa-arrow-down"></i> {l s='next' mod='idxrcustomproduct'}</a></div>
    </div>

</div><!-- panel -->