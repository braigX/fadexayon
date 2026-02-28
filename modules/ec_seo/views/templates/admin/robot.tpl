<div id="robot_form">
<input type="hidden" id="mess_url_blocked" value="{l s='Url blocked by robot' mod='ec_seo'}">
<input type="hidden" id="mess_url_not_blocked" value="{l s='Url not blocked by robot' mod='ec_seo'}">
<input type="hidden" id="mess_enterurl" value="{l s='You must enter an url.' mod='ec_seo'}">
    <div id="s_ec_domains" class="panel">
        <h3>{l s='Domains' mod='ec_seo'}</h3>
        <div class="form-group">
            <label for="ec_domains">{l s='Choose the domain' mod='ec_seo'}</label>
            <select class="form-control" id="ec_domains">
                {foreach $domains as $domain}
                    <option value="{$domain['id_shop']|escape:'htmlall':'UTF-8'}">{$domain['domain']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div id="testrobot" class="panel">
        <h3>{l s='Test robot.txt' mod='ec_seo'}</h3>
        <div class="form-group">
            <label for="urlrobot">{l s='Enter url' mod='ec_seo'}</label>
            <input type="text" class="form-control" id="urlrobot">
            <a class="btn btn-default" href="#">{l s='Test' mod='ec_seo'}</a>
        </div>
    </div>
    {$robotform nofilter}
</div>