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

<div class="alert alert-warning presta_login_header">
    {l s='You have logged in with' mod='prestaadminlogincustomer'}
        {$presta_customer->firstname|escape:'htmlall':'UTF-8'} {$presta_customer->lastname|escape:'htmlall':'UTF-8'}
    ({$presta_customer->email|escape:'htmlall':'UTF-8'})
</div>
