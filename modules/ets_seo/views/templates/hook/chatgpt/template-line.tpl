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

<tr id="gptTplId{(isset($template['id'])) ? $template['id'] : 'Null'}">
  <td class="title">
    {(isset($template['label'])) ? array_shift($template['label']) : ''}
  </td>
  <td class="content">
    {(isset($template['content'])) ? array_shift($template['content']) : ''}
  </td>
  <td class="text-right">
    <div class="btn-group-action">
      <div class="btn-group pull-right">
        <a class="edit btn btn-default btn-edit-item" href="javascript:void(0);" data-id="{(isset($template['id'])) ? $template['id'] : '0'}"><i class="icon-pencil"></i> {$transMsg.edit|escape:'html':'UTF-8'}</a>
        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
          <i class="icon-caret-down"></i>&nbsp;
        </button>
        <ul class="dropdown-menu">
          <li>
            <a class="delete-gpt-template btn-delete-item" data-confirm="{$transMsg.delConfirm|escape:'html':'UTF-8'}" href="javascript:void(0);" data-id="{(isset($template['id'])) ? $template['id'] : '0'}"><i class="icon-trash"></i> {$transMsg.del|escape:'html':'UTF-8'}</a>
          </li>
        </ul>
      </div>
    </div>
  </td>
</tr>