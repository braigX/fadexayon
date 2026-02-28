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
<div id="tag-generator-panel-quiz" class="hidden">
    <form action="" class="tag-generator-panel" data-id="quiz"><div class="control-box">
        <fieldset>
        <legend>{l s='Generate a form-tag for a question-answer pair. For more details, see' mod='ets_contactform7'} <a href="{$link_doc|escape:'html':'UTF-8'}#quiz" target="_blank">{l s='quiz' mod='ets_contactform7'}</a>.</legend>
        
        <table class="form-table">
        <tbody>
        	<tr>
        	<th scope="row"><label for="tag-generator-panel-quiz-name">{l s='Name' mod='ets_contactform7'}</label></th>
        	<td><input type="text" name="name" class="tg-name oneline" id="tag-generator-panel-quiz-name" /></td>
        	</tr>
        
        	<tr>
        	<th scope="row">{l s='Questions and answers' mod='ets_contactform7'}</th>
        	<td>
        		<fieldset>        		
        		<textarea name="values" class="values" id="tag-generator-panel-quiz-values"></textarea><br />
        		<label for="tag-generator-panel-quiz-values"><span class="description">{l s='One pipe-separated question-answer pair (e.g. The capital of Brazil?|Rio)' mod='ets_contactform7'}.</span></label>
        		</fieldset>
        	</td>
        	</tr>
        
        	<tr>
        	<th scope="row"><label for="tag-generator-panel-quiz-id">{l s='Id attribute' mod='ets_contactform7'}</label></th>
        	<td><input type="text" name="id" class="idvalue oneline option" id="tag-generator-panel-quiz-id" /></td>
        	</tr>
        
        	<tr>
        	<th scope="row"><label for="tag-generator-panel-quiz-class">{l s='Class attribute' mod='ets_contactform7'}</label></th>
        	<td><input type="text" name="class" class="classvalue oneline option" id="tag-generator-panel-quiz-class" /></td>
        	</tr>
        </tbody>
        </table>
        </fieldset>
        </div>
        
        <div class="insert-box">
        	<input type="text" name="quiz" class="tag code" readonly="readonly" onfocus="this.select()" />
        
        	<div class="submitbox">
        	<input type="button" class="button button-primary insert-tag" value="{l s='Insert Tag' mod='ets_contactform7'}" />
            </div>            
        </div>
    </form>
</div>