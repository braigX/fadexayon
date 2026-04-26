<?php

namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

abstract class Widget_Payment_Button extends Widget_Base
{
    //use Base_Widget_Trait;

    // Payment types.
    const PAYMENT_TYPE_CHECKOUT = 'checkout';
    const PAYMENT_TYPE_SUBSCRIPTION = 'subscription';
    const PAYMENT_TYPE_DONATION = 'donation';

    // Billing cycles.
    const BILLING_CYCLE_DAYS = 'days';
    const BILLING_CYCLE_WEEKS = 'weeks';
    const BILLING_CYCLE_MONTHS = 'months';
    const BILLING_CYCLE_YEARS = 'years';

    // Donation types.
    const DONATION_TYPE_ANY = 'any';
    const DONATION_TYPE_FIXED = 'fixed';

    // Error messages.
    const ERROR_MESSAGE_GLOBAL = 'global';
    const ERROR_MESSAGE_PAYMENT_METHOD = 'payment';

    // Retrieve the merchant display name.
    abstract protected function get_merchant_name();

    // Account details section.
    abstract protected function register_account_section();

    // Custom sandbox controls.
    abstract protected function register_sandbox_controls();

    public function get_group_name()
    {
        return 'payments';
    }

    // Render custom controls after product type.
    protected function after_product_type()
    {
    }

    // Return an array of supported currencies.
    protected function get_currencies()
    {
        return [
            'AUD' => _x('AUD', 'Currency', 'elementor'),
            'CAD' => _x('CAD', 'Currency', 'elementor'),
            'CZK' => _x('CZK', 'Currency', 'elementor'),
            'DKK' => _x('DKK', 'Currency', 'elementor'),
            'EUR' => _x('EUR', 'Currency', 'elementor'),
            'HKD' => _x('HKD', 'Currency', 'elementor'),
            'HUF' => _x('HUF', 'Currency', 'elementor'),
            'ILS' => _x('ILS', 'Currency', 'elementor'),
            'JPY' => _x('JPY', 'Currency', 'elementor'),
            'MXN' => _x('MXN', 'Currency', 'elementor'),
            'NOK' => _x('NOK', 'Currency', 'elementor'),
            'NZD' => _x('NZD', 'Currency', 'elementor'),
            'PHP' => _x('PHP', 'Currency', 'elementor'),
            'PLN' => _x('PLN', 'Currency', 'elementor'),
            'GBP' => _x('GBP', 'Currency', 'elementor'),
            'RUB' => _x('RUB', 'Currency', 'elementor'),
            'SGD' => _x('SGD', 'Currency', 'elementor'),
            'SEK' => _x('SEK', 'Currency', 'elementor'),
            'CHF' => _x('CHF', 'Currency', 'elementor'),
            'TWD' => _x('TWD', 'Currency', 'elementor'),
            'THB' => _x('THB', 'Currency', 'elementor'),
            'TRY' => _x('TRY', 'Currency', 'elementor'),
            'USD' => _x('USD', 'Currency', 'elementor'),
        ];
    }

    // Return an array of default error messages.
    protected function get_default_error_messages()
    {
        return [
            self::ERROR_MESSAGE_GLOBAL => PrestaHelper::__('An error occurred.', 'elementor'),
            self::ERROR_MESSAGE_PAYMENT_METHOD => PrestaHelper::__('No payment method connected. Contact seller.', 'elementor'),
        ];
    }

    // Get message text by id (`error_message_$id`).
    protected function get_custom_message($id)
    {
        $message = $this->get_settings_for_display('error_message_' . $id);

        // Return the user-defined message.
        if (!empty($message)) {
            return $message;
        }

        // Return the default message.
        $error_messages = $this->get_default_error_messages();

        return (!empty($error_messages[$id])) ? $error_messages[$id] : PrestaHelper::__('Unknown error.', 'elementor');
    }

    // Product details section.
    protected function register_product_controls()
    {
        $this->add_control(
            'type',
            [
                'label' => PrestaHelper::__('Transaction Type', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'checkout',
                'options' => [
                    self::PAYMENT_TYPE_CHECKOUT => PrestaHelper::__('Checkout', 'elementor'),
                    self::PAYMENT_TYPE_DONATION => PrestaHelper::__('Donation', 'elementor'),
                    self::PAYMENT_TYPE_SUBSCRIPTION => PrestaHelper::__('Subscription', 'elementor'),
                ],
                'separator' => 'before',
            ]
        );

        $this->after_product_type();

        $this->add_control(
            'product_name',
            [
                'label' => PrestaHelper::__('Item Name', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'product_sku',
            [
                'label' => PrestaHelper::__('SKU', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'product_price',
            [
                'label' => PrestaHelper::__('Price', 'elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '0.00',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'type!' => self::PAYMENT_TYPE_DONATION,
                ],
            ]
        );

        $this->add_control(
            'donation_type',
            [
                'label' => PrestaHelper::__('Donation Amount', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => self::DONATION_TYPE_FIXED,
                'options' => [
                    self::DONATION_TYPE_ANY => PrestaHelper::__('Any Amount', 'elementor'),
                    self::DONATION_TYPE_FIXED => PrestaHelper::__('Fixed', 'elementor'),
                ],
                'condition' => [
                    'type' => self::PAYMENT_TYPE_DONATION,
                ],
            ]
        );

        $this->add_control(
            'donation_amount',
            [
                'label' => PrestaHelper::__('Amount', 'elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '1',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'type' => self::PAYMENT_TYPE_DONATION,
                    'donation_type' => self::DONATION_TYPE_FIXED,
                ],
            ]
        );

        $this->add_control(
            'currency',
            [
                'label' => PrestaHelper::__('Currency', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'USD',
                'options' => $this->get_currencies(),
            ]
        );

        $this->add_control(
            'billing_cycle',
            [
                'label' => PrestaHelper::__('Billing Cycle', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => self::BILLING_CYCLE_MONTHS,
                'options' => [
                    self::BILLING_CYCLE_DAYS => PrestaHelper::__('Daily', 'elementor'),
                    self::BILLING_CYCLE_WEEKS => PrestaHelper::__('Weekly', 'elementor'),
                    self::BILLING_CYCLE_MONTHS => PrestaHelper::__('Monthly', 'elementor'),
                    self::BILLING_CYCLE_YEARS => PrestaHelper::__('Yearly', 'elementor'),
                ],
                'condition' => [
                    'type' => self::PAYMENT_TYPE_SUBSCRIPTION,
                ],
            ]
        );

        $this->add_control(
            'auto_renewal',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => PrestaHelper::__('Auto Renewal', 'elementor'),
                'default' => 'yes',
                'label_off' => PrestaHelper::__('Off', 'elementor'),
                'label_on' => PrestaHelper::__('On', 'elementor'),
                'condition' => [
                    'type' => self::PAYMENT_TYPE_SUBSCRIPTION,
                ],
            ]
        );

        $this->add_control(
            'quantity',
            [
                'label' => PrestaHelper::__('Quantity', 'elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
                'condition' => [
                    'type' => self::PAYMENT_TYPE_CHECKOUT,
                ],
            ]
        );

        $this->add_control(
            'shipping_price',
            [
                'label' => PrestaHelper::__('Shipping Price', 'elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'type' => self::PAYMENT_TYPE_CHECKOUT,
                ],
            ]
        );

        $this->add_control(
            'tax_type',
            [
                'label' => PrestaHelper::__('Tax', 'elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => PrestaHelper::__('None', 'elementor'),
                    'percentage' => PrestaHelper::__('Percentage', 'elementor'),
                ],
                'condition' => [
                    'type' => self::PAYMENT_TYPE_CHECKOUT,
                ],
            ]
        );

        $this->add_control(
            'tax_rate',
            [
                'label' => PrestaHelper::__('Tax Percentage', 'elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => '0',
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'type' => self::PAYMENT_TYPE_CHECKOUT,
                    'tax_type' => 'percentage',
                ],
            ]
        );
    }

    // Submission settings section.
    protected function register_settings_section()
    {
        $this->start_controls_section(
            'section_settings',
            [
                'label' => PrestaHelper::__('Additional Options', 'elementor'),
            ]
        );

        $this->add_control(
            'redirect_after_success',
            [
                'label' => PrestaHelper::__('Redirect After Success', 'elementor'),
                'type' => Controls_Manager::URL,
                'options' => false,
                'placeholder' => PrestaHelper::__('Paste URL or type', 'elementor'),
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
                'render_type' => 'none',
            ]
        );

        $this->add_control(
            'sandbox_mode',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => PrestaHelper::__('Sandbox', 'elementor'),
                'default' => 'no',
                'label_off' => PrestaHelper::__('Off', 'elementor'),
                'label_on' => PrestaHelper::__('On', 'elementor'),
            ]
        );

        $this->register_sandbox_controls();

        $this->add_control(
            'open_in_new_window',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => sprintf(PrestaHelper::__('Open %s In New Tab', 'elementor'), $this->get_merchant_name()),
                'default' => 'yes',
                'label_off' => PrestaHelper::__('No', 'elementor'),
                'label_on' => PrestaHelper::__('Yes', 'elementor'),
            ]
        );

        $this->add_control(
            'custom_messages',
            [
                'label' => PrestaHelper::__('Custom Messages', 'elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $error_messages = $this->get_default_error_messages();

        $this->add_control(
            'error_message_' . self::ERROR_MESSAGE_GLOBAL,
            [
                'label' => PrestaHelper::__('Error Message', 'elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => $error_messages[self::ERROR_MESSAGE_GLOBAL],
                'placeholder' => $error_messages[self::ERROR_MESSAGE_GLOBAL],
                'label_block' => true,
                'condition' => [
                    'custom_messages!' => '',
                ],
            ]
        );

        $this->add_control(
            'error_message_' . self::ERROR_MESSAGE_PAYMENT_METHOD,
            [
                'label' => sprintf(PrestaHelper::__('%s Not Connected', 'elementor'), $this->get_merchant_name()),
                'type' => Controls_Manager::TEXT,
                'default' => $error_messages[self::ERROR_MESSAGE_PAYMENT_METHOD],
                'placeholder' => $error_messages[self::ERROR_MESSAGE_PAYMENT_METHOD],
                'label_block' => true,
                'condition' => [
                    'custom_messages!' => '',
                ],
            ]
        );

        $this->end_controls_section();
    }

    // Customize the default button controls.
    protected function register_button_controls()
    {
        parent::register_controls();

        $this->remove_control('button_type');

        $this->remove_control('link');

        $this->remove_control('size');

        $this->update_control('selected_icon', [
            'default' => [
                'value' => 'fab fa-paypal',
                'library' => 'fa-brands',
            ],
        ]);

        $this->update_control('text', [
            'default' => 'Buy Now',
        ]);

        $this->update_control('button_text_color', [
            'default' => '#FFF',
        ]);

        $this->update_control('background_color', [
            'default' => '#032E82',
        ]);
    }

    // Add typography settings for custom messages.
    protected function register_messages_style_section()
    {
        $this->start_controls_section(
            'section_messages_style',
            [
                'label' => PrestaHelper::__('Messages', 'elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'message_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .elementor-message',
            ]
        );

        $this->add_control(
            'message_color_' . self::ERROR_MESSAGE_GLOBAL,
            [
                'label' => PrestaHelper::__('Error Message Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-message.elementor-error-message-' . self::ERROR_MESSAGE_GLOBAL => 'color: {{COLOR}};',
                ],
            ]
        );

        $this->add_control(
            'message_color_' . self::ERROR_MESSAGE_PAYMENT_METHOD,
            [
                'label' => sprintf(PrestaHelper::__('%s Not Connected Color', 'elementor'), $this->get_merchant_name()),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-message.elementor-error-message-' . self::ERROR_MESSAGE_PAYMENT_METHOD => 'color: {{COLOR}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    // Register widget controls.
    protected function _register_controls()
    {
        $this->register_account_section();
        $this->register_button_controls();
        $this->register_settings_section();
        $this->register_messages_style_section();
    }

    // Render the checkout button.
    protected function render_button($tag = 'a')
    {
        $this->add_render_attribute('button', 'class', 'elementor-payment-button');

?>
        <<?php Utils::print_validated_html_tag($tag); ?> <?php $this->print_render_attribute_string('button'); ?>>
            <?php $this->render_text(); ?>
        </<?php Utils::print_validated_html_tag($tag); ?>>
    <?php
    }

    // Render the widget.
    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('wrapper', 'class', 'elementor-button-wrapper');
        $this->add_render_attribute('button', 'class', 'elementor-button');
        $this->add_render_attribute('button', 'role', 'button');

        if (!empty($settings['button_css_id'])) {
            $this->add_render_attribute('button', 'id', $settings['button_css_id']);
        }

        if (!empty($settings['size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['size']);
        }

        if ($settings['hover_animation']) {
            $this->add_render_attribute('button', 'class', 'elementor-animation-' . $settings['hover_animation']);
        }

    ?>
        <div <?php $this->print_render_attribute_string('wrapper'); ?>>
            <?php $this->render_button(); ?>
        </div>
    <?php
    }

    protected function content_template()
    {
        return;
    ?>
        <# view.addRenderAttribute( 'text' , 'class' , 'elementor-button-text' ); view.addInlineEditingAttributes( 'text' , 'none' ); var iconHTML=elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden' : true }, 'i' , 'object' ), migrated=elementor.helpers.isIconMigrated( settings, 'selected_icon' ); #>
            <div class="elementor-button-wrapper">
                <a id="{{ settings.button_css_id }}" class="elementor-button elementor-size-{{ settings.size }} elementor-animation-{{ settings.hover_animation }}" href="#" role="button">
                    <span class="elementor-button-content-wrapper">
                        <# if ( settings.icon || settings.selected_icon ) { #>
                            <span class="elementor-button-icon elementor-align-icon-{{ settings.icon_align }}">
                                <# if ( ( migrated || ! settings.icon ) && iconHTML.rendered ) { #>
                                    {{{ iconHTML.value }}}
                                    <# } else { #>
                                        <i class="{{ settings.icon }}" aria-hidden="true"></i>
                                        <# } #>
                            </span>
                            <# } #>
                                <span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</span>
                    </span>
                </a>
            </div>
    <?php
    }

    // Check if it's sandbox mode.
    protected function is_sandbox()
    {
        return 'yes' === $this->get_settings_for_display('sandbox_mode');
    }
}
