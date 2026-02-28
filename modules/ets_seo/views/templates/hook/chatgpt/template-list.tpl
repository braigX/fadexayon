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

<div id="gptTemplateAddDiv" style="display: block">
    {include './template-add.tpl' transMsg=$transMsg fields=$fields_list languages=$languages}
</div>
<div id="gptTemplateEditDiv" style="display: block"></div>
<table class="table configuration" id="tableGptTemplate" data-count="{'<span class="badge" id="gptTemplateCount">'|cat:$totalRecords|cat:'</span>'|escape:'html':'UTF-8'}">
  <thead>
  <tr style="display: none!important;">
    <th id="panelActions">
      <span class="panel-heading-action">
        <a class="list-toolbar-btn btn-new-item" href="javascript:void(0);">
          <span data-placement="top" data-html="true" data-original-title="{$transMsg.addNew|escape:'html':'UTF-8'}" class="label-tooltip" data-toggle="tooltip" title="">
            <i class="process-icon-new"></i>
          </span>
        </a>
      </span>
    </th>
  </tr>
  <tr class="nodrag nodrop">
    <th class="title">
      <span class="title_box">
          {$transMsg.label|escape:'html':'UTF-8'}
      </span>
    </th>
    <th class="content">
      <span class="title_box">
          {$transMsg.content|escape:'html':'UTF-8'}
      </span>
    </th>
    <th style="text-align: right;">{$transMsg.action|escape:'html':'UTF-8'}</th>
  </tr>
  </thead>
  <tbody id="listGptTemplate">
  {if $totalRecords > 0}
      {foreach from=$templates item='template'}
          {include './template-line.tpl' template=get_object_vars($template) transMsg=$transMsg}
      {/foreach}
  {else}
    <tr><td colspan="3" width="100%" style="text-align: center;padding:10px;">{l s='No data found.' mod='ets_seo'}</td></tr>
  {/if}
  </tbody>
</table>