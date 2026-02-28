{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

<div class='sel_min_img_block'>
    <label class="step_title" id="step_title_{$step.id_component|intval}"><img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png"/> {$step.title|escape:'htmlall':'UTF-8'}</label>    
    <select class="minified_sel_img" id="option_{$step.id_component|intval}" data-type="{$step.type|escape:'htmlall':'UTF-8'}">
        {foreach from=$step.options->options item=option}
            <option
                id = "option_{$step.id_component|intval}_{$option->id|intval}"
                data-price-impact='{$option->price_impact|escape:'htmlall':'UTF-8'}' 
                data-weight-impact='{$option->weight_impact|escape:'htmlall':'UTF-8'}' 
                data-reference='{$option->reference|escape:'htmlall':'UTF-8'}'
                value = "{$option->id|intval}"
                data-content='<img id="option_{$step.id_component|intval}_{$option->id|intval}_img" src="{$opcion_img_dir|escape:'htmlall':'UTF-8'}{$step.id_component|intval}_{$option->id|intval}.{$option->img_ext|escape:'htmlall':'UTF-8'}" alt="Thumbnail Alt"> <span id="option_name_{$step.id_component|intval}_{$option->id|intval}">{$option->name|escape:'htmlall':'UTF-8'}{if $option->description}</span> ({$option->description|escape:'htmlall':'UTF-8'}){/if}'
            >
            </option>                   
        {/foreach}
    </select>
</div>