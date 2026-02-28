<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'teamwant_redis/autoload.php';

use Teamwant\Prestashop17\Redis\Classes\Versions\TeamwantRedisVersion;

class Teamwant_Redis extends Module
{
    use TeamwantRedisVersion;

    public function __construct()
    {
        $this->name = 'teamwant_redis';
        $this->tab = 'others';
        $this->version = '3.6.10';
        $this->author = 'Mateusz Szymański Teamwant';
        $this->need_instance = 1;
        $this->bootstrap = false;

        parent::__construct();

        $this->displayName = $this->l('Redis Cache');
        $this->description = $this->l('Support Redis Cache for Prestashop');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Redis support?');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->module_key = '9c440d93f1967f82305e8c3aa739beed';

        if (Module::isEnabled($this->name)) {
            $this->validateFilePrivilagesForTeamwantRedisModule();
        }

        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
            $this->prestashopVersion = 800;
        } else {
            $this->prestashopVersion = 1780;
        }
    }
}
