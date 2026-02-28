{** * Estimated Delivery - Front Office Feature
*
* NOTICE OF LICENSE
*
* @author    Pol Rué
* @copyright Smart Modules 2015
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @category Transport & Logistics
* Registered Trademark & Property of Smart-Modules.prpo
*
* ***************************************************
* *               Estimated Delivery                *
* *          http://www.smart-modules.com           *
* *                                                  *
* ***************************************************
*}

<div class="row">
    <form method="post"
          action="index.php?controller=AdminPickingList&token={$token|escape:'htmlall':'UTF-8'}#order"
          class="form-horizontal clearfix" id="form-order">
        {include file='./ed-admin-picking-column-selection.tpl'}
        {include file='./ed-order-states.tpl'}
    </form>
</div>