{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}

<div class="ets_seo_rss_feed">
  <h1 class="head-title">{l s='RSS' mod='ets_seo'}</h1>
  <div class="content">
      {if isset($ets_seo_rss_enable) && $ets_seo_rss_enable}
          {if count($featured_product_options)}
            <div class="rss-box">
              <h2>{l s='Featured products list' mod='ets_seo'}</h2>
              <div class="rss-list">
                  {foreach $featured_product_options as $item}
                    <li>
                      <a href="{$item.link|escape:'html':'UTF-8'}.xml">
                          {$item.name|escape:'html':'UTF-8'}
                        <i class="fa fa-rss"></i>
                      </a>
                    </li>
                  {/foreach}
              </div>
            </div>
          {/if}
          {if !empty($ets_seo_categories)}
            <div class="rss-box">
              <h2>{l s='Product categories' mod='ets_seo'}</h2>
              <div class="rss-list">
                  {foreach $ets_seo_categories as $category}
                    <li>
                      <a href="{$ets_rss_link|escape:'quotes':'UTF-8'}/category/{$category.id_category|escape:'html':'UTF-8'}.xml">
                          {$category.name|escape:'html':'UTF-8'}
                        <i class="fa fa-rss"></i>
                      </a>
                    </li>
                  {/foreach}
              </div>
            </div>
          {/if}
          {if !empty($ets_seo_cms)}
            <div class="rss-box">
              <h2>{l s='Pages' mod='ets_seo'}</h2>
              <div class="rss-list">
                  {foreach $ets_seo_cms  as $cms}
                    <li>
                      <a href="{$ets_rss_link|escape:'quotes':'UTF-8'}/page/{$cms.id_cms|escape:'html':'UTF-8'}.xml">
                          {$cms.meta_title|escape:'html':'UTF-8'}
                        <i class="fa fa-rss"></i>
                      </a>
                    </li>
                  {/foreach}
              </div>
            </div>
          {/if}
      {else}
          {l s='Rss not available' mod='ets_seo'}
      {/if}

  </div>
</div>