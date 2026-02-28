{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
*}


<script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "ParcelDelivery",
        "deliveryAddress": {
            "@type": "PostalAddress",
            "name": "{$delivery_address['firstname']|escape:'htmlall':'UTF-8'} {$delivery_address['lastname']|escape:'htmlall':'UTF-8'}",
            "streetAddress": "{$delivery_address['address1']|escape:'htmlall':'UTF-8'}",
            "addressLocality": "{$delivery_address['city']|escape:'htmlall':'UTF-8'}",
            "addressRegion": "{if !empty($delivery_address['state'])}{$delivery_address['state']|escape:'htmlall':'UTF-8'}{$delivery_address['state']|escape:'htmlall':'UTF-8'}{else}{$delivery_address['city']|escape:'htmlall':'UTF-8'}{/if}",
            "addressCountry": "{$delivery_address['country']|escape:'htmlall':'UTF-8'}",
            "postalCode": "{$delivery_address['postcode']|escape:'htmlall':'UTF-8'}"
        },
        "expectedArrivalFrom": "{$expectedArrival['from']|escape:'htmlall':'UTF-8'}",
        "expectedArrivalUntil": "{$expectedArrival['until']|escape:'htmlall':'UTF-8'}",
        "carrier": {
            "@type": "Organization",
            "name": "{$edcarrier['name']|escape:'htmlall':'UTF-8'}"
        },
        "itemShipped": [
            {foreach from=$products item=product name=prod_it}
            {
                "@type": "Product",
                "name": "{$product.name|escape:'htmlall':'UTF-8'}",
                "url": "{$product.url|escape:'htmlall':'UTF-8'}",
                {if isset($product.image) && $product.image != ''}"image": "{$product.image|escape:'htmlall':'UTF-8'}",{/if}
                "sku": "{$product.reference|escape:'htmlall':'UTF-8'}",
                {* "description": "{$product.description_short|escape:'htmlall':'UTF-8'}", *}
                {if isset($product.color) && $product.color != ''}
                "color": "{$product.color|escape:'htmlall':'UTF-8'}",
                {/if}
                "brand": {
                    "@type": "Brand",
                    "name": "{$product.brand|escape:'htmlall':'UTF-8'}"
                }
            }{if !$smarty.foreach.prod_it.last},{/if}
            {/foreach}
        ],
        "trackingNumber": "{if !empty($tracking_number)}{$tracking_number|escape:'htmlall':'UTF-8'}{/if}",
        {if isset($trackingUrl) && $trackingUrl != ''}
        "trackingUrl": "{$trackingUrl|escape:'htmlall':'UTF-8'}",
        {/if}
        "hasDeliveryMethod": "ParcelService",
        "partOfOrder": {
            "@type": "Order",
            "orderNumber": "{$orderNumber|escape:'htmlall':'UTF-8'}",
            "merchant": {
                "@type": "Organization",
                "name": "{$merchant|escape:'htmlall':'UTF-8'}",
                "sameAs": "{$store_url|escape:'htmlall':'UTF-8'}"
            },
            "orderStatus": "http://schema.org/OrderProcessing"
        }
    }
</script>

{*
<div itemscope itemtype="http://schema.org/ParcelDelivery">
    <div itemprop="deliveryAddress" itemscope itemtype="http://schema.org/PostalAddress">
        <meta itemprop="name" content="{$delivery_address['firstname']|escape:'htmlall':'UTF-8'} {$delivery_address['lastname']|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="streetAddress" content="{$delivery_address['address1']|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="addressLocality" content="{$delivery_address['city']|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="addressRegion" content="{if !empty($delivery_address['state'])}{$delivery_address['state']|escape:'htmlall':'UTF-8'}{$delivery_address['state']|escape:'htmlall':'UTF-8'}{else}{$delivery_address['city']|escape:'htmlall':'UTF-8'}{/if}" />
        <meta itemprop="addressCountry" content="{$delivery_address['country']|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="postalCode" content="{$delivery_address['postcode']|escape:'htmlall':'UTF-8'}" />
    </div>
    <div itemprop="originAddress" itemscope itemtype="http://schema.org/PostalAddress">
        <meta itemprop="name" content="{$delivery_address['firstname']|escape:'htmlall':'UTF-8'} {$delivery_address['lastname']|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="streetAddress" content="{$delivery_address['address1']|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="addressLocality" content="{$delivery_address['city']|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="addressRegion" content="{if !empty($delivery_address['state'])}{$delivery_address['state']|escape:'htmlall':'UTF-8'}{$delivery_address['state']|escape:'htmlall':'UTF-8'}{else}{$delivery_address['city']|escape:'htmlall':'UTF-8'}{/if}" />
        <meta itemprop="addressCountry" content="{$delivery_address['country']|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="postalCode" content="{$delivery_address['postcode']|escape:'htmlall':'UTF-8'}" />
    </div>
    <meta itemprop="expectedArrivalFrom" content="{$expectedArrival['from']|escape:'htmlall':'UTF-8'}" />
    <meta itemprop="expectedArrivalUntil" content="{$expectedArrival['until']|escape:'htmlall':'UTF-8'}" />
    <div itemprop="carrier" itemscope itemtype="http://schema.org/Organization">
        <meta itemprop="name" content="{$edcarrier['name']|escape:'htmlall':'UTF-8'}" />
        <link itemprop="url" href="#" />
    </div>
    {foreach $products as $product}
    <div itemprop="itemShipped" itemscope itemtype="http://schema.org/Product">
        <meta itemprop="name" content="{$product.name|escape:'htmlall':'UTF-8'}" />
        <link itemprop="url" href="{$product.url|escape:'htmlall':'UTF-8'}" />
        <link itemprop="image" href="{$product.image|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="sku" content="{$product.ean13|escape:'htmlall':'UTF-8'}" />
        <meta itemprop="description" content="{$product.description_short|escape:'htmlall':'UTF-8'}" />
        <div itemprop="brand" itemscope itemtype="http://schema.org/Brand">
            <meta itemprop="name" content="{$product.brand|escape:'htmlall':'UTF-8'}" />
        </div>
        <meta itemprop="color" content="{$product.color|escape:'htmlall':'UTF-8'}" />
    </div>
    {/foreach}
    <meta itemprop="trackingNumber" content="{if !empty($tracking_number)}{$tracking_number|escape:'htmlall':'UTF-8'}{/if}" />
    <link itemprop="trackingUrl" href="{$trackingUrl|escape:'htmlall':'UTF-8'}" />
    <div itemprop="potentialAction" itemscope itemtype="http://schema.org/TrackAction">
        <link itemprop="url" href="{$trackingUrl|escape:'htmlall':'UTF-8'}" />
    </div>
    <div itemprop="hasDeliveryMethod" itemscope itemtype="http://schema.org/ParcelService">
        <meta itemprop="name" content="http://schema.org/ParcelService" />
    </div>
    <div itemprop="partOfOrder" itemscope itemtype="http://schema.org/Order">
        <meta itemprop="orderNumber" content="{$orderNumber|escape:'htmlall':'UTF-8'}" />
        <div itemprop="merchant" itemscope itemtype="http://schema.org/Organization">
            <meta itemprop="name" content="{$merchant|escape:'htmlall':'UTF-8'}" />
            <link itemprop="sameAs" href="{$store_url|escape:'htmlall':'UTF-8'}" />
        </div>
        <link itemprop="orderStatus" href="http://schema.org/OrderInTransit" />
    </div>
</div>
*}