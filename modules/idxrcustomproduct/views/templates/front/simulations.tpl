{*
* 2007-2026 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2026 Innovadeluxe SL
* @license   INNOVADELUXE
*}

<h1>{l s='My simulations' mod='idxrcustomproduct'}</h1>

{if $simulations|@count}
  <style>
    h1 {
      font-size: 1.35rem;
    }
    table.table th,
    table.table td {
      font-size: 13px;
    }
    .idxr-sim-thumb-btn { border: 0; background: transparent; padding: 0; cursor: zoom-in; }
    .idxr-sim-thumb-svg { width: 70px; height: 70px; border: 1px solid #e6e9f5; border-radius: 6px; background: #fff; display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .idxr-sim-thumb-svg svg { width: 64px; height: 64px; }
    .idxr-sim-lightbox { position: fixed; inset: 0; background: rgba(20,25,45,.75); display: none; align-items: center; justify-content: center; z-index: 11000; padding: 20px; }
    .idxr-sim-lightbox.is-open { display: flex; }
    .idxr-sim-lightbox-content { max-width: 92vw; max-height: 92vh; background: #fff; border-radius: 10px; padding: 12px; }
    .idxr-sim-lightbox-content .idxr-sim-lightbox-svg { display:flex; align-items:center; justify-content:center; }
    .idxr-sim-lightbox-content .idxr-sim-lightbox-svg svg { max-width: 88vw; max-height: 86vh; width: 86vw; height: 84vh; display:block; }
  </style>
  <table class="table">
    <thead>
      <tr>
        <th>{l s='Preview' mod='idxrcustomproduct'}</th>
        <th>{l s='Name' mod='idxrcustomproduct'}</th>
        <th>{l s='Product' mod='idxrcustomproduct'}</th>
        <th>{l s='Saved on' mod='idxrcustomproduct'}</th>
        <th>{l s='Open product' mod='idxrcustomproduct'}</th>
        <th>{l s='Actions' mod='idxrcustomproduct'}</th>
      </tr>
    </thead>
    <tbody>
      {foreach from=$simulations item=simulation}
        <tr id="idxr-sim-row-{$simulation.id_saved_customisation|intval}">
          <td>
            {if $simulation.thumbnail_svg}
              <button type="button" class="idxr-sim-thumb-btn idxr-sim-thumb-open">
                <div class="idxr-sim-thumb-svg">{$simulation.thumbnail_svg nofilter}</div>
              </button>
            {else}
              <span>{l s='No preview' mod='idxrcustomproduct'}</span>
            {/if}
          </td>
          <td class="idxr-sim-name">{$simulation.customisation_name|escape:'htmlall':'UTF-8'}</td>
          <td>{$simulation.product_name|escape:'htmlall':'UTF-8'}</td>
          <td>{$simulation.date_add|escape:'htmlall':'UTF-8'}</td>
          <td><a class="btn btn-default" href="{$simulation.use_product_link|escape:'htmlall':'UTF-8'}">{l s='Apply' mod='idxrcustomproduct'}</a></td>
          <td>
            <button type="button" class="btn btn-default idxr-sim-rename" data-id="{$simulation.id_saved_customisation|intval}" data-name="{$simulation.customisation_name|escape:'htmlall':'UTF-8'}">{l s='Rename' mod='idxrcustomproduct'}</button>
            <button type="button" class="btn btn-default idxr-sim-duplicate" data-id="{$simulation.id_saved_customisation|intval}">{l s='Duplicate' mod='idxrcustomproduct'}</button>
            <button type="button" class="btn btn-default idxr-sim-delete" data-id="{$simulation.id_saved_customisation|intval}">{l s='Delete' mod='idxrcustomproduct'}</button>
          </td>
        </tr>
      {/foreach}
    </tbody>
  </table>
  <div id="idxr-sim-lightbox" class="idxr-sim-lightbox">
    <div class="idxr-sim-lightbox-content">
      <div id="idxr-sim-lightbox-svg" class="idxr-sim-lightbox-svg"></div>
    </div>
  </div>
{else}
  <p>{l s='No saved simulations yet.' mod='idxrcustomproduct'}</p>
{/if}
