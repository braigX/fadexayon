<?php
/**
 * 2019 GEODIS.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@geodis.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    GEODIS <contact@geodis.com>
 *  @copyright 2019 GEODIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Controller/Admin/GeodisControllerAdminAbstractMenu.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';

/**
 * Class AdminGeodisCronGrid
 */
class AdminGeodisCronGridController extends GeodisControllerAdminAbstractMenu
{
    /**
     * Constructor
     *
     * AdminGeodisCronGrid constructor.
     */
    public function __construct()
    {
        GeodisServiceTranslation::registerSmarty();
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function renderView()
    {
        $this->base_tpl_view = 'main.tpl';
        Context::getContext()->smarty->assign('cron_url', $this->getCronJobUrlToDisplay());
        Context::getContext()->smarty->assign('cron_label', GeodisServiceTranslation::get('*.*.cron.description'));

        return parent::renderView();
    }

    /**
     * getting cron url
     *
     * @return string
     */
    protected function getCronJobUrlToDisplay()
    {
        $token = Configuration::get('CRONJOBS_EXECUTION_TOKEN', null, 0, 0);
        $curl_url = Context::getContext()->link->getAdminLink('AdminGeodisCronJob', false);
        $curl_url .= '&token='.$token;

        return $curl_url;
    }
}
