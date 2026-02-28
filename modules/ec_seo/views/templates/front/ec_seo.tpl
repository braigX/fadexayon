{if $EC_SEO_OG}
    {if strlen($open_graph['og_title']) > 0}<meta property="og:title" content="{$open_graph['og_title']|escape:'htmlall':'UTF-8'}"/>{/if}
    {* <meta property="og:type" content="{$open_graph['og_type']|escape:'htmlall':'UTF-8'}"/> *}
    {if strlen($open_graph['og_description']) > 0}<meta property="og:description" content="{$open_graph['og_description']|escape:'htmlall':'UTF-8'}">{/if}
    {if strlen($open_graph['og_url']) > 0}<meta property="og:url" content="{$open_graph['og_url']|escape:'htmlall':'UTF-8'}"/>{/if}
    {if strlen($open_graph['og_locale']) > 0}<meta property="og:locale" content="{$open_graph['og_locale']|escape:'htmlall':'UTF-8'}"/>{/if}
    {if strlen($open_graph['og_site_name']) > 0}<meta property="og:site_name" content="{$open_graph['og_site_name']|escape:'htmlall':'UTF-8'}"/>{/if}
    {if strlen($open_graph['og_image']) > 0}<meta property="og:image" content="{$open_graph['og_image']|escape:'htmlall':'UTF-8'}"/>{/if}
{/if}
{if $type == 'cms' || $type == 'manufacturer' || $type == 'supplier' || $type == 'meta'}
    <link rel="canonical" href="{$ec_current_url|escape:'htmlall':'UTF-8'}">
{/if}
{if $show_seo}
{$score = 'red'}
{if $ec_tab_meta['score'] >= 75}
    {$score = 'green'}
{elseif $ec_tab_meta['score'] >= 50}
    {$score = 'yellow'}
{elseif $ec_tab_meta['score'] >= 25}
    {$score = 'orange'}
{/if}

<div id="front_seo" class="score-{$score|escape:'htmlall':'UTF-8'}">
    <div class="seo-mask-mobile"></div>
    <div class='seo-grp'><div class='seo-inner'>SEO<span class="score-btn">{$ec_tab_meta['score']|escape:'htmlall':'UTF-8'}<span class="score-btn-percent">%</span></span></div></div>
    <div class='pulsation'></div>
    <div id="seo-popup">
        <div class="seo-popup-inner">
            <div class="seo-onglets">
                <div id="seo-onglet1" class="seo-onglet active" data-toggle="seo-content1">{l s='Content' mod='ec_seo'}</div>
                <div id="seo-onglet2" class="seo-onglet" data-toggle="seo-content2">{l s='Technical' mod='ec_seo'}</div>
            </div>
            <div class="seo-popup-content">
                <div id="seo-content1" class="seo-content active">
                    <div class="seo-content-title">{l s='SEO' mod='ec_seo'} ({$ec_keyword|escape:'htmlall':'UTF-8'}) {l s='Score:' mod='ec_seo'} {$ec_tab_meta['score']|escape:'htmlall':'UTF-8'}</div>
                    <div class="seo-content-grp">
                        <div class="seo-label">{l s='Title tag' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">100</span></div>
                        <div class="seo-txt">
                            {if $ec_tab_meta['meta_title']['size'] == -1} 
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your Title tag is too short' mod='ec_seo'} ({$ec_tab_meta['meta_title']['len']|escape:'htmlall':'UTF-8'}). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['meta_title']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_title']['max']|escape:'htmlall':'UTF-8'} {l s='characters ! Give more details to your Title tag to make your prospects even more interested in coming to your site.' mod='ec_seo'} </div>
                            {else if ($ec_tab_meta['meta_title']['size'] == 2)}
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your Title tag is too long' mod='ec_seo'}({$ec_tab_meta['meta_title']['len']|escape:'htmlall':'UTF-8'}). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['meta_title']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_title']['max']|escape:'htmlall':'UTF-8'} {l s='characters !' mod='ec_seo'} </div>
                            {else}
                                <div class="seo-txt-item good"><span class="seo-icon good">👍</span>{l s='Your Title tag has a perfect size (between' mod='ec_seo'} {$category_rule['meta_title']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_title']['max']|escape:'htmlall':'UTF-8'} {l s='characters) !' mod='ec_seo'}</div>
                            {/if}

                            {if $ec_tab_meta['meta_title']['keyword']['res'] == 1}
                                <div class="seo-txt-item good"><span class="seo-icon good">👍</span>{l s='Your Title tag contains all the words of "' mod='ec_seo'} {$ec_keyword|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
                            {else}
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your Title tag does not contain all the words of "' mod='ec_seo'}{$ec_keyword|escape:'htmlall':'UTF-8'}{l s='". It can be improved! You should add the following keywords:' mod='ec_seo'}{$ec_tab_meta['meta_title']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}. </div>
                            {/if}
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                    <div class="seo-content-grp">
                        <div class="seo-label">{l s='Meta Description tag' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">3</span></div>
                        <div class="seo-txt">
                            {if $ec_tab_meta['meta_description']['size'] == -1} 
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your meta description is too short' mod='ec_seo'} ({$ec_tab_meta['meta_description']['len']|escape:'htmlall':'UTF-8'}). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['meta_description']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_description']['max']|escape:'htmlall':'UTF-8'} {l s='characters! Give more details to your meta description to make your prospects even more interested in coming to your site.' mod='ec_seo'} </div>
                            {else if ($ec_tab_meta['meta_description']['size'] == 2)}
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your meta description is too long' mod='ec_seo'}({$ec_tab_meta['meta_description']['len']|escape:'htmlall':'UTF-8'}). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['meta_description']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_description']['max']|escape:'htmlall':'UTF-8'} {l s='characters !' mod='ec_seo'} </div>
                            {else}
                                <div class="seo-txt-item good"><span class="seo-icon good">👍</span>{l s='Your tag has a perfect size (between' mod='ec_seo'} {$category_rule['meta_description']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['meta_description']['max']|escape:'htmlall':'UTF-8'} {l s='characters) !' mod='ec_seo'}</div>
                            {/if}

                            {if $ec_tab_meta['meta_description']['keyword']['res'] == 1}
                                <div class="seo-txt-item good"><span class="seo-icon good">👍</span>{l s='Your meta description contains all the words of "' mod='ec_seo'} {$ec_keyword|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
                            {else}
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your meta description tag does not contain all the words of "' mod='ec_seo'}{$ec_keyword|escape:'htmlall':'UTF-8'}{l s='". It can be improved! You should add the following keywords:' mod='ec_seo'}{$ec_tab_meta['meta_description']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}. </div>
                            {/if}
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                    <div class="seo-content-grp" {if $type == 'manufacturer' || $type == 'supplier'}style="display:none;"{/if}>
                        <div class="seo-label">{l s='URL' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">35</span></div>
                        <div class="seo-txt">
                            {if $ec_tab_meta['link_rewrite']['size'] == -1} 
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your url is too short' mod='ec_seo'} ({$ec_tab_meta['link_rewrite']['len']|escape:'htmlall':'UTF-8'}). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['link_rewrite']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['link_rewrite']['max']|escape:'htmlall':'UTF-8'} {l s='characters! Give more details to your url to make your prospects even more interested in coming to your site.' mod='ec_seo'} </div>
                            {else if ($ec_tab_meta['link_rewrite']['size'] == 2)}
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your url is too long' mod='ec_seo'}({$ec_tab_meta['link_rewrite']['len']|escape:'htmlall':'UTF-8'}). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['link_rewrite']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['link_rewrite']['max']|escape:'htmlall':'UTF-8'} {l s='characters !' mod='ec_seo'} </div>
                            {else}
                                <div class="seo-txt-item good"><span class="seo-icon good">👍</span>{l s='Your url has a perfect size (between' mod='ec_seo'} {$category_rule['link_rewrite']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['link_rewrite']['max']|escape:'htmlall':'UTF-8'} {l s='characters) !' mod='ec_seo'}</div>
                            {/if}

                            {if $ec_tab_meta['link_rewrite']['keyword']['res'] == 1}
                                <div class="seo-txt-item good"><span class="seo-icon good">👍</span>{l s='Your url contains all the words of "' mod='ec_seo'} {$ec_keyword|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
                            {else}
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your url does not contain all the words of "' mod='ec_seo'}{$ec_keyword|escape:'htmlall':'UTF-8'}{l s='". It can be improved! You should add the following keywords: ' mod='ec_seo'}{$ec_tab_meta['link_rewrite']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}. </div>
                            {/if}
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                    {if $type != 'meta'}
                    <div class="seo-content-grp" {if $type == 'manufacturer' || $type == 'supplier' || $type == 'meta' || ($type == 'cms' && !$ec_ps_version17)}style="display:none;"{/if}>
                        <div class="seo-label">{l s='H1 tag' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">73</span></div>
                        <div class="seo-txt">
                            {if $ec_tab_meta['h1']['size'] == -1} 
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your h1 tag is too short' mod='ec_seo'} ({$ec_tab_meta['h1']['len']|escape:'htmlall':'UTF-8'}). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['h1']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['h1']['max']|escape:'htmlall':'UTF-8'} {l s='characters! Give more details to your h1 tag to make your prospects even more interested in coming to your site.' mod='ec_seo'} </div>
                            {else if ($ec_tab_meta['h1']['size'] == 2)}
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your h1 tag is too long' mod='ec_seo'}({$ec_tab_meta['h1']['len']|escape:'htmlall':'UTF-8'}). {l s='It should ideally be between' mod='ec_seo'} {$category_rule['h1']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['h1']['max']|escape:'htmlall':'UTF-8'} {l s='characters !' mod='ec_seo'} </div>
                            {else}
                                <div class="seo-txt-item good"><span class="seo-icon good">👍</span>{l s='Your tag has a perfect size (between' mod='ec_seo'} {$category_rule['h1']['min']|escape:'htmlall':'UTF-8'} {l s='and' mod='ec_seo'} {$category_rule['h1']['max']|escape:'htmlall':'UTF-8'} {l s='characters) !' mod='ec_seo'}</div>
                            {/if}

                            {if $ec_tab_meta['h1']['keyword']['res'] == 1}
                                <div class="seo-txt-item good"><span class="seo-icon good">👍</span>{l s='Your h1 tag contains all the words of "' mod='ec_seo'} {$ec_keyword|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
                            {else}
                                <div class="seo-txt-item bad"><span class="seo-icon bad">🔧</span>{l s='Your h1 tag does not contain all the words of "' mod='ec_seo'}{$ec_keyword|escape:'htmlall':'UTF-8'}{l s='". It can be improved! You should add the following keywords:' mod='ec_seo'}{$ec_tab_meta['h1']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}. </div>
                            {/if}
                            <div class="seo-txt-item good baliseh1ok" style="display:none;"><span class="seo-icon good">👍</span>{l s='It is very good, the H1 tag exists and is unique!' mod='ec_seo'}</div>
                            <div class="seo-txt-item bad baliseh1mul" style="display:none;"><span class="seo-icon bad">🔧</span>{l s='The H1 tag is not unique.' mod='ec_seo'} (<span class="h1_nb"></span> : <span class="h1_content"></span>)</div>
                            <div class="seo-txt-item bad baliseh1ine" style="display:none;"><span class="seo-icon bad">🔧</span>{l s='The H1 tag is not present.' mod='ec_seo'}</div>
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                    {/if}
                    <div class="seo-content-grp">
                        <div class="seo-label">{l s='Description' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">73</span></div>
                        <div class="seo-txt">
                            <div class="seo-txt-item bad descriptionmin" {if $ec_tab_meta['desc_total']['count_word']  > 300 }style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your description must contain more than 300 words.' mod='ec_seo'}</div>
                            <div class="seo-txt-item good descriptionlenok"{if $ec_tab_meta['desc_total']['count_word']  < 300 }style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your description has a perfect size.' mod='ec_seo'}</div>

                            <div class="seo-txt-item good keyword" {if $ec_tab_meta['desc_total']['keyword']['res'] == 2}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='Your description tag contains all the words of "' mod='ec_seo'}{$ec_keyword|escape:'htmlall':'UTF-8'}{l s='", it is very good !' mod='ec_seo'} </div>
                            <div class="seo-txt-item bad keyword" {if $ec_tab_meta['desc_total']['keyword']['res'] == 1}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='Your description does not contain all the words of "' mod='ec_seo'}{$ec_keyword|escape:'htmlall':'UTF-8'}{l s='". It can be improved! You should add the following keywords (in the first 100 words of your description):' mod='ec_seo'}<span class="description_motneeded">{$ec_tab_meta['desc_total']['keyword']['mot_needed']|escape:'htmlall':'UTF-8'}.</span> </div>

                            <div class="seo-txt-item good keyword" {if $ec_tab_meta['desc_total']['h2'] == 0}style="display:none;"{/if}><span class="seo-icon good">👍</span>{l s='That\'s great, there is at least one h2 tag.' mod='ec_seo'}</div>
                            <div class="seo-txt-item bad keyword"{if $ec_tab_meta['desc_total']['h2'] == 1}style="display:none;"{/if}><span class="seo-icon bad">🔧</span>{l s='There is no h2 tag' mod='ec_seo'}</div>
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                    <div class="seo-content-grp">
                        <div class="seo-label">{l s='General structure' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">3</span></div>
                        <div class="seo-txt">
                            <table class="table-hh"><tbody><tr><th>H1</th><th>H2</th><th>H3</th><th>H4</th><th>H5</th><th>H6</th></tr><tr><td class="cpt_h1"></td><td class="cpt_h2"></td><td class="cpt_h3"></td><td class="cpt_h4"></td><td class="cpt_h5"></td><td class="cpt_h6"></td></tr></tbody></table>
                            <div class="seo-txt-item bad h2bad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='There is no h2 tag' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad h2bad2" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='There are more than 10 H2 elements. Please note, the H2 tags must remain representative of the page.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good h2good" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That\'s great, there is at least one H2 tag and there aren\'t too many either!' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad h3bad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The H3 tag is important, it helps to structure the page in the eyes of search engines. Try to use the H3 tag, ideally with the main keyword of your page.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good h3good" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That\'s great, there is at least one H3 tag and there aren\'t too many either!' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad strongbad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The Strong tag is important, it allows to indicate to the engines and the users the important content (in bold in the page). Try using the strong tag, ideally with the main keyword of your page.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good stronggood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That\'s great, there is the strong tag!' mod='ec_seo'}</span></div>
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                    <div class="seo-content-grp">
                        <div class="seo-label">{l s='Breadcrumb' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">3</span></div>
                        <div class="seo-txt">
                            <div class="seo-txt-item good breadgood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='The breadcrumb is present.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad breadbad" style="display:none;"><span class="seo-icon bad">👍</span><span class="txt">{l s='The breadcrumb is not present.' mod='ec_seo'}</span></div>
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                </div>
                <div id="seo-content2" class="seo-content">
                    {* <div class="seo-content-title">{l s='Ohohohoho !' mod='ec_seo'}</div> *}
                    <div class="seo-content-grp">
                        <div class="seo-label">{l s='Open Graph tag' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">3</span></div>
                        <div class="seo-txt">
                            <div class="seo-txt-item bad ogtitlebad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The og: title tag is not filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good ogtitlegood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That’s fine, the og: title tag is correctly filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad ogurlbad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The og: url tag is not filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good ogurlgood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That’s fine, the og: url tag is correctly filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad ogdescriptionbad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The og: description tag is not filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good ogdescriptiongood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That’s fine, the og: description tag is correctly filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad ogsite_namebad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The og: site_name tag is not filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good ogsite_namegood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='This is fine, the og: site_name tag is correctly filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad ogimagesbad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The og: images tag is not filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good ogimagesgood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That’s fine, the og: images tag is correctly filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad ogvideobad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The og: video tag is not filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good ogvideogood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That’s fine, the og: video tag is correctly filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad ogtypebad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The og: type tag is not filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good ogtypegood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That’s fine, the og: type tag is correctly filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item bad oglocalebad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='The og: locale tag is not filled.' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good oglocalegood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='That\'s fine, the og: locale tag is correctly filled.' mod='ec_seo'}</span></div>
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                    <div class="seo-content-grp">
                        <div class="seo-label">{l s='Images' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">3</span></div>
                        <div class="seo-txt">
                            <div class="seo-txt-item bad imagebad" style="display:none;"><span class="seo-icon bad">🔧</span><span class="txt">{l s='images have missing \'alt\' attribute (on' mod='ec_seo'}</span></div>
                            <div class="seo-txt-item good imagegood" style="display:none;"><span class="seo-icon good">👍</span><span class="txt">{l s='All images are optimized (' mod='ec_seo'}</span></div>
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                    <div class="seo-content-grp">
                        <div class="seo-label">{l s='Links' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">3</span></div>
                        <div class="seo-txt">
                            <div class="seo-txt-item good"><span class="txt">{l s='Internal links in the page' mod='ec_seo'}</span> (<span class="ec_lien_int"></span>)</div>
                            <div class="seo-txt-item good"><span class="txt">{l s='No follow links' mod='ec_seo'}</span> (<span class="ec_lien_nf"></span>)</div>
                            <div class="seo-txt-item good"><span class="txt">{l s='Duplicate links' mod='ec_seo'}</span> (<span class="ec_lien_db"></span>)</div>
                        </div>
                        <div class="seo-link" style="display:none;"><a href="#">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                    <div class="seo-content-grp">
                        <div class="seo-label">{l s='Rich snippet' mod='ec_seo'}</div>
                        <div class="seo-notation" style="display:none;"><span class="inner-notation">3</span></div>
                        <div class="seo-txt">
                            <a target="_blank" href="{$rich_snippet|escape:'htmlall':'UTF-8'}">{$rich_snippet|escape:'htmlall':'UTF-8'}</a>
                            {* <div class="seo-txt-item good"><span class="txt">{l s='Liens internes dans la page' mod='ec_seo'}</span> (<span class="ec_lien_int"></span>)</div>
                            <div class="seo-txt-item good"><span class="txt">{l s='Liens no follow' mod='ec_seo'}</span> (<span class="ec_lien_nf"></span>)</div>
                            <div class="seo-txt-item good"><span class="txt">{l s='Liens doublons' mod='ec_seo'}</span> (<span class="ec_lien_db"></span>)</div> *}
                        </div>
                        <div class="seo-link" style="display:none;"><a target="_blank" href="{$rich_snippet|escape:'htmlall':'UTF-8'}">{l s='See more' mod='ec_seo'}</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}