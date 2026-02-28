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

<form id="module_form" class="defaultForm form-horizontal" action="{$cache_blacklist['url']}" method="post" enctype="multipart/form-data" novalidate="">
   <input type="hidden" name="{$cache_blacklist['action']}" value="1">
   <input type="hidden" name="token" value="{$cache_blacklist['token']}">
   <div class="panel" id="fieldset_0">
      <div class="panel-heading">
         <i class="icon-cogs"></i> {Teamwant_redis::staticModuleTranslate('Disabled database tables')}
      </div>
      <div class="form-wrapper">
        <div class="row">
            <div class="col">
                <div class="alert alert-warning" role="alert">
                    <p class="alert-text">
                        {Teamwant_redis::staticModuleTranslate('If the word entered here occurs in your sql query, then this query will be skipped in the cache, for example, when you have a blog table such as ps_blog, then by entering the word ps_blog here, this table will be excluded from the cache.')}
                    </p>
                </div>
            </div>
        </div>
        <div class="form-group">
           <label class="control-label col-lg-4">
               {Teamwant_redis::staticModuleTranslate('Ignored sql code')}
           </label>
           <div class="col-lg-8">
               <input id="multiplecontrollertagpicker2" type="text" name="twredis_blacklist" class="form-control" data-tags="true" data-token-separators="[',', ' ']" value="{$cache_blacklist['data']['twredis_blacklist']}">
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