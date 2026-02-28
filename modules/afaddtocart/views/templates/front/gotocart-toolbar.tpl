{**
 *  2024 ALGO-FACTORY.COM
 *
 *  NOTICE OF LICENSE
 *
 * @author        Algo Factory <contact@algo-factory.com>
 * @copyright     Copyright (c) 2024 Algo Factory
 * @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *
 * @version       1.0.0
 * @website       www.algo-factory.com
 *
 *  You can not resell or redistribute this software.
 *}
<script id="finalize-bar-template" type="x-tmpl-mustache">
{literal}
    <div id="afFinalizeCart">
        <div id="buttonProduct">
            <a class="btn btn-primary btn-block" href="{{cartUrl}}" style="background-color: {{backgroundColor}}; color: {{textColor}};">
                {{summaryString}} {{inMyCartText}}
            </a>
        </div>
    </div>
{/literal}
</script>
