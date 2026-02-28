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

class GeodisTabs
{
    protected $tabs;
    protected $module;

    public static function getInstance($module)
    {
        return new GeodisTabs($module);
    }

    public function __construct($module)
    {
        $this->tabs = $module->getTabs();
        $this->module = $module->name;
    }

    protected function getTab($controller)
    {
        $collection = new PrestaShopCollection('Tab');

        $collection->where('class_name', '=', $controller);

        $tab = $collection->getFirst();
        if (!$tab) {
            $tab = new Tab();
        }
        $tab->class_name = $controller;
        $tab->module = $this->module;

        return $tab;
    }

    public function update()
    {
        $position = 0;
        foreach ($this->tabs as $tab) {
            $object = $this->getTab($tab['class_name']);

            if (Tools::strtoupper($tab['ParentClassName']) == $tab['ParentClassName']) {
                continue;
            }

            $parent = $this->getTab($tab['ParentClassName']);
            if (!$parent->id) {
                throw new Exception('Unanow tab "'.$tab['ParentClassName'].'"');
            }
            $object->id_parent = $parent->id;
            $object->active = (isset($tab['visible'])) ? (bool) $tab['visible'] : true;
            $object->position = ++$position;

            if (isset($tab['icon'])) {
                $object->icon = $tab['icon'];
            }

            foreach (Language::getLanguages() as $lang) {
                $object->name[$lang['id_lang']] = $tab['name'];
            }

            $object->save();
        }
    }
}
