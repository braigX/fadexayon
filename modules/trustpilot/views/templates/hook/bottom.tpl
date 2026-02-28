{**
 * Trustpilot Module
 *
 *  @author    Trustpilot
 *  @copyright Trustpilot
 *  @license   https://opensource.org/licenses/OSL-3.0
 *}
{literal}
<script data-keepinline="true">
    window.trustpilot_trustbox_settings = {/literal}{$trustbox_settings|@json_encode nofilter};{* HTML content, no escape necessary *}{literal}
</script>
{/literal}