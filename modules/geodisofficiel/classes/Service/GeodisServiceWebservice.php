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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceLog.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataRemoval.php';

class GeodisServiceWebservice
{
    protected static $instance = null;
    protected $uri;
    protected $login;
    protected $secretKey;
    protected $lang;
    protected $debug;
    protected $accountPrestationCache = array();

    public static function getInstance($login = null, $secretKey = null)
    {
        if (!self::$instance) {
            self::$instance = new GeodisServiceWebservice($login, $secretKey);
        }

        return self::$instance;
    }

    public function __construct($login = null, $secretKey = null)
    {
        if ($login != null && $secretKey != null && $this->login == null && $this->secretKey == null) {
            $this->login = $login;
            $this->secretKey = $secretKey;
        } else {
            $this->login = GeodisServiceConfiguration::getInstance()->get('api_login');
            $this->secretKey = GeodisServiceConfiguration::getInstance()->get('api_secret_key');
        }
        $this->uri = GEODIS_API_URI;
        $this->lang = Context::getContext()->language->iso_code;
        if ($this->secretKey == null || strcmp($this->secretKey, '') == 0
            || $this->login == null || strcmp($this->login, '') == 0) {
            GeodisServiceLog::getInstance()->error('Module GEODIS - Parameters login and/or secretKey are empty.');
            throw new Exception('Module GEODIS - Parameters login and/or secretKey are empty.');
        }
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    public function resetLang()
    {
        $this->lang = Context::getContext()->language->iso_code;
    }

    public function send($service, $body, $returnRaw = false)
    {
        if ($this->secretKey == null || strcmp($this->secretKey, '') == 0
        || $this->login == null || strcmp($this->login, '') == 0) {
            GeodisServiceLog::getInstance()->error('WebService GEODIS - Parameters login and/or secretKey are empty.');
            throw new Exception('WebService GEODIS - Parameters login and/or secretKey are empty.');
        }
        $inlineBody = json_encode($body);

        $timestamp = (time() * 1000);

        $message = $this->secretKey.';'.$this->login.';'.$timestamp.';'.$this->lang.';'.$service.';'.$inlineBody;
        $hash = hash('sha256', $message);
        $serviceRequestHeader = $this->login.';'.$timestamp.';'.$this->lang.';'.$hash;

        // Set header and define the content type (json)
        $headers = array(
            'X-GEODIS-Service: '.$serviceRequestHeader,
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: '.Tools::strlen($inlineBody),
        );

        $ch = curl_init();

        // Url of the service
        curl_setopt($ch, CURLOPT_URL, $this->uri.$service);

        // Define custom headers
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $headers
        );

        // POST query
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        // Body
        curl_setopt($ch, CURLOPT_POSTFIELDS, $inlineBody);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout de 500ms
        curl_setopt(
            $ch,
            CURLOPT_CONNECTTIMEOUT_MS,
            GEODIS_API_TIMEOUT
        );

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_VERBOSE, _PS_MODE_DEV_);

        // curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V6);

        $rawResult = curl_exec($ch);

        if (_PS_MODE_DEV_) {
            $info = curl_getinfo($ch);
            $this->debug = $info;
        }

        $logFilePath = _PS_MODULE_DIR_ . 'geodisofficiel/debug_log.txt';
		
        if ($rawResult === false) {
			$errorMessage = curl_error($ch);
    		file_put_contents($logFilePath, "CURL Error: " . $errorMessage . "\n", FILE_APPEND);
    
            GeodisServiceLog::getInstance()->dev("$service\nNo response");
            GeodisServiceLog::getInstance()->error('Webservice cannot be reached or invalid api query.');
            throw new Exception('Webservice not available.');
        } else {
			file_put_contents($logFilePath, "Server Response: " . $rawResult . "\n", FILE_APPEND);
		}

        if ($returnRaw) {
            GeodisServiceLog::getInstance()->dev("$service\n$inlineBody");
            return $rawResult;
        } else {
            GeodisServiceLog::getInstance()->dev("$service\n$inlineBody\n$rawResult");
        }

        return json_decode($rawResult, true);
    }

    public function getDebug()
    {
        return $this->debug;
    }

    public function getCustomerConfiguration()
    {
        $body = array(
            'codePlateforme' => 'ECP',
            'infoModule' => GEODIS_MODULE_NAME.' PrestaShop V'.GEODIS_MODULE_VERSION,
        );

        return $this->send(
            'api/ecommerce/recherche-parametrage-client',
            $body
        );
    }

    public function getGeneralConfiguration()
    {
        $body = array(
            'codePlateforme' => 'ECP',
            'infoModule' => GEODIS_MODULE_NAME.' PrestaShop V'.GEODIS_MODULE_VERSION,
        );

        return $this->send(
            'api/ecommerce/recherche-parametrage-general',
            $body
        );
    }

    public function getWithdrawalPoint(
        $countryCode,
        $zipCode,
        $city,
        $latitude,
        $longitude,
        $prestationType
    ) {
        $body = array(
            'codePays' => $countryCode,
            'codePostal' => $zipCode,
            'localite' => $city,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'typePrestation' => $prestationType,
        );

        return $this->send(
            'api/ecommerce/recherche-points-relais',
            $body
        );
    }

    public function getWithdrawalAgency(
        $countryCode,
        $zipCode,
        $city,
        $latitude,
        $longitude,
        $prestationType
    ) {
        $body = array(
            'codePays' => $countryCode,
            'codePostal' => $zipCode,
            'localite' => $city,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'typePrestation' => $prestationType,
        );

        return $this->send(
            'api/ecommerce/recherche-agences-retrait',
            $body
        );
    }

    public function getDepartureCalendar(
        $codeProductGroup,
        $codeOption,
        $codeExpeditionCountry,
        $codeProduct,
        $codeSa
    ) {
        $body = array(
            'codeGroupeProduits' => $codeProductGroup,
            'codeOption' => $codeOption,
            'codePaysExped' => $codeExpeditionCountry,
            'codeProduit' => $codeProduct,
            'codeSa' => $codeSa
        );

        return $this->send(
            'api/ecommerce/recherche-calendrier-depart',
            $body
        );
    }

    public function getRemovalCalendar(
        $codeProductGroup,
        $codeOption,
        $codeExpeditionCountry,
        $codeProduct,
        $codeSa
    ) {
        $body = array(
            'codeGroupeProduits' => $codeProductGroup,
            'codeOption' => $codeOption,
            'codePaysExped' => $codeExpeditionCountry,
            'codeProduit' => $codeProduct,
            'codeSa' => $codeSa
        );

        return $this->send(
            'api/ecommerce/recherche-calendrier-enlevement',
            $body
        );
    }

    public function getAccountPrestation(
        $destinationCountryCode,
        $expeditionCountryCode,
        $numberOfPackages,
        $numberOfPallets,
        $weight,
        $destinationType
    ) {
        $body = array(
            'codePaysDesti' => $destinationCountryCode,
            'codePaysExped' => $expeditionCountryCode,
            'nbColis' => (int) $numberOfPackages,
            'nbPalettes' => (int) $numberOfPallets,
            'poids' => (float) $weight,
            'typeDesti' => $destinationType,
        );

        $key = md5(serialize($body));

        if (!isset($this->accountPrestationCache[$key])) {
            try {
                $this->accountPrestationCache[$key] = $this->send(
                    'api/ecommerce/recherche-prestations-comptes',
                    $body
                );
            } catch (Exception $e) {
                if ($e instanceof Exception) {
                    throw $e;
                }
            }
        }

        return $this->accountPrestationCache[$key];
    }

    public function sendShipmentRecord($jsonObject)
    {
        return $this->send(
            'api/ecommerce/enregistrement-preparation-cde',
            json_decode($jsonObject)
        );
    }

    public function sendShipment($receptList)
    {
        $body = array(
            'listRecepisse' => $receptList,
        );

        return $this->send(
            'api/ecommerce/validation-preparations-cdes',
            $body
        );
    }

    public function getPickupPointFromCarriers(
        $carrierCollection,
        $latitude,
        $longitude,
        $defaultLatitude,
        $defaultLongitude,
        $city = null,
        $zipCode = null,
        $countryCode = null
    ) {
        $prestationsGrouped = array(
            'agency' => array(),
            'point' => array(),
        );
        // Group prestations
        foreach ($carrierCollection as $carrier) {
            $prestation = $carrier->getPrestation();
            $type = $prestation->withdrawal_agency ? 'agency' : 'point';

            if (!isset($prestationsGrouped[$type][$prestation->type])) {
                $prestationsGrouped[$type][$prestation->type] = $carrier;
            }
        }

        $pointList = array();
        foreach ($prestationsGrouped as $withdrawalType => $data) {
            foreach ($data as $type => $carrier) {
                $newPoints = $this->getPickupPoint(
                    $withdrawalType,
                    $latitude,
                    $longitude,
                    $defaultLatitude,
                    $defaultLongitude,
                    $city,
                    $zipCode,
                    $countryCode,
                    $type
                );

                foreach ($newPoints as $point) {
                    // Do not replace point. Exception for EXP
                    if ($this->pointsExists($point, $pointList) && $type != 'EXP') {
                        continue;
                    }

                    $point['idCarrier'] = (int) $carrier->id;
                    $pointList[] = $point;
                }
            }
        }

        usort(
            $pointList,
            function ($a, $b) {
                if ($a['distance'] == $b['distance']) {
                    return 0;
                }

                return $a['distance'] < $b['distance'] ? -1 : 1;
            }
        );

        return $pointList;
    }

    protected function pointsExists($point, $pointList)
    {
        $code = $point['code'];
        foreach ($pointList as $point) {
            if ($point['code'] == $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * $type 'point', 'agency', 'both'
     */
    public function getPickupPoint(
        $type,
        $latitude,
        $longitude,
        $defaultLatitude,
        $defaultLongitude,
        $city = null,
        $zipCode = null,
        $countryCode = null,
        $prestationType = 'EXP'
    ) {
        $pickupPointList = array();

        if ($type == 'point' || $type == 'both') {
            $result = $this->getWithdrawalPoint(
                $countryCode,
                $zipCode,
                $city,
                $latitude,
                $longitude,
                $prestationType
            );
            foreach ($result['contenu'] as $point) {
                $pickupPointList[] = $this->extractPointData($point, $defaultLatitude, $defaultLongitude, 'point');
            }
        }

        if ($type == 'agency' || $type == 'both') {
            $result = $this->getWithdrawalAgency(
                $countryCode,
                $zipCode,
                $city,
                $latitude,
                $longitude,
                $prestationType
            );
            foreach ($result['contenu'] as $point) {
                $pickupPointList[] = $this->extractPointData($point, $defaultLatitude, $defaultLongitude, 'agency');
            }
        }

        if ($defaultLatitude && $defaultLongitude) {
            usort(
                $pickupPointList,
                function ($a, $b) {
                    if ($a['distance'] == $b['distance']) {
                        return 0;
                    }

                    return $a['distance'] < $b['distance'] ? -1 : 1;
                }
            );
        }

        return $pickupPointList;
    }

    protected function formatHour($string)
    {
        if (!$string) {
            return;
        }

        $hour = new DateTime($string);
        return $hour->format('H:i');
    }

    protected function extractPointData($point, $latitude, $longitude, $type)
    {
        $openingTime = array();

        $daysOfWeek = array(
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        );
        foreach ($point['listDisponibilites'] as $day) {
            $openingTime[] = array(
                'day' => array_search($day['jour'], $daysOfWeek),
                'morningStart' => $this->formatHour($day['heureMatinDebut']),
                'morningStop' => $this->formatHour($day['heureMatinFin']),
                'eveningStart' => $this->formatHour($day['heureApresMidiDebut']),
                'eveningStop' => $this->formatHour($day['heureApresMidiFin']),
            );
        }

        return array(
            'latitude' => $point['latitude'],
            'longitude' => $point['longitude'],
            'distance' => $this->calculateDistance($latitude, $longitude, $point['latitude'], $point['longitude']),
            'code' => $point['codeTiers'],
            'name' => $point['nom'],
            'instructionsLivraison' => $point['instructionsLivraison'],
            'instructionsEnlevement' => $point['instructionsEnlevement'],
            'address1' => $point['adresse1'],
            'address2' => $point['adresse2'],
            'zipCode' => $point['codePostal'],
            'city' => $point['ville'],
            'countryCode' => $point['pays']['code'],
            'openingTime' => $openingTime,
            'type' => $type,
        );
    }

    protected function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        if (!$lat1 && !$lng1) {
            return '';
        }

        // Try to use google distance API
        $lastCallError = GeodisServiceConfiguration::getInstance()->get('google_api_distance_last_error_call');
        $triggerLastCallError = new DateTime();
        $triggerLastCallError->sub(new DateInterval('PT5M'));

        if (!$lastCallError) {
            $lastCallErrorDate = $triggerLastCallError;
        } else {
            $lastCallErrorDate = new DateTime($lastCallError);
        }

        if ($lastCallErrorDate <= $triggerLastCallError) {
            $apiKey = GeodisServiceConfiguration::getInstance()->get('google_map_api_key');
            $client = GeodisServiceConfiguration::getInstance()->get('google_map_client');

            if (!$apiKey) {
                $result = Tools::file_get_contents(
                    'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric'
                    .'&origins='.$lat1.','.$lng1
                    .'&destinations='.$lat2.','.$lng2
                    .'&key='.$apiKey
                );
            } elseif ($client) {
                $result = Tools::file_get_contents(
                    'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric'
                    .'&origins='.$lat1.','.$lng1
                    .'&destinations='.$lat2.','.$lng2
                    .'&client='.$client
                );
            } else {
                $result = false;
            }

            if ($result) {
                $jsonResult = json_decode($result);

                if ($jsonResult->status == 'OK') {
                    return (int) $jsonResult->rows[0]->elements[0]->distance->value;
                }

                GeodisServiceConfiguration::getInstance()->set(
                    'google_api_distance_last_error_call',
                    (new DateTime())->format('Y-m-d H:i:s')
                );
            }
        }

        $earthRadius = 6378137;
        $rlo1 = deg2rad($lng1);
        $rla1 = deg2rad($lat1);
        $rlo2 = deg2rad($lng2);
        $rla2 = deg2rad($lat2);
        $dlo = ($rlo2 - $rlo1) / 2;
        $dla = ($rla2 - $rla1) / 2;
        $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin($dlo));
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $meter = (int) ($earthRadius * $d);

        return $meter;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function getShipmentStatusUpdate($receptList)
    {
        $body = array(
            'listRecepisse' => $receptList,
        );

        return $this->send(
            'api/ecommerce/recherche-suivis',
            $body
        );
    }

    public function getDeliveryLabel($receptList)
    {
        $body = array(
            'listRecepisse' => $receptList,
        );

        return $this->send(
            'api/ecommerce/impression-bordereaux',
            $body,
            true
        );
    }

    public function getPackageLabel($receptList)
    {
        $body = array(
            'listRecepisse' => $receptList,
        );

        return $this->send(
            'api/ecommerce/impression-etiquettes',
            $body,
            true
        );
    }

    public function sendRemoval($removal)
    {
        $removalData = new GeodisDataRemoval();
        $removalData->hydrate($removal);

        $result =  $this->send(
            'api/ecommerce/enregistrement-demande-enl',
            $removalData
        );

        if (!$result['ok']) {
            throw new Exception($result['codeErreur'].' - '.$result['texteErreur']);
        }

        return $result['contenu']['listNoRecepisse'][0];
    }

    public function getRemovalDetail($receptList)
    {
        $body = array(
            'listRecepisse' => $receptList,
        );

        return $this->send(
            'api/ecommerce/impression-recapitulatifs',
            $body,
            true
        );
    }
}
