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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCartCarrier.php';

class GeodisDataRecipient extends GeodisDataMetaAccount
{
    public function __contruct()
    {
        parent::__construct();
    }

    public function hydrate($idAccount, $codeSa, $idOrder)
    {
        $order = new Order($idOrder);
        $customer = new Customer($order->id_customer);
        $address = new Address($order->id_address_delivery);
        $charReplaceTel = array(".", "-", "/", " ");

        if (Tools::strlen($address->lastname." ".$address->firstname) <= 35) {
            $this->nomContact =  $address->lastname." ".$address->firstname;
        } else {
            $this->nomContact = Tools::strlen($address->lastname, 0, 35);
        }

        if ($address->company != null && $address->company != '') {
            $this->typeDestinataire = "PRO";
            $this->nom = Tools::substr($address->company, 0, 35);
        } else {
            $this->typeDestinataire = "PAR";
            $this->nom = $this->nomContact;
        }

        $this->code= -1;
        $this->type = "D";
        $this->codeTiers = "";
        $this->telephoneFixe = str_replace($charReplaceTel, "", $address->phone);
        $this->email = $customer->email;
        $this->adresse1 = Tools::substr($address->address1, 0, 35);
        $this->adresse2 = Tools::substr($address->address2, 0, 35);
        $this->codePostal = $address->postcode;
        $this->ville  = Tools::strtoupper($address->city);
        $this->pays = (new GeodisDataCountry())->hydrate($address->id_country);
        $this->telephoneContact = $this->telephoneFixe;

        //instructions
        $this->instructionsLivraison = $address->other;
        $phone = $address->phone;
        $phoneMobile = str_replace($charReplaceTel, "", $address->phone_mobile);
        $flagMobileOk = false;
        if ($this->pays->code == 'FR') {
            $phone = self::formatFrenchPhoneNumber($phone, true);
            $phoneMobile = self::formatFrenchPhoneNumber($phoneMobile, true);
            $typeNumero = Tools::substr($address->phone, Tools::strlen($address->phone)-9, 1);
            if (($typeNumero=='6') || ($typeNumero=='7')) {
                $phoneMobile = $address->phone;
                $flagMobileOk = true;
                $phone = '';
            }
            $longNumero = Tools::strlen($address->phone_mobile);
            $typeNumero = Tools::substr($address->phone_mobile, $longNumero-9, 1);
            if (!(($typeNumero=='6') || ($typeNumero=='7'))) {
                $phone = $address->phone_mobile;
                $phoneMobile = $flagMobileOk ? $phoneMobile : '';
            }
        }
        // "N° Telephone Fixe du contact" - maxlength: 25 - ps_address.phone
        $this->telephoneFixe = $phone;
        // "N° Telephone Mobile du contact" - maxlength: 25 - ps_address.phone_mobile
        $this->telephoneMobile = $phoneMobile;

        $geodisCartCarrier = GeodisCartCarrier::loadFromIdCart($order->id_cart);
        if ($geodisCartCarrier) {
            $infos = json_decode($geodisCartCarrier->info);
            foreach ($infos as $info) {
                switch ($info->name) {
                    case 'dialCodeTelephone':
                        $this->indTelephoneFixe = $info->value;
                        break;
                    case 'nationalTelephone':
                        $this->telephoneFixe = str_replace(' ', '', $info->value);
                        break;
                    case 'dialCodeMobilephone':
                        $this->indTelephoneMobile = $info->value;
                        break;
                    case 'nationalMobilephone':
                        $this->telephoneMobile = str_replace(' ', '', $info->value);
                        break;
                    case 'email':
                        $this->email = $info->value;
                        break;
                    case 'digicode':
                        $this->codePorte = $info->value;
                        break;
                    case 'instruction':
                        $this->instructionsLivraison = $info->value;
                        break;
                }
            }
        }
        if ($this->telephoneFixe != null) {
            $this->telephoneFixe = str_replace($charReplaceTel, "", $this->telephoneFixe);
        }
        if ($this->telephoneMobile != null) {
            $this->telephoneMobile = str_replace($charReplaceTel, "", $this->telephoneMobile);
        }
        if ($this->instructionsLivraison != null) {
            $this->instructionsLivraison = Tools::substr($this->instructionsLivraison, 0, 110);
        }

        unset($order, $customer, $address, $phone, $phoneMobile, $typeNumero, $longNumero, $geodisCartCarrier);
        return $this;
    }

    public static function formatFrenchPhoneNumber($phoneNumber, $international = false)
    {
        //Supprimer tous les caracteres qui ne sont pas des chiffres
        $phoneNumber = preg_replace('/[^0-9]+/', '', $phoneNumber);
        //Garder les 9 derniers chiffres
        $phoneNumber = Tools::substr($phoneNumber, -9);
        //On ajoute 33 si la variable $international vaut true et 0 dans tous les autres cas
        $motif = $international ? '33': '';
        $phoneNumber = $motif.$phoneNumber;
        return $phoneNumber;
    }
}
