{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}

<div id="list_tab_author">
    <ul class="list_tab_author">
        <li class="confi_tab config_tab_author {if $control=='employees'}active{/if}" data-tab-id="all_author"><i class="ets_svg user">
                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1536 1399q0 109-62.5 187t-150.5 78h-854q-88 0-150.5-78t-62.5-187q0-85 8.5-160.5t31.5-152 58.5-131 94-89 134.5-34.5q131 128 313 128t313-128q76 0 134.5 34.5t94 89 58.5 131 31.5 152 8.5 160.5zm-256-887q0 159-112.5 271.5t-271.5 112.5-271.5-112.5-112.5-271.5 112.5-271.5 271.5-112.5 271.5 112.5 112.5 271.5z"/></svg>
            </i>&nbsp;{l s='Administrator - Authors' mod='ybc_blog'} &nbsp;<span class="badge">{$totalEmployee|intval}</span></li>
        {if $YBC_BLOG_ALLOW_CUSTOMER_AUTHOR}
            <li class="confi_tab config_tab_author {if $control=='customer'}active{/if}" data-tab-id="all_customer"><i class="ets_svg user">
                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1536 1399q0 109-62.5 187t-150.5 78h-854q-88 0-150.5-78t-62.5-187q0-85 8.5-160.5t31.5-152 58.5-131 94-89 134.5-34.5q131 128 313 128t313-128q76 0 134.5 34.5t94 89 58.5 131 31.5 152 8.5 160.5zm-256-887q0 159-112.5 271.5t-271.5 112.5-271.5-112.5-112.5-271.5 112.5-271.5 271.5-112.5 271.5 112.5 112.5 271.5z"/></svg>
                </i>&nbsp;{l s='Community - Authors' mod='ybc_blog'}{if $totalCustomer > 0}&nbsp;<span class="badge">{$totalCustomer|intval}</span>{/if}</li>
        {/if}
        <li class="confi_tab config_tab_author {if $control=='author'}active{/if}" data-tab-id="setting"><i class="icon icon-AdminAdmin"></i>&nbsp;{l s='Settings' mod='ybc_blog'}</li> 
    </ul>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.ybc-blog-panel').hide();
        $('.ybc-blog-panel-settings').hide();
        if($('.ybc-blog-panel-employee').length>0)
        {
            $('.ybc-blog-panel-employee').hide();
        }
        if($('.ybc-blog-panel-customer').length>0)
        {
            $('.ybc-blog-panel-customer').hide();
        }
        if($('.config_tab_author.active').attr('data-tab-id')=='all_customer')
        {
            $('.ybc-blog-panel').hide();
            $('.ybc-blog-panel.customer').show();
            if($('.ybc-blog-panel-employee').length>0)
            {
                $('.ybc-blog-panel-employee').hide();
            }
            if($('.ybc-blog-panel-customer').length>0)
            {
                $('.ybc-blog-panel-customer').show();
            }
        }
        else
        {
            if($('.config_tab_author.active').attr('data-tab-id')=='setting')
            {
                $('.ybc-blog-panel-settings').show();
                $('.ybc-blog-panel').hide();
                if($('.ybc-blog-panel-employee').length>0)
                {
                    $('.ybc-blog-panel-employee').hide();
                }
                if($('.ybc-blog-panel-customer').length>0)
                {
                    $('.ybc-blog-panel-customer').hide();
                }
            }
            else
            {
                $('.ybc-blog-panel').hide();
                $('.ybc-blog-panel.employee').show();
                if($('.ybc-blog-panel-employee').length>0)
                {
                    $('.ybc-blog-panel-employee').show();
                }
                if($('.ybc-blog-panel-customer').length>0)
                {
                    $('.ybc-blog-panel-customer').hide();
                }
            }
            
        }
        $(document).on('click','.config_tab_author',function(){
           if(!$(this).hasClass('active'))
           {
                $('.config_tab_author').removeClass('active');
                $(this).addClass('active');
                if($(this).attr('data-tab-id')=='all_author' || $(this).attr('data-tab-id')=='all_customer')
                {
                    $('.ybc-blog-panel-settings').hide();
                    if($(this).attr('data-tab-id')=='all_customer')
                    {
                        $('.ybc-blog-panel').hide();
                        $('.ybc-blog-panel.customer').show();
                        if($('.ybc-blog-panel-employee').length>0)
                        {
                            $('.ybc-blog-panel-employee').hide();
                        }
                        if($('.ybc-blog-panel-customer').length>0)
                        {
                            $('.ybc-blog-panel-customer').show();
                        }
                    }
                    else
                    {
                        $('.ybc-blog-panel').hide();
                        $('.ybc-blog-panel.employee').show();
                        if($('.ybc-blog-panel-employee').length>0)
                        {
                            $('.ybc-blog-panel-employee').show();
                        }
                        if($('.ybc-blog-panel-customer').length>0)
                        {
                            $('.ybc-blog-panel-customer').hide();
                        }
                    }
                }
                else
                {
                    $('.ybc-blog-panel-settings').show();
                    $('.ybc-blog-panel').hide();
                    if($('.ybc-blog-panel-employee').length>0)
                    {
                        $('.ybc-blog-panel-employee').hide();
                    }
                    if($('.ybc-blog-panel-customer').length>0)
                    {
                        $('.ybc-blog-panel-customer').hide();
                    }
                }
           } 
        });
    });
</script>