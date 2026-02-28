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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataAccount.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataPackage.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataAgency.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataSender.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataRecipient.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataOptions.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataWl.php';

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisFiscalCode.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCartCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipment.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisGroupCarrier.php';

class GeodisDataShipment
{
    protected static $instance = null;

    public $codeProvenanceWs = "ECP";
    public $codeService = "PREPA.EXPE";
    public $prestationCommerciale;
    public $compte;
    public $expediteur;
    public $destinataire;
    public $pointRelais = null;
    public $agenceBureauRestant = null;
    public $dateDepartOuEnlevement;
    public $dateDepartOuEnlevementFrs = '';
    public $periodePreferenceEnlevement = '';
    public $listUMs = null;
    public $qteUniteTaxation = 1;
    public $uniteTaxation = null;
    public $isMatiereDangereuse = false;
    public $regimeADRMD = false;
    public $poidsADRMD = 0;
    public $regimeQteLimiteeMD = false;
    public $poidsQteLimiteeMD = "";
    public $dangerEnvQteLimiteeMD = false;
    public $regimeQteExcepteeMD = false;
    public $nbColisQteExcepteeMD = "";
    public $dangerEnvQteExcepteeMD = false;
    public $listMDs = array();
    public $nbTotalUM;
    public $nbColis = 0;
    public $nbPalettes = 0;
    public $poidsTotalUM = 0;
    public $volumeTotalUM = 0;
    public $longueurTotalUM = 0;
    public $largeurTotalUM = 0;
    public $hauteurTotalUM = 0;
    public $isVinSpiritueux = false;
    public $listVSs = null;
    public $optionLivraison = 'LIVPREV';
    public $dateLivraison = null;
    public $dateLivraisonFrs = null;
    public $creneauLivraison = '';
    public $reference1;
    public $reference2;
    public $options;
    public $noRecepisse = "";
    public $typePosition = 'S';
    public $tsCreation = "";
    public $tsCreationFrs = "";
    public $tsValidation = null;
    public $tsValidationFrs = '';
    public $isACorriger = false;
    public $isRegroupement = false;
    public $isRegroupee = false;
    public $noRecepisseRegr = "0";
    public $isEtiquetteImprimee = false;
    public $isModeCorrection = false;
    public $anomalieACorriger = null;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new GeodisDataShipment();
        }

        return self::$instance;
    }

    public function hydrate($shipment)
    {
        $order = new Order($shipment->id_order);
        $geodisCarrier = new GeodisCarrier($shipment->id_carrier);
        $packagesCollection = $shipment->getPackages();

        $this->listUMs = array();
        $this->listVSs = array();
        $this->isVinSpiritueux = false;
        foreach ($packagesCollection as $package) {
            $this->listUMs[] = (new GeodisDataPackage())->hydrate($package->id);
            $this->largeurTotalUM += $package->width;
            $this->longueurTotalUM += $package->depth;
            $this->hauteurTotalUM += $package->height;
            $this->poidsTotalUM += $package->weight;
            $this->volumeTotalUM += $package->volume;
            if ($package->package_type == 'box') {
                $this->nbColis++;
            } else {
                $this->nbPalettes++;
            }

            $geodisPackageOrderDetails = GeodisPAckageOrderDetail::getCollection();
            $geodisPackageOrderDetails->where('id_package', '=', $package->id);
            foreach ($geodisPackageOrderDetails as $orderDetail) {
                if (!$this->isVinSpiritueux && $orderDetail->is_wine_and_liquor) {
                    $this->isVinSpiritueux = true;
                }
                if ($orderDetail->is_wine_and_liquor) {
                    $fiscalCode = new GeodisFiscalCode($orderDetail->id_fiscal_code);
                    $this->listVSs[] = (new GeodisDataWl())->hydrate($fiscalCode, $orderDetail);
                }
            }
        }

        // Arrondi du poids
        if ($this->poidsTotalUM != null) {
            $weight = ceil($this->poidsTotalUM * 100) / 100;
        } else {
            $weight = 0;
        }
        $this->poidsTotalUM = $weight;

        $this->nbTotalUM = $this->nbPalettes + $this->nbColis;

        $prestation = new GeodisPrestation($geodisCarrier->id_prestation);
        $groupCarrier = new GeodisGroupCarrier($geodisCarrier->id_group_carrier);

        if ($groupCarrier->reference == 'rdv') {
            if ($prestation->web_appointment) {
                $this->optionLivraison = 'RDVWEB';
            } elseif ($prestation->tel_appointment) {
                $this->optionLivraison = 'RDVTEL';
            }
        }

        $this->prestationCommerciale = (new GeodisDataPrestation())
            ->hydrate($geodisCarrier->id_prestation);
        $this->compte = (new GeodisDataAccount())->hydrate($geodisCarrier->id_account);

        $idDefaultSite = null;
        $idLang = Context::getContext()->language->id;
        $geodisSiteCollection = GeodisSite::getcollection();
        foreach ($geodisSiteCollection as $geodisSite) {
            if ($geodisSite->default[$idLang] == 1) {
                $idDefaultSite = $geodisSite->id;
            }
        }

        $this->expediteur = (new GeodisDataSender())->hydrate($idDefaultSite, null, null);
        $this->destinataire = (new GeodisDataRecipient())->hydrate(null, null, $shipment->id_order);
        $this->dateDepartOuEnlevement = $shipment->departure_date;
        $dateTab = explode('-', $shipment->departure_date);
        if (count($dateTab)) {
            $this->dateDepartOuEnlevementFrs = $dateTab[2].'/'.$dateTab[1].'/'.$dateTab[0];
        }

        $this->reference1 = (new Order($shipment->id_order))->reference;
        $this->reference2 = $shipment->reference;

        $isPrestationEur = (strcmp($prestation->zone, GeodisPrestation::ZONE_EUROPE) == 0);
        $isRetrait = $groupCarrier->reference == 'relay';
        $this->options = (new GeodisDataOptions)->hydrate($shipment->id_order, $this->destinataire, $isPrestationEur);
        if (($isPrestationEur || ($isRetrait)) && (strcmp($this->optionLivraison, 'LIVPREV') == 0)) {
            $this->optionLivraison = 'CARTEDEL';
        }
        $this->noRecepisse = $shipment->recept_number;
        $this->tsCreation = '';
        $this->tsCreationFrs = '';

        if ($shipment->type_position != null) {
            $this->typePosition = $shipment->type_position;
        }

        $this->getWithdrawalPointOrAgency($order);

        return json_encode($this);
    }

    public function getWithdrawalPointOrAgency($order)
    {
        $geodisCartCarrierCollection = GeodisCartCarrier::getcollection();
        $geodisCartCarrierCollection->where('id_cart', '=', $order->id_cart);
        $geodisCartCarrier = $geodisCartCarrierCollection->getFirst();

        if ($geodisCartCarrier != null) {
            if ($geodisCartCarrier->code_withdrawal_point != null) {
                $infos = json_decode($geodisCartCarrier->info);

                foreach ($infos as $info) {
                    if ($info->name == "instructionsEnlevement") {
                        $instructions = $info->value;
                    }

                    if ($info->name == "address") {
                        $infoAddress = $info->value;
                        $nom = $infoAddress->name;
                        $adresse1 = $infoAddress->address1;
                        $adresse2 = $infoAddress->address2;
                        $codePostal = $infoAddress->zipCode;
                        $ville = $infoAddress->city;
                        $pays = $infoAddress->countryCode;
                    }
                }

                $this->pointRelais = array(
                    "code" => 0,
                    "nom" => $nom,
                    "adresse1" => $adresse1,
                    "adresse2" => $adresse2,
                    "codePostal" => $codePostal,
                    "ville" => $ville,
                    "instructionsEnlevement" => $instructions,
                    "pays" => array(
                        "code" => $pays,
                        "libelle" => $pays,
                        "indicatifTel" => "",
                        "formatTel" => "",
                        "formatCodePostal" => "",
                        "preInfoDestinataire" => true,
                        "controleLocalite" => true,
                        "exportControl" => false,
                        "defaut" => true
                    ),
                    "codeTiers" => $geodisCartCarrier->code_withdrawal_point,
                    "type" => "R",
                );
            }

            if ($geodisCartCarrier->code_withdrawal_agency != null) {
                $infos = json_decode($geodisCartCarrier->info);
                foreach ($infos as $info) {
                    if ($info->name == "instructionsEnlevement") {
                        $instructions = $info->value;
                    }

                    if ($info->name == "address") {
                        $infoAddress = $info->value;
                        $nom = $infoAddress->name;
                        $adresse1 = $infoAddress->address1;
                        $adresse2 = $infoAddress->address2;
                        $codePostal = $infoAddress->zipCode;
                        $ville = $infoAddress->city;
                        $pays = $infoAddress->countryCode;
                    }
                }

                $this->agenceBureauRestant = array(
                    "code" => 0,
                    "nom" => $nom,
                    "adresse1" => $adresse1,
                    "adresse2" => $adresse2,
                    "codePostal" => $codePostal,
                    "ville" => $ville,
                    "instructionsEnlevement" => $instructions,
                    "pays" => array(
                        "code" => $pays,
                        "libelle" => $pays,
                        "indicatifTel" => "",
                        "formatTel" => "",
                        "formatCodePostal" => "",
                        "preInfoDestinataire" => true,
                        "controleLocalite" => true,
                        "exportControl" => false,
                        "defaut" => true
                    ),
                    "codeTiers" => $geodisCartCarrier->code_withdrawal_agency,
                    "type" => "B"
                );
            }
        }
    }
}
