<p>{l s='Last crontab usage' mod='pm_seointernallinking'} : <strong>{$cron_last_run}</strong></p>

<div class="conf pm_confirm">
	{l s='If you want to automatically optimize all the new or edited pages on your Prestashop installation, please ask your reseller to add this new cron task :' mod='pm_seointernallinking'}
	<br /><br /> {l s='Complete optimization :' mod='pm_seointernallinking'}
	<br />{$cron_url}
	<br /><br /> {l s='Optimization of the editorial module only :' mod='pm_seointernallinking'}
	<br />{$cron_url}&type=editorial
	<br /><br /> {l s='Optimization of the products pages only :' mod='pm_seointernallinking'}
	<br />{$cron_url}&type=products
	<br /><br /> {l s='Optimization of the CMS pages only :' mod='pm_seointernallinking'}
	<br />{$cron_url}&type=cms
	<br /><br /> {l s='Optimization of the categories pages only :' mod='pm_seointernallinking'}
	<br />{$cron_url}&type=categories
	<br /><br /> {l s='Optimization of the manufacturers pages only :' mod='pm_seointernallinking'}
	<br />{$cron_url}&type=manufacturers
</div>