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

if (!defined('_PS_VERSION_'))
    exit;
function upgrade_module_2_0_5()
{
    if ($shops = Shop::getShops()){
        $shops[] = array('id_shop' => null);
        foreach ($shops as $shop){
            Configuration::updateValue('ETS_CTF7_ENABLE_HOOK_SHORTCODE','0',false,null,(int)$shop['id_shop']);
            Configuration::updateValue('ETS_CTF7_RECAPTCHA_TYPE','v2',false,null,(int)$shop['id_shop']);
        }
    }
    $res = true;
    $res = ct7_check_colum('ets_ctf_contact', 'thank_you_active', 'INT(1) NOT NULL AFTER `hook`');
    $res &= ct7_check_colum('ets_ctf_contact', 'thank_you_page', 'VARCHAR(255) CHARACTER SET utf8 NOT NULL AFTER `thank_you_active`');

    $res &= ct7_check_colum('ets_ctf_contact_lang', 'message_email_black_list', 'TEXT CHARACTER SET utf8 NOT NULL AFTER `message_ip_black_list`');
    $res &= ct7_check_colum('ets_ctf_contact_lang', 'thank_you_page_title', 'VARCHAR(255) CHARACTER SET utf8 NOT NULL AFTER `message_ip_black_list`');
    $res &= ct7_check_colum('ets_ctf_contact_lang', 'thank_you_message', 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `thank_you_page_title`');
    $res &= ct7_check_colum('ets_ctf_contact_lang', 'thank_you_alias', 'VARCHAR(100) CHARACTER SET utf8 NOT NULL AFTER `thank_you_message`');
    $res &= ct7_check_colum('ets_ctf_contact_lang', 'thank_you_url', 'VARCHAR(255) CHARACTER SET utf8 NOT NULL AFTER `thank_you_message`');

    update_database();
    return $res;
}

if ( ! function_exists('ct7_check_colum')){
    function ct7_check_colum($table, $column, $suffix)
    {
        return Db::getInstance()->execute('
            SET @dbname = DATABASE();
            SET @tablename = "' . _DB_PREFIX_ . pSQL($table) . '";
            SET @columnname = "' . pSQL($column) . '";
            SET @suffix = "' . pSQL($suffix) . '";
            SET @preparedStatement = (SELECT IF(
            (
                SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                WHERE
                  (table_name = @tablename)
                  AND (table_schema = @dbname)
                  AND (column_name = @columnname)
                ) > 0,
                "SELECT 1",
                CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname," ", @suffix)
            ));
            PREPARE alterIfNotExists FROM @preparedStatement;
            EXECUTE alterIfNotExists;
            DEALLOCATE PREPARE alterIfNotExists;
        ');
    }
}

if (!function_exists('update_database')){
    function update_database(){
        DB::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_ctf_contact` SET `thank_you_active`=1,`thank_you_page`="thank_page_default" ');
        DB::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_ctf_contact_lang` SET `thank_you_page_title`="Thanks for submitting the form",
                                        `thank_you_message`="<p>Thank you for contacting us.</p> <p>This message is to confirm that you have successfully submitted the contact form.</p> <p>We\'ll get back to you shortly.</p>",
                                         `thank_you_alias` = "thanks-for-submitting-the-form" ');

        return true;
    }
}