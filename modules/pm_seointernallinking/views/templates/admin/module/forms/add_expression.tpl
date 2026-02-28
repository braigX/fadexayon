{sil_startForm id="formAddExpression" obj=$obj}

	{if $updateForm}
		{module->_displayTitle text="{l s='Update an expression' mod='pm_seointernallinking'}"}
	{else}
		{if $isDuplication}
			{module->_displayTitle text="{l s='Duplicate an expression' mod='pm_seointernallinking'}"}
		{else}
			{module->_displayTitle text="{l s='Add a new Expression' mod='pm_seointernallinking'}"}
		{/if}
	{/if}

	{if $updateForm}
		<input type="hidden" name="id_expression" id="id_expression" value="{$obj->id_expression|intval}" />
	{/if}

	{sil_select obj=$obj key='id_lang' options=$options.languagesForSelectOptions label={l s='Language to be used' mod='pm_seointernallinking'} defaultvalue=false size='200px' selectedvalue=$default_language}
	{sil_select obj=$obj key='id_group' options=$options.groupsForSelectOptions label={l s='Group to be used' mod='pm_seointernallinking'} defaultvalue=false size='200px'}

	<div class="clear"></div><br />

	{sil_inputText obj=$obj key='expression_content' label={l s='Expression (one or several words)' mod='pm_seointernallinking'} size='200px' required=true}
	{sil_inputText obj=$obj key='associated_url' label={l s='URL associated with the expression' mod='pm_seointernallinking'} size='300px' required=true}
	{sil_inputText obj=$obj key='url_title' label={l s='URL Title (maximum 100 characters)' mod='pm_seointernallinking'} size='300px' required=true maxlength='100'}

	{sil_select obj=$obj key='link_position' options=$options.linkPositionForSelectOptions label={l s='Position of the link' mod='pm_seointernallinking'} defaultvalue=false size='200px'}

	<div class="clear"></div><br />

	{sil_inputActive obj=$obj key_active='nofollow' key_db='nofollow' label={l s='Add the "nofollow" attribute (search engines will not follow this link)' mod='pm_seointernallinking'}}
	{sil_inputActive obj=$obj key_active='new_window' key_db='new_window' label={l s='Open link in a new window/tab' mod='pm_seointernallinking'}}

	{sil_inputActive obj=$obj key_active='active_expression' key_db='active' label={l s='Activate expression' mod='pm_seointernallinking'}}

	{module->_displaySubmit text="{l s='Save' mod='pm_seointernallinking'}" name='submit_expression'}

{sil_endForm id="formAddExpression"}