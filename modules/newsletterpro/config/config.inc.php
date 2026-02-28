<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

ini_set('max_execution_time', '259200');

if (!defined('ENT_HTML5')) {
    define('ENT_HTML5', 48);
}

if (!defined('ENT_HTML401')) {
    define('ENT_HTML401', 0);
}

define('_NEWSLETTER_PRO_DIR_', realpath(dirname(__FILE__).'/../'));
define('_NEWSLETTER_PREFIX_', 'pqnp');
define('_PQNP_MYSQL_CHARSET_', 'utf8');
// Enable custom Swift class for backword compatibility
define('_CUSTOM_SWIFT_ENABLED_', false);
// this value should be 255 / 4 = 191 otherwise will be an error
define('_PQNP_MYSQL_UTF8MB4_VARCHAR_MAX_LENGTH_', 191);

require_once _NEWSLETTER_PRO_DIR_.'/libraries/phpQuery.php';
require_once _NEWSLETTER_PRO_DIR_.'/classes/helpers.php';
require_once _NEWSLETTER_PRO_DIR_.'/vendor/autoload.php';
