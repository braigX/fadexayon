{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<script type="text/javascript">
    {foreach key=key item=value from=$js_vars}
    var {$key} = "{$value|escape:'htmlall':'UTF-8'}";
    {/foreach}
</script>