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

class MM_Cache { 
	private $expire = 1; 
    public function __construct()
    {
        $this->expire = (int)Configuration::get('ETS_MM_CACHE_LIFE_TIME') >=1 ? (int)Configuration::get('ETS_MM_CACHE_LIFE_TIME') : 1;
    }
	public function get($key) {
		$files = glob(_ETS_MEGAMENU_CACHE_DIR_ . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');
		if ($files) {
			$cache = Tools::file_get_contents($files[0]);
            foreach ($files as $file) {
				$time = (int)Tools::substr(strrchr($file, '.'), 1);
      			if ($time*3600 < time()) {
					if (file_exists($file)) {
						@unlink($file);
					}
      			}
    		}			
			return $cache;			
		}
        return false;
	}
  	public function set($key, $value) {
        if (!is_dir(_ETS_MEGAMENU_CACHE_DIR_)){
            @mkdir(_ETS_MEGAMENU_CACHE_DIR_, 0777, true);
            Tools::copy(dirname(__FILE__).'/index.php',_ETS_MEGAMENU_CACHE_DIR_.'index.php');
        }
    	$this->delete($key);		
		$file = _ETS_MEGAMENU_CACHE_DIR_. 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + (int)$this->expire*3600);
		$handle = fopen($file, 'w');
    	fwrite($handle, $value ? $value : '');		
    	fclose($handle);
  	}	
  	public function delete($key = false) {
		$files = glob(_ETS_MEGAMENU_CACHE_DIR_  . ($key ? 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) : '') . '.*');
		if ($files) {
    		foreach ($files as $file) {
      			if (file_exists($file) && strpos($file,'index.php')===false) {
					unlink($file);
				}
    		}
		}
  	}
}