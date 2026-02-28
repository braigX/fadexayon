{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<table class="table">
    <tbody>
    {foreach $systemInfos->getAll() as $key => $labelValue}
        <tr>
            <td style="font-weight: bold">{$labelValue['label']|escape:'html':'UTF-8'}</td>
            <td>{$labelValue['value']|escape:'html':'UTF-8'}</td>
        </tr>
    {/foreach}
    </tbody>
</table>
{if $op_cache}
<form id="pagecache_form_resetopcache" action="{$request_uri|escape:'html':'UTF-8'}" method="post">
    <input type="hidden" name="submitModule" value="true"/>
    <input type="hidden" name="pctab" value="diagnostic"/>
    <fieldset style="margin: 10px 0">
        <div class="bootstrap">
            <button type="submit" id="submitModuleResetOpcache" name="submitModuleResetOpcache" class="btn btn-warning">
                <i class="process-icon-delete"></i> {l s='Reset OP Cache' mod='jprestaspeedpack'}
            </button>
        </div>
    </fieldset>
</form>
{/if}
