{**
 * Trustpilot Module
 *
 *  @author    Trustpilot
 *  @copyright Trustpilot
 *  @license   https://opensource.org/licenses/OSL-3.0
 *}
{literal}
<script data-keepinline="true">
    var trustpilot_invitation = {/literal}{$invitation|@json_encode nofilter};{* HTML content, no escape necessary *}{literal}
</script>
<script src="{/literal}{$invite_js_dir}{literal}"></script>
{/literal}