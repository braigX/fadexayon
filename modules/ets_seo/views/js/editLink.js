/**
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
 */

if($('.language-selector ul.dropdown-menu a.dropdown-item').length)
{
    $('.language-selector ul.dropdown-menu a.dropdown-item').each(function(){
        if($(this).attr('href').indexOf('submitLang')<0)
        {
            if($(this).attr('href').indexOf('?') >0)
                $(this).attr('href',$(this).attr('href')+'&submitLang=1');
            else
                $(this).attr('href',$(this).attr('href')+'?submitLang=1');
        }

    });
}

if($('.language-selector .dropdown-menu a.dropdown-item').length)
{
    $('.language-selector .dropdown-menu a.dropdown-item').each(function(){
        if($(this).attr('href').indexOf('submitLang')<0)
        {
            if($(this).attr('href').indexOf('?') >0)
                $(this).attr('href',$(this).attr('href')+'&submitLang=1');
            else
                $(this).attr('href',$(this).attr('href')+'?submitLang=1');
        }

    });
}
if($('.language-selector select.link option').length)
{
    $('.language-selector select.link option').each(function(){
        if($(this).attr('value').indexOf('submitLang')<0)
        {
            if($(this).attr('value').indexOf('?') >0)
                $(this).attr('value',$(this).attr('value')+'&submitLang=1');
            else
                $(this).attr('value',$(this).attr('value')+'?submitLang=1');
        }

    });
}