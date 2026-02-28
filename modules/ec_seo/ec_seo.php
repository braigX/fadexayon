<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;


class Ec_seo extends Module
{
    protected $config_form = false;
    protected $FOLLOWLINK_TIMEOUT = 4;
    protected $FOLLOWLINK_RETRIES = false;
    protected $FOLLOWLINK_LOG = false;
    public $category_rule;
    public $freeToken;
    public $product_rule;
    public $tab_list;
    public $img_extension;
    public $protocol;

    private $container;
    public $ec_token;

    public $GSC_OAUTH2_REVOKE_URI = 'https://oauth2.googleapis.com/revoke';
    public $GSC_OAUTH2_TOKEN_URI = 'https://oauth2.googleapis.com/token';
    public $GSC_OAUTH2_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    public $GSC_API_BASE_PATH = 'https://www.googleapis.com';

    public function __construct()
    {
        $this->name = 'ec_seo';
        $this->tab = 'administration';
        $this->version = '2.0.9';
        $this->author = 'Ether Creation';
        $this->need_instance = 0;
        $this->module_key = 'a98fca6d602e8d7c9c677644cf4a638f';
        $this->bootstrap = true;
        parent::__construct();
        $this->img_extension = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
        $this->displayName = $this->l('SEO');
        $this->description = $this->l('Increase your turnover thanks to the optimization of your natural referencing (SEO). The module gives you a complete inventory of the optimization of your pages (product, categories, CMS, brands, etc.) and above all concrete optimization tracks for each: meta tags, relevance of the content, modification of the tags h1, everything is managed to allow you to (really) perform in SEO!');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->category_rule = array(
            'meta_title' => array('min' => 41, 'max' => 65),
            'meta_description' => array('min' => 101, 'max' => 200),
            'h1' => array('min' => 20, 'max' => 100),
            'link_rewrite' => array('min' => 21, 'max' => 100),
        );

        $this->product_rule = array(
            'meta_title' => array('min' => 46, 'max' => 65),
            'meta_description' => array('min' => 101, 'max' => 200),
            'h1' => array('min' => 20, 'max' => 100),
            'link_rewrite' => array('min' => 21, 'max' => 100),
        );
        $this->tab_list = array(
            'product' => array(
                'trad' => $this->l('Product'),
                'spe' => true,
            ),
            'category' => array(
                'trad' => $this->l('Category'),
                'spe' => true,
            ),
            'cms' => array(
                'trad' => $this->l('CMS'),
                'spe' => false,
            ),
            'supplier' => array(
                'trad' => $this->l('Supplier'),
                'spe' => false,
            ),
            'manufacturer' => array(
                'trad' => $this->l('Manufacturer'),
                'spe' => false,
            )
        );
        $this->freeToken = 'eyJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJhZG1pbkBzbWFydGtleXdvcmQuaW8iLCJqdGkiOiI5MjgifQ.ZaHIpwttGvtISz6k2cylTNV5RsrT_x1PraaCtyRRslfoItTM2jAsvpVuT9HEXk2lzLhB8bEE-RVyW-Q3x8cthBOLH775jB52JFGuQ5IzawOYFIkaeOv6aEqYz8vvllR7QIM7hWuWmG4VA16BCR8LBpGAr2HMC4ghRKL2j2m77fNwag2_KlRgCYNcMMfmSwOOXiKHzXbku31MXiIaBBQ1SIGfPkEnQta7SgKK2VgrxENSScVgtkfs8XdQc5OthMAPvQZkv86hJhwvOj65bFJcIkUdTYBYi8u566IKplAd0Dg5ZAECxYY72P_nogqW68LHPBPaQV3-tY2jns53v0ahXY9Co5PwLxedbQryfDLko3H08fa5VYgfDs_b_JqmkJ3ogQopKO1GxUK5qqHwHhhfQumSh34EkNIqboHb1T4my9hDN2nVlxNJWGhn-_zJuhKV-x2WX6hUua5pROMfIgNphQ_UsZYxu6ddFTLq480qmUucNTPxoGfpMerCQGnI25GoHuoVXzUrbTNg2L0Ojb87rYP5Pt_H-Is8q597aWJX4EW_wZhhiFas0x7URa6fC0iswUYozqBE9Ge5b5f1kTWfuwCU-fjv4WGdibmPRY6_NLxBaHC03J5HCHfKJ9ADD37kxSgPnFHrLJMMbckjH6SHN0KkITLJ1N0xW6V3HmO0ymw';
        $this->protocol = (((Configuration::get('PS_SSL_ENABLED') == 1) && (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') == 1)) ? 'https://' : 'http://' );
        $this->ec_token = Configuration::getGlobalValue('EC_TOKEN_SEO');
    }

    public function checkMeta($obj, $type = null)
    {
        $keyword = $obj->keyword;
        $tab_meta = array();
        $tab_lang = array();
        $tab_keyword = array();
        $id_shop = (int)$this->context->shop->id;
        $languages = Language::getLanguages(false);
        $tab_info_api = array();
        $infoKeywordData = array();
        foreach ($languages as $lang) {
            $tab_lang[] = $lang['id_lang'];
            $keyword_lang = explode(' ', $keyword[$lang['id_lang']]);
            foreach ($keyword_lang as &$mot_k) {
                $mot_k = Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot_k));
            }
            $keyword[$lang['id_lang']] = $keyword_lang;
            $info_api = Db::getInstance()->getValue('SELECT info FROM '._DB_PREFIX_.'ec_seo_smartkeyword WHERE id = '.(int)$obj->id.' AND page = "'.pSQL(Tools::strtolower(get_class($obj))).'" AND id_lang = '.(int)$lang['id_lang'].' AND id_shop = '.(int)$id_shop);
            if (!$info_api || !isset($info_api['data'])) {
                $info_api = array();
                $info_api['data']['semantic']['keywordsDetails'] = array();
                $info_api['data']['tagH1']['serpDetails'] = array();
                $info_api['data']['tagTitle']['serpDetails'] = array();
                $info_api['data']['tagMetaDescription']['serpDetails'] = array();
                $info_api['data']['additionalKeywords']['relatedKeywords'] = array();
                $info_api['data']['additionalKeywords']['relatedKeywords'] = array();
                $info_api['data']['additionalKeywords']['peopleAlsoAsk'] = array();
            } else {
                $info_api = json_decode($info_api, true);
            }
            $h1s = array();
            $list_erreur = array('gone', 'denied', '410', 'just a moment', 'javascript', 'captcha', 'trop de', 'oups', 'forbidden');
            foreach ($info_api['data']['tagH1']['serpDetails'] as $tag_h1) {
                if (!isset($tag_h1['h1s'][0])) {
                    continue;
                }
                $h1 = $tag_h1['h1s'][0];
                if (preg_match('/'.implode('|', $list_erreur).'/', Tools::strtolower($h1)) || Tools::strlen($h1) == 0) {
                    continue;
                }
                $h1s[] = array(
                    'url' => $tag_h1['url'],
                    'h1' => $h1
                );
            }
            $titles = array();
            foreach ($info_api['data']['tagTitle']['serpDetails'] as $tag_title) {
                if (!isset($tag_title['titles'][0])) {
                    continue;
                }
                $title = $tag_title['titles'][0];
                if (preg_match('/'.implode('|', $list_erreur).'/', Tools::strtolower($title)) || Tools::strlen($title) == 0) {
                    continue;
                }
                $titles[] = array(
                    'url' => $tag_title['url'],
                    'title' => $title
                );
            }
            $descriptions = array();
            foreach ($info_api['data']['tagMetaDescription']['serpDetails'] as $tag_description) {
                if (!isset($tag_description['metaDescriptions'][0])) {
                    continue;
                }
                $description = $tag_description['metaDescriptions'][0];
                if (preg_match('/'.implode('|', $list_erreur).'/', Tools::strtolower($description)) || Tools::strlen($description) == 0) {
                    continue;
                }
                $descriptions[] = array(
                    'url' => $tag_description['url'],
                    'title' => $title
                );
            }
            $infoKeywordData['keywordsDetails'] = $info_api['data']['semantic']['keywordsDetails'];
            $infoKeywordData['relatedKeywords'] = $info_api['data']['additionalKeywords']['relatedKeywords'];
            $infoKeywordData['peopleAlsoAsk'] = $info_api['data']['additionalKeywords']['peopleAlsoAsk'];
            $infoKeywordData['h1s'] = $h1s;
            $infoKeywordData['titles'] = $titles;
            $infoKeywordData['descriptions'] = $descriptions;
            $tab_info_api[$lang['id_lang']] = $infoKeywordData;
        }
        $this->smarty->assign(
            array(
                'is17' => version_compare(_PS_VERSION_, '1.7', '>='),
                'infoKeywordData' => $tab_info_api
            )
        );
        $h1 = '';
        $first = true;
        
        foreach ($obj->h1 as $id_lang => $value) {
            if (!isset($keyword[$id_lang])) {
                continue;
            }
            $mots_h1 = explode(' ', $obj->h1[$id_lang]);
            foreach ($mots_h1 as &$mot_h1) {
                $mot_h1 = str_replace('.', '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot_h1)));
            }
            $mot_needed = array();
            $cpt = 0;
            foreach ($keyword[$id_lang] as $mot) {
                if (in_array($mot, $mots_h1)) {
                    $cpt++;
                } else {
                    $mot_needed[] = '"'.$mot.'"';
                }
            }
            if ($cpt == count($keyword[$id_lang])) {
                $tab_keyword[$id_lang]['h1']['keyword']['res'] = 1;
            } else {
                $tab_keyword[$id_lang]['h1']['keyword']['res'] = 2;
            }
            $tab_keyword[$id_lang]['h1']['keyword']['mot_needed'] = implode(', ', $mot_needed);
        }

        foreach ($obj->meta_title as $id_lang => $value) {
            if (!isset($keyword[$id_lang])) {
                continue;
            }
            $mots_meta_title = explode(' ', $obj->meta_title[$id_lang]);
            foreach ($mots_meta_title as &$mot_meta_title) {
                $mot_meta_title = str_replace('.', '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot_meta_title)));
            }
            $mot_needed = array();
            $cpt = 0;
            foreach ($keyword[$id_lang] as $mot) {
                if (in_array($mot, $mots_meta_title)) {
                    $cpt++;
                } else {
                    $mot_needed[] = '"'.$mot.'"';
                }
            }
            if ($cpt == count($keyword[$id_lang])) {
                $tab_keyword[$id_lang]['meta_title']['keyword']['res'] = 1;
            } else {
                $tab_keyword[$id_lang]['meta_title']['keyword']['res'] = 2;
            }
            $tab_keyword[$id_lang]['meta_title']['keyword']['mot_needed'] = implode(', ', $mot_needed);
        }
        
        foreach ($obj->meta_description as $id_lang => $value) {
            if (!isset($keyword[$id_lang])) {
                continue;
            }
            $mots_meta_description = explode(' ', $obj->meta_description[$id_lang]);
            foreach ($mots_meta_description as &$mot_meta_description) {
                $mot_meta_description = str_replace('.', '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot_meta_description)));
            }
            $mot_needed = array();
            $cpt = 0;
            foreach ($keyword[$id_lang] as $mot) {
                if (in_array($mot, $mots_meta_description)) {
                    $cpt++;
                } else {
                    $mot_needed[] = '"'.$mot.'"';
                }
            }
            if ($cpt == count($keyword[$id_lang])) {
                $tab_keyword[$id_lang]['meta_description']['keyword']['res'] = 1;
            } else {
                $tab_keyword[$id_lang]['meta_description']['keyword']['res'] = 2;
            }
            $tab_keyword[$id_lang]['meta_description']['keyword']['mot_needed'] = implode(', ', $mot_needed);
        }
        if (get_class($obj) != 'Manufacturer' && get_class($obj) != 'Supplier') {
            foreach ($obj->link_rewrite as $id_lang => $value) {
                if (!isset($keyword[$id_lang])) {
                    continue;
                }
               
                $mots_name = explode('-', $obj->link_rewrite[$id_lang]);
                $cpt = 0;
                $mot_needed = array();
                foreach ($keyword[$id_lang] as $mot) {
                    $mot = Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot));
                    if (in_array($mot, $mots_name)) {
                        $cpt++;
                    } else {
                        $mot_needed[] = '"'.$mot.'"';
                    }
                }
                if ($cpt == count($keyword[$id_lang])) {
                    $tab_keyword[$id_lang]['link_rewrite']['keyword']['res'] = 1;
                } else {
                    $tab_keyword[$id_lang]['link_rewrite']['keyword']['res'] = 2;
                }
                $tab_keyword[$id_lang]['link_rewrite']['keyword']['mot_needed'] = implode(', ', $mot_needed);
            }
        }
        $h2 = 'h2';
        $oh = '<';
        $ch = '>';
        $verifh2 = $oh.$h2.$ch;
        foreach ($obj->description as $id_lang => $value) {
            $cpt = 0;
            if (!isset($keyword[$id_lang])) {
                continue;
            }
            if (!in_array($id_lang, $tab_lang)) {
                continue;
            }
            $score_desc = 0;
            $desc_total = $obj->description[$id_lang];
            if (preg_match('/'.$verifh2.'/', $desc_total)) {
                $score_desc += 10;
                $tab_keyword[$id_lang]['desc_total']['h2'] = 1;
            } else {
                $tab_keyword[$id_lang]['desc_total']['h2'] = 0;
            }
            $desc_total = strip_tags($desc_total);
            $search = array("\n", "\r", "\r\n", "\n\r", "\t");
            $desc_total = str_replace($search, " ", $desc_total);
            $desc_total = iconv('UTF-8', 'ASCII//IGNORE', $desc_total);
            preg_match_all('/\w+/', $desc_total, $matches);
            $mots100 = array_slice($matches[0], 0, 100);
            foreach ($mots100 as &$mot100) {
                $mot100 = Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot100));
            }
        
            $count_word = count($matches[0]);
            $tab_keyword[$id_lang]['desc_total']['count_word'] = $count_word;
            
            if ($count_word > 150 && $count_word < 300) {
                $score_desc += 50;
            }
            if ($count_word > 300) {
                $score_desc += 100;
            }
            $mot_needed = array();
            foreach ($keyword[$id_lang] as $mot) {
                $mot = Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot));
                if (in_array($mot, $mots100)) {
                    $cpt++;
                } else {
                    $mot_needed[] = '"'.$mot.'"';
                }
            }
            
         
            if ($cpt == count($keyword[$id_lang])) {
                $tab_keyword[$id_lang]['desc_total']['keyword']['res'] = 1;
                $score_desc += 10;
            } else {
                $tab_keyword[$id_lang]['desc_total']['keyword']['res'] = 2;
            }
            $tab_keyword[$id_lang]['desc_total']['keyword']['mot_needed'] = implode(', ', $mot_needed);
            $tab_keyword[$id_lang]['score'] = $score_desc;
        }
        $this->smarty->assign(array(
            'cat' => $obj,
            'category_rule' => $this->category_rule,
            'tab_lang' => $tab_lang,
            'field' => 'h1',
            'tab_keyword' => $tab_keyword,
            'ec_keyword' => $obj->keyword
        ));
        $h1 = $this->display(__FILE__, 'views/templates/admin/checkMeta.tpl');
       
        $tab_meta['h1'] = $h1;
        $first = true;
        $name = $this->l('(20-100 characters recommended)');

        $this->smarty->assign(array(
            'field' => 'meta_title',
        ));
        $meta_title = $this->display(__FILE__, 'views/templates/admin/checkMeta.tpl');
        $tab_meta['meta_title'] = $meta_title;
        $this->smarty->assign(array(
            'field' => 'meta_description',
        ));
        $meta_description = $this->display(__FILE__, 'views/templates/admin/checkMeta.tpl');
        $tab_meta['meta_description'] = $meta_description;
        if (get_class($obj) != 'Manufacturer' && get_class($obj) != 'Supplier') {
            $this->smarty->assign(array(
                'field' => 'link_rewrite',
            ));
            $link_rewrite = $this->display(__FILE__, 'views/templates/admin/checkMeta.tpl');
            $tab_meta['link_rewrite'] = $link_rewrite;
        }

        $this->smarty->assign(array(
            'field' => 'desc_total',
        ));
        $desc_total = $this->display(__FILE__, 'views/templates/admin/checkMeta.tpl');
        $tab_meta['desc_total'] = $desc_total;

        return $tab_meta;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('EC_SEO_REPORT_ERRORS_ONLY', 1);
        Configuration::updateValue('EC_SEO_MI_PAGINATION', 20);
        Configuration::updateValue('EC_SEO_META_ACTIVE_PROD', 1);
        Configuration::updateValue('EC_SEO_MI_ACTIVE_PROD', 1);
        Configuration::updateValue('EC_SEO_ALT_ACTIVE_PROD', 1);
        Configuration::updateValue('EC_SEO_REPORT_ACTIVE_PROD', 1);
        Configuration::updateValue('EC_SEO_BACKUP', 1);
        Configuration::updateValue('EC_SEO_FRONT', 1);
        Configuration::updateValue('EC_SEO_H1', 1);
        Configuration::updateGlobalValue('EC_SEO_HTACCESS', 0);
        Configuration::updateValue('EC_SEO_FRONT_IP', $_SERVER['REMOTE_ADDR']);
        Configuration::updateGlobalValue('EC_SEO_ONGLET_'.$this->context->employee->id, 'dashboard');
        //Redirection Image
        Configuration::updateGlobalValue('EC_TOKEN_SEO', md5(time()._COOKIE_KEY_));
        Configuration::updateValue('EC_SEO_REDIRECTIMAGE_HTACCESS', true);
        Db::getInstance()->execute(
            '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_redirectimage` (
             `id` INT AUTO_INCREMENT,
             `url` TEXT,
             `cpt` INT,
             `img_redirect` TEXT,
             `default` BOOL,
             PRIMARY KEY (`id`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        $path = _PS_ROOT_DIR_.'/.htaccess';
        $content = Tools::file_get_contents($path);
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $ec_seo_controller_uri = $this->protocol . Tools::getShopDomain() . '/module/ec_seo/';
        } else {
            $ec_seo_controller_uri = $this->protocol . Tools::getShopDomain() . __PS_BASE_URI__ . 'index.php&fc=module&module=' . $this->name.'&controller=';
        }
        $content2 = "\n\n#START EC_REDIRECT\nRewriteEngine on\nRewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f\nRewriteRule \.(gif|jpe?g|png|bmp) ".$ec_seo_controller_uri."ajax?majsel=40&ajax=1&tok=".Configuration::getGlobalValue('EC_TOKEN_SEO')."&url=%{REQUEST_URI} [NC,L]\n#END EC_REDIRECT\n";
        $content = str_replace("# Dispatcher", $content2."\n\n# Dispatcher", $content);
        file_put_contents($path, $content);
        
        $this->registerHook('actionHtaccessCreate');
        //END Redirection Image
        $structure = _PS_ROOT_DIR_."/override/controllers/front/listing";
        if (!file_exists($structure)) {
            if (!mkdir($structure, 0777, true)) {
                return false;
            }
        }
        //INIT OPEN GRAPH
        $languages = Language::getLanguages(false);
        Configuration::updateValue('EC_SEO_OG', 0);
        Configuration::updateValue('EC_SEO_OG_TITLE_DEFAULT', 1);
        Configuration::updateValue('EC_SEO_OG_DESCRPTION_DEFAULT', 1);

        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            Configuration::updateValue('EC_SEO_OG_SITE_NAME_'.$id_lang, Configuration::get('PS_SHOP_NAME'));
            Configuration::updateValue('EC_SEO_OG_LOCALE_'.$id_lang, strtoupper($lang['iso_code']));
        }
        //END OPEN GRAPH

        //TABS
        $this->installModuleTab('AdminEcSeoCategory', 'Seo Category', '9', false);
        $this->installModuleTab('AdminEcSeoProduct', 'Seo Product', '9', false);
        $this->installModuleTab('AdminEcSeoCms', 'Seo CMS', '9', false);
        $this->installModuleTab('AdminEcSeoManufacturer', 'Seo Manufacturer', '9', false);
        $this->installModuleTab('AdminEcSeoSupplier', 'Seo Supplier', '9', false);
        $this->installModuleTab('AdminEcSeoMeta', 'Seo Meta', '9', false);
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $id_parent = Tab::getIdFromClassName('IMPROVE');
        } else {
            $id_parent = 0;
        }
        $this->installModuleTab('AdminEcSeo', 'SEO', $id_parent, true, 'find_replace');
        //END TABS
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo` (
                 `id` INT AUTO_INCREMENT,
                 `type` VARCHAR(255) NOT NULL,
                 `idP` INT,
                 `idL` INT,
                 `lienS` VARCHAR(255) NOT NULL,
                 `onlineS` INT NOT NULL,
                 `nameS` VARCHAR(255) NOT NULL,
                 `typeRed` VARCHAR(255) NOT NULL,
                 `activRed` INT NOT NULL,
                 `idshop` INT NOT NULL,
                 PRIMARY KEY (`id`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_conf');
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_conf` (
                 `name` VARCHAR(255) NOT NULL,
                 `value` VARCHAR(255) NOT NULL,
                 PRIMARY KEY (`name`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_redirect` (
                 `id` INT AUTO_INCREMENT,
                 `old_link` VARCHAR(255) NOT NULL,
                 `lienS` VARCHAR(255) NOT NULL,
                 `typeRed` VARCHAR(255) NOT NULL,
                 `onlineS` INT,
                 `id_shop` INT,
                 PRIMARY KEY (`id`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        Db::getInstance()->execute(
            '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_meta` (
             `id_seo_meta` INT AUTO_INCREMENT, 
             `id_meta` INT,
             `id_shop` INT,
             PRIMARY KEY (`id_seo_meta`, `id_shop`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        Db::getInstance()->execute(
            '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_meta_lang` (
             `id_seo_meta` INT,
             `h1` VARCHAR(200),
             `keyword` TEXT,
             `description2` TEXT,
             `og_title` TEXT,
             `og_type` TEXT,
             `og_description` TEXT,
             `og_image` TEXT,
             `link_canonic` VARCHAR(128),
             `id_lang` INT,
             PRIMARY KEY (`id_seo_meta`, `id_lang`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        Db::getInstance()->execute(
            '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_meta_temp` (
             `id` INT , 
             `page` VARCHAR(20),
             `id_lang` INT , 
             `id_shop` INT , 
             PRIMARY KEY (`id`, `page`, `id_lang`, `id_shop`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        Db::getInstance()->execute(
            '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_mi_temp` (
             `id` INT , 
             `page` VARCHAR(20),
             `nb_replace` INT,
             `id_lang` INT , 
             `id_shop` INT , 
             PRIMARY KEY (`id`, `page`, `id_lang`, `id_shop`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        //Balise ALT
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_bag_gen');
        Db::getInstance()->execute(
            '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_bag_gen` (
             `balise_alt` varchar(128),
             `id_lang` INT,
             `id_shop` INT,
             PRIMARY KEY (`id_lang`, `id_shop`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        Db::getInstance()->execute(
            '
             CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_ba_temp` (
             `id_product` INT ,
             `id_image` INT,
             `id_lang` INT , 
             `id_shop` INT , 
             PRIMARY KEY (`id_product`, `id_image`, `id_lang`, `id_shop`)
             ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        

        $list = array('product' => true, 'category' => true, 'cms' => false, 'supplier' => false, 'manufacturer' => false);
        foreach ($list as $class => $spe) {
            //Redirection
            Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo_conf VALUES("'.pSQL($class).'_disabled_behav","homepage")');
            Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo_conf VALUES("'.pSQL($class).'_deleted_behav","homepage")');
            Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo_conf VALUES("'.pSQL($class).'_disabled_link","")');
            Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo_conf VALUES("'.pSQL($class).'_deleted_link","")');
            //END Redirection

            //SEO
            Db::getInstance()->execute(
                '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_'.$class.'` (
                 `id_seo_'.$class.'` INT AUTO_INCREMENT, 
                 `id_'.$class.'` INT,
                 `id_shop` INT,
                 PRIMARY KEY (`id_seo_'.$class.'`, `id_shop`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
            );
    
            Db::getInstance()->execute(
                '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_'.$class.'_lang` (
                 `id_seo_'.$class.'` INT,
                 `h1` VARCHAR(200),
                 `keyword` TEXT,
                 `description2` TEXT,
                 `og_title` TEXT,
                 `og_type` TEXT,
                 `og_description` TEXT,
                 `og_image` TEXT,
                 `link_canonic` VARCHAR(128),
                 `id_lang` INT,
                 PRIMARY KEY (`id_seo_'.$class.'`, `id_lang`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
            );
            //END SEO

            Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'_mg_gen');
            Db::getInstance()->execute(
                '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_'.$class.'_mg_gen` (
                 `meta_title` varchar(128),
                 `meta_description` varchar(512),
                 `id_lang` INT,
                 `id_shop` INT,
                 PRIMARY KEY (`id_lang`, `id_shop`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
            );
            
            //Maillage Interne
            Db::getInstance()->execute(
                '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_'.$class.'_mi` (
                 `id` INT AUTO_INCREMENT,
                 `id_shop` INT,
                 PRIMARY KEY (`id`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
            );
    
            Db::getInstance()->execute(
                '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_'.$class.'_mi_lang` (
                 `id_seo_'.$class.'_mi` INT,
                 `keyword` varchar(128),
                 `url` TEXT,
                 `id_lang` INT,
                 PRIMARY KEY (`id_seo_'.$class.'_mi`, `id_lang`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
            );
            // **Maillage Interne**
            if ($spe) {
                Db::getInstance()->execute(
                    '
                     CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_'.$class.'_mg` (
                     `id` INT AUTO_INCREMENT,
                     `id_shop` INT,
                     PRIMARY KEY (`id`)
                     ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
                );
        
                Db::getInstance()->execute(
                    '
                     CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_'.$class.'_mg_lang` (
                     `id_seo_'.$class.'_mg` INT,
                     `meta_title` varchar(128),
                     `meta_description` varchar(512),
                     `id_lang` INT,
                     PRIMARY KEY (`id_seo_'.$class.'_mg`, `id_lang`)
                     ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
                );
        
                Db::getInstance()->execute(
                    '
                     CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_'.$class.'_mg_cat` (
                     `id_seo_'.$class.'_mg` INT,
                     `id_category` INT,
                     PRIMARY KEY (`id_seo_'.$class.'_mg`, `id_category`)
                     ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
                );

                Db::getInstance()->execute(
                    '
                     CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_'.$class.'_mi_cat` (
                     `id_seo_'.$class.'_mi` INT,
                     `id_category` INT,
                     PRIMARY KEY (`id_seo_'.$class.'_mi`, `id_category`)
                     ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
                );
            }
        }
        //Footer SEO
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer` (
                 `id` INT AUTO_INCREMENT,
                 `type` VARCHAR(20),
                 `spe` TINYINT(1),
                 `active` TINYINT(1),
                 `id_shop` INT,
                 PRIMARY KEY (`id`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_lang` (
                 `id_footer` INT AUTO_INCREMENT,
                 `id_lang` INT,
                 `title` VARCHAR(50),
                 `description` TEXT,
                 PRIMARY KEY (`id_footer`, `id_lang`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_block` (
                 `id` INT AUTO_INCREMENT,
                 `id_footer` INT,
                 `active` TINYINT(1),
                 `position` INT,
                 PRIMARY KEY (`id`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_block_lang` (
                 `id_block` INT,
                 `id_lang` INT,
                 `title` VARCHAR(50),
                 PRIMARY KEY (`id_block`, `id_lang`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_link` (
                 `id` INT AUTO_INCREMENT,
                 `id_block` INT,
                 `position` INT,
                 PRIMARY KEY (`id`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_link_lang` (
                 `id_link` INT,
                 `id_lang` INT,
                 `title` VARCHAR(50),
                 `link` VARCHAR(200),
                 PRIMARY KEY (`id_link`, `id_lang`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_category` (
                 `id_footer` INT,
                 `id_category` INT,
                 PRIMARY KEY (`id_footer`, `id_category`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_product` (
                 `id_footer` INT,
                 `id_product` INT,
                 PRIMARY KEY (`id_footer`, `id_product`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_cms` (
                 `id_footer` INT,
                 `id_cms` INT,
                 PRIMARY KEY (`id_footer`, `id_cms`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_supplier` (
                 `id_footer` INT,
                 `id_supplier` INT,
                 PRIMARY KEY (`id_footer`, `id_supplier`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_footer_manufacturer` (
                 `id_footer` INT,
                 `id_manufacturer` INT,
                 PRIMARY KEY (`id_footer`, `id_manufacturer`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        //FIN Footer SEO

        //Block HTML
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_block_html` (
                 `id_block_html` INT AUTO_INCREMENT,
                 `id_hook` INT,
                 `active` TINYINT(1),
                 `id_shop` INT,
                 PRIMARY KEY (`id_block_html`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_block_html_lang` (
                 `id_block_html` INT,
                 `content` TEXT,
                 `id_lang` INT,
                 PRIMARY KEY (`id_block_html`, `id_lang`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        //FIN Block HTML

        //Page not indexed
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_page_noindex` (
                 `id` INT AUTO_INCREMENT,
                 `page` varchar(200),
                 PRIMARY KEY (`id`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        //Fin page not indexed

        //SmartKeyword API
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_smartkeyword` (
                 `id` INT,
                 `id_lang` INT,
                 `id_shop` INT,
                 `keyword` VARCHAR(100),
                 `page` VARCHAR(50),
                 `info` TEXT,
                 `date_upd` DATETIME,
                 PRIMARY KEY (`id`,`id_lang`,`id_shop`,`page`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
        //FIN SmartKeyword API

        $shops = Shop::getShops(true);
        foreach ($shops as $shop) {
            $id_shop = $shop['id_shop'];
            foreach ($list as $class => $spe) {
                Db::getinstance()->insert(
                    'ec_seo_footer',
                    array(
                        'type' => pSQL($class),
                        'spe' => 0,
                        'active' => 0,
                        'id_shop' => (int)$id_shop,
                    )
                );
                $id_footer = Db::getInstance()->Insert_ID();
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_footer_lang',
                        array(
                            'id_footer' => (int)$id_footer,
                            'id_lang' => (int)$id_lang,
                            'title' => '',
                            'description' => '',
                        )
                    );
                }
            }
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                foreach ($list as $class => $spe) {
                    Db::getinstance()->insert(
                        'ec_seo_'.$class.'_mg_gen',
                        array(
                            'meta_title' => '',
                            'meta_description' => '',
                            'id_lang' => (int)$id_lang,
                            'id_shop' => (int)$id_shop
                        )
                    );
                }
                Db::getinstance()->insert(
                    'ec_seo_bag_gen',
                    array(
                        'balise_alt' => '',
                        'id_lang' => (int)$id_lang,
                        'id_shop' => (int)$id_shop
                    )
                );
            }
        }
        
        
        
        Configuration::UpdateValue('EC_SEO_PAGINATION', '50');
        
        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_report` (
                 `type_meta` VARCHAR(255),
                 `page` VARCHAR(255),
                 `missing` INT,
                 `duplicate` INT,
                 `too_short` INT,
                 `too_long` INT,
                 `total` INT,
                 `date` DATETIME,
                 `id_shop` INT,
                 PRIMARY KEY (`type_meta`, `page`, `id_shop`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_score` (
                 `id` INT,
                 `page` VARCHAR(255),
                 `score` INT,
                 `id_shop` INT,
                 PRIMARY KEY (`id`, `page`, `id_shop`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );

        Db::getInstance()->execute(
            '
                 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_seo_score_lang` (
                 `id` INT,
                 `page` VARCHAR(255),
                 `score` INT,
                 `id_lang` INT,
                 `id_shop` INT,
                 PRIMARY KEY (`id`, `page`, `id_lang`, `id_shop`)
                 ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
        );
      
        $tab = array('Product','Category','Supplier','Manufacturer','Cms');
        foreach ($tab as $tab_obj) {
            $this->listO($tab_obj, $this->context->shop->id);
        }

        

        return parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('EcSeoDescription2') &&
            $this->registerHook('actionProductDelete') &&
            $this->registerHook('displayContentWrapperBottom') &&
            $this->registerHook('actionProductSave') &&
            $this->registerHook('actionCategoryUpdate') &&
            $this->registerHook('actionCategoryDelete') &&
            $this->registerHook('actionObjectSupplierDeleteAfter') &&
            $this->registerHook('actionObjectSupplierUpdateAfter') &&
            $this->registerHook('actionObjectManufacturerDeleteAfter') &&
            $this->registerHook('actionObjectManufacturerUpdateAfter') &&
            $this->registerHook('actionObjectCmsUpdateAfter') &&
            $this->registerHook('actionObjectCmsDeleteAfter') &&
            $this->registerHook('actionAdminProductsListingResultsModifier') &&
            $this->registerHook('actionAdminProductsListingFieldsModifier') &&
            $this->registerHook('actionAdminCategoriesListingFieldsModifier') &&
            $this->registerHook('actionAdminCategoriesListingResultsModifier') &&
            $this->registerHook('actionCategoryGridQueryBuilderModifier') &&
            $this->registerHook('actionCategoryGridDefinitionModifier') &&
            $this->registerHook('actionCategoryGridDataModifier') &&
            $this->registerHook('displayEcSeoCustomBlock') &&
            $this->registerHook('displaySeoScore') &&
            $this->registerHook('displaySeoFilter') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('actionProductGridQueryBuilderModifier') &&
            $this->registerHook('actionProductGridDataModifier') &&
            $this->registerHook('actionProductGridDefinitionModifier') &&
            $this->registerHook('ActionAdminControllerSetMedia');
    }

    private function installModuleTab($tab_class, $tab_name, $id_tab_parent, $active = true, $icon = null)
    {
        $tab = new Tab();
        $langs = Language::getLanguages();
        foreach ($langs as $l) {
            $tab->name[$l['id_lang']] = $tab_name;
        }
        $tab->active = $active;
        $tab->class_name = $tab_class;
        $tab->module = $this->name;
        //Admin>Preferences : 16
        $tab->id_parent = $id_tab_parent;
        if ($icon != null) {
            $tab->icon = $icon;
        }
        if (!$tab->save()) {
            return false;
        }

        return true;
    }

    public function uninstallTabs()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }

    public function uninstall()
    {
        $list = array('product' => true, 'category' => true, 'cms' => false, 'supplier' => false, 'manufacturer' => false);
        foreach ($list as $class => $spe) {
            //SEO
            Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'');
            Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'_lang');
            //END SEO
            Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'_mg_gen');
            //Maillage Interne
            Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'_mi');
            Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'_mi_lang');
            // **Maillage Interne**
            if ($spe) {
                Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'_mg');
                Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'_mg_lang');
                Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'_mg_cat');
                Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_'.$class.'_mi_cat');
            }
        }
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_meta');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_meta_lang');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_mi_temp');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_meta_temp');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_bag_gen');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_ba_temp');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_report');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_score');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_lang');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_block');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_block_lang');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_link');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_link_lang');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_category');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_product');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_cms');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_supplier');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'ec_seo_footer_manufacturer');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'ec_seo_smartkeyword`');
        $this->uninstallTabs();
        //Redirection Image
        Configuration::updateValue('EC_SEO_REDIRECTIMAGE_HTACCESS', false);
        Db::getInstance()->Execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."ec_seo_redirectimage`");
        $this->removeOldHtAccessRules();
        //END Redirection Image
        return parent::uninstall();
    }

    public function setLineColor(&$sheet, $line, $score)
    {
        $color = 'e55252';
        if ($score >= 75) {
            $color = 'b1e59b';
        } else if ($score >= 50) {
            $color= 'f8f85a';
        } else if ($score >= 25) {
            $color = 'f9a267';
        }
        $sheet->getStyle('A'.$line.':L'.$line)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->checkLangForm();
        //RedirectionImage
        if (((bool)Tools::isSubmit('submitEc_seo_redirectimageModule')) == true) {
            return $this->postProcessRI();
        }
        if (((bool)Tools::isSubmit('addec_seo_redirectimage')) == true) {
            return $this->renderForm2();
        }
        if (((bool)Tools::isSubmit('updateec_seo_redirectimage')) == true) {
            return $this->renderForm2(Tools::getValue('id_ec_seo_redirectimage'));
        }
        if (((bool)Tools::isSubmit('submitAddSeoImage')) == true) {
            return $this->processAdd();
        }
        if (((bool)Tools::isSubmit('submitEditSeoImage')) == true) {
            return $this->processEdit(Tools::getValue('id'));
        }
        //END Redirection Image
        $allValues = Tools::getAllValues();
        $meta_submit = $this->checkSubmit($allValues);
        if ($meta_submit) {
            return $meta_submit;
        }
        $active = Tools::getValue('oactive');
        $messconf = true;
        if (Tools::strlen($active) == 0) {
            $active = Configuration::getGlobalValue('EC_SEO_ONGLET_'.$this->context->employee->id);
            if (Tools::strlen($active) == 0) {
                $active = 'dashboard';
            }
            $messconf = false;
        }


        return $this->showConfig($active, !$messconf);
    }

    public function genMiClean($content, $keyword, $replace, $max)
    {
        $tab_info = array();
        $in_balise = false;
        $temp = false;
        $count = 0;
        $count1 = 0;
        $count2 = 0;
        $count3 = 0;
        $count4 = 0;
        $total_replace = 0;
        for ($i = 0; $i < strlen($content); $i++) {
            $char = $content[$i];
            if ($char == '<') {
                if ($temp != false) {
                    $temp = ' '.$temp;
                    $temp = preg_replace('/ '.$keyword.' /', ' '.$replace.' ', $temp, $max, $count);
                    $max -= $count;
                    $temp = preg_replace('/ '.$keyword.'\./', ' '.$replace.'.', $temp, $max, $count1);
                    $max -= $count1;
                    $temp = preg_replace('/ '.$keyword.',/', ' '.$replace.',', $temp, $max, $count2);
                    $max -= $count2;
                    $temp = preg_replace('/ '.$keyword.':/', ' '.$replace.':', $temp, $max, $count3);
                    $max -= $count3;
                    $temp = preg_replace('/ '.$keyword.'!/', ' '.$replace.'!', $temp, $max, $count4);
                    $temp = substr($temp, 1);
                    $max -= $count4;
                    $total_replace += ($count+$count1+$count2+$count3+$count4);
                    $tab_info[] = $temp;
                    $temp = '';
                }
                $in_balise = true;
            }
            if ($char == '>') {
                $temp.=$char;
                $tab_info[] = $temp;
                $in_balise = false;
                $temp = '';
                continue;
            }
            $temp.=$char;
        }
        if (count($tab_info) == 0) {
            $temp = ' '.$temp;
            $temp = preg_replace('/ '.$keyword.' /', ' '.$replace.' ', $temp, $max, $count);
            $max -= $count;
            $temp = preg_replace('/ '.$keyword.'\./', ' '.$replace.'.', $temp, $max, $count1);
            $max -= $count1;
            $temp = preg_replace('/ '.$keyword.',/', ' '.$replace.',', $temp, $max, $count2);
            $max -= $count2;
            $temp = preg_replace('/ '.$keyword.':/', ' '.$replace.':', $temp, $max, $count3);
            $max -= $count3;
            $temp = preg_replace('/ '.$keyword.'!/', ' '.$replace.'!', $temp, $max, $count4);
            $temp = substr($temp, 1, strlen($temp));
            $max -= $count4;
            $total_replace += ($count+$count1+$count2+$count3+$count4);
            $tab_info[] = $temp;
            $temp = '';
        }
        return array('content' => implode('', $tab_info), 'total_replace' => $total_replace);
    }

    public function getMiTableTask($type, $id_shop, $refresh = false, $search = null, $page = null)
    {
        if ($page == null) {
            $page = 1;
        }
        $context = Context::getContext();
        $pagination = Configuration::get('EC_SEO_MI_'.$type.'PAGINATION');
        if (($pagination > 0) == false) {
            Configuration::updateValue('EC_SEO_MI_'.$type.'PAGINATION', 20);
            $pagination = 20;
        }
        $p1 = ($page*$pagination)-$pagination;
        $p2 = $pagination;
        
        if ($type == 'cms') {
            $req = 'SELECT id, meta_title as name, nb_replace, esmt.id_lang FROM '._DB_PREFIX_.'ec_seo_mi_temp esmt
            LEFT JOIN '._DB_PREFIX_.$type.'_lang l ON (l.id_'.$type.' = esmt.id)
            WHERE page = "'.pSQL($type).'" AND esmt.id_shop = '.(int)$id_shop.' AND l.id_shop = '.(int)$id_shop.' AND l.id_lang = esmt.id_lang';
        } else if ($type == 'manufacturer' || $type == 'supplier') {
            $req = 'SELECT id, name, nb_replace, esmt.id_lang FROM '._DB_PREFIX_.'ec_seo_mi_temp esmt
            LEFT JOIN '._DB_PREFIX_.$type.' sp ON (sp.id_'.$type.' = esmt.id)
            LEFT JOIN '._DB_PREFIX_.$type.'_shop a ON (a.id_'.$type.' = esmt.id)
            WHERE page = "'.pSQL($type).'" AND esmt.id_shop = '.(int)$id_shop.' AND a.id_shop = '.(int)$id_shop;
        } else {
            $req = 'SELECT id, name, nb_replace, esmt.id_lang FROM '._DB_PREFIX_.'ec_seo_mi_temp esmt
            LEFT JOIN '._DB_PREFIX_.$type.'_lang l ON (l.id_'.$type.' = esmt.id)
            WHERE page = "'.pSQL($type).'" AND esmt.id_shop = '.(int)$id_shop.' AND l.id_shop = '.(int)$id_shop.' AND l.id_lang = esmt.id_lang';
        }
        
        if ($search != null) {
            parse_str($search, $searchs);
            if (!empty($searchs['name'])) {
                $req .= ' AND name like "%'.pSQL($searchs['name']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['id'])) {
                $req .= ' AND id like "%'.(int)$searchs['id'].'%"';
                $rsearch = true;
            }
        } else {
            $searchs= array();
        }
        $countInfo = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.$p1.','.$p2;
        $infos = Db::getInstance()->executes($req);
        $nbpage = ($countInfo/$pagination);
        if (is_float($nbpage)) {
            $nbpage = (int)$nbpage + 1;
        }
        foreach ($infos as &$info) {
            $id = $info['id'];
            $id_lang  = $info['id_lang'];
            $info['lang'] = Language::getIsoById($id_lang);
            $class = Tools::ucfirst($type);
            if ($type == 'cms') {
                $class = 'CMS';
            }
            if ($type == 'product') {
                $obj = new $class($id, false, null, $id_shop);
            } else {
                $obj = new $class($id, null, $id_shop);
            }
            
            $info['link'] = $this->getLinkByObj($obj, $id_lang, $id_shop);
        }
        $this->smarty->assign(array(
            'lines' => $infos,
            'type' => $type,
            'countInfo' => $countInfo,
            'pagination' => $pagination,
            'nbpage' => $nbpage,
            'pageActif' => $page,
            'searchs' => $searchs,
            'refresh' => $refresh,
            'token' => $this->ec_token
        ));
        return $this->display(__FILE__, 'views/templates/admin/tabMi.tpl');
    }

    public function getMetaTableTask($type, $id_shop, $refresh = false, $search = null, $page = null)
    {
        if ($page == null) {
            $page = 1;
        }
        $context = Context::getContext();
        //$context->employee = new Employee(1);
        $pagination = Configuration::get('EC_SEO_META_'.$type.'PAGINATION');
        
        if (($pagination > 0) == false) {
            Configuration::updateValue('EC_SEO_META_'.$type.'PAGINATION', 20);
            $pagination = 20;
        }
        $p1 = ($page*$pagination)-$pagination;
        $p2 = $pagination;
        
        if ($type == 'cms') {
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $req = 'SELECT id, meta_title as name, head_seo_title as meta_title, meta_description, esmt.id_lang FROM '._DB_PREFIX_.'ec_seo_meta_temp esmt
                LEFT JOIN '._DB_PREFIX_.$type.'_lang l ON (l.id_'.$type.' = esmt.id)
                WHERE page = "'.pSQL($type).'" AND esmt.id_shop = '.(int)$id_shop.' AND l.id_shop = '.(int)$id_shop.' AND l.id_lang = esmt.id_lang';
            } else {
                $req = 'SELECT id, meta_title as name, meta_title, meta_description, esmt.id_lang FROM '._DB_PREFIX_.'ec_seo_meta_temp esmt
                LEFT JOIN '._DB_PREFIX_.$type.'_lang l ON (l.id_'.$type.' = esmt.id)
                WHERE page = "'.pSQL($type).'" AND esmt.id_shop = '.(int)$id_shop.' AND l.id_shop = '.(int)$id_shop.' AND l.id_lang = esmt.id_lang';
            }
        } else if ($type == 'manufacturer' || $type == 'supplier') {
            $req = 'SELECT id, name, esmt.id_lang, meta_title, meta_description FROM '._DB_PREFIX_.'ec_seo_meta_temp esmt
            LEFT JOIN '._DB_PREFIX_.$type.' sp ON (sp.id_'.$type.' = esmt.id)
            LEFT JOIN '._DB_PREFIX_.$type.'_shop a ON (a.id_'.$type.' = esmt.id)
            LEFT JOIN '._DB_PREFIX_.$type.'_lang l ON (l.id_'.$type.' = esmt.id)
            WHERE page = "'.pSQL($type).'" AND esmt.id_shop = '.(int)$id_shop.' AND a.id_shop = '.(int)$id_shop.' AND l.id_lang = esmt.id_lang';
        } else {
            $req = 'SELECT id, name, meta_title, esmt.id_lang, meta_description FROM '._DB_PREFIX_.'ec_seo_meta_temp esmt
            LEFT JOIN '._DB_PREFIX_.$type.'_lang l ON (l.id_'.$type.' = esmt.id)
            WHERE page = "'.pSQL($type).'" AND esmt.id_shop = '.(int)$id_shop.' AND l.id_shop = '.(int)$id_shop.' AND l.id_lang = esmt.id_lang';
        }
        
        if ($search != null) {
            parse_str($search, $searchs);
            if (!empty($searchs['name'])) {
                $req .= ' AND name like "%'.pSQL($searchs['name']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['meta_title'])) {
                $req .= ' AND meta_title like "%'.pSQL($searchs['meta_title']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['meta_description'])) {
                $req .= ' AND meta_description like "%'.pSQL($searchs['meta_description']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['id'])) {
                $req .= ' AND id like "%'.(int)$searchs['id'].'%"';
                $rsearch = true;
            }
        } else {
            $searchs= array();
        }
        
        $countInfo = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.$p1.','.$p2;
        $infos = Db::getInstance()->executes($req);
        $nbpage = ($countInfo/$pagination);
        if (is_float($nbpage)) {
            $nbpage = (int)$nbpage + 1;
        }
        foreach ($infos as &$info) {
            $id = $info['id'];
            $id_lang  = $info['id_lang'];
            $info['lang'] = Language::getIsoById($id_lang);
            $class = Tools::ucfirst($type);
            if ($type == 'cms') {
                $class = 'CMS';
            }
            if ($type == 'product') {
                $obj = new $class($id, false, null, $id_shop);
            } else {
                $obj = new $class($id, null, $id_shop);
            }
            $info['link'] = $this->getLinkByObj($obj, $id_lang, $id_shop);
        }
        $this->smarty->assign(array(
            'lines' => $infos,
            'type' => $type,
            'countInfo' => $countInfo,
            'pagination' => $pagination,
            'nbpage' => $nbpage,
            'pageActif' => $page,
            'searchs' => $searchs,
            'refresh' => $refresh,
            'token' => $this->ec_token
        ));
        return $this->display(__FILE__, 'views/templates/admin/tabMeta.tpl');
    }

    public function showConfig($active = '', $error = false)
    {
        $id_shop = (int)$this->context->shop->id;
        $html = '';
        $btl = Tools::getValue('btl', 0);
        if ($active != 'dashboard' && !$error && !$btl) {
            $html.= $this->displayConfirmation($this->l('Successful update'));
        }
        $metaform = '';
        foreach ($this->tab_list as $class => $info) {
            $metaform .= $this->getMetaGPFormGen($class);
            if ($info['spe']) {
                //$metaform .= $this->getMetaFormSpe($class);
                $metaform .= $this->renderList($class);
            }
        }
        $miform = '';
        $miTableTask = '';
        $footerSEO = '';
        foreach ($this->tab_list as $class => $info) {
            //$miform .= $this->getConfigMI($class);
            $miform .= $this->renderListMI($class, $info['spe']);
            $miTableTask .= $this->getMiTableTask($class, $id_shop);
            $miTableTask .= $this->getMetaTableTask($class, $id_shop);
        }
        $lst_footer = Db::getInstance()->executes('SELECT id, type FROM '._DB_PREFIX_.'ec_seo_footer WHERE spe = 0 AND id_shop = '.(int)$id_shop);
        foreach ($lst_footer as $footer) {
            if ($footer['type'] != 'product') {
                $footerSEO .= $this->getFooterFormGen($footer['type']);
                $footerSEO .= $this->renderListBlockSeo($footer['id']);
            }
        }
        $footerSEO .= $this->renderListFooterSpeCategory();
        //$footerSEO .= $this->renderListFooterSpeProduct();
        $footerSEO .= $this->renderListFooterSpeCMS();
        $footerSEO .= $this->renderListFooterSpeSupplier();
        $footerSEO .= $this->renderListFooterSpeManufacturer();
        return $html.$this->showMenu($active).$this->showStats().$this->showRobot().$this->showPreview().$this->getConfigGenForm().$metaform.$this->getRedirectionForm().$this->showMetaVariables().$this->showTask().$this->renderListRI().$this->renderFormRI().$miform.$miTableTask.$footerSEO.$this->renderListBlockHTML().$this->renderListPageNoIndex().$this->renderListPageCMSNoIndex().$this->getOpenGraphForm()/* .$this->getConfigReportForm() */.$this->showBaliseAlt().$this->showLogo();
    }

    public function refreshBackUp()
    {
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $ec_seo_controller_uri = $this->protocol . Tools::getShopDomain() . str_replace('/modules/', '/module/', $this->getPathUri());
        } else {
            $ec_seo_controller_uri = $this->protocol . Tools::getShopDomain() . __PS_BASE_URI__ . 'index.php&fc=module&module=' . $this->name.'&controller=';
        }
        $this->smarty->assign(array(
            'uriec_seo' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_seo/',
            'ec_seo_controller_uri' => $ec_seo_controller_uri,
            'backups' => $this->getBackUp(),
            'ec_id_shop' => (int)$this->context->shop->id,
            'token' => $this->ec_token
        ));
        return $this->display(__FILE__, 'views/templates/admin/tabBackup.tpl');
    }

    public function getBackUp()
    {
        $backup_dir = scandir(dirname(__FILE__).'/backup/');
        $backups = array();
        foreach ($backup_dir as $dir) {
            if ($dir == '.'  || $dir == '..' || $dir == 'index.php') {
                continue;
            }
            $files = scandir(dirname(__FILE__).'/backup/'.$dir);
            $last_date = false;
            $last_file = false;
            foreach ($files as $file) {
                $date_modif = filemtime(dirname(__FILE__).'/backup/'.$dir.'/'. $file);
                if (($last_date == false || $last_date < $date_modif) && $file != '.' && $file != '..' && $file != 'index.php') {
                    $last_date = $date_modif;
                    $last_file = $file;
                }
            }
            if ($last_file) {
                $backups[$dir][] = $last_file;
            }
        }
       
        return $backups;
    }

    public function refreshReport()
    {
        $this->smarty->assign(array(
            'uriec_seo' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_seo/',
            'reports' => $this->getReports(),
            'link_task' => $this->context->link->getModuleLink($this->name, 'genExcel', ['ec_token' => $this->ec_token, 'id_shop' => $this->context->shop->id])
        ));
        return $this->display(__FILE__, 'views/templates/admin/tabReport.tpl');
    }

    public function getReports()
    {
        $report_dir = scandir(dirname(__FILE__).'/report/');
        $reports = array();
        foreach ($report_dir as $file) {
            if ($file == '.'  || $file == '..' || $file == 'index.php') {
                continue;
            }
            $reports[] = $file;
        }
        arsort($reports);
        return $reports;
    }

    public function showMenu($active)
    {
        $list_menu = $this->tab_list;
        $this->smarty->assign(array(
            'active' => $active,
            'list_menu' => $list_menu,
            'back_up' => $this->refreshBackUp(),
            'report' => $this->refreshReport(),
            'version16' => version_compare(_PS_VERSION_, '1.7', '<')
        ));
        return $this->display(__FILE__, 'views/templates/admin/menu.tpl');
    }

    public function showLogo()
    {
        $this->context->controller->addJS(($this->_path).'views/js/logo.js');

        $this->smarty->assign(array(
            'uriec_seo' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_seo/'
        ));
        return $this->display(__FILE__, 'views/templates/admin/logo.tpl');
    }

    public function getRedirectionForm()
    {
        $id_shop = (int)$this->context->shop->id;
        $onglet = '';
        $tab = array('Product','Category','Supplier','Manufacturer','Cms');
        $tab_name = array($this->l('Product'),$this->l('Category'),$this->l('Supplier'),$this->l('Manufacturer'),$this->l('Cms'));


        if (Tools::isSubmit('onglet')) {
            $onglet = (int)Tools::getValue('onglet');
        } else {
            $onglet = 5;
        }
        $tabsmarty = array();
        foreach ($tab as $key => $tab_obj) {
            $tabsmarty[] = array($key, $tab_name[$key]);
        }
        $tabbsmarty = array();
        foreach ($tab as $key => $tab_obj) {
            $tabbsmarty[] = array($key, $tab_obj);
        }
        $this->smarty->assign(array(
            "this" => $this,
            "disableoverride" => Configuration::get('PS_DISABLE_OVERRIDES'),
            "submitok" => Tools::isSubmit('ok'),
            "submitaddurl" =>  Tools::isSubmit('addURL'),
            "shop" => $this->context->shop->id,
            "displayerror" => $this->displayError($this->l('Overrides are disabled on your shop, you need to active it in the menu "Advanced parameters - Performance - Debug mode"')),
            "onglet" => $onglet,
            "tab" => $tab,
            "tabsmarty" => $tabsmarty,
            "tabbsmarty" => $tabbsmarty,
            'postprocess' => $this->postProcess($id_shop, (int)Tools::getValue('onglet')),
            "importcsv" => Tools::isSubmit('importCsv')
        ));
        
        return $this->display(__FILE__, 'views/templates/admin/redirection.tpl');
    }
    protected function getConfigGenForm()
    {

        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Configuration'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active SEO Front office'),
                        'name' => 'EC_SEO_FRONT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('IP adress'),
                        'name' => 'EC_SEO_FRONT_IP',
                        'desc'  => $this->l('Separate them by ";". Exemple: 192.131.154.1;190.164.554.454'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active H1'),
                        'name' => 'EC_SEO_H1',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active backup'),
                        'name' => 'EC_SEO_BACKUP',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Update only active products (Meta)'),
                        'name' => 'EC_SEO_META_ACTIVE_PROD',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Update only active products (Internal Mesh)'),
                        'name' => 'EC_SEO_MI_ACTIVE_PROD',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Update only active products (image alt)'),
                        'name' => 'EC_SEO_ALT_ACTIVE_PROD',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Perform analysis only on active products (Excel report)'),
                        'name' => 'EC_SEO_REPORT_ACTIVE_PROD',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Export only pages with errors'),
                        'name' => 'EC_SEO_REPORT_ERRORS_ONLY',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Update only empty metas'),
                        'name' => 'EC_SEO_ONLY_EMTPY_METAS',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('HT Access'),
                        'name' => 'EC_SEO_HTACCESS',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Login HT Access'),
                        'name' => 'EC_SEO_LOGIN_HTACCESS',
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Password HT Access'),
                        'name' => 'EC_SEO_PW_HTACCESS',
                    ),
                    /* array(
                        'col' => 6,
                        'type' => 'text',
                        'label' => $this->l('Google search console client ID'),
                        'name' => 'EC_SEO_GSC_CLIENT_ID',
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'label' => $this->l('Google search console client Secret'),
                        'name' => 'EC_SEO_GSC_CLIENT_SECRET',
                    ), */
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'config';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfigGeneral';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $values = array(
            'EC_SEO_FRONT' => Configuration::get('EC_SEO_FRONT'),
            'EC_SEO_FRONT_IP' => Configuration::get('EC_SEO_FRONT_IP'),
            'EC_SEO_H1' => Configuration::get('EC_SEO_H1'),
            'EC_SEO_BACKUP' => Configuration::get('EC_SEO_BACKUP'),
            'EC_SEO_META_ACTIVE_PROD' => Configuration::get('EC_SEO_META_ACTIVE_PROD'),
            'EC_SEO_MI_ACTIVE_PROD' => Configuration::get('EC_SEO_MI_ACTIVE_PROD'),
            'EC_SEO_ALT_ACTIVE_PROD' => Configuration::get('EC_SEO_ALT_ACTIVE_PROD'),
            'EC_SEO_REPORT_ACTIVE_PROD' => Configuration::get('EC_SEO_REPORT_ACTIVE_PROD'),
            'EC_SEO_REPORT_ERRORS_ONLY' => Configuration::get('EC_SEO_REPORT_ERRORS_ONLY'),
            'EC_SEO_ONLY_EMTPY_METAS' => Configuration::get('EC_SEO_ONLY_EMTPY_METAS'),
            'EC_SEO_HTACCESS' => Configuration::getGlobalValue('EC_SEO_HTACCESS'),
            'EC_SEO_LOGIN_HTACCESS' => Configuration::getGlobalValue('EC_SEO_LOGIN_HTACCESS'),
            'EC_SEO_PW_HTACCESS' => Configuration::getGlobalValue('EC_SEO_PW_HTACCESS'),
            'EC_SEO_GSC_CLIENT_ID' => Configuration::getGlobalValue('EC_SEO_GSC_CLIENT_ID'),
            'EC_SEO_GSC_CLIENT_SECRET' => Configuration::getGlobalValue('EC_SEO_GSC_CLIENT_SECRET'),
        );

        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($form));
    }

    protected function getConfigReportForm()
    {

        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Configuration'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Export only pages with errors'),
                        'name' => 'EC_SEO_REPORT_ERRORS_ONLY',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'report_config';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfigReport';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $values = array(
            'EC_SEO_REPORT_ERRORS_ONLY' => Configuration::get('EC_SEO_REPORT_ERRORS_ONLY'),
        );

        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($form));
    }

    protected function getOpenGraphForm()
    {

        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Open Graph'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active Open Graph'),
                        'name' => 'EC_SEO_OG',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('OG site name'),
                        'name' => 'EC_SEO_OG_SITE_NAME',
                        'col'  => 3,
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('OG Locale'),
                        'name' => 'EC_SEO_OG_LOCALE',
                        'col'  => 3,
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('OG title'),
                        'desc' => $this->l('Use meta title by default'),
                        'name' => 'EC_SEO_OG_TITLE_DEFAULT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('OG description'),
                        'desc' => $this->l('Use meta description by default'),
                        'name' => 'EC_SEO_OG_DESCRPTION_DEFAULT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'opengraph';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitOpenGraph';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $values = array(
            'EC_SEO_OG' => Configuration::get('EC_SEO_OG'),
            'EC_SEO_OG_TITLE_DEFAULT' => Configuration::get('EC_SEO_OG_TITLE_DEFAULT'),
            'EC_SEO_OG_DESCRPTION_DEFAULT' => Configuration::get('EC_SEO_OG_DESCRPTION_DEFAULT'),
        );
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            $values['EC_SEO_OG_SITE_NAME'][$id_lang] = Configuration::get('EC_SEO_OG_SITE_NAME_'.$id_lang);
            $values['EC_SEO_OG_LOCALE'][$id_lang] = Configuration::get('EC_SEO_OG_LOCALE_'.$id_lang);
        }

        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($form));
    }


    protected function getMetaGPFormGen($class)
    {

        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l(''.$class.' General'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title'),
                        'name' => 'meta_title',
                        'lang' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Descritption'),
                        'name' => 'meta_description',
                        'lang' => true
                    ),
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'metagenerator'.$class.'gen';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMetaGen'.$class;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $info = Db::getInstance()->ExecuteS('SELECT meta_title, meta_description, id_lang FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mg_gen WHERE id_shop = '.(int)$this->context->shop->id);
        $values = array();
        foreach ($info as $val) {
            $values['meta_title'][$val['id_lang']] = $val['meta_title'];
            $values['meta_description'][$val['id_lang']] = $val['meta_description'];
        }
        
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($form));
    }

    protected function getMetaFormSpe($class, $id = null)
    {
        $values = array();
        $categories = array();
        if ($id == null) {
            $values['id'] = 0;
            foreach (Language::getLanguages(true) as $lang) {
                $id_lang = $lang['id_lang'];
                $values['meta_title'][$id_lang] = null;
                $values['meta_description'][$id_lang] = null;
            }
        } else {
            $values['id'] = $id;
            $r_categories = Db::getInstance()->executes('SELECT id_category FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mg_cat WHERE id_seo_'.$class.'_mg = '.(int)$id);
            foreach ($r_categories as $category) {
                $categories[] = $category['id_category'];
            }
            $info = Db::getInstance()->ExecuteS('SELECT meta_title, meta_description, id_lang FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mg_lang WHERE id_seo_'.$class.'_mg = '.(int)$id);
            foreach ($info as $val) {
                $values['meta_title'][$val['id_lang']] = $val['meta_title'];
                $values['meta_description'][$val['id_lang']] = $val['meta_description'];
            }
        }
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $class,
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Meta Title'),
                        'name' => 'meta_title',
                        'lang' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Meta Descritption'),
                        'name' => 'meta_description',
                        'lang' => true
                    ),
                    array(
                        'type' => 'categories',
                        'label' => $this->l('Select categories'),
                        'name' => 'categories',
                        'class'=>'cat-test',
                        'tree' => array(
                            'root_category' => 2,
                            'id' => 'id_category',
                            'name' => 'name_category',
                            'selected_categories' => $categories,
                            'use_checkbox' => true,
                        )
                    )
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&token='.Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                        'id' => 'ecmetacancel_'.$class
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'meta_spe_'.$class;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        if ($id == null) {
            $helper->submit_action = 'submitMetaSpe'.$class;
        } else {
            $helper->submit_action = 'submitEditMetaSpe'.$class;
        }
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', true)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($form));
    }

    protected function renderList($name = 'product')
    {
        $fields_list = array();
        $fields_list['id_ec_list_meta_'.$name] = array(
            'title' => $this->l('ID'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['meta_title'] = array(
                'title' => $this->l('Meta title'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
        );
        $fields_list['meta_description'] = array(
            'title' => $this->l('Meta description'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['categories'] = array(
            'title' => $this->l('Categories'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_ec_list_meta_'.$name;
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.$name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        $helper->title = $name;
        $helper->table = 'ec_list_meta_'.$name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContent($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContent($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue('ec_product_meta_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilterec_product_meta');
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT espm.id, espm.id as id_ec_list_meta_'.$name.', ml.meta_title, ml.meta_description FROM '._DB_PREFIX_.'ec_seo_'.$name.'_mg espm
            LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$name.'_mg_lang ml ON (ml.id_seo_'.$name.'_mg = espm.id)
            WHERE id_lang = '.(int)$id_lang.' AND espm.id_shop = '.(int)$id_shop.'
        ';
        
        $orderby = Tools::getValue('ec_product_metaOrderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue('ec_product_metaOrderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        
        $res = Db::getInstance()->ExecuteS($req);
        foreach ($res as $key => &$val) {
            $categories = Db::getInstance()->executes('SELECT cl.id_category, name FROM '._DB_PREFIX_.'category_lang cl
            LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$name.'_mg_cat ec ON (ec.id_category = cl.id_category) WHERE id_seo_'.$name.'_mg = '.(int)$val['id'].' AND id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$this->context->shop->id);
            $concat_cat = '';
            foreach ($categories as $key => $cat) {
                $id_category = $cat['id_category'];
                //$link = $this->context->link->getAdminLink('AdminEcSeoCategory', true).'&id_category='.(int)$id_category;
                if ($key == 0) {
                    $concat_cat .= $cat['name'].' ('.$id_category.')';
                } else {
                    $concat_cat .= ', '.$cat['name'].' ('.$id_category.')';
                }
            }
            $val['categories'] = $concat_cat;
        }
        return array('res' => $res, 'count' => $count);
    }

    protected function getRobotForm($id_shop, $domain = '')
    {
        $id_shop_default = Configuration::get('PS_SHOP_DEFAULT');
        $id = $id_shop;
        if ($id_shop == $id_shop_default) {
            $id = '';
        }
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => 'Robots'.$id.'.txt '/* .$domain */,
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'robot_shop',
                    ),
                    array(
                        'type' => 'textarea',
                        'name' => 'robot',
                    ),
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'robottxt'.$id_shop;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitRobot';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $content = Tools::file_get_contents(_PS_ROOT_DIR_.'/robots'.$id.'.txt');
        if (Tools::strlen($content) == 0) {
            $content = Tools::file_get_contents(_PS_ROOT_DIR_.'/robots.txt');
        }
        $values = array('robot_shop' => $id_shop, 'robot' => $content);

        
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($form));
    }

    public function showRobot()
    {
        $id_shop_default = Configuration::get('PS_SHOP_DEFAULT');
        $robotform = $this->getRobotForm($id_shop_default);
        $domains = Db::getInstance()->executes('SELECT distinct(domain) FROM '._DB_PREFIX_.'shop_url');
        foreach ($domains as &$info) {
            $domain = $info['domain'];
            if (!isset($info['id_shop'])) {
                $id_shop_domain = Db::getInstance()->getValue('SELECT id_shop FROM '._DB_PREFIX_.'shop_url WHERE domain = "'.pSQL($domain).'"');
                $info['id_shop'] = $id_shop_domain;
            } else {
                $id_shop_domain = $info['id_shop'];
            }
            
            if ($id_shop_default != $id_shop_domain) {
                $robotform .= $this->getRobotForm($id_shop_domain, $domain);
            }
        }
        $this->smarty->assign(array(
            'robotform' => $robotform,
            'domains' => $domains,
        ));
        return $this->display(__FILE__, 'views/templates/admin/robot.tpl');
    }



    public function hookActionProductDelete($params)
    {
        $this->actionObjectDeleteAfter('Product', $params);
    }

    public function hookActionProductSave($params)
    {
        $this->redirectProductSave($params);
    }

    public function redirectProductSave($params)
    {
        $id_p = $params['id_product'];
        $languages = Language::getLanguages(false);

        $act = Db::getInstance()->getValue('SELECT active FROM `'._DB_PREFIX_.'product_shop` 
        WHERE id_product='.(int)$id_p.' AND id_shop = '.(int)$this->context->shop->id);
        if ($act == 0) {
            foreach ($languages as $language) {
                $exi = Db::getInstance()->getValue('SELECT id FROM `'._DB_PREFIX_.'ec_seo` 
                WHERE idP='.(int)$id_p.' AND type = "Product" AND idL='.(int)$language['id_lang'].' AND idshop ='.(int)$this->context->shop->id);
                $default_behavior = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` 
                WHERE name="product_disabled_behav"');
                $default_link = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="product_disabled_link"');
                if (!isset($exi) || $exi == '') {
                    $name = Db::getInstance()->getValue('SELECT name FROM `'._DB_PREFIX_.'product_lang` 
                    WHERE id_product='.(int)$id_p.' AND id_lang='.(int)$language['id_lang'].' AND id_shop = '.(int)$this->context->shop->id);
                    Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo 
                    VALUES("","Product", '.(int)$id_p.', '.(int)$language['id_lang'].', "'.pSQL($default_link).'", 
                    '.($default_behavior == 'None'?0:1).', "'.pSQL($name).'", "'.pSQL($default_behavior).'",1,'.(int)$this->context->shop->id.')');
                } else {
                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ec_seo SET 
                    lienS = "'.pSQL($default_link).'", onlineS = '.($default_behavior == 'None'?0:1).', typeRed = "'.pSQL($default_behavior).'", activRed = 1
                    WHERE idP = '.(int)$id_p.'  AND idL='.(int)$language['id_lang'].' AND type="Product" AND idshop='.(int)$this->context->shop->id);
                }
            }
        } else {
            foreach ($languages as $language) {
                $exi = Db::getInstance()->getValue('SELECT id FROM `'._DB_PREFIX_.'ec_seo` 
                WHERE idP='.(int)$id_p.' AND idL='.(int)$language['id_lang'].' AND `type` = "Product" AND idshop='.(int)$this->context->shop->id);
                if (isset($exi) && $exi != '') {
                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ec_seo SET onlineS=0, activRed = 0 
                    WHERE idP = '.(int)$id_p.'  AND idL='.(int)$language['id_lang'].' AND type="Product" AND idshop='.(int)$this->context->shop->id);
                }
            }
        }
    }

    public function hookActionCategoryDelete($params)
    {
        $this->actionObjectDeleteAfter('Category', $params);
    }

    public function hookActionCategoryUpdate($params)
    {
        $this->actionObjectUpdateAfter('Category', $params);
    }

    public function hookActionObjectSupplierDeleteAfter($params)
    {
        $this->actionObjectDeleteAfter('Supplier', $params);
    }
    public function hookActionObjectSupplierUpdateAfter($params)
    {
        $this->actionObjectUpdateAfter('Supplier', $params);
    }

    public function hookActionObjectManufacturerDeleteAfter($params)
    {
        $this->actionObjectDeleteAfter('Manufacturer', $params);
    }

    public function hookActionObjectManufacturerUpdateAfter($params)
    {
        $this->actionObjectUpdateAfter('Manufacturer', $params);
    }

    public function hookActionObjectCmsDeleteAfter($params)
    {
        $this->actionObjectDeleteAfter('Cms', $params);
    }

    public function hookActionObjectCmsUpdateAfter($params)
    {
        $this->actionObjectUpdateAfter('Cms', $params);
    }

    public function actionObjectUpdateAfter($obj, $params)
    {
        $obj_min = Tools::strtolower($obj);
        if (isset($params[$obj_min]) || isset($params['object'])) {
            if ($obj == 'Category') {
                $plop = $params[$obj_min];
            } else {
                $plop = $params['object'];
            }
            $id = $plop->id;
            if (Db::getInstance()->getValue('SELECT active FROM `'._DB_PREFIX_.''.pSQL($obj_min).'` WHERE id_'.pSQL($obj_min).' = '.(int)$id) == 0) {
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $exi = Db::getInstance()->getValue('SELECT id FROM `'._DB_PREFIX_.'ec_seo` 
                    WHERE idP='.(int)$id.' AND idL='.(int)$language['id_lang'].' AND type="'.pSQL($obj).'" AND idshop='.(int)$this->context->shop->id);
                    $default_behavior = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_disabled_behav"');
                    $default_link = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_disabled_link"');
                    if (!isset($exi) || $exi == '') {
                        if ($obj == 'Category') {
                            $name = Db::getInstance()->getValue('SELECT name FROM `'._DB_PREFIX_.''.pSQL($obj_min).'_lang` 
                            WHERE id_'.pSQL($obj_min).'='.(int)$id.' AND id_lang='.(int)$language['id_lang']);
                        } elseif ($obj == 'Cms') {
                            $name = Db::getInstance()->getValue('SELECT meta_title FROM `'._DB_PREFIX_.''.pSQL($obj_min).'_lang` 
                            WHERE id_'.pSQL($obj_min).'='.(int)$id.' AND id_lang='.(int)$language['id_lang']);
                        } elseif ($obj == 'Supplier' || $obj == 'Manufacturer') {
                            $name = Db::getInstance()->getValue('SELECT name FROM `'._DB_PREFIX_.''.pSQL($obj_min).'` 
                            WHERE id_'.pSQL($obj_min).'='.(int)$id);
                        }
                        ($name == '' ? 'NC': $name);
                        Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo 
                        VALUES("","'.pSQL($obj).'", '.(int)$id.', '.(int)$language['id_lang'].', "'.pSQL($default_link).'", '.($default_behavior == 'None'?0:1).', 
                        "'.pSQL($name).'", "'.pSQL($default_behavior).'",1,'.(int)$this->context->shop->id.')');
                    } else {
                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ec_seo SET 
                        lienS = "'.pSQL($default_link).'", onlineS = '.($default_behavior == 'None'?0:1).', typeRed = "'.pSQL($default_behavior).'", activRed = 1
                        WHERE idP = '.(int)$id.'  AND idL='.(int)$language['id_lang'].' AND type="'.pSQL($obj).'" AND idshop='.(int)$this->context->shop->id);
                    }
                }
            } else {
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $exi = Db::getInstance()->getValue('SELECT id FROM `'._DB_PREFIX_.'ec_seo` 
                    WHERE idP='.(int)$id.' AND idL='.(int)$language['id_lang'].' AND type="'.pSQL($obj).'" AND idshop='.(int)$this->context->shop->id);
                    if (isset($exi) && $exi != '') {
                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ec_seo SET onlineS=0,activRed=0 WHERE idP='.(int)$id.' AND type="'.pSQL($obj).'"');
                    }
                }
            }
        }
    }

    public function actionObjectDeleteAfter($obj, $params)
    {
        $obj_min = Tools::strtolower($obj);
        $id = $params['id_'.$obj_min];
        $default_behavior = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_deleted_behav"');
        $default_link = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_deleted_link"');
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $exi = Db::getInstance()->getValue('SELECT id FROM `'._DB_PREFIX_.'ec_seo` 
            WHERE idP='.(int)$id.' AND idL='.(int)$language['id_lang'].' AND type="'.pSQL($obj).'"'.($obj == 'Product'?' AND idshop='.(int)$this->context->shop->id.'':''));
            if (!isset($exi) || $exi == '') {
                if ($obj == 'Product' || $obj == 'Category') {
                    $name = $params[$obj_min]->name[$language['id_lang']];
                } elseif ($obj == 'Cms') {
                    $name = $params['object']->meta_title[$language['id_lang']];
                } else {
                    $name = $params['object']->name;
                }

                Db::getinstance()->insert(
                    'ec_seo',
                    array(
                        'type' => pSQL($obj),
                        'idP' => (int)$id,
                        'idL' => (int)$language['id_lang'],
                        'lienS' => pSQL($default_link),
                        'onlineS' => ($default_behavior == 'None'?0:1),
                        'nameS' => pSQL($name),
                        'typeRed' => pSQL($default_behavior),
                        'activRed' => 1,
                        'idshop' => (int)$this->context->shop->id,
                    )
                );
            } else {
                Db::getinstance()->update(
                    'ec_seo',
                    array(
                        'type' => pSQL($obj),
                        'idL' => (int)$language['id_lang'],
                        'lienS' => pSQL($default_link),
                        'onlineS' => ($default_behavior == 'None'?0:1),
                        'typeRed' => pSQL($default_behavior),
                        'activRed' => 1,
                        'idshop' => (int)$this->context->shop->id,
                    ),
                    'idP = '.(int)$id.' AND idL='.(int)$language['id_lang'].' AND type="'.pSQL($obj).'"'.($obj == 'Product'?' AND idshop='.(int)$this->context->shop->id.'':'')
                );
            }
        }
    }

    public function listO($obj, $id_ss)
    {
        // produit non dans la liste
        $obj_min = Tools::strtolower($obj);
        if ($obj == 'Product') {
            $lst_obj = Db::getInstance()->ExecuteS('SELECT id_'.pSQL($obj_min).' FROM `'._DB_PREFIX_.''.pSQL($obj_min).'_shop` 
            WHERE active=0  AND id_shop='.(int)$id_ss.' AND id_'.pSQL($obj_min).' 
            NOT IN (SELECT idP FROM `'._DB_PREFIX_.'ec_seo` WHERE type="'.pSQL($obj).'" AND idshop='.(int)$id_ss.')');
        } else {
            $lst_obj = Db::getInstance()->ExecuteS('SELECT cs.id_'.pSQL($obj_min).' FROM `'._DB_PREFIX_.''.pSQL($obj_min).'_shop` cs 
            LEFT JOIN `'._DB_PREFIX_.''.pSQL($obj_min).'` ca ON( cs.`id_'.pSQL($obj_min).'` = ca.`id_'.pSQL($obj_min).'`) 
            WHERE id_shop='.(int)$id_ss.' AND active = 0 AND cs.id_'.pSQL($obj_min).' NOT IN 
            (SELECT idP FROM `'._DB_PREFIX_.'ec_seo` WHERE type="'.pSQL($obj).'" AND idshop='.(int)$id_ss.')');
        }
        foreach ($lst_obj as $oo) {
            $id_o = $oo['id_'.$obj_min];
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $exi = Db::getInstance()->getValue('SELECT id FROM `'._DB_PREFIX_.'ec_seo` 
                WHERE idP='.(int)$id_o.' AND type="'.pSQL($obj).'" AND idshop='.(int)$id_ss.' AND idL='.(int)$language['id_lang']);
                $default_behavior = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_disabled_behav"');
                $default_link = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_disabled_link"');
                if (!isset($exi) || $exi == '') {
                    if ($obj == 'Product' || $obj == 'Category') {
                        $name = Db::getInstance()->getValue('SELECT name FROM `'._DB_PREFIX_.''.pSQL($obj_min).'_lang` 
                        WHERE id_'.pSQL($obj_min).'='.(int)$id_o.' AND id_shop = '.(int)$id_ss.' AND id_lang='.(int)$language['id_lang']);
                    } elseif ($obj == 'Cms') {
                        $name = Db::getInstance()->getValue('SELECT meta_title FROM `'._DB_PREFIX_.''.pSQL($obj_min).'_lang` 
                        WHERE id_'.pSQL($obj_min).'='.(int)$id_o.' AND id_lang='.(int)$language['id_lang']);
                    } elseif ($obj == 'Supplier' || $obj == 'Manufacturer') {
                        $name = Db::getInstance()->getValue('SELECT name FROM `'._DB_PREFIX_.''.pSQL($obj_min).'` 
                        WHERE id_'.pSQL($obj_min).'='.(int)$id_o);
                    }
                    $namevalue = ($name == '') ? 'NC': $name;
                    Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo 
                    VALUES("","'.pSQL($obj).'", '.(int)$id_o.', '.(int)$language['id_lang'].', "'.pSQL($default_link).'", 
                    '.($default_behavior == 'None'?0:1).', "'.pSQL($namevalue).'", "'.pSQL($default_behavior).'",1,'.(int)$id_ss.')');
                }
            }
        }
        // produit dans la liste
        if ($obj == 'Product') {
            $lst_obj = Db::getInstance()->ExecuteS('SELECT id_'.pSQL($obj_min).', active FROM `'._DB_PREFIX_.''.pSQL($obj_min).'_shop` 
            WHERE id_shop='.(int)$id_ss.' AND id_'.pSQL($obj_min).' 
            IN (SELECT idP FROM `'._DB_PREFIX_.'ec_seo` WHERE type="'.pSQL($obj).'" AND idshop='.(int)$id_ss.')');
        } else {
            $lst_obj = Db::getInstance()->ExecuteS('SELECT cs.id_'.pSQL($obj_min).', ca.active FROM `'._DB_PREFIX_.''.pSQL($obj_min).'_shop` cs 
            LEFT JOIN `'._DB_PREFIX_.''.pSQL($obj_min).'` ca ON( cs.`id_'.pSQL($obj_min).'` = ca.`id_'.pSQL($obj_min).'`) 
            WHERE id_shop='.(int)$id_ss.' AND cs.id_'.pSQL($obj_min).' IN 
            (SELECT idP FROM `'._DB_PREFIX_.'ec_seo` WHERE type="'.pSQL($obj).'" AND idshop='.(int)$id_ss.')');
        }
        
        $tab_obj = array();
        foreach ($lst_obj as $oo) {
            $tab_obj[$oo['id_'.$obj_min]] = $oo['active'];
        }
        
        $lst_red = Db::getInstance()->ExecuteS('SELECT idP, idL, activRed FROM `'._DB_PREFIX_.'ec_seo` WHERE type="'.pSQL($obj).'" AND idshop='.(int)$id_ss);
        $tab_red = array();
        foreach ($lst_red as $red) {
            $tab_red[$red['idP']][$red['idL']] = $red['activRed'];
        }
        
        foreach ($tab_red as $key_idP => $red) {
            foreach ($red as $active) {
                if (isset($tab_obj[$key_idP])) {
                    if ($active == 0 && $tab_obj[$key_idP] == 0) {
                        $default_behavior = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_disabled_behav"');
                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ec_seo SET onlineS='.($default_behavior == 'None'?0:1).', activRed=1, typeRed="'.pSQL($default_behavior).'" WHERE idP='.(int)$key_idP.' AND type="'.pSQL($obj).'"');
                    } elseif ($active == 1 && $tab_obj[$key_idP] == 1) {
                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ec_seo SET onlineS=0, activRed=0 WHERE idP='.(int)$key_idP.' AND type="'.pSQL($obj).'"');
                    }
                }
            }
        }
    }
    
    public function postProcess($id_ss, $onglet)
    {
        if (Tools::isSubmit('importCsv')) {
            $format = Tools::strtolower(Tools::substr(strrchr($_FILES['file']['name'], '.'), 1));
            if ($format == 'csv') {
                if ($onglet != 6) {
                    $tab = array('Product','Category','Supplier','Manufacturer','Cms');
                    $type = $tab[$onglet];
                }
                move_uploaded_file($_FILES['file']['tmp_name'], '../modules/ec_seo/data/'.$_FILES['file']['name']);
                $handle = @fopen('../modules/ec_seo/data/'.$_FILES['file']['name'], 'r');
                $ligne = 1;
                $error = '';
                if ($handle) {
                    while (($buffer = fgets($handle, 4096)) !== false) {
                        if ($buffer != '') {
                            $array = explode(';', $buffer);

                            if ($onglet == 6) {
                                if (array_key_exists(0, $array) && array_key_exists(1, $array) && array_key_exists(2, $array)) {
                                    $exist_url = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'ec_seo_redirect 
                                         WHERE old_link="'.pSQL($array[0]).'" AND id_shop='.(int)$id_ss);
                                    
                                    if ($exist_url > 0) {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ec_seo_redirect 
                                        SET typeRed = "'.pSQL(trim($array[2])).'", lienS = "'.pSQL($array[1]).'", onlineS=1 WHERE old_link="'.pSQL($array[0]).'" AND id_shop='.(int)$id_ss);
                                    } else {
                                        Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo_redirect 
                                        VALUES("","'.pSQL($array[0]).'", "'.pSQL($array[1]).'", "'.pSQL(trim($array[2])).'",1,'.(int)$id_ss.')');
                                    }
                                } else {
                                    $errorTxt = $this->l('Error, empty field, line').' : '.$ligne;
                                    for ($i = 1; $i < 4; $i++) {
                                        if (!array_key_exists($i, $array)) {
                                            $errorTxt .= ' - '.$this->l('column').' : '.$i;
                                        }
                                    }
                                    $error .= $this->displayError($errorTxt);
                                }
                            } else {
                                if (array_key_exists(0, $array) && array_key_exists(1, $array) && array_key_exists(2, $array) && array_key_exists(3, $array) && array_key_exists(4, $array)) {
                                    $exist_url = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'ec_seo 
                                    WHERE idL = '.(int)$array[1].' AND type="'.pSQL($type).'" AND idP ='.(int)$array[0].' AND idshop='.(int)$id_ss);
                                    
                                    if ($exist_url > 0) {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ec_seo
                                        SET typeRed = "'.pSQL($array[3]).'", lienS = "'.pSQL($array[4]).'", onlineS=1, activRed=1, nameS ="'.pSQL(trim($array[2])).'" WHERE idL = '.(int)$array[1].' AND type="'.pSQL($type).'" AND idP ='.(int)$array[0].' AND idshop='.(int)$id_ss);
                                    } else {
                                        Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo
                                        VALUES("","'.pSQL($type).'", '.(int)$array[0].', '.(int)$array[1].', "'.pSQL($array[4]).'", 1, "'.pSQL(trim($array[2])).'", "'.pSQL($array[3]).'", 1,'.(int)$id_ss.')');
                                    }
                                } else {
                                    $errorTxt = $this->l('Error, empty field, line').' : '.$ligne;
                                    for ($i = 1; $i < 6; $i++) {
                                        if (!array_key_exists($i, $array)) {
                                            $errorTxt .= ' - '.$this->l('column').' : '.$i;
                                        }
                                    }
                                    $error .= $this->displayError($errorTxt);
                                }
                            }
                        }
                        $ligne++;
                    }
                    if (!feof($handle)) {
                        $error .= $this->displayError($this->l('Error, read file fail'));
                    }
                    fclose($handle);
                }
                unlink('../modules/ec_seo/data/'.$_FILES['file']['name']);
                return $this->displayConfirmation($this->l('File uploaded!')).$error;
            } else {
                return $this->displayError($this->l('This isn\'t a csv file!'));
            }
        }
    }

    public function cronDisplay($id_ss)
    {
        $url = ShopUrl::getShopUrls($this->context->shop->id)->where('main', '=', 1)->getFirst();
        $txt2 = $this->l('In some very specific case like using Store Commander or marketplace, hooks update will not trigger so the status of product aren\'t correct, so you can refresh the list manually or by cron.');
        $txt = $this->l('Update by cron').' : http://'.$url->domain.__PS_BASE_URI__.'modules/ec_seo/cron.php?id_shop='.$id_ss.'&token='.Configuration::getGlobalValue('EC_TOKEN_SEO').'';
        $this->context->smarty->assign('txt', $txt);
        $this->context->smarty->assign('txt2', $txt2);
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        return $output;
    }

    public function defaultDisplay($obj, $onglet)
    {
        $obj_min = Tools::strtolower($obj);
        $query = array(
                    array(
                        'id_option' => '404',
                        'name' => $this->l('404 Not Found')
                        ),
                    array(
                        'id_option' => '301',
                        'name' => $this->l('301 Moved Permanently')
                        ),
                    array(
                        'id_option' => '302',
                        'name' => $this->l('302 Moved Temporarily')
                        ),
                    array(
                        'id_option' => 'homepage',
                        'name' => $this->l('Home page')
                        )
                    );
        if ($obj == 'Product') {
                array_push($query, array(
                        'id_option' => 'categorydefault',
                        'name' => $this->l('Default category')
                        ));
                array_push($query, array(
                        'id_option' => 'manufacturer',
                        'name' => $this->l('Manufacturer')
                        ));
        }
        $html_tmp = '';
        $fields_form_disabled = array(
            'form' => array(
                    'legend' => array(
                        'title' => $this->l('Default behavior for disabled'),
                        'icon' => 'icon-cogs'
                        ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'name' => $obj_min.'_disabled_behav',
                            'id' => $obj_min.'_disabled_behav',
                            'class' => 'default_behavior defaultDisabledRedirectLink'.($obj == 'Product'?$obj:'Autre'),
                            'label' => $this->l('Disabled'),
                            'options' => array(
                                'query' => $query,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'text',
                            'id' => $obj_min.'_disabled_link',
                            'label' => $this->l('Link'),
                            'name' => $obj_min.'_disabled_link',
                            'class' => 'updateLink_disabled_'.$obj_min,
                            'lang' => false,
                        ),
                        array(
                        'type' => 'hidden',
                        'name' => 'onglet'
                        ),
                    ),
                'buttons' => array(
                        'newBlock' => array(
                            'title' => $this->l('Cancel'),
                            'class' => 'pull-left hiddenAddForm',
                            'icon' => 'process-icon-cancel',
                            'name' => 'default'
                        )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'submit_link_disabled_'.$obj_min.'_ btn btn-default pull-right '
                )
            )
        );
        $helper_disabled = new HelperForm();
        $default_disabled = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_disabled_behav" ');
        $link_disabled = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_disabled_link" ');
        $helper_disabled->tpl_vars = array(
            'fields_value' => array($obj_min.'_disabled_behav' => $default_disabled,
                                    $obj_min.'_disabled_link' =>  $link_disabled,
                                    'onglet' => $onglet )
        );
        $helper_disabled->submit_action = 'ok';
        $helper_disabled->currentIndex = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper_disabled->token = Tools::getAdminTokenLite('AdminModules');
        $helper_disabled->module = $this;
        $helper_disabled->identifier = $this->identifier;
        $html_tmp .= $helper_disabled->generateForm(array($fields_form_disabled));
        
        if ($obj == 'Product') {
            array_pop($query);
            array_pop($query);
        }

        
        $fields_form_deleted = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Default behavior for deleted'),
                    'icon' => 'icon-cogs'
                    ),
                'input' => array(
                        array(
                            'type' => 'select',
                            'name' => $obj_min.'_deleted_behav',
                            'id' => $obj_min.'_deleted_behav',
                            'class' => 'default_behavior defaultDeletedRedirectLink'.($obj == 'Product'?$obj:'Autre'),
                            'label' => $this->l('Deleted'),
                            'options' => array(
                                'query' => $query,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'text',
                            'id' => $obj_min.'_deleted_link',
                            'label' => $this->l('Link'),
                            'name' => $obj_min.'_deleted_link',
                            'class' => 'updateLink_deleted_'.$obj_min,
                            'lang' => false,
                        ),
                        array(
                        'type' => 'hidden',
                        'name' => 'onglet'
                        ),
                    ),
                'buttons' => array(
                        'newBlock' => array(
                            'title' => $this->l('Cancel'),
                            'class' => 'pull-left hiddenAddForm',
                            'icon' => 'process-icon-cancel',
                            'name' => 'default'
                        )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'submit_link_deleted_'.$obj_min.'_ btn btn-default pull-right '
                )
            )
        );
        $helper_deleted = new HelperForm();
        $default_deleted = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_deleted_behav" ');
        $link_deleted = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'ec_seo_conf` WHERE name="'.pSQL($obj_min).'_deleted_link" ');
        $helper_deleted->tpl_vars = array(
            'fields_value' => array($obj_min.'_deleted_behav' => $default_deleted,
                                    $obj_min.'_deleted_link' =>  $link_deleted,
                                    'onglet' => $onglet  )
        );
        $helper_deleted->submit_action = 'ok';
        $helper_deleted->currentIndex = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper_deleted->token = Tools::getAdminTokenLite('AdminModules');
        $helper_deleted->module = $this;
        $helper_deleted->identifier = $this->identifier;
        $html_tmp .= $helper_deleted->generateForm(array($fields_form_deleted));
        return $html_tmp;
    }

    public function tab($obj, $id_ss, $page = null, $ajax = false, $search = null)
    {
        $obj_min = Tools::strtolower($obj);
        $html_tmp = '';
        if ($page == null) {
            $page = 1;
        }
        //$page = Tools::getValue('ec_page', 1);
        $pagination = Configuration::get('EC_SEO_PAGINATION');
        $p1 = ($page*$pagination)-$pagination;
        $p2 = $pagination;
        $req = 'SELECT * FROM '._DB_PREFIX_.'ec_seo WHERE type="'.pSQL($obj).'" AND idshop='.(int)$id_ss;
        $searchs= array();
        $rsearch = false;
        if ($search != null) {
            parse_str($search, $searchs);
            if (!empty($searchs['idP'])) {
                $req .= ' AND idP like "%'.(int)$searchs['idP'].'%"';
                $rsearch = true;
            }
            if (!empty($searchs['nameS'])) {
                $req .= ' AND nameS like "%'.pSQL($searchs['nameS']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['idL'])) {
                $req .= ' AND idL = '.(int)$searchs['idL'];
                $rsearch = true;
            }
            if (!empty($searchs['lienS'])) {
                $req .= ' AND lienS like "%'.pSQL($searchs['lienS']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['typeRed'])) {
                $req .= ' AND typeRed = "'.pSQL($searchs['typeRed']).'"';
                $rsearch = true;
            }
        }
        $countInfo = Db::getInstance()->getValue(str_replace('SELECT *', 'SELECT count(id)', $req));
        $req.= ' LIMIT '.$p1.','.$p2;
        
        $ads = Db::getInstance()->ExecuteS($req);
        //echo 'SELECT * FROM '._DB_PREFIX_.'ec_seo WHERE type="'.pSQL($obj).'" AND idshop='.(int)$id_ss.' LIMIT '.$p1.','.$p2;
        //exit();
        
        
        $nbpage = ($countInfo/$pagination);
        if (is_float($nbpage)) {
            $nbpage = (int)$nbpage + 1;
        }

        $infosmarty = array();
        if (count($ads) > 0) {
            $in = '';
            foreach ($ads as $ad) {
                $in .= (int)$ad['idP'].',';
            }
            $in = Tools::substr($in, 0, -1);
            if ($obj == 'Product') {
                $active_tab = Db::getInstance()->executeS('SELECT id_product, active FROM '._DB_PREFIX_.'product_shop WHERE id_product IN ('.pSQL($in).') AND id_shop='.(int)$id_ss);
            } else {
                $active_tab = Db::getInstance()->executeS('SELECT id_'.pSQL($obj_min).', active FROM '._DB_PREFIX_.''.pSQL($obj_min).' WHERE id_'.pSQL($obj_min).' IN ('.pSQL($in).')');
            }
            $active_tab2 = array();
            foreach ($active_tab as $active) {
                $active_tab2[$active['id_'.$obj_min]] = $active['active'];
            }
            $infosmarty = array();
            foreach ($ads as $ad) {
                $act = '';
                if (array_key_exists($ad['idP'], $active_tab2)) {
                    $act = $active_tab2[$ad['idP']];
                }
                
                $preview_url = Tools::getHttpHost(true).__PS_BASE_URI__.'index.php?id_'.$obj_min.'='.(int)$ad['idP'].'&controller='.Tools::strtolower($obj).'&id_lang='.$ad['idL'];
                $infosmarty[] = array($ad,$act,$preview_url);
            }
        }
        
        $this->smarty->assign(array(
                "obj" => $obj,
                "obj_min" => $obj_min,
                "infosmarty" => $infosmarty,
                "__PS_BASE_URI__" => Tools::getHttpHost(true).__PS_BASE_URI__,
                "countads" => count($ads),
                'countInfo' => $countInfo,
                'pagination' => $pagination,
                'nbpage' => $nbpage,
                'pageActif' => $page,
                'ajax' => $ajax,
                'languages' => Language::getLanguages(true, (int)$this->context->shop->id),
                'searchs' => $searchs,
                'rsearch' => $rsearch
                        ));
        return $this->display(__FILE__, 'views/templates/admin/tab.tpl');
    }

    public function option($obj, $id_ss)
    {
        $html_tmp = '';
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $fields_form = array(
                'form' => array(
                    'legend' => array(
                            'title' => $this->l('Filter'),
                            'icon' => 'icon-filter'
                    ),
                    'input' => array(
                        array(
                                'type' => 'switch',
                                'label' => $this->l('Serious'),
                                'name' => 'display_rgb(255, 78, 64)_'.$obj,
                                'is_bool' => true,
                                'class' => 'displayBtn',
                                'desc' => $this->l('Red lines'),
                                'values' => array(
                                        array(
                                                'id' => 'active_on',
                                                'value' => 1,
                                                'label' => $this->l('Yes'),
                                                'selected' => 'selected'
                                        ),
                                        array(
                                                'id' => 'active_off',
                                                'value' => 0,
                                                'label' => $this->l('No')
                                        )
                                ),
                        ),
                        array(
                                'type' => 'switch',
                                'label' => $this->l('To valid'),
                                'name' => 'display_rgb(56, 224, 93)_'.$obj,
                                'is_bool' => true,
                                'class' => 'displayBtn',
                                'desc' => $this->l('Green lines'),
                                'values' => array(
                                        array(
                                                'id' => 'active_on',
                                                'value' => 1,
                                                'label' => $this->l('Yes'),
                                                'selected' => 'selected'
                                        ),
                                        array(
                                                'id' => 'active_off',
                                                'value' => 0,
                                                'label' => $this->l('No')
                                        )
                                ),
                        ),
                        array(
                                'type' => 'switch',
                                'label' => $this->l('Blocking'),
                                'name' => 'display_rgb(255, 220, 64)_'.$obj,
                                'is_bool' => true,
                                'class' => 'displayBtn',
                                'desc' => $this->l('Yellow lines'),
                                'values' => array(
                                        array(
                                                'id' => 'active_on',
                                                'value' => 1,
                                                'label' => $this->l('Yes'),
                                                'selected' => 'selected'
                                        ),
                                        array(
                                                'id' => 'active_off',
                                                'value' => 0,
                                                'label' => $this->l('No')
                                        )
                                ),
                        ),
                        array(
                                'type' => 'switch',
                                'label' => $this->l('Ok'),
                                'name' => 'display_rgb(255, 255, 255)_'.$obj,
                                'is_bool' => true,
                                'class' => 'displayBtn',
                                'desc' => $this->l('White lines'),
                                'values' => array(
                                        array(
                                                'id' => 'active_on',
                                                'value' => 1,
                                                'label' => $this->l('Yes'),
                                                'selected' => 'selected'
                                        ),
                                        array(
                                                'id' => 'active_off',
                                                'value' => 0,
                                                'label' => $this->l('No')
                                        )
                                ),
                        ),
                        array(
                                'type' => 'switch',
                                'label' => $this->l('View deleted redirects'),
                                'name' => 'display_rgb(128, 128, 128)_'.$obj,
                                'is_bool' => true,
                                'class' => 'displayBtn',
                                'desc' => $this->l('Grey lines'),
                                'values' => array(
                                        array(
                                                'id' => 'active_on',
                                                'value' => 1,
                                                'label' => $this->l('Yes')
                                        ),
                                        array(
                                                'id' => 'active_off',
                                                'value' => 0,
                                                'label' => $this->l('No'),
                                                'selected' => 'selected'
                                        )
                                ),
                        )
                    ),
                    'buttons' => array(
                                'newBlock' => array(
                                    'title' => $this->l('Cancel'),
                                    'class' => 'pull-left hiddenAddForm',
                                    'icon' => 'process-icon-cancel',
                                    'name' => 'filter'
                                )
                        ),
                ),
            );
        } else {
            $fields_form = array(
                'form' => array(
                    'legend' => array(
                            'title' => $this->l('Display options'),
                            'icon' => 'icon-cogs'
                    ),
                    'input' => array(
                        array(
                                'type' => 'radio',
                                'label' => $this->l('Serious'),
                                'name' => 'display_rgb(255, 78, 64)_'.$obj,
                                'is_bool' => true,
                                'class' => 't displayBtn',
                                'desc' => $this->l('Red lines'),
                                'values' => array(
                                    array(
                                        'id' => 'display_rgb(255, 78, 64)_'.$obj.'_on',
                                        'value' => 1,
                                        'label' => $this->l('Yes'),
                                        'selected' => 'selected'
                                    ),
                                    array(
                                        'id' => 'display_rgb(255, 78, 64)_'.$obj.'_off',
                                        'value' => 0,
                                        'label' => $this->l('No')
                                    )
                                )
                        ),
                        array(
                                'type' => 'radio',
                                'label' => $this->l('To valid'),
                                'name' => 'display_rgb(56, 224, 93)_'.$obj,
                                'is_bool' => true,
                                'class' => 't displayBtn',
                                'desc' => $this->l('Green lines'),
                                'values' => array(
                                    array(
                                        'id' => 'display_rgb(56, 224, 93)_'.$obj.'_on',
                                        'value' => 1,
                                        'label' => $this->l('Yes'),
                                        'selected' => 'selected'
                                    ),
                                    array(
                                        'id' => 'display_rgb(56, 224, 93)_'.$obj.'_off',
                                        'value' => 0,
                                        'label' => $this->l('No')
                                    )
                                )
                        ),
                        array(
                                'type' => 'radio',
                                'label' => $this->l('Blocking'),
                                'name' => 'display_rgb(255, 220, 64)_'.$obj,
                                'is_bool' => true,
                                'class' => 't displayBtn',
                                'desc' => $this->l('Yellow lines'),
                                'values' => array(
                                    array(
                                        'id' => 'display_rgb(255, 220, 64)_'.$obj.'_on',
                                        'value' => 1,
                                        'label' => $this->l('Yes'),
                                        'selected' => 'selected'
                                    ),
                                    array(
                                        'id' => 'display_rgb(255, 220, 64)_'.$obj.'_off',
                                        'value' => 0,
                                        'label' => $this->l('No')
                                    )
                                )
                        ),
                        array(
                                'type' => 'radio',
                                'label' => $this->l('Ok'),
                                'name' => 'display_rgb(255, 255, 255)_'.$obj,
                                'is_bool' => true,
                                'class' => 't displayBtn',
                                'desc' => $this->l('White lines'),
                                'values' => array(
                                    array(
                                        'id' => 'display_rgb(255, 255, 255)_'.$obj.'_on',
                                        'value' => 1,
                                        'label' => $this->l('Yes'),
                                        'selected' => 'selected'
                                    ),
                                    array(
                                        'id' => 'display_rgb(255, 255, 255)_'.$obj.'_off',
                                        'value' => 0,
                                        'label' => $this->l('No')
                                    )
                                )
                        ),
                        array(
                                'type' => 'radio',
                                'label' => $this->l('View deleted redirects'),
                                'name' => 'display_rgb(128, 128, 128)_'.$obj,
                                'is_bool' => true,
                                'class' => 't',
                                'desc' => $this->l('Grey lines'),
                                'values' => array(
                                    array(
                                        'id' => 'display_rgb(128, 128, 128)_'.$obj.'_on',
                                        'value' => 1,
                                        'class' => 'displayBtn',
                                        'label' => $this->l('Yes')
                                    ),
                                    array(
                                        'id' => 'display_rgb(128, 128, 128)_'.$obj.'_off',
                                        'value' => 0,
                                        'class' => 'displayBtn',
                                        'label' => $this->l('No'),
                                        'selected' => 'selected'
                                    )
                                )
                        )
                    ),
                    'buttons' => array(
                                'newBlock' => array(
                                    'title' => $this->l('Cancel'),
                                    'class' => 'pull-left hiddenAddForm',
                                    'icon' => 'process-icon-cancel',
                                    'name' => 'filter'
                                )
                        ),
                ),
            );
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'addURL';
        $helper->currentIndex
        = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->override_folder = '/';
        $helper->tpl_vars = array('fields_value' => array( 'display_rgb(255, 78, 64)_'.$obj => 1,
                                                            'display_rgb(56, 224, 93)_'.$obj => 1,
                                                            'display_rgb(255, 220, 64)_'.$obj => 1,
                                                            'display_rgb(255, 255, 255)_'.$obj => 1,
                                                            'display_rgb(128, 128, 128)_'.$obj => 1));
        $html_tmp .= $helper->generateForm(array($fields_form));
        return $html_tmp;
    }


    public function add($obj, $onglet)
    {
        $html_tmp = '';
        $onglet = $onglet + 1;
        
        $option = array(
                array(
                    'id_option' => '404',
                    'name' => $this->l('404 Not Found')
                    ),
                array(
                    'id_option' => '301',
                    'name' => $this->l('301 Moved Permanently')
                    ),
                array(
                    'id_option' => '302',
                    'name' => $this->l('302 Moved Temporarily')
                    ),
                array(
                    'id_option' => 'homepage',
                    'name' => $this->l('Home page')
                    )
                );
    
        $fields_form = array(
            'form' => array(
                    'legend' => array(
                        'title' => $this->l('Add'),
                        'icon' => 'icon-cogs'
                        ),
                    'input' => array(
                            array(
                                'type' => 'text',
                                'col' => 3,
                                'id' => 'addID'.$obj,
                                'label' => $this->l('ID'),
                                'name' => 'addID'.$obj,
                            ),
                            array(
                                'type' => 'text',
                                'col' => 6,
                                'id' => 'addName'.$obj,
                                'label' => $this->l('Name'),
                                'name' => 'addName'.$obj,
                                'lang' => true,
                            ),
                            array(
                                'type' => 'text',
                                'col' => 6,
                                'id' => 'addURL'.$obj,
                                'label' => $this->l('Link'),
                                'name' => 'addURL'.$obj,
                                'lang' => true,
                            ),
                            array(
                                'type' => 'hidden',
                                'name' => 'onglet'
                            ),
                            array(
                                'type' => 'select',
                                'name' => 'addtypeRedirect'.$obj,
                                'id' => 'addtypeRedirect'.$obj,
                                'class' => 'addtypeRedirectLink'.($obj == 'Product'?$obj:'Autre'),
                                'label' => $this->l('Redirection type'),
                                'options' => array(
                                    'query' => $option,
                                        'id' => 'id_option',
                                        'name' => 'name'
                                )
                            )
                        ),
                    'buttons' => array(
                                'newBlock' => array(
                                    'title' => $this->l('Cancel'),
                                    'class' => 'pull-left hiddenAddForm',
                                    'icon' => 'process-icon-cancel',
                                    'name' => $obj
                                )
                        ),
                    'submit' => array(
                        'title' => $this->l('Add'),
                        'class' => 'submit_add_'.$obj.'_ btn btn-default pull-right'
                        )
                )
            );
            $helper = new HelperForm();
            $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
            $helper->module = $this;
            $helper->name_controller = 'ec_seo';
            $helper->identifier = $this->identifier;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }
    
            $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
            $helper->default_form_language = $default_lang;
            $helper->allow_employee_form_lang = $default_lang;
            $helper->toolbar_scroll = true;
            $helper->title = $this->displayName;
            $helper->submit_action = 'addURL';

            $helper->fields_value['addID'.$obj] = '';
            $helper->fields_value['onglet'] = '';
            $helper->fields_value['language'.$obj] = '';
            $helper->fields_value['addtypeRedirect'.$obj] = '';
        foreach (Language::getLanguages(false) as $lang) {
            $helper->fields_value['addName'.$obj][(int)$lang['id_lang']] = '';
            $helper->fields_value['addURL'.$obj][(int)$lang['id_lang']] = '';
        }
            $helper->override_folder = '/';
            $html_tmp .= $helper->generateForm(array($fields_form));
            
            $onglet = $onglet - 1;
            return $html_tmp;
    }
    
    public function uploadTabDisplay($id_ss, $page = null, $ajax = false, $search = null)
    {
        $req = 'SELECT * FROM '._DB_PREFIX_.'ec_seo_redirect WHERE id_shop='.(int)$id_ss;
        if ($page == null) {
            $page = 1;
        }
        $pagination = Configuration::get('EC_SEO_PAGINATION');
        $p1 = ($page*$pagination)-$pagination;
        $p2 = $pagination;
        $req = 'SELECT * FROM '._DB_PREFIX_.'ec_seo_redirect WHERE id_shop='.(int)$id_ss;
        $searchs= array();
        $rsearch = false;
        if ($search != null) {
            parse_str($search, $searchs);
            if (!empty($searchs['old_link'])) {
                $req .= ' AND old_link like "%'.pSQL($searchs['old_link']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['lienS'])) {
                $req .= ' AND lienS like "%'.pSQL($searchs['lienS']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['typeRed'])) {
                $req .= ' AND typeRed = "'.pSQL($searchs['typeRed']).'"';
                $rsearch = true;
            }
        }
        $countInfo = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.$p1.','.$p2;
        $ads = Db::getInstance()->ExecuteS($req);
        $nbpage = ($countInfo/$pagination);
        if (is_float($nbpage)) {
            $nbpage = (int)$nbpage + 1;
        }
        $infosmarty = array();
        if (count($ads) > 0) {
            foreach ($ads as $ad) {
                $infosmarty[] = array($ad);
            }
        }
        $this->smarty->assign(array(
            'obj' => 'other',
            "infosmarty" => $infosmarty,
            "__PS_BASE_URI__" => Tools::getHttpHost(true).__PS_BASE_URI__,
            "countads" => count($ads),
            'countInfo' => $countInfo,
            'pagination' => $pagination,
            'nbpage' => $nbpage,
            'pageActif' => $page,
            'ajax' => $ajax,
            'languages' => Language::getLanguages(true, (int)$this->context->shop->id),
            'searchs' => $searchs,
            'rsearch' => $rsearch
        ));
        return $this->display(__FILE__, 'views/templates/admin/uploadtabdisplay.tpl');
    }
    
    public function addCSV($onglet)
    {
        $html_tmp = '';
        $oh = '<';
        $ch = '>';
        $sh = '/';
        $brh= $oh.'br'.$ch;
        if ($onglet == 6) {
            $desc = $this->l('Respect the same rule like adding one redirection').$brh.'
            '.$this->l('Structure your csv file like this, for each ligne : old_url;new_url;redirection_type;').$brh.'
            '.$this->l('Redirection type can be : "301", "302" or "homepage"').$brh.'
            '.$oh.'a href="../modules/ec_seo/data/import_url.csv"'.$ch.$this->l('Click here to download an csv exemple file').$oh.$sh.'a'.$ch;
        } else {
            $desc = $this->l('Structure your csv file like this, for each ligne : id;id_lang;name;redirection_type;new_url;').$brh.'
            '.$this->l('Redirection type can be : "301", "302" or "homepage", if you choose the "homepage" redirection you don\'t need new url but wrote an empty input ";;"').$brh.'
            '.$oh.'a href="../modules/ec_seo/data/import_page.csv">'.$this->l('Click here to download an csv exemple file').$oh.$sh.'a'.$ch;
        }
        $fields_form = array(
            'form' => array(
                    'legend' => array(
                        'title' => $this->l('Import massive redirection'),
                        'icon' => 'icon-cogs'
                        ),
                    'input' => array(
                            array(
                                'type' => 'file',
                                'id' => 'file'.$onglet,
                                'label' => $this->l('Select a CSV file'),
                                'name' => 'file',
                                'desc' => $desc
                            ),
                            array(
                                'type' => 'hidden',
                                'name' => 'onglet'
                            ),
                        ),
                    'buttons' => array(
                                'newBlock' => array(
                                    'title' => $this->l('Cancel'),
                                    'class' => 'pull-left hiddenAddForm',
                                    'icon' => 'process-icon-cancel',
                                    'name' => 'import'
                                )
                        ),
                    'submit' => array(
                        'title' => $this->l('Add'),
                        'class' => 'submit_import_add btn btn-default pull-right'
                        )
                )
            );
            $helper = new HelperForm();
            $helper->module = $this;
            $helper->name_controller = 'ec_seo';
            $helper->identifier = $this->identifier;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
            $helper->toolbar_scroll = true;
            $helper->title = $this->displayName;
            $helper->submit_action = 'importCsv';
            $helper->fields_value['onglet'] = $onglet;
            $helper->override_folder = '/';
            $html_tmp .= $helper->generateForm(array($fields_form));

            return $html_tmp;
    }
    
    public function addRed($onglet)
    {
        $html_tmp = '';
        
        $option = array(
                array(
                    'id_option' => '404',
                    'name' => $this->l('404 Not Found')
                    ),
                array(
                    'id_option' => '301',
                    'name' => $this->l('301 Moved Permanently')
                    ),
                array(
                    'id_option' => '302',
                    'name' => $this->l('302 Moved Temporarily')
                    ),
                array(
                    'id_option' => 'homepage',
                    'name' => $this->l('Home page')
                    )
                );
    
        $fields_form = array(
            'form' => array(
                    'legend' => array(
                        'title' => $this->l('Add'),
                        'icon' => 'icon-cogs'
                        ),
                    'input' => array(
                            array(
                                'type' => 'text',
                                'col' => 6,
                                'id' => 'addOldUrl',
                                'label' => $this->l('Old link'),
                                'name' => 'addOldUrl',
                                'desc' => $this->l('Write the complete old url, for exemple : "http://www.shop.com/en/old_page.html"')
                            ),
                            array(
                                'type' => 'text',
                                'col' => 6,
                                'id' => 'addNewUrl',
                                'label' => $this->l('New Link'),
                                'name' => 'addNewUrl',
                                'desc' => $this->l('Write the complete new url, for exemple : "http://www.shop.com/en/new_page.html"')
                            ),
                            array(
                                'type' => 'hidden',
                                'name' => 'onglet'
                            ),
                            array(
                                'type' => 'select',
                                'name' => 'addtypeRedirectUrl',
                                'id' => 'addtypeRedirectUrl',
                                'class' => 'addtypeRedirectLinkUrl',
                                'label' => $this->l('Redirection type'),
                                'desc' => $this->l('If you choose the "home page" redirection you don\'t need new url'),
                                'options' => array(
                                    'query' => $option,
                                        'id' => 'id_option',
                                        'name' => 'name'
                                )
                            )
                        ),
                    'buttons' => array(
                                'newBlock' => array(
                                    'title' => $this->l('Cancel'),
                                    'class' => 'pull-left hiddenAddForm',
                                    'icon' => 'process-icon-cancel',
                                    'name' => 'url'
                                )
                        ),
                    'submit' => array(
                        'title' => $this->l('Add'),
                        'class' => 'submit_url_add btn btn-default pull-right'
                        )
                )
            );
            $helper = new HelperForm();
            $helper->module = $this;
            $helper->name_controller = 'ec_seo';
            $helper->identifier = $this->identifier;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
            $helper->toolbar_scroll = true;
            $helper->title = $this->displayName;
            $helper->submit_action = 'addURLspe';

            $helper->fields_value['addOldUrl'] = '';
            $helper->fields_value['onglet'] = $onglet;
            $helper->fields_value['addNewUrl'] = '';
            $helper->fields_value['addtypeRedirectUrl'] = '';
            $helper->override_folder = '/';
            $html_tmp .= $helper->generateForm(array($fields_form));

            return $html_tmp;
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookActionAdminControllerSetMedia()
    {
       // echo Tools::getValue('controller');
        $list = array('AdminEcSeoCategory', 'AdminEcSeoProduct', 'AdminEcSeoCms', 'AdminEcSeoSupplier', 'AdminEcSeoManufacturer', 'AdminEcSeoMeta');
        $controller = Tools::getValue('controller');
        $id_shop = (int)$this->context->shop->id;
        if (in_array($controller, $list)) {
            $class = str_replace('AdminEcSeo', '', $controller);
            $type = Tools::strtolower($class);
            if ($class == 'Cms') {
                $class = 'CMS';
            }
            $tab_ec_seo_iso = array();
            $tab_ec_seo_id = array();
            $tab_ec_seo = array();
            $languages = Language::getLanguages(false);
            $ec_seo_monolangue = false;
            if (count($languages) == 1) {
                $ec_seo_monolangue = true;
            }
            $id = Tools::getValue('id_'.$type);
            if ($type == 'product') {
                $obj = new $class($id, false, null, $id_shop);
            } else {
                $obj = new $class($id, null, $id_shop);
            }
            $tab_keyword = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                $id_seo = Db::getInstance()->getValue('SELECT id_seo_'.$type.' FROM '._DB_PREFIX_.'ec_seo_'.$type.' WHERE id_'.$type.' = '.(int)$id.' AND id_shop = '.(int)$id_shop);
                $keyword =  Db::getInstance()->getValue('SELECT keyword FROM '._DB_PREFIX_.'ec_seo_'.$type.'_lang WHERE id_seo_'.$type.' = '.(int)$id_seo.' AND id_lang = '.(int)$id_lang);
                $tab_keyword[$id_lang] = $keyword;
                if ($type == 'meta') {
                    $obj->meta_title[$id_lang] = $obj->title[$id_lang];
                    $obj->meta_description[$id_lang] = $obj->description[$id_lang];
                    $obj->link_rewrite = $obj->url_rewrite;
                }
                $meta_title = $obj->meta_title[$id_lang];
                if ((Tools::strlen($obj->meta_title[$id_lang])> 0) == false && $type != 'meta') {
                    $meta_title = $obj->name[$id_lang];
                }
                $link_rewrite = $obj->link_rewrite[$id_lang];
                if ($type == 'manufacturer' || $type == 'supplier') {
                    $name = $obj->name;
                    $link_rewrite = $obj->link_rewrite;
                    $meta_title = $obj->name;
                }
                $link = $this->getLinkByObj($obj, $id_lang, $id_shop);
                $url = str_replace($link_rewrite, '%rewrite%', $link);
                $tab_ec_seo[$id_lang]['meta_title'] = $meta_title;
                $tab_ec_seo[$id_lang]['meta_description'] = $obj->meta_description[$id_lang];
                $tab_ec_seo[$id_lang]['url'] = $url;
                $tab_ec_seo[$id_lang]['link_rewrite'] = $link_rewrite;
                $tab_ec_seo[$id_lang]['urllen'] = Tools::strlen($url);
                //$tab_ec_seo[$lang['id_lang']]['base_link'] = $category_link;
                $tab_ec_seo_iso[$lang['iso_code']] = $id_lang;
                $tab_ec_seo_id[$id_lang] = $lang['iso_code'];
            }

            if ($class == 'CMS') {
                $class = 'Cms';
            }
            $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
            $dateUpdateDateKeyword = Db::getInstance()->getValue('SELECT date_upd FROM '._DB_PREFIX_.'ec_seo_smartkeyword WHERE page = "'.pSQL($type).'" AND id = '.(int)$id.' AND id_shop = '.(int)$id_shop.' AND id_lang = '.(int)$id_lang_default);
            if (!$dateUpdateDateKeyword) {
                $dateUpdateDateKeyword = '';
            }
            Media::addJsDef(
                array(
                    'tab_ec_seo_iso' => $tab_ec_seo_iso,
                    'tab_ec_seo_id' => $tab_ec_seo_id,
                    'tab_ec_seo' => $tab_ec_seo,
                    'EcSeoController' => $this->context->link->getAdminLink($controller, true),
                    'ps_force_friendly_product' => false,
                    'PS_ALLOW_ACCENTED_CHARS_URL' => (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
                    'ec_id' => Tools::getValue('id_'.$type),
                    'ec_tab_keyword' => $tab_keyword,
                    'ec_class_mini' => 'adminecseo'.$type,
                    'ec_class_maj' => 'AdminEcSeo'.$class,
                    'ec_type' => $type,
                    'ec_trad_breadcrumb' => pSQL($this->l('Breadcrumb')),
                    'ec_mess_breadcrumb' => $this->l('Check that your breadcrumb is present.'),
                    'ec_seo_monolangue' => $ec_seo_monolangue,
                    'ec_ps_version17' => version_compare(_PS_VERSION_, '1.7', '>='),
                    'ec_trad_refreshDataKeyword' => $this->l('Refresh the data related to the keyword '),
                    'ec_trad_dateupdate' => $this->l('Date of update:'),
                    'dateUpdateDateKeyword' => $dateUpdateDateKeyword,
                    'ec_ps_base' => Tools::getHttpHost(true).__PS_BASE_URI__,
                    'EC_TOKEN_SEO' => $this->ec_token,
                    'ec_seo_ajax' => $this->context->link->getModuleLink('ec_seo', 'ajax'),
                    'ec_trad_searchK' => $this->l('Search google with the keyword ...'),
                    'ec_trad_analysis' => $this->l('Analysis of the result ...'),
                    'ec_trad_retieving' => $this->l('Retrieving H1s, meta titles, meta descriptions ...'),
                    'ec_trad_preview' => $this->l('Here is a preview of your page as a search result.'),
                    'ec_id_lang_default' => $id_lang_default,
                    'ec_base_uri' => Tools::getHttpHost(true).__PS_BASE_URI__
                )
            );
            $this->context->controller->addJS($this->_path.'views/js/checkSeoBack.js');
        }
        
        $ec_seo_href = false;
        if (Tools::getValue('controller') == 'AdminCategories') {
            $id_category = Tools::getValue('id_category');
            if (!$id_category) {
                if (isset($_SERVER['PATH_INFO'])) {
                    if (strpos($_SERVER['PATH_INFO'], 'edit') !== false) {
                        $id_category = explode('/', $_SERVER['PATH_INFO'])[4];
                    }
                }
            }
            if ($id_category) {
                $ec_seo_href = $this->context->link->getAdminLink('AdminEcSeoCategory', true).'&id_category='.(int)$id_category;
                $ec_seo_total_score = $this->getGlobalNote('category', $id_category, (int)$this->context->shop->id)['global'];
            }
            $this->context->controller->addJS(($this->_path).'views/js/cleanHtmlGrid.js');
        }
        if (Tools::getValue('controller') == 'AdminProducts') {
            $id_product = Tools::getValue('id_product');
            if (!$id_product) {
                if (isset($_SERVER['PATH_INFO'])) {
                    $tab_path = explode('/', $_SERVER['PATH_INFO']);
                    if (isset($tab_path[4])) {
                        $id_product = $tab_path[4];
                    }
                }
            }
            if ($id_product) {
                $ec_seo_href = $this->context->link->getAdminLink('AdminEcSeoProduct', true).'&id_product='.(int)$id_product;
                $ec_seo_total_score = $this->getGlobalNote('product', $id_product, (int)$this->context->shop->id)['global'];
            }
            $this->context->controller->addJS(($this->_path).'views/js/cleanHtmlGrid.js');
        }
        if (Tools::getValue('controller') == 'AdminCmsContent') {
            $id_cms = Tools::getValue('id_cms');
            if (!$id_cms) {
                if (isset($_SERVER['PATH_INFO'])) {
                    if (strpos($_SERVER['PATH_INFO'], 'edit') !== false) {
                        $id_cms = explode('/', $_SERVER['PATH_INFO'])[4];
                    }
                }
            }
            if ($id_cms) {
                $ec_seo_href = $this->context->link->getAdminLink('AdminEcSeoCms', true).'&id_cms='.(int)$id_cms;
                $ec_seo_total_score = $this->getGlobalNote('cms', $id_cms, (int)$this->context->shop->id)['global'];
            }
        }
        if (Tools::getValue('controller') == 'AdminManufacturers') {
            $id_manufacturer = Tools::getValue('id_manufacturer');
            if (!$id_manufacturer) {
                if (isset($_SERVER['PATH_INFO'])) {
                    if (strpos($_SERVER['PATH_INFO'], 'edit') !== false) {
                        $id_manufacturer = explode('/', $_SERVER['PATH_INFO'])[4];
                    }
                }
            }
            if ($id_manufacturer) {
                $ec_seo_href = $this->context->link->getAdminLink('AdminEcSeoManufacturer', true).'&id_manufacturer='.(int)$id_manufacturer;
                $ec_seo_total_score = $this->getGlobalNote('manufacturer', $id_manufacturer, (int)$this->context->shop->id)['global'];
            }
        }

        if (Tools::getValue('controller') == 'AdminSuppliers') {
            $id_supplier = Tools::getValue('id_supplier');
            if (!$id_supplier) {
                if (isset($_SERVER['PATH_INFO'])) {
                    if (strpos($_SERVER['PATH_INFO'], 'edit') !== false) {
                        $id_supplier = explode('/', $_SERVER['PATH_INFO'])[4];
                    }
                }
            }
            if ($id_supplier) {
                $ec_seo_href = $this->context->link->getAdminLink('AdminEcSeoSupplier', true).'&id_supplier='.(int)$id_supplier;
                $ec_seo_total_score = $this->getGlobalNote('supplier', $id_supplier, (int)$this->context->shop->id)['global'];
            }
        }

        if (Tools::getValue('controller') == 'AdminMeta') {
            $id_meta = Tools::getValue('id_meta');
            if (!$id_meta) {
                if (isset($_SERVER['PATH_INFO'])) {
                    if (strpos($_SERVER['PATH_INFO'], 'edit') !== false) {
                        $id_meta = explode('/', $_SERVER['PATH_INFO'])[4];
                    }
                }
            }
            if ($id_meta) {
                $ec_seo_href = $this->context->link->getAdminLink('AdminEcSeoMeta', true).'&id_meta='.(int)$id_meta;
                $ec_seo_total_score = $this->getGlobalNote('meta', $id_meta, (int)$this->context->shop->id)['global'];
            }
        }


        if ($ec_seo_href) {
            Media::addJsDef(
                array(
                    'ec_seo_href' => $ec_seo_href,
                    'ec_seo_total_score' => $ec_seo_total_score,
                )
            );
            $this->context->controller->addJS($this->_path.'views/js/button_seo.js');
        }
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
        
        if (Tools::getValue('configure') == $this->name) {
            $footers_spe = Db::getInstance()->executes(
                '
                SELECT id, type FROM '._DB_PREFIX_.'ec_seo_footer 
                WHERE spe = 1
                AND id_shop = '.(int)$id_shop.'
                '
            );
            $tab_link_preview_footer = array();
            foreach ($footers_spe as $footer_spe) {
                $link_preview = $this->getPreviewLinkFooter($footer_spe['type'], $footer_spe['id']);
                if ($link_preview) {
                    $tab_link_preview_footer[$footer_spe['id']] = $link_preview;
                }
            }
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                $tab_ec_seo_iso[$lang['iso_code']] = $lang['id_lang'];
                $tab_ec_seo_id[$lang['id_lang']] = $lang['iso_code'];
            }
            $hasReport = Db::getInstance()->getValue('SELECT `date` FROM '._DB_PREFIX_.'ec_seo_report WHERE id_shop = '.(int)$this->context->shop->id);
            Media::addJsDef(
                array(
                    'EC_TOKEN_SEO' => $this->ec_token,
                    'tab_ec_seo_iso' => $tab_ec_seo_iso,
                    'ec_id_shop' => (int)$this->context->shop->id,
                    'ec_current_ip' => $_SERVER['REMOTE_ADDR'],
                    'ec_add_ip' => $this->l('Add my ip'),
                    'ec_trad_history' => $this->l('History'),
                    'ec_hasReport' => $hasReport,
                    'ec_ps_shop_default' => Configuration::get('PS_SHOP_DEFAULT'),
                    'ec_id_employee' => $this->context->employee->id,
                    'ec_href' => $_SERVER['REQUEST_URI'],
                    'ec_ps_base' => __PS_BASE_URI__,
                    'ec_mess_update' => $this->l('Successful update'),
                    'ec_tab_link_preview_footer' => $tab_link_preview_footer,
                    'ec_mess_preview' => $this->l('Preview'),
                    'ec_mess_enabled' => $this->l('Enabled'),
                    'ec_mess_disabled' => $this->l('Disabled'),
                    'ec_seo_ajax' => $this->context->link->getModuleLink('ec_seo', 'ajax')
                )
            );
            $this->context->controller->addCSS($this->_path.'views/css/back_module.css');
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->context->controller->addCSS($this->_path.'views/css/back16.css');
            }
            $this->context->controller->addJS(($this->_path).'views/js/redirection.js');
            $this->context->controller->addJS(($this->_path).'views/js/task.js');
            $this->context->controller->addJS(($this->_path).'views/js/redirection_image.js');
            $this->context->controller->addJS(($this->_path).'views/js/chart.js');
            $this->context->controller->addJS(($this->_path).'views/js/jquery-ui.js');
            $this->context->controller->addJS(($this->_path).'views/js/config.js');
            if ($hasReport) {
                $this->context->controller->addJS(($this->_path).'views/js/gauge.min.js');
                $admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
                $admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
                $this->context->controller->addJS(_PS_JS_DIR_.'vendor/d3.v3.min.js');
                $this->context->controller->addJS(__PS_BASE_URI__.$admin_webpath.'/themes/default/js/vendor/nv.d3.min.js');
            }
        }
    }

    public function hookEcSeoDescription2()
    {
     
        $page = Tools::getValue('page');
        if (!$page) {
            $page = 1;
        }
        if (Tools::getValue('controller') == 'category' && (int)$page < 2) {
            $id_category = Tools::getValue('id_category');
            $id_lang = (int)$this->context->language->id;
            $description2 = Db::getInstance()->getValue('SELECT description2 FROM '._DB_PREFIX_.'ec_seo_category esc
            LEFT JOIN '._DB_PREFIX_.'ec_seo_category_lang escl ON (escl.id_seo_category = esc.id_seo_category)
            WHERE id_category = '.(int)$id_category.' and id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$this->context->shop->id);
            if (Tools::strlen($description2) > 0) {
                $this->smarty->assign(array(
                    'description2' => $description2,
                ));
                return $this->display(__FILE__, 'views/templates/front/description2.tpl');
            }
        }
        $controller = Tools::getValue('controller');
        $meta_pages = array();
        $mps = Db::getInstance()->executes('SELECT id_meta, page FROM '._DB_PREFIX_.'meta WHERE configurable = 1');
        foreach ($mps as $mp) {
            $meta_pages[str_replace('-', '', $mp['page'])] =  $mp['id_meta'];
        }
        if (array_key_exists($controller, $meta_pages)) {
            $id_meta = $meta_pages[$controller];
            $id_lang = (int)$this->context->language->id;
            $description2 = Db::getInstance()->getValue('SELECT description2 FROM '._DB_PREFIX_.'ec_seo_meta esc
            LEFT JOIN '._DB_PREFIX_.'ec_seo_meta_lang escl ON (escl.id_seo_meta = esc.id_seo_meta)
            WHERE id_meta = '.(int)$id_meta.' and id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$this->context->shop->id);
            if (Tools::strlen($description2) > 0) {
                $this->smarty->assign(array(
                    'description2' => $description2,
                ));
                return $this->display(__FILE__, 'views/templates/front/description2.tpl');
            }
        }
    }
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        $display = false;
        $this->context->controller->addCSS($this->_path.'/views/css/footer.css');
        $EC_SEO_OG = Configuration::get('EC_SEO_OG');
        $seo_front = Configuration::get('EC_SEO_FRONT');
        $adresses_ip = explode(';', Configuration::get('EC_SEO_FRONT_IP'));
        $current_ip = $_SERVER['REMOTE_ADDR'];
        $show_seo = false;
        if ($seo_front && in_array($current_ip, $adresses_ip)) {
            $this->context->controller->addJS($this->_path.'/views/js/front.js');
            $this->context->controller->addCSS($this->_path.'/views/css/front.css');
            $show_seo = true;
        }
        $this->smarty->assign(array(
            'show_seo' => $show_seo,
        ));
        $id_lang = (int)$this->context->language->id;
        $iso_code = $this->context->language->iso_code;
        $tpl_vars = $this->context->smarty->tpl_vars;
        $id_shop = (int)$this->context->shop->id;
        $current_url = Tools::getHttpHost(true).$_SERVER['REQUEST_URI'];
        $open_graph = array(
            'og_site_name' => Configuration::get('EC_SEO_OG_SITE_NAME_'.$id_lang),
            'og_image' => Tools::getHttpHost(true).__PS_BASE_URI__.'img/'.Configuration::get('PS_LOGO'),
            'og_locale' => Configuration::get('EC_SEO_OG_LOCALE_'.$id_lang),
            'og_url' => Tools::getHttpHost(true).$_SERVER['REQUEST_URI'],
        );
        $list = array('product', 'category', 'cms', 'supplier', 'manufacturer');
        $controller = Tools::getValue('controller');
        /* echo $controller;
        echo Tools::getValue('id_meta'); */
        $id_manufacturer = false;
        $id_supplier = false;
        $id_category = false;
        if ($controller == 'manufacturer') {
            $id_manufacturer = Tools::getValue('id_manufacturer');
        }

        if ($controller == 'supplier') {
            $id_supplier = Tools::getValue('id_supplier');
        }

        if ($controller == 'category') {
            $id_category = Tools::getValue('id_category');
        }

        $id = false;
        $is_meta = false;
        $meta_pages = array();
        $mps = Db::getInstance()->executes('SELECT id_meta, page FROM '._DB_PREFIX_.'meta WHERE configurable = 1');
        foreach ($mps as $mp) {
            $meta_pages[str_replace('-', '', $mp['page'])] =  $mp['id_meta'];
        }
        if (array_key_exists($controller, $meta_pages) && !$id_manufacturer && !$id_supplier &&!$id_category && $controller != 'product') {
            $id = $meta_pages[$controller];
            $controller = 'meta';
            $is_meta = true;
        }
        if (in_array($controller, $list) || $is_meta) {
            $type = $controller;
            $class = $controller;
            if ($class == 'cms') {
                $class = 'CMS';
            } else {
                $class[0] = strtoupper($class[0]);
            }
            if (!$id) {
                $id = Tools::getValue('id_'.$type);
            }
            if ($type == 'product') {
                $obj = new Product($id, false, $id_lang, $id_shop);
                $EC_SEO_OG = false;
            } else {
                $obj = new $class($id, $id_lang, $id_shop);
            }
            $info_seo = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ec_seo_'.$type.' esc
            LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$type.'_lang escl ON (escl.id_seo_'.$type.' = esc.id_seo_'.$type.')
            WHERE id_'.$type.' = '.(int)$id.' and id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop);
            if (!$info_seo) {
                $this->initSeo($type, $id);
                $info_seo = Db::getInstance()->getRow(
                    'SELECT * FROM '._DB_PREFIX_.'ec_seo_'.$type.' esc
                    LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$type.'_lang escl ON (escl.id_seo_'.$type.' = esc.id_seo_'.$type.')
                    WHERE id_'.$type.' = '.(int)$id.' and id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop
                );
            } else {
                $this->checkSeoNewLang($obj, $type, $info_seo['id_seo_'.$type]);
                $info_seo = Db::getInstance()->getRow(
                    'SELECT * FROM '._DB_PREFIX_.'ec_seo_'.$type.' esc
                    LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$type.'_lang escl ON (escl.id_seo_'.$type.' = esc.id_seo_'.$type.')
                    WHERE id_'.$type.' = '.(int)$id.' and id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop
                );
            }
            if ($type == 'meta') {
                $obj->meta_title = $obj->title;
                $obj->meta_description = $obj->description;
                $obj->description = $info_seo['description2'];
                $obj->link_rewrite = $obj->url_rewrite;
            }
            if ($type == 'cms') {
                if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                    if (Tools::strlen($obj->head_seo_title) == 0) {
                        $obj->head_seo_title = $obj->meta_title;
                        $obj->meta_title = $obj->head_seo_title;
                    }
                }
                $obj->h1 = $obj->meta_title;
                $obj->description = $obj->content;
            }
            if (isset($obj->meta_title)) {
                if (Tools::strlen($obj->meta_title) == 0 && isset($obj->name)) {
                    $obj->meta_title = $obj->name;
                }
            }
            if (isset($obj->meta_description) && $type != 'meta' && $type != 'supplier' && $type != 'manufacturer') {
                if (Tools::strlen($obj->meta_description) == 0) {
                    /* foreach ($obj->description as $id_lang => &$val) {
                        $val = strip_tags($val);
                    } */
                    if ($type == 'product') {
                        $obj->meta_description = strip_tags($obj->description_short);
                    } else {
                        $obj->meta_description = strip_tags($obj->description);
                    }
                }
            }
            if ($type == 'category') {
                $obj->description = $obj->description.' '.$info_seo['description2'];
            }
            if ($type == 'product') {
                $obj->description = $obj->description_short.' '.$obj->description;
            }
            if ($type == 'manufacturer') {
                $obj->description = $obj->short_description.' '.$obj->description;
            }
            $use_og_title_default = Configuration::get('EC_SEO_OG_TITLE_DEFAULT');
            if ($use_og_title_default) {
                $og_title = $obj->meta_title;
            } else {
                if (Tools::strlen($info_seo['og_title']) > 0) {
                    $og_title = $info_seo['og_title'];
                }
            }
            $open_graph['og_title'] = $og_title;
        
            if (Tools::strlen($info_seo['og_image']) > 0) {
                $open_graph['og_image'] = $info_seo['og_image'];
            }
            $use_og_description_default = Configuration::get('EC_SEO_OG_DESCRPTION_DEFAULT');
            if ($use_og_description_default) {
                $og_description = $obj->meta_description;
            } else {
                if (Tools::strlen($info_seo['og_description']) > 0) {
                    $og_description = $info_seo['og_description'];
                }
            }

            $open_graph['og_description'] = $og_description;
            if ($type != 'cms' && isset($tpl_vars[$type])) {
                if (Tools::strlen($info_seo['h1']) > 0) {
                    $seo_h1 = Configuration::get('EC_SEO_H1');
                    if ($seo_h1) {
                        $tpl_vars[$type]->value['name'] = $info_seo['h1'];
                    }
                }
                $obj->h1 = $tpl_vars[$type]->value['name'];
            }
           
            
            $obj->keyword = $info_seo['keyword'];
            if (!$obj->keyword) {
                $obj->keyword = '';
            }
            
            $tab_meta = $this->checkFront($obj, $current_url);
            
            $this->smarty->assign(array(
                'open_graph' => $open_graph,
                'EC_SEO_OG' => $EC_SEO_OG,
                'ec_tab_meta' => $tab_meta,
                'category_rule' => $this->category_rule,
                'ec_keyword' => $obj->keyword,
                'rich_snippet' => 'https://search.google.com/test/rich-results?url='.urlencode($current_url),
                'ec_current_url' => $current_url,
                'type' => $type,
                'ec_ps_version17' => version_compare(_PS_VERSION_, '1.7', '>='),
            ));
            Media::addJsDef(
                array(
                    'ec_ps_version17' => version_compare(_PS_VERSION_, '1.7', '>='),
                )
            );
            $this->context->controller->addJS($this->_path.'/views/js/checkSeo.js');
            $this->context->controller->addJS($this->_path.'/views/js/progressbar.min.js');
            $display .= $this->display(__FILE__, 'views/templates/front/ec_seo.tpl');
        }
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $link2 = explode('?', $actual_link)[0];
        $noindex = Db::getInstance()->getValue('SELECT page FROM '._DB_PREFIX_.'ec_seo_page_noindex WHERE (page="'.pSQL($actual_link).'" OR page LIKE "'.pSQL($link2).'?%")');
        if ($noindex) {
            $display .= $this->display(__FILE__, 'views/templates/front/page_noindex.tpl');
        }
        return $display;
    }

    public function initSeo($type, $id)
    {
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $class = $type;
        $image = '';
        if ($type == 'product') {
            $class = 'Product';
            $obj = new Product($id, false, null, (int)$this->context->shop->id);
        } else if ($type == 'cms') {
            $class = 'CMS';
            $obj = new $class($id, null);
            $obj->name = $obj->meta_title;
            //$obj->meta_title = $obj->head_seo_title;
            $obj->description = $obj->content;
        } else {
            $class[0] = strtoupper($class[0]);
            $obj = new $class($id, null);
        }
        //$name =$obj->name[$id_lang_default];
        if ($type == 'category') {
            $image = $this->context->link->getCatImageLink('', $obj->id);
        }
        if ($type == 'manufacturer' || $type == 'supplier') {
            $funcImage = 'get'.$class.'ImageLink';
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $image = $this->context->link->{$funcImage}($obj->id);
            } else {
                $m_dir = $type[0];
                if ($type == 'supplier') {
                    $m_dir .= 'u';
                }
                $image = Tools::getHttpHost(true).__PS_BASE_URI__.'img/'.$m_dir.'/'.$obj->id.'.jpg';
            }
        }
        if (isset($obj->meta_title)) {
            if (Tools::strlen($obj->meta_title) == 0 && ($type != 'manufacturer' && $type != 'supplier')) {
                $obj->meta_title = $obj->name;
            }
        }
        if (isset($obj->meta_description)) {
            if (Tools::strlen($obj->meta_description) == 0) {
                foreach ($obj->description as $id_lang => &$val) {
                    $val = strip_tags($val);
                }
                $obj->meta_description = $obj->description;
            }
        }
        Db::getinstance()->insert(
            'ec_seo_'.$type,
            array(
                'id_'.$type => (int)$id,
                'id_shop' => (int)$this->context->shop->id,
            )
        );
        $id_seo = Db::getInstance()->Insert_ID();
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            if (isset($obj->name[$id_lang])) {
                $name = $obj->name[$id_lang];
            }
            $meta_title = '';
            if (isset($obj->meta_title[$id_lang])) {
                $meta_title = $obj->meta_title[$id_lang];
            }
            if ($type== 'manufacturer' || $type== 'supplier') {
                $name = $obj->name;
                if (Tools::strlen($obj->meta_title) == 0) {
                    $meta_title = $name;
                }
            }
            if ($type == 'meta') {
                $name = $obj->title[$id_lang];
                if (Tools::strlen($name) == 0) {
                    $name = $obj->page;
                }
                $meta_title = $obj->title[$id_lang];
                $obj->meta_description[$id_lang] = $obj->description[$id_lang];
            }
            $k_search = array('-', '/', ':', '+', '&', ',');
            $keyword = str_replace($k_search, '', $name);
            $keyword = str_replace('  ', ' ', $keyword);
            $tab_ec_seo = array(
                'id_seo_'.$type => (int)$id_seo,
                'h1' => pSQl($name),
                'keyword' => pSQl($keyword),
                'description2' => '',
                'id_lang' => (int)$id_lang,
                'og_title' => pSQL($meta_title),
                'og_description' => pSQL($obj->meta_description[$id_lang]),
                'og_image' => pSQl($image),
            );
            Db::getinstance()->insert(
                'ec_seo_'.$type.'_lang',
                $tab_ec_seo,
                false,
                true,
                Db::ON_DUPLICATE_KEY
            );
        }
        return $id_seo;
    }

    public function checkSeoNewLang($obj, $type, $id_seo)
    {
        $image = '';
        if ($type == 'manufacturer' || $type == 'supplier') {
            $funcImage = 'get'.Tools::ucfirst($type).'ImageLink';
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $image = $this->context->link->{$funcImage}($obj->id);
            } else {
                $m_dir = $type[0];
                if ($type == 'supplier') {
                    $m_dir .= 'u';
                }
                $image = Tools::getHttpHost(true).__PS_BASE_URI__.'img/'.$m_dir.'/'.$obj->id.'.jpg';
            }
        }
        if ($type == 'category') {
            $image = $this->context->link->getCatImageLink('', $obj->id);
        }
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            $exist = Db::getInstance()->getValue('SELECT keyword FROM '._DB_PREFIX_.'ec_seo_'.$type.'_lang WHERE id_seo_'.$type.' = '.(int)$id_seo.' AND id_lang = '.(int)$id_lang);
            if ($exist) {
                continue;
            }
            if (isset($obj->name[$id_lang])) {
                $name = $obj->name[$id_lang];
            }
            $meta_title = '';
            if (isset($obj->meta_title[$id_lang])) {
                $meta_title = $obj->meta_title[$id_lang];
            }
            if ($type== 'manufacturer' || $type== 'supplier') {
                $name = $obj->name;
                if (Tools::strlen($obj->meta_title) == 0) {
                    $meta_title = $name;
                }
            }
            if ($type == 'meta') {
                $name = $obj->title[$id_lang];
                if (Tools::strlen($name) == 0) {
                    $name = $obj->page;
                }
                $meta_title = $obj->title[$id_lang];
                $obj->meta_description[$id_lang] = $obj->description[$id_lang];
            }
            if ($type == 'cms') {
                $name = $obj->meta_title[$id_lang];
            }
            $k_search = array('-', '/', ':', '+', '&', ',');
            $keyword = str_replace($k_search, '', $name);
            $keyword = str_replace('  ', ' ', $keyword);
            $tab_ec_seo = array(
                'id_seo_'.$type => (int)$id_seo,
                'h1' => pSQl($name),
                'keyword' => pSQl($keyword),
                'description2' => '',
                'id_lang' => (int)$id_lang,
                'og_title' => pSQL($meta_title),
                'og_description' => pSQL($obj->meta_description[$id_lang] ?? ''),
                'og_image' => pSQl($image),
            );
            Db::getinstance()->insert(
                'ec_seo_'.$type.'_lang',
                $tab_ec_seo,
                false,
                true,
                Db::ON_DUPLICATE_KEY
            );
        }
    }

    public function checkFront($obj, $link_rewrite)
    {
        /* if (get_class($obj) != 'CMS' && get_class($obj) != 'Meta') {
            if (Tools::strlen($obj->meta_title) == 0) {
                $obj->meta_title = $obj->name;
            }
            if (Tools::strlen($obj->meta_description) == 0) {
                $obj->meta_description = strip_tags($obj->description);
            }
        } */
        $keyword = $obj->keyword;
        $keyword = explode(' ', $keyword);
        foreach ($keyword as &$motcl) {
            $replace = array(',', '.', ':');
            $motcl = str_replace($replace, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $motcl)));
        }
        $tab_meta = array();
        $score = 0;
        //h1
        if (get_class($obj) == 'Manufacturer' || get_class($obj) == 'Supplier' || get_class($obj) == 'Meta' || (get_class($obj) == 'CMS' && version_compare(_PS_VERSION_, '1.7', '<'))) {
            $score += 200;
        } else {
            $obj->h1 = $obj->h1 ?? $obj->name ?? '';
            $len = Tools::strlen($obj->h1);
            $tab_meta['h1']['len'] = $len;
            if ($len < $this->category_rule['h1']['min']) {
                $tab_meta['h1']['size'] = -1;
                $score += 70;
            } else if ($len >= $this->category_rule['h1']['min'] && $len <= $this->category_rule['h1']['max']) {
                $tab_meta['h1']['size'] = 1;
                $score += 100;
            } else {
                $tab_meta['h1']['size'] = 2;
                if ($len < 201) {
                    $score += 30;
                } else {
                    $score += 10;
                }
            }
            $cpt = 0;
            $mots_h1 = explode(' ', $obj->h1);
            foreach ($mots_h1 as &$mot) {
                $replace = array(',', '.', ':');
                $mot = str_replace($replace, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot)));
            }
            $mot_needed = array();
            foreach ($keyword as $mot_k) {
                if (in_array($mot_k, $mots_h1)) {
                    $cpt++;
                } else {
                    $mot_needed[] = '"'.$mot_k.'"';
                }
            }
            if ($cpt == count($keyword)) {
                $tab_meta['h1']['keyword']['res'] = 1;
                $score += 100;
            } else {
                if ($cpt == 0) {
                    $score += 10;
                } else {
                    $score += 50;
                }
                $tab_meta['h1']['keyword']['res'] = 2;
            }
            $tab_meta['h1']['keyword']['mot_needed'] = implode(', ', $mot_needed);
        }
     

        //meta title
        $len = Tools::strlen($obj->meta_title);
        $tab_meta['meta_title']['len'] = $len;
        if ($len < $this->category_rule['meta_title']['min']) {
            if ($len < 20) {
                $score += 20;
            } else {
                $score += 70;
            }
            $tab_meta['meta_title']['size'] = -1;
        } else if ($len >= $this->category_rule['meta_title']['min'] && $len <= $this->category_rule['meta_title']['max']) {
            $tab_meta['meta_title']['size'] = 1;
            $score += 100;
        } else {
            $tab_meta['meta_title']['size'] = 2;
            $score += 20;
        }
      
        $cpt = 0;
        $mots_meta_title = explode(' ', $obj->meta_title);
        foreach ($mots_meta_title as &$mot_meta_title) {
            $replace = array(',', '.', ':');
            $mot_meta_title = str_replace($replace, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot_meta_title)));
        }
       
        $mot_needed = array();
        foreach ($keyword as $mot) {
            if (in_array($mot, $mots_meta_title)) {
                $cpt++;
            } else {
                $mot_needed[] = '"'.$mot.'"';
            }
        }
        if ($cpt == count($keyword)) {
            $tab_meta['meta_title']['keyword']['res'] = 1;
            $score += 100;
        } else {
            $tab_meta['meta_title']['keyword']['res'] = 2;
            if ($cpt == 0) {
                $score += 10;
            } else {
                $score += 50;
            }
        }
        $tab_meta['meta_title']['keyword']['mot_needed'] = implode(', ', $mot_needed);
       
        //meta description
        $len = Tools::strlen($obj->meta_description);
        $tab_meta['meta_description']['len'] = $len;
        if ($len < $this->category_rule['meta_description']['min']) {
            $score += 30;
            $tab_meta['meta_description']['size'] = -1;
        } else if ($len >= $this->category_rule['meta_description']['min'] && $len <= $this->category_rule['meta_description']['max']) {
            $tab_meta['meta_description']['size'] = 1;
            $score += 100;
        } else {
            $tab_meta['meta_description']['size'] = 2;
            if ($len < 300) {
                $score +=30;
            } else {
                $score +=10;
            }
        }
        $cpt = 0;
        $mots_description = explode(' ', $obj->meta_description);
        foreach ($mots_description as &$mot) {
            $replace = array(',', '.', ':');
            $mot = str_replace($replace, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot)));
        }
        $mot_needed = array();
        foreach ($keyword as $mot_d) {
            if (in_array($mot_d, $mots_description)) {
                $cpt++;
            } else {
                $mot_needed[] = '"'.$mot_d.'"';
            }
        }
        if ($cpt == count($keyword)) {
            $tab_meta['meta_description']['keyword']['res'] = 1;
            $score += 100;
        } else {
            $tab_meta['meta_description']['keyword']['res'] = 2;
            if ($cpt == 0) {
                $score += 10;
            } else {
                $score += 50;
            }
        }
        $tab_meta['meta_description']['keyword']['mot_needed'] = implode(', ', $mot_needed);
      

      
        if (get_class($obj) == 'Manufacturer' || get_class($obj) == 'Supplier') {
            $score += 200;
        } else {
              //url
           
            $len = Tools::strlen($link_rewrite);
            $tab_meta['link_rewrite']['len'] = $len;
            if ($len < $this->category_rule['link_rewrite']['min']) {
                $tab_meta['link_rewrite']['size'] = -1;
                $score += 50;
            } else if ($len >= $this->category_rule['link_rewrite']['min'] && $len <= $this->category_rule['link_rewrite']['max']) {
                $tab_meta['link_rewrite']['size'] = 1;
                $score += 100;
            } else {
                $tab_meta['link_rewrite']['size'] = 2;
                $score += 70;
            }
            $cpt = 0;
            $link_rewrite = str_replace('.html', '', $link_rewrite);
            $mots_lw = explode('-', $obj->link_rewrite);
            
            $mot_needed = array();
            foreach ($keyword as $mot) {
                $replace = array(',', '.', ':');
                $mot = str_replace($replace, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot)));
                if (in_array($mot, $mots_lw)) {
                    $cpt++;
                } else {
                    $mot_needed[] = '"'.$mot.'"';
                }
            }
            if ($cpt == count($keyword)) {
                $tab_meta['link_rewrite']['keyword']['res'] = 1;
                $score += 100;
            } else {
                $tab_meta['link_rewrite']['keyword']['res'] = 2;
                $score += 70;
            }
            $tab_meta['link_rewrite']['keyword']['mot_needed'] = implode(', ', $mot_needed);
            if (preg_match('/_/i', $link_rewrite)) {
                $score -= 10;
            }
            if (preg_match('/_/i', $link_rewrite)) {
                $score -= 10;
            }
            $count_slash = count(explode('/', $link_rewrite))-1;
            if ($count_slash > 5) {
                $score -= 10;
            }
        }
        
        //Description
        $desc_total = $obj->description;
        $h2 = 'h2';
        $oh = '<';
        $ch = '>';
        $verifh2 = $oh.$h2.$ch;
        if (preg_match('/'.$verifh2.'/', $desc_total)) {
            $score += 10;
            $tab_meta['desc_total']['h2'] = 1;
        } else {
            $tab_meta['desc_total']['h2'] = 0;
        }
     
        $desc_total = strip_tags($desc_total);
        $search = array("\n", "\r", "\r\n", "\n\r", "\t");
        $desc_total = str_replace($search, " ", $desc_total);
        $desc_total = iconv('UTF-8', 'ASCII//IGNORE', $desc_total);
        preg_match_all('/\w+/', $desc_total, $matches);
        $mots100 = array_slice($matches[0], 0, 100);
        foreach ($mots100 as &$mot100) {
            $mot100 = Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot100));
        }
        $count_word = count($matches[0]);
        $tab_meta['desc_total']['count_word'] = $count_word;
        
        if ($count_word > 150 && $count_word < 300) {
            $score += 50;
        }
        if ($count_word > 300) {
            $score += 100;
        }
        $mot_needed = array();
        $cpt = 0;
        foreach ($keyword as $mot) {
            $mot = Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot));
            if (in_array($mot, $mots100)) {
                $cpt++;
            } else {
                $mot_needed[] = '"'.$mot.'"';
            }
        }
        if ($cpt == count($keyword)) {
            $tab_meta['desc_total']['keyword']['res'] = 1;
            $score += 10;
        } else {
            $tab_meta['desc_total']['keyword']['res'] = 2;
        }
        $tab_meta['desc_total']['keyword']['mot_needed'] = implode(', ', $mot_needed);
        $tab_meta['score'] = $score;
        $tab_meta['score'] = (int)(($score/920)*100);
    
        return $tab_meta;
    }

    public function calculGlobalNote($obj, $link_rewrite)
    {
        
       

        $keyword = $obj->keyword;
        
        $score_lang = array();
        $tab_lang = array();
        $languages = Language::getLanguages(false);
        $score_max_mul = 0;
        foreach ($languages as $lang) {
            /* if (get_class($obj) != 'CMS' && get_class($obj) != 'Meta') {
                if (Tools::strlen($obj->meta_title[$lang['id_lang']]) == 0) {
                    $obj->meta_title[$lang['id_lang']] = $obj->name[$lang['id_lang']];
                }
                if (Tools::strlen($obj->meta_description[$lang['id_lang']]) == 0 && get_class($obj) != 'Supplier' && get_class($obj) != 'Manufacturer') {
                    if (get_class($obj) == 'Product') {
                        $obj->meta_description[$lang['id_lang']] = strip_tags($obj->description_short[$lang['id_lang']]);
                    } else {
                        $obj->meta_description[$lang['id_lang']] = strip_tags($obj->description[$lang['id_lang']]);
                    }
                }
            } */
            $tab_lang[] = $lang['id_lang'];
            $keyword_lang = explode(' ', $keyword[$lang['id_lang']]);
            foreach ($keyword_lang as &$mot_ke) {
                $search = array(',', '.');
                $mot_ke = str_replace($search, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot_ke)));
            }
            $keyword[$lang['id_lang']] = $keyword_lang;
            $score_max_mul ++;
        }
        $score = 0;
        //h1
        if (get_class($obj) == 'Manufacturer' || get_class($obj) == 'Supplier' || get_class($obj) == 'Meta' || (get_class($obj) == 'CMS' && version_compare(_PS_VERSION_, '1.7', '<'))) {
            $score += (200*$score_max_mul);
            foreach ($keyword as $id_lang => $value) {
                if (!isset($keyword[$id_lang])) {
                    continue;
                }
                $score_lang[$id_lang] = 200;
            }
        } else {
            foreach ($obj->h1 as $id_lang => $value) {
                if (!isset($keyword[$id_lang])) {
                    continue;
                }
                if (!isset($score_lang[$id_lang])) {
                    $score_lang[$id_lang] = 0;
                }
                $len = Tools::strlen($value);
                if ($len < $this->category_rule['h1']['min']) {
                    $score += 70;
                    $score_lang[$id_lang] += 70;
                } else if ($len >= $this->category_rule['h1']['min'] && $len <= $this->category_rule['h1']['max']) {
                    $score += 100;
                    $score_lang[$id_lang] += 100;
                } else {
                    if ($len < 201) {
                        $score += 30;
                        $score_lang[$id_lang] += 30;
                    } else {
                        $score += 10;
                        $score_lang[$id_lang] += 10;
                    }
                }
                $cpt = 0;
                $mots_h1 = explode(' ', $value);
                foreach ($mots_h1 as &$mot) {
                    $search = array(',', '.');
                    $mot = str_replace($search, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot)));
                }
                $mot_needed = array();
                foreach ($keyword[$id_lang] as $mot_k) {
                    if (in_array($mot_k, $mots_h1)) {
                        $cpt++;
                    }
                }
                if ($cpt == count($keyword[$id_lang])) {
                    $score += 100;
                    $score_lang[$id_lang] += 100;
                } else {
                    if ($cpt == 0) {
                        $score += 10;
                        $score_lang[$id_lang] += 10;
                    } else {
                        $score += 50;
                        $score_lang[$id_lang] += 50;
                    }
                }
            }
        }
      
      
        //meta title
        foreach ($obj->meta_title as $id_lang => $value) {
            if (!isset($keyword[$id_lang])) {
                continue;
            }
            if (!isset($score_lang[$id_lang])) {
                $score_lang[$id_lang] = 0;
            }
            $len = Tools::strlen($value);
            if ($len < $this->category_rule['meta_title']['min']) {
                if ($len < 20) {
                    $score += 20;
                    $score_lang[$id_lang] += 20;
                } else {
                    $score += 70;
                    $score_lang[$id_lang] += 70;
                }
            } else if ($len >= $this->category_rule['meta_title']['min'] && $len <= $this->category_rule['meta_title']['max']) {
                $score += 100;
                $score_lang[$id_lang] += 100;
            } else {
                $score += 20;
                $score_lang[$id_lang] += 20;
            }
        
            $cpt = 0;
            $mots_meta_title = explode(' ', $value);
            foreach ($mots_meta_title as &$mot_meta_title) {
                $search = array(',', '.');
                $mot_meta_title = str_replace($search, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot_meta_title)));
            }
            $mot_needed = array();
            foreach ($keyword[$id_lang] as $mot) {
                if (in_array($mot, $mots_meta_title)) {
                    $cpt++;
                }
            }
            if ($cpt == count($keyword[$id_lang])) {
                $score += 100;
                $score_lang[$id_lang] += 100;
            } else {
                if ($cpt == 0) {
                    $score += 10;
                    $score_lang[$id_lang] += 10;
                } else {
                    $score += 50;
                    $score_lang[$id_lang] += 50;
                }
            }
        }
       
        //meta description
        foreach ($obj->meta_description as $id_lang => $value) {
            if (!isset($keyword[$id_lang])) {
                continue;
            }
            $len = Tools::strlen($value);
         /*    echo $value;
            exit(); */
            if ($len < $this->category_rule['meta_description']['min']) {
                $score += 30;
                $score_lang[$id_lang] += 30;
            } else if ($len >= $this->category_rule['meta_description']['min'] && $len <= $this->category_rule['meta_description']['max']) {
                $score += 100;
                $score_lang[$id_lang] += 100;
            } else {
                if ($len < 300) {
                    $score +=30;
                    $score_lang[$id_lang] +=30;
                } else {
                    $score +=10;
                    $score_lang[$id_lang] +=10;
                }
            }
            $cpt = 0;
            $mots_description = explode(' ', $value);
            foreach ($mots_description as &$mot) {
                $search = array(',', '.');
                $mot = str_replace($search, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot)));
            }
            $mot_needed = array();
            foreach ($keyword[$id_lang] as $mot_d) {
                if (in_array($mot_d, $mots_description)) {
                    $cpt++;
                }
            }
            if ($cpt == count($keyword[$id_lang])) {
                $score += 100;
                $score_lang[$id_lang] += 100;
            } else {
                if ($cpt == 0) {
                    $score += 10;
                    $score_lang[$id_lang] += 10;
                } else {
                    $score += 50;
                    $score_lang[$id_lang] += 50;
                }
            }
        }
        if (get_class($obj) == 'Manufacturer' || get_class($obj) == 'Supplier') {
            $score += (200*$score_max_mul);
            foreach ($keyword as $id_lang => $value) {
                if (!isset($keyword[$id_lang])) {
                    continue;
                }
                $score_lang[$id_lang] += 200;
            }
        } else {
              //url
            foreach ($obj->link_rewrite as $id_lang => $value) {
                if (!isset($keyword[$id_lang])) {
                    continue;
                }
                $len = Tools::strlen($link_rewrite[$id_lang]);
                if ($len < $this->category_rule['link_rewrite']['min']) {
                    $score += 50;
                    $score_lang[$id_lang] += 50;
                } else if ($len >= $this->category_rule['link_rewrite']['min'] && $len <= $this->category_rule['link_rewrite']['max']) {
                    $score += 100;
                    $score_lang[$id_lang] += 100;
                } else {
                    $score += 70;
                    $score_lang[$id_lang] += 70;
                }
                $cpt = 0;
                $link_rewrite[$id_lang] = str_replace('.html', '', $link_rewrite[$id_lang]);
                $mots_lw = explode('-', $value);
                $mot_needed = array();
                foreach ($keyword[$id_lang] as $mot) {
                    $search = array(',', '.');
                    $mot = str_replace($search, '', Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot)));
                    if (in_array($mot, $mots_lw)) {
                        $cpt++;
                    } else {
                        $mot_needed[] = '"'.$mot.'"';
                    }
                }
                if ($cpt == count($keyword[$id_lang])) {
                    $score += 100;
                    $score_lang[$id_lang] += 100;
                } else {
                    $score += 70;
                    $score_lang[$id_lang] += 70;
                }
                if (preg_match('/_/i', $link_rewrite[$id_lang])) {
                    $score -= 10;
                    $score_lang[$id_lang] -= 10;
                }
                if (preg_match('/_/i', $link_rewrite[$id_lang])) {
                    $score -= 10;
                    $score_lang[$id_lang] -= 10;
                }
                $count_slash = count(explode('/', $link_rewrite[$id_lang]))-1;
                if ($count_slash > 5) {
                    $score -= 10;
                    $score_lang[$id_lang] -= 10;
                }
            }
        }
        
        //Description
        $h2 = 'h2';
        $oh = '<';
        $ch = '>';
        $verifh2 = $oh.$h2.$ch;
        foreach ($obj->description as $id_lang => $value) {
            if (!isset($keyword[$id_lang])) {
                continue;
            }
            $desc_total = $value;
            if (preg_match('/'.$verifh2.'/', $desc_total)) {
                $score += 10;
                $score_lang[$id_lang] += 10;
            }
        
            $desc_total = strip_tags($desc_total);
            $search = array("\n", "\r", "\r\n", "\n\r", "\t");
            $desc_total = str_replace($search, " ", $desc_total);
            $desc_total = iconv('UTF-8', 'ASCII//IGNORE', $desc_total);
            preg_match_all('/\w+/', $desc_total, $matches);
            $mots100 = array_slice($matches[0], 0, 100);
            foreach ($mots100 as &$mot100) {
                $mot100 = Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot100));
            }
            $count_word = count($matches[0]);
            if ($count_word > 150 && $count_word < 300) {
                $score += 50;
                $score_lang[$id_lang] += 50;
            }
            if ($count_word > 300) {
                $score += 100;
                $score_lang[$id_lang] += 100;
            }
            $mot_needed = array();
            $cpt = 0;
            foreach ($keyword[$id_lang] as $mot) {
                $mot = Tools::strtolower(iconv('UTF-8', 'ASCII//IGNORE', $mot));
                if (in_array($mot, $mots100)) {
                    $cpt++;
                }
            }
            if ($cpt == count($keyword[$id_lang])) {
                $score += 10;
                $score_lang[$id_lang] += 10;
            }
        }
        foreach ($score_lang as $id_lang => &$val) {
            $val = (int)(($val/920)*100);
        }

        return array(
            'global' => (int)(($score/(920*$score_max_mul))*100),
            'score_lang' => $score_lang,
        );
    }

    public function getGlobalNote($class, $id, $id_shop)
    {
        $info = $this->getInfoMetaByObj($class, $id, $id_shop);
        if (!$info) {
            $languages = Language::getLanguages(true);
            $score_lang = [];
            foreach ($languages as $lang) {
                $score_lang[$lang['id_lang']] = 0;
            }
            return ['global' => 0, 'score_lang' => $score_lang];
        }
        return $this->calculGlobalNote($info['obj'], $info['link_rewrite']);
    }

    public function getInfoMetaByObj($class, $id, $id_shop)
    {
        $this->context = Context::getContext();
        $type = $class;
        if ($class == 'cms') {
            $class = 'CMS';
        } else {
            $class[0] = strtoupper($class[0]);
        }
        if (!$id) {
            $id = Tools::getValue('id_'.$type);
        }
        if ($type == 'product') {
            $obj = new Product($id, false, null, $id_shop);
        } else {
            $obj = new $class($id, null, $id_shop);
        }
        if (!Validate::isLoadedObject($obj)) {
            return false;
        }
        $info_seo = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'ec_seo_'.$type.' esc
        LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$type.'_lang escl ON (escl.id_seo_'.$type.' = esc.id_seo_'.$type.')
        WHERE id_'.$type.' = '.(int)$id.' AND id_shop = '.(int)$id_shop);
        if (!$info_seo) {
            $this->initSeo($type, $id);
            $info_seo = Db::getInstance()->ExecuteS(
                'SELECT * FROM '._DB_PREFIX_.'ec_seo_'.$type.' esc
                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$type.'_lang escl ON (escl.id_seo_'.$type.' = esc.id_seo_'.$type.')
                WHERE id_'.$type.' = '.(int)$id.' AND id_shop = '.(int)$id_shop
            );
        } else {
            $this->checkSeoNewLang($obj, $type, $info_seo[0]['id_seo_'.$type]);
            $info_seo = Db::getInstance()->ExecuteS(
                'SELECT * FROM '._DB_PREFIX_.'ec_seo_'.$type.' esc
                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$type.'_lang escl ON (escl.id_seo_'.$type.' = esc.id_seo_'.$type.')
                WHERE id_'.$type.' = '.(int)$id.' AND id_shop = '.(int)$id_shop
            );
        }
        
        if ($type == 'meta') {
            $obj->meta_title = $obj->title;
            $obj->meta_description = $obj->description;
            $obj->link_rewrite = $obj->url_rewrite;
        }
        
        foreach ($info_seo as $info) {
            if ($type == 'meta') {
                $obj->description[$info['id_lang']] = $info['description2'];
            }
            if ($type == 'category') {
                $obj->description[$info['id_lang']] = $obj->description[$info['id_lang']].' '.$info['description2'];
            }
            if (isset($info['h1'])) {
                $obj->h1[$info['id_lang']] = $info['h1'];
            }
            $obj->keyword[$info['id_lang']] = $info['keyword'];
        }
      
        
        
        
        $languages = Language::getLanguages(false);
        $link_rewrite = array();
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            if ($type == 'product') {
                $obj->description[$id_lang] = $obj->description_short[$id_lang].' '.$obj->description[$id_lang];
            }
            if ($type == 'manufacturer') {
                $obj->description[$id_lang] = $obj->short_description[$id_lang].' '.$obj->description[$id_lang];
            }
            if (isset($obj->meta_title[$id_lang])) {
                if (Tools::strlen($obj->meta_title[$id_lang]) == 0) {
                    if ($type == "supplier" || $type == "manufacturer") {
                        $obj->meta_title[$id_lang] = $obj->name;
                    } else if ($type != 'meta') {
                        $obj->meta_title[$id_lang] = $obj->name[$id_lang];
                    }
                }
            }
            if (isset($obj->meta_description[$id_lang]) && $type != 'meta' && $type != 'supplier' && $type != 'manufacturer' && $type != 'cms') {
                if (Tools::strlen($obj->meta_description[$id_lang]) == 0) {
                    if ($type == 'product') {
                        $obj->meta_description[$id_lang] = strip_tags($obj->description_short[$id_lang]);
                    } else {
                        $obj->meta_description[$id_lang] = strip_tags($obj->description[$id_lang]);
                    }
                }
            }

            if ($type == 'manufacturer' || $type == 'supplier') {
                $funcLink = 'get'.$class.'Link';
                $obj_lang = new $class($obj->id, $id_lang, $id_shop);
                $link = $this->context->link->{$funcLink}($obj_lang, null, $id_lang, $id_shop);
            }

            if ($type == 'category') {
                $link = $this->context->link->getCategoryLink($obj->id, null, $id_lang);
            }
            if ($type == 'cms') {
                $link = $this->context->link->getCMSLink($obj, null, null, $id_lang, $id_shop);
                if (Tools::strlen($obj->head_seo_title[$id_lang]) == 0) {
                    $obj->head_seo_title[$id_lang] = $obj->meta_title[$id_lang];
                }
            }
            if ($type == 'product') {
                $link = $this->context->link->getProductLink($obj, null, null, null, $id_lang, $id_shop);
            }
            if ($type == 'meta') {
                $link = $this->context->link->getPageLink($obj->page, null, $id_lang, null, false, $id_shop);
            }
            $link_rewrite[$id_lang] = $link;
        }
        if ($type == 'cms') {
            $obj->h1 = $obj->meta_title;
            $obj->meta_title = $obj->head_seo_title;
            $obj->description = $obj->content;
        }
        return array(
            'obj' => $obj,
            'link_rewrite' => $link_rewrite,
        );
    }
 
    
    public function showMetaVariables($class = false)
    {
        $this->smarty->assign(
            array(
                'variablesMeta'=> $this->getVariableMetaGenerator(),
                'tclass' => $class
            )
        );
        
        return $this->display(__FILE__, 'views/templates/admin/meta_variable.tpl');
    }

    public function getVariableMetaGenerator($class = null)
    {
        $variables =  array(
            'product' => array(
                '%current_content%' => $this->l('Current content'),
                '%shop_name%' => $this->l('Shop name'),
                '%product_name%' => $this->l('Product name'),
                '%manufacturer%' => $this->l('Manufacturer name'),
                '%supplier%' => $this->l('Supplier name'),
                '%reference%' => $this->l('Product reference'),
                '%supplier_reference%' => $this->l('Supplier product reference'),
                '%ean13%' => $this->l('Product ean13'),
                '%h1%' => $this->l('Product h1'),
                '%category_default%' => $this->l('Default category name'),
                '%description_short|limit|%' => $this->l('Product description short with limitation of the number of characters via the parameter "limit"'),
                '%description|limit|%' => $this->l('Product description with limitation of the number of characters via the parameter "limit"'),
            ),
            'category' => array(
                '%current_content%' => $this->l('Current content'),
                '%shop_name%' => $this->l('Shop name'),
                '%category_h1%' => $this->l('SEO Category h1'),
                '%category_name%' => $this->l('Category name'),
                '%description|limit|%' => $this->l('Category description'),
                '%description2|limit|%' => $this->l('SEO Category description 2'),
               /*  '%category_child|nb||reverse|sep%' => '',
                '%category_parent|nb||reverse|sep%' => '', */
            ),
            'cms' => array(
                '%current_content%' => $this->l('Current content'),
                '%shop_name%' => $this->l('Shop name'),
                '%page_name%' => $this->l('Name of CMS page'),
                //'%content|limit|%' => $this->l('CMS content with limitation of the number of characters via the parameter "limit"'),
            ),
            'manufacturer' => array(
                '%current_content%' => $this->l('Current content'),
                '%shop_name%' => $this->l('Shop name'),
                '%manufacturer_name%' => $this->l('Manufacturer name'),
                '%description_short|limit|%' => $this->l('Manufacturer description short with limitation of the number of characters via the parameter "limit"'),
                '%description|limit|%' => $this->l('Manufacturer description with limitation of the number of characters via the parameter "limit"'),
                '%product_list|nb|%' => $this->l('List of manufacturer\'s product names with limitation of the number of products via the parameter "nb"'),
            ),
            'supplier' => array(
                '%current_content%' => $this->l('Current content'),
                '%shop_name%' => $this->l('Shop name'),
                '%supplier_name%' => $this->l('Supplier name'),
                '%description_supplier|limit|%' => $this->l('Supplier description with limitation of the number of characters via the parameter "limit"'),
                '%product_list|nb|%' => $this->l('List of supplier\'s product names with limitation of the number of products via the parameter "nb"'),
            ),
        );
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $features = Db::getInstance()->executes('SELECT id_feature, name FROM '._DB_PREFIX_.'feature_lang WHERE id_lang = '.(int)$id_lang);
        foreach ($features as $feature) {
            $variables['product']['%fea_'.$feature['name'].'%'] = $this->l('Feature').' '.$feature['name'];
        }
        return $variables;
    }

    public function genMetaSupplier($sup, $info, $id_lang, $current = '')
    {
        $shop_name = Configuration::get('PS_SHOP_NAME');
        $search = array('%shop_name%', '%supplier_name%', '%current_content%');
        $replace = array($shop_name, $sup->name, $current);
        $info = str_replace($search, $replace, $info);


        if (preg_match('/\%description_supplier\|([0-9]*)\|\%/', $info, $match)) {
            $limit = $match[1];
            $description = $this->cleanMetaDesc($sup->description[$id_lang], $limit);
            $info = preg_replace('/\%description_supplier\|([0-9]*)\|\%/', $description, $info);
        }

        if (preg_match('/\%product_list\|([0-9]*)\|\%/', $info, $match)) {
            $limit = $match[1];
            $prods_sup = $sup->getProductsLite($id_lang);
            $prods_list = array();
            foreach ($prods_sup as $prod) {
                $prods_list[] = $prod['name'];
            }
            $prods_list = array_slice($prods_list, 0, $limit);
            $info = preg_replace('/\%product_list\|([0-9]*)\|\%/', implode(', ', $prods_list), $info);
        }
        $search = array("\n", "\r", "\r\n", "\n\r", "\t");
        $info = str_replace($search, " ", $info);
        return trim($info);
    }

    public function genMetaManufacturer($man, $info, $id_lang, $current = '')
    {
        $shop_name = Configuration::get('PS_SHOP_NAME');
        $search = array('%shop_name%', '%manufacturer_name%', '%current_content%');
        $replace = array($shop_name, $man->name, $current);
        $info = str_replace($search, $replace, $info);
        if (preg_match('/\%description_short\|([0-9]*)\|\%/', $info, $match)) {
            $limit = $match[1];
            $description_short = $this->cleanMetaDesc($man->short_description[$id_lang], $limit);
            $info = preg_replace('/\%description_short\|([0-9]*)\|\%/', $description_short, $info);
        }

        if (preg_match('/\%description\|([0-9]*)\|\%/', $info, $match)) {
            $limit = $match[1];
            $description = $this->cleanMetaDesc($man->description[$id_lang], $limit);
            $info = preg_replace('/\%description\|([0-9]*)\|\%/', $description, $info);
        }

        if (preg_match('/\%product_list\|([0-9]*)\|\%/', $info, $match)) {
            $limit = $match[1];
            $prods_man = $man->getProductsLite($id_lang);
            $prods_list = array();
            foreach ($prods_man as $prod) {
                $prods_list[] = $prod['name'];
            }
            $prods_list = array_slice($prods_list, 0, $limit);
            $info = preg_replace('/\%product_list\|([0-9]*)\|\%/', implode(', ', $prods_list), $info);
        }
        $search = array("\n", "\r", "\r\n", "\n\r", "\t");
        $info = str_replace($search, " ", $info);
        return trim($info);
    }

    public function genMetaCMS($cms, $info, $id_lang, $current = '')
    {
        $shop_name = Configuration::get('PS_SHOP_NAME');
        $search = array('%shop_name%', '%page_name%', '%current_content%');
        $replace = array($shop_name, $cms->meta_title[$id_lang], $current);
        $info = str_replace($search, $replace, $info);

        $search = array("\n", "\r", "\r\n", "\n\r", "\t");
        $info = str_replace($search, " ", $info);
        return trim($info);
    }

    public function genMetaCategory($cat, $info, $id_lang, $current = '')
    {
        $shop_name = Configuration::get('PS_SHOP_NAME');
        $category_seo = Db::getInstance()->getRow(
            'SELECT h1, description2 FROM '._DB_PREFIX_.'ec_seo_category_lang escl 
            LEFT JOIN '._DB_PREFIX_.'ec_seo_category esc ON (esc.id_seo_category = escl.id_seo_category)
            WHERE id_category = '.(int)$cat->id.' AND id_lang = '.(int)$id_lang
        );
        $search = array('%shop_name%', '%category_h1%', '%category_name%', '%current_content%');
        if (Tools::strlen($category_seo['h1']) == 0) {
            $category_seo['h1'] = $cat->name[$id_lang];
        }
        $replace = array($shop_name, $category_seo['h1'], $cat->name[$id_lang], $current);
       
        $info = str_replace($search, $replace, $info);
        if (preg_match('/\%description\|([0-9]*)\|\%/', $info, $match)) {
            $limit = $match[1];
            $description = $this->cleanMetaDesc($cat->description[$id_lang], $limit);
            $info = preg_replace('/\%description\|([0-9]*)\|\%/', $description, $info);
        }
        if (preg_match('/\%description2\|([0-9]*)\|\%/', $info, $match)) {
            $limit = $match[1];
            $description2 = $this->cleanMetaDesc($category_seo['description2'], $limit);
            $info = preg_replace('/\%description2\|([0-9]*)\|\%/', $description2, $info);
        }
        
        $search = array("\n", "\r", "\r\n", "\n\r", "\t");
        $info = str_replace($search, " ", $info);
        return trim($info);
    }

    public function getValueVarProd($prod)
    {
        $config = Db::getInstance()->getRow('SELECT m.name as manufacturer, s.name as supplier, p.reference, p.ean13, p.supplier_reference FROM '._DB_PREFIX_.'product p
        LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
        LEFT JOIN '._DB_PREFIX_.'supplier s ON (s.id_supplier = p.id_supplier)
        WHERE id_product = '.(int)$prod->id);
        $shop_name = Configuration::get('PS_SHOP_NAME');
        $config['shop_name'] = $shop_name;
        return $config;
    }

    public function genMetaProduct($config, $prod, $info, $id_lang, $current = '', $id_image = 0)
    {
        $category_name = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'category_lang WHERE id_category = '.(int)$prod->id_category_default.' AND id_lang = '.(int)$id_lang);
        $h1 = Db::getInstance()->getValue(
            'SELECT h1 FROM '._DB_PREFIX_.'ec_seo_product_lang espl 
            LEFT JOIN '._DB_PREFIX_.'ec_seo_product esp ON (esp.id_seo_product = espl.id_seo_product)
            WHERE id_product = '.(int)$prod->id.' AND id_lang = '.(int)$id_lang
        );
        $search = array('%shop_name%', '%product_name%', '%manufacturer%', '%supplier%', '%reference%', '%supplier_reference%', '%ean13%', '%category_default%', '%current_content%', '%id_image%', '%h1%', '%position%');
        $position_image = '';
        if ($id_image != 0) {
            $position_image = Db::getInstance()->getValue('SELECT position FROM '._DB_PREFIX_.'image WHERE id_image = '.(int)$id_image);
        }
        $replace = array($config['shop_name'], $prod->name[$id_lang], $config['manufacturer'], $config['supplier'], $config['reference'], $config['supplier_reference'], $config['ean13'], $category_name, $current, $id_image, $h1, $position_image);
        $info = str_replace($search, $replace, $info);

        if (preg_match('/\%description_short\|([0-9]*)\|\%/', $info, $match)) {
            $limit = $match[1];
            $description_short = $this->cleanMetaDesc($prod->description_short[$id_lang], $limit);
            $info = preg_replace('/\%description_short\|([0-9]*)\|\%/', $description_short, $info);
        }

        if (preg_match('/\%description\|([0-9]*)\|\%/', $info, $match)) {
            $limit = $match[1];
            $description = $this->cleanMetaDesc($prod->description[$id_lang], $limit);
            $info = preg_replace('/\%description\|([0-9]*)\|\%/', $description, $info);
        }

        $lang_default = Configuration::get('PS_LANG_DEFAULT');
        preg_match_all('~%fea_([a-z]|[A-Z]|[0-9]|-| |\p{L})*%~u', $info, $tab_feature);
        if (count($tab_feature[0]) > 0) {
            foreach ($tab_feature[0] as $feature_not_clean) {
                $replace = array('fea_', '%');
                $feature_name = str_replace($replace, '', $feature_not_clean);
                $feature_value = Db::getInstance()->getValue('SELECT fvl.value FROM '._DB_PREFIX_.'feature_lang fl
                LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature = fl.id_feature)
                LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fp.id_feature_value)
                WHERE fp.id_product = '.(int)$prod->id.' AND fl.name = "'.pSQL($feature_name).'" AND fvl.id_lang = '.(int)$id_lang.' AND fl.id_lang = '.(int)$lang_default);
                if (!$feature_value) {
                    $feature_value = '';
                }
                $info = preg_replace('/'.$feature_not_clean.'/', $feature_value, $info);
            }
        }
        $search = array("\n", "\r", "\r\n", "\n\r", "\t", "\\");
        $info = str_replace($search, " ", $info);
        return trim($info);
    }

    public function cleanMetaDesc($desc, $limit)
    {
        $desc = strip_tags($desc);
        $search = array("\n", "\r", "\r\n", "\n\r", "\t");
        $desc = str_replace($search, " ", $desc);
        $desc = substr($desc, 0, $limit);
        return $desc;
    }

    public function getPreview($class, $id, $meta_title, $meta_description, $id_lang, $id_shop)
    {
       
        if ($class == 'product') {
            $prod = new Product($id, false, null, $id_shop);
            if (!$prod->id) {
                return json_encode(
                    array(
                        'meta_title' => $this->l('Product not found'),
                        'meta_description' => $this->l('Product not found')
                    )
                );
            }
            $config_prod = $this->getValueVarProd($prod);
            $meta_title = $this->genMetaProduct($config_prod, $prod, $meta_title, $id_lang, $prod->meta_title[$id_lang]);
            $meta_description = $this->genMetaProduct($config_prod, $prod, $meta_description, $id_lang, $prod->meta_description[$id_lang]);
        } else {
            if ($class == 'cms') {
                $class = 'CMS';
            } else {
                $class[0] = strtoupper($class[0]);
            }
            $obj = new $class($id, null, $id_shop);
            if (!$obj->id) {
                return json_encode(
                    array(
                        'meta_title' => $class.' '.$this->l('not found'),
                        'meta_description' => $class.' '.$this->l('not found')
                    )
                );
            }
            $func = 'genMeta'.$class;
            $meta_title = $this->{$func}($obj, $meta_title, $id_lang, $obj->meta_title[$id_lang]);
            $meta_description = $this->{$func}($obj, $meta_description, $id_lang, $obj->meta_title[$id_lang]);
        }
/* echo $meta_description;
exit(); */
        return json_encode(
            array(
                'meta_title' => $meta_title,
                'meta_description' => $meta_description
            )
        );
    }

    public function showPreview($spe = false, $class = '')
    {
        $this->smarty->assign(
            array(
                'spe'=> $spe,
                'class'=> $class,
            )
        );
        return $this->display(__FILE__, 'views/templates/admin/preview.tpl');
    }

    public function checkSubmit($allValues)
    {
        $redirect = false;
        $id_shop = (int)$this->context->shop->id;
        $id_shop_default = Configuration::get('PS_SHOP_DEFAULT');
        $languages = Language::getLanguages(false);
        $hashtag = '';
        foreach ($allValues as $key => $val) {
            if ($key == 'submitConfigReport') {
                Configuration::updateValue('EC_SEO_REPORT_ERRORS_ONLY', Tools::getValue('EC_SEO_REPORT_ERRORS_ONLY'));
                $active = 'report';
                $redirect = true;
            }
            if ($key == "submitOpenGraph") {
                Configuration::updateValue('EC_SEO_OG', Tools::getValue('EC_SEO_OG'));
                Configuration::updateValue('EC_SEO_OG_TITLE_DEFAULT', Tools::getValue('EC_SEO_OG_TITLE_DEFAULT'));
                Configuration::updateValue('EC_SEO_OG_DESCRPTION_DEFAULT', Tools::getValue('EC_SEO_OG_DESCRPTION_DEFAULT'));
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Configuration::updateValue('EC_SEO_OG_SITE_NAME_'.$id_lang, Tools::getValue('EC_SEO_OG_SITE_NAME_'.$id_lang));
                    Configuration::updateValue('EC_SEO_OG_LOCALE_'.$id_lang, Tools::getValue('EC_SEO_OG_LOCALE_'.$id_lang));
                }
                $active = 'opengraph';
                $redirect = true;
            }

            //Robots
            if ($key == 'submitRobot') {
                $content = Tools::getValue('robot');
                $robot_shop = Tools::getValue('robot_shop');
                if ($robot_shop == $id_shop_default) {
                    $robot_shop = '';
                }
                file_put_contents(_PS_ROOT_DIR_.'/robots'.$robot_shop.'.txt', $content);
                $active = 'robot';
                $redirect = true;
            }

            //Balise alt img
            if ($key == 'submitBaliseAltGen') {
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->update(
                        'ec_seo_bag_gen',
                        array(
                            'balise_alt' => pSQL(Tools::getValue('balise_alt_'.$id_lang)),
                        ),
                        'id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop.''
                    );
                }
                $active = 'balisealt';
                $redirect = true;
            }

            //Footer Seo
            $count = 0;
            $class = preg_replace('/submitFooterSeoGen/', '', $key, -1, $count);
            if ($count > 0) {
                Db::getinstance()->update(
                    'ec_seo_footer',
                    array(
                        'active' => (bool)$allValues['active_'.$class]
                    ),
                    'id  = '.(int)$allValues['id']
                );
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->update(
                        'ec_seo_footer_lang',
                        array(
                            'title' => pSQL($allValues['title_'.$id_lang]),
                            'description' => pSQL($allValues['description_'.$class.'_'.$id_lang], true)
                        ),
                        'id_footer = '.(int)$allValues['id'].' AND id_lang = '.(int)$id_lang.''
                    );
                }
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class;
            }


            if ($key == 'addBlockFooter' || preg_match('/updateec_ListBlockFooter/', $key)) {
                $id_footer = isset($allValues['id_footer'])?$allValues['id_footer']:false;
                $id = isset($allValues['id'])?$allValues['id']:null;
                if (!$id_footer) {
                    $id_footer = Db::getInstance()->getValue('SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_block WHERE id = '.(int)$id);
                }
                return $this->getFooterBlockForm($id_footer, $id);
            }
            
            if ($key == 'submitFooterSeoBlock') {
                $id_footer = $allValues['id_footer'];
                $id = $allValues['id'];
                if ($id == null) {
                    $position = ((int)Db::getInstance()->getValue('SELECT max(position) FROM '._DB_PREFIX_.'ec_seo_footer_block WHERE id_footer = '.(int)$id_footer))+1;
                    Db::getinstance()->insert(
                        'ec_seo_footer_block',
                        array(
                            'id_footer' => (int)$id_footer,
                            'active' => (bool)$allValues['active'],
                            'position' => (int)$position,
                        )
                    );
                    $id = Db::getInstance()->Insert_ID();
                } else {
                    Db::getinstance()->update(
                        'ec_seo_footer_block',
                        array(
                            'active' => (bool)$allValues['active']
                        ),
                        'id = '.(int)$id
                    );
                }
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_footer_block_lang',
                        array(
                            'id_block' => (int)$id,
                            'id_lang' => (int)$id_lang,
                            'title' => pSQL($allValues['title_'.$id_lang])
                        ),
                        false,
                        true,
                        Db::ON_DUPLICATE_KEY
                    );
                }
                $class = Db::getInstance()->getRow('SELECT type, spe FROM '._DB_PREFIX_.'ec_seo_footer WHERE id = '.(int)$id_footer);
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class['type'];
                if (isset($allValues['submitFooterSeoBlockAndStay'])) {
                    return $this->getFooterBlockForm($id_footer);
                }
                if ($class['spe']) {
                    return $this->renderListBlockSeo($id_footer, true);
                }
            }
            if (preg_match('/deleteec_ListBlockFooter/', $key)) {
                $id = $allValues['id'];
                $id_footer = Db::getInstance()->getValue('SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_block WHERE id = '.(int)$id);
                Db::getinstance()->delete('ec_seo_footer_block', 'id = '.(int)$id);
                Db::getinstance()->delete('ec_seo_footer_block_lang', 'id_block = '.(int)$id);
                $block_links = Db::getInstance()->executes('SELECT id FROM '._DB_PREFIX_.'ec_seo_footer_link WHERE id_block = '.(int)$id);
                foreach ($block_links as $block_link) {
                    $id_link = $block_link['id'];
                    Db::getinstance()->delete('ec_seo_footer_link', 'id = '.(int)$id_link);
                    Db::getinstance()->delete('ec_seo_footer_link_lang', 'id_link = '.(int)$id_link);
                }
                $this->udaptePositionBlock($id_footer);
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class;
                $class = Db::getInstance()->getValue('SELECT type FROM '._DB_PREFIX_.'ec_seo_footer WHERE id = '.(int)$id_footer);
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class;
            }
            if (preg_match('/viewec_ListBlockFooter/', $key)) {
                $id_block = $allValues['id'];
                return $this->renderListBlockLinks($id_block);
            }
            if ($key == 'addLinkBlock' || $key == 'updateec_ListBlockLinkFooter') {
                $id_block = isset($allValues['id_block'])?$allValues['id_block']:false;
                $id = isset($allValues['id'])?$allValues['id']:null;
                if (!$id_block) {
                    $id_block = Db::getInstance()->getValue('SELECT id_block FROM '._DB_PREFIX_.'ec_seo_footer_link WHERE id = '.(int)$id);
                }
                return $this->getFooterBlockLinkForm($id_block, $id);
            }
            if ($key == 'submitFooterSeoBlockLink') {
                $id_block = $allValues['id_block'];
                $id = $allValues['id'];
                if ($id == null) {
                    $position = ((int)Db::getInstance()->getValue('SELECT max(position) FROM '._DB_PREFIX_.'ec_seo_footer_link WHERE id_block = '.(int)$id_block))+1;
                    Db::getinstance()->insert(
                        'ec_seo_footer_link',
                        array(
                            'id_block' => (int)$id_block,
                            'position' => (int)$position,
                        )
                    );
                    $id = Db::getInstance()->Insert_ID();
                } /* else {
                    Db::getinstance()->update(
                        'ec_seo_footer_link',
                        array(
                            'active' => (bool)$allValues['active']
                        ),
                        'id = '.(int)$id
                    );
                } */
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_footer_link_lang',
                        array(
                            'id_link' => (int)$id,
                            'id_lang' => (int)$id_lang,
                            'title' => pSQL($allValues['title_'.$id_lang]),
                            'link' => pSQL($allValues['link_'.$id_lang]),
                        ),
                        false,
                        true,
                        Db::ON_DUPLICATE_KEY
                    );
                }
                if (isset($allValues['submitFooterSeoBlockLinkAndStay'])) {
                    return $this->getFooterBlockLinkForm($id_block);
                }
                return $this->renderListBlockLinks($id_block);
            }
            if ($key == 'deleteec_ListBlockLinkFooter') {
                $id = $allValues['id'];
                $id_block = Db::getInstance()->getValue('SELECT id_block FROM '._DB_PREFIX_.'ec_seo_footer_link WHERE id = '.(int)$id);
                Db::getinstance()->delete('ec_seo_footer_link', 'id = '.(int)$id);
                Db::getinstance()->delete('ec_seo_footer_link_lang', 'id_link = '.(int)$id);
                $this->udaptePositionBlockLink($id_block);
                return $this->renderListBlockLinks($id_block);
            }
            $count = 0;
            $class = preg_replace('/addFooterSpe/', '', $key, -1, $count);
            if ($count > 0) {
                $function = 'getFooterFormSpe'.$class;
                return $this->$function();
            }
            $count = 0;
            $class = preg_replace('/updateec_ListFooterSpe/', '', $key, -1, $count);
            if ($count > 0) {
                $function = 'getFooterFormSpe'.Tools::ucfirst($class);
                $id = isset($allValues['id'])?$allValues['id']:null;
                return $this->$function($id);
            }
            if ($key == 'submitFooterSeoSpeCategory') {
                $id = $allValues['id'];
                $categories = Tools::getValue('categories');
                if ($categories) {
                    if ((count($categories) > 0) == false) {
                        return $this->displayError($this->l('No categories selected')).$this->getFooterFormSpeCategory();
                    }
                } else {
                    return $this->displayError($this->l('No categories selected')).$this->getFooterFormSpeCategory();
                }
                if ($id == null) {
                    Db::getinstance()->insert(
                        'ec_seo_footer',
                        array(
                            'type' => 'category',
                            'active' => (bool)$allValues['active'],
                            'spe' => true,
                            'id_shop' => (int)$id_shop,
                        )
                    );
                    $id = Db::getInstance()->Insert_ID();
                } else {
                    Db::getinstance()->update(
                        'ec_seo_footer',
                        array(
                            'active' => (bool)$allValues['active']
                        ),
                        'id = '.(int)$id
                    );
                }
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_footer_lang',
                        array(
                            'id_footer' => (int)$id,
                            'id_lang' => (int)$id_lang,
                            'title' => pSQL($allValues['title_'.$id_lang]),
                            'description' => pSQL($allValues['description_'.$id_lang], true),
                        ),
                        false,
                        true,
                        Db::ON_DUPLICATE_KEY
                    );
                }
                Db::getinstance()->delete('ec_seo_footer_category', 'id_footer = '.(int)$id);
                foreach ($allValues['categories'] as $id_category) {
                    Db::getinstance()->insert(
                        'ec_seo_footer_category',
                        array(
                            'id_footer' => (int)$id,
                            'id_category' => (int)$id_category,
                        )
                    );
                }
                $class = 'category';
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class.'#spe';
            }
            if ($key == 'submitFooterSeoSpeProduct') {
                $id = $allValues['id'];
                $categories = Tools::getValue('categories');
                $cat_sel = true;
                if ($categories) {
                    if ((count($categories) > 0) == false) {
                        $cat_sel = false;
                    }
                } else {
                    $cat_sel = false;
                }
                $products = $allValues['products'];
                $prod_sel = false;
                $tab_prod = array();
                if (Tools::strlen($products) > 0) {
                    $products = explode(';', $products);
                    foreach ($products as $id_product) {
                        if ((int)$id_product > 0) {
                            $tab_prod[] = $id_product;
                        }
                    }
                }
                if (count($tab_prod) > 0) {
                    $prod_sel = true;
                }
                if (!$cat_sel && !$prod_sel) {
                    return $this->displayError($this->l('You must select a filter "IDS Product" or Categories.')).$this->getFooterFormSpeProduct($id);
                }
                if ($cat_sel && $prod_sel) {
                    return $this->displayError($this->l('You must only select "IDS Product" OR Categories.')).$this->getFooterFormSpeProduct($id);
                }
                if ($id == null) {
                    Db::getinstance()->insert(
                        'ec_seo_footer',
                        array(
                            'type' => 'product',
                            'active' => (bool)$allValues['active'],
                            'spe' => true,
                            'id_shop' => (int)$id_shop,
                        )
                    );
                    $id = Db::getInstance()->Insert_ID();
                } else {
                    Db::getinstance()->update(
                        'ec_seo_footer',
                        array(
                            'active' => (bool)$allValues['active']
                        ),
                        'id = '.(int)$id
                    );
                }
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_footer_lang',
                        array(
                            'id_footer' => (int)$id,
                            'id_lang' => (int)$id_lang,
                            'title' => pSQL($allValues['title_'.$id_lang]),
                            'description' => pSQL($allValues['description_'.$id_lang], true),
                        ),
                        false,
                        true,
                        Db::ON_DUPLICATE_KEY
                    );
                }
                Db::getinstance()->delete('ec_seo_footer_category', 'id_footer = '.(int)$id);
                if ($cat_sel) {
                    foreach ($allValues['categories'] as $id_category) {
                        Db::getinstance()->insert(
                            'ec_seo_footer_category',
                            array(
                                'id_footer' => (int)$id,
                                'id_category' => (int)$id_category,
                            )
                        );
                    }
                }
                Db::getinstance()->delete('ec_seo_footer_product', 'id_footer = '.(int)$id);
                if ($prod_sel) {
                    foreach ($tab_prod as $id_product) {
                        Db::getinstance()->insert(
                            'ec_seo_footer_product',
                            array(
                                'id_footer' => (int)$id,
                                'id_product' => (int)$id_product,
                            )
                        );
                    }
                }
                $class = 'product';
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class.'#spe';
            }
            if ($key == 'submitFooterSeoSpeCms') {
                $id = $allValues['id'];
                $lst_cms = Tools::getValue('lst_cms');
                if ($lst_cms) {
                    if ((count($lst_cms) > 0) == false) {
                        return $this->displayError($this->l('No cms pages selected')).$this->getFooterFormSpeCms($id);
                    }
                } else {
                    return $this->displayError($this->l('No cms pages selected')).$this->getFooterFormSpeCms($id);
                }
                if ($id == null) {
                    Db::getinstance()->insert(
                        'ec_seo_footer',
                        array(
                            'type' => 'cms',
                            'active' => (bool)$allValues['active'],
                            'spe' => true,
                            'id_shop' => (int)$id_shop,
                        )
                    );
                    $id = Db::getInstance()->Insert_ID();
                } else {
                    Db::getinstance()->update(
                        'ec_seo_footer',
                        array(
                            'active' => (bool)$allValues['active']
                        ),
                        'id = '.(int)$id
                    );
                }
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_footer_lang',
                        array(
                            'id_footer' => (int)$id,
                            'id_lang' => (int)$id_lang,
                            'title' => pSQL($allValues['title_'.$id_lang]),
                            'description' => pSQL($allValues['description_'.$id_lang], true),
                        ),
                        false,
                        true,
                        Db::ON_DUPLICATE_KEY
                    );
                }
                Db::getinstance()->delete('ec_seo_footer_cms', 'id_footer = '.(int)$id);
                foreach ($allValues['lst_cms'] as $id_cms) {
                    Db::getinstance()->insert(
                        'ec_seo_footer_cms',
                        array(
                            'id_footer' => (int)$id,
                            'id_cms' => (int)$id_cms,
                        )
                    );
                }
                $class = 'cms';
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class.'#spe';
            }
            if ($key == 'submitFooterSeoSpeSupplier') {
                $id = $allValues['id'];
                $lst_supplier = Tools::getValue('lst_supplier');
                if ($lst_supplier) {
                    if ((count($lst_supplier) > 0) == false) {
                        return $this->displayError($this->l('No supplier selected')).$this->getFooterFormSpeSupplier($id);
                    }
                } else {
                    return $this->displayError($this->l('No supplier selected')).$this->getFooterFormSpeSupplier($id);
                }
                if ($id == null) {
                    Db::getinstance()->insert(
                        'ec_seo_footer',
                        array(
                            'type' => 'supplier',
                            'active' => (bool)$allValues['active'],
                            'spe' => true,
                            'id_shop' => (int)$id_shop,
                        )
                    );
                    $id = Db::getInstance()->Insert_ID();
                } else {
                    Db::getinstance()->update(
                        'ec_seo_footer',
                        array(
                            'active' => (bool)$allValues['active']
                        ),
                        'id = '.(int)$id
                    );
                }
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_footer_lang',
                        array(
                            'id_footer' => (int)$id,
                            'id_lang' => (int)$id_lang,
                            'title' => pSQL($allValues['title_'.$id_lang]),
                            'description' => pSQL($allValues['description_'.$id_lang], true),
                        ),
                        false,
                        true,
                        Db::ON_DUPLICATE_KEY
                    );
                }
                Db::getinstance()->delete('ec_seo_footer_supplier', 'id_footer = '.(int)$id);
                foreach ($allValues['lst_supplier'] as $id_supplier) {
                    Db::getinstance()->insert(
                        'ec_seo_footer_supplier',
                        array(
                            'id_footer' => (int)$id,
                            'id_supplier' => (int)$id_supplier,
                        )
                    );
                }
                $class = 'supplier';
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class.'#spe';
            }
            if ($key == 'submitFooterSeoSpeManufacturer') {
                $id = $allValues['id'];
                $lst_manufacturer = Tools::getValue('lst_manufacturer');
                if ($lst_manufacturer) {
                    if ((count($lst_manufacturer) > 0) == false) {
                        return $this->displayError($this->l('No manufacturer selected')).$this->getFooterFormSpeManufacturer($id);
                    }
                } else {
                    return $this->displayError($this->l('No manufacturer selected')).$this->getFooterFormSpeManufacturer($id);
                }
                if ($id == null) {
                    Db::getinstance()->insert(
                        'ec_seo_footer',
                        array(
                            'type' => 'manufacturer',
                            'active' => (bool)$allValues['active'],
                            'spe' => true,
                            'id_shop' => (int)$id_shop,
                        )
                    );
                    $id = Db::getInstance()->Insert_ID();
                } else {
                    Db::getinstance()->update(
                        'ec_seo_footer',
                        array(
                            'active' => (bool)$allValues['active']
                        ),
                        'id = '.(int)$id
                    );
                }
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_footer_lang',
                        array(
                            'id_footer' => (int)$id,
                            'id_lang' => (int)$id_lang,
                            'title' => pSQL($allValues['title_'.$id_lang]),
                            'description' => pSQL($allValues['description_'.$id_lang], true),
                        ),
                        false,
                        true,
                        Db::ON_DUPLICATE_KEY
                    );
                }
                Db::getinstance()->delete('ec_seo_footer_manufacturer', 'id_footer = '.(int)$id);
                foreach ($allValues['lst_manufacturer'] as $id_manufacturer) {
                    Db::getinstance()->insert(
                        'ec_seo_footer_manufacturer',
                        array(
                            'id_footer' => (int)$id,
                            'id_manufacturer' => (int)$id_manufacturer,
                        )
                    );
                }
                $class = 'manufacturer';
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class.'#spe';
            }
            $count = 0;
            $class = preg_replace('/deleteec_ListFooterSpe/', '', $key, -1, $count);
            if ($count > 0) {
                $id = $allValues['id'];
                Db::getinstance()->delete('ec_seo_footer', 'id = '.(int)$id);
                Db::getinstance()->delete('ec_seo_footer_lang', 'id_footer = '.(int)$id);
                Db::getinstance()->delete('ec_seo_footer_category', 'id_footer = '.(int)$id);
                $blocks = Db::getInstance()->executes('SELECT id FROM '._DB_PREFIX_.'ec_seo_footer_block WHERE id_footer = '.(int)$id);
                foreach ($blocks as $block) {
                    $id_block = $block['id'];
                    $block_links = Db::getInstance()->executes('SELECT id FROM '._DB_PREFIX_.'ec_seo_footer_link WHERE id_block = '.(int)$id_block);
                    foreach ($block_links as $block_link) {
                        $id_link = $block_link['id'];
                        Db::getinstance()->delete('ec_seo_footer_link', 'id = '.(int)$id_link);
                        Db::getinstance()->delete('ec_seo_footer_link_lang', 'id_link = '.(int)$id_link);
                    }
                    Db::getinstance()->delete('ec_seo_footer_block', 'id = '.(int)$id_block);
                    Db::getinstance()->delete('ec_seo_footer_block_lang', 'id_block = '.(int)$id_block);
                }
                $active = 'footerseo';
                $redirect = true;
                $hashtag = $class.'#spe';
            }
            if (preg_match('/viewec_ListFooter/', $key)) {
                $id = $allValues['id'];
                return $this->renderListBlockSeo($id, true);
            }

            //Block HTML
            if ($key == 'AddBlockHtml' || $key == 'updateec_ListBockHtml') {
                $id_block_html = isset($allValues['id_block_html'])?$allValues['id_block_html']:null;
                return $this->getBlockHTMLForm($id_block_html);
            }
            if ($key == 'submitBlockHtml') {
                $id_block_html = $allValues['id_block_html'];
                $is_registerHook = Db::getInstance()->getRow(
                    '
                    SELECT id_hook FROM '._DB_PREFIX_.'hook_module
                    WHERE id_module = '.$this->id.'
                    AND id_hook = '.(int)$allValues['id_hook'].'
                    '
                );
                if (!$is_registerHook) {
                    $hook_name = Db::getInstance()->getValue('SELECT name FROM '._DB_PREFIX_.'hook WHERE id_hook = '.(int)$allValues['id_hook']);
                    $this->registerHook($hook_name);
                }
                if ($id_block_html == null) {
                    Db::getinstance()->insert(
                        'ec_seo_block_html',
                        array(
                            'id_hook' => (int)$allValues['id_hook'],
                            'active' => (bool)$allValues['active'],
                            'id_shop' => (int)$id_shop,
                        )
                    );
                    $id_block_html = Db::getInstance()->Insert_ID();
                } else {
                    Db::getinstance()->update(
                        'ec_seo_block_html',
                        array(
                            'id_hook' => (int)$allValues['id_hook'],
                            'active' => (bool)$allValues['active'],
                        ),
                        'id_block_html = '.(int)$id_block_html
                    );
                }
                foreach ($languages as $language) {
                    $id_lang = $language['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_block_html_lang',
                        array(
                            'id_block_html' => (int)$id_block_html,
                            'content' => pSQL($allValues['content_'.$id_lang], true),
                            'id_lang' => (int)$id_lang
                        ),
                        false,
                        true,
                        Db::ON_DUPLICATE_KEY
                    );
                }
                $active = 'blockhtml';
                $redirect = true;
            }
            if ($key == 'deleteec_ListBockHtml') {
                $id_block_html = $allValues['id_block_html'];
                Db::getinstance()->delete('ec_seo_block_html', 'id_block_html = '.(int)$id_block_html);
                Db::getinstance()->delete('ec_seo_block_html_lang', 'id_block_html = '.(int)$id_block_html);
                $active = 'blockhtml';
                $redirect = true;
            }

            //Page noindex
            if ($key == 'AddPageNoIndex' || $key == 'updateec_ListPageNoIndex') {
                $id = isset($allValues['id'])?$allValues['id']:null;
                return $this->getPageNoIndexForm($id);
            }
            if ($key == 'submitPageNoIndex') {
                $id = $allValues['id'];
                $page = $allValues['page'];
                if (!Validate::isUrl($page)) {
                    return $this->displayError($this->l('Page link invalid')).$this->getPageNoIndexForm($id);
                }
                if ($id != null) {
                    Db::getinstance()->update(
                        'ec_seo_page_noindex',
                        array(
                            'page' => pSQL($page)
                        ),
                        'id = '.(int)$id
                    );
                } else {
                    Db::getinstance()->insert(
                        'ec_seo_page_noindex',
                        array(
                            'page' => pSQL($page)
                        )
                    );
                }
                $active = 'pagenoindex';
                $redirect = true;
            }
            if ($key == 'deleteec_ListPageNoIndex') {
                $id = $allValues['id'];
                Db::getinstance()->delete('ec_seo_page_noindex', 'id = '.(int)$id);
                $active = 'pagenoindex';
                $redirect = true;
            }

            //Maillage Interne
            $count = 0;
            $class = preg_replace('/submitConfigMI_/', '', $key, -1, $count);
            if ($count > 0) {
                if (!Validate::isUnsignedId(Tools::getValue('EC_SEO_MAX_REPLACE_'.$class))) {
                    return $this->displayError($this->l('Maximum should be a number')).$this->showConfig('internalmesh', true);
                }
                
                Configuration::updateValue('EC_SEO_MAX_REPLACE_'.$class, Tools::getValue('EC_SEO_MAX_REPLACE_'.$class));
                $active = 'internalmesh';
                $redirect = true;
                $hashtag = $class;
            }
            $count = 0;
            $class = preg_replace('/addec_seo_mi_/', '', $key, -1, $count);
            if ($count > 0) {
                return $this->getMIForm($class, $this->tab_list[$class]['spe']);
            }
            $class = preg_replace('/updateec_list_mi_/', '', $key, -1, $count);
            if ($count > 0) {
                return $this->getMIForm($class, $this->tab_list[$class]['spe'], Tools::getValue('id_ec_list_mi_'.$class.''));
            }
            $count = 0;
            $class = preg_replace('/submitMI/', '', $key, -1, $count);
            if ($count > 0) {
                if ($this->tab_list[$class]['spe']) {
                    $categories = Tools::getValue('categories');
                    if ($categories) {
                        if ((count($categories) > 0) == false) {
                            return $this->displayError($this->l('No categories selected')).$this->getMIForm($class, $this->tab_list[$class]['spe']);
                        }
                    } else {
                        return $this->displayError($this->l('No categories selected')).$this->getMIForm($class, $this->tab_list[$class]['spe']);
                    }
                }
                
                $keywordExist = array();
                
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    $keyword = Tools::getValue('keyword_'.$id_lang);
                    if (Tools::strlen($keyword) > 0) {
                        $words = explode(' ', $keyword);
                        foreach ($words as $word) {
                            if ($this->tab_list[$class]['spe']) {
                                $req = 'SELECT  espm.id, keyword
                                FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mi espm
                                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$class.'_mi_cat espmc ON (espmc.id_seo_'.$class.'_mi = espm.id)
                                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$class.'_mi_lang espml ON (espml.id_seo_'.$class.'_mi = espm.id)
                                WHERE (keyword like "'.pSQL($word).'" or keyword like "% '.pSQL($word).'" or keyword like "'.pSQL($word).'%" or keyword like "% '.pSQL($word).'%") 
                                AND id_category IN ('.implode(",", $categories).')
                                AND id_shop = '.(int)$id_shop.' AND id_lang = '.(int)$id_lang;
                            } else {
                                $req = 'SELECT  espm.id, keyword
                                FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mi espm
                                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$class.'_mi_lang espml ON (espml.id_seo_'.$class.'_mi = espm.id)
                                WHERE (keyword like "'.pSQL($word).'" or keyword like "% '.pSQL($word).'" or keyword like "'.pSQL($word).'%" or keyword like "% '.pSQL($word).'%")
                                AND id_shop = '.(int)$id_shop.' AND id_lang = '.(int)$id_lang;
                            }
                            $exist = Db::getInstance()->executes($req);
                            if ($exist) {
                                $keywordExist[] = $word;
                            }
                        }
                    }
                }
                if (count($keywordExist) > 0) {
                    return $this->displayError($this->l('These keyword(s) already exist: ').implode(',', $keywordExist)).$this->getMIForm($class, $this->tab_list[$class]['spe']);
                }
                Db::getinstance()->insert(
                    'ec_seo_'.$class.'_mi',
                    array(
                        'id_shop' => (int)$id_shop
                    )
                );
                $id = Db::getInstance()->Insert_ID();
                if ($class == 'category') {
                    //Db::getinstance()->delete('ec_seo_'.$class.'_mi_cat', 'id_category IN ('.pSQL(implode(',', $categories)).')');
                }
                if ($this->tab_list[$class]['spe']) {
                    foreach ($categories as $id_category) {
                        Db::getinstance()->insert(
                            'ec_seo_'.$class.'_mi_cat',
                            array(
                                'id_seo_'.$class.'_mi' => (int)$id,
                                'id_category' => (int)$id_category
                            )
                        );
                    }
                }
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->insert(
                        'ec_seo_'.$class.'_mi_lang',
                        array(
                            'id_seo_'.$class.'_mi' => (int)$id,
                            'keyword' => pSQL(Tools::getValue('keyword_'.$id_lang)),
                            'url' => pSQL(Tools::getValue('url_'.$id_lang)),
                            'id_lang' => (int)$id_lang,
                        )
                    );
                }
                $active = 'internalmesh';
                $redirect = true;
                $hashtag = $class;
            }
            $count = 0;
            $class = preg_replace('/submitEditMI/', '', $key, -1, $count);
            $id = Tools::getValue('id');
            if ($count > 0) {
                if ($this->tab_list[$class]['spe']) {
                    $categories = Tools::getValue('categories');
                    if ($categories) {
                        if ((count($categories) > 0) == false) {
                            return $this->displayError($this->l('No categories selected')).$this->getMIForm($class, $this->tab_list[$class]['spe'], $id);
                        }
                    } else {
                        return $this->displayError($this->l('No categories selected')).$this->getMIForm($class, $this->tab_list[$class]['spe'], $id);
                    }
                }
                $keywordExist = array();
                
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    $keyword = Tools::getValue('keyword_'.$id_lang);
                    if (Tools::strlen($keyword) > 0) {
                        $words = explode(' ', $keyword);
                        foreach ($words as $word) {
                            if ($this->tab_list[$class]['spe']) {
                                $req = 'SELECT  espm.id, keyword
                                FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mi espm
                                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$class.'_mi_cat espmc ON (espmc.id_seo_'.$class.'_mi = espm.id)
                                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$class.'_mi_lang espml ON (espml.id_seo_'.$class.'_mi = espm.id)
                                WHERE (keyword like "'.pSQL($word).'" or keyword like "% '.pSQL($word).'" or keyword like "'.pSQL($word).'%" or keyword like "% '.pSQL($word).'%") 
                                AND espm.id != '.(int)$id.'
                                AND id_category IN ('.implode(",", $categories).')
                                AND id_shop = '.(int)$id_shop.' AND id_lang = '.(int)$id_lang;
                            } else {
                                $req = 'SELECT  espm.id, keyword
                                FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mi espm
                                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$class.'_mi_lang espml ON (espml.id_seo_'.$class.'_mi = espm.id)
                                WHERE (keyword like "'.pSQL($word).'" or keyword like "% '.pSQL($word).'" or keyword like "'.pSQL($word).'%" or keyword like "% '.pSQL($word).'%")
                                espm.id != '.(int)$id.'
                                AND id_shop = '.(int)$id_shop.' AND id_lang = '.(int)$id_lang;
                            }
                            $exist = Db::getInstance()->executes($req);
                            if ($exist) {
                                $keywordExist[] = $word;
                            }
                        }
                    }
                }
                if (count($keywordExist) > 0) {
                    return $this->displayError($this->l('These keyword(s) already exist: ').implode(',', $keywordExist)).$this->getMIForm($class, $this->tab_list[$class]['spe'], $id);
                }
                if ($this->tab_list[$class]['spe']) {
                    Db::getinstance()->delete('ec_seo_'.$class.'_mi_cat', 'id_seo_'.$class.'_mi = '.(int)$id);
                    if ($class == 'category') {
                        Db::getinstance()->delete('ec_seo_'.$class.'_mi_cat', 'id_category IN ('.pSQL(implode(',', $categories)).')');
                    }
                    foreach ($categories as $id_category) {
                        Db::getinstance()->insert(
                            'ec_seo_'.$class.'_mi_cat',
                            array(
                                'id_seo_'.$class.'_mi' => (int)$id,
                                'id_category' => (int)$id_category
                            )
                        );
                    }
                }
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->update(
                        'ec_seo_'.$class.'_mi_lang',
                        array(
                            'keyword' => pSQL(Tools::getValue('keyword_'.$id_lang)),
                            'url' => pSQL(Tools::getValue('url_'.$id_lang)),
                        ),
                        'id_seo_'.$class.'_mi = '.(int)$id.' AND id_lang ='.(int)(int)$id_lang
                    );
                }
                $active = 'internalmesh';
                $redirect = true;
                $hashtag = $class;
            }
            $count = 0;
            $class = preg_replace('/deleteec_list_mi_/', '', $key, -1, $count);
            if ($count > 0) {
                $id = Tools::getValue('id_ec_list_mi_'.$class.'');
                Db::getinstance()->delete('ec_seo_'.$class.'_mi', 'id = '.(int)$id);
                Db::getinstance()->delete('ec_seo_'.$class.'_mi_lang', 'id_seo_'.$class.'_mi = '.(int)$id);
                if ($this->tab_list[$class]['spe']) {
                    Db::getinstance()->delete('ec_seo_'.$class.'_mi_cat', 'id_seo_'.$class.'_mi = '.(int)$id);
                }
                $active = 'internalmesh';
                $redirect = true;
                $hashtag = $class;
            }
            if ($key == 'submitConfigGeneral') {
                Configuration::updateValue('EC_SEO_FRONT', Tools::getValue('EC_SEO_FRONT'));
                Configuration::updateValue('EC_SEO_FRONT_IP', Tools::getValue('EC_SEO_FRONT_IP'));
                Configuration::updateValue('EC_SEO_H1', Tools::getValue('EC_SEO_H1'));
                Configuration::updateValue('EC_SEO_BACKUP', Tools::getValue('EC_SEO_BACKUP'));
                Configuration::updateValue('EC_SEO_META_ACTIVE_PROD', Tools::getValue('EC_SEO_META_ACTIVE_PROD'));
                Configuration::updateValue('EC_SEO_MI_ACTIVE_PROD', Tools::getValue('EC_SEO_MI_ACTIVE_PROD'));
                Configuration::updateValue('EC_SEO_ALT_ACTIVE_PROD', Tools::getValue('EC_SEO_ALT_ACTIVE_PROD'));
                Configuration::updateValue('EC_SEO_REPORT_ACTIVE_PROD', Tools::getValue('EC_SEO_REPORT_ACTIVE_PROD'));
                Configuration::updateValue('EC_SEO_REPORT_ERRORS_ONLY', Tools::getValue('EC_SEO_REPORT_ERRORS_ONLY'));
                Configuration::updateValue('EC_SEO_ONLY_EMTPY_METAS', Tools::getValue('EC_SEO_ONLY_EMTPY_METAS'));
                Configuration::updateGlobalValue('EC_SEO_HTACCESS', Tools::getValue('EC_SEO_HTACCESS'));
                Configuration::updateGlobalValue('EC_SEO_LOGIN_HTACCESS', Tools::getValue('EC_SEO_LOGIN_HTACCESS'));
                Configuration::updateGlobalValue('EC_SEO_PW_HTACCESS', Tools::getValue('EC_SEO_PW_HTACCESS'));
                Configuration::updateGlobalValue('EC_SEO_GSC_CLIENT_ID', Tools::getValue('EC_SEO_GSC_CLIENT_ID'));
                Configuration::updateGlobalValue('EC_SEO_GSC_CLIENT_SECRET', Tools::getValue('EC_SEO_GSC_CLIENT_SECRET'));
                $active = 'config';
                $redirect = true;
            }
            $count = 0;
            $class = preg_replace('/addec_seo/', '', $key, -1, $count);
            if ($count > 0) {
                return $this->showMetaVariables($class).$this->getMetaFormSpe($class).$this->showPreview(true, $class);
            }
            $count = 0;
            $class = preg_replace('/updateec_list_meta_/', '', $key, -1, $count);
            if ($count > 0) {
                return $this->showMetaVariables($class).$this->getMetaFormSpe($class, Tools::getValue('id_ec_list_meta_'.$class)).$this->showPreview(true, $class);
            }
            $count = 0;
            $class = preg_replace('/deleteec_list_meta_/', '', $key, -1, $count);
            if ($count > 0) {
                $id = Tools::getValue('id_ec_list_meta_'.$class);
                Db::getinstance()->delete('ec_seo_'.$class.'_mg', 'id = '.(int)$id);
                Db::getinstance()->delete('ec_seo_'.$class.'_mg_cat', 'id_seo_'.$class.'_mg = '.(int)$id);
                Db::getinstance()->delete('ec_seo_'.$class.'_mg_lang', 'id_seo_'.$class.'_mg = '.(int)$id);
                $active = 'metagenerator';
                $redirect = true;
                $hashtag = $class;
            }
            $count = 0;
            $class = preg_replace('/submitMetaGen/', '', $key, -1, $count);
            if ($count > 0) {
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    Db::getinstance()->update(
                        'ec_seo_'.$class.'_mg_gen',
                        array(
                            'meta_title' => pSQL(Tools::getValue('meta_title_'.$id_lang)),
                            'meta_description' => pSQL(Tools::getValue('meta_description_'.$id_lang)),
                        ),
                        'id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop.''
                    );
                }
                $active = 'metagenerator';
                $redirect = true;
                $hashtag = $class;
            }
            $count = 0;
            $class = preg_replace('/submitMetaSpe/', '', $key, -1, $count);
            if ($count > 0) {
                $categories = Tools::getValue('categories');
                if ($categories) {
                    if (count($categories) > 0) {
                        Db::getinstance()->insert(
                            'ec_seo_'.$class.'_mg',
                            array(
                                'id_shop' => (int)$id_shop
                            )
                        );
                        $id = Db::getInstance()->Insert_ID();
                        foreach ($languages as $lang) {
                            $id_lang = $lang['id_lang'];
                            Db::getinstance()->insert(
                                'ec_seo_'.$class.'_mg_lang',
                                array(
                                    'id_seo_'.$class.'_mg' => (int)$id,
                                    'meta_title' => pSQL(Tools::getValue('meta_title_'.$id_lang)),
                                    'meta_description' => pSQL(Tools::getValue('meta_description_'.$id_lang)),
                                    'id_lang' => (int)$id_lang,
                                )
                            );
                        }
                        Db::getinstance()->delete('ec_seo_'.$class.'_mg_cat', 'id_category IN ('.pSQL(implode(',', $categories)).')');
                        foreach ($categories as $id_category) {
                            Db::getinstance()->insert(
                                'ec_seo_'.$class.'_mg_cat',
                                array(
                                    'id_seo_'.$class.'_mg' => (int)$id,
                                    'id_category' => (int)$id_category
                                )
                            );
                        }
                    }
                } else {
                    return $this->displayError($this->l('No categories selected')).$this->showMetaVariables($class).$this->getMetaFormSpe($class);
                }
                
                $active = 'metagenerator';
                $redirect = true;
                $hashtag = $class;
            }
            $count = 0;
            $class = preg_replace('/submitEditMetaSpe/', '', $key, -1, $count);
            if ($count > 0) {
                $id = Tools::getValue('id');
                $categories = Tools::getValue('categories');
                if ($categories) {
                    if (count($categories) > 0) {
                        foreach ($languages as $lang) {
                            $id_lang = $lang['id_lang'];
                            Db::getinstance()->update(
                                'ec_seo_'.$class.'_mg_lang',
                                array(
                                    'meta_title' => pSQL(Tools::getValue('meta_title_'.$id_lang)),
                                    'meta_description' => pSQL(Tools::getValue('meta_description_'.$id_lang)),
                                ),
                                'id_lang = '.(int)$id_lang.' AND id_seo_'.$class.'_mg = '.(int)$id.''
                            );
                        }
                        Db::getinstance()->delete('ec_seo_'.$class.'_mg_cat', 'id_seo_'.$class.'_mg = '.(int)$id);
                        Db::getinstance()->delete('ec_seo_'.$class.'_mg_cat', 'id_category IN ('.pSQL(implode(',', $categories)).')');
                        foreach ($categories as $id_category) {
                            Db::getinstance()->insert(
                                'ec_seo_'.$class.'_mg_cat',
                                array(
                                    'id_seo_'.$class.'_mg' => (int)$id,
                                    'id_category' => (int)$id_category
                                )
                            );
                        }
                    }
                } else {
                    return $this->displayError($this->l('No categories selected')).$this->showMetaVariables($class).$this->getMetaFormSpe($class, $id);
                }
                $active = 'metagenerator';
                $redirect = true;
                $hashtag = $class;
            }
        }
        if ($redirect) {
            $link = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&oactive='.$active.'#'.$hashtag;
            $admin_dir = explode('/', _PS_ADMIN_DIR_);
            $admin_dir = $admin_dir[count($admin_dir)-1];
            if (strpos($link, $admin_dir) === false) {
                $link = Tools::getHttpHost(true).__PS_BASE_URI__.$admin_dir.'/'.$link;
            }
            Tools::redirect($link);
        }
        
        return false;
    }

    public function getInfoRefresh($prefix)
    {
        $this->smarty->assign(array(
                        'START_TIME' => Configuration::get($prefix.'START_TIME'),
                        'END_TIME' => Configuration::get($prefix.'END_TIME'),
                        'STAGE' => Configuration::get($prefix.'STAGE'),
                        'PROGRESS' => Configuration::get($prefix.'PROGRESS'),
                        'LOOPS' => Configuration::get($prefix.'LOOPS'),
                        'STATE' => Configuration::get($prefix.'STATE'),
                        'ACT' => Configuration::get($prefix.'ACT'),
                    ));
        return $this->display(__FILE__, 'views/templates/admin/suivi.tpl');
    }

    public function getInfoRefreshReport($prefix)
    {
        $this->smarty->assign(array(
                        'START_TIME' => Configuration::get($prefix.'START_TIME'),
                        'END_TIME' => Configuration::get($prefix.'END_TIME'),
                        'STAGE' => Configuration::get($prefix.'STAGE'),
                        'PROGRESS' => Configuration::get($prefix.'PROGRESS'),
                        'LOOPS' => Configuration::get($prefix.'LOOPS'),
                        'STATE' => Configuration::get($prefix.'STATE'),
                        'ACT' => Configuration::get($prefix.'ACT'),
                    ));
        $tab = $this->display(__FILE__, 'views/templates/admin/suivi.tpl');
        $end = false;
        if (Tools::strlen(Configuration::get($prefix.'END_TIME')) > 0) {
            $end = true;
        }
        return json_encode(
            array(
                'tab' => $tab,
                'end' => $end
            )
        );
    }

    public function showTask()
    {
        $tab_task = ['bulkMetaProducts', 'bulkMetaCategories', 'bulkMetaCMS', 'bulkMetaSuppliers', 'bulkMetaManufacturers', 'bulkImProducts', 'bulkImCategories', 'bulkImCMS', 'bulkImSuppliers', 'bulkImManufacturers'];
        $tab_smarty = [
            'uriec_seo' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_seo/',
            'trad_tasklaunched' => $this->l('Task launched'),
            'ec_id_shop' => (int)$this->context->shop->id,
        ];
        foreach ($tab_task as $task) {
            $tab_smarty[$task] = $this->context->link->getModuleLink($this->name, $task, ['ec_token' => $this->ec_token, 'id_shop' => $this->context->shop->id]);
        }
        $this->smarty->assign($tab_smarty);
        return $this->display(__FILE__, 'views/templates/admin/task.tpl');
    }

    public function showStats()
    {
        $id_shop = (int)$this->context->shop->id;
        $tab_list = $this->tab_list;
        $tab_list['other'] = array(
            'trad' => $this->l('Other'),
            'spe' => false,
        );
        $sumMetaError = Db::getInstance()->executes('SELECT sum(missing) as missing, sum(duplicate) as duplicate, sum(too_short) as too_short, sum(too_long) as too_long, sum(total) as total, (sum(missing)+sum(duplicate)+sum(too_short)+sum(too_long)) as total_error, type_meta FROM '._DB_PREFIX_.'ec_seo_report WHERE id_shop = '.(int)$id_shop.' GROUP BY type_meta ORDER BY FIELD(type_meta, "meta_title", "meta_description", "h1") ASC');
        foreach ($sumMetaError as &$info) {
            if ($info['type_meta'] == 'meta_title') {
                $name = $this->l('Meta title');
            } else if ($info['type_meta'] == 'meta_description') {
                $name = $this->l('Meta description');
            } else {
                $name = $this->l('H1');
            }
            $info['type_meta_key'] = $info['type_meta'];
            $info['type_meta']  = $name;
            //$info['type_meta'] = Tools::ucfirst(str_replace('_', ' ', $info['type_meta']));
        }

        $sumMeta2 = Db::getInstance()->executes('SELECT page, type_meta, sum(missing) as missing, sum(duplicate) as duplicate, sum(too_short) as too_short, sum(too_long) as too_long, sum(total), (sum(missing)+sum(duplicate)+sum(too_short)+sum(too_long)) as total_error FROM '._DB_PREFIX_.'ec_seo_report WHERE id_shop = '.(int)$id_shop.' GROUP BY page, type_meta 
        ORDER BY FIELD(page, "product", "category", "cms", "manufacturer", "supplier", "other") ASC');
        $sumMetaByPage = array();
        foreach ($sumMeta2 as $val) {
            $sumMetaByPage[$val['page']]['page_name'] = $tab_list[$val['page']];
            $sumMetaByPage[$val['page']][$val['type_meta']] = $val;
        }
        $link_meta = $this->context->link->getAdminLink('AdminMeta', true);
        $link_preferences = $this->context->link->getAdminLink('AdminPreferences', true);
        $link_modules = $this->context->link->getAdminLink('AdminPsMboModule', true);
        $link_performance= $this->context->link->getAdminLink('AdminPerformance', true);
        $link_maintenance = $this->context->link->getAdminLink('AdminMaintenance', true);
        $module_site_map = Db::getInstance()->getValue('SELECT id_module FROM '._DB_PREFIX_.'module WHERE name ="gsitemap" AND active = 1');
        $date_last_report = Db::getInstance()->getValue('SELECT `date` FROM '._DB_PREFIX_.'ec_seo_report WHERE id_shop = '.(int)$id_shop);
        $count_excellent = Db::getInstance()->getValue('SELECT count(id) FROM '._DB_PREFIX_.'ec_seo_score_lang WHERE score >= 80 AND id_shop = '.(int)$id_shop);
        $count_acceptable = Db::getInstance()->getValue('SELECT count(id) FROM '._DB_PREFIX_.'ec_seo_score_lang WHERE score >= 50 AND score < 80 AND id_shop = '.(int)$id_shop);
        $count_pasbon = Db::getInstance()->getValue('SELECT count(id) FROM '._DB_PREFIX_.'ec_seo_score_lang WHERE score < 50 AND id_shop = '.(int)$id_shop);
        $total_for_donuts = Db::getInstance()->executes('SELECT (sum(missing) + sum(duplicate) + sum(too_short) + sum(too_long)) as total, page FROM '._DB_PREFIX_.'ec_seo_report WHERE id_shop = '.(int)$id_shop.' GROUP BY page ORDER BY FIELD(page, "product", "category", "cms", "manufacturer", "supplier", "other") ASC');
        foreach ($total_for_donuts as &$val) {
            $val['trad'] = $tab_list[$val['page']]['trad'];
        }
        $jauges_req = Db::getInstance()->executes('SELECT ((1-((sum(missing)+sum(duplicate)+sum(too_short)+sum(too_long))/(sum(total)*2)))*100) as moyenne, type_meta  FROM '._DB_PREFIX_.'ec_seo_report WHERE id_shop = '.(int)$id_shop.' GROUP by type_meta');
        $jauges = array();
        foreach ($jauges_req as $val_j) {
            $jauges[$val_j['type_meta']] = (int)$val_j['moyenne'];
        }
        $this->smarty->assign(array(
            'sumMetaError' => $sumMetaError,
            'sumMetaByPage' => $sumMetaByPage,
            'link_meta' => $link_meta,
            'link_preferences' => $link_preferences,
            'link_modules' => $link_modules,
            'link_performance' => $link_performance,
            'link_maintenance' => $link_maintenance,
            'count_excellent' => $count_excellent,
            'count_acceptable' => $count_acceptable,
            'count_pasbon' => $count_pasbon,
            'total_for_donuts' => $total_for_donuts,
            'jauges' => $jauges,
            'PS_REWRITING_SETTINGS' => Configuration::get('PS_REWRITING_SETTINGS'),
            'PS_SSL_ENABLED' => Configuration::get('PS_SSL_ENABLED'),
            'PS_SSL_ENABLED_EVERYWHERE' => Configuration::get('PS_SSL_ENABLED_EVERYWHERE'),
            'PS_CANONICAL_REDIRECT' => Configuration::get('PS_CANONICAL_REDIRECT'),
            'PS_ROUTE_product_rule' => Configuration::get('PS_ROUTE_product_rule') == '{category:/}{rewrite}{-:ean13}-{id}{-:id_product_attribute}.html',
            'PS_ROUTE_category_rule' => Configuration::get('PS_ROUTE_category_rule') == '{rewrite}-{id}',
            'PS_ROUTE_manufacturer_rule' => Configuration::get('PS_ROUTE_manufacturer_rule') == 'brand/{rewrite}-{id}',
            'PS_ROUTE_supplier_rule' => Configuration::get('PS_ROUTE_supplier_rule') == 'supplier/{rewrite}-{id}',
            'PS_ROUTE_cms_rule' => Configuration::get('PS_ROUTE_cms_rule') == 'content/{rewrite}-{id}',
            'PS_ROUTE_cms_category_rule' => Configuration::get('PS_ROUTE_cms_category_rule') == 'content/category/{rewrite}-{id}',
            'PS_SHOP_ENABLE' => Configuration::get('PS_SHOP_ENABLE'),
            'PS_SMARTY_CACHE' => Configuration::get('PS_SMARTY_CACHE'),
            'module_site_map' => $module_site_map,
            'robot_created' => Tools::file_get_contents(_PS_ROOT_DIR_.'/robots.txt'),
            '_PS_MODE_DEV_' => _PS_MODE_DEV_,
            'm_missing' => $this->l('Missing'),
            'm_duplicate' => $this->l('Duplicate'),
            'm_tooshort' => $this->l('Too short'),
            'm_toolong' => $this->l('Too long'),
            'm_error' => $this->l('Error'),
            'm_opti' => $this->l('Optimization possible by page types'),
            'm_meta_title' => $this->l('Meta title'),
            'm_meta_description' => $this->l('Meta description'),
            'm_h1' => $this->l('H1'),
            'uri_ec_seo' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_seo/',
            'last_report' => $this->getLastReport(),
            'date_last_report' => $date_last_report,
            'genExcel' => $this->context->link->getModuleLink($this->name, 'genExcel', ['ec_token' => $this->ec_token, 'id_shop' => $this->context->shop->id]),
            'token' => $this->ec_token,
        ));
        return $this->display(__FILE__, 'views/templates/admin/stats.tpl');
    }

    public function getReportProgress()
    {
        $current = Configuration::get('TASK_GEN_EXCEL_AVANCEMENT');
        $total = Configuration::get('TASK_GEN_EXCEL_TOTAL');
        $last_report = $this->getLastReport();
        $date = Db::getInstance()->getValue('SELECT date FROM '._DB_PREFIX_.'ec_seo_report');
        return json_encode(array('current' => $current, 'total' => $total, 'last_report' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_seo/report/'.$last_report,'date' => $date));
    }

    public function showBaliseAlt()
    {
        $id_shop = (int)$this->context->shop->id;
        $variablesMeta = array(
            '%current_content%' => $this->l('Current content'),
            '%shop_name%' => $this->l('Shop name'),
            '%id_image%' => $this->l('Image ID'),
            '%product_name%' => $this->l('Product name'),
            '%manufacturer%' => $this->l('Manufacturer name'),
            '%supplier%' => $this->l('Supplier name'),
            '%reference%' => $this->l('Product reference'),
            '%supplier_reference%' => $this->l('Supplier product reference'),
            '%ean13%' => $this->l('Product ean13'),
            '%category_default%' => $this->l('Default category name'),
            '%description_short|limit|%' => $this->l('Product description short with limitation of the number of characters via the parameter "limit"'),
            '%description|limit|%' => $this->l('Product description with limitation of the number of characters via the parameter "limit"'),
            '%position%' => $this->l('Position of the image'),
            /* '%attribute_reference%' => $this->l('Attribute reference'),
            '%attribute_ean13%' => $this->l('Attribute ean13'), */
        );
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $features = Db::getInstance()->executes('SELECT id_feature, name FROM '._DB_PREFIX_.'feature_lang WHERE id_lang = '.(int)$id_lang);
        foreach ($features as $feature) {
            $variablesMeta['%fea_'.$feature['name'].'%'] = $this->l('Feature').' '.$feature['name'];
        }
        $form = $this->getBaliseGenForm();
        $this->smarty->assign(array(
            'balisealt_form' => $form,
            'variablesMeta' => $variablesMeta,
            'uriec_seo' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_seo/',
            'ec_id_shop' => (int)$this->context->shop->id,
            'table_modif' => $this->getBaTableTask($id_shop),
            'token' => $this->ec_token,
        ));
        return $this->display(__FILE__, 'views/templates/admin/baliseAlt.tpl');
    }

    protected function getBaliseGenForm()
    {

        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Balise alt General'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Image Alt tag'),
                        'name' => 'balise_alt',
                        'lang' => true,
                        'col' => 4,
                    ),
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'balisealtgen';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBaliseAltGen';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $info = Db::getInstance()->ExecuteS('SELECT balise_alt, id_lang FROM '._DB_PREFIX_.'ec_seo_bag_gen WHERE id_shop = '.(int)$this->context->shop->id);
        $values = array();
        foreach ($info as $val) {
            $values['balise_alt'][$val['id_lang']] = $val['balise_alt'];
        }
        
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($form));
    }

    public function getBaTableTask($id_shop, $refresh = false, $search = null, $page = null)
    {
        if ($page == null) {
            $page = 1;
        }
        $context = Context::getContext();
        $pagination = Configuration::get('EC_SEO_BA_PAGINATION');
        if (($pagination > 0) == false) {
            Configuration::updateValue('EC_SEO_BA_PAGINATION', 20);
            $pagination = 20;
        }
        $p1 = ($page*$pagination)-$pagination;
        $p2 = $pagination;
        
        $req = 'SELECT esbt.id_product, name, esbt.id_image, legend, esbt.id_lang FROM '._DB_PREFIX_.'ec_seo_ba_temp esbt
        LEFT JOIN '._DB_PREFIX_.'image_lang l ON (l.id_image = esbt.id_image)
        LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = esbt.id_product)
        WHERE esbt.id_shop = '.(int)$id_shop.' AND pl.id_shop = '.(int)$id_shop.' AND l.id_lang = esbt.id_lang AND pl.id_lang = esbt.id_lang';

        
        if ($search != null) {
            parse_str($search, $searchs);
            if (!empty($searchs['name'])) {
                $req .= ' AND name like "%'.pSQL($searchs['name']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['legend'])) {
                $req .= ' AND legend like "%'.pSQL($searchs['legend']).'%"';
                $rsearch = true;
            }
            if (!empty($searchs['id_product'])) {
                $req .= ' AND esbt.id_product like "%'.(int)$searchs['id_product'].'%"';
                $rsearch = true;
            }
            if (!empty($searchs['id_image'])) {
                $req .= ' AND esbt.id_image like "%'.(int)$searchs['id_image'].'%"';
                $rsearch = true;
            }
        } else {
            $searchs= array();
        }
        $countInfo = count(Db::getInstance()->ExecuteS($req));
        $req.= ' ORDER by esbt.id_product LIMIT '.$p1.','.$p2;
        $infos = Db::getInstance()->executes($req);
        $nbpage = ($countInfo/$pagination);
        if (is_float($nbpage)) {
            $nbpage = (int)$nbpage + 1;
        }
        foreach ($infos as &$info) {
            $id = $info['id_product'];
            $id_lang  = $info['id_lang'];
            $info['lang'] = Language::getIsoById($id_lang);
            $obj = new Product($id, false, null, $id_shop);
            $link = $this->context->link->getProductLink($obj, null, null, null, $id_lang, $id_shop);

            $info['link'] = $link;
        }
        $this->smarty->assign(array(
            'lines' => $infos,
            'countInfo' => $countInfo,
            'pagination' => $pagination,
            'nbpage' => $nbpage,
            'pageActif' => $page,
            'searchs' => $searchs,
            'refresh' => $refresh,
            'token' => $this->ec_token
        ));
        return $this->display(__FILE__, 'views/templates/admin/tabBaliseAlt.tpl');
    }

    public function getLastReport()
    {
        $report_dir = dirname(__FILE__).'/report/';
        $last_date = false;
        $last_file = false;
        foreach (scandir($report_dir) as $file) {
            if ($file == '.'  || $file == '..' || $file == 'index.php') {
                continue;
            }
            $date_modif = filemtime($report_dir. $file);
            if (($last_date == false || $last_date < $date_modif)) {
                $last_date = $date_modif;
                $last_file = $file;
            }
        }
        return $last_file;
    }

    //RedirectionImage
    protected function renderFormRI()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'ec_seo_config_redirectimage';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = '';
        $helper->submit_action = 'submitEc_seo_redirectimageModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => array(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigFormRI()));
    }

    protected function getConfigFormRI()
    {
        $oh = '<';
        $ch = '>';
        $sh = '/';
        $brh= $oh.'br'.$ch;
        $thumbnail = $oh.'img src="../modules/ec_seo/views/img/'.Configuration::get('EC_SEO_REDIRECTIMAGE_NAME').'" class="imgm img-thumbnail" alt=""'.$sh.$ch;
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Default image redirection'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'file',
                        'desc' => $this->l('Image 404 will be redirect to this image').$brh.$brh.$thumbnail,
                        'name' => 'EC_SEO_REDIRECTIMAGE_IMAGE',
                        'label' => $this->l('Redirection image'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
              /*   'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Back to list'),
                        'icon' => 'process-icon-back'
                        )
                ), */
            ),
        );
    }

    protected function renderListRI()
    {
        $fields_list = array();
        $fields_list['id_ec_seo_redirectimage'] = array(
            'title' => $this->l('ID'),
            'type' => 'text',
            'search' => true,
            'class' => 'fixed-width-xs',
            'orderby' => true,
        );
        $fields_list['url'] = array(
                'title' => $this->l('Url'),
                'type' => 'text',
                'search' => true,
                'orderby' => false,
        );
        $fields_list['cpt'] = array(
                'title' => $this->l('Counter'),
                'type' => 'text',
                'search' => true,
                'orderby' => true,
        );
        $fields_list['img_redirect'] = array(
                'title' => $this->l('Redirect image'),
                'type' => 'text',
                'search' => true,
                'orderby' => false,
        );

        $fields_list['default'] = array(
                'title' => $this->l('Default redirection'),
                'type' => 'text',
                'search' => true,
                'orderby' => false,
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_ec_seo_redirectimage';
        $helper->actions = array('edit');
        $helper->show_toolbar = true;
        $helper->imageType = 'jpg';
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addec_seo_redirectimage&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        $helper->title = $this->l('Redirect images');
        $helper->table = 'ec_seo_redirectimage';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentRI($this->context->language->id);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }

    public function getListContentRI()
    {
        $pagination = Tools::getValue('ec_seo_redirectimage_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilterec_seo_redirectimage');
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }
        $req = 'SELECT *, id as id_ec_seo_redirectimage FROM '._DB_PREFIX_.'ec_seo_redirectimage';
        
        $search = Tools::getValue('submitFilterec_seo_redirectimage');
        if ($search) {
            $req.= ' WHERE 1';
            $id = Tools::getValue('ec_seo_redirectimageFilter_id');
            if (!empty($id)) {
                $req.= ' AND id like "%'.(int)$id.'%"';
            }
            
            $url = Tools::getValue('ec_seo_redirectimageFilter_url');
            if (!empty($url)) {
                $req.= ' AND url like "%'.pSQL($url).'%"';
            }
            
            $cpt = Tools::getValue('ec_seo_redirectimageFilter_cpt');
            if (!empty($cpt)) {
                $req.= ' AND cpt like "%'.(int)$cpt.'%"';
            }
            
            $def = Tools::getValue('ec_seo_redirectimageFilter_default');
            if (Tools::strlen($def) > 0) {
                $req.= ' AND `default` = '.(int)$def.'';
            }
        }
        $orderby = Tools::getValue('ec_seo_redirectimageOrderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue('ec_seo_redirectimageOrderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        
        $res = Db::getInstance()->ExecuteS($req);
        foreach ($res as $key => $value) {
            if (!$value['default']) {
                $res[$key]['img_redirect'] = Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_seo/views/img/'.$value['img_redirect'];
            }
        }
        return array('res' => $res, 'count' => $count);
    }

    protected function renderForm2($id = null)
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        if ($id == null) {
            $helper->submit_action = 'submitAddSeoImage';
        } else {
            $helper->submit_action = 'submitEditSeoImage';
        }
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
        if ($id != null) {
            $info = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ec_seo_redirectimage WHERE id = '.(int)$id);
            $values = array(
                'url' => $info['url'],
                'img_redirect' => $info['img_redirect'],
                'default' => $info['default'],
                'id' => $id
            );
        } else {
            $values = array(
                'url' => null,
                'img_redirect' => null,
                'default' => null
            );
        }
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        
        $oh = '<';
        $ch = '>';
        $brh= $oh.'br'.$ch;
        $thumbnail = "";
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    
                    array(
                        'type' => 'text',
                        'name' => 'url',
                        'label' => $this->l('Url'),
                        'desc' => $this->l('Image url to redirect'),
                        'col' => 3,
                        'required' => true
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Image by default'),
                        'name' => 'default',
                        'is_bool' => true,
                        'required' => true,
                        'desc' => $this->l('Use default image redirection'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        //'col' => 3,
                        'type' => 'file',
                        'desc' => $this->l('Image 404 will be redirect to this image').$brh.$brh.$thumbnail,
                        'name' => 'img_redirect',
                        'label' => $this->l('Redirection image'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&oactive=redirectionimage&btl=1',
                        'title' => $this->l('Back to list'),
                        'icon' => 'process-icon-back'
                        )
                ),
            ),
        );
        if ($id != null) {
            $form['form']['input'][] = array(
                'col' => 1,
                'type' => 'hidden',
                'name' => 'id',
            );
        }
        return $helper->generateForm(array($form));
    }

    protected function postProcessRI()
    {
        $redirect = false;
        $active = 'redirectionimage';
        if (Tools::strlen($_FILES['EC_SEO_REDIRECTIMAGE_IMAGE']['name']) > 0) {
            $ext = Tools::strtolower(Tools::substr(strrchr($_FILES['EC_SEO_REDIRECTIMAGE_IMAGE']['name'], '.'), 1));
            $tmp_name = $_FILES["EC_SEO_REDIRECTIMAGE_IMAGE"]["tmp_name"];
            $name = "redirect.".$ext;
            if (!in_array($ext, $this->img_extension)) {
                return $this->displayError($this->l('File must be an image')).$this->showConfig('redirectionimage', true);
            }
      
            move_uploaded_file($tmp_name, $this->local_path.'views/img/'.$name);

            Configuration::updateValue('EC_SEO_REDIRECTIMAGE_NAME', $name);
            $redirect = true;
        }
        if ($redirect) {
            Tools::redirect($this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&oactive='.$active);
        }
    }


    public function processAdd()
    {
        $url = Tools::getValue('url');
        $default = Tools::getValue('default');
        if (!Validate::isUrl($url)) {
            return $this->displayError($this->l('Url incorrect')).$this->renderForm2();
        }
        if (!$default) {
            if (Tools::strlen($_FILES['img_redirect']['name']) > 0) {
                $ext = Tools::strtolower(Tools::substr(strrchr($_FILES['img_redirect']['name'], '.'), 1));
                $tmp_name = $_FILES["img_redirect"]["tmp_name"];
                $name = $_FILES['img_redirect']['name'];
                if (!in_array($ext, $this->img_extension)) {
                    return $this->displayError($this->l('File must be an image')).$this->renderForm2();
                }
                if (!move_uploaded_file($tmp_name, $this->local_path.'views/img/'.$name)) {
                    return $this->displayError($this->l('Error')).$this->renderForm2();
                }
                Db::getinstance()->insert(
                    'ec_seo_redirectimage',
                    array(
                        'url' => pSQL($url),
                        'default' => (bool)$default,
                        'img_redirect' => pSQL($name)
                    )
                );
                Tools::redirect($this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&oactive=redirectionimage');
            } else {
                return $this->displayError($this->l('You must add an image')).$this->renderForm2();
            }
        } else {
            Db::getinstance()->insert(
                'ec_seo_redirectimage',
                array(
                    'url' => pSQL($url),
                    'default' => (bool)$default,
                )
            );
            Tools::redirect($this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&oactive=redirectionimage');
        }
    }
    
    public function processEdit($id)
    {
        $url = Tools::getValue('url');
        $default = Tools::getValue('default');
        $name = null;
        if (!Validate::isUrl($url)) {
            return $this->displayError($this->l('Url incorrect')).$this->renderForm2($id);
        }
        if (!$default) {
            if (Tools::strlen($_FILES['img_redirect']['name']) > 0) {
                $ext = Tools::strtolower(Tools::substr(strrchr($_FILES['img_redirect']['name'], '.'), 1));
                $tmp_name = $_FILES["img_redirect"]["tmp_name"];
                $name = $_FILES['img_redirect']['name'];
                if (!in_array($ext, $this->img_extension)) {
                    return $this->displayError($this->l('File must be an image')).$this->renderForm2();
                }
                move_uploaded_file($tmp_name, $this->local_path.'views/img/'.$name);
            } else {
                return $this->displayError($this->l('You must add an image')).$this->renderForm2().$this->renderList();
            }
        }
        Db::getinstance()->update('ec_seo_redirectimage', array('url' => pSQL($url), 'default' => (bool)$default, 'img_redirect' => pSQL($name)), 'id = '.(int)$id);
        
        Tools::redirect($this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&oactive=redirectionimage');
    }

    private static function removeOldHtAccessRules()
    {
        $path = _PS_ROOT_DIR_.'/.htaccess';
        $htaccessContent = Tools::file_get_contents($path);
        $start = strpos($htaccessContent, '#START EC_REDIRECT');
        $end = strpos($htaccessContent, '#END EC_REDIRECT');
        if ($start !== false && $end !== false) {
            $toReplace = Tools::substr($htaccessContent, $start, $end + Tools::strlen('#END EC_REDIRECT'));
            if (!empty($toReplace)) {
                $newHtaccesContent = trim(str_replace($toReplace, '', $htaccessContent));
                file_put_contents($path, $newHtaccesContent);
                Tools::generateHtaccess();
            }
        }
    }

    public function hookActionHtaccessCreate()
    {
        if (Configuration::get('EC_SEO_REDIRECTIMAGE_HTACCESS')) {
            $path = _PS_ROOT_DIR_.'/.htaccess';
            $content = Tools::file_get_contents($path);
            $content2 = "\n\n#START EC_REDIRECT\nRewriteEngine on\nRewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f\nRewriteRule \.(gif|jpe?g|png|bmp) ".Tools::getHttpHost(true).__PS_BASE_URI__."modules/ec_seo/redirect.php?token=".Configuration::getGlobalValue('EC_TOKEN_SEO')."&url=%{REQUEST_URI} [NC,L]\n#END EC_REDIRECT\n";
            $content = str_replace("# Dispatcher", $content2."\n\n# Dispatcher", $content);
            file_put_contents($path, $content);
        }
    }
    //END redirection image

    //Maillage internet
    protected function getConfigMI($class)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'configMI_'.$class;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfigMI_'.$class;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => array('EC_SEO_MAX_REPLACE_'.$class => Configuration::get('EC_SEO_MAX_REPLACE_'.$class)),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings').' '.$class,
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'EC_SEO_MAX_REPLACE_'.$class,
                        'label' => $this->l('Maximum number of replacements'),
                        'desc' => $this->l('0 for unlimited')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        return $helper->generateForm(array($form));
    }

    protected function renderListMI($class, $spe = false)
    {
        $fields_list = array();
        $fields_list['id_ec_list_mi_'.$class] = array(
            'title' => $this->l('ID'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['keyword'] = array(
                'title' => $this->l('Keyword'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
        );
        $fields_list['url'] = array(
            'title' => $this->l('Url'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        if ($spe) {
            $fields_list['categories'] = array(
                'title' => $this->l('Categories'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
            );
        }
        
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_ec_list_mi_'.$class;
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'_mi_'.$class.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        $helper->title = $class.' interal mesh';
        $helper->table = 'ec_list_mi_'.$class;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentMI($class, $spe);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentMI($class, $spe)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue('ec_list_mi_'.$class.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilterec_list_mi_'.$class.'');
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT espm.id, espm.id as id_ec_list_mi_'.$class.', ml.keyword, ml.url FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mi espm
            LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$class.'_mi_lang ml ON (ml.id_seo_'.$class.'_mi = espm.id)
            WHERE id_lang = '.(int)$id_lang.' AND espm.id_shop = '.(int)$id_shop.'
        ';
        
        $orderby = Tools::getValue('ec_list_mi_'.$class.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue('ec_list_mi_'.$class.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        
        $res = Db::getInstance()->ExecuteS($req);
        if ($spe) {
            foreach ($res as $key => &$val) {
                $categories = Db::getInstance()->executes('SELECT cl.id_category, name FROM '._DB_PREFIX_.'category_lang cl
                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$class.'_mi_cat ec ON (ec.id_category = cl.id_category) 
                LEFT JOIN '._DB_PREFIX_.'ec_seo_'.$class.'_mi m ON (m.id = ec.id_seo_'.$class.'_mi)
                WHERE id_seo_'.$class.'_mi = '.(int)$val['id'].' AND id_lang = '.(int)$id_lang.' AND cl.id_shop = m.id_shop');

                $concat_cat = '';
                foreach ($categories as $key => $cat) {
                    $id_category = $cat['id_category'];
                    if ($key == 0) {
                        $concat_cat .= $cat['name'].' ('.$id_category.')';
                    } else {
                        $concat_cat .= ', '.$cat['name'].' ('.$id_category.')';
                    }
                }
                $val['categories'] = $concat_cat;
            }
        }
        
        return array('res' => $res, 'count' => $count);
    }

    protected function getMIForm($class, $spe, $id = null)
    {
        $values = array();
        $categories = array();
        if ($id == null) {
            $values['id'] = 0;
            foreach (Language::getLanguages(true) as $lang) {
                $id_lang = $lang['id_lang'];
                $values['keyword'][$id_lang] = null;
                $values['url'][$id_lang] = null;
            }
        } else {
            $values['id'] = $id;
            if ($spe) {
                $r_categories = Db::getInstance()->executes('SELECT id_category FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mi_cat WHERE id_seo_'.$class.'_mi = '.(int)$id);
                foreach ($r_categories as $category) {
                    $categories[] = $category['id_category'];
                }
            }
            
            $info = Db::getInstance()->ExecuteS('SELECT keyword, url, id_lang FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mi_lang WHERE id_seo_'.$class.'_mi = '.(int)$id);
            foreach ($info as $val) {
                $values['keyword'][$val['id_lang']] = $val['keyword'];
                $values['url'][$val['id_lang']] = $val['url'];
            }
        }
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $class,
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Keyword'),
                        'name' => 'keyword',
                        'lang' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Url'),
                        'name' => 'url',
                        'lang' => true
                    ),
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=internalmesh&token='.Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                        'id' => 'ecmetacancel_'.$class
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        if ($spe) {
            $form['form']['input'][] = array(
                'type' => 'categories',
                'label' => $this->l('Select categories'),
                'name' => 'categories',
                'class'=>'cat-test',
                'tree' => array(
                    'root_category' => 2,
                    'id' => 'id_category',
                    'name' => 'name_category',
                    'selected_categories' => $categories,
                    'use_checkbox' => true,
                )
            );
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'mi_'.$class;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        if ($id == null) {
            $helper->submit_action = 'submitMI'.$class;
        } else {
            $helper->submit_action = 'submitEditMI'.$class;
        }
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', true)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($form));
    }

    public function hookDisplayContentWrapperBottom($params)
    {
            return $this->hookEcSeoDescription2($params);
    }

    public function checkRedirection()
    {
        $uri_var = Tools::getHttpHost(true).$_SERVER['REQUEST_URI'];
        $info = Db::getInstance()->getRow('SELECT lienS, typeRed FROM '._DB_PREFIX_.'ec_seo_redirect 
            WHERE old_link = "'.$uri_var.'" AND onlineS = 1 AND id_shop = '.(int)$this->context->shop->id);
        if (!$info) {
            $uri_var = 'http://'.Tools::getHttpHost(false).$_SERVER['REQUEST_URI'];
            $info = Db::getInstance()->getRow('SELECT lienS, typeRed FROM '._DB_PREFIX_.'ec_seo_redirect 
            WHERE old_link = "'.pSQL($uri_var).'" AND onlineS = 1 AND id_shop = '.(int)$this->context->shop->id);
        }
        if (!$info) {
            $uri_var = str_replace('www.', '', $uri_var);
            $info = Db::getInstance()->getRow('SELECT lienS, typeRed FROM '._DB_PREFIX_.'ec_seo_redirect 
            WHERE old_link = "'.pSQL($uri_var).'" AND onlineS = 1 AND id_shop = '.(int)$this->context->shop->id);
        }
        if ($info) {
            if ($info['typeRed'] == 'homepage') {
                header('HTTP/1.1 301 Moved Permanently');
                Tools::redirect(Tools::getHttpHost(true).__PS_BASE_URI__);
                exit();
            } elseif (isset($info['lienS']) && $info['lienS'] != '' && ($info['typeRed'] == '301' || $info['typeRed'] == '302')) {
                if ($info['typeRed'] == '301') {
                    header('HTTP/1.1 301 Moved Permanently');
                    Tools::redirect($info['lienS']);
                    exit();
                } elseif ($info['typeRed'] == '302') {
                    header('HTTP/1.1 302 Moved Temporarily');
                    Tools::redirect($info['lienS']);
                    exit();
                }
            }
        }
        
        $controller = Tools::getValue('controller');
        $module = Tools::getValue('module');
        if (strlen($module) > 0) {
            return;
        }
        if ($controller == 'product') {
            $id = Tools::getValue('id_product');
            $idlang = $this->context->language->id;
            $info = Db::getInstance()->getRow('SELECT lienS, typeRed 
                                    FROM '._DB_PREFIX_.'ec_seo 
                                    WHERE idP='.(int)$id.' 
                                    AND idL='.(int)$idlang.' 
                                    AND onlineS=1 
                                    AND type="Product" 
                                    AND idshop='.(int)$this->context->shop->id);
        }
       
        if ($controller == 'category') {
            $info = Db::getInstance()->getRow('SELECT lienS, typeRed FROM '._DB_PREFIX_.'ec_seo 
            WHERE idP='.(int)Tools::getValue('id_category').' AND idL='.(int)$this->context->language->id.' AND onlineS=1 AND type="Category" AND idshop='.(int)$this->context->shop->id);
        }
        if ($controller == 'cms' && !Tools::getValue('id_cms_category')) {
            $info = Db::getInstance()->getRow('SELECT lienS, typeRed FROM '._DB_PREFIX_.'ec_seo 
            WHERE idP='.(int)Tools::getValue('id_cms').' AND idL='.(int)$this->context->language->id.' AND onlineS=1 AND type="Cms" AND idshop='.(int)$this->context->shop->id);
        }
        if ($controller == 'supplier') {
            $info = Db::getInstance()->getRow('SELECT lienS, typeRed FROM '._DB_PREFIX_.'ec_seo 
            WHERE idP='.(int)Tools::getValue('id_supplier').' AND idL='.(int)$this->context->language->id.' AND onlineS=1 AND type="Supplier" AND idshop='.(int)$this->context->shop->id);
        }
        if ($controller == 'manufacturer') {
            if ((int)Tools::getValue('id_manufacturer') > 0) {
                $info = Db::getInstance()->getRow('SELECT lienS, typeRed FROM '._DB_PREFIX_.'ec_seo 
                WHERE idP='.(int)Tools::getValue('id_manufacturer').' AND idL='.(int)$this->context->language->id.' 
                AND onlineS=1 AND type="Manufacturer" AND idshop='.(int)$this->context->shop->id);
            }
        }
        if (!$info) {
            return;
        }
        if ($info['typeRed'] == 'homepage') {
            header('HTTP/1.1 301 Moved Permanently');
            Tools::redirect(Tools::getHttpHost(true).__PS_BASE_URI__);
            exit();
        } elseif ($info['typeRed'] == 'categorydefault') {
            header('HTTP/1.1 301 Moved Permanently');
            $product = new Product($id);
            $category = new Category($product->id_category_default, (int)$idlang);
            $category_default = $category->getLink();
            Tools::redirect($category_default);
            exit();
        } elseif (isset($info['lienS']) && $info['lienS'] != '' && ($info['typeRed'] == '301' || $info['typeRed'] == '302')) {
            if ($info['typeRed'] == '301') {
                header('HTTP/1.1 301 Moved Permanently');
                Tools::redirect($info['lienS']);
                exit();
            } elseif ($info['typeRed'] == '302') {
                header('HTTP/1.1 302 Moved Temporarily');
                Tools::redirect($info['lienS']);
                exit();
            }
        }
    }

    public function hookActionCategoryGridDataModifier($params)
    {
        $id_shop = (int)$this->context->shop->id;
        $datas = $params['data']->getRecords()->all();
        foreach ($datas as $key => $cat) {
            $score = $this->getGlobalNote('category', $cat['id_category'], $id_shop)['global'];
            Db::getinstance()->insert(
                'ec_seo_score',
                array(
                    'id' => (int)$cat['id_category'],
                    'page' => 'category',
                    'score' => (int)$score,
                    'id_shop' => (int)$id_shop,
                ),
                false,
                true,
                Db::ON_DUPLICATE_KEY
            );
            
            $datas[$key]['score']= $this->ShowSeoScoreListing('category', $cat['id_category'], $id_shop);
        }
        $records = new RecordCollection($datas);
        $params['data'] = new GridData($records, $params['data']->getRecordsTotal(), $params['data']->getQuery());
    }
    public function hookActionCategoryGridQueryBuilderModifier($params)
    {
        $searchQueryBuilder = $params['search_query_builder'];
        $id_shop = (int)$this->context->shop->id;
        $searchQueryBuilder->addSelect('ecs.score')
            ->leftJoin(
                'c',
                _DB_PREFIX_. 'ec_seo_score',
                'ecs',
                'c.id_category = ecs.id AND page="category" AND ecs.id_shop = '.(int)$id_shop
            )
        ;
        $allValues = Tools::getAllValues();
        if (isset($allValues['category']['filters']['score'])) {
            $filter_seo = $allValues['category']['filters']['score'];
            if ($filter_seo > 0) {
                if ($filter_seo != 4) {
                    $signe = '<';
                    if ($filter_seo == 3) {
                        $val = 80;
                    } else if ($filter_seo == 2) {
                        $val = 50;
                    } else {
                        $val = 25;
                    }
                } else {
                    $signe = '>=';
                    $val = 80;
                }
                $searchQueryBuilder->andWhere('ecs.score '.$signe.' '.$val);
            }
        }
    }

    

    public function hookActionCategoryGridDefinitionModifier(&$hookParams)
    {
        $definition = $hookParams['definition'];
        $columns = $definition->getColumns();
        $scoreColumn = (new DataColumn('score'))
        ->setName($this->l('SEO Score'))
        ->setOptions([
            'field' => 'score',
        ])
        ;
        $columns->addAfter('active', $scoreColumn);
        $filters = $definition->getFilters();
        $filters->add(
            (new Filter('score', ChoiceType::class))
            ->setTypeOptions([
                'choices' => [
                    $this->l('Very Bad') => 1,
                    $this->l('Not Good') => 2,
                    $this->l('Acceptable') => 3,
                    $this->l('Excellent') => 4,
                ],
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->setAssociatedColumn('score')
        );
    }

    public function hookActionAdminCategoriesListingFieldsModifier(&$params)
    {
        $scores_list = array(
            1 => $this->l('Very Bad'),
            2 => $this->l('Not Good'),
            3 => $this->l('Acceptable'),
            4 => $this->l('Excellent'),
        );
        $id_shop = (int)$this->context->shop->id;
        if (isset($params['select'])) {
            $params['select'].= ', ecs.score';
            $params['join'] .= ' LEFT JOIN '._DB_PREFIX_.'ec_seo_score ecs ON (a.id_category = ecs.id AND page="category" AND ecs.id_shop = '.(int)$id_shop.')';
            $params['fields']['score'] = array(
                'title' => $this->l('SEO score'),
                'type' => 'select',
                'list' => $scores_list,
                'filter_key' => 'ecs!score',
                'class' => 'fixed-width-lg',
                'remove_onclick' => true,
                'float' =>  true
            );
            if (Tools::getValue('submitFiltercategory') == 1) {
                $score_filter = 'categoriescategoryFilter_ecs!score';
                $score = $this->context->cookie->$score_filter;
                if ($score > 0) {
                    if ($score != 4) {
                        $signe = '<';
                        if ($score == 3) {
                            $val = 80;
                        } else if ($score == 2) {
                            $val = 50;
                        } else {
                            $val = 25;
                        }
                    } else {
                        $signe = '>=';
                        $val = 80;
                    }
                    $params['where'] .= ' AND ecs.score '.$signe.' '.$val;
                }
            }
        }
    }

    public function hookactionAdminCategoriesListingResultsModifier(&$params)
    {
        $id_shop = (int)$this->context->shop->id;
        foreach ($params['list'] as $key => $cat) {
            $score = $this->getGlobalNote('category', $cat['id_category'], $id_shop)['global'];
            Db::getinstance()->insert(
                'ec_seo_score',
                array(
                    'id' => (int)$cat['id_category'],
                    'page' => 'category',
                    'score' => (int)$score,
                    'id_shop' => (int)$id_shop,
                ),
                false,
                true,
                Db::ON_DUPLICATE_KEY
            );
            
            $params['list'][$key]['score'] = $this->ShowSeoScoreListing('category', $cat['id_category'], $id_shop);
        }
    }

    public function hookActionProductGridDefinitionModifier(&$hookParams)
    {
        $definition = $hookParams['definition'];
        $columns = $definition->getColumns();
        $scoreColumn = (new DataColumn('score'))
        ->setName($this->l('SEO Score'))
        ->setOptions([
            'field' => 'score',
        ])
        ;
        $columns->addAfter('active', $scoreColumn);
        $filters = $definition->getFilters();
        $filters->add(
            (new Filter('score', ChoiceType::class))
            ->setTypeOptions([
                'choices' => [
                    $this->l('Very Bad') => 1,
                    $this->l('Not Good') => 2,
                    $this->l('Acceptable') => 3,
                    $this->l('Excellent') => 4,
                ],
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->setAssociatedColumn('score')
        );
    }

    public function hookActionProductGridQueryBuilderModifier($params)
    {
        $searchQueryBuilder = $params['search_query_builder'];
        $id_shop = (int)$this->context->shop->id;
        $searchQueryBuilder->addSelect('ecs.score')
            ->leftJoin(
                'p',
                _DB_PREFIX_. 'ec_seo_score',
                'ecs',
                'p.id_product = ecs.id AND page="product" AND ecs.id_shop = '.(int)$id_shop
            )
        ;
        $allValues = Tools::getAllValues();
        if (isset($allValues['product']['filters']['score'])) {
            $filter_seo = $allValues['product']['filters']['score'];
            if ($filter_seo > 0) {
                if ($filter_seo != 4) {
                    $signe = '<';
                    if ($filter_seo == 3) {
                        $val = 80;
                    } else if ($filter_seo == 2) {
                        $val = 50;
                    } else {
                        $val = 25;
                    }
                } else {
                    $signe = '>=';
                    $val = 80;
                }
                $searchQueryBuilder->andWhere('ecs.score '.$signe.' '.$val);
            }
        }
    }

    public function hookActionProductGridDataModifier($params)
    {
        $id_shop = (int)$this->context->shop->id;
        $datas = $params['data']->getRecords()->all();
        foreach ($datas as $key => $product) {
            $score = $this->getGlobalNote('product', $product['id_product'], $id_shop)['global'];
            Db::getinstance()->insert(
                'ec_seo_score',
                array(
                    'id' => (int)$product['id_product'],
                    'page' => 'product',
                    'score' => (int)$score,
                    'id_shop' => (int)$id_shop,
                ),
                false,
                true,
                Db::ON_DUPLICATE_KEY
            );
            
            $datas[$key]['score']= $this->ShowSeoScoreListing('product', $product['id_product'], $id_shop);
        }
        $records = new RecordCollection($datas);
        $params['data'] = new GridData($records, $params['data']->getRecordsTotal(), $params['data']->getQuery());

    }

    public function hookActionAdminProductsListingFieldsModifier(&$hookParams)
    {
        $id_shop = (int)$this->context->shop->id;
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $scores_list = array(
                1 => $this->l('Very Bad'),
                2 => $this->l('Not Good'),
                3 => $this->l('Acceptable'),
                4 => $this->l('Excellent'),
            );
            if (isset($hookParams['select'])) {
                /* $hookParams['select'].= ', ecs.score'; */
                $hookParams['join'] .= ' LEFT JOIN '._DB_PREFIX_.'ec_seo_score ecs ON (a.id_product = ecs.id AND page="product" AND ecs.id_shop = '.(int)$id_shop.')';
                $hookParams['fields']['score'] = array(
                    'title' => $this->l('SEO score'),
                    'type' => 'select',
                    'list' => $scores_list,
                    'filter_key' => 'ecs!score',
                    'class' => 'fixed-width-lg',
                    'remove_onclick' => true,
                    'float' =>  true
                );
                if (Tools::getValue('submitFilterproduct') == 1) {
                    $score_filter = 'productsproductFilter_ecs!score';
                    $score = $this->context->cookie->$score_filter;
         
                    if ($score > 0) {
                        if ($score != 4) {
                            $signe = '<';
                            if ($score == 3) {
                                $val = 80;
                            } else if ($score == 2) {
                                $val = 50;
                            } else {
                                $val = 25;
                            }
                        } else {
                            $signe = '>=';
                            $val = 80;
                        }
                        $hookParams['where'] .= ' AND ecs.score '.$signe.' '.$val;
                    }
                }
            }
        } else {
            $filter_seo = Tools::getValue('product_filter_column_seo_score');
            if ($filter_seo > 0) {
                if ($filter_seo != 4) {
                    $signe = '<';
                    if ($filter_seo == 3) {
                        $val = 80;
                    } else if ($filter_seo == 2) {
                        $val = 50;
                    } else {
                        $val = 25;
                    }
                } else {
                    $signe = '>=';
                    $val = 80;
                }
                $hookParams['sql_where'][] = 'ecs.score '.$signe.' '.$val;
            }
            $hookParams['sql_select']['score'] = [
                'table' => 'ecs',
                'field' => 'score',
                'filtering' => "LIKE '%%%s%%'",
            ];

            $hookParams['sql_table']['ecs'] = array(
                'table' => 'ec_seo_score',
                'join' => 'LEFT JOIN',
                'on' => 'ecs.`id` = p.`id_product` AND page="product" AND ecs.id_shop = '.(int)$id_shop,
            );
        }
        
    }

    public function hookActionAdminProductsListingResultsModifier($params)
    {
        $id_shop = (int)$this->context->shop->id;
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            if (isset($params['list'])) {
                if (is_array($params['list'])) {
                    foreach ($params['list'] as $key => $prod) {
                        $score = $this->getGlobalNote('product', $prod['id_product'], $id_shop)['global'];
                        Db::getinstance()->insert(
                            'ec_seo_score',
                            array(
                                'id' => (int)$prod['id_product'],
                                'page' => 'product',
                                'score' => (int)$score,
                                'id_shop' => (int)$id_shop,
                            ),
                            false,
                            true,
                            Db::ON_DUPLICATE_KEY
                        );
                        $params['list'][$key]['score'] = $this->ShowSeoScoreListing('product', $prod['id_product'], $id_shop);
                    }
                }
            }
        } else {
            $products = $params['products'];
            foreach ($products as $product) {
                $score = $this->getGlobalNote('product', $product['id_product'], $id_shop)['global'];
                Db::getinstance()->insert(
                    'ec_seo_score',
                    array(
                        'id' => (int)$product['id_product'],
                        'page' => 'product',
                        'score' => (int)$score,
                        'id_shop' => (int)$id_shop,
                    ),
                    false,
                    true,
                    Db::ON_DUPLICATE_KEY
                );
            }
        }
    }

    public function hookDisplaySeoScore($params)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_product = $params['id_product'];
        return $this->ShowSeoScoreListing('product', $id_product, $id_shop);
    }

    public function ShowSeoScoreListing($type, $id, $id_shop)
    {
        $score = $this->getGlobalNote($type, $id, $id_shop);
        $global_score = $score['global'];
        $global_color = 'red';
        if ($global_score >= 80) {
            $global_color = 'green';
        } else if ($global_score >= 50) {
            $global_color = 'yellow';
        } else if ($global_score >= 25) {
            $global_color = 'orange';
        }
        $score_lang = $score['score_lang'];
        foreach ($score_lang as $id_lang => &$val) {
            $score = $val;
            $iso = Language::getIsoById($id_lang);
            $val = array();
            $val['iso'] = $iso;
            $val['score'] = $score;
            $val['img'] = Tools::getHttpHost(true).__PS_BASE_URI__.'img/tmp/lang_mini_'.$id_lang.'_1.jpg';
        }

        $this->smarty->assign(array(
            'global_score' => $global_score,
            'global_color' => $global_color,
            'score_lang' => $score_lang,
            'EcSeoLink' => $this->context->link->getAdminLink('AdminEcSeo'.Tools::ucfirst($type), true).'&id_'.$type.'='.(int)$id
        ));
        return $this->display(__FILE__, 'views/templates/admin/listing.tpl');
    }

    public function hookDisplaySeoFilter($params)
    {
        //return $params['product_filter_column_seo_score'];
        $this->smarty->assign(array(
            'product_filter_column_seo_score' =>  $params['product_filter_column_seo_score']
        ));
        return $this->display(__FILE__, 'views/templates/admin/seo_filter.tpl');
    }

    public function hookDisplayFooter()
    {
        $controller = Tools::getValue('controller');
        return $this->getFooterByPage($controller, Tools::getValue('id_'.$controller));
    }

    public function getFooterByPage($controller, $id, $prev = false)
    {

        if (Tools::getValue('prev') && md5($_SERVER['REMOTE_ADDR']) == Tools::getValue('k')) {
            $prev = true;
        }
        $id_shop =  (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $id_footer = false;
        if (!in_array($controller, array('category', 'product', 'cms', 'supplier', 'manufacturer'))) {
            return false;
        }
        if ($controller == 'category') {
            $id_category = $id;
        }
        if ($controller == 'product') {
            $controller = 'category';
            $id_product = $id;
            $id_category = Db::getInstance()->getValue(
                '
                SELECT id_category_default FROM '._DB_PREFIX_.'product_shop 
                WHERE id_product = '.(int)$id_product.'
                AND id_shop = '.(int)$id_shop.'
                '
            );
        }
        if ($controller == 'category') {
            $id_footer = Db::getInstance()->getValue(
                '
                SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_category fc
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                WHERE id_shop = '.(int)$id_shop.'
                AND id_category = '.(int)$id_category.'
                AND type = "category"
                '.(!$prev?'AND active = 1':'').'
                '
            );
            if (!$id_footer) {
                //Automatique sur les enfants
               /*  $id_category = Db::getInstance()->getValue('SELECT id_parent FROM '._DB_PREFIX_.'category WHERE id_category = '.(int)$id_category);
                while ($id_category > 0 && !$id_footer) {
                    $id_footer = Db::getInstance()->getValue(
                        '
                        SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_category fc
                        LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                        WHERE id_shop = '.(int)$id_shop.'
                        AND id_category = '.(int)$id_category.'
                        AND type = "category"
                        '.(!$prev?'AND active = 1':'').'
                        '
                    );
                    if (!$id_footer) {
                        $id_category = Db::getInstance()->getValue('SELECT id_parent FROM '._DB_PREFIX_.'category WHERE id_category = '.(int)$id_category);
                    }
                } */
            }
        }
        //SPE PRODUCT
     /*    if ($controller == 'product') {
            $id_product = $id;
            $id_category_default = Db::getInstance()->getValue(
                '
                SELECT id_category_default FROM '._DB_PREFIX_.'product_shop
                WHERE id_product = '.(int)$id_product.'
                AND id_shop = '.(int)$id_shop.'
                '
            );
            $id_footer = Db::getInstance()->getValue(
                '
                SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_product fp
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fp.id_footer)
                WHERE id_shop = '.(int)$id_shop.'
                AND id_product = '.(int)$id_product.'
                '.(!$prev?'AND active = 1':'').'
                '
            );
            if (!$id_footer) {
                $id_footer = Db::getInstance()->getValue(
                    '
                    SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_category fc
                    LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                    WHERE id_shop = '.(int)$id_shop.'
                    AND id_category = '.(int)$id_category_default.'
                    '.(!$prev?'AND active = 1':'').'
                    '
                );
            }
        } */

        if ($controller == 'cms') {
            $id_cms = $id;
            $id_footer = Db::getInstance()->getValue(
                '
                SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_cms fc
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                WHERE id_shop = '.(int)$id_shop.'
                AND id_cms = '.(int)$id_cms.'
                '.(!$prev?'AND active = 1':'').'
                '
            );
        }
        if ($controller == 'manufacturer') {
            $id_manufacturer = $id;
            $id_footer = Db::getInstance()->getValue(
                '
                SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_manufacturer fm
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fm.id_footer)
                WHERE id_shop = '.(int)$id_shop.'
                AND id_manufacturer = '.(int)$id_manufacturer.'
                '.(!$prev?'AND active = 1':'').'
                '
            );
        }
        if ($controller == 'supplier') {
            $id_supplier = $id;
            $id_footer = Db::getInstance()->getValue(
                '
                SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_supplier fs
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fs.id_footer)
                WHERE id_shop = '.(int)$id_shop.'
                AND id_supplier = '.(int)$id_supplier.'
                '.(!$prev?'AND active = 1':'').'
                '
            );
        }
        if (!$id_footer) {
            $id_footer = Db::getInstance()->getValue(
                '
                SELECT id FROM '._DB_PREFIX_.'ec_seo_footer
                WHERE id_shop = '.(int)$id_shop.'
                AND type = "'.pSQL($controller).'"
                AND spe = 0
                '.(!$prev?'AND active = 1':'').'
                '
            );
        }
        if (!$id_footer) {
            return;
        }
        $infoFooter = Db::getInstance()->getRow(
            'SELECT title, description FROM '._DB_PREFIX_.'ec_seo_footer_lang 
            WHERE id_footer = '.(int)$id_footer.'
            AND id_lang = '.(int)$id_lang
        );
        $blocks = Db::getInstance()->executes(
            '
            SELECT id_block, title  FROM '._DB_PREFIX_.'ec_seo_footer_block fb
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_block_lang fbl ON (fbl.id_block = fb.id)
            WHERE active = 1
            AND id_lang = '.(int)$id_lang.'
            AND id_footer = '.(int)$id_footer.'
            ORDER BY position
            '
        );
        foreach ($blocks as &$block) {
            $block['links'] = Db::getInstance()->executes(
                '
                SELECT title, link  FROM '._DB_PREFIX_.'ec_seo_footer_link fl
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_link_lang fll ON (fll.id_link = fl.id)
                WHERE id_lang = '.(int)$id_lang.'
                AND id_block = '.(int)$block['id_block'].'
                ORDER BY position
                '
            );
        }
        if ($infoFooter && $blocks) {
            $this->smarty->assign(array(
                'infoFooter' => $infoFooter,
                'blocks' => $blocks,
                'col' => (int)(12/count($blocks))
            ));
            return $this->display(__FILE__, 'views/templates/front/ec_seo_footer.tpl');
        }
    }

    public function getPreviewLinkFooter($type, $id_footer = false)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $link_preview = false;
        if ($type == 'product') {
            if (!$id_footer) {
                $categories = Db::getInstance()->executes(
                    '
                    SELECT id_category FROM '._DB_PREFIX_.'ec_seo_footer_category fc
                    LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                    WHERE type = "product"
                    AND id_shop = '.(int)$id_shop.'
                    '
                );
                $tab_categories = array();
                foreach ($categories as $cat_info) {
                    $tab_categories[] = $cat_info['id_category'];
                }
                $products = Db::getInstance()->executes(
                    '
                    SELECT id_product FROM '._DB_PREFIX_.'ec_seo_footer_product fp
                    LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fp.id_footer)
                    WHERE type = "product"
                    AND id_shop = '.(int)$id_shop.'
                    '
                );
                $tab_products = array();
                foreach ($products as $prod_info) {
                    $tab_products[] = $prod_info['id_product'];
                }
                $id_product = Db::getInstance()->getValue(
                    '
                    SELECT id_product FROM '._DB_PREFIX_.'product_shop ps
                    WHERE active = 1
                    '.(count($tab_categories)>0?'AND id_category_default NOT IN ('.implode(',', $tab_categories).')':'').'
                    '.(count($tab_products)>0?'AND id_product NOT IN ('.implode(',', $tab_products).')':'').'
                    AND id_shop = '.(int)$id_shop.'
                    ORDER BY id_product DESC
                    '
                );
            } else {
                $filter = '';
                $categories = Db::getInstance()->executes(
                    '
                    SELECT id_category FROM '._DB_PREFIX_.'ec_seo_footer_category fc
                    LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                    WHERE type = "product"
                    AND id_footer = '.(int)$id_footer.'
                    AND id_shop = '.(int)$id_shop.'
                    '
                );
                if ($categories) {
                    $tab_categories = array();
                    foreach ($categories as $cat_info) {
                        $tab_categories[] = $cat_info['id_category'];
                    }
                    if (count($tab_categories) > 0) {
                        $filter .= ' AND id_category_default IN ('.implode(',', $tab_categories).')';
                    }
                } else {
                    $products = Db::getInstance()->executes(
                        '
                        SELECT id_product FROM '._DB_PREFIX_.'ec_seo_footer_product fp
                        LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fp.id_footer)
                        WHERE type = "product"
                        AND id_footer = '.(int)$id_footer.'
                        AND id_shop = '.(int)$id_shop.'
                        '
                    );
                    $tab_products = array();
                    foreach ($products as $prod_info) {
                        $tab_products[] = $prod_info['id_product'];
                    }
                    if (count($tab_products) > 0) {
                        $filter .= ' AND id_product IN ('.implode(',', $tab_products).')';
                    }
                }
                $id_product = Db::getInstance()->getValue(
                    '
                    SELECT id_product FROM '._DB_PREFIX_.'product_shop ps
                    WHERE active = 1
                    '.$filter.'
                    AND id_shop = '.(int)$id_shop.'
                    ORDER BY id_product DESC
                    '
                );
            }
            if ($id_product) {
                $obj = new Product($id_product, false, null, $id_shop);
                $link_preview = $this->context->link->getProductLink($obj, null, null, null, $id_lang, $id_shop);
            }
        }
        if ($type == 'category') {
            if (!$id_footer) {
                $categories = Db::getInstance()->executes(
                    '
                    SELECT id_category FROM '._DB_PREFIX_.'ec_seo_footer_category fc
                    LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                    WHERE type = "category"
                    AND id_shop = '.(int)$id_shop.'
                    '
                );
                $tab_categories = array();
                foreach ($categories as $cat_info) {
                    $tab_categories[] = $cat_info['id_category'];
                }
                $id_category = Db::getInstance()->getValue(
                    '
                    SELECT cs.id_category FROM '._DB_PREFIX_.'category_shop cs
                    LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cs.id_category)
                    WHERE active = 1
                    '.(count($tab_categories)>0?'AND cs.id_category NOT IN ('.implode(',', $tab_categories).')':'').'
                    AND cs.id_category != 1
                    AND id_shop = '.(int)$id_shop.'
                    ORDER BY id_category DESC
                    '
                );
            } else {
                $categories = Db::getInstance()->executes(
                    '
                    SELECT id_category FROM '._DB_PREFIX_.'ec_seo_footer_category fc
                    LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                    AND id_footer = '.(int)$id_footer.'
                    WHERE type = "category"
                    AND id_shop = '.(int)$id_shop.'
                    '
                );
                $tab_categories = array();
                foreach ($categories as $cat_info) {
                    $tab_categories[] = $cat_info['id_category'];
                }
                $id_category = Db::getInstance()->getValue(
                    '
                    SELECT cs.id_category FROM '._DB_PREFIX_.'category_shop cs
                    LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cs.id_category)
                    WHERE active = 1
                    AND cs.id_category IN ('.implode(',', $tab_categories).')
                    AND cs.id_category != 1
                    AND id_shop = '.(int)$id_shop.'
                    ORDER BY id_category DESC
                    '
                );
            }
            
            if ($id_category) {
                $link_preview = $this->context->link->getCategoryLink($id_category, null, $id_lang);
            }
        }
        if ($type == 'cms') {
            $filter_footer = '';
            $condition = 'NOT IN';
            if ($id_footer) {
                $filter_footer .= ' AND id_footer = '.(int)$id_footer;
                $condition = 'IN';
            }
            $cms = Db::getInstance()->executes(
                '
                SELECT id_cms FROM '._DB_PREFIX_.'ec_seo_footer_cms fc
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                WHERE type = "cms"
                '.$filter_footer.'
                AND id_shop = '.(int)$id_shop.'
                '
            );
            $tab_cms = array();
            foreach ($cms as $cms_info) {
                $tab_cms[] = $cms_info['id_cms'];
            }
            
            $id_cms = Db::getInstance()->getValue(
                '
                SELECT cs.id_cms FROM '._DB_PREFIX_.'cms_shop cs
                LEFT JOIN '._DB_PREFIX_.'cms c ON (c.id_cms = cs.id_cms)
                WHERE active = 1
                '.(count($tab_cms)>0?'AND cs.id_cms '.$condition.' ('.implode(',', $tab_cms).')':'').'
                AND id_shop = '.(int)$id_shop.'
                ORDER BY id_cms DESC
                '
            );

            if ($id_cms) {
                $cms = new CMS($id_cms, null, $id_shop);
                $link_preview = $this->context->link->getCMSLink($cms, null, null, $id_lang, $id_shop);
            }
        }
        if ($type == 'supplier') {
            $filter_footer = '';
            $condition = 'NOT IN';
            if ($id_footer) {
                $filter_footer .= ' AND id_footer = '.(int)$id_footer;
                $condition = 'IN';
            }
            $suppliers = Db::getInstance()->executes(
                '
                SELECT id_supplier FROM '._DB_PREFIX_.'ec_seo_footer_supplier fc
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                WHERE type = "supplier"
                '.$filter_footer.'
                AND id_shop = '.(int)$id_shop.'
                '
            );
            $tab_supplier = array();
            foreach ($suppliers as $supplier_info) {
                $tab_supplier[] = $supplier_info['id_supplier'];
            }
            $id_supplier = Db::getInstance()->getValue(
                '
                SELECT ss.id_supplier FROM '._DB_PREFIX_.'supplier_shop ss
                LEFT JOIN '._DB_PREFIX_.'supplier s ON (s.id_supplier = ss.id_supplier)
                WHERE active = 1
                '.(count($tab_supplier)>0?'AND ss.id_supplier '.$condition.' ('.implode(',', $tab_supplier).')':'').'
                AND id_shop = '.(int)$id_shop.'
                ORDER BY id_supplier DESC
                '
            );
            if ($id_supplier) {
                $supplier = new Supplier($id_supplier, $id_lang, $id_shop);
                $link_preview =  $this->context->link->getSupplierLink($supplier, null, $id_lang, $id_shop);
            }
        }
        if ($type == 'manufacturer') {
            $filter_footer = '';
            $condition = 'NOT IN';
            if ($id_footer) {
                $filter_footer .= ' AND id_footer = '.(int)$id_footer;
                $condition = 'IN';
            }
            $manufacturers = Db::getInstance()->executes(
                '
                SELECT id_manufacturer FROM '._DB_PREFIX_.'ec_seo_footer_manufacturer fc
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fc.id_footer)
                WHERE type = "manufacturer"
                '.$filter_footer.'
                AND id_shop = '.(int)$id_shop.'
                '
            );
            $tab_manufacturer = array();
            foreach ($manufacturers as $manufacturer_info) {
                $tab_manufacturer[] = $manufacturer_info['id_manufacturer'];
            }
            $id_manufacturer = Db::getInstance()->getValue(
                '
                SELECT ss.id_manufacturer FROM '._DB_PREFIX_.'manufacturer_shop ss
                LEFT JOIN '._DB_PREFIX_.'manufacturer s ON (s.id_manufacturer = ss.id_manufacturer)
                WHERE active = 1
                '.(count($tab_manufacturer)>0?'AND ss.id_manufacturer '.$condition.' ('.implode(',', $tab_manufacturer).')':'').'
                AND id_shop = '.(int)$id_shop.'
                ORDER BY id_manufacturer DESC
                '
            );
            if ($id_manufacturer) {
                $manufacturer = new Manufacturer($id_manufacturer, $id_lang, $id_shop);
                $link_preview =  $this->context->link->getManufacturerLink($manufacturer, null, $id_lang, $id_shop);
            }
        }
        if ($link_preview) {
            $link_preview .= '?prev=1&k='.md5($_SERVER['REMOTE_ADDR']);
        }
        return $link_preview;
    }

    protected function getFooterFormGen($class)
    {
        
        $link_preview = $this->getPreviewLinkFooter($class);
        
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l(''.$class.' Footer General'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Descritption'),
                        'name' => 'description_'.$class,
                        'lang' => true,
                        'autoload_rte' => false,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active_'.$class,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        
        if ($link_preview) {
            $form['form']['buttons'][] = array(
                'href' => '#',
                'title' => $this->l('Preview'),
                'icon' => 'process-icon-preview',
                'class' => 'pull-right',
                'js' => "window.open('".$link_preview."', '_blank');"
                //'id' => 'ecmetacancel_'.$class
            );
        }
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'footerseo'.$class.'gen';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFooterSeoGen'.$class;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $info = Db::getInstance()->ExecuteS(
            'SELECT id, title, description, id_lang, active
            FROM '._DB_PREFIX_.'ec_seo_footer sf
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = sf.id)
            WHERE type="'.pSQL($class).'" AND spe = 0 AND id_shop = '.(int)$this->context->shop->id.'
            '
        );
        $values = array();
        foreach ($info as $val) {
            $values['title'][$val['id_lang']] = $val['title'];
            $values['description_'.$class][$val['id_lang']] = $val['description'];
            $values['active_'.$class] = $val['active'];
            $values['id'] = $val['id'];
        }
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($form));
    }

    protected function renderListBlockSeo($id_footer, $spe = 0)
    {
        $id_lang  = (int)$this->context->language->id;
        $infoFooter = Db::getInstance()->getRow(
            'SELECT sf.id, title, active, spe, type
            FROM '._DB_PREFIX_.'ec_seo_footer sf
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = sf.id)
            WHERE id = '.(int)$id_footer.' AND spe = '.$spe.' AND sfl.id_lang = '.(int)$id_lang.'
            '
        );
        $name = 'ec_ListBlockFooter';
        $title = 'Footer : '.$infoFooter['title'].'('.$infoFooter['id'].') >  Blocks';
        if ($infoFooter['spe']) {
            $name .= 'Spe';
        } else {
            $name .= 'Gen';
        }
        $name .= $infoFooter['type'];
        $fields_list = array();
        $fields_list['id'] = array(
            'title' => $this->l('ID Block'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['title'] = array(
            'title' => $this->l('Title'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['position'] = array(
            'title' => $this->l('Position'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['active'] = array(
            'title' => $this->l('Active'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->_defaultOrderBy = 'position';
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id';
        $helper->actions = array('view', 'edit', 'delete');
        $helper->show_toolbar = true;
        $countblock = Db::getInstance()->getValue('SELECT count(id) FROM '._DB_PREFIX_.'ec_seo_footer_block WHERE id_footer = '.(int)$id_footer);
        if ($countblock < 6) {
            $helper->toolbar_btn['new'] = array(
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addBlockFooter&id_footer='.$id_footer.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Add new')
            );
        }
        if ($spe) {
            $link = AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=footerseo&token='.Tools::getAdminTokenLite('AdminModules').'#'.$infoFooter['type'].'#spe';
            $helper->toolbar_btn['back'] = array(
                'href' => $link,
                'desc' => $this->l('Back to list'),
            );
        }
        $helper->title = $title;
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentBlock($id_footer, $name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentBlock($id_footer, $name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT id, active, position, title
        FROM '._DB_PREFIX_.'ec_seo_footer_block fb
        LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_block_lang fbl ON (fbl.id_block = fb.id)
        WHERE id_footer = '.(int)$id_footer.'
        AND id_lang = '.(int)$id_lang.'
        ';
        
        $orderby = Tools::getValue($name.'Orderby');
        if (!$orderby) {
            $orderby = 'position';
        }
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        
        $res = Db::getInstance()->ExecuteS($req);
      /*   foreach ($res as $key => &$val) {

        } */
        return array('res' => $res, 'count' => $count);
    }

    protected function getFooterBlockForm($id_footer, $id_block = null)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'footerseoblock';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFooterSeoBlock';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $spe = Db::getInstance()->getValue('SELECT spe FROM '._DB_PREFIX_.'ec_seo_footer WHERE id = '.(int)$id_footer);
        $button_link = AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=footerseo&token='.Tools::getAdminTokenLite('AdminModules');
        if ($spe) {
            $button_link .= '&viewec_ListFooter&id='.(int)$id_footer;
        }
        $values = array(
            'id_footer' => $id_footer,
            'id' => $id_block,
            'title' => null,
            'active' => true
        );
        if ($id_block != null) {
            $info = Db::getInstance()->executes(
                '
                SELECT active, position, title, id_lang FROM '._DB_PREFIX_.'ec_seo_footer_block fb
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_block_lang fbl ON (fbl.id_block = fb.id)
                WHERE id_block = '.(int)$id_block.'
                '
            );
            foreach ($info as $val) {
                $values['title'][$val['id_lang']] = $val['title'];
                $values['active'] = $val['active'];
            }
        }
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $infoFooter = Db::getInstance()->getRow(
            'SELECT sf.id, title, active, spe, type
            FROM '._DB_PREFIX_.'ec_seo_footer sf
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = sf.id)
            WHERE id = '.(int)$id_footer.' AND spe = '.$spe.' AND sfl.id_lang = '.(int)$this->context->language->id.'
            '
        );
        $title = 'Footer : '.$infoFooter['title'].'('.$infoFooter['id'].') >  Block';
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $title,
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'id_footer',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                ),
                'buttons' => array(
                    array(
                        'href' => $button_link,
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                        //'id' => 'ecmetacancel_'.$class
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        $count_block = Db::getInstance()->getValue('SELECT count(id) FROM '._DB_PREFIX_.'ec_seo_footer_block WHERE id_footer = '.(int)$id_footer);
        if ($count_block < 5) {
            $form['form']['buttons']['save-and-stay']  = array(
                'title' => $this->l('Save then add a new block'),
                'name' => 'submitFooterSeoBlockAndStay',
                'type' => 'submit',
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save'
            );
        }

        return $helper->generateForm(array($form));
    }

    public function udaptePositionBlock($id_footer)
    {
        $blocks = Db::getInstance()->executes('SELECT id FROM '._DB_PREFIX_.'ec_seo_footer_block WHERE id_footer = '.(int)$id_footer);
        $cpt = 1;
        foreach ($blocks as $block) {
            Db::getinstance()->update(
                'ec_seo_footer_block',
                array(
                    'position' => (int)$cpt
                ),
                'id = '.(int)$block['id']
            );
            $cpt++;
        }
    }

    protected function renderListBlockLinks($id_block)
    {
        $id_lang = (int)$this->context->language->id;
        $infoBlock = Db::getInstance()->getRow(
            'SELECT fbl.title, sfl.title as title_footer, f.id
            FROM '._DB_PREFIX_.'ec_seo_footer_block fb
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_block_lang fbl ON (fbl.id_block = fb.id)
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fb.id_footer)
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = f.id)
            WHERE fb.id = '.(int)$id_block.' AND fbl.id_lang = '.(int)$id_lang.' AND sfl.id_lang = '.(int)$id_lang.'
            '
        );
      
        $name = 'ec_ListBlockLinkFooter';
        $title =  'Footer : '.$infoBlock['title_footer'].'('.$infoBlock['id'].') >  Block : '.$infoBlock['title'].'('.$id_block.') > '.$this->l('Links');
        $fields_list = array();
        $fields_list['id'] = array(
            'title' => $this->l('ID Link'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['title'] = array(
            'title' => $this->l('Title'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['link'] = array(
            'title' => $this->l('Link'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['position'] = array(
            'title' => $this->l('Position'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->_defaultOrderBy = 'position';
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addLinkBlock&id_block='.$id_block.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        $active = 'footerseo';
        $class = Db::getInstance()->getValue(
            '
            SELECT type FROM '._DB_PREFIX_.'ec_seo_footer f
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_block fb ON (fb.id_footer = f.id)
            WHERE fb.id = '.(int)$id_block.'
            '
        );
        $hashtag = $class;

        $link = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&oactive='.$active.'#'.$hashtag;
        $admin_dir = explode('/', _PS_ADMIN_DIR_);
        $admin_dir = $admin_dir[count($admin_dir)-1];
        if (strpos($link, $admin_dir) === false) {
            $link = Tools::getHttpHost(true).__PS_BASE_URI__.$admin_dir.'/'.$link;
        }
        $infoFooter = Db::getInstance()->getRow(
            'SELECT id_footer, spe FROM '._DB_PREFIX_.'ec_seo_footer f
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_block fb ON (fb.id_footer = f.id)
            WHERE fb.id = '.(int)$id_block.'
            '
        );
        if ($infoFooter['spe']) {
            $link = AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=footerseo&token='.Tools::getAdminTokenLite('AdminModules');
            $link .= '&viewec_ListFooter&id='.(int)$infoFooter['id_footer'];
        }
        $helper->toolbar_btn['back'] = array(
            'href' => $link,
            'desc' => $this->l('Back to list'),
        );
        $helper->title = $title;
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentBlocklink($id_block, $name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentBlocklink($id_block, $name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT id, position, title, link
        FROM '._DB_PREFIX_.'ec_seo_footer_link fl
        LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_link_lang fll ON (fll.id_link = fl.id)
        WHERE id_block = '.(int)$id_block.'
        AND id_lang = '.(int)$id_lang.'
        ';
        
        $orderby = Tools::getValue($name.'Orderby');
        if (!$orderby) {
            $orderby = 'position';
        }
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        
        $res = Db::getInstance()->ExecuteS($req);
      /*   foreach ($res as $key => &$val) {

        } */
        return array('res' => $res, 'count' => $count);
    }

    protected function getFooterBlockLinkForm($id_block, $id_link = null)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'footerseoblocklink';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFooterSeoBlockLink';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
       
        $values = array(
            'id_block' => $id_block,
            'id' => $id_link,
            'title' => null,
            'link' => null,
        );
        if ($id_block != null) {
            $info = Db::getInstance()->executes(
                '
                SELECT position, title, link, id_lang FROM '._DB_PREFIX_.'ec_seo_footer_link fl
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_link_lang fll ON (fll.id_link = fl.id)
                WHERE id_link = '.(int)$id_link.'
                '
            );
            foreach ($info as $val) {
                $values['title'][$val['id_lang']] = $val['title'];
                $values['link'][$val['id_lang']] = $val['link'];
            }
        }
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $id_lang = (int)$this->context->language->id;
        $infoBlock = Db::getInstance()->getRow(
            'SELECT fbl.title, sfl.title as title_footer, f.id
            FROM '._DB_PREFIX_.'ec_seo_footer_block fb
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_block_lang fbl ON (fbl.id_block = fb.id)
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer f ON (f.id = fb.id_footer)
            LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = f.id)
            WHERE fb.id = '.(int)$id_block.' AND fbl.id_lang = '.(int)$id_lang.' AND sfl.id_lang = '.(int)$id_lang.'
            '
        );
        $title =  'Footer : '.$infoBlock['title_footer'].'('.$infoBlock['id'].') >  Block : '.$infoBlock['title'].'('.$id_block.') > '.$this->l('Link');
        if ($id_link != null) {
            $title .= ': '.$values['title'][$id_lang];
        }
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $title,
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'id_block',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Link'),
                        'name' => 'link',
                        'lang' => true,
                        'required' => true,
                    ),

                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&viewec_ListBlockFooter&id='.(int)$id_block.'&token='.Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                        //'id' => 'ecmetacancel_'.$class
                    ),
                    'save-and-stay' => array(
                        'title' => $this->l('Save then add a new link'),
                        'name' => 'submitFooterSeoBlockLinkAndStay',
                        'type' => 'submit',
                        'class' => 'btn btn-default pull-right',
                        'icon' => 'process-icon-save'
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        return $helper->generateForm(array($form));
    }

    public function udaptePositionBlockLink($id_block)
    {
        $links = Db::getInstance()->executes('SELECT id FROM '._DB_PREFIX_.'ec_seo_footer_link WHERE id_block = '.(int)$id_block);
        $cpt = 1;
        foreach ($links as $link) {
            Db::getinstance()->update(
                'ec_seo_footer_link',
                array(
                    'position' => (int)$cpt
                ),
                'id = '.(int)$link['id']
            );
            $cpt++;
        }
    }

    protected function renderListFooterSpeCategory()
    {
        $name = 'ec_ListFooterSpecategory';
        $fields_list = array();
        $fields_list['id'] = array(
            'title' => $this->l('ID Footer'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['title'] = array(
            'title' => $this->l('Title'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['description'] = array(
            'title' => $this->l('Description'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['categories'] = array(
            'title' => $this->l('Categories'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['active'] = array(
            'title' => $this->l('Active'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id';
        $helper->actions = array('view', 'edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addFooterSpeCategory&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        
        $helper->title = $this->l('Footer specific by category');
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentFooter($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentFooter($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT id, active, title, description
        FROM '._DB_PREFIX_.'ec_seo_footer f
        LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang fl ON (fl.id_footer = f.id)
        WHERE id_lang = '.(int)$id_lang.' AND type="category" AND spe = 1
        ';
        
        $orderby = Tools::getValue($name.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        $res = Db::getInstance()->ExecuteS($req);
        foreach ($res as $key => &$val) {
            $categories = Db::getInstance()->executes(
                'SELECT cl.id_category, name FROM '._DB_PREFIX_.'category_lang cl
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_category fc ON (fc.id_category = cl.id_category) 
                WHERE id_footer = '.$val['id'].'
                AND id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop
            );
            $concat_cat = '';
            foreach ($categories as $key => $cat) {
                $id_category = $cat['id_category'];
                if ($key == 0) {
                    $concat_cat .= $cat['name'].' ('.$id_category.')';
                } else {
                    $concat_cat .= ', '.$cat['name'].' ('.$id_category.')';
                }
            }
            $val['categories'] = $concat_cat;
        }
       
        return array('res' => $res, 'count' => $count);
    }

    protected function getFooterFormSpeCategory($id = null)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'footerseospecategory';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFooterSeoSpeCategory';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $values = array(
            'id' => null,
            'title' => null,
            'description' => null,
            'active' => null,
        );
        $selected_categories = array();
        if ($id != null) {
            $info = Db::getInstance()->ExecuteS(
                'SELECT id, title, description, id_lang, active
                FROM '._DB_PREFIX_.'ec_seo_footer sf
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = sf.id)
                WHERE id = '.(int)$id.'
                '
            );
            foreach ($info as $val) {
                $values['title'][$val['id_lang']] = $val['title'];
                $values['description'][$val['id_lang']] = $val['description'];
                $values['active'] = $val['active'];
                $values['id'] = $val['id'];
            }
            $lst_cats = Db::getInstance()->executes('SELECT id_category FROM '._DB_PREFIX_.'ec_seo_footer_category WHERE id_footer = '.(int)$id);
            foreach ($lst_cats as $cat) {
                $selected_categories[] = $cat['id_category'];
            }
        }
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        

        $root = Category::getRootCategory();

        $tree = new HelperTreeCategories('categories_col1');
        $tree->setUseCheckBox(true)
            ->setAttribute('is_category_filter', $root->id)
            ->setRootCategory($root->id)
            ->setSelectedCategories($selected_categories)
            ->setInputName('categories');
        $categories = $tree->render();

        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Footer Specific category'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Descritption'),
                        'name' => 'description',
                        'lang' => true,
                        'autoload_rte' => false
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type'  => 'categories_select',
                        'label' => $this->l('Categories'),
                        'name'  => 'categories',
                        'category_tree'  => $categories,
                        'required' => true,
                     ),
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=footerseo&token='.Tools::getAdminTokenLite('AdminModules').'#category#spe',
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                        //'id' => 'ecmetacancel_'.$class
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        return $helper->generateForm(array($form));
    }

    protected function renderListFooterSpeProduct()
    {
        $name = 'ec_ListFooterSpeproduct';
        $fields_list = array();
        $fields_list['id'] = array(
            'title' => $this->l('ID Footer'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['title'] = array(
            'title' => $this->l('Title'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['description'] = array(
            'title' => $this->l('Description'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['products'] = array(
            'title' => $this->l('Products'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['categories'] = array(
            'title' => $this->l('Categories'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['active'] = array(
            'title' => $this->l('Active'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id';
        $helper->actions = array('view', 'edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addFooterSpeProduct&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        
        $helper->title = $this->l('Footer specific by category/product');
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentProductFooter($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentProductFooter($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT id, active, title, description
        FROM '._DB_PREFIX_.'ec_seo_footer f
        LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang fl ON (fl.id_footer = f.id)
        WHERE id_lang = '.(int)$id_lang.' AND type="product" AND spe = 1
        ';
        
        $orderby = Tools::getValue($name.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        $res = Db::getInstance()->ExecuteS($req);
        foreach ($res as $key => &$val) {
            $categories = Db::getInstance()->executes(
                'SELECT cl.id_category, name FROM '._DB_PREFIX_.'category_lang cl
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_category fc ON (fc.id_category = cl.id_category) 
                WHERE id_footer = '.$val['id'].'
                AND id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop
            );
            $concat_cat = '';
            foreach ($categories as $key => $cat) {
                $id_category = $cat['id_category'];
                if ($key == 0) {
                    $concat_cat .= $cat['name'].' ('.$id_category.')';
                } else {
                    $concat_cat .= ', '.$cat['name'].' ('.$id_category.')';
                }
            }
            $val['categories'] = $concat_cat;

            $products = Db::getInstance()->executes(
                'SELECT pl.id_product, name FROM '._DB_PREFIX_.'product_lang pl
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_product fc ON (fc.id_product = pl.id_product) 
                WHERE id_footer = '.$val['id'].'
                AND id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop
            );
            $concat_prod = '';
            foreach ($products as $key => $prod) {
                $id_product = $prod['id_product'];
                if ($key == 0) {
                    $concat_prod .= $prod['name'].' ('.$id_product.')';
                } else {
                    $concat_prod .= ', '.$prod['name'].' ('.$id_product.')';
                }
            }
            $val['products'] = $concat_prod;
        }
       
        return array('res' => $res, 'count' => $count);
    }

    protected function getFooterFormSpeProduct($id = null)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'footerseospeproduct';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFooterSeoSpeProduct';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $values = array(
            'id' => null,
            'title' => null,
            'description' => null,
            'active' => null,
            'products' => null,
        );
        $selected_categories = array();
        if ($id != null) {
            $info = Db::getInstance()->ExecuteS(
                'SELECT id, title, description, id_lang, active
                FROM '._DB_PREFIX_.'ec_seo_footer sf
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = sf.id)
                WHERE id = '.(int)$id.'
                '
            );
            foreach ($info as $val) {
                $values['title'][$val['id_lang']] = $val['title'];
                $values['description'][$val['id_lang']] = $val['description'];
                $values['active'] = $val['active'];
                $values['id'] = $val['id'];
            }
            $lst_cats = Db::getInstance()->executes('SELECT id_category FROM '._DB_PREFIX_.'ec_seo_footer_category WHERE id_footer = '.(int)$id);
            foreach ($lst_cats as $cat) {
                $selected_categories[] = $cat['id_category'];
            }
            $lst_prods = Db::getInstance()->executes('SELECT id_product FROM '._DB_PREFIX_.'ec_seo_footer_product WHERE id_footer = '.(int)$id);
            $tab_prod = array();
            foreach ($lst_prods as $prod) {
                $tab_prod[] = $prod['id_product'];
            }
            if (count($tab_prod) > 0) {
                $values['products'] = implode(';', $tab_prod);
            }
        }
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        

        $root = Category::getRootCategory();

        $tree = new HelperTreeCategories('categories_col1');
        $tree->setUseCheckBox(true)
            ->setAttribute('is_category_filter', $root->id)
            ->setRootCategory($root->id)
            ->setSelectedCategories($selected_categories)
            ->setInputName('categories');
        $categories = $tree->render();
        $oh = '<';
        $ch = '>';
        $sh = '/';
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Footer Specific category/product'),
                'icon' => 'icon-cogs',
                ),
                'warning' => $this->l('You can only filters by ').$oh.'strong'.$ch.$this->l('products OR categories').$oh.$sh.'strong'.$ch.', '.$this->l('not').' '.$this->l('products and categories').'.',
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Descritption'),
                        'name' => 'description',
                        'lang' => true,
                        'autoload_rte' => false
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('IDS Product'),
                        'name' => 'products',
                        'desc' => $this->l('Separate them with ";", exemple : 12;56;985;25;48')
                    ),
                    array(
                        'type'  => 'categories_select',
                        'label' => $this->l('Categories'),
                        'name'  => 'categories',
                        'category_tree'  => $categories,
                        //'required' => true,
                     ),
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=footerseo&token='.Tools::getAdminTokenLite('AdminModules').'#product#spe',
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                        //'id' => 'ecmetacancel_'.$class
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        return $helper->generateForm(array($form));
    }

    protected function renderListFooterSpeCMS()
    {
        $name = 'ec_ListFooterSpecms';
        $fields_list = array();
        $fields_list['id'] = array(
            'title' => $this->l('ID Footer'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['title'] = array(
            'title' => $this->l('Title'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['description'] = array(
            'title' => $this->l('Description'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['cms'] = array(
            'title' => $this->l('CMS Pages'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['active'] = array(
            'title' => $this->l('Active'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id';
        $helper->actions = array('view', 'edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addFooterSpeCms&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        
        $helper->title = $this->l('Footer specific by cms');
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentCMSFooter($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentCMSFooter($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT id, active, title, description
        FROM '._DB_PREFIX_.'ec_seo_footer f
        LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang fl ON (fl.id_footer = f.id)
        WHERE id_lang = '.(int)$id_lang.' AND type="cms" AND spe = 1
        ';
        
        $orderby = Tools::getValue($name.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        $res = Db::getInstance()->ExecuteS($req);
        foreach ($res as $key => &$val) {
            $cms = Db::getInstance()->executes(
                'SELECT cl.id_cms, meta_title FROM '._DB_PREFIX_.'cms_lang cl
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_cms fc ON (fc.id_cms = cl.id_cms) 
                WHERE id_footer = '.$val['id'].'
                AND id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop
            );
            $concat_cms = '';
            foreach ($cms as $key => $info) {
                $id_cms = $info['id_cms'];
                if ($key == 0) {
                    $concat_cms .= $info['meta_title'].' ('.$id_cms.')';
                } else {
                    $concat_cms .= ', '.$info['meta_title'].' ('.$id_cms.')';
                }
            }
            $val['cms'] = $concat_cms;
        }
       
        return array('res' => $res, 'count' => $count);
    }

    protected function getFooterFormSpeCms($id = null)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'footerseospecms';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFooterSeoSpeCms';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $values = array(
            'id' => null,
            'title' => null,
            'description' => null,
            'active' => null,
        );
        $selected_cms = array();
        if ($id != null) {
            $info = Db::getInstance()->ExecuteS(
                'SELECT id, title, description, id_lang, active
                FROM '._DB_PREFIX_.'ec_seo_footer sf
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = sf.id)
                WHERE id = '.(int)$id.'
                '
            );
            foreach ($info as $val) {
                $values['title'][$val['id_lang']] = $val['title'];
                $values['description'][$val['id_lang']] = $val['description'];
                $values['active'] = $val['active'];
                $values['id'] = $val['id'];
            }
            $lst_cms = Db::getInstance()->executes('SELECT id_cms FROM '._DB_PREFIX_.'ec_seo_footer_cms WHERE id_footer = '.(int)$id);
            foreach ($lst_cms as $cms) {
                $selected_cms[] = $cms['id_cms'];
            }
        }
        $values['lst_cms[]'] = $selected_cms;
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Footer Specific cms'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Descritption'),
                        'name' => 'description',
                        'lang' => true,
                        'autoload_rte' => false
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'size' => 10,
                        'label' => $this->l('CMS Pages'),
                        'name' => 'lst_cms[]',
                        'options' => array(
                            'query' => CMS::getCMSPages((int)$this->context->language->id, null, true, (int)$this->context->shop->id),
                            'id' => 'id_cms',
                            'name' => 'meta_title'
                        )
                    )
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=footerseo&token='.Tools::getAdminTokenLite('AdminModules').'#cms#spe',
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                        //'id' => 'ecmetacancel_'.$class
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        return $helper->generateForm(array($form));
    }

    protected function renderListFooterSpeSupplier()
    {
        $name = 'ec_ListFooterSpesupplier';
        $fields_list = array();
        $fields_list['id'] = array(
            'title' => $this->l('ID Footer'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['title'] = array(
            'title' => $this->l('Title'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['description'] = array(
            'title' => $this->l('Description'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['suppliers'] = array(
            'title' => $this->l('Suppliers'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['active'] = array(
            'title' => $this->l('Active'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id';
        $helper->actions = array('view', 'edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addFooterSpeSupplier&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        
        $helper->title = $this->l('Footer specific by supplier');
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentSupplierFooter($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentSupplierFooter($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT id, active, title, description
        FROM '._DB_PREFIX_.'ec_seo_footer f
        LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang fl ON (fl.id_footer = f.id)
        WHERE id_lang = '.(int)$id_lang.' AND type="supplier" AND spe = 1
        ';
        
        $orderby = Tools::getValue($name.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        $res = Db::getInstance()->ExecuteS($req);
        foreach ($res as $key => &$val) {
            $suppliers = Db::getInstance()->executes(
                'SELECT s.id_supplier, name FROM '._DB_PREFIX_.'supplier s
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_supplier fs ON (fs.id_supplier = s.id_supplier) 
                WHERE id_footer = '.$val['id']
            );
            $concat_supplier = '';
            foreach ($suppliers as $key => $info) {
                $id_supplier = $info['id_supplier'];
                if ($key == 0) {
                    $concat_supplier .= $info['name'].' ('.$id_supplier.')';
                } else {
                    $concat_supplier .= ', '.$info['name'].' ('.$id_supplier.')';
                }
            }
            $val['suppliers'] = $concat_supplier;
        }
       
        return array('res' => $res, 'count' => $count);
    }

    protected function getFooterFormSpeSupplier($id = null)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'footerseospesupplier';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFooterSeoSpeSupplier';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $values = array(
            'id' => null,
            'title' => null,
            'description' => null,
            'active' => null,
        );
        $selected_supplier = array();
        if ($id != null) {
            $info = Db::getInstance()->ExecuteS(
                'SELECT id, title, description, id_lang, active
                FROM '._DB_PREFIX_.'ec_seo_footer sf
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = sf.id)
                WHERE id = '.(int)$id.'
                '
            );
            foreach ($info as $val) {
                $values['title'][$val['id_lang']] = $val['title'];
                $values['description'][$val['id_lang']] = $val['description'];
                $values['active'] = $val['active'];
                $values['id'] = $val['id'];
            }
            $lst_supplier = Db::getInstance()->executes('SELECT id_supplier FROM '._DB_PREFIX_.'ec_seo_footer_supplier WHERE id_footer = '.(int)$id);
            foreach ($lst_supplier as $supplier) {
                $selected_supplier[] = $supplier['id_supplier'];
            }
        }
        $values['lst_supplier[]'] = $selected_supplier;
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Footer Specific supplier'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Descritption'),
                        'name' => 'description',
                        'lang' => true,
                        'autoload_rte' => false
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'size' => 10,
                        'label' => $this->l('Suppliers'),
                        'name' => 'lst_supplier[]',
                        'options' => array(
                            'query' => Supplier::getSuppliers(false, (int)$this->context->language->id),
                            'id' => 'id_supplier',
                            'name' => 'name'
                        )
                    )
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=footerseo&token='.Tools::getAdminTokenLite('AdminModules').'#supplier#spe',
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                        //'id' => 'ecmetacancel_'.$class
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        return $helper->generateForm(array($form));
    }

    protected function renderListFooterSpeManufacturer()
    {
        $name = 'ec_ListFooterSpemanufacturer';
        $fields_list = array();
        $fields_list['id'] = array(
            'title' => $this->l('ID Footer'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['title'] = array(
            'title' => $this->l('Title'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['description'] = array(
            'title' => $this->l('Description'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['manufacturers'] = array(
            'title' => $this->l('Manufacturers'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['active'] = array(
            'title' => $this->l('Active'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id';
        $helper->actions = array('view', 'edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&addFooterSpeManufacturer&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        
        $helper->title = $this->l('Footer specific by manufacturer');
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentManufacturerFooter($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentManufacturerFooter($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT id, active, title, description
        FROM '._DB_PREFIX_.'ec_seo_footer f
        LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang fl ON (fl.id_footer = f.id)
        WHERE id_lang = '.(int)$id_lang.' AND type="manufacturer" AND spe = 1
        ';
        
        $orderby = Tools::getValue($name.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        $res = Db::getInstance()->ExecuteS($req);
        foreach ($res as $key => &$val) {
            $manufacturers = Db::getInstance()->executes(
                'SELECT m.id_manufacturer, name FROM '._DB_PREFIX_.'manufacturer m
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_manufacturer fm ON (fm.id_manufacturer = m.id_manufacturer) 
                WHERE id_footer = '.$val['id']
            );
            $concat_manufacturer = '';
            foreach ($manufacturers as $key => $info) {
                $id_manufacturer = $info['id_manufacturer'];
                if ($key == 0) {
                    $concat_manufacturer .= $info['name'].' ('.$id_manufacturer.')';
                } else {
                    $concat_manufacturer .= ', '.$info['name'].' ('.$id_manufacturer.')';
                }
            }
            $val['manufacturers'] = $concat_manufacturer;
        }
       
        return array('res' => $res, 'count' => $count);
    }

    protected function getFooterFormSpeManufacturer($id = null)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'footerseospemanufacturer';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitFooterSeoSpeManufacturer';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $values = array(
            'id' => null,
            'title' => null,
            'description' => null,
            'active' => null,
        );
        $selected_manufacturer = array();
        if ($id != null) {
            $info = Db::getInstance()->ExecuteS(
                'SELECT id, title, description, id_lang, active
                FROM '._DB_PREFIX_.'ec_seo_footer sf
                LEFT JOIN '._DB_PREFIX_.'ec_seo_footer_lang sfl ON (sfl.id_footer = sf.id)
                WHERE id = '.(int)$id.'
                '
            );
            foreach ($info as $val) {
                $values['title'][$val['id_lang']] = $val['title'];
                $values['description'][$val['id_lang']] = $val['description'];
                $values['active'] = $val['active'];
                $values['id'] = $val['id'];
            }
            $lst_manufacturer = Db::getInstance()->executes('SELECT id_manufacturer FROM '._DB_PREFIX_.'ec_seo_footer_manufacturer WHERE id_footer = '.(int)$id);
            foreach ($lst_manufacturer as $manufacturer) {
                $selected_manufacturer[] = $manufacturer['id_manufacturer'];
            }
        }
        $values['lst_manufacturer[]'] = $selected_manufacturer;
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Footer Specific manufacturer'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Descritption'),
                        'name' => 'description',
                        'lang' => true,
                        'autoload_rte' => false
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'size' => 10,
                        'label' => $this->l('Manufacturers'),
                        'name' => 'lst_manufacturer[]',
                        'options' => array(
                            'query' => Manufacturer::getManufacturers(false, (int)$this->context->language->id),
                            'id' => 'id_manufacturer',
                            'name' => 'name'
                        )
                    )
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=footerseo&token='.Tools::getAdminTokenLite('AdminModules').'#manufacturer#spe',
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                        //'id' => 'ecmetacancel_'.$class
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        return $helper->generateForm(array($form));
    }

    public function updatePositionBlock($position_block)
    {
        foreach ($position_block as $id_block => $position) {
            Db::getinstance()->update(
                'ec_seo_footer_block',
                array(
                    'position' => (int)$position
                ),
                'id = '.(int)$id_block
            );
        }
        return true;
    }

    public function updatePositionBlockLink($position_block)
    {
        foreach ($position_block as $id_link => $position) {
            Db::getinstance()->update(
                'ec_seo_footer_link',
                array(
                    'position' => (int)$position
                ),
                'id = '.(int)$id_link
            );
        }
        return true;
    }

    protected function renderListBlockHTML()
    {
        $name = 'ec_ListBockHtml';
        $fields_list = array();
        $fields_list['id_block_html'] = array(
            'title' => $this->l('ID Block HTML'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => false,
        );
        $fields_list['hook_name'] = array(
            'title' => $this->l('Hook'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        /* $fields_list['content'] = array(
            'title' => $this->l('Content'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        ); */
        $fields_list['active'] = array(
            'title' => $this->l('Active'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_block_html';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&AddBlockHtml&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        
        $helper->title = $this->l('Block HTML');
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentBlockHTML($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentBlockHTML($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT bh.id_block_html, bh.active, content, name as hook_name
        FROM '._DB_PREFIX_.'ec_seo_block_html bh
        LEFT JOIN '._DB_PREFIX_.'ec_seo_block_html_lang bhl ON (bhl.id_block_html = bh.id_block_html)
        LEFT JOIN '._DB_PREFIX_.'hook h ON (h.id_hook = bh.id_hook)
        WHERE id_lang = '.(int)$id_lang.'
        ';
        
        $orderby = Tools::getValue($name.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        $res = Db::getInstance()->ExecuteS($req);
        foreach ($res as $key => &$val) {
        }
       
        return array('res' => $res, 'count' => $count);
    }

    protected function getBlockHTMLForm($id_block_html = null)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'blockhtml';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBlockHtml';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $values = array(
            'id_block_html' => null,
            'id_hook' => null,
            'content' => null,
            'active' => null,
        );
        $title = $this->l('Block HTML');
        $hook_filter = '';
        if ($id_block_html != null) {
            $title .= ' '.$id_block_html;
            $infos = Db::getInstance()->executes(
                '
                SELECT id_hook, bh.active, content, id_lang FROM '._DB_PREFIX_.'ec_seo_block_html bh
                LEFT JOIN '._DB_PREFIX_.'ec_seo_block_html_lang bhl ON (bhl.id_block_html = bh.id_block_html)
                WHERE bh.id_block_html = '.(int)$id_block_html.'
                '
            );
            $values['id_block_html'] = $id_block_html;
            foreach ($infos as $info) {
                $values['id_hook'] = $info['id_hook'];
                $values['active'] = $info['active'];
                $values['content'][$info['id_lang']] = $info['content'];
            }
            $hook_filter = ' AND id_hook != '.(int)$values['id_hook'];
        }
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $id_shop = (int)$this->context->shop->id;
        $tab_hook = array();
        $ec_hook = Db::getInstance()->getRow('SELECT id_hook, name  FROM '._DB_PREFIX_.'hook WHERE name ="displayEcSeoCustomBlock"');
        $tab_hook[] = $ec_hook;
        $hooks = Db::getInstance()->ExecuteS(
            '
            SELECT id_hook, name FROM '._DB_PREFIX_.'hook
            WHERE id_hook NOT IN (SELECT id_hook FROM '._DB_PREFIX_.'ec_seo_block_html WHERE id_shop = '.(int)$id_shop.$hook_filter.')
            AND id_hook != '.(int)$ec_hook['id_hook'].'
            AND name LIKE "%display%"
            ORDER by name
            '
        );
        foreach ($hooks as $hook) {
            $tab_hook[] = $hook;
        }
        $oh = '<';
        $ch = '>';
        $sh = '/';
        $brh= $oh.'br'.$ch;
        $span= $oh.'span';
        $spanc= $oh.$sh.'span'.$ch;
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $title,
                'icon' => 'icon-cogs',
                ),
                'description' => $this->l('If you are using hook ').' '.$oh.'b'.$ch.'displayEcSeoCustomBlock'.$oh.$sh.'b'.$ch.''.$this->l(', just put directly in the template').$span."  style='color:#00B710'>{hook h='displayEcSeoCustomBlock' id_block_html='value_id'}".$spanc.$brh."Exemple :".$brh." id_block_html = 4 : ".$span." style='color:#00B710'".$ch."{hook h='displayEcSeoCustomBlock' id_block_html='4'}".$spanc,
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id_block_html',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Hook'),
                        'name' => 'id_hook',
                        'required' => true,
                        'options' => array(
                          'query' => $tab_hook,
                          'id' => 'id_hook',
                          'name' => 'name'
                        )
                      ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Content'),
                        'name' => 'content',
                        'lang' => true,
                        'autoload_rte' => true
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'required' => true,
                    ),
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=blockhtml&token='.Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        return $helper->generateForm(array($form));
    }

    protected function renderListPageNoIndex()
    {
        $name = 'ec_ListPageNoIndex';
        $fields_list = array();
        $fields_list['id'] = array(
            'title' => $this->l('ID'),
            'type' => 'text',
            'search' => false,
            'class' => 'fixed-width-xs',
            'orderby' => true,
        );
        $fields_list['page'] = array(
            'title' => $this->l('Page'),
            'type' => 'text',
            'search' => true,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&AddPageNoIndex&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        );
        
        $helper->title = $this->l('Page not indexed');
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentPageNoIndex($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentPageNoIndex($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT * FROM '._DB_PREFIX_.'ec_seo_page_noindex
        WHERE 1
        ';
        $values = Tools::getAllValues();
        if (isset($values['submitReset'.$name])) {
            $_POST[$name.'Filter_page'] = '';
        }
        $search = Tools::getValue('submitFilter'.$name);
        if ($search) {
            $page = Tools::getValue($name.'Filter_page');
            if (!empty($page)) {
                $req.= ' AND page like "%'.pSQL($page).'%"';
            }
        }
        $orderby = Tools::getValue($name.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;
        
        
        $res = Db::getInstance()->ExecuteS($req);
        return array('res' => $res, 'count' => $count);
    }

    protected function renderListPageCMSNoIndex()
    {
        $name = 'ec_ListPageCMSNoIndex';
        $fields_list = array();
        $fields_list['id_cms'] = array(
            'title' => $this->l('ID CMS'),
            'type' => 'text',
            'search' => true,
            'class' => 'fixed-width-xs',
            'orderby' => true,
        );
        $fields_list['meta_title'] = array(
            'title' => $this->l('Name'),
            'type' => 'text',
            'search' => true,
            'orderby' => false,
        );
        $fields_list['page'] = array(
            'title' => $this->l('Page'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $fields_list['indexation'] = array(
            'title' => $this->l('Indexation by search engines'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
        );
        $helper = new HelperList();
        $helper->no_link = true;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_cms';
        //$helper->actions = array('delete');
        $helper->show_toolbar = true;
        /* $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&AddPageNoIndex&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new')
        ); */
        
        $helper->title = $this->l('Page CMS not indexed');
        $helper->table = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        $content = $this->getListContentPageCMSNoIndex($name);
        $helper->listTotal = $content['count'];
        return $helper->generateList($content['res'], $fields_list);
    }
    
    public function getListContentPageCMSNoIndex($name)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $pagination = Tools::getValue($name.'_pagination');
        if ($pagination == null || $pagination == 0) {
            $pagination = Tools::getValue('selected_pagination');
            if ($pagination == null || $pagination == 0) {
                $pagination = 50;
            }
        }
        $page = Tools::getValue('submitFilter'.$name);
        if ($page == null || $page == 0) {
            $page = Tools::getValue('page');
            if ($page == null || $page == 0) {
                $page = 1;
            }
        }
        if ($page == 1) {
            $page = 0;
        } else {
            $page = ($page-1)*$pagination;
        }

        $req = 'SELECT c.id_cms, cl.meta_title, indexation FROM '._DB_PREFIX_.'cms c
        LEFT JOIN '._DB_PREFIX_.'cms_lang cl ON (cl.id_cms = c.id_cms)
        WHERE id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop.'
        ';
        $values = Tools::getAllValues();
        if (isset($values['submitReset'.$name])) {
            $_POST[$name.'Filter_meta_title'] = '';
            $_POST[$name.'Filter_id_cms'] = '';
            $_POST[$name.'Filter_indexation'] = '';
        }
        $search = Tools::getValue('submitFilter'.$name);
        if ($search) {
            $meta_title = Tools::getValue($name.'Filter_meta_title');
            if (!empty($meta_title)) {
                $req.= ' AND meta_title like "%'.pSQL($meta_title).'%"';
            }
            $id_cms = Tools::getValue($name.'Filter_id_cms');
            if (!empty($id_cms)) {
                $req.= ' AND c.id_cms like "%'.pSQL($id_cms).'%"';
            }
            $indexation = Tools::getValue($name.'Filter_indexation');
            if (!empty($indexation)) {
                $req.= ' AND c.indexation like "%'.pSQL($indexation).'%"';
            }
        }
        $orderby = Tools::getValue($name.'Orderby');
        if (Tools::strlen($orderby) > 0) {
            $req .= ' ORDER BY '.pSQL($orderby).' '.pSQL(Tools::getValue($name.'Orderway'));
        }
        $count = count(Db::getInstance()->ExecuteS($req));
        $req.= ' LIMIT '.(int)$page.', '.(int)$pagination;

        
        $res = Db::getInstance()->ExecuteS($req);
        foreach ($res as $key => &$val) {
            $cms = new CMS($val['id_cms']);
            $pagelink = $this->getLinkByObj($cms, $id_lang, $id_shop);
            $val['page'] = $pagelink;
        }
        
        return array('res' => $res, 'count' => $count);
    }

    protected function getPageNoIndexForm($id = null)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = 'pagenoindex';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPageNoIndex';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $values = array(
            'id' => null,
            'page' => null,
        );
        $title = $this->l('Page not indexed');
        if ($id != null) {
            $title .= ' '.$id;
            $values = Db::getInstance()->getRow(
                '
                SELECT id, page FROM '._DB_PREFIX_.'ec_seo_page_noindex 
                WHERE id = '.(int)$id.'
                '
            );
        }
        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $title,
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'id',
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'page',
                        'label' => $this->l('Page link'),
                    ),
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&btl=1&oactive=pagenoindex&token='.Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                    )
                ),
                 'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        return $helper->generateForm(array($form));
    }

    public function __call($name, $arguments)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $hook_name = str_replace('hook', '', $name);
        $content = Db::getInstance()->getValue(
            '
            SELECT content FROM '._DB_PREFIX_.'ec_seo_block_html_lang bhl
            LEFT JOIN '._DB_PREFIX_.'ec_seo_block_html bh ON (bh.id_block_html = bhl.id_block_html)
            LEFT JOIN '._DB_PREFIX_.'hook h ON (h.id_hook = bh.id_hook)
            WHERE h.name = "'.pSQL($hook_name).'"
            AND bh.id_shop = '.(int)$id_shop.'
            AND bhl.id_lang = '.(int)$id_lang.'
            AND bh.active = 1
            '
        );
        if ($content) {
            return $this->showContentCustom($content);
        }
    }

    public function hookDisplayEcSeoCustomBlock($params)
    {
        $id_shop = (int)$this->context->shop->id;
        $id_lang = (int)$this->context->language->id;
        $id_block_html = $params['id_block_html'];
        $content = Db::getInstance()->getValue(
            '
            SELECT content FROM '._DB_PREFIX_.'ec_seo_block_html_lang bhl
            LEFT JOIN '._DB_PREFIX_.'ec_seo_block_html bh ON (bh.id_block_html = bhl.id_block_html)
            LEFT JOIN '._DB_PREFIX_.'hook h ON (h.id_hook = bh.id_hook)
            WHERE bh.id_block_html = '.(int)$id_block_html.'
            AND bh.id_shop = '.(int)$id_shop.'
            AND bhl.id_lang = '.(int)$id_lang.'
            AND bh.active = 1
            '
        );
        if ($content) {
            return $this->showContentCustom($content);
        }
    }

    public function showContentCustom($content)
    {
        $this->smarty->assign(array(
            'content' => $content,
        ));
        return $this->display(__FILE__, 'views/templates/front/customBlockHtml.tpl');
    }

    public function getAuthSK()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.smartkeyword.io/oauth/token?refresh_token='.$this->freeToken,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        ));

        $response = json_decode(curl_exec($curl), true);
        
        
        curl_close($curl);
        if (!isset($response['access_token'])) {
            return false;
        }
        return $response['access_token'];
    }

    public function getKeywordData($id_lang, $keyword, $page)
    {
        return;
        $content_page = Tools::file_get_contents($page);
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = $dom->loadHTML($content_page);
        $main = $dom->getElementById('content-wrapper');
        $html = $dom->saveHTML($main);
        return $this->getAPIKeyworkData(str_replace('en', 'us', Language::getIsoById($id_lang)), $keyword, $clean_html);
    }

    public function getAPIKeyworkData($country, $keyword, $content = '')
    {
        $token = $this->getAuthSK();
        $curl = curl_init();
        $data = array(
            'searchEngine' => array(
                'country' => $country
            ),
            'keyword' => array(
                'text' => $keyword
            ),
            'content' => $content
        );
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.smartkeyword.io/semantic/content-optimization',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token,
                    'Content-Type: text/plain'
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    public function refreshKeywordData($id, $page_type, $page_infos)
    {
        $id_shop = (int)$this->context->shop->id;
        foreach ($page_infos as $id_lang => &$page_info) {
            $page = $page_info['link'];
            $keyword_datas = $this->getKeywordData($id_lang, $page_info['keyword'], $page);
            $keyword_datas = $this->getKeywordData($id_lang, $page_info['keyword'], $page);
            Db::getinstance()->insert(
                'ec_seo_smartkeyword',
                array(
                    'id' => (int)$id,
                    'id_lang' => (int)$id_lang,
                    'id_shop' => (int)$id_shop,
                    'keyword' => pSQL($page_info['keyword']),
                    'page' => pSQL($page_type),
                    'info' => pSQL(json_encode($keyword_datas)),
                    'date_upd' => date('Y-m-d H:i:s'),
                ),
                false,
                true,
                Db::ON_DUPLICATE_KEY
            );
           /*
            $netlinking = array();
            if (isset($keyword_datas['data'])) {
                if (is_array($keyword_datas['data']['semantic']['serpDetails'][0])) {
                    //dump($keyword_datas['data']['semantic']['serpDetails'][0]);
                    $cpt_link = 1;
                    foreach ($keyword_datas['data']['semantic']['serpDetails'][0] as $infolink) {
                        if (isset($infolink['score'])) {
                            $netlinking[($infolink['score'].$cpt_link)*1000] = $infolink['url'];
                            $cpt_link++;
                        }
                    }
                } else {
                    $infolink = $keyword_datas['data']['semantic']['serpDetails'][0];
                    $netlinking[$infolink['score']*1000] = $infolink['url'];
                }
                krsort($netlinking);
                $page_info['netlinking'] = $netlinking;
                $page_info['keywordsDetails'] = $keyword_datas['data']['semantic']['keywordsDetails'];
                $page_info['relatedKeywords'] = $keyword_datas['data']['additionalKeywords']['relatedKeywords'];
                $page_info['peopleAlsoAsk'] = $keyword_datas['data']['additionalKeywords']['peopleAlsoAsk'];
            } */
        }
        return true;
    }

    public function getLinkByObj($obj, $id_lang, $id_shop)
    {
        $class = get_class($obj);
        $type = Tools::strtolower($class);
        if ($type == 'manufacturer' || $type == 'supplier') {
            $funcLink = 'get'.$type.'Link';
            $obj_lang = new $class($obj->id, $id_lang, $id_shop);
            $link = $this->context->link->{$funcLink}($obj, null, $id_lang, $id_shop);
        }
        if ($type == 'category') {
            $link = $this->context->link->getCategoryLink($obj->id, null, $id_lang);
        }
        if ($type == 'cms') {
            $link = $this->context->link->getCMSLink($obj, null, null, $id_lang, $id_shop);
        }
        if ($type == 'product') {
            $link = $this->context->link->getProductLink($obj, null, null, null, $id_lang, $id_shop);
        }
        if ($type == 'meta') {
            $link = $this->context->link->getPageLink($obj->page, null, $id_lang, null, false, $id_shop);
        }
        return $link;
    }

    public function checkLangForm()
    {
        $list = array('product' => true, 'category' => true, 'cms' => false, 'supplier' => false, 'manufacturer' => false);
        $shops = Shop::getShops(true);
        $languages = Language::getLanguages(false);
        foreach ($shops as $shop) {
            $id_shop = $shop['id_shop'];
            foreach ($list as $class => $spe) {
                $id_footer = Db::getInstance()->getValue('SELECT id FROM '._DB_PREFIX_.'ec_seo_footer WHERE type = "'.pSQL($class).'" AND id_shop = '.(int)$id_shop);
                foreach ($languages as $lang) {
                    $id_lang = $lang['id_lang'];
                    $exist = Db::getInstance()->getValue('SELECT id_footer FROM '._DB_PREFIX_.'ec_seo_footer_lang WHERE id_footer = '.(int)$id_footer.' AND id_lang = '.(int)$id_lang);
                    if ($exist) {
                        continue;
                    }
                    Db::getinstance()->insert(
                        'ec_seo_footer_lang',
                        array(
                            'id_footer' => (int)$id_footer,
                            'id_lang' => (int)$id_lang,
                            'title' => '',
                            'description' => '',
                        )
                    );
                }
            }
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                foreach ($list as $class => $spe) {
                    $exist = Db::getInstance()->getValue('SELECT id_lang FROM '._DB_PREFIX_.'ec_seo_'.$class.'_mg_gen WHERE id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop);
                    if ($exist) {
                        continue;
                    }
                    Db::getinstance()->insert(
                        'ec_seo_'.$class.'_mg_gen',
                        array(
                            'meta_title' => '',
                            'meta_description' => '',
                            'id_lang' => (int)$id_lang,
                            'id_shop' => (int)$id_shop
                        )
                    );
                }
                $exist = Db::getInstance()->getValue('SELECT id_lang FROM '._DB_PREFIX_.'ec_seo_bag_gen WHERE id_lang = '.(int)$id_lang.' AND id_shop = '.(int)$id_shop);
                if ($exist) {
                    continue;
                }
                Db::getinstance()->insert(
                    'ec_seo_bag_gen',
                    array(
                        'balise_alt' => '',
                        'id_lang' => (int)$id_lang,
                        'id_shop' => (int)$id_shop
                    )
                );
            }
        }
    }

    public function followLink($link, $logger = null, $forced_timeout = null)
    {
        $timeout = is_null($forced_timeout) ? $this->FOLLOWLINK_TIMEOUT : $forced_timeout;
        $a_ret = $this->goCurl($link, $timeout);
        if (preg_match('/SSL/', $a_ret['infos']['err'])) {
            $link = str_replace('https', 'http', $link);
            $a_ret = $this->goCurl($link, $timeout);
        }
        $tries = 1;
        //'Operation timed out after x milliseconds' is normal response
        while (preg_match('/Connection\ timed\ out\ after/', $a_ret['infos']['err'])      // 522
            || preg_match('/connect\(\)\ timed\ out\!/', $a_ret['infos']['err'])          //
            || preg_match('/Gateway\ Time\-out/', $a_ret['infos']['err'])                 // 504
            || preg_match('/name\ lookup\ timed\ out/', $a_ret['infos']['err'])           //
            || preg_match('/Resolving\ timed\ out\ after/', $a_ret['infos']['err'])) {    //
            if ($logger != null) {
                $logger->logInfo(
                    'Error (' . $a_ret['infos']['errno'] . ') "' . $a_ret['infos']['err']. '"' . "\n"
                    . 'Infos ' . var_export($a_ret['infos']['infos'], true)
                );
            }
            
            $tries++;
            if (($this->FOLLOWLINK_RETRIES && $this->FOLLOWLINK_RETRIES < $tries) || (50 < $tries)) {
                return false;
            }
            if ($logger != null) {
                $logger->logInfo('Try (' . $tries . ') relaunching ' . $link);
            }
            $a_ret = $this->goCurl($link, $timeout);
        }
/*        while (in_array($a_ret['infos']['errno'], array('6', '7'))) { // 22 ? test http
            $this->logger->logInfo(
                $logger,
                'Error (' . $a_ret['infos']['errno'] . ') "' . $a_ret['infos']['err']. '"' . "\n"
                . 'Infos ' . var_export($a_ret['infos']['infos'], true) . "\n"
                . 'Relaunched ' . $link);
            $a_ret = $this->goCurl($link, $timeout);
        }*/

        if ($this->FOLLOWLINK_LOG && $logger != null) {
            $logger->logInfo(var_export($a_ret, true));
        }

        return $a_ret['result'];
    }

    public function goCurl($link, $forced_timeout = null)
    {
        $timeout = is_null($forced_timeout) ? $this->FOLLOWLINK_TIMEOUT : $forced_timeout;
        $useragent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0';
        $cookiefile = dirname(__FILE__).'/' . 'cookie_' . (parse_url($link, PHP_URL_HOST) ?: 1) . '.txt';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $ht_access = Configuration::getGlobalValue('EC_SEO_HTACCESS');
        if ($ht_access) {
            $user = Configuration::getGlobalValue('EC_SEO_LOGIN_HTACCESS');
            $pass = Configuration::getGlobalValue('EC_SEO_PW_HTACCESS');
            if (Tools::strlen($user) > 0 && Tools::strlen($pass) > 0) {
                curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$pass);
            }
        }
        //curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/../cacert.pem');
        //curl_setopt($ch, CURLOPT_CAINFO, _PS_CACHE_CA_CERT_FILE_);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
        $result = curl_exec($ch);
        $tab_err = array(
            'err' => curl_error($ch),
            'errno' => curl_errno($ch),
            'infos' => curl_getinfo($ch)
        );
        curl_close($ch);

        return array('infos' => $tab_err, 'result' => $result);
    }
}
