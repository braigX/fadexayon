{*
* 2007-2023 PrestaShop
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
*  @copyright 2023 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{if !empty($reviews.review)}
    "review" : [
{foreach from=$reviews.review item=review name=richsnipptsproduct}
        {
{if !empty($review.customer_name)}
            "author": {
                "name": {$review.customer_name|@json_encode nofilter},
                "@type": "Person"
            },
{/if}
{if !empty($review.content)}
            "reviewBody": {$review.content|strip_tags:false|@json_encode nofilter},
{/if}
{if !empty($review.date_add)}
            "datePublished": {$review.date_add|@json_encode nofilter},
{/if}
{if !empty($review.grade)}
            "reviewRating" : {
                "ratingValue": "{$review.grade|intval}",
                "worstRating": "{$review.worstRating|intval}",
                "bestRating": "{$review.bestRating|intval}",
                "@type": "Rating"
            },
{/if}
            "@type": "Review"
        }{if !$smarty.foreach.richsnipptsproduct.last},{/if} 
{/foreach}
    ],
{/if}
{if !empty($reviews.ratingCount)}
    "aggregateRating": {
{if !empty($reviews.worstRating)}
        "worstRating": "{$reviews.worstRating|intval}",
{/if}
        "ratingValue": "{$reviews.ratingValue}",
        "bestRating": "{$reviews.bestRating|intval}",
        "ratingCount": "{$reviews.ratingCount|intval}",
{if !empty($reviews.reviewCount)}
        "reviewCount": "{$reviews.reviewCount|intval}",
{/if}
        "@type" : "AggregateRating"
    },
{/if}