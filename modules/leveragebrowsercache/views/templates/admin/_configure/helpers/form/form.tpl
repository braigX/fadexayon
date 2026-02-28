{*
*  @author    keshva
*  @copyright 2017
*}

{extends file="helpers/form/form.tpl"}

{block name="label"}
    {if $input.type == 'topform'}

        <div class="panel ">
            <div class="panel-heading">
                <i class="icon-cogs"></i>     
                {l s="Leverage Browser Caching Setting and Gzip" mod="leveragebrowsercache"}
            </div>
            <div class="form-wrapper">
                <div class="row" style="background-color: transparent;" >
                    <div id="tab-description" class="plugin-description section "> 
                        <form action="" method="post" id="orderreferenceform"> 

                            <div class="form-wrapper">
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='Enable Browser Caching.' mod='leveragebrowsercache'}</label>
                                    <div class="col-lg-9 ">
                                        <span class="switch prestashop-switch fixed-width-lg">
                                            <input type="radio" name="leveragebrowsercache" id="ref_on" value="1" {if $input.leveragebrowsercache == '1'} checked="checked" {/if}>
                                            <label for="ref_on">Yes</label>
                                            <input type="radio" name="leveragebrowsercache" id="ref_off" value="0" {if $input.leveragebrowsercache == '0'} checked="checked" {/if}>
                                            <label for="ref_off">No</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                        <p class="help-block"></p>
                                    </div>
                                </div>
                                <div class="col-lg-12 ">
                                    <div class="panel-footer">
                                        <button type="submit" value="1" id="module_form_submit_btn" name="submitleveragebrowsercache" class="btn btn-default pull-right">
                                            <i class="process-icon-save"></i> {l s='Save' mod='leveragebrowsercache'}
                                        </button>
                                    </div>	
                                </div>

                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>


        <div class="panel ">
            <div class="panel-heading">
                <i class="icon-cogs"></i>     
                {l s="Leverage Browser Caching" mod="leveragebrowsercache"}
            </div>
            <div class="form-wrapper">
                <div class="row" style="background-color: transparent;" >
                    <div id="tab-description" class="plugin-description section ">                      
                        <p>As it’s name, it will fix Leverage Browser Caching issues in your website. Also it improves page speed score in website testing tools like: Pingdom, GTmetrix, PageSpeed, Google PageSpeed Insights and YSlow.</p>
                        <h4>About Leverage Browser Caching</h4>
                        <p>Leverage Browser Caching means storing static files of a website in visitor browser. And then retrieving them from browser quickly instead again from server. Actually it uses to speed up each page of a website.</p>
                        <h4>How Leverage Browser Caching Works?</h4>
                        <p>When you visit a web page, your browser downloads all content of the particular page as well as common static files like css and js files. And when you visit other page of same website, your browser downloads them again. But if you have enabled Leverage Browser Caching, then all statics files will serve from your browser instead server. Now when you will visit any page of the particular website, it will only download unique contains of the page and static files will serve from your browser. in this way, it speed up each page of a website.</p>
                        <h4>Benefits of Leverage Browser Caching</h4>
                        <p>Primary benefit is speeding up website because static files will serve from your browser. it saves internet data of website visitor. it also saves bandwidth of website server and decrease load of server. Simply it decrease HTTP requests.</p></div>
                </div>
            </div>
        </div>

    {/if}
{/block}