<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class SeoInternalLinkingModel extends ObjectModel
{
    public $id_seointernallinking;
    public $title;
    public $active;
    public $rel;
    public $types;
    public $color;
    public $url;
    public $keywords;
    public $replacements;
    public $target;

    public static $definition = [
        'table' => 'seointernallinking',
        'primary' => 'id_seointernallinking',
        'multilang' => true,
        'fields' => [
            'id_seointernallinking' => ['type' => self::TYPE_INT],
            'active' => ['type' => self::TYPE_BOOL],
            'color' => ['type' => self::TYPE_STRING, 'required' => false],
            'title' => ['type' => self::TYPE_STRING, 'lang' => true],
            'url' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true],
            'keywords' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true],
            'types' => ['type' => self::TYPE_STRING],
            'rel' => ['type' => self::TYPE_INT],
            'replacements' => ['type' => self::TYPE_INT, 'required' => false],
            'target' => ['type' => self::TYPE_INT, 'required' => false],
        ],
    ];

    public function add($autoDate = true, $nullValues = false)
    {
        $types = ['index', 'cms', 'category', 'product'];
        $selected_types = [];
        foreach ($types as $type) {
            $val = Tools::getValue('types_' . $type);
            if ($val && !empty($val)) {
                array_push($selected_types, $val);
            }
        }
        if (!empty($selected_types)) {
            $selected_types = implode(',', $selected_types);
            $this->types = $selected_types;
        }

        return parent::add($autoDate, $nullValues);
    }

    public function update($nullValues = false)
    {
        $types = ['index', 'cms', 'category', 'product'];
        $selected_types = [];
        foreach ($types as $type) {
            $val = Tools::getValue('types_' . $type);
            if ($val && !empty($val)) {
                array_push($selected_types, $val);
            }
        }
        if (!empty($selected_types)) {
            $selected_types = implode(',', $selected_types);
            $this->types = $selected_types;
        }

        return parent::update($nullValues);
    }

    public static function getActiveRule($page, $id_lang, $id_shop)
    {
        return Db::getInstance()->executeS('SELECT a.*, l.`title`, l.`keywords`, l.`url`
            FROM `' . _DB_PREFIX_ . 'seointernallinking` a
            LEFT JOIN `' . _DB_PREFIX_ . 'seointernallinking_shop` b
            ON (a.`id_seointernallinking` = b.`id_seointernallinking`)
            LEFT JOIN `' . _DB_PREFIX_ . 'seointernallinking_lang` l
            ON (a.`id_seointernallinking` = l.`id_seointernallinking` AND l.`id_lang` = ' . (int) $id_lang . ')
            WHERE a.`types` LIKE (\'%' . pSQL($page) . '%\')
            AND a.`active` > 0
            AND b.`id_shop` = ' . (int) $id_shop);
    }
}
