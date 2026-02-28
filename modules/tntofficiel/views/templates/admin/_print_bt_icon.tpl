{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

<span class="btn-group-action">
    <span class="btn-group">
        <a class="btn btn-default _blank {if ($strBTLabelName == '')}disabled{/if}"
           href="{$hrefDownloadBT|escape:'html':'UTF-8'}"
           target="_blank"
           rel="tooltip"
           title="{$strBTLabelName|escape:'html':'UTF-8'}"
        >
            <i class="icon-tnt"></i>
        </a>
        <a class="btn btn-default _blank"
           href="{$hrefGetManifest|escape:'html':'UTF-8'}"
           rel="tooltip"
           title="{l s='Manifest' mod='tntofficiel'}"
        >
            <i class="icon-file-text"></i>
        </a>
    </span>
</span>
