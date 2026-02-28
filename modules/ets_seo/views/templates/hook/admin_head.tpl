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
{if isset($is_no_referrer) && $is_no_referrer}
<meta name="referrer" content="no-referrer">
{/if}
<script type="text/javascript">
    var text_image_not_valid = '{l s='The image is not in the correct format, accepted formats: png, jgg, jpeg, gif' mod='ets_seo' js=1}';
    var ETS_SEO_IS_CMS_CATEGORY = {$is_cms_category|escape:'html':'UTF-8'};
    var ETS_SEO_LANGUAGES = {$ets_languages|@json_encode nofilter};
    var ETS_SEO_DEFINED = {$ets_seo_defined|@json_encode nofilter};
    var ETS_SEO_SCORE_DATA = {$seoData|@json_encode nofilter};
    var ETS_SEO_ENABLE_AUTO_ANALYSIS = {if isset($isAutoAnalysis)}{$isAutoAnalysis|escape:'html':'UTF-8'}{else}1{/if};
    var ETS_SEO_ENABLE_CONTENT_ANALYSIS_BASE64 = {if isset($ets_seo_enable_content_analysis_base64)}{$ets_seo_enable_content_analysis_base64|escape:'html':'UTF-8'}{else}1{/if};
    var ETS_SEO_LINK_MODULE = "{$link_module|escape:'quotes':'UTF-8'}";
    var ETS_SEO_META_CODES = {$meta_codes|@json_encode nofilter};
    var ETS_SEO_LINK_REWRITE_RULES = {$link_rewrite_rules|@json_encode nofilter};
    var ets_seo_confirm_delete_image = "{l s='Do you want to delete this image?' mod='ets_seo'}";
    var ETS_SEO_PRODUCT_IMAGE = {if isset($product_image_data) && $product_image_data}{$product_image_data|@json_encode nofilter}{else}null{/if};
    var ETS_SEO_FORCE_USE_META_TEMPLATE = {if isset($forceUseMetaTemplate) && $forceUseMetaTemplate}1{else}0{/if};
    var ETS_SEO_MESSAGE = {literal}{};{/literal}
    ETS_SEO_MESSAGE['rating_avg_required'] = "{l s='The Average rating is required' mod='ets_seo'}";
    ETS_SEO_MESSAGE['rating_avg_invalid'] = "{l s='The Average rating is invalid' mod='ets_seo'}";
    ETS_SEO_MESSAGE['rating_avg_decimal'] = "{l s='The Average rating must be a decimal, greater than 0 and less than or equal to 5' mod='ets_seo'}";
    ETS_SEO_MESSAGE['rating_count_required'] = "{l s='The Rating count is required' mod='ets_seo'}";
    ETS_SEO_MESSAGE['rating_count_invalid'] = "{l s='The Rating count is invalid' mod='ets_seo'}";
    ETS_SEO_MESSAGE['rating_count_int'] = "{l s='The Rating count must be an integer and greater than 0' mod='ets_seo'}";
    ETS_SEO_MESSAGE['best_rating_required'] = "{l s='The Best rating is required' mod='ets_seo'}";
    ETS_SEO_MESSAGE['best_rating_invalid'] = "{l s='The Best rating is invalid' mod='ets_seo'}";
    ETS_SEO_MESSAGE['best_rating_int'] = "{l s='The Best rating must be an integer, greater than or equal to the average rating and less than 5' mod='ets_seo'}";
    ETS_SEO_MESSAGE['worst_rating_required'] = "{l s='The Worst rating is required' mod='ets_seo'}";
    ETS_SEO_MESSAGE['worst_rating_invalid'] = "{l s='The Worst rating is invalid' mod='ets_seo'}";
    ETS_SEO_MESSAGE['worst_rating_int'] = "{l s='The Worst rating must be an integer, greater than 0 and less than the average rating' mod='ets_seo'}";
    ETS_SEO_MESSAGE['vote'] = "{l s='review' mod='ets_seo'}";
    ETS_SEO_MESSAGE['votes'] = "{l s='reviews' mod='ets_seo'}";
    ETS_SEO_MESSAGE['analysis_success'] = "{l s='SEO and readability analysis are done' mod='ets_seo'}";
    ETS_SEO_MESSAGE['popover_meta_desc'] = "{l s='This description will appear in search engines. You need a single sentence, shorter than 156 characters (including spaces)' mod='ets_seo'}";
    ETS_SEO_MESSAGE['add_keyword'] = "{l s='Add keyword' mod='ets_seo'}";
    ETS_SEO_MESSAGE['placeholder_meta'] = "{l s='Leave blank to use meta template value' mod='ets_seo'}";
    ETS_SEO_MESSAGE['meta_title_recommended'] = "{l s='of 60 characters used (recommended)' mod='ets_seo'}";
    ETS_SEO_MESSAGE['meta_desc_recommended'] = "{l s='of 156 characters used (recommended)' mod='ets_seo'}";
    ETS_SEO_MESSAGE['minor_keyphrase_same_focus_keyphrase'] = "{l s='The related keyphrases should not be the same as focus keyphrase' mod='ets_seo'}";
    ETS_SEO_MESSAGE['focus_keyphrase_same_minor_keyphrase'] = "{l s='The focus keyphrases should not be the same as related keyphrases' mod='ets_seo'}";
    ETS_SEO_MESSAGE['no_index'] = "{l s='No index!' mod='ets_seo'}";
    ETS_SEO_MESSAGE['no_focus_keyphrase'] = "{l s='No Focus or Related key phrases!' mod='ets_seo'}";
    ETS_SEO_MESSAGE['excellent'] = "{l s='Excellent!' mod='ets_seo'}";
    ETS_SEO_MESSAGE['acceptance'] = "{l s='Acceptable!' mod='ets_seo'}";
    ETS_SEO_MESSAGE['not_good'] = "{l s='Not good!' mod='ets_seo'}";
    ETS_SEO_MESSAGE['not_analysis'] = "{l s=' No analysis available!' mod='ets_seo'}";
    ETS_SEO_MESSAGE['analyzing'] = "{l s='Analyzing...' mod='ets_seo'}";
    ETS_SEO_MESSAGE['analysis_success'] = "{l s='Analysis successfully. Please click "Save" button to save analysis results' mod='ets_seo'}";
    ETS_SEO_MESSAGE['warning_title_use_meta_template'] = "{l s='Force using preset meta template for meta title' mod='ets_seo'}";
    ETS_SEO_MESSAGE['warning_img_alt_use_meta_template'] = "{l s='Force using preset meta template for image alt attribute' mod='ets_seo'}";
    ETS_SEO_MESSAGE['warning_desc_use_meta_template'] = "{l s='Force using preset meta template for meta description' mod='ets_seo'}";
    var priceDisplayPrecision = 2;
    var ETS_SEO_CURRENT_CATEGORY_NAME = {if isset($defaultCategoryName) && $defaultCategoryName}{$defaultCategoryName|@json_encode nofilter}{else}null{/if};
</script>
{if $is_use_module}
    <script type="text/javascript" src="{$link_helpers_js|escape:'quotes':'UTF-8'}?v={rand()}"></script>
    <script type="text/javascript" src="{$link_select2_js|escape:'quotes':'UTF-8'}?v={rand()}"></script>
    <script type="text/javascript" src="{$link_admin_js|escape:'quotes':'UTF-8'}?v={rand()}"></script>
    <script type="text/javascript" src="{$link_analysis_js|escape:'quotes':'UTF-8'}?v={rand()}"></script>
{/if}
<script type="text/javascript" src="{$link_admin_all_js|escape:'quotes':'UTF-8'}?v={rand()}"></script>
