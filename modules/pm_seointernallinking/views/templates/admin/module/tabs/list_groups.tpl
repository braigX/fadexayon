<div id="displayGroupTable" rel="{$base_config_url|sil_nofilter}&getPanel=displayGroupTable">
	{sil_button text={l s='Create group' mod='pm_seointernallinking'} href="{$base_config_url|sil_nofilter}&getPanel=displayFormAddGroup" onclick=false icon_class='ui-icon ui-icon-circle-plus' class='open_on_dialog_iframe' title='' rel='800_500_1'}

	<table id="groupTable" cellspacing="0" cellpadding="0" class="display"  style="width:100%;">
		<thead>
			<tr>
		  		<th width="50" style="text-align:center;">{l s='ID' mod='pm_seointernallinking'}</th>
		  		<th style="width:auto; text-align:center;">{l s='Group name' mod='pm_seointernallinking'}</th>
		  		<th style="width:auto; text-align:center;">{l s='Group type' mod='pm_seointernallinking'}</th>
		  		<th style="width:auto; text-align:center;">{l s='Group description' mod='pm_seointernallinking'}</th>
		  		<th width="60" style="text-align:center;">{l s='Edit' mod='pm_seointernallinking'}</th>
		  		<th width="60" style="text-align:center;">{l s='Delete' mod='pm_seointernallinking'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$groups item=group}
				<tr>
					<td width="50">{$group.id_group|intval}</td>
					<td style="width:auto">{$group.name}</td>
					<td style="width:auto">{$group_type[$group.group_type]}</td>
					<td style="width:auto">{$group.groupCombinaisonInformations}</td>
					<td style="text-align:center;">
						{sil_button text='' href="{$base_config_url|sil_nofilter}&getPanel=displayFormAddGroup&alter=1&id_group={$group.id_group|intval}" onclick=false icon_class='ui-icon ui-icon-pencil' class='open_on_dialog_iframe' title='' rel='800_500_1'}
					</td>
					<td style="text-align:center;">
						{sil_button text='' href="{$base_config_url|sil_nofilter}&deleteGroup=1&id_group={$group.id_group|intval}" onclick=false icon_class='ui-icon ui-icon-trash' class='ajax_script_load pm_confirm' title={l s='Delete this group and all the associated expressions ?' mod='pm_seointernallinking'} rel='800_500_1'}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>

{sil_initDataTable id="groupTable"}