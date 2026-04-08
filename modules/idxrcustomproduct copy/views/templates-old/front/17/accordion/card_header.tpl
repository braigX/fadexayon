{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<div class="card-header" role="tab" style="border-color: {$step.color|escape:'htmlall':'UTF-8'}; background-color: {$step.color|escape:'htmlall':'UTF-8'};">
    <div class="card-header-h5"> 
        <a class="d-block" data-toggle="collapse" data-parent="#component_steps_container" href="#step_title_{$step.id_component|intval}" >
            {if $step.icon_exist}
                <img class="step-img" src="{$modules_dir|escape:'htmlall':'UTF-8'}idxrcustomproduct/img/icon/{$step.id_component|intval}.png"/> 
            {/if}
            {$step.title|escape:'htmlall':'UTF-8'}<i class="material-icons float-lg-right float-md-right float-sm-right float-xs-right">expand_more</i>
        </a>
    </div>
    {*if $step.optional}<span class='idxrcustomproduct_optionaltag'>{l s='optional' mod='idxrcustomproduct'}</span>{/if*}
</div>