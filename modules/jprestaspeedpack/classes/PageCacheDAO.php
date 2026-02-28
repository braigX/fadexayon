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
if (!class_exists('PageCacheDAO')) {
    class PageCacheDAO
    {
        /**
         * MyIsam locks whole table while InnoDb locks rows. InnoDb is fine but you must not use relation between tables
         * or it will be too slow.
         */
        const MYSQL_ENGINE = 'InnoDb';

        /**
         * Log infos when a refreshment deletes more than this number of pages
         */
        const LOG_WHEN_MORE_THAN = 500;

        const TABLE = 'jm_pagecache';
        const TABLE_CONTEXTS = 'jm_pagecache_contexts';
        const TABLE_PERFS = 'jm_pagecache_perfs';
        const TABLE_DETAILS = 'jm_pagecache_details';
        const TABLE_BACKLINK = 'jm_pagecache_bl';
        const TABLE_MODULE = 'jm_pagecache_mods';

        // This table is created to store state of the cache refreshment concerning a specific price
        const TABLE_SPECIFIC_PRICES = 'jm_pagecache_sp';

        // Store date for profiling
        const TABLE_PROFILING = 'jm_pagecache_prof';

        /**
         * @throws PrestaShopDatabaseException
         */
        public static function createTables()
        {
            JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE_DETAILS . '`(
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `details` TEXT NOT NULL,
            `details_md5` VARCHAR(32) DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX details_md5 (`details_md5`)
            ) ENGINE=' . PageCacheDAO::MYSQL_ENGINE . ' DEFAULT CHARSET=utf8', true, true);

            self::createTableContexts();

            self::createTableStatsPerf();

            $sqlCreateMainTable = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE . '`(
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_shop` TINYINT UNSIGNED NOT NULL DEFAULT 1,
            `cache_key` INT UNSIGNED NOT NULL,
            `url` TEXT NOT NULL,
            `id_context` INT(10) UNSIGNED NOT NULL,
            `id_controller` TINYINT(1) UNSIGNED DEFAULT NULL,
            `id_object` INT UNSIGNED,
            `count_missed` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
            `count_hit` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
            `last_gen` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE `cache_key` (`cache_key`),
            KEY `id_controller_object` (`id_controller`, `id_object`),
            KEY `id_controller_last_gen` (`id_controller`, `last_gen`),
            KEY `id_controller` (`id_controller`),
            KEY `id_shop` (`id_shop`),
            KEY `id_context` (`id_context`),
            KEY `last_gen` (`last_gen`),
            KEY `deleted`(`deleted`)
            ) ENGINE=' . PageCacheDAO::MYSQL_ENGINE . ' DEFAULT CHARSET=utf8';
            JprestaUtils::dbExecuteSQL($sqlCreateMainTable, true, true);

            JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '`(
            `id_backlink` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id` INT(11) UNSIGNED NOT NULL,
            `backlink_key` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`id_backlink`),
            KEY (`id`),
            KEY (`backlink_key`)
            ) ENGINE=' . PageCacheDAO::MYSQL_ENGINE . ' DEFAULT CHARSET=utf8', true, true);

            JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE_MODULE . '`(
            `id` int(11) UNSIGNED NOT NULL,
            `id_module` int(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`,`id_module`),
            KEY (`id_module`)
            ) ENGINE=' . PageCacheDAO::MYSQL_ENGINE . ' DEFAULT CHARSET=utf8', true, true);

            JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '`(
            `id_specific_price` int(10) unsigned NOT NULL,
            `id_product` int(10) unsigned NOT NULL,
            `date_from` datetime,
            `date_to` datetime,
            PRIMARY KEY (`id_specific_price`),
            KEY `idxfrom` (`date_from`),
            KEY `idxto` (`date_to`)
            ) ENGINE=' . PageCacheDAO::MYSQL_ENGINE . ' DEFAULT CHARSET=utf8', true, true);

            JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE_PROFILING . '`(
            `id_profiling` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_module` int(10) unsigned NOT NULL,
            `description` varchar(255) NOT NULL,
            `date_exec` timestamp DEFAULT CURRENT_TIMESTAMP,
            `duration_ms` mediumint unsigned NOT NULL,
            PRIMARY KEY (`id_profiling`)
            ) ENGINE=' . PageCacheDAO::MYSQL_ENGINE . ' DEFAULT CHARSET=utf8', true, true);

            // Feed TABLE_SPECIFIC_PRICES to trigger cache reffreshment when a reduction starts or ends
            $now = date('Y-m-d H:i:00');
            $sqlInsertQuery = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` (`id_specific_price`,`id_product`,`date_from`,`date_to`)
                SELECT `id_specific_price`, `id_product`, `from`, `to` FROM `' . _DB_PREFIX_ . 'specific_price`
                WHERE `from`>\'' . pSQL($now) . '\' OR `to`>\'' . pSQL($now) . '\'';
            JprestaUtils::dbExecuteSQL($sqlInsertQuery, true, true);
        }

        public static function createTableContexts()
        {
            $ret = JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '`(
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `context_key` VARCHAR(64) NOT NULL,
                `id_shop` INT(10) UNSIGNED DEFAULT NULL,
                `id_lang` INT(10) UNSIGNED DEFAULT NULL,
                `id_currency` INT(10) UNSIGNED,
                `id_fake_customer` INT(10) UNSIGNED DEFAULT NULL,
                `id_device` TINYINT(1) UNSIGNED,
                `id_country` INT(10) UNSIGNED DEFAULT NULL,
                `id_tax_csz` INT(11) UNSIGNED DEFAULT NULL,
                `id_specifics` INT(11) UNSIGNED DEFAULT NULL,
                `v_css` SMALLINT UNSIGNED DEFAULT NULL,
                `v_js` SMALLINT UNSIGNED DEFAULT NULL,
                `used_by_cw` TINYINT UNSIGNED NOT NULL DEFAULT 0,
                `active` TINYINT UNSIGNED NOT NULL DEFAULT 1,
                `count_bot` INT UNSIGNED NOT NULL DEFAULT 0,
                `count_hit_server` INT UNSIGNED NOT NULL DEFAULT 0,
                `count_hit_static` INT UNSIGNED NOT NULL DEFAULT 0,
                `count_hit_browser` INT UNSIGNED NOT NULL DEFAULT 0,
                `count_hit_bfcache` INT UNSIGNED NOT NULL DEFAULT 0,
                `count_missed` INT UNSIGNED NOT NULL DEFAULT 0,
                `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `uniq_key` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE (`context_key`)
                ) ENGINE=' . PageCacheDAO::MYSQL_ENGINE . ' DEFAULT CHARSET=utf8', true, true);
            $ret = $ret && JprestaUtils::dbExecuteSQL('CREATE UNIQUE INDEX idx_find_context_full ON `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` (id_shop, id_lang, id_currency, id_fake_customer, id_device, id_country, id_tax_csz, id_specifics, v_css, v_js)');
            $ret = $ret && JprestaUtils::dbExecuteSQL('CREATE UNIQUE INDEX idx_uniq_key ON `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` (uniq_key)');
            $ret = $ret && JprestaUtils::dbExecuteSQL('CREATE INDEX idx_find_context ON `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` (id_shop, id_lang, id_currency, id_fake_customer, id_device, id_country, id_tax_csz, id_specifics)');
            $ret = $ret && JprestaUtils::dbExecuteSQL('CREATE INDEX idx_order_context ON `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` (used_by_cw, date_add)');
            $ret = $ret && JprestaUtils::dbExecuteSQL('CREATE INDEX idx_active_context ON `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` (active)');

            return $ret;
        }

        public static function createTableStatsPerf()
        {
            return JprestaUtils::dbExecuteSQL('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . self::TABLE_PERFS . '`(
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_shop` int(11) UNSIGNED NOT NULL,
                `id_controller` TINYINT(1) UNSIGNED DEFAULT NULL,
                `date_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `day_add` DATE DEFAULT NULL,
                `ttfb_ms_hit_server` INT UNSIGNED DEFAULT NULL,
                `ttfb_ms_hit_static` INT UNSIGNED DEFAULT NULL,
                `ttfb_ms_hit_browser` INT UNSIGNED DEFAULT NULL,
                `ttfb_ms_hit_bfcache` INT UNSIGNED DEFAULT NULL,
                `ttfb_ms_missed` INT UNSIGNED DEFAULT NULL,
                `reduced` TINYINT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                INDEX date_add_idx (`date_add`),
                INDEX day_add_idx (`day_add`),
                INDEX id_shop_idx (`id_shop`),
                INDEX idx_sdc (id_shop, day_add, id_controller),
                INDEX idx_sd (id_shop, day_add)
                ) ENGINE=' . PageCacheDAO::MYSQL_ENGINE . ' DEFAULT CHARSET=utf8', true, true);
        }

        public static function insertSpecificPrice($id, $id_product, $from, $to)
        {
            $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` (id_specific_price,id_product,date_from,date_to) VALUES ';
            $query .= '(' . (int) $id . ',' . (int) $id_product . ',\'' . pSQL($from) . '\',\'' . pSQL($to) . '\');';
            JprestaUtils::dbExecuteSQL($query);
            // Prestashop deletes and creates new specific prices when saving a product
            $queryDeleteOld = 'DELETE psp FROM `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` psp LEFT JOIN `' . _DB_PREFIX_ . 'specific_price` sp ON psp.id_specific_price=sp.id_specific_price WHERE sp.id_specific_price IS NULL;';
            JprestaUtils::dbExecuteSQL($queryDeleteOld);
        }

        public static function updateSpecificPrice($id, $id_product, $from, $to)
        {
            $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` (id_specific_price,id_product,date_from,date_to) VALUES ';
            $query .= '(' . (int) $id . ',' . (int) $id_product . ',\'' . pSQL($from) . '\',\'' . pSQL($to) . '\')';
            JprestaUtils::dbExecuteSQL($query . ' ON DUPLICATE KEY UPDATE date_from=\'' . pSQL($from) . '\', date_to=\'' . pSQL($to) . '\'');
        }

        public static function deleteSpecificPrice($id)
        {
            $query = 'DELETE FROM `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` WHERE id_specific_price=' . (int) $id . ';';
            JprestaUtils::dbExecuteSQL($query);
        }

        /**
         * Refresh cache if any specific price started or ended since last check
         */
        public static function triggerReffreshment()
        {
            // Use a lock system to avoid doing this treatment multiple time
            $lockFilePath = _PS_CACHE_DIR_ . '/jprestaCacheTriggerRefreshment.lock';

            // Open lock file
            $lockFile = fopen($lockFilePath, 'c');
            if (!$lockFile) {
                // Could not open lock file
                return;
            }

            // Attempt to acquire a non-blocking lock
            if (!flock($lockFile, LOCK_EX | LOCK_NB)) {
                // Lock not acquired, another process is running
                fclose($lockFile);

                return;
            }

            // Check if processing for the current date has already been done
            $nowFs = date('Y-m-d-H-i');
            $processedFilePath = _PS_CACHE_DIR_ . '/jprestaCacheTriggerRefreshment_' . $nowFs;
            if (file_exists($processedFilePath)) {
                // Processing already done, release lock and exit
                flock($lockFile, LOCK_UN);
                fclose($lockFile);

                return;
            }

            // Delete old "traitementFait" files, except the current one
            $files = glob(_PS_CACHE_DIR_ . '/jprestaCacheTriggerRefreshment_*');
            foreach ($files as $file) {
                if ($file !== $processedFilePath) {
                    unlink($file);
                }
            }

            $now = date('Y-m-d H:i:00');
            $query = 'SELECT DISTINCT id_product FROM `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` WHERE date_from<=\'' . pSQL($now) . '\' OR date_to<\'' . pSQL($now) . '\';';
            $rows = JprestaUtils::dbSelectRows($query);
            if (JprestaUtils::isIterable($rows)) {
                if (count($rows) > 0) {
                    // To avoid/limit deadlock errors I added "ORDER BY id_specific_price" as explained
                    // here: https://stackoverflow.com/questions/2332768/how-to-avoid-mysql-deadlock-found-when-trying-to-get-lock-try-restarting-trans

                    // Change date now to avoid other visitors to trigger refreshment
                    $queryUpdFrom = 'UPDATE `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` SET date_from=\'6666-01-01 00:00:00\' WHERE date_from<=\'' . pSQL($now) . '\' ORDER BY id_specific_price;';
                    $queryUpdTo = 'UPDATE `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` SET date_to=\'6666-01-01 00:00:00\' WHERE date_to<\'' . pSQL($now) . '\' ORDER BY id_specific_price;';
                    // Clean useless rows
                    $queryDel = 'DELETE FROM `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` WHERE date_from=\'6666-01-01 00:00:00\' AND date_to=\'6666-01-01 00:00:00\' ORDER BY id_specific_price;';
                    // Executes all in one within a transaction to avoid Dead locks
                    if (!JprestaUtils::dbExecuteSQL("START TRANSACTION;\n$queryUpdFrom\n$queryUpdTo\n$queryDel\nCOMMIT;", false, false)) {
                        // Some configurations do not accept multiple SQL queries in the same statement
                        JprestaUtils::dbExecuteSQL($queryUpdFrom);
                        JprestaUtils::dbExecuteSQL($queryUpdTo);
                        JprestaUtils::dbExecuteSQL($queryDel);
                    }
                    foreach ($rows as $row) {
                        $id_product = (int) $row['id_product'];
                        // Clear product cache and linking pages because price has changed
                        if ($id_product !== 0) {
                            self::clearCacheOfObject('product', $id_product, true, 'specific price');
                        } else {
                            // Specific case where the specific rule is global (all products are concerned)
                            PageCacheDAO::clearCacheOfObject('index', null, false,
                                'start/end of a global price rule', Configuration::get('pagecache_logs'));
                            PageCacheDAO::clearCacheOfObject('category', false, false,
                                'start/end of a global price rule', Configuration::get('pagecache_logs'));
                            PageCacheDAO::clearCacheOfObject('product', false, false,
                                'start/end of a global price rule', Configuration::get('pagecache_logs'));
                        }
                    }
                }
            }

            // Create the "traitementFait" file
            file_put_contents($processedFilePath, "Refreshment done for $now");

            // Release lock and close the file
            flock($lockFile, LOCK_UN);
            fclose($lockFile);
        }

        /**
         * @param int $nbHourExpired Number of hour since the cache is expired, can be negative to pages that are about to expire
         * @param bool $maxRows Max number of returned rows
         * @param bool $deleted false if you want pages that have available cache, true for pages where the cache has been deleted, null if it does not matter
         * @param array $obsoleteContextsIds List of context IDs that are obsolete (and can be purged)
         *
         * @return array cached pages
         */
        public static function getCachedPages($nbHourExpired, $maxRows = false, $deleted = false, $obsoleteContextsIds = [])
        {
            $rowsToReturn = [];
            $query = 'SELECT * FROM `' . _DB_PREFIX_ . self::TABLE . '`';
            $whereClauses = [];

            foreach (Jprestaspeedpack::getManagedControllers() as $controller => $controllerInfos) {
                $configuredMaxAge = 60 * ((int) Configuration::get('pagecache_' . $controller . '_timeout'));
                if ($configuredMaxAge < 0) {
                    // Never expire
                    continue;
                } elseif ($configuredMaxAge === 0) {
                    // Cache is disabled
                    $whereClauses[] = 'id_controller=' . JprestaUtils::dbToInt($controllerInfos['id']);
                } else {
                    $minAgeToReturn = max(0, $configuredMaxAge + ((int) $nbHourExpired * 60 * 60));
                    $whereClauses[] = '(id_controller=' . JprestaUtils::dbToInt($controllerInfos['id']) . ' AND last_gen < (NOW() - INTERVAL ' . (int) $minAgeToReturn . ' SECOND))';
                }
            }
            if (count($whereClauses) > 0) {
                $query .= ' WHERE (' . implode(' OR ', $whereClauses) . ')';
                if ($deleted !== null) {
                    $query .= ' AND `deleted`=' . ($deleted ? 1 : 0);
                }
                if (count($obsoleteContextsIds) > 0) {
                    // If the context is obsolete it does not matter if the cache is available or not, it will not be used
                    // anymore
                    $query .= ' OR id_context IN (' . implode(',', $obsoleteContextsIds) . ')';
                }
            } else {
                if (count($obsoleteContextsIds) > 0) {
                    // If the context is obsolete it does not matter if the cache is available or not, it will not be used
                    // anymore
                    $query .= ' WHERE id_context IN (' . implode(',', $obsoleteContextsIds) . ')';
                } else {
                    // Nothing to return
                    return [];
                }
            }
            $query .= ' ORDER BY last_gen ASC';
            if ($maxRows !== false) {
                $query .= ' LIMIT ' . ((int) $maxRows - count($rowsToReturn));
            }

            return JprestaUtils::dbSelectRows($query);
        }

        /**
         * @param $rows array Rows returned by self::getCachedPages()
         * @param bool $deleteStats
         */
        public static function deleteCachedPages($rows, $deleteStats = false)
        {
            if (JprestaUtils::isIterable($rows) && count($rows) > 0) {
                $cacheIdsToDelete = [];
                foreach ($rows as $row) {
                    Jprestaspeedpack::getCache()->delete($row['url'], self::getContextKeyById($row['id_context']));
                    $cacheIdsToDelete[] = (int) $row['id'];
                }
                if ($deleteStats) {
                    // Delete all rows
                    $whereClause = 'id IN (' . pSQL(implode(',', array_map('intval', $cacheIdsToDelete))) . ')';
                    JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE ' . $whereClause);
                    JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '` WHERE ' . $whereClause);
                    JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . self::TABLE_MODULE . '` WHERE ' . $whereClause);
                } else {
                    // Mark deleted cache contents as deleted in Db
                    $query = 'UPDATE `' . _DB_PREFIX_ . self::TABLE . '` SET `deleted`=1 WHERE `id` IN (' . pSQL(implode(',', array_map('intval', $cacheIdsToDelete))) . ')';
                    JprestaUtils::dbExecuteSQL($query);
                }
            }
        }

        public static function hasTriggerIn2H()
        {
            $now = date('Y-m-d H:i:00');
            $now_plus_2h = new DateTime();
            $now_plus_2h->modify('+2 hour');
            $now_plus_2h = $now_plus_2h->format('Y-m-d H:i:00');
            $hasTriggerIn2H = (int) JprestaUtils::dbGetValue('SELECT * FROM `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '` WHERE (date_from >= \'' . pSQL($now) . '\' AND date_from <= \'' . pSQL($now_plus_2h) . '\') OR (date_to >= \'' . pSQL($now) . '\'   AND date_to <= \'' . pSQL($now_plus_2h) . '\');') > 0;
            if (!$hasTriggerIn2H && JprestaUtils::isModuleEnabled('groupinc')) {
                // Scheduled configs are not taken into account here
                $hasTriggerIn2H = (int) JprestaUtils::dbGetValue('SELECT * FROM `' . _DB_PREFIX_ . 'groupinc_configuration` WHERE (date_from >= \'' . pSQL($now) . '\' AND date_from <= \'' . pSQL($now_plus_2h) . '\') OR (date_to >= \'' . pSQL($now) . '\'   AND date_to <= \'' . pSQL($now_plus_2h) . '\');', false, false) > 0;
            }

            return $hasTriggerIn2H;
        }

        public static function dropTables()
        {
            JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE_MODULE . '`;');
            JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '`;');
            JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE . '`;');
            JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE_DETAILS . '`;');
            JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '`;');
            JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE_PERFS . '`;');
            JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE_SPECIFIC_PRICES . '`;');
            JprestaUtils::dbExecuteSQL('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . self::TABLE_PROFILING . '`;');
        }

        /**
         * @param $jprestaCacheKey JprestaCacheKey
         * @param $cacheSource int 0=no cache, 1=server, 2=browser, 3=static, 4=back/forward cache
         *
         * @throws PrestaShopDatabaseException
         */
        public static function incrementCountHitMissed($jprestaCacheKey, $cacheSource)
        {
            // Stats for URL
            if ($cacheSource > 0) {
                // The missed are incremented in the insert() function
                $url = $jprestaCacheKey->get('url');
                $cleanUrl = preg_replace(
                    '/(?:action|ajax|stats|page_cache_dynamics_mods)=[^&]*(?:&|$)/',
                    '',
                    $url
                );
                $cleanUrl = rtrim($cleanUrl, '&?');
                $jprestaCacheKey->add('url', $cleanUrl);
                JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . self::TABLE . '` SET count_hit=count_hit+1
                WHERE `cache_key`=' . JprestaUtils::dbToInt($jprestaCacheKey->toInt()));
            }

            // Stats for context
            switch ($cacheSource) {
                case 0:
                    JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` SET count_missed=count_missed+1
                    WHERE id=' . (int) self::getOrCreateContextId($jprestaCacheKey) . ';');
                    break;
                case 1:
                    JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` SET count_hit_server=count_hit_server+1
                    WHERE id=' . (int) self::getOrCreateContextId($jprestaCacheKey) . ';');
                    break;
                case 3:
                    JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` SET count_hit_static=count_hit_static+1
                    WHERE id=' . (int) self::getOrCreateContextId($jprestaCacheKey) . ';');
                    break;
                case 2:
                    JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` SET count_hit_browser=count_hit_browser+1
                    WHERE id=' . (int) self::getOrCreateContextId($jprestaCacheKey) . ';');
                    break;
                case 4:
                    JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` SET count_hit_bfcache=count_hit_bfcache+1
                    WHERE id=' . (int) self::getOrCreateContextId($jprestaCacheKey) . ';');
                    break;
            }
        }

        /**
         * @param $jprestaCacheKey JprestaCacheKey
         */
        public static function incrementCountBot($jprestaCacheKey)
        {
            JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` SET count_bot=count_bot+1
            WHERE id=' . (int) self::getOrCreateContextId($jprestaCacheKey) . ';');
        }

        /**
         * @param $id_shop int
         * @param $controller string Controller name
         * @param $ttfb int TTFB in ms
         * @param $cacheSource int 0=no cache, 1=server, 2=browser, 3=static, 4=back/forward cache
         */
        public static function addStatsPerf($id_shop, $controller, $ttfb, $cacheSource)
        {
            $colName = null;
            switch ($cacheSource) {
                case 0:
                    $colName = 'ttfb_ms_missed';
                    break;
                case 1:
                    $colName = 'ttfb_ms_hit_server';
                    break;
                case 3:
                    $colName = 'ttfb_ms_hit_static';
                    break;
                case 2:
                    $colName = 'ttfb_ms_hit_browser';
                    break;
                case 4:
                    $colName = 'ttfb_ms_hit_bfcache';
                    break;
            }
            if ($id_shop && $colName && $ttfb !== null) {
                $id_controller = Jprestaspeedpack::getManagedControllerId($controller);
                if ($id_controller) {
                    $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE_PERFS . '` (`' . $colName . '`,`id_controller`, `id_shop`, day_add)
                        VALUES (' . (int) $ttfb . ',' . JprestaUtils::dbToInt($id_controller) . ',' . JprestaUtils::dbToInt($id_shop) . ',CURRENT_DATE());';
                    JprestaUtils::dbExecuteSQL($query);
                    JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . self::TABLE_PERFS . '` WHERE date_add < CURRENT_DATE() - interval 40 DAY;');
                }
                // else ignore
            }
        }

        public static function getMostUsedSpecifics($id_shop, $limit = 4)
        {
            $query = 'SELECT cd.`id` as id_specifics, cd.`details` as `specifics`, sum(1) AS `count`
              FROM `' . _DB_PREFIX_ . self::TABLE . '` AS cc
              INNER JOIN `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` AS ctx
              ON cc.id_context = ctx.id
              LEFT JOIN `' . _DB_PREFIX_ . self::TABLE_DETAILS . '` AS cd
              ON ctx.id_specifics = cd.id
              WHERE ctx.id_shop=' . (int) $id_shop . ' AND ctx.active=1
              GROUP BY cd.`id`
              ORDER BY 3 DESC
              LIMIT ' . (int) $limit;

            return JprestaUtils::dbSelectRows($query);
        }

        /**
         * @param $id_details
         *
         * @return string
         */
        public static function getDetailsById($id_details)
        {
            $query = 'SELECT `details` FROM `' . _DB_PREFIX_ . self::TABLE_DETAILS . '` AS cd WHERE cd.id = ' . (int) $id_details;

            return JprestaUtils::dbGetValue($query);
        }

        /**
         * @param $id_context int
         *
         * @return array
         */
        public static function getContextById($id_context)
        {
            static $contexts = [];
            if (!isset($contexts[$id_context])) {
                $query = 'SELECT * FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` WHERE id = ' . (int) $id_context;
                $rows = JprestaUtils::dbSelectRows($query);
                if (count($rows) >= 1) {
                    $contexts[$id_context] = $rows[0];
                } else {
                    $contexts[$id_context] = null;
                }
            }

            return $contexts[$id_context];
        }

        /**
         * @param $id_shop int
         *
         * @return array
         */
        public static function getAllContexts($id_shop)
        {
            $query = 'SELECT * FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` WHERE id_shop=' . (int) $id_shop;

            return JprestaUtils::dbSelectRows($query);
        }

        /**
         * @param $id_shop int
         * @param $active boolean
         *
         * @return array
         */
        public static function getAllContextsAndDetails($id_shop, $active = true)
        {
            $idCtxDetails = [];
            $query = 'SELECT ctx.id as id_ctx, det.details as details FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` ctx LEFT JOIN `' . _DB_PREFIX_ . self::TABLE_DETAILS . '` det ON ctx.id_specifics=det.id WHERE ctx.active=' . ($active ? '1' : '0') . ' AND id_shop=' . (int) $id_shop;
            $rows = JprestaUtils::dbSelectRows($query);
            if ($rows) {
                foreach ($rows as $row) {
                    $idCtxDetails[(int) $row['id_ctx']] = $row['details'];
                }
            }

            return $idCtxDetails;
        }

        /**
         * @param $id_ctx int
         */
        public static function disableContext($id_ctx)
        {
            $query = 'UPDATE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` SET active=0 WHERE id=' . (int) $id_ctx;
            JprestaUtils::dbExecuteSQL($query);
        }

        /**
         * @param $id_ctx int
         */
        public static function enableContext($id_ctx)
        {
            $query = 'UPDATE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` SET active=1 WHERE id=' . (int) $id_ctx;
            JprestaUtils::dbExecuteSQL($query);
        }

        public static function getObsoleteContextsIds($id_shop)
        {
            $ids = [];
            $whereClauses = [];

            // If CSS or JS version has been incremented or option has been modified
            if (Configuration::get('pagecache_depend_on_css_js')) {
                $whereClauses[] = 'v_css IS NULL OR v_css<>' . (int) Configuration::get('PS_CCCCSS_VERSION');
                $whereClauses[] = 'v_js IS NULL OR v_js<>' . (int) Configuration::get('PS_CCCJS_VERSION');
            } else {
                $whereClauses[] = 'v_css IS NOT NULL';
                $whereClauses[] = 'v_js IS NOT NULL';
            }

            // If a currency is not cached anymore
            $pagecache_currencies_to_cache = explode(',', Configuration::get('pagecache_currencies_to_cache'));
            if (count($pagecache_currencies_to_cache) > 0) {
                $curIds = [];
                foreach ($pagecache_currencies_to_cache as $cur_iso) {
                    $curIds[] = Currency::getIdByIsoCode($cur_iso);
                }
                $whereClauses[] = 'id_currency NOT IN (' . implode(',', $curIds) . ')';
            }

            // If a fake customer does not exist anymore
            $obsoleteCustomerId = [];
            $query = 'SELECT DISTINCT id_fake_customer FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` WHERE id_fake_customer IS NOT NULL;';
            $rows = JprestaUtils::dbSelectRows($query);
            foreach ($rows as $row) {
                if (!JprestaUtils::dbGetValue('SELECT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'customer` WHERE id_customer=' . (int) $row['id_fake_customer'] . ' LIMIT 1)')) {
                    $obsoleteCustomerId[] = $row['id_fake_customer'];
                }
            }
            if (count($obsoleteCustomerId) > 0) {
                $whereClauses[] = 'id_fake_customer IN (' . implode(',', $obsoleteCustomerId) . ')';
            }

            // If it is marked as inactive (obsolete)
            $whereClauses[] = 'active=0';

            if (count($whereClauses) > 0) {
                $query = 'SELECT id FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` WHERE id_shop=' . (int) $id_shop . ' AND (' . implode(' OR ', $whereClauses) . ')';
                $rows = JprestaUtils::dbSelectRows($query);
                foreach ($rows as $row) {
                    $ids[] = $row['id'];
                }
            }

            return $ids;
        }

        public static function deleteUnusedContexts()
        {
            $query = 'DELETE ctx FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` ctx LEFT JOIN `' . _DB_PREFIX_ . self::TABLE . '` c ON ctx.id=c.id_context WHERE c.id_context IS NULL;';
            JprestaUtils::dbExecuteSQL($query);
        }

        public static function deleteUnusedFakeUsers()
        {
            $query = 'DELETE c FROM `' . _DB_PREFIX_ . 'customer` c WHERE email like \'%@fakeemail.com\' AND active=0 AND id_customer NOT IN (SELECT id_fake_customer FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` WHERE id_fake_customer IS NOT NULL)';
            JprestaUtils::dbExecuteSQL($query);
        }

        /**
         * @param $id_shop int
         * @param $id_lang int
         * @param $id_currency int
         * @param $id_device int
         * @param $id_country int
         * @param $id_fake_customer int
         * @param $id_tax_csz int|null
         * @param $id_specifics int
         *
         * @return int|null
         */
        public static function getContextIdByInfos(
            $id_shop,
            $id_lang,
            $id_currency,
            $id_device,
            $id_country,
            $id_fake_customer,
            $id_tax_csz,
            $id_specifics
        ) {
            $whereClauses = [];
            $orderBy = '';
            $whereClauses[] = 'active=1';
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_shop', $id_shop);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_lang', $id_lang);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_currency', $id_currency);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_fake_customer', $id_fake_customer);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_device', $id_device);
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_country', $id_country);
            if ($id_tax_csz) {
                $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_tax_csz', $id_tax_csz);
            } else {
                $orderBy = ' ORDER BY used_by_cw DESC, date_add DESC';
            }
            $whereClauses[] = JprestaUtils::dbWhereIntEqual('id_specifics', $id_specifics);
            if (Configuration::get('pagecache_depend_on_css_js')) {
                $whereClauses[] = JprestaUtils::dbWhereIntEqual('v_css', Configuration::get('PS_CCCCSS_VERSION'));
                $whereClauses[] = JprestaUtils::dbWhereIntEqual('v_js', Configuration::get('PS_CCCJS_VERSION'));
            }

            return JprestaUtils::dbGetValue('SELECT id FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` WHERE ' . implode(' AND ', $whereClauses) . $orderBy . ' LIMIT 1');
        }

        /**
         * @param $jprestaCacheKey JprestaCacheKey
         *
         * @return int
         */
        public static function getOrCreateContextId($jprestaCacheKey)
        {
            static $contexts = [];
            if (!array_key_exists($jprestaCacheKey->toInt(), $contexts)) {
                $id = self::getContextIdByInfos(
                    $jprestaCacheKey->get('id_shop'),
                    $jprestaCacheKey->get('id_lang'),
                    $jprestaCacheKey->get('id_currency'),
                    $jprestaCacheKey->get('id_device'),
                    $jprestaCacheKey->get('id_country'),
                    $jprestaCacheKey->get('id_fake_customer'),
                    $jprestaCacheKey->get('id_tax_manager'),
                    $jprestaCacheKey->get('id_specifics')
                );
                if (!$id) {
                    // Create the new context

                    // uniq_key is used to have a uniq index. We cannot do it on multiple columns because they are nullable
                    $uniq_key = crc32(JprestaUtils::dbToInt($jprestaCacheKey->get('id_shop'))
                        . '|' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_lang'))
                        . '|' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_currency'))
                        . '|' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_fake_customer'))
                        . '|' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_device'))
                        . '|' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_country'))
                        . '|' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_tax_manager'))
                        . '|' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_specifics'))
                        . '|' . JprestaUtils::dbToInt($jprestaCacheKey->get('css_version'))
                        . '|' . JprestaUtils::dbToInt($jprestaCacheKey->get('js_version')));

                    $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` (`context_key`,`id_shop`,`id_lang`,`id_currency`,`id_fake_customer`,`id_device`,`id_country`,`id_tax_csz`,`id_specifics`,`v_css`,`v_js`, `uniq_key`) VALUES (
                            UUID(),
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_shop')) . ',
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_lang')) . ',
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_currency')) . ',
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_fake_customer')) . ',
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_device')) . ',
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_country')) . ',
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_tax_manager')) . ',
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('id_specifics')) . ',
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('css_version')) . ',
                            ' . JprestaUtils::dbToInt($jprestaCacheKey->get('js_version')) . ',
                            ' . sprintf('%u', $uniq_key);
                    // We use the ON DUPLICATE... to be thread safe
                    $query .= ') ON DUPLICATE KEY UPDATE id=id;';
                    JprestaUtils::dbExecuteSQL($query);
                    // Make sure to get the ID (don't use lastInsertId...)
                    $contexts[$jprestaCacheKey->toInt()] = JprestaUtils::dbGetValue('SELECT id FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` WHERE uniq_key=' . sprintf('%u', $uniq_key));
                } else {
                    $contexts[$jprestaCacheKey->toInt()] = $id;
                }
            }

            return $contexts[$jprestaCacheKey->toInt()];
        }

        public static function getContextKeyById($id_context)
        {
            static $contextsKeys = [];
            if (!array_key_exists($id_context, $contextsKeys)) {
                $contextsKeys[$id_context] = JprestaUtils::dbGetValue('SELECT context_key FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` WHERE id = ' . (int) $id_context);
            }

            return $contextsKeys[$id_context];
        }

        /**
         * @param $jprestaCacheKey JprestaCacheKey
         *
         * @return array Array with 3 keys: 'hit', 'missed' and 'age' in seconds
         */
        public static function getStats($jprestaCacheKey)
        {
            $query = 'SELECT count_hit, count_missed, TIMESTAMPDIFF(SECOND, last_gen, NOW()) as age FROM `' . _DB_PREFIX_ . self::TABLE . '`
                WHERE `cache_key`=' . JprestaUtils::dbToInt($jprestaCacheKey->toInt());
            $result = JprestaUtils::dbSelectRows($query);
            if (JprestaUtils::isIterable($result) && count($result) == 1) {
                return [
                    'hit' => (int) $result[0]['count_hit'],
                    'missed' => (int) $result[0]['count_missed'],
                    'age' => (int) $result[0]['age'],
                ];
            }

            return ['hit' => 0, 'missed' => 0, 'age' => 0];
        }

        /**
         * @return int Number of rows of the main table
         */
        public static function getMainRowsCount()
        {
            static $rowCount = null;
            if ($rowCount === null) {
                $rowCount = JprestaUtils::dbGetValue('SELECT count(*) FROM `' . _DB_PREFIX_ . self::TABLE . '`');
            }

            return $rowCount;
        }

        /**
         * @param $jprestaCacheKey JprestaCacheKey Cache key
         * @param $cache_ttl integer configured timeout in minutes
         *
         * @return int Number of minutes the page will leave in cache
         */
        public static function getTtl($jprestaCacheKey, $cache_ttl_minutes)
        {
            $query = 'SELECT `deleted`, TIMESTAMPDIFF(MINUTE, last_gen, NOW()) as age  FROM `' . _DB_PREFIX_ . self::TABLE . '`
                WHERE `cache_key`=' . JprestaUtils::dbToInt($jprestaCacheKey->toInt());
            $result = JprestaUtils::dbSelectRows($query);
            if (JprestaUtils::isIterable($result) && count($result) == 1) {
                if ($result[0]['deleted']) {
                    return 0;
                } else {
                    return max(0, $cache_ttl_minutes - $result[0]['age']);
                }
            }

            return 0;
        }

        /**
         * @param $jprestaCacheKey JprestaCacheKey
         *
         * @return array|mixed
         *
         * @throws PrestaShopDatabaseException
         */
        public static function getStatsByCacheKey($jprestaCacheKey)
        {
            /**
             * ATTENTION: this code is called a lot of times by cache-warmer, it has been optimized, don't change anything
             */
            $query = 'SELECT sum(count_hit) as sum_hit, sum(count_missed) as sum_missed, TIMESTAMPDIFF(MINUTE, min(last_gen), NOW()) as max_age_minutes, max(deleted) as deleted FROM `' . _DB_PREFIX_ . self::TABLE . '`
            WHERE `cache_key`=' . JprestaUtils::dbToInt($jprestaCacheKey->toInt());
            $result = JprestaUtils::dbGetRow($query);
            if ($result && $result['sum_hit'] !== null) {
                return $result;
            }

            return [
                'count' => 0,
                'sum_hit' => 0,
                'sum_missed' => 0,
                'max_age_minutes' => PHP_INT_MAX,
                'deleted' => 0,
            ];
        }

        public static function getStatsByContext(
            $id_context
        ) {
            $query = 'SELECT sum(1) as count, sum(deleted) as count_deleted FROM `' . _DB_PREFIX_ . self::TABLE . '` WHERE id_context=' . (int) $id_context;
            $result = JprestaUtils::dbGetRow($query);
            if ($result && $result['count'] !== null) {
                return $result;
            }

            return [
                'count' => 0,
                'count_deleted' => 0,
            ];
        }

        public static function getPerformances($id_shop)
        {
            $performances['count_total'] = 0;
            $performances['count_context'] = 0;
            $performances['count_hit'] = 0;
            $performances['count_hit_server'] = 0;
            $performances['count_hit_static'] = 0;
            $performances['count_hit_browser'] = 0;
            $performances['count_hit_bfcache'] = 0;
            $performances['count_missed'] = 0;
            $performances['percent_hit'] = 0.00;
            $performances['percent_hit_server'] = 0.00;
            $performances['percent_hit_static'] = 0.00;
            $performances['percent_hit_browser'] = 0.00;
            $performances['percent_hit_bfcache'] = 0.00;
            $performances['percent_missed'] = 0.00;
            $performances['start_date'] = null;
            $query = 'SELECT sum(1) as `count_context`,
                       sum(count_hit_server)+sum(count_hit_static)+sum(count_hit_browser)+sum(count_hit_bfcache) as count_hit,
                       sum(count_hit_server) as count_hit_server,
                       sum(count_hit_static) as count_hit_static,
                       sum(count_hit_browser) as count_hit_browser,
                       sum(count_hit_bfcache) as count_hit_bfcache,
                       sum(count_missed) as count_missed,
                       UNIX_TIMESTAMP(MIN(date_add)) as start_date
                       FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` WHERE id_shop=' . (int) $id_shop;
            $rows = JprestaUtils::dbSelectRows($query);
            if (count($rows) > 0) {
                $row = $rows[0];
                $totalClics = (int) $row['count_missed'] + (int) $row['count_hit'];
                if ($totalClics > 0) {
                    $start_date = new DateTime();
                    $start_date->setTimestamp($row['start_date']);
                    $performances['start_date'] = $start_date;
                    $performances['count_context'] = (int) $row['count_context'];
                    $performances['count_total'] = (int) $row['count_hit'] + (int) $row['count_missed'];
                    $performances['count_hit'] = (int) $row['count_hit'];
                    $performances['count_missed'] = (int) $row['count_missed'];
                    $performances['count_hit_server'] = (int) $row['count_hit_server'];
                    $performances['count_hit_static'] = (int) $row['count_hit_static'];
                    $performances['count_hit_browser'] = (int) $row['count_hit_browser'];
                    $performances['count_hit_bfcache'] = (int) $row['count_hit_bfcache'];
                    $performances['percent_hit'] = round($row['count_hit'] * 100 / $totalClics, 2);
                    $performances['percent_hit_server'] = round($row['count_hit_server'] * 100 / $totalClics, 2);
                    $performances['percent_hit_static'] = round($row['count_hit_static'] * 100 / $totalClics, 2);
                    $performances['percent_hit_browser'] = round($row['count_hit_browser'] * 100 / $totalClics, 2);
                    $performances['percent_hit_bfcache'] = round($row['count_hit_bfcache'] * 100 / $totalClics, 2);
                    $performances['percent_missed'] = round($row['count_missed'] * 100 / $totalClics, 2);
                }
            }

            return $performances;
        }

        /**
         * @param $details string
         *
         * @return int|null
         */
        public static function getOrCreateDetailsId($details, $donotcreate = false)
        {
            $id_details = null;
            if ($details) {
                $db = Db::getInstance();
                $query = 'SELECT id FROM `' . _DB_PREFIX_ . self::TABLE_DETAILS . '` WHERE `details_md5`=MD5(' . JprestaUtils::dbToString($db,
                    $details) . ');';
                $id_details = JprestaUtils::dbGetValue($query);
                if (!$donotcreate && !$id_details) {
                    $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE_DETAILS . '` (`details`,`details_md5`) VALUES (' . JprestaUtils::dbToString($db,
                        $details) . ',MD5(' . JprestaUtils::dbToString($db, $details) . '));';
                    JprestaUtils::dbExecuteSQL($query);
                    $id_details = $db->Insert_ID();
                }
            } else {
                return null;
            }

            return (int) $id_details;
        }

        /**
         * @param $jprestaCacheKey JprestaCacheKey Cache key with informations
         * @param $controller string Controller that manage the URL
         * @param $id_shop integer
         * @param $id_object integer ID of the object (product, category, supplier, etc.) if any
         * @param $module_ids array IDs of called module on this page
         * @param $backlinks_cache_keys int[] List of cache keys present in this page
         * @param int $log_level
         * @param bool $stats_it False when it is from Cache-Warmer
         *
         * @throws PrestaShopDatabaseException
         */
        public static function insert(
            $jprestaCacheKey,
            $controller,
            $id_shop,
            $id_object,
            $module_ids,
            $backlinks_cache_keys,
            $log_level = 0,
            $stats_it = true
        ) {
            $startTime1 = microtime(true);

            $db = Db::getInstance();

            //
            // Insert a new row or update stats if it exists
            //
            if ($stats_it) {
                $onDuplicateQuery = '`count_missed`=`count_missed` + 1, last_gen = CURRENT_TIMESTAMP, `deleted` = 0';
            } else {
                $onDuplicateQuery = 'last_gen = CURRENT_TIMESTAMP, `deleted` = 0';
            }
            $id_context = self::getOrCreateContextId($jprestaCacheKey);
            $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE . '` (`cache_key`, `url`, `id_context`, `id_controller`, `id_shop`, `id_object`, `count_missed`, `count_hit`)
                VALUES (
                ' . JprestaUtils::dbToInt($jprestaCacheKey->toInt()) . ',
                ' . JprestaUtils::dbToString($db, $jprestaCacheKey->get('url')) . ',
                ' . JprestaUtils::dbToInt($id_context) . ',
                ' . JprestaUtils::dbToInt(Jprestaspeedpack::getManagedControllerId($controller)) . ',
                ' . JprestaUtils::dbToInt($id_shop) . ',
                ' . JprestaUtils::dbToInt($id_object) . ',
                ' . ($stats_it ? '1' : '0') . ',
                0) ON DUPLICATE KEY UPDATE ' . $onDuplicateQuery . ';';

            JprestaUtils::dbExecuteSQL($query);
            $insertedId = Db::getInstance()->Insert_ID();
            if ($insertedId) {
                $id_pagecache = $insertedId;
            } else {
                // Get the ID of the updated row
                $query = 'SELECT id FROM `' . _DB_PREFIX_ . self::TABLE . '`
                    WHERE `cache_key`=' . JprestaUtils::dbToInt($jprestaCacheKey->toInt());
                $id_pagecache = JprestaUtils::dbGetValue($query, false);
                if (!$id_pagecache) {
                    // Should not be here...
                    return;
                }
            }

            //
            // MODULES
            //
            $startTime3 = microtime(true);
            $startTime4 = microtime(true);
            JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . self::TABLE_MODULE . '` WHERE `id`=' . (int) $id_pagecache . ';');
            if (count($module_ids) > 0) {
                $module_query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE_MODULE . '` (`id`, `id_module`) VALUES ';
                $idx = 0;
                foreach ($module_ids as $id_module) {
                    $module_query .= '(' . $id_pagecache . ',' . $id_module . ')';
                    ++$idx;
                    if ($idx < count($module_ids)) {
                        $module_query .= ',';
                    }
                }
                $startTime4 = microtime(true);
                JprestaUtils::dbExecuteSQL($module_query . ' ON DUPLICATE KEY UPDATE id=id');
            }

            //
            // BACKLINKS
            //
            $startTime5 = microtime(true);
            $startTime6 = microtime(true);

            // Retrieve the current keys in the database for the given id
            $currentKeysQuery = 'SELECT `backlink_key` FROM `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '` WHERE `id` = ' . (int) $id_pagecache;
            $currentKeysResult = JprestaUtils::dbSelectRows($currentKeysQuery);
            $currentKeys = array_column($currentKeysResult, 'backlink_key');

            // Convert new keys to integers for comparison
            $newKeys = array_map('intval', $backlinks_cache_keys);

            // Determine keys to delete (present in the database but not in the new set)
            $keysToDelete = array_diff($currentKeys, $newKeys);

            // Determine keys to insert (new but not present in the database)
            $keysToInsert = array_diff($newKeys, $currentKeys);

            // Delete old keys that are no longer needed
            if (!empty($keysToDelete)) {
                $deleteQuery = 'DELETE FROM `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '` WHERE `id` = ' . (int) $id_pagecache . ' AND `backlink_key` IN (' . implode(',', $keysToDelete) . ')';
                JprestaUtils::dbExecuteSQL($deleteQuery);
            }

            // Insert only the necessary new keys
            if (!empty($keysToInsert)) {
                $insertQuery = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '` (`id`, `backlink_key`) VALUES ';
                $insertValues = [];
                foreach ($keysToInsert as $key) {
                    $insertValues[] = '(' . (int) $id_pagecache . ',' . JprestaUtils::dbToInt($key) . ')';
                }
                $insertQuery .= implode(',', $insertValues);
                $startTime6 = microtime(true);
                JprestaUtils::dbExecuteSQL($insertQuery . ' ON DUPLICATE KEY UPDATE id=id');
            }

            if (((int) $log_level) > 0) {
                JprestaUtils::addLog("PageCache | insert | added cache for $controller#$id_object during "
                    . number_format($startTime3 - $startTime1, 3) . '+'
                    . number_format($startTime4 - $startTime3, 3) . '+'
                    . number_format($startTime5 - $startTime4, 3) . '+'
                    . number_format($startTime6 - $startTime5, 3) . '+'
                    . number_format(microtime(true) - $startTime6, 3) . '='
                    . number_format(microtime(true) - $startTime1, 3)
                    . ' second(s) with ' . count($backlinks_cache_keys) . ' backlinks', 1, null, null, null, true);
            }
        }

        public static function clearCacheOfObject(
            $controller,
            $id_object,
            $delete_linking_pages,
            $action_origin = '',
            $log_level = 0
        ) {
            // Some code to avoid calling this method multiple times (can happen when saving a product for exemple)
            static $already_done = [];
            $key = $controller . '|' . $id_object . '|' . ($delete_linking_pages ? '1' : '0');
            if (array_key_exists($key, $already_done)) {
                return;
            }
            $already_done[$key] = true;
            if ($delete_linking_pages) {
                // When called with option $delete_linking_pages we can skip call without the option...
                $already_done[$controller . '|' . $id_object . '|0'] = true;
            }

            $startTime1 = microtime(true);
            if ($id_object !== null && $id_object !== false) {
                $query = 'SELECT id, id_shop, url, id_context, cache_key FROM `' . _DB_PREFIX_ . self::TABLE . '`
                WHERE id_controller=' . JprestaUtils::dbToInt(Jprestaspeedpack::getManagedControllerId($controller)) . ' AND id_object=' . ((int) $id_object) . ';';
            } elseif ($id_object === false) {
                $query = 'SELECT id, id_shop, url, id_context, cache_key FROM `' . _DB_PREFIX_ . self::TABLE . '`
                WHERE id_controller=' . JprestaUtils::dbToInt(Jprestaspeedpack::getManagedControllerId($controller));
            } else {
                $query = 'SELECT id, id_shop, url, id_context, cache_key FROM `' . _DB_PREFIX_ . self::TABLE . '`
                WHERE id_controller=' . JprestaUtils::dbToInt(Jprestaspeedpack::getManagedControllerId($controller)) . ' AND id_object IS NULL;';
            }
            $results = JprestaUtils::dbSelectRows($query);
            $startTime2 = microtime(true);

            $keys = [];
            $cacheIdsToDelete = [];
            $deletedCount = 0;
            if (JprestaUtils::isIterable($results)) {
                foreach ($results as $result) {
                    if (Jprestaspeedpack::getCache($result['id_shop'])->delete($result['url'], self::getContextKeyById($result['id_context']))) {
                        ++$deletedCount;
                    }
                    $keys[] = JprestaUtils::dbToInt(Jprestaspeedpack::getCacheKeyForBacklink($result['url']));
                    $cacheIdsToDelete[] = $result['id'];
                }
            }
            if (((int) $log_level) > 0 || $deletedCount > self::LOG_WHEN_MORE_THAN) {
                JprestaUtils::addLog("PageCache | $action_origin | reffreshed $deletedCount pages from $controller#$id_object during "
                    . number_format($startTime2 - $startTime1, 3) . '+'
                    . number_format(microtime(true) - $startTime2, 3) . '='
                    . number_format(microtime(true) - $startTime1, 3)
                    . ' second(s)', 1, null, null, null, true);
                $startTime1 = microtime(true);
            }
            if ($delete_linking_pages) {
                // Also add the default link of the object in case that the page has never been cached
                $default_links = self::_getDefaultLinks($controller, $id_object, $log_level);
                if (count($default_links) > 0) {
                    $remainingLinks = count($default_links);
                    foreach ($default_links as $default_link) {
                        $keys[] = JprestaUtils::dbToInt(Jprestaspeedpack::getCacheKeyForBacklink($default_link));
                        --$remainingLinks;
                    }
                }
                $startTime2 = microtime(true);
                $startTime3 = microtime(true);
                // Delete pages that link to these pages
                $deletedCount = 0;
                if (count($keys) > 0) {
                    $query = 'SELECT DISTINCT pc.id, pc.url, pc.id_context FROM `' . _DB_PREFIX_ . self::TABLE . '` AS pc
                    LEFT JOIN `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '` AS bl ON (bl.id = pc.id)
                    WHERE pc.deleted=0 AND `backlink_key` IN (' . implode(',', array_map('intval', $keys)) . ')';
                    $results = JprestaUtils::dbSelectRows($query);
                    $startTime3 = microtime(true);
                    if (JprestaUtils::isIterable($results)) {
                        $cache = Jprestaspeedpack::getCache();
                        foreach ($results as $result) {
                            if ($cache->delete($result['url'], self::getContextKeyById($result['id_context']))) {
                                ++$deletedCount;
                            }
                            $cacheIdsToDelete[] = $result['id'];
                        }
                    }
                }
                if (((int) $log_level) > 0 || $deletedCount > self::LOG_WHEN_MORE_THAN) {
                    JprestaUtils::addLog("PageCache | $action_origin | reffreshed $deletedCount pages that were linking to $controller#$id_object during "
                        . number_format($startTime2 - $startTime1, 3) . '+'
                        . number_format($startTime3 - $startTime2, 3) . '+'
                        . number_format(microtime(true) - $startTime3, 3) . '='
                        . number_format(microtime(true) - $startTime1, 3)
                        . ' second(s)', 1, null, null, null, true);
                }
            }
            if (count($cacheIdsToDelete) > 0) {
                // Mark deleted cache contents as deleted in Db
                $query_deleted = 'UPDATE `' . _DB_PREFIX_ . self::TABLE . '` SET `deleted`=1 WHERE `id` IN (' . implode(',', $cacheIdsToDelete) . ')';
                JprestaUtils::dbExecuteSQL($query_deleted);
            }
        }

        private static function _getDefaultLinks($controller, $id_object, $log_level = 0)
        {
            $links = [];
            try {
                if ($id_object != null) {
                    $context = Context::getContext();
                    if (!isset($context->link)) {
                        /* Link should be initialized in the context but sometimes it is not */
                        $https_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                        $context->link = new Link($https_link, $https_link);
                    }
                    foreach (Language::getLanguages(true) as $language) {
                        $id_lang = $language['id_lang'];
                        // TODO Get default links of other modules's contollers
                        switch ($controller) {
                            case 'cms':
                                $links[] = $context->link->getCMSLink((int) $id_object, null, null, $id_lang, null, true);
                                break;
                            case 'product':
                                $idShop = Shop::getContextShopID();
                                if (!is_object($context->cart)) {
                                    $context->cart = new Cart();
                                }
                                $product = new Product((int) $id_object, false, $id_lang, $idShop);
                                $ipass = Product::getProductAttributesIds((int) $id_object);
                                if (is_array($ipass)) {
                                    foreach ($ipass as $ipas) {
                                        foreach ($ipas as $ipa) {
                                            $links[] = $context->link->getProductLink($product, null, null, null, $id_lang, $idShop, $ipa, false, true);
                                        }
                                    }
                                }
                                $links[] = $context->link->getProductLink((int) $id_object, null, null, null, $id_lang, null, 0, false, true);
                                break;
                            case 'category':
                                $links[] = $context->link->getCategoryLink((int) $id_object, null, $id_lang, null, null, true);
                                break;
                            case 'manufacturer':
                                $links[] = $context->link->getManufacturerLink((int) $id_object, null, $id_lang, null, true);
                                break;
                            case 'supplier':
                                $links[] = $context->link->getSupplierLink((int) $id_object, null, $id_lang, null, true);
                                break;
                        }
                    }
                }
            } catch (Exception $e) {
                if (((int) $log_level) > 0) {
                    JprestaUtils::addLog('PageCache | Cannot get the default links: ' . $e->getMessage());
                }
            }

            return $links;
        }

        public static function clearCacheOfModule($module_name, $action_origin = '', $log_level = 0)
        {
            $startTime1 = microtime(true);
            $module = Module::getInstanceByName($module_name);
            if ($module instanceof Module) {
                $id_module = $module->id;
                if (!empty($id_module)) {
                    $query = 'SELECT pc.id, pc.url, pc.id_context FROM `' . _DB_PREFIX_ . self::TABLE . '` AS pc
                    LEFT JOIN `' . _DB_PREFIX_ . self::TABLE_MODULE . '` AS mods ON (mods.id = pc.id)
                    WHERE pc.deleted=0 AND `id_module`=' . ((int) $id_module);
                    $results = JprestaUtils::dbSelectRows($query);

                    $startTime2 = microtime(true);
                    $deletedCount = 0;
                    $cacheIdsToDelete = [];
                    if (JprestaUtils::isIterable($results)) {
                        foreach ($results as $result) {
                            if (Jprestaspeedpack::getCache()->delete($result['url'], self::getContextKeyById($result['id_context']))) {
                                ++$deletedCount;
                            }
                            $cacheIdsToDelete[] = $result['id'];
                        }
                    }
                    if (((int) $log_level) > 0 || $deletedCount > self::LOG_WHEN_MORE_THAN) {
                        JprestaUtils::addLog("PageCache | $action_origin | reffreshed $deletedCount pages containing module $module_name during "
                            . number_format($startTime2 - $startTime1, 3) . '+'
                            . number_format(microtime(true) - $startTime2, 3) . '='
                            . number_format(microtime(true) - $startTime1, 3)
                            . ' second(s)', 1, null, null, null, true);
                    }

                    if (count($cacheIdsToDelete) > 0) {
                        // Mark deleted cache contents as deleted in Db
                        $query_deleted = 'UPDATE `' . _DB_PREFIX_ . self::TABLE . '` SET `deleted`=1 WHERE `id` IN (' . pSQL(implode(',', $cacheIdsToDelete)) . ')';
                        JprestaUtils::dbExecuteSQL($query_deleted);
                    }
                }
            }
        }

        public static function resetCache($ids_shop = null)
        {
            if (empty($ids_shop)) {
                JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE . '`');
                JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE_DETAILS . '`');
                JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '`');
                JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '`');
                JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE_MODULE . '`');
            } else {
                JprestaUtils::dbExecuteSQL('DELETE bl, mods, pc, ctx FROM `' . _DB_PREFIX_ . self::TABLE . '` AS pc
                LEFT JOIN `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` AS ctx ON pc.id_context=ctx.id
                LEFT JOIN `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '` AS bl ON pc.id=bl.id
                LEFT JOIN `' . _DB_PREFIX_ . self::TABLE_MODULE . '` AS mods ON pc.id=mods.id
                WHERE pc.id_shop IS NULL OR pc.id_shop IN (' . pSQL(implode(',', array_map('intval', $ids_shop))) . ');');
            }
            self::deleteUnusedContexts();
        }

        public static function resetStatPerfs($ids_shop = null)
        {
            if (empty($ids_shop)) {
                JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE_PERFS . '`');
            } else {
                JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . self::TABLE_PERFS . '`
                WHERE id_shop IS NULL OR id_shop IN (' . pSQL(implode(',', array_map('intval', $ids_shop))) . ');');
            }
        }

        /**
         * Request to MySQL to refresh the number of rows
         */
        public static function analyzeTables()
        {
            JprestaUtils::dbExecuteSQL('ANALYZE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE . '`;', false, false);
            JprestaUtils::dbExecuteSQL('ANALYZE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_BACKLINK . '`;', false,
                false);
            JprestaUtils::dbExecuteSQL('ANALYZE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_MODULE . '`;', false,
                false);
            JprestaUtils::dbExecuteSQL('ANALYZE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_DETAILS . '`;', false,
                false);
            JprestaUtils::dbExecuteSQL('ANALYZE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_CONTEXTS . '`;', false,
                false);
            JprestaUtils::dbExecuteSQL('ANALYZE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_PROFILING . '`;', false,
                false);
            JprestaUtils::dbExecuteSQL('ANALYZE TABLE `' . _DB_PREFIX_ . PageCacheDAO::TABLE_SPECIFIC_PRICES . '`;',
                false, false);
        }

        public static function clearAllCache()
        {
            JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . self::TABLE . '` SET `deleted`=1;');
            JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE_BACKLINK . '`;');
            JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE_MODULE . '`;');
        }

        /**
         * @param $id_module
         * @param $description
         * @param $duration
         * @param int $max_records Maximum number of records
         *
         * @return bool true if the number of records is less than $max_records
         */
        public static function addProfiling($id_module, $description, $duration, $max_records = 1000)
        {
            try {
                $db = Db::getInstance();
                $query = 'INSERT INTO `' . _DB_PREFIX_ . self::TABLE_PROFILING . '` (`id_module`, `description`, `duration_ms`) VALUES (' . (int) $id_module . ', \'' . $db->escape($description) . '\', ' . (int) $duration . ');';
                JprestaUtils::dbExecuteSQL($query);

                return JprestaUtils::dbGetValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . self::TABLE_PROFILING) < $max_records;
            } catch (Exception $e) {
                error_log('Warning, cannot insert profiling datas ' . $e->getMessage());
            }

            return true;
        }

        public static function clearProfiling($minMs = 0)
        {
            try {
                if ($minMs === 0) {
                    JprestaUtils::dbExecuteSQL('TRUNCATE TABLE `' . _DB_PREFIX_ . self::TABLE_PROFILING . '`;');
                } else {
                    JprestaUtils::dbExecuteSQL('DELETE FROM `' . _DB_PREFIX_ . self::TABLE_PROFILING . '` WHERE `duration_ms` < ' . (int) $minMs . ';');
                }
            } catch (Exception $e) {
                error_log('Warning, cannot clear profiling datas ' . $e->getMessage());
            }
        }

        /**
         * @param $forMobile bool
         *
         * @return string
         */
        public static function getMostUsedContextKeyByBots($id_shop, $forMobile = false)
        {
            if ($forMobile) {
                $whereClause = ' WHERE active=1 AND id_shop=' . (int) $id_shop . ' AND id_fake_customer is NULL AND id_device=' . Jprestaspeedpack::DEVICE_MOBILE;
            } else {
                $whereClause = ' WHERE active=1 AND id_shop=' . (int) $id_shop . ' AND id_fake_customer is NULL AND id_device=' . Jprestaspeedpack::DEVICE_COMPUTER;
            }

            return JprestaUtils::dbGetValue('SELECT context_key FROM `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '`' . $whereClause . ' ORDER BY count_bot DESC LIMIT 1');
        }

        public static function setContextUsedByCacheWarmer($id_context)
        {
            JprestaUtils::dbExecuteSQL('UPDATE `' . _DB_PREFIX_ . self::TABLE_CONTEXTS . '` SET used_by_cw=1 WHERE used_by_cw=0 AND id=' . (int) $id_context);
        }
    }
}
