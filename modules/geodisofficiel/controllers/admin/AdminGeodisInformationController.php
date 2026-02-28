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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceWebservice.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceSynchronize.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';

class AdminGeodisInformationController extends GeodisControllerAdminAbstractMenu
{
    protected $texteErreur;
    protected $codeErreur;
    protected $ok;

    public function __construct()
    {
        $this->bootstrap = true;

        if (Tools::isSubmit('submit')) {
            $this->processLogin();
        } elseif (Tools::isSubmit('synchronise')) {
            try {
                GeodisServiceSynchronize::getInstance()->syncCustomerConfiguration();
                $this->confirmations[] = (string) GeodisServiceTranslation::get(
                    'Admin.Information.sync.success'
                );
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        $this->texteErreur = (string) GeodisServiceTranslation::get(
            'Admin.Information.connection.error.default'
        );
        parent::__construct();
    }

    public function renderList()
    {
        $this->base_tpl_view = 'main.tpl';

        $dateLastSynchronization = GeodisServiceConfiguration::getInstance()
            ->get('date_customer_synchronization');
        if ($dateLastSynchronization) {
            $this->tpl_view_vars['lastSynchronizationDate'] = Tools::displayDate($dateLastSynchronization, null, true);
        } else {
            $this->tpl_view_vars['lastSynchronizationDate'] = false;
        }

        $this->tpl_view_vars['id_lang'] = Context::getContext()->language->id;
        $this->tpl_view_vars['login'] = (string) GeodisServiceConfiguration::getInstance()
            ->getPostValue('api_login');
        $this->tpl_view_vars['secretKey'] = (string) GeodisServiceConfiguration::getInstance()
            ->getPostValue('api_secret_key');

        $this->assignInformation();

        return parent::renderView();
    }

    protected function assignInformation()
    {
        $infosSociety = $this->getInfosSociety();

        if (!count($infosSociety)) {
            $this->tpl_view_vars['hasInformation'] = false;
            return;
        }

        $preparationServices = $this->getServices(GeodisPrestation::TYPE_PREPA_EXPE);
        $expeditionServices = $this->getServices(GeodisPrestation::TYPE_REMOVAL);

        $this->tpl_view_vars['hasInformation'] = true;
        $this->tpl_view_vars['infosSociety'] = $infosSociety[0];
        $this->tpl_view_vars['preparationServices'] = $preparationServices;
        $this->tpl_view_vars['expeditionServices'] = $expeditionServices;
    }

    protected function isLogged()
    {
        $login = GeodisServiceConfiguration::getInstance()->get('api_login');
        $secretKey = GeodisServiceConfiguration::getInstance()->get('api_secret_key');

        return !empty($login) && !empty($secretKey);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(_PS_MODULE_DIR_.'geodisofficiel/views/css/admin/informations.css', 'all', 1);
        $this->addJS(_PS_MODULE_DIR_.'geodisofficiel/views/js/admin/informations.js');
        $this->addjQueryPlugin(array('chosen'));
        if (Tools::isSubmit('submit')) {
            Media::addJsDef(
                array(
                    'geodis' => array(
                        'submit' => true,
                        'ok' => $this->ok,
                        'codeErreur' => $this->codeErreur,
                        'texteErreur' => $this->texteErreur,
                        'success' => (string) GeodisServiceTranslation::get(
                            'Admin.Information.index.connection.login.success'
                        ),
                    ),
                )
            );
        } elseif ($this->isLogged()) {
            Media::addJsDef(
                array(
                    'geodis' => array(
                        'submit' => false,
                        'logged' => true,
                    ),
                )
            );
        } else {
            Media::addJsDef(
                array(
                    'geodis' => array(
                        'submit' => false,
                    ),
                )
            );
        }
    }

    protected function processLogin()
    {
        $webService = GeodisServiceWebservice::getInstance(Tools::getValue('login'), Tools::getValue('secretKey'));

        try {
            $response = $webService->getCustomerConfiguration();

            if (isset($response['ok'])) {
                $this->ok = $response['ok'];
                $this->codeErreur = $response['codeErreur'];
                $this->texteErreur = $response['texteErreur'];
                GeodisServiceConfiguration::getInstance()->set('api_login', Tools::getValue('login'));
                GeodisServiceConfiguration::getInstance()->set('api_secret_key', Tools::getValue('secretKey'));

                try {
                    GeodisServiceSynchronize::getInstance()->syncCustomerConfiguration(true);
                } catch (Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    public function getInfosSociety()
    {
        $infosSocietyQuery = new DbQuery();
        $infosSocietyQuery->select('*');
        $infosSocietyQuery->select('s.name AS society');
        $infosSocietyQuery->from(GEODIS_NAME_SQL.'_site', 's');
        $infosSocietyQuery->leftJoin('country_lang', 'l', 's.id_country = l.id_country');
        $infosSocietyQuery->leftJoin('country', 'c', 'c.id_country = l.id_country');
        $infosSocietyQuery->where('s.type = "C"');

        $res = Db::getInstance()->executeS($infosSocietyQuery);
        return $res;
    }

    public function getServices($type)
    {
        $serviceQuery = new DbQuery();
        $serviceQuery->select('*');
        $serviceQuery->from(GEODIS_NAME_SQL.'_site', 's');
        $serviceQuery->leftJoin(GEODIS_NAME_SQL.'_account', 'ac', 'ac.id_agency = s.id_site');
        $serviceQuery->leftJoin(GEODIS_NAME_SQL.'_account_prestation', 'ap', 'ac.id_account = ap.id_account');
        $serviceQuery->leftJoin(GEODIS_NAME_SQL.'_prestation', 'p', 'p.id_prestation = ap.id_prestation');
        $serviceQuery->where('p.type_service = "'.pSql($type).'"');
        $serviceQuery->where('s.type = "A"');

        $res = Db::getInstance()->executeS($serviceQuery);

        $results = array();
        foreach ($res as $row) {
            if (!isset($results[$row['id_account']])) {
                $results[$row['id_account']] = array(
                    'id_account' => $row['id_account'],
                    'name' => $row['name'],
                    'codeClient' => $row['code_client'],
                    'services' => array(
                    ),
                );
            }
            $results[$row['id_account']]['services'][] = $row['libelle'];
        }

        return $results;
    }
}
