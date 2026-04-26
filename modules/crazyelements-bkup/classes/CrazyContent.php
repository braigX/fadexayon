<?php
use CrazyElements\Core\DocumentTypes\Post;
use CrazyElements\PrestaHelper;

class AdminCrazyContent extends ObjectModel {


	public $id_crazy_content;
	public $id_content_type;
	public $hook;
	public $title;
	public $id_shop;
	public $status;
	public $date_created;
	public $active = 1;
	public $resource;
	public static $definition = array(
		'table'     => 'crazy_content',
		'primary'   => 'id_crazy_content',
		'multilang' => true,
		'multishop' => true,
		'fields'    => array(
			'id_content_type' => array( 'type' => self::TYPE_INT ),
			'hook'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'active'          => array(
				'type'     => self::TYPE_BOOL,
				'validate' => 'isBool',
				'required' => true,
			),
			'date_created'    => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'title'           => array(
				'type'     => self::TYPE_STRING,
				'lang'     => true,
				'validate' => 'isString',
				'required' => true,
			),
			'resource'        => array(
				'type'     => self::TYPE_HTML,
				'lang'     => true,
				'validate' => 'isString',
			),
			'id_shop'         => array(
				'type' => self::TYPE_INT,
				'lang' => true,
			),
		),
	);

	public function __construct( $id = null, $id_lang = null, $id_shop = null ) {
		Shop::addTableAssociation( 'crazycontent', array( 'type' => 'shop' ) );
		$this->id_shop = (int) \Context::getContext()->shop->id;
		parent::__construct( $id, $id_lang, $this->id_shop );
	}

	public function save_builder( $request ) {
		$hook    = PrestaHelper::$hook_current;
		$id_lang = PrestaHelper::$id_lang_global;
		$id      = PrestaHelper::$id_content_global;
		$context = Context::getContext();
		$shop_id = $context->shop->id;
		$type    = PrestaHelper::$hook_current;
		switch ( $type ) {
			case 'cms':
			case 'product':
			case 'category':
			case 'manufacturer':
			case 'supplier':
				$id_controller                           = $request['editor_post_id'];
				$table_name                              = _DB_PREFIX_ . 'crazy_content';
				$hook                                    = $type;
				$results                                 = Db::getInstance()->executeS( "SELECT * FROM $table_name WHERE hook = '" . $hook . "' AND id_content_type = " . $id_controller );
				$id_crazy_content                        = $results[0]['id_crazy_content'];
				$AdminCrazyContent                       = new AdminCrazyContent( $id_crazy_content );
				$AdminCrazyContent->active               = 1;
				$AdminCrazyContent->hook                 = $hook;
				$AdminCrazyContent->title[ $id_lang ]    = $hook;
				$AdminCrazyContent->resource[ $id_lang ] = json_encode( $request['elements'] );
				$AdminCrazyContent->date_created         = date( 'y-m-d h:i:s' );
				$AdminCrazyContent->save();
				break;
			case 'prdlayouts':
				$id_controller                           = $request['editor_post_id'];
				$table_name                              = _DB_PREFIX_ . 'crazy_content';
				$hook                                    = $type;
				$results                                 = Db::getInstance()->executeS( "SELECT * FROM $table_name WHERE hook = 'prdlayouts' AND id_content_type = " . $id_controller );
				$id_crazy_content                        = $results[0]['id_crazy_content'];
				$AdminCrazyContent                       = new AdminCrazyContent( $id_crazy_content );
				$AdminCrazyContent->active               = 1;
				$AdminCrazyContent->hook                 = $hook;
				$AdminCrazyContent->title[ $id_lang ]    = $hook;
				$AdminCrazyContent->resource[ $id_lang ] = json_encode( $request['elements'] );
				$AdminCrazyContent->date_created         = date( 'y-m-d h:i:s' );
				$AdminCrazyContent->save();
				break;
			case 'elementor_library':
				require_once _PS_MODULE_DIR_ . 'crazyelements/classes/CrazyLibrary.php';
				$id_controller                           = $request['editor_post_id'];
				$table_name                              = _DB_PREFIX_ . 'crazy_library';
				$settings = array(
					'post_status' => 'publish'
				);
				$results                                 = Db::getInstance()->executeS( "SELECT * FROM $table_name WHERE id_crazy_library = " . $id_controller );
				$id_crazy_library                        = $results[0]['id_crazy_library'];
				$CrazyLibrary                       	 = new CrazyLibrary( $id_crazy_library );
				$CrazyLibrary->status               	 = 'publish';
				$CrazyLibrary->data                 = json_encode( $request['elements'] );
				$CrazyLibrary->elements             = json_encode( $request['elements'] );
				$CrazyLibrary->settings         = json_encode( $settings );
				$CrazyLibrary->title    = $results[0]['title'];
				$CrazyLibrary->date         = date( 'y-m-d h:i:s' );
				$CrazyLibrary->human_date         = date( 'y-m-d h:i:s' );
				$CrazyLibrary->export_link         = $this->get_template_export_link($id_controller, 'local');
				$CrazyLibrary->url         = PrestaHelper::setPreviewForHook( $type );
				$CrazyLibrary->save();
				break;
			case 'extended':
				$id_controller                           = $request['editor_post_id'];
				$table_name                              = _DB_PREFIX_ . 'crazy_content';
				$hook                                    = $type;
				$results                                 = Db::getInstance()->executeS( "SELECT * FROM $table_name WHERE hook = 'extended' AND id_content_type = " . $id_controller );
				$id_crazy_content                        = $results[0]['id_crazy_content'];
				$AdminCrazyContent                       = new AdminCrazyContent( $id_crazy_content );
				$AdminCrazyContent->active               = 1;
				$AdminCrazyContent->hook                 = $hook;
				$AdminCrazyContent->title[ $id_lang ]    = $hook;
				$AdminCrazyContent->resource[ $id_lang ] = json_encode( $request['elements'] );
				$AdminCrazyContent->date_created         = date( 'y-m-d h:i:s' );
				$AdminCrazyContent->save();
				break;
			default:
				$id_crazy_content                        = $request['editor_post_id'];
				$AdminCrazyContent                       = new AdminCrazyContent( $id_crazy_content );
				$AdminCrazyContent->resource[ $id_lang ] = json_encode( $request['elements'] );
				$AdminCrazyContent->date_created         = date( 'y-m-d h:i:s' );
				$AdminCrazyContent->save();
				break;
		}
		switch ( $type ) {
			case 'cms':
				$permalink = $context->link->getCMSLink( $id, null, true, $id_lang );
				$preview           = $permalink;
				break;
			case 'product':
				$permalink = $context->link->getProductLink( $id, null, null, null, $id_lang );
				$preview           = $permalink;
				break;
			case 'category':
				$permalink = $context->link->getCategoryLink( $id, null, $id_lang );
				$preview           = $permalink;
				break;
			case 'supplier':
				$permalink = $context->link->getSupplierLink( $id, null, $id_lang );
				$preview           = $permalink;
				break;
			case 'manufacturer':
				$permalink = $context->link->getManufacturerLink( $id, null, $id_lang );
				$preview           = $permalink;
				break;
			case 'prdlayouts':
				$permalink           = PrestaHelper::setPreviewForHook( $hook );
				$preview           = $permalink;
				break;
			case 'extended':
				$permalink           = PrestaHelper::setPreviewForHook( $hook );
				$preview           = $permalink;
				break;
			default:
				$permalink           = PrestaHelper::setPreviewForHook( $hook );
				$preview           = $permalink . '?hook=' . $hook . '&id=' . $id . '&id_lang=' . $id_lang . '&id_shop=' . $shop_id . '&elementor-preview='.$id;
				break;
		}
		$data     = array(
			'elements' => $request['elements'],
			'settings' => $request['settings'],
		);
		$document = new Post();
		$document->save( $data );
		$return_data = array(
			'config' => array(
				'document' => array(
					'last_edited' => '',
					'urls'        => array(
						'wp_preview' => $preview,
						'permalink'  => $permalink,
					),
				),
			),
		);
		return $return_data;
	}

	public static function getHigherPosition() {
		$sql      = 'SELECT MAX(`id_crazy_content`) FROM `' . _DB_PREFIX_ . 'crazy_content`';
		$position = DB::getInstance()->getValue( $sql );
		return ( is_numeric( $position ) ) ? $position : -1;
	}

	private function get_template_export_link( $template_id,$source='' ) {
        
		$http_build_query= 
			[
				'action' => 'elementor_library_direct_actions',
				'library_action' => 'export_template',
				'template_id' => $template_id,
				'source' => $source,
				
			];
        return  PrestaHelper::getAjaxUrl().'&'.http_build_query($http_build_query);
	}

	public function get_elements_data($id_crazy_content = null)
	{
		$context = Context::getContext();
		$id_lang = \Tools::getValue('id_lang', $context->language->id);
		$hook = \Tools::getValue('controller');
		if ($id_crazy_content == null) {
			$id_crazy_content = PrestaHelper::$id_content_primary_global;
		}
		$hook_name = \Tools::getValue('hook');
		if ($hook_name == "elementor_library") {
			require_once _PS_MODULE_DIR_ . 'crazyelements/classes/CrazyLibrary.php';
			$AdminCrazyContent = new CrazyLibrary($id_crazy_content);
			return $AdminCrazyContent->data;
		} else {
			$AdminCrazyContent = new AdminCrazyContent($id_crazy_content, $id_lang);
			$resource          = $AdminCrazyContent->resource;
			return $resource;
		}
	}
}