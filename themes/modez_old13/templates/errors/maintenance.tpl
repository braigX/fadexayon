{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='layouts/layout-error.tpl'}

{block name='content'}
  <section id="main" class="maintenance-page" style="font-family: 'Arial', sans-serif; background: linear-gradient(135deg, #ff006699, #4a90e2d4); color: #fff; text-align: center; padding: 50px 20px;">

    {block name='page_header_container'}
      <header class="page-header" style="margin-bottom: 20px;">
        {block name='page_header_logo'}
          <div class="logo" style="margin-bottom: 20px;">
            <img src="{$shop.logo}" alt="logo" class="logo-img" style="max-width: 150px; animation: bounceLogo 1.5s infinite ease-in-out;">
          </div>
        {/block}

        {block name='hook_maintenance'}
          {$HOOK_MAINTENANCE nofilter}
        {/block}

        {block name='page_header'}
          <h1 class="maintenance-title" style="font-size: 36px; font-weight: bold; color: #fff; text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); margin-bottom: 20px;">
            {block name='page_title'}{l s='Nous revenons bientôt.' d='Shop.Theme.Global'}{/block}
          </h1>
        {/block}
      </header>
    {/block}

    {block name='page_content_container'}
      <section id="content" class="page-content page-maintenance" style="background-color: rgba(255, 255, 255, 0.1); border-radius: 10px; max-width: 600px; margin: 0 auto; animation: fadeInUp 2s ease-out;">
        {block name='page_content'}
          <div class="maintenance-message" style="background-color: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 10px; margin: 0 auto;">
            <p class="intro-text" style="color: #fff; font-size: 20px; margin-bottom: 10px; font-weight: 600;">Nous améliorons actuellement notre boutique pour mieux vous servir.</p>
            <p class="info-text" style="color: #fff; font-size: 16px; margin-bottom: 15px;">Nous travaillons dur pour remettre tout en ligne. Merci de votre patience.</p>
            <p class="contact-info" style="color: #fff; font-size: 14px; margin-bottom: 20px;">Besoin d'assistance ? Contactez-nous à <a href="tel:33825950850" style="color: #ffdd57;">+33 (0)825 950 850</a></p>
            <p class="social-media" style="color: #fff; font-size: 18px;">
              <a href="https://www.facebook.com/bfpcindar" target="_blank" style="color: #5500ff; font-weight: bold; text-decoration: none; margin: 0 10px; transition: color 0.3s ease;">Facebook</a> |
              <a href="https://twitter.com/bfpcindar" target="_blank" style="color: #5500ff; font-weight: bold; text-decoration: none; margin: 0 10px; transition: color 0.3s ease;">Twitter</a> |
              <a href="https://fr.linkedin.com/company/bfp-cindar" target="_blank" style="color: #5500ff; font-weight: bold; text-decoration: none; margin: 0 10px; transition: color 0.3s ease;">LinkedIn</a>
            </p>
          </div>
        {/block}
      </section>
    {/block}

    {block name='page_footer_container'}
      <footer class="page-footer" style="margin-top: 30px; font-size: 14px; color: rgba(255, 255, 255, 0.7);">
        <p>&copy; {date("Y")} {$shop.name}. Tous droits réservés.</p>
      </footer>
    {/block}

  </section>
  

  <style>
    @keyframes bounceLogo {
      0% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0); }
    }

    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
  </style>

{/block}
