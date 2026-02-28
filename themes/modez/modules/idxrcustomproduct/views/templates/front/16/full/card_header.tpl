{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<h3 class="step_title" id="step_title_{$step.id_component|intval}">
    {if $step.icon_exist}
        <img src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png" height="30" width='30' />
    {/if}
    {$step.title|escape:'htmlall':'UTF-8'}
    
</h3>