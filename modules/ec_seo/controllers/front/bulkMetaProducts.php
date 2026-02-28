<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL Ether Creation
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL Ether Creation is strictly forbidden.
 * In order to obtain a license, please contact us: contact@ethercreation.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Ether Creation
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la SARL Ether Creation est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter la SARL Ether Creation a l'adresse: contact@ethercreation.com
 * ...........................................................................
 *
 * @author    Ether Creation SARL <contact@ethercreation.com>
 * @copyright 2008-2021 Ether Creation SARL
 * @license   Commercial license
 * International Registered Trademark & Property of Ether Creation SARL
 */
require_once dirname(__FILE__) . '/../../ec_seo.php';
if ('cli' != php_sapi_name()) {
    require_once _PS_ROOT_DIR_.'/init.php';
}


class Ec_seoBulkMetaProductsModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    public $ajax = true;
    protected $ec_token;
    protected $prefix = 'TASK_EC_SEO_META_PRODUCTS_';
    protected $cron = 'bulkMetaProducts';
    protected $FOLLOWLINK_TIMEOUT = 4;
    protected $FOLLOWLINK_RETRIES = false;
    protected $FOLLOWLINK_LOG = false;
    protected $logger;
    protected $help;
    protected $stopTime;
    protected $nbCron;
    protected $params;
    protected $chain;
    protected $kill;
    protected $spy;
    protected $spy2;
    protected $who;
    protected $prg;
    protected $pos;

    private $id_shop;

    protected $listStages = [
        'started',
        'metaProductGen',
        'metaProductSpe',
    ];
    
    protected $starting;

    public function __construct()
    {
        parent::__construct();
        $this->ajax = true;
        $this->logger = new FileLogger();
        $this->logger->setFilename(_PS_ROOT_DIR_ . '/modules/ec_seo/log/'.$this->cron.'.log');
        $this->protocol = (((Configuration::get('PS_SSL_ENABLED') == 1) &&
                (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') == 1)) ? 'https://' : 'http://' );
        set_error_handler([get_class(), 'exception_error_handler']);
    }

    public function init()
    {
        parent::init();

        
        //get input
        $this->ec_token = Tools::getValue('ec_token', null);
        $this->id_shop = Tools::getValue('id_shop', $this->context->shop->id ?? Configuration::get('PS_SHOP_DEFAULT'));
        $this->stopTime = time() + 25;
        $paramHelp = Tools::getValue('help', null);
        $this->help = !is_null($paramHelp);
        
        $paramNbCron = Tools::getValue('nbC', null);
        $this->nbCron = is_null($paramNbCron) ? 0 : (int) $paramNbCron;

        $paramJobID = Tools::getValue('jid', null);

        $paramAct = Tools::getValue('act', null);
        $this->action = (Configuration::getGlobalValue($this->prefix . 'ACT') === 'die') ? 'die' : ((empty($paramAct)) ? 'go' : $paramAct);

        $paramSpy = Tools::getValue('spy', null);
        $this->spy = (empty($paramSpy)) ? false : true;

        $paramSpy2 = Tools::getValue('spytwo', null);
        $this->spy2 = (empty($paramSpy2)) ? false : true;
        $this->who = $this->spy ? ($this->spy2 ? 'spy2' : 'spy') : 'normal';

        $paramKill = Tools::getValue('kill', null);
        $this->kill = is_null($paramKill) ? false : true;

        $paramPrg = Tools::getValue('prg', null);
        $paramPos = Tools::getValue('pos', null);
        if (!is_null($paramPrg)) {
            $this->prg = (int) $paramPrg;
            $this->pos = is_null($paramPos) ? 1 : (int) $paramPos;
            $this->chain = '&prg=' . $this->prg . '&pos=' . $this->pos;
        } else {
            $this->prg = false;
            $this->chain = '';
        }

        $this->ts = preg_replace('/0\.([0-9]{6}).*? ([0-9]+)/', '$2$1', microtime());
        $this->jid = is_null($paramJobID) ? $this->ts : $paramJobID;
        $this->params = '?ec_token=' . $this->ec_token . '&ts=' . $this->ts . '&jid=' . $this->jid.'&id_shop='.$this->id_shop;
        
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $this->base_uri = $this->context->link->getModuleLink($this->module->name, $this->cron) . $this->params . $this->chain;
        } else {
            $this->base_uri = $this->protocol . Tools::getShopDomain() . __PS_BASE_URI__ . 'index.php' . $this->params . $this->chain . '&fc=module&module=' . $this->module->name . '&controller=' . $this->cron;
        }
                
        
        $this->starting = ((bool) $this->ec_token) & is_null($paramSpy) & is_null($paramNbCron) & is_null($paramKill) & is_null($paramAct);
        
//        $this->logger->logInfo(
//            $this->cron . ' '
//            . $this->who . ' entered, parameters '
//            . ','
//            . (int) $this->nbCron . ','
//            . $this->action . ','
//            . (int) $this->spy . ','
//            . (int) $this->spy2 . ','
//            . (int) $this->kill
//        );
    }
    
    public function checkAccess()
    {
        //verify token and other infos
        if ($this->ec_token !== Configuration::getGlobalValue('EC_TOKEN_SEO')) {
            Tools::redirect($this->context->link->getPageLink('pagenotfound'));
        }
        return true;
    }

    public function postProcess()
    {
        // kill
        if ($this->kill) {
            if (Configuration::getGlobalValue($this->prefix . 'STATE') != 'done') {
                Configuration::updateGlobalValue($this->prefix . 'ACT', 'die');
            }
            exit('kill');
        }


        
        //TODO ajouter sécurité

        // advancement control
        if ($this->spy) {
            self::answer('spy');
            sleep(14);
            $state = Configuration::getGlobalValue($this->prefix . 'STATE');
            $progress = Configuration::getGlobalValue($this->prefix . 'PROGRESS');
            if ($this->nbCron == $progress) {
                if ($this->spy2) {
                    if ($state != 'done') {
                        Configuration::updateGlobalValue($this->prefix . 'STATE', 'still');
                    }
                } else {
                    $this->module->followLink($this->base_uri . '&spy=1&spytwo=1&nbC=' . $progress, $this->logger);
                }
            } else {
                $this->module->followLink($this->base_uri . '&spy=1&nbC=' . $progress, $this->logger);
            }
            exit('bond');
        }

        // init or give up
        $etat = Configuration::getGlobalValue($this->prefix . 'STATE');
        if (!$this->starting && ($this->action === 'die')) {
            // kill -> die
            Configuration::updateGlobalValue($this->prefix . 'STATE', 'done');
            Configuration::updateGlobalValue($this->prefix . 'END_TIME', date('Y-m-d H:i:s'));
            Configuration::updateGlobalValue($this->prefix . 'ACT', 'go');
            exit('dead');
        }
        if ($this->starting && ($etat === 'running')) {
            // avoid double launch
            $progress = Configuration::getGlobalValue($this->prefix . 'PROGRESS');
            // spy should be launched to verify false running state due to server reboot
            $this->module->followLink($this->base_uri . '&spy=1&nbC=' . (int) $progress, $this->logger);
            exit('nodoubleplease');
        }
        if (!$this->starting && ($etat === 'still')) {
            // spy got asleep but process is running
            Configuration::updateGlobalValue($this->prefix . 'STATE', 'running');
            $this->module->followLink($this->base_uri . '&spy=1&nbC=' . $this->nbCron, $this->logger);
        }
        if ($this->starting) {
            // init
            Configuration::updateGlobalValue($this->prefix . 'START_TIME', date('Y-m-d H:i:s'));
            Configuration::updateGlobalValue($this->prefix . 'END_TIME', '');
            Configuration::updateGlobalValue($this->prefix . 'STAGE', reset($this->listStages));
            Configuration::updateGlobalValue($this->prefix . 'PROGRESS', 0);
            Configuration::updateGlobalValue($this->prefix . 'PROGRESSMAX', 0);
            Configuration::updateGlobalValue($this->prefix . 'LOOPS', 0);
            Configuration::updateGlobalValue($this->prefix . 'STATE', 'running');
            Configuration::updateGlobalValue($this->prefix . 'ACT', 'go');
            Configuration::updateGlobalValue($this->prefix . 'MESSAGE', '');
            // start advancement control
            $this->module->followLink($this->base_uri . '&spy=1&nbC=0');
        } else {
            Configuration::updateGlobalValue($this->prefix . 'PROGRESS', $this->nbCron);
        }
        $stage = Configuration::getGlobalValue($this->prefix . 'STAGE');
        
        // treat loops, breaks, end
        if ($this->action === 'next') {
            $this->action = 'go';
            Configuration::updateGlobalValue($this->prefix . 'ACT', 'go');

            $numStage = array_search($stage, $this->listStages, true);
            $keys = array_keys($this->listStages);
            $next = $nextKey = false;
            foreach ($keys as $key) {
                if ($next) {
                    $nextKey = $key;
                    break;
                }
                if ($numStage == $key) {
                    $next = true;
                }
            }

            if ($nextKey) {
                $stage = $this->listStages[$nextKey];
                $this->nbCron = 0;
            } else {
                Configuration::updateGlobalValue($this->prefix . 'STATE', 'done');
                Configuration::updateGlobalValue($this->prefix . 'END_TIME', date('Y-m-d H:i:s'));
                if ($this->prg) {
                    //chaining with other task
                    //$nextCron = Catalog::getNextCron($this->prg, $this->pos);
                    $nextCron = false;
                    if ($nextCron) {
                        $this->module->followLink($nextCron['link'] . '&prg=' . $this->prg . '&pos=' . $nextCron['position'], $this->logger);
                    }
                }

                exit('done');
            }
            Configuration::updateGlobalValue($this->prefix . 'STAGE', $stage);
            Configuration::updateGlobalValue($this->prefix . 'LOOPS', 0);
            Configuration::updateGlobalValue($this->prefix . 'PROGRESS', 0);
            Configuration::updateGlobalValue($this->prefix . 'PROGRESSMAX', 0);
        }
        

        
        //aiguillage
        self::answer($stage);
        if (method_exists(get_class(), $stage)) {
            try {
                $reps = $this->$stage();
            } catch (Exception $e) {
                $reps = 'In "' . $stage . '" : ' . $e->getMessage() . ' in line ' . $e->getLine() . ' of file ' . $e->getFile();
            }
            if ($reps === true) {
                Configuration::updateGlobalValue($this->prefix . 'ACT', 'next');
                $this->module->followLink($this->base_uri . '&nbC=0&act=next', $this->logger);
            } elseif (is_numeric($reps)) {
                Configuration::updateGlobalValue($this->prefix . 'LOOPS', Configuration::getGlobalValue($this->prefix . 'LOOPS') + 1);
                $this->module->followLink($this->base_uri . '&nbC=' . $reps, $this->logger);
            }
        } else {
            exit('done');
        }

        if ($reps !== true && (!is_numeric($reps))) {
            Configuration::updateGlobalValue($this->prefix . 'MESSAGE', var_export($reps, true));
            $this->logger->logInfo(
                $this->cron . ' ' . $this->who . ', ' . ', stage ' . $stage . ', ' . var_export($reps, true)
            );
        }
    }

    private function started()
    {
        Db::getinstance()->delete('ec_seo_meta_temp', 'page = "product"');
        echo 'Task successfully started. ';

        return true;
    }
    

    
    public static function exception_error_handler($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            // Ce code d'erreur n'est pas inclu dans error_reporting

            return;
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    public static function answer($response)
    {
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }
        
        if (is_callable('fastcgi_finish_request')) {
            /*
             * This works in Nginx but the next approach not
             */
            echo $response;
            session_write_close();
            fastcgi_finish_request();
            return;
        }
        
        if (is_callable('litespeed_finish_request')) {
            /*
             * This works in Nginx but the next approach not
             */
            echo $response;
            session_write_close();
            litespeed_finish_request();
            return;
        }

        ob_start();
        $serverProtocole = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
        if (!preg_match('/^HTTP/', $serverProtocole)) {
            $serverProtocole = ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0') ?: 'HTTP/1.0';
        }
        header($serverProtocole.' 200 OK');
        echo $response;
        header("Content-Encoding: none");
        header('Content-Length: '.ob_get_length());
        header('Connection: close');
        ob_end_flush();
        @ob_flush();
        flush();
    }

    public function metaProductGen()
    {
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        $id_shop = $this->id_shop;
        $context = Context::getContext();
        $context->employee = new Employee(1);
        $context->shop->id = (int) $id_shop;
        $mod = new Ec_seo();
        $errors = array();
        $info_gen = Db::getInstance()->executes('SELECT meta_title, meta_description, id_lang FROM '._DB_PREFIX_.'ec_seo_product_mg_gen WHERE id_shop = '.(int)$id_shop);
        $tab_config = array();
        foreach ($info_gen as $info) {
            $tab_config['meta_title'][$info['id_lang']] = $info['meta_title'];
            $tab_config['meta_description'][$info['id_lang']] = $info['meta_description'];
        }
        $tab_cat = array();
        $cats = Db::getInstance()->executes('SELECT id_category FROM '._DB_PREFIX_.'ec_seo_product_mg_cat');
        foreach ($cats as $cat) {
            $tab_cat[] = $cat['id_category'];
        }
        $filter_cat = '';
        if (count($tab_cat) > 0) {
            $filter_cat = 'AND id_category_default NOT IN ('.implode(',', $tab_cat).')';
        }
        $active_backup = Configuration::get('EC_SEO_BACKUP');
        if ($active_backup) {
            $backup = array();
            $start_date = str_replace(' ', '-', Configuration::get($this->prefix.'START_TIME'));
            $backup_file = dirname(__FILE__).'/../../backup/MetaProducts/'.$start_date.'.json';
        }
        $active_prod = Configuration::get('EC_SEO_META_ACTIVE_PROD');
        $only_empty_metas = Configuration::get('EC_SEO_ONLY_EMTPY_METAS');
        $req_active_prod = '';
        if ($active_prod) {
            $req_active_prod = 'AND active = 1';
        }
        $prods = Db::getInstance()->executes('SELECT id_product FROM '._DB_PREFIX_.'product_shop WHERE id_shop = '.(int)$id_shop.' '.$filter_cat.' '.$req_active_prod);
        $jobLine = 0;
        foreach ($prods as $key => $val) {
            if (($this->stopTime < time()) && ($jobLine > $this->nbCron)) {
                if ($active_backup) {
                    if (count($backup) > 0) {
                        file_put_contents($backup_file, json_encode($backup), FILE_APPEND);
                    }
                }
                return $jobLine;
            }
            $jobLine++;
            if (($jobLine < $this->nbCron)) {
                continue;
            }
            $id_product = $val['id_product'];
            if (($id_product > 0) == false) {
                continue;
            }
            $prod = new Product($id_product, false, null, $id_shop);
            if (!Validate::isLoadedObject($prod)) {
                $this->logger->logInfo('Product '.$id_product.' object invalid');
                continue;
            }
            $config_prod = $mod->getValueVarProd($prod);
            if ($active_backup) {
                $backup_prod = array(
                    'id' => $prod->id,
                    'meta_description' => $prod->meta_description,
                    'meta_title' => $prod->meta_title,
                );
                $backup[] = $backup_prod;
            }
            
            $update = false;
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                $u_lang = false;
                if (Tools::strlen($tab_config['meta_title'][$id_lang]) > 0 && (!$only_empty_metas || Tools::strlen($prod->meta_title[$id_lang]) == 0)) {
                    $meta_title = $mod->genMetaProduct($config_prod, $prod, $tab_config['meta_title'][$id_lang], $id_lang, $prod->meta_title[$id_lang]);
                    if ($meta_title != $prod->meta_title[$id_lang]) {
                        $prod->meta_title[$id_lang] = $meta_title;
                        $update = true;
                        $u_lang = true;
                    }
                }
                if (Tools::strlen($tab_config['meta_description'][$id_lang]) > 0 && (!$only_empty_metas || Tools::strlen($prod->meta_description[$id_lang]) == 0)) {
                    $meta_description = $mod->genMetaProduct($config_prod, $prod, $tab_config['meta_description'][$id_lang], $id_lang, $prod->meta_description[$id_lang]);
                    if ($meta_description != $prod->meta_description[$id_lang]) {
                        $prod->meta_description[$id_lang] = $meta_description;
                        $update = true;
                        $u_lang = true;
                    }
                }
                if ($u_lang) {
                    Db::getinstance()->insert(
                        'ec_seo_meta_temp',
                        array(
                            'id' => (int)$id_product,
                            'page' => 'product',
                            'id_lang' => (int)$id_lang,
                            'id_shop' => (int)$id_shop,
                        )
                    );
                }
            }

            if ($update) {
                try {
                    $prod->save();
                } catch (Exception $e) {
                    $this->logger->logInfo('Fail save prod '.$id_product.' ' . $e->getMessage() . ' in line ' . $e->getLine() . ' of file ' . $e->getFile());
                }
            }

            if (0 === ($jobLine % 50)) {
                Configuration::updateGlobalValue($this->prefix.'PROGRESS', $jobLine);
            }
        }
        if ($active_backup) {
            if (count($backup) > 0) {
                file_put_contents($backup_file, json_encode($backup), FILE_APPEND);
            }
        }
        return true;
    }

    public function metaProductSpe()
    {
        //return true;
        $languages = Language::getLanguages(false);
        $id_shop = $this->id_shop;
        $context = Context::getContext();
        $context->employee = new Employee(1);
        $context->shop->id = (int) $id_shop;
        $mod = new Ec_seo();
        $errors = array();
        $info_spe = Db::getInstance()->executes(
            'SELECT id, meta_title, meta_description, id_category, id_lang FROM '._DB_PREFIX_.'ec_seo_product_mg mg
            LEFT JOIN '._DB_PREFIX_.'ec_seo_product_mg_lang mgl ON (mg.id = mgl.id_seo_product_mg)
            LEFT JOIN '._DB_PREFIX_.'ec_seo_product_mg_cat mgc ON (mg.id = mgc.id_seo_product_mg)
            WHERE id_shop = '.(int)$id_shop.' AND id_category > 0'
        );
        if (!$info_spe) {
            return true;
        }
        $tab_config = array();
        $tab_cat = array();
        foreach ($info_spe as $info) {
            $tab_config[$info['id_category']]['meta_title'][$info['id_lang']] = $info['meta_title'];
            $tab_config[$info['id_category']]['meta_description'][$info['id_lang']] = $info['meta_description'];
            $tab_cat[$info['id_category']] = $info['id_category'];
        }
        
        $filter_cat = '';
        if (count($tab_cat) > 0) {
            $filter_cat = 'AND id_category_default IN ('.implode(',', $tab_cat).')';
        }
        $active_backup = Configuration::get('EC_SEO_BACKUP');
        if ($active_backup) {
            $backup = array();
            $start_date = str_replace(' ', '-', Configuration::get($this->prefix.'START_TIME'));
            $backup_file = dirname(__FILE__).'/../../backup/MetaProducts/'.$start_date.'.json';
        }
        $only_empty_metas = Configuration::get('EC_SEO_ONLY_EMTPY_METAS');
        $prods = Db::getInstance()->executes('SELECT id_product FROM '._DB_PREFIX_.'product_shop WHERE id_shop = '.(int)$id_shop.' '.$filter_cat);
        $jobLine = 0;
        foreach ($prods as $key => $val) {
            if (($this->stopTime < time()) && ($jobLine > $this->nbCron)) {
                if ($active_backup) {
                    if (count($backup) > 0) {
                        $backup_old = json_decode(Tools::file_get_contents($backup_file), true);
                        foreach ($backup_old as $val) {
                            $backup[] = $val;
                        }
                        file_put_contents($backup_file, json_encode($backup));
                    }
                }
                return $jobLine;
            }
            $jobLine++;
            if (($jobLine < $this->nbCron)) {
                continue;
            }
            $id_product = $val['id_product'];
            $prod = new Product($id_product, false, null, $id_shop);
            if (!Validate::isLoadedObject($prod)) {
                $this->logger->logInfo('Product '.$id_product.' object invalid');
                continue;
            }
            $config_prod = $mod->getValueVarProd($prod);
            if ($active_backup) {
                $backup_prod = array(
                    'id' => $prod->id,
                    'meta_description' => $prod->meta_description,
                    'meta_title' => $prod->meta_title,
                );
                $backup[] = $backup_prod;
            }
            $update = false;
            foreach ($languages as $lang) {
                $u_lang = false;
                $id_lang = $lang['id_lang'];
                if (Tools::strlen($tab_config[$prod->id_category_default]['meta_title'][$id_lang]) > 0 && (!$only_empty_metas || Tools::strlen($prod->meta_title[$id_lang]) == 0)) {
                    $meta_title = $mod->genMetaProduct($config_prod, $prod, $tab_config[$prod->id_category_default]['meta_title'][$id_lang], $id_lang, $prod->meta_title[$id_lang]);
                    if ($meta_title != $prod->meta_title[$id_lang]) {
                        $prod->meta_title[$id_lang] = $meta_title;
                        $update = true;
                        $u_lang = true;
                    }
                }
                if (Tools::strlen($tab_config[$prod->id_category_default]['meta_description'][$id_lang]) > 0 && (!$only_empty_metas || Tools::strlen($prod->meta_description[$id_lang]) == 0)) {
                    $meta_description = $mod->genMetaProduct($config_prod, $prod, $tab_config[$prod->id_category_default]['meta_description'][$id_lang], $id_lang, $prod->meta_description[$id_lang]);
                    if ($meta_description != $prod->meta_description[$id_lang]) {
                        $prod->meta_description[$id_lang] = $meta_description;
                        $update = true;
                        $u_lang = true;
                    }
                }
                if ($u_lang) {
                    Db::getinstance()->insert(
                        'ec_seo_meta_temp',
                        array(
                            'id' => (int)$id_product,
                            'page' => 'product',
                            'id_lang' => (int)$id_lang,
                            'id_shop' => (int)$id_shop,
                        )
                    );
                }
            }

            if ($update) {
                try {
                    $prod->save();
                } catch (Exception $e) {
                    $this->logger->logInfo('Fail save prod '.$id_product.' ' . $e->getMessage() . ' in line ' . $e->getLine() . ' of file ' . $e->getFile());
                }
            }

            if (0 === ($jobLine % 50)) {
                Configuration::updateGlobalValue($this->prefix.'PROGRESS', $jobLine);
            }
        }
        if ($active_backup) {
            if (count($backup) > 0) {
                $backup_old = json_decode(Tools::file_get_contents($backup_file), true);
                foreach ($backup_old as $val) {
                    $backup[] = $val;
                }
                file_put_contents($backup_file, json_encode($backup));
            }
        }
        return true;
    }
}
