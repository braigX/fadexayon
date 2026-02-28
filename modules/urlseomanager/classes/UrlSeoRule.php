<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class UrlSeoRule extends ObjectModel
{
    public $id_url_seo;
    public $id_shop;
    public $url_pattern;
    public $is_regex;
    public $robots;
    public $canonical;
    public $hreflang;
    public $active;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'url_seo_manager',
        'primary' => 'id_url_seo',
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'url_pattern' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 255],
            'is_regex' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'robots' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64],
            'canonical' => ['type' => self::TYPE_STRING, 'size' => 255],
            'hreflang' => ['type' => self::TYPE_STRING, 'size' => 2048], // JSON encoded
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        
        // Default shop if not set
        if (!$this->id_shop) {
            $this->id_shop = Context::getContext()->shop->id;
        }
    }
}
