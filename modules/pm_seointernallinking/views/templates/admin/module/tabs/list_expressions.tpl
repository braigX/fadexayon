<div id="displayExpressionTable" rel="{$base_config_url|sil_nofilter}&getPanel=displayExpressionTable">
	{if !$groups|is_array}
		<p class="error">You must have at least one group in order to add an expression.</p>
	{else}
		{sil_button text={l s='Add a new expression' mod='pm_seointernallinking'} href="{$base_config_url|sil_nofilter}&getPanel=displayFormAddExpression" onclick=false icon_class='ui-icon ui-icon-circle-plus' class='open_on_dialog_iframe' title='' rel='800_500_1'}
			
		<table id="expressionTable" cellspacing="0" cellpadding="0" class="display"  style="width:100%;">
			<thead>
				<tr>
					<th width="50" style="text-align:center;">{l s='ID' mod='pm_seointernallinking'}</th>
					<th style="width:auto; text-align:center;">{l s='Expression' mod='pm_seointernallinking'}</th>
					<th style="text-align:center;">{l s='Language' mod='pm_seointernallinking'}</th>
					<th style="text-align:center;">{l s='Group' mod='pm_seointernallinking'}</th>
					<th style="text-align:center;">{l s='URL' mod='pm_seointernallinking'}</th>
					<th width="30" style="text-align:center;">{l s='No follow' mod='pm_seointernallinking'}</th>
					<th width="30" style="text-align:center;">{l s='Open in a new window' mod='pm_seointernallinking'}</th>
					<th width="30" style="text-align:center;">{l s='Active' mod='pm_seointernallinking'}</th>
					<th width="40" style="text-align:center;">{l s='Duplicate' mod='pm_seointernallinking'}</th>
					<th width="40" style="text-align:center;">{l s='Edit' mod='pm_seointernallinking'}</th>
					<th width="40" style="text-align:center;">{l s='Delete' mod='pm_seointernallinking'}</th>
				</tr>
			</thead>
			<tbody>
			{if $expressions|is_array && $expressions|count}
				{foreach from=$expressions item=expression}
					<tr>
						<td width="50">{$expression.id_expression|intval}</td>
						<td style="width:auto">{$expression.expression_content}</td>
						<td style="width:auto">{$languages_cache[$expression.id_lang]->name}</td>
						<td style="width:auto">{$expression.group_name}</td>
						<td style="text-align:center;"><a href="{$expression.associated_url}" title="{$expression.associated_url}" target="_blank" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-link"></span></a></td>
						<td style="text-align:center;">
							<span id="spanNoFollowExpression{$expression.id_expression|intval}" style="display:none">{$expression.nofollow}</span>
							<a href="{$base_config_url|sil_nofilter}&noFollowExpression=1&id_expression={$expression.id_expression|intval}" class="ajax_script_load">
								<img	title="{if $expression.nofollow}{l s='Enabled' mod='pm_seointernallinking'}{else}{l s='Disabled' mod='pm_seointernallinking'}{/if}"
										alt="{if $expression.nofollow}{l s='Enabled' mod='pm_seointernallinking'}{else}{l s='Disabled' mod='pm_seointernallinking'}{/if}"
										src="{$module_path}views/img/module_{if $expression.nofollow}install{else}disabled{/if}.png"
										id="imgNoFollowExpression{$expression.id_expression|intval}"/>
							</a>
						</td>
						<td style="text-align:center;">
							<span id="spanNewWindowExpression{$expression.id_expression|intval}" style="display:none">{$expression.new_window}</span>
							<a href="{$base_config_url|sil_nofilter}&newWindowExpression=1&id_expression={$expression.id_expression|intval}" class="ajax_script_load">
								<img	title="{if $expression.new_window}{l s='Enabled' mod='pm_seointernallinking'}{else}{l s='Disabled' mod='pm_seointernallinking'}{/if}"
										alt="{if $expression.new_window}{l s='Enabled' mod='pm_seointernallinking'}{else}{l s='Disabled' mod='pm_seointernallinking'}{/if}"
										src="{$module_path}views/img/module_{if $expression.new_window}install{else}disabled{/if}.png"
										id="imgNewWindowExpression{$expression.id_expression|intval}"/>
							</a>
						</td>
						<td style="text-align:center;">
							<span id="spanActiveExpression{$expression.id_expression|intval}" style="display:none">{$expression.active}</span>
							<a href="{$base_config_url|sil_nofilter}&activeExpression=1&id_expression={$expression.id_expression|intval}" class="ajax_script_load">
								<img	title="{if $expression.active}{l s='Active' mod='pm_seointernallinking'}{else}{l s='Inactive' mod='pm_seointernallinking'}{/if}"
										alt="{if $expression.active}{l s='Active' mod='pm_seointernallinking'}{else}{l s='Inactive' mod='pm_seointernallinking'}{/if}"
										src="{$module_path}views/img/module_{if $expression.active}install{else}disabled{/if}.png"
										id="imgActiveExpression{$expression.id_expression|intval}"/>
							</a>
						</td>
						<td style="text-align:center;">
							{sil_button text='' href="{$base_config_url|sil_nofilter}&getPanel=displayFormAddExpression&duplicate=1&id_expression={$expression.id_expression|intval}" onclick=false icon_class='ui-icon ui-icon-extlink' class='open_on_dialog_iframe' title='' rel='800_500_1'}
						</td>
						<td style="text-align:center;">
							{sil_button text='' href="{$base_config_url|sil_nofilter}&getPanel=displayFormAddExpression&alter=1&id_expression={$expression.id_expression|intval}" onclick=false icon_class='ui-icon ui-icon-pencil' class='open_on_dialog_iframe' title='' rel='800_500_1'}
						</td>
						<td style="text-align:center;">
							{sil_button text='' href="{$base_config_url|sil_nofilter}&deleteExpression=1&id_expression={$expression.id_expression|intval}" onclick=false icon_class='ui-icon ui-icon-trash' class='ajax_script_load pm_confirm' title={l s='Delete item # ?' mod='pm_seointernallinking'} rel=''}
						</td>
					</tr>
				{/foreach}
			{/if}
			</tbody>
		</table>

		{sil_initDataTable id="expressionTable"}
	{/if}
</div>