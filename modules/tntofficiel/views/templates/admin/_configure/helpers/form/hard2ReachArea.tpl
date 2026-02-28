{**
* TNT OFFICIAL MODULE FOR PRESTASHOP.
*
* @author    Inetum <inetum.com>
* @copyright 2016-2024 Inetum, 2016-2024 TNT
* @license   https://opensource.org/licenses/MIT MIT License
*}

<p class="clearfix">
    <a class="btn btn-default tntofficiel-action-update-hra" href="javascript:void(0);"><i class="icon-cogs"></i>
        {l s='Download and update the list of communes in hard-to-access areas' mod='tntofficiel'}</a>
    <br/><a class="_blank" href="{TNTOfficielCarrier::URL_HRA_HELP|escape:'html':'UTF-8'}">
        {l s='Consult the list of communes subject to a supplement difficult access areas.' mod='tntofficiel'}</a>
</p>

<script type="text/javascript">
{literal}

    // On Ready.
    window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
    window.TNTOfficiel_Ready.push(function (jQuery) {

        jQuery(window.document)
        .off('click.' + window.TNTOfficiel.module.name, '.tntofficiel-action-update-hra')
        .on('click.' + window.TNTOfficiel.module.name, '.tntofficiel-action-update-hra', function (objEvent) {
            // Prevent bubbling plus further handlers to execute.
            objEvent.stopImmediatePropagation();
            // Prevent default action.
            objEvent.preventDefault();

            var objJqXHR = TNTOfficiel_AJAX({
                "url": window.TNTOfficiel.link.back.module.updateHRA,
                "method": 'GET',
                "dataType": 'json',
                "async": true,
                "cache": false
            });

            objJqXHR
            .done(function (objResponseJSON, strTextStatus, objJqXHR) {
                if (objResponseJSON && objResponseJSON.result) {
                    showSuccessMessage(TNTOfficiel_getCodeTranslate('successUpdateSuccessful'));
                } else {
                    showErrorMessage(TNTOfficiel_getCodeTranslate('errorDownloadingHRA'));
                }
            })
            .fail(function (objJqXHR, strTextStatus, strErrorThrown) {
                //window.console.error(objJqXHR.status + ' ' + objJqXHR.statusText);
            });

            return false;
        });

    });

{/literal}
</script>