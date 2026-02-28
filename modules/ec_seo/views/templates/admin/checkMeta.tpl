{if $field == 'h1'}
    {foreach $cat->h1 as $id_lang => $value}
        {if in_array($id_lang, $tab_lang)} 
            {assign var="len" value={$value|count_characters:true}}
            <div style="display:none;" id="ec_seo_h1_{$id_lang|escape:'htmlall':'UTF-8'}" class="ec_seo_{$id_lang|escape:'htmlall':'UTF-8'} ec_seo col-lg-9"><p class="help-block"><span class="h1_len_{$id_lang|escape:'htmlall':'UTF-8'}">{$len|escape:'htmlall':'UTF-8'}</span> {l s='characters (20-100 characters recommended)' mod='ec_seo'}</p>
                <div class="seo-txt-item bad h1min" {if $len > $category_rule['h1']['min']}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your h1 tag is too short' mod='ec_seo'} (<span class="h1_len_{$id_lang|escape:'htmlall':'UTF-8'}">{$len|escape:'htmlall':'UTF-8'}</span>). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['h1']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['h1']['max']|escape:'htmlall':'UTF-8'} {l s='characters ! Give more details to your h1 tag to make your prospects even more interested in coming to your site.' mod='ec_seo'}</div>
                <div class="seo-txt-item bad h1max" {if $len < $category_rule['h1']['max']}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your h1 tag is too long' mod='ec_seo'} (<span class="h1_len_{$id_lang|escape:'htmlall':'UTF-8'}">{$len|escape:'htmlall':'UTF-8'}</span>). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['h1']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['h1']['max']|escape:'htmlall':'UTF-8'} {l s='characters!' mod='ec_seo'} </div>
                <div class="seo-txt-item good h1lenok" {if $len > $category_rule['h1']['max'] || $len < $category_rule['h1']['min']|escape:'htmlall':'UTF-8'}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your balise h1 a une taille parfaite (entre' mod='ec_seo'} {$category_rule['h1']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['h1']['max']|escape:'htmlall':'UTF-8'} {l s='characters) !' mod='ec_seo'}</div>
                
                <div class="seo-txt-item good keyword" {if $tab_keyword[$id_lang]['h1']['keyword']['res'] = 2}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your h1 tag contains all the words of "' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
                <div class="seo-txt-item bad keyword" {if $tab_keyword[$id_lang]['h1']['keyword']['res'] == 1}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your h1 tag does not contain all the words of "' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='".It can be improved! You should add the following keywords:' mod='ec_seo'}<span class="h1_motneeded_{$id_lang|escape:'htmlall':'UTF-8'}">{$tab_keyword[$id_lang]['h1']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}.</span> </div>
                <div class="ec_competitors_info">
                    <div class="ec_datakeyword_title_block">
                        <h4>{l s='H1s from competitors' mod='ec_seo'}<span class="badge">{count($infoKeywordData[$id_lang]['h1s'])|escape:'htmlall':'UTF-8'}</span></h4>
                        <i class="{if $is17}material-icons{else}ec_icon16{/if} iclose">{if $is17}navigate_next{/if}</i>
                    </div>
                    <div class="ec_competitors_info_list" style="display:none;">
                        {foreach $infoKeywordData[$id_lang]['h1s'] as $key => $info_tag}
                            <div class="ec_competitors_info_elem">
                                <div class="ec_competitors_info_elem_data">{$info_tag.h1|escape:'htmlall':'UTF-8'}</div>
                                <div class="ec_competitors_info_elem_url"><a href="{$info_tag.url|escape:'htmlall':'UTF-8'}" target="_blank">{$info_tag.url|escape:'htmlall':'UTF-8'}</a></div>
                            </div>
                        {/foreach}
                       
                    </div>
                </div>         
            </div>
        {/if}
        
    {/foreach}
{else if $field == 'meta_title'}
    {foreach $cat->meta_title as $id_lang => $value}
        {if in_array($id_lang, $tab_lang)} 
            {assign var="len" value={$value|count_characters:true}}
            <div style="display:none;" id="ec_seo_meta_title_{$id_lang|escape:'htmlall':'UTF-8'}" class="ec_seo_{$id_lang|escape:'htmlall':'UTF-8'} ec_seo col-lg-9"><p class="help-block"><span class="meta_title_len_{$id_lang|escape:'htmlall':'UTF-8'}">{$len|escape:'htmlall':'UTF-8'}</span> {l s='characters (46-65 characters recommended)' mod='ec_seo'}</p>
                <div class="seo-txt-item bad meta_titlemin" {if $len > $category_rule['meta_title']['min']}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your Title tag is too short' mod='ec_seo'} (<span class="meta_title_len_{$id_lang|escape:'htmlall':'UTF-8'}">{$len|escape:'htmlall':'UTF-8'}</span>). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['meta_title']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_title']['max']|escape:'htmlall':'UTF-8'} {l s='characters!' mod='ec_seo'} </div>
                <div class="seo-txt-item bad meta_titlemax" {if $len < $category_rule['meta_title']['max']}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your Title tag is too long! Try not to exceed 65 characters.' mod='ec_seo'}</div>
                <div class="seo-txt-item good meta_titlelenok" {if $len > $category_rule['meta_title']['max'] || $len < $category_rule['meta_title']['min']}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your Title tag has a perfect size (between' mod='ec_seo'} {$category_rule['meta_title']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_title']['max']|escape:'htmlall':'UTF-8'} {l s='characters) !' mod='ec_seo'}</div>

                <div class="seo-txt-item good keyword" {if $tab_keyword[$id_lang]['meta_title']['keyword']['res'] == 2}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your Title tag contains all the words of "' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
                <div class="seo-txt-item bad keyword" {if $tab_keyword[$id_lang]['meta_title']['keyword']['res'] == 1}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your Title tag does not contain all the words of"' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='".It can be improved! You should add the following keywords:' mod='ec_seo'}<span class="meta_title_motneeded_{$id_lang|escape:'htmlall':'UTF-8'}">{$tab_keyword[$id_lang]['meta_title']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}.</span> </div>
                <div class="ec_competitors_info">
                    <div class="ec_datakeyword_title_block">
                        <h4>{l s='Meta titles from competitors' mod='ec_seo'}<span class="badge">{count($infoKeywordData[$id_lang]['titles'])|escape:'htmlall':'UTF-8'}</span></h4>
                        <i class="{if $is17}material-icons{else}ec_icon16{/if} iclose">{if $is17}navigate_next{/if}</i>
                    </div>
                    <div class="ec_competitors_info_list" style="display:none;">
                        {foreach $infoKeywordData[$id_lang]['titles'] as $key => $info_tag}
                            <div class="ec_competitors_info_elem">
                                <div class="ec_competitors_info_elem_data">{$info_tag.title|escape:'htmlall':'UTF-8'}</div>
                                <div class="ec_competitors_info_elem_url"><a href="{$info_tag.url|escape:'htmlall':'UTF-8'}" target="_blank">{$info_tag.url|escape:'htmlall':'UTF-8'}</a></div>
                            </div>
                        {/foreach}
                        
                    </div>
                </div> 
            </div>
            
        {/if}
        
    {/foreach}
{else if $field == 'meta_description'}
    {foreach $cat->meta_description as $id_lang => $value}
        {if in_array($id_lang, $tab_lang)} 
            {assign var="len" value={$value|count_characters:true}}
            <div style="display:none;" id="ec_seo_meta_description_{$id_lang|escape:'htmlall':'UTF-8'}" class="ec_seo_{$id_lang|escape:'htmlall':'UTF-8'} ec_seo col-lg-9"><p class="help-block"><span class="meta_description_len_{$id_lang|escape:'htmlall':'UTF-8'}">{$len|escape:'htmlall':'UTF-8'}</span> {l s='characters (101-200 characters recommended)' mod='ec_seo'}</p>
                <div class="seo-txt-item bad meta_descriptionmin" {if $len > $category_rule['meta_description']['min']}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your meta description tag is too short' mod='ec_seo'} (<span class="meta_description_len_{$id_lang|escape:'htmlall':'UTF-8'}">{$len|escape:'htmlall':'UTF-8'}</span>). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['meta_description']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_description']['max']|escape:'htmlall':'UTF-8'} {l s='characters ! Give more details to Your meta description to make your prospects even more interested in coming to Your site.' mod='ec_seo'} </div>

                <div class="seo-txt-item bad meta_descriptionmax" {if $len < $category_rule['meta_description']['max']}style="display:none;"{/if}> <span class="seo-icon bad">🔧</span>{l s='Your meta description tag is too long! Try not to exceed 200 characters.' mod='ec_seo'}</div>
                
                <div class="seo-txt-item good meta_descriptionlenok" {if $len > $category_rule['meta_description']['max'] || $len < $category_rule['meta_description']['min']}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your meta description tag has a perfect size (between' mod='ec_seo'} {$category_rule['meta_description']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_description']['max']|escape:'htmlall':'UTF-8'} {l s='characters) !' mod='ec_seo'}</div>

                <div class="seo-txt-item good keyword" {if $tab_keyword[$id_lang]['meta_description']['keyword']['res'] == 2}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your meta description tag contains all the words of "' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
                <div class="seo-txt-item bad keyword" {if $tab_keyword[$id_lang]['meta_description']['keyword']['res'] == 1}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your meta description tag does not contain all the words of"' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='".It can be improved! You should add the following keywords:' mod='ec_seo'}<span class="meta_description_motneeded_{$id_lang|escape:'htmlall':'UTF-8'}">{$tab_keyword[$id_lang]['meta_description']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}.</span> </div>
                <div class="ec_competitors_info">
                    <div class="ec_datakeyword_title_block">
                        <h4>{l s='Meta descriptions from competitors' mod='ec_seo'}<span class="badge">{count($infoKeywordData[$id_lang]['descriptions'])|escape:'htmlall':'UTF-8'}</span></h4>
                        <i class="{if $is17}material-icons{else}ec_icon16{/if} iclose">{if $is17}navigate_next{/if}</i>
                    </div>
                    <div class="ec_competitors_info_list" style="display:none;">
                        {foreach $infoKeywordData[$id_lang]['descriptions'] as $key => $info_tag}
                            <div class="ec_competitors_info_elem">
                                <div class="ec_competitors_info_elem_data">{if isset($info_tag.description)}{$info_tag.description|escape:'htmlall':'UTF-8'}{/if}</div>
                                <div class="ec_competitors_info_elem_url"><a href="{$info_tag.url|escape:'htmlall':'UTF-8'}" target="_blank">{$info_tag.url|escape:'htmlall':'UTF-8'}</a></div>
                            </div>
                        {/foreach}
                        
                    </div>
                </div> 
            </div>
        {/if}
        
    {/foreach}
{else if $field == 'link_rewrite'}
    {foreach $cat->link_rewrite as $id_lang => $value}
        {if in_array($id_lang, $tab_lang)} 
        {assign var="len" value={$value|count_characters:true}}
        <div style="display:none;" id="ec_seo_link_rewrite_{$id_lang|escape:'htmlall':'UTF-8'}" class="ec_seo_{$id_lang|escape:'htmlall':'UTF-8'} ec_seo col-lg-9"><p class="help-block"><span class="link_rewrite_len_{$id_lang|escape:'htmlall':'UTF-8'}">{$len|escape:'htmlall':'UTF-8'}</span> {l s='characters (21-100 characters recommended)' mod='ec_seo'}</p>
            <div class="seo-txt-item bad link_rewritemin" {if $len > $category_rule['link_rewrite']['min']}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your url is very short, are you sure you have included your keywords in your url?' mod='ec_seo'}</div> 

            <div class="seo-txt-item bad link_rewritemax" {if $len < $category_rule['link_rewrite']['max']}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your url is long! The ideal would be not to exceed 100 characters.' mod='ec_seo'}{* (<span class="link_rewrite_len_{$id_lang}">{$len}</span>). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['link_rewrite']['min']} {l s='and' mod='ec_seo'} {$category_rule['link_rewrite']['max']} {l s='characters!' mod='ec_seo'} (<span class="link_rewrite_full_{$id_lang}">{$len}</span>)*}</div> 

            <div class="seo-txt-item good link_rewritelenok" {if $len > $category_rule['link_rewrite']['max'] || $len < $category_rule['link_rewrite']['min']}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your url has a standard size!' mod='ec_seo'} {* {$category_rule['link_rewrite']['min']} {l s='and' mod='ec_seo'} {$category_rule['link_rewrite']['max']} {l s='characters) !' mod='ec_seo'} (<span class="link_rewrite_full_{$id_lang}">{$value}</span>) *}</div>

            <div class="seo-txt-item good keyword" {if $tab_keyword[$id_lang]['link_rewrite']['keyword']['res'] == 2}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your url contains all the words of "' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
            <div class="seo-txt-item bad keyword" {if $tab_keyword[$id_lang]['link_rewrite']['keyword']['res'] == 1}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your url does not contain all the words of"' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='".It can be improved! You should add the following keywords:' mod='ec_seo'}<span class="link_rewrite_motneeded_{$id_lang|escape:'htmlall':'UTF-8'}">{$tab_keyword[$id_lang]['link_rewrite']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}.</span> </div>

            <div class="seo-txt-item bad link_rewrite_barre" style="display:none;"><span class="seo-icon bad">🔧</span>{l s='Avoid using the \'_\' character, use \'- \'' mod='ec_seo'}</div> 
            <div class="seo-txt-item bad link_rewrite_spe" style="display:none;"><span class="seo-icon bad">🔧</span>{l s='Avoid the use of special characters such as \'* + é è à ç "... \'' mod='ec_seo'}</div> 
            <div class="seo-txt-item bad link_rewrite_sep" style="display:none;"><span class="seo-icon bad">🔧</span>{l s='Simplify your urls by reducing the number of separators \'/ \'' mod='ec_seo'}</div> 
        </div>
        {/if}
        
    {/foreach}
{else if $field == 'desc_total'}
    {foreach $cat->description as $id_lang => $value}
        {if in_array($id_lang, $tab_lang)} 
            <div style="display:none;" id="ec_seo_description_{$id_lang|escape:'htmlall':'UTF-8'}" class="ec_seo_{$id_lang|escape:'htmlall':'UTF-8'} ec_seo col-lg-9"><p class="help-block"><span class="description_len_{$id_lang|escape:'htmlall':'UTF-8'}">{$tab_keyword[$id_lang]['desc_total']['count_word']|escape:'htmlall':'UTF-8'}</span> {l s='words (>300 words recommended, description 1 and 2).' mod='ec_seo'}<span class="ecseo_saveDesc"> {l s='Please note, you must "Save" your modifications so that the optimization score of the "Description" fields recalculates.' mod='ec_seo'}<span></p>
                <div class="seo-txt-item bad descriptionmin" {if $tab_keyword[$id_lang]['desc_total']['count_word']  > 300 }style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your description must contain more than 300 words.' mod='ec_seo'}</div>

                
                <div class="seo-txt-item good descriptionlenok"{if $tab_keyword[$id_lang]['desc_total']['count_word']  < 300 }style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your description has a perfect size.' mod='ec_seo'}</div>

                <div class="seo-txt-item good keyword" {if $tab_keyword[$id_lang]['desc_total']['keyword']['res'] == 2}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your description contains all the words of "' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
                <div class="seo-txt-item bad keyword" {if $tab_keyword[$id_lang]['desc_total']['keyword']['res'] == 1}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your description does not contain all the words of"' mod='ec_seo'}{$ec_keyword[$id_lang]|escape:'htmlall':'UTF-8'}{l s='". It can be improved! You should add the following keywords (in the first 100 words of your description):' mod='ec_seo'}<span class="description_motneeded_{$id_lang|escape:'htmlall':'UTF-8'}">{$tab_keyword[$id_lang]['desc_total']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}.</span> </div>

                <div class="seo-txt-item good keyword" {if $tab_keyword[$id_lang]['desc_total']['h2'] == 0}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='That\'s great, there is at least one h2 tag.' mod='ec_seo'}</div>
                <div class="seo-txt-item bad keyword"{if $tab_keyword[$id_lang]['desc_total']['h2'] == 1}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='There is no h2 tag' mod='ec_seo'}</div>
                <input type="hidden" id="ec_seo_desc_score_{$id_lang|escape:'htmlall':'UTF-8'}" value="{$tab_keyword[$id_lang]['score']|escape:'htmlall':'UTF-8'}"/>
                <div class="ec_recommended_keywords">
                    <div class="ec_datakeyword_title_block">
                        <h4>{l s='Recommended Keywords' mod='ec_seo'}<span class="badge">{count($infoKeywordData[$id_lang]['keywordsDetails'])|escape:'htmlall':'UTF-8'}</span></h4>
                        <i class="{if $is17}material-icons{else}ec_icon16{/if} iclose">{if $is17}navigate_next{/if}</i>
                    </div>
                    <div class="ec_recommended_keywords_list" style="display:none;">
                        {foreach $infoKeywordData[$id_lang]['keywordsDetails'] as $key => $info_keyword}
                            <div class="ec_reco_keyword">
                                <span class="{if $info_keyword['currentOccurences']>=$info_keyword['minTargetOccurences']}el-tag el-tag--success{else}el-tag el-tag--info{/if}">
                                    {if $info_keyword['currentOccurences']>=$info_keyword['maxTargetOccurences']}<span style="color: red;">⚠</span>{/if}{$info_keyword['text']|escape:'htmlall':'UTF-8'}
                                </span>
                                <div class="popup_info_keyword" style="display:none">
                                    {l s='Nb Occurrences' mod='ec_seo'} : {$info_keyword['currentOccurences']|escape:'htmlall':'UTF-8'} (min {$info_keyword['minTargetOccurences']|escape:'htmlall':'UTF-8'}, max {$info_keyword['maxTargetOccurences']|escape:'htmlall':'UTF-8'})
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
                <div class="ec_related_keywords">
                    <div class="ec_datakeyword_title_block">
                        <h4>{l s='People Also Ask' mod='ec_seo'}<span class="badge">{count($infoKeywordData[$id_lang]['peopleAlsoAsk'])|escape:'htmlall':'UTF-8'}</span></h4>
                        <i class="{if $is17}material-icons{else}ec_icon16{/if} iclose">{if $is17}navigate_next{/if}</i>
                    </div>
                    <div class="ec_related_keywords_list" style="display:none;">
                        {foreach $infoKeywordData[$id_lang]['peopleAlsoAsk'] as $key => $peopleAlsoAsk}
                            <div class="ec_related_keyword">
                                <span class="el-tag">{$peopleAlsoAsk|escape:'htmlall':'UTF-8'}</span>
                            </div>
                        {/foreach}
                       
                    </div>
                </div>
                <div class="ec_related_keywords">
                    <div class="ec_datakeyword_title_block">
                        <h4>{l s='Associated researches' mod='ec_seo'}<span class="badge">{count($infoKeywordData[$id_lang]['relatedKeywords'])|escape:'htmlall':'UTF-8'}</span></h4>
                        <i class="{if $is17}material-icons{else}ec_icon16{/if} iclose">{if $is17}navigate_next{/if}</i>
                    </div>
                    <div class="ec_related_keywords_list" style="display:none;">
                        {foreach $infoKeywordData[$id_lang]['relatedKeywords'] as $key => $keyword_related}
                            <div class="ec_related_keyword">
                                <span class="el-tag">{$keyword_related|escape:'htmlall':'UTF-8'}</span>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
        
    {/foreach}
{/if}

<style>
    .ecseo_saveDesc {
        font-style: normal;
        color: orange;
        text-decoration: underline;
    }
</style>