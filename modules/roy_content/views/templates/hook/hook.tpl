{if isset($htmlitems) && $htmlitems}
<div id="roycontent_{$hook|escape:'htmlall':'UTF-8'}" class="roycontent">
	<ul class="clearfix row">
		{foreach name=items from=$htmlitems item=hItem}
			{if $hook == 'left' || $hook == 'right'}
                <li class="htmlcontent-item-{$smarty.foreach.items.iteration|escape:'htmlall':'UTF-8'} col-xs-12 bview bview-first{if $hItem.hover} hover{/if}{if $hItem.fit && $hItem.fit == 1} fit_mobile{/if}{if $hItem.hide && $hItem.hide == 1} hide_mobile{/if}{if isset($hItem.hides) && $hItem.hides == 1} hide_desktop{/if}">
                    <div class="li-cont">
			{else}
				<li class="htmlcontent-item-{$smarty.foreach.items.iteration|escape:'htmlall':'UTF-8'} col-md-{if $hItem.cols}{$hItem.cols}{/if} bview bview-first{if $hItem.hover} hover{/if}{if $hItem.fit && $hItem.fit == 1} fit_mobile{/if}{if $hItem.hide && $hItem.hide == 1} hide_mobile{/if}{if isset($hItem.hides) && $hItem.hides == 1} hide_desktop{/if}">
                    <div class="li-cont">
			{/if}

			{if $hItem.image}
				{if $hItem.url}
					<a href="{$hItem.url|escape:'htmlall':'UTF-8'}" class="item-link"{if $hItem.target == 1} onclick="return !window.open(this.href);"{/if} title="{$hItem.title|escape:'htmlall':'UTF-8'}">
				{/if}
					{if $hItem.title && $hItem.title_use}
						<h2 class="item-title-top {if $hItem.url}href{/if} {if $hItem.fit && $hItem.fit == 1}fit_mobile{/if}">{$hItem.title|escape:'htmlall':'UTF-8'}</h2>
					{/if}
					{if $hItem.fit && $hItem.fit == 1}
						<div class="fit_content">
	              <h2 class="item-title">{$hItem.title|escape:'htmlall':'UTF-8'}</h2>
	              <p>
	                {if $hItem.html}
	                    {$hItem.html nofilter}
	                {/if}
	              </p>
	          </div>
					{/if}

					<img src="{$link->getMediaLink("`$module_dir`img/`$hItem.image`")}" class="item-img {if $hook == 'left' || $hook == 'right'}img-responsive{/if} {if $hItem.url}href{/if}" title="{$hItem.title|escape:'htmlall':'UTF-8'}" alt="{$hItem.title|escape:'htmlall':'UTF-8'}" width="{if $hItem.image_w}{$hItem.image_w|intval}{else}100%{/if}" height="{if $hItem.image_h}{$hItem.image_h|intval}{else}100%{/if}"/>

					{if $hItem.hover == 1}
            <div class="mask" {if $hItem.fit && $hItem.fit == 1}hide_mobile{/if} {if $hItem.bg_color}style="background:{$hItem.bg_color}"{/if}>
              <div class="content">
                <h2 class="item-title" {if $hItem.title_color}style="color:{$hItem.title_color}"{/if}>{$hItem.title|escape:'htmlall':'UTF-8'}</h2>
                <p>
                {if $hItem.html}
                    {$hItem.html nofilter}
                {/if}
                </p>
              </div>
            </div>
          {/if}

					{if $hItem.url}
						</a>
					{/if}

				{else}
				<div class="html_inside {if $hItem.video}video_banner{/if}">
					{if $hItem.html}
						{$hItem.html nofilter}
					{/if}
				</div>
				{/if}

				</div>
			</li>
		{/foreach}
	</ul>
</div>
{/if}
