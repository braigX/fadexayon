<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class ObjectModelSMP extends ObjectModel
{
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        if (isset(static::$definition['multishop']) && !Shop::isTableAssociated(static::$definition['table'])) {
            Shop::addTableAssociation(static::$definition['table'], static::$definition['multishop']);
        }
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function getImage()
    {
        if ($this->checkImage()) {
            return $this->getPathImage();
        }
        return false;
    }

    public function uploadImage($tmp_name)
    {
        if (!$this->id) {
            return false;
        }
        if ($tmp_name) {
            if ($this->checkImage()) {
                $this->deleteImg();
            }
            if (ToolsModuleSMP::checkImage($tmp_name)) {
                $width = null;
                $height = null;
                if (property_exists($this, 'image_size')) {
                    $width = $this->{'image_size'}[0];
                    $height = $this->{'image_size'}[1];
                }
                ImageManager::resize($tmp_name, $this->getFullPathImage(), $width, $height);
            }
            return true;
        }
        return false;
    }

    public function getFullPathImage()
    {
        return _PS_MODULE_DIR_.ToolsModuleSMP::getModNameForPath(__FILE__)
            .'/views/img/'.Tools::strtolower($this->getClassName()).'/'.(int)$this->id.'.jpg';
    }

    public function getPathImage()
    {
        return _MODULE_DIR_.ToolsModuleSMP::getModNameForPath(__FILE__)
            .'/views/img/'.Tools::strtolower($this->getClassName()).'/'.(int)$this->id.'.jpg';
    }

    public function checkImage()
    {
        return file_exists($this->getFullPathImage());
    }

    public function deleteImg()
    {
        if ($this->checkImage()) {
            unlink($this->getFullPathImage());
        }
    }

    public function getClassName()
    {
        return 'object_model';
    }

    public function toArray()
    {
        $array = array(
            'id' => $this->id
        );
        foreach (static::$definition['fields'] as $field_name => $field) {
            unset($field);
            if (Tools::substr($field_name, 0, 4) == 'ids_') {
                $field_name = Tools::substr($field_name, 4);
            }
            $method = 'toArray'.Tools::toCamelCase($field_name, true);
            if (method_exists($this, $method)) {
                $array[$field_name] = $this->{$method}();
            } else {
                $array[$field_name] = $this->{$field_name};
            }
        }
        return $array;
    }
}
