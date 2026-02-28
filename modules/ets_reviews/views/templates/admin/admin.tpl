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

<script type="text/javascript">
    state_token = '{getAdminToken tab='AdminStates'}';
    address_token = '{getAdminToken tab='AdminAddresses'}';
</script>
{*{(Module::getInstanceByName('ets_reviews')->hookRenderJavascript()) nofilter}*}
<div class="ets_rv_panel">
    {$menus nofilter}
    {if $moduleIsEnabled}
        <div class="ets-pc-form-group-wrapper">
            {$html nofilter}
        </div>
    {else}
        <p class="alert alert-warning">{l s='You must enable "Product Reviews - Ratings, Google Snippets, Q&A" module to configure its features' mod='ets_reviews'}</p>
    {/if}
</div>
{if isset($currentTab) && preg_match('/Reviews|Comments|Replies|Users|Questions|QuestionComments|Answers|AnswerComments|Activity/i', $currentTab)}
    <div class="ets_rv_overload">
        <div class="table">
            <div class="table-cell">
                <div class="ets_rv_form_wrapper">
                    <div class="ets_rv_form_off" title="{l s='Close' mod='ets_reviews'}"></div>
                    <div class="ets_rv_form">
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}