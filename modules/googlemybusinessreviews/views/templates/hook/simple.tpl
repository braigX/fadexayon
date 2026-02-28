{*
* 2020 AN Eshop Group
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0).
* It is available through the world-wide-web at this URL:
* https://opensource.org/licenses/osl-3.0.php
* If you are unable to obtain it through the world-wide-web, please send an email
* to contact@payplug.com so we can send you a copy immediately.
*
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PayPlug module to newer
 * versions in the future.
*
*  @author  AN Eshop Group
*  @copyright  2020 AN Eshop Group
*  @license   Private
*  AN Eshop Group
*}
<div class="section-rating">
    {if $rating}
        <div class="Review__Line Review__Align__Center Review__p-1">
            <span class="Review__Rating Review__Image">
                {$rating|escape:'htmlall':'UTF-8'}
            </span>
        </div>
        <div class="Review__Line Review__align__center">
            <span class="Review__Rating_Stars">
              {include 'module:googlemybusinessreviews/views/templates/hook/partials/stars.tpl' rating=$rating}
            </span>
        </div>
    {/if}
    {if $nb_reviews > 0}
        <div class="Review__Line Review__align__center Review__p-1">
            <span class="Review__NB_Rating">
                <a class="Review__Bt" href="{$place_url|escape:'htmlall':'UTF-8'}" target="_blank">
                    {* {$nb_reviews}*}
                    {*            {if $nb_reviews == 1}*}
                    {*                {l s='Google rewiew' mod='googlemybusinessreviews''}*}
                    {*            {else}*}
                    {*                {l s='Google rewiews' mod='googlemybusinessreviews'}*}
                    {*            {/if}*}
                    {l s='See all our reviews' mod='googlemybusinessreviews'}
                 </a>
            </span>
        </div>
    {/if}
</div>
