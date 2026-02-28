{**
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2022 Innova Deluxe SL
* @license   INNOVADELUXE
*}

<tr id="resume_tr_{$step.id_component}" class="{if $step.constraint}hidden{/if}">
    <td>{$step.title|escape:'htmlall':'UTF-8'}</td>
    <td colspan="2">
        <table class="resume_option_line_table">
            <span class='hidden sel_opt' data-required="{if $step.constraint || $step.optional}false{else}true{/if}" data-optional="{if $step.optional}true{else}false{/if}" id='js_opt_{$step.id_component}_value'>false</span>
            <span class='hidden sel_opt_wqty' id='js_opt_{$step.id_component}_value_wqty'>false</span>
            <span class='hidden sel_opt_extra' id='js_opt_extra_{$step.id_component}_value'>false</span>    
            <input autocomplete="off" type="hidden" id='js_resume_opt_{$step.id_component}_price_wodiscount' value="0" class='js_resume_price_wodiscount'>
            <input autocomplete="off" type="hidden" id='js_resume_opt_{$step.id_component}_price' value="0" class='js_resume_price'>        
            <tr class="resume_price_block" data-option="template">
              <td>
                <span class='option_title opt_type_{$step.type|escape:'htmlall':'UTF-8'}'>{l s='Not selected' mod='idxrcustomproduct'}  
                {*if $step.optional}
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle icp_optional_info_icon" title="{l s='Optional, not necessary to be selected for personalisation.' mod='idxrcustomproduct'}" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                </svg>
                {/if*}
                </span>
              </td>
              <td>
                <span class="pull-right {if !$steps.show_increment}hidden{/if}">
                    <div>
                        <span id='js_resume_opt_{$step.id_component}_price_wodiscount_formated' class="idxrcp_resume_opt_price_wodiscount"></span>
                        <span id='js_resume_opt_{$step.id_component}_price_formated' class="idxrcp_resume_opt_price"></span>
                    </div>
                </span>
              </td>
            </tr>
        </table>
    </td>
</tr>