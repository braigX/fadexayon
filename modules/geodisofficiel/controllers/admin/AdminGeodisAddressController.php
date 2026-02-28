<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Controller/Admin/GeodisControllerAdminAbstractMenu.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisSite.php';

class AdminGeodisAddressController extends GeodisControllerAdminAbstractMenu
{
    public function __construct()
    {
        GeodisServiceTranslation::registerSmarty();
        $this->bootstrap = true;
        parent::__construct();
    }

    public function renderList()
    {
        $this->base_tpl_view = 'main.tpl';
        $sites = $this->getSites();
        $this->tpl_view_vars['removalSites'] = $sites;
        $this->tpl_view_vars['shopCollection'] = $this->getShopCollection();
        $this->tpl_view_vars['shopCollection'] = Tools::getValue('token');
        $this->tpl_view_vars['url'] = $this->context->link->getAdminLink(
            GEODIS_ADMIN_PREFIX.'AddressConfiguration'
        );

        return parent::renderView();
    }

    public function getSites()
    {
        $collection = GeodisSite::getCollection();
        $collection->where('removal', '=', 1);
        $collection->sqlWhere('a1.id_shop = '.(int)Context::getContext()->shop->id);
        foreach ($collection as $site) {
            $countryName = $site->getCountryName($site->id_country);
            $site->country_name = $countryName;
        }
        return $collection;
    }


    public function setMedia($isNewTheme = false)
    {
        $this->addCSS(_PS_MODULE_DIR_.'geodisofficiel//views/css/admin/adresses.css');

        return parent::setMedia($isNewTheme);
    }

    public function getShopCollection()
    {
        $collection = new PrestaShopCollection('shop');
        return $collection;
    }
}
