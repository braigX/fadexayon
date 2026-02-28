<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class AdminTopbannerController extends ModuleAdminController
{
    /** @var Topbanner */
    public $module;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function ajaxProcessChangeState()
    {
        $id_banner = (int) Tools::getValue('id_banner', 0);
        $state = (int) Tools::getValue('state', 0);
        if ($state == 1) {
            $this->module->disableAll();
        }

        $query = 'UPDATE ' . _DB_PREFIX_ . 'topbanner SET status = ' . (int) $state . ' WHERE id_banner = ' . (int) $id_banner;
        exit(Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query));
    }

    public function ajaxProcessPreview()
    {
        $topbanner = new Topbanner();

        $params = [];
        parse_str(Tools::getValue('data'), $params);

        $paramsFormatted = [];
        foreach ($params as $key => $value) {
            $newKey = str_replace('topbanner_banner_', '', $key);

            if ($newKey == 'id') {
                $newKey = 'id_banner';
            }

            $paramsFormatted[$newKey] = $value;
        }

        exit($topbanner->previewBanner($paramsFormatted, $this->context->language->iso_code));
    }
}
