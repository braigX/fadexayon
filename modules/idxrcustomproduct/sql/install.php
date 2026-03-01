<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2016 Innova Deluxe SL

 * @license   INNOVADELUXE
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'idxrcustomproduct_configurations (
    id_configuration int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    active tinyint(1) NOT NULL DEFAULT 0,
    categories text,
    products text,
    components text,
    visualization varchar(255),
    hook varchar(255),    
    color varchar(255),
    final_color varchar(255),
    add_base tinyint(1) NOT NULL DEFAULT 1, 
    show_increment tinyint(1) NOT NULL DEFAULT 0,
    show_topprice tinyint(1) NOT NULL DEFAULT 0,
    first_open tinyint(1) NOT NULL DEFAULT 0,
    resume_open tinyint(1) NOT NULL DEFAULT 0,
    breakdown_attachment tinyint(1) NOT NULL DEFAULT 0,
    discount tinyint(1) NOT NULL DEFAULT 0,
    discount_type varchar(255),
    discount_amount decimal(17,2),
    discount_createdas varchar(255),
    constraints_options text,
    default_configuration text,    
    PRIMARY KEY  (id_configuration)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'idxrcustomproduct_components (
    id_component int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    type varchar(255) NOT NULL,
    columns int(11) NOT NULL,
    zoom tinyint(1) unsigned NOT NULL DEFAULT 0,
    color varchar(255),
    default_opt int(11) NOT NULL DEFAULT -1,
    PRIMARY KEY  (id_component)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'idxrcustomproduct_components_lang (
    id_components_lang int(11) NOT NULL AUTO_INCREMENT,
    id_component int(11) NOT NULL,
    id_lang int(11) NOT NULL,
    title varchar(255) NOT NULL,
    description text,
    json_values text,
    PRIMARY KEY  (id_components_lang)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'idxrcustomproduct_components_opt_impact (
    id_comp_opt int(11) NOT NULL AUTO_INCREMENT,
    id_component int(11) NOT NULL,
    id_option int(11) NOT NULL,
    price_impact_type varchar(255) DEFAULT "fixed",
    price_impact decimal(20,6),
    price_impact_calc varchar(255),
    weight_impact decimal(20,6),
    reference varchar(32),
    att_product varchar(255),
    att_qty int(10),    
    PRIMARY KEY  (id_comp_opt)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'idxrcustomproduct_notes (
    id_note int(11) NOT NULL AUTO_INCREMENT,
    id_cart int(11) NOT NULL,
    id_cart_product int(11) NOT NULL,    
    id_order int(11) NOT NULL,
    id_order_detail int(11) NOT NULL,
    private_note text,
    public_note text,
    PRIMARY KEY  (id_note)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'idxrcustomproduct_customer_fav (
    id_fav int(11) NOT NULL AUTO_INCREMENT,
    id_customer int(11) NOT NULL,
    id_product int(11) NOT NULL,    
    icp_code varchar(255) NOT NULL,
    description text,
    PRIMARY KEY (id_fav)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'idxrcustomproduct_customer_extra (
    id_extra int(11) NOT NULL AUTO_INCREMENT,
    id_fav int(11),
    id_cart int(11),
    id_product int(11),
    id_component int(11) NOT NULL,
    id_option int(11) NOT NULL,
    extra text,
    PRIMARY KEY (id_extra)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'idxrcustomproduct_files (
    id_file int(11) NOT NULL AUTO_INCREMENT,
    id_fav int(11),
    id_cart int(11),
    id_product int(11),
    id_component int(11) NOT NULL,
    id_option int(11) NOT NULL,
    original_name varchar(255),
    target_name varchar(255),
    PRIMARY KEY (id_file)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'idxrcustomproduct_clones` (
 `id_producto` int(11) NOT NULL,
 `id_clon` int(11) NOT NULL,
 `icp_code` text NOT NULL,
 PRIMARY KEY (`id_producto`,`id_clon`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'idxrcustomproduct_saved_customisations` (
 `id_saved_customisation` int(11) NOT NULL AUTO_INCREMENT,
 `id_customer` int(11) NOT NULL,
 `id_product` int(11) NOT NULL,
 `id_product_attribute` int(11) NOT NULL DEFAULT 0,
 `customisation_name` varchar(100) NOT NULL,
 `customization` longtext,
 `extra_info` longtext,
 `snapshot_json` longtext,
 `preview_html` longtext,
 `thumbnail_svg` longtext,
 `date_add` datetime NOT NULL,
 `date_upd` datetime NOT NULL,
 PRIMARY KEY (`id_saved_customisation`),
 KEY `idxr_saved_customer_product` (`id_customer`, `id_product`),
 KEY `idxr_saved_customer_date` (`id_customer`, `date_add`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

require_once(dirname(__FILE__).'/upgrade-1.4.0.php');
require_once(dirname(__FILE__).'/upgrade-1.4.1.php');
require_once(dirname(__FILE__).'/upgrade-1.4.2.php');
require_once(dirname(__FILE__).'/upgrade-1.4.4.php');
require_once(dirname(__FILE__).'/upgrade-1.5.0.php');
require_once(dirname(__FILE__).'/upgrade-1.6.0.php');
require_once(dirname(__FILE__).'/upgrade-1.6.1.php');
require_once(dirname(__FILE__).'/upgrade-1.6.2.php');
require_once(dirname(__FILE__).'/upgrade-1.6.3.php');
require_once(dirname(__FILE__).'/upgrade-1.6.6.php');
require_once(dirname(__FILE__).'/upgrade-1.7.3.php');
require_once(dirname(__FILE__).'/upgrade-1.7.6.php');
