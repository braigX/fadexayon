<?php
require_once dirname( __FILE__ ) . '/../../classes/CrazyContent.php';


require_once dirname( __FILE__ ) . '/../../includes/template-library/classes/class-import-images.php';

require_once _PS_MODULE_DIR_ . 'crazyelements/includes/plugin.php';
use CrazyElements\PrestaHelper;
use CrazyElements\TemplateLibrary\Classes\Import_Images;

class AdminCrazyhbuilderController extends ModuleAdminController {


	public $activeButton = true;
	public function __construct() {
		$this->table        = 'crazy_content';
		$this->className    = 'AdminCrazyContent';
		$this->lang         = true;
		$this->deleted      = false;
		$this->bootstrap    = true;
		$this->module       = 'crazyelements';
		$this->activeButton = PrestaHelper::get_option( 'ce_licence', 'false' );
		if ( Shop::isFeatureActive() ) {
			Shop::addTableAssociation( $this->table, array( 'type' => 'shop' ) );
		}
		parent::__construct();

		$this->_where = 'AND id_content_type = 0 ';

		$this->fields_list  = array(
			'id_crazy_content' => array(
				'title'   => $this->l( 'Id' ),
				'width'   => 100,
				'type'    => 'text',
				'orderby' => false,
				'filter'  => false,
				'search'  => false,
			),
			'title'            => array(
				'title'   => $this->l( 'Title' ),
				'width'   => 440,
				'type'    => 'text',
				'lang'    => true,
				'orderby' => false,
				'filter'  => false,
				'search'  => false,
			),
			'hook'             => array(
				'title' => $this->l( 'Hook' ),
				'type'  => 'text',
			),
			'active'           => array(
				'title'   => $this->l( 'Status' ),
				'width'   => '70',
				'align'   => 'center',
				'active'  => 'status',
				'type'    => 'bool',
				'orderby' => false,
				'filter'  => false,
				'search'  => false,
			),
		);
		$this->bulk_actions = array(
			'delete' => array(
				'text'    => $this->l( 'Delete selected' ),
				'icon'    => 'icon-trash',
				'confirm' => $this->l( 'Delete selected items?' ),
			),
		);
		parent::__construct();
	}

	public function initContent() {
		if ( $this->display == 'list' ) {
			$this->display = '';
		}
		if ( isset( $this->display ) && method_exists( $this, 'render' . $this->display ) ) {
			$this->content .= $this->initPageHeaderToolbar();
			$this->content .= $this->{'render' . $this->display}();
			$this->context->smarty->assign(
				array(
					'content'                   => $this->content,
					'show_page_header_toolbar'  => $this->show_page_header_toolbar,
					'page_header_toolbar_title' => $this->page_header_toolbar_title,
					'page_header_toolbar_btn'   => $this->page_header_toolbar_btn,
				)
			);
		} else {
			return parent::initContent();
		}
	}

	public function display() {
		parent::display();
	}

	public function renderForm() {
		$GetAlldisplayHooks = array(
			array(
				'id'   => 'hbuilder',
				'name' => 'Header Builder',
			)
		);
		$this->fields_form = array(
			'legend'  => array(
				'title' => $this->l( 'Header Builder' ),
			),
			'input'   => array(
				array(
					'type'     => 'text',
					'label'    => $this->l( 'Title' ),
					'name'     => 'title',
					'lang'     => true,
					'required' => true,
					'desc'     => $this->l( 'Enter Your Title' ),
				),
				array(
					'type'     => 'textarea',
					'callback' => 'gohere',
					'label'    => $this->l( 'Content' ),
					'name'     => 'resource',
					'lang'     => true,
				),
				array(
					'type'    => 'select',
					'label'   => $this->l( 'Select Display Hook' ),
					'name'    => 'hook',
					'options' => array(
						'query' => $GetAlldisplayHooks,
						'id'    => 'id',
						'name'  => 'name',
					),
				),
				array(
					'type'     => 'switch',
					'label'    => $this->l( 'Status' ),
					'name'     => 'active',
					'required' => false,
					'class'    => 't',
					'is_bool'  => true,
					'values'   => array(
						array(
							'id'    => 'active',
							'value' => 1,
							'label' => $this->l( 'Enabled' ),
						),
						array(
							'id'    => 'active',
							'value' => 0,
							'label' => $this->l( 'Disabled' ),
						),
					),
				),
			),
			'submit'  => array(
				'title' => $this->l( 'Save And Close' ),
				'class' => 'btn btn-default pull-right',
			),
			'buttons' => array(
				'save-and-stay' => array(
					'name'  => 'submitAdd' . $this->table . 'AndStay',
					'type'  => 'submit',
					'title' => $this->l( 'Save And Stay' ),
					'class' => 'btn btn-default pull-right',
					'icon'  => 'process-icon-save',
				),
			),
		);

		if ( Shop::isFeatureActive() ) {
			$this->fields_form['input'][] = array(
				'type'  => 'shop',
				'label' => $this->l( 'Shop association:' ),
				'name'  => 'checkBoxShopAsso',
			);
		}
		return parent::renderForm();
	}

	public function setMedia( $isNewTheme = false ) {
		parent::setMedia();
	}

	public function renderList() {
		$this->addRowAction( 'edit' );
		$this->addRowAction( 'delete' );
		
		$this->_where = 'AND a.`hook` = "hbuilder"';

		$link      = new Link();
		$settingslink  = PrestaHelper::get_setting_page_url();
		$setting_link = '<a class="btn btn-primary" href="'.$settingslink.'#configuration_form_6">Set Header Template</a>';

		return parent::renderList() . $setting_link;
	}

	public function initProcess() {
		if ( ! $this->action ) {
			parent::initProcess();
		} else {
			$this->id_object = (int) Tools::getValue( $this->identifier );
		}
	}

	public function initPageHeaderToolbar() {
		parent::initPageHeaderToolbar();
	}

	public function initToolbar() {
		parent::initToolbar();
	}
}