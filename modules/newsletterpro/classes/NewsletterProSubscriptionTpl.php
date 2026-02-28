<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

class NewsletterProSubscriptionTpl extends ObjectModel
{
    public $name;

    public $active;

    public $voucher;

    public $css_style;

    public $display_gender;

    public $display_firstname;

    public $display_lastname;

    public $display_language;

    public $display_birthday;

    public $display_list_of_interest;

    public $display_subscribe_message;

    public $list_of_interest_type;

    public $body_width;

    public $body_max_width;

    public $body_min_width;

    public $body_top;

    public $show_on_pages;

    public $cookie_lifetime;

    public $start_timer;

    public $when_to_show;

    public $allow_multiple_time_subscription;

    public $mandatory_fields;

    public $date_add;

    public $content;

    public $subscribe_message;

    public $email_subscribe_voucher_message;

    public $email_subscribe_confirmation_message;

    public $email_unsubscribe_confirmation_message;

    public $terms_and_conditions_url;

    /* defined */
    public $context;

    public $module;

    public $errors = [];

    public $extend_vars = [];

    public $css_dir_path;

    public $css_uri_path;

    public $render_loader;

    public $load_file;

    public static $replace_vars = [];

    const CSS_STYLE_GLOBAL_PATH = 'newsletter_subscribe.css';

    const LIST_OF_INTEREST_TYPE_SELECT = 0;

    const LIST_OF_INTEREST_TYPE_CHECKBOX = 1;

    const DEFAULT_BODY_WIDTH = '40%';

    const SHOW_ON_PAGES_NONE = 0;

    const SHOW_ON_PAGES_ALL = -1;

    const WHEN_TO_SHOW_POPUP_COOKIE = 0;

    const WHEN_TO_SHOW_POPUP_ALWAYS = 1;

    const RENDER_LOADER_DEFAULT = 0;

    const RENDER_LOADER_BETTER = 1;

    public static $definition = [
        'table' => 'newsletter_pro_subscription_tpl',
        'primary' => 'id_newsletter_pro_subscription_tpl',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'voucher' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],

            'display_gender' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'display_firstname' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'display_lastname' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'display_language' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'display_birthday' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'display_list_of_interest' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'display_subscribe_message' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'list_of_interest_type' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'body_width' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'body_max_width' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'body_min_width' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'body_top' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'allow_multiple_time_subscription' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'mandatory_fields' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'render_loader' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],

            /* Lang fields */
            'content' => ['type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true],
            'subscribe_message' => ['type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true],
            'email_subscribe_voucher_message' => ['type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true],
            'email_subscribe_confirmation_message' => ['type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true],
            'email_unsubscribe_confirmation_message' => ['type' => self::TYPE_HTML, 'validate' => 'isString', 'lang' => true],

            /* Shop fields */
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'shop' => true],
            'css_style' => ['type' => self::TYPE_HTML, 'validate' => 'isString', 'shop' => true],

            'show_on_pages' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'shop' => true],
            'cookie_lifetime' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'shop' => true],
            'start_timer' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],
            'when_to_show' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],

            'terms_and_conditions_url' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'shop' => true],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        self::initAssoTables();

        parent::__construct($id, $id_lang, $id_shop);

        $this->context = Context::getContext();
        $this->module = NewsletterPro::getInstance();
        $this->css_dir_path = $this->module->dir_location.'views/css/subscription_template';
        $this->css_uri_path = $this->module->uri_location.'views/css/subscription_template';
    }

    public static function initAssoTables()
    {
        NewsletterProTools::addTableAssociationArray(self::getAssoTables());
    }

    public static function getAssoTables()
    {
        return [
            'newsletter_pro_subscription_tpl' => ['type' => 'shop'],
            // if it si liltiland multishop the fk_shop is requered, all the values will be availalbe in all the shop
            'newsletter_pro_subscription_tpl_lang' => ['type' => 'shop'],
        ];
    }

    public static function getDefined()
    {
        return [
            'RENDER_LOADER_DEFAULT' => self::RENDER_LOADER_DEFAULT,
            'RENDER_LOADER_BETTER' => self::RENDER_LOADER_BETTER,
        ];
    }

    public function delete()
    {
        try {
            $response = parent::delete();

            if ($response) {
                $css_filename = $this->getCSSStyleFileName();
                $full_path = $this->css_dir_path.'/'.$css_filename;

                if (file_exists($this->css_dir_path) && file_exists($full_path) && is_file($full_path)) {
                    $response = @unlink($full_path);
                    if (!$response) {
                        $this->addError(sprintf('Cannot delete the css template file "%s" from the disk. Please check the CHMOD permissions!'), $full_path);
                    }
                }
            }

            return $response;
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                $this->addError($e->getMessage());
            } else {
                $this->addError('An error occurred when inserting the record into database!');
            }
        }

        return false;
    }

    public static function getNameFormatted($name)
    {
        return Tools::strtolower(preg_replace('/\s+/', '_', trim($name)));
    }

    public function add($autodate = true, $null_values = false)
    {
        // verify duplocate names
        try {
            if (!isset($this->date_add)) {
                $this->date_add = date('Y-m-d H:i:s');
            }

            $this->name = self::getNameFormatted($this->name);

            $name_display = Tools::ucfirst(str_replace('_', ' ', $this->name));

            if (empty($this->name)) {
                $this->addError('The template name cannot be empty.');
            } elseif (!NewsletterProTools::isFileName($this->name)) {
                $this->addError(sprintf('The template name "%s" is not valid, there are illegal charactes.', $name_display));
            } elseif ($this->isDuplicateName()) {
                $this->addError(sprintf('The template name "%s" already exists in database.', $name_display));
            }

            if ($this->active) {
                self::setActive((int) $this->id);
            }

            if (!$this->hasErrors()) {
                $response = parent::add($autodate, $null_values);
                // only if the option Smarty Cache for CSS is activated a css file will be created | if ($response && (int)Configuration::get('PS_CSS_THEME_CACHE'))
                if ($response) {
                    $response = $this->saveCSSStyleAsFile($this->css_style);
                    if (!$response) {
                        $this->addError(sprintf('The css style cannot be saved as a file. Please check the CHMOD permissions.'));
                    }
                }

                self::setActiveIfNotExists();

                return $response;
            }
        } catch (Exception $e) {
            if (_PS_MODE_DEV_) {
                $this->addError($e->getMessage());
            } else {
                $this->addError('An error occurred when inserting the record into database!');
            }
        }

        return false;
    }

    public function update($null_values = false)
    {
        if ($this->active) {
            self::setActive((int) $this->id);
        }

        $response = parent::update($null_values);
        // only if the option Smarty Cache for CSS is activated a css file will be created | if ($response && (int)Configuration::get('PS_CSS_THEME_CACHE'))
        if ($response) {
            $response = $this->saveCSSStyleAsFile($this->css_style);
            if (!$response) {
                $this->addError(sprintf('The css style cannot be saved as a file. Please check the CHMOD permissions.'));
            }
        }
        self::setActiveIfNotExists();

        return $response;
    }

    public static function setActive($id_template, $shop_context = null)
    {
        if (isset($shop_context) && Shop::isFeatureActive()) {
            Shop::setContext($shop_context);
        }

        $shops_id = NewsletterProTools::getActiveShopsId();

        $success = [];

        if (Db::getInstance()->update('newsletter_pro_subscription_tpl_shop', [
            'active' => 0,
        ], '`active` = 1 AND `id_shop` IN ('.implode(',', $shops_id).')')) {
            $success[] = Db::getInstance()->update('newsletter_pro_subscription_tpl_shop', [
                'active' => 1,
            ], '`id_newsletter_pro_subscription_tpl` = '.(int) $id_template.' AND `id_shop` IN ('.implode(',', $shops_id).') ', 1);
        }

        if (Db::getInstance()->update('newsletter_pro_subscription_tpl', [
            'active' => 0,
        ], '`active` = 1')) {
            $success[] = Db::getInstance()->update('newsletter_pro_subscription_tpl', [
                'active' => 1,
            ], '`id_newsletter_pro_subscription_tpl` = '.(int) $id_template, 1);
        }

        self::setActiveIfNotExists();

        return !in_array(false, $success);
    }

    public static function setActiveIfNotExists()
    {
        $success = [];
        if (!Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` WHERE `active` = 1')) {
            $success[] = Db::getInstance()->update('newsletter_pro_subscription_tpl', [
                'active' => 1,
            ], '`name` = "default"', 1);
        }

        return !in_array(false, $success);
    }

    public function isDuplicateName()
    {
        return Db::getInstance()->getValue('
				SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` WHERE `name` = "'.pSQL($this->name).'"
			');
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public static function getTemplateByName($name)
    {
        $id = (int) Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_subscription_tpl`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl`
			WHERE `name` = "'.pSQL($name).'"
		');
        $template = new NewsletterProSubscriptionTpl($id);
        if (Validate::isLoadedObject($template)) {
            return $template;
        }

        return false;
    }

    public static function getTemplatesDataGrid($id_lang = null, $id_shop = null)
    {
        $id_lang = (isset($id_lang) ? $id_lang : Context::getContext()->language->id);
        $id_shop = (isset($id_shop) ? $id_shop : Context::getContext()->shop->id);

        $result = Db::getInstance()->executeS(self::getTemplatesSql([
            'select' => '
				s.`id_newsletter_pro_subscription_tpl`,
				s.`name`,
				s.`voucher`,
				s.`display_gender`,
				s.`display_firstname`,
				s.`display_lastname`,
				s.`display_language`,
				s.`display_birthday`,
				s.`display_list_of_interest`,
				s.`list_of_interest_type`,
				s.`display_subscribe_message`,
				s.`date_add`,
				s.`render_loader`,
				sl.`id_lang`,
				sl.`id_shop`,
				ss.`active`,
				ss.`show_on_pages`,
				ss.`cookie_lifetime`,
				ss.`start_timer`,
				ss.`when_to_show`,
				ss.`terms_and_conditions_url`
			',
            'id_lang' => (int) $id_lang,
            'id_shop' => (int) $id_shop,
        ]));

        return $result;
    }

    public static function getTemplatesSql($config = [])
    {
        $context = Context::getContext();

        $select = isset($config['select']) ? $config['select'] : '*';

        if (isset($config['id_lang'])) {
            $id_lang = (int) $config['id_lang'];
        } else {
            $id_lang = (int) $context->language->id;
        }

        if (isset($config['id_shop'])) {
            $id_shop = (int) $config['id_shop'];
        } else {
            $id_shop = (int) $context->shop->id;
        }

        $sql = [];
        $sql[] = '
			SELECT '.$select.' FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` s
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_lang` sl
				ON (s.`id_newsletter_pro_subscription_tpl` = sl.`id_newsletter_pro_subscription_tpl`)

			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_shop` ss
				ON (sl.`id_newsletter_pro_subscription_tpl` = ss.`id_newsletter_pro_subscription_tpl`
					AND sl.`id_shop` = ss.`id_shop` )

			WHERE sl.`id_lang` = '.(int) $id_lang.'
			AND sl.`id_shop` = '.(int) $id_shop;

        if (isset($config['and'])) {
            $sql[] = 'AND '.$config['and'];
        }

        $sql[] = 'ORDER BY s.`date_add`
			DESC
		';

        return implode(' ', $sql);
    }

    public static function getControllerTemplates()
    {
        $results = Db::getInstance()->executeS('
			SELECT `id_newsletter_pro_subscription_tpl`, `name` FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` st
			WHERE `render_loader` = 1
		');

        $selectedTemplateId = pqnp_config_get('SUBSCRIPTION_CONTROLLER_TEMPLATE_ID', 0);

        $results = array_map(function ($row) use ($selectedTemplateId) {
            $row['selected'] = false;
            if ((int) $row['id_newsletter_pro_subscription_tpl'] == (int) $selectedTemplateId) {
                $row['selected'] = true;
            }

            return $row;
        }, $results);

        return $results;
    }

    public static function getActiveTemplatesAllShops($id_lang = null)
    {
        $context = Context::getContext();

        if (!isset($id_lang)) {
            $id_lang = (int) $context->language->id;
        }

        $sql = [];

        $sql[] = '
			SELECT
				s.`id_newsletter_pro_subscription_tpl`,
				s.`name`,
				s.`voucher`,
				s.`display_gender`,
				s.`display_firstname`,
				s.`display_lastname`,
				s.`display_language`,
				s.`display_birthday`,
				s.`display_list_of_interest`,
				s.`list_of_interest_type`,
				s.`display_subscribe_message`,
				s.`date_add`,
				sl.`id_lang`,
				sl.`id_shop`,
				ss.`active`,
				ss.`show_on_pages`,
				ss.`cookie_lifetime`,
				ss.`start_timer`,
				ss.`when_to_show`,
				ss.`terms_and_conditions_url`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` s
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_lang` sl
				ON (s.`id_newsletter_pro_subscription_tpl` = sl.`id_newsletter_pro_subscription_tpl`)

			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_shop` ss
				ON (sl.`id_newsletter_pro_subscription_tpl` = ss.`id_newsletter_pro_subscription_tpl`
					AND sl.`id_shop` = ss.`id_shop` ) ';
        $sql[] = 'WHERE sl.`id_lang` = '.(int) $id_lang;
        $sql[] = 'AND ss.`active` = 1 ';
        $sql[] = 'ORDER BY s.`date_add`
			DESC
		';

        $sql_str = implode(' ', $sql);

        return Db::getInstance()->executeS($sql_str);
    }

    public function renderAll($id_lang = null)
    {
        return [
            'content' => $this->render($id_lang),
            'subscribe_message' => $this->renderSubscribeMessage($id_lang),
            'email_subscribe_voucher_message' => $this->renderEmailSubscribeVoucherMessage($id_lang),
            'email_subscribe_confirmation_message' => $this->renderEmailSubscribeConfirmationMessage($id_lang),
            'email_unsubscribe_confirmation_message' => $this->renderEmailUnsubscribeConfirmationMessage($id_lang),
        ];
    }

    public function render($id_lang = null)
    {
        return $this->renderByField('content', $id_lang);
    }

    public function renderSubscribeMessage($id_lang = null)
    {
        return $this->renderByField('subscribe_message', $id_lang);
    }

    public function renderEmailSubscribeVoucherMessage($id_lang = null)
    {
        return $this->renderByField('email_subscribe_voucher_message', $id_lang);
    }

    public function renderEmailSubscribeConfirmationMessage($id_lang = null)
    {
        return $this->renderByField('email_subscribe_confirmation_message', $id_lang);
    }

    public function renderEmailUnsubscribeConfirmationMessage($id_lang = null)
    {
        return $this->renderByField('email_unsubscribe_confirmation_message', $id_lang);
    }

    public function renderByField($field_name, $id_lang = null)
    {
        if (!isset($id_lang)) {
            $id_lang = $this->context->language->id;
        }

        $default_lang = (int) pqnp_config('PS_LANG_DEFAULT');

        $content = '';
        if (isset($this->{$field_name}[$id_lang])) {
            $content = $this->{$field_name}[$id_lang];
        } else {
            $content = $this->{$field_name}[$default_lang];
        }

        if (self::RENDER_LOADER_BETTER === (int) $this->render_loader) {
            $content_final = $this->renderContent($content, $id_lang);
        } else {
            // default one
            $content_final = $this->getRenderedContent($content, $id_lang);
        }

        return $content_final;
    }

    public function getMandatoryFields()
    {
        return NewsletterProTools::unSerialize($this->mandatory_fields);
    }

    public function isMandatory($field_name)
    {
        $mandatory_fields = $this->getMandatoryFields();
        if (in_array($field_name, $mandatory_fields)) {
            return true;
        }

        return false;
    }

    public function renderContent($content, $id_lang)
    {
        // mandatory_fields
        $context = Context::getContext();
        $customer = isset($context->customer) && $context->customer->isLogged() ? $context->customer : null;
        $customer_id = isset($customer) ? (int) $customer->id : 0;
        $customer_email = isset($customer) ? $customer->email : '';
        $subscriber = NewsletterProSubscribers::getInstanceByEmail($customer_email);

        $languages = array_map(function ($language) {
            return [
                'id' => $language['id_lang'],
                'name' => $language['name'],
                'iso_code' => $language['iso_code'],
            ];
        }, Language::getLanguages(true));
        $language_tmp = new Language((int) $id_lang);
        $language = [
            'id' => (int) $language_tmp->id,
            'name' => $language_tmp->name,
            'iso_code' => $language_tmp->iso_code,
        ];
        $genders_tmp = Gender::getGenders((int) $id_lang);
        $genders = [];

        foreach ($genders_tmp as $gender) {
            $genders[$gender->id] = [
                'id' => (int) $gender->id,
                'name' => $gender->name,
            ];
        }

        $firstname = '';
        $lastname = '';
        $birthday = '';
        $birthday_year = '';
        $birthday_month = '';
        $birthday_day = '';

        $gender = [
            'id' => 0,
            'name' => '-',
        ];

        $render_language_options = implode("\n", array_map(function ($item) use ($language) {
            return '<option value="'.$item['id'].'" '.((int) $language['id'] == (int) $item['id'] ? 'selected' : '').'>'.$item['name'].'</option>';
        }, $languages));

        $list_of_interest = array_map(function ($item) {
            return array_merge($item, [
                'checked' => true,
            ]);
        }, NewsletterProListOfInterest::getListActive());

        if (Validate::isLoadedObject($subscriber)) {
            $list_of_interest = NewsletterProListOfInterest::getListActiveSubscriber($customer_email);

            if (array_key_exists($subscriber->id_gender, $genders)) {
                $gender = $genders[$subscriber->id_gender];
            }

            $firstname = $subscriber->firstname;
            $lastname = $subscriber->lastname;
            $birthday = Validate::isDate($subscriber->birthday) ? $subscriber->birthday : '';

            if (Tools::strlen($birthday) > 0) {
                $birthday_split = explode('-', $birthday);

                $birthday_year = $birthday_split[0];
                $birthday_month = $birthday_split[1];
                $birthday_day = $birthday_split[2];
            }

            // pqd('asdfdadfsa', $firstname, $language, $birthday);
        } elseif (Validate::isLoadedObject($customer)) {
            $list_of_interest = NewsletterProListOfInterest::getListActiveCustomer((int) $customer_id, $id_lang, (int) $context->shop->id);

            if (array_key_exists($customer->id_gender, $genders)) {
                $gender = $genders[$customer->id_gender];
            }

            $firstname = $customer->firstname;
            $lastname = $customer->lastname;
            $birthday = Validate::isDate($customer->birthday) ? $customer->birthday : '';

            if (Tools::strlen($birthday) > 0) {
                $birthday_split = explode('-', $birthday);

                $birthday_year = $birthday_split[0];
                $birthday_month = $birthday_split[1];
                $birthday_day = $birthday_split[2];
            }
        }

        $render_gender_options = implode("\n", array_map(function ($item) use ($gender) {
            return '<option value="'.$item['id'].'" '.((int) $gender['id'] == (int) $item['id'] ? 'selected' : '').'>'.$item['name'].'</option>';
        }, $genders));

        $render_birthday_year = implode("\n", array_map(function ($item) use ($birthday_year) {
            return '<option value="'.$item.'" '.((string) $birthday_year === (string) $item ? 'selected' : '').'>'.$item.'</option>';
        }, Tools::dateYears()));

        $render_birthday_month = [];

        foreach ($this->module->dateMonths() as $key => $value) {
            $render_birthday_month[] = '<option value="'.$key.'" '.((string) $birthday_month === (string) $key ? 'selected' : '').'>'.$value.'</option>';
        }

        $render_birthday_month = join("\n", $render_birthday_month);

        $render_birthday_day = implode("\n", array_map(function ($item) use ($birthday_day) {
            return '<option value="'.$item.'" '.((string) $birthday_day == (string) $item ? 'selected' : '').'>'.$item.'</option>';
        }, Tools::dateDays()));

        $mandatory_fields = $this->getMandatoryFields();
        $mandatory_vars = [];
        foreach ($mandatory_fields as $value) {
            $mandatory_vars['is_mandatory_'.$value] = true;
        }

        $template_vars = array_merge([
            'languages' => $languages,
            'genders' => $genders,
            'field' => [
                'email' => 'email',
                'firstname' => 'firstname',
                'lastname' => 'lastname',
                'gender' => 'gender',
                'birthday' => 'birthday',
                'birthday_year' => 'birthday_year',
                'birthday_month' => 'birthday_month',
                'birthday_day' => 'birthday_day',
                'list_of_interest' => 'list_of_interest[]',
                'terms_and_conditions' => 'terms_and_conditions',
            ],
            'id' => [
                'form' => 'pqnp-pupup-form',
                'email' => 'pqnp-popup-email',
                'firstname' => 'pqnp-popup-firstname',
                'lastname' => 'pqnp-popup-lastname',
                'gender' => 'pqnp-popup-gender',
                'birthday' => 'pqnp-popup-birthday',
                'birthday_year' => 'pqnp-popup-birthday-year',
                'birthday_month' => 'pqnp-popup-birthday-month',
                'birthday_day' => 'pqnp-popup-birthday-day',
                'terms_and_conditions' => 'pqnp-popup-terms-and-conditions',
                'error' => 'pqnp-popup-error',
                'success' => 'pqnp-popup-success',
                'subscribe' => 'pqnp-popup-subscribe',
                'unsubscribe' => 'pqnp-popup-unsubscribe',
                'destroy' => 'pqnp-popup-destroy',
                'close_timeout' => 'pqnp-popup-close-timeout',
            ],
            'actions' => [
            ],
            'form' => [
                'email' => '',
                'firstname' => $firstname,
                'lastname' => $lastname,
                'birthday' => $birthday,
                'gender' => $gender['id'],
                'language' => $language['id'],
            ],
            'mandatory_fields' => $mandatory_fields,
            'display_gender' => (int) $this->display_gender,
            'display_firstname' => (int) $this->display_firstname,
            'display_lastname' => (int) $this->display_lastname,
            'display_language' => (int) $this->display_language,
            'display_birthday' => (int) $this->display_birthday,
            'display_list_of_interest' => ((int) $this->display_list_of_interest && count($list_of_interest) > 0),
            'terms_and_conditions_url' => $this->terms_and_conditions_url,
            'render_mandatory_firstname' => in_array('firstname', $mandatory_fields) ? '<sup>*</sup>' : '',
            'render_mandatory_lastname' => in_array('lastname', $mandatory_fields) ? '<sup>*</sup>' : '',
            'render_gender_options' => $render_gender_options,
            'render_language_options' => $render_language_options,
            'render_birthday_year' => $render_birthday_year,
            'render_birthday_month' => $render_birthday_month,
            'render_birthday_day' => $render_birthday_day,
            'list_of_interest' => $list_of_interest,
            'list_of_interest_type' => $this->list_of_interest_type,
        ], $mandatory_vars, $this->getDefaultVars($id_lang), $this->getVoucherVars($id_lang), $this->getCustomFields($id_lang, $customer_email), $this->extend_vars);

        return NewsletterProHTMLRender::output($content, $template_vars, true, false);
    }

    public function getRenderedContent($content, $id_lang)
    {
        $content_final = $content;
        $conditions = $this->getConditions($content);
        $template_vars = $this->getTemplateVars($id_lang);

        foreach ($conditions as $var => $value) {
            if (isset($template_vars[$var])) {
                if ($template_vars[$var]) {
                    $content_final = str_replace($value['match'], $value['replace'], $content_final);
                } else {
                    $content_final = str_replace($value['match'], '', $content_final);
                }
            }
        }
        // replace vars
        $content_final = $this->replaceVars($content_final, $template_vars);

        return $content_final;
    }

    public function replaceVars($template, $variables = [])
    {
        self::$replace_vars = $variables;
        $template = preg_replace_callback(
            '/\{(?P<tag>\w+)\}/',
            [$this, 'replaceCallback'],
            $template
        );

        return $template;
    }

    public function replaceCallback($matches)
    {
        $tag = $matches[1]; // tag

        return (isset(self::$replace_vars[$tag])) ? self::$replace_vars[$tag] : '{'.$tag.'}';
    }

    private function getConditions($content)
    {
        $conditions = [];
        if (preg_match_all('/(?P<all>\{if\s(?:\s+)?(?P<variables>\w+[^}])\}(?P<if>[\s\S]*?)\{\/if\})/', $content, $matches)) {
            $variables = $matches['variables'];
            $if = $matches['if'];
            $all = $matches['all'];

            foreach ($variables as $key => $variable) {
                if (isset($if[$key])) {
                    $conditions[$variable] = [
                        'match' => $all[$key],
                        'replace' => $if[$key],
                    ];
                }
            }
        }

        return $conditions;
    }

    private function getVoucherVars($id_lang)
    {
        $voucher = $this->getVoucher();

        $voucher_vars = [
            'voucher' => $voucher,
            'voucher_name' => false,
            'voucher_quantity' => false,
            'voucher_value' => false,
        ];

        if ($voucher && ($id_cart_rule = CartRule::getIdByCode($voucher))) {
            $cart_rule = new CartRule($id_cart_rule);
            if (Validate::isLoadedObject($cart_rule)) {
                // if the name language exists, other values exists alsow
                if (isset($cart_rule->name[$id_lang])) {
                    $voucher_vars['voucher_name'] = $cart_rule->name[$id_lang];
                }

                $voucher_vars['voucher_quantity'] = $cart_rule->quantity;

                if ($cart_rule->reduction_percent > 0) {
                    $voucher_vars['voucher_value'] = $cart_rule->reduction_percent.'%';
                } elseif ($cart_rule->reduction_amount > 0) {
                    if ((int) $this->context->currency->id != (int) $cart_rule->reduction_currency) {
                        $price_convert = Tools::convertPrice((float) $cart_rule->reduction_amount, (int) $cart_rule->reduction_currency, false);
                    } else {
                        $price_convert = (float) $cart_rule->reduction_amount;
                    }

                    $price_display = Tools::displayPrice($price_convert);
                    $voucher_vars['voucher_value'] = $price_display;
                }
            }
        }

        return $voucher_vars;
    }

    private function getCustomVars()
    {
        $custom_vars = [];

        $variables_name = NewsletterProSubscribersCustomField::getVariables();

        foreach ($variables_name as $variable_name) {
            $field = NewsletterProSubscribersCustomField::getInstanceByVariableName($variable_name);
            if ($field) {
                $custom_vars[$variable_name] = $field->render();
            }
        }

        return $custom_vars;
    }

    private function getCustomFields($id_lang, $customer_email = '')
    {
        $custom_vars = [];
        $variables_name = NewsletterProSubscribersCustomField::getVariables();

        foreach ($variables_name as $variable_name) {
            $field = NewsletterProSubscribersCustomField::getInstanceByVariableName($variable_name);
            if ($field) {
                $custom_vars[$variable_name] = $field->renderBetterLoader($id_lang, $customer_email);
            }
        }

        return $custom_vars;
    }

    private function getDefaultVars($id_lang)
    {
        $shop_url = $this->context->link->getPageLink('index', null, $id_lang, [], false, (int) $this->context->shop->id);
        $shop_name = $this->context->shop->name;
        $shop_logo_url = $this->module->getShopLogoUrl();
        $shop_logo = '<a title="'.$this->context->shop->name.'" href="'.$shop_url.'"> <img style="border: none;" src="'.$shop_logo_url.'" alt="'.$shop_name.'" /> </a>';

        return [
            'shop_url' => $shop_url,
            'shop_name' => $shop_name,
            'shop_logo_url' => $shop_logo_url,
            'shop_logo' => $shop_logo,
            'module_url' => Tools::getHttpHost(true).$this->module->uri_location,
            'module_path' => $this->module->uri_location,
        ];
    }

    public function getTemplateVars($id_lang)
    {
        $vars = [
            'displayGender' => ($this->display_gender ? $this->getGenderHTML() : false),
            'displayFirstName' => ($this->display_firstname ? $this->getFirstNameHTML() : false),
            'displayLastName' => ($this->display_lastname ? $this->getLastNameHTML() : false),
            'displayEmail' => $this->getEmailHTML(),
            'displayLanguages' => ($this->display_language ? $this->getLanguagesHTML() : false),
            'displayBirthday' => ($this->display_birthday ? $this->getBirthdayHTML() : false),
            'displayInfo' => $this->getInfoHTML(),
            'displayListOfInterest' => ($this->display_list_of_interest ? $this->getListOfInterestHTML() : false),
            'submitButton' => $this->getSubmitHTML(),
            'displayTermsAndConditionsLink' => $this->getTermsAndConditionsLinkHTML(),
            'displayTermsAndConditionsCheckbox' => $this->getTermsAndConditionsCheckboxHTML(),
            'displayTermsAndConditionsFull' => $this->getTermsAndConditionsFullHTML(),
            'close_forever' => $this->getCloseForeverHTML(),
            'close_forever_onclick_function' => 'NewsletterPro.modules.newsletterSubscribe.closeForever();',
        ];

        $all_vars = array_merge($vars, $this->getDefaultVars($id_lang), $this->getVoucherVars($id_lang), $this->extend_vars, $this->getCustomVars());

        return $all_vars;
    }

    public function extendVars($vars)
    {
        $this->extend_vars = array_merge($this->extend_vars, $vars);
    }

    public function getVoucher()
    {
        if (isset($this->voucher) && !NewsletterProTools::isEmpty($this->voucher)) {
            return $this->voucher;
        }

        return false;
    }

    public function getCartRuleId()
    {
        return (int) CartRule::getIdByCode($this->voucher);
    }

    public function getVoucherCode()
    {
        if (isset($this->voucher)) {
            return trim($this->voucher);
        }

        return '';
    }

    public function hasValidVoucher()
    {
        return (int) $this->getCartRuleId() > 0 && Tools::strlen($this->getVoucherCode()) > 0;
    }

    public function getStyle()
    {
        $style = '';
        $style .= '<style type="text/css">';
        $style .= $this->css_style;
        $style .= '</style>'."\n";

        return $style;
    }

    public function getStyleLink()
    {
        return '<link rel="stylesheet" type="text/css" href="'.$this->getSubscriptionCSSLink().'">'."\n";
    }

    public function getSubscriptionCSSLinkWithDetails($id_shop = null)
    {
        if (!isset($id_shop)) {
            $id_shop = (int) $this->context->shop->id;
        }

        $css_filename = $this->getCSSStyleFileName($id_shop);
        $full_path = $this->css_dir_path.'/'.$css_filename;

        $link = '';
        $file_exists = false;

        if (file_exists($full_path) && is_file($full_path) && is_readable($full_path)) {
            $file_exists = true;
            $link = $this->css_uri_path.'/'.$css_filename;
        } else {
            $file_exists = false;
            // $link = _MODULE_DIR_.'newsletterpro/css.php?getSubscriptionCSS&idTemplate='.(int)$this->id.'&idShop='.(int)$id_shop.'&uid='.uniqid();

            $link = NewsletterProApi::getLink('css', [
                'getSubscriptionCSS' => true,
                'idTemplate' => (int) $this->id,
                'idShop' => (int) $id_shop,
                'uid' => uniqid(),
            ], false, true);
        }

        return [
            'link' => $link,
            'file_exists' => (bool) $file_exists,
        ];
    }

    public function getSubscriptionCSSLink($id_shop = null)
    {
        $details = $this->getSubscriptionCSSLinkWithDetails($id_shop);

        return $details['link'];
    }

    public function saveCSSStyleAsFile($content, $id_shop = null)
    {
        if (!isset($id_shop)) {
            $id_shop = (int) $this->context->shop->id;
        }

        if (file_exists($this->css_dir_path)) {
            $css_filename = $this->getCSSStyleFileName($id_shop);

            $full_path = $this->css_dir_path.'/'.$css_filename;

            $h = @fopen($full_path, 'w');
            $success = fwrite($h, $content);
            fclose($h);

            return $success;
        }

        return false;
    }

    public function getCSSStyleFileName($id_shop = null)
    {
        if (!isset($id_shop)) {
            $id_shop = (int) $this->context->shop->id;
        }

        return $this->name.'.'.(int) $this->id.'.'.$id_shop.'.css';
    }

    /**
     * this should be use only in the bakcoffice.
     *
     * @param bool $get_link
     *
     * @return string
     */
    public function getGlobalStyle($get_link = true)
    {
        $style = '';
        foreach (array_keys($this->getFrontOfficeCSSFiles()) as $path) {
            $style .= '<link rel="stylesheet" type="text/css" href="'.$path.'">'."\n";
        }

        $info = $this->getCssGlobalStyleInfo();
        $style .= '<link rel="stylesheet" type="text/css" href="'.$info['full_path'].'">'."\n";

        if ($get_link) {
            $style .= $this->getStyleLink();
        } else {
            $style .= $this->getStyle();
        }

        return $style;
    }

    public function getGlobalStyleLinks($uniqid = false)
    {
        $links = [];
        foreach (array_keys($this->getFrontOfficeCSSFiles()) as $path) {
            if ($uniqid) {
                $links[] = $path.'?uid='.uniqid();
            } else {
                $links[] = $path;
            }
        }

        $info = $this->getCssGlobalStyleInfo();

        $links[] = $info['full_path'];
        $links[] = $this->getSubscriptionCSSLink();

        return $links;
    }

    /**
     * this should be use only in the bakcoffice.
     *
     * @param bool $js_regex
     * @param bool $get_link
     *
     * @return string
     */
    public function getGlobalHeader($js_regex = false, $get_link = true)
    {
        $output = '';

        $controller_header = $this->getFrontControllerHeader();

        $info = $this->getCssGlobalStyleInfo();
        $output .= '<link rel="stylesheet" type="text/css" href="'.$info['full_path'].'">'."\n";

        if ($controller_header) {
            $css_files = $controller_header['css_files'];
            $header = $controller_header['header'];
            $js_files = $controller_header['js_files'];

            foreach (array_keys($css_files) as $path) {
                $output .= '<link rel="stylesheet" type="text/css" href="'.$path.'">'."\n";
            }

            if ($get_link) {
                $output .= $this->getStyleLink();
            } else {
                $output .= $this->getStyle();
            }

            if ($js_regex) {
                $grep = preg_grep($js_regex, $js_files);

                foreach ($grep as $path) {
                    $output .= '<script type="text/javascript" src="'.$path.'"></script>'."\n";
                }
            } else {
                foreach ($js_files as $path) {
                    $output .= '<script type="text/javascript" src="'.$path.'"></script>'."\n";
                }
            }

            $output .= $header;
        } else {
            $css_files = [];
            $css_files[] = _THEME_CSS_DIR_.'grid_prestashop.css';
            $css_files[] = _THEME_CSS_DIR_.'global.css';

            foreach (array_keys($css_files) as $path) {
                $output .= '<link rel="stylesheet" type="text/css" href="'.$path.'">'."\n";
            }

            if ($get_link) {
                $output .= $this->getStyleLink();
            } else {
                $output .= $this->getStyle();
            }
        }

        return $output;
    }

    public function getFrontOfficeCSSFiles()
    {
        $css_files = [];

        $controller_header = $this->getFrontControllerHeader();

        if ($controller_header) {
            $css_files = $controller_header['css_files'];
        } else {
            $css_files[] = _THEME_CSS_DIR_.'grid_prestashop.css';
            $css_files[] = _THEME_CSS_DIR_.'global.css';
        }

        return $css_files;
    }

    private function getThemeDir()
    {
        return $this->useMobileTheme() ? _PS_THEME_MOBILE_DIR_ : _PS_THEME_DIR_;
    }

    private function useMobileTheme()
    {
        static $use_mobile_template = null;

        if (null === $use_mobile_template) {
            $use_mobile_template = ($this->context->getMobileDevice() && file_exists(_PS_THEME_MOBILE_DIR_.'layout.tpl'));
        }

        return $use_mobile_template;
    }

    public function getFrontControllerHeader()
    {
        try {
            $css_module_path = $this->module->getCssPath();
            $css_module_path_default = $this->module->getCssPath(true);

            $this->context->cookie = new Cookie('ps');
            $this->context->controller = new FrontController();
            $this->context->customer = new Customer();
            $this->context->cart = new Cart();

            if (1 == pqnp_ini_config('load_subscription_front_controller')) {
                try {
                    if (method_exists($this->context->controller, 'init')) {
                        $this->context->controller->init();
                    }

                    if (method_exists($this->context->controller, 'setMedia')) {
                        $this->context->controller->setMedia();
                    }
                } catch (Exception $e) {
                    // do nothing
                }
            } elseif (2 == pqnp_ini_config('load_subscription_front_controller')) {
                try {
                    $this->context->controller->addCSS(_THEME_CSS_DIR_.'grid_prestashop.css', 'all');  // retro compat themes 1.5.0.1
                    $this->context->controller->addCSS(_THEME_CSS_DIR_.'global.css', 'all');
                    $this->context->controller->addJquery();
                    $this->context->controller->addJqueryPlugin('easing');
                    $this->context->controller->addJS(_PS_JS_DIR_.'tools.js');
                    $this->context->controller->addJS(_THEME_JS_DIR_.'global.js');

                    if (@filemtime($this->getThemeDir().'js/autoload/')) {
                        foreach (scandir($this->getThemeDir().'js/autoload/', 0) as $file) {
                            if (preg_match('/^[^.].*\.js$/', $file)) {
                                $this->context->controller->addJS($this->getThemeDir().'js/autoload/'.$file);
                            }
                        }
                    }

                    if (@filemtime($this->getThemeDir().'css/autoload/')) {
                        foreach (scandir($this->getThemeDir().'css/autoload', 0) as $file) {
                            if (preg_match('/^[^.].*\.css$/', $file)) {
                                $this->context->controller->addCSS($this->getThemeDir().'css/autoload/'.$file);
                            }
                        }
                    }

                    if (Configuration::get('PS_QUICK_VIEW')) {
                        $this->context->controller->addjqueryPlugin('fancybox');
                    }

                    if (Configuration::get('PS_COMPARATOR_MAX_ITEM') > 0) {
                        $this->context->controller->addJS(_THEME_JS_DIR_.'products-comparison.js');
                    }

                    // Execute Hook FrontController SetMedia
                    Hook::exec('actionFrontControllerSetMedia', []);
                } catch (Exception $e) {
                    // do nothing
                }
            }

            if (pqnp_ini_config('load_subscription_hook_header')) {
                $header = Hook::exec('displayHeader');
            } else {
                $header = '';
            }

            $css_files = $this->context->controller->css_files;
            $js_files = $this->context->controller->js_files;

            if (NewsletterPro::isUniformRequired()) {
                $css_files[$css_module_path_default.'uniform.default.css'] = 'all';
            }

            if (NewsletterPro::isFontAwesomeRequired()) {
                $css_files[$css_module_path_default.'font-awesome.css'] = 'all';
            }

            $css_files[$css_module_path.'front_window.css'] = 'all';

            // filter the undesired scripts that can cause an error
            $js_files = preg_grep('/modules\/newsletterpro|jquery|bootstrap\.min\.js|jquery\.uniform\.min\.js/', $js_files);

            // restore the controller
            $this->context->cookie = new Cookie('psAdmin');
            $this->context->controller = Controller::getController($this->module->class_name);

            try {
                $this->context->controller->init();
                if (method_exists($this->context->controller, 'initToolbar')) {
                    $this->context->controller->initToolbar();
                }
                if (method_exists($this->context->controller, 'initPageHeaderToolbar')) {
                    $this->context->controller->initPageHeaderToolbar();
                }
                if (method_exists($this->context->controller, 'setMedia')) {
                    $this->context->controller->setMedia();
                }
                if (method_exists($this->context->controller, 'initHeader')) {
                    $this->context->controller->initHeader();
                }
                if (method_exists($this->context->controller, 'initFooter')) {
                    $this->context->controller->initFooter();
                }
            } catch (Exception $e) {
                NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
            }

            return [
                'header' => $header,
                'css_files' => $css_files,
                'js_files' => $js_files,
            ];
        } catch (Exception $e) {
            NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);

            return false;
        }

        return false;
    }

    public static function templateIdExists($id)
    {
        if (Db::getInstance()->getValue('
			SELECT `id_newsletter_pro_subscription_tpl`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl`
			WHERE `id_newsletter_pro_subscription_tpl` = '.(int) $id.'
		')) {
            return (int) $id;
        } else {
            return false;
        }
    }

    public function getCssGlobalStyleInfo()
    {
        $info = pathinfo(self::CSS_STYLE_GLOBAL_PATH);

        return [
            'path' => $this->module->uri_location.'views/css/'.NewsletterProTools::getVersion().'/',
            'dir_path' => $this->module->dir_location.'views/css/'.NewsletterProTools::getVersion().'/',
            'dir_path_full' => $this->module->dir_location.'views/css/'.NewsletterProTools::getVersion().'/'.$info['basename'],
            'full_path' => $this->module->uri_location.'views/css/'.NewsletterProTools::getVersion().'/'.$info['basename'],
            'basename' => $info['basename'],
            'extension' => $info['extension'],
            'filename' => $info['filename'],
        ];
    }

    public function getCssGlobalStyleContent()
    {
        $info = $this->getCssGlobalStyleInfo();

        $filename = $info['dir_path'].$info['basename'];
        if (file_exists($filename)) {
            $content = Tools::file_get_contents($filename);
            if (false !== $content) {
                return $content;
            }
        }

        return '';
    }

    public function getTermsAndConditionsLinkHTML()
    {
        $url = trim($this->terms_and_conditions_url);

        return $this->fetchComponent([
            'get_terms_and_conditions_link' => true,
            'terms_and_conditions_url' => (!empty($url) ? $url : '#'),
        ]);
    }

    public function getTermsAndConditionsCheckboxHTML()
    {
        return $this->fetchComponent([
            'get_terms_and_conditions_checkbox' => true,
        ]);
    }

    public function getTermsAndConditionsFullHTML()
    {
        return $this->fetchComponent([
            'get_terms_and_conditions_full' => true,
            'gtac_link' => $this->getTermsAndConditionsLinkHTML(),
            'gtac_checkbox' => $this->getTermsAndConditionsCheckboxHTML(),
        ]);
    }

    public function getGenderHTML()
    {
        return $this->fetchComponent([
            'get_gender' => true,
            'genders' => Gender::getGenders(),
        ]);
    }

    public function getFirstNameHTML()
    {
        return $this->fetchComponent([
            'get_firstname' => true,
        ]);
    }

    public function getLastNameHTML()
    {
        return $this->fetchComponent([
            'get_lastname' => true,
        ]);
    }

    public function getLanguagesHTML()
    {
        $languages = $this->module->getLanguages();

        return $this->fetchComponent([
            'get_languages' => true,
            'langs_sub' => $languages,
        ]);
    }

    public function getBirthdayHTML()
    {
        return $this->fetchComponent([
            'get_birthday' => true,
            'years' => Tools::dateYears(),
            'months' => $this->module->dateMonths(),
            'days' => Tools::dateDays(),
        ]);
    }

    public function getListOfInterestHTML()
    {
        return $this->fetchComponent([
            'get_list_of_interest' => true,
            'list_of_interest_type' => $this->list_of_interest_type,
            'LIST_OF_INTEREST_TYPE_SELECT' => self::LIST_OF_INTEREST_TYPE_SELECT,
            'LIST_OF_INTEREST_TYPE_CHECKBOX' => self::LIST_OF_INTEREST_TYPE_CHECKBOX,
            'list_of_interest' => NewsletterProListOfInterest::getListActive(),
        ]);
    }

    public function getEmailHTML()
    {
        return $this->fetchComponent([
            'get_email' => true,
        ]);
    }

    public function getSubmitHTML()
    {
        return $this->fetchComponent([
            'get_submit' => true,
        ]);
    }

    public function getCloseForeverHTML()
    {
        return $this->fetchComponent([
            'get_close_forever' => true,
        ]);
    }

    public function getInfoHTML()
    {
        return $this->fetchComponent([
            'get_info' => true,
        ]);
    }

    public function fetchComponent($params = [])
    {
        $tpl = $this->context->smarty->createTemplate(pqnp_template_path($this->module->dir_location.'views/templates/hook/newsletter_subscribe_components.tpl'));
        $tpl->assign($params);

        return $tpl->fetch();
    }

    public static function getActiveTemplateInstance()
    {
        $context = Context::getContext();

        $sql = '
			SELECT s.`id_newsletter_pro_subscription_tpl`
			FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` s
			LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_subscription_tpl_shop` ss
				ON (s.`id_newsletter_pro_subscription_tpl` = ss.`id_newsletter_pro_subscription_tpl`)
			WHERE ss.`active` = 1
			AND ss.`id_shop` = '.(int) $context->shop->id.'
		';

        $id = Db::getInstance()->getValue($sql);

        if (!$id) {
            $sql = '
				SELECT s.`id_newsletter_pro_subscription_tpl`
				FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl` s
				WHERE s.`active` = 1
			';
            $id = Db::getInstance()->getValue($sql);
        }

        if ($id) {
            return new NewsletterProSubscriptionTpl((int) $id);
        } else {
            self::setActiveIfNotExists();
            $id = Db::getInstance()->getValue($sql);
            if ($id) {
                return new NewsletterProSubscriptionTpl((int) $id);
            }
        }

        return false;
    }

    public function getJSData()
    {
        $one_day = 60 * 60 * 24;
        $life_time_days = round($one_day * (float) $this->cookie_lifetime);

        $cookie = new NewsletterProCookie('subscription_template_front', time() + $life_time_days);

        if (!$cookie->exists('popup_show')) {
            $cookie->set('popup_show', '1');
        }

        $popup_show = (int) $cookie->get('popup_show');

        $page_name = (Tools::getValue('controller') ? Tools::getValue('controller') : $this->context->controller->php_self);

        // if the page is a cms page
        if ('cms' == $page_name && Tools::isSubmit('id_cms') && Tools::getValue('id_cms') && preg_match('/cms-\d+/', $this->show_on_pages)) {
            $id_cms = (int) Tools::getValue('id_cms');
            if (Tools::strtolower('cms'.'-'.$id_cms) == Tools::strtolower($this->show_on_pages)) {
                $page_name = $this->show_on_pages;
            }
        }

        $bool_show_on_page = 0;
        if ($page_name == $this->show_on_pages) {
            $bool_show_on_page = 1;
        } elseif (self::SHOW_ON_PAGES_ALL == $this->show_on_pages) {
            $bool_show_on_page = 1;
        } elseif (self::SHOW_ON_PAGES_NONE == $this->show_on_pages) {
            $bool_show_on_page = 0;
        }

        if (self::WHEN_TO_SHOW_POPUP_ALWAYS == (int) $this->when_to_show) {
            $display_popup = ($bool_show_on_page ? true : false);
        } else {
            $display_popup = ($popup_show && $bool_show_on_page ? true : false);
        }

        return [
            'id' => (int) $this->id,
            'load_file' => $this->getLoadFileBasename(),
            // 'display_subscribe_message'        => (bool)$this->display_subscribe_message,
            'subscription_template_front_info' => [
                'body_width' => $this->body_width,
                'body_min_width' => (int) $this->body_min_width,
                'body_max_width' => (int) $this->body_max_width,
                'body_top' => (int) $this->body_top,
                'show_on_pages' => $this->show_on_pages,
                'cookie_lifetime' => (float) $this->cookie_lifetime,
                'start_timer' => (int) $this->start_timer,
                'when_to_show' => (int) $this->when_to_show,
                'bool_show_on_page' => $bool_show_on_page,
                'popup_show_cookie' => (bool) $popup_show,
                'display_popup' => (bool) $display_popup,
                'close_forever' => (bool) pqnp_ini_config('close_forever'),
            ],
            'configuration' => [
                'CROSS_TYPE_CLASS' => pqnp_config('CROSS_TYPE_CLASS'),
            ],
        ];
    }

    public function getLoadFileBasename()
    {
        return isset($this->load_file) ? pathinfo($this->load_file[0], PATHINFO_BASENAME) : null;
    }

    public static function getFrontLink($id_lang = null, $id_shop = null)
    {
        $context = Context::getContext();

        $link = $context->link->getPageLink('index', null, $id_lang, ['newsletterproSubscribe' => 1], false, $id_shop);

        return $link;
    }

    public static function getPagesAsMeta()
    {
        $selected_pages = [];
        $context = Context::getContext();

        $path = _PS_ROOT_DIR_.'/controllers/front/';
        if (file_exists($path)) {
            $dirs = new DirectoryIterator($path);
            $filesi = new RegexIterator($dirs, '/.php$/', RecursiveRegexIterator::MATCH);

            $files = [];
            foreach ($filesi as $file) {
                if (method_exists($file, 'getBasename')) {
                    $files[] = $file->getBasename();
                } else {
                    $files[] = basename($file->getFilename());
                }
            }

            $exlude_pages = [
                'category', 'changecurrency', 'cms', 'footer', 'header',
                'pagination', 'product', 'product-sort', 'statistics',
            ];

            foreach ($files as $file) {
                if ('index.php' != $file && preg_match('/^[a-z0-9_.-]*\.php$/i', $file) && !in_array(Tools::strtolower(str_replace('Controller.php', '', $file)), $exlude_pages)) {
                    $selected_pages[Tools::strtolower(str_replace('Controller.php', '', $file))] = Tools::strtolower(str_replace('Controller.php', '', $file));
                } elseif ('index.php' != $file && preg_match('/^([a-z0-9_.-]*\/)?[a-z0-9_.-]*\.php$/i', $file) && !in_array(Tools::strtolower(str_replace('Controller.php', '', $file)), $exlude_pages)) {
                    $selected_pages[Tools::strtolower(sprintf(Tools::displayError('%1$s (in %2$s)'), dirname($file), str_replace('Controller.php', '', basename($file))))] = Tools::strtolower(str_replace('Controller.php', '', basename($file)));
                }
            }

            // Add modules controllers to list (this function is cool !)
            foreach (glob(_PS_MODULE_DIR_.'*/controllers/front/*.php') as $file) {
                $filename = basename($file, '.php');
                if ('index' == $filename) {
                    continue;
                }

                $module = basename(dirname(dirname(dirname($file))));
                $selected_pages[$module.' - '.$filename] = 'module-'.$module.'-'.$filename;
            }

            foreach (CMS::listCms($context->language->id) as $value) {
                $selected_pages['CMS'.' - '.$value['meta_title']] = 'cms-'.$value['id_cms'];
            }
        }

        return $selected_pages;
    }

    public static function getPages()
    {
        $result = [];
        try {
            $selected_pages = self::getPagesAsMeta();

            foreach ($selected_pages as $key => $value) {
                $result[] = [
                    'title' => Tools::ucfirst(preg_replace('/\s+/', ' ', str_replace('-', ' ', trim($key)))),
                    'value' => $value,
                ];
            }
        } catch (Exception $e) {
            NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
        }

        return $result;
    }

    public static function getUniqueName($name)
    {
        $new_name = self::getNameFormatted($name);
        $index = 1;
        $count = (int) Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscription_tpl`
			WHERE `name` = "'.pSQL($new_name).'"
		');
        while ($count > 0) {
            $new_name = self::getNameFormatted($name.sprintf(' Copy %s', $index));
            ++$index;
        }

        return $new_name;
    }

    public static function loadFile($filename, $keep_name = false, $save_css_style = false)
    {
        $languages_tmp = Language::getLanguages(false);
        $languages = [];

        $subscription_tpl = new NewsletterProSubscriptionTpl();
        $subscription_tpl->load_file = [$filename, $keep_name, $save_css_style];

        foreach ($languages_tmp as $language) {
            $languages[$language['iso_code']] = $language;
        }

        if (!file_exists($filename)) {
            throw new Exception(sprintf('Unable to load the subscription template from the file [%s].', $filename));
        }

        $settings_filename = $filename.'settings.json';
        if (($settings = Tools::file_get_contents($settings_filename)) === false) {
            throw new Exception(sprintf('Unable to read the settings file [%s].', $settings_filename));
        }

        $settings = json_decode($settings, true);

        if (false == $settings) {
            throw new Exception(sprintf('Unable to decode the settings file [%s].', $settings_filename));
        }

        // init object
        foreach ($settings as $field_name => $field_value) {
            if (property_exists($subscription_tpl, $field_name)) {
                if ('mandatory_fields' === $field_name) {
                    $subscription_tpl->mandatory_fields = serialize($field_value);
                } else {
                    $subscription_tpl->{$field_name} = $field_value;
                }
            }
        }

        $name = NewsletterProSubscriptionTpl::getUniqueName($settings['name']);
        if ($keep_name) {
            // for testing template
            $name = NewsletterProSubscriptionTpl::getNameFormatted($settings['name']);
        }

        $subscription_tpl->name = $name;
        $subscription_tpl->date_add = date('Y-m-d H:i:s');

        foreach ($settings['_const'] as $value) {
            if (property_exists($subscription_tpl, $value['field_name']) && defined($value['field_value'])) {
                $subscription_tpl->{$value['field_name']} = constant($value['field_value']);
            }
        }

        foreach ($settings['_content'] as $field_name => $basename) {
            if (property_exists($subscription_tpl, $field_name)) {
                $content_filename = $filename.$basename;
                if (!file_exists($content_filename)) {
                    throw new Exception(sprintf('Unable to read the file [%s].', $content_filename));
                }

                if (($content = Tools::file_get_contents($content_filename)) === false) {
                    throw new Exception(sprintf('Unable to read the file [%s].', $content_filename));
                }

                $subscription_tpl->{$field_name} = $content;
            }
        }

        $content_lang_data = [];

        foreach ($languages as $iso_code => $language) {
            $content_lang_dir = $filename.$iso_code.'/';
            if (!file_exists($content_lang_dir)) {
                $content_lang_dir = $filename.'en'.'/';
                if (!file_exists($content_lang_dir)) {
                    throw new Exception(sprintf('Unable to read the file [%s].', $content_lang_dir));
                }
            }

            foreach ($settings['_content_lang'] as $field_name => $basename) {
                if (property_exists($subscription_tpl, $field_name)) {
                    if (!array_key_exists($field_name, $content_lang_data)) {
                        $content_lang_data[$field_name] = [];
                    }
                    $content_lang_filename = $content_lang_dir.$basename;

                    if (!file_exists($content_lang_filename)) {
                        throw new Exception(sprintf('Unable to read the file [%s].', $content_lang_filename));
                    }

                    if (($content = Tools::file_get_contents($content_lang_filename)) === false) {
                        throw new Exception(sprintf('Unable to read the file [%s].', $content_lang_filename));
                    }

                    $content_lang_data[$field_name][$language['id_lang']] = $content;
                }
            }
        }

        foreach ($content_lang_data as $field_name => $field_value) {
            if (property_exists($subscription_tpl, $field_name)) {
                $subscription_tpl->{$field_name} = $field_value;
            }
        }

        if ($save_css_style) {
            $subscription_tpl->saveCSSStyleAsFile($subscription_tpl->css_style);
        }

        return $subscription_tpl;
    }
}
