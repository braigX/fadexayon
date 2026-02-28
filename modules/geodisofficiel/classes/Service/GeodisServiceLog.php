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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisLog.php';

class GeodisServiceLog
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new GeodisServiceLog();
        }

        return self::$instance;
    }

    public function dev($message)
    {
        if (!_PS_MODE_DEV_) {
            return;
        }

        $this->log($message);
    }

    public function log($message)
    {
        $geodisLog = new GeodisLog();
        $geodisLog->message = $message;
        $geodisLog->is_error = 0;
        $geodisLog->save();
    }

    public function error($message)
    {
        $geodisLog = new GeodisLog();
        $geodisLog->message = $message;
        $geodisLog->is_error = 1;
        $geodisLog->save();
    }

    public function purge()
    {
        $delay = GeodisServiceConfiguration::getInstance()->get('purge_delay');

        $triggerDate = new DateTime();
        $triggerDate->sub(new DateInterval('P'.(int) $delay.'D'));

        $sql = 'DELETE FROM `'._DB_PREFIX_.GEODIS_NAME_SQL.'_log` 
                WHERE date_add < "'.pSql($triggerDate->format('Y-m-d')).'"';
        Db::getInstance()->execute($sql);
    }
}
