{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<div class="hidden" id="tag-generator-panel-recaptcha">
    {if Configuration::get('ETS_CFT7_SITE_KEY') && Configuration::get('ETS_CFT7_SECRET_KEY')}
        <form data-id="recaptcha" class="tag-generator-panel" action="">
            <div class="control-box">
                <fieldset>
                <legend>{l s='Generate a form-tag for a reCAPTCHA widget. For more details, see' mod='ets_contactform7'} <a href="{$link_doc|escape:'html':'UTF-8'}#recaptcha" target="_blank">{l s='reCAPTCHA' mod='ets_contactform7'}</a>.</legend>
                <table class="form-table">
                    <tbody>
                    	<tr>
                        	<th scope="row">{l s='Size' mod='ets_contactform7'}</th>
                        	<td>
                        		<fieldset>
                        		
                        		<label for="tag-generator-panel-recaptcha-size-normal"><input type="radio" checked="checked" value="normal" id="tag-generator-panel-recaptcha-size-normal" class="option default" name="size" /> {l s='Normal' mod='ets_contactform7'}</label>
                        		<br/>
                        		<label for="tag-generator-panel-recaptcha-size-compact"><input type="radio" value="compact" id="tag-generator-panel-recaptcha-size-compact" class="option" name="size" /> {l s='Compact' mod='ets_contactform7'}</label>
                        		</fieldset>
                        	</td>
                    	</tr>
                       	<tr>
                        	<th scope="row">{l s='Theme' mod='ets_contactform7'}</th>
                        	<td>
                        		<fieldset>
                        		
                        		<label for="tag-generator-panel-recaptcha-theme-light"><input type="radio" checked="checked" value="light" id="tag-generator-panel-recaptcha-theme-light" class="option default" name="theme" /> {l s='Light' mod='ets_contactform7'}</label>
                        		<br />
                        		<label for="tag-generator-panel-recaptcha-theme-dark"><input type="radio" value="dark" id="tag-generator-panel-recaptcha-theme-dark" class="option" name="theme" />{l s='Dark' mod='ets_contactform7'}</label>
                        		</fieldset>
                        	</td>
                       	</tr>
                       	<tr>
                        	<th scope="row"><label for="tag-generator-panel-recaptcha-id">{l s='Id attribute' mod='ets_contactform7'}</label></th>
                        	<td><input type="text" id="tag-generator-panel-recaptcha-id" class="idvalue oneline option" name="id" /></td>
                        	</tr>
                        	<tr>
                        	<th scope="row"><label for="tag-generator-panel-recaptcha-class">{l s='Class attribute' mod='ets_contactform7'}</label></th>
                        	<td><input type="text" id="tag-generator-panel-recaptcha-class" class="classvalue oneline option" name="class" /></td>
                       	</tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div class="insert-box">
            	<input type="text" onfocus="this.select()" readonly="readonly" class="tag code" name="recaptcha" />
            	<div class="submitbox">
            	<input type="button" value="{l s='Insert Tag' mod='ets_contactform7'}" class="button button-primary insert-tag" />
            	</div>
            </div>
        </form>
    {else}
        <p>{l s='config google captcha' mod='ets_contactform7'} <a href="{$link->getAdminLink('AdminContactFormIntegration',true)|escape:'html':'UTF-8'}">{l s='click here' mod='ets_contactform7'}</a></p>
    {/if}
</div>
<div class="hidden" id="tag-generator-panel-recaptcha_v3">
    {if Configuration::get('ETS_CTF7_SITE_KEY_V3') && Configuration::get('ETS_CTF7_SECRET_KEY_V3')}
        <form data-id="recaptcha_v3" class="tag-generator-panel" action="">
            <div class="control-box">
                <fieldset>
                <legend>{l s='Generate a form-tag for a reCAPTCHA widget. For more details, see' mod='ets_contactform7'} <a href="{$link_doc|escape:'html':'UTF-8'}" target="_blank">{l s='reCAPTCHA' mod='ets_contactform7'}</a>.</legend>
                <table class="form-table">
                    <tbody>
                       	<tr>
                        	<th scope="row"><label for="tag-generator-panel-recaptcha-id">{l s='Id attribute' mod='ets_contactform7'}</label></th>
                        	<td><input type="text" id="tag-generator-panel-recaptcha-id" class="idvalue oneline option" name="id" /></td>
                       	</tr>
                       	<tr>
                        	<th scope="row"><label for="tag-generator-panel-recaptcha-class">{l s='Class attribute' mod='ets_contactform7'}</label></th>
                        	<td><input type="text" id="tag-generator-panel-recaptcha-class" class="classvalue oneline option" name="class" /></td>
                       	</tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div class="insert-box">
            	<input type="text" onfocus="this.select()" readonly="readonly" class="tag code" name="recaptcha" />
            	<div class="submitbox">
            	<input type="button" value="{l s='Insert Tag' mod='ets_contactform7'}" class="button button-primary insert-tag" />
            	</div>
            </div>
        </form>
    {else}
        <p>{l s='config google captcha' mod='ets_contactform7'} <a href="{$link->getAdminLink('AdminContactFormIntegration',true)|escape:'html':'UTF-8'}">{l s='click here' mod='ets_contactform7'}</a></p>
    {/if}
</div>