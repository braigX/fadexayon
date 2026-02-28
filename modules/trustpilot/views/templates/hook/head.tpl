{**
 * Trustpilot Module
 *
 *  @author    Trustpilot
 *  @copyright Trustpilot
 *  @license   https://opensource.org/licenses/OSL-3.0
 *}
{literal}
<script data-keepinline="true">
    var trustpilot_script_url = '{/literal}{$script_url}{literal}';
    var trustpilot_key = '{/literal}{$key}{literal}';
    var trustpilot_widget_script_url = '{/literal}{$widget_script_url}{literal}';
    var trustpilot_integration_app_url = '{/literal}{$integration_app_url}{literal}';
    var trustpilot_preview_css_url = '{/literal}{$preview_css_url}{literal}';
    var trustpilot_preview_script_url = '{/literal}{$preview_script_url}{literal}';
    var trustpilot_ajax_url = '{/literal}{$trustpilot_ajax_url}{literal}';
    var user_id = '{/literal}{$user_id}{literal}';
    var trustpilot_trustbox_settings = {/literal}{$trustbox_settings|@json_encode nofilter};{* HTML content, no escape necessary *}{literal}
</script>
<script src="{/literal}{$register_js_dir}{literal}"></script>
<script src="{/literal}{$trustbox_js_dir}{literal}"></script>
<script src="{/literal}{$preview_js_dir}{literal}"></script>
{/literal}