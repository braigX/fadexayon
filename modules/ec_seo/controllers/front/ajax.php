<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/../../ec_seo.php';
require_once dirname(__FILE__) . '/../../classes/Robots.php';
class Ec_seoAjaxModuleFrontController extends ModuleFrontController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->module = new Ec_seo();
        $this->ajax = true;
    }

    public function displayAjax()
    {
        if (Tools::getValue('tok') != Configuration::getGlobalValue('EC_TOKEN_SEO')) {
            Tools::redirect('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            Tools::redirect('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            Tools::redirect('Cache-Control: no-store, no-cache, must-revalidate');
            Tools::redirect('Cache-Control: post-check=0, pre-check=0', false);
            Tools::redirect('Pragma: no-cache');
            Tools::redirect('Location: ../');
            exit;
        }
        
        
        switch ((int) Tools::getValue('majsel')) {
            case 1:
                echo Configuration::updateValue('EC_SEO_PAGINATION', Tools::getValue('pagination'));
                break;
            case 2:
                $type = Tools::getValue('type');
                if ($type == 'other') {
                    echo $this->module->uploadTabDisplay(Tools::getValue('id_shop'), Tools::getValue('page'), true, Tools::getValue('search'));
                } else {
                    echo $this->module->tab($type, Tools::getValue('id_shop'), Tools::getValue('page'), true, Tools::getValue('search'));
                }
                break;
            case 3:
                $type = Tools::getValue('type');
                if ($type == 'other') {
                    echo $this->module->uploadTabDisplay(Tools::getValue('id_shop'), null, true, Tools::getValue('search'));
                } else {
                    echo $this->module->tab($type, Tools::getValue('id_shop'), null, true, Tools::getValue('search'));
                }
                break;
            case 4:
                echo $this->module->getInfoRefresh(Tools::getValue('prefix'));
                break;
            case 5:
                echo $this->module->refreshBackUp();
                break;
            case 6:
                echo unlink(dirname(__FILE__).'/../../'.Tools::getValue('file'));
                break;
            case 7:
                echo $this->module->getPreview(Tools::getValue('class'), Tools::getValue('id'), Tools::getValue('meta_title'), Tools::getValue('meta_description'), Tools::getValue('id_lang'), Tools::getValue('id_shop'));
                break;
            case 8:
                echo $this->module->refreshReport();
                break;
            case 9:
                echo $this->module->getInfoRefreshReport(Tools::getValue('prefix'));
                break;
            case 10:
                $content = Tools::getValue('robot');
                echo file_put_contents(_PS_ROOT_DIR_.'/robots.txt', $content);
                break;
            case 11:
                echo $this->module->getMiTableTask(Tools::getValue('type'), Tools::getValue('id_shop'), true, Tools::getValue('search'), Tools::getValue('page'));
                break;
            case 12:
                echo Configuration::updateValue('EC_SEO_MI_'.Tools::getValue('type').'PAGINATION', Tools::getValue('pagination'));
                break;
            case 13:
                echo $this->module->getMetaTableTask(Tools::getValue('type'), Tools::getValue('id_shop'), true, Tools::getValue('search'), Tools::getValue('page'));
                break;
            case 14:
                echo Configuration::updateValue('EC_SEO_META_'.Tools::getValue('type').'PAGINATION', Tools::getValue('pagination'));
                break;
            case 15:
                $base = Tools::getHttpHost(true).__PS_BASE_URI__;
                $url = '/'.str_replace($base, '', Tools::getValue('url'));
                $rob = new Robots($base, Tools::getValue('domain_id_shop'));
                echo $rob->isOkToCrawl($url);
                break;
            case 16:
                echo $this->module->getBaTableTask(Tools::getValue('id_shop'), true, Tools::getValue('search'), Tools::getValue('page'));
                break;
            case 17:
                echo Configuration::updateValue('EC_SEO_BA_PAGINATION', Tools::getValue('pagination'));
                break;
            case 18:
                echo Configuration::updateGlobalValue('EC_SEO_ONGLET_'.Tools::getValue('id_employee'), Tools::strtolower(Tools::getValue('target')));
                break;
            case 19:
                echo $this->module->getReportProgress();
                break;
            case 20:
                echo $this->module->updatePositionBlock(Tools::getValue('position_block'));
                break;
            case 21:
                echo $this->module->updatePositionBlockLink(Tools::getValue('position_block'));
                break;
            case 22:
                echo $this->module->refreshKeywordData(Tools::getValue('id'), Tools::getValue('page'), Tools::getValue('page_infos'), true);
                break;
            case 23:
                if (Tools::getValue('statut') == 0) {
                    $stat = 1;
                } else {
                    $stat = 0;
                }
                if ((int)Tools::getValue('onglet') == 6) {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo_redirect SET onlineS='.(int)$stat.' 
                    WHERE id='.(int)Tools::getValue('id'));
                } else {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET onlineS='.(int)$stat.' 
                    WHERE id='.(int)Tools::getValue('id'));
                }
                break;
            case 24:
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET activRed=0 WHERE id='.(int)Tools::getValue('id'));
                break;
            case 25:
                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET activRed=1 WHERE id='.(int)Tools::getValue('id'));
                break;
            case 26:
                $class_alrt = Db::getInstance()->getRow('SELECT lienS,onlineS,activRed,typeRed FROM '._DB_PREFIX_.'ec_seo WHERE id='.(int)Tools::getValue('id'));
                if ($class_alrt['activRed'] == 0) {
                    echo 'grey';
                } elseif (($class_alrt['typeRed'] == 'homepage' || $class_alrt['typeRed'] == 'categorydefault') && $class_alrt['onlineS'] == 1) {
                    echo 'white';
                } elseif ($class_alrt['lienS'] == '' && $class_alrt['onlineS'] == 1) {
                    echo '#FF4E40';
                } elseif ($class_alrt['lienS'] == '' && $class_alrt['onlineS'] == 0) {
                    echo '#FFDC40';
                } elseif ($class_alrt['lienS'] != '' && $class_alrt['onlineS'] == 0) {
                    echo '#38E05D';
                } else {
                    echo 'white';
                }
                break;
            case 27:
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ec_seo_redirect WHERE id='.(int)Tools::getValue('id'));
                break;
            case 28:
                if (Tools::getValue('onglet') == 6) {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo_redirect SET lienS="'.pSQL(Tools::getValue('lien')).'" WHERE id='.(int)Tools::getValue('id'));
                } else {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET lienS="'.pSQL(Tools::getValue('lien')).'" WHERE id='.(int)Tools::getValue('id'));
                }
                break;
            case 29:
                if (Tools::getValue('onglet') == 6) {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo_redirect SET typeRed="'.pSQL(Tools::getValue('type')).'" WHERE id='.(int)Tools::getValue('id'));
                } else {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET typeRed="'.pSQL(Tools::getValue('type')).'" WHERE id='.(int)Tools::getValue('id'));
                }
                break;
            case 30:
                $existseo = Db::getInstance()->getValue('SELECT id FROM '._DB_PREFIX_.'ec_seo 
                WHERE idP='.(int)Tools::getValue('id').' AND idL='.(int)Tools::getValue('lang').' AND type="'.pSQL(Tools::getValue('type')).'"');
                
                $existbase = Db::getInstance()->getValue('SELECT id_'.pSQL(Tools::strtolower(Tools::getValue('type'))).' FROM '._DB_PREFIX_.pSQL(Tools::strtolower(Tools::getValue('type'))).' 
                WHERE id_'.pSQL(Tools::strtolower(Tools::getValue('type'))).'='.(int)Tools::getValue('id'));
                
                if ((!isset($existseo) || $existseo == '') && (!isset($existbase) || $existbase == '')) {
                    Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo 
                    VALUES("","'.pSQL(Tools::getValue('type')).'", '.(int)Tools::getValue('id').', '.(int)Tools::getValue('lang').', 
                    "'.pSQL(Tools::getValue('lien')).'", 1, "'.pSQL(Tools::getValue('name')).'", "'.pSQL(Tools::getValue('redi')).'",1,'.(int)Tools::getValue('shop').')');
                }
                break;
            case 31:
                $old_url = Tools::getValue('old_url');
                $new_url = Tools::getValue('new_url');
                $exist_url = Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'ec_seo_redirect 
                WHERE old_link="'.pSQL($old_url).'" AND id_shop='.(int)Tools::getValue('shop'));
                
                if ($exist_url > 0) {
                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'ec_seo_redirect 
                    SET typeRed = "'.pSQL(Tools::getValue('redi')).'", lienS = "'.pSQL($new_url).'", onlineS=1 WHERE old_link="'.pSQL($old_url).'" AND id_shop='.(int)Tools::getValue('shop'));
                } else {
                    Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'ec_seo_redirect 
                    VALUES("","'.pSQL($old_url).'", "'.pSQL($new_url).'", "'.pSQL(Tools::getValue('redi')).'",1,'.(int)Tools::getValue('shop').')');
                }
                break;
            case 32:
                $tab = Tools::getValue('tab');
                if (count($tab) > 0) {
                    if (Tools::getValue('statut') == 0) {
                        $stat = 1;
                    } else {
                        $stat = 0;
                    }
                    if (Tools::getValue('onglet') == 6) {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo_redirect SET onlineS='.(int)$stat.' WHERE id IN ('.pSQL(implode(',', $tab)).')');
                    } else {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET onlineS='.(int)$stat.' WHERE id IN ('.pSQL(implode(',', $tab)).')');
                    }
                }
                break;
            case 33:
                $tab = Tools::getValue('tab');
                if (count($tab) > 0) {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET activRed=0
                        WHERE id IN ('.pSQL(implode(',', $tab)).')');
                }
                break;
            case 34:
                $tab = Tools::getValue('tab');
                if (count($tab) > 0) {
                    Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ec_seo_redirect
                        WHERE id IN ('.pSQL(implode(',', $tab)).')');
                }
                break;
            case 35:
                $tab = Tools::getValue('tab');
                if (count($tab) > 0) {
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET activRed=1
                        WHERE id IN ('.pSQL(implode(',', $tab)).')');
                }
                break;
            case 36:
                $i = Tools::getValue('i');
                $tab = Tools::getValue('tab');
                if (count($tab) > 0) {
                    if (Tools::getValue('onglet') == 6) {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo_redirect SET lienS="'.pSQL(Tools::getValue('lien')).'"
                            WHERE id IN ('.pSQL(implode(',', $tab)).')');
                    } else {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET lienS="'.pSQL(Tools::getValue('lien')).'"
                            WHERE id IN ('.pSQL(implode(',', $tab)).')');
                    }
                }
                break;
            case 37:
                $i = Tools::getValue('i');
                $tab = Tools::getValue('tab');
                if (count($tab) > 0) {
                    if (Tools::getValue('onglet') == 6) {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo_redirect SET typeRed="'.pSQL(Tools::getValue('type')).'"
                        WHERE id IN ('.pSQL(implode(',', $tab)).')');
                    } else {
                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ec_seo SET typeRed="'.pSQL(Tools::getValue('type')).'"
                        WHERE id IN ('.pSQL(implode(',', $tab)).')');
                    }
                }
                break;
            case 38:
                $exist = Db::getInstance()->getValue('SELECT value FROM '._DB_PREFIX_.'ec_seo_conf WHERE name="'.pSQL(Tools::getValue('type')).'"');
                if ($exist) {
                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ec_seo_conf` SET value="'.pSQL(Tools::getValue('value')).'" 
                WHERE name="'.pSQL(Tools::getValue('type')).'"');
                } else {
                    echo Db::getinstance()->insert('ec_seo_conf', array('value'=>pSQL(Tools::getValue('value')), 'name' =>pSQL(Tools::getValue('type'))));
                }
                break;
            case 39:
                $tab = array('Product','Category','Supplier','Manufacturer','Cms');
                foreach ($tab as $tab_obj) {
                    $this->module->listO($tab_obj, (int)Tools::getValue('id_shop'));
                }
                break;
            case 40:
                $url = Tools::getHttpHost(true).Tools::getValue('url');
                $exist = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ec_seo_redirectimage WHERE url = "'.pSQL($url).'"');
                if ($exist['id'] > 0) {
                    Db::getinstance()->update(
                        'ec_seo_redirectimage',
                        array(
                            'cpt' => (int)$exist['cpt']+1
                        ),
                        'id = '.(int)$exist['id']
                    );
                    if ($exist['default']) {
                        $file = _PS_MODULE_DIR_.'ec_seo/views/img/'.Configuration::get('EC_SEO_REDIRECTIMAGE_NAME');
                        $filename = basename($file);
                        $file_extension = Tools::strtolower(Tools::substr(strrchr($filename, "."), 1));
                        switch ($file_extension) {
                            case "gif":
                                $ctype="image/gif";
                                break;
                            case "png":
                                $ctype="image/png";
                                break;
                            case "jpeg":
                            case "jpg":
                                $ctype="image/jpeg";
                                break;
                            default:
                        }
                    } else {
                        $file = _PS_MODULE_DIR_.'ec_seo/views/img/'.$exist['img_redirect'];
                        $filename = basename($file);
                        $file_extension = Tools::strtolower(Tools::substr(strrchr($filename, "."), 1));
                        switch ($file_extension) {
                            case "gif":
                                $ctype="image/gif";
                                break;
                            case "png":
                                $ctype="image/png";
                                break;
                            case "jpeg":
                            case "jpg":
                                $ctype="image/jpeg";
                                break;
                            default:
                        }
                    }
                } else {
                    Db::getinstance()->insert(
                        'ec_seo_redirectimage',
                        array(
                            'url' => pSQL($url),
                            'cpt' => 1,
                            'default'  => 1
                        )
                    );
                    $file = _PS_MODULE_DIR_.'ec_seo/views/img/'.Configuration::get('EC_SEO_REDIRECTIMAGE_NAME');
                    $filename = basename($file);
                    $file_extension = Tools::strtolower(Tools::substr(strrchr($filename, "."), 1));
                    switch ($file_extension) {
                        case "gif":
                            $ctype="image/gif";
                            break;
                        case "png":
                            $ctype="image/png";
                            break;
                        case "jpeg":
                        case "jpg":
                            $ctype="image/jpeg";
                            break;
                        default:
                    }
                }

                header('Content-type: ' . $ctype);
                readfile($file);
                break;
            case 41:
                Db::getinstance()->update(
                    'cms',
                    array(
                        'indexation' => 0
                    ),
                    'id_cms = '.(int)Tools::getValue('id_cms')
                );
                break;
            case 42:
                Db::getinstance()->update(
                    'cms',
                    array(
                        'indexation' => 1
                    ),
                    'id_cms = '.(int)Tools::getValue('id_cms')
                );
                break;
            default:
                break;
        }
    }
}
