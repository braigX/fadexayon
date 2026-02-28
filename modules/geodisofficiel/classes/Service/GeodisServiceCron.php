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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceSynchronize.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceLog.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipment.php';

class GeodisServiceCron
{
    const HOURLY = 'PT1H';
    const DAILY = 'P1D';
    const WEEKLY = 'P1W';
    const MONHTLY = 'P1M';

    protected $cronTasks = array(
        array(
            'service' => 'GeodisServiceSynchronize',
            'method' => 'syncCustomerConfiguration',
            'id' => 'customer_synchronization_crontask',
            'frequency' => self::WEEKLY,
            'params' => array(),
        ),
        array(
            'service' => 'GeodisServiceSynchronize',
            'method' => 'syncShipmentStatus',
            'id' => 'shipment_synchronization_crontask',
            'frequency' => self::HOURLY,
            'params' => array(),
        ),
        array(
            'service' => 'GeodisServiceLog',
            'method' => 'purge',
            'id' => 'log_purge_crontask',
            'frequency' => self::DAILY,
            'params' => array(),
        ),
    );

    protected function getReceptNumberList()
    {
        $receptList = array();
        $geodisShipmentCollection = GeodisShipment::getListRecepMajStatus();
        foreach ($geodisShipmentCollection as $geodisShipment) {
            if ($geodisShipment->recept_number != null && !$geodisShipment->is_endlife) {
                $receptList[] = $geodisShipment->recept_number;
            }
        }

        return $receptList;
    }

    protected static $instance = null;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function run()
    {
        foreach ($this->cronTasks as $task) {
            $triggerDate = new DateTime();
            $triggerDate->sub(new DateInterval($task['frequency']));
            $lastCronDate = GeodisServiceConfiguration::getInstance()->get($task['id'], null, null, null, false);
            if ($lastCronDate) {
                $dateLastSynchronization = new DateTime($lastCronDate);
            } else {
                $dateLastSynchronization = $triggerDate;
            }

            if ($triggerDate >= $dateLastSynchronization) {
                $geodisServiceSynchronize = $task['service']::getInstance();
                if ($task['method'] == 'syncShipmentStatus') {
                    if (!empty($this->getReceptNumberList())) {
                        $geodisServiceSynchronize->{$task['method']}($this->getReceptNumberList());
                    } else {
                        break;
                    }
                } else {
                    $geodisServiceSynchronize->{$task['method']}($task['params']);
                }
                GeodisServiceConfiguration::getInstance()->set($task['id'], (new DateTime())->format('Y-m-d'));
            }
        }
    }
}
