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
<div class="hidden" id="tag-generator-panel-file">
    <form data-id="file" class="tag-generator-panel" action="">
        <div class="control-box">
        <fieldset>
        <legend>{l s='Generate a form-tag for a file uploading field. For more details, see' mod='ets_contactform7'} <a href="{$link_doc|escape:'html':'UTF-8'}#file-upload" target="_blank">{l s='File Uploading and Attachment' mod='ets_contactform7'}</a>.</legend>
            <table class="form-table">
            <tbody>
            	<tr>
            	<th scope="row">{l s='Field type' mod='ets_contactform7'}</th>
            	<td>
            		<fieldset>            		
            		<label class="cursor_pointer"><input type="checkbox" name="required" /> {l s='Required field' mod='ets_contactform7'}</label>
            		</fieldset>
            	</td>
            	</tr>
            	<tr>
            	<th scope="row"><label for="tag-generator-panel-file-name">{l s='Name' mod='ets_contactform7'}</label></th>
            	<td><input type="text" id="tag-generator-panel-file-name" class="tg-name oneline" name="name" /></td>
            	</tr>
            	<tr>
            	<th scope="row"><label for="tag-generator-panel-file-limit">{l s='File size limit (bytes)' mod='ets_contactform7'}</label></th>
            	<td><input type="text" id="tag-generator-panel-file-limit" class="filesize oneline option" name="limit" /></td>
            	</tr>
            	<tr>
            	<th scope="row"><label for="tag-generator-panel-file-filetypes">{l s='Acceptable file types' mod='ets_contactform7'}</label></th>
            	<td><input type="text" id="tag-generator-panel-file-filetypes" class="filetype oneline option" name="filetypes" />
                <br />
                    <span class="help-block">{l s='Eg: gif|png|jpg|jpeg' mod='ets_contactform7'}</span>
                </td>
            	</tr>
            	<tr>
            	<th scope="row"><label for="tag-generator-panel-file-id">{l s='Id attribute' mod='ets_contactform7'}</label></th>
            	<td><input type="text" id="tag-generator-panel-file-id" class="idvalue oneline option" name="id" /></td>
            	</tr>
            	<tr>
            	<th scope="row"><label for="tag-generator-panel-file-class">{l s='Class attribute' mod='ets_contactform7'}</label></th>
            	<td><input type="text" id="tag-generator-panel-file-class" class="classvalue oneline option" name="class" /></td>
            	</tr>
            </tbody>
            </table>
        </fieldset>
        </div>
    <div class="insert-box">
    	<input type="text" onfocus="this.select()" readonly="readonly" class="tag code" name="file" />
    	<div class="submitbox">
    	   <input type="button" value="Insert Tag" class="button button-primary insert-tag" />
    	</div>
    	<br class="clear" />
    	<p class="description mail-tag"><label for="tag-generator-panel-file-mailtag">{l s='To attach the file uploaded through this field to mail, you need to insert the corresponding mail-tag' mod='ets_contactform7'} (<strong><span class="mail-tag"></span></strong>) {l s='into the File Attachments field on the Mail tab' mod='ets_contactform7'}.<input type="text" id="tag-generator-panel-file-mailtag" readonly="readonly" class="mail-tag code hidden" /></label></p>
    </div>
    </form>
</div>