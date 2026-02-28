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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisSite.php';

class AdminGeodisAddressConfigurationController extends GeodisControllerAdminAbstractMenu
{
    protected $confirmation = '';
    public $name = '';
    protected $site;
    protected $error;

    public function __construct()
    {
        GeodisServiceTranslation::registerSmarty();
        $this->bootstrap = true;
        parent::__construct();

        try {
            $this->site = new GeodisSite(Tools::getValue('id_site'));
            if ($this->site->id == null) {
                throw new Exception(GeodisServiceTranslation::get('Admin.AddressConfiguration.index.error'));
            }
        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }

        $value = array();
        foreach (Language::getLanguages() as $lang) {
            $value[$lang['id_lang']] = 1;
        }

        $this->site->default = $value;
        $this->site->save();

        foreach (GeodisSite::getCollection() as $site) {
            $value = array();
            foreach (Language::getLanguages() as $lang) {
                $value[$lang['id_lang']] = 0;
            }

            if ($this->site->id != $site->id) {
                $site->default = $value;
                $site->save();
            }
        }

        Tools::redirect($this->context->link->getAdminLink(GEODIS_ADMIN_PREFIX.'Address'));
    }
}
