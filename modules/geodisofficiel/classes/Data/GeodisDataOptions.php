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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisOption.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCartCarrier.php';

class GeodisDataOptions
{
    public $enregistrementExpediteur =  false;
    public $enregistrementDestinataire =  false;
    public $notificationExpediteur =  false;
    public $notificationDestinataireParEmail = false;
    public $notificationDestinataireParSms = false;
    public $notificationConfirmationEnlevement =  false;
    public $emailConfirmationEnlevement = '';
    public $notificationPriseEnChargeEnlevement = false;
    public $emailPriseEnChargeEnlevement = '';
    public $valeurDeclaree =  0;
    public $contreRemboursement =  null;
    public $deviseContreRemboursement = array(
        'code' => '',
        'libelle' => '',
        'defaut'  => true,
        'typeMontant' => null,
        'montantMax' => null,
    );
    public $deviseValeurDeclaree = array(
        'code' => '',
        'libelle' => '',
        'defaut' => true,
        'typeMontant' => null,
        'montantMax' => null,
    );
    public $incotermConditionLivraison = null;
    /*array(
        'code' => 'P',
        'libelle' => 'PORT PAYE',
        'defaut' => true,
    );*/
    public $natureMarchandises = '';
    public $sadLivEtage = false;
    public $sadMiseLieuUtil = false;
    public $sadDepotage = false;
    public $etage = 0;
    public $impressionEtiquette  = false;

    public function hydrate($idOrder, $destinataire, $isPrestationEurope)
    {
        $order = new Order($idOrder);
        $currency = new Currency($order->id_currency);
        $this->deviseContreRemboursement['code'] = $currency->iso_code;
        $this->deviseContreRemboursement['libelle'] = $currency->name;
        $this->deviseValeurDeclaree['code'] = $currency->iso_code;
        $this->deviseValeurDeclaree['libelle'] = $currency->name;
        $geodisCartCarrier = GeodisCartCarrier::getCollection();
        $geodisCartCarrier->where('id_cart', '=', $order->id_cart);
        $cartCarrier = $geodisCartCarrier->getFirst();

        if ($destinataire->email != null && $destinataire->email != '') {
            $this->notificationDestinataireParEmail = true;
        }
        if ($destinataire->telephoneMobile != null && $destinataire->telephoneMobile != '') {
            $this->notificationDestinataireParSms = true;
        }

        if ($cartCarrier) {
            $infos = json_decode($cartCarrier->info);
            foreach ($infos as $info) {
                switch ($info->name) {
/*                    case 'email':
                        $this->notificationDestinataireParEmail = true;
                        break;
                    case 'mobilephone':
                        $this->notificationDestinataireParSms = true;
                        break;
*/                    case 'floor':
                        $this->etage = (int) $info->value;
                        break;
                }
            }

            $options = $cartCarrier->id_option_list;
            $optionsTab = explode(',', $options);
            foreach ($optionsTab as $optionId) {
                $option = new GeodisOption($optionId);
                switch ($option->code) {
                    case 'depotage':
                        $this->sadLivEtage = true;
                        break;
                    case 'livEtage':
                        $this->sadMiseLieuUtil = true;
                        break;
                    case 'miseLieuUtil':
                        $this->sadDepotage = true;
                        break;
                }
            }
        }

        if ($isPrestationEurope) {
            $this->incotermConditionLivraison = array(
                'code' => 'DAP');
        } else {
            $this->incotermConditionLivraison = array(
                'code' => 'P');
        }

        unset($order, $currency, $geodisCartCarrier, $cartCarrier, $infos, $options, $optionsTab);
        return $this;
    }
}
