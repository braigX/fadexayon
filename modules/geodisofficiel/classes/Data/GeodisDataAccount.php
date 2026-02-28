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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataAgency.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Data/GeodisDataCustomerAccount.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisAccount.php';

class GeodisDataAccount
{
    public $codeSa;
    public $codeClient;
    public $agence;
    public $compteClient;
    public $isSuiviTemp = false;
    public $isNotifDestiTemp = false;
    public $seuilMin = 0;
    public $seuilMax = 0;
    public $listPrestationsCommerciales = null;

    public function hydrate($idAccount)
    {
        $account = new GeodisAccount($idAccount);
        $this->codeSa = $account->code_sa;
        $this->codeClient = $account->code_client;
        $this->agence = (new GeodisDataAgency())->hydrate($account->id_agency, $this->codeSa, null);
        $this->compteClient = (new GeodisDataCustomerAccount())
             ->hydrate($account->id_customer_account, $this->codeClient, null);

        return $this;
    }
}
