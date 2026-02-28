<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   See the license of this module in file LICENSE.txt, thank you.
 */

use JPresta\SpeedPack\JprestaUtils;

if (!defined('_PS_VERSION_')) {
    exit;
}

// Check existence of the class to be able to handle compatibility problems in a friendly way
if (!class_exists('JprestaCacheWarmerSettings')) {
    class JprestaCacheWarmerSettings
    {
        public $id_shop;

        public $languages;

        public $currencies;

        public $groups;

        public $countries;

        public $devices;

        public $tax_managers;

        public $specifics;

        /**
         * @var array List of contexts to warmp [language, currency, device, country, specifics]
         */
        public $contexts;

        /**
         * @var bool
         */
        public $contexts_auto;

        /**
         * @var string
         */
        public $filter_products_cats_ids = '';

        /**
         * @var array List of controllers name that must be warmed up
         */
        public $controllers;

        public $pages_count;

        /**
         * JprestaCacheWarmerSettings constructor.
         *
         * @param $id_shop
         */
        public function __construct($id_shop)
        {
            $this->id_shop = $id_shop;
        }

        private function init()
        {
            $this->initControllers();
            $this->initLanguages();
            $this->initCurrencies();
            $this->initGroups();
            $this->initCountries();
            $this->initDevices();
            $this->initTaxManagers();
            $this->initSpecifics();
            $this->initContexts();
            $this->pages_count = $this->getPagesCount();
            // Save changes
            $this->save();

            return $this;
        }

        private function initContexts()
        {
            // Purge old fake customers before so it does not generate useless context
            JprestaCustomer::purge();

            if ($this->contexts_auto) {
                $this->createContextsAuto();
            } else {
                if (!is_array($this->contexts) || count($this->contexts) === 0) {
                    $this->contexts = [];
                }
                // Remove contexts that have inactive parameter value
                foreach ($this->contexts as $index => $context) {
                    if (!isset($context['language']) || !array_key_exists($context['language'], $this->languages)) {
                        unset($this->contexts[$index]);
                        continue;
                    }
                    if (!isset($context['currency']) || !array_key_exists($context['currency'], $this->currencies)) {
                        unset($this->contexts[$index]);
                        continue;
                    }
                    if (!isset($context['country']) || !array_key_exists($context['country'], $this->countries)) {
                        unset($this->contexts[$index]);
                        continue;
                    }
                    if (!isset($context['device']) || !array_key_exists($context['device'], $this->devices)) {
                        unset($this->contexts[$index]);
                        continue;
                    }
                    if (!isset($context['group']) || !array_key_exists($context['group'], $this->groups)) {
                        unset($this->contexts[$index]);
                        continue;
                    }
                    if (!isset($context['specifics']) || !array_key_exists($context['specifics'], $this->specifics)) {
                        unset($this->contexts[$index]);
                    }
                }
            }
            // Remove duplicated contexts
            $this->removeDuplicatedContexts();

            // If all contexts have been removed, then set contexts_auto to true and generates new contexts automatically
            if (!is_array($this->contexts) || count($this->contexts) === 0) {
                $this->contexts_auto = true;
                $this->createContextsAuto();

                // Remove duplicated contexts
                $this->removeDuplicatedContexts();
            }
        }

        private function removeDuplicatedContexts()
        {
            $contextKeys = [];
            foreach ($this->contexts as $index => $context) {
                $contextKey = implode('|', [
                    $context['language'],
                    $context['currency'],
                    $context['country'],
                    $context['device'],
                    $context['group'],
                    $context['specifics'],
                ]);
                if (array_key_exists($contextKey, $contextKeys)) {
                    unset($this->contexts[$index]);
                } else {
                    $contextKeys[$contextKey] = 1;
                }
            }
        }

        /**
         * Create contexts depending on the statistics of the shop
         */
        private function createContextsAuto()
        {
            $this->contexts = [];
            $sql = 'SELECT *,
                count_hit_server + count_hit_static + count_hit_browser + count_hit_bfcache + count_missed as count_visit
                FROM `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`
                WHERE id_shop=' . (int) $this->id_shop . ' AND active=1
                ORDER BY count_visit+count_bot DESC';
            $rows = JprestaUtils::dbSelectRows($sql);
            $pagesCount = $this->getPagesCount();
            $minPercent = 0.01;
            $minCover = 0.95;
            if (count($rows) > 0) {
                $coveredVisit = 0.0;
                $coveredBot = 0.0;
                $total_visit_count = 0;
                $total_bot_count = 0;
                foreach ($rows as $row) {
                    $total_visit_count += (int) $row['count_visit'];
                    $total_bot_count += (int) $row['count_bot'];
                }
                $defaultCountry = null;
                foreach ($this->countries as $countryCode => $country) {
                    if (!$defaultCountry || $country['default']) {
                        $defaultCountry = $countryCode;
                    }
                }
                $defaultGroup = null;
                foreach ($this->groups as $groupEmail => $group) {
                    if ($defaultGroup === null || $group['default']) {
                        $defaultGroup = $groupEmail;
                    }
                }
                foreach ($rows as $row) {
                    if ($row['count_visit'] < ($minPercent * $total_visit_count) && $row['count_bot'] < ($minPercent * $total_bot_count)) {
                        // This context and next ones do not cover enougth cases
                        break;
                    }

                    // Creates a new context to warmup
                    $lang_iso = Language::getIsoById($row['id_lang']);
                    if (!isset($this->languages[$lang_iso])) {
                        // Language is not enabled anymore
                        continue;
                    }
                    if (method_exists('Currency', 'getIsoCodeById')) {
                        $currency_iso = Currency::getIsoCodeById($row['id_currency']);
                    } else {
                        $currency_iso = JprestaUtils::dbGetValue('SELECT `iso_code` FROM ' . _DB_PREFIX_ . 'currency WHERE `id_currency` = ' . (int) $row['id_currency']);
                    }
                    $country_iso = $row['id_country'] ? Country::getIsoById($row['id_country']) : $defaultCountry;
                    $device = 'desktop';
                    if ($row['id_device'] == Jprestaspeedpack::DEVICE_MOBILE) {
                        $device = 'mobile';
                    }
                    $group = $defaultGroup;
                    if ($row['id_fake_customer']) {
                        $fakeCustomer = new Customer($row['id_fake_customer']);
                        if (!Validate::isLoadedObject($fakeCustomer)) {
                            // Skip this context because the fake user does not exists anymore
                            continue;
                        }
                        $group = $fakeCustomer->email;
                    }
                    $this->contexts[] = [
                        'language' => $lang_iso,
                        'currency' => $currency_iso,
                        'country' => $country_iso,
                        'device' => $device,
                        'group' => $group,
                        'specifics' => $row['id_specifics'],
                    ];

                    if (($pagesCount * count($this->contexts)) > 100000) {
                        $minPercent = 0.03;
                        $minCover = 0.90;
                    }

                    $coveredVisit += $row['count_visit'];
                    $coveredBot += $row['count_bot'];
                    if ($coveredVisit >= ($minCover * $total_visit_count) && $coveredBot >= ($minCover * $total_bot_count)) {
                        // Created contexts cover enougth cases
                        break;
                    }
                }
            }
        }

        /**
         * @param $controllers_names string[]
         */
        public function checkControllers($controllers_names)
        {
            foreach ($this->controllers as $controller_name => &$managed_controller) {
                $managed_controller['checked'] = in_array($controller_name, $controllers_names);
            }
        }

        private function initControllers()
        {
            if (!$this->controllers || !is_array($this->controllers)) {
                $this->controllers = [];
                foreach (Jprestaspeedpack::getManagedControllersNames() as $managed_controller) {
                    $this->controllers[$managed_controller] = ['checked' => true, 'disabled' => false, 'count' => 1];
                }
            } else {
                // Add missing controllers
                foreach (Jprestaspeedpack::getManagedControllersNames() as $managed_controller) {
                    if (!isset($this->controllers[$managed_controller]) && JprestaUtilsModule::canBeWarmed($managed_controller)) {
                        $this->controllers[$managed_controller] = [
                            'checked' => false,
                            'disabled' => false,
                            'count' => null,
                        ];
                    }
                }
            }
            $shop = new Shop($this->id_shop);
            foreach ($this->controllers as $controller_name => &$managed_controller) {
                if (!Configuration::get('pagecache_' . $controller_name)) {
                    $managed_controller['checked'] = false;
                    $managed_controller['disabled'] = true;
                } else {
                    $managed_controller['disabled'] = false;
                }

                if (in_array($controller_name, ['index', 'newproducts', 'pricesdrop', 'contact', 'sitemap', 'bestsales'])) {
                    $managed_controller['count'] = 1;
                } elseif ($controller_name === 'manufacturer') {
                    $sql = 'SELECT COUNT(c.id_manufacturer)
                        FROM `' . _DB_PREFIX_ . 'manufacturer` c' . $shop->addSqlAssociation('manufacturer', 'c') . '
                        WHERE c.`active` = 1';
                    $managed_controller['count'] = (int) JprestaUtils::dbGetValue($sql) + 1;
                } elseif ($controller_name === 'supplier') {
                    $sql = 'SELECT COUNT(c.id_supplier)
                        FROM `' . _DB_PREFIX_ . 'supplier` c' . $shop->addSqlAssociation('supplier', 'c') . '
                        WHERE c.`active` = 1';
                    $managed_controller['count'] = (int) JprestaUtils::dbGetValue($sql) + 1;
                } elseif ($controller_name === 'product') {
                    // It is an estimation, we should substract all customizable products, etc.
                    // But we just want an idea of how many product this shop contains
                    $dispatcher = Dispatcher::getInstance();
                    if ($dispatcher->hasKeyword('product_rule', Configuration::get('PS_LANG_DEFAULT'), 'id_product_attribute')) {
                        $sql = 'SELECT COUNT(*)
                        FROM `' . _DB_PREFIX_ . 'product` p' . $shop->addSqlAssociation('product', 'p') . '
                        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa   ON p.id_product=pa.id_product' . '
                        WHERE product_shop.`active` = 1';
                    } else {
                        $sql = 'SELECT COUNT(*)
                        FROM `' . _DB_PREFIX_ . 'product` p' . $shop->addSqlAssociation('product', 'p') . '
                        WHERE product_shop.`active` = 1';
                    }
                    $toSubstract = 0;
                    if (!Configuration::get('pagecache_cache_customizable')) {
                        // Subsctract the number of active customizable products
                        $sqlCustom = 'SELECT COUNT(DISTINCT p.id_product)
                        FROM `' . _DB_PREFIX_ . 'customization_field` cf
                        LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON cf.id_product=p.id_product' . $shop->addSqlAssociation('product', 'p') . '
                        WHERE p.id_product IS NOT NULL AND product_shop.`active` = 1';
                        if (JprestaUtils::version_compare(_PS_VERSION_, '1.7.3.0', '>=')) {
                            $sqlCustom .= ' AND cf.is_deleted=0';
                        }
                        $toSubstract = (int) JprestaUtils::dbGetValue($sqlCustom);
                    }
                    $managed_controller['count'] = (int) JprestaUtils::dbGetValue($sql) - $toSubstract;
                } elseif ($controller_name === 'category') {
                    $sql = 'SELECT COUNT(c.id_category)
                        FROM `' . _DB_PREFIX_ . 'category` c' . $shop->addSqlAssociation('category', 'c') . '
                        WHERE c.`active` = 1 AND c.is_root_category = 0 AND c.id_parent > 0';
                    $managed_controller['count'] = (int) JprestaUtils::dbGetValue($sql);
                } elseif ($controller_name === 'cms') {
                    // CMS
                    $sql = 'SELECT COUNT(c.id_cms)
                        FROM `' . _DB_PREFIX_ . 'cms` c' . $shop->addSqlAssociation('cms', 'c') . '
                        WHERE c.`active` = 1';
                    $managed_controller['count'] = (int) JprestaUtils::dbGetValue($sql);

                    // CMS CATEGORIES
                    $sql = 'SELECT COUNT(c.id_cms_category)
                        FROM `' . _DB_PREFIX_ . 'cms_category` c' . $shop->addSqlAssociation('cms_category', 'c') . '
                        WHERE c.`active` = 1';
                    $managed_controller['count'] += (int) JprestaUtils::dbGetValue($sql);
                } elseif (JprestaUtilsModule::isModuleController($controller_name)) {
                    $managed_controller['count'] = JprestaUtilsModule::getAllURLsCount($controller_name);
                }
            }
        }

        /**
         * @return int Number of different pages
         *
         * @throws PrestaShopDatabaseException
         */
        public function getPagesCount()
        {
            $pageCount = 0;
            if (is_array($this->controllers)) {
                foreach ($this->controllers as $managed_controller) {
                    if ($managed_controller['checked']) {
                        $pageCount += $managed_controller['count'];
                    }
                }
            }

            return $pageCount;
        }

        private function initLanguages()
        {
            $this->languages = [];
            foreach (Language::getLanguages(true, $this->id_shop) as $language) {
                $this->languages[$language['iso_code']] = [];
                $this->languages[$language['iso_code']]['label'] = preg_replace('/\s\(.*\)$/', '', $language['name']);
                $this->languages[$language['iso_code']]['value'] = $language['iso_code'];
                $this->languages[$language['iso_code']]['default'] = $language['id_lang'] == Configuration::get('PS_LANG_DEFAULT', null, null, $this->id_shop);
            }
            uasort($this->languages, ['self', 'sortContext']);
        }

        private function initCurrencies()
        {
            $this->currencies = [];
            foreach (Currency::getCurrenciesByIdShop($this->id_shop) as $currency) {
                if ($currency['active'] && Jprestaspeedpack::isCacheEnabledForCurrency($currency['iso_code'])) {
                    $this->currencies[$currency['iso_code']] = [];
                    $this->currencies[$currency['iso_code']]['label'] = $currency['iso_code'];
                    $this->currencies[$currency['iso_code']]['value'] = $currency['iso_code'];
                    $this->currencies[$currency['iso_code']]['default'] = $currency['id_currency'] == Configuration::get('PS_CURRENCY_DEFAULT', null, null, $this->id_shop);
                }
            }
            uasort($this->currencies, ['self', 'sortContext']);
        }

        private function initGroups()
        {
            $this->groups = [];
            $hasDefault = false;

            // Take all fake users already created (don't generate all combinations)
            // Only if cache is enabled for logged in users
            if (!Configuration::get('pagecache_skiplogged')) {
                // Force the creation of the fake user for default customer (visitor)
                JprestaCustomer::getOrCreateCustomerWithSameGroups(new Customer());

                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'customer` WHERE `id_shop` =' . (int) $this->id_shop . ' AND `firstname` = \'fake-user-for-pagecache\'';
                $fakeUsers = Db::getInstance()->executeS($sql);
                foreach ($fakeUsers as $fakeUser) {
                    $fakeCustomer = new Customer($fakeUser['id_customer']);
                    $fakeCustomerRecursive = JprestaCustomer::getOrCreateCustomerWithSameGroups($fakeCustomer, true);
                    if ($fakeCustomerRecursive->id != $fakeCustomer->id) {
                        // $fakeCustomer is not used anymore
                        continue;
                    }
                    $this->groups[$fakeUser['email']] = [];
                    $this->groups[$fakeUser['email']]['label'] = $fakeCustomerRecursive->getLabel();
                    $this->groups[$fakeUser['email']]['value'] = $fakeUser['email'];
                    $this->groups[$fakeUser['email']]['default'] = JprestaCustomer::isVisitor($fakeUser['id_customer']);
                    $hasDefault = $hasDefault || $this->groups[$fakeUser['email']]['default'];
                    $this->groups[$fakeUser['email']]['count'] = $this->getGroupCount($fakeUser['id_customer']);
                }
            }

            if (!$hasDefault) {
                // Add anonymous group
                $this->groups[''] = [];
                $this->groups['']['label'] = 'Default';
                $this->groups['']['value'] = '';
                $this->groups['']['default'] = true;
            }

            uasort($this->groups, ['self', 'sortContext']);
        }

        private function getGroupCount($id_customer)
        {
            if (!Group::isFeatureActive()) {
                return 1;
            }
            $groupIds = Customer::getGroupsStatic($id_customer);

            return JprestaUtils::dbGetValue('
			SELECT COUNT(*)
			FROM (SELECT cg.id_customer FROM `' . _DB_PREFIX_ . 'customer_group` cg
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (cg.`id_customer` = c.`id_customer`)
			WHERE cg.`id_group` IN (' . pSQL(implode(',', $groupIds)) . ')
			AND c.`deleted` != 1 AND c.`active` = 1
			GROUP BY cg.id_customer
			HAVING SUM(1)=' . (int) count($groupIds) . '
			) subtable');
        }

        private function initCountries()
        {
            $this->countries = [];

            $defaultCountryIso = Country::getIsoById((int) Configuration::get('PS_COUNTRY_DEFAULT'));
            $this->countries[$defaultCountryIso] = [];
            $this->countries[$defaultCountryIso]['label'] = Country::getNameById(Context::getContext()->cookie->id_lang, (int) Configuration::get('PS_COUNTRY_DEFAULT'));
            $this->countries[$defaultCountryIso]['value'] = $defaultCountryIso;
            $this->countries[$defaultCountryIso]['default'] = true;

            $haveOthers = false;
            $currentCacheKeyCountryConf = json_decode(JprestaUtils::getConfigurationByShopId('pagecache_cachekey_countries', Shop::getContextShopID(), '{}'), true);
            foreach ($currentCacheKeyCountryConf as $id_country => $country_conf) {
                if (!$country_conf['specific_cache']) {
                    $haveOthers = true;
                    break;
                }
            }
            if ($haveOthers) {
                $this->countries['OTHERS'] = [];
                $this->countries['OTHERS']['label'] = 'Countries without specific cache';
                $this->countries['OTHERS']['value'] = 'OTHERS';
                $this->countries['OTHERS']['default'] = false;
            }
            foreach ($currentCacheKeyCountryConf as $id_country => &$country_conf) {
                $country = new Country($id_country, Context::getContext()->cookie->id_lang);
                if ($country->iso_code === $defaultCountryIso && !$country_conf['specific_cache']) {
                    // The default country does not have specific cache so it will be included in OTHERS which becomes the default value
                    $this->countries['OTHERS']['label'] = 'Countries without specific cache (' . $this->countries[$defaultCountryIso]['label'] . ', ...)';
                    $this->countries['OTHERS']['value'] = 'OTHERS';
                    $this->countries['OTHERS']['default'] = true;
                    unset($this->countries[$country->iso_code]);
                } elseif ($country_conf['specific_cache']) {
                    // This country has a specific cache so add to the list
                    $this->countries[$country->iso_code] = [];
                    $this->countries[$country->iso_code]['label'] = $country->name;
                    $this->countries[$country->iso_code]['value'] = $country->iso_code;
                    $this->countries[$country->iso_code]['default'] = false;
                } else {
                    // It's not the default country and there is no specific cache so remove it from the list
                    unset($this->countries[$country->iso_code]);
                }
            }
            uasort($this->countries, ['self', 'sortContext']);
        }

        public function isCountryOthers($iso_country)
        {
            return !array_key_exists($iso_country, $this->countries);
        }

        private function initDevices()
        {
            $this->devices = [];

            if (!Configuration::get('pagecache_depend_on_device_auto')) {
                // Configuration is set to get the same content on mobile and desktop
                $this->devices['desktop'] = [];
                $this->devices['desktop']['label'] = 'Any device';
                $this->devices['desktop']['value'] = 'desktop';
                $this->devices['desktop']['default'] = true;
            } else {
                $this->devices['desktop'] = [];
                $this->devices['desktop']['label'] = 'Desktop';
                $this->devices['desktop']['value'] = 'desktop';
                $this->devices['desktop']['default'] = true;

                $this->devices['mobile'] = [];
                $this->devices['mobile']['label'] = 'Mobile';
                $this->devices['mobile']['value'] = 'mobile';
                $this->devices['mobile']['default'] = true;
            }
            uasort($this->devices, ['self', 'sortContext']);
        }

        private function initTaxManagers()
        {
            // Disabled because when warmng up a country, the corresponding tax manager will be used. There is no need
            // to warmup a country with a tax manager that will never apply to it.
            $this->tax_managers = [];
        }

        private function initSpecifics()
        {
            $this->specifics = [];

            $mostUsedSpecificsRows = PageCacheDAO::getMostUsedSpecifics($this->id_shop, 10);
            foreach ($mostUsedSpecificsRows as $mostUsedSpecificsRow) {
                if ($mostUsedSpecificsRow['id_specifics']) {
                    $this->specifics[$mostUsedSpecificsRow['id_specifics']] = [];
                    $this->specifics[$mostUsedSpecificsRow['id_specifics']]['label'] = '#' . $mostUsedSpecificsRow['id_specifics'];
                    $this->specifics[$mostUsedSpecificsRow['id_specifics']]['value'] = $mostUsedSpecificsRow['id_specifics'];
                    $this->specifics[$mostUsedSpecificsRow['id_specifics']]['default'] = false;
                    $this->specifics[$mostUsedSpecificsRow['id_specifics']]['moreinfos'] = $mostUsedSpecificsRow['specifics'];
                    $this->specifics[$mostUsedSpecificsRow['id_specifics']]['count'] = $mostUsedSpecificsRow['count'];
                } else {
                    // Add "Default" only if it exists, sometimes there is always some specifics datas
                    $this->specifics[''] = [];
                    $this->specifics['']['label'] = 'Default';
                    $this->specifics['']['value'] = '';
                    $this->specifics['']['default'] = true;
                }
            }

            uasort($this->specifics, ['self', 'sortContext']);
        }

        public function getContextsToWarmup()
        {
            $contextsToWarmup = [];
            foreach ($this->contexts as $index => $context) {
                $contextsToWarmup[$index] = $context;
                if ($contextsToWarmup[$index]['country'] === 'OTHERS') {
                    $defaultCountry = (int) Configuration::get('PS_COUNTRY_DEFAULT');
                    $currentCacheKeyCountryConf = json_decode(JprestaUtils::getConfigurationByShopId('pagecache_cachekey_countries', $this->id_shop, '{}'), true);
                    foreach ($currentCacheKeyCountryConf as $id_country => $country_conf) {
                        if (!$country_conf['specific_cache']) {
                            $contextsToWarmup[$index]['country'] = Country::getIsoById($id_country);
                            if ($id_country === $defaultCountry) {
                                break;
                            }
                        }
                    }
                }
            }

            return $contextsToWarmup;
        }

        /**
         * @return int Number of context to warm-up
         */
        public function getContextCount()
        {
            return count($this->contexts);
        }

        /**
         * @param $id_shop
         *
         * @return JprestaCacheWarmerSettings
         */
        public static function get($id_shop)
        {
            $cws_json = JprestaUtils::getConfigurationByShopId('pagecache_cache_warmer_settings', $id_shop);
            if (!$cws_json) {
                $cws = new JprestaCacheWarmerSettings($id_shop);
                // Set it here to preserve existing contexts
                $cws->contexts_auto = true;
            } else {
                $stdClass = json_decode($cws_json, true);
                $cws = new JprestaCacheWarmerSettings($id_shop);
                foreach ($stdClass as $key => $value) {
                    if ($key != 'id_shop') {
                        $cws->{$key} = $value;
                    }
                }
                // Automatically create contexts if there is none
                if (!is_array($cws->contexts) || count($cws->contexts) === 0) {
                    $cws->contexts_auto = true;
                }
            }

            return $cws->init();
        }

        public function save()
        {
            JprestaUtils::saveConfigurationByShopId('pagecache_cache_warmer_settings', json_encode($this), $this->id_shop);
        }

        public static function sortContext($c1, $c2)
        {
            $c1Count = array_key_exists('count', $c1) ? (int) $c1['count'] : 0;
            $c2Count = array_key_exists('count', $c2) ? (int) $c2['count'] : 0;
            $c1Label = array_key_exists('label', $c1) ? $c1['label'] : '';
            $c2Label = array_key_exists('label', $c2) ? $c2['label'] : '';
            if (array_key_exists('default', $c1) && $c1['default']) {
                return -PHP_INT_MAX;
            }
            if (array_key_exists('default', $c2) && $c2['default']) {
                return PHP_INT_MAX;
            }
            if ($c1Count != $c2Count) {
                return $c2Count - $c1Count;
            }

            return strcasecmp($c1Label, $c2Label);
        }
    }
}
