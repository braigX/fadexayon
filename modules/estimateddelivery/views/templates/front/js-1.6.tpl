{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @version 2.7.7
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 2.7.7                      *
 * ***************************************************
**}

{addJsDefL name=ed_hours}{l s='hours' mod='estimateddelivery' js=1}{/addJsDefL}
{addJsDefL name=ed_minutes}{l s='minutes' mod='estimateddelivery' js=1}{/addJsDefL}
{addJsDefL name=ed_and}{l s='and' mod='estimateddelivery' js=1}{/addJsDefL}
{addJsDefL name=ed_refresh}{l s='Picking time limit reached please refresh your browser to see your new estimated delivery.' mod='estimateddelivery' js=1}{/addJsDefL}
{addJsDef ed_has_combi = $ed_has_combi|intval}