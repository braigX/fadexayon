{*
* 2020 ExtraSolutions
*
* NOTICE OF LICENSE
*
* @author    ExtraSolutions
* @copyright 2019 ExtraSolutions
* @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

<div class="panel pb10">
  <div class="row">
    <div class="col-md-6">
      <div class="panel">
        <div class="panel-heading">
            {l s='GOOGLE TAXONOMY' mod='gmerchantfeedes'}
          <span class="step-info pull-right">{l s='Step 1' mod='gmerchantfeedes'}</span>
        </div>
        <div class="f-between taxonomy-lang-list">
          <ul class="bullet-list c-between">
              {foreach from=$languages item='lang'}
                <li>
                  <a href="{$currentIndex|escape:'htmlall':'UTF-8'}&taxonomyForm&language_id={$lang['id_lang']|escape:'htmlall':'UTF-8'}"><span
                            class="bullet {if isset($taxonomies[$lang['id_lang']]['taxonomy'])
                            && $taxonomies[$lang['id_lang']]['taxonomy']}l-green{/if}"></span>
                      {$lang['iso_code']|escape:'htmlall':'UTF-8'}
                    <span class="l-code-dec">
								({$lang['language_code']|escape:'htmlall':'UTF-8'})
							</span>
                  </a>
                </li>
              {/foreach}
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel">
        <div class="panel-heading">
            {l s='LINKING YOUR CATEGORIES WITH GOOGLE CATEGORIES' mod='gmerchantfeedes'}
          <span class="step-info pull-right">{l s='Step 2' mod='gmerchantfeedes'}</span>
        </div>

        <div class="f-between taxonomy-category-list">
          <ul class="bullet-list c-between">
              {foreach from=$languages item='lang'}
                <li>
                  <a href="{$currentIndex|escape:'htmlall':'UTF-8'}&taxonomyCategoryForm&language_id={$lang['id_lang']|escape:'htmlall':'UTF-8'}">
								<span class="bullet {if (isset($taxonomiesLight[$lang['id_lang']]['filling'])
                && $taxonomiesLight[$lang['id_lang']]['filling']==1)} l-yellow {elseif (isset($taxonomiesLight[$lang['id_lang']]['filling']) && $taxonomiesLight[$lang['id_lang']]['filling']==2)}l-green{/if}"></span>
                      {$lang['iso_code']|escape:'htmlall':'UTF-8'}
                    <span class="l-code-dec">
									({$lang['language_code']|escape:'htmlall':'UTF-8'})
								</span>
                  </a>
                </li>
              {/foreach}
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <ul class="bullet-list info">
        <li>
          <span class="bullet-success"></span> <span>{l s='Success' mod='gmerchantfeedes'}</span>
        </li>
        <li>
          <span class="bullet-not-completed"></span> <span>{l s='May work incrorrectly' mod='gmerchantfeedes'}</span>
        </li>
        <li>
          <span class="bullet-error"></span> <span>{l s='Need your action' mod='gmerchantfeedes'}</span>
        </li>
      </ul>
    </div>
  </div>
</div>

<div class="alert alert-info">
    {l s='You can skip Steps 1 and 2; just set "Export products without reference to taxonomy" to Yes or Enabled in the feed settings' mod='gmerchantfeedes'}
</div>
