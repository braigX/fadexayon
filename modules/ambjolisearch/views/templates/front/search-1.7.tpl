{*
*   @author    Ambris Informatique
*   @copyright Copyright (c) 2013-2023 Ambris Informatique SARL
*   @license   Licensed under the EUPL-1.2-or-later
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file='catalog/listing/search.tpl'}

{block name='subcategory_list'}
  {if isset($categories) && is_array($categories) && count($categories) > 0}
    {include file='catalog/_partials/subcategories.tpl' subcategories=$categories}
  {/if}
{/block}


