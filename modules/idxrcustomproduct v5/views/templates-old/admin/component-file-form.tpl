{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2015 Innova Deluxe SL
* @license   INNOVADELUXE
*}
<div class="panel col-lg-12">
    <form action='{$currentIndex}&token={$token}' method="post">
    <div class="panel-heading"> 
        {l s='Component options' mod='idxrcustomproduct'} - {$component->name|escape:'htmlall':'UTF-8'}
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3 text-right">
            {l s='Maximum file size' mod='idxrcustomproduct'}
        </label>
        <div class="col-lg-9">
            <input name="max_size" id="max_size" value="{$component->size}" class="" type="number" min="1" max="{$max_file_server}">
            <p class="help-block">
                {l s='Set a maximum size for the files, your server configuration have a limit of ' mod='idxrcustomproduct'}{$max_file_server}M
            </p>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3 text-right">
            {l s='Allowed extensions' mod='idxrcustomproduct'}
        </label>
        <div class="col-lg-9">
            {foreach from=$file_extensions  key=k item=extension}
                <div class='col-lg-2'>
                    <div class="checkbox">
                        <label><input type="checkbox" name="ext_{$k}" class="extension_chekbox" {if $k|in_array:$component->allowed_extensions}checked="checked"{/if}>.{$k}</label>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    <input type='hidden' name='id_component' value='{$component->id_component}'/>

    <div class="panel-footer col-lg-12">
        <button class="btn btn-default pull-right" type="submit" value='1' name='submitUpdateFileConfigurationStay'>
            <i class="process-icon-save"></i> {l s='Save and stay' mod='idxrcustomproduct'}
        </button>
        <button  class="btn btn-default pull-right" type="submit" value='1'  name='submitUpdateFileConfiguration'>
            <i class="process-icon-save"></i> {l s='Save' mod='idxrcustomproduct'}
        </button>
        <a href="#" class="btn btn-default">
            <i class="process-icon-cancel"></i> {l s='Cancel' mod='idxrcustomproduct'}
        </a>
    </div>
    </form>
</div>