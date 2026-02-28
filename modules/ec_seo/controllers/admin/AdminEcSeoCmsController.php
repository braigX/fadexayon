<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/../../ec_seo.php';
//require_once dirmeta_title(__FILE__) . '/../../class/cmsReport.php';

class AdminEcSeoCmsController extends ModuleAdminController
{
    /* protected $position_identifier = 'id_ec_cms_report_cms'; */
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->id_shop = (int)$this->context->shop->id;
    }

    public function renderForm()
    {
        $mod = new Ec_seo();
        
        $id_cms = Tools::getValue('id_cms');
        $cms = new CMS($id_cms, null, $this->id_shop);
        $info_ec_seo = array();
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            if (Tools::strlen($cms->head_seo_title[$id_lang]) == 0) {
                $cms->head_seo_title[$id_lang] = $cms->meta_title[$id_lang];
            }
        }
        $cms->h1 = $cms->meta_title;
        $this->fields_value = (array)$cms;
        $this->fields_value['id_cms'] = $id_cms;
        $info_ec_seo_cms = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ec_seo_cms WHERE id_cms = '.(int)$id_cms.' AND id_shop = '.(int)$this->id_shop);
        if ($info_ec_seo_cms) {
            //$this->fields_value['use_h1'] = $info_ec_seo_cms['use_h1'];
            $info_ec_seo_cms_lang = Db::getInstance()->executes('SELECT * FROM '._DB_PREFIX_.'ec_seo_cms_lang WHERE id_seo_cms = '.(int)$info_ec_seo_cms['id_seo_cms']);
            foreach ($info_ec_seo_cms_lang as $val) {
                $cms->keyword[$val['id_lang']] = $val['keyword'];
                //$this->fields_value['h1'][$val['id_lang']] = $val['h1'];
                //$this->fields_value['description2'][$val['id_lang']] = $val['description2'];
                $this->fields_value['og_title'][$val['id_lang']] = $val['og_title'];
                //$this->fields_value['og_type'][$val['id_lang']] = $val['og_type'];
                $this->fields_value['og_description'][$val['id_lang']] = $val['og_description'];
                $this->fields_value['og_image'][$val['id_lang']] = $val['og_image'];
                $this->fields_value['link_canonic'][$val['id_lang']] = $val['link_canonic'];
                //$cms->h1[$val['id_lang']] = $val['h1'];
                //$cms->description2[$val['id_lang']] = $val['description2'];
                //$cms->link_canonic[$val['id_lang']] = $val['link_canonic'];
            }
        }
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
           /*  if (Tools::strlen($cms->meta_title[$id_lang]) == 0) {
                $cms->meta_title[$id_lang] = $cms->name[$id_lang];
            }
            if (Tools::strlen($cms->meta_description[$id_lang]) == 0) {
                $cms->meta_description[$id_lang] = strip_tags($cms->description[$id_lang]);
            } */
            $cms->description[$id_lang] = $cms->content[$id_lang];
        }
        
        $meta_desc = $mod->checkMeta($cms);

        $href = $this->context->link->getAdminLink('AdminCmsContent', true);

        if (strpos($href, 'sell') !== false) {
            $href = explode('?_token', $href);
            $href = $href[0].'/'.$id_cms.'/edit?_token'.$href[1];
        } else {
            $href.= '&id_cms='.$id_cms.'&updatecms';
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
                'title' => $this->l('cms'),
                'icon' => 'icon-home'
            ),
            'warning' => '',
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_cms',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                array(
                    'href' => $href,
                    'title' => $this->l('Back to cms'),
                    'icon' => 'process-icon-back'
                    )
            ),
        );
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Meta title'),
                'name' => 'head_seo_title',
                'lang' => true,
                'hint' => array(
                    $this->l('Used to override the title tag value. If left blank, the default title value is used.'),
                    $this->l('Invalid characters:') . ' &lt;&gt;;=#{}',
                ),
                'desc' => $meta_desc['meta_title'],
            );
        } else {
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' =>  $this->l('Meta title'),
                'name' => 'meta_title',
                'maxlength' => 255,
                'maxchar' => 255,
                'lang' => true,
                'rows' => 5,
                'cols' => 100,
                'hint' =>  $this->l('Forbidden characters:') . ' <>;=#{}',
                'placeholder' => $this->l('To have a different title from the cms name, enter it here.'),
                'desc' => $meta_desc['meta_title']
            );
        }
        

        $this->fields_form['input'][] = array(
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
            'placeholder' => $this->l('To have a different description than your cms summary in search results page, write it here.')
            
        );
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' =>  $this->l('Title/H1'),
                'name' => 'meta_title',
                'maxlength' => 255,
                'maxchar' => 255,
                'lang' => true,
                'rows' => 5,
                'cols' => 100,
                'hint' =>  $this->l('Forbidden characters:') . ' <>;=#{}',
                'placeholder' => $this->l('To have a different title from the cms name, enter it here.'),
                'disabled' => true,
                'desc' => $meta_desc['h1']
            );
        }
        

        $this->fields_form['input'][] = array(
            'type' => 'textarea',
            'label' => $this->l('Page content'),
            'name' => 'content',
            'autoload_rte' => true,
            'lang' => true,
            'rows' => 5,
            'cols' => 40,
            'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
            'desc' => $meta_desc['desc_total']
        );

        $this->fields_form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Friendly URL'),
            'name' => 'link_rewrite',
            'lang' => true,
            'required' => true,
            'hint' => $this->l('Only letters, numbers, underscore (_) and the minus (-) character are allowed.'),
            'desc' => 'Attention, il faut utiliser les changements d’url avec parcimonie.'.$meta_desc['link_rewrite']
        );
        $this->fields_form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('Canonique URL'),
            'name' => 'link_canonic',
            'lang' => true,
            'required' => true,
            'hint' => $this->l('Only letters, numbers, underscore (_) and the minus (-) character are allowed.'),
            'disabled' => true,
        );
        $this->fields_form['input'][] = array(
            'type' => 'switch',
            'label' => $this->l('Indexation by search engines'),
            'name' => 'indexation',
            'required' => false,
            'class' => 't',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'indexation_on',
                    'value' => 1,
                    'label' => $this->l('Enabled'),
                ),
                array(
                    'id' => 'indexation_off',
                    'value' => 0,
                    'label' => $this->l('Disabled'),
                ),
            ),
        );
        $this->fields_form['input'][] = $og_title_input;
        $this->fields_form['input'][] = $og_description_input;
        $this->fields_form['input'][] = array(
            'type' => 'text',
            'label' => $this->l('OG Image'),
            'name' => 'og_image',
            'lang' => true,
        );
        $br = '';
        foreach ($languages as $lang) {
            if (Tools::strlen($cms->keyword[$lang['id_lang']]) == 0) {
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
        return parent::renderForm();
    }
    
    public function initContent()
    {
        parent::initContent();
        $mod = new Ec_seo();
        $languages = Language::getLanguages(false);
        $content = '';
        $id_cms = Tools::getValue('id_cms');
        $cms = new cms($id_cms, null, $this->id_shop);
        $id_seo_cms = Db::getInstance()->getValue('SELECT id_seo_cms FROM '._DB_PREFIX_.'ec_seo_cms WHERE id_cms = '.(int)$id_cms.' AND id_shop = '.(int)$this->id_shop);
        if (Tools::getValue('updateKeyWord') == "1") {
            $ec_id_lang = Tools::getValue('ec_id_lang');
            Db::getinstance()->update(
                'ec_seo_cms_lang',
                array(
                    'keyword' => pSQL(Tools::getValue('keyword'))
                ),
                'id_seo_cms = '.(int)$id_seo_cms.' AND id_lang = '.(int)$ec_id_lang.''
            );
            
            echo $this->renderForm();
            exit();
        }

        
        if (!$id_seo_cms) {
            $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
            $id_seo_cms = $mod->initSeo('cms', $id_cms);
            $tab_keyword = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                $keyword =  Db::getInstance()->getValue('SELECT keyword FROM '._DB_PREFIX_.'ec_seo_cms_lang WHERE id_seo_cms = '.(int)$id_seo_cms.' AND id_lang = '.(int)$id_lang.'');
                $tab_keyword[$id_lang] = $keyword;
            }
            Media::addJsDef(
                array(
                    'ec_tab_keyword' => $tab_keyword,
                )
            );
        }
        $mod->checkSeoNewLang($cms, 'cms', $id_seo_cms);
        if (((bool)Tools::isSubmit('submitAddconfiguration')) == true) {
            $info = Tools::getAllValues();
            $id_cms = $info['id_cms'];
            
            $id_seo_cms = Db::getInstance()->getValue('SELECT id_seo_cms FROM '._DB_PREFIX_.'ec_seo_cms WHERE id_cms = '.(int)$id_cms.' AND id_shop = '.(int)$this->id_shop);
            if (!$id_seo_cms) {
                $id_seo_cms = $mod->initSeo('cms', $id_cms);
            }
            $cms->indexation = $info['indexation'];
            
            //$tab_ec_seo = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                //$cms->name[$id_lang] = pSQL($info['name_'.$id_lang]);
                $cms->content[$id_lang] = str_replace('\r\n', '', $info['content_'.$id_lang]);
                $cms->head_seo_title[$id_lang] = $info['head_seo_title_'.$id_lang];
                //$cms->meta_title[$id_lang] = $info['meta_title_'.$id_lang];
                $cms->meta_description[$id_lang] = $info['meta_description_'.$id_lang];
                $cms->link_rewrite[$id_lang] = $info['link_rewrite_'.$id_lang];
               /*  if (Tools::strlen($info['h1_'.$id_lang]) == 0) {
                    $info['h1_'.$id_lang] = $cms->name[$id_lang];
                } */
                $tab_ec_seo = array(
                    'id_seo_cms' => (int)$id_seo_cms,
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
                    'ec_seo_cms_lang',
                    $tab_ec_seo,
                    false,
                    true,
                    Db::ON_DUPLICATE_KEY
                );
            }
            try {
                $cms->save();
                $this->confirmations[] = $this->l('Mise à jour réussi.');
            } catch (Exception $e) {
                $this->errors[] =$e->getMessage();
            }
        }
        //$content = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ec_seo/views/templates/admin/seo_cms.tpl');
        
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
