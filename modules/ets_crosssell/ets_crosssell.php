<?php
/**
 * 2007-2022 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2022 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if(!defined('_PS_VERSION_'))
	exit;
if (!defined('_ETS_CROSSSELL_CACHE_DIR_')) 
    define('_ETS_CROSSSELL_CACHE_DIR_',_PS_CACHE_DIR_.'ets_crosssell_cache/');
require_once(dirname(__FILE__) . '/Ets_crosssell_db.php');
class Ets_crosssell extends Module
{ 
    public $_config_types;
    public $_configs;
    public $_sidebars;
    public $is17 = false;
    public $_sort_options;
    public $shortlink;
    public function __construct()
	{
        $this->name = 'ets_crosssell';
		$this->tab = 'front_office_features';
		$this->version = '2.2.4';
		$this->author = 'ETS-Soft';
		$this->need_instance = 0;
		$this->bootstrap = true;
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true; 
        $this->module_key = '0d2ff6d8b136b0e02a7c5c446415d6df';
		parent::__construct();
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        $this->displayName =$this->l('Cross Selling Pro - Upsell - Shopping cart and all pages');
        $this->description = $this->l('Automated product suggestions based on customer’s interest to display on the shopping cart, product page, order page, etc. Cross-selling Pro (upsell) helps increase the visibility of all products and encourage customers to buy more!');
        $this->shortlink = 'https://mf.short-link.org/';
        if(Tools::getValue('configure')==$this->name && Tools::isSubmit('othermodules'))
        {
            $this->displayRecommendedModules();
        }
	}
    public function _defines()
    {
        $this->context->smarty->assign('link',$this->context->link);
        $this->_sort_options=array(
            array(
                'id_option' => 'cp.position asc',
                'name' => $this->l('Popularity')
            ),
            array(
                'id_option' => 'pl.name asc',
                'name' => $this->l('Product name: A-Z')
            ),
            array(
                'id_option' => 'pl.name desc',
                'name' => $this->l('Product name: Z-A')
            ),
            array(
                'id_option' => 'price asc',
                'name' => $this->l('Price: Lowest first')
            ),
            array(
                'id_option' => 'price desc',
                'name' => $this->l('Price: Highest first')
            ),
            array(
                'id_option' => 'p.id_product desc',
                'name' => $this->l('Newest items first')
            ),
        );
        $this->_config_types = array(
            'purchasedtogether' =>array(
                'title' => $this->l('Frequently purchased together'),
                'default'=>1,
                'desc' => $this->l('Display the products that were often purchased in the same cart as the product currently viewed'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            
            'popularproducts' =>array(
                'title'=> $this->l('Popular products'),
                'default'=>1,
                'desc' => $this->l('Popular products of a product category'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'id_category',
                        'type'=>'text',
                        'required' => true,
                        'validate' => 'isunsignedInt',  
                        'default' => Configuration::get('HOME_FEATURED_CAT'),
                        'label' => $this->l('Category whose products will be selected to display'),
                        'desc' => $this->l('Choose the category ID of the products that you would like to display on store front (default: 2 for "Home").'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'mostviewedproducts' => array(
                'title' => $this->l('Most viewed products'),
                'default' => 1,
                'desc' => $this->l('Products which are viewed most by visitors/customers in your store'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'trendingproducts' => array(
                'title' => $this->l('Trending products'),
                'default' => 1,
                'desc' => $this->l('Products which get most sales in a period of time are considered as trending'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name' => 'day',
                        'type' =>'text',
                        'label' => $this->l('Most purchased in (days)'),
                        'required' => true,
                        'default' => 30,
                        'validate' => 'isunsignedInt',
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'topratedproducts' =>array(
                'title' => $this->l('Top rated products'),
                'default'=>1,
                'desc' => $this->l('Products with the highest rating by customers in your store'),
                'warning' => (Module::isInstalled('productcomments') && Module::isEnabled('productcomments')) || (Module::isInstalled('ets_productcomments') && Module::isEnabled('ets_productcomments')) ? false : $this->l('module is not installed on your site. This module is made by PrestaShop and it\'s free. Please install that module to display top rated products to customers'),
                'module_name' => $this->l('Product Comments'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                        
                    )
                )
            ),
            'featuredproducts' =>array(
                'title' => $this->l('Featured products'),
                'default'=>1,
                'desc' => $this->l('Featured products of a category'),
                'setting' => array(
                    array(
                            'name' => 'title',
                            'type' =>'text',
                            'label' => $this->l('Custom title'),
                            'validate'=>'isCleanHtml',
                            'lang'=>true,
                            'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'id_category',
                        'label' => $this->l('Category'),
                        'type' => 'categories',
                        'required' => true,
                        'validate' => 'isunsignedInt',
                        'default' => Configuration::get('HOME_FEATURED_CAT'),
                        'tree' => array(
                            'id'=>Configuration::get('PS_ROOT_CATEGORY'),
                        )
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'youmightalsolike' =>array(
                'title' => $this->l('You might also like'),
                'default'=>1,
                'desc' => $this->l('Suggest products that are related to the product customers are viewing or the products which are put into their shopping cart'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                        
                    )
                )
            ),
            'productinthesamecategories' =>array(
                'title' => $this->l('Products in the same category'),
                'default'=>1,
                'desc' => $this->l('Products which are in the same category with the ones customers currently viewing'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'viewedproducts' =>array(
                'title' => $this->l('Viewed products'),
                'default'=>1,
                'desc' => $this->l('Products which visitors/customers recently viewed'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'bestselling' =>array(
                'title'=>$this->l('Best selling'),
                'default'=>1,
                'desc' => $this->l('The top products based on sales'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'newproducts' =>array(
                'title' => $this->l('New products'),
                'default'=>1,
                'desc' => $this->l('The newest products within your online store'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'lang'=>true,
                        'validate'=>'isCleanHtml',
                        'label' => $this->l('Custom title'),
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'specialproducts' =>array(
                'title' => $this->l('Special products'),
                'default'=>1,
                'desc' => $this->l('Products which are discounted on the current time'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'lang'=>true,
                        'validate'=>'isCleanHtml',
                        'label' => $this->l('Custom title'),
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'productinthesamemanufacture' =>array(
                'title'=> $this->l('Product in the same brand'),
                'default' =>1,
                'desc' => $this->l('Products which come from the same manufacturer'),
                'info' => Manufacturer::getManufacturers() ? false : $this->display(__FILE__,'brand.tpl'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'specificproducts' => array(
                'title'=> $this->l('Specific products'),
                'default' =>0,
                'desc' => $this->l('Specific products'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name' => 'id_products',
                        'type' =>'search',
                        'label' => $this->l('Products'),
                        'placeholder' => $this->l('Search product by ID, name or reference'),
                        'validate'=>'isCleanHtml',
                    ),
                )
            ),
        );
        $id_root_category = Context::getContext()->shop->getCategory();
        $sub_categories_default=array();
        $categories = Category::getChildren($id_root_category,$this->context->language->id,1,$this->context->shop->id);
        if($categories)
        {
            foreach($categories as $category)
                $sub_categories_default[]= $category['id_category'].',';
        }
        $this->_configs = array(
            'home_page' => array( 
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'category_page' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'product_page' => array(
                'purchasedtogether' =>$this->l('Frequently purchased together'),
                'trendingproducts' => $this->l('Trending products'),
                'productinthesamecategories' =>$this->l('Products from the same category'),
                'productinthesamemanufacture' =>$this->l('Products from the same brand'),
                'youmightalsolike' =>$this->l('You might also like'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'viewedproducts' =>$this->l('Viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'quick_view_page' => array(
                'purchasedtogether' =>$this->l('Frequently purchased together'),
                'trendingproducts' => $this->l('Trending products'),
                'productinthesamecategories' =>$this->l('Products from the same category'),
                'productinthesamemanufacture' =>$this->l('Products from the same brand'),
                'youmightalsolike' =>$this->l('You might also like'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'viewedproducts' =>$this->l('Viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'added_popup_page' => array(
                'purchasedtogether' =>$this->l('Frequently purchased together'),
                'trendingproducts' => $this->l('Trending products'),
                'productinthesamecategories' =>$this->l('Products from the same category'),
                'productinthesamemanufacture' =>$this->l('Products from the same brand'),
                'youmightalsolike' =>$this->l('You might also like'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'viewedproducts' =>$this->l('Viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'cart_page' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'order_conf' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'cms_page' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'contact_page' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'settings'=>array(
                array(
                    'name'=>'ETS_CS_CATEGORY_SUB',
                    'label' => $this->l('Sub categories to filter'),
                    'type' => 'categories',
                    'default' => $sub_categories_default,
                    'use_checkbox'=>true,
                    'tree' => array(
                        'id'=>Configuration::get('PS_ROOT_CATEGORY'),
                        'use_checkbox'=>true,
                        'selected_categories'=> explode(',',Configuration::get('ETS_CS_CATEGORY_SUB')),
                    ),
                    'desc' => $this->l('Customers can filter products by categories'),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Enable cache'),
                    'name' => 'ETS_CS_ENABLE_CACHE',
                    'default'=>1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			)
                ),
                array(
                    'type' =>'text',
                    'label' => $this->l('Cache lifetime'),
                    'name' => 'ETS_CS_CACHE_LIFETIME',
                    'default'=>24,
                    'suffix' => $this->l('hour(s)'),
                    'col' => '2',
                    'required' => true,
                    'validate' => 'isUnsignedFloat',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Display "Out of stock" products'),
                    'name' => 'ETS_CS_OUT_OF_STOCK',
                    'default'=>1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			)
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Exclude free products'),
                    'name' => 'ETS_CS_EXCL_FREE_PRODUCT',
                    'default'=>0,
                    'values' => array(
        				array(
        					'id' => 'ETS_CS_EXCL_FREE_PRODUCT_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'ETS_CS_EXCL_FREE_PRODUCT_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			)
                )
            ),
        ); 
        $this->_sidebars = array(
            'home_page' => $this->l('Homepage'),
            'category_page' => $this->l('Product category page'),
            'product_page' => $this->l('Product details page'),
            'quick_view_page' => $this->l('Product quick view popup'),
            'added_popup_page' => $this->l('Added product popup'),
            'cart_page' => $this->l('Shopping cart page'),
            'order_conf' => $this->l('Order confirmation page'),
            'cms_page' => $this->l('CMS page'),
            'contact_page' => $this->l('Contact page'),
            'settings' => $this->l('General settings'),
                        
        );
    }
    
    /**
	 * @see Module::install()
	 */
    public function install()
	{
	    return parent::install()
        && $this->registerHook('displayBackOfficeHeader') 
        && $this->registerHook('displayHome') 
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayContentWrapperBottom')
        && $this->registerHook('displayProductAdditionalInfo')
        && $this->registerHook('displayRightColumnProduct')
        && $this->registerHook('displayProductPopupAdded')
        && $this->registerHook('displayShoppingCartFooter')
        && $this->registerHook('actionProductAdd')
        && $this->registerHook('actionProductUpdate')
        && $this->registerHook('actionProductDelete')
        && $this->registerHook('actionOrderStatusPostUpdate')
        && $this->registerHook('displayOrderConfirmation')
        && $this->registerHook('displayOrderConfirmation2')
        && $this->registerHook('actionPageCacheAjax')
        && $this->registerHook('actionDeleteAllCache')
        && $this->registerHook('displayFooterProduct') && Ets_crosssell_db::installDb() && $this->installDbDefault() ;
    }
    public function installDbDefault()
    {
        if(!$this->_sidebars)
            $this->_defines();
        foreach($this->_sidebars as $control=> $sidebar)
        {
            $this->_saveConfig($control,true);
            unset($sidebar);
        }
        
        Configuration::updateValue('ETS_CS_HOME_PAGE_LAYOUT','tab');
        Configuration::updateValue('ETS_CS_HOME_PAGE_MODE','grid');
        Configuration::updateValue('ETS_CS_HOME_PAGE_TRENDINGPRODUCTS',1);
        Configuration::updateValue('ETS_CS_HOME_PAGE_MOSTVIEWEDPRODUCTS',1);
        Configuration::updateValue('ETS_CS_HOME_PAGE_TOPRATEDPRODUCTS',1);
        Configuration::updateValue('ETS_CS_CATEGORY_PAGE_LAYOUT','tab');
        Configuration::updateValue('ETS_CS_CATEGORY_PAGE_MODE','slide');
        Configuration::updateValue('ETS_CS_PRODUCT_PAGE_LAYOUT','list');
        Configuration::updateValue('ETS_CS_PRODUCT_PAGE_MODE','slide');
        Configuration::updateValue('ETS_CS_PRODUCT_PAGE_PURCHASEDTOGETHER',1);
        Configuration::updateValue('ETS_CS_PRODUCT_PAGE_PRODUCTINTHESAMECATEGORIES',1);
        Configuration::updateValue('ETS_CS_QUICK_VIEW_PAGE_LAYOUT','tab');
        Configuration::updateValue('ETS_CS_QUICK_VIEW_PAGE_MODE','slide');
        Configuration::updateValue('ETS_CS_QUICK_VIEW_PAGE_YOUMIGHTALSOLIKE',1);        
        Configuration::updateValue('ETS_CS_ADDED_POPUP_PAGE_LAYOUT','tab');
        Configuration::updateValue('ETS_CS_ADDED_POPUP_PAGE_MODE','slide');
        Configuration::updateValue('ETS_CS_ADDED_POPUP_PAGE_YOUMIGHTALSOLIKE',1);
        Configuration::updateValue('ETS_CS_CART_PAGE_LAYOUT','list');
        Configuration::updateValue('ETS_CS_CART_PAGE_MODE','slide');
        Configuration::updateValue('ETS_CS_CART_PAGE_YOUMIGHTALSOLIKE',1);
        Configuration::updateValue('ETS_CS_ORDER_CONF_LAYOUT','list');
        Configuration::updateValue('ETS_CS_ORDER_CONF_MODE','slide');
        Configuration::updateValue('ETS_CS_ORDER_CONF_YOUMIGHTALSOLIKE',1);
        Configuration::updateValue('ETS_CS_CMS_PAGE_LAYOUT','list');
        Configuration::updateValue('ETS_CS_CMS_PAGE_MODE','grid');
        Configuration::updateValue('ETS_CS_CONTACT_PAGE_LAYOUT','tab');
        Configuration::updateValue('ETS_CS_CONTACT_PAGE_MODE','grid');
        Configuration::updateValue('ETS_CS_CONTACT_PAGE_TRENDINGPRODUCTS',1);        
        
        if($pages= array_keys($this->_sidebars))
        {
            foreach($pages as $page)
            {
                if($page!='settings')
                {
                    Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($page).'_ROW_DESKTOP');
                    Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($page).'_ROW_TABLET');
                    Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($page).'_ROW_MOBILE');                
                   if($page=='category_page'|| $page=='contact_page') 
                    {
                        Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_DESKTOP',3);
                        Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_TABLET',2);
                        Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_MOBILE',1);
                    }elseif($page=='quick_view_page' || $page=='added_popup_page' ){
                        Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_DESKTOP',4);
                        Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_TABLET',2);
                        Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_MOBILE',1);
                    }
                    else
                    {
                        Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_DESKTOP',4);
                        Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_TABLET',3);
                        Configuration::updateValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_MOBILE',1);
                    }
                    
                }
            }
        }
        return true;
    }   
    public function _unregisterHooks()
    {
        if(!$this->_config_types)
            $this->_defines();
        foreach($this->_config_types as $key=>$config_type)
        {
            $this->unregisterHook('display'.$key);
            unset($config_type);
        }
        return true;
    } 
    /**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
        $this->clearCache();
        return parent::uninstall()
        && $this->unregisterHook('displayBackOfficeHeader') 
        && $this->unregisterHook('displayHome') 
        && $this->unregisterHook('displayHeader')
        && $this->unregisterHook('displayContentWrapperBottom')
        && $this->unregisterHook('displayProductAdditionalInfo')
        && $this->unregisterHook('displayRightColumnProduct')
        && $this->unregisterHook('displayProductPopupAdded')
        && $this->unregisterHook('displayShoppingCartFooter')
        && $this->unregisterHook('actionProductAdd')
        && $this->unregisterHook('actionProductUpdate')
        && $this->unregisterHook('actionProductDelete')
        && $this->unregisterHook('actionOrderStatusPostUpdate')
        && $this->unregisterHook('displayOrderConfirmation')
        && $this->unregisterHook('displayOrderConfirmation2')
        && $this->unregisterHook('actionPageCacheAjax')
        && $this->unregisterHook('actionDeleteAllCache')
        && $this->unregisterHook('displayFooterProduct')
        && $this->uninstallDbDefault() && Ets_crosssell_db::unInstallDb();
    }
    public function uninstallDbDefault()
    {
        if(!$this->_sidebars)
            $this->_defines();
        foreach($this->_sidebars as $control=> $sidebar)
        {
            $configs = $this->_configs[$control];
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key));
                    if(isset($this->_config_types[$key]['setting']))
                    {
                        foreach($this->_config_types[$key]['setting'] as $setting)
                        {
                            Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key).'_'.Tools::strtoupper($setting['name']));
                        }
                    }
                    unset($config);
                }
             }
             Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT');
             Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_MODE');
             Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT');
             Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS');
             Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP');
             Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET');
             Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE');
             unset($sidebar);
        }
        Configuration::deleteByName('ETS_CS_CATEGORY_SUB');
        Configuration::deleteByName('ETS_CS_ENABLE_CACHE');
        Configuration::deleteByName('ETS_CS_CACHE_LIFETIME');
        return true;
    }
    public function hookDisplayBackOfficeHeader()
    {
        if((Tools::getValue('controller')=='AdminModules' && Tools::getValue('configure')==$this->name))
        {
            $this->context->controller->addCSS($this->_path.'views/css/admin.css');
            $this->context->controller->addCSS($this->_path.'views/css/other.css');
        }
    }
    public function hookActionPageCacheAjax()
    {
        if(!Module::isInstalled('ets_homecategories') || !Module::isEnabled('ets_homecategories'))
        {
            $this->context->cookie->ets_homecat_order_seed = rand(1, 10000);
            $this->context->cookie->write();
        }
        $id_product = (int)Tools::getValue('id_product');
		$productsViewed = (isset($this->context->cookie->viewed) && !empty($this->context->cookie->viewed)) ? array_slice(array_reverse(explode(',', $this->context->cookie->viewed)), 0,Configuration::get('PRODUCTS_VIEWED_NBR')) : array();
        $productMostViewed = (isset($this->context->cookie->mostViewed) && !empty($this->context->cookie->mostViewed)) ? array_slice(array_reverse(explode(',', $this->context->cookie->mostViewed)), 0) : array();
        if(Tools::getValue('controller')=='product' && $id_product && (!in_array($id_product, $productsViewed) || !in_array($id_product,$productMostViewed)))
		{
			$product = new Product((int)$id_product);
			if ($product->checkAccess((int)$this->context->customer->id))
			{
			    if(!in_array($id_product, $productsViewed))
                {
                    if (isset($this->context->cookie->viewed) && !empty($this->context->cookie->viewed))
    					$this->context->cookie->viewed .= ','.(int)$id_product;
    				else
    					$this->context->cookie->viewed = (int)$id_product;
                    $this->context->cookie->write();
                } 
				if(!in_array($id_product,$productMostViewed))
                {
                    Ets_crosssell_db::addProductViewed($id_product);
                    if (isset($this->context->cookie->mostViewed) && !empty($this->context->cookie->mostViewed))
        				$this->context->cookie->mostViewed .= ','.(int)$id_product;
        			else
        				$this->context->cookie->mostViewed = (int)$id_product;
                    $this->context->cookie->write(); 
                }
                
			}
		}
    }
    public function hookActionProductAdd()
    {
        $this->clearCache();
    }
    public function hookActionProductUpdate()
    {
        $this->clearCache();
    }
    public function hookActionProductDelete()
    {
        $this->clearCache();
    }
    public function hookActionOrderStatusPostUpdate($params)
    {
        $this->clearCache();
    }
    public function getContent()
	{
	   if(Tools::isSubmit('add_specific_product'))
            $this->addSpecificProduct();
	   if(Tools::isSubmit('search_product'))
           Ets_crosssell_db::getInstance()->searchProduct();
        if(!$this->_sidebars)
            $this->_defines();
        $control = Tools::getValue('control','home_page');
        if(!in_array($control,array('home_page','category_page','product_page','quick_view_page','added_popup_page','cart_page','order_conf','cms_page','contact_page','settings')))
            $control= 'home_page';
        $this->context->controller->addJqueryUI('ui.sortable');
        if(Tools::getValue('action')=='clearCache')
        {
            $this->clearCache();
            die(
                json_encode(
                    array(
                        'success' => $this->l('Clear cache successfully'),
                    )
                )
            );
        }
        if(Tools::getValue('action')=='updateBlock')
        {
            $field = Tools::getValue('field');
            $value_filed = Tools::getValue('value_filed');
            Configuration::updateValue($field,$value_filed);
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                    )
                )
            );
        }
        if(Tools::getValue('action')=='updateFieldOrdering')
        {
            $field_positions= Tools::getValue('field_positions');
            Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS',implode(',',$field_positions));
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                    )
                )
            );
        }
        if(Tools::isSubmit('saveConfig'))
        {
            if($this->_checkValidatePost($control))
            {
                $this->_saveConfig($control);
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'success' => $this->l('Updated successfully'),
                            )
                        )
                    );
                }
                else
                    Tools::redirect($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&control='.$control.'&conf=4');
            }
            
        }
        $this->smarty->assign(array(
            'ets_crossell_sidebar' => $this->renderSidebar($control),
            'ets_crossell_body_html' => $this->renderAdminBodyHtml($control),
            'control' => $control,
            'ets_cs_module_dir' => $this->_path,
        ));
        return $this->display(__FILE__, 'admin.tpl');           
    }
    public function renderSidebar($control)
    {
        $intro = true;
        $localIps = array(
            '127.0.0.1',
            '::1'
        );
		$baseURL = Tools::strtolower(self::getBaseModLink());
		if(!Tools::isSubmit('intro') && (in_array(Tools::getRemoteAddr(), $localIps) || preg_match('/^.*(localhost|demo|test|dev|:\d+).*$/i', $baseURL)))
		    $intro = false;
        $this->context->smarty->assign(
            array(
                'sidebars' => $this->_sidebars,
                'control' => $control,
                'cs_link_module' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name,
                'other_modules_link' => isset($this->refs) ? $this->refs.$this->context->language->iso_code : $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name.'&othermodules=1',
                'intro' => $intro,
                'refsLink' => isset($this->refs) ? $this->refs.$this->context->language->iso_code : false,
                'ets_cs_link_search_product' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&search_product=1',
            )
        );
        return $this->display(__FILE__,'sidebar.tpl');
    }
    public static function getBaseModLink()
    {
        $context = Context::getContext();
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$context->shop->domain.$context->shop->getBaseURI();
    }
    public function renderAdminBodyHtml($control)
    {
        $languages = Language::getLanguages(false);
        $fields_form = array(
    		'form' => array(
    			'legend' => array(
    				'title' => ($control!='settings' ? $this->l('Product blocks').': ' : '').$this->_sidebars[$control],
    				'icon' => 'fa fa-list-ul'
    			),
    			'input' => array(),
                'submit' => array(
    				'title' => $this->l('Save'),
    			)
            ),
    	);
        $configs = $this->_configs[$control];
        $fields = array();
        if($control!='settings')
        {
            if($configs)
            {
                $first_field=true;
                foreach($configs as $key => $config){
                    $arg = array(
                        'type' =>'switch',
                        'label' => $config,
                        'first_field' => $first_field ? true : false,
                        'name' => 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key),
                        'form_group_class' => 'ets-cs-form-group-field',
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)	
                    );
                    
                    $fields['ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key)] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key));
                    $fields_form['form']['input'][] = $arg;
                    if(isset($this->_config_types[$key]['setting']) && $this->_config_types[$key]['setting'])
                    {
                        foreach($this->_config_types[$key]['setting'] as $index=> $setting)
                        {
                            $name = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key).'_'.Tools::strtoupper($setting['name']);
                            $arg = array(
                                'type' =>$setting['type'],
                                'label' => $setting['label'],
                                'begin_group' => $index==0 ? true:false,
                                'title_group' => $this->_config_types[$key]['title'],
                                'end_group' => $index==count($this->_config_types[$key]['setting'])-1 ? true:false,
                                'module_name' => isset($this->_config_types[$key]['module_name']) ? $this->_config_types[$key]['module_name']:false,
                                'warning' => isset($this->_config_types[$key]['warning']) ? $this->_config_types[$key]['warning']: false,
                                'info' => isset($this->_config_types[$key]['info']) ? $this->_config_types[$key]['info']: false,
                                'name' => $name,
                                'lang'=> isset($setting['lang']) ? $setting['lang']:false,
                                'desc' => isset($setting['desc']) ? $setting['desc'] :'',
                                'form_group_class' => (isset($setting['form_group_class'] ) ? $setting['form_group_class'].' ':'').$key,
                                'tree' => isset($setting['tree']) ? $setting['tree']:array(),
                                'required' => isset($setting['required']) ? $setting['required'] : false,
                                'values' => isset($setting['values']) ? $setting['values']:'',	
                                'options' => isset($setting['options']) ? $setting['options']:false,
                                'placeholder' => isset($setting['placeholder']) ? $setting['placeholder']:false,
                                
                            );
                            if(isset($setting['tree']))
                            {
                                $tree = $setting['tree'];
                                $tree['selected_categories'] = array(Configuration::get($name));
                                $arg['tree']= $tree;
                            }
                            if(isset($setting['lang'])  && $setting['lang'])
                            {
                                foreach($languages as $lang)
                                {
                                    $fields[$name][$lang['id_lang']] = Configuration::get($name,$lang['id_lang']);
                                }
                            }
                            else
                                $fields[$name] = Configuration::get($name);
                            $fields_form['form']['input'][] = $arg;
                        }
                    }
                    $first_field=false;
                }
            }
            $fields_form['form']['input'][] = array(
                'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_LAYOUT',
                'type'=>'radio',
                'label' => $this->l('Product layout'),
                'global_field' => true,
                'values' => array(
                    array(
                        'id' => 'ETS_CS_'.Tools::strtoupper($control).'_LAYOUT_LIST',
                        'value'=>'list',
                        'label' => $this->l('List')
                    ),
                    array(
                        'id' => 'ETS_CS_'.Tools::strtoupper($control).'_LAYOUT_TAB',
                        'value'=>'tab',
                        'label' => $this->l('Tab')
                    ),
                ),
            );
            $fields_form['form']['input'][] = array(
                'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_MODE',
                'type'=>'radio',
                'label' => $this->l('Product listing mode'),
                'values' => array(
                    array(
                        'id' => 'ETS_CS_'.Tools::strtoupper($control).'_MODE_GRID',
                        'value'=>'grid',
                        'label' => $this->l('Grid')
                    ),
                    array(
                        'id' => 'ETS_CS_'.Tools::strtoupper($control).'_MODE_SLIDE',
                        'value'=>'slide',
                        'label' => $this->l('Carousel slider')
                    ),
                ),
            );
            $fields_form['form']['input'][] = array(
                'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT',
                'type'=>'text',
                'required' => true,
                'label' => $this->l('Product count'),
                'desc' => $this->l('The number of products will be displayed per Ajax load'),
            );
            $fields_form['form']['input'][] = array(
                'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP',
                'type'=>'select',
                'label' => $this->l('Number of displayed products per row on desktop'),
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' =>1,
                            'name' =>1,
                        ),
                        array(
                            'id_option' =>2,
                            'name' =>2,
                        ),
                        array(
                            'id_option' =>3,
                            'name' =>3,
                        ),
                        array(
                            'id_option' =>4,
                            'name' =>4,
                        ),
                        array(
                            'id_option' =>5,
                            'name' =>5,
                        ),
                        array(
                            'id_option' =>6,
                            'name' =>6,
                        )
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            );
            $fields_form['form']['input'][] = array(
                'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET',
                'type'=>'select',
                'label' => $this->l('Number of displayed products per row on tablet'),
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' =>1,
                            'name' =>1,
                        ),
                        array(
                            'id_option' =>2,
                            'name' =>2,
                        ),
                        array(
                            'id_option' =>3,
                            'name' =>3,
                        ),
                        array(
                            'id_option' =>4,
                            'name' =>4,
                        ),
                        array(
                            'id_option' =>5,
                            'name' =>5,
                        ),
                        array(
                            'id_option' =>6,
                            'name' =>6,
                        )
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            );
            $fields_form['form']['input'][] = array(
                'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE',
                'type'=>'select',
                'label' => $this->l('Number of displayed products per row on mobile'),
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' =>1,
                            'name' =>1,
                        ),
                        array(
                            'id_option' =>2,
                            'name' =>2,
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            );
            $fields['ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP');
            $fields['ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET');
            $fields['ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE');
            $fields['ETS_CS_'.Tools::strtoupper($control).'_LAYOUT'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT');
            $fields['ETS_CS_'.Tools::strtoupper($control).'_MODE'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_MODE');
            $fields['ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT');
        }
        else
        {
            foreach($configs as $config)
            {
                $fields_form['form']['input'][] = $config;
                $fields[$config['name']] = Configuration::get($config['name'],Tools::getValue($config['name']));
            }
        }
        $helper = new HelperForm();
    	$helper->show_toolbar = false;
    	$helper->table = $this->table;
    	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    	$helper->default_form_language = $lang->id;
    	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    	$helper->module = $this;
    	$helper->identifier = $this->identifier;
    	$helper->submit_action = 'saveConfig';
    	$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control;
    	$helper->token = Tools::getAdminTokenLite('AdminModules');
    	$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));            
        $helper->override_folder = '/';
        $fields_position = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS') ? explode(',',Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS')) :array_keys($this->_configs[$control]);
        $fields_postion_value = array();
        if($fields_position)
        {
            foreach($fields_position as &$field_position)
            {
                $fields_postion_value[] = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($field_position);
            }
        }
        $helper->tpl_vars = array(
    		'base_url' => $this->context->shop->getBaseURL(),
    		'language' => array(
    			'id_lang' => $language->id,
    			'iso_code' => $language->iso_code
    		),
    		'fields_value' => $fields,
            'fields_position' => $fields_position,
            'fields_postion_value' =>$fields_postion_value,
            '_config_types' => $this->_config_types,
            'control' => Tools::strtoupper($control),
    		'languages' => $this->context->controller->getLanguages(),
    		'id_language' => $this->context->language->id,
            'isConfigForm' => true,
            'image_baseurl' => $this->_path.'views/img/',
            'page_title' => $this->_sidebars[$control],
            'tab' => $control,
        );
        return $helper->generateForm(array($fields_form));	
    }
    public function _saveConfig($control,$default=false)
    {
        $languages = Language::getLanguages(false);
        $configs = $this->_configs[$control];
        if($control!='settings')
        {
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    $name = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key);
                    if($default)
                    {
                        $value=0;
                    }else
                    {
                        $value = Tools::getValue($name);
                    }
                    Configuration::updateValue($name,$value);
                    if(isset($this->_config_types[$key]['setting']))
                    {
                        foreach($this->_config_types[$key]['setting'] as $setting)
                        {
                            $name = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key).'_'.Tools::strtoupper($setting['name']);
                            if(isset($setting['lang']) && $setting['lang'])
                            {
                                $valules = array();
                                foreach($languages as $lang)
                                {
                                    $valules[$lang['id_lang']] = trim(Tools::getValue($name.'_'.$lang['id_lang'])) ? trim(Tools::getValue($name.'_'.$lang['id_lang'])) : '';
                                }
                                Configuration::updateValue($name,$valules);
                            }
                            else
                            {
                                if($default)
                                {
                                    if(isset($setting['default']))
                                        $value= $setting['default'];
                                    else
                                        $value=0;
                                }
                                else
                                    $value = Tools::getValue($name);
                                Configuration::updateValue($name,$value);
                            }
                        }
                    }
                }
            }
            Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT','tab'));
            Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_MODE',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_MODE','grid'));
            Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT',8));
            Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP'));
            Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET'));
            Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE'));
        }
        else
        {
            foreach($configs as $config)
            {
                if($config['type']!='categories')
                    Configuration::updateValue($config['name'],Tools::getValue($config['name'],(isset($config['default']) ? $config['default']:'') ));
                else
                {
                    Configuration::updateValue($config['name'],implode(',',Tools::getValue($config['name'],(isset($config['default']) && !Tools::isSubmit('saveConfig') ? $config['default']:array()))));
                }
            }
        }
        $this->clearCache();
    }
    public function _checkValidatePost($control)
    {
        $errors = array();
        $languages = Language::getLanguages(false);
        $configs = $this->_configs[$control];
        if($configs)
        {
            if($control!='settings')
            {
                if(!Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT'))
                    $errors[] = $this->l('Product count is required');
                elseif(!Validate::isUnsignedInt(Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT')))
                    $errors[]= $this->l('Product count is not valid');
            }
            foreach($configs as $key => $config)
            {
                if($control!='settings')
                {

                    if(isset($this->_config_types[$key]['setting']) && Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key)))
                    {
                        foreach($this->_config_types[$key]['setting'] as $setting)
                        {
                            $name = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key).'_'.Tools::strtoupper($setting['name']);
                            if((isset($setting['required']) && $setting['required']) ||  (isset($setting['validate']) && $setting['validate'] && method_exists('Validate',$setting['validate'])))
                            {
                                $validate = $setting['validate'];
                                if(isset($setting['lang']) && $setting['lang'])
                                { 
                                    foreach($languages as $lang)
                                    {
                                        if(isset($setting['required']) && $setting['required']  && !Tools::getValue($name.'_'.$lang['id_lang']))
                                            $errors[] = $setting['label'].' ('.$this->_config_types[$key]['title'].')'.' '.$this->l('is required ');
                                        elseif((isset($setting['validate']) && $setting['validate'] && method_exists('Validate',$setting['validate'])) && !Validate::$validate(trim(Tools::getValue($name.'_'.$lang['id_lang']))))
                                            $errors[] =  $setting['label'].' ('.$this->_config_types[$key]['title'].')'.' '.$this->l('is not valid in ').$lang['iso_code'];
                                    }
                                }
                                else
                                {
                                    if(isset($setting['required']) && $setting['required'] && !Tools::getValue($name))
                                        $errors[] = $setting['label'].' ('.$this->_config_types[$key]['title'].')'.' '. $this->l('is required');
                                    elseif(isset($setting['validate']) && $setting['validate'] && !Validate::$validate(trim(Tools::getValue($name))))
                                        $errors[] = $setting['label'].' ('.$this->_config_types[$key]['title'].')'.' '. $this->l('is not valid');
                                }
                                unset($validate);
                            }
                        }
                    }
                }
                else
                {
                    $validate = isset($config['validate']) ? $config['validate']:'';
                    $name = $config['name'];
                    if(isset($config['lang']) && $config['lang'])
                    { 
                        foreach($languages as $lang)
                        {
                            if(isset($config['required']) && $config['required']  && !Tools::getValue($name.'_'.$lang['id_lang']))
                                $errors[] = $config['label'].' '.$this->l('is required ');
                            elseif((isset($config['validate']) && $config['validate'] && method_exists('Validate',$config['validate'])) && !Validate::$validate(trim(Tools::getValue($name.'_'.$lang['id_lang']))))
                                $errors[] =  $config['label'].' '.$this->l('is not valid in ').$lang['iso_code'];
                        }
                    }
                    else
                    {
                        if(isset($config['required']) && $config['required'] && !Tools::getValue($name))
                            $errors[] = $config['label'].' '. $this->l('is required');
                        elseif($validate &&  !Validate::$validate(trim(Tools::getValue($name))))
                            $errors[] = $config['label'].' '. $this->l('is not valid');
                    }
                    unset($validate);
                }
            }
         }
         if(!$errors)
            return true;
         else
         {
            die(
                json_encode(
                    array(
                        'errors' => $this->displayError($errors),
                    )
                )
            );
         }       
    }
    public function hookDisplayHeader()
    {
        if(!$this->_configs)
            $this->_defines();
        if(!$this->is17 && Tools::getValue('controller')!='index' && Tools::getValue('controller')!='category')
            $this->context->controller->addCSS($this->_path . 'views/css/product_list16.css', 'all');
        if(Tools::isSubmit('getCrosssellContent') && ($tab = Tools::getValue('tab')) && isset($this->_config_types[$tab]) && ($name_page = Tools::getValue('page_name')) && isset($this->_configs[$name_page]))
        {
            $id_product = (int)Tools::getValue('id_product');
            $func = 'hookdisplay'.$tab;
            die(
                json_encode(
                    array(
                        'product_list' => $this->{$func}(array('name_page'=>$name_page,'id_product'=>$id_product)),
                    )
                )  
            );
        }
        if(Tools::isSubmit('sortProductsCrosssellContent') && ($tab = Tools::getValue('tab')) && isset($this->_config_types[$tab]) && ($name_page = Tools::getValue('page_name')) && isset($this->_configs[$name_page]) )
        {
            $sort_by = ($sort_by = Tools::getValue('sort_by')) && in_array($sort_by,self::getSortOptions()) ? $sort_by: '';
            if(!in_array($sort_by,self::getSortOptions()))
                $sort_by ='';
            $id_product = (int)Tools::getValue('id_product');
            $func = 'hookdisplay'.$tab;
            die(
                json_encode(
                    array(
                        'product_list' =>$this->{$func}(array('name_page'=>$name_page,'id_product'=>$id_product,'order_by'=>$sort_by)),
                    )
                )  
            );
        }
        if(Tools::getValue('getProductPopupAdded'))
        {
            die(
                json_encode(
                    array(
                        'product_lists' => Hook::exec('displayProductPopupAdded',array('name_page'=>'added_popup_page','id_product'=>Tools::getValue('id_product')),$this->id),
                    )
                )  
            );
        }
        if(Tools::getValue('getProductExtraPage') && !$this->is17)
        {
            die(
                json_encode(
                    array(
                        'product_lists' => Hook::exec('displayContentWrapperBottom',array(),$this->id),
                    )
                )  
            );
        }
        $this->hookActionPageCacheAjax();
        $this->context->controller->addCSS($this->_path . 'views/css/slick.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/slick.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        if(!$this->is17)
            $this->context->controller->addCSS($this->_path . 'views/css/front16.css', 'all');
        $this->context->smarty->assign(
            array(
                'ets_crosssell_16' => !$this->is17,
            )
        );
        return $this->display(__FILE__,'header.tpl');
    }
    public function hookDisplayOrderConfirmation()
    {
        if(!$this->is17)
            return $this->_execHook('order_conf');
    }
    public function hookDisplayOrderConfirmation2()
    {
        if($this->is17)
            return $this->_execHook('order_conf');
    }
    public function hookDisplayHome()
    {
        return $this->_execHook('home_page');
    }
    public function hookDisplayFooterProduct($params)
    {
        return $this->_execHook('product_page',array('id_product'=>$params['product']->id));
    }
    public function hookDisplayContentWrapperBottom()
    {
        if(Tools::getValue('controller')=='category')
        {
            return $this->_execHook('category_page');
        }   
        if(Tools::getValue('controller')=='contact')
        {
            return $this->_execHook('contact_page');
        }
        if(Tools::getValue('controller')=='cms')
            return $this->_execHook('cms_page');
    }
    public function hookDisplayProductAdditionalInfo()
    {
        if(Tools::getValue('action')=='quickview' && $this->is17)
        {
            return $this->_execHook('quick_view_page',array('id_product'=>Tools::getValue('id_product')));
        }
    }
    public function hookDisplayRightColumnProduct()
    {
        if(Tools::isSubmit('content_only') && !$this->is17)
            return $this->_execHook('quick_view_page',array('id_product'=>Tools::getValue('id_product')));
    }
    public function hookDisplayProductPopupAdded($params)
    {
        return $this->_execHook('added_popup_page',array('id_product'=>isset($params['id_product'])? $params['id_product']:0 ));
    }
    public function hookDisplayShoppingCartFooter()
    {
        return $this->_execHook('cart_page');
    }
    public function displayRecommendedModules()
    {
        $cacheDir = dirname(__file__) . '/../../cache/'.$this->name.'/';
        $cacheFile = $cacheDir.'module-list.xml';
        $cacheLifeTime = 24;
        $cacheTime = (int)Configuration::getGlobalValue('ETS_MOD_CACHE_'.$this->name);
        $profileLinks = array(
            'en' => 'https://addons.prestashop.com/en/207_ets-soft',
            'fr' => 'https://addons.prestashop.com/fr/207_ets-soft',
            'it' => 'https://addons.prestashop.com/it/207_ets-soft',
            'es' => 'https://addons.prestashop.com/es/207_ets-soft',
        );
        if(!is_dir($cacheDir))
        {
            @mkdir($cacheDir, 0755,true);
            if ( @file_exists(dirname(__file__).'/index.php')){
                @copy(dirname(__file__).'/index.php', $cacheDir.'index.php');
            }
        }
        if(!file_exists($cacheFile) || !$cacheTime || time()-$cacheTime > $cacheLifeTime * 60 * 60)
        {
            if(file_exists($cacheFile))
                @unlink($cacheFile);
            if($xml = self::file_get_contents($this->shortlink.'ml.xml'))
            {
                $xmlData = @simplexml_load_string($xml);
                if($xmlData && (!isset($xmlData->enable_cache) || (int)$xmlData->enable_cache))
                {
                    @file_put_contents($cacheFile,$xml);
                    Configuration::updateGlobalValue('ETS_MOD_CACHE_'.$this->name,time());
                }
            }
        }
        else
            $xml = Tools::file_get_contents($cacheFile);
        $modules = array();
        $categories = array();
        $categories[] = array('id'=>0,'title' => $this->l('All categories'));
        $enabled = true;
        $iso = Tools::strtolower($this->context->language->iso_code);
        $moduleName = $this->displayName;
        $contactUrl = '';
        if($xml && ($xmlData = @simplexml_load_string($xml)))
        {
            if(isset($xmlData->modules->item) && $xmlData->modules->item)
            {
                foreach($xmlData->modules->item as $arg)
                {
                    if($arg)
                    {
                        if(isset($arg->module_id) && (string)$arg->module_id==$this->name && isset($arg->{'title'.($iso=='en' ? '' : '_'.$iso)}) && (string)$arg->{'title'.($iso=='en' ? '' : '_'.$iso)})
                            $moduleName = (string)$arg->{'title'.($iso=='en' ? '' : '_'.$iso)};
                        if(isset($arg->module_id) && (string)$arg->module_id==$this->name && isset($arg->contact_url) && (string)$arg->contact_url)
                            $contactUrl = $iso!='en' ? str_replace('/en/','/'.$iso.'/',(string)$arg->contact_url) : (string)$arg->contact_url;
                        $temp = array();
                        foreach($arg as $key=>$val)
                        {
                            if($key=='price' || $key=='download')
                                $temp[$key] = (int)$val;
                            elseif($key=='rating')
                            {
                                $rating = (float)$val;
                                if($rating > 0)
                                {
                                    $ratingInt = (int)$rating;
                                    $ratingDec = $rating-$ratingInt;
                                    $startClass = $ratingDec >= 0.5 ? ceil($rating) : ($ratingDec > 0 ? $ratingInt.'5' : $ratingInt);
                                    $temp['ratingClass'] = 'mod-start-'.$startClass;
                                }
                                else
                                    $temp['ratingClass'] = '';
                            }
                            elseif($key=='rating_count')
                                $temp[$key] = (int)$val;
                            else
                                $temp[$key] = (string)strip_tags($val);
                        }
                        if($iso)
                        {
                            if(isset($temp['link_'.$iso]) && isset($temp['link_'.$iso]))
                                $temp['link'] = $temp['link_'.$iso];
                            if(isset($temp['title_'.$iso]) && isset($temp['title_'.$iso]))
                                $temp['title'] = $temp['title_'.$iso];
                            if(isset($temp['desc_'.$iso]) && isset($temp['desc_'.$iso]))
                                $temp['desc'] = $temp['desc_'.$iso];
                        }
                        $modules[] = $temp;
                    }
                }
            }
            if(isset($xmlData->categories->item) && $xmlData->categories->item)
            {
                foreach($xmlData->categories->item as $arg)
                {
                    if($arg)
                    {
                        $temp = array();
                        foreach($arg as $key=>$val)
                        {
                            $temp[$key] = (string)strip_tags($val);
                        }
                        if(isset($temp['title_'.$iso]) && $temp['title_'.$iso])
                                $temp['title'] = $temp['title_'.$iso];
                        $categories[] = $temp;
                    }
                }
            }
        }
        if(isset($xmlData->{'intro_'.$iso}))
            $intro = $xmlData->{'intro_'.$iso};
        else
            $intro = isset($xmlData->intro_en) ? $xmlData->intro_en : false;
        $this->smarty->assign(array(
            'modules' => $modules,
            'enabled' => $enabled,
            'module_name' => $moduleName,
            'categories' => $categories,
            'img_dir' => $this->_path . 'views/img/',
            'intro' => $intro,
            'shortlink' => $this->shortlink,
            'ets_profile_url' => isset($profileLinks[$iso]) ? $profileLinks[$iso] : $profileLinks['en'],
            'trans' => array(
                'txt_must_have' => $this->l('Must-Have'),
                'txt_downloads' => $this->l('Downloads!'),
                'txt_view_all' => $this->l('View all our modules'),
                'txt_fav' => $this->l('Prestashop\'s favourite'),
                'txt_elected' => $this->l('Elected by merchants'),
                'txt_superhero' => $this->l('Superhero Seller'),
                'txt_partner' => $this->l('Module Partner Creator'),
                'txt_contact' => $this->l('Contact us'),
                'txt_close' => $this->l('Close'),
            ),
            'contactUrl' => $contactUrl,
         ));
         echo $this->display(__FILE__, 'module-list.tpl');
         die;
    }
    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60)
    {
        if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
            $stream_context = stream_context_create(array(
                "http" => array(
                    "timeout" => $curl_timeout,
                    "max_redirects" => 101,
                    "header" => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
                ),
                "ssl"=>array(
                    "allow_self_signed"=>true,
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            ));
        }
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => html_entity_decode($url),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => $curl_timeout,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
            ));
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } elseif (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
            return Tools::file_get_contents($url, $use_include_path, $stream_context);
        } else {
            return false;
        }
    }
    public function excuteHookDisplay($hook_name, $name_page,$id_product =0)
    {
        $func = 'hook'.$hook_name;
        return $this->{$func}(array('name_page'=>$name_page,'id_product'=>$id_product));
    }
    public function _execHook($control,$params=array())
    {
        if(!$this->_configs)
            $this->_defines();
        $layout = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT');
        $configs = $this->_configs[$control];
        $sc_configs = array();
        $fields_position = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS') ? explode(',',Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS')) :array_keys($this->_configs[$control]);
        if($fields_position)
        {
            foreach($fields_position as $filed_position)
            {
                if(Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($filed_position)) && ($filed_position!='viewedproducts' || $this->hookDisplayViewedProducts(array('name_page'=>$control,'id_product'=>isset($params['id_product']) ? $params['id_product']:0))))
                {
                    $title = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($filed_position).'_TITLE',$this->context->language->id);
                    if($id_categories = Configuration::get('ETS_CS_CATEGORY_SUB'))
                    {
                          $id_categories = explode(',',$id_categories);
                          $sub_categories = Ets_crosssell_db::getCategoriesByIDs($id_categories);
                    }
                    else
                        $sub_categories=array();
                    $sc_configs[] = array(
                        'tab_name' => $title ? $title : $configs[$filed_position],
                        'hook' => 'display'.$filed_position,
                        'tab' => $filed_position,
                        'sub_categories' => Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($filed_position).'_DISPLAY_SUB_CATEGORY') ? $sub_categories : array()
                    );
                }
            }
        }
        if($layout=='tab')
        {
            $array = array();
            if($sc_configs)
            {
                foreach($sc_configs as $sc_config)
                {
                    $func = 'hook'.$sc_config['hook'];
                    if($this->{$func}(array('name_page'=>$control,'id_product'=>isset($params['id_product']) ? $params['id_product']:0,'check'=>true)))
                    {
                        $array[] = $sc_config;
                    }  
                }
            }
            $sc_configs = $array;
        }
        $this->smarty->assign(
            array(
                'sc_configs' => $sc_configs,
                'name_page' => $control,
                'id_product' => isset($params['id_product']) ? $params['id_product']:0,
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_MODE') :'list',
            )   
        );
        if($layout=='tab')
            return $this->display(__FILE__,'layout_tab.tpl');
        else
            return $this->display(__FILE__,'layout_list.tpl');
    }
    public static function productsForTemplate($products, Context $context = null)
    {
        if (!$products || !is_array($products))
            return array();
        if (!$context)
            $context = Context::getContext();
        $assembler = new ProductAssembler($context);
        $presenterFactory = new ProductPresenterFactory($context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
            new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
                $context->link
            ),
            $context->link,
            new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
            new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
            $context->getTranslator()
        );

        $products_for_template = array();

        foreach ($products as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $context->language
            );
        }
        return $products_for_template;
    }

    protected function getBestSellers($nProducts)
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }
		if (!($result = Ets_crosssell_db::getBestSalesLight((int)$this->context->language->id, 0, (int)$nProducts)))
			return  array();
        if($this->is17)
            return Ets_crosssell::productsForTemplate($result);                    
		return $result;
    }
    protected function getNewProducts($nbProducts=8,$order_sort = 'cp.position asc')
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }
        if($order_sort)
        {
            $order_sort = explode(' ',$order_sort);
            $order_by = $order_sort[0];
            if(isset($order_sort[1]))
                $order_way = $order_sort[1];
            else
                $order_way = null;
        }
        else
        {
            $order_way = null;
            $order_by = null;
        }
		$newProducts = Ets_crosssell_db::getListNewProducts((int) $this->context->language->id, 0, (int)$nbProducts,false,$order_by,$order_way);
        if($this->is17)
            return Ets_crosssell::productsForTemplate($newProducts);
		return $newProducts;
    }
    private function getSpecialProducts($nbProducts,$order_sort = 'cp.position asc')
    {
        if($order_sort)
        {
            $order_sort = explode(' ',$order_sort);
            $order_by = $order_sort[0];
            if(isset($order_sort[1]))
                $order_way = $order_sort[1];
            else
                $order_way = null;
        }
        else
        {
            $order_way = null;
            $order_by = null;
        }
        if($order_by=='rand')
        {
            $order_way = null;
            $order_by = null;
        }
        $products = Ets_crosssell_db::getPricesDrop(
            (int)Context::getContext()->language->id,
            0,
            (int)$nbProducts,false,$order_by,$order_way
        );
        if($this->is17)
        {
            return Ets_crosssell::productsForTemplate($products);
        }
        else
            return $products;
    }
    public function createCache($html,$params)
    {
        if(!Configuration::get('ETS_CS_ENABLE_CACHE'))
            return false;
        if(!is_dir(_ETS_CROSSSELL_CACHE_DIR_))
        {
            @mkdir(_ETS_CROSSSELL_CACHE_DIR_,0777,true);
            if ( @file_exists(dirname(__file__).'/index.php')){
                @copy(dirname(__file__).'/index.php', _ETS_CROSSSELL_CACHE_DIR_.'index.php');
            }
        }

        $str = '';
        if($params)
        {
            foreach($params as $key=>$value)
            {
                if(!is_array($value))
                    $str .='&'.$key.'='.$value;
            }
        }
        $str .= '&id_lang='.$this->context->language->id;
        $str .= '&ets_currency='.($this->context->cookie->id_currency ? $this->context->cookie->id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
        $id_customer = (isset($this->context->customer->id)) ? (int)($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        } 
        $str .= '&ets_group='.(int)$id_group; 
        $id_country =isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && Validate::isLanguageIsoCode($this->context->cookie->iso_code_country) ?
                    (int) Country::getByIso(Tools::strtoupper($this->context->cookie->iso_code_country)) : (int) Tools::getCountry();
        $str .='&ets_country='.($id_country ? $id_country : (int)$this->context->country->id);
        if(isset($this->context->cookie->id_cart) && $this->context->cookie->id_cart)
            $str .='&hascart=1';
        $str .='&id_category='.(int)Tools::getValue('id_ets_css_sub_category');
        file_put_contents(_ETS_CROSSSELL_CACHE_DIR_.md5($str).'.'.time(),$html);    
    }
    public function hookActionDeleteAllCache()
    {
        $this->clearCache(false);
    }
    public function clearCache($clear_all = true)
    {
        if(is_dir(_ETS_CROSSSELL_CACHE_DIR_) && ($files = glob(_ETS_CROSSSELL_CACHE_DIR_.'*')))
        {
            foreach ($files as $filename) {
                if(file_exists($filename) && $filename!=_ETS_CROSSSELL_CACHE_DIR_.'index.php')
                    @unlink($filename);
                }
        }
        if($clear_all)
        {
            if((int)Configuration::get('ETS_SPEED_ENABLE_PAGE_CACHE') && Module::isInstalled('ets_superspeed') && Module::isEnabled('ets_superspeed') && class_exists('Ets_ss_class_cache'))
            {
                $cacheObjSuperSpeed = new Ets_ss_class_cache();
                if(method_exists($cacheObjSuperSpeed,'deleteCache'))
                    $cacheObjSuperSpeed->deleteCache('index');
            }
            if((int)Configuration::get('ETS_SPEED_ENABLE_PAGE_CACHE') && Module::isInstalled('ets_pagecache') && Module::isEnabled('ets_pagecache') && class_exists('Ets_pagecache_class_cache'))
            {
                $cacheObjPageCache = new Ets_pagecache_class_cache();
                if(method_exists($cacheObjPageCache,'deleteCache'))
                    $cacheObjPageCache->deleteCache('index');
            }
        }
        return true;
    }
    public function getCache($params){
	    if(!Configuration::get('ETS_CS_ENABLE_CACHE'))
            return false;
        if ( !$params )
            return false;
        $str = '';
        if($params)
        {
            foreach($params as $key=>$value)
            {
                if(!is_array($value))
                    $str .='&'.$key.'='.$value;
            }
        }
        $str .= '&id_lang='.$this->context->language->id;
        $str .= '&ets_currency='.($this->context->cookie->id_currency ? $this->context->cookie->id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
        $id_customer = (isset($this->context->customer->id)) ? (int)($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        } 
        $str .= '&ets_group='.(int)$id_group; 
        $id_country =isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && Validate::isLanguageIsoCode($this->context->cookie->iso_code_country) ?
                    (int) Country::getByIso(Tools::strtoupper($this->context->cookie->iso_code_country)) : (int) Tools::getCountry();
        $str .='&ets_country='.($id_country ? $id_country : (int)$this->context->country->id);
        if(isset($this->context->cookie->id_cart) && $this->context->cookie->id_cart)
            $str .='&hascart=1';
        $str .='&id_category='.(int)Tools::getValue('id_ets_css_sub_category');
        $url_file = _ETS_CROSSSELL_CACHE_DIR_.md5($str);
        $cacheLifeTime = (float)Configuration::get('ETS_CS_CACHE_LIFETIME');
        if($files = @glob($url_file.'.*'))
            foreach ($files as $file) {
                if(file_exists($file)){
                    $file_extends = Tools::substr(strrchr($file, '.'), 1);
                    if ( is_numeric( $file_extends )){
                        if ( (time() - (int)$file_extends <= $cacheLifeTime*60*60) || !$cacheLifeTime){
                            return Tools::file_get_contents($file);
                        }else{
                            unlink($file);
                        }
                    }
                }
            }
        return false;
    }
    public function displayProductList($params)
    {
        if($id_categories = Configuration::get('ETS_CS_CATEGORY_SUB'))
        {
              $id_categories = explode(',',$id_categories);
              $sub_categories = Ets_crosssell_db::getCategoriesByIDs($id_categories);
        }
        else
            $sub_categories=array();
        $this->smarty->assign(array(
            'products' => $params['products'],
            'tab' => $params['tab'],
            'name_page' => $params['name_page'],
            'ets_per_row_desktop' => (int)Configuration::get('ETS_CS_'.Tools::strtoupper($params['name_page']).'_ROW_DESKTOP'),
            'ets_per_row_tablet' => (int)Configuration::get('ETS_CS_'.Tools::strtoupper($params['name_page']).'_ROW_TABLET'),
            'ets_per_row_mobile' => (int)Configuration::get('ETS_CS_'.Tools::strtoupper($params['name_page']).'_ROW_MOBILE'),
            'layout_mode' => $params['layout_mode'],
            'sub_categories' =>Configuration::get('ETS_CS_'.Tools::strtoupper($params['name_page']).'_'.Tools::strtoupper($params['tab']).'_DISPLAY_SUB_CATEGORY') ? $sub_categories:array(),
            'id_ets_css_sub_category' => Tools::getValue('id_ets_css_sub_category'),
            'id_product_page' => Tools::getValue('id_product'),
            'sort_by' => isset($params['sort_by']) && $params['sort_by'] ? $params['sort_by']:'',
            'sort_options' =>isset($params['sort_options']) && $params['sort_options'] ? $params['sort_options'] :false,
        ));
        if($this->is17)
        {
            $this->smarty->assign('page_name',Tools::getValue('controller'));
        }
        return  $this->display(__FILE__, 'product_list' . ($this->is17 ? '_17' : '') . '.tpl');
    }
    public function getRandomSeed()
    {
        if ((int)Tools::getValue('ets_homecat_order_seed') > 0 && (int)Tools::getValue('ets_homecat_order_seed') <= 10000)
            return (int)Tools::getValue('ets_homecat_order_seed');
        elseif ((int)$this->context->cookie->ets_homecat_order_seed > 0 && (int)$this->context->cookie->ets_homecat_order_seed <= 10000)
            return (int)$this->context->cookie->ets_homecat_order_seed;
        else
            return 1;
    }
    public function hookDisplayViewedProducts($params)
    {
        $name_page = $params['name_page'];
        if(isset($this->_configs[$name_page]))
        {
            $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
            $productsViewed = (isset($this->context->cookie->viewed) && !empty($this->context->cookie->viewed)) ? array_slice(array_reverse(explode(',', $this->context->cookie->viewed)), 0) : array();
            if($productsViewed || Tools::isSubmit('ajax'))
            {
                $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_VIEWEDPRODUCTS_ENABLE_SORT_BY');
                $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_VIEWEDPRODUCTS_SORT_BY_DEFAULT');
                $products = Ets_crosssell_db::getProducts(false,1,$count_product,isset($params['order_by']) && $params['order_by'] ? $params['order_by']: ($sort_by_default ? $sort_by_default :'cp.position') ,$productsViewed);
                $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
                if(isset($params['check']) && $params['check'])
                {
                    if($products)
                        return true;
                    else
                        return false;
                }
                $params = array(
                    'products' => $products,
                    'tab' => 'viewedproducts',
                    'name_page' => $name_page, 
                    'sort_by' => $sort_by,
                    'sort_options' =>$enable_sort_by ? $this->_sort_options:false,
                    'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
                );
                return $this->displayProductList($params);
            }
            else
            {
                return false;
            }
        }
        
    }
    public function hookDisplayFeaturedProducts($params)
    {
        $name_page = $params['name_page'];
        $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS_SORT_BY_DEFAULT');        
        $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
        $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
        $cacheparams = array(
            'tab' => 'featuredproducts',
            'name_page' => $name_page, 
            'sort_by' => $sort_by,
            'sort_options' =>$enable_sort_by ? $this->_sort_options:false,
            'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
        );
        if($html = $this->getCache($cacheparams))
            return $html;
        if(isset($this->_configs[$name_page]))
        {
            if($id_category = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS_ID_CATEGORY'))
            {
                $products = Ets_crosssell_db::getProducts($id_category,1,$count_product,isset($params['order_by']) && $params['order_by'] ? $params['order_by']: ($sort_by_default? $sort_by_default: 'cp.position'));
                if( isset($params['check']) && $params['check'])
                {
                    if($products)
                        return true;
                    else
                        return false;
                }
                $params = array(
                    'products' => $products,
                    'tab' => 'featuredproducts',
                    'name_page' => $name_page, 
                    'sort_by' => $sort_by,
                    'sort_options' =>$enable_sort_by ? $this->_sort_options:false,
                    'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
                );
                $html = $this->displayProductList($params);
                $this->createCache($html,$cacheparams);
                return $html;
            }
            else{
                if(isset($params['check']) && $params['check'])
                    return false;    
                $this->smarty->assign(
                    array(
                        'tab' => 'featuredproducts',
                        'name_page' => $name_page, 
                        'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list', 
                    )
                );
                $html = $this->display(__FILE__,'no_product.tpl');
                $this->createCache($html,$cacheparams);
                return $html;
            }
        }
    }
    public function hookDisplayPopularProducts($params)
    {
        $name_page = $params['name_page'];
        $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
        $id_category = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_POPULARPRODUCTS_ID_CATEGORY') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_POPULARPRODUCTS_ID_CATEGORY') :2;
        $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_POPULARPRODUCTS_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_POPULARPRODUCTS_SORT_BY_DEFAULT');
        $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
        $cacheparams = array(
            'tab' => 'popularproducts',
            'name_page' => $name_page, 
            'sort_by' => $sort_by,
            'sort_options' => $enable_sort_by ? $this->_sort_options:false,
            'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
        );
        if($html = $this->getCache($cacheparams))
            return $html;
        
        if(isset($this->_configs[$name_page]))
        {
            $products = Ets_crosssell_db::getProducts($id_category,1,$count_product,isset($params['order_by']) && $params['order_by']? $params['order_by']: ($sort_by_default ? $sort_by_default :'cp.position'));
            if(isset($params['check']) && $params['check'])
            {
                if($products)
                    return true;
                else
                    return false;
            }
            $params = array(
            'products'=>$products,
            'tab' => 'popularproducts',
            'name_page' => $name_page, 
            'sort_by' => $sort_by,
            'sort_options' => $enable_sort_by ? $this->_sort_options:false,
            'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            $html = $this->displayProductList($params);
            $this->createCache($html,$cacheparams);
            return $html;
        }
    }
    public function hookDisplayMostViewedProducts($params)
    {
        $name_page = $params['name_page'];
        if(isset($this->_configs[$name_page]))
        {
            $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
            $products = Ets_crosssell_db::getMostViewedProducts($count_product);
            if(isset($params['check']) && $params['check'])
            {
                if($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'mostviewedproducts',
                'name_page' => $name_page, 
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            return $this->displayProductList($params);
        }
        
    }
    public function hookDisplayYouMightAlsoLike($params)
    {
        $name_page = $params['name_page'];
        $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
        if(Tools::getValue('id_product'))
            $id_product = Tools::getValue('id_product');
        else
            $id_product =0;
        $sort_by = ($sort_by =Tools::getValue('sort_by')) && in_array($sort_by,self::getSortOptions()) ? $sort_by: '';
        if($id_product)
        {

            $cacheparams = array(
                'id_product' => $id_product,
                'tab' => 'youmightalsolike',
                'name_page' => $name_page,
                'sort_by' =>  $sort_by,
                'sort_options' =>false,
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            if($html = $this->getCache($cacheparams))
                return $html;
        }
        if(isset($this->_configs[$name_page]))
        {
            $products = Ets_crosssell_db::getProductYouMightAlsoLike($id_product,$count_product,isset($params['order_by']) && $params['order_by']? $params['order_by']:'total_product desc');
            if(isset($params['check']) && $params['check'])
            {
                if($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'youmightalsolike',
                'name_page' => $name_page, 
                'sort_by' => $sort_by,
                'sort_options' =>false,
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            $html = $this->displayProductList($params);
            if($id_product)
                $this->createCache($html,$cacheparams);
            return $html;
        }  
    }
    public function hookDisplayBestSelling($params)
    {
        $name_page = $params['name_page'];
        if(isset($this->_configs[$name_page]))
        {
            $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
            $products = $this->getBestSellers($count_product);
            if(isset($params['check']) && $params['check'])
            {
                if($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'bestselling',
                'name_page' => $name_page, 
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            return $this->displayProductList($params);
        }
        
    }
    public function hookDisplayTrendingProducts($params)
    {
        $name_page = $params['name_page'];
        if(isset($this->_configs[$name_page]))
        {
            $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
            $day = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_TRENDINGPRODUCTS_DAY');
            $products = Ets_crosssell_db::getTrendingProducts($count_product,$day);
            if(isset($params['check']) && $params['check'])
            {
                if($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'trendingproducts',
                'name_page' => $name_page, 
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            return $this->displayProductList($params);
        }
    }
    public function hookDisplayNewProducts($params)
    {
        $name_page = $params['name_page'];
        $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
        $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_NEWPRODUCTS_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_NEWPRODUCTS_SORT_BY_DEFAULT');
        $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
        $cacheparams = array(
            'tab' => 'newproducts',
            'name_page' => $name_page, 
            'sort_by' => $sort_by,
            'sort_options' => $enable_sort_by ? $this->_sort_options:false,
            'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
        );
        if($html = $this->getCache($cacheparams))
            return $html;                
        if(isset($this->_configs[$name_page]))
        {
            $products = $this->getNewProducts($count_product,isset($params['order_by']) && $params['order_by'] ? $params['order_by']: ($sort_by_default ? $sort_by_default : 'cp.position'));
            if(isset($params['check']) && $params['check'])
            {
                if($products)   
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'newproducts',
                'name_page' => $name_page, 
                'sort_by' => $sort_by,
                'sort_options' => $enable_sort_by ? $this->_sort_options:false,
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            $html = $this->displayProductList($params);
            $this->createCache($html,$cacheparams);
            return $html;
        }
    }
    public function hookDisplaySpecialProducts($params)
    {
        $name_page = $params['name_page'];
        $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
        $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_SPECIALPRODUCTS_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_SPECIALPRODUCTS_SORT_BY_DEFAULT');
        $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
        $cacheparams = array(
            'tab' => 'specialproducts',
            'name_page' => $name_page, 
            'sort_by' => $sort_by,
            'sort_options' => $enable_sort_by ?  $this->_sort_options :false,
            'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
        );
        if($html = $this->getCache($cacheparams))
            return $html;
        if(isset($this->_configs[$name_page]))
        {
            $products = $this->getSpecialProducts($count_product,isset($params['order_by']) && $params['order_by'] ? $params['order_by']: ($sort_by_default ? $sort_by_default : 'cp.position'));
            if(isset($params['check']) && $params['check'])
            {
                if($products)
                    return true;
                else
                    return false;    
            }
            $params = array(
                'products' =>$products,
                'tab' => 'specialproducts',
                'name_page' => $name_page, 
                'sort_by' => $sort_by,
                'sort_options' => $enable_sort_by ?  $this->_sort_options :false,
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            $html = $this->displayProductList($params);
            $this->createCache($html,$cacheparams);
            return $html;
        }
    }
    public function hookDisplayTopratedProducts($params)
    {
        $name_page = $params['name_page'];
        if(isset($this->_configs[$name_page]))
        {
            $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
            $products = Ets_crosssell_db::getTopRatedProducts($count_product);
            if(isset($params['check']) && $params['check'])
            {
                if($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'topratedproducts',
                'name_page' => $name_page, 
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            return $this->displayProductList($params);
        }
    }
    public function hookDisplayPurchasedTogether($params)
    {
        $name_page = $params['name_page'];
        if(isset($this->_configs[$name_page]))
        {
            $id_product= isset($params['id_product']) ? (int)$params['id_product'] :0;
            $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
            $products = Ets_crosssell_db::getProductPurchasedTogether($id_product,$count_product);
            if(isset($params['check']) && $params['check'])
            {
                if($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'purchasedtogether',
                'name_page' => $name_page, 
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            return $this->displayProductList($params);
        }
    }
    public function hookDisplayProductInTheSameCategories($params)
    {
        $name_page = $params['name_page'];
        $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
        $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_PRODUCTINTHESAMECATEGORIES_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_PRODUCTINTHESAMECATEGORIES_SORT_BY_DEFAULT');
        $id_product= isset($params['id_product']) ? (int)$params['id_product'] :0;
        $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
        if(!$id_product)
            return false;
        $cacheparams = array(
            'id_product' => $id_product,
            'tab' => 'productinthesamecategories',
            'name_page' => $name_page, 
            'sort_by' => $sort_by,
            'sort_options' => $enable_sort_by ? $this->_sort_options :false,
            'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
        );
        if($html = $this->getCache($cacheparams))
            return $html;
        if(isset($this->_configs[$name_page]))
        {
            if($id_product)
            {
                $product = new Product($id_product);
                $id_category = $product->id_category_default;
                $products = Ets_crosssell_db::getProducts($id_category,0,$count_product, isset($params['order_by']) ? $params['order_by']: ($sort_by_default ? $sort_by_default : 'cp.position'),false,array($id_product));
                if(isset($params['check']) && $params['check'])
                {
                    if($products)
                        return true;
                    else
                        return false;
                }
                $params = array(
                    'products' => $products,
                    'tab' => 'productinthesamecategories',
                    'name_page' => $name_page, 
                    'sort_by' => $sort_by,
                    'sort_options' => $enable_sort_by ? $this->_sort_options :false,
                    'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
                );
                $html = $this->displayProductList($params);
                $this->createCache($html,$cacheparams);
                return $html;
            }
        }
    }
    public function hookDisplayProductInTheSameManufacture($params)
    {
        $name_page = $params['name_page'];
        $id_product= isset($params['id_product']) ? (int)$params['id_product'] :0;
        if(!$id_product)
            return false;
        $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
        $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_PRODUCTINTHESAMEMANUFACTURE_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_PRODUCTINTHESAMEMANUFACTURE_SORT_BY_DEFAULT');
        $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
        $cacheparams = array(
            'id_product' => $id_product,
            'tab' => 'productinthesamemanufacture',
            'name_page' => $name_page, 
            'sort_by' => $sort_by,
            'sort_options' => $enable_sort_by ? $this->_sort_options:false,
            'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
        );
        if($html = $this->getCache($cacheparams))
            return $html;
        if(isset($this->_configs[$name_page]))
        {
            if($id_product)
            {
                $product = new Product($id_product);
                $id_manufacturer = $product->id_manufacturer;
                $products = Ets_crosssell_db::getProducts(0,0,$count_product,isset($params['order_by']) ? $params['order_by']:($sort_by_default ? $sort_by_default : 'cp.position'),false,array($id_product),false,false,$id_manufacturer);
                if(isset($params['check']) && $params['check'])
                {
                    if($products)
                        return true;
                    else
                        return false;
                }
                $params = array(
                    'products' => $products,
                    'tab' => 'productinthesamemanufacture',
                    'name_page' => $name_page, 
                    'sort_by' => $sort_by,
                    'sort_options' => $enable_sort_by ? $this->_sort_options:false,
                    'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
                );
                $html = $this->displayProductList($params);
                $this->createCache($html,$cacheparams);
                return $html;
            }
        }
    }
    public function hookdisplayspecificproducts($params)
    {
        $name_page = isset($params['name_page']) ? $params['name_page'] :'';
        if($name_page && ($productIds = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_SPECIFICPRODUCTS_ID_PRODUCTS')))
        {
            $cacheparams = array(
                'id_products' => $productIds,
                'tab' => 'specificproducts',
                'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
            );
            if($html = $this->getCache($cacheparams))
                return $html;
            
            $IDs = explode(',', $productIds);
            $products = array();
            foreach ($IDs as $ID) {
                if ($ID &&($tmpIDs = explode('-', $ID)) && isset($tmpIDs[0]) && $tmpIDs[0] && ($product = new Product($tmpIDs[0])) && Validate::isLoadedObject($product) && $product->active) {
                    $products[] = array(
                        'id_product' => $tmpIDs[0],
                        'id_product_attribute' => !empty($tmpIDs[1])? $tmpIDs[1] : 0,
                    );
                }
            }
            if(isset($params['check']) && $params['check'])
            {
                if(!$products)
                    return false;
                else    
                    return true;
            }
            
            if ($products) {
                $products = Ets_crosssell_db::getInstance()->getBlockProducts($products);
                $params = array(
                    'products' => $products,
                    'tab' => 'specificproducts',
                    'name_page' => $name_page, 
                    'sort_by' => false,
                    'sort_options' => false,
                    'layout_mode' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') ? Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MODE') :'list',
                );
                $html = $this->displayProductList($params);
                $this->createCache($html,$cacheparams);
                return $html;
            }
        }
    }
    public function displaySearchProductList($productIds)
    {
        if ($productIds)
        {
            $IDs = explode(',', $productIds);
            $products = array();
            foreach ($IDs as $ID) {
                if ($ID &&($tmpIDs = explode('-', $ID))) {
                    $products[] = array(
                        'id_product' => $tmpIDs[0],
                        'id_product_attribute' => !empty($tmpIDs[1])? $tmpIDs[1] : 0,
                    );
                }
            }
            if ($products) {
                $products = Ets_crosssell_db::getInstance()->getBlockProducts($products);
            }
            $this->smarty->assign('products', $products);
            return $this->display(__FILE__, 'block-product-item.tpl');
        }
    }
    public function addSpecificProduct()
    {
        if (($IDs = Tools::getValue('ids', false)) && self::validateArray($IDs))
        {
            die(
                json_encode(
                    array(
                        'html' => $this->displaySearchProductList($IDs),
                    )
                )
            );
        }
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(!is_array($array))
        {
            if(method_exists('Validate',$validate))
            {
                return Validate::$validate($array);
            }
            else
                return true;
        }
        if(method_exists('Validate',$validate))
        {
            if($array && is_array($array))
            {
                $ok= true;
                foreach($array as $val)
                {
                    if(!is_array($val))
                    {
                        if($val && !Validate::$validate($val))
                        {
                            $ok= false;
                            break;
                        }
                    }
                    else
                        $ok = self::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public static function getSortOptions()
    {
        return array('cp.position desc','cp.position asc','pl.name asc','pl.name desc','price asc','price desc','p.id_product desc','p.id_product asc');
    }
}