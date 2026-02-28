<?php
/*
* RoyThemes Copyright 2021
* This module is an official part of MODEZ theme.
*/

if (!defined('_PS_VERSION_'))
	exit;

class Roy_Content extends Module
{
	protected $max_image_size = 1048576;
	protected $default_language;
	protected $languages;

	public function __construct()
	{
		$this->name = 'roy_content';
		$this->tab = 'front_office_features';
		$this->version = '3.2';
		$this->bootstrap = true;
		$this->secure_key = Tools::encrypt($this->name);
		$this->default_language = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
		$this->languages = Language::getLanguages();
		$this->author = 'RoyThemes';
		parent::__construct();

		$this->displayName = $this->trans(
				'Roy Content',
				array(),
				'Modules.RoyContent.Admin'
		);
		$this->description = $this->trans(
				'Add banners, HTML or video content to your shop',
				array(),
				'Modules.RoyContent.Admin'
		);

		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
		$this->module_path = _PS_MODULE_DIR_.$this->name.'/';
		$this->uploads_path = _PS_MODULE_DIR_.$this->name.'/img/';
		$this->admin_tpl_path = _PS_MODULE_DIR_.$this->name.'/views/templates/admin/';
		$this->hooks_tpl_path = _PS_MODULE_DIR_.$this->name.'/views/templates/hooks/';
	}

	public function createAjaxController()
	{
		$tab = new Tab();
		$tab->active = 1;
		$languages = Language::getLanguages(false);
		if (is_array($languages))
			foreach ($languages as $language)
				$tab->name[$language['id_lang']] = 'roy_content';
		$tab->class_name = 'AdminBannerOrder';
		$tab->module = $this->name;
		$tab->id_parent = - 1;
		return (bool)$tab->add();
	}

	private function _removeAjaxContoller()
	{
		if ($tab_id = (int)Tab::getIdFromClassName('AdminBannerOrder'))
		{
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		return true;
	}

	public function install()
	{
        return parent::install() &&
			$this->installDB() &&
			$this->installFixtures(Language::getLanguages(true)) &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('displayBanner') &&
			$this->registerHook('displayHeaderContent') &&
			$this->registerHook('displayTopColumn') &&
			$this->registerHook('displayLeftColumn') &&
			$this->registerHook('displayRightColumn') &&
			$this->registerHook('displayHome') &&
			$this->registerHook('displayFooterBefore') &&
			$this->registerHook('displayFooter') &&
			$this->registerHook('displaySideMobileMenu') &&
			$this->registerHook('displayProductBeforeBuy') &&
			$this->registerHook('displayBackOfficeHeader') &&
			$this->registerHook('actionObjectLanguageAddAfter') &&
			$this->createAjaxController();
			
	}

	private function installDB()
	{
		return (
			Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'roy_content`') &&
			Db::getInstance()->Execute('
			CREATE TABLE `'._DB_PREFIX_.'roy_content` (
					`id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`id_shop` int(10) unsigned NOT NULL,
					`id_lang` int(10) unsigned NOT NULL,
					`item_order` int(10) unsigned NOT NULL,
					`title` VARCHAR(100),
					`title_use` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`hook` VARCHAR(100),
					`url` TEXT,
					`target` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`video` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`hover` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					`hover_type` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					`fit` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`hide` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`hides` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`cols` VARCHAR(2),
					`banner_layout` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`height_rows` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`bg_color` VARCHAR(100),
					`grad_start` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`grad_end` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`title_color` VARCHAR(100),
					`desc_color` VARCHAR(100),
					`button_type` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`button_bg` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`button_color` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`button_border` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`button_bgh` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`button_colorh` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`button_borderh` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
					`image` VARCHAR(100),
					`image_w` VARCHAR(10),
					`image_h` VARCHAR(10),
					`html` TEXT,
					`active` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
					PRIMARY KEY (`id_item`)
			) ENGINE = '._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;')
		);
	}

	protected function installFixture($hook, $id_image, $id_shop, $id_lang)
	{
		$result = true;

		$bannerswithext = glob((dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'banner-img'.(int)$id_image.'.*'));
		$ext = pathinfo($bannerswithext[0], PATHINFO_EXTENSION);

		$sizes = @getimagesize(glob((dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'banner-img'.(int)$id_image.'.'.$ext.'')));
		$width = (isset($sizes[0]) && $sizes[0])? (int)$sizes[0] : 0;
		$height = (isset($sizes[1]) && $sizes[1])? (int)$sizes[1] : 0;

		$result &= Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'roy_content` (
					`id_shop`, `id_lang`, `item_order`, `title`, `title_use`, `hook`, `url`, `target`, `video`, `hover`, `hover_type`, `fit`, `hide`, `hides`, `cols`, `banner_layout`, `height_rows`, `bg_color`, `grad_start`, `grad_end`, `title_color`, `desc_color`, `button_type`, `button_bg`, `button_color`, `button_border`, `button_bgh`, `button_colorh`, `button_borderh`, `image`, `image_w`, `image_h`, `html`, `active`
			) VALUES (
				\''.(int)$id_shop.'\',
				\''.(int)$id_lang.'\',
				\''.(int)$id_image.'\',
				\'Title of Banner\',
				\'0\',
				\''.pSQL($hook).'\',
				\'#\',
				\'0\',
				\'0\',
				\'0\',
				\'0\',
				\'0\',
				\'0\',
				\'0\',
				\'4\',
				\'0\',
				\'0\',
				\'#ffffff\',
				\'0\',
				\'0\',
				\'#777777\',
				\'0\',
				\'0\',
				\'0\',
				\'0\',
				\'0\',
				\'0\',
				\'0\',
				\'0\',
				\'banner-img'.(int)$id_image.'.'.$ext.'\',
				'.$width.',
				'.$height.',
				\'Demo banner description goes here. It is <strong>HTML formatted</strong> text.\',
				1)
			');

		return $result;
	}

	public function installFixtures($languages = null)
	{
		$result = true;

		if ($languages === null)
			$languages = Language::getLanguages(true);

			foreach ($languages as $language)
			{
				for ($m1 = 1; $m1 < 2; $m1++)
					$result &= $this->installFixture('footer', $m1, $this->context->shop->id, $language['id_lang']);
			}

		return $result;
	}

	public function uninstall()
	{
		$images = array();
		if (count(Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'roy_content\'')))
			$images = Db::getInstance()->executeS('SELECT image FROM `'._DB_PREFIX_.'roy_content`');

		foreach ($images as $image)
			if ($image = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'roy_content` WHERE id_item = '.(int)Tools::getValue('item_id')))
				$this->deleteImage($image);

		if (!Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'roy_content`') || !$this->_removeAjaxContoller() || !parent::uninstall())
			return false;

		return true;
	}

	public function hookDisplayBackOfficeHeader()
	{
		if (Tools::getValue('configure') != $this->name)
			return;
		$this->context->controller->addCSS($this->_path.'css/admin.css');
		$this->context->controller->addJquery();
        $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
        $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
        $this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/jquery.colorpicker.js');
		$this->context->controller->addJS($this->_path.'js/admin.js');
	}

	public function hookdisplayHeader()
	{
		$this->context->controller->addCss($this->_path.'css/hooks.css', 'all');
	}

	public function hookActionObjectLanguageAddAfter($params)
	{
		return $this->installFixtures(array(array('id_lang' => (int)$params['object']->id)));
	}

	public function hookdisplayTopColumn()
	{
		return $this->hookdisplayTop();
	}

	public function hookdisplayTop()
	{
		if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index')
			return;
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('top'),
			'hook' => 'top'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}

	public function hookDisplayHeaderContent()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('headercontent'),
			'hook' => 'headercontent'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}
	public function hookDisplayHome()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('home'),
			'hook' => 'home'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}
	public function hookDisplayLeftColumn()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('left'),
			'hook' => 'left'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}

	public function hookDisplayRightColumn()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('right'),
			'hook' => 'right'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}

	public function hookDisplayFooter()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('footer'),
			'hook' => 'footer'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}

	public function hookDisplayFooterBefore()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('footerbefore'),
			'hook' => 'footerbefore'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}

	public function hookDisplayBanner()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('beforeheader'),
			'hook' => 'beforeheader'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}
	public function hookDisplaySideMobileMenu()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('sidemobilemenu'),
			'hook' => 'sidemobilemenu'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}
	public function hookDisplayProductBeforeBuy()
	{
		$this->context->smarty->assign(array(
			'htmlitems' => $this->getItemsFromHook('productbeforebuy'),
			'hook' => 'productbeforebuy'
		));
		return $this->display(__FILE__, 'hook.tpl');
	}

	protected function getItemsFromHook($hook)
	{
		if (!$hook)
			return false;

		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'roy_content`
		WHERE id_shop = '.(int)$this->context->shop->id.' AND id_lang = '.(int)$this->context->language->id.'
		AND hook = \''.pSQL($hook).'\' AND active = 1
		ORDER BY item_order ASC');
	}

	protected function deleteImage($image)
	{
		$file_name = $this->uploads_path.$image;

		if (realpath(dirname($file_name)) != realpath($this->uploads_path))
			Tools::dieOrLog(sprintf('Could not find upload directory'));

		if ($image != '' && is_file($file_name) && !strpos($file_name, 'banner-img') && !strpos($file_name, 'bg-theme') && !strpos($file_name, 'footer-bg'))
			unlink($file_name);
	}

	protected function removeItem()
	{
		$id_item = (int)Tools::getValue('item_id');

		if ($image = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'roy_content` WHERE id_item = '.(int)$id_item))
			$this->deleteImage($image);

			Db::getInstance()->delete('roy_content', 'id_item = '.(int)$id_item);

			if (Db::getInstance()->Affected_Rows() > 0)
		{
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'roy_content`
				SET item_order = item_order-1
				WHERE (
					item_order > '.(int)Tools::getValue('item_order').' AND
					id_shop = '.(int)$this->context->shop->id.' AND
					hook = \''.pSQL(Tools::getValue('item_hook')).'\')'
			);
			Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.'&conf=6&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		else
			$this->context->smarty->assign('error', $this->l('Can\'t delete the slide.'));
	}

	protected function updateItem()
	{
		$id_item = (int)Tools::getValue('item_id');
		$title = Tools::getValue('item_title');
		$content = Tools::getValue('item_html');

		if (!Validate::isCleanHtml($title, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')) || !Validate::isCleanHtml($content, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
		{
			$this->context->smarty->assign('error', $this->l('Invalid content'));
			return false;
		}

		$new_image = '';
		$image_w = (is_numeric(Tools::getValue('item_img_w'))) ? (int)Tools::getValue('item_img_w') : '';
		$image_h = (is_numeric(Tools::getValue('item_img_h'))) ? (int)Tools::getValue('item_img_h') : '';

		if (!empty($_FILES['item_img']['name']))
		{
			if ($old_image = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'roy_content` WHERE id_item = '.(int)$id_item))
				if (file_exists(dirname(__FILE__).'/img/'.$old_image))
					@unlink(dirname(__FILE__).'/img/'.$old_image);

			if (!$image = $this->uploadImage($_FILES['item_img'], $image_w, $image_h))
				return false;

			$new_image = 'image = \''.pSQL($image).'\',';
		}

		if (!Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'roy_content` SET
					title = \''.pSQL($title).'\',
					title_use = '.(int)Tools::getValue('item_title_use').',
					hook = \''.pSQL(Tools::getValue('item_hook')).'\',
					url = \''.pSQL(Tools::getValue('item_url')).'\',
					target = '.(int)Tools::getValue('item_target').',
					video = '.(int)Tools::getValue('item_video').',
					hover = '.(int)Tools::getValue('item_hover').',
					hover_type = '.(int)Tools::getValue('item_hover_type').',
					fit = '.(int)Tools::getValue('item_fit').',
					hide = '.(int)Tools::getValue('item_hide').',
					hides = '.(int)Tools::getValue('item_hides').',
					cols = \''.pSQL(Tools::getValue('item_cols')).'\',
					banner_layout = \'0\',
					height_rows = \'0\',
					bg_color = \''.pSQL(Tools::getValue('item_bg_color')).'\',
					grad_start = \'0\',
					grad_end = \'0\',
					title_color = \''.pSQL(Tools::getValue('item_title_color')).'\',
					desc_color = \''.pSQL(Tools::getValue('item_desc_color')).'\',
					button_type = \'0\',
					button_bg = \'0\',
					button_color = \'0\',
					button_border = \'0\',
					button_bgh = \'0\',
					button_colorh = \'0\',
					button_borderh = \'0\',
					'.$new_image.'
					image_w = '.(int)$image_w.',
					image_h = '.(int)$image_h.',
					html = \''.pSQL($content, true).'\',
					active = '.(int)Tools::getValue('item_active').'
			WHERE id_item = '.(int)Tools::getValue('item_id')
		))
		{
			if ($image = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'roy_content` WHERE id_item = '.(int)Tools::getValue('item_id')))
				$this->deleteImage($image);

			$this->context->smarty->assign('error', $this->l('An error occurred while saving data.'));

			return false;
		}

		$this->context->smarty->assign('confirmation', $this->l('Successfully updated.'));

		return true;
	}

	protected function uploadImage($image, $image_w = '', $image_h = '')
	{
		$res = false;
		if (is_array($image) && (ImageManager::validateUpload($image, $this->max_image_size) === false) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($image['tmp_name'], $tmp_name))
		{
			$salt = sha1(microtime());
			$pathinfo = pathinfo($image['name']);
			$img_name = $salt.'_'.Tools::str2url($pathinfo['filename']).'.'.$pathinfo['extension'];
			if (ImageManager::resize($tmp_name, dirname(__FILE__).'/img/'.$img_name, $image_w, $image_h))
				$res = true;
		}
		if (!$res)
		{
			$this->context->smarty->assign('error', $this->l('An error occurred during the image upload.'));
			return false;
		}
		return $img_name;
	}

	public function getContent()
	{
		if (Tools::isSubmit('newItem'))
			$this->addItem();
		elseif (Tools::isSubmit('updateItem'))
			$this->updateItem();
		elseif (Tools::isSubmit('removeItem'))
			$this->removeItem();

		$html = $this->renderThemeConfiguratorForm();

		return $html;
	}

	protected function addItem()
	{
		$title = Tools::getValue('item_title');
		$content = Tools::getValue('item_html');

		if (!Validate::isCleanHtml($title, (int)Configuration::get('PS_ALLOW_HTML_IFRAME'))
			|| !Validate::isCleanHtml($content, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
		{
			$this->context->smarty->assign('error', $this->l('Invalid content'));

			return false;
		}

		if (!$current_order = (int)Db::getInstance()->getValue('
			SELECT item_order + 1
			FROM `'._DB_PREFIX_.'roy_content`
			WHERE
					id_shop = '.(int)$this->context->shop->id.'
					AND id_lang = '.(int)Tools::getValue('id_lang').'
					AND hook = \''.pSQL(Tools::getValue('item_hook')).'\'
					ORDER BY item_order DESC'
		) )

			$current_order = 1;

		$image_w = is_numeric(Tools::getValue('item_img_w')) ? (int)Tools::getValue('item_img_w') : '';
		$image_h = is_numeric(Tools::getValue('item_img_h')) ? (int)Tools::getValue('item_img_h') : '';

		if (!empty($_FILES['item_img']['name']))
		{
			if (!$image = $this->uploadImage($_FILES['item_img'], $image_w, $image_h))
				return false;
		}
		else
		{
			$image = '';
			$image_w = '';
			$image_h = '';
		}

		if (!Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'roy_content` (
					`id_shop`, `id_lang`, `item_order`, `title`, `title_use`, `hook`, `url`, `target`, `video`, `hover`, `hover_type`, `fit`, `hide`, `hides`, `cols`, `banner_layout`, `height_rows`, `bg_color`, `grad_start`, `grad_end`, `title_color`, `desc_color`, `button_type`, `button_bg`, `button_color`, `button_border`, `button_bgh`, `button_colorh`, `button_borderh`, `image`, `image_w`, `image_h`, `html`, `active`
			) VALUES (
					\''.(int)$this->context->shop->id.'\',
					\''.(int)Tools::getValue('id_lang').'\',
					\''.(int)$current_order.'\',
					\''.pSQL($title).'\',
					\''.(int)Tools::getValue('item_title_use').'\',
					\''.pSQL(Tools::getValue('item_hook')).'\',
					\''.pSQL(Tools::getValue('item_url')).'\',
					\''.(int)Tools::getValue('item_target').'\',
					\''.(int)Tools::getValue('item_video').'\',
					\''.(int)Tools::getValue('item_hover').'\',
					\''.(int)Tools::getValue('item_hover_type').'\',
					\''.(int)Tools::getValue('item_fit').'\',
					\''.(int)Tools::getValue('item_hide').'\',
					\''.(int)Tools::getValue('item_hides').'\',
					\''.pSQL(Tools::getValue('item_cols')).'\',
					\'0\',
					\'0\',
					\''.pSQL(Tools::getValue('item_bg_color')).'\',
					\'0\',
					\'0\',
					\''.pSQL(Tools::getValue('item_title_color')).'\',
					\''.pSQL(Tools::getValue('item_desc_color')).'\',
					\'0\',
					\'0\',
					\'0\',
					\'0\',
					\'0\',
					\'0\',
					\'0\',
					\''.pSQL($image).'\',
					\''.pSQL($image_w).'\',
					\''.pSQL($image_h).'\',
					\''.pSQL($content, true).'\',
					1)
			')
		)
		{
			if (!Tools::isEmpty($image))
				$this->deleteImage($image);

			$this->context->smarty->assign('error', $this->l('An error occurred while saving data.'));

			return false;
		}

		$this->context->smarty->assign('confirmation', $this->l('New item successfully added.'));

		return true;
	}

	protected function renderThemeConfiguratorForm()
	{
		$id_shop = (int)$this->context->shop->id;
		$items = array();
		$hooks = array();

		$this->context->smarty->assign('htmlcontent', array(
			'admin_tpl_path' => $this->admin_tpl_path,
			'hooks_tpl_path' => $this->hooks_tpl_path,

			'info' => array(
				'module' => $this->name,
				'name' => $this->displayName,
				'version' => $this->version,
				'psVersion' => _PS_VERSION_,
					'context' => (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 0)
						? 1
						: (($this->context->shop->getTotalShops() != 1) ? $this->context->shop->getContext() : 1)
				)
			));

		foreach ($this->languages as $language)
		{
			$hooks[$language['id_lang']] = array(
				'beforeheader',
				'headercontent',
				'top',
				'left',
				'right',
				'home',
				'footerbefore',
				'footer',
                'sidemobilemenu',
				'productbeforebuy'
			);
			foreach ($hooks[$language['id_lang']] as $hook)
				$items[$language['id_lang']][$hook] = Db::getInstance()->ExecuteS('
					SELECT * FROM `'._DB_PREFIX_.'roy_content`
					WHERE id_shop = '.(int)$id_shop.'
					AND id_lang = '.(int)$language['id_lang'].'
					AND hook = \''.pSQL($hook).'\'
					ORDER BY item_order ASC'
			);
		}

		$this->context->smarty->assign('htmlitems', array(
			'items' => $items,
			'theme_url' => $this->context->link->getAdminLink('AdminBannerOrder'),
			'lang' => array(
				'default' => $this->default_language,
				'all' => $this->languages,
				'lang_dir' => _THEME_LANG_DIR_,
				'user' => $this->context->language->id
			),
			'postAction' => 'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module=other&module_name='.$this->name.'',
			'id_shop' => $id_shop
		));

		$this->context->controller->addJqueryUI('ui.sortable');
		return $this->display(__FILE__, 'views/templates/admin/admin.tpl');
	}

	public function getLiveConfiguratorToken()
	{
		return Tools::getAdminToken($this->name.(int)Tab::getIdFromClassName($this->name)
			.(is_object(Context::getContext()->employee) ? (int)Context::getContext()->employee->id :
				Tools::getValue('id_employee')));
	}

}
