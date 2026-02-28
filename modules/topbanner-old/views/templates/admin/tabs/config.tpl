{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}

<div class="form-group">
	<h3>
		<i class="icon-indent"></i> {l s='Configuration' mod='topbanner'}
	</h3>
	<div class="form-group">
		<a href="#newbanner" data-toggle="tab" class="btn btn-primary">{l s='New banner' mod='topbanner'}</a>
	</div>
	
	<table class="dataTable">
		<thead>
			<tr>
				<th>{l s='ID' mod='topbanner'}</th>
				<th>{l s='Name' mod='topbanner'}</th>
				<th>{l s='Banner type' mod='topbanner'}</th>
				<th>{l s='Promotion rule' mod='topbanner'}</th>
				<th>{l s='Status' mod='topbanner'}</th>
				<th>{l s='Actions' mod='topbanner'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$banners item='banner'}
				<tr>
					<td>{$banner['id_banner']|escape:'htmlall':'UTF-8'}</td>
					<td>{$banner['name']|escape:'htmlall':'UTF-8'}</td>
					<td>{$banner['type']|escape:'htmlall':'UTF-8'}</td>
					<td>{if isset($banner['cr_name'])}{$banner['cr_name']|escape:'htmlall':'UTF-8'}{if $banner['code'] != ''} - {$banner['code']|escape:'htmlall':'UTF-8'}{/if}{/if}</td>
					<td class="text-center">
                        <i title="{l s='Change state' mod='topbanner'}" class="icon-check font-green change-state {if $banner['status'] == 0}hidden{/if}" data-id="{$banner['id_banner']|escape:'htmlall':'UTF-8'}" data-state="0" aria-hidden="true"></i>
                        <i title="{l s='Change state' mod='topbanner'}" class="icon-times font-red change-state {if $banner['status'] == 1}hidden{/if}" data-id="{$banner['id_banner']|escape:'htmlall':'UTF-8'}" data-state="1" aria-hidden="true"></i>
                    </td>
					<td class="text-center">
						<a href="{$current_url|escape:'quotes':'UTF-8'}{* url *}&id_banner={$banner['id_banner']|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="icon-pencil" aria-hidden="true"></i></a>
						<a href="{$current_url|escape:'quotes':'UTF-8'}{* url *}&delete_id_banner={$banner['id_banner']|escape:'htmlall':'UTF-8'}" class="btn btn-default delete-banner"><i class="icon-trash" aria-hidden="true"></i></a>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>
        
<script>
    
    {literal}
        admin_module_ajax_url = '{/literal}{$controller_url_ajax|escape:'quotes':'UTF-8'}{* url *}{literal}';
        token = '{/literal}{$token_ajax|escape:'htmlall':'UTF-8'}{literal}';
    {/literal}
    
    $(document).on('click', '.change-state', function() {
        var $this = $(this);
        
        $.ajax({
            type: 'POST',
            url: admin_module_ajax_url,
            dataType: 'html',
            data: {
                action : 'ChangeState',
                ajax : true,
                token: token,
                id_banner: $this.attr('data-id'),
                state: $this.attr('data-state')
            },
            success: function(data){
                if (parseInt(data) === 1) {
                    if (parseInt($this.attr('data-state')) === 1) {
                        $('.change-state.font-green').addClass('hidden');
                        $('.change-state.font-red').removeClass('hidden');
                        
                        $this.parent().find('.font-red').addClass('hidden');
                        $this.parent().find('.font-green').removeClass('hidden');
                    } else {
                        $this.parent().find('.font-red').removeClass('hidden');
                        $this.parent().find('.font-green').addClass('hidden');
                    }
                }
            }
        });
    });
    
</script>