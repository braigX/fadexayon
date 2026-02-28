{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<script type="text/javascript">
    $( document ).ready(function() {
        $('#btn-webp-faq-li').prependTo('.btn-toolbar ul.nav');
    });
</script>
<div class="alert alert-info">
    {l s='If you have any questions or encounter a problem, our FAQ provides a list of known issues along with their solutions' mod='jprestaspeedpack'}: <a href="{$faq_url|escape:'html':'UTF-8'}" target="_blank" class="btn btn-sm btn-success">{l s='FAQ Webp/Avif' mod='jprestaspeedpack'}</a>
</div>
<ul style="display:none">
    <li id="btn-webp-faq-li">
        <a id="webp-faq" class="toolbar_btn" href="{$faq_url|escape:'html':'UTF-8'}" target="_blank" style="color:white; background-color: #33bd25">
            <i class="process-icon-help" style="color:white;"></i>
            <div>{l s='FAQ Webp/Avif' mod='jprestaspeedpack'}</div>
        </a>
    </li>
</ul>
