<?php
/**
*
*
*    Social Meta Data
*    Copyright 2018  Inno-mods.io
*
*    @author    Inno-mods.io
*    @copyright Inno-mods.io
*    @version   1.2.0
*    Visit us at http://www.inno-mods.io
*
*
**/

/*
* check presta
*/
if (!defined('_PS_VERSION_')) {
    exit;
}


/*
* SocialMetaData Module
*/
class SocialMetaData extends Module
{

    /*
    * constructor
    */
    public function __construct()
    {
        $this->name = 'socialmetadata';
        $this->tab = 'administration';
        $this->version = '1.2.0';
        $this->author = 'Inno-mods.io';
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => '8.99.99' );
        $this->bootstrap = true;

        parent::__construct(); //needed for translations

        $this->displayName = $this->l('Social Meta Data');
        $this->description = $this->l('Social Meta Data is a plugin that helps you get absolute control over your shop meta data.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall '.$this->displayName.'?');
    }



    /*
    * install
    */
    public function install()
    {
        if (Shop::isFeatureActive()) { //  check whether the multistore feature is active or not, and if at least two stores are presently activated
            Shop::setContext(Shop::CONTEXT_ALL); // change the context in order  to apply coming changes to all existing stores instead of only the current store
        }

        if (!parent::install() || // install
            //!$this->addTabs() || // add hidden tab for admin controllers
            !$this->registerHook('header') || // register front office header hook
            !$this->registerHook('displayBackOfficeHeader') ) { // register admin header hook
            return false;
        }

    		// add default configuration options
    		if ( !Configuration::get('OGP_GENERAL_STATUS') ){
      			// open graph protocol
      			// general settings
      			Configuration::updateValue('OGP_GENERAL_STATUS', 0);
    	      // facebook
    			  Configuration::updateValue('SOCIAL_META_DATA_FACEBOOK_APPID', '');
    	      // twitter cards
    	      Configuration::updateValue('SOCIAL_META_DATA_TWITTER_CARDS_STATUS', 0);
    	      Configuration::updateValue('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME', '');
      			// home page settings
      			Configuration::updateValue('OGP_HOMEPAGE_STATUS', '');
      			Configuration::updateValue('OGP_HOMEPAGE_TITLE', '');
      			Configuration::updateValue('OGP_HOMEPAGE_DESCRIPTION', '');
      			Configuration::updateValue('OGP_HOMEPAGE_IMAGE', '');
      			// product settings
      			Configuration::updateValue('OGP_PRODUCT_STATUS', 0);
      			Configuration::updateValue('OGP_PRODUCT_TITLE', '');
      			Configuration::updateValue('OGP_PRODUCT_DESCRIPTION', '');
      			Configuration::updateValue('OGP_PRODUCT_IMAGE', '');
      			Configuration::updateValue('OGP_PRODUCT_DEFAULT_IMAGE', '');
      			// category settings
      			Configuration::updateValue('OGP_CATEGORY_STATUS', 0);
      			Configuration::updateValue('OGP_CATEGORY_TITLE', '');
      			Configuration::updateValue('OGP_CATEGORY_DESCRIPTION', '');
      			Configuration::updateValue('OGP_CATEGORY_IMAGE', '');
      			Configuration::updateValue('OGP_CATEGORY_DEFAULT_IMAGE', '');
      			// manufacturer settings
      			Configuration::updateValue('OGP_MANUFACTURER_STATUS', 0);
      			Configuration::updateValue('OGP_MANUFACTURER_TITLE', '');
      			Configuration::updateValue('OGP_MANUFACTURER_DESCRIPTION', '');
      			Configuration::updateValue('OGP_MANUFACTURER_IMAGE', '');
      			Configuration::updateValue('OGP_MANUFACTURER_DEFAULT_IMAGE', '');
      			// supplier settings
      			Configuration::updateValue('OGP_SUPPLIER_STATUS', 0);
      			Configuration::updateValue('OGP_SUPPLIER_TITLE', '');
      			Configuration::updateValue('OGP_SUPPLIER_DESCRIPTION', '');
      			Configuration::updateValue('OGP_SUPPLIER_IMAGE', '');
      			Configuration::updateValue('OGP_SUPPLIER_DEFAULT_IMAGE', '');
      			// cms
      			Configuration::updateValue('OGP_CMS_STATUS', 0);
      			Configuration::updateValue('OGP_CMS_TITLE', '');
      			Configuration::updateValue('OGP_CMS_DESCRIPTION', '');
      			Configuration::updateValue('OGP_CMS_DEFAULT_IMAGE', '');
      			// cms category
      			Configuration::updateValue('OGP_CMS_CATEGORY_STATUS', 0);
      			Configuration::updateValue('OGP_CMS_CATEGORY_TITLE', '');
      			Configuration::updateValue('OGP_CMS_CATEGORY_DESCRIPTION', '');
      			Configuration::updateValue('OGP_CMS_CATEGORY_DEFAULT_IMAGE', '');
      			// google rich snippets
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_STATUS', 0);
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_SHOP_TITLE', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_SHOP_DESCRIPTION', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_LOGO', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_ADDRESSES_STATUS', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_GEO_STATUS', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PHONE', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_FACEBOOK_URL', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_TWITTER_URL', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_GOOGLE_PLUS_URL', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_INSTAGRAM_URL', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_YOUTUBE_URL', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_LINKEDIN_URL', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PINTEREST_URL', '');
      			// google rich snippets for product page
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_STATUS', 0);
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_TITLE', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_DESCRIPTION', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_IMAGE', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_DEFAULT_IMAGE', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_PRICE_STATUS', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_REFERENCE_STATUS', '');
      			Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_MANUFACTURER_STATUS', '');
    		}

        // return true
        return true;
    }



    /*
    * uninstall
    */
    public function uninstall()
    {

        // uninstall and drop module's database tables, if something fails, return false
        if (!parent::uninstall() || // uninstall
        	// open graph protocol
    			// general settings
    			!Configuration::deleteByName('OGP_GENERAL_STATUS') ||
	        // facebook
			    !Configuration::deleteByName('SOCIAL_META_DATA_FACEBOOK_APPID') ||
	        // twitter cards
	        !Configuration::deleteByName('SOCIAL_META_DATA_TWITTER_CARDS_STATUS') ||
	        !Configuration::deleteByName('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME') ||
    			// home page settings
    			!Configuration::deleteByName('OGP_HOMEPAGE_STATUS') ||
    			!Configuration::deleteByName('OGP_HOMEPAGE_TITLE') ||
    			!Configuration::deleteByName('OGP_HOMEPAGE_DESCRIPTION') ||
    			!Configuration::deleteByName('OGP_HOMEPAGE_IMAGE') ||
    			// product settings
    			!Configuration::deleteByName('OGP_PRODUCT_STATUS') ||
    			!Configuration::deleteByName('OGP_PRODUCT_TITLE') ||
    			!Configuration::deleteByName('OGP_PRODUCT_DESCRIPTION') ||
    			!Configuration::deleteByName('OGP_PRODUCT_IMAGE') ||
    			!Configuration::deleteByName('OGP_PRODUCT_DEFAULT_IMAGE') ||
    			// category settings
    			!Configuration::deleteByName('OGP_CATEGORY_STATUS') ||
    			!Configuration::deleteByName('OGP_CATEGORY_TITLE') ||
    			!Configuration::deleteByName('OGP_CATEGORY_DESCRIPTION') ||
    			!Configuration::deleteByName('OGP_CATEGORY_IMAGE') ||
    			!Configuration::deleteByName('OGP_CATEGORY_DEFAULT_IMAGE') ||
    			// manufacturer settings
    			!Configuration::deleteByName('OGP_MANUFACTURER_STATUS') ||
    			!Configuration::deleteByName('OGP_MANUFACTURER_TITLE') ||
    			!Configuration::deleteByName('OGP_MANUFACTURER_DESCRIPTION') ||
    			!Configuration::deleteByName('OGP_MANUFACTURER_IMAGE') ||
    			!Configuration::deleteByName('OGP_MANUFACTURER_DEFAULT_IMAGE') ||
    			// supplier settings
    			!Configuration::deleteByName('OGP_SUPPLIER_STATUS') ||
    			!Configuration::deleteByName('OGP_SUPPLIER_TITLE') ||
    			!Configuration::deleteByName('OGP_SUPPLIER_DESCRIPTION') ||
    			!Configuration::deleteByName('OGP_SUPPLIER_IMAGE') ||
    			!Configuration::deleteByName('OGP_SUPPLIER_DEFAULT_IMAGE') ||
    			// cms
    			!Configuration::deleteByName('OGP_CMS_STATUS') ||
    			!Configuration::deleteByName('OGP_CMS_TITLE') ||
    			!Configuration::deleteByName('OGP_CMS_DESCRIPTION') ||
    			!Configuration::deleteByName('OGP_CMS_DEFAULT_IMAGE') ||
    			// cms category
    			!Configuration::deleteByName('OGP_CMS_CATEGORY_STATUS') ||
    			!Configuration::deleteByName('OGP_CMS_CATEGORY_TITLE') ||
    			!Configuration::deleteByName('OGP_CMS_CATEGORY_DESCRIPTION') ||
    			!Configuration::deleteByName('OGP_CMS_CATEGORY_DEFAULT_IMAGE') ||
    			// google rich snippets
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_STATUS') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_SHOP_TITLE') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_SHOP_DESCRIPTION') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_LOGO') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_ADDRESSES_STATUS') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_GEO_STATUS') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PHONE') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_FACEBOOK_URL') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_TWITTER_URL') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_GOOGLE_PLUS_URL') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_INSTAGRAM_URL') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_YOUTUBE_URL') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_LINKEDIN_URL') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PINTEREST_URL') ||
    			// google rich snippets for product page
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PRODUCT_STATUS') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PRODUCT_TITLE') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PRODUCT_DESCRIPTION') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PRODUCT_IMAGE') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PRODUCT_DEFAULT_IMAGE') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PRODUCT_PRICE_STATUS') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PRODUCT_REFERENCE_STATUS') ||
    			!Configuration::deleteByName('GOOGLE_RICH_SNIPPETS_PRODUCT_MANUFACTURER_STATUS')
			  ){
           return false;
        }

        // return
        return true;
    }




    /*
    * getContent - actually it works like a module index page
    */
    public function getContent()
    {
        $output = null;

        // get controller
        $controller = Tools::getValue('moduleController');

        // baseAdminModuleUrl
        $baseAdminModuleUrl = AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'';

    		// get languages
    		$langs = $this->getLanguagesList();

    		// prepare image directory
    		$imageDir = dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR ;


    		/*
    		* save configuration
    		*/
        if (Tools::getValue('submitSocialMetaData')) {



		    /*
		    * set open graph general options
		    */
		    if (Tools::getValue('options') == 'ogp_general') {

				Configuration::updateValue('OGP_GENERAL_STATUS', (int)Tools::getValue('ogp_general_status') );




		    /*
		    * set facebook options
		    */
		    } else if (Tools::getValue('options') == 'facebook') {

				Configuration::updateValue('SOCIAL_META_DATA_FACEBOOK_APPID', Tools::getValue('facebook_appid') );




		    /*
		    * set twitter cards options
		    */
		    } else if (Tools::getValue('options') == 'twitter_cards') {

				Configuration::updateValue('SOCIAL_META_DATA_TWITTER_CARDS_STATUS', (int)Tools::getValue('twitter_cards_status') );
				Configuration::updateValue('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME', Tools::getValue('twitter_cards_username') );




			/*
			* set open graph homepage options
			*/
			} else if (Tools::getValue('options') == 'ogp_homepage') {

			    // set simple data
			    Configuration::updateValue('OGP_HOMEPAGE_STATUS', (int)Tools::getValue('ogp_homepage_status') );

			    // assign multilanguage data
		      $homepageTitle = array();
          $homepageDescription = array();
          // foreach shop language prepare respective data
          foreach ($langs as $lang) {
              $homepageTitle[$lang['id_lang']] = Tools::getValue('ogp_homepage_title_'.$lang['id_lang']);
              $homepageDescription[$lang['id_lang']] = Tools::getValue('ogp_homepage_description_'.$lang['id_lang']);
          }

          // set multilanguage data
          Configuration::updateValue('OGP_HOMEPAGE_TITLE',$homepageTitle);
          Configuration::updateValue('OGP_HOMEPAGE_DESCRIPTION',$homepageDescription);

  				// save custom image
  				$this->saveCustomImage('ogp_homepage_image');








			/*
			* set open graph product options
			*/
			} else if (Tools::getValue('options') == 'ogp_product') {

		        // set simple data
				Configuration::updateValue('OGP_PRODUCT_STATUS', (int)Tools::getValue('ogp_product_status') );
				Configuration::updateValue('OGP_PRODUCT_IMAGE', Tools::getValue('ogp_product_image'));


				// assign multilanguage data
				$productTitle = array();
	            $productDescription = array();
	            // foreach shop language prepare respective data
	            foreach ($langs as $lang) {
	                $productTitle[$lang['id_lang']] = Tools::getValue('ogp_product_title_'.$lang['id_lang']);
	                $productDescription[$lang['id_lang']] = Tools::getValue('ogp_product_description_'.$lang['id_lang']);
	            }

	            // set multilanguage data
	            Configuration::updateValue('OGP_PRODUCT_TITLE',$productTitle);
	            Configuration::updateValue('OGP_PRODUCT_DESCRIPTION',$productDescription);

				// save custom image
				$this->saveCustomImage('ogp_product_default_image');









			/*
			* set open graph category options
			*/
			} else if (Tools::getValue('options') == 'ogp_category') {

			    // set simple data
			    Configuration::updateValue('OGP_CATEGORY_STATUS', (int)Tools::getValue('ogp_category_status') );
				Configuration::updateValue('OGP_CATEGORY_IMAGE', Tools::getValue('ogp_category_image'));

			    // assign multilanguage data
				$categoryTitle = array();
	            $categoryDescription = array();
	            // foreach shop language prepare respective data
	            foreach ($langs as $lang) {
	                $categoryTitle[$lang['id_lang']] = Tools::getValue('ogp_category_title_'.$lang['id_lang']);
	                $categoryDescription[$lang['id_lang']] = Tools::getValue('ogp_category_description_'.$lang['id_lang']);
	            }

	            // set multilanguage data
	            Configuration::updateValue('OGP_CATEGORY_TITLE',$categoryTitle);
	            Configuration::updateValue('OGP_CATEGORY_DESCRIPTION',$categoryDescription);

				// save custom image
				$this->saveCustomImage('ogp_category_default_image');






			/*
			* set open graph manufacturer options
			*/
			} else if (Tools::getValue('options') == 'ogp_manufacturer') {

			    // set simple data
			    Configuration::updateValue('OGP_MANUFACTURER_STATUS', (int)Tools::getValue('ogp_manufacturer_status') );
				Configuration::updateValue('OGP_MANUFACTURER_IMAGE', Tools::getValue('ogp_manufacturer_image'));

			    // assign multilanguage data
				$manufacturerTitle = array();
	            $manufacturerDescription = array();
	            // foreach shop language prepare respective data
	            foreach ($langs as $lang) {
	                $manufacturerTitle[$lang['id_lang']] = Tools::getValue('ogp_manufacturer_title_'.$lang['id_lang']);
	                $manufacturerDescription[$lang['id_lang']] = Tools::getValue('ogp_manufacturer_description_'.$lang['id_lang']);
	            }

	            // set multilanguage data
	            Configuration::updateValue('OGP_MANUFACTURER_TITLE',$manufacturerTitle);
	            Configuration::updateValue('OGP_MANUFACTURER_DESCRIPTION',$manufacturerDescription);

				// save custom image
				$this->saveCustomImage('ogp_manufacturer_default_image');






			/*
			* set open graph supplier options
			*/
			} else if (Tools::getValue('options') == 'ogp_supplier') {

			    // set simple data
			    Configuration::updateValue('OGP_SUPPLIER_STATUS', (int)Tools::getValue('ogp_supplier_status') );
				Configuration::updateValue('OGP_SUPPLIER_IMAGE', Tools::getValue('ogp_supplier_image'));

			    // assign multilanguage data
				$supplierTitle = array();
	            $supplierDescription = array();
	            // foreach shop language prepare respective data
	            foreach ($langs as $lang) {
	                $supplierTitle[$lang['id_lang']] = Tools::getValue('ogp_supplier_title_'.$lang['id_lang']);
	                $supplierDescription[$lang['id_lang']] = Tools::getValue('ogp_supplier_description_'.$lang['id_lang']);
	            }

	            // set multilanguage data
	            Configuration::updateValue('OGP_SUPPLIER_TITLE',$supplierTitle);
	            Configuration::updateValue('OGP_SUPPLIER_DESCRIPTION',$supplierDescription);

				// save custom image
				$this->saveCustomImage('ogp_supplier_default_image');







			/*
			* set open graph cms options
			*/
			} else if (Tools::getValue('options') == 'ogp_cms') {

			    // set simple data
			    Configuration::updateValue('OGP_CMS_STATUS', (int)Tools::getValue('ogp_cms_status') );

			    // assign multilanguage data
				$cmsTitle = array();
	            $cmsDescription = array();
	            // foreach shop language prepare respective data
	            foreach ($langs as $lang) {
	                $cmsTitle[$lang['id_lang']] = Tools::getValue('ogp_cms_title_'.$lang['id_lang']);
	                $cmsDescription[$lang['id_lang']] = Tools::getValue('ogp_cms_description_'.$lang['id_lang']);
	            }

	            // set multilanguage data
	            Configuration::updateValue('OGP_CMS_TITLE',$cmsTitle);
	            Configuration::updateValue('OGP_CMS_DESCRIPTION',$cmsDescription);


				// save custom image
				$this->saveCustomImage('ogp_cms_default_image');







			/*
			* set open graph cms options
			*/
			} else if (Tools::getValue('options') == 'ogp_cms_category') {

			    // set simple data
			    Configuration::updateValue('OGP_CMS_CATEGORY_STATUS', (int)Tools::getValue('ogp_cms_category_status') );

			    // assign multilanguage data
				$cmscategoryTitle = array();
	            $cmscategoryDescription = array();
	            // foreach shop language prepare respective data
	            foreach ($langs as $lang) {
	                $cmscategoryTitle[$lang['id_lang']] = Tools::getValue('ogp_cms_category_title_'.$lang['id_lang']);
	                $cmscategoryDescription[$lang['id_lang']] = Tools::getValue('ogp_cms_category_description_'.$lang['id_lang']);
	            }

	            // set multilanguage data
	            Configuration::updateValue('OGP_CMS_CATEGORY_TITLE',$cmscategoryTitle);
	            Configuration::updateValue('OGP_CMS_CATEGORY_DESCRIPTION',$cmscategoryDescription);

				// save custom image
				$this->saveCustomImage('ogp_cms_category_default_image');








			/*
			* set google rich snippets options
			*/
			} else if (Tools::getValue('options') == 'google_rich_snippets') {

			    // set simple data
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_STATUS', (int)Tools::getValue('google_rich_snippets_status') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_ADDRESSES_STATUS', (int)Tools::getValue('google_rich_snippets_addresses_status') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_GEO_STATUS', (int)Tools::getValue('google_rich_snippets_geo_status') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PHONE', Tools::getValue('google_rich_snippets_phone') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_FACEBOOK_URL', Tools::getValue('google_rich_snippets_facebook_url') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_TWITTER_URL', Tools::getValue('google_rich_snippets_twitter_url') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_GOOGLE_PLUS_URL', Tools::getValue('google_rich_snippets_google_plus_url') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_INSTAGRAM_URL', Tools::getValue('google_rich_snippets_instagram_url') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_YOUTUBE_URL', Tools::getValue('google_rich_snippets_youtube_url') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_LINKEDIN_URL', Tools::getValue('google_rich_snippets_linkedin_url') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PINTEREST_URL', Tools::getValue('google_rich_snippets_pinterest_url') );


			    // assign multilanguage data
				$googleRichSnippetsShopTitle = array();
	            $googleRichSnippetsShopDescription = array();
	            // foreach shop language prepare respective data
	            foreach ($langs as $lang) {
	                $googleRichSnippetsShopTitle[$lang['id_lang']] = Tools::getValue('google_rich_snippets_shop_title_'.$lang['id_lang']);
	                $googleRichSnippetsShopDescription[$lang['id_lang']] = Tools::getValue('google_rich_snippets_shop_description_'.$lang['id_lang']);
	            }

	            // set multilanguage data
	            Configuration::updateValue('GOOGLE_RICH_SNIPPETS_SHOP_TITLE',$googleRichSnippetsShopTitle);
	            Configuration::updateValue('GOOGLE_RICH_SNIPPETS_SHOP_DESCRIPTION',$googleRichSnippetsShopDescription);

				// save custom image
				$this->saveCustomImage('google_rich_snippets_logo');




			/*
			* set google rich snippets product options
			*/
			} else if (Tools::getValue('options') == 'google_rich_snippets_product') {

			    // set simple data
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_STATUS', (int)Tools::getValue('google_rich_snippets_product_status') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_IMAGE', Tools::getValue('google_rich_snippets_product_image') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_PRICE_STATUS', (int)Tools::getValue('google_rich_snippets_product_price_status') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_REFERENCE_STATUS', Tools::getValue('google_rich_snippets_product_reference_status') );
			    Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_MANUFACTURER_STATUS', Tools::getValue('google_rich_snippets_product_manufacturer_status') );


			    // assign multilanguage data
				$googleRichSnippetsProductTitle = array();
	            $googleRichSnippetsProductDescription = array();
	            // foreach shop language prepare respective data
	            foreach ($langs as $lang) {
	                $googleRichSnippetsProductTitle[$lang['id_lang']] = Tools::getValue('google_rich_snippets_product_title_'.$lang['id_lang']);
	                $googleRichSnippetsProductDescription[$lang['id_lang']] = Tools::getValue('google_rich_snippets_product_description_'.$lang['id_lang']);
	            }

	            // set multilanguage data
	            Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_TITLE',$googleRichSnippetsProductTitle);
	            Configuration::updateValue('GOOGLE_RICH_SNIPPETS_PRODUCT_DESCRIPTION',$googleRichSnippetsProductDescription);

				// save custom image
				$this->saveCustomImage('google_rich_snippets_product_default_image');











		    }

			// redirect with a success message
			Tools::redirectAdmin($baseAdminModuleUrl.'&moduleController=options&data='.Tools::getValue('data').'&options='.Tools::getValue('options').'&saveSuccess=true');

		} // save configuration end





		/*
		* delete image
		*/
		if (Tools::getValue('deleteCustomImage') != ''){
			@unlink($imageDir.Configuration::get(strtoupper(Tools::getValue('deleteCustomImage'))));
			Configuration::updateValue(strtoupper(Tools::getValue('deleteCustomImage')),'');
		}





        /*
        *
        *  options page display
        *
        */
        if ($controller == 'options' || $controller == '') {
            // get options page
            $output = $this->optionsPage();
        }



        // return
        return $this->getHeader().$output.$this->getFooter();
    }







    /*
    * getHeader in admin
    */
    public function getHeader()
    {
        // get controller
        $controller = Tools::getValue('moduleController');

        // baseAdminModuleUrl
        $baseAdminModuleUrl = AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'';

        // if context is in group or all shops mode, then redirect to single shop mode ( we show this page only in single shop mode )
        if (Shop::getContext() != Shop::CONTEXT_SHOP) {
            Tools::redirectAdmin($baseAdminModuleUrl.'&moduleController=options&setShopContext=s-'.$this->context->shop->id);
            die;
        }

        // show current store only if multistore is enabled
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            $displayCurrentStore = true;
        } else {
            $displayCurrentStore = false;
        }

        // assign to smarty
        $this->smarty->assign(array(
                                    'shop'                => $this->context->shop->id,
                                    'storeName'           => $this->context->shop->name ,
                                    'logoSrc'             => _PS_BASE_URL_.$this->_path.'/views/img/logo-white.png',
                                    'moduleImageDir'      => _PS_BASE_URL_.$this->_path.'/views/img/',
                                    'psVersion'           => $this->psVersion(),
                                    'displayCurrentStore' => $displayCurrentStore,
                                    'uri'                 => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.''
                                    ));
        // return
        return $this->display(__FILE__, 'views/templates/admin/header.tpl');
    }







    /*
    * getFooter in Admin
    */
    public function getFooter()
    {
        $this->smarty->assign(array(
                                    'moduleVersion'       => $this->version ,
                                    ));
        return $this->display(__FILE__, 'views/templates/admin/footer.tpl');
    }





    /*
    * optionsPage
    */
    public function optionsPage()
    {
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		// prepare image directory
		$imageDir = dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR ;

		// current url
		$currentUrl = AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;


        // Init Fields form array
		$fields_form = array();
		// get form requested
        $options = Tools::getValue('options');
        // set default if needed
        if ($options==''){ $options = 'ogp_general'; }
        // switch
        switch ($options) {
	        case 'ogp_general':
				/*
				* ogp_general
				*/
			    $fields_form['ogp_general']['form'] = array(
			        'legend' => array(
			            'title' => $this->l('Open Graph Protocol - General settings'),
			            'icon' => 'icon-cogs'
			        ),
			        'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options',
		                ),
			            array(
			                    'type'      =>  'switch',
			                    'label'     => $this->l('Active'),
			                    'name'      => 'ogp_general_status',
			                    'class'     => 't',
			                    'is_bool'   => true,
			                    'values'    => array(
			                                        array(
			                                            'id' => 'active_on',
			                                            'value' => 1,
			                                            'label' => $this->l('Enabled')
			                                        ),
			                                        array(
			                                            'id' => 'active_off',
			                                            'value' => 0,
			                                            'label' => $this->l('Disabled')
			                                        )
			                    ),
			                    'desc'     => $this->l('Activate / Deactivate Open Graph Data globaly.').'<br><br>'.$this->l('Open Graph is required for Facebook and Pinterest sharing and is the backup method of Twitter.').'<br><br>'.$this->l('Be sure to disable any existing Open Graph meta data!').'<br><br>'.$this->l('Do not forget to fill in the settings for the different types of pages below').'<br>'.$this->l('(Home page, Product, Category, etc.)'),
			            ),
			        ),
			        'submit' => array(
			            'title' => $this->l('Save'),
			            'class' => 'button'
			        )
			    );
				break;





        case 'facebook':
				/*
				* ogp_general
				*/
			    $fields_form['facebook']['form'] = array(
			        'legend' => array(
			            'title' => $this->l('Facebook settings'),
			            'icon' => 'icon-cogs'
			        ),
			        'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options',
		                ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('App ID'),
			                    'name'     => 'facebook_appid',
			                    'required' => false,
			                    'desc'     => $this->l('Your App ID. It is required in order to get Open Graph Data for Facebook.'),
			            ),
			        ),
			        'submit' => array(
			            'title' => $this->l('Save'),
			            'class' => 'button'
			        )
			    );
				break;






	        case 'twitter_cards':
				/*
				* ogp_general
				*/
			    $fields_form['twitter_cards']['form'] = array(
			        'legend' => array(
			            'title' => $this->l('Twitter cards'),
			            'icon' => 'icon-cogs'
			        ),
			        'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options',
		                ),
			            array(
			                    'type'      =>  'switch',
			                    'label'     => $this->l('Active'),
			                    'name'      => 'twitter_cards_status',
			                    'class'     => 't',
			                    'is_bool'   => true,
			                    'values'    => array(
			                                        array(
			                                            'id' => 'active_on',
			                                            'value' => 1,
			                                            'label' => $this->l('Enabled')
			                                        ),
			                                        array(
			                                            'id' => 'active_off',
			                                            'value' => 0,
			                                            'label' => $this->l('Disabled')
			                                        )
			                    ),
			                    'desc'     => $this->l('Activate / Deactivate Twitter Cards markup.').'<br>'.$this->l('Twitter falls back to Open Graph meta data, if twitter meta data are absent.').'<br>'.$this->l('In Meta Data Pro, both presentations are made based on the same Open Graph configuration settings in the following sections.'),
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Username'),
			                    'name'     => 'twitter_cards_username',
			                    'required' => false,
			                    'desc'     => $this->l('@username for the website used in the card'),
			            ),
			        ),
			        'submit' => array(
			            'title' => $this->l('Save'),
			            'class' => 'button'
			        )
			    );
				break;








			case 'ogp_homepage':
		    	/*
			    *
			    * ogp_homepage
			    *
			    */
			    /*
				* prepare images
			    */
			    $ogp_homepage_image_thumb = '';
			    if (Configuration::get('OGP_HOMEPAGE_IMAGE')!=''){
				    $ogp_homepage_image_thumb = ImageManager::thumbnail($imageDir.Configuration::get('OGP_HOMEPAGE_IMAGE'), 'ogp_homepage_image_thumb.jpg', 125, 'jpg', true, true);
				    $ogp_homepage_image_size = file_exists($imageDir.Configuration::get('OGP_HOMEPAGE_IMAGE')) ? filesize($imageDir.Configuration::get('OGP_HOMEPAGE_IMAGE')) / 1000 : false;
			    }
			    /*
				* form fields
				*/
				$fields_form['ogp_homepage']['form'] = array(
		            'legend' => array(
		                'title' => $this->l('Home page settings'),
		                'icon' => 'icon-cogs'
		            ),
		            'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options'
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Active'),
		                    'name'      => 'ogp_homepage_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate Open Graph Data for Home page.'),
		                ),
		                array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Home page title'),
			                    'name'     => 'ogp_homepage_title',
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_product_title_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_product_title_shortcodes">{SHOP_NAME}<br></div>',
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Home page description'),
			                    'name'     => 'ogp_homepage_description',
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_product_description_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_product_description_shortcodes">{SHOP_NAME}<br></div>',
			            ),
			            array(
			                'type' =>'file',
			                'label'=>$this->l('Custom image'),
			                'name'=>'ogp_homepage_image',
			                'required' => false,
			                'display_image' => true,
			                'image' => $ogp_homepage_image_thumb,
			                'size' => $ogp_homepage_image_thumb ? $ogp_homepage_image_size : false,
			                'desc'	=> $this->l('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. The minimum image size is 200 x 200 pixels.'),
			                'delete_url' => $currentUrl.'&options=ogp_homepage&deleteCustomImage=ogp_homepage_image',
			            ),
		            ),
		            'submit' => array(
		                'title' => $this->l('Save'),
		                'class' => 'button'
		            )
		        );
		        break;





		    case 'ogp_product':
		    	/*
			    * ogp_product
			    */
			    /*
				* prepare images
			    */
			    $ogp_product_default_image_thumb = '';
			    if (Configuration::get('OGP_PRODUCT_DEFAULT_IMAGE')!=''){
				    $ogp_product_default_image_thumb = ImageManager::thumbnail($imageDir.Configuration::get('OGP_PRODUCT_DEFAULT_IMAGE'), 'ogp_product_default_image_thumb.jpg', 125, 'jpg', true, true);
				    $ogp_product_default_image_size = file_exists($imageDir.Configuration::get('OGP_PRODUCT_DEFAULT_IMAGE')) ? filesize($imageDir.Configuration::get('OGP_PRODUCT_DEFAULT_IMAGE')) / 1000 : false;
			    }
			    /*
				* form fields
				*/
				$fields_form['ogp_product']['form'] = array(
		            'legend' => array(
		                'title' => $this->l('Product settings'),
		                'icon' => 'icon-cogs'
		            ),
		            'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options'
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Active'),
		                    'name'      => 'ogp_product_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate Open Graph Data for products.'),
		                ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Product title'),
			                    'name'     => 'ogp_product_title',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_product_title_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_product_title_shortcodes">{SHOP_NAME}<br>{PRODUCT_NAME}<br>{PRODUCT_DESCRIPTION}<br>{PRODUCT_DESCRIPTION_SHORT}<br>{PRODUCT_ID}<br>{PRODUCT_REF}<br>{PRODUCT_PRICE}<br>{PRODUCT_URL}<br>{PRODUCT_MANUFACTURER}<br>{PRODUCT_SUPPLIER}</div>',
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Product description'),
			                    'name'     => 'ogp_product_description',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_product_description_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_product_description_shortcodes">{SHOP_NAME}<br>{PRODUCT_NAME}<br>{PRODUCT_DESCRIPTION}<br>{PRODUCT_DESCRIPTION_SHORT}<br>{PRODUCT_ID}<br>{PRODUCT_REF}<br>{PRODUCT_PRICE}<br>{PRODUCT_URL}<br>{PRODUCT_MANUFACTURER}<br>{PRODUCT_SUPPLIER}</div>',
			            ),
			            array(
			                    'type' => 'select',
			                    'label' => $this->l('Product image size'),
			                    'name' => 'ogp_product_image',
			                    'options' => array(
			                        'query' => ImageType::getImagesTypes(),
			                        'id' => 'id_image_type',
			                        'name' => 'name'
			                    ),
			                    'desc'	=> $this->l('Choose the image size for products'),
			            ),
			            array(
			                'type' =>'file',
			                'label'=>$this->l('Default image'),
			                'name'=>'ogp_product_default_image',
			                'required' => false,
			                'display_image' => true,
			                'image' => $ogp_product_default_image_thumb,
			                'size' => $ogp_product_default_image_thumb ? $ogp_product_default_image_size : false,
			                'desc'	=> $this->l('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. The minimum image size is 200 x 200 pixels.'),
			                'delete_url' => $currentUrl.'&options=ogp_product&deleteCustomImage=ogp_product_default_image',
			            ),
		            ),
		            'submit' => array(
		                'title' => $this->l('Save'),
		                'class' => 'button'
		            )
		        );
		        break;






			case 'ogp_category':
		    	/*
			    * ogp_category
			    */
			    /*
				  * prepare images
			    */
			    $ogp_category_default_image_thumb = '';
			    if (Configuration::get('OGP_CATEGORY_DEFAULT_IMAGE')!=''){
				    $ogp_category_default_image_thumb = ImageManager::thumbnail($imageDir.Configuration::get('OGP_CATEGORY_DEFAULT_IMAGE'), 'ogp_category_default_image_thumb.jpg', 125, 'jpg', true, true);
				    $ogp_category_default_image_size = file_exists($imageDir.Configuration::get('OGP_CATEGORY_DEFAULT_IMAGE')) ? filesize($imageDir.Configuration::get('OGP_CATEGORY_DEFAULT_IMAGE')) / 1000 : false;
			    }
			    /*
				* form fields
				*/
				$fields_form['ogp_category']['form'] = array(
		            'legend' => array(
		                'title' => $this->l('Category settings'),
		                'icon' => 'icon-cogs'
		            ),
		            'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options'
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Active'),
		                    'name'      => 'ogp_category_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate Open Graph Data for categories.'),
		                ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Category title'),
			                    'name'     => 'ogp_category_title',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_category_title_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_category_title_shortcodes">{SHOP_NAME}<br>{CATEGORY_NAME}<br>{CATEGORY_DESCRIPTION}<br>{CATEGORY_META_TITLE}<br>{CATEGORY_META_DESCRIPTION}<br>{CATEGORY_URL}</div>',
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Category description'),
			                    'name'     => 'ogp_category_description',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_category_description_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_category_description_shortcodes">{SHOP_NAME}<br>{CATEGORY_NAME}<br>{CATEGORY_DESCRIPTION}<br>{CATEGORY_META_TITLE}<br>{CATEGORY_META_DESCRIPTION}<br>{CATEGORY_URL}</div>',
			            ),
			            array(
			                    'type' => 'select',
			                    'label' => $this->l('Category image size'),
			                    'name' => 'ogp_category_image',
			                    'options' => array(
			                        'query' => ImageType::getImagesTypes(),
			                        'id' => 'id_image_type',
			                        'name' => 'name'
			                    ),
			                    'desc'	=> $this->l('Choose the image size for categories'),
			            ),
			            array(
			                'type' =>'file',
			                'label'=>$this->l('Default image'),
			                'name'=>'ogp_category_default_image',
			                'required' => false,
			                'display_image' => true,
			                'image' => $ogp_category_default_image_thumb,
			                'size' => $ogp_category_default_image_thumb ? $ogp_category_default_image_size : false,
			                'desc'	=> $this->l('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. The minimum image size is 200 x 200 pixels.'),
			                'delete_url' => $currentUrl.'&options=ogp_category&deleteCustomImage=ogp_category_default_image',
			            ),
		            ),
		            'submit' => array(
		                'title' => $this->l('Save'),
		                'class' => 'button'
		            )
		        );
		        break;






			case 'ogp_manufacturer':
		    	/*
			    * ogp_manufacturer
			    */
			    /*
				* prepare images
			    */
			    $ogp_manufacturer_default_image_thumb = '';
			    if (Configuration::get('OGP_MANUFACTURER_DEFAULT_IMAGE')!=''){
				    $ogp_manufacturer_default_image_thumb = ImageManager::thumbnail($imageDir.Configuration::get('OGP_MANUFACTURER_DEFAULT_IMAGE'), 'ogp_manufacturer_default_image_thumb.jpg', 125, 'jpg', true, true);
				    $ogp_manufacturer_default_image_size = file_exists($imageDir.Configuration::get('OGP_MANUFACTURER_DEFAULT_IMAGE')) ? filesize($imageDir.Configuration::get('OGP_MANUFACTURER_DEFAULT_IMAGE')) / 1000 : false;
			    }
			    /*
				* form fields
				*/
				$fields_form['ogp_manufacturer']['form'] = array(
		            'legend' => array(
		                'title' => $this->l('Manufacturer settings'),
		                'icon' => 'icon-cogs'
		            ),
		            'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options'
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Active'),
		                    'name'      => 'ogp_manufacturer_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate Open Graph Data for manufacturers.'),
		                ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Manufacturer title'),
			                    'name'     => 'ogp_manufacturer_title',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_manufacturer_title_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_manufacturer_title_shortcodes">{SHOP_NAME}<br>{MANUFACTURER_NAME}<br>{MANUFACTURER_SHORT_DESCRIPTION}<br>{MANUFACTURER_DESCRIPTION}<br>{MANUFACTURER_META_TITLE}<br>{MANUFACTURER_META_DESCRIPTION}<br>{MANUFACTURER_URL}</div>',
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Manufacturer description'),
			                    'name'     => 'ogp_manufacturer_description',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_manufacturer_description_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_manufacturer_description_shortcodes">{SHOP_NAME}<br>{MANUFACTURER_NAME}<br>{MANUFACTURER_SHORT_DESCRIPTION}<br>{MANUFACTURER_DESCRIPTION}<br>{MANUFACTURER_META_TITLE}<br>{MANUFACTURER_META_DESCRIPTION}<br>{MANUFACTURER_URL}</div>',
			            ),
			            array(
			                    'type' => 'select',
			                    'label' => $this->l('Manufacturer image size'),
			                    'name' => 'ogp_manufacturer_image',
			                    'options' => array(
			                        'query' => ImageType::getImagesTypes(),
			                        'id' => 'id_image_type',
			                        'name' => 'name'
			                    ),
			                    'desc'	=> $this->l('Choose the image size for manufacturers'),
			            ),
			            array(
			                'type' =>'file',
			                'label'=>$this->l('Default image'),
			                'name'=>'ogp_manufacturer_default_image',
			                'required' => false,
			                'display_image' => true,
			                'image' => $ogp_manufacturer_default_image_thumb,
			                'size' => $ogp_manufacturer_default_image_thumb ? $ogp_manufacturer_default_image_size : false,
			                'desc'	=> $this->l('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. The minimum image size is 200 x 200 pixels.'),
			                'delete_url' => $currentUrl.'&options=ogp_category&deleteCustomImage=ogp_manufacturer_default_image',
			            ),
		            ),
		            'submit' => array(
		                'title' => $this->l('Save'),
		                'class' => 'button'
		            )
		        );
		        break;




			case 'ogp_supplier':
		    	/*
			    * ogp_supplier
			    */
			    /*
				* prepare images
			    */
			    $ogp_supplier_default_image_thumb = '';
			    if (Configuration::get('OGP_SUPPLIER_DEFAULT_IMAGE')!=''){
				    $ogp_supplier_default_image_thumb = ImageManager::thumbnail($imageDir.Configuration::get('OGP_SUPPLIER_DEFAULT_IMAGE'), 'ogp_supplier_default_image_thumb.jpg', 125, 'jpg', true, true);
				    $ogp_supplier_default_image_size = file_exists($imageDir.Configuration::get('OGP_SUPPLIER_DEFAULT_IMAGE')) ? filesize($imageDir.Configuration::get('OGP_SUPPLIER_DEFAULT_IMAGE')) / 1000 : false;
			    }
			    /*
				* form fields
				*/
				$fields_form['ogp_supplier']['form'] = array(
		            'legend' => array(
		                'title' => $this->l('Supplier settings'),
		                'icon' => 'icon-cogs'
		            ),
		            'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options'
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Active'),
		                    'name'      => 'ogp_supplier_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate Open Graph Data for suppliers.'),
		                ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Supplier title'),
			                    'name'     => 'ogp_supplier_title',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_supplier_title_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_supplier_title_shortcodes">{SHOP_NAME}<br>{SUPPLIER_NAME}<br>{SUPPLIER_DESCRIPTION}<br>{SUPPLIER_META_TITLE}<br>{SUPPLIER_META_DESCRIPTION}<br>{SUPPLIER_URL}</div>',
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Supplier description'),
			                    'name'     => 'ogp_supplier_description',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_supplier_description_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_supplier_description_shortcodes">{SHOP_NAME}<br>{SUPPLIER_NAME}<br>{SUPPLIER_DESCRIPTION}<br>{SUPPLIER_META_TITLE}<br>{SUPPLIER_META_DESCRIPTION}<br>{SUPPLIER_URL}</div>',
			            ),
			            array(
			                    'type' => 'select',
			                    'label' => $this->l('Suppliers image size'),
			                    'name' => 'ogp_supplier_image',
			                    'options' => array(
			                        'query' => ImageType::getImagesTypes(),
			                        'id' => 'id_image_type',
			                        'name' => 'name'
			                    ),
			                    'desc'	=> $this->l('Choose the image size for suppliers'),
			            ),
			            array(
			                'type' =>'file',
			                'label'=>$this->l('Default image'),
			                'name'=>'ogp_supplier_default_image',
			                'required' => false,
			                'display_image' => true,
			                'image' => $ogp_supplier_default_image_thumb,
			                'size' => $ogp_supplier_default_image_thumb ? $ogp_supplier_default_image_size : false,
			                'desc'	=> $this->l('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. The minimum image size is 200 x 200 pixels.'),
			                'delete_url' => $currentUrl.'&options=ogp_category&deleteCustomImage=ogp_supplier_default_image',
			            ),
		            ),
		            'submit' => array(
		                'title' => $this->l('Save'),
		                'class' => 'button'
		            )
		        );
		        break;




			case 'ogp_cms':
		    	/*
			    * ogp_cms
			    */
			    /*
				* prepare images
			    */
			    $ogp_cms_default_image_thumb = '';
			    if (Configuration::get('OGP_CMS_DEFAULT_IMAGE')!=''){
				    $ogp_cms_default_image_thumb = ImageManager::thumbnail($imageDir.Configuration::get('OGP_CMS_DEFAULT_IMAGE'), 'ogp_cms_default_image_thumb.jpg', 125, 'jpg', true, true);
				    $ogp_cms_default_image_size = file_exists($imageDir.Configuration::get('OGP_CMS_DEFAULT_IMAGE')) ? filesize($imageDir.Configuration::get('OGP_CMS_DEFAULT_IMAGE')) / 1000 : false;
			    }
			    /*
				* form fields
				*/
				$fields_form['ogp_cms']['form'] = array(
		            'legend' => array(
		                'title' => $this->l('CMS settings'),
		                'icon' => 'icon-cogs'
		            ),
		            'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options'
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Active'),
		                    'name'      => 'ogp_cms_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate Open Graph Data for CMS pages.'),
		                ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('CMS pages title'),
			                    'name'     => 'ogp_cms_title',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_cms_title_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_cms_title_shortcodes">{SHOP_NAME}<br>{CMS_META_TITLE}<br>{CMS_META_DESCRIPTION}<br>{CMS_PARENT_CATEGORY}<br>{CMS_URL}</div>',
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('CMS pages description'),
			                    'name'     => 'ogp_cms_description',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_cms_description_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_cms_description_shortcodes">{SHOP_NAME}<br>{CMS_META_TITLE}<br>{CMS_META_DESCRIPTION}<br>{CMS_PARENT_CATEGORY}<br>{CMS_URL}</div>',
			            ),
			            array(
			                'type' =>'file',
			                'label'=>$this->l('Default image'),
			                'name'=>'ogp_cms_default_image',
			                'required' => false,
			                'display_image' => true,
			                'image' => $ogp_cms_default_image_thumb,
			                'size' => $ogp_cms_default_image_thumb ? $ogp_cms_default_image_size : false,
			                'desc'	=> $this->l('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. The minimum image size is 200 x 200 pixels.'),
			                'delete_url' => $currentUrl.'&options=ogp_cms&deleteCustomImage=ogp_cms_default_image',
			            ),
		            ),
		            'submit' => array(
		                'title' => $this->l('Save'),
		                'class' => 'button'
		            )
		        );
		        break;




				case 'ogp_cms_category':
		    	/*
			    * ogp_cms_category
			    */

			    /*
				* prepare images
			    */
			    $ogp_cms_category_default_image_thumb = '';
			    if (Configuration::get('OGP_CMS_CATEGORY_DEFAULT_IMAGE')!=''){
				    $ogp_cms_category_default_image_thumb = ImageManager::thumbnail($imageDir.Configuration::get('OGP_CMS_CATEGORY_DEFAULT_IMAGE'), 'ogp_cms_category_default_image_thumb.jpg', 125, 'jpg', true, true);
				    $ogp_cms_category_default_image_size = file_exists($imageDir.Configuration::get('OGP_CMS_CATEGORY_DEFAULT_IMAGE')) ? filesize($imageDir.Configuration::get('OGP_CMS_CATEGORY_DEFAULT_IMAGE')) / 1000 : false;
			    }
			    /*
				* form fields
				*/
				$fields_form['ogp_cms_category']['form'] = array(
		            'legend' => array(
		                'title' => $this->l('CMS category settings'),
		                'icon' => 'icon-cogs'
		            ),
		            'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options'
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Active'),
		                    'name'      => 'ogp_cms_category_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate Open Graph Data for CMS category pages.'),
		                ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('CMS category pages title'),
			                    'name'     => 'ogp_cms_category_title',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_cms_category_title_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_cms_category_title_shortcodes">{SHOP_NAME}<br>{CMS_CATEGORY_NAME}<br>{CMS_CATEGORY_DESCRIPTION}<br>{CMS_CATEGORY_META_TITLE}<br>{CMS_CATEGORY_META_DESCRIPTION}<br>{CMS_CATEGORY_PARENT_CATEGORY}<br>{CMS_CATEGORY_URL}</div>',
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('CMS category pages description'),
			                    'name'     => 'ogp_cms_category_description',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#ogp_cms_category_description_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="ogp_cms_category_description_shortcodes">{SHOP_NAME}<br>{CMS_CATEGORY_NAME}<br>{CMS_CATEGORY_DESCRIPTION}<br>{CMS_CATEGORY_META_TITLE}<br>{CMS_CATEGORY_META_DESCRIPTION}<br>{CMS_CATEGORY_PARENT_CATEGORY}<br>{CMS_CATEGORY_URL}</div>',
			            ),
			            array(
			                'type' =>'file',
			                'label'=>$this->l('Default image'),
			                'name'=>'ogp_cms_category_default_image',
			                'required' => false,
			                'display_image' => true,
			                'image' => $ogp_cms_category_default_image_thumb,
			                'size' => $ogp_cms_category_default_image_thumb ? $ogp_cms_category_default_image_size : false,
			                'desc'	=> $this->l('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. The minimum image size is 200 x 200 pixels.'),
			                'delete_url' => $currentUrl.'&options=ogp_cms_category&deleteCustomImage=ogp_cms_category_default_image',
			            ),
		            ),
		            'submit' => array(
		                'title' => $this->l('Save'),
		                'class' => 'button'
		            )
		        );
		        break;





			case 'google_rich_snippets':
		    	/*
			    *
			    * google_rich_snippets
			    *
			    */
			    /*
				* prepare images
			    */
			    $google_rich_snippets_logo_thumb = '';
			    if (Configuration::get('GOOGLE_RICH_SNIPPETS_LOGO')!=''){
				    $google_rich_snippets_logo_thumb = ImageManager::thumbnail($imageDir.Configuration::get('GOOGLE_RICH_SNIPPETS_LOGO'), 'google_rich_snippets_logo_thumb.jpg', 125, 'jpg', true, true);
				    $google_rich_snippets_logo_size = file_exists($imageDir.Configuration::get('GOOGLE_RICH_SNIPPETS_LOGO')) ? filesize($imageDir.Configuration::get('GOOGLE_RICH_SNIPPETS_LOGO')) / 1000 : false;
			    }
			    /*
				* form fields
				*/
				$fields_form['google_rich_snippets']['form'] = array(
		            'legend' => array(
		                'title' => $this->l('Google Rich Snippets'),
		                'icon' => 'icon-cogs'
		            ),
		            'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options'
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Active'),
		                    'name'      => 'google_rich_snippets_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate Google Rich Snippets.'),
		                ),
		                array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Shop title'),
			                    'name'     => 'google_rich_snippets_shop_title',
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#google_rich_snippets_shop_title_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="google_rich_snippets_shop_title_shortcodes">{SHOP_NAME}<br></div>',
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Shop description'),
			                    'name'     => 'google_rich_snippets_shop_description',
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#google_rich_snippets_shop_description_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="google_rich_snippets_shop_description_shortcodes">{SHOP_NAME}<br></div>',
			            ),
			            array(
			                'type' =>'file',
			                'label'=>$this->l('Shop logo'),
			                'name'=>'google_rich_snippets_logo',
			                'required' => false,
			                'display_image' => true,
			                'image' => $google_rich_snippets_logo_thumb,
			                'size' => $google_rich_snippets_logo_thumb ? $google_rich_snippets_logo_size : false,
			                'desc'	=> $this->l('Use images that are at least 160 x 90 pixels.'),
			                'delete_url' => $currentUrl.'&options=google_rich_snippets&deleteCustomImage=google_rich_snippets_logo',
			            ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Enable addresses'),
		                    'name'      => 'google_rich_snippets_addresses_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Enable / Disable addresses. Your stores addresses will be included in the Rich Snippets.').'<br>'.$this->l('Available for Prestashop 1.7'),
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Enable geo data'),
		                    'name'      => 'google_rich_snippets_geo_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Enable / Disable geo data. Your stores geo data will be included in the Rich Snippets.').'<br>'.$this->l('Available for Prestashop 1.7'),
		                ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Contact phone'),
			                    'name'     => 'google_rich_snippets_phone',
			                    'required' => false,
			                    'desc'     => $this->l('Your shop\'s contact phone'),
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Facebook'),
			                    'name'     => 'google_rich_snippets_facebook_url',
			                    'required' => false,
			                    'desc'     => $this->l('Your shop\'s Facebook url'),
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Twitter'),
			                    'name'     => 'google_rich_snippets_twitter_url',
			                    'required' => false,
			                    'desc'     => $this->l('Your shop\'s Twitter url'),
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Google Plus'),
			                    'name'     => 'google_rich_snippets_google_plus_url',
			                    'required' => false,
			                    'desc'     => $this->l('Your shop\'s Google Plus url'),
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Instagram'),
			                    'name'     => 'google_rich_snippets_instagram_url',
			                    'required' => false,
			                    'desc'     => $this->l('Your shop\'s Instagram url'),
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Youtube'),
			                    'name'     => 'google_rich_snippets_youtube_url',
			                    'required' => false,
			                    'desc'     => $this->l('Your shop\'s Youtube url'),
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Linkedin'),
			                    'name'     => 'google_rich_snippets_linkedin_url',
			                    'required' => false,
			                    'desc'     => $this->l('Your shop\'s Linkedin url'),
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Pinterest'),
			                    'name'     => 'google_rich_snippets_pinterest_url',
			                    'required' => false,
			                    'desc'     => $this->l('Your shop\'s Pinterest url'),
			            ),
		            ),
		            'submit' => array(
		                'title' => $this->l('Save'),
		                'class' => 'button'
		            )
		        );
		        break;







		    case 'google_rich_snippets_product':
		    	/*
			    * google_rich_snippets_product
			    */
			    /*
				* prepare images
			    */
			    $google_rich_snippets_product_default_image_thumb = '';
			    if (Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_DEFAULT_IMAGE')!=''){
				    $google_rich_snippets_product_default_image_thumb = ImageManager::thumbnail($imageDir.Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_DEFAULT_IMAGE'), 'google_rich_snippets_product_default_image_thumb.jpg', 125, 'jpg', true, true);
				    $google_rich_snippets_product_default_image_size = file_exists($imageDir.Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_DEFAULT_IMAGE')) ? filesize($imageDir.Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_DEFAULT_IMAGE')) / 1000 : false;
			    }
			    /*
				* form fields
				*/
				$fields_form['google_rich_snippets_product']['form'] = array(
		            'legend' => array(
		                'title' => $this->l('Product Rich Snippets'),
		                'icon' => 'icon-cogs'
		            ),
		            'input' => array(
		                array(
		                    'type'      => 'hidden',
		                    'name'      => 'options'
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Active'),
		                    'name'      => 'google_rich_snippets_product_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate Rich Snippets for products.'),
		                ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Product title'),
			                    'name'     => 'google_rich_snippets_product_title',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#google_rich_snippets_product_title_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="google_rich_snippets_product_title_shortcodes">{SHOP_NAME}<br>{PRODUCT_NAME}<br>{PRODUCT_DESCRIPTION}<br>{PRODUCT_DESCRIPTION_SHORT}<br>{PRODUCT_ID}<br>{PRODUCT_REF}<br>{PRODUCT_PRICE}<br>{PRODUCT_URL}<br>{PRODUCT_MANUFACTURER}<br>{PRODUCT_SUPPLIER}</div>',
			            ),
			            array(
			                    'type'     => 'text',
			                    'label'    => $this->l('Product description'),
			                    'name'     => 'google_rich_snippets_product_description',
			                    'required' => false,
			                    'lang'     => true,
			                    'desc'     => $this->l('Available shortcodes: ').'<a data-toggle="collapse" href="#google_rich_snippets_product_description_shortcodes" aria-expanded="false">'.$this->l('Show / Hide').'</a><div class="collapse" id="google_rich_snippets_product_description_shortcodes">{SHOP_NAME}<br>{PRODUCT_NAME}<br>{PRODUCT_DESCRIPTION}<br>{PRODUCT_DESCRIPTION_SHORT}<br>{PRODUCT_ID}<br>{PRODUCT_REF}<br>{PRODUCT_PRICE}<br>{PRODUCT_URL}<br>{PRODUCT_MANUFACTURER}<br>{PRODUCT_SUPPLIER}</div>',
			            ),
			            array(
			                    'type' => 'select',
			                    'label' => $this->l('Product image size'),
			                    'name' => 'google_rich_snippets_product_image',
			                    'options' => array(
			                        'query' => ImageType::getImagesTypes(),
			                        'id' => 'id_image_type',
			                        'name' => 'name'
			                    ),
			                    'desc'	=> $this->l('Choose the image size for products'),
			            ),
			            array(
			                'type' =>'file',
			                'label'=>$this->l('Default image'),
			                'name'=>'google_rich_snippets_product_default_image',
			                'required' => false,
			                'display_image' => true,
			                'image' => $google_rich_snippets_product_default_image_thumb,
			                'size' => $google_rich_snippets_product_default_image_thumb ? $google_rich_snippets_product_default_image_size : false,
			                'desc'	=> $this->l('Use images that are at least 1200 x 630 pixels for the best display on high resolution devices. At the minimum, you should use images that are 600 x 315 pixels to display link page posts with larger images. The minimum image size is 200 x 200 pixels.'),
			                'delete_url' => $currentUrl.'&options=google_rich_snippets_product&deleteCustomImage=google_rich_snippets_product_default_image',
			            ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Enable price'),
		                    'name'      => 'google_rich_snippets_product_price_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate price Rich Snippet for products.'),
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Enable Reference'),
		                    'name'      => 'google_rich_snippets_product_reference_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate reference Rich Snippet for products.'),
		                ),
		                array(
		                    'type'      =>  'switch',
		                    'label'     => $this->l('Enable manufacturer'),
		                    'name'      => 'google_rich_snippets_product_manufacturer_status',
		                    'class'     => 't',
		                    'is_bool'   => true,
		                    'values'    => array(
		                                        array(
		                                            'id' => 'active_on',
		                                            'value' => 1,
		                                            'label' => $this->l('Enabled')
		                                        ),
		                                        array(
		                                            'id' => 'active_off',
		                                            'value' => 0,
		                                            'label' => $this->l('Disabled')
		                                        )
		                    ),
		                    'desc'     => $this->l('Activate / Deactivate manufacturer Rich Snippet for products.'),
		                ),
		            ),
		            'submit' => array(
		                'title' => $this->l('Save'),
		                'class' => 'button'
		            )
		        );
		        break;





		}



        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;


        // Toolbar
        $helper->show_toolbar = false;        // false -> remove toolbar
        $helper->toolbar_scroll = false;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submitSocialMetaData';
        $helper->toolbar_btn = array(
                                    'save' => array(
                                                    'desc' => $this->l('Save'),
                                                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                                                    '&token='.Tools::getAdminTokenLite('AdminModules'),
                                                ),
                                    'back' => array(
                                        'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'',
                                        'desc' => $this->l('Back to list')
                                    )
                                );


        $config = $this->getConfiguration();

        $helper->tpl_vars = array(
            'fields_value' =>  $config ,
            'languages' => $this->getLanguagesList(),
            'id_language' => $this->context->language->id,
        );




        // treat errors, if they exist
        $errorsHtml = '';
        if (Tools::getValue('error') == 'emptyFields') {
            $errorsHtml = $this->displayError($this->l('Please fill the required fields!'));
        }

        $this->smarty->assign(array(
                                    'uri'         				=> $currentUrl,
                                    'errorsHtml'  				=> $errorsHtml ,
                                    'current_options_page' 		=> $options,
                                    'saveSuccess'				=> Tools::getValue('saveSuccess'),
	                                'form' 						=> $helper->generateForm($fields_form),
                                    ));
        return $this->display(__FILE__, 'views/templates/admin/options.tpl');

    }



    /*
    * get configuration if it exists
    */
    public function getConfiguration()
    {
	      // assign values
        $settings = array(
	        'options'													                  => Tools::getValue('options') ? Tools::getValue('options') : 'ogp_general',
	        // general settings
    			'ogp_general_status' 										            => Configuration::get('OGP_GENERAL_STATUS'),
    			'ogp_general_appid' 										            => Configuration::get('OGP_GENERAL_APPID'),
	        // facebook
			    'facebook_appid' 											              => Configuration::get('SOCIAL_META_DATA_FACEBOOK_APPID'),
	        // twitter cards
    			'twitter_cards_status' 										          => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_STATUS'),
    			'twitter_cards_username' 									          => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME'),
    			// home page
    			'ogp_homepage_status' 										          => Configuration::get('OGP_HOMEPAGE_STATUS'),
    			'ogp_homepage_image'										            => Configuration::get('OGP_HOMEPAGE_IMAGE'),
    			// product
    			'ogp_product_status' 										            => Configuration::get('OGP_PRODUCT_STATUS'),
    			'ogp_product_image' 										            => Configuration::get('OGP_PRODUCT_IMAGE'),
    			'ogp_product_default_image' 								        => Configuration::get('OGP_PRODUCT_DEFAULT_IMAGE'),
    			// category
    			'ogp_category_status' 										          => Configuration::get('OGP_CATEGORY_STATUS'),
    			'ogp_category_image' 										            => Configuration::get('OGP_CATEGORY_IMAGE'),
    			'ogp_category_default_image' 								        => Configuration::get('OGP_CATEGORY_DEFAULT_IMAGE'),
    			// manufacturer
    			'ogp_manufacturer_status' 									        => Configuration::get('OGP_MANUFACTURER_STATUS'),
    			'ogp_manufacturer_image' 									          => Configuration::get('OGP_MANUFACTURER_IMAGE'),
    			'ogp_manufacturer_default_image' 							      => Configuration::get('OGP_MANUFACTURER_DEFAULT_IMAGE'),
    			// supplier
    			'ogp_supplier_status' 										          => Configuration::get('OGP_SUPPLIER_STATUS'),
    			'ogp_supplier_image' 										            => Configuration::get('OGP_SUPPLIER_IMAGE'),
    			'ogp_supplier_default_image' 								        => Configuration::get('OGP_SUPPLIER_DEFAULT_IMAGE'),
    			// cms
    			'ogp_cms_status' 											              => Configuration::get('OGP_CMS_STATUS'),
    			'ogp_cms_default_image' 									          => Configuration::get('OGP_CMS_DEFAULT_IMAGE'),
    			// cms category
    			'ogp_cms_category_status' 									        => Configuration::get('OGP_CMS_CATEGORY_STATUS'),
    			'ogp_cms_category_default_image' 							      => Configuration::get('OGP_CMS_CATEGORY_DEFAULT_IMAGE'),
    			// google rich snippets
    			'google_rich_snippets_status' 								      => Configuration::get('GOOGLE_RICH_SNIPPETS_STATUS'),
    			'google_rich_snippets_logo' 								        => Configuration::get('GOOGLE_RICH_SNIPPETS_LOGO'),
    			'google_rich_snippets_addresses_status' 					  => Configuration::get('GOOGLE_RICH_SNIPPETS_ADDRESSES_STATUS'),
    			'google_rich_snippets_geo_status' 							    => Configuration::get('GOOGLE_RICH_SNIPPETS_GEO_STATUS'),
    			'google_rich_snippets_phone' 								        => Configuration::get('GOOGLE_RICH_SNIPPETS_PHONE'),
    			'google_rich_snippets_facebook_url' 						    => Configuration::get('GOOGLE_RICH_SNIPPETS_FACEBOOK_URL'),
    			'google_rich_snippets_twitter_url' 							    => Configuration::get('GOOGLE_RICH_SNIPPETS_TWITTER_URL'),
    			'google_rich_snippets_google_plus_url'						  => Configuration::get('GOOGLE_RICH_SNIPPETS_GOOGLE_PLUS_URL'),
    			'google_rich_snippets_instagram_url'						    => Configuration::get('GOOGLE_RICH_SNIPPETS_INSTAGRAM_URL'),
    			'google_rich_snippets_youtube_url'							    => Configuration::get('GOOGLE_RICH_SNIPPETS_YOUTUBE_URL'),
    			'google_rich_snippets_linkedin_url'							    => Configuration::get('GOOGLE_RICH_SNIPPETS_LINKEDIN_URL'),
    			'google_rich_snippets_pinterest_url'						    => Configuration::get('GOOGLE_RICH_SNIPPETS_PINTEREST_URL'),
    			// google rich snippets for product page
    			'google_rich_snippets_product_status' 						  => Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_STATUS'),
    			'google_rich_snippets_product_image' 						    => Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_IMAGE'),
    			'google_rich_snippets_product_default_image' 				=> Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_DEFAULT_IMAGE'),
    			'google_rich_snippets_product_price_status' 				=> Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_PRICE_STATUS'),
    			'google_rich_snippets_product_reference_status' 		=> Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_REFERENCE_STATUS'),
    			'google_rich_snippets_product_manufacturer_status' 	=> Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_MANUFACTURER_STATUS'),
    		);

        // assign multi-language settings
        // for ps 8 and above
        if ($this->psMajorVersion() != '1'){
            // assign values
            $settingsMultiLang = array(
              // home page
              'ogp_homepage_title' 										        => Configuration::getConfigInMultipleLangs('OGP_HOMEPAGE_TITLE'),
              'ogp_homepage_description' 							        => Configuration::getConfigInMultipleLangs('OGP_HOMEPAGE_DESCRIPTION'),
              // product
              'ogp_product_title' 										        => Configuration::getConfigInMultipleLangs('OGP_PRODUCT_TITLE'),
              'ogp_product_description' 							        => Configuration::getConfigInMultipleLangs('OGP_PRODUCT_DESCRIPTION'),
              // category
              'ogp_category_title' 										        => Configuration::getConfigInMultipleLangs('OGP_CATEGORY_TITLE'),
              'ogp_category_description' 							        => Configuration::getConfigInMultipleLangs('OGP_CATEGORY_DESCRIPTION'),
              // manufacturer
              'ogp_manufacturer_title' 								        => Configuration::getConfigInMultipleLangs('OGP_MANUFACTURER_TITLE'),
              'ogp_manufacturer_description' 					        => Configuration::getConfigInMultipleLangs('OGP_MANUFACTURER_DESCRIPTION'),
              // supplier
              'ogp_supplier_title' 										        => Configuration::getConfigInMultipleLangs('OGP_SUPPLIER_TITLE'),
              'ogp_supplier_description' 							        => Configuration::getConfigInMultipleLangs('OGP_SUPPLIER_DESCRIPTION'),
              // cms
              'ogp_cms_title' 											          => Configuration::getConfigInMultipleLangs('OGP_CMS_TITLE'),
              'ogp_cms_description' 										      => Configuration::getConfigInMultipleLangs('OGP_CMS_DESCRIPTION'),
              // cms category
              'ogp_cms_category_title' 									      => Configuration::getConfigInMultipleLangs('OGP_CMS_CATEGORY_TITLE'),
              'ogp_cms_category_description' 								  => Configuration::getConfigInMultipleLangs('OGP_CMS_CATEGORY_DESCRIPTION'),
              // google rich snippets
              'google_rich_snippets_shop_title' 							=> Configuration::getConfigInMultipleLangs('GOOGLE_RICH_SNIPPETS_SHOP_TITLE'),
              'google_rich_snippets_shop_description' 				=> Configuration::getConfigInMultipleLangs('GOOGLE_RICH_SNIPPETS_SHOP_DESCRIPTION'),
              // google rich snippets for product page
              'google_rich_snippets_product_title' 						=> Configuration::getConfigInMultipleLangs('GOOGLE_RICH_SNIPPETS_PRODUCT_TITLE'),
              'google_rich_snippets_product_description' 			=> Configuration::getConfigInMultipleLangs('GOOGLE_RICH_SNIPPETS_PRODUCT_DESCRIPTION'),
            );

        // for ps 1.7
        } else {
            // assign values
            $settingsMultiLang = array(
              // home page
              'ogp_homepage_title' 										        => Configuration::getInt('OGP_HOMEPAGE_TITLE'),
              'ogp_homepage_description' 							        => Configuration::getInt('OGP_HOMEPAGE_DESCRIPTION'),
              // product
              'ogp_product_title' 										        => Configuration::getInt('OGP_PRODUCT_TITLE'),
              'ogp_product_description' 							        => Configuration::getInt('OGP_PRODUCT_DESCRIPTION'),
              // category
              'ogp_category_title' 										        => Configuration::getInt('OGP_CATEGORY_TITLE'),
              'ogp_category_description' 							        => Configuration::getInt('OGP_CATEGORY_DESCRIPTION'),
              // manufacturer
              'ogp_manufacturer_title' 								        => Configuration::getInt('OGP_MANUFACTURER_TITLE'),
              'ogp_manufacturer_description' 					        => Configuration::getInt('OGP_MANUFACTURER_DESCRIPTION'),
              // supplier
              'ogp_supplier_title' 										        => Configuration::getInt('OGP_SUPPLIER_TITLE'),
              'ogp_supplier_description' 							        => Configuration::getInt('OGP_SUPPLIER_DESCRIPTION'),
              // cms
              'ogp_cms_title' 											          => Configuration::getInt('OGP_CMS_TITLE'),
              'ogp_cms_description' 										      => Configuration::getInt('OGP_CMS_DESCRIPTION'),
              // cms category
              'ogp_cms_category_title' 									      => Configuration::getInt('OGP_CMS_CATEGORY_TITLE'),
              'ogp_cms_category_description' 								  => Configuration::getInt('OGP_CMS_CATEGORY_DESCRIPTION'),
              // google rich snippets
              'google_rich_snippets_shop_title' 							=> Configuration::getInt('GOOGLE_RICH_SNIPPETS_SHOP_TITLE'),
              'google_rich_snippets_shop_description' 				=> Configuration::getInt('GOOGLE_RICH_SNIPPETS_SHOP_DESCRIPTION'),
              // google rich snippets for product page
              'google_rich_snippets_product_title' 						=> Configuration::getInt('GOOGLE_RICH_SNIPPETS_PRODUCT_TITLE'),
              'google_rich_snippets_product_description' 			=> Configuration::getInt('GOOGLE_RICH_SNIPPETS_PRODUCT_DESCRIPTION'),
            );
        }

        // return
        return array_merge($settings,$settingsMultiLang);
    }




    /*
    * getLanguagesList
    */
    public function getLanguagesList()
    {
        $languages_list = array();
        $langs = Language::getLanguages(true, $this->context->shop->id);

        foreach ($langs as $lang) {
            if ($lang['id_lang'] == $this->context->language->id) {
                $isDefault = 1;
            } else {
                $isDefault = 0;
            }

            $languages_list[] = array(
                                        'id_lang' => $lang['id_lang'],
                                        'name' => $lang['name'],
                                        'is_default' => $isDefault,
                                        'iso_code' => $lang['iso_code'],
                                    );
        }

        return $languages_list;
    }



    /*
    * getShopsList
    */
    private function getShopsList()
    {
        $shops_list = array();
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $shops_list[] = array( 'id_shop' => $shop['id_shop'], 'name' => $shop['name']);
        }

        // return shops
        return $shops_list;
    }

    /*
    * getShopsListIds
    */
    private function getShopsListIds()
    {
        $shops_list = array();
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $shops_list[] = $shop['id_shop'];
        }

        // return shop ids
        return $shops_list;
    }


    /*
    * psMajorVersion
    */
    public function psMajorVersion()
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        return $exp[0];
    }


    /*
    * psVersion
    */
    public function psVersion()
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        return $exp[0].'.'.$exp[1];
    }


    /*
    * minorVersion
    */
    public static function minorVersion()
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        return $exp[2];
    }


   /*
    * patchVersion
    */
    public static function patchVersion()
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        return $exp[3];
    }


    /*
    * back office includes
    */
    public function hookDisplayBackOfficeHeader($params)
    {
        // add jquery
        $this->context->controller->addJquery();
        // add styling
        $this->context->controller->addCSS($this->_path.'/views/css/socialmetadata-backend.css', 'all');
    }



    /*
    * front office includes
    */
    public function hookHeader()
    {
  	    $shortcodes = array();

    		// prepare image directory
    		$imageDir = _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/img/' ;

    	  /*
    		* home page
    		*/
    	  if (Dispatcher::getInstance()->getController() == 'index'){

      		  // home page shortcodes
      			$shortcodes = array(
      								'{SHOP_NAME}' => Configuration::get('PS_SHOP_NAME'),
      								);

      			// prepare addresses and geo data for google rich snippets
      			$geoData = array();
      			$addressData = array();
            if ($this->psMajorVersion()=='1'){
                if ($this->psVersion() == '1.7') {
          		      if ($this->minorVersion() >= '3') {
          					    $stores = Store::getStores($this->context->language->id);
          		      } else {
          		          $stores = Store::getStores();
          		      }
                }
            } else if ($this->psMajorVersion()!='1'){
                $stores = Store::getStores($this->context->language->id);
            }
            foreach ($stores as $store){
                if ($store['active']){
                    $state = new State($store['id_state']);
                    $geoData[] = array(
                              'latitude' => $store['latitude'],
                              'longitude' => $store['longitude']
                              );
                    $addressData[] = array(
                              'streetAddress'	=> $store['address1'].($store['address2'] ? ' '.$store['address2'] : ''),
                              'addressLocality' => $store['city'].', '.$state->name,
                              'addressRegion' => $state->iso_code,
                                'postalCode' => $store['postcode'],
                                'addressCountry' => Country::getIsoById($store['id_country'])
                              );
                }
            }

      			// prepare social networks for google rich nsippets
      			$socialUrls = array();
      			if ( Configuration::get('GOOGLE_RICH_SNIPPETS_FACEBOOK_URL') != '' ){ $socialUrls[] = Configuration::get('GOOGLE_RICH_SNIPPETS_FACEBOOK_URL'); }
      			if ( Configuration::get('GOOGLE_RICH_SNIPPETS_TWITTER_URL') != '' ){ $socialUrls[] = Configuration::get('GOOGLE_RICH_SNIPPETS_TWITTER_URL'); }
      			if ( Configuration::get('GOOGLE_RICH_SNIPPETS_GOOGLE_PLUS_URL') != '' ){ $socialUrls[] = Configuration::get('GOOGLE_RICH_SNIPPETS_GOOGLE_PLUS_URL'); }
      			if ( Configuration::get('GOOGLE_RICH_SNIPPETS_INSTAGRAM_URL') != '' ){ $socialUrls[] = Configuration::get('GOOGLE_RICH_SNIPPETS_INSTAGRAM_URL'); }
      			if ( Configuration::get('GOOGLE_RICH_SNIPPETS_YOUTUBE_URL') != '' ){ $socialUrls[] = Configuration::get('GOOGLE_RICH_SNIPPETS_YOUTUBE_URL'); }
      			if ( Configuration::get('GOOGLE_RICH_SNIPPETS_LINKEDIN_URL') != '' ){ $socialUrls[] = Configuration::get('GOOGLE_RICH_SNIPPETS_LINKEDIN_URL'); }
      			if ( Configuration::get('GOOGLE_RICH_SNIPPETS_PINTEREST_URL') != '' ){ $socialUrls[] = Configuration::get('GOOGLE_RICH_SNIPPETS_PINTEREST_URL'); }


    		    // assign to template
    		    $this->smarty->assign(array(
                	        						'shop_name'									              => Configuration::get('PS_SHOP_NAME'),
                	        						'shop_url'									              => $this->context->link->getPageLink(''),
    	                                // facebook
    	                                'facebook_appid'							            => Configuration::get('SOCIAL_META_DATA_FACEBOOK_APPID') ,
    	                                // twitter_cards
                                      'twitter_cards_status'        				    => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_STATUS') ,
                                      'twitter_cards_username'       				    => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME') ,
            						              // open graph protocol
                                      'ogp_general_status'        				      => Configuration::get('OGP_GENERAL_STATUS') ,
                                      'ogp_general_appid'         				      => Configuration::get('OGP_GENERAL_APPID'),
                                      'ogp_homepage_status'  						        => Configuration::get('OGP_HOMEPAGE_STATUS') ,
                                      'ogp_homepage_title' 						          => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_HOMEPAGE_TITLE',$this->context->language->id))), ENT_QUOTES),
                                      'ogp_homepage_description'					      => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_HOMEPAGE_DESCRIPTION',$this->context->language->id))), ENT_QUOTES),
    	                                'logo' 										                => _PS_BASE_URL_.__PS_BASE_URI__.'img/'.Configuration::get('PS_LOGO'),
    	                                'ogp_homepage_image'						          => $imageDir.Configuration::get('OGP_HOMEPAGE_IMAGE'),
    	                                // google rich snippets
                    									'google_rich_snippets_status' 				    => Configuration::get('GOOGLE_RICH_SNIPPETS_STATUS'),
                    									'google_rich_snippets_shop_title'			    => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('GOOGLE_RICH_SNIPPETS_SHOP_TITLE',$this->context->language->id))), ENT_QUOTES),
                    									'google_rich_snippets_shop_description'		=> htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('GOOGLE_RICH_SNIPPETS_SHOP_DESCRIPTION',$this->context->language->id))), ENT_QUOTES),
    	                                'google_rich_snippets_logo'					      => $imageDir.Configuration::get('GOOGLE_RICH_SNIPPETS_LOGO'),
    									                'google_rich_snippets_addresses_status' 	=> Configuration::get('GOOGLE_RICH_SNIPPETS_ADDRESSES_STATUS'),
    	                                'addressData'								              => $addressData,
    									                'google_rich_snippets_geo_status' 			  => Configuration::get('GOOGLE_RICH_SNIPPETS_GEO_STATUS'),
    	                                'geoData'									                => $geoData,
                    									'google_rich_snippets_phone' 				      => Configuration::get('GOOGLE_RICH_SNIPPETS_PHONE'),
                    									'socialURLs' 								              => $socialUrls,
    									));

    		    // return template
    		    return $this->display(__FILE__, 'views/templates/hook/home-meta.tpl');
    		}






    	  /*
    		* product page
    		*/
    	  if (Dispatcher::getInstance()->getController() == 'product'){

            // get price precision
            // for ps 8 and above
            if ($this->psMajorVersion()!='1'){
                $pricePrecision = $this->context->getComputingPrecision();
            // for ps 1.7
            } else {
                $pricePrecision = Configuration::get('PS_PRICE_DISPLAY_PRECISION');
            }

    		    // get product object
    	    	$product = $this->context->controller->getProduct();

      			// get cover image
      			$coverImage = Image::getCover($product->id);
      			// get appropriate image types
      			$imageType = new ImageType(Configuration::get('OGP_PRODUCT_IMAGE'));
      			$googleRichSnippetsImageType = new ImageType(Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_IMAGE'));

      			// product shortcodes
      			$shortcodes = array(
                								'{SHOP_NAME}' => Configuration::get('PS_SHOP_NAME'),
                								'{PRODUCT_NAME}' => $product->name,
                								'{PRODUCT_DESCRIPTION}' => $product->description,
                								'{PRODUCT_DESCRIPTION_SHORT}' => $product->description_short,
                								'{PRODUCT_ID}' => $product->id,
                								'{PRODUCT_REF}' => $product->reference,
                								'{PRODUCT_PRICE}' => $product->getPrice(true,null,Configuration::get('PS_PRICE_DISPLAY_PRECISION')).$this->context->currency->sign,
                								'{PRODUCT_URL}' => $this->context->link->getProductLink($product->id, null, null, null, $this->context->language->id),
                								'{PRODUCT_MANUFACTURER}' => $product->manufacturer_name,
                								'{PRODUCT_SUPLIER}' => $product->supplier_name,
      								         );

      			// assign to template
            $this->smarty->assign(array(
          						            'shop_name'													                    => Configuration::get('PS_SHOP_NAME'),
                                  // facebook
                                  'facebook_appid'											                  => Configuration::get('SOCIAL_META_DATA_FACEBOOK_APPID') ,
                                  // twitter_cards
                                  'twitter_cards_status'      								            => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_STATUS') ,
                                  'twitter_cards_username'    								            => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME') ,
                                  // open graph protocol
                                  'ogp_general_status'       									            => Configuration::get('OGP_GENERAL_STATUS') ,
                                  'ogp_general_appid'         								            => Configuration::get('OGP_GENERAL_APPID'),
                                  'ogp_product_status'  										              => Configuration::get('OGP_PRODUCT_STATUS') ,
                                  'ogp_product_title' 										                => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_PRODUCT_TITLE',$this->context->language->id))), ENT_QUOTES),
                                  'ogp_product_description'									              => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_PRODUCT_DESCRIPTION',$this->context->language->id))), ENT_QUOTES),
                                  'ogp_product_image' 										                => $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], (int)$coverImage['id_image'], $imageType->name),
                                  'ogp_product_has_cover'										              => $product->getCoverWs(),
                                  'ogp_product_default_image'									            => $imageDir.Configuration::get('OGP_PRODUCT_DEFAULT_IMAGE'),
                                  'ogp_product_url' 											                => $this->context->link->getProductLink($product->id, null, null, null, $this->context->language->id),
                                  'manufacturer_name'											                => $product->manufacturer_name,
                                  'sku'														                        => $product->reference,
                                  'price'		 												                      => $product->getPrice(true, null, $pricePrecision),
                                  'price_tax_excl'                                        => $product->getPrice(false, null, $pricePrecision),
                                  'weight'                                                => $product->weight,
                                  'currency' 													                    => $this->context->currency->iso_code,
                                  // google rich snippets for product page
                									'google_rich_snippets_status' 								          => Configuration::get('GOOGLE_RICH_SNIPPETS_STATUS'),
                									'google_rich_snippets_product_status' 						      => Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_STATUS'),
                									'google_rich_snippets_product_title' 						        => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_TITLE',$this->context->language->id))), ENT_QUOTES),
                									'google_rich_snippets_product_description' 					    => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_DESCRIPTION',$this->context->language->id))), ENT_QUOTES),
                									'google_rich_snippets_product_image' 						        => $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], (int)$coverImage['id_image'], $googleRichSnippetsImageType->name),
                									'google_rich_snippets_product_default_image' 				    => $imageDir.Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_DEFAULT_IMAGE'),
                									'google_rich_snippets_product_price_status' 				    => Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_PRICE_STATUS'),
                									'google_rich_snippets_product_reference_status' 			  => Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_REFERENCE_STATUS'),
                									'google_rich_snippets_product_manufacturer_status' 			=> Configuration::get('GOOGLE_RICH_SNIPPETS_PRODUCT_MANUFACTURER_STATUS'),
                                ));

      			// return template
      			return $this->display(__FILE__, 'views/templates/hook/product-meta.tpl');
  	    }






        /*
    		* category page
    		*/
        if (Dispatcher::getInstance()->getController() == 'category' && empty($this->context->controller->module)){

    		    // get category object
    		    $category = $this->context->controller->getCategory();

    		    // get appropriate image type
    			  $imageType = new ImageType(Configuration::get('OGP_CATEGORY_IMAGE'));

    		    // category page shortcodes
    			  $shortcodes = array(
    								'{SHOP_NAME}' => Configuration::get('PS_SHOP_NAME'),
    								'{CATEGORY_NAME}' => $category->name,
    								'{CATEGORY_DESCRIPTION}' => $category->description,
    								'{CATEGORY_META_TITLE}' => $category->meta_title,
    								'{CATEGORY_META_DESCRIPTION}' => $category->meta_description,
    								'{CATEGORY_URL}' => $this->context->link->getCategoryLink($category->id, null, $this->context->language->id),
    								);
    		    // assign to template
    		    $this->smarty->assign(array(
    	        						            'shop_name'							        => Configuration::get('PS_SHOP_NAME'),
    	                                // facebook
    	                                'facebook_appid'					      => Configuration::get('SOCIAL_META_DATA_FACEBOOK_APPID') ,
    	                                // twitter_cards
                                      'twitter_cards_status'      		=> Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_STATUS') ,
                                      'twitter_cards_username'    		=> Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME') ,
                                      // open graph protocol
                                      'ogp_general_status'        		=> Configuration::get('OGP_GENERAL_STATUS') ,
                                      'ogp_general_appid'         		=> Configuration::get('OGP_GENERAL_APPID'),
                                      'ogp_category_status'  				  => Configuration::get('OGP_CATEGORY_STATUS') ,
                                      'ogp_category_title' 				    => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_CATEGORY_TITLE',$this->context->language->id))), ENT_QUOTES),
                                      'ogp_category_description'			=> htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_CATEGORY_DESCRIPTION',$this->context->language->id))), ENT_QUOTES),
                                      'ogp_category_image' 				    => $this->context->link->getCatImageLink($category->link_rewrite[$this->context->language->id], $category->id, $imageType->name),
                                      'ogp_category_has_cover'			  => $category->id_image,
    	                                'ogp_category_default_image'		=> $imageDir.Configuration::get('OGP_CATEGORY_DEFAULT_IMAGE'),
    	                                'ogp_category_url' 					    => $this->context->link->getCategoryLink($category->id, null, $this->context->language->id),
    	                                'shop_url'							        => $this->context->link->getPageLink(''),
    	                                ));

    		    // return template
    		    return $this->display(__FILE__, 'views/templates/hook/category-meta.tpl');
    		}




        /*
    		* manufacturer page
    		*/
        if (Dispatcher::getInstance()->getController() == 'manufacturer'){

    		    // get manufacturer object
    		    $manufacturer = new Manufacturer(Tools::getValue('id_manufacturer'), $this->context->language->id);

    		    // get appropriate image type
      			$imageType = new ImageType(Configuration::get('OGP_MANUFACTURER_IMAGE'));

      			// check for $manufacturer logo
      			$ogp_manufacturer_has_cover = false;
      			if (file_exists(_PS_MANU_IMG_DIR_.$manufacturer->id.'.jpg')) {
      				$ogp_manufacturer_has_cover = true;
      			}

      			// set manufacturer image
      			$manufacturerImage = $this->context->link->getManufacturerImageLink($manufacturer->id, $imageType->name);

      		    // category page shortcodes
      			$shortcodes = array(
      								'{SHOP_NAME}' => Configuration::get('PS_SHOP_NAME'),
      								'{MANUFACTURER_NAME}' => $manufacturer->name,
      								'{MANUFACTURER_SHORT_DESCRIPTION}' => $manufacturer->short_description,
      								'{MANUFACTURER_DESCRIPTION}' => $manufacturer->description,
      								'{MANUFACTURER_META_TITLE}' => $manufacturer->meta_title,
      								'{MANUFACTURER_META_DESCRIPTION}' => $manufacturer->meta_description,
      								'{MANUFACTURER_URL}' => $this->context->link->getManufacturerLink($manufacturer),
      								);
    		    // assign to template
    		    $this->smarty->assign(array(
    	        						            'shop_name'							          => Configuration::get('PS_SHOP_NAME'),
    	                                // facebook
    	                                'facebook_appid'					        => Configuration::get('SOCIAL_META_DATA_FACEBOOK_APPID') ,
    	                                // twitter_cards
                                      'twitter_cards_status'      		  => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_STATUS') ,
                                      'twitter_cards_username'    		  => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME') ,
                                      // open graph protocol
                                      'ogp_general_status'        		  => Configuration::get('OGP_GENERAL_STATUS') ,
                                      'ogp_general_appid'         		  => Configuration::get('OGP_GENERAL_APPID'),
                                      'ogp_manufacturer_status'  			  => Configuration::get('OGP_MANUFACTURER_STATUS') ,
                                      'ogp_manufacturer_title' 			    => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_MANUFACTURER_TITLE',$this->context->language->id))), ENT_QUOTES),
                                      'ogp_manufacturer_description'		=> htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_MANUFACTURER_DESCRIPTION',$this->context->language->id))), ENT_QUOTES),
                                      'ogp_manufacturer_image' 			    => $manufacturerImage,
                                      'ogp_manufacturer_has_cover'		  => $ogp_manufacturer_has_cover,
    	                                'ogp_manufacturer_default_image'	=> $imageDir.Configuration::get('OGP_MANUFACTURER_DEFAULT_IMAGE'),
    	                                'ogp_manufacturer_url' 				    => $this->context->link->getManufacturerLink($manufacturer),
    	                                'shop_url'							          => $this->context->link->getPageLink(''),
    	                                ));

    		    // return template
    		    return $this->display(__FILE__, 'views/templates/hook/manufacturer-meta.tpl');
    		}







    	  /*
    		* supplier page
    		*/
    	  if (Dispatcher::getInstance()->getController() == 'supplier'){

    		    // get supplier object
    		    $supplier = new Supplier(Tools::getValue('id_supplier'), $this->context->language->id);
    		    //print_r($supplier);

    		    // get appropriate image type
      			$imageType = new ImageType(Configuration::get('OGP_SUPPLIER_IMAGE'));

      			// check for supplier logo
      			$ogp_supplier_has_cover = false;
      			if (file_exists(_PS_SUPP_IMG_DIR_.$supplier->id.(empty($imageType->name) ? '.jpg' : '-'.$imageType->name.'.jpg'))) {
      				$ogp_supplier_has_cover = true;
      			}

      		    // category page shortcodes
      			$shortcodes = array(
      								'{SHOP_NAME}' => Configuration::get('PS_SHOP_NAME'),
      								'{SUPPLIER_NAME}' => $supplier->name,
      								'{SUPPLIER_DESCRIPTION}' => $supplier->description,
      								'{SUPPLIER_META_TITLE}' => $supplier->meta_title,
      								'{SUPPLIER_META_DESCRIPTION}' => $supplier->meta_description,
      								'{SUPPLIER_URL}' => $this->context->link->getSupplierLink($supplier),
      								);
    		    // assign to template
    		    $this->smarty->assign(array(
    	        						            'shop_name'						        => Configuration::get('PS_SHOP_NAME'),
    	                                // facebook
    	                                'facebook_appid'				      => Configuration::get('SOCIAL_META_DATA_FACEBOOK_APPID') ,
    	                                // twitter_cards
                                      'twitter_cards_status'      	=> Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_STATUS') ,
                                      'twitter_cards_username'    	=> Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME') ,
                                      // open graph protocol
                                      'ogp_general_status'        	=> Configuration::get('OGP_GENERAL_STATUS') ,
                                      'ogp_general_appid'         	=> Configuration::get('OGP_GENERAL_APPID'),
                                      'ogp_supplier_status'  			  => Configuration::get('OGP_SUPPLIER_STATUS') ,
                                      'ogp_supplier_title' 			    => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_SUPPLIER_TITLE',$this->context->language->id))), ENT_QUOTES),
                                      'ogp_supplier_description'		=> htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_SUPPLIER_DESCRIPTION',$this->context->language->id))), ENT_QUOTES),
                                      'ogp_supplier_image' 			    => $this->context->link->getSupplierImageLink($supplier->id, $imageType->name),
                                      'ogp_supplier_has_cover'		  => $ogp_supplier_has_cover,
    	                                'ogp_supplier_default_image'	=> $imageDir.Configuration::get('OGP_SUPPLIER_DEFAULT_IMAGE'),
    	                                'ogp_supplier_url' 				    => $this->context->link->getSupplierLink($supplier),
    	                                'shop_url'						        => $this->context->link->getPageLink(''),
    	                                ));

    		    // return template
    		    return $this->display(__FILE__, 'views/templates/hook/supplier-meta.tpl');
    		}




    	  /*
    		* cms page
    		*/
    	  if (Dispatcher::getInstance()->getController() == 'cms' && Tools::getIsset('id_cms')){

    		    // get cms object
    		    $cms = new CMS(Tools::getValue('id_cms'), $this->context->language->id);
    		    // parent category
    		    $parentCategory = new CMSCategory($cms->id_cms_category);

    		    // category page shortcodes
      			$shortcodes = array(
      								'{SHOP_NAME}' 				=> Configuration::get('PS_SHOP_NAME'),
      								'{CMS_META_TITLE}' 			=> $cms->meta_title,
      								'{CMS_META_DESCRIPTION}' 	=> $cms->meta_description,
      								'{CMS_PARENT_CATEGORY}' 	=> $parentCategory->getName(),
      								'{CMS_URL}' 				=> $this->context->link->getCMSLink($cms),
      								);
    		    // assign to template
    		    $this->smarty->assign(array(
    	        						            'shop_name'						        => Configuration::get('PS_SHOP_NAME'),
    	                                // facebook
    	                                'facebook_appid'				      => Configuration::get('SOCIAL_META_DATA_FACEBOOK_APPID') ,
    	                                // twitter_cards
                                      'twitter_cards_status'      	=> Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_STATUS') ,
                                      'twitter_cards_username'    	=> Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME') ,
                                      // open graph protocol
                                      'ogp_general_status'        	=> Configuration::get('OGP_GENERAL_STATUS') ,
                                      'ogp_general_appid'         	=> Configuration::get('OGP_GENERAL_APPID'),
                                      'ogp_cms_status'  				    => Configuration::get('OGP_CMS_STATUS') ,
                                      'ogp_cms_title' 				      => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_CMS_TITLE',$this->context->language->id))), ENT_QUOTES),
                                      'ogp_cms_description'			    => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_CMS_DESCRIPTION',$this->context->language->id))), ENT_QUOTES),
    	                                'ogp_cms_url' 					      => $this->context->link->getCMSLink($cms),
    	                                'ogp_cms_default_image'			  => $imageDir.Configuration::get('OGP_CMS_DEFAULT_IMAGE'),
    	                                'shop_url'						        => $this->context->link->getPageLink(''),
    	                                ));

    		    // return template
    		    return $this->display(__FILE__, 'views/templates/hook/cms-meta.tpl');
    		}




    	  /*
    		* cms category page
    		*/
    	  if (Dispatcher::getInstance()->getController() == 'cms' && Tools::getIsset('id_cms_category')){

    		    // get cms category object
    		    $cmsCategory = new CMSCategory(Tools::getValue('id_cms_category'), $this->context->language->id);
    		    //print_r($this->context);
    		    // parent category
    		    $parentCategory = new CMSCategory($cmsCategory->id_parent);

    		    // category page shortcodes
    			  $shortcodes = array(
    								'{SHOP_NAME}' 						=> Configuration::get('PS_SHOP_NAME'),
    								'{CMS_CATEGORY_NAME}' 				=> $cmsCategory->name,
    								'{CMS_CATEGORY_DESCRIPTION}' 		=> $cmsCategory->description,
    								'{CMS_CATEGORY_META_TITLE}' 		=> $cmsCategory->meta_title,
    								'{CMS_CATEGORY_META_DESCRIPTION}' 	=> $cmsCategory->meta_description,
    								'{CMS_CATEGORY_PARENT_CATEGORY}' 	=> $parentCategory->getName(),
    								'{CMS_CATEGORY_URL}' 				=> $this->context->link->getCMSCategoryLink($cmsCategory),
    								);
    		    // assign to template
    		    $this->smarty->assign(array(
      	        						          'shop_name'							          => Configuration::get('PS_SHOP_NAME'),
      		                            // facebook
      		                            'facebook_appid'					        => Configuration::get('SOCIAL_META_DATA_FACEBOOK_APPID') ,
      		                            // twitter_cards
    	                                'twitter_cards_status'      		  => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_STATUS') ,
    	                                'twitter_cards_username'    		  => Configuration::get('SOCIAL_META_DATA_TWITTER_CARDS_USERNAME') ,
    	                                // open graph protocol
                                      'ogp_general_status'        		  => Configuration::get('OGP_GENERAL_STATUS') ,
                                      'ogp_general_appid'         		  => Configuration::get('OGP_GENERAL_APPID'),
                                      'ogp_cms_category_status'  			  => Configuration::get('OGP_CMS_CATEGORY_STATUS') ,
                                      'ogp_cms_category_title' 			    => htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_CMS_CATEGORY_TITLE',$this->context->language->id))), ENT_QUOTES),
                                      'ogp_cms_category_description'		=> htmlspecialchars(strip_tags(str_replace(array_keys($shortcodes), array_values($shortcodes), Configuration::get('OGP_CMS_CATEGORY_DESCRIPTION',$this->context->language->id))), ENT_QUOTES),
    	                                'ogp_cms_category_url' 				    => $this->context->link->getCMSCategoryLink($cmsCategory),
    	                                'ogp_cms_category_default_image'	=> $imageDir.Configuration::get('OGP_CMS_CATEGORY_DEFAULT_IMAGE'),
    	                                'shop_url'							          => $this->context->link->getPageLink(''),
    	                                ));

    		    // return template
    		    return $this->display(__FILE__, 'views/templates/hook/cmscategory-meta.tpl');
    		}


    } // end of hook header











  	/*
  	* save configuration image
  	*/
  	public static function saveCustomImage($imageConfigurationName){
    		// if an image is given
    		if (isset($_FILES[$imageConfigurationName]) && isset($_FILES[$imageConfigurationName]['tmp_name']) && !empty($_FILES[$imageConfigurationName]['tmp_name'])) {
  			    // check for a valid upload
  	        if ($error = ImageManager::validateUpload($_FILES[$imageConfigurationName], 4000000)) {
  	            return $error;
  	        } else {
    		        // process uploaded image
    		        // prepare image directory
  				      $imageDir = dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR ;
  		          // get file extension
  	            $ext = substr($_FILES[$imageConfigurationName]['name'], strrpos($_FILES[$imageConfigurationName]['name'], '.') + 1);
  	            // set file names
  	            $file_name = md5($_FILES[$imageConfigurationName]['name']).'_original.'.$ext;
  	            $resized_file_name = md5($_FILES[$imageConfigurationName]['name']).date('U').'.'.$ext;
  				       // move uploaded image to our directory
  	            if (!move_uploaded_file($_FILES[$imageConfigurationName]['tmp_name'], $imageDir.$file_name)) {
  	                return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
  	            } else {
    		            // get image dimensions
    		            $infos = getimagesize($imageDir.$file_name);
          					$x = $infos[0];
          					$y = $infos[1];
    					      // check if image is too big
    		            if ($x > 1500){
      						      // resize image in case it is too big
      			            ImageManager::resize($imageDir.$file_name, $imageDir.$resized_file_name, 1500, (1500*$y)/$x, 'jpg' );
      			            // delete original
      			            @unlink($imageDir.$file_name);
    		            } else {
      			            // rename
      			            rename($imageDir.$file_name, $imageDir.$resized_file_name);
    		            }

  		              // clear old image
  	                if (Configuration::get(strtoupper($imageConfigurationName)) && Configuration::get(strtoupper($imageConfigurationName)) != $resized_file_name) {
  	                    @unlink($imageDir.Configuration::get(strtoupper($imageConfigurationName)));
  	                }

  					         // save image to config
  	                Configuration::updateValue(strtoupper($imageConfigurationName), $resized_file_name );
  	            }
  	        }
  	    }
  	}


    /*
    * End of file
    */
}
