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
<div class="hidden" id="tag-generator-panel-captcha">
<form data-id="captcha" class="tag-generator-panel" action="">
<div class="control-box">
    <fieldset>
    <legend>{l s='Generate a form-tag for a CAPTCHA widget. For more details, see' mod='ets_contactform7'} <a href="{$link_doc|escape:'html':'UTF-8'}#captcha" target="_blank">{l s='image captcha' mod='ets_contactform7'}</a>.</legend>
    <table class="form-table">
        <tbody>
        	<tr>
        	   <th scope="row"><label for="tag-generator-panel-captcha">{l s='Name' mod='ets_contactform7'}</label></th>
        	   <td><input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-captcha" /></td>
        	</tr>
           	<tr>
            	<th scope="row">{l s='Theme' mod='ets_contactform7'}</th>
            	<td>
            		<fieldset>
            		
            		<label for="tag-generator-panel-captcha-theme-basic"><input type="radio" checked="checked" value="basic" id="tag-generator-panel-captcha-theme-light" class="option default" name="theme" />{l s='Basic' mod='ets_contactform7'}</label>
                    <br />
            		<label for="tag-generator-panel-captcha-theme-colorful"><input type="radio" value="colorful" id="tag-generator-panel-recaptcha-theme-colorful" class="option" name="theme" />{l s='Colorful' mod='ets_contactform7'}</label>
            		</fieldset>
            	</td>
           	</tr>
           	<tr>
            	<th scope="row"><label for="tag-generator-panel-captcha-id">{l s='Id attribute' mod='ets_contactform7'}</label></th>
            	<td><input type="text" id="tag-generator-panel-captcha-id" class="idvalue oneline option" name="id" /></td>
            	</tr>
            	<tr>
            	<th scope="row"><label for="tag-generator-panel-captcha-class">{l s='Class attribute' mod='ets_contactform7'}</label></th>
            	<td><input type="text" id="tag-generator-panel-captcha-class" class="classvalue oneline option" name="class" /></td>
           	</tr>
            </tbody>
        </table>
    </fieldset>
</div>
    <div class="insert-box">
    	<input type="text" onfocus="this.select()" readonly="readonly" class="tag code" name="captcha" />
    	<div class="submitbox">
    	<input type="button" value="{l s='Insert Tag' mod='ets_contactform7'}" class="button button-primary insert-tag" />
    	</div>
    </div>
    </form>
</div>