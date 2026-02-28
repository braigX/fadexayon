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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestation.php';

class GeodisDataPrestation
{
    public $codeGroupeProduits;
    public $codeProduit;
    public $codeOption;
    public $type;
    public $libelle;

    public function hydrate($idPrestation)
    {
        $prestation = new GeodisPrestation($idPrestation);
        $this->codeGroupeProduits = $prestation->code_groupe_produits;
        $this->codeProduit = $prestation->code_produit;
        $this->codeOption = $prestation->code_option;
        $this->type = $prestation->type;
        $this->libelle = $prestation->libelle;
        return $this;
    }
}
