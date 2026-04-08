{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div class='sel_text_block'>
     <label class="step_title" id="step_title_{$step.id_component|intval}">
         {if $step.icon_exist}
         <img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png"/> 
         {/if}
         {$step.title|escape:'htmlall':'UTF-8'}
     </label>
     <div class="input-container" style="background: {$step.color}">
     <input class='minified_text form-control' data-type="text" id="option_{$step.id_component|intval}" placeholder="{l s='Insert text' mod='idxrcustomproduct'}"/>
     </div>
</div>
