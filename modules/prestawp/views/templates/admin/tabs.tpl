{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2018 Presta.Site
* @license   LICENSE.txt
*}
<div class="pstg-tabs row {if $psv == 1.5}pspc15{/if}">
    <div class="col-md-2">
        <div class="pst-tabs-list list-group">
            {foreach from=$tabs item="tab" name="tab_names" key='key'}
                <a class="list-group-item {if ($smarty.foreach.tab_names.first && !$pswp_tab) || $pswp_tab == $key}active{/if}"
                   href="#psttab-{$key|escape:'html':'UTF-8'}"
                   data-hash="#tab-{$key|escape:'html':'UTF-8'}"
                   id="psttn-{$key|escape:'html':'UTF-8'}"
                >
                    {$tab.name|escape:'html':'UTF-8'}
                </a>
            {/foreach}
        </div>
    </div>
    <div class="col-md-10 pst-tab-content-wrp">
        {foreach from=$tabs item="tab" name="tab_contents" key='key'}
            <div class="pst-tab-content" id="psttab-{$key|escape:'html':'UTF-8'}" {if !(($smarty.foreach.tab_names.first && !$pswp_tab) || $pswp_tab == $key)}style="display: none;" {/if}>
                {$tab.content nofilter} {* html *}
            </div>
        {/foreach}
    </div>
</div>
