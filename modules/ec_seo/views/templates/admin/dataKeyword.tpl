<div id="ec_datakeyword" class="panel">
    <h3>{l s='Data related to the keyword' mod='ec_seo'}</h3>
    {foreach $page_infos as $id_lang => $page_info}
        <div id="global_info_data_keywork_{$id_lang|escape:'htmlall':'UTF-8'}" class="global_info_data_keywork">
            <h2>{$page_info['keyword']|escape:'htmlall':'UTF-8'}</h2>
            <div id="ec_info_data_keyword_{$id_lang|escape:'htmlall':'UTF-8'}" class="ec_info_data_keyword">
                <div class="ec_data_keyword_block1">
                    {* <h4>{l s='NetLinking' mod='ec_seo'}</h4> *}
                    <div class="ec_netlinking">
                        <div>
                            {if isset($page_info['netlinking'])}
                            {$cpt = 1}
                                {foreach $page_info['netlinking'] as $pos => $url}
                                    <div class="ec_url_netlinking">
                                        <div class="ec_url_netlinking_score">{$cpt++|escape:'htmlall':'UTF-8'}</div>
                                        <div class="ec_url_netlinking_url">
                                            <a target="_blank" href="{$url|escape:'htmlall':'UTF-8'}">{$url|escape:'htmlall':'UTF-8'}</a>
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="ec_data_keyword_block2">
                    <div class="ec_recommended_keywords">
                        <div class="ec_datakeyword_title_block">
                            <h4>{l s='Recommended Keywords' mod='ec_seo'}</h4>
                            <i class="material-icons">navigate_next</i>
                        </div>
                        <div class="ec_recommended_keywords_list">
                            {if isset($page_info['netlinking'])}
                                {foreach $page_info['keywordsDetails'] as $key => $info_keyword}
                                    <div class="ec_reco_keyword">
                                        <span class="{if $info_keyword['currentOccurences']>=$info_keyword['minTargetOccurences']}el-tag el-tag--success{else}el-tag el-tag--info{/if}">
                                            {if $info_keyword['currentOccurences']>=$info_keyword['maxTargetOccurences']}<span style="color: red;">⚠</span>{/if}{$info_keyword['text']|escape:'htmlall':'UTF-8'}
                                        </span>
                                        <div class="popup_info_keyword" style="display:none">
                                            {l s='Nb Occurrences' mod='ec_seo'} : {$info_keyword['currentOccurences']|escape:'htmlall':'UTF-8'} (min {$info_keyword['minTargetOccurences']|escape:'htmlall':'UTF-8'}, max {$info_keyword['maxTargetOccurences']|escape:'htmlall':'UTF-8'})
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                    <div class="ec_related_keywords">
                        <div class="ec_datakeyword_title_block">
                            <h4>{l s='People Also Ask' mod='ec_seo'}</h4>
                            <i class="material-icons">navigate_next</i>
                        </div>
                        <div class="ec_related_keywords_list">
                            {if isset($page_info['netlinking'])}
                                {foreach $page_info['peopleAlsoAsk'] as $key => $peopleAlsoAsk}
                                    <div class="ec_related_keyword">
                                        <span class="el-tag">{$peopleAlsoAsk|escape:'htmlall':'UTF-8'}</span>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                    <div class="ec_related_keywords">
                        <div class="ec_datakeyword_title_block">
                            <h4>{l s='Associated researches' mod='ec_seo'}</h4>
                            <i class="material-icons">navigate_next</i>
                        </div>
                        <div class="ec_related_keywords_list">
                            {if isset($page_info['netlinking'])}
                                {foreach $page_info['relatedKeywords'] as $key => $keyword_related}
                                    <div class="ec_related_keyword">
                                        <span class="el-tag">{$keyword_related|escape:'htmlall':'UTF-8'}</span>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
</div>