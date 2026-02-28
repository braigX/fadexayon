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
require_once dirname(__FILE__) . '/../../../../config/config.inc.php';
require_once dirname(__FILE__) . '/../../ec_seo.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Shared\Font;


class Ec_seoGenExcelModuleFrontController extends ModuleFrontController
{
    public $auth = false;

    public $ajax = true;
    protected $ec_token;
    protected $prefix = 'TASK_GEN_EXCEL_';
    protected $cron = 'genExcel';
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
        'loadData',
        'genExcel',
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
            $this->module->followLink($this->base_uri . '&spy=1&nbC=0', $this->logger);
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
        $id_shop = $this->id_shop;
        Db::getinstance()->delete('ec_seo_score', 'id_shop = '.(int)$id_shop);
        Db::getinstance()->delete('ec_seo_score_lang', 'id_shop = '.(int)$id_shop);
        Configuration::updateGlobalValue($this->prefix.'AVANCEMENT', 0);
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

    public function loadData()
    {
        $mod = new Ec_seo();
        $id_shop = $this->id_shop;
        $context = Context::getContext();
        $context->shop->id = (int)$id_shop;
        $start_time = Configuration::getGlobalValue($this->prefix.'START_TIME');
        $start_time = str_replace(' ', '-', $start_time);
        $start_time = str_replace(':', '-', $start_time);
        $temp_file = dirname(__FILE__).'/../../report/temp_'.$start_time.'.json';
        $list = array('category', 'product', 'cms', 'manufacturer', 'supplier', 'other');
        $active_prod = Configuration::get('EC_SEO_REPORT_ACTIVE_PROD');
        $req_active_prod = '';
        if ($active_prod) {
            $req_active_prod = ' AND active = 1';
        }
        $cpt_avancement = 0;
        if ($this->nbCron == 0) {
            Configuration::updateGlobalValue($this->prefix.'TYPE_'.$start_time, 0);
            $c_type = 0;
            $tab_exist = array();
            $tab_final = array();
            $total = 0;
            foreach ($list as $k_type => $type) {
                if ($type == 'other') {
                    $count = Db::getInstance()->getValue('SELECT count(id_meta) FROM '._DB_PREFIX_.'meta WHERE configurable = 1 AND page IN ("best-sales", "manufacturer", "	new-products", "prices-drop","sitemap", "discount", "stores")');
                } else {
                    $req = 'SELECT count(id_'.$type.') FROM '._DB_PREFIX_.$type.'_shop WHERE id_shop = '.(int)$id_shop.' ORDER BY id_'.$type;
                    if ($type == 'product') {
                        $req = 'SELECT count(id_'.$type.') FROM '._DB_PREFIX_.$type.'_shop WHERE id_shop = '.(int)$id_shop.''.$req_active_prod.' ORDER BY id_'.$type;
                    }
                    $count = Db::getInstance()->getValue($req);
                }
                $total += (int)$count;
                Configuration::updateGlobalValue($this->prefix.'TOTAL', $total+1);
            }
        } else {
            $cpt_avancement = Configuration::getGlobalValue($this->prefix.'AVANCEMENT');
            $c_type = Configuration::getGlobalValue($this->prefix.'TYPE_'.$start_time);
            $info_file = json_decode(Tools::file_get_contents($temp_file), true);
            $tab_exist = $info_file['tab_exist'];
            $tab_final = $info_file['tab_final'];
        }
        $languages = Language::getLanguages(false);
        $tab_ec_seo_id = array();
        foreach ($languages as $lang) {
            $tab_ec_seo_id[$lang['id_lang']] = $lang['iso_code'];
        }
        
        //$this->logger->logInfo(__LINE__);
        
        foreach ($list as $k_type => $type) {
            if ($c_type > $k_type) {
                continue;
            }
            $cl = $type;
            if ($type == 'other') {
                $cl = 'meta';
                $type_info = Db::getInstance()->executes('SELECT id_meta FROM '._DB_PREFIX_.'meta WHERE configurable = 1 AND page IN ("best-sales", "manufacturer", "	new-products", "prices-drop","sitemap", "discount", "stores")');
            } else {
                $req = 'SELECT id_'.$type.' FROM '._DB_PREFIX_.$type.'_shop WHERE id_shop = '.(int)$id_shop.' ORDER BY id_'.$type;
                if ($type == 'product') {
                    $req = 'SELECT id_'.$type.' FROM '._DB_PREFIX_.$type.'_shop WHERE id_shop = '.(int)$id_shop.''.$req_active_prod.' ORDER BY id_'.$type;
                }
                $type_info = Db::getInstance()->executes($req);
            }
            $jobLine = 0;
            foreach ($type_info as $ti) {
                $jobLine++;
                if (($this->stopTime < time())) {
                    Configuration::updateGlobalValue($this->prefix.'TYPE_'.$start_time, $k_type);
                    Configuration::updateGlobalValue($this->prefix.'AVANCEMENT', $cpt_avancement);
                    $temp = array(
                        'tab_exist' => $tab_exist,
                        'tab_final' => $tab_final,
                    );
                    file_put_contents($temp_file, json_encode($temp));
                    return $jobLine;
                }
                if (($jobLine < $this->nbCron)) {
                    continue;
                }

                //$this->logger->logInfo(__LINE__.' '.$cl.' '.$ti['id_'.$cl]);
                try {
                    $info = $mod->getInfoMetaByObj($cl, $ti['id_'.$cl], $id_shop);
                } catch (Exception $e) {
                    $this->logger->logInfo($e->getMessage());
                }
                if (!$info) {
                    $this->logger->logInfo($cl.' '.$ti['id_'.$cl].' object invalid ');
                    continue;
                }
                $obj = $info['obj'];
                //$this->logger->logInfo(__LINE__.' '.$obj->id);
                if (!Validate::isLoadedObject($obj) || ($obj->id > 0) == false) {
                    $this->logger->logInfo($cl.' '.$ti['id_'.$cl].' object invalid '.json_encode($obj));
                    continue;
                }
                $link_rewrite = $info['link_rewrite'];
                $keyword = $obj->keyword;
                $score_lang = array();
                foreach ($languages as $lang) {
                    $keyword_lang = explode(' ', $keyword[$lang['id_lang']]);
                    foreach ($keyword_lang as &$mot_ke) {
                        $mot_ke = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $mot_ke));
                    }
                    $keyword[$lang['id_lang']] = $keyword_lang;
                }
                if (get_class($obj) == 'Manufacturer' || get_class($obj) == 'Supplier' || get_class($obj) == 'Meta') {
                    foreach ($keyword as $id_lang => $val) {
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
                        if (!isset($tab_exist[md5($value)]['h1'])) {
                            $tab_exist[md5($value)]['h1'] = 1;
                        } else {
                            $tab_exist[md5($value)]['h1'] += 1;
                        }
                        $tab_final[$type][$obj->id][$id_lang]['h1']['value'] = $value;
                        $len = Tools::strlen($value);
                        if ($len == 0 || $len == 1) {
                            $tab_final[$type][$obj->id][$id_lang]['h1']['missing'] = true;
                            $tab_final[$type][$obj->id][$id_lang]['h1']['error'] = true;
                        }
                        if ($len < $mod->category_rule['h1']['min']) {
                            $score_lang[$id_lang] += 70;
                            $tab_final[$type][$obj->id][$id_lang]['h1']['min'] = true;
                            $tab_final[$type][$obj->id][$id_lang]['h1']['error'] = true;
                        } else if ($len >= $mod->category_rule['h1']['min'] && $len <= $mod->category_rule['h1']['max']) {
                            $score_lang[$id_lang] += 100;
                        } else {
                            if ($len < 201) {
                                $score_lang[$id_lang] += 30;
                            } else {
                                $score_lang[$id_lang] += 10;
                            }
                            $tab_final[$type][$obj->id][$id_lang]['h1']['max'] = true;
                            $tab_final[$type][$obj->id][$id_lang]['h1']['error'] = true;
                        }
                        $cpt = 0;
                        $mots_h1 = explode(' ', $value);
                        foreach ($mots_h1 as &$mot) {
                            $mot = str_replace('.', '', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $mot)));
                        }
                        $mot_needed = array();
                        foreach ($keyword[$id_lang] as $mot_k) {
                            if (in_array($mot_k, $mots_h1)) {
                                $cpt++;
                            } else {
                                $mot_needed[] = $mot_k;
                            }
                        }
                        if ($cpt == count($keyword[$id_lang])) {
                            $score_lang[$id_lang] += 100;
                        } else {
                            if ($cpt == 0) {
                                $score_lang[$id_lang] += 10;
                            } else {
                                $score_lang[$id_lang] += 50;
                            }
                            $tab_final[$type][$obj->id][$id_lang]['h1']['m_keywords'] = $mot_needed;
                            $tab_final[$type][$obj->id][$id_lang]['h1']['error'] = true;
                        }
                    }
                }
            
                //meta title
                foreach ($obj->meta_title as $id_lang => $value) {
                    if (!isset($keyword[$id_lang])) {
                        continue;
                    }
                    if (!isset($tab_exist[md5($value)]['meta_title'])) {
                        $tab_exist[md5($value)]['meta_title'] = 1;
                    } else {
                        $tab_exist[md5($value)]['meta_title'] += 1;
                    }
                    $tab_final[$type][$obj->id][$id_lang]['meta_title']['value'] = $value;
                    $len = Tools::strlen($value);
                    if ($len == 0 || $len == 1) {
                        $tab_final[$type][$obj->id][$id_lang]['meta_title']['missing'] = true;
                        $tab_final[$type][$obj->id][$id_lang]['meta_title']['error'] = true;
                    }
                    if ($len < $mod->category_rule['meta_title']['min']) {
                        $tab_final[$type][$obj->id][$id_lang]['meta_title']['min'] = true;
                        $tab_final[$type][$obj->id][$id_lang]['meta_title']['error'] = true;
                        if ($len < 20) {
                            $score_lang[$id_lang] += 20;
                        } else {
                            $score_lang[$id_lang] += 70;
                        }
                    } else if ($len >= $mod->category_rule['meta_title']['min'] && $len <= $mod->category_rule['meta_title']['max']) {
                        $score_lang[$id_lang] += 100;
                    } else {
                        $tab_final[$type][$obj->id][$id_lang]['meta_title']['max'] = true;
                        $tab_final[$type][$obj->id][$id_lang]['meta_title']['error'] = true;
                        $score_lang[$id_lang] += 20;
                    }
                    $cpt = 0;
                    $mots_meta_title = explode(' ', $value);
                    foreach ($mots_meta_title as &$mot_meta_title) {
                        $mot_meta_title = str_replace('.', '', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $mot_meta_title)));
                    }
                    $mot_needed = array();
                    foreach ($keyword[$id_lang] as $mot) {
                        if (in_array($mot, $mots_meta_title)) {
                            $cpt++;
                        } else {
                            $mot_needed[] = $mot;
                        }
                    }
                    if ($cpt == count($keyword[$id_lang])) {
                        $score_lang[$id_lang] += 100;
                    } else {
                        if ($cpt == 0) {
                            $score_lang[$id_lang] += 10;
                        } else {
                            $score_lang[$id_lang] += 50;
                        }
                        $tab_final[$type][$obj->id][$id_lang]['meta_title']['m_keywords'] = $mot_needed;
                    }
                }
                //meta description
                foreach ($obj->meta_description as $id_lang => $value) {
                    if (!isset($keyword[$id_lang])) {
                        continue;
                    }
                    if (!isset($tab_exist[md5($value)]['meta_description'])) {
                        $tab_exist[md5($value)]['meta_description'] = 1;
                    } else {
                        $tab_exist[md5($value)]['meta_description'] += 1;
                    }
                    $tab_final[$type][$obj->id][$id_lang]['meta_description']['value'] = $value;
                    $len = Tools::strlen($value);
                    if ($len == 0 || $len == 1) {
                        $tab_final[$type][$obj->id][$id_lang]['meta_description']['missing'] = true;
                        $tab_final[$type][$obj->id][$id_lang]['meta_description']['error'] = true;
                    }
                    if ($len < $mod->category_rule['meta_description']['min']) {
                        $tab_final[$type][$obj->id][$id_lang]['meta_description']['min'] = true;
                        $tab_final[$type][$obj->id][$id_lang]['meta_description']['error'] = true;
                        $score_lang[$id_lang] += 30;
                    } else if ($len >= $mod->category_rule['meta_description']['min'] && $len <= $mod->category_rule['meta_description']['max']) {
                        $score_lang[$id_lang] += 100;
                    } else {
                        if ($len < 300) {
                            $score_lang[$id_lang] +=30;
                        } else {
                            $score_lang[$id_lang] +=10;
                        }
                        $tab_final[$type][$obj->id][$id_lang]['meta_description']['max'] = true;
                        $tab_final[$type][$obj->id][$id_lang]['meta_description']['error'] = true;
                    }
                    $cpt = 0;
                    $mots_description = explode(' ', $value);
                    foreach ($mots_description as &$mot) {
                        $mot = str_replace('.', '', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $mot)));
                    }
                    $mot_needed = array();
                    foreach ($keyword[$id_lang] as $mot_d) {
                        if (in_array($mot_d, $mots_description)) {
                            $cpt++;
                        } else {
                            $mot_needed[] = $mot_d;
                        }
                    }

                    if ($cpt == count($keyword[$id_lang])) {
                        $score_lang[$id_lang] += 100;
                    } else {
                        if ($cpt == 0) {
                            $score_lang[$id_lang] += 10;
                        } else {
                            $score_lang[$id_lang] += 50;
                        }
                        $tab_final[$type][$obj->id][$id_lang]['meta_description']['m_keywords'] = $mot_needed;
                        $tab_final[$type][$obj->id][$id_lang]['meta_description']['error'] = true;
                    }
                }
                if (get_class($obj) == 'Manufacturer' || get_class($obj) == 'Supplier') {
                    foreach ($keyword as $id_lang => $val) {
                        $score_lang[$id_lang] += 200;
                    }
                } else {
                    //url
                    foreach ($obj->link_rewrite as $id_lang => $value) {
                        if (!isset($keyword[$id_lang])) {
                            continue;
                        }
                        $len = Tools::strlen($link_rewrite[$id_lang]);
                        if ($len < $mod->category_rule['link_rewrite']['min']) {
                            $score_lang[$id_lang] += 50;
                        } else if ($len >= $mod->category_rule['link_rewrite']['min'] && $len <= $mod->category_rule['link_rewrite']['max']) {
                            $score_lang[$id_lang] += 100;
                        } else {
                            $score_lang[$id_lang] += 70;
                        }
                        $cpt = 0;
                        $link_rewrite[$id_lang] = str_replace('.html', '', $link_rewrite[$id_lang]);
                        $mots_lw = explode('-', $value);
                        $mot_needed = array();
                        foreach ($keyword[$id_lang] as $mot) {
                            $mot = str_replace('.', '', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $mot)));
                            if (in_array($mot, $mots_lw)) {
                                $cpt++;
                            } else {
                                $mot_needed[] = '"'.$mot.'"';
                            }
                        }
                        if ($cpt == count($keyword[$id_lang])) {
                            $score_lang[$id_lang] += 100;
                        } else {
                            $score_lang[$id_lang] += 70;
                        }
                        if (preg_match('/_/i', $link_rewrite[$id_lang])) {
                            $score_lang[$id_lang] -= 10;
                        }
                        if (preg_match('/_/i', $link_rewrite[$id_lang])) {
                            $score_lang[$id_lang] -= 10;
                        }
                        $count_slash = count(explode('/', $link_rewrite[$id_lang]))-1;
                        if ($count_slash > 5) {
                            $score_lang[$id_lang] -= 10;
                        }
                    }
                }
                //Description
                foreach ($obj->description as $id_lang => $value) {
                    if (!isset($keyword[$id_lang])) {
                        continue;
                    }
                    $desc_total = $value;
                    if (preg_match('/<h2>/', $desc_total)) {
                        $score_lang[$id_lang] += 10;
                    }
                
                    $desc_total = strip_tags($desc_total);
                    $search = array("\n", "\r", "\r\n", "\n\r", "\t");
                    $desc_total = str_replace($search, " ", $desc_total);
                    $desc_total = iconv('UTF-8', 'ASCII//TRANSLIT', $desc_total);
                    preg_match_all('/\w+/', $desc_total, $matches);
                    $mots100 = array_slice($matches[0], 0, 100);
                    foreach ($mots100 as &$mot100) {
                        $mot100 = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $mot100));
                    }
                    $count_word = count($matches[0]);
                    if ($count_word > 150 && $count_word < 300) {
                        $score_lang[$id_lang] += 50;
                    }
                    if ($count_word > 300) {
                        $score_lang[$id_lang] += 100;
                    }
                    $mot_needed = array();
                    $cpt = 0;
                    foreach ($keyword[$id_lang] as $mot) {
                        $mot = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $mot));
                        if (in_array($mot, $mots100)) {
                            $cpt++;
                        } else {
                            $mot_needed[] = $mot_k;
                        }
                    }
                    if ($cpt == count($keyword[$id_lang])) {
                        $score_lang[$id_lang] += 10;
                    }
                    $score = (int)(($score_lang[$id_lang]/920)*100);
                    $tab_final[$type][$obj->id][$id_lang]['score'] = $score;
                    Db::getinstance()->insert(
                        'ec_seo_score_lang',
                        array(
                            'id' => (int)$obj->id,
                            'page' => pSQL($cl),
                            'score' => (int)$score,
                            'id_lang' => (int)$id_lang,
                            'id_shop' => (int)$id_shop,
                        ),
                        false,
                        true,
                        Db::ON_DUPLICATE_KEY
                    );
                }
                $score_total = 0;
                $cpt_lang = 0;
                foreach ($keyword as $id_lang => $val) {
                    //if (in_array($id_lang, $pb_lang)) {
                        $tab_final[$type][$obj->id][$id_lang]['error'] = true;
                        $tab_final[$type][$obj->id][$id_lang]['link_rewrite'] = $link_rewrite[$id_lang];
                        $tab_final[$type][$obj->id][$id_lang]['keywords'] = $keyword[$id_lang];
                    //}
                    $score_total += (int)(($score_lang[$id_lang]/920)*100);
                    $cpt_lang++;
                }
                $score_total = (int)($score_total/$cpt_lang);
                Db::getinstance()->insert(
                    'ec_seo_score',
                    array(
                        'id' => (int)$obj->id,
                        'page' => pSQL($cl),
                        'score' => (int)$score_total,
                        'id_shop' => (int)$id_shop,
                    ),
                    false,
                    true,
                    Db::ON_DUPLICATE_KEY
                );
                $cpt_avancement++;
                if (0 === ($cpt_avancement % 50)) {
                    Configuration::updateGlobalValue($this->prefix.'AVANCEMENT', $cpt_avancement);
                }
            }
            $this->nbCron = 0;
        }
        //$this->logger->logInfo(__LINE__);
        Configuration::updateGlobalValue($this->prefix.'AVANCEMENT', $cpt_avancement);
        $temp = array(
            'tab_exist' => $tab_exist,
            'tab_final' => $tab_final,
        );
        file_put_contents($temp_file, json_encode($temp));
        return true;
    }

    public function genExcel()
    {
        //$this->logger->logInfo(__LINE__);
        $id_shop = $this->id_shop;
        $mod = new Ec_seo();
        $only_errors = Configuration::get('EC_SEO_REPORT_ERRORS_ONLY');
        $start_time = Configuration::getGlobalValue($this->prefix.'START_TIME');
        //$this->logger->logInfo(__LINE__);
        $start_time = str_replace(' ', '-', $start_time);
        $start_time = str_replace(':', '-', $start_time);
        Configuration::deleteByName($this->prefix.'TYPE_'.$start_time);
        $temp_file = dirname(__FILE__).'/../../report/temp_'.$start_time.'.json';
        $file_name = $mod->l('seo_report', 'genExcel').'_'.$start_time.'.xlsx';
        //$this->logger->logInfo(__LINE__);
        $info_file = json_decode(Tools::file_get_contents($temp_file), true);
        $tab_exist = $info_file['tab_exist'];
        $tab_final = $info_file['tab_final'];
        unlink($temp_file);
        //$this->logger->logInfo(__LINE__);
        $spreadsheet = new Spreadsheet();
        //$this->logger->logInfo(__LINE__);
        $summary = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $mod->l('Summary', 'genExcel'));
        //$this->logger->logInfo(__LINE__);
        $spreadsheet->addSheet($summary, 0);
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $sheet->setShowGridlines(false);
        $sheet->getStyle('B2:E2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BFBFBF');
        $sheet->getStyle('B2:E2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->setCellValue('B2', $mod->l('Problem', 'genExcel'));
        $sheet->getStyle('B2:E2')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('B2:E2')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('B2')->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('E2')->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->setCellValue('C2', $mod->l('Description', 'genExcel'));
        $sheet->getStyle('C2')->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->setCellValue('D2', $mod->l('Number of errors', 'genExcel'));
        $sheet->getStyle('D2')->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('E2')->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->setCellValue('E2', $mod->l('Action', 'genExcel'));
        //$this->logger->logInfo(__LINE__);
        $sheet->setCellValue('E5', $mod->l('There must be a Title tag for each page on your site.', 'genExcel'));
        $sheet->setCellValue('E6', $mod->l('Title tags must be unique across the site, otherwise your pages will compete!', 'genExcel'));
        $sheet->setCellValue('E7', $mod->l('Some Title tags are too short and therefore not sufficiently optimized, add commercial arguments!', 'genExcel'));
        $sheet->setCellValue('E8', $mod->l('The title tag should ideally not exceed 65 characters, otherwise it may be cut in Google.', 'genExcel'));
        $sheet->setCellValue('E11', $mod->l('There must be a Meta-description tag for each page of your site.', 'genExcel'));
        $sheet->setCellValue('E12', $mod->l('Meta description tags must be unique throughout the site, otherwise your pages will compete!', 'genExcel'));
        $sheet->setCellValue('E13', $mod->l('Some Meta description tags are too short and therefore not sufficiently optimized, add commercial arguments!', 'genExcel'));
        $sheet->setCellValue('E14', $mod->l('The meta-description tag should ideally not exceed 200 characters, otherwise it may be cut in Google.', 'genExcel'));
        $sheet->setCellValue('E17', $mod->l('There must be an h1 tag for each page on your site.', 'genExcel'));
        $sheet->setCellValue('E18', $mod->l('The h1 tags must be unique throughout the site, otherwise your pages will compete!', 'genExcel'));
        $sheet->setCellValue('E19', $mod->l('Some h1 tags are too short and therefore not sufficiently optimized, add commercial arguments!', 'genExcel'));
        $sheet->setCellValue('E20', $mod->l('Some h1 tags are too long. Remember these tags are Titles, not paragraphs, shorten them if possible.', 'genExcel'));
        $sheet->getStyle('D5:D20')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("B2:E2")->getFont()->setBold(true);
        foreach (range('B', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        $tab_pb = array(
            array(
                'type' => 'meta_title',
                'trad' =>$mod->l('Problem meta title', 'genExcel'),
                'column_name' => 'Meta Title',
            ),
            array(
                'type' => 'meta_description',
                'trad' => $mod->l('Problem meta descrtiption', 'genExcel'),
                'column_name' => 'Meta Description',
            ),
            array(
                'type' => 'h1',
                'trad' => $mod->l('Problem h1', 'genExcel'),
                'column_name' => 'h1',
            ),
        );
        //$this->logger->logInfo(__LINE__);
        $mul_grp = 5;
        foreach ($tab_pb as $key => $val) {
            $sheet = $spreadsheet->setActiveSheetIndex(0);
            $sheet->setCellValue('B'.$mul_grp, $val['column_name']);
            $sheet->getStyle('B'.$mul_grp)->getFont()->setBold(true);
            $sheet->getStyle('B'.$mul_grp)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.$mul_grp)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->mergeCells('B'.$mul_grp.':B'.($mul_grp+3));
            $sheet->getStyle('B'.$mul_grp.':E'.$mul_grp)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('B'.($mul_grp+3).':E'.($mul_grp+3))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('B'.$mul_grp.':B'.($mul_grp+3))->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('B'.$mul_grp.':B'.($mul_grp+3))->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('E'.$mul_grp.':E'.($mul_grp+3))->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->setCellValue('C'.$mul_grp, $mod->l('Missing', 'genExcel'));
            $sheet->setCellValue('C'.($mul_grp+1), $mod->l('Duplicate', 'genExcel'));
            $sheet->setCellValue('C'.($mul_grp+2), $mod->l('Too short', 'genExcel'));
            $sheet->setCellValue('C'.($mul_grp+3), $mod->l('Too long', 'genExcel'));
            $sheet->getStyle('C'.$mul_grp)->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('C'.($mul_grp+1))->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('C'.($mul_grp+2))->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('C'.($mul_grp+3))->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('D'.$mul_grp)->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('D'.($mul_grp+1))->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('D'.($mul_grp+2))->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('D'.($mul_grp+3))->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('C'.($mul_grp).':E'.($mul_grp))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('C'.($mul_grp+1).':E'.($mul_grp+1))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('C'.($mul_grp+2).':E'.($mul_grp+2))->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('B'.$mul_grp.':E'.($mul_grp+3))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4F81BD');
            $sheet->getStyle('B'.$mul_grp.':E'.($mul_grp+3))->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
            $sheet->getStyle('A45')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
            $mul_grp += 6;

            $n_sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $val['trad']);
            $spreadsheet->addSheet($n_sheet, $key+1);
            $sheet = $spreadsheet->setActiveSheetIndex($key+1);
            $sheet->freezePane('E2');
            $sheet->setCellValue('A1', $mod->l('Type', 'genExcel'));
            $sheet->setCellValue('B1', $mod->l('ID', 'genExcel'));
            $sheet->setCellValue('C1', $mod->l('Language', 'genExcel'));
            $sheet->setCellValue('D1', $mod->l('Url', 'genExcel'));
            $sheet->setCellValue('E1', $mod->l('Keyword', 'genExcel'));
            $sheet->setCellValue('F1', $val['column_name']);
            if ($val['column_name'] == 'Meta Title') {
                $sheet->setCellValue('G1', $mod->l('Missing Meta Title', 'genExcel'));
                $sheet->setCellValue('H1', $mod->l('Duplicate Meta Title', 'genExcel'));
            } else if ($val['column_name'] == 'Meta Description') {
                $sheet->setCellValue('G1', $mod->l('Missing Meta Description', 'genExcel'));
                $sheet->setCellValue('H1', $mod->l('Duplicate Meta Description', 'genExcel'));
            } else {
                $sheet->setCellValue('G1', $mod->l('Missing h1', 'genExcel'));
                $sheet->setCellValue('H1', $mod->l('Duplicate h1', 'genExcel'));
            }
            $sheet->setCellValue('I1', $val['column_name'].' '.$mod->l('too short', 'genExcel'));
            $sheet->setCellValue('J1', $val['column_name'].' '.$mod->l('too long', 'genExcel'));
            $sheet->setCellValue('K1', $mod->l('Missing keyword', 'genExcel'));
            $sheet->setCellValue('L1', $mod->l('Score', 'genExcel'));
            foreach (range('A', 'C') as $columnID) {
                $sheet->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }
            foreach (range('E', 'L') as $columnID) {
                $sheet->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }
            $sheet->getStyle("A1:L1")->getFont()->setBold(true);
        }
        //$this->logger->logInfo(__LINE__);
        $total_cpt = array();
        /* $tab_pb = array(
            array(
                'type' => 'meta_title',
                'trad' =>$mod->l('Problem meta title', 'genExcel'),
                'column_name' => 'Meta Title',
            ),
            array(
                'type' => 'meta_description',
                'trad' => $mod->l('Problem meta descrtiption', 'genExcel'),
                'column_name' => 'Meta Description',
            ),
            array(
                'type' => 'h1',
                'trad' => $mod->l('Problem h1', 'genExcel'),
                'column_name' => 'h1',
            ),
        ); */
        $info_total = array();
        $languages = Language::getLanguages(false);
        $tab_ec_seo_id = array();
        foreach ($languages as $lang) {
            $tab_ec_seo_id[$lang['id_lang']] = $lang['iso_code'];
        }
        //$this->logger->logInfo(__LINE__);
        foreach ($tab_pb as $key => $val) {
            $line = 2;
            $type_meta = $val['type'];
            $total_cpt[$type_meta]['missing'] = 0;
            $total_cpt[$type_meta]['duplicate'] = 0;
            $total_cpt[$type_meta]['min'] = 0;
            $total_cpt[$type_meta]['max'] = 0;
            $sheet = $spreadsheet->setActiveSheetIndex($key+1);
            foreach ($tab_final as $type => $info_type) {
                foreach ($tab_pb as $key => $val) {
                    if (!isset($info_total[$type][$val['type']]['total'])) {
                        $info_total[$type][$val['type']]['total'] = 0;
                    }
                    if (!isset($info_total[$type][$val['type']]['missing'])) {
                        $info_total[$type][$val['type']]['missing'] = 0;
                    }
                    if (!isset($info_total[$type][$val['type']]['duplicate'])) {
                        $info_total[$type][$val['type']]['duplicate'] = 0;
                    }
                    if (!isset($info_total[$type][$val['type']]['too_short'])) {
                        $info_total[$type][$val['type']]['too_short'] = 0;
                    }
                    if (!isset($info_total[$type][$val['type']]['too_long'])) {
                        $info_total[$type][$val['type']]['too_long'] = 0;
                    }
                }
                
                //$this->logger->logInfo(__LINE__);
                
                foreach ($info_type as $id => $info_by_l) {
                    foreach ($info_by_l as $id_lang => $info) {
                        if (!isset($info[$type_meta])) {
                            continue;
                        }
                        //$this->logger->logInfo(__LINE__);
                        $info_total[$type][$type_meta]['total'] +=1 ;
                        if ($tab_exist[md5($info[$type_meta]['value'])][$type_meta] > 1) {
                            $info[$type_meta]['error'] = true;
                        }
                        //$this->logger->logInfo(__LINE__);
                        if (isset($info[$type_meta]['error']) || !$only_errors) {
                            $sheet->setCellValue('A'.$line, $type);
                            //$this->logger->logInfo(__LINE__);
                            //$sheet->setCellValue('B'.$line, $id);
                            $sheet->setCellValue('C'.$line, $tab_ec_seo_id[$id_lang]);
                            $sheet->setCellValue('D'.$line, $info['link_rewrite']);
                            $sheet->setCellValue('E'.$line, implode(' ', $info['keywords']));
                            $sheet->setCellValue('F'.$line, $info[$type_meta]['value']);
                            if (isset($info[$type_meta]['missing'])) {
                                $sheet->setCellValue('G'.$line, 'x');
                                $total_cpt[$type_meta]['missing'] += 1;
                                $info_total[$type][$type_meta]['missing'] += 1;
                            }
                            if ($tab_exist[md5($info[$type_meta]['value'])][$type_meta] > 1 && !isset($info[$type_meta]['missing'])) {
                                $sheet->setCellValue('H'.$line, 'x');
                                $total_cpt[$type_meta]['duplicate'] += 1;
                                $info_total[$type][$type_meta]['duplicate'] += 1;
                            }
                            if (isset($info[$type_meta]['min'])) {
                                $sheet->setCellValue('I'.$line, 'x');
                                $total_cpt[$type_meta]['min'] += 1;
                                $info_total[$type][$type_meta]['too_short'] += 1;
                            }
                            if (isset($info[$type_meta]['max'])) {
                                $sheet->setCellValue('J'.$line, 'x');
                                $total_cpt[$type_meta]['max'] += 1;
                                $info_total[$type][$type_meta]['too_long'] += 1;
                            }
                            if (isset($info[$type_meta]['m_keywords'])) {
                                $sheet->setCellValue('K'.$line, implode(', ', $info[$type_meta]['m_keywords']));
                            }
                            //$this->logger->logInfo(__LINE__);
                            $sheet->setCellValue('L'.$line, $info['score'].'%');
                            $this->setLineColor($sheet, $line, $info['score']);
                            //$this->logger->logInfo(__LINE__);
                            $line++;
                        }
                    }
                }
            }
        }
        //$this->logger->logInfo(__LINE__);
        $date = date('Y-m-d H:i:s');
        $insert = array();
        foreach ($info_total as $page => $info) {
            foreach ($tab_pb as $key => $val) {
                $type_meta = $val['type'];
                $data = array(
                    'type_meta' => pSQl($type_meta),
                    'page'=> pSQL($page),
                    'missing' => (int)$info[$type_meta]['missing'],
                    'duplicate' => (int)$info[$type_meta]['duplicate'],
                    'too_short' => (int)$info[$type_meta]['too_short'],
                    'too_long' => (int)$info[$type_meta]['too_long'],
                    'total' => (int)$info[$type_meta]['total'],
                    'date'=> pSQL($date),
                    'id_shop' => (int)$id_shop
                );
                $insert[] = $data;
            }
        }
        if (count($insert) > 0) {
            Db::getinstance()->delete('ec_seo_report', 'id_shop = '.(int)$id_shop);
            try {
                Db::getinstance()->insert(
                    'ec_seo_report',
                    $insert
                );
            } catch (Exception $e) {
                $this->logger->logInfo($e->getMessage());
            }
        }
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $sheet->setCellValue('D5', $total_cpt['meta_title']['missing']);
        $sheet->setCellValue('D6', $total_cpt['meta_title']['duplicate']);
        $sheet->setCellValue('D7', $total_cpt['meta_title']['min']);
        $sheet->setCellValue('D8', $total_cpt['meta_title']['max']);

        $sheet->setCellValue('D11', $total_cpt['meta_description']['missing']);
        $sheet->setCellValue('D12', $total_cpt['meta_description']['duplicate']);
        $sheet->setCellValue('D13', $total_cpt['meta_description']['min']);
        $sheet->setCellValue('D14', $total_cpt['meta_description']['max']);

        $sheet->setCellValue('D17', $total_cpt['h1']['missing']);
        $sheet->setCellValue('D18', $total_cpt['h1']['duplicate']);
        $sheet->setCellValue('D19', $total_cpt['h1']['min']);
        $sheet->setCellValue('D20', $total_cpt['h1']['max']);
        //$this->logger->logInfo(__LINE__);
        $spreadsheet->removeSheetByIndex(4);
        $writer = new Xlsx($spreadsheet);
        $writer->save(_PS_MODULE_DIR_.'/'.$this->module->name.'/report/'.$file_name);
        //$this->logger->logInfo(__LINE__);
        $cpt_avancement = Configuration::getGlobalValue($this->prefix.'AVANCEMENT');
        Configuration::updateGlobalValue($this->prefix.'AVANCEMENT', $cpt_avancement+1);
        //$this->logger->logInfo(__LINE__);
        return true;
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
}
