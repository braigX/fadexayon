<?php
/*
* RoyThemes Copyright 2019
* This module is an official part of MODEZ theme.
* RoyThemes Copyright 2019
*/

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

if (!defined('_PS_VERSION_'))
	exit;

class Roy_Levibox extends Module
{
	public function __construct()
	{
		$this->name = 'roy_levibox';
		$this->tab = 'front_office_features';
		$this->version = '2.2';
		$this->author = 'RoyThemes';

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->trans(
				'Roy LeviBox',
				array(),
				'Modules.Roylevibox.Roylevibox'
		);
		$this->description = $this->trans(
				'Levitating box with main shop navigation.',
				array(),
				'Modules.Roylevibox.Roylevibox'
		);
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
	}

	public function install()
	{
        return (parent::install() &&
            $this->registerHook('displayHeader') &&
            Configuration::updateValue('BOX_CART', 1) &&
            Configuration::updateValue('BOX_SEARCH', 1) &&
            Configuration::updateValue('BOX_MENU', 1) &&
            Configuration::updateValue('BOX_MAIL', 1) &&
            Configuration::updateValue('BOX_ACC', 1) &&
            Configuration::updateValue('BOX_ARROW', 1) &&
            Configuration::updateValue('BOX_SEARCH_TAGS','dress,black,cotton') &&
            Configuration::updateValue('BOX_SEARCH_PRODS','1,2,3,4,5,6,7,8') &&
            Configuration::updateValue('BOX_ADMIN_MAIL','example@yourdomain.com') &&
            Configuration::updateValue('BOX_FB_LAN', 'en_US') &&
            Configuration::updateValue('BOX_FACEBOOK_URL', 'https://www.facebook.com/prestashop') &&
            $this->registerHook([
								'displayLeviBox',
								'displaySideMail',
								'displaySideAcc',
								'displaySideSearch',
								'displaySideMenu'
						])
				);
	}

	public function uninstall()
	{
        Configuration::deleteByName('BOX_CART');
        Configuration::deleteByName('BOX_SEARCH');
        Configuration::deleteByName('BOX_MENU');
        Configuration::deleteByName('BOX_MAIL');
        Configuration::deleteByName('BOX_ACC');
        Configuration::deleteByName('BOX_ARROW');
        Configuration::deleteByName('BOX_SEARCH_TAGS');
        Configuration::deleteByName('BOX_SEARCH_PRODS');
        Configuration::deleteByName('BOX_ADMIN_MAIL');
        Configuration::deleteByName('BOX_FB_LAN');
        Configuration::deleteByName('BOX_FACEBOOK_URL');
        $this->_clearCache('roy_levibox.tpl');
		return parent::uninstall();
	}

	public function getContent()
	{
        $output = '';

        global $cookie;
        Context::getContext()->cookie->email;

		if (Tools::isSubmit('submitRoyLevibox'))
        {
                Configuration::updateValue('BOX_CART', (int)(Tools::getValue('box_cart_sw')));
                Configuration::updateValue('BOX_SEARCH', (int)(Tools::getValue('box_search_sw')));
                Configuration::updateValue('BOX_MENU', (int)(Tools::getValue('box_menu_sw')));
                Configuration::updateValue('BOX_MAIL', (int)(Tools::getValue('box_mail_sw')));
                Configuration::updateValue('BOX_ACC', (int)(Tools::getValue('box_acc_sw')));
                Configuration::updateValue('BOX_ARROW', (int)(Tools::getValue('box_arrow_sw')));
                Configuration::updateValue('BOX_FB_LAN', Tools::getValue('box_fb_lan', ''));
                Configuration::updateValue('BOX_ADMIN_MAIL', Tools::getValue('box_admin_mail', ''));
                Configuration::updateValue('BOX_SEARCH_TAGS', Tools::getValue('box_search_tags', ''));
                Configuration::updateValue('BOX_SEARCH_PRODS', Tools::getValue('box_search_prods', ''));
                Configuration::updateValue('BOX_FACEBOOK_URL', Tools::getValue('box_facebook_url', ''));
								$this->_clearCache('roy_levibox.tpl');
								$this->_clearCache('roy_sidemail.tpl');
								$this->_clearCache('roy_sidemenu.tpl');
								$this->_clearCache('roy_sideacc.tpl');
								$this->_clearCache('roy_sidesearch.tpl');
                $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        return $output.$this->renderForm();

    }
    
    public function isUsingNewTranslationSystem()
    {
        return true;
    }


	/**
	* Returns module content
	*
	* @param array $params Parameters
	* @return string Content
	*/
    public function hookHeader($params)
    {
        $this->context->controller->addCSS(($this->_path).'css/contactable.css', 'all');
        $this->context->controller->addJS(($this->_path).'js/roy_levibox.js', 'all');
        $this->context->controller->addJS(($this->_path).'js/jquery.contactable.js', 'all');
    }

	public function hookdisplayLeviBox($params)
	{
        Context::getContext()->cookie->email;
        if (!$this->isCached('roy_levibox.tpl', $this->getCacheId()))
        $this->smarty->assign(
        array(
            'box_cart' => Configuration::get('BOX_CART'),
            'box_search' => Configuration::get('BOX_SEARCH'),
            'box_menu' => Configuration::get('BOX_MENU'),
            'box_mail' => Configuration::get('BOX_MAIL'),
            'box_acc' => Configuration::get('BOX_ACC'),
            'box_arrow' => Configuration::get('BOX_ARROW'),
            'box_admin_mail' => Configuration::get('BOX_ADMIN_MAIL'),
            'box_fb_lan' => Configuration::get('BOX_FB_LAN'),
            'box_facebook_url' => Configuration::get('BOX_FACEBOOK_URL'),
            'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order'
        ));

		return $this->display(__FILE__, 'roy_levibox.tpl', $this->getCacheId());
	}

	public function hookdisplaySideMail($params)
	{
        if (!$this->isCached('roy_sidemail.tpl', $this->getCacheId()))
        $this->smarty->assign(
        array(
            'box_mail' => Configuration::get('BOX_MAIL'),
            'box_admin_mail' => Configuration::get('BOX_ADMIN_MAIL')
        ));
		return $this->display(__FILE__, 'roy_sidemail.tpl', $this->getCacheId());
	}

	public function hookdisplaySideMenu($params)
	{
			return $this->display(__FILE__, 'roy_sidemenu.tpl', $this->getCacheId());
	}
	public function hookdisplaySideAcc($params)
	{

			$logged = $this->context->customer->isLogged();

      if ($logged) {
          $customerName = $this->getTranslator()->trans(
              '%firstname% %lastname%',
              array(
                  '%firstname%' => $this->context->customer->firstname,
                  '%lastname%' => $this->context->customer->lastname,
              ),
              'Modules.Customersignin.Admin'
          );
      } else {
          $customerName = '';
      }

			$link = $this->context->link;

			$my_account_urls = array(
					2 => array(
							'title' => $this->trans('Orders', array(), 'Admin.Global'),
							'url' => $link->getPageLink('history', true),
					),
					3 => array(
							'title' => $this->trans('Credit slips', array(), 'Modules.Customeraccountlinks.Admin'),
							'url' => $link->getPageLink('order-slip', true),
					),
					4 => array(
							'title' => $this->trans('Addresses', array(), 'Shop.Theme.Global'),
							'url' => $link->getPageLink('addresses', true),
					),
					0 => array(
							'title' => $this->trans('Personal info', array(), 'Modules.Customeraccountlinks.Admin'),
							'url' => $link->getPageLink('identity', true),
					),
			);

			if ((int)Configuration::get('PS_ORDER_RETURN')) {
					$my_account_urls[1] = array(
							'title' => $this->trans('Merchandise returns', array(), 'Modules.Customeraccountlinks.Admin'),
							'url' => $link->getPageLink('order-follow', true),
					);
			}

			if (CartRule::isFeatureActive()) {
					$my_account_urls[5] = array(
							'title' => $this->trans('Vouchers', array(), 'Shop.Theme.Customeraccount'),
							'url' => $link->getPageLink('discount', true),
					);
			}

			// Sort Account links base in his index
			ksort($my_account_urls);

			$this->smarty->assign(
				array(
          'logged' => $logged,
          'customerName' => $customerName,
          'logout_url' => $link->getPageLink('index', true, null, 'mylogout'),
          'my_account_url' => $link->getPageLink('my-account', true),
					'my_account_urls' => $my_account_urls,
					'logout_url' => $link->getPageLink('index', true, null, "mylogout"),
			));

			return $this->display(__FILE__, 'roy_sideacc.tpl', $this->getCacheId());
	}

	public function hookdisplaySideSearch($params)
	{
        if (!$this->isCached('roy_sidesearch.tpl', $this->getCacheId()))
        $this->smarty->assign(
        array(
            'box_search_tags' => Configuration::get('BOX_SEARCH_TAGS'),
            'box_search_prods' => Configuration::get('BOX_SEARCH_PRODS')
        ));

				$box_search_tags = Configuration::get('BOX_SEARCH_TAGS');

			  $tags_string="".$box_search_tags."";
        $tags_array=explode(",",$tags_string);
        $this->context->smarty->assign('box_tags_array',$tags_array);

				$enter_id_product = Configuration::get('BOX_SEARCH_PRODS');
        $array_id_product = explode(",", $enter_id_product);
				$prts = Product::getProducts($this->context->language->id, 1, 5000, 'name', 'ASC');

				foreach ($array_id_product as $key => $arr) {
            foreach ($prts as $pr) {
                if ($pr['id_product'] == $arr) {
                    $result[$key]['id_product'] = $arr;
                }
            }
        }

	      if (isset($result)) {
	        foreach ($result as $product) {
	            $product = (new ProductAssembler($this->context))
	                ->assembleProduct($product);
	            $presenterFactory = new ProductPresenterFactory($this->context);
	            $presentationSettings = $presenterFactory->getPresentationSettings();
	            $presenter = new ProductListingPresenter(
	                new ImageRetriever(
	                    $this->context->link
	                ),
	                $this->context->link,
	                new PriceFormatter(),
	                new ProductColorsRetriever(),
	                $this->context->getTranslator()
	            );
	            $template_products[] = $presenter->present(
	                $presentationSettings,
	                $product,
	                $this->context->language
	            );
	        }

	        $this->context->smarty->assign(array(
	            'products' => $template_products
	        ));
	      }

				return $this->display(__FILE__, 'roy_sidesearch.tpl');
	}

	public function renderForm()
	{
        $_fb_languages = array(
            array(
                'id_option' => 'af_ZA',
                'name_option' => 'Afrikaans'
            ),
            array(
                'id_option' => 'sq_AL',
                'name_option' => 'Albanian'
            ),
            array(
                'id_option' => 'ar_AR',
                'name_option' => 'Arabic'
            ),
            array(
                'id_option' => 'hy_AM',
                'name_option' => 'Armenian'
            ),
            array(
                'id_option' => 'ay_BO',
                'name_option' => 'Aymara'
            ),
            array(
                'id_option' => 'az_AZ',
                'name_option' => 'Azeri'
            ),
            array(
                'id_option' => 'eu_ES',
                'name_option' => 'Basque'
            ),
            array(
                'id_option' => 'be_BY',
                'name_option' => 'Belarusian'
            ),
            array(
                'id_option' => 'bn_IN',
                'name_option' => 'Bengali'
            ),
            array(
                'id_option' => 'bs_BA',
                'name_option' => 'Bosnian'
            ),
            array(
                'id_option' => 'bg_BG',
                'name_option' => 'Bulgarian'
            ),
            array(
                'id_option' => 'ca_ES',
                'name_option' => 'Catalan'
            ),
            array(
                'id_option' => 'ck_US',
                'name_option' => 'Cherokee'
            ),
            array(
                'id_option' => 'hr_HR',
                'name_option' => 'Croatian'
            ),
            array(
                'id_option' => 'cs_CZ',
                'name_option' => 'Czech'
            ),
            array(
                'id_option' => 'da_DK',
                'name_option' => 'Danish'
            ),
            array(
                'id_option' => 'nl_NL',
                'name_option' => 'Dutch'
            ),
            array(
                'id_option' => 'nl_BE',
                'name_option' => 'Dutch (Belgi?)'
            ),
            array(
                'id_option' => 'en_PI',
                'name_option' => 'English (Pirate)'
            ),
            array(
                'id_option' => 'en_GB',
                'name_option' => 'English (UK)'
            ),
            array(
                'id_option' => 'en_UD',
                'name_option' => 'English (Upside Down)'
            ),
            array(
                'id_option' => 'en_US',
                'name_option' => 'English (US)'
            ),
            array(
                'id_option' => 'eo_EO',
                'name_option' => 'Esperanto'
            ),
            array(
                'id_option' => 'et_EE',
                'name_option' => 'Estonian'
            ),
            array(
                'id_option' => 'fo_FO',
                'name_option' => 'Faroese'
            ),
            array(
                'id_option' => 'tl_PH',
                'name_option' => 'Filipino'
            ),
            array(
                'id_option' => 'fi_FI',
                'name_option' => 'Finnish'
            ),
            array(
                'id_option' => 'fb_FI',
                'name_option' => 'Finnish (test)'
            ),
            array(
                'id_option' => 'fr_CA',
                'name_option' => 'French (Canada)'
            ),
            array(
                'id_option' => 'fr_FR',
                'name_option' => 'French (France)'
            ),
            array(
                'id_option' => 'gl_ES',
                'name_option' => 'Galician'
            ),
            array(
                'id_option' => 'ka_GE',
                'name_option' => 'Georgian'
            ),
            array(
                'id_option' => 'de_DE',
                'name_option' => 'German'
            ),
            array(
                'id_option' => 'el_GR',
                'name_option' => 'Greek'
            ),
            array(
                'id_option' => 'gn_PY',
                'name_option' => 'Guaran?'
            ),
            array(
                'id_option' => 'gu_IN',
                'name_option' => 'Gujarati'
            ),
            array(
                'id_option' => 'he_IL',
                'name_option' => 'Hebrew'
            ),
            array(
                'id_option' => 'hi_IN',
                'name_option' => 'Hindi'
            ),
            array(
                'id_option' => 'hu_HU',
                'name_option' => 'Hungarian'
            ),
            array(
                'id_option' => 'is_IS',
                'name_option' => 'Icelandic'
            ),
            array(
                'id_option' => 'id_ID',
                'name_option' => 'Indonesian'
            ),
            array(
                'id_option' => 'ga_IE',
                'name_option' => 'Irish'
            ),
            array(
                'id_option' => 'it_IT',
                'name_option' => 'Italian'
            ),
            array(
                'id_option' => 'ja_JP',
                'name_option' => 'Japanese'
            ),
            array(
                'id_option' => 'jv_ID',
                'name_option' => 'Javanese'
            ),
            array(
                'id_option' => 'kn_IN',
                'name_option' => 'Kannada'
            ),
            array(
                'id_option' => 'kk_KZ',
                'name_option' => 'Kazakh'
            ),
            array(
                'id_option' => 'km_KH',
                'name_option' => 'Khmer'
            ),
            array(
                'id_option' => 'tl_ST',
                'name_option' => 'Klingon'
            ),
            array(
                'id_option' => 'ko_KR',
                'name_option' => 'Korean'
            ),
            array(
                'id_option' => 'ku_TR',
                'name_option' => 'Kurdish'
            ),
            array(
                'id_option' => 'la_VA',
                'name_option' => 'Latin'
            ),
            array(
                'id_option' => 'lv_LV',
                'name_option' => 'Latvian'
            ),
            array(
                'id_option' => 'fb_LT',
                'name_option' => 'Leet Speak'
            ),
            array(
                'id_option' => 'li_NL',
                'name_option' => 'Limburgish'
            ),
            array(
                'id_option' => 'lt_LT',
                'name_option' => 'Lithuanian'
            ),
            array(
                'id_option' => 'mk_MK',
                'name_option' => 'Macedonian'
            ),
            array(
                'id_option' => 'mg_MG',
                'name_option' => 'Malagasy'
            ),
            array(
                'id_option' => 'ms_MY',
                'name_option' => 'Malay'
            ),
            array(
                'id_option' => 'ml_IN',
                'name_option' => 'Malayalam'
            ),
            array(
                'id_option' => 'mt_MT',
                'name_option' => 'Maltese'
            ),
            array(
                'id_option' => 'mr_IN',
                'name_option' => 'Marathi'
            ),
            array(
                'id_option' => 'mn_MN',
                'name_option' => 'Mongolian'
            ),
            array(
                'id_option' => 'ne_NP',
                'name_option' => 'Nepali'
            ),
            array(
                'id_option' => 'se_NO',
                'name_option' => 'Northern S?mi'
            ),
            array(
                'id_option' => 'nb_NO',
                'name_option' => 'Norwegian (bokmal)'
            ),
            array(
                'id_option' => 'nn_NO',
                'name_option' => 'Norwegian (nynorsk)'
            ),
            array(
                'id_option' => 'ps_AF',
                'name_option' => 'Pashto'
            ),
            array(
                'id_option' => 'fa_IR',
                'name_option' => 'Persian'
            ),
            array(
                'id_option' => 'pl_PL',
                'name_option' => 'Polish'
            ),
            array(
                'id_option' => 'pt_BR',
                'name_option' => 'Portuguese (Brazil)'
            ),
            array(
                'id_option' => 'pt_PT',
                'name_option' => 'Portuguese (Portugal)'
            ),
            array(
                'id_option' => 'pa_IN',
                'name_option' => 'Punjabi'
            ),
            array(
                'id_option' => 'qu_PE',
                'name_option' => 'Quechua'
            ),
            array(
                'id_option' => 'ro_RO',
                'name_option' => 'Romanian'
            ),
            array(
                'id_option' => 'rm_CH',
                'name_option' => 'Romansh'
            ),
            array(
                'id_option' => 'ru_RU',
                'name_option' => 'Russian'
            ),
            array(
                'id_option' => 'sa_IN',
                'name_option' => 'Sanskrit'
            ),
            array(
                'id_option' => 'sr_RS',
                'name_option' => 'Serbian'
            ),
            array(
                'id_option' => 'zh_CN',
                'name_option' => 'Simplified Chinese (China)'
            ),
            array(
                'id_option' => 'sk_SK',
                'name_option' => 'Slovak'
            ),
            array(
                'id_option' => 'sl_SI',
                'name_option' => 'Slovenian'
            ),
            array(
                'id_option' => 'so_SO',
                'name_option' => 'Somali'
            ),
            array(
                'id_option' => 'es_LA',
                'name_option' => 'Spanish'
            ),
            array(
                'id_option' => 'es_CL',
                'name_option' => 'Spanish (Chile)'
            ),
            array(
                'id_option' => 'es_CO',
                'name_option' => 'Spanish (Colombia)'
            ),
            array(
                'id_option' => 'es_MX',
                'name_option' => 'Spanish (Mexico)'
            ),
            array(
                'id_option' => 'es_ES',
                'name_option' => 'Spanish (Spain)'
            ),
            array(
                'id_option' => 'es_VE',
                'name_option' => 'Spanish (Venezuela)'
            ),
            array(
                'id_option' => 'sw_KE',
                'name_option' => 'Swahili'
            ),
            array(
                'id_option' => 'sv_SE',
                'name_option' => 'Swedish'
            ),
            array(
                'id_option' => 'sy_SY',
                'name_option' => 'Syriac'
            ),
            array(
                'id_option' => 'tg_TJ',
                'name_option' => 'Tajik'
            ),
            array(
                'id_option' => 'ta_IN',
                'name_option' => 'Tamil'
            ),
            array(
                'id_option' => 'tt_RU',
                'name_option' => 'Tatar'
            ),
            array(
                'id_option' => 'te_IN',
                'name_option' => 'Telugu'
            ),
            array(
                'id_option' => 'th_TH',
                'name_option' => 'Thai'
            ),
            array(
                'id_option' => 'zh_HK',
                'name_option' => 'Traditional Chinese (Hong Kong)'
            ),
            array(
                'id_option' => 'zh_TW',
                'name_option' => 'Traditional Chinese (Taiwan)'
            ),
            array(
                'id_option' => 'tr_TR',
                'name_option' => 'Turkish'
            ),
            array(
                'id_option' => 'uk_UA',
                'name_option' => 'Ukrainian'
            ),
            array(
                'id_option' => 'ur_PK',
                'name_option' => 'Urdu'
            ),
            array(
                'id_option' => 'uz_UZ',
                'name_option' => 'Uzbek'
            ),
            array(
                'id_option' => 'vi_VN',
                'name_option' => 'Vietnamese'
            ),
            array(
                'id_option' => 'cy_GB',
                'name_option' => 'Welsh'
            ),
            array(
                'id_option' => 'xh_ZA',
                'name_option' => 'Xhosa'
            ),
            array(
                'id_option' => 'yi_DE',
                'name_option' => 'Yiddish'
            ),
            array(
                'id_option' => 'zu_ZA',
                'name_option' => 'Zulu'
            )
        );

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Cart box?'),
                        'name' => 'box_cart_sw',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('YES')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('NO')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Search box?'),
                        'name' => 'box_search_sw',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('YES')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('NO')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Popular search tags'),
                        'name' => 'box_search_tags',
                        'class' => 'fixed-width-xxl',
                        'desc' => $this->l('Add search tags for sidebar search. Separate with comma without spaces like: dress,black,cotton')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Featured products for search'),
                        'name' => 'box_search_prods',
                        'class' => 'fixed-width-xxl',
                        'desc' => $this->l('Add products IDs for sidebar search. Separate with comma without spaces like: 1,8,24,150')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Account box?'),
                        'name' => 'box_acc_sw',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('YES')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('NO')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Language of Facebook content'),
                        'class' => 'fixed-width-xxl',
                        'name' => 'box_fb_lan',
                        'options' => array(
                            'query' => $_fb_languages,
                            'id' => 'id_option',
                            'name' => 'name_option'
                            )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Facebook link'),
                        'name' => 'box_facebook_url',
                        'class' => 'fixed-width-xxl',
                        'desc' => $this->l('Your full facebook page link ( ex. https://facebook.com/roythemes )')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Mail box?'),
                        'name' => 'box_mail_sw',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('YES')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('NO')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Administrator email'),
                        'name' => 'box_admin_mail',
                        'class' => 'fixed-width-xxl',
                        'desc' => $this->l('E-mail that will receive mails through the contact form')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Up arrow box?'),
                        'name' => 'box_arrow_sw',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('YES')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('NO')
                            )
                        ),
                    )
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang =
				Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
				Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') :
				0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitRoyLevibox';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
      'box_cart_sw' => Tools::getValue('box_cart_sw', Configuration::get('BOX_CART')),
      'box_search_sw' => Tools::getValue('box_search_sw', Configuration::get('BOX_SEARCH')),
      'box_search_tags' => Tools::getValue('box_search_tags', Configuration::get('BOX_SEARCH_TAGS')),
      'box_search_prods' => Tools::getValue('box_search_prods', Configuration::get('BOX_SEARCH_PRODS')),
      // 'box_menu_sw' => Tools::getValue('box_menu_sw', Configuration::get('BOX_MENU')),
      'box_acc_sw' => Tools::getValue('box_acc_sw', Configuration::get('BOX_ACC')),
      'box_fb_lan' => Tools::getValue('box_fb_lan', Configuration::get('BOX_FB_LAN')),
      'box_facebook_url' => Tools::getValue('box_facebook_url', Configuration::get('BOX_FACEBOOK_URL')),
      'box_mail_sw' => Tools::getValue('box_mail_sw', Configuration::get('BOX_MAIL')),
      'box_admin_mail' => Tools::getValue('box_admin_mail', Configuration::get('BOX_ADMIN_MAIL')),
      'box_arrow_sw' => Tools::getValue('box_arrow_sw', Configuration::get('BOX_ARROW'))
		);
	}

}
