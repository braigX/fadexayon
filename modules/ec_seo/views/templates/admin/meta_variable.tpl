<textarea id="ec_copy" name="hide" style="display:none;"></textarea>
<input type="hidden" id="m_vcopied" value="{l s='Your variable is copied, you can paste it' mod='ec_seo'}">
{foreach $variablesMeta as $key => $variables}
    {if !$tclass || $key == $tclass}
        <div id="meta_generator_variables_{$key|escape:'htmlall':'UTF-8'}" class="meta_generator_variables panel">
            <div class="panel-heading">{$key|escape:'htmlall':'UTF-8'} Variable</div>
            <ul class="ec_seo_variables">
                {foreach $variables as $variable => $title}
                    <li><a href="#" title="{$title|escape:'htmlall':'UTF-8'}">{$variable|escape:'htmlall':'UTF-8'}</a></li>
                {/foreach}
            </ul>
        </div>
    {/if}
{/foreach}