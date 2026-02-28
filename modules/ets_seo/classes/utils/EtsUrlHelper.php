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
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Class EtsUrlHelper.
 */
class EtsUrlHelper
{
    /**
     * @var self
     */
    private static $_instance;

    /**
     * Singleton instance.
     *
     * @return \EtsUrlHelper
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param string $url Relative URL
     * @param string $iso Lang ISO Code (2 Chars)
     *
     * @return bool
     */
    public function isUrlHasLangIso($url, $iso)
    {
        $url = ltrim($url, '/');

        return 0 === strpos($url, $iso);
    }

    /**
     * @param string $url Relative URL
     * @param string $iso Lang ISO Code (2 Chars)
     *
     * @return string
     */
    public function prependLangIso($url, $iso)
    {
        if (!$this->isUrlHasLangIso($url, $iso)) {
            $iso = ltrim($iso, '/');
            if (0 === strpos($url, '/')) {
                return '/' . $iso . $url;
            }

            return $iso . '/' . $url;
        }

        return $url;
    }

    /**
     * @param string $url Relative URL
     * @param string $iso Lang ISO Code (2 Chars)
     *
     * @return string
     */
    public function removeLangIso($url, $iso)
    {
        if ($this->isUrlHasLangIso($url, $iso)) {
            $iso = ltrim($iso, '/');
            if (0 === strpos($url, '/')) {
                $url = ltrim($url, '/');

                return '/' . ltrim($url, $iso);
            }

            return ltrim($url, $iso);
        }

        return $url;
    }
}
