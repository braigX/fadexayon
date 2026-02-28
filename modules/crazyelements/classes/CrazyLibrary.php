<?php
// use CrazyElements\Core\DocumentTypes\Post;
use CrazyElements\PrestaHelper;

class CrazyLibrary extends ObjectModel {


	public $id_crazy_library;
	public $data;
	public $elements;
	public $settings;
	public $title;
	public $status = 'publish';
	public $type = 'page';
	public $post_type = 'elementor_library';
	public $source = 'local';
	public $thumbnail;
	public $date;
	public $human_date;
	public $author;
	public $hasPageSettings = 'false';
	public $tags;
	public $export_link;
	public $url;
	public static $definition = array(
		'table'     => 'crazy_library',
		'primary'   => 'id_crazy_library',
		'multilang' => false,
		'multishop' => false,
		'fields'    => array(
			'id_crazy_library' => array( 'type' => self::TYPE_INT ),
			'data'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'elements'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'settings'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'title'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'status'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'type'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'post_type'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'source'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'thumbnail'            => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'date'    => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'human_date'    => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'author'           => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'hasPageSettings'        => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'tags'        => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
			'export_link'        => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			),
            'url'        => array(
				'type'     => self::TYPE_STRING,
				'validate' => 'isString',
			)
		)
	);

	public function __construct( $id = null, $id_lang = null, $id_shop = null ) {
       
        parent::__construct( $id, $id_lang, $id_shop );
    }
}