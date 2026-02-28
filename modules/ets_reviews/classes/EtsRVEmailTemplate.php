<?php
/**
 * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

class EtsRVEmailTemplate extends ObjectModel
{
    public $template;
    public $subject;
    public $content_html;
    public $content_html_full;
    public $content_txt;
    public $active = true;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ets_rv_email_template',
        'primary' => 'id_ets_rv_email_template',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'template' => array('type' => self::TYPE_STRING, 'validate' => 'isTplName', 'size' => 128),
            'active' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),

            /* Lang fields */
            'subject' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isMailSubject', 'size' => 500),
        ),
    );

    public static function deleteByIdShop($id_shop)
    {
        if (!$id_shop || !Validate::isUnsignedInt($id_shop)) {
            return false;
        }
        $res = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_email_template_lang` WHERE `id_shop`=' . (int)$id_shop);
        $res &= Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'ets_rv_email_template_shop` WHERE `id_shop`=' . (int)$id_shop);

        return $res;
    }

    public static function duplicateByIdShop($id_shop)
    {
        if (!$id_shop || !Validate::isUnsignedInt($id_shop)) {
            return false;
        }
        $idShopDefault = Configuration::get('PS_SHOP_DEFAULT');
        $res = Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_email_template_lang` 
            SELECT `id_ets_rv_email_template`, `id_lang`, ' . (int)$id_shop . ' AS `id_shop`, `subject` 
            FROM `' . _DB_PREFIX_ . 'ets_rv_email_template_lang` WHERE `id_shop`=' . (int)$idShopDefault
        );
        $res &= Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_email_template_shop` 
            SELECT `id_ets_rv_email_template`, ' . (int)$id_shop . ' AS `id_shop`, `active` 
            FROM `' . _DB_PREFIX_ . 'ets_rv_email_template_shop` WHERE `id_shop`=' . (int)$idShopDefault
        );

        return $res;
    }

    public static function getSubjectByLangShop($template, $id_lang, $id_shop, $active = true)
    {
        if (trim($template) == '' || !Validate::isTplName($template) || !Validate::isUnsignedInt($id_lang) || !Validate::isUnsignedInt($id_shop))
            return false;
        $dq = new  DbQuery();
        $dq
            ->select('l.subject')
            ->from('ets_rv_email_template', 'a')
            ->innerJoin('ets_rv_email_template_lang', 'l', 'l.id_ets_rv_email_template = a.id_ets_rv_email_template AND l.id_lang=' . (int)$id_lang . ' AND l.id_shop=' . (int)$id_shop)
            ->where('a.template=\'' . pSQL(trim($template)) . '\'');

        if (Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_ALL) {
            $dq
                ->innerJoin('ets_rv_email_template_shop', 's', 's.id_ets_rv_email_template=a.id_ets_rv_email_template AND s.id_shop=' . (int)$id_shop)
                ->where('s.active=' . (int)$active);
        } else
            $dq->where('a.active=' . (int)$active);

        return Db::getInstance()->getValue($dq);
    }

    public static function isEnabled($template, $id_shop = 0)
    {
        if (trim($template) == '' || !Validate::isTplName($template) || !Validate::isUnsignedInt($id_shop))
            return false;
        if ($id_shop == 0) {
            $id_shop = Context::getContext()->shop->id;
        }
        $dq = new  DbQuery();
        $dq
            ->select('a.id_ets_rv_email_template')
            ->from('ets_rv_email_template', 'a')
            ->where('a.template=\'' . pSQL(trim($template)) . '\'');

        if (Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_ALL) {
            $dq
                ->innerJoin('ets_rv_email_template_shop', 's', 's.id_ets_rv_email_template=a.id_ets_rv_email_template AND s.id_shop=' . (int)$id_shop)
                ->where('s.active=1');
        } else
            $dq->where('a.active=1');

        return (int)Db::getInstance()->getValue($dq) > 0;
    }
}