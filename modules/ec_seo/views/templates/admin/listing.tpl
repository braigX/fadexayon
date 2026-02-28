<div class="ec_divscoreprod">
    {if count($score_lang) > 1}<a href ="{$EcSeoLink|escape:'htmlall':'UTF-8'}" target="blank"><span class="ecseovignette {$global_color|escape:'htmlall':'UTF-8'}"><span style="font-size:10px;">{l s='AVG' mod='ec_seo'}</span><br><span>{$global_score|escape:'htmlall':'UTF-8'}%</span></span></a>{/if}
    {foreach $score_lang as $id_lang => $val}
        {$mycolor = 'red'}
        {if $val['score'] >= 80}
            {$mycolor = 'green'}
        {else if $val['score'] >= 50}
            {$mycolor = 'yellow'}
        {else if $val['score'] >= 25}
            {$mycolor = 'orange'}
        {/if}
        <a href ="{$EcSeoLink|escape:'htmlall':'UTF-8'}" target="blank"><span class="ecseovignette {$mycolor|escape:'htmlall':'UTF-8'}"><span><img src="{$val['img']|escape:'htmlall':'UTF-8'}"/></span><br><span>{$val['score']|escape:'htmlall':'UTF-8'}%</span></span></a>
    {/foreach}
</div>