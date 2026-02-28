<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */

namespace Teamwant\Prestashop17\Redis\Classes;

if (!defined('_PS_VERSION_')) {
    exit;
}

// todo: poogarniac consty w wolnej chwili

require_once _PS_MODULE_DIR_ . 'teamwant_redis/const.php';

class FileManager
{
    public function parseConfigFile($file)
    {
        $template_file = TEAMWANT_REDIS_ROOT_DIR . '/config/' . $file;

        if (file_exists($template_file)) {
            $json = null;
            $json_1_3_0 = null;
            include $template_file;

            // upgrade to 1.3.0
            if (isset($json) && $json) {
                return $this->upgradeTo_1_3_0_Version($json);
            }

            if (isset($json_1_3_0) && $json_1_3_0) {
                return $json_1_3_0;
            }
        }

        return '';
    }

    /**
     * Podczas aktualizacji do 1.3.0 doszly nam nowe pola, musimy wyrównać je aby aktualizacja nie rozwalila strony
     *
     * @param $json
     *
     * @return mixed
     */
    private function upgradeTo_1_3_0_Version($json)
    {
        $json = json_decode($json, true);

        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        if (!empty($json) && is_array($json)) {
            $json_1_3_0 = [];

            // Dodajemy możliwe braki pomiędzy wersjami modulu
            $json_1_3_0['_servers'] = $json;
            $json_1_3_0['_config'] = []; // nowe pole

            if (empty($json_1_3_0['_config']['use_cache_admin'])) {
                $json_1_3_0['_config']['use_cache_admin'] = 0;
            }

            if (empty($json_1_3_0['_config']['use_prefix'])) {
                $json_1_3_0['_config']['use_prefix'] = 0;
            }

            if (empty($json_1_3_0['_config']['use_multistore'])) {
                $json_1_3_0['_config']['use_multistore'] = 0;
            }

            if (empty($json_1_3_0['_config']['prefix'])) {
                $json_1_3_0['_config']['prefix'] = null;
            }

            if (empty($json_1_3_0['_config']['defalut_ttl'])) {
                $json_1_3_0['_config']['defalut_ttl'] = null;
            }

            // 1.6.2 - dodanie wyboru systemu redis
            $json_1_3_0['_config']['redis_engine'] = null;
        } else {
            $json_1_3_0 = [
                '_servers' => [],
                '_config' => [],
            ];
        }

        $this->saveConfigFile('_RedisConfiguration.php', json_encode($json_1_3_0));

        return $json_1_3_0;
    }

    public function saveConfigFile($file, $data)
    {
        $template_file = TEAMWANT_REDIS_ROOT_DIR . '/config/' . $file;

        $json = "<?php \n \$json_1_3_0 = '{$data}';";
        $output = file_put_contents($template_file, $json);

        if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && !$output) {
            $trans = \Teamwant_Redis::staticModuleTranslate("Can't write file %s, please check \"rw\" permissions on this file", [
                $template_file,
            ]);
            @trigger_error($trans, E_USER_NOTICE);
        }

        return true;
    }
}
