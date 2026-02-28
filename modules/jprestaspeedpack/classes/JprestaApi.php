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
if (!class_exists('JprestaApi')) {
    class JprestaApi
    {
        const JPRESTA_PROTO = 'https://';
        const JPRESTA_DOMAIN_EXT = '.com';
        const JPRESTA_DOMAIN = 'jpresta';
        const JPRESTA_PATH_API_LICENSES = '/fr/module/jprestacrm/licenses';
        const JPRESTA_DOMAIN_ADMIN = 'admin.jpresta';
        const JPRESTA_PATH_URL_LICENSES = '/licenses.php';
        const JPRESTA_DOMAIN_CACHE_WARMER = 'cachewarmer.jpresta';
        const JPRESTA_PATH_URL_CACHE_WARMER = '/';
        const JPRESTA_DOMAIN_AUTOCONF = 'autoconf.jpresta';
        const JPRESTA_PATH_URL_AUTOCONF = '/autoconf.php';

        /**
         * @var string JPresta Account Key
         */
        private $jak;

        /**
         * @var string The string that identify this Prestashop instance
         */
        private $psToken;

        /**
         * JprestaApi constructor.
         *
         * @param string $jak
         * @param string $psToken
         */
        public function __construct($jak, $psToken)
        {
            $this->jak = $jak;
            $this->psToken = $psToken;
        }

        /**
         * @return string[] All installed JPresta module names
         */
        private static function getJPrestaModules()
        {
            $modulesName = [];
            $rows = JprestaUtils::dbSelectRows('SELECT name FROM `' . _DB_PREFIX_ . 'module` WHERE name LIKE \'jpresta%\' OR name IN (\'pagecache\',\'pagecachestd\')');
            foreach ($rows as $row) {
                $modulesName[] = $row['name'];
            }

            return $modulesName;
        }

        /**
         * @param $psIsTest boolean true if this is a Prestashop instance for test, not production
         *
         * @return bool|string true if ok, error message if not ok
         */
        public function attach($psIsTest)
        {
            if (function_exists('curl_init')) {
                $curl = curl_init();

                $defaultShop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
                $post_data = [
                    'action' => 'attach_module',
                    'ajax' => 1,
                    'ps_token' => $this->psToken,
                    'shop_url' => $defaultShop->getBaseURL(true),
                    'ps_version' => _PS_VERSION_,
                    'modules' => implode(',', self::getJPrestaModules()),
                    'ps_is_test' => (bool) $psIsTest,
                ];

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_URL,
                    self::JPRESTA_PROTO . self::JPRESTA_DOMAIN . self::JPRESTA_DOMAIN_EXT . self::JPRESTA_PATH_API_LICENSES);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    'x-jpresta-account-key: ' . $this->jak,
                    'User-Agent: ' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Page Cache Ultimate'),
                ]);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_MAXREDIRS, 5);

                $content = curl_exec($curl);

                if (false === $content) {
                    $res = sprintf('error code %d - %s',
                        curl_errno($curl),
                        curl_error($curl)
                    );
                } else {
                    $jsonContent = json_decode($content, true);
                    if (!is_array($jsonContent) || !array_key_exists('status', $jsonContent)) {
                        $res = 'JPresta server returned response in incorrect format';
                    } else {
                        if ($jsonContent['status'] === 'ok') {
                            $res = true;
                        } else {
                            if (array_key_exists('message', $jsonContent)) {
                                $res = $jsonContent['message'];
                            } else {
                                $res = 'The account has not been attached for an unknown reason';
                            }
                        }
                    }
                }

                curl_close($curl);
            } else {
                $res = 'CURL must be available';
            }

            return $res;
        }

        public function detach()
        {
            if (function_exists('curl_init')) {
                if (method_exists('Tools', 'refreshCACertFile')) {
                    // Does not exist in some PS1.6
                    Tools::refreshCACertFile();
                }
                $curl = curl_init();

                $post_data = [
                    'action' => 'detach',
                    'ajax' => 1,
                    'ps_token' => $this->psToken,
                ];

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_URL,
                    self::JPRESTA_PROTO . self::JPRESTA_DOMAIN . self::JPRESTA_DOMAIN_EXT . self::JPRESTA_PATH_API_LICENSES);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    'x-jpresta-account-key: ' . $this->jak,
                    'User-Agent: ' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Page Cache Ultimate'),
                ]);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($curl, CURLOPT_MAXREDIRS, 0);

                $content = curl_exec($curl);

                if (false === $content) {
                    $res = sprintf('error code %d - %s',
                        curl_errno($curl),
                        curl_error($curl)
                    );
                    JprestaUtils::addLog('PageCache | Detach JAK - ' . $res, 2);
                } else {
                    $jsonContent = json_decode($content, true);
                    if (!is_array($jsonContent) || !array_key_exists('status', $jsonContent)) {
                        $res = 'JPresta server returned response in incorrect format';
                        JprestaUtils::addLog('PageCache | Detach JAK - ' . $res, 2);
                    } else {
                        if ($jsonContent['status'] === 'ok') {
                            $res = true;
                        } elseif ($jsonContent['status'] === 'jak_invalid') {
                            if (array_key_exists('message', $jsonContent)) {
                                JprestaUtils::addLog('PageCache | Ignored error: cannot detach JAK ' . $this->jak . ' - ' . $jsonContent['message'],
                                    2);
                            } else {
                                JprestaUtils::addLog('PageCache | Ignored error: cannot detach JAK ' . $this->jak, 2);
                            }
                            $res = true;
                        } else {
                            if (array_key_exists('message', $jsonContent)) {
                                $res = $jsonContent['message'];
                            } else {
                                $res = 'The account has not been detached for an unknown reason';
                            }
                            JprestaUtils::addLog('PageCache | Detach JAK - ' . $res, 2);
                        }
                    }
                }

                curl_close($curl);
            } else {
                $res = 'CURL must be available';
            }

            return $res;
        }

        public static function getLicensesURL()
        {
            return self::JPRESTA_PROTO . self::JPRESTA_DOMAIN_ADMIN . self::JPRESTA_DOMAIN_EXT . self::JPRESTA_PATH_URL_LICENSES;
        }

        public static function getCacheWarmerDashboardURL()
        {
            return self::JPRESTA_PROTO . self::JPRESTA_DOMAIN_CACHE_WARMER . self::JPRESTA_DOMAIN_EXT . self::JPRESTA_PATH_URL_CACHE_WARMER;
        }

        public static function getAutoconfURL()
        {
            return self::JPRESTA_PROTO . self::JPRESTA_DOMAIN_AUTOCONF . self::JPRESTA_DOMAIN_EXT . self::JPRESTA_PATH_URL_AUTOCONF;
        }

        /**
         * @return bool true if this Prestashop instance seems to be a clone of an other Prestashop
         */
        public static function getPrestashopIsClone()
        {
            if (self::getJPrestaAccountKey()) {
                $currentPrestashopChecksum = self::getPrestashopChecksum();
                $storedPrestashopChecksum = Configuration::get('jpresta_ps_checksum', null, 0, 0, false);

                return $currentPrestashopChecksum != $storedPrestashopChecksum;
            }

            return false;
        }

        /**
         * @param $isClone bool If true then a new Prestashop token is generated, if false it updates the current checksum of Prestashop
         */
        public static function setPrestashopIsClone($isClone)
        {
            if ($isClone) {
                Configuration::deleteByName('jpresta_ps_token');
                Configuration::deleteByName('jpresta_account_key');
                self::getPrestashopToken(true);
            }
            self::setPrestashopChecksum();
        }

        /**
         * Make this Prestashop instance an original one (store the current checksum as the new one)
         */
        private static function setPrestashopChecksum()
        {
            Configuration::updateValue('jpresta_ps_checksum', self::getPrestashopChecksum(), false, 0, 0);
        }

        /**
         * @return string A checksum to identify the current Prestashop instance
         */
        private static function getPrestashopChecksum()
        {
            $checksum = '';
            if (JprestaUtils::version_compare(_PS_VERSION_, '1.7', '>')) {
                // PS1.7
                $configFile = dirname(__FILE__) . '/../../../app/config/parameters.php';
                if (file_exists($configFile)) {
                    $config = require $configFile;
                    $checksum .= $config['parameters']['database_host'] . '|';
                    $checksum .= $config['parameters']['database_name'] . '|';
                } else {
                    // Is it possible?
                    $checksum .= _DB_SERVER_ . '|';
                    $checksum .= _DB_NAME_ . '|';
                }
                $checksum .= JprestaUtils::dbGetValue('SELECT GROUP_CONCAT(domain, physical_uri) FROM `' . _DB_PREFIX_ . 'shop_url`;', false, false);
            } else {
                // PS1.5 PS1.6
                $checksum .= _DB_SERVER_ . '|';
                $checksum .= _DB_NAME_ . '|';
            }

            return md5($checksum);
        }

        /**
         * @param bool $reset If true a new Prestashop token is generated
         *
         * @return string A string that identify this Prestashop instance
         */
        public static function getPrestashopToken($reset = false)
        {
            if ($reset) {
                Configuration::deleteByName('jpresta_ps_token');
            }
            $token = Configuration::get('jpresta_ps_token', null, 0, 0, false);
            if (!$token) {
                // Generate a new token
                $token = 'PS-' . Tools::strtoupper(self::generateRandomString(12));
                Configuration::updateValue('jpresta_ps_token', $token, false, 0, 0);
                self::setPrestashopChecksum();
            }

            return $token;
        }

        public static function getPrestashopType()
        {
            return Configuration::get('jpresta_ps_type', null, 0, 0, null);
        }

        public static function setPrestashopType($type)
        {
            Configuration::updateValue('jpresta_ps_type', $type === 'test' ? 'test' : 'prod', false, 0, 0);
        }

        public static function getJPrestaAccountKey()
        {
            return Configuration::get('jpresta_account_key', null, 0, 0, null);
        }

        public static function setJPrestaAccountKey($key)
        {
            Configuration::updateValue('jpresta_account_key', $key, false, 0, 0);
        }

        private static function generateRandomString($length = 16)
        {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
            $final_rand = '';
            for ($i = 0; $i < $length; ++$i) {
                $final_rand .= $chars[rand(0, JprestaUtils::strlen($chars) - 1)];
            }

            return $final_rand;
        }

        public static function getLatestVersion($moduleName)
        {
            $latest = null;
            if (!_PS_MODE_DEMO_ && Validate::isModuleName($moduleName)) {
                $cacheFile = _PS_CACHE_DIR_ . "/jpresta-latest-$moduleName.cache.tmp";
                $cacheTime = @filemtime($cacheFile);
                if (!$cacheTime || (time() - $cacheTime) > (24 * 60 * 60)) {
                    $datas = self::getLatestVersionDatas($moduleName);
                    if (is_array($datas)) {
                        $latest = $datas;
                    }
                    // PathTraversal : Here $cacheFile is secured
                    file_put_contents($cacheFile, json_encode($latest));
                } else {
                    // PathTraversal : Here $cacheFile is secured
                    $latest = json_decode(Tools::file_get_contents($cacheFile, false, null, 30, true), true);
                }
            }

            return $latest;
        }

        private static function getLatestVersionDatas($moduleName)
        {
            if (!Validate::isModuleName($moduleName)) {
                return 'Invalid module name';
            }

            $get_data = [
                'action' => 'get_latest',
                'ajax' => 1,
                'module_name' => $moduleName,
            ];

            $url = self::JPRESTA_PROTO . self::JPRESTA_DOMAIN . self::JPRESTA_DOMAIN_EXT . self::JPRESTA_PATH_API_LICENSES . '?' . http_build_query($get_data);

            try {
                $timeout = 30;
                $headers = [];
                $headers[] = 'Referer: pagecacheultimate';
                $headers[] = 'User-Agent: ' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Page Cache Ultimate');
                $stream_context = @stream_context_create(
                    [
                        'http' => [
                            'timeout' => $timeout,
                            'header' => implode("\r\n", $headers),
                        ],
                        /* Remove this check because it often fails :'(
                         * 'ssl' => [
                            'verify_peer' => true,
                            'cafile' => CaBundle::getBundledCaBundlePath(),
                        ],*/
                    ]
                );
                // PathTraversal : Here $url is secured
                $content = Tools::file_get_contents($url, false, $stream_context, $timeout, true);
                if ($content) {
                    $jsonContent = json_decode($content, true);
                    if (!is_array($jsonContent) || !array_key_exists('status', $jsonContent)) {
                        $res = 'JPresta server returned response in incorrect format';
                    } else {
                        if ($jsonContent['status'] === 'ok') {
                            $res = $jsonContent['latest'];
                        } else {
                            if (array_key_exists('message', $jsonContent)) {
                                $res = $jsonContent['message'];
                            } else {
                                $res = 'Cannot retreive latest versions for an unknown reason (status not OK but no message)';
                            }
                        }
                    }
                } else {
                    $res = 'Cannot retreive latest versions for an unknown reason (empty without error)';
                }
            } catch (Exception $e) {
                $res = $e->getMessage();
            }

            return $res;
        }
    }
}
