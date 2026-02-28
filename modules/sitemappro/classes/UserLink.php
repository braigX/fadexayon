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

class UserLink extends ObjectModelSMP
{
    /**
     * @var string|string[]
     */
    public $link;
    /**
     * @var string
     */
    public $priority;
    /**
     * @var string
     */
    public $changefreq;

    /**
     * @var bool
     */
    public $is_active = 1;

    public static $definition = array(
        'table'             => 'user_link',
        'primary'           => 'id_user_link',
        'multilang' => true,
        'multishop' => array('type' => 'shop'),
        'multilang_shop' => true,
        'fields'            => array(
            'link' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'lang' => true),
            'priority' => array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isUnsignedFloat'),
            'changefreq' => array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isString')
        )
    );

    public static function getAll($id_lang = null, $include_link = null, $id_user_link = null)
    {
        $query = new DbQuery();
        array_map(array($query, 'select'), array(
            'ul.`id_user_link`',
            'ull.`link`'
        ));

        $query->from('user_link', 'ul');
        $query->leftJoin('user_link_lang', 'ull', 'ull.`id_user_link` = ul.`id_user_link`');

        if (!is_null($id_lang)) {
            $query->where('ull.`id_lang` = '.(int)$id_lang);
        } else {
            $query->where(
                'ull.`id_lang` IN('.implode(
                    ',',
                    array_map(
                        'intval',
                        Language::getLanguages(true, false, true)
                    )
                ).')'
            );
        }

        $query->where('ull.`id_shop` = '.(int)Shop::getContextShopID());

        if (Shop::isFeatureActive()) {
            array_map(array($query, 'select'), array(
                'uls.`priority`',
                'uls.`changefreq`'
            ));
            $query->leftJoin('user_link_shop', 'uls', 'uls.`id_user_link` = ul.`id_user_link`');
            $query->where('uls.`id_shop` = '.(int)Shop::getContextShopID());
        } else {
            array_map(array($query, 'select'), array(
                'ul.`priority`',
                'ul.`changefreq`'
            ));
        }

        if (!is_null($id_user_link)) {
            $query->where('ul.`id_user_link` = '.(int)$id_user_link);
        }

        $result = Db::getInstance()->executeS($query->build());

        if (is_array($result) && count($result)) {
            if (is_null($id_user_link)) {
                $nb_languages = count(ToolsModuleSMP::getLanguages(true));
                foreach ($result as &$item) {
                    if ($item['priority']== "1.000000") {
                        $item['priority'] = "1.0";
                    }
                    if ($nb_languages > 1 && $include_link) {
                        $item['links'] = array();
                        foreach (self::getAll(null, null, $item['id_user_link']) as $link) {
                            $item['links'][] = $link['link'];
                        }
                    } else {
                        $item['links'] = array();
                    }
                }
            }

            return $result;
        }

        return array();
    }

    public static function getCollection($as_array = false)
    {
        $result = Db::getInstance()->executeS(
            'SELECT ul.`id_user_link` FROM '._DB_PREFIX_.'user_link ul
            LEFT JOIN '._DB_PREFIX_.'user_link_shop uls
            ON ul.`id_user_link` = uls.`id_user_link`
            WHERE uls.`id_shop` = '.(int)Shop::getContextShopID()
        );

        if (is_array($result) && count($result)) {
            $items = array();
            foreach ($result as $item) {
                $object = new self($item['id_user_link']);
                if ($as_array) {
                    $items[] = $object->toArray();
                } else {
                    $items[] = $object;
                }
            }
            return $items;
        }

        return array();
    }
}
