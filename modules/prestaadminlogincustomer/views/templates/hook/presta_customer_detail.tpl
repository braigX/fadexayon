{**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 *}

<div class="col">
    <div class="card">
        <h3 class="card-header">
            <i class="material-icons">lock_open</i>{l s='Customer Login' mod='prestaadminlogincustomer'}
        </h3>
        <div class="card-body">
            <a
                target="_blank"
                href="{$link->getAdminLink('AdminPrestaCustomer', true, [], ['id_customer' => $id_customer, 'logincustomer' => 1])|escape:'htmlall':'UTF-8'}"
                class="btn btn-primary">
                <i class="material-icons">lock_open</i> {l s='Login As' mod='prestaadminlogincustomer'} {$customer->firstname|escape:'htmlall':'UTF-8'} {$customer->lastname|escape:'htmlall':'UTF-8'}
            </a>
        </div>
    </div>
</div>
