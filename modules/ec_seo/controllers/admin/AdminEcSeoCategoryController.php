<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/../../ec_seo.php';

class AdminEcSeoCategoryController extends ModuleAdminController
{
    /* protected $position_identifier = 'id_ec_product_report_category'; */
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->id_shop = (int)$this->context->shop->id;
    }

    public function renderForm()
    {
        $mod = new Ec_seo();
        
        $id_category = Tools::getValue('id_category');
        $cat = new Category($id_category, null, $this->id_shop);
        $info_ec_seo = array();
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            if (Tools::strlen($cat->meta_title[$id_lang]) == 0) {
                $cat->meta_title[$id_lang] = $cat->name[$id_lang];
            }
            if (Tools::strlen($cat->meta_description[$id_lang]) == 0) {
                $desc = strip_tags($cat->description[$id_lang]);
                $search = array("\n", "\r", "\r\n", "\n\r", "\t", "\\", "&", "Nbsp;");
                $desc = str_replace($search, " ", $desc);
                $cat->meta_description[$id_lang] = $desc;
            }
        }
        $this->fields_value = (array)$cat;
        $this->fields_value['id_category'] = $id_category;
        $info_ec_seo_cat = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ec_seo_category WHERE id_category = '.(int)$id_category.' AND id_shop = '.(int)$this->id_shop);
        
        if ($info_ec_seo_cat) {
            //$this->fields_value['use_h1'] = $info_ec_seo_cat['use_h1'];
            $info_ec_seo_cat_lang = Db::getInstance()->executes(
                'SELECT * FROM '._DB_PREFIX_.'ec_seo_category_lang cl
                LEFT JOIN '._DB_PREFIX_.'ec_seo_category c ON c.id_seo_category = cl.id_seo_category
                WHERE c.id_seo_category = '.(int)$info_ec_seo_cat['id_seo_category'].' AND id_shop = '.(int)$this->id_shop
            );
            foreach ($info_ec_seo_cat_lang as $val) {
                $cat->keyword[$val['id_lang']] = $val['keyword'];
                $this->fields_value['h1'][$val['id_lang']] = $val['h1'];
                $this->fields_value['description2'][$val['id_lang']] = $val['description2'];
                $this->fields_value['og_title'][$val['id_lang']] = $val['og_title'];
                //$this->fields_value['og_type'][$val['id_lang']] = $val['og_type'];
                $this->fields_value['og_description'][$val['id_lang']] = $val['og_description'];
                $this->fields_value['og_image'][$val['id_lang']] = $val['og_image'];
                $this->fields_value['link_canonic'][$val['id_lang']] = $val['link_canonic'];
                $cat->h1[$val['id_lang']] = $val['h1'];
                $cat->description2[$val['id_lang']] = $val['description2'];
                $cat->link_canonic[$val['id_lang']] = $val['link_canonic'];
            }
        }
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            $cat->description[$id_lang] = $cat->description[$id_lang].' '.$cat->description2[$id_lang];
        }
        
        $meta_desc = $mod->checkMeta($cat);

        $href = $this->context->link->getAdminLink('AdminCategories', true);

        if (strpos($href, 'sell') !== false) {
            $href = explode('?_token', $href);
            $href = $href[0].'/'.$id_category.'/edit?_token'.$href[1];
        } else {
            $href.= '&id_category='.$id_category.'&updatecategory';
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
                'title' => $this->l('Category'),
                'icon' => 'icon-home'
            ),
            'warning' => '',
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_category',
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
                    'placeholder' => $this->l('To have a different title from the category name, enter it here.')
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
                    'placeholder' => $this->l('To have a different description than your category summary in search results page, write it here.')
                    
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'class' => 'copy2friendlyUrl',
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    //'desc' => $meta_desc['name']
                    'disabled' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('H1'),
                    'name' => 'h1',
                    'lang' => true,
                    'required' => true,
                    'class' => 'copy2friendlyUrl',
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    'desc' => $meta_desc['h1']
                ),
                array(
                    'type' => 'textarea',
                    'label' =>  $this->l('Description'),
                    'name' => 'description',
                    'autoload_rte' => true,
                    'lang' => true,
                    'hint' =>  $this->l('Invalid characters:') . ' <>;=#{}',
                ),
                array(
                    'type' => 'textarea',
                    'label' =>  $this->l('Description 2'),
                    'name' => 'description2',
                    'autoload_rte' => true,
                    'lang' => true,
                    'hint' =>  $this->l('Invalid characters:') . ' <>;=#{}',
                    'desc' => $meta_desc['desc_total']
                ),
                array(
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
                    //'hint' => $this->l('Only letters, numbers, underscore (_) and the minus (-) character are allowed.'),
                    'disabled' => true,
                ),
                $og_title_input,
                $og_description_input,
                array(
                    'type' => 'text',
                    'label' => $this->l('OG Image'),
                    'name' => 'og_image',
                    'lang' => true,
                ),
               /*  array(
                    'type' => 'tags',
                    'label' =>  $this->l('Meta keywords'),
                    'name' => 'meta_keywords',
                    'lang' => true,
                    'hint' =>  $this->l('To add "tags," click in the field, write something, and then press "Enter."') . '&nbsp;' .  $this->l('Forbidden characters:') . ' <>;=#{}',
                    'desc' => $meta_desc['meta_keywords']
                ), */
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                array(
                    'href' => $href,
                    'title' => $this->l('Back to Category'),
                    'icon' => 'process-icon-back'
                    )
            ),
        );
        $br = '';
        foreach ($languages as $lang) {
            if (Tools::strlen($cat->keyword[$lang['id_lang']]) == 0) {
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
        $id_category = Tools::getValue('id_category');
        $cat = new Category($id_category, null, $this->id_shop);
        $id_seo_category = Db::getInstance()->getValue('SELECT id_seo_category FROM '._DB_PREFIX_.'ec_seo_category WHERE id_category = '.(int)$id_category.' AND id_shop = '.(int)$this->id_shop);
        if (Tools::getValue('updateKeyWord') == "1") {
            $ec_id_lang = Tools::getValue('ec_id_lang');
            Db::getinstance()->update(
                'ec_seo_category_lang',
                array(
                    'keyword' => pSQL(Tools::getValue('keyword'))
                ),
                'id_seo_category = '.(int)$id_seo_category.' AND id_lang = '.(int)$ec_id_lang.''
            );
            
            echo $this->renderForm();
            exit();
        }

        
        if (!$id_seo_category) {
            $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
            $id_seo_category = $mod->initSeo('category', $id_category);
            $tab_keyword = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                $keyword =  Db::getInstance()->getValue('SELECT keyword FROM '._DB_PREFIX_.'ec_seo_category_lang WHERE id_seo_category = '.(int)$id_seo_category.' AND id_lang = '.(int)$id_lang.'');
                $tab_keyword[$id_lang] = $keyword;
            }
            Media::addJsDef(
                array(
                    'ec_tab_keyword' => $tab_keyword,
                )
            );
        }
        $mod->checkSeoNewLang($cat, 'category', $id_seo_category);
        if (((bool)Tools::isSubmit('submitAddconfiguration')) == true) {
            $info = Tools::getAllValues();
            $id_category = $info['id_category'];
            
            $id_seo_category = Db::getInstance()->getValue('SELECT id_seo_category FROM '._DB_PREFIX_.'ec_seo_category WHERE id_category = '.(int)$id_category.' AND id_shop = '.(int)$this->id_shop);
            if (!$id_seo_category) {
                $id_seo_category = $mod->initSeo('category', $id_category);
            }
            
            
            //$tab_ec_seo = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                //$cat->name[$id_lang] = pSQL($info['name_'.$id_lang]);
                $cat->description[$id_lang] = str_replace('\r\n', '', $info['description_'.$id_lang]);
                $cat->meta_title[$id_lang] = $info['meta_title_'.$id_lang];
                $cat->meta_description[$id_lang] = $info['meta_description_'.$id_lang];
                $cat->link_rewrite[$id_lang] = $info['link_rewrite_'.$id_lang];
                if (Tools::strlen($info['h1_'.$id_lang]) == 0) {
                    $info['h1_'.$id_lang] = $cat->name[$id_lang];
                }
                $tab_ec_seo = array(
                    'id_seo_category' => (int)$id_seo_category,
                    'h1' => pSQL($info['h1_'.$id_lang], true),
                    'description2' => pSQL($info['description2_'.$id_lang], true),
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
                    'ec_seo_category_lang',
                    $tab_ec_seo,
                    false,
                    true,
                    Db::ON_DUPLICATE_KEY
                );
            }
            try {
                $cat->save();
                $this->confirmations[] = $this->l('Mise à jour réussi.');
            } catch (Exception $e) {
                $this->errors[] =$e->getMessage();
            }
        }
        //$content = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ec_seo/views/templates/admin/seo_category.tpl');
        
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
