<?php
/**
 *
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 * @license   Commercial
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

 if (!defined('_PS_VERSION_')) {
    exit;
}
class seointernallinkingexpressionclass extends ObjectModel
{
    public $id_expression;
    public $id_lang;
    public $id_group;
    public $expression_content;
    public $associated_url;
    public $url_title;
    public $active = 1;
    public $nofollow = 0;
    public $new_window = 0;
    public $link_position = 1;
    protected $tables = array('pm_seointernallinking');
    protected $fieldsRequired     =   array('active');
    protected $fieldsSize         =   array('link_position' => 1, 'new_window'=> 1, 'nofollow'=> 1, 'active'=> 1, 'expression_content' => 255, 'url_title' => 100);
    protected $fieldsValidate     =   array(
                                            'id_lang' => 'isUnsignedId',
                                            'id_group' => 'isUnsignedId',
                                            'expression_content' => 'isGenericName',
                                            'url_title' => 'isGenericName',
                                            'active' => 'isBool',
                                            'nofollow' => 'isBool',
                                            'new_window' => 'isBool',
                                            'link_position' => 'isUnsignedId'
                                        );
    protected $table              =   'pm_seointernallinking';
    public $identifier         =   'id_expression';
    protected $fieldsRequiredLang =   array();
    protected $fieldsSizeLang     =   array();
    protected $fieldsValidateLang =   array();
    public static $valid_page       =   array();
    public function __construct($id = null)
    {
        parent::__construct($id);
        if (!isset($this->id_lang)) {
            $this->id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }
    }
    public function getFields()
    {
        $fields = array();
        parent::validateFields();
        if (isset($this->id_expression)) {
            $fields['id_expression'] = (int)$this->id_expression;
        }
        $fields['id_lang']              = (int)$this->id_lang;
        $fields['id_group']             = (int)$this->id_group;
        $fields['expression_content']   = pSQL($this->expression_content);
        $fields['associated_url']       = pSQL($this->associated_url);
        $fields['url_title']            = pSQL($this->url_title);
        $fields['active']               = (int)$this->active;
        $fields['nofollow']             = (int)$this->nofollow;
        $fields['new_window']           = (int)$this->new_window;
        $fields['link_position']        = (int)$this->link_position;
        return $fields;
    }
    public function save($nullValues = false, $autodate = true)
    {
        if (parent::save()) {
            return true;
        }
        return false;
    }
    public function delete()
    {
        return parent::delete();
    }
}
