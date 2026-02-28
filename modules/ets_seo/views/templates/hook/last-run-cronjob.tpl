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
{if $needRunNow}
  <div class="egl-block form-horizontal egl-warning-cronjob" style="margin-bottom: 5px;float: left;width: 100%">
    <p class="alert alert-danger">{l s='The last executed cronjob seems too old (longer than 7 days). It would be best if you ran cronjob now.' mod='ets_seo'}</p>
  </div>
{/if}
{if $lastRun}
  <div class="mb-25 emp-block form-horizontal" style="margin-top: 65px;margin-bottom: 25px;">
    <p class="alert alert-info">{l s='Last time cronjob run' mod='ets_seo'}: {$lastRun|escape:'html':'UTF-8'}</p>
  </div>
{/if}