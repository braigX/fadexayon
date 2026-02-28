{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<div class="row" id="dashboard">
    {if $latest_version}
        <div class="col-md-12">
            <div class="panel" style="font-size: 0.9rem;">
                <h3 class="panel-heading" style="color: #59c763">{if $avec_bootstrap}<i class="icon-star" style="color: #59c763"></i>{else}<img width="16" height="16" src="../img/admin/asterisk.gif" alt=""/>{/if} {l s='New version %s is now available!' mod='jprestaspeedpack' sprintf=[$latest_version['version']]}</h3>
                <p>{l s='Version %s has been published, check the changelogs to know what is new.' mod='jprestaspeedpack' sprintf=[$latest_version['version']]}</p>
                {if !$latest_version['upgrade_link']}
                    {if $pagecache_seller == 'addons'}
                        <p>
                            {l s='Publishing all versions to Addons is very time consuming so I created a free module to allow you to upgrade to the latest version very easily (one click). Find how it works and download it here:' mod='jprestaspeedpack'}
                            <a href="{$jpresta_shop_url|escape:'html':'UTF-8'}{l s='/en/prestashop-modules/20-jpresta-easy-upgrade.html' mod='jprestaspeedpack'}" target="_blank">{$jpresta_shop_url|escape:'html':'UTF-8'}{l s='/en/prestashop-modules/20-jpresta-easy-upgrade.html' mod='jprestaspeedpack'}</a>
                        </p>
                    {else}
                        <p>
                            {l s='To make your life easier I created a free module to allow you to upgrade to the latest version in one click. Find how it works and download it here:' mod='jprestaspeedpack'}
                            <a href="{$jpresta_shop_url|escape:'html':'UTF-8'}{l s='/en/prestashop-modules/20-jpresta-easy-upgrade.html' mod='jprestaspeedpack'}" target="_blank">{$jpresta_shop_url|escape:'html':'UTF-8'}{l s='/en/prestashop-modules/20-jpresta-easy-upgrade.html' mod='jprestaspeedpack'}</a>
                        </p>
                    {/if}
                {/if}
                <p>{l s='Using the latest version is always recommended because it may fix a bug or make the cache more efficient or even faster!' mod='jprestaspeedpack'}</p>
                {if $latest_version['upgrade_link']}
                    <a href="{$latest_version['upgrade_link']|escape:'html':'UTF-8'}" class="btn btn-primary">{l s='Upgrade with JPresta Easy Upgrade' mod='jprestaspeedpack'}...</a>
                {else}
                    <a href="{$jpresta_shop_url|escape:'html':'UTF-8'}{l s='/en/prestashop-modules/20-jpresta-easy-upgrade.html' mod='jprestaspeedpack'}" target="_blank" class="btn btn-primary">{l s='Download JPresta Easy Upgrade' mod='jprestaspeedpack'}...</a>
                {/if}
                {if $latest_version['changelogs']}
                    <a data-toggle="collapse" href="#changelogs" role="button" aria-expanded="false" aria-controls="changelogs" class="btn btn-default">{l s='See changelogs' mod='jprestaspeedpack'}</a>
                    <div id="changelogs" class="collapse changelogs" style="border: 1px solid #ccc; border-radius: 3px; padding: 0.5rem; margin-top: 0.5rem; max-height: 200px; overflow: auto;">
                        {foreach from=$latest_version['changelogs'] key=versionLogs item=logs}
                            {$versionLogs|escape:'html':'UTF-8'}
                            <ul>
                                {foreach from=$logs item=log}
                                    <li>{$log|escape:'html':'UTF-8'}</li>
                                {/foreach}
                            </ul>
                        {/foreach}
                    </div>
                {/if}
            </div>
        </div>
    {/if}
    <div class="{if $pagecache_debug}col-md-12{else}col-md-12 col-lg-6{/if}">
        <div class="panel">
            <h3 class="panel-heading">{if $avec_bootstrap}<i class="icon-wrench"></i>{else}<img width="16" height="16" src="../img/admin/prefs.gif" alt=""/>{/if} {l s='Installation' mod='jprestaspeedpack'}</h3>
            <form id="pagecache_form_install" action="{$request_uri|escape:'html':'UTF-8'}" method="post">
                <input type="hidden" name="submitModule" value="true"/>
                <input type="hidden" name="pctab" value="install"/>
                <input type="hidden" name="pagecache_disable_tokens" value="false" id="pagecache_disable_tokens"/>
                <fieldset>
                <div style="clear: both;">
                {if $pagecache_debug}

                    <input type="hidden" name="pagecache_install_step" id="pagecache_install_step" value="{$cur_step + 1|escape:'html':'UTF-8'}"/>
                    <input type="hidden" name="pagecache_disable_loggedin" id="pagecache_disable_loggedin" value="0"/>
                    <input type="hidden" name="pagecache_seller" id="pagecache_seller" value="{$pagecache_seller|escape:'html':'UTF-8'}"/>
                    <input type="hidden" name="pagecache_autoconf" id="pagecache_autoconf" value="false"/>

                    {if $cur_step > $INSTALL_STEP_INSTALL}
                        <div class="installstep">{l s='Congratulations!' mod='jprestaspeedpack'} {$module_displayName|escape:'html':'UTF-8'} {l s='is currently installed in' mod='jprestaspeedpack'} <b>{l s='test mode' mod='jprestaspeedpack'}</b>{l s=', that means it\'s not yet activated to your visitors.' mod='jprestaspeedpack'}</div>
                    {/if}

                    <div class="installstep">{l s='To complete the installation, please follow these steps:' mod='jprestaspeedpack'}

                        {* INSTALL STEP *}
                        <div class="step {if $cur_step > $INSTALL_STEP_INSTALL}stepok{elseif $cur_step < $INSTALL_STEP_INSTALL}steptodo{/if}">
                            {if $cur_step > $INSTALL_STEP_INSTALL}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="24" height="24" />
                            {elseif $cur_step < $INSTALL_STEP_INSTALL}
                               <span>{$INSTALL_STEP_INSTALL|escape:'html':'UTF-8'}</span>
                            {else}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/curstep.gif" alt="todo" width="24" height="24" />
                            {/if}
                            {l s='Install the module and enable test mode' mod='jprestaspeedpack'}
                            {if $cur_step == $INSTALL_STEP_INSTALL}
                            <div class="stepdesc"><ol><li>{l s='Resolve displayed errors above' mod='jprestaspeedpack'}</li></ol></div>
                            {/if}
                        </div>

                        {* BUY FROM STEP *}
                        <div class="step {if $cur_step > $INSTALL_STEP_BUY_FROM}stepok{elseif $cur_step < $INSTALL_STEP_BUY_FROM}steptodo{/if}">
                            {if $cur_step > $INSTALL_STEP_BUY_FROM}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="24" height="24" />
                            {elseif $cur_step < $INSTALL_STEP_BUY_FROM}
                               <span>{$INSTALL_STEP_BUY_FROM|escape:'html':'UTF-8'}</span>
                            {else}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/curstep.gif" alt="todo" width="24" height="24" />
                            {/if}
                            {l s='Tell us where did you buy the module' mod='jprestaspeedpack'}
                            {if $cur_step == $INSTALL_STEP_BUY_FROM}
                            <div class="stepdesc">
                                <ol>
                                    <li>{l s='In order to display correct links for support just tell us where you bought ' mod='jprestaspeedpack'}{$module_displayName|escape:'html':'UTF-8'}</li>
                                </ol>
                                <a href="#" class="okbtn" onclick="$('#pagecache_seller').val('addons');$('#pagecache_form_install').submit();return false;">{l s='Prestashop Addons' mod='jprestaspeedpack'}</a>
                                <a href="#" class="okbtn" onclick="$('#pagecache_seller').val('jpresta');$('#pagecache_form_install').submit();return false;">{l s='JPresta.com' mod='jprestaspeedpack'}</a>
                            </div>
                            {/if}
                        </div>

                        {* IN ACTION STEP *}
                        <div class="step {if $cur_step > $INSTALL_STEP_IN_ACTION}stepok{elseif $cur_step < $INSTALL_STEP_IN_ACTION}steptodo{/if}">
                            {if $cur_step > $INSTALL_STEP_IN_ACTION}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="24" height="24" />
                            {elseif $cur_step < $INSTALL_STEP_IN_ACTION}
                               <span>{$INSTALL_STEP_IN_ACTION|escape:'html':'UTF-8'}</span>
                            {else}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/curstep.gif" alt="todo" width="24" height="24" />
                            {/if}
                            {l s='Check that the module is well installed' mod='jprestaspeedpack'}
                            {if $cur_step == $INSTALL_STEP_IN_ACTION}
                            <div class="stepdesc">
                                <ol>
                                    <li><a href="{$shop_link_debug|escape:'html':'UTF-8'}" target="_blank">{l s='Click here to browse your site in test mode' mod='jprestaspeedpack'}</a></li>
                                    <li>{l s='You must see a box displayed in bottom left corner of your store' mod='jprestaspeedpack'}</li>
                                    <li>{l s='You must be able to play with these buttons' mod='jprestaspeedpack'} &nbsp;&nbsp;<img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/on.png" alt="" width="16" height="16" /><img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/reload.png" alt="" width="16" height="16" /><img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/trash.png" alt="" width="16" height="16" /><img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/close.png" alt="" width="16" height="16" /></li>
                                </ol>
                                <a href="#" class="okbtn" onclick="$('#pagecache_form_install').submit();return false;">{l s='OK, I validate this step' mod='jprestaspeedpack'}</a>
                                <a href="#" class="kobtn" onclick="$('#helpINSTALL_STEP_IN_ACTION').toggle();return false;">{l s='No, I\'m having trouble' mod='jprestaspeedpack'}</a>
                                <div class="stephelp" id="helpINSTALL_STEP_IN_ACTION">
                                    <ol>
                                        <li>{l s='Reset the module and see if it\'s better' mod='jprestaspeedpack'}</li>
                                        <li>{l s='If, after resetting the module, you are still having trouble,' mod='jprestaspeedpack'} <a href="{$contact_url|escape:'html':'UTF-8'}" target="_blank">{l s='contact us here' mod='jprestaspeedpack'}</a></li>
                                    </ol>
                                </div>
                            </div>
                            {/if}
                        </div>

                        {* AUTOCONF STEP *}
                        <div class="step {if $cur_step > $INSTALL_STEP_AUTOCONF}stepok{elseif $cur_step < $INSTALL_STEP_AUTOCONF}steptodo{/if}">
                            {if $cur_step > $INSTALL_STEP_AUTOCONF}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="24" height="24" />
                            {elseif $cur_step < $INSTALL_STEP_AUTOCONF}
                               <span>{$INSTALL_STEP_AUTOCONF|escape:'html':'UTF-8'}</span>
                            {else}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/curstep.gif" alt="todo" width="24" height="24" />
                            {/if}
                            {l s='Auto-configuration of known modules' mod='jprestaspeedpack'}
                            {if $cur_step == $INSTALL_STEP_AUTOCONF}
                            <div class="stepdesc">
                                <div class="alert alert-info">{l s='Contacts our server to automatically configures Page Cache Ultimate for all known modules like the shopping cart. You should rely on this auto-configuration and only do manual modifications of dynamic modules when something does not work as usual when the cache is enabled.' mod='jprestaspeedpack'}</div>
                                {if !empty($pagecache_cfgadvancedjs)}
                                    <div class="bootstrap">
                                        <div class="alert alert-warning" style="display: block;">&nbsp;{l s='Warning: if you perform the automatic configuration again, this will replace the current configuration of Page Cache' mod='jprestaspeedpack'}</div>
                                    </div>
                                    <button class="okbtn" onclick="if (confirm('{l s='Warning: if you perform the automatic configuration again, this will replace the current configuration of Page Cache' js='true' mod='jprestaspeedpack'}')){ $('#pagecache_autoconf').val('true');$('#pagecache_form_install').submit();$(this).prop('disabled', 'true');};return false;">{l s='Perform the automatic configuration again' mod='jprestaspeedpack'}</button>
                                    <a href="#" class="kobtn" onclick="$('#pagecache_autoconf').val('false');$('#pagecache_form_install').submit();return false;">{l s='Skip the automatic configuration' mod='jprestaspeedpack'}</a>
                                {else}
                                    <button class="okbtn" onclick="$('#pagecache_autoconf').val('true');$('#pagecache_form_install').submit();$(this).prop('disabled', 'true');return false;">{l s='Perform the automatic configuration' mod='jprestaspeedpack'}</button>
                                {/if}
                            </div>
                            {/if}
                        </div>

                        {* CART STEP *}
                        <div class="step {if $cur_step > $INSTALL_STEP_CART}stepok{elseif $cur_step < $INSTALL_STEP_CART}steptodo{/if}">
                            {if $cur_step > $INSTALL_STEP_CART}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="24" height="24" />
                            {elseif $cur_step < $INSTALL_STEP_CART}
                               <span>{$INSTALL_STEP_CART|escape:'html':'UTF-8'}</span>
                            {else}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/curstep.gif" alt="todo" width="24" height="24" />
                            {/if}
                            {l s='Check that the cart is working good' mod='jprestaspeedpack'}
                            {if $cur_step == $INSTALL_STEP_CART}
                            <div class="stepdesc">
                                <ol>
                                    <li><a href="{$shop_link_debug|escape:'html':'UTF-8'}" target="_blank">{l s='Click here to browse your site in test mode' mod='jprestaspeedpack'}</a></li>
                                    <li>{l s='Check that you can add products into the cart as usual' mod='jprestaspeedpack'}</li>
                                    <li>{l s='Once you have a product in your cart, display an other page and see if cart still contains the products you added' mod='jprestaspeedpack'}</li>
                                </ol>
                                <a href="#" class="okbtn" onclick="$('#pagecache_form_install').submit();return false;">{l s='OK, I validate this step' mod='jprestaspeedpack'}</a>
                                <a href="#" class="kobtn" onclick="$('#helpINSTALL_STEP_CART').toggle();return false;">{l s='No, I\'m having trouble' mod='jprestaspeedpack'}</a>
                                <div class="stephelp" id="helpINSTALL_STEP_CART">
                                    <ol>
                                        <li>{l s='When you display an other page, check that you have the parameter dbgpagecache=1 in the URL. If not, just add it.' mod='jprestaspeedpack'}</li>
                                        <li>{l s='When refreshing the cart, PageCache may remove some "mouse over" behaviours. To set them back you can execute some javascript after all dynamics modules have been displayed.' mod='jprestaspeedpack'} <a href="#tabdynhooksjs" onclick="displayTab('dynhooks');return true;">{l s='Go in "Dynamic modules" tab in Javascript form.' mod='jprestaspeedpack'}</a></li>
                                        <li>{l s='If you cannot make it work,' mod='jprestaspeedpack'} <a href="{$contact_url|escape:'html':'UTF-8'}" target="_blank">{l s='contact us here' mod='jprestaspeedpack'}</a></li>
                                    </ol>
                                </div>
                            </div>
                            {/if}
                        </div>

                        {* LOGGED_IN STEP *}
                        <div class="step {if $cur_step > $INSTALL_STEP_LOGGED_IN}stepok{elseif $cur_step < $INSTALL_STEP_LOGGED_IN}steptodo{/if}">
                            {if $cur_step > $INSTALL_STEP_LOGGED_IN}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="24" height="24" />
                            {elseif $cur_step < $INSTALL_STEP_LOGGED_IN}
                               <span>{$INSTALL_STEP_LOGGED_IN|escape:'html':'UTF-8'}</span>
                            {else}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/curstep.gif" alt="todo" width="24" height="24" />
                            {/if}
                            {l s='Check that logged in users are recognized' mod='jprestaspeedpack'}
                            {if $cur_step == $INSTALL_STEP_LOGGED_IN}
                            <div class="stepdesc">
                                <ol>
                                    {if $pagecache_skiplogged}
                                        {if $avec_bootstrap}
                                            <div class="bootstrap">
                                                <div class="alert alert-info" style="display: block;">&nbsp;{l s='Cache is disabled for logged in users so this step should be OK now, but you should check this out anyway ;-)' mod='jprestaspeedpack'}
                                                    <br/>{l s='If you want you can' mod='jprestaspeedpack'} <a href="#" class="browsebtn" onclick="$('#pagecache_disable_loggedin').val(-1);$('#pagecache_form_install').submit();return false;">{l s='reactivate cache for logged in users' mod='jprestaspeedpack'}</a>
                                                </div>
                                            </div>
                                        {else}
                                            <div class="hint clear" style="display: block;">&nbsp;{l s='Cache is disabled for logged in users so this step should be OK now, but you should check this out anyway ;-)' mod='jprestaspeedpack'}
                                                <br/>{l s='If you want you can' mod='jprestaspeedpack'} <a href="#" class="browsebtn" onclick="$('#pagecache_disable_loggedin').val(-1);$('#pagecache_form_install').submit();return false;">{l s='reactivate cache for logged in users' mod='jprestaspeedpack'}</a>
                                            </div>
                                        {/if}
                                    {/if}
                                    <li><a href="{$shop_link_debug|escape:'html':'UTF-8'}" target="_blank">{l s='Click here to browse your site in test mode' mod='jprestaspeedpack'}</a></li>
                                    <li>{l s='You must see the "sign in" link when you are not logged in' mod='jprestaspeedpack'}</li>
                                    <li>{l s='You must see the the user name when you are logged in' mod='jprestaspeedpack'}</li>
                                    <li>{l s='Of course it depends on your theme so just check that being logged in or not has the same behaviour with PageCache' mod='jprestaspeedpack'}</li>
                                </ol>
                                <a href="#" class="okbtn" onclick="$('#pagecache_form_install').submit();return false;">{l s='OK, I validate this step' mod='jprestaspeedpack'}</a>
                                <a href="#" class="kobtn" onclick="$('#helpINSTALL_STEP_LOGGED_IN').toggle();return false;">{l s='No, I\'m having trouble' mod='jprestaspeedpack'}</a>
                                <div class="stephelp" id="helpINSTALL_STEP_LOGGED_IN">
                                    {if !$pagecache_skiplogged}
                                        <ol>
                                            <li>{l s='Make sure that module displaying user informations or sign in links are set as "dynamic".' mod='jprestaspeedpack'}</li>
                                            <li>{l s='Your theme may be uncompatible with this feature, specially if these informations are "hard coded" in theme without using a module. In this case just disable PageCache for logged in users.' mod='jprestaspeedpack'}</li>
                                        </ol>
                                        <a href="#" class="browsebtn" onclick="$('#pagecache_disable_loggedin').val(1);$('#pagecache_form_install').submit();return false;">{l s='Disable cache for logged in users' mod='jprestaspeedpack'}</a>
                                    {else}
                                        <ol>
                                            <li>{l s='Still having problem? Then ' mod='jprestaspeedpack'} <a href="{$contact_url|escape:'html':'UTF-8'}" target="_blank">{l s='contact us here' mod='jprestaspeedpack'}</a></li>
                                        </ol>
                                    {/if}
                                </div>
                            </div>
                            {/if}
                        </div>

                        {* EU_COOKIE STEP *}
                        <div class="step {if $cur_step > $INSTALL_STEP_EU_COOKIE}stepok{elseif $cur_step < $INSTALL_STEP_EU_COOKIE}steptodo{/if}">
                            {if $cur_step > $INSTALL_STEP_EU_COOKIE}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="24" height="24" />
                            {elseif $cur_step < $INSTALL_STEP_EU_COOKIE}
                               <span>{$INSTALL_STEP_EU_COOKIE|escape:'html':'UTF-8'}</span>
                            {else}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/curstep.gif" alt="todo" width="24" height="24" />
                            {/if}
                            {l s='Check your european law module if any' mod='jprestaspeedpack'}
                            {if $cur_step == $INSTALL_STEP_EU_COOKIE}
                            <div class="stepdesc">
                                <ol>
                                    <li><a href="{$shop_link_debug|escape:'html':'UTF-8'}" target="_blank">{l s='Click here to browse your site in test mode' mod='jprestaspeedpack'}</a></li>
                                    <li>{l s='Remove your cookies, reset the cache, then display a page' mod='jprestaspeedpack'}</li>
                                    <li>{l s='You should see the cookie law message; click to hide it' mod='jprestaspeedpack'}</li>
                                    <li>{l s='Reload the page, you should not see the message again' mod='jprestaspeedpack'}</li>
                                </ol>
                                <a href="#" class="okbtn" onclick="$('#pagecache_form_install').submit();return false;">{l s='OK, I validate this step' mod='jprestaspeedpack'}</a>
                                <a href="#" class="kobtn" onclick="$('#helpINSTALL_STEP_EU_COOKIE').toggle();return false;">{l s='No, I\'m having trouble' mod='jprestaspeedpack'}</a>
                                <div class="stephelp" id="helpINSTALL_STEP_EU_COOKIE">
                                    <ol>
                                        <li>{l s='Make sure you have the latest version of the module' mod='jprestaspeedpack'}</li>
                                        <li>{l s='Still having problem? Then ' mod='jprestaspeedpack'} <a href="{$contact_url|escape:'html':'UTF-8'}" target="_blank">{l s='contact us here' mod='jprestaspeedpack'}</a></li>
                                    </ol>
                                </div>
                            </div>
                            {/if}
                        </div>

                        {* VALIDATE STEP *}
                        <div class="step {if $cur_step > $INSTALL_STEP_VALIDATE}stepok{elseif $cur_step < $INSTALL_STEP_VALIDATE}steptodo{/if}">
                            {if $cur_step > $INSTALL_STEP_VALIDATE}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="24" height="24" />
                            {elseif $cur_step < $INSTALL_STEP_VALIDATE}
                               <span>{$INSTALL_STEP_VALIDATE|escape:'html':'UTF-8'}</span>
                            {else}
                               <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/curstep.gif" alt="todo" width="24" height="24" />
                            {/if}
                            {l s='Push in production mode' mod='jprestaspeedpack'}
                            {if $cur_step == $INSTALL_STEP_VALIDATE}
                            <div class="stepdesc">
                                <ol>
                                    <li><a href="{$shop_link_debug|escape:'html':'UTF-8'}" target="_blank">{l s='Click here to browse your site in test mode' mod='jprestaspeedpack'}</a></li>
                                    <li>{l s='You can do more tests and once your are ready...' mod='jprestaspeedpack'}</li>
                                </ol>
                                <a href="#" class="okbtn" onclick="$('#pagecache_form_install').submit();return false;">{l s='Enable PageCache for my customers!' mod='jprestaspeedpack'}</a>
                                <a href="#" class="kobtn" onclick="$('#helpINSTALL_STEP_VALIDATE').toggle();return false;">{l s='No, I\'m having trouble' mod='jprestaspeedpack'}</a>
                                <div class="stephelp" id="helpINSTALL_STEP_VALIDATE">
                                    <ol>
                                        <li>{l s='Make sure that the problem you have does not occur if you disable PageCache module' mod='jprestaspeedpack'}</li>
                                        <li>{l s='If your problem is only occuring with PageCache enabled, then' mod='jprestaspeedpack'} <a href="{$contact_url|escape:'html':'UTF-8'}" target="_blank">{l s='contact us here' mod='jprestaspeedpack'}</a></li>
                                    </ol>
                                </div>
                            </div>
                            {/if}
                        </div>

                        <div class="bootstrap actions">
                            <button type="submit" value="1" onclick="$('#pagecache_install_step').val({$INSTALL_STEP_BUY_FROM|escape:'html':'UTF-8'}); return true;" id="submitModuleRestartInstall" name="submitModuleRestartInstall" class="btn btn-default">
                                <i class="process-icon-cancel" style="color:red"></i> {l s='Restart from first step' mod='jprestaspeedpack'}
                            </button>
                            {if $cur_step !== $INSTALL_STEP_VALIDATE}
                            <button type="submit" value="1" onclick="$('#pagecache_install_step').val({$INSTALL_STEP_VALIDATE|escape:'html':'UTF-8'}); return true;" id="submitModuleGoToProd" name="submitModuleGoToProd" class="btn btn-default">
                                <i class="process-icon-next" style="color: #59C763"></i> {l s='Validate all steps' mod='jprestaspeedpack'}
                            </button>
                            {/if}
                        </div>

                    </div>
                {else}
                    <input type="hidden" name="pagecache_install_step" id="pagecache_install_step" value="{$INSTALL_STEP_BACK_TO_TEST|escape:'html':'UTF-8'}"/>
                    <div class="installstep"><img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="20" height="20" /> {l s='Congratulations!' mod='jprestaspeedpack'} {$module_displayName|escape:'html':'UTF-8'} {l s='is currently installed in' mod='jprestaspeedpack'} <b>{l s='production mode' mod='jprestaspeedpack'}</b>{if $pagecache_skiplogged}{l s=' for not logged in users' mod='jprestaspeedpack'}{/if}{l s=', that means your site is now faster than ever!' mod='jprestaspeedpack'}
                    </div>
                    <div class="installstep">{l s='If you are having trouble, ' mod='jprestaspeedpack'}<a href="#" class="browsebtn" onclick="$('#pagecache_form_install').submit();return false;">{l s='go back to test mode' mod='jprestaspeedpack'}</a></div>
                {/if}
                    <button type="submit" value="1" id="submitModuleClearCache" name="submitModuleClearCache" class="btn btn-default" style="display:none">
                        <i class="process-icon-delete" style="color:orange"></i> {l s='Clear cache' mod='jprestaspeedpack'}
                    </button>
                    <ul style="display:none">
                        <li id="desc-module-clearcache-li">
                            <a id="desc-module-clearcache" class="toolbar_btn" href="#" onclick="$('#submitModuleClearCache').click(); return false;" style="color:white; background-color: #FFA500FF">
                                <i class="process-icon-delete"></i>
                                <div>{l s='Clear cache' mod='jprestaspeedpack'}</div>
                            </a>
                        </li>
                    </ul>
                </div>
                </fieldset>
            </form>
        </div>
        {if !$pagecache_debug}
            <div class="panel">
                <h3 class="panel-heading">{if $avec_bootstrap}<i class="icon-dashboard"></i>{else}<img width="16" height="16" src="../img/admin/stats.gif" alt=""/>{/if} {l s='Cache performance' mod='jprestaspeedpack'}</h3>
                <label for="hitrate">{l s='Hit rate' mod='jprestaspeedpack'}</label>
                <div id="hitrate" class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="{$performances.percent_hit|intval}" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width: {$performances.percent_hit|intval}%;">
                        {$performances.percent_hit|escape:'html':'UTF-8'}%
                    </div>
                </div>
                <p>{l s='This represents the rate of visitors getting the cached page, which mean the fast way. Higher is better! Don\'t worry, it is normal to get a low rate at the beginning.' mod='jprestaspeedpack'}</p>
                <p>{l s='You can improve this rate by using JPresta Cache Warmer' mod='jprestaspeedpack'}.</p>
                {if $performances.count_total > 0}
                    <div id="used_cache_chart" class='chart with-transitions' style="height: 400px">
                        <svg></svg>
                    </div>
                    <p>{l s='These metrics are based on %d visits since %s' sprintf=[$performances.count_total|intval,$performances.start_date|date_format:"%Y-%m-%d %H:%M:%S"] mod='jprestaspeedpack'}</p>
                    <p>{l s='These metrics are flushed when you go in the statistics tab and click on' mod='jprestaspeedpack'} "{l s='Reset cache (with stats)' mod='jprestaspeedpack'}".</p>
                {/if}
                <div class="alert alert-info">
                    {l s='If you use Pagespeed Insight or GTMetrix to measure the "speed" of your shop, then you should read our article to better understand the computed score and how to improve it' mod='jprestaspeedpack'}.
                    <a href="{$jpresta_shop_url|escape:'html':'UTF-8'}{l s='/en/blog/post/4-increase-the-pagespeed-score-of-prestashop' mod='jprestaspeedpack'}" target="_blank">{l s='Read our article' mod='jprestaspeedpack'}&nbsp;<i class="material-icons" style="font-size: 1rem;vertical-align: text-top;">launch</i></a>
                </div>
            </div>
        {/if}
    </div>
    {if !$pagecache_debug}
        <div class="col-md-12 col-lg-6">
            <div class="panel">
                <h3 class="panel-heading">{if $avec_bootstrap}<i class="icon-dashboard"></i>{else}<img width="16" height="16" src="../img/admin/stats.gif" alt=""/>{/if} {l s='TTFB' mod='jprestaspeedpack'}</h3>
                <p>{l s='The TTFB is the real one computed by the browser of your visitors' mod='jprestaspeedpack'}</p>
                {if $pagecache_statsttfb}
                    <select id="perf_controller" name="perf_controller">
                        <option value="all">{l s='All pages' mod='jprestaspeedpack'}</option>
                        {foreach $managed_controllers as $controller_name => $controller}
                            <option value="{$controller_name|escape:'javascript':'UTF-8'}">{$controller['title']|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    <div id="ttfb_chart" class='chart with-transitions' style="height: 400px">
                        <svg></svg>
                    </div>
                    <p id="perf_infos"></p>
                    <p>{l s='You may see holes in lines or dates without data, this happens when no visits was recorded on that date for that type of page and that type of cache' mod='jprestaspeedpack'}.</p>
                {else}
                    <div class="alert alert-info">{l s='Statistics about TTFB have been disabled in "Advanced mode" > "Options"' mod='jprestaspeedpack'}</div>
                {/if}
            </div>
        </div>
    {/if}
</div>
<script type="application/javascript">
    var chart_ttfb = null;
    $(document).ready(function () {
        let used_cache_datas = [
            {
                "label": "{l s='Cache not available' mod='jprestaspeedpack'}",
                "value": {$performances.percent_missed|escape:'javascript':'UTF-8'}
            },
            {
                "label": "{l s='Server cache' mod='jprestaspeedpack'}",
                "value": {$performances.percent_hit_server|escape:'javascript':'UTF-8'}
            },
            {
                "label": "{l s='Static cache' mod='jprestaspeedpack'}",
                "value": {$performances.percent_hit_static|escape:'javascript':'UTF-8'}
            },
            {
                "label": "{l s='Browser cache' mod='jprestaspeedpack'}",
                "value": {$performances.percent_hit_browser|escape:'javascript':'UTF-8'}
            },
            {
                "label": "{l s='Back/forward cache' mod='jprestaspeedpack'}",
                "value": {$performances.percent_hit_bfcache|escape:'javascript':'UTF-8'}
            },
        ];

        //Donut chart example
        if (typeof nv !== 'undefined') {
            nv.addGraph(function () {
                let chart_visits = nv.models.pieChart()
                    .x(function (d) {
                        return d.label
                    })
                    .y(function (d) {
                        return d.value
                    })
                    .color(['#ce720b', '#007e00', '#00bd00', '#00da00', '#00ff00'])
                    .donut(true)          //Turn on Donut mode. Makes pie chart look tasty!
                    .donutRatio(0.25)     //Configure how big you want the donut hole size to be.
                    .tooltipContent(function (key, y, e, graph) {
                        return '<b>' + y + ' %' + '</b>';
                    });

                d3.select("#used_cache_chart svg")
                    .datum(used_cache_datas)
                    .transition().duration(500)
                    .call(chart_visits);

                //Update the chart when window resizes.
                nv.utils.windowResize(function () {
                    chart_visits.update()
                });

                return chart_visits;
            });

            let initGraph = function () {
                d3.json('{$pagecache_datas_url|escape:'javascript':'UTF-8'}&type=ttfb&controller_name=' + $('#perf_controller').val() + '&_=' + Date.now(), function (datasInfos) {
                    nv.addGraph(function () {
                        let data = datasInfos['datas'];

                        if (datasInfos['total_count'] > 0) {
                            let msg = "{l s='These metrics are based on _TTFB_VISIT_ visits since _TTFB_START_' mod='jprestaspeedpack'}.";
                            $('#perf_infos').html(msg.replace('_TTFB_START_', datasInfos['start_date']).replace('_TTFB_VISIT_', datasInfos['total_count']));
                        } else {
                            $('#perf_infos').html('');
                        }

                        chart_ttfb = nv.models.lineChart()
                            .margin({
                                left: 80,
                                bottom: 80,
                                right: 5
                            })  //Adjust chart margins to give the x-axis some breathing room.
                            .useInteractiveGuideline(true)  //We want nice looking tooltips and a guideline!
                            .transitionDuration(350)  //how fast do you want the lines to transition?
                            .showLegend(true)       //Show the legend, allowing users to turn on/off line series.
                            .showYAxis(true)        //Show the y-axis
                            .showXAxis(true)        //Show the x-axis
                            .noData("{l s='No metric available yet, reload the page once your shop get some visitors' mod='jprestaspeedpack'}");
                        ;

                        let maxTtfb = 0;
                        $.each(data, function (i, rowType) {
                            $.each(rowType['values'], function (j, value) {
                                maxTtfb = Math.max(maxTtfb, value['y']);
                            });
                        });

                        // X axis
                        let midnight = new Date();
                        midnight.setHours(0);
                        midnight.setMinutes(0);
                        midnight.setSeconds(0);
                        midnight.setMilliseconds(0);
                        let startDate = new Date(midnight.getTime());
                        startDate.setDate(midnight.getDate() - 14);
                        let endDate = new Date(midnight.getTime());
                        endDate.setDate(midnight.getDate() + 1);
                        chart_ttfb.forceX([startDate.getTime() / 1000, endDate.getTime() / 1000]);
                        let tickValues = [];
                        for (let i = 0; i < 15; i++) {
                            tickValues[i] = (startDate.getTime() / 1000 + 86400 * i);
                        }
                        chart_ttfb.xAxis.axisLabel('Day').rotateLabels(-45).showMaxMin(false).tickValues(tickValues).tickFormat(function (d) {
                            let date = new Date(d * 1000);
                            return d3.time.format('%m-%d')(date);
                        });

                        // Y axis
                        chart_ttfb.forceY([0, Math.ceil(maxTtfb / 100) * 100]);
                        chart_ttfb.yAxis
                            .axisLabel('TTFB (ms)')
                            .tickFormat(function (d) {
                                return d != null ? d + ' ms' : 'N/A';
                            });
                        d3.select('#ttfb_chart svg')    //Select the <svg> element you want to render the chart in.
                            .datum(data)         //Populate the <svg> element with chart data...
                            .call(chart_ttfb);          //Finally, render the chart!

                        // Update the chart when window resizes.
                        nv.utils.windowResize(function () {
                            chart_ttfb.update()
                        });

                        return chart_ttfb;
                    });
                });
            }

            let redrawGraph = function () {
                console.log('Refresh chart for controller: ' + $('#perf_controller').val());
                d3.json('{$pagecache_datas_url|escape:'javascript':'UTF-8'}&type=ttfb&controller_name=' + $('#perf_controller').val() + '&_=' + Date.now(), function (datasInfos) {
                    let data = datasInfos['datas'];

                    if (datasInfos['total_count'] > 0) {
                        let msg = "{l s='These metrics are based on _TTFB_VISIT_ visits since _TTFB_START_' mod='jprestaspeedpack'}";
                        $('#perf_infos').html(msg.replace('_TTFB_START_', datasInfos['start_date']).replace('_TTFB_VISIT_', datasInfos['total_count']));
                    } else {
                        $('#perf_infos').html('');
                    }

                    let maxTtfb = 0;
                    $.each(data, function (i, rowType) {
                        $.each(rowType['values'], function (j, value) {
                            maxTtfb = Math.max(maxTtfb, value['y']);
                        });
                    });

                    chart_ttfb.forceY([0, Math.ceil(maxTtfb / 100) * 100]);
                    d3.select('#ttfb_chart svg > *').remove();
                    d3.select('#ttfb_chart svg').datum(data).transition().duration(500).call(chart_ttfb);
                });
            };

            $('#perf_controller').change(function () {
                redrawGraph();
            });

            initGraph();
        }
    });
</script>
