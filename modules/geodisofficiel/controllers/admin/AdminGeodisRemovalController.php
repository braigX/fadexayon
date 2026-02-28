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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPackage.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisSite.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisRemoval.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisWSCapacity.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisFiscalCode.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';

class AdminGeodisRemovalController extends GeodisControllerAdminAbstractMenu
{
    public $name;
    protected $removalSiteOptions;
    protected $error;
    protected $success;

    public function __construct()
    {
        if (Tools::getIsset('ajaxRemovalDate')) {
            echo $this->getDaysOff(
                (int)Tools::getValue('idPrestation'),
                (int)Tools::getValue('idAccount'),
                (int)Tools::getValue('idSite')
            );
            die();
        }

        $this->error = array();
        $this->success = false;
        if (Tools::getValue('action') == 'print') {
            $this->processPrint();
        }

        if (Tools::getValue('submit')) {
            $this->success = $this->processPost();
        }

        if (Tools::getIsset('call')) {
            echo json_encode($this->getAccountPrestation((int)Tools::getValue('siteId')));
            die();
        }

        $this->removalSiteOptions = $this->getRemovalSite();
        $this->name = 'removal';
        GeodisServiceTranslation::registerSmarty();
        $this->bootstrap = true;
        parent::__construct();
        if (Tools::getValue('action') == 'setDefaultSite') {
            $this->setDefaultSite((int)Tools::getValue('newDefaultSiteId'));
        }
    }

    protected function getDaysOff($idPrestation, $idAccount, $idSite)
    {
        $site = new GeodisSite($idSite);
        $iso = Country::getIsoById($site->id_country);
        $prestation = new GeodisPrestation($idPrestation);
        $account = new GeodisAccount($idAccount);

        try {
            $response = GeodisServiceWebservice::getInstance()->getRemovalCalendar(
                $prestation->code_groupe_produits,
                $prestation->code_option,
                $iso,
                $prestation->code_produit,
                $account->code_sa
            );

            $data = array(
                'daysOff' =>  $response['contenu']['listExceptions'],
                'express' => $response['contenu']['express'],
                'morningAvailable' => $response['contenu']['matinDisponible'],
                'afternoonAvailable' => $response['contenu']['apresMidiDisponible'],
                'firstDateAvailable' => $response['contenu']['jourDebut'],
            );
        } catch (Exception $e) {
            $data = array(
                'error' => true,
            );
        }

        return json_encode($data);
    }

    public function renderList()
    {
        $this->base_tpl_view = 'main.tpl';
        if (!isset($this->tpl_view_vars)) {
            $this->tpl_view_vars = array();
        }
        $this->tpl_view_vars['form'] = $this->getForm();
        $this->tpl_view_vars['history'] = $this->gethistory();
        $this->tpl_view_vars['error'] = $this->error;
        $this->tpl_view_vars['success'] = $this->success;

        return parent::renderView();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_.'geodisofficiel//views/js/admin/GeodisRemoval.js');
        Media::addJsDef(
            array(
                'geodis' => array(
                    'optionNotAvailable' => (string) GeodisServiceTranslation::get(
                        'Admin.Removal.index.optionNotAvailable'
                    ),
                    'token' => Tools::getAdminTokenLite('AdminModules'),
                    'admin' => GEODIS_ADMIN_PREFIX,
                    'defaultFiscalCode' => GeodisServiceConfiguration::getInstance()->get('default_fiscal_code'),
                    'defaultRemovalSite' => (int) GeodisSite::getDefault()->id,
                ),
            )
        );
    }

    public function getForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->submit_action = 'submit';

        $configurations = $this->getFields();

        foreach ($configurations as $configuration) {
            if (Tools::getIsset($configuration['name']) && !$this->success) {
                $helper->fields_value[$configuration['name']] = Tools::getValue($configuration['name']);
            } else {
                if ($configuration['name'] == "fiscalCode") {
                    $helper->fields_value[$configuration['name']] =
                    GeodisServiceConfiguration::getInstance()->get('default_fiscal_code');
                } else {
                    $helper->fields_value[$configuration['name']] = "";
                }
            }
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => GeodisServiceTranslation::get('Admin.Removal.index.form.legend'),
                ),
                'input' => $configurations,
                'submit' => array(
                    'title' => GeodisServiceTranslation::get('Admin.Removal.index.submit.title'),
                    'class' => 'btn btn-default pull-right button',
                    'name' => 'submit',
                ),
            ),
        );
        $helper->languages = $this->context->controller->getLanguages();
        $helper->default_form_language = (int) $this->context->language->id;

        return $helper->generateForm(array($fields_form));
    }

    public function getRemovalSite()
    {
        $collection = GeodisSite::getCollection();
        $collection->where('removal', '=', 1);
        $res = array();
        foreach ($collection as $item) {
            $res[] = array(
                'value' => $item->id,
                'name' => $item->name,
            );
        }
        return $res;
    }

    public function getFiscalCode()
    {
        $collection = GeodisFiscalCode::getCollection();
        $res = array();
        foreach ($collection as $item) {
            $res[] = array(
                'value' => $item->id,
                'name' => $item->label,
            );
        }
        return $res;
    }

    public function getWsCapacity()
    {
        $collection = GeodisWSCapacity::getCollection();
        $res = array();
        foreach ($collection as $item) {
            $res[] = array(
                'value' => $item->key,
                'name' => $item->label,
            );
        }
        return $res;
    }

    public function getFields()
    {
        return array(
            array(
                'type' => 'select',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.removalAdresses'),
                'name' => 'removalAddress',
                'required' => true,
                'class' => 'js-removalSite',
                'options' => array(
                    'query' => $this->removalSiteOptions,
                    'id' => 'value',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'hidden',
                'name' => 'firstDateAvailable',
                'required' => false,
                'class' => 'js-firstDateAvailable',
            ),
            array(
                'type' => 'select',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.account'),
                'name' => 'account',
                'class' => 'js-account',
                'required' => true,
                'options' => array(
                    'query' => $this->getAccount(),
                    'id' => 'value',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'select',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.prestation'),
                'name' => 'prestation',
                'class' => 'js-prestation',
                'required' => true,
                'options' => array(
                    'query' => $this->getPrestation(),
                    'id' => 'value',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'text',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.numberOfBox'),
                'name' => 'nbBox',
                'class' => 'js-box',
                'required' => true,
            ),
            array(
                'type' => 'text',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.numberOfPallet'),
                'name' => 'nbPallet',
                'class' => 'js-pallet',
                'required' => true,
            ),
            array(
              'type' => 'text',
              'label' => GeodisServiceTranslation::get('Admin.Removal.index.weight'),
              'class' => 'js-float',
              'name' => 'weight',
              'required' => true,
            ),
            array(
                'type' => 'text',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.volume'),
                'class' => 'js-float js-volume',
                'name' => 'volume',
                'required' => true,
            ),
            array(
                'type' => 'text',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.observations'),
                'name' => 'observations',
                'size' => 70,
                'required' => false,
            ),
            array(
                'type' => 'radio',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.reglementedTransport'),
                'name' => 'reglemented_transport',
                'class' => 'js-reglemented_transport',
                'required' => false,
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'reglemented_transport_on',
                        'label' => GeodisServiceTranslation::get('Admin.Removal.index.noReglemented'),
                        'value' => 0,
                    ),
                    array(
                        'id' => 'reglemented_transport_off',
                        'label' => GeodisServiceTranslation::get('Admin.Removal.index.reglemented'),
                        'value' => 1,
                    ),
                ),
            ),
            array(
                'type' => 'select',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.fiscalCode'),
                'name' => 'fiscalCode',
                'class' => 'js-fiscalCode fiscalCode',
                'required' => false,
                'options' => array(
                    'query' => $this->getFiscalCode(),
                    'id' => 'value',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'select',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.legalVolume'),
                'name' => 'legalVolume',
                'class' => 'js-legalVolume legalVolume',
                'required' => false,
                'options' => array(
                    'query' => $this->getWsCapacity(),
                    'id' => 'value',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => 'text',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.totalVolume'),
                'name' => 'totalVolume',
                'class' => 'js-totalVolume totalVolume',
                'required' => false,
            ),
            array(
                'type' => 'text',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.removalDateWished'),
                'name' => 'removalDate',
                'size' => 50,
                'id' => "datepicker",
                'class' => 'js-removalDate datepicker',
                'required' => true,
            ),
            array(
                'type' => 'select',
                'label' => GeodisServiceTranslation::get('Admin.Removal.index.timeSlot'),
                'name' => 'timeSlot',
                'class' => 'js-timeSlot',
                'required' => false,
                'options' => array(
                    'query' => array(
                        array(
                            'value' => null,
                            'name' => '',
                        ),
                        array(
                            'value' => 0,
                            'name' => GeodisServiceTranslation::get('Admin.Removal.index.timeSlot.daytime'),
                        ),
                        array(
                            'value' => 1,
                            'name' => GeodisServiceTranslation::get('Admin.Removal.index.timeSlot.morning'),
                        ),
                        array(
                            'value' => 2,
                            'name' => GeodisServiceTranslation::get('Admin.Removal.index.timeSlot.afternoon'),
                        ),
                    ),
                    'id' => 'value',
                    'name' => 'name',
                ),
            ),
        );
    }

    public function processPost()
    {
        $this->error = array();
        if (!empty(Tools::getValue('weight')) && !preg_match('/^\d{1,6}(.\d{1,2})?$/', Tools::getValue('weight'))) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidWeightFormat'
            );
        } elseif (!Tools::getValue('weight')
            || (string) (float) Tools::getValue('weight') != Tools::getValue('weight')
        ) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidWeight'
            );
        }

        $idAccount = Tools::getValue('account');
        $idPrestation = Tools::getValue('prestation');
        $prestation = new GeodisPrestation($idPrestation);
        $accountPrestation = GeodisAccountPrestation::getFromExternal($idAccount, $idPrestation);

        if (!empty(Tools::getValue('volume')) && !preg_match('/^\d{1,6}(.\d{1,2})?$/', Tools::getValue('volume'))) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidVolumeFormat'
            );
        } elseif (!Tools::getValue('volume') && $prestation->zone != 'France'
            || !empty(Tools::getValue('volume'))
            && (string) (float) Tools::getValue('volume') != Tools::getValue('volume')
        ) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidVolume'
            );
        }

        if (Tools::getValue('reglemented_transport') == 1
            && $accountPrestation->manage_wine_and_liquor == 0
        ) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.removal.index.error.notWlPrestation'
            );
        }

        $nbBox = Tools::getValue('nbBox');
        if (!empty($nbBox) && $nbBox > 99999) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidBoxFormat'
            );
        }

        $nbPallet = Tools::getValue('nbPallet');
        if (!empty($nbPallet) && $nbPallet > 99999) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidPalletFormat'
            );
        }

        if ((!Tools::getValue('nbBox') && !Tools::getValue('nbPallet'))) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidQuantity'
            );
        }

        if (Tools::getValue('nbBox') && !ctype_digit(Tools::getValue('nbBox'))) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidQuantity'
            );
        }

        if (Tools::getValue('nbPallet') && !ctype_digit(Tools::getValue('nbPallet'))) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidQuantity'
            );
        }

        if ((!Tools::getValue('account'))) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidAccount'
            );
        }

        if ((!Tools::getValue('prestation'))) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidPrestation'
            );
        }

        if (Tools::getValue('reglemented_transport') == 1 && !Tools::getValue('totalVolume')) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.missingTotalVolume'
            );
        }

        if (Tools::getValue('reglemented_transport') == 1
            && (string) (float) Tools::getValue('totalVolume') != Tools::getValue('totalVolume')
        ) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidTotalVolume'
            );
        }

        try {
            if (!Tools::getValue('removalDate')) {
                throw new Exception('Invalid date');
            }

            $removalDate = new DateTime(Tools::getValue('removalDate'));
            $firstDayAvailable = new DateTime(Tools::getValue('firstDayAvailable'));
            $firstDayAvailable->setTime(0, 0, 0);

            if ($removalDate < $firstDayAvailable) {
                $this->error[] = (string) (GeodisServiceTranslation::get(
                    'Admin.Removal.index.error.pastDate.%s'
                )->addVar(Tools::displayDate($firstDayAvailable->format('Y-m-d'))));
            }
            $this->formData['removalDate'] = Tools::getValue('removalDate');
        } catch (Exception $e) {
            $this->error[] = GeodisServiceTranslation::get(
                'Admin.Removal.index.error.invalidDate'
            );
        }

        if (empty($this->error)) {
            return $this->saveRemovalRequest();
        }
        return false;
    }

    public function saveRemovalRequest()
    {
        $idAccount = Tools::getValue('account');
        $idPrestation = Tools::getValue('prestation');

        $removal = new GeodisRemoval();
        $removal->reference = $this->generateReference();
        $removal->removal_date = Tools::getValue('removalDate');
        $removal->id_site = (int) Tools::getValue('removalAddress');
        $removal->id_account = (int) $idAccount;
        $removal->id_prestation = (int) $idPrestation;
        $removal->number_of_box = (int) Tools::getValue('nbBox');
        $removal->number_of_pallet = (int) Tools::getValue('nbPallet');
        $removal->weight = Tools::getValue('weight');
        $removal->volume = Tools::getValue('volume');
        $removal->observations = Tools::getValue('observations');
        $removal->sent = 1;
        $removal->is_hazardous = (int) Tools::getValue('reglemented_transport');
        if ($removal->is_hazardous) {
            $removal->fiscal_code = (int) Tools::getValue('fiscalCode');
            $removal->legal_volume = round((float)Tools::getValue('legalVolume') / (float) 1000, 4);
            $removal->total_volume = (float) Tools::getValue('totalVolume');
        }

        $removal->time_slot = Tools::getValue('timeSlot');

        try {
            $removal->validateFields(); // Validate fields
            $removal->recept_number = GeodisServiceWebservice::getInstance()->sendRemoval($removal);
            $removal->save();
            $this->success = true;
        } catch (Exception $e) {
            $message = $e->getMessage();
            $position = strpos($message, 'com.geodis');

            if ($position !== false) {
                $message = Tools::substr($message, 0, $position);
            }

            $this->error[] = $message;
            return false;
        }

        return true;
    }

    public function generateReference()
    {
        $reference = array();
        $list = "ABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $size = Tools::strlen($list) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $size);
            $reference[$i] = $list[$n];
        }

        return implode($reference);
    }

    public function extractData($tabIds)
    {
        return explode("-", $tabIds);
    }

    public function getAccountPrestation()
    {
        $collectionQuery = new DbQuery();
        $collectionQuery->select('*');
        $collectionQuery->from(GEODIS_NAME_SQL.'_account', 'a');
        $collectionQuery->innerJoin(
            GEODIS_NAME_SQL.'_account_prestation',
            'ap',
            'ap.id_account = a.id_account'
        );
        $collectionQuery->innerJoin(
            GEODIS_NAME_SQL.'_prestation',
            'p',
            'p.id_prestation = ap.id_prestation AND p.type_service = "ENLEVT.SUR.SITE"'
        );

        $select = Db::getInstance()->executeS($collectionQuery);

        $res = array();
        foreach ($select as $row) {
            $res[] = array(
                'id_prestation' => (int) $row['id_prestation'],
                'id_account' => (int) $row['id_account'],
                'name' => $row['libelle'].' - '.$row['code_produit'],
                'volumeRequired' => $row['zone'] != 'France',
            );
        }

        return $res;
    }

    protected function getPrestation()
    {
        $res = array();
        foreach (GeodisPrestation::getCollection() as $item) {
            $res[] = array(
                'value' => $item->id,
                'name' => $item->libelle.' - '.$item->code_produit,
            );
        }

        return $res;
    }

    protected function getAccount()
    {
        $collectionQuery = new DbQuery();
        $collectionQuery->select('*');
        $collectionQuery->from(GEODIS_NAME_SQL.'_account', 'a');
        $collectionQuery->innerJoin(
            GEODIS_NAME_SQL.'_account_prestation',
            'ap',
            'ap.id_account = a.id_account'
        );
        $collectionQuery->innerJoin(
            GEODIS_NAME_SQL.'_prestation',
            'p',
            'p.id_prestation = ap.id_prestation AND p.type_service = "ENLEVT.SUR.SITE"'
        );
        $collectionQuery->innerJoin(
            GEODIS_NAME_SQL.'_site',
            'ca',
            'ca.id_site = a.id_customer_account'
        );
        $collectionQuery->groupBy('a.id_account');

        $select = Db::getInstance()->executeS($collectionQuery);

        $res = array();
        foreach ($select as $row) {
            $res[] = array(
                'value' => $row['id_account'],
                'name' => $row['code_sa'] . ' / ' . $row['code_client'] . ' - '. $row['name'],
            );
        }

        return $res;
    }

    public function getHistory()
    {
        $toDate = new DateTime();
        $toDate->add(new DateInterval("P1M"));
        $collection = GeodisRemoval::getCollection();
        $collection->where('removal_date', '<=', $toDate->format('Y-m-d h:m:s'));
        $collection->orderBy('removal_date', "DESC");

        $history = array();
        foreach ($collection as $removal) {
            $removal->removal_date = Tools::displayDate($removal->removal_date, null, false);
            $removal->date_add = Tools::displayDate($removal->date_add, null, true);
            $site = new GeodisSite($removal->id_site);
            $prestation = new GeodisPrestation($removal->id_prestation);
            $history[] = array(
                'siteName' => $site->name,
                'siteZipCode' => $site->zip_code,
                'siteCity' => $site->city,
                'prestationName' => $prestation->libelle,
                'removal' => $removal,
                'printUrl' => $this->context->link->getAdminLink(GEODIS_ADMIN_PREFIX.'Removal')
                .'&action=print&idRemoval='.$removal->id
            );
        }
        return $history;
    }

    public function processPrint()
    {
        $idRemoval = (int) Tools::getValue('idRemoval');
        $removal = new GeodisRemoval($idRemoval);

        try {
            $pdf = GeodisServiceWebservice::getInstance()->getRemovalDetail(array($removal->recept_number));
            $pdf = Tools::substr($pdf, 0, strpos($pdf, '%%EOF') + 5);

            $pdfFile = 'removal-' . $idRemoval . '.pdf';
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $pdfFile . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . Tools::strlen($pdf, "ASCII"));
            die($pdf);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }
}
