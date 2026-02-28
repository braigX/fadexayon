<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/../../ec_seo.php';
//require_once dirname(__FILE__) . '/../../class/productReport.php';

class AdminEcSeoProductController extends ModuleAdminController
{
    /* protected $position_identifier = 'id_ec_product_report_product'; */
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->id_shop = (int)$this->context->shop->id;
    }

    public function renderForm()
    {
        $mod = new Ec_seo();
        
        $id_product = Tools::getValue('id_product');
        $prod = new Product($id_product, false, null, $this->id_shop);
        $info_ec_seo = array();
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            if (Tools::strlen($prod->meta_title[$id_lang]) == 0) {
                $prod->meta_title[$id_lang] = $prod->name[$id_lang];
            }
            if (Tools::strlen($prod->meta_description[$id_lang]) == 0) {
                $prod->meta_description[$id_lang] = strip_tags($prod->description_short[$id_lang]);
            }
        }
        
        $this->fields_value = (array)$prod;
        foreach ($languages as $lang) {
            $id_lang = $lang['id_lang'];
            $prod->description[$id_lang] = $prod->description[$id_lang].' '.$prod->description_short[$id_lang];
        }
        $this->fields_value['id_product'] = $id_product;
        $cover = Product::getCover($id_product);
        if ($cover) {
            $id_image = $cover['id_image'];
            $this->fields_value['og_image'] = $this->context->link->getImageLink($prod->link_rewrite[(int)$this->context->language->id], $id_product.'-'.$id_image, 'large_default');
        }
        
        $info_ec_seo_prod = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ec_seo_product WHERE id_product = '.(int)$id_product.' AND id_shop = '.(int)$this->id_shop);
        if ($info_ec_seo_prod) {
            //$this->fields_value['use_h1'] = $info_ec_seo_prod['use_h1'];
            $info_ec_seo_prod_lang = Db::getInstance()->executes('SELECT * FROM '._DB_PREFIX_.'ec_seo_product_lang WHERE id_seo_product = '.(int)$info_ec_seo_prod['id_seo_product']);
            foreach ($info_ec_seo_prod_lang as $val) {
                $prod->keyword[$val['id_lang']] = $val['keyword'];
                $this->fields_value['h1'][$val['id_lang']] = $val['h1'];
                $this->fields_value['og_title'][$val['id_lang']] = $prod->meta_title[$id_lang];
                //$this->fields_value['og_type'][$val['id_lang']] = $val['og_type'];
                $this->fields_value['og_description'][$val['id_lang']] = $prod->meta_description[$id_lang];
                //$this->fields_value['og_image'][$val['id_lang']] = $val['og_image'];
                //$this->fields_value['link_canonic'][$val['id_lang']] = $val['link_canonic'];
                $prod->h1[$val['id_lang']] = $val['h1'];
                $prod->link_canonic[$val['id_lang']] = $val['link_canonic'];
            }
        }
       
        
        $meta_desc = $mod->checkMeta($prod);

        $href = $this->context->link->getAdminLink('AdminProducts', true);

        if (strpos($href, 'sell') !== false) {
            $href = explode('?_token', $href);
            $href = $href[0].'/'.$id_product.'?_token'.$href[1];
        } else {
            $href.= '&id_product='.$id_product.'&updateproduct';
        }
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Product'),
                'icon' => 'icon-home'
            ),
            'warning' => '',
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_product',
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
                    'placeholder' => $this->l('To have a different title from the product name, enter it here.')
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
                    'placeholder' => $this->l('To have a different description than your product summary in search results page, write it here.')
                    
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
                    'label' =>  $this->l('Short description'),
                    'name' => 'description_short',
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
                array(
                    'type' => 'text',
                    'label' => $this->l('Friendly URL'),
                    'name' => 'link_rewrite',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Only letters, numbers, underscore (_) and the minus (-) character are allowed.'),
                    'desc' =>  $this->l('Please note that url changes should be used sparingly.').$meta_desc['link_rewrite']
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Canonique URL'),
                    'name' => 'link_canonic',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Only letters, numbers, underscore (_) and the minus (-) character are allowed.'),
                    'disabled' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('OG Title'),
                    'name' => 'og_title',
                    'lang' => true,
                    'disabled' => true,
                    'desc' => $this->l('Managed automatically by Prestashop')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('OG Description'),
                    'name' => 'og_description',
                    'lang' => true,
                    'disabled' => true,
                    'desc' => $this->l('Managed automatically by Prestashop')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('OG Image'),
                    'name' => 'og_image',
                    'lang' => false,
                    'disabled' => true,
                    'desc' => $this->l('Managed automatically by Prestashop')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
            'buttons' => array(
                array(
                    'href' => $href,
                    'title' => $this->l('Back to product'),
                    'icon' => 'process-icon-back'
                    )
            ),
        );
        $br = '';
        foreach ($languages as $lang) {
            if (Tools::strlen($prod->keyword[$lang['id_lang']]) == 0) {
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
        $id_product = Tools::getValue('id_product');
        $prod = new product($id_product, false, null, $this->id_shop);
        $id_seo_product = Db::getInstance()->getValue('SELECT id_seo_product FROM '._DB_PREFIX_.'ec_seo_product WHERE id_product = '.(int)$id_product.' AND id_shop = '.(int)$this->id_shop);
        if (Tools::getValue('updateKeyWord') == "1") {
            $ec_id_lang = Tools::getValue('ec_id_lang');
            Db::getinstance()->update(
                'ec_seo_product_lang',
                array(
                    'keyword' => pSQL(Tools::getValue('keyword'))
                ),
                'id_seo_product = '.(int)$id_seo_product.' AND id_lang = '.(int)$ec_id_lang.''
            );
            
            echo $this->renderForm();
            exit();
        }

        
        if (!$id_seo_product) {
            $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
            $id_seo_product = $mod->initSeo('product', $id_product);
            $tab_keyword = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                $keyword =  Db::getInstance()->getValue('SELECT keyword FROM '._DB_PREFIX_.'ec_seo_product_lang WHERE id_seo_product = '.(int)$id_seo_product.' AND id_lang = '.(int)$id_lang.'');
                $tab_keyword[$id_lang] = $keyword;
            }
            Media::addJsDef(
                array(
                    'ec_tab_keyword' => $tab_keyword,
                )
            );
        }
        $mod->checkSeoNewLang($prod, 'product', $id_seo_product);
        if (((bool)Tools::isSubmit('submitAddconfiguration')) == true) {
            $info = Tools::getAllValues();
            $id_product = $info['id_product'];
            
            $id_seo_product = Db::getInstance()->getValue('SELECT id_seo_product FROM '._DB_PREFIX_.'ec_seo_product WHERE id_product = '.(int)$id_product.' AND id_shop = '.(int)$this->id_shop);
            if (!$id_seo_product) {
                $id_seo_product = $mod->initSeo('product', $id_product);
            }
            
            
            //$tab_ec_seo = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                //$prod->name[$id_lang] = pSQL($info['name_'.$id_lang]);
                $prod->description_short[$id_lang] = str_replace('\r\n', '', $info['description_short_'.$id_lang]);
                $prod->description[$id_lang] = str_replace('\r\n', '', $info['description_'.$id_lang]);
                $prod->meta_title[$id_lang] = $info['meta_title_'.$id_lang];
                $prod->meta_description[$id_lang] = $info['meta_description_'.$id_lang];
                $prod->link_rewrite[$id_lang] = $info['link_rewrite_'.$id_lang];
                if (Tools::strlen($info['h1_'.$id_lang]) == 0) {
                    $info['h1_'.$id_lang] = $prod->name[$id_lang];
                }
                $tab_ec_seo = array(
                    'id_seo_product' => (int)$id_seo_product,
                    'h1' => pSQL($info['h1_'.$id_lang], true),
                    //'description2' => pSQL($info['description2_'.$id_lang], true),
                    //'link_canonic' => pSQL($info['link_canonic_'.$id_lang], true),
                    //'og_title' => pSQL($info['og_title_'.$id_lang]),
                    //'og_type' => pSQL($info['og_type_'.$id_lang]),
                  /*   'og_description' => pSQL($info['og_description_'.$id_lang]),
                    'og_image' => pSQL($info['og_image_'.$id_lang]),  */
                    'id_lang' => (int)$id_lang,
                );

                Db::getinstance()->insert(
                    'ec_seo_product_lang',
                    $tab_ec_seo,
                    false,
                    true,
                    Db::ON_DUPLICATE_KEY
                );
            }
            try {
                $prod->save();
                $this->confirmations[] = $this->l('Successful update.');
            } catch (Exception $e) {
                $this->errors[] =$e->getMessage();
            }
        }
        //$content = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ec_seo/views/templates/admin/seo_product.tpl');
        
        $this->context->smarty->assign(array(
        'content' => $content.$this->content.$this->renderForm().$mod->showLogo(),
        ));
    }

   /*  public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryUi('ui.widget');
        $this->addJqueryPlugin('tagify');
    } */
}
