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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataCountry.php';

class GeodisDataMetaAccount
{
    public $type;
    public $code = 0;
    public $nom;
    public $codeTiers = "";
    public $indTelephoneFixe = '';
    public $telephoneFixe;
    public $indTelephoneMobile = '';
    public $telephoneMobile = '';
    public $fax = '';
    public $email;
    public $noVoie = '';
    public $libelleVoie = '';
    public $adresse1;
    public $adresse2 = "";
    public $codePostal;
    public $ville;
    public $pays;
    public $codePorte = "";
    public $latitude = null;
    public $longitude = null;
    public $instructionsLivraison = "";
    public $instructionsEnlevement = null;
    public $nomContact = '';
    public $telephoneContact = '';
    public $eaDestinataire = '';
    public $typePreinfo = null;
    public $typeDestinataire = "";
    public $adresseRetour = null;
    public $marque = null;
    public $defaut = null;
    public $listDisponibilites = null;

    public function hydrate($idAccount, $codeSa, $idOrder)
    {
        $geodisSite = new GeodisSite($idAccount);

        $this->type = $geodisSite->type;
        $this->code = (int) $geodisSite->code;
        $this->nom =  $geodisSite->name;
        $this->codeTiers = $codeSa;
        $this->telephoneFixe =  $geodisSite->telephone;
        $this->email =  $geodisSite->email;
        $this->adresse1 =  $geodisSite->address1;
        $this->adresse2 =  $geodisSite->address2;
        $this->codePostal =  $geodisSite->zip_code;
        $this->ville  = $geodisSite->city;
        $this->pays = (new GeodisDataCountry())->hydrate($geodisSite->id_country);
        return $this;
    }
}
