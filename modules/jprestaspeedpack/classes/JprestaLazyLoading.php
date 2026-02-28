<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaSubModule;
use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JprestaLazyLoading')) {
    require_once 'JprestaSubModule.php';
    require_once 'JprestaLazyLoadingTplParser.php';

    class JprestaLazyLoading extends JprestaSubModule
    {
        const BACKUP_SUFFIX = '.orig_before_speedpack';
        const PRODUCT_CARD_TPL = 'catalog/_partials/miniatures/product.tpl';
        const LOADER_URI = '{$urls.base_url}modules/jprestaspeedpack/views/img/lazyloader.svg';

        public function __construct($module)
        {
            parent::__construct($module);
        }

        public function saveConfiguration()
        {
            $output = '';
            if (Tools::isSubmit('submitLazy')) {
                if (_PS_MODE_DEMO_ && !Context::getContext()->employee->isSuperAdmin()) {
                    $output .= $this->module->displayError($this->module->l('In DEMO mode you cannot modify the configuration.', 'jprestalazyloading'));
                } else {
                    //
                    // Enable / disable lazy loading
                    //
                    $newEnableWebp = (int) Tools::getValue('SPEED_PACK_LAZY_ENABLE');
                    $oldEnableWebp = self::isEnabledLazy();
                    if ($newEnableWebp && !$oldEnableWebp) {
                        $this->resetLogs();
                        if (!$this->enableLazy()) {
                            // Error
                            self::setEnabledLazy(0);
                        } else {
                            self::setEnabledLazy(1);
                        }
                    } elseif (!$newEnableWebp && $oldEnableWebp) {
                        $this->resetLogs();
                        $this->disableLazy();
                        self::setEnabledLazy(0);
                    }

                    $output .= $this->module->displayConfirmation($this->module->l('Settings updated',
                        'jprestalazyloading'));
                }
            }

            return $output;
        }

        public function displayForm()
        {
            // Init Fields form array
            $fieldsForm = [];
            $fieldsForm[0]['form'] = [
                'legend' => [
                    'title' => $this->module->l('Lazy loading of images', 'jprestalazyloading'),
                ],
            ];
            $theme = self::getCurrentTheme();
            $fieldsForm[0]['form']['input'] = [];
            if (!$theme) {
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'alert_warn',
                    'name' => 'SPEED_PACK_LAZY_MULTIPSHOP',
                    'text' => $this->module->l('To enable or disable the lazy loading of images you must select a shop in the select list (top right), not a group of shop',
                        'jprestalazyloading'),
                ];
            } elseif (Shop::isFeatureActive()) {
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'alert_info',
                    'name' => 'SPEED_PACK_LAZY_MULTIPSHOP',
                    'text' => $this->module->l('You will enable or disable lazy loading of images for theme',
                        'jprestalazyloading') . ' "' . $theme . '"',
                ];
            }

            if ($this->isHasAlreadyALoadingSystem($theme)) {
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'alert_warn',
                    'name' => 'SPEED_PACK_LAZY_ALREADY',
                    'text' => $this->l('Your theme or a module already provides a lazy loading system. It can\'t be replaced, but it\'s not a problem as long as you have one.', 'jprestalazyloading'),
                ];
            }

            $logs = $this->getLogs();
            if ($logs) {
                $fieldsForm[0]['form']['input'][] = [
                    'type' => 'logs',
                    'name' => 'SPEED_PACK_LAZY_LOGS',
                    'logs' => $logs,
                ];
            }

            $fieldsForm[0]['form']['input'][] =
                [
                    'type' => 'switch',
                    'label' => $this->module->l('Enable', 'Admin.Actions'),
                    'name' => 'SPEED_PACK_LAZY_ENABLE',
                    'is_bool' => true,
                    'desc' => $this->module->l('Enable lazy load of images', 'jprestalazyloading'),
                    'disabled' => !$theme,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->module->l('Enabled', 'Admin.Global'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->module->l('Disabled', 'Admin.Global'),
                        ],
                    ],
                ];
            $fieldsForm[0]['form']['submit'] = [
                'title' => $this->module->l('Save', 'Admin.Actions'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitLazy',
            ];

            $helper = new HelperForm();
            $helper->module = $this->module;

            // Load current value
            $helper->fields_value['SPEED_PACK_LAZY_ENABLE'] = $this->isEnabledLazy();

            return $helper->generateForm($fieldsForm);
        }

        private function addLogError($line)
        {
            $this->addLog($line, 'error');
        }

        private function addLog($line, $type = 'info')
        {
            $logs = json_decode(Configuration::get('SPEED_PACK_LAZY_LOGS', null, null, null, []));
            $logs[] = ['date' => date('Y-m-d H:i:s'), 'msg' => $line, 'type' => $type];
            Configuration::updateValue('SPEED_PACK_LAZY_LOGS', json_encode($logs));
        }

        private function getLogs()
        {
            return json_decode(Configuration::get('SPEED_PACK_LAZY_LOGS', null, null, null, ''));
        }

        private function resetLogs()
        {
            Configuration::updateValue('SPEED_PACK_LAZY_LOGS', json_encode([]));
        }

        public function install()
        {
            if (Shop::isFeatureActive()) {
                $shops = Shop::getShopsCollection();
                foreach ($shops as $shop) {
                    if ($shop->theme === null) {
                        $shop->setTheme();
                    }
                    $this->setEnabledLazy(1, $shop->theme->getName());
                }
            } else {
                $this->setEnabledLazy(1);
            }
            // This is called before install() but wee need to call it after settings configuration values
            $this->enable();

            return true;
        }

        public function uninstall()
        {
            $this->setEnabledLazy(0);

            return true;
        }

        public function enable()
        {
            // Don't want the cache to be cleared during migration from PCU
            if (!defined('JprestaMigPCU2SP')) {
                // This is also called after install
                if (Shop::isFeatureActive()) {
                    $shops = Shop::getShopsCollection();
                    $themesDone = [];
                    $this->resetLogs();
                    foreach ($shops as $shop) {
                        if ($shop->theme === null) {
                            $shop->setTheme();
                        }
                        if (self::isEnabledLazy($shop->theme->getName()) && array_key_exists($shop->theme->getName(), $themesDone)) {
                            $themesDone[$shop->theme->getName()] = true;
                            if (!$this->enableLazy($shop->theme->getName(), $shop->theme->get('parent'))) {
                                // Error
                                self::setEnabledLazy(0);
                            } else {
                                self::setEnabledLazy(1);
                            }
                        }
                    }
                } else {
                    if (self::isEnabledLazy()) {
                        $this->resetLogs();
                        if (!$this->enableLazy()) {
                            // Error
                            self::setEnabledLazy(0);
                        } else {
                            self::setEnabledLazy(1);
                        }
                    }
                }
            }

            return true;
        }

        public function disable()
        {
            // This is also called during uninstall
            if (Shop::isFeatureActive()) {
                $shops = Shop::getShopsCollection();
                foreach ($shops as $shop) {
                    if ($shop->theme === null) {
                        $shop->setTheme();
                    }
                    if (self::isEnabledLazy($shop->theme->getName())) {
                        $this->resetLogs();
                        $this->disableLazy($shop->theme->getName(), $shop->theme->get('parent'));
                    }
                }
            } else {
                if (self::isEnabledLazy()) {
                    $this->resetLogs();
                    $this->disableLazy();
                }
            }

            return true;
        }

        /**
         * @return string Current theme name or false if a group of shops is selected
         */
        private static function getCurrentTheme()
        {
            $themeName = false;
            $id_shop = Shop::getContextShopID();
            if ($id_shop === null) {
                // Group of shop
            } else {
                $shop = Shop::getShop($id_shop);
                $themeName = $shop['theme_name'];
            }

            return $themeName;
        }

        private function isEnabledLazy($theme = false)
        {
            if (!$theme) {
                $theme = self::getCurrentTheme();
            }

            return (int) Configuration::get('SPEED_PACK_LAZY_ENABLE_' . $theme, null, 0, 0);
        }

        private function setEnabledLazy($enabled, $theme = false)
        {
            if (!$theme) {
                $theme = self::getCurrentTheme();
            }
            Configuration::updateValue('SPEED_PACK_LAZY_ENABLE_' . $theme, (int) $enabled, false, 0, 0);
        }

        private function setHasAlreadyALoadingSystem($already, $theme = false)
        {
            if (!$theme) {
                $theme = self::getCurrentTheme();
            }
            Configuration::updateValue('SPEED_PACK_LAZY_ALREADY_' . $theme, (int) $already, false, 0, 0);
        }

        private function isHasAlreadyALoadingSystem($theme = false)
        {
            if (!$theme) {
                $theme = self::getCurrentTheme();
            }

            return (int) Configuration::get('SPEED_PACK_LAZY_ALREADY_' . $theme, null, 0, 0);
        }

        private function getTemplateFile($template, $theme = false, $parentTheme = false)
        {
            if (!$theme) {
                if (Shop::isFeatureActive()) {
                    $id_shop = Shop::getContextShopID();
                    if ($id_shop === null) {
                        // Should not be here
                        $theme = _THEME_NAME_;
                        $parentTheme = _PARENT_THEME_NAME_;
                    } else {
                        $shop = new Shop($id_shop);
                        $theme = $shop->theme->getName();
                        $parentTheme = $shop->theme->get('parent');
                    }
                } else {
                    $theme = _THEME_NAME_;
                    $parentTheme = _PARENT_THEME_NAME_;
                }
            }
            $file = _PS_ROOT_DIR_ . '/themes/' . $theme . '/templates/' . $template;
            if (!Tools::file_exists_no_cache($file) && $parentTheme) {
                // Copy the parent template to the child theme
                $parentFile = _PS_ROOT_DIR_ . '/themes/' . $parentTheme . '/templates/' . $template;
                if (Tools::file_exists_no_cache($parentFile)) {
                    if (!Tools::file_exists_no_cache(dirname($file)) && !@mkdir(dirname($file), 0777, true)) {
                        throw new Exception('Failed creating folder: ' . dirname($file) . '. Check permissions!');
                    }
                    Tools::copy($parentFile, $file);
                }
            }

            return $file;
        }

        private function enableLazy($theme = false, $parentTheme = false)
        {
            $ret = false;
            try {
                $this->addLog('Enabling lazy loading...');
                $productCardTemplateFile = $this->getTemplateFile(self::PRODUCT_CARD_TPL, $theme, $parentTheme);
                $this->enableLazyTemplate($productCardTemplateFile, self::LOADER_URI, $theme, $parentTheme);
                $this->addLog('Clearing cache...');
                Tools::clearSmartyCache();
                // The hook actionClearCache is not correctly called :'( so we do it ourselves
                $this->module->clearCache('enabling lazy load of images');
                $this->addLog('Lazy loading enabled.');
                $ret = true;
            } catch (Exception $e) {
                $this->addLogError($e->getMessage() . ': ' . $e->getTraceAsString());
                $this->addLog('Restoring templates after error...');
                $this->disableLazy($theme, $parentTheme);
            }

            return $ret;
        }

        private function enableLazyTemplate($templateFile, $imgLoaderUri, $theme = false, $parentTheme = false)
        {
            if (Tools::file_exists_no_cache($templateFile)) {
                $templateFileBackup = $templateFile . self::BACKUP_SUFFIX;
                if (Tools::file_exists_no_cache($templateFileBackup)) {
                    // Just ignore this file
                    return;
                }

                $templateContent = Tools::file_get_contents($templateFile);

                if (JprestaUtils::strpos($templateContent, 'data-src=') !== false
                    || JprestaUtils::strpos($templateContent, 'lazy') !== false
                ) {
                    $this->setHasAlreadyALoadingSystem(true, $theme);
                    throw new Exception($this->module->l('A lazy loading system is already installed in ') . $templateFile);
                } else {
                    $this->setHasAlreadyALoadingSystem(false, $theme);
                }

                $parser = new JprestaLazyLoadingTplParser($templateContent);
                if (count($parser->getImgTags()) > 0) {
                    // Creates a backup only if we need to modify the template
                    $this->addLog('Creating backup for template file ' . $templateFile . ' -> ' . $templateFileBackup);
                    Tools::copy($templateFile, $templateFileBackup);

                    // Add azy loading feature on img tags
                    $newTemplateContent = $parser->applyLazyLoading($imgLoaderUri);

                    // Save the template
                    $this->addLog('Adding lazy loading into template file ' . $templateFile . ' (' . count($parser->getImgTags()) . ' img tags)');
                    if (file_put_contents($templateFile, $newTemplateContent) === false) {
                        throw new Exception('Cannot write into file ' . $templateFile . ', please check file permissions.');
                    }
                }

                foreach ($parser->getSubTemplates() as $subTemplate) {
                    // Do the same treatment for all subtemplates
                    $this->enableLazyTemplate($this->getTemplateFile($subTemplate, $theme, $parentTheme), $imgLoaderUri, $theme, $parentTheme);
                }
            } else {
                $this->addLog('Template file ' . $templateFile . ' does not exist');
            }
        }

        private function disableLazy($theme = false, $parentTheme = false)
        {
            $ret = false;
            try {
                $this->addLog('Disabling lazy loading...');
                $productCardTemplateFile = $this->getTemplateFile(self::PRODUCT_CARD_TPL, $theme, $parentTheme);
                $this->disableLazyTemplate($productCardTemplateFile, $theme, $parentTheme);
                $this->addLog('Clearing cache...');
                Tools::clearSmartyCache();
                // The hook actionClearCache is not correctly called :'( so we do it ourselves
                $this->module->clearCache('disabling lazy load of images');
                $this->addLog('Lazy loading disabled.');
            } catch (Exception $e) {
                $this->addLogError($e->getMessage() . ': ' . $e->getTraceAsString());
            }

            return $ret;
        }

        private function disableLazyTemplate($templateFile, $theme = false, $parentTheme = false)
        {
            if (Tools::file_exists_no_cache($templateFile)) {
                $templateFileBackup = $templateFile . self::BACKUP_SUFFIX;
                if (Tools::file_exists_no_cache($templateFileBackup)) {
                    $this->addLog('Restoring template file ' . $templateFileBackup . ' -> ' . $templateFile);

                    $templateContent = Tools::file_get_contents($templateFile);

                    Tools::copy($templateFileBackup, $templateFile);
                    JprestaUtils::deleteFile($templateFileBackup);

                    $parser = new JprestaLazyLoadingTplParser($templateContent);
                    foreach ($parser->getSubTemplates() as $subTemplate) {
                        // Do the same treatment for all subtemplates
                        $this->disableLazyTemplate($this->getTemplateFile($subTemplate, $theme, $parentTheme), $theme, $parentTheme);
                    }
                }
            } else {
                $this->addLog('Template file ' . $templateFile . ' does not exist');
            }
        }

        public function displayHeader()
        {
            if (self::isEnabledLazy()) {
                $controller = Context::getContext()->controller;
                $controller->registerJavascript('lazysizes',
                    'modules/' . $this->module->name . '/views/js/lazysizes.min.js',
                    ['position' => 'bottom', 'priority' => 90]);
                $controller->registerStylesheet('lazyloadimage',
                    'modules/' . $this->module->name . '/views/css/lazyloadimage.css',
                    ['media' => 'all', 'priority' => 300]);
            }
        }
    }
}
