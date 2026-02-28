<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innovadeluxe SL
* @copyright 2016 Innovadeluxe SL

* @license   INNOVADELUXE
*/

function upgrade_module_1_1_4($module)
{
    include(dirname(__FILE__) . '/../sql/install.php');

    $colums_to_check = array(
        'idxrcustomproduct_configurations' => array(
            array(
                'name' => 'default_configuration',
                'type' => 'text',
                'update' => ''
            )
        ),
        'idxrcustomproduct_components' => array(
            array(
                'name' => 'default_opt',
                'type' => 'int(11) NOT NULL DEFAULT -1',
                'update' => '-1'
            )
        ),
    );

    foreach ($colums_to_check as $table => $columns) {
        foreach ($columns as $column) {
            $exist = Db::getInstance()->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . pSQL($table) . ' where Field = "' . pSQL($column['name']) .'" ;');
            if (!$exist) {
                $updateRun=false;
                $add_sql = 'ALTER TABLE ' . _DB_PREFIX_ . pSQL($table) . ' ADD '.  pSQL($column['name']) . ' ' . pSQL($column['type']) . ';';
                if ($column['update']) {
                    $update_sql = 'UPDATE '._DB_PREFIX_. pSQL($table) . ' set ' . pSQL($column['name']) . ' = ' . pSQL($column['update']);
                    $updateRun=true;
                }
                Db::getInstance()->execute($add_sql);
                if ($updateRun) {
                    Db::getInstance()->execute($update_sql);
                }
            }
        }
    }
    
    return true;
}
