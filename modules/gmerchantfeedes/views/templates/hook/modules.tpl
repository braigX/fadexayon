{**
* 2019 ExtraSolutions
*
* NOTICE OF LICENSE
*
* @author    ExtraSolutions
* @copyright 2019 ExtraSolutions
* @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

<div class="row">
    <div class="col-md-12">
        {if !empty($this_module->id)}
            <div class="alert alert-info height-60">
                {l s=$labels.like sprintf=[$this_module->displayName] tags=['<strong>'] mod="gmerchantfeedes"}
                <a href="https://addons.prestashop.com/en/ratings.php?id_product={$this_module->id_product|intval}" target="_blank" class="btn btn-default">
                    <i class="icon-thumbs-o-up"></i> <span>{$labels.yes|escape:'quotes':'UTF-8'}</span>
                </a>
                <a href="https://addons.prestashop.com/en/contact-us?id_product={$this_module->id_product|intval}" target="_blank" class="btn btn-default">
                    <i class="icon-thumbs-o-down"></i> <span>{$labels.no|escape:'quotes':'UTF-8'}</span>
                </a>
            </div>
        {/if}
    </div>
    <div class="col-md-12">
        {if isset($documentation_link) && !empty($documentation_link)}
            <div class="alert alert-info height-60">
                <a href="{$documentation_link|escape:'quotes':'UTF-8'}" target="_blank" class="">
                    <span>
                        {$documentation_text|escape:'quotes':'UTF-8'}
                    </span>
                </a>
            </div>
        {/if}
    </div>
</div>
