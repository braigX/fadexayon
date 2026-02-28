{*
* @module       Recherche dynamique avancée (AmbJoliSearch)
* @file         ambjolisearch.tpl
* @subject      template pour champ recherche en haut de page sur le 'front office'
* @copyright    Copyright (c) 2013-2023 Ambris Informatique SARL (http://www.ambris.com/)
* @author       Richard Stefan (@RicoStefan)
* @license      Licensed under the EUPL-1.2-or-later
* Support by mail: support@ambris.com
*}
<div id="jolisearch_mobile_modal" style="display:none;" class="jolisearch-modal jolisearch-modal--mobile">
	<div id="jolisearch_mobile_header" class="jolisearch-modal__header">
		<span class="h1">{l s='Search' d="Shop.Theme.Catalog"}</span>
		<button type="button" class="jolisearch-modal__close close" data-dismiss="modal">&times;</button>

		<div id="jolisearch_mobile_form" class="jolisearch-modal__searchbox jolisearch-widget search-widget" data-search-controller-url="{$search_controller_url}">
			<form method="get" action="{$search_controller_url}" class="jolisearch-widget__form">
				<input type="hidden" name="controller" value="search">
				<input type="text" name="s" value="{$search_string}" class="jolisearch-widget__input" placeholder="{l s='Search our catalog' d='Shop.Theme.Catalog'}" aria-label="{l s='Search' d='Shop.Theme.Catalog'}" data-custom-target="#jolisearch_mobile_results">
				<button type="submit" class="jolisearch-widget__submit">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="jolisearch-widget__icon"><!-- Font Awesome Pro 5.15.4 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Licensed under the EUPL-1.2-or-later) --><defs><style>.fa-secondary{literal}{opacity:.4}{/literal}</style></defs><path d="M208 80a128 128 0 1 1-90.51 37.49A127.15 127.15 0 0 1 208 80m0-80C93.12 0 0 93.12 0 208s93.12 208 208 208 208-93.12 208-208S322.88 0 208 0z" class="fa-secondary"/><path d="M504.9 476.7L476.6 505a23.9 23.9 0 0 1-33.9 0L343 405.3a24 24 0 0 1-7-17V372l36-36h16.3a24 24 0 0 1 17 7l99.7 99.7a24.11 24.11 0 0 1-.1 34z" class="fa-primary"/></svg>
				</button>
			</form>
		</div>
	</div>

	<div id="jolisearch_mobile_results" class="jolisearch-modal__content">

	</div>
</div>
