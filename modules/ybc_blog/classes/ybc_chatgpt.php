<?php
/**
 * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_'))
    exit;
class Ybc_chatgpt extends ObjectModel
{
    protected $api = '';
    public static $instance;
    public $position;
    public $label;
    public $content;
    public static $definition = array(
        'table' => 'ybc_chatgpt_template',
        'primary' => 'id_ybc_chatgpt_template',
        'multilang' => true,
        'fields' => array(
            'position' => array('type' => self::TYPE_INT),
            'label' =>	array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml'),
            'content' => array('type' => self::TYPE_STRING, 'lang' => true,'validate' => 'isCleanHtml'),
        )
    );
    public function __construct($id_item = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id_item, $id_lang, $id_shop);
        $this->api = Configuration::get('YBC_BLOG_API_GPT');
    }
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ybc_chatgpt();
        }
        return self::$instance;
    }
    public function l($string,$file_name='')
    {
        return Translate::getModuleTranslation('ybc_blog', $string, $file_name ? : pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public function chatGPT()
    {
        if(Tools::isSubmit('clear_all_message'))
        {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ybc_blog_chatgpt_message`');
            die(
                json_encode(
                    array(
                        'success' => $this->l('Clear all messages successfully'),
                    )
                )
            );
        }
        $errors = array();
        $gpt_content = Tools::getValue('gpt_content');
        $input_name = Tools::getValue('input_content_name');
        if(!in_array($input_name,array('title','short_description','description','meta_title','meta_description')))
            $input_name ='title';
        if(!Validate::isCleanHtml($gpt_content,true))
        {
            $errors[] = $this->l('Message is not valid');
        }
        if(!$errors)
        {
            $message = new Ybc_chatgpt_message();
            $message->is_chatgpt =0;
            $message->message = $gpt_content;
            if(!$message->add())
                $errors[] = $this->l('An error occurred while saving the message');
        }
        if(!$errors)
        {
            $url = "https://api.openai.com/v1/chat/completions";
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->api . '',
            );
            $temperature = 0.7;
            $max_tokens = 4000;
            $top_p =1;
            $frequency_penalty = 0;
            $presence_penalty =0;
            $data = array(
                'model' => "gpt-3.5-turbo",
                'temperature' => $temperature,
                'max_tokens' => $max_tokens,
                'top_p' => $top_p,
                'frequency_penalty' => $frequency_penalty,
                'presence_penalty' => $presence_penalty,
                'stop' => '[" Human:", " AI:"]',
                "messages" => array(
                    array(
                        "role" => "user",
                        "content" => str_replace('"', '', urldecode($gpt_content))
                    )
                ),
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $result = curl_exec($ch);
            if($result && ($result = json_decode($result,true)))
            {
                if(isset($result['error']) && $result['error'])
                {
                    if(isset($result['error']['message']) && $result['error']['message'])
                        $errors[] = $result['error']['message'];
                    else
                        $errors[] = $this->l('ChatGPT API request failed.');
                }
                elseif(isset($result['choices']) && ($choices = $result['choices']) && isset($choices[0]['message']['content']) && $choices[0]['message']['content'])
                {
                    $message = new Ybc_chatgpt_message();
                    $message->is_chatgpt = 1;
                    $message->message = $choices[0]['message']['content'];
                    $message->field = $input_name;
                    if($message->add())
                    {
                        die(
                            json_encode(
                                array(
                                    'success' => true,
                                    'message' => $this->displayMessage($message),
                                )
                            )
                        );
                    }
                    else
                        $errors[] = $this->l('An error occurred while saving the message');

                }
                else
                    $errors[] = $this->l('ChatGPT API request failed.');
            }
            else
                $errors[] = $this->l('ChatGPT API request failed.');
        }
        if($errors)
        {
            die(
                json_encode(
                    array(
                        'error' => $errors[0],
                    )
                )
            );
        }
    }
    public function displayMessage($message)
    {
        if(!is_object($message))
        {
            $message = new Ybc_chatgpt_message($message);
        }
        Context::getContext()->smarty->assign(
              array(
                  'chatgpt_message' => $message,
                  'languages' => Language::getLanguages(),
                  'defaultFormLanguage' => Configuration::get('PS_LANG_DEFAULT'),
              )
        );
        return Module::getInstanceByName('ybc_blog')->display(Module::getInstanceByName('ybc_blog')->getLocalPath(),'chatgpt-message.tpl');
    }
    public static function countTemplatesWithFilter($filter = false)
    {
        $req = 'SELECT COUNT(t.id_ybc_chatgpt_template) 
            FROM `'._DB_PREFIX_.'ybc_chatgpt_template` t
            LEFT JOIN `'._DB_PREFIX_.'ybc_chatgpt_template_lang` tl ON (t.id_ybc_chatgpt_template =tl.id_ybc_chatgpt_template AND tl.id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE 1 '.($filter ? $filter : '');
        return (int)Db::getInstance()->getValue($req);
    }
    public static function getTemplatesWithFilter($filter = false, $sort = false, $start = false, $limit = false)
    {
        $req = 'SELECT t.id_ybc_chatgpt_template,tl.*,tl.label as title
            FROM `' . _DB_PREFIX_ . 'ybc_chatgpt_template` t
            LEFT JOIN `'._DB_PREFIX_.'ybc_chatgpt_template_lang` tl ON (t.id_ybc_chatgpt_template =tl.id_ybc_chatgpt_template AND id_lang="'.(int)Context::getContext()->language->id.'")
            WHERE 1 ' . ($filter ? $filter : '')
            . ($start !== false && $limit ? " LIMIT " . (int)$start . ", " . (int)$limit : "");
        return Db::getInstance()->executeS($req);
    }
    public static function getAllTemplate()
    {
        $languages = Language::getLanguages(false);
        $templates = array();
        if($languages)
        {
            foreach($languages as $language)
            {
                $sql ='SELECT tl.label,tl.content FROM `'._DB_PREFIX_.'ybc_chatgpt_template` t
                INNER JOIN `'._DB_PREFIX_.'ybc_chatgpt_template_lang` tl ON (t.id_ybc_chatgpt_template = tl.id_ybc_chatgpt_template AND tl.id_lang="'.(int)$language['id_lang'].'")';
                $templates[$language['id_lang']] = Db::getInstance()->executeS($sql);
            }
        }
        return $templates;
    }
}