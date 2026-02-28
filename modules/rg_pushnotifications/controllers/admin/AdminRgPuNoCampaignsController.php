<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class AdminRgPuNoCampaignsController extends ModuleAdminController
{
    private $module_name;
    private static $__finished_campaigns = null;

    public function __construct()
    {
        $this->module_name = 'rg_pushnotifications';
        $this->table = 'rg_pushnotifications_campaign';
        $this->identifier = 'id_campaign';
        $this->className = 'RgPuNoCampaign';
        $this->lang = false;
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->allow_export = true;
        $this->list_no_link = true;

        $this->_select = 'ROUND(`total_clicked` / `total_delivered` * 100) AS `click_rate`, `id_campaign` AS `id_image`';
        $this->_orderWay = 'DESC';

        $this->addRowAction('delete');
        $this->addRowAction('cancel');

        parent::__construct();

        $this->fields_list = [
            'id_campaign' => [
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'id_image' => [
                'title' => $this->l('Image'),
                'align' => 'text-center',
                'callback' => 'printImage',
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ],
            'title' => [
                'title' => $this->l('Title'),
            ],
            'total_notifications' => [
                'align' => 'text-center',
                'title' => $this->l('Total'),
            ],
            'total_delivered' => [
                'align' => 'text-center',
                'title' => $this->l('Delivered'),
            ],
            'total_unreachable' => [
                'align' => 'text-center',
                'title' => $this->l('Unreachable'),
            ],
            'click_rate' => [
                'align' => 'text-center',
                'title' => $this->l('Click Rate'),
                'suffix' => '%',
            ],
            'date_start' => [
                'title' => $this->l('Start Date'),
                'type' => 'datetime',
                'align' => 'text-right',
            ],
            'date_end' => [
                'title' => $this->l('End Date'),
                'type' => 'datetime',
                'align' => 'text-right',
            ],
            'date_add' => [
                'title' => $this->l('Added Date'),
                'type' => 'datetime',
                'align' => 'text-right',
            ],
        ];
    }

    public function printImage($id, $tr)
    {
        $module = Module::getInstanceByName($this->module_name);

        if (file_exists($this->module->getLocalPath() . 'uploads/c_' . (int) $id . '.png')) {
            $icon_url = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'uploads/c_' . (int) $id . '.png';
        } else {
            $icon_url = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'views/img/100.png';
        }

        $this->context->smarty->assign([
            'image_url' => $icon_url,
        ]);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/image.tpl');
    }

    public function renderForm()
    {
        $genders_collection = Gender::getGenders();
        $genders = [['id_gender' => 0, 'name' => $this->l('Not set')]];

        foreach ($genders_collection as $gen) {
            $genders[] = ['id_gender' => $gen->id_gender, 'name' => $gen->name];
        }

        $carriers = [];
        $carriers_data = Carrier::getCarriers(Context::getContext()->language->id, true, 0, false, null, Carrier::ALL_CARRIERS);

        foreach ($carriers_data as $data) {
            $carriers[] = ['id' => $data['id_carrier'], 'name' => $data['name'] . ($data['delay'] ? ' - ' . $data['delay'] : '')];
        }

        $platforms = [];
        $platforms_data = RgPuNoSubscriber::getPlatforms();

        foreach ($platforms_data as $name) {
            $platforms[] = ['id' => $name, 'name' => $name];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('New Notifications Campaign'),
                'icon' => 'icon-bell',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Notification title.'),
                    'desc' => $this->l('Remember to fill all languages.'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Message'),
                    'name' => 'message',
                    'lang' => true,
                    'cols' => 50,
                    'rows' => 5,
                    'required' => true,
                    'maxchar' => RgPuNoTools::MAX_NOTIFICATION_MESSAGE_LENGTH,
                    'maxlength' => RgPuNoTools::MAX_NOTIFICATION_MESSAGE_LENGTH,
                    'hint' => $this->l('Notification message.'),
                    'desc' => $this->l('Remember to fill all languages.'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Link'),
                    'name' => 'link',
                    'hint' => $this->l('Link where user will be leaded when click on the notification.'),
                    'desc' => $this->l('You can convert any multi-language URL from your shop into a generic one, removing the language component from it, so that each user can navigate in their own language. For example for http://www.mishop.com/en/bajamos-precios you should remove the "en/" and enter only http://www.mishop.com/bajamos-precios. Leave blank to use your default shop URL.'),
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Icon'),
                    'name' => 'icon',
                    'values' => [
                        [
                            'id' => 'icon_default',
                            'value' => 'default',
                            'label' => $this->l('Default'),
                        ],
                        [
                            'id' => 'icon_file',
                            'value' => 'file',
                            'label' => $this->l('Image file'),
                        ],
                        [
                            'id' => 'icon_url',
                            'value' => 'url',
                            'label' => $this->l('Image URL'),
                        ],
                    ],
                    'desc' => $this->l('Icon must have an HTTPS path. If not, it could be not properly displayed in some web browsers.'),
                ],
                [
                    'type' => 'file',
                    'name' => 'icon_image_file',
                    'desc' => $this->l('File will be converted to a 129x129 PNG image. Max allowed file size: 1024 KB.'),
                ],
                [
                    'type' => 'text',
                    'name' => 'icon_url_file',
                    'desc' => $this->l('URL must use HTTPS to be displayed properly in all web browsers. A 129x129 PNG image is recommended.'),
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Image'),
                    'name' => 'image',
                    'values' => [
                        [
                            'id' => 'image_none',
                            'value' => 'none',
                            'label' => $this->l('None'),
                        ],
                        [
                            'id' => 'image_file',
                            'value' => 'file',
                            'label' => $this->l('Image file'),
                        ],
                        [
                            'id' => 'image_url',
                            'value' => 'url',
                            'label' => $this->l('Image URL'),
                        ],
                    ],
                    'desc' => $this->l('Chrome on Windows Desktop and Android supports displaying a large image below the notification\'s title and message. Image must have an HTTPS path. If not, it could be not properly displayed in some web browsers.'),
                ],
                [
                    'type' => 'file',
                    'name' => 'image_image_file',
                    'desc' => $this->l('File will be converted to a 512x256 PNG image.'),
                ],
                [
                    'type' => 'text',
                    'name' => 'image_url_file',
                    'desc' => $this->l('URL must use HTTPS to be displayed properly in all web browsers. A 512x256 PNG image is recommended.'),
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Delivery mode'),
                    'name' => 'delivery_mode',
                    'values' => [
                        [
                            'id' => 'delivery_mode_male',
                            'value' => 'immediately',
                            'label' => $this->l('Immediately'),
                        ],
                        [
                            'id' => 'delivery_mode_female',
                            'value' => 'intelligent',
                            'label' => $this->l('Intelligent'),
                        ],
                        [
                            'id' => 'delivery_mode_neutral',
                            'value' => 'optimized',
                            'label' => $this->l('Optimized'),
                        ],
                    ],
                    'desc' => $this->l('Immediately') . ': ' . $this->l('Notification is sent right away.') . '<br>'
                        . $this->l('Intelligent') . ': ' . $this->l('Delivers over a 24 hour period at the time each user is most likely to open notifications. Maximizes open rates, but does not deliver right away.') . '<br>'
                        . $this->l('Optimized') . ': ' . $this->l('Deliver at the same time of day wherever each user is.'),
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->l('Start date'),
                    'name' => 'date_start',
                    'size' => 20,
                    'hint' => $this->l('Use this field to set start date for scheduled notifications.'),
                    'desc' => $this->l('Leave blank to send notification immediately.'),
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->l('End date'),
                    'name' => 'date_end',
                    'size' => 20,
                    'desc' => $this->l('Time lapse when notification is considered alive. Leave blank to automatically expire 72 hours later.'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Include guests'),
                    'name' => 'include_guests',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                    'hint' => $this->l('Include the guests of the shop in the users filter.'),
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Device'),
                    'name' => 'devices',
                    'values' => RgPuNoSubscriber::getDevices(),
                    'id_field' => 'device',
                    'name_field' => 'device',
                    'hide_id_column' => true,
                    'hint' => $this->l('Filter users by the device used.'),
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Platform'),
                    'name' => 'platforms',
                    'values' => $platforms,
                    'id_field' => 'id',
                    'name_field' => 'name',
                    'hide_id_column' => true,
                    'hint' => $this->l('Filter users by the platform used.'),
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->l('Last active'),
                    'name' => 'last_active',
                    'size' => 20,
                    'hint' => $this->l('Filter users by their last activity date.'),
                    'desc' => $this->l('Only subscribers with the last active date greater than this will be included.'),
                ],
                [
                    'type' => 'rg-group',
                    'label' => $this->l('Session count'),
                    'name' => 'generic-group',
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'min_session',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('from'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'max_session',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('to'),
                        ],
                    ],
                    'hint' => $this->l('Filter users by the number of sessions they have had on the site, according to OneSignal statistics.'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Specific customers'),
                    'name' => 'clients',
                    'desc' => $this->l('If you fill this, all remaining fields will be ignored.'),
                    'hint' => $this->l('Filter specific customers (write their email or name to do the search).'),
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Customer group'),
                    'name' => 'client_group',
                    'values' => Group::getGroups($this->context->language->id, true),
                    'id_field' => 'id_group',
                    'name_field' => 'name',
                    'hint' => $this->l('Filter users by the customer group.'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Subscribed to newsletter'),
                    'name' => 'newsletter',
                    'options' => [
                        'query' => [
                            ['id' => 0, 'name' => $this->l('No')],
                            ['id' => 1, 'name' => $this->l('Yes')],
                            ['id' => 2, 'name' => $this->l('Both')],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'hint' => $this->l('Filter users by their subscription status to the newsletter.'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Subscribed to opt-in'),
                    'name' => 'optin',
                    'options' => [
                        'query' => [
                            ['id' => 0, 'name' => $this->l('No')],
                            ['id' => 1, 'name' => $this->l('Yes')],
                            ['id' => 2, 'name' => $this->l('Both')],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'hint' => $this->l('Filter users by their subscription status to the opt-in.'),
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Gender'),
                    'name' => 'gender',
                    'values' => (array) $genders,
                    'id_field' => 'id_gender',
                    'name_field' => 'name',
                    'hint' => $this->l('Filter users by gender.'),
                ],
                [
                    'type' => 'rg-group',
                    'label' => $this->l('Total amount bought'),
                    'name' => 'generic-group',
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'min_sell',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('from'),
                            'suffix' => Currency::getDefaultCurrency()->sign . ' (' . Currency::getDefaultCurrency()->iso_code . ')',
                        ],
                        [
                            'type' => 'text',
                            'name' => 'max_sell',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('to'),
                            'suffix' => Currency::getDefaultCurrency()->sign . ' (' . Currency::getDefaultCurrency()->iso_code . ')',
                        ],
                    ],
                    'hint' => $this->l('Filter users by the total amount spent in your shop.'),
                ],
                [
                    'type' => 'rg-group',
                    'label' => $this->l('Total units bought'),
                    'name' => 'generic-group',
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'min_sell_units',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('from'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'max_sell_units',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('to'),
                        ],
                    ],
                    'hint' => $this->l('Filter users by the total number of units bought over time.'),
                ],
                [
                    'type' => 'rg-group',
                    'label' => $this->l('Total units bought of a single product in a single order'),
                    'name' => 'generic-group',
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'min_sell_units_order',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('from'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'max_sell_units_order',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('to'),
                        ],
                    ],
                    'hint' => $this->l('Filter users by the total number of units bought in a single order.'),
                ],
                [
                    'type' => 'rg-group',
                    'label' => $this->l('Ranking position'),
                    'name' => 'generic-group',
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'min_ranking',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('from'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'max_ranking',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('to'),
                        ],
                    ],
                    'hint' => $this->l('Filter users by their position in the ranking, according to the total amount spent in your store.'),
                ],
                [
                    'type' => 'rg-group',
                    'label' => $this->l('Valid orders'),
                    'name' => 'generic-group',
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'min_valid_orders',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('from'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'max_valid_orders',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('to'),
                        ],
                    ],
                    'hint' => $this->l('Filter users by the total number of valid orders.'),
                ],
                [
                    'type' => 'rg-group',
                    'label' => $this->l('Has bought the products'),
                    'name' => 'generic-group',
                    'form_group_class' => 'flexdatalist-in-group',
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'bought_product',
                            'class' => 'input',
                        ],
                        [
                            'type' => 'text',
                            'name' => 'bought_product_days',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('in the last'),
                            'suffix' => $this->l('days'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'bought_product_qty',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('a quantity greater than'),
                            'suffix' => $this->l('units'),
                        ],
                    ],
                    'hint' => $this->l('Filter users who have bought certain products (write the name of the product to do the search).'),
                ],
                [
                    'type' => 'rg-group',
                    'label' => $this->l('Has bought products from the categories'),
                    'name' => 'generic-group',
                    'form_group_class' => 'flexdatalist-in-group',
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'bought_category',
                        ],
                        [
                            'type' => 'text',
                            'name' => 'bought_category_days',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('in the last'),
                            'suffix' => $this->l('days'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'bought_category_qty',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('a quantity greater than'),
                            'suffix' => $this->l('units'),
                        ],
                    ],
                    'hint' => $this->l('Filter users who bought products from certain categories (write the name of the category to do the search).'),
                ],
                [
                    'type' => 'rg-group',
                    'label' => $this->l('Has bought products from the manufacturers'),
                    'name' => 'generic-group',
                    'form_group_class' => 'flexdatalist-in-group',
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'bought_manufacturer',
                        ],
                        [
                            'type' => 'text',
                            'name' => 'bought_manufacturer_days',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('in the last'),
                            'suffix' => $this->l('days'),
                        ],
                        [
                            'type' => 'text',
                            'name' => 'bought_manufacturer_qty',
                            'class' => 'fixed-width-sm',
                            'prefix' => $this->l('a quantity greater than'),
                            'suffix' => $this->l('units'),
                        ],
                    ],
                    'hint' => $this->l('Filter users who bought products from certain manufacturers (write the name of the manufacturer to do the search).'),
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Payment method used'),
                    'name' => 'payment_method_list',
                    'values' => PaymentModule::getInstalledPaymentModules(),
                    'id_field' => 'name',
                    'name_field' => 'name',
                    'hide_id_column' => true,
                    'hint' => $this->l('Filter users by the payment method used in their orders.'),
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Carrier used'),
                    'name' => 'carrier_list',
                    'values' => $carriers,
                    'id_field' => 'id',
                    'name_field' => 'name',
                    'hint' => $this->l('Filter users by the carrier used in their orders.'),
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Currency used'),
                    'name' => 'currency_list',
                    'values' => Currency::getCurrencies(false, true, true),
                    'id_field' => 'id_currency',
                    'name_field' => 'name',
                    'hint' => $this->l('Filter users by the currency used on their orders.'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Abandoned cart, elapsed time'),
                    'name' => 'abandoned_cart_time',
                    'class' => 'fixed-width-sm',
                    'suffix' => $this->l('hours'),
                    'hint' => $this->l('Filter users by abandoned cars (write the elapsed time of the cart to be considered as a valid abandoned cart).'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Abandoned cart, min amount'),
                    'name' => 'abandoned_cart_amount',
                    'class' => 'fixed-width-sm',
                    'suffix' => Currency::getDefaultCurrency()->sign . ' (' . Currency::getDefaultCurrency()->iso_code . ')',
                    'hint' => $this->l('Minimum amount of the cart to be considered as a valid abandoned cart.'),
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->l('Customers registered from'),
                    'name' => 'min_registration',
                    'size' => 20,
                    'hint' => $this->l('Filter users by their registration date in the shop, start date.'),
                ],
                [
                    'type' => 'datetime',
                    'label' => $this->l('Customers registered to'),
                    'name' => 'max_registration',
                    'size' => 20,
                    'hint' => $this->l('Filter users by their registration date in the shop, end date.'),
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Customer language'),
                    'name' => 'languages_list',
                    'values' => Language::getLanguages(),
                    'id_field' => 'id_lang',
                    'hint' => $this->l('Filter users by their preferred language.'),
                    'name_field' => 'name',
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Customer country'),
                    'name' => 'countries',
                    'values' => Country::getCountries($this->context->language->id, true),
                    'id_field' => 'id_country',
                    'hint' => $this->l('Filter users by the country specified in their addresses.'),
                    'name_field' => 'name',
                ],
                [
                    'type' => 'rg-multiple-checkbox',
                    'label' => $this->l('Customer shop'),
                    'name' => 'shops_list',
                    'values' => Shop::getShops(),
                    'id_field' => 'id_shop',
                    'hint' => $this->l('Filter users by the shop where they were registered.'),
                    'name_field' => 'name',
                ],
            ],
            'submit' => [
                'title' => $this->l('Register'),
            ],
            'buttons' => [
                'preCalculation' => [
                    'name' => 'preCalculation',
                    'title' => $this->l('Calculate Affected Customers'),
                    'class' => 'pull-right',
                    'icon' => 'process-icon-preview',
                    'js' => 'calculateClients();',
                ],
            ],
        ];

        $this->object->newsletter = 2;
        $this->object->optin = 2;
        $this->object->delivery_mode = 'immediately';
        $this->object->icon = 'default';
        $this->object->image = 'none';
        $this->object->clients = Tools::getValue('clients');
        $this->object->bought_product = Tools::getValue('bought_product');
        $this->object->bought_category = Tools::getValue('bought_category');
        $this->object->bought_manufacturer = Tools::getValue('bought_manufacturer');

        return parent::renderForm();
    }

    public function initContent()
    {
        if (!RgPuNoTools::validateBasicSettings()) {
            $this->errors[] = $this->displayWarning($this->l('Apparently there are no credentials configured in this shop. It is necessary to set them in the module configurator in order to continue.'));

            return;
        }

        return parent::initContent();
    }

    public function init()
    {
        if (Tools::getValue('refresh_campaign')) {
            $this->action = 'refreshCampaign';
        }

        parent::init();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if ($this->display != 'edit' && $this->display != 'add') {
            $this->page_header_toolbar_btn['new_campaign'] = [
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('Add new'),
                'icon' => 'process-icon-new',
                'js' => 'return confirm(\'' . $this->l('It is recommended to Refresh Subscribers Data before creating a new campaign to maximize effect of campaign filters over the subscribers. Do you want to continue?') . '\')',
            ];

            $this->page_header_toolbar_btn['config'] = [
                'desc' => $this->l('Module Config'),
                'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module_name,
                'icon' => 'process-icon-configure',
            ];

            $this->page_header_toolbar_btn['refresh_campaign'] = [
                'href' => self::$currentIndex . '&refresh_campaign=1&token=' . $this->token,
                'desc' => $this->l('Refresh Campaign Data'),
                'icon' => 'process-icon-refresh',
            ];

            $this->page_header_toolbar_btn['refresh_subscriber'] = [
                'js' => 'refreshSubscribers();',
                'desc' => $this->l('Refresh Subscriber Data'),
                'icon' => 'process-icon-refresh',
            ];
        }
    }

    public function processRefreshCampaign()
    {
        RgPuNoTools::refreshCampaignData();
        $this->redirect_after = $this->context->link->getAdminLink('AdminRgPuNoCampaigns') . '&conf=4';
    }

    public function processAdd()
    {
        $this->validateFormValues();

        $icon_mode = trim(Tools::getValue('icon'));
        $icon = false;

        if ($icon_mode == 'file') {
            if (isset($_FILES['icon_image_file']['tmp_name']) &&
                !Tools::isEmpty($_FILES['icon_image_file']['tmp_name'])
            ) {
                if (!($error = ImageManager::validateUpload($_FILES['icon_image_file'], Tools::getMaxUploadSize(1024 * 1024)))) {
                    $temp_path = tempnam(_PS_TMP_IMG_DIR_, $this->module_name);

                    if ($temp_path && move_uploaded_file($_FILES['icon_image_file']['tmp_name'], $temp_path)) {
                        if (ImageManager::checkImageMemoryLimit($temp_path)) {
                            $icon_path = $this->module->getLocalPath() . 'uploads/c.png';

                            if (ImageManager::resize($temp_path, $icon_path, 129, 129, 'png')) {
                                unlink($temp_path);
                                $icon = $icon_path;
                            } else {
                                $this->errors[] = $this->l('Image cannot be converted. Please try again.');
                            }
                        } else {
                            $this->errors[] = $this->l('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings.');
                        }
                    } else {
                        $this->errors[] = $this->l('An error occurred while uploading the image.');
                    }
                } else {
                    $this->errors[] = $error;
                }
            }
        } elseif ($icon_mode == 'url') {
            $icon = trim(Tools::getValue('icon_url_file'));
        }

        $image_mode = trim(Tools::getValue('image'));
        $image = false;

        if ($image_mode == 'file') {
            if (isset($_FILES['image_image_file']['tmp_name']) &&
                !Tools::isEmpty($_FILES['image_image_file']['tmp_name'])
            ) {
                if (!($error = ImageManager::validateUpload($_FILES['image_image_file'], Tools::getMaxUploadSize()))) {
                    $temp_path = tempnam(_PS_TMP_IMG_DIR_, $this->module_name);

                    if ($temp_path && move_uploaded_file($_FILES['image_image_file']['tmp_name'], $temp_path)) {
                        if (ImageManager::checkImageMemoryLimit($temp_path)) {
                            $image_path = $this->module->getLocalPath() . 'uploads/c-img.png';

                            if (ImageManager::resize($temp_path, $image_path, 512, 256, 'png')) {
                                unlink($temp_path);
                                $image = $image_path;
                            } else {
                                $this->errors[] = $this->l('Image cannot be converted. Please try again.');
                            }
                        } else {
                            $this->errors[] = $this->l('Due to memory limit restrictions, this image cannot be loaded. Please increase your memory_limit value via your server\'s configuration settings.');
                        }
                    } else {
                        $this->errors[] = $this->l('An error occurred while uploading the image.');
                    }
                } else {
                    $this->errors[] = $error;
                }
            }
        } elseif ($image_mode == 'url') {
            $image = trim(Tools::getValue('image_url_file'));
        }

        if (count($this->errors)) {
            $this->display = 'edit';

            return false;
        }

        $date_start_default = date('Y-m-d H:i:s');
        $date_end_default = date('Y-m-d H:i:s', strtotime('+72 hours'));
        $date_start = Tools::getValue('date_start');
        $date_end = Tools::getValue('date_end');

        if (!$date_end && $date_start) {
            $date = new DateTime($date_start);
            $date->modify('+72 hours');
            $date_end_default = $date->format('Y-m-d H:i:s');
        }

        $delivery_mode = Tools::getValue('delivery_mode', 'immediately');
        $devices = Tools::getValue('devices');
        $platforms = Tools::getValue('platforms');
        $last_active = Tools::getValue('last_active');
        $min_session = Tools::getValue('min_session');
        $max_session = Tools::getValue('max_session');
        $extra_params = [
            'devices' => $devices,
            'platforms' => $platforms,
            'last_active' => $last_active,
            'min_session' => $min_session,
            'max_session' => $max_session,
        ];
        $include_guests = (int) Tools::getValue('include_guests');
        $message = [];
        $title = [];
        $languages = $this->getLanguages();
        $os_langs = RgPuNoTools::getLanguagesPSandOS();

        foreach ($languages as $lang) {
            $title[$os_langs[Tools::strtoupper($lang['iso_code'])]] = trim(Tools::getValue('title_' . (int) $lang['id_lang']));
            $message[$os_langs[Tools::strtoupper($lang['iso_code'])]] = trim(Tools::getValue('message_' . (int) $lang['id_lang']));
        }

        if (!isset($title['en']) || !$title['en']) {
            $title['en'] = trim(Tools::getValue('title_' . (int) Configuration::get('PS_LANG_DEFAULT'), reset($title)));
        }

        if (!isset($message['en']) || !$message['en']) {
            $message['en'] = trim(Tools::getValue('message_' . (int) Configuration::get('PS_LANG_DEFAULT'), reset($message)));
        }

        $url = trim(Tools::getValue('link'));

        if (!$url) {
            $url = Tools::getShopDomainSsl(true) . __PS_BASE_URI__;
        }

        $fields = [
            'app_id' => RgPuNoConfig::get('OS_APP_ID'),
            'contents' => $message,
            'headings' => $title,
            'web_url' => $url,
        ];
        if (Module::isEnabled('rg_psmobileapp') && Configuration::get('RGMOAPP_SET_APP_NAME')) {
            if (strpos($url, Tools::getShopDomainSsl(true) . __PS_BASE_URI__) !== false) {
                $fields['app_url'] = str_replace(Tools::getShopProtocol(), Configuration::get('RGMOAPP_SET_APP_NAME') . '://', $fields['web_url']);
            } else {
                $fields['app_url'] = $fields['web_url'];
            }
        }

        if ($date_start) {
            $date_to_sent = RgPuNoTools::convertDate($date_start);
            $fields['send_after'] = $date_to_sent;
        }

        if ($delivery_mode == 'intelligent') {
            $fields['delayed_option'] = 'last-active';
        } elseif ($delivery_mode == 'optimized') {
            $fields['delayed_option'] = 'timezone';
            $fields['delivery_time_of_day'] = date('g:iA', strtotime($date_start));
        }

        $campaign = new RgPuNoCampaign();
        $campaign->title = trim(Tools::getValue('title_' . (int) Configuration::get('PS_LANG_DEFAULT'), reset($title)));
        $campaign->delivery = $delivery_mode;
        $campaign->date_start = $date_start ?: $date_start_default;
        $campaign->date_end = $date_end ?: $date_end_default;

        if ($campaign->add()) {
            $module = Module::getInstanceByName($this->module_name);

            if ($icon_mode == 'url') {
                $temp_path = tempnam(_PS_TMP_IMG_DIR_, $this->module_name);

                if (@copy($icon, $temp_path)) {
                    $icon_path = $this->module->getLocalPath() . 'uploads/c_' . (int) $campaign->id . '.png';
                    ImageManager::resize($temp_path, $icon_path, 129, 129, 'png');
                    unlink($temp_path);
                }
            } else {
                if ($icon) {
                    rename($icon, str_replace('c.png', 'c_' . (int) $campaign->id . '.png', $icon));
                    $icon = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'uploads/c_' . (int) $campaign->id . '.png';
                } else {
                    if (file_exists($module->getLocalPath() . 'views/img/100.png')) {
                        $icon = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'views/img/100.png';
                    } else {
                        $icon = Tools::getShopDomainSsl(true) . _PS_IMG_ . Configuration::get('PS_FAVICON');
                    }
                }
            }

            $fields['chrome_web_icon'] = $icon;
            $fields['firefox_icon'] = $icon;

            if ($image) {
                if ($image_mode == 'file') {
                    rename($image, str_replace('c-img.png', 'c-img_' . (int) $campaign->id . '.png', $image));
                    $image = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'uploads/c-img_' . (int) $campaign->id . '.png';
                }

                $fields['chrome_web_image'] = $image;
            }

            if ($ids_client = Tools::getValue('clients')) {
                $ids_client = explode(',', $ids_client);
                $ids_player = RgPuNoSubscriber::getIdPlayersByIdCustomers($ids_client, $include_guests, $extra_params);

                $campaign->total_notifications = count($ids_player);
                $campaign->update();

                for ($start = 0; $start < count($ids_player); $start += RgPuNoTools::MAX_PLAYERS_BY_NOTIFICATION) {
                    $chunk_id_players = array_slice($ids_player, $start, RgPuNoTools::MAX_PLAYERS_BY_NOTIFICATION, true);
                    $fields['include_player_ids'] = array_values($chunk_id_players);
                    $response = RgPuNoTools::sendRealOneSignalNotification($fields);
                    $invalid_players = RgPuNoTools::checkInvalidPlayers($response);

                    if ($id_onesignal = RgPuNoTools::getOneSignalNotificationId($response)) {
                        foreach ($chunk_id_players as $id_subscriber => $id_player) {
                            $notification = new RgPuNoNotification();
                            $notification->id_campaign = (int) $campaign->id;
                            $notification->id_onesignal = $id_onesignal;
                            $notification->id_subscriber = (int) $id_subscriber;

                            if (in_array($id_player, $invalid_players)) {
                                $notification->status = 'norecipients';
                            }

                            $notification->clicked = null;
                            $notification->title = $title['en'];
                            $notification->notification_type = 'message';
                            $notification->date_start = $date_start ?: $date_start_default;
                            $notification->date_end = $date_end ?: $date_end_default;
                            $notification->add(true, true);
                        }
                    }
                }

                $this->redirect_after = $this->context->link->getAdminLink('AdminRgPuNoCampaigns') . '&conf=3';
            } else {
                $groups = Tools::getValue('client_group');
                $newsletter = (int) Tools::getValue('newsletter');

                if ($newsletter == 2) {
                    $newsletter = '0,1';
                }

                $optin = (int) Tools::getValue('optin');

                if ($optin == 2) {
                    $optin = '0,1';
                }

                $gender = Tools::getValue('gender');
                $min_sell = Tools::getValue('min_sell');
                $max_sell = Tools::getValue('max_sell');
                $min_sell_units = Tools::getValue('min_sell_units');
                $max_sell_units = Tools::getValue('max_sell_units');
                $min_sell_units_order = Tools::getValue('min_sell_units_order');
                $max_sell_units_order = Tools::getValue('max_sell_units_order');
                $min_ranking = Tools::getValue('min_ranking');
                $max_ranking = Tools::getValue('max_ranking');
                $min_valid_orders = Tools::getValue('min_valid_orders');
                $max_valid_orders = Tools::getValue('max_valid_orders');

                if ($bought_product = Tools::getValue('bought_product')) {
                    $bought_product = explode(',', $bought_product);
                }

                $bought_product_days = Tools::getValue('bought_product_days');
                $bought_product_qty = Tools::getValue('bought_product_qty');

                if ($bought_category = Tools::getValue('bought_category')) {
                    $bought_category = explode(',', $bought_category);
                }

                $bought_category_days = Tools::getValue('bought_category_days');
                $bought_category_qty = Tools::getValue('bought_category_qty');

                if ($bought_manufacturer = Tools::getValue('bought_manufacturer')) {
                    $bought_manufacturer = explode(',', $bought_manufacturer);
                }

                $bought_manufacturer_days = Tools::getValue('bought_manufacturer_days');
                $bought_manufacturer_qty = Tools::getValue('bought_manufacturer_qty');
                $payment_method_list = Tools::getValue('payment_method_list');
                $carrier_list = Tools::getValue('carrier_list');
                $currency_list = Tools::getValue('currency_list');
                $abandoned_cart_time = Tools::getValue('abandoned_cart_time');
                $abandoned_cart_amount = Tools::getValue('abandoned_cart_amount');
                $min_registration = Tools::getValue('min_registration');
                $max_registration = Tools::getValue('max_registration');
                $languages_list = Tools::getValue('languages_list');
                $shops_list = Tools::getValue('shops_list');
                $countries = Tools::getValue('countries');

                $params = [
                    'groups' => $groups,
                    'newsletter' => $newsletter,
                    'optin' => $optin,
                    'gender' => $gender,
                    'min_sell' => $min_sell,
                    'max_sell' => $max_sell,
                    'min_sell_units' => $min_sell_units,
                    'max_sell_units' => $max_sell_units,
                    'min_sell_units_order' => $min_sell_units_order,
                    'max_sell_units_order' => $max_sell_units_order,
                    'min_ranking' => $min_ranking,
                    'max_ranking' => $max_ranking,
                    'min_valid_orders' => $min_valid_orders,
                    'max_valid_orders' => $max_valid_orders,
                    'bought_product' => $bought_product,
                    'bought_product_days' => $bought_product_days,
                    'bought_product_qty' => $bought_product_qty,
                    'bought_category' => $bought_category,
                    'bought_category_days' => $bought_category_days,
                    'bought_category_qty' => $bought_category_qty,
                    'bought_manufacturer' => $bought_manufacturer,
                    'bought_manufacturer_days' => $bought_manufacturer_days,
                    'bought_manufacturer_qty' => $bought_manufacturer_qty,
                    'payment_method_list' => $payment_method_list,
                    'carrier_list' => $carrier_list,
                    'currency_list' => $currency_list,
                    'abandoned_cart_time' => $abandoned_cart_time,
                    'abandoned_cart_amount' => $abandoned_cart_amount,
                    'min_registration' => $min_registration,
                    'max_registration' => $max_registration,
                    'languages_list' => $languages_list,
                    'shops_list' => $shops_list,
                    'countries' => $countries,
                    'devices' => $devices,
                    'platforms' => $platforms,
                    'last_active' => $last_active,
                    'min_session' => $min_session,
                    'max_session' => $max_session,
                ];

                $customers = RgPuNoSubscriber::getCustomersToNotify($params);

                if (count($customers) || $include_guests) {
                    $ids_client = array_column($customers, 'id_customer');
                    $ids_player = RgPuNoSubscriber::getIdPlayersByIdCustomers($ids_client, $include_guests, $extra_params);

                    $campaign->total_notifications = count($ids_player);
                    $campaign->update();

                    for ($start = 0; $start < count($ids_player); $start += RgPuNoTools::MAX_PLAYERS_BY_NOTIFICATION) {
                        $chunk_id_players = array_slice($ids_player, $start, RgPuNoTools::MAX_PLAYERS_BY_NOTIFICATION, true);

                        $fields['include_player_ids'] = array_values($chunk_id_players);

                        $response = RgPuNoTools::sendRealOneSignalNotification($fields);
                        $invalid_players = RgPuNoTools::checkInvalidPlayers($response);

                        if ($id_onesignal = RgPuNoTools::getOneSignalNotificationId($response)) {
                            foreach ($chunk_id_players as $id_subscriber => $id_player) {
                                $notification = new RgPuNoNotification();
                                $notification->id_campaign = (int) $campaign->id;
                                $notification->id_onesignal = $id_onesignal;
                                $notification->id_subscriber = (int) $id_subscriber;

                                if (in_array($id_player, $invalid_players)) {
                                    $notification->status = 'norecipients';
                                }

                                $notification->clicked = null;
                                $notification->title = $title['en'];
                                $notification->notification_type = 'message';
                                $notification->date_start = $date_start ?: $date_start_default;
                                $notification->date_end = $date_end ?: $date_end_default;
                                $notification->add(true, true);
                            }
                        }
                    }

                    $this->redirect_after = $this->context->link->getAdminLink('AdminRgPuNoCampaigns') . '&conf=3';
                }
            }
        } else {
            $this->errors[] = $this->l('Campaign could not be added at this time.');
            $this->display = 'edit';

            return false;
        }

        return true;
    }

    public function processCancel()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if ($object->cancel()) {
                $ids_onesignal = RgPuNoNotification::getIdOneSignalByCampaign($object->id_campaign);
                RgPuNoTools::cancelRealOneSignalNotifications($ids_onesignal);

                $this->redirect_after = self::$currentIndex . '&conf=4&token=' . $this->token;
            }

            $this->errors[] = 'An error occurred during cancelation.';
        } else {
            $this->errors[] = 'An error occurred while canceling the object.' .
                ' <b>' . $this->table . '</b> (cannot load object)';
        }

        return $object;
    }

    public function initProcess()
    {
        if (Tools::isSubmit('cancel' . $this->table)) {
            $this->action = 'cancel';
        }

        parent::initProcess();
    }

    private function validateFormValues()
    {
        $languages = $this->getLanguages();

        foreach ($languages as $lang) {
            if (!($title = trim(Tools::getValue('title_' . (int) $lang['id_lang']))) || !Validate::isMessage($title)) {
                $this->errors[] = $this->l('Title') . ' (' . $lang['iso_code'] . ') ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }
        }

        foreach ($languages as $lang) {
            if (!($message = trim(Tools::getValue('message_' . (int) $lang['id_lang']))) || !Validate::isMessage($message)) {
                $this->errors[] = $this->l('Message') . ' (' . $lang['iso_code'] . ') ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid string.');
            }
        }

        if (($link = trim(Tools::getValue('link'))) && !Validate::isAbsoluteUrl($link)) {
            $this->errors[] = $this->l('Link') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid URL.');
        }

        if (($icon = trim(Tools::getValue('icon'))) && !in_array($icon, ['default', 'file', 'url'])) {
            $this->errors[] = $this->l('Icon') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if ($icon == 'file' && (!isset($_FILES['icon_image_file']['tmp_name']) || Tools::isEmpty($_FILES['icon_image_file']['tmp_name']))) {
            $this->errors[] = $this->l('Icon file') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Can\'t be emtpy.');
        }

        if ($icon == 'url' && (Tools::isEmpty(Tools::getValue('icon_url_file')) || !Validate::isAbsoluteUrl(Tools::getValue('icon_url_file')))) {
            $this->errors[] = $this->l('Icon URL') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid URL.');
        }

        if (($image = trim(Tools::getValue('image'))) && !in_array($image, ['none', 'file', 'url'])) {
            $this->errors[] = $this->l('Image') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if ($image == 'file' && (!isset($_FILES['image_image_file']['tmp_name']) || Tools::isEmpty($_FILES['image_image_file']['tmp_name']))) {
            $this->errors[] = $this->l('Image file') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Can\'t be emtpy.');
        }

        if ($image == 'url' && (Tools::isEmpty(Tools::getValue('image_url_file')) || !Validate::isAbsoluteUrl(Tools::getValue('image_url_file')))) {
            $this->errors[] = $this->l('Image URL') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid URL.');
        }

        if (($delivery_mode = trim(Tools::getValue('delivery_mode'))) &&
            !in_array($delivery_mode, ['immediately', 'intelligent', 'optimized'])
        ) {
            $this->errors[] = $this->l('Delivery mode') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($start = Tools::getValue('date_start')) && !Validate::isDateFormat($start)) {
            $this->errors[] = $this->l('Start date') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid date format.');
        }

        if (($end = Tools::getValue('date_end')) && !Validate::isDateFormat($end)) {
            $this->errors[] = $this->l('End date') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid date format.');
        }

        if ($start && $end && $start >= $end) {
            $this->errors[] = $this->l('End date') . ' ' . $this->l('must be greater than') . ' ' . $this->l('Start date') . '.';
        }

        if (!$start && $end && date('Y-m-d H:i:s') >= $end) {
            $this->errors[] = $this->l('End date') . ' ' . $this->l('must be greater than') . ' ' . $this->l('current date') . '.';
        }

        if (($last_active = Tools::getValue('last_active')) && !Validate::isDateFormat($last_active)) {
            $this->errors[] = $this->l('Last active') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid date format.');
        }

        if (($min_session = Tools::getValue('min_session')) && !Validate::isUnsignedInt($min_session)) {
            $this->errors[] = $this->l('Session count') . ' ' . $this->l('From') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid integer number.');
        }

        if (($max_session = Tools::getValue('max_session')) &&
            (!Validate::isUnsignedInt($max_session) || $max_session < $min_session)
        ) {
            $this->errors[] = $this->l('Session count') . ' ' . $this->l('To') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid integer number.') . ' ' . $this->l('Must be greater than From value.');
        }

        if (!Validate::isBool(Tools::getValue('include_guests'))) {
            $this->errors[] = $this->l('Include guests') . ' ' . $this->l('is invalid.');
        }

        if ($devices = Tools::getValue('devices')) {
            $available_devices = array_column(RgPuNoSubscriber::getDevices(), 'device');

            foreach ($devices as $device) {
                if (!in_array($device, $available_devices)) {
                    $this->errors[] = $this->l('Device') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');

                    break;
                }
            }
        }

        if (($platforms = Tools::getValue('platforms')) && !in_array(0, $platforms) && !Validate::isArrayWithIds($platforms)) {
            $this->errors[] = $this->l('Platform') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($groups = Tools::getValue('client_group')) && !Validate::isArrayWithIds($groups)) {
            $this->errors[] = $this->l('Customer group') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($newsletter = Tools::getValue('newsletter')) && !in_array($newsletter, [1, 2, 3])) {
            $this->errors[] = $this->l('Subscribed to newsletter') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($optin = Tools::getValue('optin')) && !in_array($optin, [1, 2, 3])) {
            $this->errors[] = $this->l('Subscribed to opt-in') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($gender = Tools::getValue('gender')) && !in_array(0, $gender) && !Validate::isArrayWithIds($gender)) {
            $this->errors[] = $this->l('Gender') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($min_sell = Tools::getValue('min_sell')) && !Validate::isUnsignedFloat($min_sell)) {
            $this->errors[] = $this->l('Total amount bought') . ' ' . $this->l('From') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned decimal number.');
        }

        if (($max_sell = Tools::getValue('max_sell')) && (!Validate::isUnsignedFloat($max_sell) || $max_sell < $min_sell)) {
            $this->errors[] = $this->l('Total amount bought') . ' ' . $this->l('To') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned decimal number.') . ' ' . $this->l('Must be greater than From value.');
        }

        if (($min_sell_units = Tools::getValue('min_sell_units')) && !Validate::isUnsignedInt($min_sell_units)) {
            $this->errors[] = $this->l('Total units bought') . ' ' . $this->l('From') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
        }

        if (($max_sell_units = Tools::getValue('max_sell_units')) &&
            (!Validate::isUnsignedInt($max_sell_units) || $max_sell_units < $min_sell_units)
        ) {
            $this->errors[] = $this->l('Total units bought') . ' ' . $this->l('To') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.') . ' ' . $this->l('Must be greater than From value.');
        }

        if (($min_sell_units_order = Tools::getValue('min_sell_units_order')) && !Validate::isUnsignedInt($min_sell_units_order)) {
            $this->errors[] = $this->l('Total units bought of a single product in a single order') . ' ' . $this->l('From') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
        }

        if (($max_sell_units_order = Tools::getValue('max_sell_units_order')) &&
            (!Validate::isUnsignedInt($max_sell_units_order) || $max_sell_units_order < $min_sell_units_order)
        ) {
            $this->errors[] = $this->l('Total units bought of a single product in a single order') . ' ' . $this->l('To') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.') . ' ' . $this->l('Must be greater than From value.');
        }

        if (($min_ranking = Tools::getValue('min_ranking')) && !Validate::isUnsignedInt($min_ranking)) {
            $this->errors[] = $this->l('Ranking position') . ' ' . $this->l('From') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
        }

        if (($max_ranking = Tools::getValue('max_ranking')) &&
            (!Validate::isUnsignedInt($max_ranking) ||
                $max_ranking < $min_ranking)
        ) {
            $this->errors[] = $this->l('Ranking position') . ' ' . $this->l('To') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.') . ' ' . $this->l('Must be greater than From value.');
        }

        if (($min_valid_orders = Tools::getValue('min_valid_orders')) && !Validate::isUnsignedInt($min_valid_orders)) {
            $this->errors[] = $this->l('Valid orders') . ' ' . $this->l('From') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
        }

        if (($max_valid_orders = Tools::getValue('max_valid_orders')) &&
            (!Validate::isUnsignedInt($max_valid_orders) || $max_valid_orders < $min_valid_orders)
        ) {
            $this->errors[] = $this->l('Valid orders') . ' ' . $this->l('To') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.') . ' ' . $this->l('Must be greater than From value.');
        }

        if ($bought_product = Tools::getValue('bought_product')) {
            if (!($bought_product = explode(',', $bought_product)) || !Validate::isArrayWithIds($bought_product)) {
                $this->errors[] = $this->l('Has bought the products') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the autocomplete list.');
            }

            if (($bought_product_days = Tools::getValue('bought_product_days')) &&
                (!Validate::isUnsignedInt($bought_product_days) || $max_valid_orders < $min_valid_orders)
            ) {
                $this->errors[] = $this->l('Has bought the products') . ' ' . $this->l('Days') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
            }

            if (($bought_product_qty = Tools::getValue('bought_product_qty')) && (!Validate::isUnsignedInt($bought_product_qty) || $max_valid_orders < $min_valid_orders)) {
                $this->errors[] = $this->l('Has bought the products') . ' ' . $this->l('Quantity') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
            }
        }

        if ($bought_category = Tools::getValue('bought_category')) {
            if (!($bought_category = explode(',', $bought_category)) || !Validate::isArrayWithIds($bought_category)) {
                $this->errors[] = $this->l('Has bought products from the categories') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the autocomplete list.');
            }

            if (($bought_category_days = Tools::getValue('bought_category_days')) &&
                (!Validate::isUnsignedInt($bought_category_days) || $max_valid_orders < $min_valid_orders)
            ) {
                $this->errors[] = $this->l('Has bought products from the categories') . ' ' . $this->l('Days') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
            }

            if (($bought_category_qty = Tools::getValue('bought_category_qty')) &&
                (!Validate::isUnsignedInt($bought_category_qty) || $max_valid_orders < $min_valid_orders)
            ) {
                $this->errors[] = $this->l('Has bought products from the categories') . ' ' . $this->l('Quantity') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
            }
        }

        if ($bought_manufacturer = Tools::getValue('bought_manufacturer')) {
            if (!($bought_manufacturer = explode(',', $bought_manufacturer)) ||
                !Validate::isArrayWithIds($bought_manufacturer)
            ) {
                $this->errors[] = $this->l('Has bought products from the manufacturers') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the autocomplete list.');
            }

            if (($bought_manufacturer_days = Tools::getValue('bought_manufacturer_days')) &&
                (!Validate::isUnsignedInt($bought_manufacturer_days) || $max_valid_orders < $min_valid_orders)
            ) {
                $this->errors[] = $this->l('Has bought products from the manufacturers') . ' ' . $this->l('Days') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
            }

            if (($bought_manufacturer_qty = Tools::getValue('bought_manufacturer_qty')) &&
                (!Validate::isUnsignedInt($bought_manufacturer_qty) || $max_valid_orders < $min_valid_orders)
            ) {
                $this->errors[] = $this->l('Has bought products from the manufacturers') . ' ' . $this->l('Quantity') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
            }
        }

        if ($payment_method_list = Tools::getValue('payment_method_list')) {
            $available_payment = array_column(PaymentModule::getInstalledPaymentModules(), 'name');

            foreach ($payment_method_list as $payment) {
                if (!in_array($payment, $available_payment)) {
                    $this->errors[] = $this->l('Payment method used') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');

                    break;
                }
            }
        }

        if (($carrier_list = Tools::getValue('carrier_list')) && !Validate::isArrayWithIds($carrier_list)) {
            $this->errors[] = $this->l('Carrier used') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($currency_list = Tools::getValue('currency_list')) && !Validate::isArrayWithIds($currency_list)) {
            $this->errors[] = $this->l('Currency used') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($abandoned_cart_time = Tools::getValue('abandoned_cart_time')) && !Validate::isUnsignedInt($abandoned_cart_time)) {
            $this->errors[] = $this->l('Abandoned cart, elapsed time') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned integer.');
        }

        if (($abandoned_cart_amount = Tools::getValue('abandoned_cart_amount')) &&
            !Validate::isUnsignedFloat($abandoned_cart_amount)
        ) {
            $this->errors[] = $this->l('Abandoned cart, min amount') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid unsigned decimal number.');
        }

        if (($min_registration = Tools::getValue('min_registration')) && !Validate::isDateFormat($min_registration)) {
            $this->errors[] = $this->l('Customers registered from') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid date format.');
        }

        if (($max_registration = Tools::getValue('max_registration')) && !Validate::isDateFormat($max_registration)) {
            $this->errors[] = $this->l('Customers registered to') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a valid date format.');
        }

        if (($languages_list = Tools::getValue('languages_list')) && !Validate::isArrayWithIds($languages_list)) {
            $this->errors[] = $this->l('Customer language') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($countries = Tools::getValue('countries')) && !Validate::isArrayWithIds($countries)) {
            $this->errors[] = $this->l('Customer country') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (($shops_list = Tools::getValue('shops_list')) && !Validate::isArrayWithIds($shops_list)) {
            $this->errors[] = $this->l('Customer shop') . ' ' . $this->l('is invalid.') . ' ' . $this->l('Must be a value from the list.');
        }

        if (!Tools::getValue('clients') && !Tools::getValue('include_guests') && $this->ajaxProcessPreCalculation(true) <= 0) {
            $this->errors[] = $this->l('Your campaign filters exclude all subscribed customers.');
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS($this->module->getPathUri() . 'views/css/back.css');
        $this->addJS($this->module->getPathUri() . 'views/js/back.js');
        $this->addCSS($this->module->getPathUri() . 'views/libs/jquery.flexdatalist.min.css');
        $this->addJS($this->module->getPathUri() . 'views/libs/jquery.flexdatalist.min.js');

        Media::addJsDef([
            'no_clients_results_text' => $this->l('No customers with this name or email address') . ':',
            'no_products_results_text' => $this->l('No products with this name') . ':',
            'no_categories_results_text' => $this->l('No categories with this name') . ':',
            'no_manufacturers_results_text' => $this->l('No manufacturers with this name') . ':',
            'refresh_loading_text' => $this->l('Updating suscribers data. Please wait...'),
            'refresh_processed_text' => $this->l('Processed') . ':',
            'suscribers_token' => Tools::getAdminTokenLite('AdminRgPuNoSubscribers'),
        ]);
    }

    public function ajaxProcessPreCalculation($return_total = false)
    {
        if ($ids_client = Tools::getValue('clients')) {
            $ids_client = explode(',', $ids_client);

            if ($return_total) {
                return count($ids_client);
            }

            $include_guests = (int) Tools::getValue('include_guests');
            $devices = Tools::getValue('devices');
            $platforms = Tools::getValue('platforms');
            $last_active = Tools::getValue('last_active');
            $min_session = Tools::getValue('min_session');
            $max_session = Tools::getValue('max_session');
            $extra_params = [
                'devices' => $devices,
                'platforms' => $platforms,
                'last_active' => $last_active,
                'min_session' => $min_session,
                'max_session' => $max_session,
            ];

            $ids_player = RgPuNoSubscriber::getIdPlayersByIdCustomers($ids_client, $include_guests, $extra_params);

            $message = '<strong>' . $this->l('Total Subscribers') . ': ' . count($ids_player) . '</strong></br>' .
                $this->l('Total Customers') . ': ' . count($ids_client);
            $alert_type = 'success';
        } else {
            $groups = Tools::getValue('client_group');
            $newsletter = (int) Tools::getValue('newsletter');

            if ($newsletter == 2) {
                $newsletter = '0,1';
            }

            $optin = (int) Tools::getValue('optin');

            if ($optin == 2) {
                $optin = '0,1';
            }

            $gender = Tools::getValue('gender');
            $min_sell = Tools::getValue('min_sell');
            $max_sell = Tools::getValue('max_sell');
            $min_sell_units = Tools::getValue('min_sell_units');
            $max_sell_units = Tools::getValue('max_sell_units');
            $min_sell_units_order = Tools::getValue('min_sell_units_order');
            $max_sell_units_order = Tools::getValue('max_sell_units_order');
            $min_ranking = Tools::getValue('min_ranking');
            $max_ranking = Tools::getValue('max_ranking');
            $min_valid_orders = Tools::getValue('min_valid_orders');
            $max_valid_orders = Tools::getValue('max_valid_orders');

            if ($bought_product = Tools::getValue('bought_product')) {
                $bought_product = explode(',', $bought_product);
            }

            $bought_product_days = Tools::getValue('bought_product_days');
            $bought_product_qty = Tools::getValue('bought_product_qty');

            if ($bought_category = Tools::getValue('bought_category')) {
                $bought_category = explode(',', $bought_category);
            }

            $bought_category_days = Tools::getValue('bought_category_days');
            $bought_category_qty = Tools::getValue('bought_category_qty');

            if ($bought_manufacturer = Tools::getValue('bought_manufacturer')) {
                $bought_manufacturer = explode(',', $bought_manufacturer);
            }

            $bought_manufacturer_days = Tools::getValue('bought_manufacturer_days');
            $bought_manufacturer_qty = Tools::getValue('bought_manufacturer_qty');
            $payment_method_list = Tools::getValue('payment_method_list');
            $carrier_list = Tools::getValue('carrier_list');
            $currency_list = Tools::getValue('currency_list');
            $abandoned_cart_time = Tools::getValue('abandoned_cart_time');
            $abandoned_cart_amount = Tools::getValue('abandoned_cart_amount');
            $min_registration = Tools::getValue('min_registration');
            $max_registration = Tools::getValue('max_registration');
            $languages_list = Tools::getValue('languages_list');
            $shops_list = Tools::getValue('shops_list');
            $countries = Tools::getValue('countries');
            $devices = Tools::getValue('devices');
            $platforms = Tools::getValue('platforms');
            $last_active = Tools::getValue('last_active');
            $min_session = Tools::getValue('min_session');
            $max_session = Tools::getValue('max_session');
            $include_guests = (int) Tools::getValue('include_guests');

            $params = [
                'groups' => $groups,
                'newsletter' => $newsletter,
                'optin' => $optin,
                'gender' => $gender,
                'min_sell' => $min_sell,
                'max_sell' => $max_sell,
                'min_sell_units' => $min_sell_units,
                'max_sell_units' => $max_sell_units,
                'min_sell_units_order' => $min_sell_units_order,
                'max_sell_units_order' => $max_sell_units_order,
                'min_ranking' => $min_ranking,
                'max_ranking' => $max_ranking,
                'min_valid_orders' => $min_valid_orders,
                'max_valid_orders' => $max_valid_orders,
                'bought_product' => $bought_product,
                'bought_product_days' => $bought_product_days,
                'bought_product_qty' => $bought_product_qty,
                'bought_category' => $bought_category,
                'bought_category_days' => $bought_category_days,
                'bought_category_qty' => $bought_category_qty,
                'bought_manufacturer' => $bought_manufacturer,
                'bought_manufacturer_days' => $bought_manufacturer_days,
                'bought_manufacturer_qty' => $bought_manufacturer_qty,
                'payment_method_list' => $payment_method_list,
                'carrier_list' => $carrier_list,
                'currency_list' => $currency_list,
                'abandoned_cart_time' => $abandoned_cart_time,
                'abandoned_cart_amount' => $abandoned_cart_amount,
                'min_registration' => $min_registration,
                'max_registration' => $max_registration,
                'languages_list' => $languages_list,
                'shops_list' => $shops_list,
                'countries' => $countries,
                'devices' => $devices,
                'platforms' => $platforms,
                'last_active' => $last_active,
                'min_session' => $min_session,
                'max_session' => $max_session,
            ];

            $customers = RgPuNoSubscriber::getCustomersToNotify($params);

            if ($return_total) {
                return count($customers);
            }

            $ids_client = array_column($customers, 'id_customer');

            if (!$ids_client) {
                $ids_client = [-1];
            }

            $ids_player = RgPuNoSubscriber::getIdPlayersByIdCustomers($ids_client, $include_guests, $params);

            if (count($ids_player)) {
                $emails = array_column($customers, 'email');
                $message = '<strong>' . $this->l('Total Subscribers') . ': ' . count($ids_player) . '</strong></br>' .
                    $this->l('Customers') . ': ' . count($emails) . '<br>' . Tools::truncate(implode(', ', $emails), 1000);
                $alert_type = 'success';
            } else {
                $message = $this->l('Your campaign filters exclude all subscribers.');
                $alert_type = 'warning';
            }
        }

        die(json_encode(['message' => $message, 'type' => $alert_type]));
    }

    public function ajaxProcessFilterClients()
    {
        $result = [];

        if ($load = Tools::getValue('load')) {
            $result = RgPuNoSubscriber::searchSubscribedCustomerByIdCustomer($load);
        } elseif ($keyword = Tools::getValue('keyword')) {
            $result = RgPuNoSubscriber::searchSubscribedCustomer($keyword, 50);
        }

        die(json_encode($result));
    }

    public function ajaxProcessFilterProducts()
    {
        $result = [];

        if ($load = Tools::getValue('load')) {
            $result = Db::getInstance()->executeS('
                SELECT id_product, name FROM ' . _DB_PREFIX_ . 'product_lang
                WHERE id_lang = ' . (int) Context::getContext()->language->id . '
                    AND id_shop = ' . (int) Context::getContext()->shop->id . '
                    AND id_product IN(' . implode(',', array_map('intval', $load)) . ')
            ');
        } elseif ($keyword = Tools::getValue('keyword')) {
            $result = Product::searchByName(Context::getContext()->language->id, $keyword);
        }

        die(json_encode($result));
    }

    public function ajaxProcessFilterCategories()
    {
        $result = [];

        if ($load = Tools::getValue('load')) {
            $result = Db::getInstance()->executeS('
                SELECT `id_category`, `name` FROM `' . _DB_PREFIX_ . 'category_lang`
                WHERE `id_lang` = ' . (int) Context::getContext()->language->id . '
                    AND `id_shop` = ' . (int) Context::getContext()->shop->id . '
                    AND `id_category` IN(' . implode(',', array_map('intval', $load)) . ')
            ');
        } elseif ($keyword = Tools::getValue('keyword')) {
            $result = Category::searchByName(Context::getContext()->language->id, $keyword);
        }

        die(json_encode($result));
    }

    public function ajaxProcessFilterManufacturers()
    {
        $result = [];

        if ($load = Tools::getValue('load')) {
            $result = Db::getInstance()->executeS('
                SELECT * FROM `' . _DB_PREFIX_ . 'manufacturer`
                WHERE `id_manufacturer` IN(' . implode(',', array_map('intval', $load)) . ')
            ');
        } elseif ($keyword = Tools::getValue('keyword')) {
            $result = Db::getInstance()->executeS('
                SELECT * FROM `' . _DB_PREFIX_ . 'manufacturer` WHERE `name` LIKE "%' . pSQL($keyword) . '%"
            ');
        }

        die(json_encode($result));
    }

    public function renderKpis()
    {
        $kpis = [];
        $data = RgPuNoCampaign::getTotalsData();

        $helper = new HelperKpi();
        $helper->id = 'box-total-clients';
        $helper->icon = 'icon-envelope';
        $helper->color = 'color1';
        $helper->title = $this->l('Notifications', null, null, false);
        $helper->subtitle = $this->l('TOTAL', null, null, false);
        $helper->value = (int) $data['total_notifications'];
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-subscribed-clients';
        $helper->icon = 'icon-bell';
        $helper->color = 'color2';
        $helper->title = $this->l('Delivered', null, null, false);
        $helper->subtitle = $this->l('TOTAL', null, null, false);
        $helper->value = (int) $data['total_delivered'];
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-total-notifications';
        $helper->icon = 'icon-thumbs-down';
        $helper->color = 'color3';
        $helper->title = $this->l('Unreachable', null, null, false);
        $helper->subtitle = $this->l('TOTAL', null, null, false);
        $helper->value = (int) $data['total_unreachable'];
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-viewed-notifications';
        $helper->icon = 'icon-check';
        $helper->color = 'color4';
        $helper->title = $this->l('Click Rate', null, null, false);
        $helper->subtitle = $this->l('PERCENT', null, null, false);
        $helper->value = ((int) $data['total_delivered'] ? round((float) $data['total_clicked'] / (float) $data['total_delivered'] * 100) : '0') . '%';
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }

    public function displayDeleteLink($token = null, $id = null, $name = null)
    {
        if (!isset(self::$__finished_campaigns)) {
            self::$__finished_campaigns = RgPuNoCampaign::getIdCampaignFinished();
        }

        if (in_array($id, self::$__finished_campaigns)) {
            return $this->helper->displayDeleteLink($token, $id, $name);
        }
    }

    public function displayCancelLink($token = null, $id = null, $name = null)
    {
        if (!isset(self::$__finished_campaigns)) {
            self::$__finished_campaigns = RgPuNoCampaign::getIdCampaignFinished();
        }

        if (!in_array($id, self::$__finished_campaigns)) {
            $tpl = $this->createTemplate('list_action_cancel.tpl');

            if (!is_null($name)) {
                $name = addcslashes('\n\n' . $this->l('Name') . ' ' . $name, '\'');
            }

            $data = [
                $this->identifier => $id,
                'href' => self::$currentIndex . '&' . $this->identifier . '=' . $id . '&cancel' . $this->table
                    . '&token=' . ($token != null ? $token : $this->token),
                'action' => $this->l('Cancel'),
                'confirm' => Tools::safeOutput($this->l('Cancel selected item?') . $name),
            ];

            $tpl->assign(array_merge($this->tpl_delete_link_vars, $data));

            return $tpl->fetch();
        }
    }

    public function getLanguages()
    {
        parent::getLanguages();

        $os_langs = RgPuNoTools::getLanguagesPSandOS();
        $included_langs = [];

        for ($i = 0; $i < count($this->_languages); ++$i) {
            if (!isset($os_langs[Tools::strtoupper($this->_languages[$i]['iso_code'])])) {
                unset($this->_languages[$i]);
            } elseif (in_array($os_langs[Tools::strtoupper($this->_languages[$i]['iso_code'])], $included_langs)) {
                if (in_array(Tools::strtoupper($this->_languages[$i]['iso_code']), ['EN', 'ES', 'PT', 'NO'])) {
                    $pos = array_search($os_langs[Tools::strtoupper($this->_languages[$i]['iso_code'])], $included_langs);
                    unset($this->_languages[$pos]);
                    $included_langs[$i] = $os_langs[Tools::strtoupper($this->_languages[$i]['iso_code'])];
                } else {
                    unset($this->_languages[$i]);
                }
            } else {
                $included_langs[$i] = $os_langs[Tools::strtoupper($this->_languages[$i]['iso_code'])];
            }
        }

        return $this->_languages;
    }

    public function getFieldsValue($obj)
    {
        $values = parent::getFieldsValue($obj);

        return array_merge(
            $values,
            [
                'min_sell' => Tools::getValue('min_sell'),
                'max_sell' => Tools::getValue('max_sell'),
                'min_sell_units' => Tools::getValue('min_sell_units'),
                'max_sell_units' => Tools::getValue('max_sell_units'),
                'min_sell_units_order' => Tools::getValue('min_sell_units_order'),
                'max_sell_units_order' => Tools::getValue('max_sell_units_order'),
                'min_ranking' => Tools::getValue('min_ranking'),
                'max_ranking' => Tools::getValue('max_ranking'),
                'min_valid_orders' => Tools::getValue('min_valid_orders'),
                'max_valid_orders' => Tools::getValue('max_valid_orders'),
                'bought_product' => Tools::getValue('bought_product'),
                'bought_product_days' => Tools::getValue('min_session'),
                'bought_product_qty' => Tools::getValue('min_session'),
                'bought_category' => Tools::getValue('bought_category'),
                'bought_category_days' => Tools::getValue('bought_category_days'),
                'bought_category_qty' => Tools::getValue('bought_category_qty'),
                'bought_manufacturer' => Tools::getValue('bought_manufacturer'),
                'bought_manufacturer_days' => Tools::getValue('bought_manufacturer_days'),
                'bought_manufacturer_qty' => Tools::getValue('bought_manufacturer_qty'),
                'min_session' => Tools::getValue('min_session'),
                'max_session' => Tools::getValue('max_session'),
            ]
        );
    }
}
