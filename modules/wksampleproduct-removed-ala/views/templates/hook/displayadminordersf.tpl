{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<div class="card">
    <div class="card-header">
        <i class="icon-money"></i>
        <h3>{l s='Sample product' mod='wksampleproduct'}<span class="badge badge-info ml-2">{$sampleCount|escape:'htmlall':'UTF-8'}</span></h3>

    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Product' mod='wksampleproduct'}</th>
                    <th>{l s='Quantity' mod='wksampleproduct'}</th>
                    <th>
                        <span class="title_box">{l s='Total price' mod='wksampleproduct'}</span>
                        <small class="text-muted">({l s='Tax included' mod='wksampleproduct'})</small>
                    </th>
                    <th>{l s='Action' mod='wksampleproduct'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $sample as $product}
                    <tr>
                        <td>{$product.product_name|escape:'htmlall':'UTF-8'}</td>
                        <td><span class="product_quantity_show badge">{$product.product_quantity|escape:'htmlall':'UTF-8'}</span></td>
                        <td>{$product.sample_price|escape:'htmlall':'UTF-8'}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product.product_id, 'updateproduct' => 1])|escape:'htmlall':'UTF-8'}#tab-hooks" class="btn btn-default" title="View">
                                    <i class="icon-search-plus"></i>
                                    {l s='View' mod='wksampleproduct'}
                                </a>
                            </div>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
