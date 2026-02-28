{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
{if !$jpresta_account_key}
<script type="application/javascript">
    function refreshSubmitModuleJakStatus() {
        if ($('#jprestaAccountKey').val().length >= 20 && $('input[name=prestashopType]:checked').length > 0) {
            $('#submitModuleJak').removeAttr('disabled');
            $('#cannotValidate').hide();
        }
        else {
            $('#submitModuleJak').attr('disabled', '1');
            $('#cannotValidate').show();
        }
    }
    $(document).ready(function() {
        refreshSubmitModuleJakStatus();
        $('input').on('keyup keypress blur change', refreshSubmitModuleJakStatus);
    });
</script>
<style type="text/css">
    button:disabled {
        cursor: not-allowed;
        pointer-events: all !important;
    }
</style>
{/if}
<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <h3 class="panel-heading"><img height="20" src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/logo-jpresta.png" alt=""/>&nbsp;{l s='JPresta account' mod='jprestaspeedpack'}</h3>

            {if $pagecache_clone_detected}
                <div class="alert alert-danger">
                    <strong>{l s='This Prestashop instance seems to be a clone of an other Prestahop.' mod='jprestaspeedpack'}</strong>
                    <p>
                        {l s='Clones are allowed but can messes up your JPresta-Cache-Warmer subscription so please, just tell us if it is a clone or not.' mod='jprestaspeedpack'}
                    </p>
                    <p>
                        {l s='This message can be displayed if you modified your database connexion or the URL of your shop. If so then just click on "No, it is the same Prestashop".' mod='jprestaspeedpack'}
                    </p>

                    <form id="confirmClone" method="post" action="{$request_uri|escape:'html':'UTF-8'}" class="form-inline">
                        <input type="hidden" name="submitModule" value="true"/>
                        <input type="hidden" name="pctab" value="license"/>
                        <div style="text-align: left; margin: 10px 0 0 0;">
                            <button type="submit" id="submitModuleConfirmClone" name="submitModuleConfirmClone" class="btn btn-secondary">{l s='Yes, it is a clone' mod='jprestaspeedpack'}</button>
                            <button type="submit" id="submitModuleNotAClone" name="submitModuleNotAClone" class="btn btn-secondary">{l s='No, it is the same Prestashop' mod='jprestaspeedpack'}</button>
                        </div>
                    </form>
                </div>
            {/if}

            {if !$jpresta_account_key}
                <p>{l s="To use the JPresta Cache Warmer service, you must create an account on jpresta.com and attach your JPresta Account Key to this Prestashop instance." mod='jprestaspeedpack'}</p>
                <form method="post" action="{$request_uri|escape:'html':'UTF-8'}" class="form-inline">
                    <input type="hidden" name="submitModule" value="true"/>
                    <input type="hidden" name="pctab" value="license"/>
                    <div style="margin: 10px 0">
                        {l s='This Prestashop instance is: ' mod='jprestaspeedpack'}
                        <label class="radio-inline" style="margin-left: 10px">
                            <input type="radio" name="prestashopType" id="prestashopType1" value="prod"> {l s='a live site with real customers' mod='jprestaspeedpack'}
                        </label>
                        <label class="radio-inline" style="margin-left: 10px">
                            <input type="radio" name="prestashopType" id="prestashopType2" value="test"> {l s='for test only' mod='jprestaspeedpack'}
                        </label>
                    </div>
                    <div class="form-group">
                        <input type="text" style="width:20rem" class="form-control" id="jprestaAccountKey" name="jprestaAccountKey" placeholder="{l s='Example: JPRESTA-AB12YZ89XX00' mod='jprestaspeedpack'}">
                    </div>
                    <button type="submit" id="submitModuleJak" name="submitModuleJak" class="btn btn-primary"><i class="icon-sign-in"></i>&nbsp;{l s='Attach my JPresta Account Key' mod='jprestaspeedpack'}</button>
                    <div class="alert alert-warning" style="margin-top: 10px" id="cannotValidate">
                        {l s='In order to validate, select the type of Prestashop instance and fill in the JPresta Account Key' mod='jprestaspeedpack'}
                    </div>
                </form>
            {else}
                <p>
                    <input type="text" style="width:20rem;display: inline-block" class="form-control" name="jprestaAccountKey" readonly disabled value="{$jpresta_account_key|escape:'url':'UTF-8'}">
                    <i class="icon-check" style="font-size: 1.5rem; margin: 0 6px; color: green;"></i>
                    <a href="#" onclick="$('#confirmDetach').toggle()">{l s='detach' mod='jprestaspeedpack'}</a>
                </p>
                <p>{l s="Congratulation, the module is attached to your JPresta Account." mod='jprestaspeedpack'}</p>
                <form id="confirmDetach" style="display: none" method="post" action="{$request_uri|escape:'html':'UTF-8'}" class="form-inline">
                    <input type="hidden" name="submitModule" value="true"/>
                    <input type="hidden" name="pctab" value="license"/>
                    {l s="If you detach your JPresta Account your Cache Warmer subscription (if any) will be suspended" mod='jprestaspeedpack'}
                    <div style="text-align: center; margin: 10px 0 0 0;">
                    <button type="submit" id="submitModuleJakDetach" name="submitModuleJakDetach" class="btn btn-danger"><i class="icon-sign-out"></i>&nbsp;{l s='I confirm, detach my JPresta Account' mod='jprestaspeedpack'}</button>
                    </div>
                </form>
            {/if}
            {if $advanced_mode}
                <div style="border-top: 1px dotted #dbe6e9; margin-top: 1rem; padding-top: 0.5rem;">
                    <strong>{l s='Advanced mode' mod='jprestaspeedpack'}:</strong>
                    {l s='In case of troubles with license key, if you cloned your shop for exemple, you can recompute the ID of this Prestashop instance by clicking on the following button:' mod='jprestaspeedpack'}
                    <form method="post" action="{$request_uri|escape:'html':'UTF-8'}" class="form-inline">
                        <input type="hidden" name="submitModule" value="true"/>
                        <input type="hidden" name="pctab" value="license"/>
                        <button type="submit" id="submitModuleJakReset" name="submitModuleJakReset" class="btn btn-sm btn-danger">{l s='Recompute the ID of this Prestashop instance' mod='jprestaspeedpack'}</button>
                    </form>
                </div>
            {/if}
        </div>
    </div>
</div>
{if $jpresta_ps_token && $jpresta_account_key && !$maybe_a_clone}
<iframe loading="lazy" src="{$jpresta_api_url_licenses|escape:'html':'UTF-8'}?nogutter&ps_token={$jpresta_ps_token|escape:'url':'UTF-8'}&jpresta_account_key={$jpresta_account_key|escape:'url':'UTF-8'}&shop_url={$pagecache_cron_base|escape:'url':'UTF-8'}&shop_url_cw={$pagecache_cw_url|escape:'url':'UTF-8'}&shop_name={$shop_name|escape:'url':'UTF-8'}&ps_version={$prestashop_version|escape:'url':'UTF-8'}&module_name={$module_name|escape:'url':'UTF-8'}&module_version={$module_version|escape:'url':'UTF-8'}"
        style="width: 100%; height: 1500px; border: none"></iframe>
{/if}
