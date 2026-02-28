{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Ádalop <contact@prestashop.com>
*  @copyright 2022 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<h3>
    <i class="icon-eye"></i>
    {l s='Analyzes microdata' mod='adpsearchlocatemicrodatos'}
    <small>
        {l s='Analyzes microdata in Google structured data' mod='adpsearchlocatemicrodatos'}
    </small>
</h3>
<form action="" class="form-horizontal ">
    <br>
    <h3><b>{l s='ANALYZES WITH SCHEMA MARKUP VALIDATOR' mod='adpsearchlocatemicrodatos'}</b></h3>

    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://validator.schema.org/#url=</span>
           <input disabled name="url_home_page" type="text" class="text form-control url_home_page" value="{$url_home_page}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://validator.schema.org/#url={$url_home_page}" target="_blank">
                <i class="icon-eye"></i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata on the home page' mod='adpsearchlocatemicrodatos'}</div>
    </div>

    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://validator.schema.org/#url=</span>
           <input disabled name="url_list_product" type="text" class="text form-control url_list_product" value="{$url_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://validator.schema.org/#url={$url_list_product}" target="_blank">
                <i class="icon-eye"></i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata from product listings' mod='adpsearchlocatemicrodatos'}</div>
    </div>

    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://validator.schema.org/#url=</span>
           <input disabled name="url_product_page" type="text" class="text form-control url_product_page" value="{$url_new_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://validator.schema.org/#url={$url_new_list_product}" target="_blank">
                <i class="icon-eye"></i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata from new product listings' mod='adpsearchlocatemicrodatos'}</div>
    </div>

    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://validator.schema.org/#url=</span>
           <input disabled name="url_product_page" type="text" class="text form-control url_product_page" value="{$url_best_sales_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://validator.schema.org/#url={$url_best_sales_list_product}" target="_blank">
                <i class="icon-eye"></i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata from best sales product listings' mod='adpsearchlocatemicrodatos'}</div>
    </div>

    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://validator.schema.org/#url=</span>
           <input disabled name="url_product_page" type="text" class="text form-control url_product_page" value="{$url_discount_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://validator.schema.org/#url={$url_discount_list_product}" target="_blank">
                <i class="icon-eye"></i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata from discounted product listings' mod='adpsearchlocatemicrodatos'}</div>
    </div>

    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://validator.schema.org/#url=</span>
           <input disabled name="url_product_page" type="text" class="text form-control url_product_page" value="{$url_product_page}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://validator.schema.org/#url={$url_product_page}" target="_blank">
                <i class="icon-eye"></i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata from product page' mod='adpsearchlocatemicrodatos'}</div>
    </div>

    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://validator.schema.org/#url=</span>
           <input id="url_custom" name="url_custom" type="text" class="url_custom text form-control">
           <span class="input-group-btn">
                <a id="url_custom_link" class="btn btn-default" data-url-prefix="https://validator.schema.org/#url=" href="" target="_blank">
                <i class="icon-eye"></i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Enter the url you want to analyze' mod='adpsearchlocatemicrodatos'}</div>
    </div>

    <hr/>

    <br>
    <h3><b>{l s='ANALYZES WITH RICH RESULT TEST' mod='adpsearchlocatemicrodatos'}</b></h3>

    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_home_page" type="text" class="text form-control url_home_page" value="{$url_home_page}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_home_page}&user_agent=2" target="_blank" style="width:90px;">
                 <i class="icon-eye"> {l s='Desktop' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
        </div>
        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_home_page" type="text" class="text form-control url_home_page" value="{$url_home_page}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_home_page}&user_agent=1" target="_blank" style="width:90px;">
                 <i class="icon-eye"> {l s='Mobile' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata on the home page' mod='adpsearchlocatemicrodatos'}</div>
    </div>

    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_list_product" type="text" class="text form-control url_list_product" value="{$url_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_list_product}&user_agent=2" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Desktop' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_list_product" type="text" class="text form-control url_list_product" value="{$url_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_list_product}&user_agent=1" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Mobile' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata from product listings' mod='adpsearchlocatemicrodatos'}</div>
    </div>
    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_new_list_product" type="text" class="text form-control url_new_list_product" value="{$url_new_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_new_list_product}&user_agent=2" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Desktop' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_new_list_product" type="text" class="text form-control url_new_list_product" value="{$url_new_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_new_list_product}&user_agent=1" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Mobile' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata from new product listings' mod='adpsearchlocatemicrodatos'}</div>
    </div>
    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_best_sales_list_product" type="text" class="text form-control url_best_sales_list_product" value="{$url_best_sales_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_best_sales_list_product}&user_agent=2" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Desktop' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_best_sales_list_product" type="text" class="text form-control url_best_sales_list_product" value="{$url_best_sales_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_best_sales_list_product}&user_agent=1" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Mobile' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
       <div class="help-block">{l s='Analyzes microdata from best sales product listings' mod='adpsearchlocatemicrodatos'}</div>
    </div>
    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_discount_list_product" type="text" class="text form-control url_discount_list_product" value="{$url_discount_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_discount_list_product}&user_agent=2" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Desktop' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_discount_list_product" type="text" class="text form-control url_discount_list_product" value="{$url_discount_list_product}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_discount_list_product}&user_agent=1" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Mobile' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata from discounted product listings' mod='adpsearchlocatemicrodatos'}</div>
    </div>
    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_product_page" type="text" class="text form-control url_product_page" value="{$url_product_page}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_product_page}&user_agent=2" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Desktop' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input disabled name="url_product_page" type="text" class="text form-control url_product_page" value="{$url_product_page}">
           <span class="input-group-btn">
                <a class="btn btn-default" href="https://search.google.com/test/rich-results?url={$url_product_page}&user_agent=1" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Mobile' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>
           
        </div>
        <div class="help-block">{l s='Analyzes microdata from product page' mod='adpsearchlocatemicrodatos'}</div>
    </div>
    <div class="form-group">

        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input id="url_custom_2" name="url_custom_2" type="text" class="url_custom_2 text form-control">
           <span class="input-group-btn">
                <a id="url_custom_link_2" class="btn btn-default" data-url-prefix="https://search.google.com/test/rich-results?user_agent=2&url=" href="" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Desktop' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>       
        </div>
        <div class="input-group" style="width:100%;">
           <span class="input-group-addon">https://search.google.com/test/rich-results?url=</span>
           <input id="url_custom_3" name="url_custom_3" type="text" class="url_custom_3 text form-control">
           <span class="input-group-btn">
                <a id="url_custom_link_3" class="btn btn-default" data-url-prefix="https://search.google.com/test/rich-results?user_agent=1&url=" href="" target="_blank" style="width:90px;">
                <i class="icon-eye"> {l s='Mobile' mod='adpsearchlocatemicrodatos'}</i></a>
           </span>       
        </div>
        <div class="help-block">{l s='Enter the url you want to analyze' mod='adpsearchlocatemicrodatos'}</div>
    </div>
</form>