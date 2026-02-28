<?php
/***
 *    2007-2025 PrestaShop
 *
 *    NOTICE OF LICENSE
 *
 *    This source file is subject to the Academic Free License (AFL 3.0)
 *    that is bundled with this package in the file LICENSE.txt.
 *    It is also available through the world-wide-web at this URL:
 *    http://opensource.org/licenses/afl-3.0.php
 *    If you did not receive a copy of the license and are unable to
 *    obtain it through the world-wide-web, please send an email
 *    to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *    @author    PrestaShop SA <contact@prestashop.com>
 *    @copyright 2007-2015 PrestaShop SA
 *    @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_'))
exit;
class ultimateimagetool extends Module
{
    public $languages;
    public $html;
    public $_html;
    public $author_address;
    public $module_p;
    
    public function __construct()
    {
        $this->name = 'ultimateimagetool';
        $this->tab = 'administration';
        $this->version = '2.3.2';
        $this->author = 'advancedplugins';
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->module_key = 'c5e7e38aa69cc969804a213ca75bde94';
        $this->author_address = '0x6b0EA5e7A2019Ca82d288436edf45e1B9a3540b5';
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        parent::__construct();
        $this->displayName = $this->l('Image: WebP, Compress, Zoom, Regenerate & More');
        $this->description = $this->l('Compress, WebP, Zoom, Regenerate, Delete, Lazy Load, SEO Alt Tags, Swap on  hover and much more.');
        $this->_buildData();
    }
    public function installDb()
    {
        Configuration::updateValue('uit_token', md5(Configuration::get('PS_SHOP_EMAIL')));
        Configuration::updateValue('uit_products', 1);
        Configuration::updateValue('uit_compress_os', 0);
        Configuration::updateValue('uit_cron_quality', 80);
        Configuration::updateValue('uit_quality', 80);
        Configuration::updateValue('uit_quality_manuf', 80);
        Configuration::updateValue('uit_cron_quality_manuf', 80);
        Configuration::updateValue('uit_quality_sup', 80);
        Configuration::updateValue('uit_cron_quality_sup', 80);
        Configuration::updateValue('uit_quality_cat', 80);
        Configuration::updateValue('uit_quality_cms', 80);
        Configuration::updateValue('uit_cron_quality_cat', 80);
        Configuration::updateValue('uit_image_quality_webp_cron', 80);
        Configuration::updateValue('uit_simple_load', 1);
        Configuration::updateValue('uit_exceptions', '.zoomimgthumb, .zoomimg, .swiper-lazy, .lazy');
        Configuration::updateValue('uit_lazy_load', 'disabled');
        Configuration::updateValue('uit_lazy_load_image', 'blank.png');
        Configuration::updateValue('uit_mouse_hover', 'disabled');
        Configuration::updateValue('uit_mouse_hover_thumb', 'disabled');
        Configuration::updateValue('uit_hover_image_type', 'home_' . 'default');
        Configuration::updateValue('uit_mouse_hover_position', 'last_image');
        Configuration::updateValue('uit_hover_image_ps', 'home_' . 'default');
        Configuration::updateValue('uit_hover_image_ts', 'cart_' . 'default');
        Configuration::updateValue('uit_alt_format', '{PRODUCT_NAME} {MANUFACTURER_NAME} - {IMAGE_POSITION}');
        Configuration::updateValue('alt_tags_auto', 'no');
        Configuration::updateValue('uit_use_webp', '2');
        Configuration::updateValue('uit_use_webp_termination', 0);
        Configuration::updateValue('uit_lazy_op', 0);
        $imagewebp = function_exists('imagewebp');
        $imagick = extension_loaded('imagick');
        $is_imagick = false;
        if ($imagick)
        {
            if (class_exists('Imagick'))
            {
                $formats = Imagick::queryFormats();
                foreach ($formats as $format)
                {
                    if (Tools::strtoupper($format) == 'WEBP')
                    $is_imagick = true;
                }
            }
        }
        if ($imagewebp && !$is_imagick)
        Configuration::updateValue('uit_use_external_api', 1);
        else
        Configuration::updateValue('uit_use_external_api', 0);
        Configuration::updateValue('uit_auto_webp', '1');
        //Configuration::updateValue('uit_use_external_api', 0);
        Configuration::updateValue('uit_use_picture_webp', '0');
        Configuration::updateValue('uit_zoom', '0');
        Configuration::updateValue('uit_zoom_type', '0');
        Configuration::updateValue('uit_enable_gzip', '0');
        Configuration::updateValue('ait_force_regenerate', '0');
        $sql = 'SELECT name FROM `' . _DB_PREFIX_ . 'image_type` WHERE products = 1 ORDER BY `width` DESC';
        $result = Db::getInstance()->getValue($sql);
        if ($result)
        {
            Configuration::updateValue('uit_zoom_full_image_size', $result);
            Configuration::updateValue('uit_zoom_normal_image_size', $result);
            Configuration::updateValue('uit_zoom_thumb_image_size', $result);
        }
        else
        {
            Configuration::updateValue('uit_zoom_full_image_size', '');
            Configuration::updateValue('uit_zoom_normal_image_size', '');
            Configuration::updateValue('uit_zoom_thumb_image_size', '');
        }
        return true;
    }
    public function hookactionHtaccessCreate()
    {
        require_once $this->getLocalPath() . 'src/htaccess.php';
        $htaccess_builder = new Htaccess();
        $htaccess_builder->generate_htaccess_content();
        $htaccess_builder->add_to_htaccess();
        if (file_exists(_PS_IMG_DIR_ . '/.htaccess'))
        {
            $str = Tools::file_get_contents(_PS_IMG_DIR_ . '/.htaccess');
            file_put_contents(_PS_IMG_DIR_ . '/.htaccess', str_replace("|ico)", "|ico|webp)", $str));
        }
        return true;
    }
    public function uninstall()
    {
        Configuration::deleteByName('uit_token');
        $tab_id = Tab::getIdFromClassName($this->name);
        if ($tab_id)
        {
            $tab = new Tab($tab_id);
            $tab->delete();
        }
        $tab_id = Tab::getIdFromClassName($this->name . 'ajax');
        if ($tab_id)
        {
            $tab = new Tab($tab_id);
            $tab->delete();
        }
        parent::uninstall();
        Tools::generateHtaccess();
        return true;
    }
    public function hookActionCategoryUpdate($params)
    {
        if ((int)Configuration::get('uit_use_external_api') != 1 || (int)Configuration::get('uit_auto_webp') == 0)
        return true;
        require_once (_PS_MODULE_DIR_ . '/ultimateimagetool/vendor/autoload.php');
        $imagewebp = function_exists('imagewebp');
        $imagick = extension_loaded('imagick');
        $is_imagick = false;
        $true = false;
        if ($imagick)
        {
            if (class_exists('Imagick'))
            {
                $formats = Imagick::queryFormats();
                foreach ($formats as $format)
                {
                    if (Tools::strtoupper($format) == 'WEBP')
                    $is_imagick = true;
                }
            }
        }
        if ((int)Configuration::get('uit_use_external_api') == 1)
        {
            $is_imagick = false;
            $imagewebp = false;
        }
        $id_category = false;
        $id_image = false;
        $lossless = true;
        $imagewebp = function_exists('imagewebp');
        if ($imagewebp)
        $lossless = false;
        $image_quality = 85;
        if (isset($params['category']))
        {
            if (isset($params['category']->id_category))
            $id_category = $params['category']->id_category;
            if (isset($params['category']->id_category))
            $id_image = (int)$params['category']->id_image;
        }
        if ($id_image)
        {
            $image_types = ImageType::getImagesTypes('categories');
            if ($image_types)
            {
                $langs = Language::getLanguages(true);
                $link = new Link();
                $image_types[] = array(
                    'name' => ''
                );
                foreach ($langs as $l)
                {
                    $category = new Category($id_category, $l['id_lang']);
                    foreach ($image_types as $type)
                    {
                        //echo '<pre>'; print_r($type); echo '</pre>';
                        if (!empty($category->id_image))
                        {
                            $image_type = $type['name'];
                            $imageLink = self::get_domain_prefix(). $link->getCatImageLink($category->link_rewrite, $category->id_category, $image_type);
                            $image_ext = explode('.', $imageLink);
                            $image_ext = str_replace('webp', 'jpg', end($image_ext));
                            $image_destination = '';
                            if (Tools::strlen($image_type) > 0)
                            {
                                $image_path = _PS_CAT_IMG_DIR_ . $category->id_category . '-' . $image_type . '.' . $image_ext;
                                $image_destination = _PS_CAT_IMG_DIR_ . $category->id_category . '-' . $image_type . '.webp';
                            }
                            else
                            {
                                $image_path = _PS_CAT_IMG_DIR_ . $category->id_category . '.' . $image_ext;
                                $image_destination = _PS_CAT_IMG_DIR_ . $category->id_category . '.webp';
                            }
                            if ($image_path)
                            {
                                if (file_exists($image_destination) && substr_count($image_destination, '.webp') > 0)
                                unlink($image_destination);
                                if ($is_imagick || $imagewebp)
                                $true = \WebPConvert\WebPConvert::convert($image_path, $image_destination, array(
                                    'converters' => ['gd',
                                    'imagick',
                                    'imagemagick'],
                                    'lossless' => false,
                                    'max-quality' => $image_quality,
                                    'quality' => $image_quality,
                                    'default-quality' => $image_quality,
                                    'fail' => 'original',
                                    'serve-image' => ['headers' => ['cache-control' => true,
                                    'vary-accept' => true],
                                    'cache-control-header' => 'max-age=2'],
                                    'convert' => ['quality' => $image_quality]
                                ));
                                if (!file_exists($image_destination))
                                {
                                    $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)) , 'image_file_b64');
                                    if ($image)
                                    file_put_contents($image_destination, $image);
                                }
                            }
                            if (Configuration::get('PS_HIGHT_DPI'))
                            {
                                if (Tools::strlen($image_type) > 0)
                                {
                                    $image_path = _PS_CAT_IMG_DIR_ . $category->id_category . '-' . $image_type . '2x.' . $image_ext;
                                    $image_destination = _PS_CAT_IMG_DIR_ . $category->id_category . '-' . $image_type . '2x.webp';
                                }
                                else
                                {
                                    $image_path = _PS_CAT_IMG_DIR_ . $category->id_category . '2x.' . $image_ext;
                                    $image_destination = _PS_CAT_IMG_DIR_ . $category->id_category . '2x.webp';
                                }
                                if ($image_path)
                                {
                                    if (file_exists($image_destination) && substr_count($image_destination, '.webp') > 0)
                                    unlink($image_destination);
                                    if ($is_imagick || $imagewebp)
                                    $true = \WebPConvert\WebPConvert::convert($image_path, $image_destination, array(
                                        'converters' => ['gd',
                                        'imagick',
                                        'imagemagick'],
                                        'lossless' => false,
                                        'max-quality' => $image_quality,
                                        'quality' => $image_quality,
                                        'default-quality' => $image_quality,
                                        'fail' => 'original',
                                        'serve-image' => ['headers' => ['cache-control' => true,
                                        'vary-accept' => true],
                                        'cache-control-header' => 'max-age=2'],
                                        'convert' => ['quality' => $image_quality]
                                    ));
                                    if (!file_exists($image_destination))
                                    {
                                        $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)) , 'image_file_b64');
                                        if ($image)
                                        file_put_contents($image_destination, $image);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    public function install()
    {
        if (file_exists(_PS_MODULE_DIR_ . '/' . $this->name . '/controllers/front/ajax_public.php'))
        {
            unlink(_PS_MODULE_DIR_ . '/' . $this->name . '/controllers/front/ajax_public.php');
        }
        if (
        !parent::install() or
        !$this->registerHook('updateproduct') or
        !$this->registerHook('addproduct') or
        !$this->registerHook('displayHeader') or
        !$this->registerHook('actionCategoryUpdate') or
        !$this->registerHook('actionObjectDeleteBefore') or
        !$this->registerHook('actionOnImageResizeAfter')
		)
        return false;
        $langs = Language::getLanguages();
        $tab = new Tab();
        $tab->class_name = $this->name;
        $tab->module = $this->name;
        $tab->id_parent = - 1;
        $tab->icon = 'shopping_basket';
        foreach ($langs as $l)
        $tab->name[$l['id_lang']] = $this->l('Main');
        $tab->save();
        unset($tab);
        $tab = new Tab();
        $tab->class_name = $this->name . 'ajax';
        $tab->module = $this->name;
        $tab->id_parent = - 1;
        $tab->icon = 'alarm_on';
        foreach ($langs as $l)
        $tab->name[$l['id_lang']] = $this->l('ajax');
        $tab->save();
        unset($tab);
        $this->installDb();
        $this->registerHook('actionHtaccessCreate');
        Tools::generateHtaccess();
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'uit_smush` (
					`id` int(99) NOT NULL AUTO_INCREMENT,
					`object_id` int(11) NOT NULL,
					`object_type` varchar(255) NOT NULL,
					`image_size` varchar(255) NOT NULL,
					`original_size` int(11) NOT NULL,
					`new_size`  int(11) NOT NULL,
					`date_add` datetime NOT NULL,
					`processed` int(1) NOT NULL,
					PRIMARY KEY (`id`),
  					KEY `id` (`id`),
  					KEY `object_id` (`object_id`),
  					KEY `object_type` (`object_type`),
  					KEY `image_size` (`image_size`),
  					KEY `processed` (`processed`)
					) ENGINE= ' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ';
        Db::getInstance()->execute($sql);
        return true;
    }
    public static function is_imagick()
    {
        //ultimateimagetool::is_imagick()
        $imagick = extension_loaded('imagick');
        $is_imagick = false;
        if ($imagick)
        {
            if (class_exists('Imagick'))
            {
                $formats = Imagick::queryFormats();
                foreach ($formats as $format)
                {
                    if (Tools::strtoupper($format) == 'WEBP')
                    $is_imagick = true;
                }
            }
        }
        return $is_imagick;
    }
    public function hookActionObjectDeleteBefore($params)
    {
        if ($params['object'] instanceof Product)
        {
            $this->convert_all_product_images_to_webp($params['object']->id, true);
        }
        return true;
    }
    public function hookActionOnImageResizeAfter($params)
    {
        if ((int)Configuration::get('uit_auto_webp') == 0 || Tools::getIsset('submitCustomizedData'))
        return true;
        $pathParts = pathinfo($params['dst_file']);
        if (!isset($pathParts['extension']))
        {
            return true;
        }
        require_once (_PS_MODULE_DIR_ . '/ultimateimagetool/vendor/autoload.php');
        $imagewebp = function_exists('imagewebp');
        $imagick = extension_loaded('imagick');
        $is_imagick = false;
        $true = false;
        if ($imagick)
        {
            if (class_exists('Imagick'))
            {
                $formats = Imagick::queryFormats();
                foreach ($formats as $format)
                {
                    if (Tools::strtoupper($format) == 'WEBP')
                    $is_imagick = true;
                }
            }
        }
        try
        {
            $image_path = $params['dst_file'];
            $image_quality = (int)Configuration::get('uit_image_quality_webp_cron');
            if ($image_quality < 60)
            $image_quality = 60;
            if ((int)Configuration::get('uit_use_external_api') == 1)
            {
                $is_imagick = false;
                $imagewebp = false;
            }
            if (file_exists($image_path))
            {
                if ($is_imagick || $imagewebp)
                {
                    $true = \WebPConvert\WebPConvert::convert($image_path, str_replace('.jpg', '.webp', $image_path) , array(
                        'converters' => ['gd',
                        'imagick',
                        'imagemagick'],
                        'lossless' => false,
                        'max-quality' => $image_quality,
                        'quality' => $image_quality,
                        'default-quality' => $image_quality,
                        'fail' => 'original',
                        'serve-image' => ['headers' => ['cache-control' => true,
                        'vary-accept' => true],
                        'cache-control-header' => 'max-age=2'],
                        'convert' => ['quality' => $image_quality]
                    ));
                    $image_path2x = str_replace('.jpg', '2x.jpg', $image_path);
                    if (file_exists($image_path2x))
                    {
                        $true = \WebPConvert\WebPConvert::convert($image_path2x, str_replace('.jpg', '.webp', $image_path2x) , array(
                            'converters' => ['gd',
                            'imagick',
                            'imagemagick'],
                            'lossless' => false,
                            'max-quality' => $image_quality,
                            'quality' => $image_quality,
                            'default-quality' => $image_quality,
                            'fail' => 'original',
                            'serve-image' => ['headers' => ['cache-control' => true,
                            'vary-accept' => true],
                            'cache-control-header' => 'max-age=2'],
                            'convert' => ['quality' => $image_quality]
                        ));
                    }
                }
                else
                {
                    if (!file_exists(str_replace('.jpg', '.webp', $image_path)))
                    {
                        $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)) , 'image_file_b64', $image_quality);
                        if ($image)
                        file_put_contents(str_replace('.jpg', '.webp', $image_path) , $image);
                    }
                }
            }
        }
        catch(Exception $exception)
        {
            PrestaShopLogger::addLog(
            empty($exception->getMessage()) ? 'Unknown error' : $exception->getMessage() ,
            1,
            $exception->getCode() ,
            $this->name,
            null,
            true
);
        }
    }
    public function register_filters($params)
    {
        global $smarty;
        if (Tools::getIsset('ajax') || Tools::getIsset('id_order') || Tools::getIsset('ajaxMode') || Tools::getIsset('action') || Tools::getIsset('from-xhr') || Tools::getIsset('s') || Tools::getIsset('id_order_return') || Tools::getIsset('id_employee'))
        return true;
        if (Context::getContext()
            ->controller->controller_type != 'front')
        {
            return true;
        }
        if (Tools::getIsset('controller'))
        {
            if (Tools::getValue('controller') == 'orderconfirmation' || Tools::getValue('controller') == 'order-confirmation' || Tools::getValue('controller') == 'cart' || Tools::getValue('controller') == 'order')
            return true;
        }
        if (!empty($_POST))
        {
            if (sizeof($_POST) >= 2)
            return true;
        }
        if (Configuration::get('uit_lazy_load') == 'enabled' || Configuration::get('uit_use_picture_webp') == '1')
        {
            $smarty->registerFilter('output', array(
                Module::getInstanceByName($this->name) ,
                'parseImagesLazy'
            ));
            if (Tools::getValue('controller') == 'product' && (int)Configuration::get('uit_zoom') == 1)
            {
                $smarty->registerFilter('output', array(
                    Module::getInstanceByName($this->name) ,
                    'parseTemplateStandard'
                ));
            }
            return true;
        }
        if (Tools::getValue('controller') != 'product' || Configuration::get('uit_zoom') != '1')
        return true;
        $smarty->registerFilter('output', array(
            Module::getInstanceByName($this->name) ,
            'parseTemplateStandard'
        ));
    }
    public function parsePictureWeb($output, $smarty = NULL)
    {
        if (empty($output) || Tools::getIsset('ajax') || Tools::getIsset('id_order') || Tools::getIsset('ajaxMode') || Tools::getIsset('action') || Tools::getIsset('from-xhr'))
        return $output;
        if (!empty($_POST))
        {
            if (sizeof($_POST) >= 2)
            return $output;
        }
        if (Tools::getIsset('action'))
        {
            if (Tools::getValue('action') == 'quickview')
            return $output;
        }
    }
    public function does_url_exists($url)
    {
        $url_jpg = str_replace('.webp', '.jpg', $url);
        // Check if category image
        if (strpos($url_jpg, '/c/') !== false)
        {
            preg_match("/c\/([0-9]+)-?([a-zA-Z_-]+)?\/.+\.jpg$/", $url_jpg, $matches);
            if (!empty($matches))
            {
                if (isset($matches[2]))
                {
                    if (file_exists(_PS_ROOT_DIR_ . '/img/c/' . $matches[1] . '-' . $matches[2] . '.webp'))
                    {
                        return true;
                    }
                    else
                    {
                        if (file_exists(_PS_ROOT_DIR_ . '/img/c/' . $matches[1] . '.webp') && empty($matches[2]))
                        return true;
                        return false;
                    }
                }
            }
        }
        // Check if manufacturer image
        if (strpos($url_jpg, '/m/') !== false)
        {
            preg_match("/m\/([0-9]+)-?([a-zA-Z_-]+)?\/.+\.jpg$/", $url_jpg, $matches);
            if (!empty($matches))
            {
                if (isset($matches[2]))
                {
                    if (file_exists(_PS_ROOT_DIR_ . '/img/m/' . $matches[1] . '-' . $matches[2] . '.webp'))
                    {
                        return true;
                    }
                    else
                    {
                        if (file_exists(_PS_ROOT_DIR_ . '/img/m/' . $matches[1] . '.webp') && empty($matches[2]))
                        return true;
                        return false;
                    }
                }
            }
        }
        // Check if manufacturer image
        if (strpos($url_jpg, '/s/') !== false)
        {
            preg_match("/s\/([0-9]+)-?([a-zA-Z_-]+)?\/.+\.jpg$/", $url_jpg, $matches);
            if (!empty($matches))
            {
                if (isset($matches[2]))
                {
                    if (file_exists(_PS_ROOT_DIR_ . '/img/s/' . $matches[1] . '-' . $matches[2] . '.webp'))
                    {
                        return true;
                    }
                    else
                    {
                        if (file_exists(_PS_ROOT_DIR_ . '/img/s/' . $matches[1] . '.webp') && empty($matches[2]))
                        return true;
                        return false;
                    }
                }
            }
        }
        // Check if product image
        preg_match("/\/([0-9]+)-?([a-zA-Z_-]+)?\/.+\.jpg$/", $url_jpg, $matches);
        if (!empty($matches))
        {
            if (isset($matches[2]))
            {
                $folder = implode('/', str_split($matches[1]));
                if (file_exists(_PS_ROOT_DIR_ . '/img/p/' . $folder . '/' . $matches[1] . '-' . $matches[2] . '.webp'))
                {
                    return true;
                }
                else
                {
                    if (file_exists(_PS_ROOT_DIR_ . '/img/p/' . $folder . '/' . $matches[1] . '.webp') && empty($matches[2]))
                    return true;
                    return false;
                }
            }
        }
        $parse_url = parse_url($url);
        //echo '<pre>'; print_r($url); echo '</pre>';
        //die();
        if (isset($parse_url['path']))
        {
            if (!empty($parse_url['path']))
            {
                if (file_exists(_PS_ROOT_DIR_ . '/' . str_replace(array(
                    '.jpg',
                    '.png'
                ) , '.webp', $parse_url['path'])))
                return true;
            }
        }
        return false;
    }
    public function parseImagesLazy($output, $smarty = NULL)
    {
        if (empty($output) || Tools::getIsset('ajax') || Tools::getIsset('id_order') || Tools::getIsset('ajaxMode') || Tools::getIsset('action') || Tools::getIsset('from-xhr'))
        return $output;
        if (Tools::getIsset('action'))
        {
            if (Tools::getValue('action') == 'quickview')
            return $output;
        }
        if (!empty($_POST))
        {
            if (sizeof($_POST) >= 2)
            return $output;
        }
        
        if (Configuration::get('uit_simple_load') == 0)
        {
            return preg_replace_callback('/<\s*?img\s+[^>]*?\s* *?>/u', function ($matches)
            {
                $img_new = $matches[0];
                if ((int)Configuration::get('uit_use_picture_webp') == 1 && strpos($img_new, 'pi=') === false)
                {
                    preg_match('/\bsrc\s*=\s*[\'"](.*?)[\'"]/u', $matches[0], $img_results);
                    if (!empty($img_results[1]))
                    {
                        if (strpos($img_new, 'data-original') !== false)
                        {
                            preg_match('/data-original\s*=\s*[\'"](.*?)[\'"]/u', $img_new, $img_results2);
                            if (isset($img_results2[1]))
                            {
                                if (!empty($img_results2[1]))
                                $img_results = $img_results2;
                            }
                        }
                        $webp_src = str_replace(array(
                            '.jpg',
                            '.png'
                        ) , '.webp', $img_results[1]);
                        $is_valid = true;
                        if (substr_count($webp_src, '//') > 0)
                        {
                            if (!$this->does_url_exists($webp_src))
                            $is_valid = false;
                        }
                        else
                        {
                            if (!file_exists(_PS_ROOT_DIR_ . '/' . $webp_src))
                            $is_valid = false;
                        }

                        if ($is_valid){


                            $img_new = str_replace($img_new, html_entity_decode('&lt;picture&gt;&lt;source srcset=&quot;') . $webp_src . html_entity_decode('&quot; type=&quot;image/webp&quot;&gt;') . str_replace(html_entity_decode('&lt;img'), html_entity_decode('&lt;img pi=&quot;1&quot;'), $img_new) . html_entity_decode('&lt;/picture&gt;'), $img_new);




                        }
                    }
                }
                if (Configuration::get('uit_lazy_load') != 'enabled')
                    return $img_new;

                if (strpos($matches[0], 'uitlazyload') !== false || strpos($matches[0], 'slider') !== false)
                    return $img_new;
                $exceptions_validation = true;
                $exceptions = Configuration::get('uit_exceptions');
                if (!empty($exceptions))
                {
                    $elements = array_unique(array_filter(explode(',', $exceptions)));
                    if (!empty($elements))
                    {
                        if (strpos($matches[0], 'class') !== false)
                        {
                            foreach ($elements as $e)
                            {
                                if (strpos($matches[0], str_replace('.', '', trim($e))) !== false)
                                {
                                    $exceptions_validation = false;
                                }
                            }
                        }
                    }
                }
                if ($exceptions_validation && (strpos($matches[0], Configuration::get('uit_lazy_load_image')) === false))
                {

                    $img_new = preg_replace('/ src\s*=\s*[\'"](.*?)[\'"]/u', ' src="' . _MODULE_DIR_ . '' . $this->name . '/views/img/' . Configuration::get('uit_lazy_load_image') . '" data-original="$1"', $img_new);
                   
                    if (strpos($img_new, 'class') !== false){
                        $img_new = preg_replace('/\bclass\s*=\s*[\'"](.*?)[\'"]/u', 'class="$1 uitlazyload"', $img_new);
                    }
                    else{
                  
                        $img_new = str_replace(html_entity_decode('&lt;img'), html_entity_decode('&lt;img class=&quot;uitlazyload&quot;'), $img_new);
                    }






                }
                return $img_new;
            }
            , $output);
        }
        require_once (_PS_MODULE_DIR_ . '/' . $this->name . '/src/simple_html_dom.php');
        $output = str_replace('//setREVStartSize();', '', $output);
        $html = str_get_html($output);
        $uit_lazy_op = (int)Configuration::get('uit_lazy_op');
        $exceptions = Configuration::get('uit_exceptions');
        if (is_object($html))
        {
            foreach ($html->find('img') as $imgc)
            {
                $attribute_src = $imgc->getAttribute('src');
                $img_new = $imgc->outertext;
                if (((int)Configuration::get('uit_use_picture_webp') == 1 && strpos($img_new, 'pi=') === false))
                {
                    preg_match('/\bsrc\s*=\s*[\'"](.*?)[\'"]/u', $img_new, $img_results);
                    //echo '<pre>'; print_r($img_results); echo '</pre>';
                    if (!empty($img_results[1]))
                    {
                        if (strpos($img_new, 'data-original') !== false)
                        {
                            preg_match('/data-original\s*=\s*[\'"](.*?)[\'"]/u', $img_new, $img_results2);
                            if (isset($img_results2[1]))
                            {
                                if (!empty($img_results2[1]))
                                $img_results = $img_results2;
                            }
                        }
                        $webp_src = str_replace(array(
                            '.jpg',
                            '.png'
                        ) , '.webp', $img_results[1]);
                        $is_valid = true;
                        if (substr_count($webp_src, '//') > 0)
                        {
                            //var_dump($webp_src);
                            //var_dump($this->does_url_exists($webp_src));
                            if (!$this->does_url_exists($webp_src))
                            $is_valid = false;
                        }
                        else
                        {
                            if (!file_exists(_PS_ROOT_DIR_ . '/' . $webp_src))
                            $is_valid = false;
                        }
                        if ($is_valid){
                             $imgc->outertext = str_replace($img_new, html_entity_decode('&lt;picture&gt;&lt;source srcset=&quot;') . $webp_src . html_entity_decode('&quot; type=&quot;image/webp&quot;&gt;') . str_replace(html_entity_decode('&lt;img'), html_entity_decode('&lt;img pi=&quot;1&quot;'), $img_new) . html_entity_decode('&lt;/picture&gt;'), $img_new);
                        }
                       
                    }
                }
                else
                {
                    if ((substr_count($attribute_src, '/modules/') > 0 || substr_count($attribute_src, '/themes/') > 0 || substr_count($attribute_src, '/img/') > 0) && substr_count($attribute_src, '.webp') == 0)
                    {
                        $webp_src = str_replace(array(
                            '.jpg',
                            '.png'
                        ) , '.webp', $attribute_src);
                        $is_valid = true;
                        if (substr_count($webp_src, '//') > 0)
                        {
                            if (!$this->does_url_exists($webp_src))
                            $is_valid = false;
                        }
                        else
                        {
                            if (!file_exists(_PS_ROOT_DIR_ . '/' . $webp_src))
                            $is_valid = false;
                        }
                        if ($is_valid)
                        {
                            $imgc->outertext = str_replace($attribute_src, $webp_src, $imgc->outertext);
                        }
                    }
                }
                if (Configuration::get('uit_lazy_load') == 'enabled')
                {
                    $parent_node_class = Tools::strtolower($imgc->parent()
                        ->getAttribute('class'));
                    $current_class = Tools::strtolower($imgc->getAttribute('class'));
                    $is_valid = true;
                    $attribute_src = $imgc->getAttribute('src');
                    $exceptions_validation = true;
                    if (!empty($exceptions))
                    {
                        $elements = array_unique(array_filter(explode(',', $exceptions)));
                        if (!empty($elements))
                        {
                            if ($current_class)
                            {
                                foreach ($elements as $e)
                                {
                                    if (strpos($current_class, str_replace('.', '', trim($e))) !== false)
                                    {
                                        $exceptions_validation = false;
                                    }
                                }
                            }
                            if ($parent_node_class)
                            {
                                foreach ($elements as $e)
                                {
                                    if (strpos($parent_node_class, str_replace('.', '', trim($e))) !== false)
                                    {
                                        $exceptions_validation = false;
                                    }
                                }
                            }
                        }
                    }
                    if (strpos($current_class, 'uitlazyload') !== false || strpos($current_class, 'rev') !== false || strpos($parent_node_class, 'magic') !== false || strpos($parent_node_class, 'layer') !== false || strpos($attribute_src, 'slide') !== false || !$exceptions_validation)
                    $is_valid = false;
                    if ($uit_lazy_op == 1)
                    {
                        if (strpos($parent_node_class, 'thumbnail') === false && strpos($parent_node_class, 'product_img_link') === false && strpos($parent_node_class, 'thumb') === false && strpos($parent_node_class, 'product-cover') === false)
                        $is_valid = false;
                    }
                    if ($is_valid)
                    {
                        $imgc->setAttribute('class', $current_class . ' uitlazyload');
                        $imgc->setAttribute('data-original', $attribute_src);
                        $imgc->setAttribute('src', _MODULE_DIR_ . $this->name . '/views/img/' . Configuration::get('uit_lazy_load_image'));
                    }
                }
            }
        }
        return $html . '';
    }
    private function get_zoom_html()
    {
        $product = new Product((int)Tools::getValue('id_product') , true, (int)$this
            ->context
            ->cookie
            ->id_lang);
        $lrw = $product->link_rewrite;
        $pid = (int)$product->id;
        $link = new Link();
        $productImages = $product->getImages((int)$this
            ->context
            ->cookie
            ->id_lang);
        if (!is_array($productImages) || empty($productImages))
        {
            return '';
        }
        if (strlen(Configuration::get('uit_zoom_normal_image_size')) > 3)
        {
            $thumb_info_normal = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'image_type` WHERE products = 1 AND name = "' . pSQL(Configuration::get('uit_zoom_normal_image_size')) . '"');
        }
        else
        $thumb_info_normal = array(
            'width' => 502,
            'height' => 334
        );
        $prd_images_full = array();
        $thumbnail_size = Configuration::get('uit_zoom_thumb_image_size');
        $normal_size = Configuration::get('uit_zoom_normal_image_size');
        $full_size = Configuration::get('uit_zoom_full_image_size');
        foreach ($productImages as $i)
        {
            $a = array();
            $a['full'] = '//' . $link->getImageLink($product->link_rewrite, $pid . '-' . $i['id_image'], $full_size);
            $a['normal'] = '//' . $link->getImageLink($product->link_rewrite, $pid . '-' . $i['id_image'], $normal_size);
            if (!empty($thumbnail_size))
            $a['thumb'] = '//' . $link->getImageLink($product->link_rewrite, $pid . '-' . $i['id_image'], $thumbnail_size);
            else
            $a['thumb'] = '';
            $a['legend'] = $i['legend'];
            $prd_images_full[] = $a;
        }
        $type = (int)Configuration::get('uit_zoom_type');
        $thumb_info_small = false;
        if (!empty($thumbnail_size))
        {
            if (strlen(Configuration::get('uit_zoom_thumb_image_size')) > 2)
            $thumb_info_small = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'image_type` WHERE products = 1 AND name = "' . pSQL(Configuration::get('uit_zoom_thumb_image_size')) . '"');
            else
            $thumb_info_small = array(
                'width' => 80,
                'height' => 80
            );
        }
        if (sizeof($prd_images_full) == 1)
        $thumb_info_small = false;
        $arr = array();
        $arr['type'] = $type;
        $arr['productImagesFull'] = $prd_images_full;
        $arr['thumbnail_size'] = $thumbnail_size;
        $arr['thumb_info_normal'] = $thumb_info_normal;
        $arr['thumb_info_small'] = $thumb_info_small;
        $this
            ->smarty
            ->assign($arr);
        $zoom_html = $this->display(__FILE__, 'views/templates/front/zoom/zoom_html.tpl');
        return $zoom_html;
    }
    public function parseTemplateStandard($output, $smarty = NULL)
    {
        if (Tools::getIsset('ajax'))
        return $output;
        if (Tools::getIsset('action'))
        {
            if (Tools::getValue('action') == 'quickview')
            return $output;
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true)
        if (substr_count($output, 'images-container') == 0)
        return $output;
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<') === true)
        if (substr_count($output, 'image-block') == 0)
        return $output;
        global $smarty;
        $output = str_replace('js-qv-product-images', 'js-qv-product-images-disabled', $output);
        $output = str_replace('js-qv-product-cover', 'js-qv-product-cover-disabled', $output);
        $zoom_html = $this->get_zoom_html();
        if (empty($zoom_html))
        return $output;
        require_once (_PS_MODULE_DIR_ . '/' . $this->name . '/src/simple_html_dom.php');
        $output = str_replace('//setREVStartSize();', '', $output);
        $html = str_get_html($output);
        if (is_object($html))
        {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true)
            {
                $inserted = false;
                foreach ($html->find('.images-container') as $imgc)
                {
                    $inserted = true;
                    $imgc->outertext = $zoom_html;
                }
                foreach ($html->find('#product-modal') as $imgc)
                {
                    $imgc->outertext = '';
                }
                foreach ($html->find('.scroll-box-arrows') as $imgc)
                $imgc->style = "display:none !important;";
                if (!$inserted)
                {
                    foreach ($html->find('#content, .product-block-images') as $imgc)
                    $imgc->innertext .= $zoom_html;
                }
            }
            else
            {
                foreach ($html->find('#image-block') as $imgc)
                $imgc->innertext = $zoom_html;
                foreach ($html->find('#views_block') as $imgc)
                $imgc->outertext = '';
                foreach ($html->find('#view_full_size') as $imgc)
                $imgc->outertext = '';
            }
        }
        if (!$html)
        $html = $output;
        unset($zoom_html);
        return $html . '';
    }
    public function hookdisplayHeader($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=') === true)
        {
            if (Configuration::get('uit_lazy_load') == 'enabled')
            $this
                ->context
                ->controller
                ->registerJavascript(
            'qazy-lib',
            'modules/' . $this->name . '/views/js/qazy.js',
            array(
                'position' => 'bottom',
                'attribute' => 'async',
                'inline' => false,
                'priority' => 10,
            )
);
            if (Configuration::get('uit_mouse_hover') == 'enabled')
            $this
                ->context
                ->controller
                ->registerJavascript(
            'mousehover-lib',
            'modules/' . $this->name . '/views/js/mousehover_17.js',
            array(
                'position' => 'bottom',
                'attribute' => 'async',
                'inline' => false,
                'priority' => 11,
            )
);
            if (Configuration::get('uit_zoom') == '1' && 'product' === $this
                ->context
                ->controller
                ->php_self)
            {
                $this
                    ->context
                    ->controller
                    ->registerStylesheet(
                'bxslider-css',
                'js/jquery/plugins/bxslider/jquery.bxslider.css',
                [
                'media' => 'all',
                'priority' => 200,
                ]
);
                $this
                    ->context
                    ->controller
                    ->registerJavascript(
                'bxslides-js',
                'js/jquery/plugins/bxslider/jquery.bxslider.js',
                [
                'priority' => 200,
                'inline' => false,
                ]
);
                $this
                    ->context
                    ->controller
                    ->registerJavascript(
                'modulobox-lib',
                'modules/' . $this->name . '/views/js/modulobox.min.js',
                array(
                    'position' => 'bottom',
                    'inline' => false,
                    'priority' => 300,
                )
);
                $this
                    ->context
                    ->controller
                    ->registerStylesheet(
                'modulobox-lib-css',
                'modules/' . $this->name . '/views/css/modulobox.min.css',
                [
                'media' => 'all',
                'priority' => 300,
                ]
);
                $this
                    ->context
                    ->smarty
                    ->assign(
                array(
                    'jqZoomEnabled' => false
                ));
            }
            $this->register_filters($params);
        }
        else
        {
            if (Configuration::get('uit_lazy_load') == 'enabled')
            $this
                ->context
                ->controller
                ->addJS(($this->_path) . 'views/js/qazy.js', 'all');
            if (Configuration::get('uit_zoom') == '1')
            {
                $this
                    ->context
                    ->controller
                    ->addJqueryPlugin('bxslider');
                $this
                    ->context
                    ->controller
                    ->addJS(($this->_path) . 'views/js/modulobox.min.js', 'all');
                $this
                    ->context
                    ->controller
                    ->addCSS(($this->_path) . 'views/css/modulobox.min.css', 'all');
                $this
                    ->context
                    ->smarty
                    ->assign(
                array(
                    'jqZoomEnabled' => false
                ));
            }
            $this->register_filters($params);
            if (Configuration::get('uit_mouse_hover') == 'enabled')
            {
                $this
                    ->context
                    ->controller
                    ->addJS(($this->_path) . 'views/js/mousehover.js', 'all');
            }
        }
    }
    public static function get_domain_prefix()
    {
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        return $protocol_link;
    }
    public function hookaddproduct($params)
    {
        $this->hookupdateproduct($params);
        return true;
    }
    public static function imageExists($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if (curl_exec($ch) !== false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    public static function get_webp_from_advancedplugins($path = NULL, $image_type = 'image_file_b64', $image_quality = 85)
    {
        if ($path == NULL)
        return false;
        $ch = curl_init();
        $data = array(
            'action' => 'convert2webp',
            'q' => $image_quality,
            'token' => '63etrbf3yhrtbsgwsd',
            $image_type => $path,
        );
        curl_setopt($ch, CURLOPT_URL, "https://api.advancedplugins.com");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: image/jpeg"));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        $server_output = curl_exec($ch);
        curl_close($ch);
        if (!$server_output)
        {
            return false;
        }
        if (substr_count($server_output, 'WEBPVP8') == 0)
        {
            return false;
        }
        $im = getimagesizefromstring($server_output);
        return $server_output;
    }
    public static function get_optimized_image_api($file, $quality = 70)
    {
        if (!file_exists($file))
        return array(
            'is_end' => 1
        );
        if ($quality < 60)
        $quality = 60;
        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];
        $output = new CURLFile($file, $mime, $name);
        $data = array(
            "files" => $output,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.resmush.it/?qlty=' . (int)$quality);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        if (curl_errno($ch))
        {
            return array(
                'is_end' => 1
            );
        }
        curl_close($ch);
        $o = json_decode($result, true);
        if (isset($o->error) || $o == NULL)
        {
            return array(
                'is_end' => 1
            );
        }
        else
        return $o;
    }
    public static function get_url_content2($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
        curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 8);
        $contents = curl_exec($curl);
        curl_close($curl);
        if (empty($contents))
        {
            return false;
        }
        return $contents;
    }
    public static function get_optimized_image_url($img_src, $quality = 70)
    {
        $service = 'http://api.resmush.it/ws.php?qlty=' . $quality . '&img=';
        $o = json_decode(self::get_url_content_static2($service . $img_src) , true);
        if (isset($o->error))
        {
            return array(
                'is_end' => 1
            );
        }
        else
        return $o;
    }
    public function hookupdateproduct($params)
    {
        $id_product = (int)$params['id_product'];
        $image_types = ImageType::getImagesTypes('products');
        $alt_apply = Configuration::get('alt_tags_auto');
        $alt_format = Configuration::get('uit_alt_format');
        $sql = '';
        
        $alt_tags_enbled = 0;
        foreach ($image_types as $it)
        {
            $sql2 = 'SELECT * FROM  `' . _DB_PREFIX_ . 'uit_smush` WHERE object_id = ' . (int)$id_product . ' AND image_size = "' . pSQL($it['name']) . '"  AND object_type ="product"';
            $row2 = Db::getInstance()->getRow($sql2);
            if (!$row2)
            {
                $sql = 'INSERT INTO  `' . _DB_PREFIX_ . 'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, processed, image_size  )  VALUES (NULL,' . (int)$id_product . ',"product", 0,0, "' . date('Y-m-d H:i:s') . '", 0, "' . pSQL($it['name']) . '" )';
                Db::getInstance()->execute($sql);
            }
        }
        if ((int)Configuration::get('uit_compress_os') == 1)
        {
            $sql = 'INSERT INTO  `' . _DB_PREFIX_ . 'uit_smush` ( id, object_id, object_type, original_size, new_size, date_add, processed, image_size  )  VALUES (NULL,' . (int)$id_product . ', "product", 0,0, "' . date('Y-m-d H:i:s') . '", 0, "" )';
            Db::getInstance()->execute($sql);
        }
        if ($alt_apply == 'yes-all')
        {
            $sql = 'SELECT  * FROM `' . _DB_PREFIX_ . 'image` i INNER JOIN `' . _DB_PREFIX_ . 'image_lang` il ON i.id_image = il.id_image WHERE i.id_product =' . (int)$id_product;
            $product_images = Db::getInstance()->executeS($sql);
            if ($product_images)
            {
                foreach ($product_images as $pi)
                {
                    $q = 'UPDATE `' . _DB_PREFIX_ . 'image_lang` SET legend = "" WHERE id_image = ' . (int)$pi['id_image'];
                    Db::getInstance()->execute($q);
                }
            }
            $alt_tags_enbled = 1;
        }
        elseif ($alt_apply == 'yes-only-without-tags')
        {
            $sql = 'SELECT  * FROM `' . _DB_PREFIX_ . 'image` i INNER JOIN `' . _DB_PREFIX_ . 'image_lang` il ON i.id_image = il.id_image AND il.legend = "" WHERE i.id_product =' . (int)$id_product;
            $alt_tags_enbled = 1;
        }
        if ($alt_tags_enbled == 1)
        {
            $results = Db::getInstance()->executeS($sql);
            if (!empty($results))
            {
                foreach ($results as $res)
                {
                    $alt_tag = $alt_format;
                    $product = new Product($res['id_product'], true, $res['id_lang']);
                    $category = new Category($product->id_category_default, $res['id_lang']);
                    $price = Product::getPriceStatic($product->id, true);
                    $alt_tag = str_replace(array(
                        '{PARENT_CATEGORY_NAME}',
                        '{SUPPLIER_NAME}',
                        '{MANUFACTURER_NAME}',
                        '{PRODUCT_NAME}',
                        '{PRODUCT_PRICE}',
                        '{PRODUCT_SHORT_DESCRIPTION}',
                        '{IMAGE_POSITION}',
                        '{PRODUCT_REFERENCE}'
                    ) , array(
                        $category->name,
                        $product->supplier_name,
                        $product->manufacturer_name,
                        $product->name,
                        $price,
                        $product->description_short,
                        $res['position'],
                        $product->reference
                    ) , $alt_tag);
                    $q = 'UPDATE `' . _DB_PREFIX_ . 'image_lang` SET legend = "' . pSQL($alt_tag) . '" WHERE id_image =' . (int)$res['id_image'] . ' AND id_lang =' . (int)$res['id_lang'];
                    Db::getInstance()->execute($q);
                }
            }
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<') === true && (int)Configuration::get('uit_auto_webp') == 1)
        {
            $this->convert_all_product_images_to_webp($id_product);
        }
        return true;
    }
    public function convert_all_product_images_to_webp($id_product = NULL, $delete = false)
    {
        require_once (_PS_MODULE_DIR_ . '/ultimateimagetool/vendor/autoload.php');
        $results = Db::getInstance()->executeS('SELECT name FROM `' . _DB_PREFIX_ . 'image_type` WHERE products = 1 ORDER BY `width` DESC');
        $link = new Link();
        $langs = Language::getLanguages(true);
        $image_quality = (int)Configuration::get('uit_image_quality_webp_cron');
        if ($image_quality < 60)
        $image_quality = 60;
        if ($results)
        {
            foreach ($results as $res)
            {
                $image_type = $res['name'];
                foreach ($langs as $l)
                {
                    $product = new Product($id_product, false, $l['id_lang']);
                    $images = $product->getImages($l['id_lang']);
                    foreach ($images as $image)
                    {
                        $image_old = new Image($image['id_image']);
                        if (Tools::strlen($image_type) > 0)
                        {
                            $image_path = _PS_PROD_IMG_DIR_ . $image_old->getExistingImgPath() . '-' . $image_type . '.jpg';
                        }
                        else
                        {
                            $image_path = _PS_PROD_IMG_DIR_ . $image_old->getExistingImgPath() . '.jpg';
                        }
                        if (!$delete)
                        {
                            if (file_exists($image_path) && !file_exists(str_replace('.jpg', '.webp', $image_path)))
                            {
                                if ((int)Configuration::get('uit_use_external_api') != 1)
                                {
                                    try
                                    {
                                        $true = \WebPConvert\WebPConvert::convert($image_path, str_replace('.jpg', '.webp', $image_path) , array(
                                            'converters' => ['imagick',
                                            'imagemagick',
                                            'gd'],
                                            'lossless' => true,
                                            'max-quality' => $image_quality,
                                            'quality' => $image_quality,
                                            'default-quality' => $image_quality,
                                            'fail' => 'original',
                                            'serve-image' => ['headers' => ['cache-control' => true,
                                            'vary-accept' => true],
                                            'cache-control-header' => 'max-age=2'],
                                            'convert' => ['quality' => $image_quality]
                                        ));
                                    }
                                    catch(Exception $e)
                                    {
                                    }
                                }
                                else
                                {
                                    if (!file_exists(str_replace('.jpg', '.webp', $image_path)))
                                    {
                                        $image = ultimateimagetool::get_webp_from_advancedplugins(base64_encode(Tools::file_get_contents($image_path)) , 'image_file_b64', $image_quality);
                                        if ($image)
                                        file_put_contents(str_replace('.jpg', '.webp', $image_path) , $image);
                                    }
                                }
                            }
                        }
                        else
                        {
                            $image_path_webp = str_replace(array(
                                '.jpg',
                                '.png'
                            ) , '.webp', $image_path);
                            if (file_exists($image_path_webp))
                            {
                                unlink($image_path_webp);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    public function _buildData()
    {
        $this->languages = Language::getLanguages(true);
    }
    public function process_post()
    {
        Configuration::updateValue('uit_compress_os', Tools::getValue('uit_compress_os'));
        Configuration::updateValue('uit_products', Tools::getValue('product_per_excution'));
        Configuration::updateValue('uit_cron_quality', Tools::getValue('cron_image_quality'));
        Configuration::updateValue('uit_quality', Tools::getValue('cron_image_quality'));
        return true;
    }
    public function getContent()
    {
        if (Tools::getIsset('product_per_excution'))
        $this->process_post();
        $this->_html = html_entity_decode('&lt;h2&gt;') . $this->displayName . html_entity_decode('&lt;/h2&gt;');
        $this->_buildData();
        $this->_buildHtml();
        return $this->_html;
    }
    private function get_url_content($url)
    {
        return Tools::file_get_contents($url);
    }
    public static function get_url_content_static2($url)
    {
        return Tools::file_get_contents($url);
    }
    public function set_sitemap_variables()
    {
        $arr = array();
        $arr['sitemap_image_size'] = Configuration::get('sitemap_image_size');
        $this
            ->smarty
            ->assign($arr);
    }
    public function set_alt_tags()
    {
        $arr = array();
        $arr['uit_alt_format'] = Configuration::get('uit_alt_format');
        $arr['alt_tags_auto'] = Configuration::get('alt_tags_auto');
        $this
            ->smarty
            ->assign($arr);
    }
    public function set_product_variables()
    {
        $arr = array();
        $sql = 'SELECT  count(id_product) FROM `' . _DB_PREFIX_ . 'product`  WHERE active = 1';
        $results = Db::getInstance()->getRow($sql);
        if ($results)
        $arr['products_count'] = $results['count(id_product)'];
        else
        $arr['products_count'] = 0;
        $is_writable = false;
        if (Tools::substr(decoct(fileperms(_PS_PROD_IMG_DIR_)) , -3) >= 755)
        $is_writable = true;
        $image_types = ImageType::getImagesTypes('products');
        $image_types_convert = $image_types;
        $k = 0;
        $arr['total_saved_space_products'] = (int)Configuration::get('uit_saved_');
        foreach ($image_types as $it)
        {
            $image_types[$k]['saved_space'] = (int)Configuration::get('uit_saved_' . $it['name']);
            $arr['total_saved_space_products'] += $image_types[$k]['saved_space'];
            $off = Configuration::get('uit_i_o_' . $it['name']);
            $off_convert = Configuration::get('uit_i_o_c_' . $it['name']);
            if ($off < $arr['products_count'])
            $image_types[$k]['offset'] = (int)$off;
            else
            $image_types[$k]['offset'] = (int)$arr['products_count'];
            if ($off_convert < $arr['products_count'])
            $image_types_convert[$k]['offset'] = (int)$off_convert;
            else
            $image_types_convert[$k]['offset'] = (int)$arr['products_count'];
            if ((int)$arr['products_count'] > 0)
            {
                $image_types_convert[$k]['percent'] = $image_types_convert[$k]['offset'] * 100 / $arr['products_count'];
                $image_types[$k]['percent'] = $image_types[$k]['offset'] * 100 / $arr['products_count'];
            }
            else
            {
                $image_types_convert[$k]['percent'] = 0;
                $image_types[$k]['percent'] = 0;
            }
            $k++;
        }
        //var_dump(Configuration::get('uit_use_external_api'));
        //die();
        $arr['product_writable'] = $is_writable;
        $arr['product_sizes'] = $image_types;
        $arr['product_sizes_convert'] = $image_types_convert;
        $arr['original_saved_space'] = (int)Configuration::get('uit_saved_');
        $offset_original = (int)Configuration::get('uit_i_o_');
        $offset_original_convert = (int)Configuration::get('uit_i_o_c_');
        $arr['uit_product'] = (int)Configuration::get('uit_products');
        $arr['uit_compress_os'] = (int)Configuration::get('uit_compress_os');
        $arr['uit_cron_quality'] = (int)Configuration::get('uit_cron_quality');
        $arr['uit_quality'] = (int)Configuration::get('uit_quality');
        $arr['uit_quality_cms'] = (int)Configuration::get('uit_quality_cms');
        $arr['uit_use_webp'] = (int)Configuration::get('uit_use_webp');
        $arr['uit_use_external_api'] = (int)Configuration::get('uit_use_external_api');
        $arr['uit_exceptions'] = Configuration::get('uit_exceptions');
        $arr['uit_use_picture_webp'] = Configuration::get('uit_use_picture_webp');
        $arr['has_cloudflare'] = $this->is_cloudflare();
        /**
        if($arr['has_cloudflare']){
        $arr['uit_use_webp_termination'] = 1;
        Configuration::updateValue('uit_use_webp_termination', 1);
        }
        else
        */
        $arr['uit_use_webp_termination'] = (int)Configuration::get('uit_use_webp_termination');
        $arr['uit_lazy_op'] = (int)Configuration::get('uit_lazy_op');
        $arr['uit_auto_webp'] = (int)Configuration::get('uit_auto_webp');
        $arr['logs'] = false;
        $arr['log_pages'] = false;
        $arr['uit_cron_last_execution'] = Configuration::get('uit_cron_last_execution');
        if ($offset_original >= $arr['products_count'])
        {
            $arr['original_offset'] = (int)$arr['products_count'];
        }
        else
        {
            $arr['original_offset'] = (int)$offset_original;
        }
        if ($offset_original_convert >= $arr['products_count'])
        {
            $arr['original_offset_convert'] = (int)$arr['products_count'];
        }
        else
        {
            $arr['original_offset_convert'] = (int)$offset_original_convert;
        }
        if ($arr['products_count'] > 0)
        {
            $arr['original_percent'] = $arr['original_offset'] * 100 / $arr['products_count'];
            $arr['original_percent_convert'] = $arr['original_offset_convert'] * 100 / $arr['products_count'];
        }
        else
        {
            $arr['original_percent'] = 0;
            $arr['original_percent_convert'] = 0;
        }
        $this
            ->smarty
            ->assign($arr);
    }
    public function set_category_variables()
    {
        $arr = array();
        $sql = 'SELECT  count(id_category) FROM `' . _DB_PREFIX_ . 'category`  WHERE active = 1';
        $results = Db::getInstance()->getRow($sql);
        if ($results)
        $arr['category_count'] = $results['count(id_category)'];
        else
        $arr['category_count'] = 0;
        $is_writable = false;
        if (Tools::substr(decoct(fileperms(_PS_CAT_IMG_DIR_)) , -3) >= 755)
        $is_writable = true;
        $image_types = ImageType::getImagesTypes('categories');
        $image_types_convert = $image_types;
        $k = 0;
        $arr['total_saved_space_categories'] = (int)Configuration::get('uit_saved_cat_');
        foreach ($image_types as $it)
        {
            $image_types[$k]['saved_space'] = (int)Configuration::get('uit_saved_cat_' . $it['name']);
            $arr['total_saved_space_categories'] += $image_types[$k]['saved_space'];
            $off = Configuration::get('uit_i_o_cat_' . $it['name']);
            $off_convert = Configuration::get('uit_i_o_cat_c_' . $it['name']);
            if ($off < $arr['category_count'])
            $image_types[$k]['offset'] = (int)$off;
            else
            $image_types[$k]['offset'] = (int)$arr['category_count'];
            if ($off_convert < $arr['category_count'])
            $image_types_convert[$k]['offset'] = (int)$off_convert;
            else
            $image_types_convert[$k]['offset'] = (int)$arr['category_count'];
            $image_types_convert[$k]['percent'] = $image_types_convert[$k]['offset'] * 100 / $arr['category_count'];
            $image_types[$k]['percent'] = $image_types[$k]['offset'] * 100 / $arr['category_count'];
            $k++;
        }
        $arr['category_sizes_convert'] = $image_types_convert;
        $arr['category_sizes'] = $image_types;
        $arr['category_writable'] = $is_writable;
        $arr['original_saved_space_cat'] = (int)Configuration::get('uit_saved_cat_');
        $offset_original = (int)Configuration::get('uit_i_o_cat_');
        $offset_original_convert = (int)Configuration::get('uit_i_o_cat_c_');
        $arr['uit_category'] = (int)Configuration::get('uit_categories');
        $arr['uit_cron_quality_cat'] = (int)Configuration::get('uit_cron_quality_cat');
        $arr['uit_quality_cat'] = (int)Configuration::get('uit_quality_cat');
        $arr['uit_image_quality_webp_cron'] = (int)Configuration::get('uit_image_quality_webp_cron');
        if ($offset_original >= $arr['category_count'])
        $arr['original_offset_cat'] = (int)$arr['category_count'];
        else
        $arr['original_offset_cat'] = (int)$offset_original;
        if ($offset_original_convert >= $arr['category_count'])
        {
            $arr['original_offset_cat_convert'] = (int)$arr['category_count'];
        }
        else
        {
            $arr['original_offset_cat_convert'] = (int)$offset_original_convert;
        }
        if ($arr['category_count'] > 0)
        {
            $arr['original_percent_cat'] = $arr['original_offset_cat'] * 100 / $arr['category_count'];
            $arr['original_percent_cat_convert'] = $arr['original_offset_cat_convert'] * 100 / $arr['category_count'];
        }
        else
        {
            $arr['original_percent_cat'] = 0;
            $arr['original_percent_cat_convert'] = 0;
        }
        $this
            ->smarty
            ->assign($arr);
    }
    public function set_manufacturers_variables()
    {
        $arr = array();
        $sql = 'SELECT  count(id_manufacturer) FROM `' . _DB_PREFIX_ . 'manufacturer`  WHERE active = 1';
        $results = Db::getInstance()->getRow($sql);
        if ($results)
        $arr['manufacturer_count'] = $results['count(id_manufacturer)'];
        else
        $arr['manufacturer_count'] = 0;
        $is_writable = false;
        if (Tools::substr(decoct(fileperms(_PS_MANU_IMG_DIR_)) , -3) >= 755)
        $is_writable = true;
        $image_types = ImageType::getImagesTypes('manufacturers');
        $image_types_convert = $image_types;
        $k = 0;
        $arr['total_saved_space_manufacturers'] = (int)Configuration::get('uit_saved_manuf_');
        foreach ($image_types as $it)
        {
            $image_types[$k]['saved_space'] = (int)Configuration::get('uit_saved_manuf_' . $it['name']);
            $arr['total_saved_space_manufacturers'] += $image_types[$k]['saved_space'];
            $off = Configuration::get('uit_i_o_manuf_' . $it['name']);
            $off_convert = Configuration::get('uit_i_o_manuf_' . $it['name']);
            if ($off < $arr['manufacturer_count'])
            $image_types[$k]['offset'] = (int)$off;
            else
            $image_types[$k]['offset'] = (int)$arr['manufacturer_count'];
            if ($off_convert < $arr['manufacturer_count'])
            $image_types_convert[$k]['offset'] = (int)$off_convert;
            else
            $image_types_convert[$k]['offset'] = (int)$arr['manufacturer_count'];
            if ($arr['manufacturer_count'] > 0)
            $image_types_convert[$k]['percent'] = $image_types_convert[$k]['offset'] * 100 / $arr['manufacturer_count'];
            else
            $image_types_convert[$k]['percent'] = 0;
            $image_types[$k]['percent'] = $image_types[$k]['offset'] * 100 / $arr['manufacturer_count'];
            $k++;
        }
        $arr['manufacturer_sizes_convert'] = $image_types_convert;
        $arr['manufacturer_sizes'] = $image_types;
        $arr['manufacturer_writable'] = $is_writable;
        $arr['original_saved_space_manuf'] = (int)Configuration::get('uit_saved_manuf_');
        $offset_original = (int)Configuration::get('uit_i_o_manuf_');
        $offset_original_convert = (int)Configuration::get('uit_i_o_manuf_c_');
        $arr['uit_manufacturer'] = (int)Configuration::get('uit_manufacturer');
        $arr['uit_cron_quality_manuf'] = (int)Configuration::get('uit_cron_quality_manuf');
        $arr['uit_quality_manuf'] = (int)Configuration::get('uit_quality_manuf');
        if ($offset_original >= $arr['manufacturer_count'])
        $arr['original_offset_manuf'] = (int)$arr['manufacturer_count'];
        else
        $arr['original_offset_manuf'] = (int)$offset_original;
        if ($offset_original_convert >= $arr['manufacturer_count'])
        {
            $arr['original_offset_manuf_convert'] = (int)$arr['manufacturer_count'];
        }
        else
        {
            $arr['original_offset_manuf_convert'] = (int)$offset_original_convert;
        }
        if ($arr['manufacturer_count'] > 0)
        {
            $arr['original_percent_manuf'] = $arr['original_offset_manuf'] * 100 / $arr['manufacturer_count'];
            $arr['original_percent_manuf_convert'] = $arr['original_offset_manuf_convert'] * 100 / $arr['manufacturer_count'];
        }
        else
        {
            $arr['original_percent_manuf'] = 0;
            $arr['original_percent_manuf_convert'] = 0;
        }
        $this
            ->smarty
            ->assign($arr);
    }
    public function set_suppliers_variables()
    {
        $arr = array();
        $sql = 'SELECT  count(id_supplier) FROM `' . _DB_PREFIX_ . 'supplier`  WHERE active = 1';
        $results = Db::getInstance()->getRow($sql);
        if ($results)
        $arr['supplier_count'] = $results['count(id_supplier)'];
        else
        $arr['supplier_count'] = 0;
        $is_writable = false;
        if (Tools::substr(decoct(fileperms(_PS_SUPP_IMG_DIR_)) , -3) >= 755)
        $is_writable = true;
        $image_types = ImageType::getImagesTypes('suppliers');
        $image_types_convert = $image_types;
        $k = 0;
        $arr['total_saved_space_suppliers'] = (int)Configuration::get('uit_saved_sup_');
        foreach ($image_types as $it)
        {
            $image_types[$k]['saved_space'] = (int)Configuration::get('uit_saved_sup_' . $it['name']);
            $arr['total_saved_space_suppliers'] += $image_types[$k]['saved_space'];
            $off = Configuration::get('uit_i_o_sup_' . $it['name']);
            $off_convert = Configuration::get('uit_i_o_sup_c_' . $it['name']);
            if ($off < $arr['supplier_count'])
            $image_types[$k]['offset'] = (int)$off;
            else
            $image_types[$k]['offset'] = (int)$arr['supplier_count'];
            if ($off_convert < $arr['supplier_count'])
            $image_types_convert[$k]['offset'] = (int)$off_convert;
            else
            $image_types_convert[$k]['offset'] = (int)$arr['supplier_count'];
            if (!empty($arr['supplier_count']))
            $image_types[$k]['percent'] = $image_types[$k]['offset'] * 100 / $arr['supplier_count'];
            else
            $image_types[$k]['percent'] = 0;
            if (!empty($arr['supplier_count']))
            $image_types_convert[$k]['percent'] = $image_types_convert[$k]['offset'] * 100 / $arr['supplier_count'];
            else
            $image_types_convert[$k]['percent'] = 0;
            $k++;
        }
        $arr['supplier_sizes'] = $image_types;
        $arr['supplier_writable'] = $is_writable;
        $arr['original_saved_space_sup'] = (int)Configuration::get('uit_saved_sup_');
        $offset_original = (int)Configuration::get('uit_i_o_sup_');
        $offset_original_convert = (int)Configuration::get('uit_i_o_cat_c_');
        $arr['uit_supplier'] = (int)Configuration::get('uit_supplier');
        $arr['uit_cron_quality_sup'] = (int)Configuration::get('uit_cron_quality_sup');
        $arr['uit_quality_sup'] = (int)Configuration::get('uit_quality_sup');
        if ($offset_original >= $arr['supplier_count'])
        $arr['original_offset_sup'] = (int)$arr['supplier_count'];
        else
        $arr['original_offset_sup'] = (int)$offset_original;
        if ($offset_original_convert >= $arr['supplier_count'])
        {
            $arr['original_offset_sup_convert'] = (int)$arr['supplier_count'];
        }
        else
        {
            $arr['original_offset_sup_convert'] = (int)$offset_original_convert;
        }
        if (!empty($arr['supplier_count']))
        {
            $arr['original_percent_sup'] = $arr['original_offset_sup'] * 100 / $arr['supplier_count'];
            $arr['original_percent_sup_convert'] = $arr['original_offset_sup_convert'] * 100 / $arr['supplier_count'];
        }
        else
        {
            $arr['original_percent_sup'] = 0;
            $arr['original_percent_sup_convert'] = 0;
        }
        $arr['supplier_sizes_convert'] = $image_types_convert;
        $this
            ->smarty
            ->assign($arr);
    }
    public static function getAllDirs($directory, $directory_seperator)
    {
        $dirs = array_map(function ($item) use ($directory_seperator)
        {
            return $item . $directory_seperator;
        }
        , array_filter(glob($directory . '*') , 'is_dir'));
        foreach ($dirs AS $dir)
        {
            $dirs = array_merge($dirs, self::getAllDirs($dir, $directory_seperator));
        }
        return $dirs;
    }
    public static function getAllImgs($directory, $type = '', $is_webp = false, $get_2x = false)
    {
        $resizedFilePath = array();
        foreach ($directory AS $dir)
        {
            foreach (glob($dir . '/**' . $type . '.jpg') as $filename)
            array_push($resizedFilePath, $filename);
            foreach (glob($dir . '/**' . $type . '.JPG') as $filename)
            array_push($resizedFilePath, $filename);
            foreach (glob($dir . '/**' . $type . '.png') as $filename)
            array_push($resizedFilePath, $filename);
            foreach (glob($dir . '/**' . $type . '.PNG') as $filename)
            array_push($resizedFilePath, $filename);
            foreach (glob($dir . '/**' . $type . '.jpeg') as $filename)
            array_push($resizedFilePath, $filename);
            foreach (glob($dir . '/**' . $type . '.JPEG') as $filename)
            array_push($resizedFilePath, $filename);
            if ($get_2x)
            {
                foreach (glob($dir . '/**' . $type . '2x.jpg') as $filename)
                array_push($resizedFilePath, $filename);
                foreach (glob($dir . '/**' . $type . '2x.png') as $filename)
                array_push($resizedFilePath, $filename);
                foreach (glob($dir . '/**' . $type . '2x.PNG') as $filename)
                array_push($resizedFilePath, $filename);
                foreach (glob($dir . '/**' . $type . '2x.jpeg') as $filename)
                array_push($resizedFilePath, $filename);
                foreach (glob($dir . '/**' . $type . '2x.JPEG') as $filename)
                array_push($resizedFilePath, $filename);
                foreach (glob($dir . '/**' . $type . '2x.JPG') as $filename)
                array_push($resizedFilePath, $filename);
            }
            if ($is_webp)
            {
                foreach (glob($dir . '/**' . $type . '.webp') as $filename)
                array_push($resizedFilePath, $filename);
            }
        }
        return $resizedFilePath;
    }
    public static function getDirContents($root)
    {
        $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS) ,
        RecursiveIteratorIterator::SELF_FIRST,
        RecursiveIteratorIterator::CATCH_GET_CHILD
        // Ignore "Permission denied"
        );
        $paths = array(
            $root
        );
        foreach ($iter as $path => $dir)
        {
            if ($dir->isDir())
            {
                $paths[] = $path;
            }
        }
        return $paths;
    }
    public static function search_for_images_in_folder($directory, $type = '', $is_webp = false, $get_2x = false)
    {
        $directories = self::getDirContents($directory);
        $directories[] = $directory;
        if (substr_count($directory, '/modules') > 0)
        {
            $directories[] = str_replace('/modules', '/img/co', $directory);
            $directories[] = str_replace('/modules', '/img/l', $directory);
            $directories[] = str_replace('/modules', '/img/genders', $directory);
            $directories[] = str_replace('/modules', '/img/os', $directory);
            $directories[] = str_replace('/modules', '/img/su', $directory);
            $directories[] = str_replace('/modules', '/img/jquery-ui', $directory);
            $directories[] = str_replace('/modules', '/img/st', $directory);
            $directories[] = str_replace('/modules', '/img/scenes', $directory);
            $directories[] = str_replace('/modules', '/upload', $directory);
        }
        $directories = array_unique($directories);
        array_filter($directories);
        $allimages = self::getAllImgs($directories, $type, $is_webp, $get_2x);
        return $allimages;
    }
    public function verify_if_htaccess_is_written()
    {
        if (file_exists(_PS_ROOT_DIR_ . '/.htaccess'))
        {
            $str = Tools::file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
            if ($str)
            {
                if (substr_count($str, '# Ultimate Image Tools - Do not edit') > 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
    }
    public static function get_subdirectories($directory)
    {
        $glob = glob($directory . '/**');
        if ($glob === false)
        {
            return array();
        }
        return array_filter($glob, function ($dir)
        {
            return is_dir($dir);
        });
    }
    public function is_cloudflare()
    {
        $image_type = '';
        $sql = 'SELECT  count(*) FROM `' . _DB_PREFIX_ . 'product`  WHERE active = 1';
        $results = Db::getInstance()->getRow($sql);
        $count = $results['count(*)'];
        $offset = rand(0, ($count - 1));
        $sql = 'SELECT  id_product FROM `' . _DB_PREFIX_ . 'product`  WHERE active = 1 ORDER by id_product DESC  LIMIT 1 OFFSET ' . (int)$offset;
        $results = Db::getInstance()->executeS($sql);
        $id_product = 0;
        if ($results)
        {
            $l = Language::getLanguages(true);
            $l = end($l);
            $results = $results[0];
            $id_product = $results['id_product'];
            $link = new Link();
            $product = new Product($results['id_product'], false, $l['id_lang']);
            $images = $product->getImages($l['id_lang']);
            if ($images)
            {
                $images = end($images);
                foreach ($images as $image)
                {
                    if (isset($image['id_image']))
                    {
                        $imageLink = self::get_domain_prefix() . $link->getImageLink($product->link_rewrite, $image['id_image'], $image_type);
                        if ($imageLink)
                        {
                            $url = $imageLink;
                            $ct = Tools::file_get_contents($url, false, null, 3);
                            if (!$ct)
                            return false;
                            $headers = get_headers($url);
                            if ($headers)
                            {
                                foreach ($headers as $header)
                                {
                                    if (substr_count($header, 'cloudflare') > 0)
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
    public function _buildHtml()
    {
        $ps_version = Tools::substr(_PS_VERSION_, 0, 3);
        $arr = array();
        $htaccess_modified = $this->verify_if_htaccess_is_written();
        if ((int)Configuration::get('ait_force_regenerate') == 0)
        {
            Configuration::updateValue('ait_force_regenerate', 1);
            Tools::generateHtaccess();
            $htaccess_modified = $this->verify_if_htaccess_is_written();
        }
        $arr['has_page_cache_installed'] = false;
        $arr['uit_module_path'] = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'modules/' . $this->name;
        $arr['uit_module_path_short'] = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'module/' . $this->name;
        $arr['uit_domain_url'] = Tools::getHttpHost(true) . __PS_BASE_URI__;
        $arr['uit_token'] = Configuration::get('uit_token');
        $arr['uit_tpl_dir'] = _PS_THEME_DIR_;
        $arr['uit_module_dir'] = _PS_MODULE_DIR_;
        $arr['uit_root_dir'] = _PS_ROOT_DIR_;
        $arr['uit_htaccess'] = $htaccess_modified;
        $arr['uit_quality_theme'] = (int)Configuration::get('uit_quality_theme');
        $arr['uit_quality_module'] = (int)Configuration::get('uit_quality_module');
        $arr['total_saved_space_theme'] = (int)Configuration::get('uit_saved_theme');
        $arr['total_saved_space_module'] = (int)Configuration::get('uit_saved_module');
        $arr['uit_lazy_load'] = Configuration::get('uit_lazy_load');
        $arr['uit_lazy_load_image'] = Configuration::get('uit_lazy_load_image');
        $arr['uit_simple_load'] = Configuration::get('uit_simple_load');
        $arr['uit_mouse_hover'] = Configuration::get('uit_mouse_hover');
        $arr['uit_mouse_hover_thumb'] = Configuration::get('uit_mouse_hover_thumb');
        $arr['uit_mouse_hover_ts'] = Configuration::get('uit_mouse_hover_ts');
        $arr['uit_mouse_hover_ps'] = Configuration::get('uit_mouse_hover_ps');
        $arr['uit_mouse_hover_position'] = Configuration::get('uit_mouse_hover_position');
        $arr['uit_hover_image_type'] = Configuration::get('uit_hover_image_type');
        $arr['uit_shop_enable'] = Configuration::get('PS_SHOP_ENABLE');
        $arr['uit_zoom'] = Configuration::get('uit_zoom');
        $arr['uit_zoom_full_image_size'] = Configuration::get('uit_zoom_full_image_size');
        $arr['uit_zoom_normal_image_size'] = Configuration::get('uit_zoom_normal_image_size');
        $arr['uit_zoom_thumb_image_size'] = Configuration::get('uit_zoom_thumb_image_size');
        $arr['uit_zoom_type'] = Configuration::get('uit_zoom_type');
        $arr['uit_enable_gzip'] = Configuration::get('uit_enable_gzip');
        $arr['uit_compress_os'] = Configuration::get('uit_compress_os');
        $arr['root_folders'] = self::get_subdirectories(_PS_ROOT_DIR_);
        $arr['uit_image_type'] = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'image_type` WHERE products = 1 ORDER BY `id_image_type` ASC');
        if (sizeof(Language::getLanguages(true)) > 1)
        $arr['uit_ajax_url'] = '../index.php?fc=module&module=ultimateimagetool&controller=ajax';
        else
        $arr['uit_ajax_url'] = Context::getContext()
            ->link
            ->getModuleLink($this->name, 'ajax', array() , null, Context::getContext()
            ->cookie
            ->id_lang);
        $arr['uit_ajax_url'] = Context::getContext()
            ->link
            ->getAdminLink($this->name . 'ajax');
        $arr['webp_exists'] = function_exists('imagewebp');
        $arr['imagick_exists'] = extension_loaded('imagick');
        if ((int)Configuration::get('PS_CACHE_ENABLED') > 0)
        $arr['uit_disable_server_cache'] = true;
        else
        $arr['uit_disable_server_cache'] = false;
        if ($arr['imagick_exists'])
        {
            if (class_exists('Imagick'))
            {
                $is_imagick = false;
                $formats = Imagick::queryFormats();
                foreach ($formats as $format)
                {
                    if (Tools::strtoupper($format) == 'WEBP')
                    $is_imagick = true;
                }
                $arr['imagick_exists'] = $is_imagick;
            }
        }
        $sql = 'SELECT  count(id_image) FROM `' . _DB_PREFIX_ . 'image_lang`';
        $results = Db::getInstance()->getRow($sql);
        $arr['uit_total_images'] = (int)$results['count(id_image)'];
        $sql = 'SELECT  count(id_image) FROM `' . _DB_PREFIX_ . 'image_lang` WHERE ( legend = ""  OR legend is NULL )';
        $results = Db::getInstance()->getRow($sql);
        $arr['uit_empty_images'] = (int)$results['count(id_image)'];
        if ($ps_version == '1.5')
        $arr['css_file'] = 'global_15.css';
        else
        $arr['css_file'] = 'global_16.css';
        $arr['display_ssl_error'] = false;
        $arr['allowed_qualities'] = array(
            65,
            70,
            75,
            80,
            81,
            82,
            83,
            84,
            85,
            86,
            87,
            88,
            89,
            90,
            91,
            92,
            93,
            94,
            95,
            96,
            97,
            98,
            99,
            100
        );
        if (Configuration::get('PS_SSL_ENABLED') && !self::check_https())
        $arr['display_ssl_error'] = true;
        $this->set_product_variables();
        $this->set_category_variables();
        $this->set_manufacturers_variables();
        $this->set_suppliers_variables();
        $this->set_sitemap_variables();
        $this->set_alt_tags();
        $this
            ->smarty
            ->assign($arr);
        $this->_html .= $this->display(__FILE__, 'views/templates/admin/content.tpl');
    }
    public static function check_https()
    {
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443)
        {
            return true;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']))
        {
            if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
            return true;
        }
        return false;
    }
}