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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceWebservice.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceLog.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisSite.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisAccount.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisAccountPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestationOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisRemoval.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisWSCapacity.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';

class GeodisServiceSynchronize
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function syncCustomerConfiguration($force = false)
    {
        $currentDate = new DateTime();
        $triggerDate = new DateTime();
        $triggerDate->sub(new DateInterval('PT4H'));

        $lastSynchronization = GeodisServiceConfiguration::getInstance()->get(
            'date_customer_synchronization',
            null,
            null,
            null,
            false
        );
        if ($lastSynchronization) {
            $dateLastSynchronization = new DateTime($lastSynchronization);
        } else {
            $dateLastSynchronization = $triggerDate;
        }

        if (!$force && $triggerDate < $dateLastSynchronization) {
            throw new Exception(
                (string) GeodisServiceTranslation::get('Admin.ServiceSynchronize.error.alreadySynchronize')
            );
        }

        $result = GeodisServiceWebservice::getInstance()->getCustomerConfiguration();
        if (!$result) {
            throw new Exception(
                (string) GeodisServiceTranslation::get('Admin.ServiceSynchronize.error.default')
            );
        }

        foreach ($result['contenu']['listSitesEnlevements'] as $site) {
            $this->processSite($site, true);
        }

        foreach ($result['contenu']['listServices'] as $service) {
            $typeService = $service['codeService'];

            foreach ($service['listComptes'] as $account) {
                $this->processAccount($account, $typeService);
            }
        }

        $result = GeodisServiceWebservice::getInstance()->getGeneralConfiguration();
        if (!$result) {
            throw new Exception(
                (string) GeodisServiceTranslation::get('Admin.ServiceSynchronize.error.default')
            );
        }
        foreach ($result['contenu']['listContenancesVS'] as $contenance) {
            $this->processWSCapacity($contenance);
        }

        $this->syncTranslations();

        $this->purge($currentDate);

        // Sync carriers name
        if (!GeodisServiceConfiguration::getInstance()->get('use_white_label')) {
            foreach ($this->getGroupCarrierCollection() as $group) {
                $carrier = $group->getCarrier();
                $carrier->name = $group->getDefaultName();
                $carrier->delay = $group->getDefaultDelay();
                $carrier->save();
            }
        }

        GeodisServiceConfiguration::getInstance()->set(
            'date_customer_synchronization',
            (new DateTime('now'))->format('Y-m-d H:i:s')
        );

        return true;
    }

    public function syncTranslations($login = false, $key = false)
    {
        $webservice = GeodisServiceWebservice::getInstance();

        if ($login && $key) {
            $webservice->setLogin($login);
            $webservice->setSecretKey($key);
        }

        foreach (Language::getLanguages() as $lang) {
            $webservice->setLang($lang['iso_code']);
            $result = $webservice->getGeneralConfiguration();

            if (!$result) {
                continue;
            }
            foreach ($result['contenu']['mapLibelles'] as $key => $value) {
                GeodisTranslation::set($key, $lang['id_lang'], $value, true);
            }
        }

        $webservice->resetLang();
    }

    public function getGroupCarrierCollection()
    {
        $groupCarrierCollection = GeodisGroupCarrier::getCollection();
        $groupCarrierCollection->where('id_reference_carrier', '>', 0);

        return $groupCarrierCollection;
    }

    public function syncShipmentStatus($listReceptNumber)
    {
        try {
            $result = GeodisServiceWebservice::getInstance()->getShipmentStatusUpdate($listReceptNumber);
            if (!$result) {
                throw new Exception(
                    (string) GeodisServiceTranslation::get('Admin.ServiceSynchronize.error.shipment.status.update')
                );
            }
        } catch (Exception $e) {
            GeodisServiceLog::getInstance()->log($e->getMessage());
            return true;
        }

        foreach ($result['contenu'] as $receptData) {
            $this->processShipmentUpdate($receptData);
        }

        return true;
    }

    protected function purge($triggerDate)
    {
        // 1. Remove not updated PrestationOption
        $prestationOptionCollection = GeodisPrestationOption::getCollection();
        $prestationOptionCollection->where('date_upd', '<', $triggerDate->format('Y-m-d H:i:s'));

        foreach ($prestationOptionCollection as $prestationOption) {
            $prestationOption->delete();
        }

        // 2. Remove not updated prestation
        $prestationCollection = GeodisPrestation::getCollection();
        $prestationCollection->where('date_upd', '<', $triggerDate->format('Y-m-d H:i:s'));

        foreach ($prestationCollection as $prestation) {
            // 2.1 Remove PrestationOption linked to the Prestation
            foreach (GeodisPrestationOption::getCollectionFromPrestation($prestation) as $prestationOption) {
                $prestationOption->delete();
            }

            // 2.2 Remove GeodisAccountPrestation linked to the Prestation
            foreach (GeodisAccountPrestation::getCollectionFromPrestation($prestation) as $accountPrestation) {
                $accountPrestation->delete();
            }

            // 2.3 Remove GeodisCarrier linked to Prestation
            foreach (GeodisCarrier::getCollectionFilterByPrestation($prestation) as $carrier) {
                // 2.3.1 Remove GeodisCarrierOption linked to Carrier
                foreach ($carrier->getCarrierOptionCollection() as $carrierOption) {
                    $carrierOption->delete();
                }

                $carrier->deleted = true;
                $carrier->save();
            }

            $prestation->deleted = true;
            $prestation->save();
        }

        // 3. Remove not updated ws capacities
        $wsCapacityCollection = GeodisWSCapacity::getCollection();
        $wsCapacityCollection->where('date_upd', '<', $triggerDate->format('Y-m-d H:i:s'));
        foreach ($wsCapacityCollection as $wsCapacity) {
            $wsCapacity->delete();
        }

        // 4. Remove not updated account_prestation
        $accountPrestationCollection = GeodisAccountPrestation::getCollection();
        $accountPrestationCollection->where('date_upd', '<', $triggerDate->format('Y-m-d H:i:s'));

        foreach ($accountPrestationCollection as $accountPrestation) {
            $accountPrestation->delete();
        }

        // 5. Remove not updated account
        $accountCollection = GeodisAccount::getCollection();
        $accountCollection->where('date_upd', '<', $triggerDate->format('Y-m-d H:i:s'));

        foreach ($accountCollection as $account) {
            // 5.1 Remove GeodisCarrier linked to the Account
            $carrierCollection = new PrestaShopCollection('GeodisCarrier');
            $carrierCollection->where('id_account', '=', $account->id);

            foreach ($carrierCollection as $carrier) {
                // 5.1.1 Remove GeodisCarrierOption linked to the Carrier
                foreach ($carrier->getCarrierOptionCollection() as $option) {
                    $option->delete();
                }

                // 5.1.2 Remove GeodisCartCarrier linked to the Carrier
                $cartCarrierCollection = GeodisCartCarrier::getCollection();
                $cartCarrierCollection->where('id_carrier', '=', $carrier->id);
                foreach ($cartCarrierCollection as $cartCarrier) {
                    $cartCarrier->delete();
                }

                $carrier->delete();
            }

            // 5.2 Remove GeodisRemoval linked to the Account
            $removalCollection = GeodisRemoval::getCollection();
            $removalCollection->where('id_account', '=', $account->id);
            foreach ($removalCollection as $removal) {
                $removal->delete();
            }

            // 5.3 Remove GeodisAccountPrestation linked to the Account
            foreach (GeodisAccountPrestation::getCollectionFromAccount($account) as $accountPrestation) {
                $accountPrestation->delete();
            }

            $account->delete();
        }

        // 6. Remove not updated site
        $siteCollection = GeodisSite::getCollection();
        $siteCollection->where('date_upd', '<', $triggerDate->format('Y-m-d H:i:s'));

        foreach ($siteCollection as $site) {
            $site->delete();
        }
    }

    protected function processAccount($account, $typeService)
    {
        $object = GeodisAccount::getFromExternal(
            $account['codeSa'],
            $account['codeClient']
        );

        $agency = $this->processSite($account['agence']);
        $customerAccount = $this->processSite($account['compteClient']);

        $object->id_agency = $agency->id;
        $object->id_customer_account = $customerAccount->id;

        $object->save();

        foreach ($account['listPrestationsCommerciales'] as $prestation) {
            $this->processPrestation($prestation, $object, $typeService);
        }
    }

    protected function processPrestation($prestation, $account, $typeService)
    {
        if (empty($prestation['libelle'])) {
            return;
        }

        $object = GeodisPrestation::getFromExternal(
            $prestation['codeGroupeProduits'],
            $prestation['codeProduit'],
            $prestation['codeOption'],
            $account->code_sa,
            $account->code_client,
            $typeService,
            false
        );

        $object->type = $prestation['type'];
        $object->libelle = $prestation['libelle'];
        $object->deleted = false;
        $object->code_sa = $account->code_sa;
        $object->code_client = $account->code_client;

        if (array_key_exists('rdvWeb', $prestation) && $prestation['rdvWeb'] == true) {
            $object->web_appointment = 1;
        }

        if (array_key_exists('rdvTel', $prestation) && $prestation['rdvTel'] == true) {
            $object->tel_appointment = 1;
        }

        if (array_key_exists('bureauRestant', $prestation) && $prestation['bureauRestant'] == true) {
            $object->withdrawal_agency = true;
        }
        if (array_key_exists('pointRelais', $prestation) && $prestation['pointRelais'] == true) {
            $object->withdrawal_point = true;
        }

        $object->zone = ($prestation['europe'])? "Europe": "France";
        $object->save();

        $linkObject = GeodisAccountPrestation::get(
            $account->id,
            $object->id
        );

        foreach (GeodisOption::getCollection() as $option) {
            if (isset($prestation[$option->attribute])) {
                $prestationOption = GeodisPrestationOption::getFromPrestationAndOption($object, $option);

                if (!$prestationOption) {
                    $prestationOption = new GeodisPrestationOption();
                    $prestationOption->id_prestation = $object->id;
                    $prestationOption->id_option = $option->id;
                }
                $prestationOption->active = (bool) $prestation[$option->attribute];

                $prestationOption->save();
            }
        }

        $linkObject->manage_wine_and_liquor = $prestation['vinsSpiritueux'];
        $linkObject->save();
    }

    protected function processSite($site, $removal = false)
    {
        if (!$site['codeTiers']) {
            $code = $site['code'];
        } else {
            $code = $site['codeTiers'];
        }

        $object = GeodisSite::getFromExternal(
            $site['type'],
            $code
        );

        $alreadySynchronized = GeodisServiceConfiguration::getInstance()->get(
            'date_customer_synchronization',
            null,
            null,
            null,
            false
        );
        if (!$alreadySynchronized) {
            if (array_key_exists('defaut', $site) && $site['defaut'] == true) {
                $value = array();
                foreach (Language::getLanguages() as $lang) {
                    $value[$lang['id_lang']] = true;
                }

                $object->default = $value;
            }
        }

        $object->name = $site['nom'];
        $object->email = $site['email'];
        $object->telephone = $site['telephoneFixe'];
        $object->address1 = $site['adresse1'];
        $object->address2 = $site['adresse2'];
        $object->zip_code = $site['codePostal'];
        $object->city = $site['ville'];
        $object->id_country = (int) Country::getByIso($site['pays']['code']);
        $object->removal = $removal;

        $object->save();

        return $object;
    }

    protected function processWSCapacity($contenance)
    {
        $object = GeodisWSCapacity::getFromExternal(
            $contenance
        );
        $object->save();

        return $object;
    }

    protected function processShipmentUpdate($receptData)
    {
        $object = GeodisShipment::getFromExternal(
            $receptData['noRecepisse']
        );

        $defaultStatus = (string) GeodisServiceTranslation::get(
            'Admin.ServiceSynchronize.shipment.statusLabel.waitingTransmission'
        );

        if (!empty($receptData['listSuivisEnvois']) && $receptData['listSuivisEnvois'][0]['incident'] == true) {
            $geodisLog = new GeodisLog();
            $geodisLog->is_error = true;
            $geodisLog->message = (string) GeodisServiceTranslation::get(
                'Admin.ServiceSynchronize.error.shipment.event'
            );
            $geodisLog->save();
            $object->incident = 1;
        }

        $object->status_code = 1;
        $object->tracking_number = isset($receptData['noSuivi']) ? $receptData['noSuivi'] : '';
        $object->tracking_url =  $receptData['urlSuiviDestinataire'];

        if ($object->status_label == $defaultStatus && $object->is_complete) {
            $object->status_label = (string) GeodisServiceTranslation::get(
                'Admin.ServiceSynchronize.shipment.statusLabel.transmitted'
            );
        }

        if (count($receptData['listSuivisEnvois'])) {
            $object->status_label = $receptData['listSuivisEnvois'][0]['libelleCourtSuivi'];
        }

        if ($receptData['finDeVie'] != null) {
            $object->is_endlife = $receptData['finDeVie'] == true;
        }

        $object->save();

        $history = $object->getHistory();
        $lastUpdateDatetime = null;
        if ($lastRecord = $history->getFirst()) {
            $lastUpdateDatetime = new DateTime($lastRecord->event_date);
        }

        foreach ($receptData['listSuivisEnvois'] as $event) {
            $eventDateTime = new DateTime($event['dateSuivi'].' '.$event['heureSuivi']);
            if ($eventDateTime > $lastUpdateDatetime || $lastUpdateDatetime == null) {
                $history = new GeodisShipmentHistory();
                $history->id_shipment = $object->id;
                $history->status_code = 1; // TODO: idem status code
                $history->status_label = $event['libelleCourtSuivi'];
                $history->event_date = $event['dateSuivi'].' '.$event['heureSuivi'];
                $history->event_place = $event['libelleCentre'];
                $history->event_trace = $event['libelleSuivi'];
                $history->event_infos = '';
                foreach ($event['listInformationsComplementaires'] as $infos) {
                    $history->event_infos .= ','.$infos;
                }
                $history->save();
            }
        }
        return true;
    }
}
