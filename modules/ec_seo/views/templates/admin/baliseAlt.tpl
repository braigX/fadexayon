<div id="balisealt_form">
    <textarea id="ec_copy" name="hide" style="display:none;"></textarea>
    <input type="hidden" id="m_vcopied" value="{l s='Your variable is copied, you can paste it' mod='ec_seo'}">
    <div id="meta_generator_variables_alt" class="meta_generator_variables panel">
    <div class="panel-heading">{l s='Image alt tag' mod='ec_seo'} Variable</div>
        <ul class="ec_seo_variables">
            {foreach $variablesMeta as $variable => $title}
            <li><a href="#" title="{$title|escape:'htmlall':'UTF-8'}">{$variable|escape:'htmlall':'UTF-8'}</a></li>  
            {/foreach}
        </ul>
    </div>
    {$balisealt_form nofilter}
    <div class="panel taskImageAlt">
        <div class="task-grp">
            <label class="control-label task-label">
                {l s='Bulk Produts Image Alt tag ' mod='ec_seo'}
            </label>
            <button class="btn btn-default task-btn" onclick="javascript:$.post('{$ec_seo_controller_uri|escape:'htmlall':'UTF-8'}bulkImageAlt?ec_token={$token|escape:'htmlall':'UTF-8'}&id_shop={$ec_id_shop|escape:'htmlall':'UTF-8'}');showNoticeMessage('{$trad_tasklaunched|escape:'htmlall':'UTF-8'}');$('#refreshTabBaliseAlt i').addClass('pulse');return false;">
                {l s='Launch' mod='ec_seo'}
            </button>
            <div class="margin-form task-form">
                <input type="text" readonly value="{$ec_seo_controller_uri|escape:'htmlall':'UTF-8'}bulkImageAlt?ec_token={$token|escape:'htmlall':'UTF-8'}&id_shop={$ec_id_shop|escape:'htmlall':'UTF-8'}" style="cursor: initial;"/>
            </div>
        </div>
        <div class="ec_suivi_task refreshImageAltPanel" >
            <i>{l s='(leave the mouse in the frame for a real-time display update)' mod='ec_seo'}</i>
            <div class="tabrefreshImageAltpanel"></div>
        </div>
    </div>
    {$table_modif nofilter}
</div>