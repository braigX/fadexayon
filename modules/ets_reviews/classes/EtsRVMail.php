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

class EtsRVMail extends EtsRVCore
{
    const ETS_RV_MODULE_NAME = 'ets_reviews';

    public static function send(
        $idLang,
        $template,
        $subject = null,
        $templateVars = array(),
        $to = null,
        $toName = null,
        $push_to_queue = true,
        $id_customer = 0,
        $employee = 0,
        $product_id = 0,
        $id_shop = 0,
        $product_comment_id = 0,
        $cart_rule_id = 0,
        $id_order = 0,
        $schedule = false
    )
    {
        if (is_array($to)) {
            if (isset($to['customer'])) {
                $toMail = $to['customer'];
                if (!$idLang) {
                    if (($customers = Customer::getCustomersByEmail($toMail)) && ($customer = $customers[0]) && ($lang = new Language($customer['id_lang'])) && $lang->id)
                        $idLang = $lang->id;
                }
            } elseif (isset($to['employee'])) {
                $toMail = $to['employee'];
                if (!$idLang) {
                    $employeeObj = new Employee();
                    if (($employee = $employeeObj->getByEmail($toMail)) && ($lang = new Language($employee->id_lang)) && $lang->id)
                        $idLang = $employee->id_lang;
                }
            } else
                return false;
        } else
            $toMail = $to;
        if (!$toMail)
            return false;
        $ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        if (EtsRVUnsubscribe:: isUnsubscribe($toMail)) {
            return true;
        } else
            $templateVars['{unsubscribe}'] = Context::getContext()->link->getModuleLink(self::ETS_RV_MODULE_NAME, 'unsubscribe', array('email' => $toMail, 'verify' => EtsRVTools::encrypt($toMail)), $ssl);
        if (!$idLang)
            $idLang = Context::getContext()->language->id;
        if (!$idLang)
            $idLang = Configuration::get('PS_LANG_DEFAULT');

        //Build to subject:
        if ($subject == null) {
            $subject = EtsRVEmailTemplate::getSubjectByLangShop($template, $idLang, $id_shop);
            if (!$subject)
                return false;
        } elseif (is_array($subject) && isset($subject['origin']) && isset($subject['translation']) && isset($subject['specific'])) {

            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $locale = isset($lang) ? $lang->locale : Language::getLocaleByIso(Language::getIsoById($idLang));
                $subject = Translate::getModuleTranslation(self::ETS_RV_MODULE_NAME, $subject['origin'], $subject['specific'], isset($subject['params']) ? $subject['params'] : null, false, $locale);
            } else {
                $subject = self::trans($subject['origin'], isset($lang) ? $lang : $idLang, $subject['specific']);
            }
            if (!$subject)
                $subject = $subject['translation'];
        } elseif (is_array($subject))
            return false;

        $res = true;
        $guid = sha1(uniqid());
        $templateVars['{tracking}'] = Context::getContext()->link->getModuleLink(self::ETS_RV_MODULE_NAME, 'image', ['uid' => $guid], $ssl);
        $templateVars['{logo_img}'] = self::getShopLogoMail($id_shop);

        if (isset($templateVars['{product_name}']))
            $subject = str_replace('{product_name}', $templateVars['{product_name}'], $subject);
        if (isset($templateVars['{object}']))
            $subject = str_replace('{object}', $templateVars['{object}'], $subject);
        if (isset($templateVars['{customer_name}']))
            $subject = str_replace('{customer_name}', $templateVars['{customer_name}'], $subject);
        if (isset($templateVars['{from_person_name}']))
            $subject = str_replace('{from_person_name}', $templateVars['{from_person_name}'], $subject);
        if (isset($templateVars['{shop_name}']))
            $subject = str_replace('{shop_name}', $templateVars['{shop_name}'], $subject);
        if (isset($templateVars['{voucher_value}']))
            $templateVars['{discount_value}'] = $templateVars['{voucher_value}'];
        // Push to queue:
        if ($push_to_queue) {
            $content = null;
            if (isset($templateVars['{content}'])) {
                $content = $templateVars['{content}'];
                unset($templateVars['{content}']);
            }
            $queue = new EtsRVEmailQueue();
            $queue->id_lang = $idLang;
            $queue->id_shop = $id_shop;
            $queue->template = $template;
            $queue->subject = $subject;
            $queue->content = $content;
            $queue->template_vars = json_encode($templateVars);
            $queue->to_email = $toMail;
            $queue->to_name = $toName;
            $queue->id_customer = $id_customer;
            $queue->employee = $employee;
            if ($schedule) {
                $scheduleTime = trim(Configuration::getGlobalValue('ETS_RV_CRONJOB_SCHEDULE_TIME'));
                if ($scheduleTime !== '') {
                    $queue->schedule_time = time() + (int)$scheduleTime * 86400;
                }
            }
            $res &= $queue->add();
        }
        // Add tracking:
        $tracking = new EtsRVTracking();
        $tracking->id_shop = $id_shop;
        $tracking->id_customer = $id_customer;
        $tracking->employee = $employee;
        $tracking->product_comment_id = $product_comment_id;
        $tracking->product_id = $product_id;
        $tracking->ip_address = ($ip_address = Tools::getRemoteAddr()) && $ip_address == '::1' ? '127.0.0.1' : $ip_address;
        $tracking->email = $toMail;
        $tracking->guid = $guid;
        $tracking->is_read = 0;
        $tracking->delivered = $push_to_queue ? 0 : 1;
        $tracking->subject = $subject;
        $tracking->id_order = $id_order;
        if (isset($queue)) {
            $tracking->queue_id = $queue->id;
        }
        $res &= $tracking->add();
        if ($tracking->id > 0 && $cart_rule_id > 0) {
            $res &= EtsRVTracking::trackingDiscount($tracking->id, $cart_rule_id);
        }
        // Not push to queue then run sendmail now:
        if (!$push_to_queue) {
            EtsRVTools::getInstance()->replaceShortCode($templateVars, $idLang);
            $res &= Mail::send(
                $idLang,
                $template,
                $subject,
                $templateVars,
                $toMail,
                $toName,
                null,
                null,
                null,
                null,
                dirname(__FILE__) . '/../mails/',
                false,
                $id_shop
            );
        }

        return $res;
    }

    public static function getShopLogoMail($id_shop = null)
    {
        if ($id_shop == null)
            $id_shop = Context::getContext()->shop->id;
        if (Configuration::get('PS_LOGO_MAIL') !== false && @file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $id_shop)))
            $logo = Configuration::get('PS_LOGO_MAIL', null, null, $id_shop);
        elseif (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $id_shop)))
            $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        else
            $logo = '';
        $base = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' . Context::getContext()->shop->domain_ssl : 'http://' . Context::getContext()->shop->domain;
        return $logo !== '' ? $base . Context::getContext()->shop->getBaseURI() . 'img/' . $logo : '';
    }
}