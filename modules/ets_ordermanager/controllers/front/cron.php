<?php
/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2023 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
	exit;
class Ets_ordermanagerCronModuleFrontController extends ModuleFrontController
{
    public function __construct()
	{
		parent::__construct();
	}
    public function postProcess()
    {
        
        if (Configuration::getGlobalValue('ETS_ODE_USE_CRONJOB')) {
            $secure = Tools::getValue('secure');
            if ($secure == Configuration::getGlobalValue('ETS_ODE_CRONJOB_TOKEN')) {
                $this->module->_runCronJob();
            }
            if(Configuration::getGlobalValue('ETS_ODE_SAVE_CRONJOB_TOKEN'))
                Tools::error_log(date('Y-m-d H:i:s')." Secure token is not valid\n",3,_PS_ETS_ODE_LOG_DIR_ . '/ode_cronjob.log');
            if(Tools::isSubmit('ajax'))
            {
                die(
                    json_encode(
                        array(
                            'error' => $this->module->l('Secure token is not valid','cron'),
                        )
                    )
                );
            }
            die($this->module->l('Secure token is not valid','cron'));
        }
        if(Configuration::getGlobalValue('ETS_ODE_SAVE_CRONJOB_TOKEN'))
            Tools::error_log(date('Y-m-d H:i:s')." Cronjob is disabled\n",3,_PS_ETS_ODE_LOG_DIR_ . '/ode_cronjob.log');
        if(Tools::isSubmit('ajax'))
        {
            die(
                json_encode(
                    array(
                        'error' => $this->module->l('Cronjob is disabled','cron'),
                    )
                )
            );
        }
        die($this->module->l('Cronjob is disabled','cron'));
    }
 }