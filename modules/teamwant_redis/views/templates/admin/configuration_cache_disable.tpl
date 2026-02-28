{**
 * Redis Cache
 * Version: 2.0.0
 * Copyright (c) 2020. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Teamwant
 * @package   Teamwant
 *}

<form id="module_form" class="defaultForm form-horizontal" action="{$cache_disable['url']}" method="post" enctype="multipart/form-data" novalidate="">
   <input type="hidden" name="{$cache_disable['action']}" value="1">
   <input type="hidden" name="token" value="{$cache_disable['token']}">
   <div class="panel" id="fieldset_0">
      <div class="panel-heading">
         <i class="icon-cogs"></i> {Teamwant_redis::staticModuleTranslate('Disable cache for modules')}
      </div>
      <div class="form-wrapper">
        <div class="row">
            <div class="col">
                <div class="alert alert-warning" role="alert">
                    <p class="alert-text">
                        {Teamwant_redis::staticModuleTranslate('If these settings are not enough, you can add code in the PHP function or method:')}
                        <code>define('_DISABLE_REDIS_', true);</code>
                        - OR - 
                        <code>$_REQUEST['_DISABLE_REDIS_'] = true;</code>
                    </p>
                </div>
            </div>
        </div>
        <div class="form-group">
           <label class="control-label col-lg-4">
               {Teamwant_redis::staticModuleTranslate('Ignored module controllers')}
           </label>
           <div class="col-lg-8">
               <input id="multiplecontrollertagpicker" type="text" name="twredis_ignoredmodulecontrollers" class="form-control" data-tags="true" data-token-separators="[',', ' ']" value="{$cache_disable['data']['twredis_ignoredmodulecontrollers']}">
                <p style=" font-weight: 900; font-size: 11px; ">{Teamwant_redis::staticModuleTranslate('Enter the controller class to be skipped, such as psgdprExportDataToCsvModuleFrontController, Ps_EmailAlertsAccountModuleFrontController, etc.')}</p>
           </div>
        </div>
        <div class="form-group">
           <label class="control-label col-lg-4">
               {Teamwant_redis::staticModuleTranslate('Ignored modules')}
           </label>
           <div class="col-lg-8">
               <input id="multiplecontrollertagpicker3" type="text" name="twredis_ignoredmodules" class="form-control" data-tags="true" data-token-separators="[',', ' ']" value="{$cache_disable['data']['twredis_ignoredmodules']}">
                <p style=" font-weight: 900; font-size: 11px; ">{Teamwant_redis::staticModuleTranslate('Enter the name of the module folder. While the js code is running from the selected module, the cache will be disabled')}</p>
           </div>
        </div>
      </div>
      <!-- /.form-wrapper -->
      <div class="panel-footer">
         <button type="submit" value="1" class="btn btn-default pull-right">
             <i class="process-icon-save"></i> {Teamwant_redis::staticModuleTranslate('Save')}
         </button>
      </div>
   </div>
</form>