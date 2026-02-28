{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}

id="component_step_{$step.id_component}" 
class="col-lg-12 component_step {if $step.constraint && !$step.display}hidden{/if}"
data-constraints="{implode( "," , $step.constraint)}"
data-default="{$step.default_opt}"
data-optional="{$step.optional}"
data-multivalue="{$step.multivalue}"