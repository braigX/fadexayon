<?php

if (!defined('_PS_VERSION_')) {
	exit;
}

use CrazyElements\Plugin;

class Roy_Elements extends Module
{

	public function __construct()
	{
		$this->name          = 'roy_elements';
		$this->tab           = 'administration';
		$this->version       = '1.1';
		$this->author        = 'RoyThemes';
		$this->need_instance = 0;
		$this->bootstrap     = true;

		$this->displayName = $this->l('Roy Elements');
		$this->description = $this->l('Modez Elements Module');

		$this->confirmUninstall = $this->l('Uninstall the module?');
		parent::__construct();

		if (!defined('ROYELEMENTS_PATH')) {
			define('ROYELEMENTS_PATH', __DIR__);
		}

		$this->registerHook('actionGenHookImportant');
		$this->registerHook('home');
	}

	public function install()
	{
		$langs = Language::getLanguages();
		return (parent::install() &&
			$this->registerHook('displayBackOfficeHeader')
			&& $this->registerHook('displayHeader')
			&& $this->registerHook('actionCrazyBeforeInit')
			&& $this->registerHook('actionCrazyAddCategory'));
	}

	public function hookhome($params)
	{
		if (Configuration::get('cms_page_info')) {
			$this->context->smarty->assign('cmsonhome', new CMS(Configuration::get('cms_page_info'), $this->context->cookie->id_lang));
			return ($this->display(__FILE__, '/cmshomepage.tpl'));
		} else {
			return false;
		}
	}

	public function hookActionCrazyAddCategory($params)
	{

        $params['custom'][] = array(
            'modez' => array(
            'title' => 'Modez Elements',
            'icon' => 'ceicon-font',
            )
        );
	}

	public function hookActionGenHookImportant($param)
	{
		$param['genhook'] = array(
			'roy_elements' => array('displayBackOfficeHeader', 'displayHeader', 'actionCrazyBeforeInit', 'actionCrazyAddCategory'),
		);
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path . 'assets/widget-style.css');
	}

    public function hookDisplayBackOfficeHeader()
	{
		// $this->context->controller->addCSS($this->_path . 'assets/back-office.css');
	}

	public function hookActionCrazyBeforeInit($params)
	{
			require_once _PS_MODULE_DIR_ . 'roy_elements/functions.php';

			require_once _PS_MODULE_DIR_ . 'roy_elements/widgets/product-tabs.php';
			Plugin::instance()->widgets_manager->register_widget_type(new Roy_Product_Tabs());

			require_once _PS_MODULE_DIR_ . 'roy_elements/widgets/featured-products.php';
			Plugin::instance()->widgets_manager->register_widget_type(new Roy_Featured_Products());

			require_once _PS_MODULE_DIR_ . 'roy_elements/widgets/bestsellers-products.php';
			Plugin::instance()->widgets_manager->register_widget_type(new Roy_Bestsellers_Products());

			require_once _PS_MODULE_DIR_ . 'roy_elements/widgets/new-products.php';
			Plugin::instance()->widgets_manager->register_widget_type(new Roy_New_Products());

			require_once _PS_MODULE_DIR_ . 'roy_elements/widgets/special-products.php';
			Plugin::instance()->widgets_manager->register_widget_type(new Roy_Special_Products());

			require_once _PS_MODULE_DIR_ . 'roy_elements/widgets/brands-slider.php';
			Plugin::instance()->widgets_manager->register_widget_type(new Roy_Brands_Slider());

	}

	/**
	 * get all cms page list.
	 */

	public function getCMS($lang)
	{
		return CMS::listCms($lang);
	}

}
