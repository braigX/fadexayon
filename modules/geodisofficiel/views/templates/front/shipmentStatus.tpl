{*
* 2018 GEODIS
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
*  @author    GEODIS <contact@geodis.com>
*  @copyright 2018 GEODIS
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{extends file='page.tpl'}
{block name='content'}
{if !$error}
    <div class ="main">
        {foreach $shipments as $key => $shipment}
            <div class="shipment card card-block hidden-sm-down">
                {if ($key > 0)}
                    <h1 class="h1">{__ s='Admin.ShipmentStatus.shipment.title.sameOrder'}</h1>
                {/if}
                {if ($shipment->tracking_number != null)}
                    <h1 class="h1">{__ s='Admin.ShipmentStatus.shipment.title.%s' vars=[($shipment->tracking_number)|escape:'htmlall':'UTF-8']}</h1>
                {else}
                    <h1 class="h1">{__ s='Admin.ShipmentStatus.shipment.title.%s' vars=[($shipment->reference)|escape:'htmlall':'UTF-8']}</h1>
                {/if}
                <div>
                    <p>
                        <span style="font-size:10pt;font-family:Arial;font-style:normal;">
                            {__ s='Admin.ShipmentStatus.shipment.status.%s' vars=[($shipment->status_label)|escape:'htmlall':'UTF-8']}
                        </span>
                    </p>
                    {if ($shipment->tracking_url != null)}
                    <p>
                        <span style="font-size:10pt;font-family:Arial;font-style:normal;">
                            {__ s='Admin.ShipmentStatus.shipment.trackingUrl'} <a href='{($shipment->tracking_url)|escape:'htmlall':'UTF-8'}' target="_blank">{__ s='Admin.ShipmentStatus.shipment.trackingUrl.link.name'}</a>
                        </span>
                    </p>
                    {/if}
                </div>
                {if $history[$key]|@count gt 0}
                <h2 class="h3">{__ s='Admin.ShipmentStatus.shipment.history.title'}</h2>
                    <div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="padding: 0 10px 0 10px">{__ s='Admin.ShipmentStatus.table.date'}</th>
                                        <th style="padding: 0 10px 0 10px">{__ s='Admin.ShipmentStatus.table.status'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $history[$key] as $event}
                                        <tr>
                                            <td style="padding: 4px 10px 4px 10px">{dateFormat date=$event->event_date|date_format:'%Y-%m-%d %H:%M:%S' full=1}</td>
                                            <td style="padding: 4px 10px 4px 10px">{($event->status_label)|escape:'htmlall':'UTF-8'}</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                    </div>
                {/if}
            </div>

            <div class="packages">
                {foreach $packagesInfos[$key] as $packageInfos}
                    <section id="products">
                        <div id="js-product-list-top" class="row products-selection">
                            <div class="col-md-6 hidden-sm-down total-products">
                            <p>{__ s='Admin.ShipmentStatus.package.referenceExpedition.%s' vars=[($packageInfos['package']->reference)|escape:'htmlall':'UTF-8']}</p>
                            </div>
                        </div>
                        <div>
                            <div id="js-product-list">
                                <div class="products row">
                                    {foreach $packageInfos['products'] as $product}
                                    <article class="product-miniature js-product-miniature" itemscope="" itemtype="http://schema.org/Product">
                                        <div class="thumbnail-container">
                                            <a href="{$product['productUrl']|escape:'htmlall':'UTF-8'}" class="thumbnail product-thumbnail">
                                                <img src="{$product['imagePath']|escape:'htmlall':'UTF-8'}" alt="{$product['imagePath']|escape:'htmlall':'UTF-8'}" alt="Product picture" />
                                            </a>
                                            <div class="product-description">
                                                <h2 class="h3 product-title" itemprop="name"><a>{{$product['productName']|truncate:30|escape:'htmlall':'UTF-8'}}</a></h2>
                                                <p style="text-align:center">{__ s='Admin.ShipmentStatus.package.orderedQuantity.%s' vars=[$product['productQuantity']|escape:'htmlall':'UTF-8']}</p>
                                            </div>
                                            <div class="highlighted-informations no-variants hidden-sm-down">
                                              <a href="{$product['productUrl']}">
                                                <i class="material-icons search"></i>{__ s='Admin.ShipmentStatus.package.quickView'}
                                              </a>
                                            </div>
                                        </div>
                                    </article>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    </section>
                {/foreach}
            </div>
        {/foreach}
    </div>
{/if}
{/block}
