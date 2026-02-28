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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisFiscalCode.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataSender.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataAccount.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataPrestation.php';

class GeodisDataRemoval
{
    public $codeProvenanceWs = 'ECP';
    public $dateEnlevement;
    public $expediteur;
    public $isVinSpiritueux;
    public $listPrestationsComptes = array();
    public $nbColis;
    public $nbPalettes;
    public $observations;
    public $periodePreferenceEnlevement;
    public $poidsTotal;
    public $regimeFiscal;
    public $volumeEnDroits;
    public $volumeTotal;

    public function hydrate($removal)
    {
        $dataSender = new GeodisDataSender();
        $dataSender->hydrate($removal->id_site, '', null);

        $dataPrestation = new GeodisDataPrestation();
        $dataPrestation->hydrate($removal->id_prestation);

        $dataAccount = new GeodisDataAccount();
        $dataAccount->hydrate($removal->id_account);

        $this->codeProvenanceWs = 'ECP';
        $this->dateEnlevement = $removal->removal_date;
        $this->expediteur = $dataSender;
        $this->isVinSpiritueux = (bool) $removal->is_hazardous;
        $this->listPrestationsComptes = array(
            array(
                'prestationCommerciale' => $dataPrestation,
                'compte' => $dataAccount,
            ),
        );
        $this->nbColis = $removal->number_of_box;
        $this->nbPalettes = $removal->number_of_pallet;
        $this->observations = $removal->observations;
        if ($removal->time_slot == 'morning') {
            $this->periodePreferenceEnlevement = 'MAT';
        } elseif ($removal->time_slot == 'afternoon') {
            $this->periodePreferenceEnlevement = 'APM';
        } else {
            $this->periodePreferenceEnlevement = '';
        }
        $this->poidsTotal = $removal->weight;

        $geodisFiscalCode = new GeodisFiscalCode($removal->fiscal_code);
        $this->regimeFiscal = $geodisFiscalCode->label;
        $this->volumeEnDroits = $removal->legal_volume;
        $this->volumeTotal = $removal->total_volume;

        return $this;
    }
}
