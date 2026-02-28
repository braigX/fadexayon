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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisTranslation.php';

class GeodisServiceTranslation
{
    protected $vars = array();
    protected $value;
    protected $idLang;
    protected $default = false;
    protected $key;

    protected static $registeredInSmarty = false;

    protected static $cache = array();

    public static function get($key, $idLang = null)
    {
        return new GeodisServiceTranslation($key, $idLang);
    }

    public function __construct($key, $idLang = null)
    {
        if (is_null($idLang)) {
            $idLang = Context::getContext()->language->id;
        }

        $this->idLang = $idLang;
        $this->key = $key;

        if (!isset(self::$cache[$idLang][$key])) {
            $this->load();
        }

        $this->value = self::$cache[$idLang][$key];

        self::registerSmarty();
    }

    public static function registerSmarty()
    {
        if (self::$registeredInSmarty) {
            return;
        }
        smartyRegisterFunction(
            Context::getContext()->smarty,
            'function',
            '__',
            'geodisSmartyTranslate',
            false
        );

        self::$registeredInSmarty = true;
    }

    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    protected function load()
    {
        $translation = GeodisTranslation::get($this->key, $this->idLang);

        if (!$translation->id) {
            self::$cache[$this->idLang][$this->key] = $this->key;
        } else {
            self::$cache[$this->idLang][$this->key] = $translation->value;
        }
    }

    public function addVar($value)
    {
        $this->vars[] = $value;
        return $this;
    }

    public function __toString()
    {
        $value = $this->value;

        if ($value == $this->key && $this->default !== false) {
            $value = $this->default;
        }
        return vsprintf($value, $this->vars);
    }
}

function geodisSmartyTranslate($params)
{
    if (!isset($params['s'])) {
        throw new Exception('Missing params \'s\'.');
    }
    $translation = GeodisServiceTranslation::get($params['s']);

    if (isset($params['vars'])) {
        if (!is_array($params['vars'])) {
            $translation->addVar($params['vars']);
        } else {
            foreach ($params['vars'] as $var) {
                $translation->addVar($var);
            }
        }
    }

    return (string) $translation;
}
