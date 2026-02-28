{*
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from SARL Ether Création
* Use, copy, modification or distribution of this source file without written
* license agreement from the SARL Ether Création is strictly forbidden.
* In order to obtain a license, please contact us: contact@ethercreation.com
* ...........................................................................
* INFORMATION SUR LA LICENCE D'UTILISATION
*
* L'utilisation de ce fichier source est soumise a une licence commerciale
* concedee par la societe Ether Création
* Toute utilisation, reproduction, modification ou distribution du present
* fichier source sans contrat de licence ecrit de la part de la SARL Ether Création est
* expressement interdite.
* Pour obtenir une licence, veuillez contacter la SARL Ether Création a l'adresse: contact@ethercreation.com
* ...........................................................................
*  @package ec_seo
*  @copyright Copyright (c) 2010-2016 S.A.R.L Ether Création (http://www.ethercreation.com)
*  @author Arthur R.
*  @license Commercial license
*}

<div class="panel">
    <h3><i class="icon icon-refresh"></i> {l s='Refresh list' mod='ec_seo'}</h3>
    <p>
        {$txt2|escape:'htmlall':'UTF-8'}
    </p>
    <p>
        {$txt|escape:'htmlall':'UTF-8'}
    </p>
    <div class="panel-footer">
    <button id="configuration_form_submit_btn" class="refresh btn btn-default pull-right " name="cron" value="1" type="submit">
        <i class="process-icon-refresh"></i>
    {l s='Refresh' mod='ec_seo'}
    </button>
    </div>
</div>
