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

class NewsletterProInstallData
{
    private $errors = [];

    private $module;

    private $languages;

    private $languages_iso;

    private $translate;

    private $shops;

    private $call_functions = [
        'installSubscriptionTpl',
    ];

    public function __construct()
    {
        $this->module = NewsletterProTools::module();
        $this->translate = new NewsletterProTranslate(__CLASS__);

        $this->languages = Language::getLanguages(false);
        $this->languages_iso = [];

        foreach ($this->languages as $lang) {
            $this->languages_iso[$lang['iso_code']] = $lang;
        }

        $this->shops = Shop::getShops(false);
    }

    public static function newInstance()
    {
        return new self();
    }

    private function success()
    {
        return empty($this->errors);
    }

    public function execute(&$errors = [], $ignore = [])
    {
        try {
            foreach ($this->call_functions as $func_name) {
                if (in_array($func_name, $ignore)) {
                    continue;
                }
                if (!$this->{$func_name}()) {
                    break;
                }
            }
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                $this->errors[] = $e->__toString();
            } else {
                $this->errors[] = $e->getMessage();
            }
        }

        $errors = $this->errors;

        return $this->success();
    }

    private function installSubscriptionTpl()
    {
        $files_dir = _NEWSLETTER_PRO_DIR_.'/install/tables/subscription_tpl/';

        $files = NewsletterProTools::getDirectoryIterator($files_dir, '/^[a-zA-Z0-9_-]+$/');
        $default = 'responsive_new';
        $default_id = 0;

        foreach ($files as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $dirname = $file->getPathname().'/';
                $basename = $file->getBasename();

                $subscription_tpl = NewsletterProSubscriptionTpl::loadFile($dirname);

                if ($basename === $default) {
                    $subscription_tpl->active = true;
                }

                if (!$subscription_tpl->add()) {
                    $this->errors[] = sprintf($this->translate->l('Unable to install the subscription templates [%s].'), $dirname);
                }

                if ($basename === $default) {
                    $default_id = (int) $subscription_tpl->id;
                }
            }
        }

        if ((int) $default_id > 0) {
            NewsletterProSubscriptionTpl::setActive($default_id, Shop::CONTEXT_ALL);
            $subscription_tpl = new NewsletterProSubscriptionTpl((int) $default_id);
            if (Validate::isLoadedObject($subscription_tpl)) {
                NewsletterProConfigurationShop::updateValue('SUBSCRIPTION_TEMPLATE', $subscription_tpl->name);
            }
        }

        return $this->success();
    }
}
