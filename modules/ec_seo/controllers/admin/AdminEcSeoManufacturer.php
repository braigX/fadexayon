<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/../../ec_seo.php';

class AdminEcSeoManufacturerController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->id_shop = (int)$this->context->shop->id;
    }

    public function renderForm()
    {
        $mod = new Ec_seo();
        
        $id_manufacturer = Tools::getValue('id_manufacturer');
        $man = new Manufacturer($id_manufacturer, null);
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            if (Tools::strlen($man->meta_title[$id_lang]) == 0) {
                $man->meta_title[$id_lang] = $man->name;
            }
            //$man->h1[$id_lang] = $man->name;
            /* if (Tools::strlen($man->meta_description[$id_lang]) == 0) {
                $man->meta_description[$id_lang] = strip_tags($man->description[$id_lang]);
            } */
        }

        $this->fields_value = (array)$man;
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            $man->description[$id_lang] = $man->description[$id_lang].' '.$man->short_description[$id_lang];
        }
        $this->fields_value['id_manufacturer'] = $id_manufacturer;
        $info_ec_seo_man = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ec_seo_manufacturer WHERE id_manufacturer = '.(int)$id_manufacturer.' AND id_shop = '.(int)$this->id_shop);
        if ($info_ec_seo_man) {
            $info_ec_seo_man_lang = Db::getInstance()->executes('SELECT * FROM '._DB_PREFIX_.'ec_seo_manufacturer_lang WHERE id_seo_manufacturer = '.(int)$info_ec_seo_man['id_seo_manufacturer']);
            foreach ($info_ec_seo_man_lang as $val) {
                $man->keyword[$val['id_lang']] = $val['keyword'];
                //$this->fields_value['h1'][$val['id_lang']] = $val['h1'];
                $this->fields_value['og_title'][$val['id_lang']] = $val['og_title'];
                //$this->fields_value['og_type'][$val['id_lang']] = $val['og_type'];
                $this->fields_value['og_description'][$val['id_lang']] = $val['og_description'];
                $this->fields_value['og_image'][$val['id_lang']] = $val['og_image'];
                //$this->fields_value['link_canonic'][$val['id_lang']] = $val['link_canonic'];
                $man->h1[$val['id_lang']] = $val['h1'];
                $man->link_canonic[$val['id_lang']] = $val['link_canonic'];
            }
        }
       
        
        $meta_desc = $mod->checkMeta($man);

        $href = $this->context->link->getAdminLink('AdminManufacturers', true);

        if (strpos($href, 'sell') !== false) {
            $href = explode('?_token', $href);
            $href = $href[0].'/'.$id_manufacturer.'?_token'.$href[1];
        } else {
            $href.= '&id_manufacturer='.$id_manufacturer.'&updatemanufacturer';
        }
        $og_title_input = array(
            'type' => 'text',
            'label' => $this->l('OG Title'),
            'name' => 'og_title',
            'lang' => true,
        );
        if (Configuration::get('EC_SEO_OG_TITLE_DEFAULT')) {
            $og_title_input['disabled'] = true;
            $og_title_input['desc'] = $this->l('Og title is disabled because you have selected "Use meta title by default" int the parameter of the module.');
        }
        $og_description_input = array(
            'type' => 'text',
            'label' => $this->l('OG Description'),
            'name' => 'og_description',
            'lang' => true,
        );
        if (Configuration::get('EC_SEO_OG_DESCRPTION_DEFAULT')) {
            $og_description_input['disabled'] = true;
            $og_description_input['desc'] = $this->l('Og description is disabled because you have selected "Use meta description by default" int the parameter of the module.');
        }
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Manufacturer'),
                'icon' => 'icon-home'
            ),
            'warning' => '',
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_manufacturer',
                ),
                array(
                    'type' => 'text',
                    'label' =>  $this->l('Meta title'),
                    'name' => 'meta_title',
                    'maxlength' => 255,
                    'maxchar' => 255,
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 100,
                    'hint' =>  $this->l('Forbidden characters:') . ' <>;=#{}',
                    'desc' => $meta_desc['meta_title'],
                ),
                array(
                    'type' => 'textarea',
                    'label' =>  $this->l('Meta description'),
                    'name' => 'meta_description',
                    /* 'maxlength' => 512,
                    'maxchar' => 512, */
                    'lang' => true,
                    'rows' => 5,
                    'cols' => 100,
                    'hint' =>  $this->l('Forbidden characters:') . ' <>;=#{}',
                    'desc' => $meta_desc['meta_description'],
                    
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                    'class' => 'copy2friendlyUrl',
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    //'desc' => $meta_desc['h1'],
                    'disabled' => true,
                ),
              /*   array(
                    'type' => 'text',
                    'label' => $this->l('H1'),
                    'name' => 'h1',
                    'lang' => true,
                    'required' => true,
                    'class' => 'copy2friendlyUrl',
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    'desc' => $meta_desc['h1']
                ), */
                array(
                    'type' => 'textarea',
                    'label' =>  $this->l('Short description'),
                    'name' => 'short_description',
                    'autoload_rte' => true,
                    'lang' => true,
                    'hint' =>  $this->l('Invalid characters:') . ' <>;=#{}',
                ),
                array(
                    'type' => 'textarea',
                    'label' =>  $this->l('Description'),
                    'name' => 'description',
                    'autoload_rte' => true,
                    'lang' => true,
                    'hint' =>  $this->l('Invalid characters:') . ' <>;=#{}',
                    'desc' => $meta_desc['desc_total']
                ),
               /*  array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Only letters, numbers, underscore (_) and the minus (-) character are allowed.'),
                    'desc' => 'Attention, il faut utiliser les changements d’url avec parcimonie.'.$meta_desc['link_rewrite']
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Canonique URL'),
                    'name' => 'link_canonic',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Only letters, numbers, underscore (_) and the minus (-) character are allowed.'),
                    'disabled' => true,
                ), */
                $og_title_input,
                $og_description_input,
                array(
                    'type' => 'text',
                    'label' => $this->l('OG Image'),
                    'name' => 'og_image',
                    'lang' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                array(
                    'href' => $href,
                    'title' => $this->l('Back to manufacturer'),
                    'icon' => 'process-icon-back'
                    )
            ),
        );
        $br = '';
        foreach ($languages as $lang) {
            if (Tools::strlen($man->keyword[$lang['id_lang']]) == 0) {
                if (Tools::strlen($this->fields_form['warning']) > 0) {
                    $br = '<br>';
                }
                $this->fields_form['warning'] .= $br.$this->l('No keywords have been defined! Choose one and then optimize your content!').' ('.Language::getIsoById($lang['id_lang']).')';
            }
        }
        if (Configuration::get('PS_CANONICAL_REDIRECT') != 2) {
            if (Tools::strlen($this->fields_form['warning']) > 0) {
                $br = '<br>';
            }
            $this->fields_form['warning'] .= $br.$this->l('You must change "Redirect to the canonical URL" to 301 in your').' <a target="_blank" href="'.$this->context->link->getAdminLink('AdminMeta', true).'">'.$this->l('shop parameter').'</a>.';
        }
        $this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int) Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
       // if (Shop::isFeatureActive()) {
//            $this->fields_form['input'][] = array(
//                'type' => 'shop',
//                'label' => $this->l('Shop association'),
//                'name' => 'checkBoxShopAsso',
//            );
//        }
        return parent::renderForm();
    }
    
    public function initContent()
    {
        parent::initContent();
        $mod = new Ec_seo();
        $languages = Language::getLanguages(false);
        $content = '';
        $id_manufacturer = Tools::getValue('id_manufacturer');
        $man = new Manufacturer($id_manufacturer, null);
        $id_seo_manufacturer = Db::getInstance()->getValue('SELECT id_seo_manufacturer FROM '._DB_PREFIX_.'ec_seo_manufacturer WHERE id_manufacturer = '.(int)$id_manufacturer.' AND id_shop = '.(int)$this->id_shop);
        if (Tools::getValue('updateKeyWord') == "1") {
            $ec_id_lang = Tools::getValue('ec_id_lang');
            Db::getinstance()->update(
                'ec_seo_manufacturer_lang',
                array(
                    'keyword' => pSQL(Tools::getValue('keyword'))
                ),
                'id_seo_manufacturer = '.(int)$id_seo_manufacturer.' AND id_lang = '.(int)$ec_id_lang.''
            );
            
            echo $this->renderForm();
            exit();
        }

        
        if (!$id_seo_manufacturer) {
            $id_seo_manufacturer = $mod->initSeo('manufacturer', $id_manufacturer);
            $tab_keyword = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                $keyword =  Db::getInstance()->getValue('SELECT keyword FROM '._DB_PREFIX_.'ec_seo_manufacturer_lang WHERE id_seo_manufacturer = '.(int)$id_seo_manufacturer.' AND id_lang = '.(int)$id_lang.'');
                $tab_keyword[$id_lang] = $keyword;
            }
            Media::addJsDef(
                array(
                    'ec_tab_keyword' => $tab_keyword,
                )
            );
        }
        $mod->checkSeoNewLang($man, 'manufacturer', $id_seo_manufacturer);
        if (((bool)Tools::isSubmit('submitAddconfiguration')) == true) {
            $info = Tools::getAllValues();
            $id_manufacturer = $info['id_manufacturer'];
            
            $id_seo_manufacturer = Db::getInstance()->getValue('SELECT id_seo_manufacturer FROM '._DB_PREFIX_.'ec_seo_manufacturer WHERE id_manufacturer = '.(int)$id_manufacturer.' AND id_shop = '.(int)$this->id_shop);
            if (!$id_seo_manufacturer) {
                $id_seo_manufacturer = $mod->initSeo('manufacturer', $id_manufacturer);
            }
            
            
            //$tab_ec_seo = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                //$man->name[$id_lang] = pSQL($info['name_'.$id_lang]);
                $man->short_description[$id_lang] = str_replace('\r\n', '', $info['short_description_'.$id_lang]);
                $man->description[$id_lang] = str_replace('\r\n', '', $info['description_'.$id_lang]);
                $man->meta_title[$id_lang] = $info['meta_title_'.$id_lang];
                $man->meta_description[$id_lang] = $info['meta_description_'.$id_lang];
                //$man->link_rewrite[$id_lang] = pSQL($info['link_rewrite_'.$id_lang], true);
            /*     if (Tools::strlen($info['h1_'.$id_lang]) == 0) {
                    $info['h1_'.$id_lang] = $man->name[$id_lang];
                } */
                $tab_ec_seo = array(
                    'id_seo_manufacturer' => (int)$id_seo_manufacturer,
                    //'h1' => pSQL($info['h1_'.$id_lang], true),
                    //'description2' => pSQL($info['description2_'.$id_lang], true),
                    //'link_canonic' => pSQL($info['link_canonic_'.$id_lang], true),
                    //'og_title' => pSQL($info['og_title_'.$id_lang]),
                    //'og_type' => pSQL($info['og_type_'.$id_lang]),
                    //'og_description' => pSQL($info['og_description_'.$id_lang]),
                    'og_image' => pSQL($info['og_image_'.$id_lang]),
                    'id_lang' => (int)$id_lang,
                );
                if (isset($info['og_title_'.$id_lang])) {
                    $tab_ec_seo['og_title'] = pSQL($info['og_title_'.$id_lang]);
                }
                if (isset($info['og_description_'.$id_lang])) {
                    $tab_ec_seo['og_description'] = pSQL($info['og_description_'.$id_lang]);
                }
                Db::getinstance()->insert(
                    'ec_seo_manufacturer_lang',
                    $tab_ec_seo,
                    false,
                    true,
                    Db::ON_DUPLICATE_KEY
                );
            }
            try {
                $man->save();
                $this->confirmations[] = $this->l('Mise à jour réussi.');
            } catch (Exception $e) {
                $this->errors[] =$e->getMessage();
            }
        }
        //$content = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ec_seo/views/templates/admin/seo_manufacturer.tpl');
        
        $this->context->smarty->assign(array(
        'content' => $content.$this->content.$this->renderForm().$mod->showLogo(),
        ));
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
    }
}
