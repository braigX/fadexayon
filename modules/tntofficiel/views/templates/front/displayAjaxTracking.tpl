{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

<html>
    <head>
        <title>{l s='shipping detail' mod='tntofficiel'}</title>
        <link type="text/css" href="{TNTOfficiel::getURLModulePath('css')|escape:'htmlall':'UTF-8'}tracking.css" rel="stylesheet" />
        <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    </head>
    <body style="padding:0;margin:0;">
    <div id="order_tracking">
        <div class="tracking-header">
            {l s='shipping detail' mod='tntofficiel'}
            <a class="close" href="#" onclick="window.close(); window.opener.focus();return false;" title="{l s='Close' mod='tntofficiel'}"> </a>
        </div>
        <div class="tracking-body">
        {foreach from=$arrObjTNTParcelModelList item=objTNTParcelModel}
            {assign var='arrStageEvents' value=$objTNTParcelModel->getStageEvents()}
            <div class="header-track">
                <div class="track-label">{l s='tracking number' mod='tntofficiel'}</div>
                <div class="track-number">{$objTNTParcelModel->parcel_number|escape:'htmlall':'UTF-8'}</div>
                <div class="button">
                    <a href="{$objTNTParcelModel->tracking_url|escape:'html':'UTF-8'}" onclick="this.target='_blank';">{l s='follow my parcel' mod='tntofficiel'}&nbsp;<div class="tnt-arrow"></div></a>
                </div>
            </div>
            <div class="status-track">
                {if ($objTNTParcelModel->stage_id > 0)}
                    <ul>{foreach from=$objTNTParcelModel->getStageList() item=strStageLabel key=intStageCode}<li class="status {if ($intStageCode == $objTNTParcelModel->stage_id || ($intStageCode == TNTOfficielParcel::STAGE_DELIVERED && $objTNTParcelModel->stage_id > TNTOfficielParcel::STAGE_DELIVERED))} current {/if}" >{$strStageLabel|escape:'htmlall':'UTF-8'}</li>{/foreach}</ul>
                {/if}
                {if (isset($arrStageEvents))}
                    <div class="history-track">
                        <table>
                            <tbody>
                            {foreach from=$arrStageEvents item=arrStageEventInfos key=intEventIndex}
                                <tr>
                                    <td>
                                        <span class="index">{$intEventIndex|intval}</span>
                                    </td>
                                    <td>
                                        <span class="label">{$arrStageEventInfos['label']|escape:'htmlall':'UTF-8'}</span>
                                    </td>
                                    <td>
                                        <span class="date">
                                        {if (isset($arrStageEventInfos['date']) && strlen($arrStageEventInfos['date']))}
                                            {$arrStageEventInfos['date']|escape:'htmlall':'UTF-8'|date_format:"%d.%m.%Y"}
                                        {/if}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="hour">
                                        {if (isset($arrStageEventInfos['date']) && strlen($arrStageEventInfos['date']))}
                                            {$arrStageEventInfos['date']|escape:'htmlall':'UTF-8'|date_format:"%R"}
                                        {/if}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="center">
                                        {if (isset($arrStageEventInfos['center']) && strlen($arrStageEventInfos['center']))}
                                            {$arrStageEventInfos['center']|escape:'htmlall':'UTF-8'}
                                        {/if}
                                        </span>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                {/if}
            </div>
        {/foreach}
        </div>
        <div class="tracking-footer">
            <div class="button">
                <a href="#" onclick="window.close(); window.opener.focus();return false;">{l s='Close' mod='tntofficiel'}</a>
            </div>
        </div>
    </div>
    </body>
</html>