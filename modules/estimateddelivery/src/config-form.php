<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015-2018
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of Smart-Modules.prpo
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

// Estimated Delivery Configuration Form
// Needed for the translations to work!
$specific = 'config-form';

// Initialize the SmartForm class to build rich descriptions for the inputs
SmartForm::init($this);
// Fill the basic vars Like Weekdays
$weekdays = [
    [
        'id' => 0,
        'name' => $this->l('Monday', $specific),
    ],
    [
        'id' => 1,
        'name' => $this->l('Tuesday', $specific),
    ],
    [
        'id' => 2,
        'name' => $this->l('Wednesday', $specific),
    ],
    [
        'id' => 3,
        'name' => $this->l('Thursday', $specific),
    ],
    [
        'id' => 4,
        'name' => $this->l('Friday', $specific),
    ],
    [
        'id' => 5,
        'name' => $this->l('Saturday', $specific),
    ],
    [
        'id' => 6,
        'name' => $this->l('Sunday', $specific),
    ],
];
// Set the carriers
$carriers = $this->getCarriersList();
// Switch options
$switch_options = [
    [
        'id' => 'active_on',
        'value' => 1,
        'label' => $this->l('Enabled', $specific),
    ],
    [
        'id' => 'active_off',
        'value' => 0,
        'label' => $this->l('Disabled', $specific),
    ],
];
if ($this->is_17) {
    // WAS if (version_compare(_PS_VERSION_, '1.7', '>='))
    // It's PS 1.7.X
    $query = [
        [
            'id' => 0,
            'name' => $this->l('On the Additional buttons Placement', $specific) . ' (' . $this->l('by default', $specific) . ')',
        ],
        [
            'id' => 2,
            'name' => $this->l('After', $specific) . ' ' . $this->l('the title', $specific),
        ],
        [
            'id' => 3,
            'name' => $this->l('Before', $specific) . ' ' . $this->l('the short description', $specific),
        ],
        [
            'id' => 4,
            'name' => $this->l('After', $specific) . ' ' . $this->l('the short description', $specific),
        ],
        [
            'id' => 5,
            'name' => $this->l('Before', $specific) . ' ' . $this->l('the price', $specific),
        ],
        [
            'id' => 6,
            'name' => $this->l('After', $specific) . ' ' . $this->l('the price', $specific),
        ],
        [
            'id' => 7,
            'name' => $this->l('Before', $specific) . ' ' . $this->l('the Quantity / Add to Cart block', $specific),
        ],
        [
            'id' => 8,
            'name' => $this->l('After', $specific) . ' ' . $this->l('the Quantity / Add to Cart block', $specific),
        ],
        [
            'id' => -1,
            'name' => $this->l('Display on the Product Tabs', $specific) . ' ' . $this->l('Only if the theme supports it', $specific),
        ],
        [
            'id' => 50,
            'name' => $this->l('Custom Placement', $specific) . ' (' . $this->l('You will need to input the selector and the insertion method in the next field', $specific) . ')',
        ],
    ];
} else {
    $query = [
        [
            'id' => 1,
            'name' => $this->l('Right Column: After the Add to cart Button (Default)', $specific),
        ],
        [
            'id' => 2,
            'name' => $this->l('Left Column: After the print link, in some themes may be on the left', $specific),
        ],
        [
            'id' => 3,
            'name' => $this->l('Show after the product long description', $specific) . ' ' . $this->l('Needs the hook', $specific) . ' hookDisplayProductFooter ' . $this->l('to be in the theme', $specific),
        ],
        [
            'id' => 4,
            'name' => $this->l('Show as content Tab: Where the Sheet, More, and Review tabs are located)', $specific),
        ],
        [
            'id' => -5,
            'name' => $this->l('Show in the Display Product Delivery Time hook. Note that some themes may don\'t have it.', $specific),
        ],
    ];
}
$options_placement = ['query' => $query, 'id' => 'id', 'name' => 'name'];
$insert_options = [
    'query' => [
        [
            'id' => 1,
            'name' => $this->l('Before', $specific),
        ],
        [
            'id' => 2,
            'name' => $this->l('At the beginning', $specific),
        ],
        [
            'id' => 3,
            'name' => $this->l('At the end', $specific),
        ],
        [
            'id' => 4,
            'name' => $this->l('After', $specific),
        ],
    ],
    'id' => 'id',
    'name' => 'name',
];

$date_calculation_options = [
    'query' => [
        [
            'id' => 0,
            'name' => $this->l('Only the current product', $specific),
        ],
        [
            'id' => 1,
            'name' => $this->l('Current Product + Products in cart', $specific),
        ],
    ],
    'id' => 'id',
    'name' => 'name',
];
/*
    Date Format
    '0' => '',
    '3' => '%x', // Prefered date format based on Locale
    '4' => '%d-%m-%Y', //d-m-Y
    '5' => '%d/%m/%Y', //d/m/Y
    '6' => '%d.%m.%Y', //d-m-Y
    '7' => '%a %e', // short weekday and day number
    '8' => '%A %e', // full weekday and day number
    '9' => '%a %e %B', // short weekday day and month
    '10' => '%a, %e %B', // short weekday, day and month
    '11' => '%a %e. %B', // short weekday, day and month
    '12' => '%A %e %B', // full weekday day and month
    '13' => '%A, %e %B', // full weekday, day and month
    '14' => '%A %e. %B', // full weekday, day and month
    '1' => 'jS F', // 'jS F'
    '2' => 'l jS F', // 'jS F'
    '15' => '%F'); // 'Y-m-d'
*/

// List of force options
$force_options = [
    'query' => [
        [
            'id' => 0,
            'name' => $this->l('None (disabled)'),
        ],
        [
            'id' => 1,
            'name' => $this->l('Force all Carriers'),
        ],
        [
            'id' => 2,
            'name' => $this->l('Force selected Carriers'),
        ],
    ],
    'id' => 'id',
    'name' => 'name',
];
$summary_options = [
    [
        'id' => -1,
        'value' => -1,
        'label' => $this->l('None (disabled)'),
    ],
    [
        'id' => 1,
        'value' => 1,
        'label' => $this->l('Display a list on summary') . ' - displayShoppingCart',
    ],
    [
        'id' => 2,
        'value' => 2,
        'label' => $this->l('Display a list on footer of the summay\'s page') . ' - displayShoppingCartFooter',
    ],
];

// List of carriers to be forced
$force_carriers = [];

foreach ($carriers as $c) {
    $force_carriers[] = [
        'id' => $c['id_reference'],
        'name' => $c['name'],
    ];
}
$force_carriers = [
    'query' => $force_carriers,
    'id' => 'id',
    'name' => 'name',
];

// TODO Cange Icon selector for Unicode and Emojis
// Icon list to add 🚚  ⛟ 🚛 🚀  🛥 🛦 🛧 🛨 🛩 🛪 🛫 🛬 ✈️ ⏱ 🗓 📆 📅 🏳️ 🏴
$email_icons_options = [
    'query' => [
        [
            'id' => 0,
            'name' => $this->l('Truck 1') . '(🚚)',
        ],
    ],
    'id' => 'id',
    'name' => 'name',
];
// Product List Placement
$ed_list_placement = [];
if (version_compare(_PS_VERSION_, '1.7', '<')) {
    $ed_list_placement[] = ['id' => 0, 'name' => $this->l('Product Delivery Time Hook', $specific)];
}
if (version_compare(_PS_VERSION_, '1.6', '>=')) {
    $ed_list_placement[] = ['id' => 1, 'name' => $this->l('Product List Functional Buttons', $specific)];
}
$ed_list_placement[] = ['id' => 2, 'name' => $this->l('ED Product List Hook', $specific)];
$ed_list_placement = ['query' => $ed_list_placement, 'id' => 'id', 'name' => 'name'];
$list_delivery_keyword = [
    'query' => [
        [
            'id' => 0,
            'name' => $this->l('Deliver', $specific),
        ],
        [
            'id' => 1,
            'name' => $this->l('Receive', $specific),
        ],
        [
            'id' => 2,
            'name' => $this->l('Truck Icon', $specific),
        ],
    ],
    'id' => 'id',
    'name' => 'name',
];

/* Date Formats */
$query = [];
$date = date('Y-m-d');
foreach ($this->dateFormat as $dateFormat) {
    $df = EDTools::createDateFormat($dateFormat, $this->context->language->locale);
    $formatted_date = $df->format($date);
    $query[] = [
        'id' => $dateFormat['index'],
        'name' => $formatted_date . ' - ' . $dateFormat['desc'],
    ];
}

$date_format_options = [
    'query' => $query,
    'id' => 'id',
    'name' => 'name',
];

$query = [];
$id = 0;
foreach ($this->listDateFormat as $df) {
    $query[] = [
        'id' => $id++,
        'name' => $df['desc'],
    ];
}

$list_date_format = [
    'query' => $query,
    'id' => 'id',
    'name' => 'name',
];
// Display Options for delivery list
$display_priority_options = [
    [
        'id' => 1,
        'name' => $this->l('Fastest carrier first', $specific),
    ],
    [
        'id' => 2,
        'name' => $this->l('Cheapest carrier first', $specific),
    ],
    [
        'id' => 3,
        'name' => $this->l('Display carriers by position (ascending)', $specific),
    ],
];

$calendar_display_options = [
    [
        'id' => 'CART',
        'name' => $this->l('Display after the cart summary', $specific),
    ],
    [
        'id' => 'CARTFOOTER',
        'name' => $this->l('Display on the cart summary\'s footer', $specific),
    ],
    [
        'id' => 'CARRIERS',
        'name' => $this->l('Display after the carriers selection', $specific),
    ],
    [
        'id' => 'PAYMENT',
        'name' => $this->l('Display before the payment options', $specific),
    ],
    [
        'id' => 'HOOK',
        'name' => $this->l('Display on a custom hook', $specific),
    ],
];

// List of order states
$orderStates = [];
$order_states = OrderState::getOrderStates($this->context->language->id);
foreach ($order_states as $state) {
    $orderStates[] = [
        'id' => $state['id_order_state'],
        'name' => $state['name'],
    ];
}
$orderStates = [
    'query' => $orderStates,
    'id' => 'id',
    'name' => 'name',
];
$dd_admin_hours = Configuration::get('ed_dd_admin_hours');
$dd_customer_hours = Configuration::get('ed_dd_customer_hours');
$DD_CRON_URL = $this->context->link->getModuleLink('estimateddelivery', 'DelayedDeliveryWarning') . '?cron_secret_key=' . Tools::getAdminTokenLite('AdminModules');
$review_past_orders_ajax_url = $this->context->link->getModuleLink('estimateddelivery', 'DelayedDeliveryWarning') . '?token=' . Tools::getAdminTokenLite('AdminModules');

$sample_carrier = [
    'name' => 'DHL 24h',
    'alias' => $this->l('Express Delivery'),
];
$fields_form = [];
$fields_form['basic'] = [
    'form' => [
        'legend' => [
        'title' => '1 - ' . $this->l('Estimated Delivery Basic Settings', $specific),
        'icon' => 'icon-cogs',
        ],
        'description' => $this->l('In this section, you will be able to configure the test mode for the module and the visual design', $specific) .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            SmartForm::genDesc($this->l('Test Mode:', $specific), 'b') . ' ' .
            SmartForm::genDesc($this->l('Also known as Sandbox mode'), 'u') . '. ' . $this->l('This allows you to set up which IPs are allowed to see the module. Making it useful when testing a feature or when configuring the module for the first time. Preventing any impact on your visitors until the module is perfectly configured.', $specific) . ' ' .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            SmartForm::genDesc($this->l('If you use a cache module, be aware of the cache and make sure to tag the Estimated Delivery module as Dynamic', $specific), 'u') . '.' .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            SmartForm::genDesc($this->l('Design Options:', $specific), 'b') . ' ' .
            $this->l('Set up how the Estimated Delivery module will be displayed. The text format, the colors, the date format, if you want to display the price... all related to the design is configured here', $specific),
        'input' => [
            [
                'type' => 'switch',
                'label' => $this->l('Enable Sandbox Mode', $specific),
                'desc' => SmartForm::genDesc($this->l('Also known as test mode, enable this option to only display the module for the allowed IPs', $specific), null, 'br') .
                    SmartForm::genDesc($this->l('Enter the allowed IPs in the following field', $specific)),
                'name' => 'ED_TEST_MODE',
                'hint' => $this->l('The sandbox mode or test mode is recommended when installing the module for the first time or when trying a beta feature', $specific),
                'bool' => true,
                'values' => $switch_options],
            [
                'label' => $this->l('Restrict Tests & debug by IP') . ' (' . $this->l('Recommended') . ')',
                'type' => 'textbutton',
                'name' => 'ED_TEST_MODE_IPS',
                'hint' => $this->l('Limit the module to few IPs while testing it to not affect your users experience meanwhile', $specific),
                'desc' => SmartForm::genDesc($this->l('Restrict the module by IP', $specific) . ':', 'strong', 'br') . $this->l('This is highly recommended when you want to "play" or "test" the module configuration and options.'),
                'validation' => 'isGenericName',
                'size' => 20,
                'button' => [
                    'attributes' => [
                        'class' => 'btn btn-outline-primary add_ip_button',
                        'onclick' => 'addRemoteAddr(\'ED_TEST_MODE_IPS\');',
                    ],
                    'label' => $this->l('Add my IP'),
                    'icon' => 'plus',
                ],
            ],
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('Display Styles', $specific), ['h4', 'class="modal-title text-info"']),
                'desc' => SmartForm::genDesc('', '', 'hr'),
                'name' => 'free',
            ],
            [
                'type' => 'select',
                'label' => $this->l('Choose The Estimated Delivery Style', $specific),
                'name' => 'ED_STYLE',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Select how you want to display the Estimated Delivery', $specific),
                'desc' => SmartForm::genDesc($this->l('Choose between %s display modes'), '', '/p', [5]) .
                    SmartForm::genList(
                        [
                            SmartForm::genDesc($this->l('Carriers display', $specific) . ':', 'u') .
                            SmartForm::genDesc($this->l('Show available Carriers', $specific)),
                            SmartForm::genDesc($this->l('Order Before... Style', $specific) . ' (' . $this->l('countdown', $specific) . ':', 'u') .
                            SmartForm::genDesc($this->l('Show only one carrier and a countdown until the picking', $specific)) . ' ' .
                            SmartForm::genDesc($this->l('The results will resemble how Amazon displays it', $specific)) .
                            SmartForm::genDesc($this->l('(order before XX:XX and recive it XXX)', $specific)),
                            SmartForm::genDesc($this->l('Order Before... Style', $specific) . ':', 'u') .
                            SmartForm::genDesc('(' . $this->l('time left to picking', $specific) . ')'),
                            SmartForm::genDesc($this->l('Display Picking Day', $specific) . ':', 'u') .
                            SmartForm::genDesc($this->l('Display the day the order will be shipped', $specific)),
                            SmartForm::genDesc($this->l('Double display', $specific) . ':', 'u') .
                            SmartForm::genDesc($this->l('Display a pair of carriers based on the priorities. This allows you to display the fastest carrier + cheapest carrier', $specific)),
                            ]
                    ),
                'options' => [
                    'query' => [
                        [
                            'id' => 1,
                            'name' => $this->l('Carriers display', $specific),
                        ],
                        [
                            'id' => 2,
                            'name' => $this->l('Order Before... Style', $specific) . ' (' . $this->l('countdown ', $specific) . ')',
                        ],
                        [
                            'id' => 3,
                            'name' => $this->l('Order Before... Style', $specific) . ' (' . $this->l('time left to picking ', $specific) . ')',
                        ],
                        [
                            'id' => 4,
                            'name' => $this->l('Display Picking Day', $specific),
                        ],
                        [
                            'id' => 5,
                            'name' => $this->l('Double Display', $specific),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ],
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('Display Preview', $specific), ['h3', 'class="modal-title text-info"']),
                'desc' => SmartForm::genDesc('', '', 'hr') .
                    SmartForm::genDesc('', ['div', 'id="previewstyle"']),
                'name' => 'free',
            ],
            [
                'type' => 'text',
                'form_group_class' => 'ed_countdown_options',
                'label' => $this->l('Countdown limit', $specific),
                'desc' => $this->l('This setting allows you to set up a limit of hours for the countdown mode') . '.' .
                    SmartForm::openTag('br') .
                    sprintf($this->l('If the countdown is greater than the limit the message "%s" will be displayed instead of the hours and minutes left for the picking limit', $specific), $this->l('Buy it now')) .
                    SmartForm::closeTag('p') .
                    SmartForm::openTag('div', 'class="time-to-picking-additional-info"') .
                    SmartForm::genDesc($this->l('If you use the %s mode and the picking is greater than 6 days the countdown mode will be displayed. We recommend to set up a limit here between 1 and 144 if you don\'t want to display the hour count.', $specific), 'strong', false, [$this->l('time left to picking ', $specific)]) .
                    SmartForm::closeTag('div') .
                    SmartForm::openTag('p', 'class="help-block"') .
                    $this->l('If you don\'t want to use this feature, leave the field empty.', $specific),
                'suffix' => $this->l('hours'),
                'hint' => $this->l('Do not display the count if remaining hours are greater than...', $specific),
                'name' => 'ED_COUNTDOWN_LIMIT',
                'class' => 'fixed-width-xxl',
            ],
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('Estimated Delivery Styles', $specific), ['h4', 'class="modal-title text-info"']),
                'desc' => SmartForm::genDesc('', '', 'hr'),
                'name' => 'free',
            ],
            [
                'type' => 'select',
                'label' => $this->l('Choose date format', $specific),
                'name' => 'ED_DATE_TYPE',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('How will Estimated Delivery print the dates', $specific),
                'options' => $date_format_options,
            ],
            [
                'form_group_class' => 'ed_custom_format',
                'type' => 'text',
                'label' => $this->l('Custom Date Format', $specific),
                'name' => 'ED_DATE_CUSTOM',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Type the date format for your dates', $specific),
                'desc' => $this->l('If any of the above date formats does not march your needs you can specify your own here', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('%s to see all the available options and write your prefered one', $specific), [['a', 'href="https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table" target="_blank"'], 'span'], null, [$this->l('Click here')]),
            ],
            [
                'form_group_class' => 'ed_custom_format_regular',
                'type' => 'text',
                'label' => $this->l('Regular Date, Custom Format', $specific),
                'name' => 'ED_DATE_CUSTOM_REGULAR',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Type the date format for your dates', $specific),
                'desc' => $this->l('If any of the above date formats does not march your needs you can specify your own here', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('%s to see all the available options and write your prefered one', $specific), [['a', 'href="https://www.php.net/manual/' . $this->context->language->iso_code . '/function.date.php" target="_blank"'], 'span'], null, [$this->l('Click here')]) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('The Regular date format is not recommendable to print weekdays and months as they won\'t be translated') .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('%s to select the option to generate a custom date with weekdays and months', $specific), [['a', 'href="#" class="select_option" data-target="#ED_DATE_TYPE" data-value="-1"'], 'span'], null, [$this->l('Click here')]),
//                    $this->l('To generate a custom date with weekdays and months use the custom date option').' '.
//                    SmartForm::genDesc(
//                        $this->l('click here to select it'),
//                        ['a', 'href="" class="select_option" data-target="#ED_DATE_TYPE" data-value="-1"']
//                    ),
            ],
            [
                'type' => 'select',
                'label' => $this->l('Choose Box Color', $specific),
                'name' => 'ed_class',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Choose you Prefered Color style or choose custom to create your custom color scheme', $specific),
                'options' => [
                    'query' => [
                        [
                            'id' => '',
                            'name' => $this->l('White / No background Color', $specific),
                        ],
                        [
                            'id' => 'ed_lightblue',
                            'name' => $this->l('Light Blue', $specific),
                        ],
                        [
                            'id' => 'ed_softred',
                            'name' => $this->l('Soft Red', $specific),
                        ],
                        [
                            'id' => 'ed_lightgreen',
                            'name' => $this->l('Light Green', $specific),
                        ],
                        [
                            'id' => 'ed_lightpurple',
                            'name' => $this->l('Light Purple', $specific),
                        ],
                        [
                            'id' => 'ed_lightbrown',
                            'name' => $this->l('Light Brown', $specific),
                        ],
                        [
                            'id' => 'ed_lightyellow',
                            'name' => $this->l('Light Yellow', $specific),
                        ],
                        [
                            'id' => 'ed_orange',
                            'name' => $this->l('Light Orange', $specific),
                        ],
                        [
                            'id' => 'custom',
                            'name' => $this->l('Custom Colors', $specific),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ],
            [
                'type' => 'color',
                'label' => $this->l('Choose Background Color', $specific),
                'name' => 'ed_custombg',
                'class' => 'fixed-width-xxl edcustombg',
                'hint' => $this->l('Use the Color Picker to set up your BG Color', $specific),
            ],
            [
                'type' => 'color',
                'label' => $this->l('Choose Border Color', $specific),
                'name' => 'ed_customborder',
                'class' => 'fixed-width-xxl edcustomborder',
                'hint' => $this->l('Use the Color Picker to set up your Border Color', $specific),
            ],
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('Estimated Delivery Priority', $specific), ['h4', 'class="modal-title text-info"']),
                'desc' => SmartForm::genDesc('', '', 'hr'),
                'name' => 'free',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display the default carrier first?', $specific),
                'desc' => $this->l('Forces the default carrier to be displayed, in display methods that shows only one carrier will only show the default one', $specific),
                'hint' => $this->l('Default carrier will go first if it\'s available...', $specific),
                'name' => 'ED_DEFAULT_CARRIER_FIRST',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'type' => 'select',
                'label' => $this->l('Carrier display priority', $specific),
                'name' => 'ED_DISPLAY_PRIORITY',
                'class' => 'input fixed-width-xxl',
                'desc' => $this->l('Determines the carriers order', $specific),
                'hint' => $this->l('Which parameter should be decisive to order the carriers?', $specific),
                'options' => [
                    'query' => $display_priority_options,
                    'id' => 'id',
                    'name' => 'name',
                ],
            ],
            [
                'form_group_class' => 'ed_display_secondary_option',
                'type' => 'select',
                'label' => $this->l('Second display priority', $specific),
                'name' => 'ED_DISPLAY_PRIORITY_2',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('This will only activate if you select the double dates display', $specific),
                'desc' => sprintf($this->l('Only applicable when the %s is active', $specific), $this->l('Double Display', $specific)),
                'options' => [
                    'query' => $display_priority_options,
                    'id' => 'id',
                    'name' => 'name',
                ],
            ],
            [
                'form_group_class' => 'ed_display_secondary_option',
                'type' => 'select',
                'label' => $this->l('Can repeat carriers?', $specific),
                'name' => 'ED_DISPLAY_DOUBLE_REPEAT',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Activate this option to allow the carrier to be repeated if it wins the 2 priorities, otherwise the module will discard the carrier who won the first priority', $specific),
                'desc' => $this->l('Disabled by default', $specific) .
                    SmartForm::openTag('br') .
                    sprintf($this->l('Only applicable when the %s is active', $specific), $this->l('Double Display', $specific)) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Activate this option to allow the carrier to be repeated if it wins the 2 priorities, otherwise the module will discard the carrier who won the first priority', $specific),
                'options' => [
                    'query' => $display_priority_options,
                    'id' => 'id',
                    'name' => 'name',
                ],
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display carrier with longer picking time?'),
                'desc' => $this->l('When two carriers win the same priority, the one with the shortest picking time will be chosen', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('If you activate this setting, the one with the longest picking time will be chosen', $specific),
                'name' => 'ed_longer_picking',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'type' => 'select',
                'label' => $this->l('How you use carriers, by...', $specific),
                'name' => 'ED_SHIPPING_TYPE',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('How have your store shipping zones, by Country or State', $specific),
                'options' => [
                    'query' => [
                        [
                            'id' => 1,
                            'name' => $this->l('Country', $specific) . ' (' . $this->l('recommended', $specific) . ')',
                        ],
                        [
                            'id' => 2,
                            'name' => $this->l('State', $specific),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ],
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('Estimated Delivery Placement', $specific), ['h4', 'class="modal-title text-info"']),
                'desc' => SmartForm::genDesc('', '', 'hr'),

                'name' => 'free',
            ],
            [
                'type' => 'select',
                'label' => $this->l('Where do you want the Estimated Delivery box appear', $specific),
                'name' => 'ED_LOCATION',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Choose the location of the Estimated Delivery information box', $specific),
                'desc' => (!$this->is_17 ? $this->l('Choose between 4 locations. The 3 first are included by default on any theme', $specific) .
                    '. ' . SmartForm::openTag('br') .
                    $this->l('The 4th option Show in the Display Product Delivery Time hook requieres to have the hook set in your theme', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('If you choose this option and don\'t see the estimated delivery you can add this line ', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc('{hook h="displayProductDeliveryTime" product=$product}', 'strong', 'br') .
                    $this->l('anywhere in your product.tpl file to display the estimated delivery in a custom position', $specific) . '.' : $this->l('Choose your prefered location to display the module', $specific) .
                    SmartForm::openTag('br') .
                    sprintf($this->l('To place the module in a custom location use the option location "%s"', $specific), $this->l('Custom Placement', $specific))) .
                    SmartForm::openTag('br') .
                    $this->l('Then input the selector where to place the Estimated delivery and the insertion method', $specific),
                'options' => $options_placement,
            ],
            [
                'type' => 'text',
                'form_group_class' => 'ed_cust_placement',
                'name' => 'ED_LOCATION_SEL',
                'class' => 'input fixed-width-xxl',
                'label' => $this->l('Custom Selector', $specific),
                'desc' => $this->l('Selector for the element which will hold the Estimated Delivery', $specific),
                'hint' => $this->l('Enter a valid query selector...', $specific),
            ],
            [
                'type' => 'select',
                'form_group_class' => 'ed_cust_placement',
                'label' => $this->l('Insertion method', $specific),
                'name' => 'ED_LOCATION_INS',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Choose the insertion method', $specific),
                'desc' => $this->l('Choose the insertion method', $specific),
                'options' => $insert_options,
            ],
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('Date Calculation Method', $specific), ['h4', 'class="modal-title text-info"']),
                'desc' => SmartForm::genDesc('', '', 'hr'),
                'name' => 'free',
            ],
            [
                'type' => 'select',
                'label' => $this->l('Date Calculation Method', $specific),
                'name' => 'ED_CALCULATION_METHOD',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('How the delivery date should be calculated on the product page', $specific),
                'desc' => $this->l('How the delivery date should be calculated on the product page', $specific) .
                        '. ' . SmartForm::openTag('br') .
                        $this->l('Option 1: Current product. Calculate the delivery date only for the current product', $specific) .
                        SmartForm::openTag('br') .
                        $this->l('Option 2: Product + Products in cart. Calculate the delivery date based on the current product + al the products already in the cart', $specific),
                'options' => $date_calculation_options,
            ],
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('Delivery Prices', $specific), ['h4', 'class="modal-title text-info"']),
                'desc' => SmartForm::genDesc('', '', 'hr'),
                'name' => 'free',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display Prices?', $specific) . ' (' . $this->l('beta feature', $specific) . ')',
                'desc' => $this->l('Enable this option to show the estimated delivery price along with the date.', $specific),
                'name' => 'ed_disp_price',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Price Prefix:', $specific),
                'desc' => $this->l('Text to show before the price, i.e. "(" or "-"', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('leave it blank to disable this feature', $specific),
                'name' => 'ed_price_prefix',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Price Suffix:', $specific),
                'desc' => $this->l('Text to show after the price, i.e. ")"', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('leave it blank to disable this feature', $specific),
                'name' => 'ed_price_suffix',
            ],
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('Additional display options', $specific), ['h4', 'class="modal-title text-info"']),
                'desc' => SmartForm::genDesc('', '', 'hr'),
                'name' => 'free',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Show ED in cart modal', $specific) . ' (' . $this->l('beta') . ')',
                'desc' => $this->l('Enable this option to show the estimated delivery dates in the modal box displayed after ajax cart successes.', $specific),
                'hint' => $this->l('Beta features are not warranted to work', $specific),
                'name' => 'ed_cart_modal',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Enable tooltip', $specific),
                'desc' => $this->l('Tooltip is a text displayed over the date and the carrier name to display additional information', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Some themes doesn\'t support it, if you don\'t see the date try disabling it.', $specific),
                'hint' => $this->l('Use this if you have multiple picking times.', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Format: 00:00', $specific),
                'name' => 'ed_tooltip',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'type' => 'free',
                'label' => SmartForm::genDesc($this->l('All Carriers Popup Button', $specific), ['h4', 'class="modal-title text-info"']),
                'desc' => SmartForm::genDesc('', '', 'hr'),
                'name' => 'free',
            ],
            [
                'type' => 'switch',
                'name' => 'ED_DISPLAY_POPUP_CARRIERS',
                'label' => $this->l('Display All carriers Pop-up button', $specific) . ' (' . $this->l('beta') . ')',
                'desc' => $this->l('Add a button to display all the carriers information in a popup fashion.', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Only avilable for PS 1.7 versions', $specific), 'strong') .
                    SmartForm::openTag('br') .
                    $this->l('Soon it will also be available for PS 1.6', $specific),
                'hint' => $this->l('Beta features are not warranted to work', $specific),
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'type' => 'radio',
                'name' => 'ED_DISPLAY_POPUP_CARRIERS_NAME',
                'label' => $this->l('Carrier Name Display', $specific),
                'desc' => $this->l('Choose how the carrier name will be displayed', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('You can choose to display the carrier name, the alias or a combination of both', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Example usage:') .
                    SmartForm::openTag('br') .
                    '- ' . sprintf($this->l('Carrier: %s'), $sample_carrier['name']) .
                    SmartForm::openTag('br') .
                    '- ' . sprintf($this->l('Alias: %s'), $sample_carrier['alias']) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Display Options:') . ' ' . $this->l('You can choose to display the carrier name, the alias or a combination of both', $specific),
                    SmartForm::genList([
                        'Name: ' . $sample_carrier['name'],
                        'Alias: ' . $sample_carrier['alias'],
                        'Name + Alias: ' . $sample_carrier['name'] . ' (' . $sample_carrier['alias'] . ')',
                        'Alias + Name: ' . $sample_carrier['alias'] . ' (' . $sample_carrier['name'] . ')',
                    ]),
                'values' => [
                    [
                        'id' => 'name',
                        'value' => 'name',
                        'label' => $this->l('Carrier name', $specific),
                    ],
                    [
                        'id' => 'alias',
                        'value' => 'alias',
                        'label' => $this->l('Carrier alias', $specific),
                    ],
                    [
                        'id' => 'name:alias',
                        'value' => 'name:alias',
                        'label' => $this->l('Carrier Name (Alias)', $specific),
                    ],
                    [
                        'id' => 'alias:name',
                        'value' => 'alias:name',
                        'label' => $this->l('Alias (Carrier Name)', $specific),
                    ],
                ],
            ],
            [
                'type' => 'switch',
                'name' => 'ED_DISPLAY_POPUP_CARRIERS_IMG',
                'label' => $this->l('Display Carrier Image?', $specific),
                'desc' => $this->l('Enable this setting to display the carrier image before it\'s name', $specific),
                'values' => $switch_options,
            ],
            [
                'type' => 'switch',
                'name' => 'ED_DISPLAY_POPUP_CARRIERS_DESC',
                'label' => $this->l('Display Carrier Description?', $specific),
                'desc' => $this->l('Enable this setting to add a column to display the carrier\'s description', $specific),
                'values' => $switch_options,
            ],
            [
                'type' => 'switch',
                'name' => 'ED_DISPLAY_POPUP_CARRIERS_PRICE',
                'label' => $this->l('Display Carrier Price?', $specific),
                'desc' => $this->l('Enable this setting to add a column to display the carrier\'s price', $specific),
                'values' => $switch_options,
            ],
            [
                'type' => 'switch',
                'name' => 'ED_DISPLAY_POPUP_BACKGROUND',
                'label' => $this->l('Display a Background?', $specific),
                'desc' => $this->l('Enable this setting to display a background beneath the popup to block interaction with other page elements.', $specific),
                'values' => $switch_options,
            ],
        ],
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];
$fields_form['messages'] = [
    'form' => [
        'legend' => [
            'title' => '1.1 - ' . $this->l('Special Dates', $specific) . ' - ' . $this->l('Release, Avilable & Virtual Products', $specific),
            'icon' => 'icon-calendar',
            'description' => $this->l('Configure the messages to display for Virtual Products, products with a release date (Presales) or products with a fixed restock date (available date)'),
        ],
        'description' => $this->l('Set up the custom messages for the different delivery situations. Pre-order products, available date, virtual products, customized products, undefined deliveries...', $specific),
        'input' => [
            [
                'type' => 'text',
                'lang' => true,
                'col' => 9,
                'label' => $this->l('Pre-order Products Message', $specific),
                'desc' => $this->l('Message to display when a product isn\'t released yet', $specific) . '. ' .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Write the message and add %s where you want the date to appear', $specific), 'strong', false, ['{date}']) .
                    SmartForm::openTag('br') .
                    $this->l('Examples:', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genList(
                        [
                            sprintf($this->l('Pre-order now. And receive it on %s', $specific), '{date}'),
                            sprintf($this->l('This product has not been released yet, pre-order now to receive it on %s', $specific), '{date}'),
                            sprintf($this->l('Pre-order now. And receive it on %s', $specific), '{date}'),
                            '...',
                        ]
                    ),
                'name' => 'ed_preorder_msg',
            ],
            [
                'type' => 'text',
                'lang' => true,
                'col' => 9,
                'label' => $this->l('Available Date Products Message', $specific),
                'desc' => $this->l('Fill this field if you want to show a custom message for products with available date instead of calculating the delivery', $specific) .
                    '. ' . $this->l('Leave it blank to allow the Estimated Delivery to perform the date calculation') .
                    SmartForm::openTag('br') .
                    $this->l('Message to display when a product out of stock will be available on a future date', $specific) . '.' .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Write the message and add %s where you want the date to appear', $specific), 'strong', false, ['{date}']) . '.' .
                    SmartForm::openTag('br') .
                    $this->l('Examples:', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genList(
                        [
                            sprintf($this->l('This product will be available again on %s', $specific), '{date}'),
                            sprintf($this->l('We will receive stock for this product on %s', $specific), '{date}'),
                            '...',
                        ]
                    ),
                'name' => 'ed_available_date_msg',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Apply OOS Days to Available Dates?', $specific),
                'desc' => SmartForm::genDesc($this->l('Disabled by default', $specific), 'strong') .
                    SmartForm::openTag('br') .
                    $this->l('Enable this option if you want to add the Additional OOS configured in products with an available date', $specific),
                'name' => 'ED_APPLY_OOS_TO_AVAIL',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'type' => 'select',
                'label' => $this->l('Choose date format for Preorder and Available dates', $specific),
                'name' => 'ED_SPECIAL_DATE_FORMAT',
                'class' => 'fixed-width-xxl',
                'hint' => $this->l('How will Estimated Delivery print the dates', $specific),
                'options' => $date_format_options,
            ],
            [
                'type' => 'free',
                'label' => '',
                'name' => 'free',
                'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr'),
            ],
            [
                'type' => 'text',
                'lang' => true,
                'col' => 9,
                'label' => $this->l('Virtual Products Message', $specific),
                'desc' => $this->l('Message to display for delivery when there is a virtual product', $specific) . ' ' . $this->l('or', $specific) .
                    SmartForm::genDesc($this->l('leave it blank to disable this feature', $specific), '</strong>') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Examples:', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genList(
                        [
                            $this->l('Instantly', $specific),
                            $this->l('Instant delivey after purchase', $specific),
                            $this->l('A few minutes after purchase', $specific),
                            '...',
                        ]
                    ),
                'name' => 'ed_virtual_msg',
            ],
            [
                'type' => 'text',
                'lang' => true,
                'col' => 9,
                'label' => $this->l('Customization Date Products Message', $specific),
                'desc' => $this->l('Fill this field if you want to show a custom message for products with customization date instead of calculating the delivery', $specific) . '. ' .
                    $this->l('Leave it blank to allow the Estimated Delivery to perform the date calculation') .
                    SmartForm::openTag('br') .
                    $this->l('Message to display when a product is customized on a future date', $specific) . '.' .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Write the message and add %s where you want the date to appear', $specific), 'strong', false, ['{date}']) . '.' .
                    SmartForm::genList(
                        [
                            sprintf($this->l('This product is customized and it has %s extra delivery', $specific), '{date}'),
                            sprintf($this->l('We will deliver this product on %s', $specific), '{date}'),
                            '...',
                        ]
                    ),
                'name' => 'ed_custom_date_msg',
            ],
            [
                'type' => 'text',
                'lang' => true,
                'col' => 9,
                'label' => $this->l('Undefined Date Message', $specific),
                'desc' => sprintf($this->l('Fill this field if you want to show a custom message for products with an undefined delivery date as configured in section %d', $specific), '2.6') . '. ' .
                    $this->l('Leave it blank to generate the default message which is:') .
                    SmartForm::openTag('br') .
                    $this->l('Delivery date to be established. You will be contacted as soon as we have a estimated date', $specific) . '.',
                'name' => 'ed_undefined_delivery_msg',
            ],
        ],
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];
$fields_form['picking'] = [
    'form' => [
        'legend' => [
        'title' => '2 - ' . $this->l('Picking Days', $specific),
        'icon' => 'icon-time',
        ],
        'description' => $this->l('Click on a day to activate or deactivate the picking for that day (days you prepare orders)', $specific) .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('Set up the time limit for you to receive an order and prepare it the same day', $specific),
        'input' => [
            [
                'type' => 'checklimit',
                'label' => $this->l('Select Order Picking days', $specific),
                'desc' => $this->l('Click on the day to enable the picking for that day', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Set up a picking limit for the enabled days', $specific) . '. ' . $this->l('Format 00:00', $specific),
                'hint' => $this->l('Click on the days you prepare orders and configure the order preparation\'s time limit for that day', $specific),
                'name' => 'ed_picking_days',
                'name2' => 'ed_picking_limit',
            ],
        ],
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];
// Picking advanced mode
$fields_form['picking_advanced'] = [
    'form' => [
        'legend' => [
        'title' => '2.1 - ' . $this->l('Picking Days', $specific) . ' - (' . $this->l('advanced mode', $specific) . ')',
        'icon' => 'icon-time',
        ],
        'description' => $this->l('Optional feature. Set up individually the picking for each carrier', $specific),
        'input' => $this->printCarriers($carriers, 'picking', $weekdays),
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];
// Shipping days by Carrier
$fields_form['shipping'] = [
    'form' => [
        'legend' => [
        'title' => '3 - ' . $this->l('Carrier Shipping Days', $specific),
        'icon' => 'icon-truck',
        ],
        'description' => $this->l('Select which days the carriers can perform a delivery') . '.' .
            SmartForm::openTag('br') .
            $this->l('Usual values are from Monday to Friday, only Saturdays...', $specific),
        'input' => $this->printCarriers($carriers, 'weekdays', $weekdays),
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];
// Carriers Advanced Mode
$fields_form['carrier_advanced'] = [
    'form' => [
        'legend' => [
        'title' => '3.1 - ' . $this->l('Carriers', $specific) . ' - (' . $this->l('advanced mode', $specific) . ')',
        'icon' => 'icon-truck',
        ],
        'description' => $this->l('Control the carriers individually to choose which ones should be displayed on the product page') . '.' .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('Usually, "pick-up in store" or "click and collect" carriers should be disabled here since they always outperform the ones that requires a physical delivery', $specific) . '.' .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('If you have enabled the option to show the Estimated Delivery on orders, you can re-enable the carriers disabled here.', $specific),
        'input' => $this->printCarriers($carriers, 'advanced', $switch_options),
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];
// Delivery days by Carrier
$fields_form['carrier_delivery'] = [
    'form' => [
        'legend' => [
        'title' => '4 - ' . $this->l('Carrier Delivery Intervals', $specific),
        'icon' => 'icon-calendar',
        ],
        'description' => $this->l('Set up the number of days a carrier need to delivery the goods.', $specific) .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('Set the same days in the minimum and maximum fields to display a single date.', $specific) .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('Select different delivery days to display a delivery range.', $specific) .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('Value meanings:', $specific) .
            SmartForm::genList([
                $this->l('0 - For same day deliveries', $specific),
                $this->l('1 - 24h delivery', $specific),
                $this->l('2 - 48h delivery', $specific),
                $this->l('...', $specific),
            ]),
        'input' => $this->printCarriersDelivery($carriers),
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];
$fields_form['orders'] = [
    'form' => [
        'legend' => [
        'title' => '5 - ' . $this->l('Orders - Process, History and Emails', $specific),
        'icon' => 'icon-credit-card',
        ],
        'description' => $this->l('Enable this section to display the delivery dates on the order process') . '.' .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('By default, the module will add the delivery dates on the carrier selection step, but you can enable additional placements such as the summary or below each product (a special hook is required in this case).', $specific) .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('You can also display the carriers disabled on section 3.1 (optional) or perform some other features such as the LDA (Long delivery advise), display the ED in the invoice, and many other options.', $specific),
        'input' => [
            [
                'type' => 'switch',
                'label' => $this->l('Enable the Estimated Delivery on Orders?', $specific),
                'desc' => SmartForm::genDesc($this->l('Enablling this feature will allow you to:', $specific), 'strong') .
                    SmartForm::openTag('br') .
                    SmartForm::genList(
                        [
                            $this->l('Show it during the order process', $specific),
                            $this->l('Save the Estimated Delivery in the Database', $specific),
                            $this->l('Add the Estimated Delivery Information on emails', $specific) . ' (' . $this->l('optional', $specific) . ')',
                            $this->l('Show the Estimated Delivery in the order details', $specific) . ' (' . $this->l('BO and FO', $specific) . ')',
                        ]
                    ),
                'name' => 'ED_ORDER',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'hr') .
                    SmartForm::openTag('br', '', true) .
                    SmartForm::genDesc($this->l('Back Office Orders Page', $specific), ['h3', 'class="modal-title text-info"']) .
                    $this->l('Choose whenever you want to display the ED data on the orders list'),
                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Add Picking and Shipping data?', $specific),
                'hint' => $this->l('Add the Picking Day and the Delivery date columns on the orders list', $specific),
                'desc' => $this->l('Enabling this feature will add the information about picking the limit and the estimated delivery dates for your orders in the Back Office orders list', $specific),
                'name' => 'ED_ORDER_BO_COLUMNS',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'hr') .
                    SmartForm::openTag('br', '', true) .
                    SmartForm::genDesc($this->l('Cart Summary Display Options', $specific), ['h3', 'class="modal-title text-info"']) .
                    $this->l('Choose how you want to display the Estimated Delivery in the cart summary page'),
                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'radio',
                'label' => $this->l('Display ED options after the cart summary', $specific),
                'hint' => $this->l('Display a list with the delivery options after the customer\'s cart summary', $specific),
                'desc' => $this->l('Choose how you will like to display the Estimated Delivery options after the product\'s cart summary', $specific),
                'name' => 'ED_ORDER_SUMMARY',
                'values' => $summary_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Add the ED data below each product in the cart summary', $specific),
                'hint' => $this->l('On each product line, add the delivery information of that product?', $specific),
                'desc' => SmartForm::genDesc($this->l('Beta Feature'), 'strong') . '. ' . $this->l('Enable this feature to add the individual delivery time of each product on the cart summary.', $specific) .
                    SmartForm::closeTag('p') .
                    SmartForm::openTag('p') .
                    $this->l('If you enable this feature, a special hook will be required on the cart summary template:', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc('{hook h=\'displayCartSummaryProductDelivery\' product=$product}', ['span', 'class="badge badge-info"']) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('The hook must be placed inside this file:', $specific) . ' ' .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc('/themes/' . _THEME_NAME_ . '/templates/checkout/_partials/cart-detailed_product-line.tpl', ['span', 'class="badge badge-info"']),
                'name' => 'ED_ORDER_SUMMARY_PRODUCT',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'text',
                'label' => $this->l('HTML to insert when using the hook', $specific),
                'name' => 'ED_ORDER_SUMMARY_LINE',
                'lang' => false,
                'hint' => $this->l('To improve the visuals, HTML is permitted and recommended', $specific),
                'desc' => SmartForm::genDesc($this->l('To better integrate the delivery date you may want to add some HTML', $specific), 'p') .
                    SmartForm::genDesc($this->l('Write the HTML and then use the special variable %s to tell the module where to display the Estimated Delivery info', $specific) . '.', 'p', false, ['{estimateddelivery}']) .
                    SmartForm::openTag('br') .
                    $this->l('For example:', $specific) .
                    SmartForm::openTag('br') .
                    htmlentities(
                        SmartForm::openTag('div', 'class="row"') .
                        SmartForm::genDesc('', ['div', 'class="col-md-3 col-xs-4"']) .
                        SmartForm::genDesc('{estimateddelivery}', [['div', 'class="col-md-9 col-xs-8"'], 'strong']) .
                        SmartForm::closeTag('div')
                    ),
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'text',
                'label' => $this->l('Custom order controller', $specific),
                'name' => 'ED_CUST_CHECKOUT',
                'lang' => false,
                'hint' => $this->l('Only if you use a module for the checkout and the module does not automatically detect it', $specific),
                'desc' => SmartForm::genDesc($this->L('Only required if the ED is not displayed on the order process'), 'strong', 'br') .
                    $this->l('Enter the controller name for the checkout process if is not standard', $specific) .
                    SmartForm::openTag('br') .
                    sprintf($this->l('The module is compatible with the most popular One Page Checkout modules, but if you use a module for the checkout and the %s does not show in the delivery options you will probably need to enter the controller in this field.', $specific), $this->l('Estimated Delivery', $specific)) .
                    SmartForm::genDesc('', '', 'br'),
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'hr') .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Order Process Options', $specific), ['h3', 'class="modal-title text-info"']) .
                    $this->l('Choose how you want to display the Estimated Delivery during the order steps'),
                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'select',
                'label' => $this->l('How to show the ED in the Carrier Selection', $specific),
                'name' => 'ED_ORDER_TYPE',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Choose along with each carrier to show the Estimated Delivery just after the carrier description', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Choose after carriers to show the Estimated Delivery for the carrier selected', $specific) .
                    ' (' . $this->l('after the carriers list', $specific) . ')',
                'options' => [
                    'query' => [
                        [
                            'id' => -1,
                            'name' => $this->l('None', $specific),
                        ],
                        [
                            'id' => 0,
                            'name' => $this->l('Each Carrier', $specific) . ' (' . $this->l('recommended', $specific) . ')',
                        ],
                        [
                            'id' => 1,
                            'name' => $this->l('After Carriers List', $specific),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'select',
                'label' => $this->l('Force carriers on orders', $specific),
                'hint' => $this->l('Any carrier disabled in the Advanced options can be forced here. You can force all carriers or just some specific ones', $specific),
                'desc' => $this->l('Calculate the estimated delivery for the disabled carriers.', $specific) . '.' . SmartForm::genDesc('', '', 'br') .
                    $this->l('This opion is only taken into account if you have the advanced carriers option enabled and it\'s only applicable to the order process', $specific),
                'name' => 'ED_ORDER_FORCE',
                'options' => $force_options,
            ],
            [
                'form_group_class' => 'ed_order_options force_carriers',
                'type' => 'checkbox',
                'hint' => $this->l('Select which carriers do you want to force in the order process', $specific),
                'label' => $this->l('Select carriers to force', $specific), // $this->l('Force all carriers', $specific),
                'name' => 'ED_ORDER_FORCE_CARRIER',
                'bool' => true,
                'values' => $force_carriers,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Hide carrier delay info', $specific),
                'hint' => $this->l('Tries to hide the default carrier description in the carrier selection step', $specific),
                'desc' => sprintf($this->l('Only if you have selected the "%s" option', $specific), $this->l('Each Carrier', $specific)) .
                    SmartForm::openTag('br') .
                    $this->l('When this setting is enabled, the module will try to remove the default delay message added by the carrier description field', $specific),
                'name' => 'ED_ORDER_HIDE_DELAY',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'hr') .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Longer Delivery Advise', $specific), ['h3', 'class="modal-title text-info"']) .
                    $this->l('This feature will allow you to notify the client when the cart has a both type of products: in stock and out of stock at the same time') .
                    SmartForm::openTag('br', '', true) .
                    $this->l('This is useful to inform the customers of a longer delivery time due to the product out of stock') .
                    SmartForm::openTag('br', '', true) .
                    $this->l('There is also a special option for those shops who will split the delivery in two parts one for the products in stock and the other one for products out of stock'),
                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Longer Delivery Advice', $specific) . ' - ' . $this->l('LDA'),
                'hint' => sprintf($this->l('Enable the %s?', $specific), $this->l('Longer Delivery Advice', $specific)),
                'desc' => $this->l('Show an additional message to the customer when the cart has both types of products, in stock and out of stock', $specific),
                'name' => 'ED_ORDER_LONG',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'text',
                'label' => $this->l('Message for LDA', $specific),
                'name' => 'ed_order_long_msg',
                'lang' => true,
                'hint' => $this->l('The message that will be shown if the option avobe is enabled', $specific),
                'desc' => $this->l('Examples', $specific) . ':' .
                    SmartForm::openTag('br') .
                    $this->l('Some products have a longer Delivery Date', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('There are several products with a longer delivery date in this order', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('There are several products with a longer delivery date in this order. To make you receive the products as soon as possible we will divide it in two shippings at no extra cost for you', $specific),
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Show fastest delivery date', $specific),
                'desc' => $this->l('If you have the option for LDA enabled and you want to show the fastest non Out Of Stock Delivery Date enable this option', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('This is helpful if you are going to make two deliveries', $specific),
                'name' => 'ED_ORDER_LONG_NO_OOS',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'hr') .
                    SmartForm::openTag('br', '', true) .
                    SmartForm::genDesc($this->l('Individual Product Dates', $specific), ['h3', 'class="modal-title text-info"']) .
                    $this->l('This feature will allow you to individually display each product delivery date') .
                    SmartForm::openTag('br', '', true) .
                    $this->l('This is useful for shops who send the products as soon as they are ready, splitting the shipping process into several parts') .
                    SmartForm::openTag('br', '', true) .
                    $this->l('The module will group the products by delivery date'),
                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Enable Individual Product Dates', $specific) . ' (IPD)',
                'desc' => $this->l('Enable this setting to show the delivery date for each product instead of the global computed estimation', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Only enable this setting if you are going to ship the products separately', $specific),
                'name' => 'ED_DATES_BY_PRODUCT',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => sprintf($this->l('Update the %s mode when regenerating the dates', $specific), 'IPD'),
                'desc' => $this->l('Then the Estimated Delivery date is updated from the back office or a payment validates a new date is generated', $specific) .
                    SmartForm::openTag('br') .
                    sprintf($this->l('Enable this option to force the update in the state related to the %s option', $specific), 'IPD'),
                'name' => 'ED_DATES_BY_PRODUCT_FORCE',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'hr') .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Calendar Days', $specific) . ' - Beta', ['h3', 'class="modal-title text-info"']) .
                    $this->l('Add a calendar to select the Desired Delivery date', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('This will allow the customer to select a desired delivery date between some days.', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('To generate the elegible days the module first calculates the minimum delivery date, then it adds X number of selectable days depending on the settings', $specific),
                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Display calendar', $specific),
                'desc' => $this->l('Display calendar to check custom delivery date', $specific),
                'name' => 'ED_CALENDAR_DATE',
                'hint' => $this->l('Display calendar to check custom delivery date', $specific),
                'bool' => true,
                'values' => $switch_options,
            ],
            /* // TODO in the future
             array(
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Force Calendar Day Selection'),
                'desc' => $this->l('Block the order confirmation button until the calendar date has been selected'),
                'name' => 'ED_CALENDAR_DATE_FORCE',
                'hint' => $this->l('Force the user to select a calendar date before completing the order'),
                'bool' => true,
                'values' => $switch_options,
            ),*/
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'text',
                'label' => $this->l('Calendar (Number of Estimated delivery days)', $specific),
                'desc' => $this->l('Input for the number of selectable delivery days', $specific),
                'name' => 'ED_CALENDAR_DATE_DAYS',
                'hint' => $this->l('Input for the number of selectable delivery days', $specific),
            ],
            [
                'form_group_class' => 'ed_order_options checkbox_no_float',
                'type' => 'checkbox',
                'label' => $this->l('Choose where to display the calendar', $specific),
                'name' => 'ED_CALENDAR_DISPLAY',
                'class' => 'input',
                'hint' => $this->l('choose the placements to display the calendar', $specific),
                'values' => [
                    'query' => $calendar_display_options,
                    'id' => 'id',
                    'name' => 'name',
                ],
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'hr') .
                    SmartForm::genDesc($this->l('Emails - Order Related Emails', $specific), ['h3', 'class="modal-title text-info"']) .
                    sprintf($this->l('To add the Estimated Delivery information on emails just add the special variable %s in your email template', $specific), SmartForm::genDesc('{estimateddelivery}', 'strong')) .
                    SmartForm::openTag('br') .
                    $this->l('We do recommend adding it to the payment method that requires validation and in the Paymen Accepted template', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('When an order is created using a payment that requires manual validation like bankwire, check...', $specific) . ' ' . $this->l('an advice will be displayed to make clear the date is orientative and it will be updated once the payment is confirmed', $specific),
                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Add check box to email subject', $specific),
                'desc' => sprintf($this->l('Enable this option to add a checkbox icon (%s) in the email subject for the updated ED email', $specific), '✔'),
                'name' => 'ED_EMAIL_ICON',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'select',
                'label' => $this->l('Choose date format', $specific),
                'name' => 'ED_EMAIL_DATE_FORMAT',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('How will Estimated Delivery print the dates', $specific),
                'options' => $date_format_options,
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'hr') .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Invoices', $specific), ['h3', 'class="modal-title text-info"']),
                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_order_options',
                'type' => 'switch',
                'label' => $this->l('Show ED on invoice', $specific),
                'desc' => $this->l('Enable or Disable to show ED on invoice ', $specific),
                'name' => 'ED_SHOW_INVOICE',
                'bool' => true,
                'values' => $switch_options,
            ],
        ],
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];
$test_path = dirname(__FILE__) . '/../logs/dd_test_results.txt';
$dd_test_results = Tools::file_exists_cache($test_path);
if ($dd_test_results) {
    $dd_test_results = SmartForm::genDesc($this->l('Open Last Test results (%s)'), ['a', 'class="badge" href="' . $this->context->link->getBaseLink() . 'modules/' . $this->name . '/logs/dd_test_results.txt" target="_blank"'], false, [date($this->context->language->date_format_full, filemtime($test_path))]);
    unset($test_path);
}
$fields_form['delayed_delivery_warning'] = [
    'form' => [
        'legend' => [
            'title' => '5.1 - ' . $this->l('Delayed delivery warning', $specific) . ' (' . $this->l('beta', $specific) . ')',
            'icon' => 'icon-warning',
        ],
        'description' => $this->l('Enable this section if you want to automatically notify your clients about the delayed deliveries (based on the selected order state). This section also allows you send you a reminder before the delayed delivery happens to try to prevent it whenever it\'s possible', $specific) . '.' .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            sprintf($this->l('To be able to automatize this process, a cron job have to be configured. When you activate this seccion, a new menu will appear on the left column called "%s". There you will be able to find a guide to configure it.', $specific), $this->l('Cron Job')),
        'input' => [
            [
                'type' => 'switch',
                'label' => $this->l('Enable the Delayed Delivery Messages', $specific),
                'name' => 'ed_enable_delayed_delivery',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'select',
                'class' => 'input fixed-width-xxl fixed-width-xl',
                'label' => $this->l('Shipped Order\'s state', $specific),
                'desc' => $this->l('Choose which order state considers an order to be shipped. All previous states from the order will be checked too', $specific) .
                    SmartForm::genDesc($this->l('If it\'s found, the order will be skipped from the Delayed Delivery check', $specific), ['p', 'class="help-block"']),
                'name' => 'ed_dd_order_state',
                'options' => $orderStates,
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'html',
                'label' => $this->l('Preprocess existing orders', $specific),
                'html_content' => SmartForm::genDesc($this->l('Review past orders', $specific), ['a', 'class="ajaxcall-review-past-orders btn btn-default" href="' . $review_past_orders_ajax_url . '"']),
                'desc' => $this->l('Click on this button to review all past orders.', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('This will take into account the "shipped" order state configured in this section.', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('The first time you configure this feature is recommended to click at this button once to greatly improve the efficiency of the delayed deliveries check.', $specific),
                'name' => 'review_order',
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'text',
                'class' => 'fixed-width-xxl only-numbers',
                'suffix' => $this->l('days'),
                'label' => $this->l('How far should the Delayed Delivery check', $specific),
                'desc' => $this->l('Input the number of days for the Delayed Delivery to check.', $specific),
                'name' => 'ed_dd_days_limit',
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'free',
                'name' => 'free',
                'label' => SmartForm::genDesc($this->l('Admin Notification Options', $specific), ['h3', 'class="modal-title text-info"']),
                'desc' => SmartForm::openTag('br') .
                    SmartForm::genDesc('', '', 'hr'),
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'html',
                'label' => $this->l('Hours to notify the admin', $specific),
                'html_content' => SmartForm::openTag('div', 'class="input-group fixed-width-xl"') .
                    SmartForm::openTag('input', 'type="text" name="ed_dd_admin_hours" id="ed_dd_admin_hours" value="' . $dd_admin_hours . '" class="fixed-width-xl" onkeypress="return /\d/.test(String.fromCharCode(((event||window.event).which||(event||window.event).which)));"', true) .
                    SmartForm::genDesc($this->l('Hours', $specific), ['span', 'class="input-group-addon"']) .
                    SmartForm::closeTag('div'),
                'desc' => SmartForm::genDesc($this->l('Number of hours prior of the minimum delivery date to send the message.', $specific), 'strong') .
                    SmartForm::genDesc($this->l('Configure the number of hours you want to receive the delayed message before the order\'s minimum delivery date.', $specific), ['p', 'class="help-block"']) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Delivery days time is always 00:00:00. Keep this in mind to calculate the time difference needed for the messages', $specific), 'em'),
                'name' => 'ed_dd_admin_hours',
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'text',
                'label' => $this->l('Email address to notify the Admin', $specific),
                'desc' => $this->l('If no change, it will use the default email address of the  shop admin.', $specific),
                'name' => 'ed_dd_admin_email',
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'free',
                'name' => 'free',
                'label' => SmartForm::genDesc($this->l('Customer Notification Options', $specific), ['h3', 'class="modal-title text-info"']),
                'desc' => SmartForm::openTag('br') .
                    SmartForm::genDesc('', '', 'hr'),
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'html',
                'label' => $this->l('Hours to notify the customer', $specific),
                'html_content' => SmartForm::openTag('div', 'class="input-group fixed-width-xl"') .
                    SmartForm::openTag('input', 'type="text" name="dd_customer_hours" id="dd_customer_hours" value="' . $dd_customer_hours . '" class="fixed-width-xl" onkeypress="return /\d/.test(String.fromCharCode(((event||window.event).which||(event||window.event).which)));"', true) .
                    SmartForm::genDesc($this->l('Hours', $specific), ['span', 'class="input-group-addon"']) .
                    SmartForm::closeTag('div'),
                'desc' => SmartForm::genDesc($this->l('Number of hours prior of the minimum delivery date to send the message.', $specific), 'strong') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Configure the number of hours you want to notify the customer about the delayed delivery before the order\'s minimum delivery date.', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc($this->l('Delivery days time is always 00:00:00. Keep this in mind to calculate the time difference needed for the messages', $specific), 'em'),
                'name' => 'ed_dd_customer_hours',
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'switch',
                'label' => $this->l('Enable to send a copy of the customer email to the admin', $specific),
                'name' => 'ed_enable_cc_email',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'free',
                'name' => 'free',
                'label' => SmartForm::genDesc($this->l('Cron Job / Manual URL', $specific), ['h3', 'class="modal-title text-info"']),
                'desc' => SmartForm::openTag('br') .
                    SmartForm::genDesc('', '', 'hr'),
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'html',
                'label' => $this->l('Delayed Delivery Processing URL ', $specific),
                'html_content' => SmartForm::openTag('div', 'class="input-group"') .
                    SmartForm::openTag('input', 'class="click_to_copy" type="text" name="cron_job_url" id="cron_job_url" value="' . $DD_CRON_URL . '" readonly', true) .
                    SmartForm::genDesc($this->l('Click To Copy', $specific), ['span', 'class="input-group-addon"']) .
                    SmartForm::closeTag('div'),
                'desc' => $this->l('Open above URL to check the orders and send the messages.', $specific) . SmartForm::openTag('br') .
                          $this->l('If you want to automatize this process, please create a cron job.', $specific) . SmartForm::openTag('br') .
                          $this->l('For more details about how to create a cron job', $specific) . '  ' . $this->l('visit', $specific) . '  <a href="#cron_jobs" class="target-menu badge">' . $this->l('Cron Job', $specific) . '</a>  ' . $this->l('section in this module', $specific),
                'name' => 'cron_job_url',
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'free',
                'name' => 'free',
                'label' => SmartForm::genDesc($this->l('Test mode', $specific), ['h3', 'class="modal-title text-info"']),
                'desc' => SmartForm::openTag('br') .
                    SmartForm::genDesc('', '', 'hr') .
                    $this->l('Options to test the feature before going into production.', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('While the test mode is active all information will be saved on a file.', $specific) .
                    SmartForm::openTag('br') .
                    $this->l(' This file will contain various information abou the execution date and the output what will be generated by the module.') . '.' .
                    SmartForm::genDesc('', '', 'br'),
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'switch',
                'label' => $this->l('Enable Test mode', $specific),
                'name' => 'ed_dd_test_mode',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'select',
                'label' => $this->l('Test mode\'s output', $specific),
                'hint' => $this->l('Choose the desired output for the test. Send them through an Email or save it to File', $specific),
                'name' => 'ed_dd_test_orders_mode',
                'desc' => $this->l('Choose the desire output for the tests.', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('If you choose email, you will have to enter an email to send all the tests (both customer and admin)', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('If you choose File, the test will be saved to a txt file. After the test is genereated you will see a link to check the test results', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $dd_test_results,
                'options' => [
                    'query' => [
                        [
                            'id' => 'email',
                            'name' => $this->l('Email', $specific),
                        ],
                        [
                            'id' => 'file',
                            'name' => $this->l('File', $specific),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'text',
                'label' => $this->l('Orders to test', $specific),
                'name' => 'ed_dd_test_orders',
                'desc' => $this->l('This setting only applies if the test mode is enabled.', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Enter a comma separated list of order ids or references.', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('While in test mode, the orders processed will not get updated. Each time the test is done all the selected orders will be reviewed', $specific),
            ],
            [
                'form_group_class' => 'delayed_delivery_group',
                'type' => 'text',
                'label' => $this->l('Email for tests', $specific),
                'name' => 'ed_dd_test_orders_email',
                'suffix' => SmartForm::genDesc('', ['i', 'icon icon-envelope-o']),
                'desc' => $this->l('Only applicable if you have selected ther Email test mode.', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('All the emails will be replaced for this one, both admin and customer to perform the test.', $specific),
            ],
        ],
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];

$fields_form['product_lists'] = [
    'form' => [
        'legend' => [
        'title' => '6 - ' . $this->l('Estimated Delivery on Product Lists', $specific) . ' (' . $this->l('beta feature', $specific) . ')',
        'icon' => 'icon-warning',
        ],
        'description' => $this->l('Enable this section if you want to display the Estimated Delivery in the product listing.', $specific) .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('The module needs one of the 3 suggested hooks (will depend on you version) to be present on the theme. If it\'s not present, you will need to edit the template and add the missing hook', $specific),
        'input' => [
            [
                'type' => 'switch',
                'label' => $this->l('Enable this Feature', $specific),
                'desc' => $this->l('Enable Estimated delivery on product lists', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    ($this->is_17 ? ($this->l('To use this it\'s a must to have one of this hooks', $specific) .
                          SmartForm::openTag('br') .
                          SmartForm::genDesc('{hook h="displayProductListFunctionalButtons" product=$product}', 'strong') .
                        SmartForm::openTag('br') .
                        SmartForm::genDesc('{hook h="displayEDInProductList" product=$product}', 'strong') .
                        SmartForm::openTag('br') .
                          $this->l('in your product list template', $specific) . ', ' .
                        SmartForm::openTag('br') .
                        SmartForm::openTag('br') .
                        $this->l('The default location for this template is', $specific) .
                          SmartForm::openTag('br') .
                          SmartForm::genDesc('/themes/' . _THEME_NAME_ . '/templates/catalog/_partials/miniatures/product.tpl', 'strong')) :
                        (
                            sprintf($this->l('To use this it\'s a must to have the %s hook on your product list template file', $specific), SmartForm::genDesc('{hook h="displayProductDeliveryTime" product=$product}', 'strong')) .
                          SmartForm::openTag('br') .
                          $this->l('The default location for this template is', $specific) . ' ' .
                          SmartForm::genDesc('/themes/' . _THEME_NAME_ . '/product-list.tpl', 'strong')
                        )),
                'name' => 'ED_LIST',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'select',
                'label' => $this->l('Delivery Key Word', $specific),
                'name' => 'ED_LIST_FORMAT',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Choose the format for the Estimated Delivery in the listings', $specific),
                'desc' => $this->l('Options:', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Deliver', $specific) . ': ' . $this->l('Deliver in ... / Deliver between ... and ...', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Receive', $specific) . ': ' . $this->l('Receive it in ... / Receive it between ... and ...', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Truck Icon', $specific) . ': ' . $this->l('Shortest version possible. Just the truck icon + the delivery date/range', $specific),
                'options' => $list_delivery_keyword,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'select',
                'label' => $this->l('Date format', $specific),
                'name' => 'ED_LIST_DATE_FORMAT',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Choose the format for the Estimated Delivery in the listings', $specific),
                'desc' => $this->l('Options:', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Weekdays (short)', $specific) . ': ' . $this->l('Mon 2, Wed 4...', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Weekdays (long)', $specific) . ': ' . $this->l('Monday 2, Wednesday 4...', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Day + Month (short)', $specific) . ': ' . $this->l('2 Feb, 10 May, 25 Sep...', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Day / Month', $specific) . ': 01/01, 31/12...' .
                    SmartForm::openTag('br') .
                    $this->l('Weekday', $specific) . ': ' . $this->l('Monday, Tuesday, Wednesday...') .
                    SmartForm::openTag('br') .
                    $this->l('Hours format', $specific) . ': ' . $this->l('24-48h', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Days format', $specific) . ': ' . $this->l('1-2 days', $specific),
                'options' => $list_date_format,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'text',
                'label' => $this->l('Maximum days display:', $specific),
                'name' => 'ed_list_max_display',
                'class' => 'input fixed-width-xxl',
                'suffix' => $this->l('days'),
                'hint' => $this->l('set the maximum number of days to display. If the delivery is longer than that it won\'t be displayed.', $specific),
                'desc' => $this->l('set the maximum number of days to display. If the delivery is longer than that it won\'t be displayed.', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Uses the maximum delivery generated to make the calculations', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Set 0 to disable this feature', $specific),
                'options' => $list_date_format,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'select',
                'label' => $this->l('What hook will be used', $specific),
                'name' => 'ED_LIST_LOCATION',
                'class' => 'input fixed-width-xxl',
                'hint' => $this->l('Choose the right hook to display the Estiamted Delviery on the listings', $specific),
                'desc' => $this->l('Depending on your shop version you will have available one or more hooks', $specific) .
                    SmartForm::openTag('br') .
                    $this->l('Since it\'s a hook not mandatory some customized themes doesn\'t have it, but Prestashop does support them', $specific) . '.' .
                    $this->l('Make sure your theme have the hooks in the right file', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('For 1.5 and 1.6 versions:', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc('{hook h="displayProductDeliveryTime" product=$product}', 'strong') .
                    SmartForm::openTag('br') .
                    $this->l('anywhere in your product.tpl file to display the estimated delivery in a custom position', $specific) . '.' .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('For 1.6, 1.7 and 8 versions use one of these two:', $specific) .
                    SmartForm::openTag('br') .
                    SmartForm::genDesc('{hook h="displayProductListFunctionalButtons" product=$product}', 'strong') .
                    SmartForm::openTag('br') .
                    SmartForm::openTag('br') .
                    $this->l('Custom Hook from the module:', $specific) .
                    SmartForm::genDesc('{hook h="displayEDInProductList" product=$product}', 'strong') .
                    SmartForm::openTag('br') .
                    $this->l('If no page builders are used, the hook should be added to:', $specific) .
                    SmartForm::openTag('br') .
                    ' /themes/' . _THEME_NAME_ . '/templates/catalog/_partials/miniatures/product.tpl',
                'options' => $ed_list_placement,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'free',
                'label' => '',
                'desc' => SmartForm::genDesc('', '', 'hr'),

                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'free',
                'label' => $this->l('Placements to enable:', $specific),
                'desc' => $this->l('Enable / Disable this placements to show the ED in the allowed product listings', $specific),
                'name' => 'free',
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'switch',
                'label' => $this->l('Enable on Products list indise Product Page', $specific),
                'desc' => $this->l('Enable this feature on', $specific) . ' ' . $this->l('Products Page', $specific),
                'name' => 'ED_LIST_PROD',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'switch',
                'label' => $this->l('Enable on Home Page', $specific),
                'desc' => $this->l('Enable this feature on', $specific) . ' ' . $this->l('Homepage', $specific),
                'name' => 'ED_LIST_INDEX',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'switch',
                'label' => $this->l('Enable on Searches', $specific),
                'desc' => $this->l('Enable this feature on', $specific) . ' ' . $this->l('Searches', $specific),
                'name' => 'ED_LIST_SEARCH',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'switch',
                'label' => $this->l('Enable on Categories', $specific),
                'desc' => $this->l('Enable this feature on', $specific) . ' ' . $this->l('Categories', $specific),
                'name' => 'ED_LIST_CATEGORY',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'switch',
                'label' => $this->l('Enable on Manufacturers', $specific),
                'desc' => $this->l('Enable this feature on', $specific) . ' ' . $this->l('Manufacturers', $specific),
                'name' => 'ED_LIST_MANUFACTURER',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'switch',
                'label' => $this->l('Enable on New Products', $specific),
                'desc' => $this->l('Enable this feature on', $specific) . ' ' . $this->l('New Products', $specific),
                'name' => 'ED_LIST_NEW-PRODUCTS',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'switch',
                'label' => $this->l('Enable on Prices Drop', $specific),
                'desc' => $this->l('Enable this feature on', $specific) . ' ' . $this->l('Prices Drop', $specific),
                'name' => 'ED_LIST_PRICES-DROP',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'switch',
                'label' => $this->l('Enable on Best Sales', $specific),
                'desc' => $this->l('Enable this feature on', $specific) . ' ' . $this->l('Best Sales', $specific),
                'name' => 'ED_LIST_BEST-SALES',
                'bool' => true,
                'values' => $switch_options,
            ],
            [
                'form_group_class' => 'ed_list_options',
                'type' => 'text',
                'label' => $this->l('Additional Controllers', $specific),
                'desc' => $this->l('A comma-separated list of the custom controllers you want to display the module in.') . '.' .
                    $this->l('You will need to know the controller name you want to add', $specific),
                'name' => 'ED_LIST_EXTRA_CONTROLLERS',
                'bool' => true,
                'values' => $switch_options,
            ],

            /* Disabled since now there is a full control on the Estimated Delivery
            array(
                'type' => 'switch',
                'label' => $this->l('Enable on Checkout', $specific),
                'desc' => $this->l('Enable this feature on').' '.$this->l('Checkout', $specific).
                SmartForm::genDesc('', '', 'br')
                .
                          $this->l('Calculated for every product in the cart', $specific),
                'name' => 'ED_LIST_CART',
                'bool' => true,
                'values' => $switch_options,
            ),*/
        ],
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];

$fields_form['no_delivery_days'] = [
    'form' => [
        'legend' => [
            'title' => '7 - ' . $this->l('No delivery days or picking - include here your vacations, local holidays, national holidays...', $specific),
            'icon' => 'icon-calendar',
        ],
        'description' => $this->l('When there is an occasional day when you don\'t prepare orders, or your carriers don\'t ship them, due to a local holidays, a national holidays or because you\'re not open to the public you can set it here and the module will skip that day for the calculations.', $specific) .
            SmartForm::openTag('br') .
            $this->l('If it\'s a holiday that repeats each year, you can set the holiday as "repeatable", then you will only need to set it once and every year will be taken into account', $specific),
        'input' => [
            [
                'type' => 'text',
                'label' => $this->l('Reference:', $specific),
                'desc' => $this->l('Enter a Name for this holiday period:', $specific),
                'hint' => $this->l('For example local holidays, labor day, vacations...', $specific),
                'name' => 'holiday_name',
                'size' => 10,
            ],
            [
                'type' => 'date',
                'label' => $this->l('Start Date', $specific),
                'name' => 'holiday_start',
                'size' => 10,
            ],
            [
                'type' => 'date',
                'label' => $this->l('End Date', $specific),
                'name' => 'holiday_end',
                'size' => 10,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Holiday Repeatable', $specific),
                'desc' => $this->l('Enable this setting to configure the holiday repeatable', $specific),
                'name' => 'ED_HOLIDAY_REPEATABLE',
                'bool' => true,
                'values' => $switch_options,
            ],
        ],
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];

// Conditionally add shop checkbox list based on multishop feature
if (Shop::isFeatureActive()) {
    $shop_options = [];
    $shop_options[] = [
        'type' => 'free',
        'label' => '',
        'desc' => SmartForm::openTag('hr') .
            SmartForm::genDesc($this->l('Choose the shops to associate with this holiday', $specific), ['h3', 'class="modal-title text-info"']),
        'name' => 'free',
    ];
    foreach (Shop::getShops() as $shop) {
        $shop_options[] = [
            'type' => 'switch',
            'label' => $shop['name'],
            'name' => 'holiday_id_shop_' . $shop['id_shop'],  // Array notation for handling multiple selections
            'bool' => true,
            'values' => $switch_options,
            'default_value' => false,
        ];
    }

    $fields_form['no_delivery_days']['form']['input'] = array_merge(
        $fields_form['no_delivery_days']['form']['input'],
        $shop_options
    );
} else {
    $fields_form['no_delivery_days']['form']['input'][] = [
        'type' => 'hidden',
        'name' => 'holiday_id_shop',
        'default_value' => (int) Context::getContext()->shop->id,
    ];
}

$adv_form_inputs = [
    [
        'type' => 'switch',
        'label' => $this->l('Enable advanced options', $specific),
        'desc' => $this->l('Please do not activate this unless you know what you are doing', $specific) . $this->l('This settings are for debugging purposes and to fine tune the module') . '. ' . $this->l('If you need support just contact us', $specific),
        'name' => 'ed_adv_mode',
        'bool' => true,
        'values' => $switch_options],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Force Country Search', $specific),
        'desc' => $this->l('Enable this option if you don\'t see the Estimated Delivery data if the user is not logged in.', $specific) .
            SmartForm::openTag('br') .
            $this->l('This is caused by the carrier\'s configuration', $specific) .
            SmartForm::openTag('br') .
            $this->l('Usually, when there are no states configured', $specific),
        'name' => 'ED_FORCE_COUNTRY',
        'bool' => true,
        'values' => $switch_options],
    [
        'type' => 'free',
        'label' => SmartForm::genDesc($this->l('Product-related Settings', $specific), ['h3', 'class="modal-title text-info"']),
        'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr'),
        'name' => 'free',
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Process packs as single product', $specific),
        'desc' => $this->l('Disabled by default', $specific) .
            SmartForm::openTag('br') .
            $this->l('Enable this setting if you want the Estimated Delivery to treat the packs as a independent product.', $specific) .
            SmartForm::openTag('br') .
            $this->l('If disabled, the module will calculate the Estimated Delivery from all products in the pack.', $specific),
        'name' => 'ED_PACK_AS_PRODUCT',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Disable Product Carriers Check', $specific),
        'desc' => $this->l('Enable this setting to ignore the carriers assigned to the product', $specific) . '. ' .
            SmartForm::openTag('br') .
            $this->l('This will make all available carriers to be displayed', $specific) . '.',
        'name' => 'ED_DISABLE_PRODUCT_CARRIERS',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Disable Common Carriers Check', $specific),
        'desc' => $this->l('Enable this option to generate all available carrier IDs instead of only those common to all products', $specific) . '. ' .
            SmartForm::openTag('br') .
            $this->l('This setting is useful when the module is unable to accurately detect all carrier IDs, resulting in fewer carriers being displayed.', $specific),
        'name' => 'ED_DIS_COMMON_CARRIER_INTERSECTION',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Enable multiple instances on Product Page', $specific),
        'desc' => SmartForm::genDesc($this->l('Only available for PS +1.7 versions', $specific), 'strong') .
            SmartForm::openTag('br') .
            $this->l('Enable this setting to ignore the limitation to display one instance of the Estimated Delivery in the product page', $specific) . '. ' .
            SmartForm::openTag('br') .
            $this->l('This may be needed when using some page builders, like Creative Elements', $specific) . '.',
        'name' => 'ED_ALLOW_MULTIPLE_INSTANCES',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'col' => 7,
        'type' => 'text',
        'class' => 'fixed-width-l',
        'label' => $this->l('Combination change delay:', $specific),
        'desc' => $this->l('If the message does not appear from time to time when the combination changes it means the module is faster than th HTML update. In those cases an additional delay (milliseconds) will be needed. Check your site trying some configurations until you find the right value for your site', $specific) . '.' .
            SmartForm::openTag('br') .
            sprintf($this->l('Examples: %s', $specific), '100, 200, 400, 500, 800...'),
        'hint' => $this->l('Set the delay in milliseconds (1000ms >> 1s)', $specific),
        'name' => 'ed_refresh_delay',
        'size' => 4,
        'suffix' => $this->l('milliseconds'),
    ],
    [
        'col' => 7,
        'type' => 'text',
        'class' => 'fixed-width-l',
        'label' => $this->l('Ajax detection delay:', $specific),
        'desc' => $this->l('The module should listen to the ajax events on the product page to detect combination and quantity changes. If the module does not trigger any change an additional timeout may be necessary', $specific) . '.' .
            SmartForm::openTag('br') .
            sprintf($this->l('Examples: %s', $specific), '100, 200, 400, 500, 800...'),
        'hint' => $this->l('Set the delay in milliseconds (1000ms >> 1s)', $specific),
        'name' => 'ed_ajax_delay',
        'size' => 4,
        'suffix' => $this->l('milliseconds'),
    ],
    [
        'type' => 'free',
        'label' => SmartForm::genDesc($this->l('Disabling options', $specific), ['h3', 'class="modal-title text-info"']),
        'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr'),
        'name' => 'free',
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Disable Geolocation', $specific),
        'desc' => $this->l('Disable the geolocation', $specific) .
            SmartForm::openTag('br') .
            $this->l('If the geolocation is disabled, the module will use the default country to calculate the Estimated dates unless the user log in.', $specific),
        'name' => 'ED_DISABLE_GEOLOCATION',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Disable special keywords "Today" / "Tomorrow"', $specific),
        'desc' => $this->l('Enable this option if you want to use the delivery date instead of the special words "Today" and "Tomorrow" for near delivery dates', $specific),
        'name' => 'ED_USE_TOT',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Disable Font Awesome Loading', $specific),
        'desc' => $this->l('If your theme already has the Font Awesome library you can disable this option to make the module even faster and save resources too.', $specific),
        'name' => 'ed_disable_font_awesome',
        'bool' => true,
        'values' => $switch_options],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Disable Carrier Restrictions', $specific),
        'desc' => $this->l('Disable the carrier restriction feature', $specific) .
            SmartForm::openTag('br') .
            $this->l('Carrier restriction is based on price or weight, if your theme supports adding to cart multiple combinations at once you may need to enable this setting', $specific) . '.',
        'name' => 'ED_DIS_REST',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Disable ED After Shipping', $specific),
        'desc' => $this->l('Do not display the ED information in the order detail page after an order is "Shipped"', $specific),
        'name' => 'ED_DISABLE_AFTER_SHIPPING',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Disable ED for OOS products', $specific),
        'desc' => $this->l('By default the Estimated Delivery module shows the date of delivery for the OOS products with sales enabled', $specific) .
            SmartForm::openTag('br') .
            $this->l('If you activate this options the ED won\'t be shown in those cases.', $specific),
        'name' => 'ED_DISABLE_OOS',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Disable Carrier Allowed Groups Check', $specific),
        'desc' => $this->l('By default the Estimated Delivery module will check if a carrier is allowed for a particular group. Enable this option to disable the check and accept all the carriers', $specific) .
            SmartForm::openTag('br') .
            $this->l('This can be useful if you have hidden carriers configured that you assign directly through other means than the Front Office', $specific),
        'name' => 'ED_ALLOW_EMPTY_CARRIER_GROUPS',
        'bool' => true,
        'values' => $switch_options,
    ],

    [
        'type' => 'free',
        'label' => SmartForm::genDesc($this->l('Direct stock check for combinations', $specific), ['h3', 'class="modal-title text-info"']),
        'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr'),
        'name' => 'free',
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Get Combinations quantity directly from database', $specific),
        'desc' => $this->l('Enable this setting to ignore default procedures of the module and use a direct query instead', $specific) . '. ' .
            SmartForm::openTag('br') .
            $this->l('Useful if you have some kind of override that alters the stock and you want to work with the one stored on the database, like a software that adds the virtual stock (provider) to the shop stock', $specific) . '.',
        'name' => 'ED_GET_QUANTITY_FROM_DATABASE',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Use pseudo OOS check', $specific),
        'desc' => $this->l('Option linked to the previous one', $specific) . '. ' .
            SmartForm::openTag('br') .
            $this->l('Activate it to compare the original value in case quantity is lower to the wanted quantity. This will allow a pseudo OOS trigger from the module\'e when the original quantity is in stock and the database quantity is out of stock', $specific) . '.' .
            SmartForm::openTag('br') .
            $this->l('If the quantity wanted is greater than the available quantity it will perform a check with the quantity returned from the default methods, if with that quantity the sale is possible the module will treat it as a OOS case', $specific),
        'name' => 'ED_SET_CAN_OOS_IF_ORIGINAL_IS_POSITIVE',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'type' => 'free',
        'label' => SmartForm::genDesc($this->l('AMP compatibility', $specific), ['h3', 'class="modal-title text-info"']),
        'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr'),
        'name' => 'free',
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Add AMP JS', $specific) . ' (' . $this->l('beta feature') . ')',
        'desc' => $this->l('Disable the carrier restriction feature', $specific) .
            SmartForm::openTag('br') .
            $this->l('Carrier restriction is based on price or weight, if your theme supports adding to cart multiple combinations at once you may need to enable this setting', $specific) . '.',
        'name' => 'ED_AMP',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'type' => 'free',
        'label' => SmartForm::genDesc($this->l('Debug the Estimated Delivery generation', $specific), ['h3', 'class="modal-title text-info"']),
        'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr'),
        'name' => 'free',
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Debug Vars', $specific),
        'desc' => $this->l('Show debugging vars', $specific),
        'name' => 'ed_debug_var',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'label' => $this->l('IP for Debugging Variables', $specific),
        'desc' => $this->l('Limit the debbuging information to one IP', $specific) . ', ' .
            $this->l('leave it blank to debug for all IPs', $specific) . ' (' . $this->l('not recommended', $specific) . ')',
        'name' => 'ed_debug_var_ip',
        'size' => 15,
        'col' => 4,
        'type' => 'textbutton',
        'validation' => 'isGenericName',
        'button' => [
            'attributes' => [
                'class' => 'btn btn-outline-primary add_ip_button',
                'onclick' => 'addRemoteAddr(\'ed_debug_var_ip\');',
            ],
            'label' => $this->l('Add my IP'),
            'icon' => 'plus',
        ],
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Force Print for debugging info', $specific),
        'desc' => $this->l('Set yes to directly output the content in the page, leave it to no to generate the information inside the page comments', $specific),
        'name' => 'ed_debug_force_print',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Debug Generation Times', $specific),
        'desc' => $this->l('Save the generation times to see which sections consumes more time', $specific),
        'name' => 'ed_debug_time',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'type' => 'free',
        'label' => SmartForm::genDesc($this->l('ED Geolocation Tests', $specific), ['h3', 'class="modal-title text-info"']),
        'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr') .
            SmartForm::genDesc($this->l('Use this settings to simulate the results for the Estimated Delivery in other countries'), [['p', 'class="help-block"']]),
        'name' => 'free',
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'text',
        'label' => $this->l('Force IP', $specific),
        'desc' => $this->l('Force a ip for the entire module', $specific) . ', ' . $this->l('leave it blank to deactivate this feature', $specific),
        'name' => 'ed_force_ip',
        'size' => 10,
        'col' => 4,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Force Date', $specific),
        'desc' => $this->l('Only for delivery testing purposes, this will force a fixed date in the module', $specific),
        'name' => 'ED_FORCE_DATE',
        'bool' => true,
        'values' => $switch_options,
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'datetime',
        'label' => $this->l('Forced Date', $specific),
        'desc' => $this->l('The date that will be used in the ED for the tests', $specific),
        'name' => 'ED_FORCED_DATE',
        'size' => 10,
    ],
    [
        'type' => 'free',
        'label' => SmartForm::genDesc($this->l('Other Misc Settings', $specific), ['h3', 'class="modal-title text-info"']),
        'desc' => SmartForm::openTag('br') . SmartForm::openTag('hr'),
        'name' => 'free',
    ],
    [
        'form_group_class' => 'ed_advanced_options',
        'type' => 'switch',
        'label' => $this->l('Override Locales', $specific),
        'desc' => $this->l('Override the automatically detected locales with the choosen ones', $specific),
        'name' => 'ed_force_locale',
        'bool' => true,
        'values' => $switch_options,
    ],
];

// Prepare the locales override system
$langs = Language::getLanguages();
// Get server defined locales
$locales_options = [['id' => 0, 'name' => ' --- ']];
if (class_exists('ResourceBundle')) {
    $server_locales = ResourceBundle::getLocales('');
    foreach ($server_locales as $sl) {
        $locales_options[] = [
            'id' => $sl,
            'name' => $sl,
        ];
    }
}
foreach ($langs as $lang) {
    if (count($locales_options) > 1) {
        $adv_form_inputs[] = [
            'form_group_class' => 'ed_advanced_options',
            'type' => 'select',
            'label' => $this->l('Locale for', $specific) . ': ' . $lang['name'],
            'name' => 'ed_locale_' . $lang['id_lang'],
            'options' => [
                'query' => $locales_options,
                'id' => 'id',
                'name' => 'name',
            ],
        ];
    } else {
        // If resourceBundle did not work
        $adv_form_inputs[] = [
            'form_group_class' => 'ed_advanced_options',
            'type' => 'text',
            'label' => $this->l('Locale for', $specific) . ': ' . $lang['name'],
            'desc' => sprintf($this->l('Manually enter a locale for %s dates', $specific), $lang['name']),
            'name' => 'ed_locale_' . $lang['id_lang'],
            'size' => 10,
            'col' => 4,
        ];
    }
}
// Field for special encoding
$adv_form_inputs[] = [
    'form_group_class' => 'ed_advanced_options',
    'type' => 'text',
    'label' => $this->l('Encoding'),
    'desc' => $this->l('Write here a special iso encoding if your language needs it, elsewhere leave it empty', $specific),
    'name' => 'ED_SPECIAL_ENCODING',
    'size' => 10,
    'col' => 4,
];
$time_zone_options = [
    [
        'id' => '',
        'name' => $this->l('Default Timezone'),
    ],
];
foreach (DateTimeZone::listIdentifiers() as $timezone) {
    $time_zone_options[] = [
        'id' => $timezone,
        'name' => $timezone,
    ];
}

$adv_form_inputs[] = [
    'form_group_class' => 'ed_advanced_options',
    'type' => 'select',
    'label' => $this->l('Locale for', $specific) . ': ' . $lang['name'],
    'desc' => $this->l('Write here a special iso encoding if your language needs it, elsewhere leave it empty', $specific),
    'name' => 'ED_DEFAULT_TIMEZONE',
    'options' => [
        'query' => $time_zone_options,
        'id' => 'id',
        'name' => 'name',
    ],
];
// Review updates procedure
$adv_form_inputs[] = [
    'form_group_class' => 'ed_advanced_options',
    'type' => 'free',
    'label' => $this->l('Review Updates', $specific),
    'desc' => SmartForm::openTag('button', 'class="btn btn-default" id="review_ed_db" name="review_ed_db" type="submit"') .
        SmartForm::genDesc('', ['i', 'class="icon icon-search"']) .
        $this->l('Review ED DataBase', $specific) .
        SmartForm::closeTag('button') .
        SmartForm::openTag('br') .
        $this->l('Click here to run a check on the ED databases to ensure all updates has been applied', $specific),
    'name' => 'free',
    'id' => $this->name . 'checkupdates',
];

$fields_form['advanced_options'] = [
    'form' => [
        'legend' => [
        'title' => $this->l('Debuging', $specific) . ' & ' . $this->l('Advanced options', $specific),
        'icon' => 'icon-cogs',
        ],
        'description' => $this->l('The module has plenty of options everywhere, but what if you even need something more particular or you need to debug the output or test the module display for another country? Here you will find a complete set of options to personalize even more the module.', $specific) .
            SmartForm::openTag('br') .
            $this->l('Each set of options has been grouped to allow an easier understanding of what are they related to', $specific) .
            SmartForm::openTag('br') .
            $this->l('In most of the cases, none of a few options are needed so don\'t worry if you feel like you don\'t need any at all', $specific),
        'input' => $adv_form_inputs,
        'submit' => [
            'title' => $this->l('Save', $specific),
        ],
    ],
];

$fields_form['hooks'] = [
    'form' => [
        'legend' => [
            'title' => $this->l('Hooks Management', $specific),
            'icon' => 'icon-link',
        ],
        'description' => $this->l('A fast and easy way to manage the module hooks. Enable or disable the hooks with just one click', $specific) .
            SmartForm::openTag('br') .
            SmartForm::openTag('br') .
            $this->l('It\'s recommendable to have all hooks enabled, as the module controls their usage throughout the configurable fields and options', $specific),
        'input' => [
            [
                'type' => 'free',
                'label' => sprintf($this->l('%s Hooks'), $this->displayName),
                'name' => 'free',
                'col' => 9,
                'desc' => $this->renderHooksManagementSection(),
            ],
        ],
    ],
];
// Retrocompatibility for Switch in Prestashop 1.5
if (version_compare(_PS_VERSION_, '1.6', '<')) {
    foreach ($fields_form as &$form) {
        foreach ($form['form']['input'] as &$input) {
            if ($input['type'] == 'switch') {
                $input['type'] = 'radio';
            }
        }
    }
}
