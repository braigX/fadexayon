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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisSite.php';

abstract class GeodisControllerAdminAbstractMenu extends ModuleAdminController
{
    protected $isSynchronized = null;

    public function __construct()
    {
        parent::__construct();
        $this->initMenu();
    }

    protected function getCurrentController()
    {
        return get_class($this);
    }

    protected function initMenu()
    {
        $this->base_tpl_view = 'menu.tpl';

        if (!$this->isSynchronized()) {
            $install = new GeodisDbInstall();
            $install->run();
            unset($install);
    
            Context::getContext()->smarty->assign(
                'entries',
                array(
                    'home' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.module')
                            ->setDefault('My module'),
                        'class' => 'module',
                        'active' => $this->isActive('index'),
                        'link' => $this->getLink('index'),
                    ),
                    'information' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.information')
                            ->setDefault('My information'),
                        'class' => 'information',
                        'active' => $this->isActive('information'),
                        'link' => $this->getLink('information'),
                    ),
                )
            );
    
            $this->errors[] = GeodisServiceTranslation::get(
                '*.*.error.notSyncronized'
            );
            $this->tpl_view_vars['general_error'] = true;
        } else {
            Context::getContext()->smarty->assign(
                'entries',
                array(
                    'home' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.module')
                            ->setDefault('My module'),
                        'class' => 'module',
                        'active' => $this->isActive('index'),
                        'link' => $this->getLink('index'),
                    ),
                    'information' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.information')
                            ->setDefault('My information'),
                        'class' => 'information',
                        'active' => $this->isActive('information'),
                        'link' => $this->getLink('information'),
                    ),
                    'back' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.back')
                            ->setDefault('My back configuration'),
                        'class' => 'back',
                        'active' => $this->isActive('configurationBack'),
                        'link' => $this->getLink('configurationBack'),
                    ),
                    'front' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.front')
                            ->setDefault('My front configuration'),
                        'class' => 'front',
                        'active' => $this->isActive('configurationFront'),
                        'link' => $this->getLink('configurationFront'),
                    ),
                    'address' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.address')
                            ->setDefault('My addresses'),
                        'class' => 'address',
                        'active' => $this->isActive('address'),
                        'link' => $this->getLink('address'),
                    ),
                    'order' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.order')
                            ->setDefault('My orders'),
                        'class' => 'order',
                        'active' => $this->isActive(
                            array(
                                'ordersGrid',
                                'shipment',
                                'shipmentsGridPrint',
                                'shipmentsGridTransmit'
                            )
                        ),
                        'link' => $this->getLink('ordersGrid'),
                    ),
                    'removal' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.removal')
                            ->setDefault('My removals'),
                        'class' => 'removal',
                        'active' => $this->isActive('removal'),
                        'link' => $this->getLink('removal'),
                    ),
                    'log' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.log')
                            ->setDefault('My logs'),
                        'class' => 'log',
                        'active' => $this->isActive('logsGrid'),
                        'link' => $this->getLink('logsGrid'),
                    ),
                    'cronjob' => array(
                        'name' => GeodisServiceTranslation::get('*.*.menu.cron')
                            ->setDefault('CronJob'),
                        'class' => 'cron',
                        'active' => $this->isActive('cronGrid'),
                        'link' => $this->getLink('cronGrid'),
                    ),
                )
            );
            $this->tpl_view_vars['general_error'] = false;
        }

        $this->tpl_view_vars['menu'] = Context::getContext()->smarty->fetch(
            _PS_MODULE_DIR_.'geodisofficiel/views/templates/admin/_partial/menu.tpl'
        );
        $this->tpl_list_vars['menu'] = Context::getContext()->smarty->fetch(
            _PS_MODULE_DIR_.'geodisofficiel/views/templates/admin/_partial/menu.tpl'
        );
    }

    protected function getLink($controller)
    {
        $link = Context::getContext()->link;
        return $link->getAdminLink(
            'AdminGeodis'.Tools::ucfirst($controller)
        );
    }

    protected function isActive($controller)
    {
        if (is_array($controller)) {
            foreach ($controller as $c) {
                if ($this->isActive($c)) {
                    return true;
                }
            }
            return false;
        }
        return $this->getCurrentController() == GEODIS_ADMIN_PREFIX.Tools::ucfirst($controller).'Controller';
    }

    public function setMedia($isNewTheme = false)
    {
        $this->addCSS(_PS_MODULE_DIR_.'geodisofficiel//views/css/admin/menu.css');

        return parent::setMedia($isNewTheme);
    }

    public function isSynchronized()
    {
        if (is_null($this->isSynchronized)) {
            $this->isSynchronized = (bool) count(GeodisSite::getCollection());
        }

        return $this->isSynchronized;
    }
}
