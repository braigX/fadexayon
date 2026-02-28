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

<ul class="prestaadminlogincustomer">
	<li class="dropdown presta_header" id="presta_customer_header">
		<a data-toggle="dropdown" class="dropdown-toggle presta_login_customer" href="javascript:void(0);">
			<i class="icon-unlock"></i> {l s='Login Customer' mod='prestaadminlogincustomer'}
		</a>
		<div id="dropdown_presta_customer_header" class="dropdown-menu" style="max-height: 400px;overflow-y: auto;">
			<section class="panel">
				<div class="panel-header">
					<h3>{l s='Login as Customer' mod='prestaadminlogincustomer'}</h3>
					<div class="presta_loader_img prestahide">
						<img src="{$modules_dir|escape:'htmlall':'UTF-8'}prestaadminlogincustomer/views/img/ajax-loader.gif">
					</div>
				</div>
				<div class="presta_result">
					<input id="presta_customer_search" type="text" placeholder="Search for a customer" class="form-control">
				</div>
				<div id="presta_searched_result">
					{if isset($presta_log) && $presta_log}
						{foreach $presta_log as $log}
							<li class="clearfix">
                                <span>(#{$log.id_customer|escape:'htmlall':'UTF-8'}) {$log.cust_name|escape:'htmlall':'UTF-8'}</span>
                                <span style="margin-left:10px;" class="icon-envelope-o">
                                    <a
                                        href="{$presta_cust|escape:'htmlall':'UTF-8'}&id_customer={$log.id_customer|escape:'htmlall':'UTF-8'}&logincustomer=1"
                                        target="_blank"
                                        class="btn btn-primary"
                                        value="{$log.id_customer|escape:'htmlall':'UTF-8'}">{$log.cust_email|escape:'htmlall':'UTF-8'}
                                    </a>
                                </span>
                            </li>
						{/foreach}
					{/if}
				</div>
			</section>
		</div>
	</li>
</ul>
