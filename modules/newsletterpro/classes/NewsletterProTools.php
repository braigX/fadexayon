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


use GuzzleHttp\Utils;

class NewsletterProTools
{
    public static function getActiveShops()
    {
        $context = Context::getContext();

        $shop_list = [];

        if (!method_exists('Shop', 'getContextListShopID')) {
            $shops_id = ['1' => (int) $context->shop->id];
        } else {
            $shops_id = Shop::getContextListShopID();
        }

        foreach ($shops_id as $key => $shop_id) {
            $shop_list[$key] = Shop::getShop((int) $shop_id);
            if (!isset($shop_list[$key]['id_shop_group'])) {
                $shop_list[$key]['id_shop_group'] = 1;
            }
        }

        return $shop_list;
    }

    public static function getActiveShopsId()
    {
        $context = Context::getContext();

        if (!method_exists('Shop', 'getContextListShopID')) {
            return ['1' => (int) $context->shop->id];
        } else {
            return Shop::getContextListShopID();
        }
    }

    public static function is17()
    {
        return version_compare(_PS_VERSION_, '1.7.0.0', '>=');
    }

    public static function isEmpty($str)
    {
        $str_trim = trim($str);

        return empty($str_trim);
    }

    public static function addTableAssociationArray($array)
    {
        foreach ($array as $table_name => $value) {
            if (method_exists('Shop', 'addTableAssociation')) {
                if (!NewsletterPro::isTableAssociated($table_name)) {
                    Shop::addTableAssociation($table_name, $value);
                }
            } elseif (method_exists('Shop', 'addTableAssociationNewsletterPro')) {
                if (!NewsletterPro::isTableAssociated($table_name)) {
                    // mothod verified
                    Shop::addTableAssociationNewsletterPro($table_name, $value);
                }
            } else {
                $module = NewsletterPro::getInstance();
                exit(Tools::displayError(sprintf($module->l('The functions "%s" or "%s" does not exists. Please override the Shop.php file.'), 'Shop::addTableAssociation', 'Shop::addTableAssociationNewsletterPro')));
            }
        }
    }

    public static function isFileName($name)
    {
        return preg_match('/^[a-zA-Z0-9%àâçéèêëîïôûùüÿñæœčšđžćČŠĐĆŽİıÖöÜüÇçĞğŞş₤\s_-]*$/', $name);
    }

    public static function getFileNameIncrement($filename, $new_filename = null, $increment = 0)
    {
        if ($increment > 0 && isset($new_filename)) {
            $filename = $new_filename;
        }

        if (!file_exists($filename)) {
            return $filename;
        }

        ++$increment;

        $info = pathinfo($filename);
        $fn = preg_replace('/_\d+$/', '', $info['filename']);

        if (is_dir($filename)) {
            $nfn = $info['dirname'].'/'.$fn.'_'.$increment;

            return self::getFileNameIncrement($filename, $nfn, $increment);
        } else {
            if (isset($info['extension'])) {
                $nfn = $info['dirname'].'/'.$fn.'_'.$increment.'.'.$info['extension'];

                return self::getFileNameIncrement($filename, $nfn, $increment);
            } else {
                $nfn = $info['dirname'].'/'.$fn.'_'.$increment;

                return self::getFileNameIncrement($filename, $nfn, $increment);
            }
        }
    }

    public static function unSerialize($serialized)
    {
        if (is_string($serialized) && preg_match('/a:[0-9]+:\{.*\}/', $serialized)) {
            $return = @unserialize($serialized);
            if (false === $return) {
                // in case the serialization is corrupted
                $serialized = preg_replace_callback('/s:(\d+):"(.*?)";/', function ($m) {
                    return 's:'.Tools::strlen($m[2]).':"'.$m[2].'";';
                }, $serialized);

                $return = @unserialize($serialized);
                if (false === $return) {
                    return [];
                }
            }

            return $return;
        }

        return [];
    }

    public static function isBase64($s)
    {
        return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s);
    }

    public static function dbSerialize($value)
    {
        return addcslashes(serialize($value), "\x00..\x2C./:;<=>?@[\\]^`{|}~");
    }

    public static function addCShashes($str)
    {
        return addcslashes($str, "\x00..\x2C./:;<=>?@[\\]^`{|}~");
    }

    public static function strSize($string)
    {
        if (function_exists('mb_strlen')) {
            $size = mb_strlen($string, '8bit');
        } else {
            $size = Tools::strlen($string);
        }

        return $size;
    }

    /**
     * Get xml errors as string.
     *
     * @param object $error An xml error object
     * @param object $xml   An intance of the xml object
     *
     * @return string
     */
    public static function displayXMLError($error, $xml)
    {
        $return = $xml[$error->line - 1];

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }

        $return .= trim($error->message)." Line: $error->line Column: $error->column";

        if ($error->file) {
            $return .= " File: $error->file";
        }

        return $return;
    }

    public static function normalizePath($path)
    {
        $parts = [];
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('/\/+/', '/', $path);
        $segments = explode('/', $path);
        $test = '';

        foreach ($segments as $segment) {
            if ('.' != $segment) {
                $test = array_pop($parts);
                if (is_null($test)) {
                    $parts[] = $segment;
                } elseif ('..' == $segment) {
                    if ('..' == $test) {
                        $parts[] = $test;
                    }

                    if ('..' == $test || '' == $test) {
                        $parts[] = $segment;
                    }
                } else {
                    $parts[] = $test;
                    $parts[] = $segment;
                }
            }
        }

        return implode('/', $parts);
    }

    public static function strToHex($str)
    {
        return unpack('H*', $str);
    }

    public static function hexToStr($str)
    {
        if (preg_match('/^0x/i', $str)) {
            $str = preg_replace('/^0x/i', '', $str);
        }

        return pack('H*', $str);
    }

    public static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (('.' != $file) && ('..' != $file)) {
                if (is_dir($src.'/'.$file)) {
                    self::recurseCopy($src.'/'.$file, $dst.'/'.$file);
                } else {
                    copy($src.'/'.$file, $dst.'/'.$file);
                }
            }
        }
        closedir($dir);
    }

    public static function getIdShopGroup($context = null)
    {
        if (!isset($context)) {
            $context = Context::getContext();
        }

        if (class_exists('ShopGroup')) {
            return $context->shop->id_shop_group;
        } else {
            return $context->shop->id_group_shop;
        }
    }

    public static function getInShopGroupColumnName()
    {
        if (class_exists('ShopGroup')) {
            return 'id_shop_group';
        }

        return 'id_group_shop';
    }

    public static function createFolder($path = false, $path_thumbs = false)
    {
        $oldumask = umask(0);
        if ($path && !file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if ($path_thumbs && !file_exists($path_thumbs)) {
            mkdir($path_thumbs, 0777, true);
        }
        umask($oldumask);
    }

    public static function blockNewsletterExists()
    {
        $sql = "SELECT COUNT(*) AS `count`
		FROM INFORMATION_SCHEMA.TABLES
		WHERE  TABLE_SCHEMA = '"._DB_NAME_."' 
		AND TABLE_NAME = '"._DB_PREFIX_."newsletter';";

        return Db::getInstance()->getValue($sql);
    }

    public static function closeConnection($content = null)
    {
        @ob_implicit_flush(true);
        @ob_end_clean();
        @ob_start();
        echo $content;
        $size = ob_get_length();
        header("Content-Length: $size");
        header('Connection: close');
        ob_end_flush();
        ob_flush();
        flush();

        if (session_id()) {
            session_write_close();
        }
    }

    public static function getTableColumns($table_name)
    {
        $columns_db = Db::getInstance()->executeS('
			SELECT `column_name` 
			FROM `information_schema`.`columns` 
			WHERE `table_schema`="'._DB_NAME_.'" 
			AND `table_name`="'._DB_PREFIX_.pSQL($table_name).'";
		');

        $columns = [];
        foreach ($columns_db as $column) {
            $columns[] = $column['column_name'];
        }

        return $columns;
    }

    public static function tableExists($table_name)
    {
        $table_name = _DB_PREFIX_.$table_name;

        return count(Db::getInstance()->executeS('SHOW TABLES LIKE "'.pSQL($table_name).'"'));
    }

    public static function columnExists($table, $name)
    {
        return Db::getInstance()->getValue(
            "
			SELECT COUNT(*)
			FROM INFORMATION_SCHEMA.COLUMNS 
			WHERE 
				TABLE_SCHEMA = '"._DB_NAME_."' 
			AND TABLE_NAME = '"._DB_PREFIX_.pSQL($table)."' 
			AND COLUMN_NAME = '".pSQL($name)."'"
        );
    }

    public static function getDbColumns($table_name)
    {
        $columns_array = [];
        $columns = Db::getInstance()->executeS(
            "
			SELECT COLUMN_NAME
			FROM INFORMATION_SCHEMA.COLUMNS 
			WHERE 
				TABLE_SCHEMA = '"._DB_NAME_."' 
			AND TABLE_NAME = '"._DB_PREFIX_.pSQL($table_name)."'"
        );

        foreach ($columns as $row) {
            $columns_array[] = $row['COLUMN_NAME'];
        }

        return $columns_array;
    }

    /**
     * List files from folder.
     *
     * @param string $path
     * @param string $regex
     *
     * @return objects
     */
    public static function getDirectoryIterator($path, $regex)
    {
        $directory = new DirectoryIterator($path);
        $result = new RegexIterator($directory, $regex, RecursiveRegexIterator::MATCH);

        return $result;
    }

    public static function deleteDirAndFiles($path)
    {
        $succeed = [];

        $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                $succeed[] = rmdir($file->getRealPath());
            } else {
                $succeed[] = unlink($file->getRealPath());
            }
        }

        $succeed[] = rmdir($path);

        return !in_array(false, $succeed);
    }

    public static function languageExists($id_lang, $check_active = false)
    {
        return (int) Db::getInstance()->getValue('
			SELECT `id_lang`
			FROM `'._DB_PREFIX_.'lang`
			WHERE `id_lang` = "'.(int) $id_lang.'"
			'.($check_active ? ' AND `active` = 1 ' : '').'
		');
    }

    public static function getTemplatePath($path)
    {
        $module = NewsletterPro::getInstance();

        $template_name = pathinfo($path, PATHINFO_BASENAME);
        $rel_path = preg_replace('/^.*'.$module->name.'(\/|\\\)/', '', $path);

        $template = _PS_THEME_DIR_.'modules/'.$module->name.'/'.$rel_path;

        if (file_exists($template)) {
            return $template;
        } else {
            return $path;
        }
    }

    public static function hasTemplatePath($path)
    {
        $module = NewsletterPro::getInstance();
        $template_name = pathinfo($path, PATHINFO_BASENAME);
        $rel_path = preg_replace('/^.*'.$module->name.'(\/|\\\)/', '', $path);
        $template = _PS_THEME_DIR_.'modules/'.$module->name.'/'.$rel_path;

        return file_exists($template);
    }

    public static function loadTemplatePath($path)
    {
        $module = NewsletterPro::getInstance();
        $template_name = pathinfo($path, PATHINFO_BASENAME);
        $rel_path = preg_replace('/^.*'.$module->name.'(\/|\\\)/', '', $path);
        $template = _PS_THEME_DIR_.'modules/'.$module->name.'/'.$rel_path;

        return $template;
    }

    /**
     * @return NewsletterPro
     */
    public static function module()
    {
        return NewsletterPro::getInstance();
    }

    public static function getMin($filename)
    {
        $info = pathinfo($filename);

        return (bool) pqnp_config('LOAD_MINIFIED') ? $info['filename'].'.min.'.$info['extension'] : $info['filename'].'.'.$info['extension'];
    }

    public static function getCSS($basename)
    {
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
            return NewsletterProTools::module()->uri_location.'views/css/1.5/'.$basename;
        } elseif (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            return NewsletterProTools::module()->uri_location.'views/css/1.6/'.$basename;
        } else {
            return NewsletterProTools::module()->uri_location.'views/css/1.7/'.$basename;
        }
    }

    public static function getVersion()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.0', '<')) {
            return '1.5';
        } elseif (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            return '1.6';
        } else {
            return '1.7';
        }
    }

    public static function getJsDefContent($data = null)
    {
        if (!isset($data)) {
            return '';
        }

        $output = [];

        foreach ($data as $variable => $variable_value) {
            if (is_array($variable_value)) {
                $output[] = 'var '.$variable.' = '.NewsletterProTools::jsonEncode($variable_value).';';
            } elseif (is_string($variable_value)) {
                $output[] = 'var '.$variable.' = \''.addcslashes($variable_value, '\'').'\';';
            } elseif (is_numeric($variable_value)) {
                $output[] = 'var '.$variable.' = '.(int) $variable_value.';';
            } elseif (is_bool($variable_value)) {
                $output[] = 'var '.$variable.' = '.(bool) $variable_value.';';
            }
        }

        return "\t".implode("\n\t", $output);
    }

    public static function getJsDefScript($data = null, $extend = '')
    {
        return "\t<script type=\"text/javascript\">\n".NewsletterProTools::getJsDefContent($data)."\n\t".trim($extend)."\n\t</script>";
    }

    public static function getDbVersion()
    {
        return Db::getInstance()->getValue('SELECT `version` FROM `'._DB_PREFIX_.'module` WHERE `name` = "'.self::module()->name.'"');
    }

    public static function isValidHook($hook_name)
    {
        $count = (int) Db::getInstance()->getValue('
            SELECT COUNT(*) FROM `'._DB_PREFIX_.'hook`
            WHERE `name` = "'.pSQL($hook_name).'"
        ');

        if ($count > 0) {
            return true;
        }

        $count = (int) Db::getInstance()->getValue('
            SELECT COUNT(*) FROM `'._DB_PREFIX_.'hook_alias`
            WHERE `name` = "'.pSQL($hook_name).'"
            OR `alias` = "'.pSQL($hook_name).'"
        ');

        if ($count > 0) {
            return true;
        }

        return false;
    }

    public static function getUri($params = [])
    {
        return self::module()->link.(count($params) > 0 ? '&'.http_build_query($params) : '');
    }

    public static function jsonEncode($data, $options = 0, $depth = 512)
    {
        if (version_compare(_PS_VERSION_, '8', '>=')) {
            return Utils::jsonEncode($data, $options, $depth);
        } else {
            return Tools::jsonEncode($data, $options, $depth);
        }
    }

    public static function jsonDecode($data, $assoc = false, $depth = 512, $options = 0)
    {
        if (version_compare(_PS_VERSION_, '8', '>=')) {
            return Utils::jsonDecode($data, $assoc, $depth, $options);
        } else {
            return Tools::jsonDecode($data, $assoc, $depth, $options);
        }
    }
}
