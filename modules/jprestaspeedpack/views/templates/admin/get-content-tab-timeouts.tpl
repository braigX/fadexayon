{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}

<script type="text/javascript">
    var slidersServer = [];
    var slidersBrowser = [];
    function onSliderChange(value, controller) {
        switch (value) {
            case 0:
                text = "{l s='Disabled' mod='jprestaspeedpack'}";
                break;
            case 1:
                text = "{l s='1 day' mod='jprestaspeedpack'}";
                break;
            case 10:
                text = "{l s='To infinity...' mod='jprestaspeedpack'}";
                break;
            case 8:
                value = 14;
                text = value + " {l s='days' mod='jprestaspeedpack'}";
                break;
            case 9:
                value = 30;
                text = value + " {l s='days' mod='jprestaspeedpack'}";
                break;
            default:
                text = value + " {l s='days' mod='jprestaspeedpack'}";
                break;
        }
        $("#pc"+controller+"SliderVal").text(text);
        if (value === 0) {
            if (slidersBrowser[controller]) { slidersBrowser[controller].setValue(0); onBrowserSliderChange(0, controller+"2"); slidersBrowser[controller].disable(); }
            $("#pc"+controller).parent().find(".slider-handle").css("background-color", "gray").css("background-image", "none");
        }
        else {
            if (slidersBrowser[controller]) slidersBrowser[controller].enable();
            $("#pc"+controller).parent().find(".slider-handle").css("background-image", "linear-gradient(to bottom,#149bdf 0,#0480be 100%);");
        }
    }
    function onBrowserSliderChange(value, controller) {
        switch (value) {
            case 0:
                text = "{l s='Disabled' mod='jprestaspeedpack'}";
                break;
            default:
                text = value + " {l s='minutes' mod='jprestaspeedpack'}";
                break;
        }
        $("#pc"+controller+"SliderVal").text(text);
        if (value === 0) {
            $("#pc"+controller).parent().find(".slider-handle").css("background-color", "gray").css("background-image", "none");
        }
        else {
            $("#pc"+controller).parent().find(".slider-handle").css("background-image", "linear-gradient(to bottom,#149bdf 0,#0480be 100%);");
        }
    }
$( document ).ready(function() {
    slidersBrowser["static"] = new Slider('#pcstatic2');
    slidersBrowser["static"].setValue({$pagecache_static_expires|intval|default:'0'});
    onBrowserSliderChange({$pagecache_static_expires|intval|default:'0'}, "static2");
    $("#pcstatic2").on("change", function(slideEvt) { onBrowserSliderChange(slideEvt.value.newValue, "static2"); });

    {foreach $managed_controllers as $controller_name => $controller}
        slidersServer["{$controller_name|escape:'javascript':'UTF-8'}"] = new Slider('#pc{$controller_name|escape:'javascript':'UTF-8'}');
        slidersServer["{$controller_name|escape:'javascript':'UTF-8'}"].setValue({$controller['timeout']|default:'0'});
        slidersBrowser["{$controller_name|escape:'javascript':'UTF-8'}"] = new Slider('#pc{$controller_name|escape:'javascript':'UTF-8'}2');
        slidersBrowser["{$controller_name|escape:'javascript':'UTF-8'}"].setValue({$controller['expires']|default:'0'});
        onSliderChange({$controller['timeout']|escape:'html':'UTF-8'|default:'0'}, "{$controller_name|escape:'javascript':'UTF-8'}");
        onBrowserSliderChange({$controller['expires']|escape:'html':'UTF-8'|default:'0'}, "{$controller_name|escape:'javascript':'UTF-8'}2");
        $("#pc{$controller_name|escape:'javascript':'UTF-8'}").on("change", function(slideEvt) { onSliderChange(slideEvt.value.newValue, "{$controller_name|escape:'javascript':'UTF-8'}"); });
        $("#pc{$controller_name|escape:'javascript':'UTF-8'}2").on("change", function(slideEvt) { onBrowserSliderChange(slideEvt.value.newValue, "{$controller_name|escape:'javascript':'UTF-8'}2"); });
    {/foreach}
});
</script>
<div class="panel">
<h3>{if $avec_bootstrap}<i class="icon-time"></i>{else}<img width="16" height="16" src="../img/admin/time.gif" alt=""/>{/if}&nbsp;{l s='Pages & timeouts' mod='jprestaspeedpack'}</h3>
<form id="pagecache_form_timeouts" action="{$request_uri|escape:'html':'UTF-8'}" method="post">
    <input type="hidden" name="submitModule" value="true"/>
    <input type="hidden" name="pctab" value="timeouts"/>
    <fieldset>
        <div style="clear: both;">
            {if $avec_bootstrap}
                <div class="bootstrap"><div class="alert alert-info" style="display: block;">
                        <dl>
                            <dt>{l s='Server cache maximum duration' mod='jprestaspeedpack'}</dt>
                            <dd>{l s='Server cache is automatically refreshed when you modify prices, descriptions, stocks, etc. (not when you do modifications in theme, modules, CSS, etc.) so here you set the maximum age of the cache if there is no modification.' mod='jprestaspeedpack'}</dd>
                            <dt>{l s='Browser cache duration' mod='jprestaspeedpack'}</dt>
                            <dd>{l s='Browser cache cannot be refreshed (except if the visitor refreshes the page in the browser) so here you set the maximum age of the cache whatever modifications are done in back office. This is why it is limited to 60 minutes.' mod='jprestaspeedpack'}</dd>
                        </dl>
                </div></div>
            {else}
                <div class="hint clear" style="display: block;">
                    <dl>
                        <dt>{l s='Server cache maximum duration' mod='jprestaspeedpack'}</dt>
                        <dd>{l s='Server cache is automatically refreshed when you modify prices, descriptions, stocks, etc. (not when you do modifications in theme, modules, CSS, etc.) so here you set the maximum age of the cache if there is no modification.' mod='jprestaspeedpack'}</dd>
                        <dt>{l s='Browser cache duration' mod='jprestaspeedpack'}</dt>
                        <dd>{l s='Browser cache cannot be refreshed (except if the visitor refreshes the page in the browser) so here you set the maximum age of the cache whatever modifications are done in back office. This is why it is limited to 60 minutes.' mod='jprestaspeedpack'}</dd>
                    </dl>
                </div>
            {/if}
            <h4>{l s='When using Prestashop Static cache' mod='jprestaspeedpack'}</h4>
            <div>
                {l s='When the static cache is used it is not possible to know what kind of page is requested and so the browser cache duration must be the same for all cached pages' mod='jprestaspeedpack'}:
            </div>
            <table>
                <tr class="first">
                    <td>{l s='Browser cache duration for static files' mod='jprestaspeedpack'}</td>
                    <td><input id="pcstatic2" name="pagecache_static_expires" style="padding: 0 10px;" type="text" data-slider-ticks="[0, 15, 30, 45, 60]" data-slider-value="{$pagecache_static_expires|escape:'html':'UTF-8'}" data-slider-tooltip="hide" data-slider-ticks-snap-bounds="3" data-slider-handle="square"/>&nbsp;<span id="pcstatic2SliderVal" style="font-weight:bold"></span></td>
                </tr>
            </table>
            <h4 style="margin-top: 2rem">{l s='By Pages' mod='jprestaspeedpack'}</h4>
            <table>
            {foreach $managed_controllers as $controller_name => $controller}
                <tr class="first">
                    <td class="label">{$controller['title']|escape:'html':'UTF-8'}</td>
                    <td>{l s='Server cache maximum duration' mod='jprestaspeedpack'}:</td>
                    <td class="slider"><input id="pc{$controller_name|escape:'html':'UTF-8'}" name="pagecache_{$controller_name|escape:'html':'UTF-8'}_timeout" style="padding: 0 10px;" type="text" data-slider-ticks="[0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]" data-slider-value="{$controller['timeout']|escape:'html':'UTF-8'}" data-slider-tooltip="hide" data-slider-handle="square"/>&nbsp;<span id="pc{$controller_name|escape:'html':'UTF-8'}SliderVal" style="font-weight:bold"></span></td>
                </tr>
                <tr>
                    <td></td>
                    <td>{l s='Browser cache duration' mod='jprestaspeedpack'}:</td>
                    <td class="slider"><input id="pc{$controller_name|escape:'html':'UTF-8'}2" name="pagecache_{$controller_name|escape:'html':'UTF-8'}_expires" style="padding: 0 10px;" type="text" data-slider-ticks="[0, 15, 30, 45, 60]" data-slider-value="{$controller['expires']|escape:'html':'UTF-8'}" data-slider-tooltip="hide" data-slider-ticks-snap-bounds="3" data-slider-handle="square"/>&nbsp;<span id="pc{$controller_name|escape:'html':'UTF-8'}2SliderVal" style="font-weight:bold"></span></td>
                </tr>
            {/foreach}
            </table>
        </div>
        <div class="bootstrap">
            <button type="submit" value="1" id="submitModuleTimeouts" name="submitModuleTimeouts" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='jprestaspeedpack'}
            </button>
        </div>
    </fieldset>
</form>
</div>
