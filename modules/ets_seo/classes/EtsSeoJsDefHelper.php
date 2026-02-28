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
 **/
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once __DIR__ . '/EtsSeoArrayHelper.php';
require_once __DIR__ . '/traits/EtsSeoGetInstanceTrait.php';

/**
 * Class EtsSeoJsDefHelper
 */
class EtsSeoJsDefHelper
{
    use EtsSeoGetInstanceTrait;

    /**
     * Key name for using w Media::addJsDef on Back Office
     *
     * @var string
     */
    private $_boKeyName = 'etsSeoBo';
    /**
     * Key name for using w Media::addJsDef on Front Office
     *
     * @var string
     */
    private $_foKeyName = 'etsSeoFo';

    /**
     * Hold js def values for Back Office
     *
     * @var array
     */
    private $_boDefs = [];

    /**
     * Hold js def values for Front Office
     *
     * @var array
     */
    private $_foDefs = [];

    /**
     * @param string $key
     * @param mixed $val
     *
     * @return \EtsSeoJsDefHelper
     */
    public function addBo($key, $val)
    {
        $this->_boDefs = EtsSeoArrayHelper::add($this->_boDefs, $key, $val);
        Media::addJsDef($this->getBoDef());

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $val
     *
     * @return \EtsSeoJsDefHelper
     */
    public function addFo($key, $val)
    {
        $this->_foDefs = EtsSeoArrayHelper::add($this->_foDefs, $key, $val);
        Media::addJsDef($this->getFoDef());

        return $this;
    }
    public function setBo($key, $val)
    {
        EtsSeoArrayHelper::set($this->_boDefs, $key, $val);
        Media::addJsDef($this->getBoDef());

        return $this;
    }
    public function setFo($key, $val)
    {
        EtsSeoArrayHelper::set($this->_foDefs, $key, $val);
        Media::addJsDef($this->getFoDef());

        return $this;
    }
    public function removeBo($key)
    {
        EtsSeoArrayHelper::forget($this->_boDefs, $key);
        Media::addJsDef($this->getBoDef());

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \EtsSeoJsDefHelper
     */
    public function removeFo($key)
    {
        EtsSeoArrayHelper::forget($this->_foDefs, $key);
        Media::addJsDef($this->getFoDef());

        return $this;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getBo($key, $default = null)
    {
        return EtsSeoArrayHelper::get($this->_boDefs, $key, $default);
    }

    /**
     * @param string $key
     * @param mixed|null $default
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getFo($key, $default = null)
    {
        return EtsSeoArrayHelper::get($this->_foDefs, $key, $default);
    }

    /**
     * @return array[]
     */
    public function getBoDef()
    {
        return [$this->_boKeyName => $this->_boDefs];
    }

    /**
     * @return string
     */
    public function getBoKey()
    {
        return $this->_boKeyName;
    }

    /**
     * @return array
     */
    public function getBoValues()
    {
        return $this->_boDefs;
    }

    /**
     * @return array[]
     */
    public function getFoDef()
    {
        return [$this->_foKeyName => $this->_foDefs];
    }

    /**
     * @return string
     */
    public function getFoKey()
    {
        return $this->_foKeyName;
    }

    /**
     * @return array
     */
    public function getFoValues()
    {
        return $this->_foDefs;
    }

    /**
     * @return array
     */
    public function getAllDef()
    {
        return array_merge($this->getBoDef(), $this->getFoDef());
    }
}
