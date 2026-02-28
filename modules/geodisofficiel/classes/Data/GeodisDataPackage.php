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

class GeodisDataPackage
{
    public $hauteurUnitaire;
    public $largeurUnitaire;
    public $longueurUnitaire;
    public $poidsUnitaire;
    public $quantite = 1;
    public $referenceColis;
    public $referenceEtiquette = '';
    public $uniteManutention = array(
        'code' => '',
        'codeUMRegroupement' => '',
        'defaut' => true,
        'hauteur' => 0,
        'isPalette' => '',
        'isPaletteConsignee' => false,
        'largeur' => 0,
        'libelle'  => '',
        'longueur' => 0,
        'poids' => 0,
        'listUMsStd' => array(),
    );

    public function hydrate($idPackage)
    {
        $package = new GeodisPackage($idPackage);
        $this->hauteurUnitaire = (int) $package->height;
        $this->largeurUnitaire = (int) $package->width;
        $this->longueurUnitaire = (int) $package->depth;
        $this->poidsUnitaire = round($package->weight, 2);
        $this->referenceColis = $package->reference;
        if ($package->package_type == "box") {
            $this->uniteManutention['code'] = 'PC';
            $this->uniteManutention['libelle'] = 'Colis';
            $this->uniteManutention['isPalette'] = false;
        } else {
            $this->uniteManutention['code'] = 'PE';
            $this->uniteManutention['libelle'] = 'Palette(s) spécifique(s)';
            $this->uniteManutention['isPalette'] = true;
        }
        return $this;
    }
}
