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

if (!defined('_PS_VERSION_')) {
    exit;
}

class EtsRVDefines extends EtsRVCore
{
    static $_INSTANCE;

    public static function getInstance()
    {
        if (!self::$_INSTANCE) {
            self::$_INSTANCE = new  EtsRVDefines();
        }

        return self::$_INSTANCE;
    }

    static $quickTabs;

    public function getSortBy($option = null)
    {
        $options = array(
            'date_add.desc' => array(
                'id' => 'latest',
                'name' => $this->l('Latest', 'EtsRVDefines'),
                'value' => 'date_add.desc',
            ),
            'date_add.asc' => array(
                'id' => 'oldest',
                'name' => $this->l('Oldest', 'EtsRVDefines'),
                'value' => 'date_add.asc',
            ),
            'grade.desc' => array(
                'id' => 'high_rating',
                'name' => $this->l('High rating', 'EtsRVDefines'),
                'value' => 'grade.desc',
            ),
            'grade.asc' => array(
                'id' => 'low_rating',
                'name' => $this->l('Low rating', 'EtsRVDefines'),
                'value' => 'grade.asc',
            ),
            'usefulness.desc' => array(
                'id' => 'helpful',
                'name' => $this->l('Helpful', 'EtsRVDefines'),
                'value' => 'usefulness.desc',
            ),
        );
        if ($option !== null)
            return isset($options[$option]) ? $options[$option] : [];
        return $options;
    }

    public function getSortByQuestion($option = null)
    {
        $options = array(
            'date_add.desc' => array(
                'id' => 'latest',
                'name' => $this->l('Latest', 'EtsRVDefines'),
                'value' => 'date_add.desc',
            ),
            'date_add.asc' => array(
                'id' => 'oldest',
                'name' => $this->l('Oldest', 'EtsRVDefines'),
                'value' => 'date_add.asc',
            ),
            'usefulness.desc' => array(
                'id' => 'helpful',
                'name' => $this->l('Helpful', 'EtsRVDefines'),
                'value' => 'usefulness.desc',
            ),
        );
        if ($option !== null && isset($options[$option]))
            return $options[$option];
        return $options;
    }

    public function getQuickTabs()
    {
        if (!self::$quickTabs) {
            self::$quickTabs = array(
                'reviews' => array(
                    'label' => $this->l('Reviews & Ratings', 'EtsRVDefines'),
                    'origin' => 'Reviews & Ratings',
                    'icon' => 'reviews',
                    'class' => 'Reviews',
                    'sub' => array(
                        'reviews' => array(
                            'label' => $this->l('Reviews & Ratings', 'EtsRVDefines'),
                            'origin' => 'Reviews & Ratings',
                            'icon' => 'reviews',
                            'class' => 'ReviewsRatings',
                        ),
                        'comments' => array(
                            'label' => $this->l('Comments', 'EtsRVDefines'),
                            'origin' => 'Comments',
                            'icon' => 'comments',
                            'class' => 'Comments',
                        ),
                        'replies' => array(
                            'label' => $this->l('Replies', 'EtsRVDefines'),
                            'origin' => 'Replies',
                            'icon' => 'replies',
                            'class' => 'Replies',
                        )
                    )
                ),
                'questions' => array(
                    'label' => $this->l('Questions & Answers', 'EtsRVDefines'),
                    'origin' => 'Questions & Answers',
                    'icon' => 'questions',
                    'class' => 'QuestionsAnswers',
                    'sub' => array(
                        'questions' => array(
                            'label' => $this->l('Questions', 'EtsRVDefines'),
                            'origin' => 'Questions',
                            'icon' => 'questions',
                            'class' => 'Questions',
                        ),
                        'question_comments' => array(
                            'label' => $this->l('Comments for question', 'EtsRVDefines'),
                            'origin' => 'Question Comments',
                            'icon' => 'question-comments',
                            'class' => 'QuestionComments',
                        ),
                        'answers' => array(
                            'label' => $this->l('Answers', 'EtsRVDefines'),
                            'origin' => 'Answers',
                            'icon' => 'answers',
                            'class' => 'Answers',
                        ),
                        'answer_comments' => array(
                            'label' => $this->l('Comments for answer', 'EtsRVDefines'),
                            'origin' => 'Answer Comments',
                            'icon' => 'answer-comments',
                            'class' => 'AnswerComments',
                        )
                    )
                ),
                'staffs' => array(
                    'label' => $this->l('Staff', 'EtsRVDefines'),
                    'origin' => 'Staff',
                    'icon' => 'staffs',
                    'class' => 'Staffs',
                    'sub' => isset($this->context->employee) && $this->context->employee->id_profile == _PS_ADMIN_PROFILE_ ? array(
                        'employee' => array(
                            'label' => $this->l('BO staff', 'EtsRVDefines'),
                            'origin' => 'BO staff',
                            'icon' => 'employee',
                            'class' => 'Staffs',
                            'tab' => 'employee',
                        ),
                        'customer' => array(
                            'label' => $this->l('FO staff', 'EtsRVDefines'),
                            'origin' => 'FO staff',
                            'icon' => 'customer',
                            'class' => 'Staffs',
                            'tab' => 'customer',
                        ),
                    ) : []
                ),
                'activity' => array(
                    'label' => $this->l('Activities', 'EtsRVDefines'),
                    'origin' => 'Activities',
                    'icon' => 'activity',
                    'class' => 'Activity',
                ),
                'users' => array(
                    'label' => $this->l('Authors', 'EtsRVDefines'),
                    'origin' => 'Authors',
                    'icon' => 'users',
                    'class' => 'Users',
                ),
                'discounts' => array(
                    'label' => $this->l('Discounts', 'EtsRVDefines'),
                    'origin' => 'Discounts',
                    'icon' => 'discounts',
                    'class' => 'Discounts',
                ),
                'review_criteria' => array(
                    'label' => $this->l('Criteria', 'EtsRVDefines'),
                    'origin' => 'Criteria',
                    'icon' => 'review-criteria',
                    'class' => 'ReviewCriteria',
                ),
                'import_export' => array(
                    'label' => $this->l('Import/Export', 'EtsRVDefines'),
                    'origin' => 'Import/Export',
                    'icon' => 'import-export',
                    'class' => 'ImportExport',
                ),
                'email' => [
                    'label' => $this->l('Email', 'EtsRVDefines'),
                    'origin' => 'Email',
                    'icon' => 'email',
                    'class' => 'Email',
                    'sub' => [
                        'tracking' => [
                            'label' => $this->l('Mail tracking', 'EtsRVDefines'),
                            'origin' => 'Mail tracking',
                            'icon' => 'tracking',
                            'class' => 'Tracking',
                        ],
                        'queue' => [
                            'label' => $this->l('Mail queue', 'EtsRVDefines'),
                            'origin' => 'Email queue',
                            'icon' => 'queue',
                            'class' => 'Queue',
                        ],
                        'email_template' => [
                            'label' => $this->l('Mail templates', 'EtsRVDefines'),
                            'origin' => 'Mail templates',
                            'icon' => 'email_template',
                            'class' => 'EmailTemplate',
                        ],
                        'cronjob' => [
                            'label' => $this->l('Automation', 'EtsRVDefines'),
                            'origin' => 'Automation',
                            'icon' => 'cronjob',
                            'class' => 'Cronjob',
                        ],
                        'mail_log' => [
                            'label' => $this->l('Mail log', 'EtsRVDefines'),
                            'origin' => 'Mail log',
                            'icon' => 'mail_log',
                            'class' => 'MailLog',
                        ],
                        'unsubscribe' => [
                            'label' => $this->l('Unsubscribe list', 'EtsRVDefines'),
                            'origin' => 'Unsubscribe list',
                            'icon' => 'unsubscribe',
                            'class' => 'Unsubscribe',
                        ]
                    ]
                ],
                'settings' => [
                    'label' => $this->l('Settings', 'EtsRVDefines'),
                    'origin' => 'Settings',
                    'icon' => 'settings',
                    'class' => 'Settings',
                ],
            );
        }
        return self::$quickTabs;
    }

    static $cache_auto_config;

    public function getAutoConfigs()
    {
        if (!self::$cache_auto_config) {
            $values = array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('Enabled', 'EtsRVDefines'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('Disabled', 'EtsRVDefines'),
                ),
            );
            self::$cache_auto_config = array(
                array(
                    'name' => 'ETS_RV_CRONJOB_MAIL_LOG',
                    'type' => 'switch',
                    'label' => $this->l('Enable mail log', 'EtsRVDefines'),
                    'values' => $values,
                    'global' => 1,
                    'tab' => 'cronjob',
                    'default' => 1,
                    'form_group_class' => 'ets_rv_cronjob_emails',
                    'desc' => $this->l('Enable this option for testing purposes only', 'EtsRVDefines'),
                ),
                array(
                    'name' => 'ETS_RV_CRONJOB_EMAILS',
                    'type' => 'text',
                    'label' => $this->l('Mail queue step', 'EtsRVDefines') . ' (' . $this->l('Maximum number of emails sent every time cronjob file run', 'EtsRVDefines') . ')',
                    'default' => 5,
                    'col' => 3,
                    'suffix' => $this->l('email(s)'),
                    'required' => true,
                    'global' => 1,
                    'validate' => 'isUnsignedInt',
                    'tab' => 'cronjob',
                    'form_group_class' => 'ets_rv_cronjob_emails',
                    'desc' => $this->l('Every time cronjob is run, it will check the mail queue for the emails to be sent. Reduce this value if your server has limited timeout value.', 'EtsRVDefines')
                ),
                array(
                    'name' => 'ETS_RV_AUTO_CLEAR_DISCOUNT',
                    'type' => 'switch',
                    'label' => $this->l('Auto clear expired discount codes', 'EtsRVDefines'),
                    'default' => 0,
                    'values' => $values,
                    'global' => 1,
                    'tab' => 'cronjob',
                    'form_group_class' => 'ets_rv_auto_clear_discount',
                ),
                array(
                    'name' => 'ETS_RV_CRONJOB_MAX_TRY',
                    'type' => 'text',
                    'label' => $this->l('Mail queue max-trying times', 'EtsRVDefines'),
                    'default' => 5,
                    'col' => 3,
                    'suffix' => $this->l('time(s)', 'EtsRVDefines'),
                    'required' => true,
                    'global' => 1,
                    'validate' => 'isUnsignedInt',
                    'tab' => 'cronjob',
                    'form_group_class' => 'ets_rv_cronjob_max_try',
                    'desc' => $this->l('The times to try to send an email again if it was failed! After that, the email will be deleted from queue.', 'EtsRVDefines'),
                ),
                array(
                    'name' => 'ETS_RV_SEND_RATING_INVITATION',
                    'type' => 'switch',
                    'label' => $this->l('Send rating invitation email', 'EtsRVDefines'),
                    'default' => 1,
                    'values' => $values,
                    'global' => 1,
                    'tab' => 'cronjob',
                    'form_group_class' => 'ets_rv_send_rating_invitation',
                    'desc' => $this->l('Turn off this option to stop sending the rating invitation email to customers.', 'EtsRVDefines'),
                ),
                array(
                    'type' => 'radios',
                    'label' => $this->l('Send review invitation email when:', 'EtsRVDefines'),
                    'name' => 'ETS_RV_EMAIL_TO_CUSTOMER_ORDER_STATUS',
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 'validated',
                                'name' => $this->l('When order is validated', 'EtsRVDefines')
                            ),
                            array(
                                'id_option' => 'new',
                                'name' => $this->l('When order is created', 'EtsRVDefines')
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name',
                    ),
                    'default' => 'validated',
                    'form_group_class' => 'cronjob ets_rv_email_to_customer_order_status',
                    'tab' => 'cronjob',
                ),
                array(
                    'name' => 'ETS_RV_CRONJOB_SCHEDULE_TIME',
                    'type' => 'text',
                    'label' => $this->l('Schedule review invitation email sending time', 'EtsRVDefines'),
                    'desc' => $this->l('The invitation email will be sent X day(s) after the order is validated or created. Leave blank to send invitation email immediately.', 'EtsRVDefines'),
                    'default' => 1,
                    'col' => 3,
                    'suffix' => $this->l('day(s)', 'EtsRVDefines'),
                    'global' => 1,
                    'validate' => 'isUnsignedInt',
                    'tab' => 'cronjob',
                    'form_group_class' => 'ets_rv_cronjob_schedule_time',
                ),
                array(
                    'name' => 'ETS_RV_SECURE_TOKEN',
                    'type' => 'text',
                    'label' => $this->l('Cronjob secure token', 'EtsRVDefines'),
                    'default' => Tools::passwdGen(10),
                    'col' => 3,
                    'global' => 1,
                    'required' => true,
                    'suffix' => $this->l('Generate', 'EtsRVDefines'),
                    'tab' => 'cronjob',
                    'form_group_class' => 'ets_rv_secure_token',
                ),
                array(
                    'name' => 'ETS_RV_SAVE_CRONJOB_LOG',
                    'type' => 'switch',
                    'label' => $this->l('Save cronjob log', 'EtsRVDefines'),
                    'default' => 0,
                    'desc' => $this->l('Only recommended for debug purpose', 'EtsRVDefines'),
                    'tab' => 'cronjob',
                    'global' => 1,
                    'values' => $values,
                    'form_group_class' => 'ets_rv_save_cronjob_log',
                ),
                array(
                    'name' => 'ETS_RV_CRONJOB_LOG',
                    'type' => 'html',
                    'label' => $this->l('Cronjob log', 'EtsRVDefines'),
                    'default' => '',
                    'tab' => 'cronjob',
                    'global' => 1,
                    'form_group_class' => 'ets_rv_cronjob_log',
                ),
            );
        }
        return self::$cache_auto_config;
    }

    public function getALlConfigs()
    {
        return array_merge(
            $this->getConfigs(),
            $this->getAutoConfigs()
        );
    }

    public function getConfigs($tab_name = null)
    {
        $values = array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Yes', 'EtsRVDefines'),
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('No', 'EtsRVDefines'),
            ),
        );
        $free_downloads_enabled = (int)Configuration::get('ETS_RV_FREE_DOWNLOADS_ENABLED');
        $customer_review = [
            [
                'id' => 'guest',
                'name' => $this->l('Guest', 'EtsRVDefines'),
            ],
            [
                'id' => 'purchased',
                'name' => $this->l('Customers who already purchased product', 'EtsRVDefines'),
            ],
        ];
        if ($free_downloads_enabled < 1) {
            $customer_review = array_merge($customer_review, [
                [
                    'id' => 'no_purchased',
                    'name' => $this->l('Customers who have not purchased the product', 'EtsRVDefines'),
                ]
            ]);
        } else {
            $customer_review = array_merge($customer_review, [
                [
                    'id' => 'no_purchased_incl',
                    'name' => $this->l('Registered customers who have not purchased the product if it\'s free', 'EtsRVDefines'),
                ],
                [
                    'id' => 'no_purchased_excl',
                    'name' => $this->l('Registered customers who have not purchased the product if it\'s not free', 'EtsRVDefines'),
                ],
            ]);
        }
        $customer_rating = [
            [
                'id' => 'guest',
                'name' => $this->l('Guest', 'EtsRVDefines'),
            ],
            [
                'id' => 'purchased',
                'name' => $this->l('Customers who already purchased product', 'EtsRVDefines'),
            ],
        ];

        if ($free_downloads_enabled < 1) {
            $customer_rating = array_merge($customer_rating, [
                [
                    'id' => 'no_purchased',
                    'name' => $this->l('Customers who have not purchased the product', 'EtsRVDefines'),
                ]
            ]);
        } else {
            $customer_rating = array_merge($customer_rating, [
                [
                    'id' => 'no_purchased_incl',
                    'name' => $this->l('Registered customers who have not purchased the product if it\'s free', 'EtsRVDefines'),
                ],
                [
                    'id' => 'no_purchased_excl',
                    'name' => $this->l('Registered customers who have not purchased the product if it\'s not free', 'EtsRVDefines'),
                ],
            ]);
        }

        $order_state_paid = Db::getInstance()->getValue('SELECT GROUP_CONCAT(`id_order_state` SEPARATOR \',\') FROM `' . _DB_PREFIX_ . 'order_state` WHERE `paid` = 1');

        $configs = array(
            // begin general:
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Enable multiple languages for review', 'EtsRVDefines'),
                'desc' => $this->l('Allow customers to leave product reviews in multiple languages', 'EtsRVDefines'),
                'name' => 'ETS_RV_MULTILANG_ENABLED',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'radio',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('How to display reviews, comments, Q&A and replies', 'EtsRVDefines'),
                'name' => 'ETS_RV_PUBLISH_ALL_LANGUAGE',
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Display reviews, comments, Q&A and replies from all languages', 'EtsRVDefines'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Display reviews, comments, Q&A and replies from the selected language', 'EtsRVDefines'),
                    ),
                ),
                'default' => 1,
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Enable reCAPTCHA on comment form', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECAPTCHA_ENABLED',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'checkboxes',
                'label' => $this->l('Enable reCAPTCHA for', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECAPTCHA_FOR',
                'values' => array(
                    array(
                        'id' => 'review',
                        'name' => $this->l('Review', 'EtsRVDefines')
                    ),
                    array(
                        'id' => 'comment',
                        'name' => $this->l('Comments for review', 'EtsRVDefines')
                    ),
                    array(
                        'id' => 'reply',
                        'name' => $this->l('Reply', 'EtsRVDefines')
                    ),
                    array(
                        'id' => 'qa',
                        'name' => $this->l('Questions', 'EtsRVDefines')
                    ),
                    array(
                        'id' => 'qa_answer',
                        'name' => $this->l('Answers', 'EtsRVDefines')
                    ),
                    array(
                        'id' => 'qa_comment',
                        'name' => $this->l('Comments for questions/answers', 'EtsRVDefines')
                    )
                ),
                'default' => 'all',
                'form_group_class' => 'general recaptcha_type is_parent_group',
                'tab' => 'general',
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('reCAPTCHA type', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECAPTCHA_TYPE',
                'values' => array(
                    array(
                        'id' => 'recaptcha_v2',
                        'label' => $this->l('reCAPTCHA v2', 'EtsRVDefines'),
                        'value' => 'recaptcha_v2',
                    ),
                    array(
                        'id' => 'recaptcha_v3',
                        'label' => $this->l('reCAPTCHA v3', 'EtsRVDefines'),
                        'value' => 'recaptcha_v3',
                    )
                ),
                'default' => 'recaptcha_v2',
                'form_group_class' => 'general recaptcha_type is_parent_group',
                'tab' => 'general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Site key', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECAPTCHA_SITE_KEY_V2',
                'col' => '6',
                'required' => true,
                'form_group_class' => 'general recaptcha_type recaptcha_v2',
                'tab' => 'general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Secret key', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECAPTCHA_SECRET_KEY_V2',
                'col' => '6',
                'required' => true,
                'form_group_class' => 'general recaptcha_type recaptcha_v2',
                'tab' => 'general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Site key', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECAPTCHA_SITE_KEY_V3',
                'col' => '6',
                'required' => true,
                'form_group_class' => 'general recaptcha_type recaptcha_v3',
                'tab' => 'general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Secret key', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECAPTCHA_SECRET_KEY_V3',
                'col' => '6',
                'required' => true,
                'form_group_class' => 'general recaptcha_type recaptcha_v3',
                'tab' => 'general',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Do not require registered user to enter reCAPTCHA', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECAPTCHA_USER_REGISTERED',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'general recaptcha_type is_parent_group',
                'tab' => 'general',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Press Enter key to post review/comment', 'EtsRVDefines'),
                'name' => 'ETS_RV_PRESS_ENTER_ENABLED',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'group',
                'label' => $this->l('Validated order statuses', 'EtsRVDefines'),
                'name' => 'ETS_RV_VERIFY_PURCHASE',
                'desc' => $this->l('Customer\'s order needs to be changed to the checked status above before customers are able to leave a review', 'EtsRVDefines'),
                'values' => array(
                    'query' => OrderState::getOrderStates($this->context->language->id),
                    'id' => 'id_order_state',
                    'name' => 'name'
                ),
                'default' => $order_state_paid,
                'form_group_class' => 'general validator_label',
                'tab' => 'general',
            ),
            array(
                'type' => 'checkboxes',
                'label' => $this->l('Recorded activities', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECORDED_ACTIVITIES',
                'values' => array(
                    [
                        'id' => EtsRVActivity::ETS_RV_RECORDED_REVIEWS,//'rev',
                        'name' => $this->l('Customer leaves a review, comment or reply', 'EtsRVDefines')
                    ],
                    [
                        'id' => EtsRVActivity::ETS_RV_RECORDED_QUESTIONS,//'que',
                        'name' => $this->l('Customer asks a question, leaves an answer, comments or replies to a question/an answer', 'EtsRVDefines')
                    ],
                    [
                        'id' => EtsRVActivity::ETS_RV_RECORDED_USEFULNESS,//'lie',
                        'name' => $this->l('Customer likes/dislikes a review, question, etc.', 'EtsRVDefines')
                    ],
                ),
                'default' => implode(',', [EtsRVActivity::ETS_RV_RECORDED_REVIEWS, EtsRVActivity::ETS_RV_RECORDED_QUESTIONS, EtsRVActivity::ETS_RV_RECORDED_USEFULNESS]),
                'form_group_class' => 'general ets_rv_recorded_activities',
                'tab' => 'general',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Do not record admins\' activities', 'EtsRVDefines'),
                'name' => 'ETS_RV_RECORD_ADMIN',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('How to display customer name', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISPLAY_NAME',
                'values' => array(
                    [
                        'id' => 'ets_rv_display_name_' . EtsRVProductComment::DISPLAY_CUSTOMER_FULL_NAME,
                        'label' => $this->l('Full name (e.g: John Smith)', 'EtsRVDefines'),
                        'value' => EtsRVProductComment::DISPLAY_CUSTOMER_FULL_NAME
                    ],
                    [
                        'id' => 'ets_rv_display_name_' . EtsRVProductComment::DISPLAY_CUSTOMER_ACRONYM_FIRSTNAME,
                        'label' => $this->l('Acronym first name (e.g: J. Smith)', 'EtsRVDefines'),
                        'value' => EtsRVProductComment::DISPLAY_CUSTOMER_ACRONYM_FIRSTNAME
                    ],
                    [
                        'id' => 'ets_rv_display_name_' . EtsRVProductComment::DISPLAY_CUSTOMER_ACRONYM_LASTNAME,
                        'label' => $this->l('Acronym last name (e.g: John S.)', 'EtsRVDefines'),
                        'value' => EtsRVProductComment::DISPLAY_CUSTOMER_ACRONYM_LASTNAME
                    ],
                ),
                'default' => EtsRVProductComment::DISPLAY_CUSTOMER_FULL_NAME,
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Display product information when write review and add question', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISPLAY_PRODUCT_INFO',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Display time period', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISPLAY_TIME_PERIOD',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('How to display rating and question when no data is available', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISPLAY_RATE_AND_QUESTION',
                'desc' => $this->l('The rating box includes the average product rating and five-star icons.', 'EtsRVDefines'),
                'values' => array(
                    array(
                        'id' => 'display_rate_and_question_button',
                        'value' => 'button',
                        'label' => $this->l('Display "Write your review" and "Ask a question" buttons', 'EtsRVDefines'),

                    ),
                    array(
                        'id' => 'display_rate_and_question_box',
                        'value' => 'box',
                        'label' => $this->l('Display "Average rating" box', 'EtsRVDefines'),
                    ),
                ),
                'default' => 'button',
                'validate' => 'isCleanHtml',
                'form_group_class' => 'general ets_rv_average_rate_position',
                'tab' => 'general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('"Rate now" button', 'EtsRVDefines'),
                'name' => 'ETS_RV_MAIL_RATE_NOW_TEXT',
                'lang' => true,
                'default' => $this->l('Rate now', 'EtsRVDefines'),
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('"Unsubscribe" button', 'EtsRVDefines'),
                'name' => 'ETS_RV_MAIL_UNSUBSCRIBE_TEXT',
                'lang' => true,
                'default' => $this->l('Unsubscribe', 'EtsRVDefines'),
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable cache', 'EtsRVDefines'),
                'name' => 'ETS_RV_CACHE_ENABLED',
                'values' => $values,
                'default' => 0,
                'desc' => $this->l('The module uses PrestaShop Smarty Cache, so please make sure that PrestaShop Smarty Cache is enabled to use this feature'),
                'form_group_class' => 'general ets_rv_cache_enabled',
                'tab' => 'general',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Cache lifetime', 'EtsRVDefines'),
                'name' => 'ETS_RV_CACHE_LIFETIME',
                'default' => 24,
                'col' => 2,
                'suffix' => $this->l('Hours', 'EtsRVDefines'),
                'form_group_class' => 'general ets_rv_cache_lifetime',
                'desc' => $this->l('Leave blank to cache permanently', 'EtsRVDefines'),
                'tab' => 'general',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Disable Slick library', 'EtsRVDefines'),
                'name' => 'ETS_RV_SLICK_LIBRARY_DISABLED',
                'values' => $values,
                'default' => 0,
                'desc' => $this->l('If your current theme already has the Slick library, you can disable the Slick library of "Product Reviews - Ratings, Google Snippets, Q&A" module to avoid calling this library multiple times, therefore making page loading time increase. If your current theme does not have a Slick library, activating this option will cause an error.', 'EtsRVDefines'),
                'form_group_class' => 'general',
                'tab' => 'general',
            ),
            // end general:

            // begin review:
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Enable product reviews', 'EtsRVDefines'),
                'name' => 'ETS_RV_REVIEW_ENABLED',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'checkboxes',
                'label' => $this->l('Who can write reviews for products?', 'EtsRVDefines'),
                'name' => 'ETS_RV_WHO_POST_REVIEW',
                'values' => $customer_review,
                'default' => 'purchased' . ($free_downloads_enabled > 0 ? ',no_purchased_incl' : ',no_purchased'),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Available time range to write review after purchasing product', 'EtsRVDefines'),
                'name' => 'ETS_RV_REVIEW_AVAILABLE_TIME',
                'values' => $values,
                'suffix' => $this->l('days', 'EtsRVDefines'),
                'col' => '4',
                'desc' => $this->l('Within X day(s) after successfully purchasing a product, customers can write a review. Leave blank to allow customers to write product review anytime after purchasing product.', 'EtsRVDefines'),
                'form_group_class' => 'review ets_rv_review_available_time',
                'validate' => 'isNullOrUnsignedId',
                'tab' => 'review',
            ),
            array(
                'type' => 'checkboxes',
                'label' => $this->l('Who can rate products?', 'EtsRVDefines'),
                'name' => 'ETS_RV_WHO_POST_RATING',
                'values' => $customer_rating,
                'default' => 'purchased' . ($free_downloads_enabled > 0 ? ',no_purchased_incl' : ''),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('All reviews must be validated by an employee', 'EtsRVDefines'),
                'name' => 'ETS_RV_MODERATE',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Auto-approve review if customer has purchased product', 'EtsRVDefines'),
                'name' => 'ETS_RV_PURCHASED_PRODUCT_APPROVE',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'review moderate_yes',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Decline product review, comment, reply', 'EtsRVDefines'),
                'name' => 'ETS_RV_REFUSE_REVIEW',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Maximum number of reviews per user per product', 'EtsRVDefines'),
                'name' => 'ETS_RV_MAXIMUM_REVIEW_PER_USER',
                'default' => 1,
                'col' => 2,
                'validate' => 'isUnsignedInt',
                'desc' => $this->l('Leave this field blank to not limit the number of reviews per user', 'EtsRVDefines'),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Maximum number of ratings per user per product', 'EtsRVDefines'),
                'name' => 'ETS_RV_MAXIMUM_RATING_PER_USER',
                'default' => 1,
                'col' => 2,
                'desc' => $this->l('Leave this field blank to not limit the number of ratings per user', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Minimum time between 2 reviews from the same user', 'EtsRVDefines'),
                'name' => 'ETS_RV_MINIMAL_TIME',
                'desc' => $this->l('Configure a minimum time interval between two reviews left by the same user. Leave this field blank to not require minimum time', 'EtsRVDefines'),
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('second(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 30,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Maximum content length of review, comment or reply', 'EtsRVDefines'),
                'name' => 'ETS_RV_MAX_LENGTH',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('character(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => EtsRVModel::NAME_MAX_LENGTH,//200
                'desc' => sprintf($this->l('Leave blank for maximum limit %d characters', 'EtsRVDefines'), EtsRVModel::NAME_MAX_LENGTH),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Minimum content length of review, comment or reply', 'EtsRVDefines'),
                'name' => 'ETS_RV_MIN_LENGTH',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('character(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => EtsRVModel::NAME_MIN_LENGTH,
                'desc' => sprintf($this->l('Leave blank for minimum limit %d characters', 'EtsRVDefines'), EtsRVModel::NAME_MIN_LENGTH),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Who can comment (or reply) on a review?', 'EtsRVDefines'),
                'name' => 'ETS_RV_WHO_COMMENT_REPLY',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 'user',
                            'name' => $this->l('Any registered user', 'EtsRVDefines'),
                        ),
                        array(
                            'id_option' => 'admin_author',
                            'name' => $this->l('Admin and author of the review', 'EtsRVDefines'),
                        ),
                        array(
                            'id_option' => 'admin',
                            'name' => $this->l('Admin only', 'EtsRVDefines'),
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
                'default' => 'user',
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Always show comment box', 'EtsRVDefines'),
                'name' => 'ETS_RV_SHOW_COMMENT_BOX',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Always show reply box', 'EtsRVDefines'),
                'name' => 'ETS_RV_SHOW_REPLY_BOX',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Enable like/dislike on reviews', 'EtsRVDefines'),
                'name' => 'ETS_RV_USEFULNESS',
                'desc' => $this->l('Allow your customers to vote (thumbs up or thumbs down) on the reviews posted', 'EtsRVDefines'),
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Require user to enter "Review title"', 'EtsRVDefines'),
                'name' => 'ETS_RV_REQUIRE_TITLE',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Allow user to delete reviews', 'EtsRVDefines'),
                'name' => 'ETS_RV_ALLOW_DELETE_COMMENT',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Allow customers to delete reviews, comments or replies when their reviews, comments or replies status are approved', 'EtsRVDefines'),
                'name' => 'ETS_RV_CUSTOMER_DELETE_APPROVED',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review ets_rv_customer_delete_approved',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Allow user to edit reviews', 'EtsRVDefines'),
                'name' => 'ETS_RV_ALLOW_EDIT_COMMENT',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Allow customers to edit reviews, comments or replies when their reviews, comments or replies status are approved', 'EtsRVDefines'),
                'name' => 'ETS_RV_CUSTOMER_EDIT_APPROVED',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review ets_rv_customer_edit_approved',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Auto-approve comment/reply', 'EtsRVDefines'),
                'name' => 'ETS_RV_AUTO_APPROVE',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Auto-approve comment or reply if user has already purchased product', 'EtsRVDefines'),
                'name' => 'ETS_RV_AUTO_APPROVE_PURCHASED',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'review allow_guests_no auto_approve_no',
                'tab' => 'review',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Default rating', 'EtsRVDefines'),
                'name' => 'ETS_RV_DEFAULT_RATE',
                'options' => array(
                    'query' => array(
                        array(
                            'id' => '0',
                            'label' => $this->l('No, require to enter', 'EtsRVDefines')
                        ),
                        array(
                            'id' => '1',
                            'label' => '1'
                        ),
                        array(
                            'id' => '2',
                            'label' => '2'
                        ),
                        array(
                            'id' => '3',
                            'label' => '3'
                        ),
                        array(
                            'id' => '4',
                            'label' => '4'
                        ),
                        array(
                            'id' => '5',
                            'label' => '5'
                        ),
                    ),
                    'id' => 'id',
                    'name' => 'label'
                ),
                'default' => 5,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of initial reviews', 'EtsRVDefines'),
                'name' => 'ETS_RV_REVIEWS_INITIAL',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('review(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of product reviews displayed initially. If the actual number of product reviews is larger, the "View more" link/button will appear', 'EtsRVDefines'),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of reviews per "View more"', 'EtsRVDefines'),
                'name' => 'ETS_RV_REVIEWS_PER_PAGE',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('review(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of product reviews loaded each time user clicks "View more".', 'EtsRVDefines'),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of initial comments', 'EtsRVDefines'),
                'name' => 'ETS_RV_COMMENTS_INITIAL',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('comment(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 1,
                'desc' => $this->l('The number of product comments displayed initially. If the actual number of product comments is larger, the "View more" link will appear', 'EtsRVDefines'),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of comments per "View more"', 'EtsRVDefines'),
                'name' => 'ETS_RV_COMMENTS_PER_PAGE',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('comment(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of product comments loaded each time user clicks "View more"', 'EtsRVDefines'),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of initial replies', 'EtsRVDefines'),
                'name' => 'ETS_RV_REPLIES_INITIAL',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('reply (replies)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 1,
                'desc' => $this->l('The number of replies displayed initially. If the actual number of replies is larger, the "View more" link will appear', 'EtsRVDefines'),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of replies per "View more"', 'EtsRVDefines'),
                'name' => 'ETS_RV_REPLIES_PER_PAGE',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('reply (replies)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of replies loaded each time user clicks "View more"', 'EtsRVDefines'),
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Allow users to upload photos in their review', 'EtsRVDefines'),
                'name' => 'ETS_RV_UPLOAD_PHOTO_ENABLED',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'is_bool' => true,
                'label' => $this->l('Maximum number of upload photos', 'EtsRVDefines'),
                'name' => 'ETS_RV_MAX_UPLOAD_PHOTO',
                'values' => $values,
                'col' => '3',
                'suffix' => $this->l('photo(s)', 'EtsRVDefines'),
                'required' => true,
                'default' => 4,
                'validate' => 'isUnsignedId',
                'desc' => $this->l('The number of max upload photos must be greater than 0', 'EtsRVDefines'),
                'form_group_class' => 'review ets_rv_max_upload_photo',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Allow users to upload videos in their review', 'EtsRVDefines'),
                'name' => 'ETS_RV_UPLOAD_VIDEO_ENABLED',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'is_bool' => true,
                'label' => $this->l('Maximum number of upload videos', 'EtsRVDefines'),
                'name' => 'ETS_RV_MAX_UPLOAD_VIDEO',
                'values' => $values,
                'col' => '3',
                'suffix' => $this->l('video(s)', 'EtsRVDefines'),
                'required' => true,
                'default' => 4,
                'validate' => 'isUnsignedId',
                'desc' => $this->l('The number of max upload videos must be greater than 0', 'EtsRVDefines'),
                'form_group_class' => 'review ets_rv_max_upload_video',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Verified purchase label', 'EtsRVDefines'),
                'name' => 'ETS_RV_VERIFIED_PURCHASE_LABEL',
                'lang' => true,
                'col' => '6',
                'desc' => $this->l('Leave blank to not display the label', 'EtsRVDefines'),
                'default' => [
                    'og' => 'Verified purchase',
                    't' => $this->l('Verified purchase'),
                ],
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Display publish time of product review', 'EtsRVDefines'),
                'name' => 'ETS_RV_SHOW_DATE_ADD',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('By default, sort reviews by:', 'EtsRVDefines'),
                'name' => 'ETS_RV_DEFAULT_SORT_BY',
                'options' => array(
                    'query' => $this->getSortBy(),
                    'id' => 'value',
                    'name' => 'name'
                ),
                'default' => 'date_add.desc',
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Display all photos in review', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISPLAY_ALL_PHOTO',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Display average rating and the latest reviews on the homepage', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISPLAY_ON_HOME',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'review',
            ),
            array(
                'type' => 'text',
                'is_bool' => true,
                'label' => $this->l('Number of latest reviews to display', 'EtsRVDefines'),
                'name' => 'ETS_RV_NUMBER_OF_LAST_REVIEWS',
                'values' => $values,
                'col' => '3',
                'suffix' => $this->l('review(s)', 'EtsRVDefines'),
                'required' => true,
                'default' => 8,
                'validate' => 'isUnsignedId',
                'desc' => $this->l('The number of latest reviews to display must be greater than 0', 'EtsRVDefines'),
                'form_group_class' => 'review ets_rv_display_on_home',
                'tab' => 'review',
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('"Average review" block position', 'EtsRVDefines'),
                'name' => 'ETS_RV_AVERAGE_RATE_POSITION',
                'values' => array(
                    array(
                        'id' => 'add_to_cart',
                        'value' => 'add_to_cart',
                        'label' => $this->l('Under the "Add to cart" button', 'EtsRVDefines'),
                    ),
                    array(
                        'id' => 'product_price',
                        'value' => 'product_price',
                        'label' => $this->l('Under the product price', 'EtsRVDefines'),
                    ),
                    array(
                        'id' => 'product_additional_info',
                        'value' => 'product_additional_info',
                        'label' => $this->l('Under "Share" buttons (default)', 'EtsRVDefines'),
                    ),
                    array(
                        'id' => 'product_reassurance',
                        'value' => 'product_reassurance',
                        'label' => $this->l('In the "Customer reassurance" block', 'EtsRVDefines'),
                    ),
                    array(
                        'id' => 'custom',
                        'value' => 'custom',
                        'label' => $this->l('Custom hook', 'EtsRVDefines'),
                    ),
                ),
                'default' => 'product_additional_info',
                'validate' => 'isCleanHtml',
                'form_group_class' => 'review ets_rv_average_rate_position',
                'tab' => 'review',
            ),
            // end review:

            // begin question:
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Enable Questions & Answers', 'EtsRVDefines'),
                'name' => 'ETS_RV_QUESTION_ENABLED',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Allow guests to add question', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_ALLOW_GUESTS',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('All questions must be validated by an employee', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_MODERATE',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Who can answer (or comment) to a question?', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_WHO_COMMENT_REPLY',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 'user',
                            'name' => $this->l('Any registered user', 'EtsRVDefines'),
                        ),
                        array(
                            'id_option' => 'admin_author',
                            'name' => $this->l('Admin and author of the question', 'EtsRVDefines'),
                        ),
                        array(
                            'id_option' => 'admin',
                            'name' => $this->l('Admin only', 'EtsRVDefines'),
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
                'default' => 'user',
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Always show answer box', 'EtsRVDefines'),
                'name' => 'ETS_RV_SHOW_ANSWER_BOX',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Always show comment box', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_SHOW_COMMENT_BOX',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Enable like/dislike', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_USEFULNESS',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Allow customers to delete questions', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_ALLOW_DELETE_COMMENT',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Allow customers to delete questions, comments or replies when their questions, comments or replies status are approved', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_CUSTOMER_DELETE_APPROVED',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'question ets_rv_qa_customer_delete_approved',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Allow customers to edit questions', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_ALLOW_EDIT_COMMENT',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true, //retro compat 1.5
                'label' => $this->l('Allow customers to edit question, answer or comment when the question, answer or comment status is approved', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_CUSTOMER_EDIT_APPROVED',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'question ets_rv_qa_customer_edit_approved',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Auto-approve comment/answer', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_AUTO_APPROVE',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Minimum time between 2 questions from the same user', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_MINIMAL_TIME',
                'desc' => $this->l('Configure a minimum time interval between two questions left by the same user', 'EtsRVDefines'),
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('second(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 30,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Minimum content length of question, comment or answer', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_MIN_LENGTH',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('character(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 3,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Maximum content length of question, answer or comment', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_MAX_LENGTH',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('character(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 200,
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of initial questions', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_REVIEWS_INITIAL',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('question(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of questions displayed initially. If the actual number of questions is larger, the "View more" link/button will appear.', 'EtsRVDefines'),
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of questions per "View more"', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_REVIEWS_PER_PAGE',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('question(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of questions loaded each time user clicks "View more"', 'EtsRVDefines'),
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of initial answers', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_COMMENTS_INITIAL',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('answer(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of answers displayed initially. If the actual number of answers is larger, the "View more" link/button will appear.', 'EtsRVDefines'),
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of answers per "View more"', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_COMMENTS_PER_PAGE',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('answer(s)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of answers loaded each time user clicks "View more"', 'EtsRVDefines'),
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of initial replies', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_REPLIES_INITIAL',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('reply (replies)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of replies displayed initially. If the actual number of replies is larger, the "View more" link/button will appear.', 'EtsRVDefines'),
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Number of replies per "View more"', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_REPLIES_PER_PAGE',
                'class' => 'fixed-width-lgx',
                'suffix' => $this->l('reply (replies)', 'EtsRVDefines'),
                'validate' => 'isUnsignedInt',
                'default' => 5,
                'desc' => $this->l('The number of replies loaded each time user clicks "View more"', 'EtsRVDefines'),
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Display publish time of question', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_SHOW_DATE_ADD',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'review',
                'tab' => 'question',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('By default, sort questions by:', 'EtsRVDefines'),
                'name' => 'ETS_RV_QA_DEFAULT_SORT_BY',
                'options' => array(
                    'query' => $this->getSortByQuestion(),
                    'id' => 'value',
                    'name' => 'name'
                ),
                'default' => 'date_add.desc',
                'form_group_class' => 'question',
                'tab' => 'question',
            ),
            // end question:

            // begin discount:
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Offer a voucher code to new review', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISCOUNT_ENABLED',
                'values' => $values,
                'default' => 0,
                'desc' => $this->l('Increase review number by giving a voucher to customers after they submit a new review', 'EtsRVDefines'),
                'form_group_class' => 'discount discount_enabled',
                'tab' => 'discount',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Only give voucher for rating of 5 stars', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISCOUNT_HIGH_RATING',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'discount',
                'tab' => 'discount',
            ),
            array(
                'type' => 'switch',
                'is_bool' => true,
                'label' => $this->l('Only give voucher for the first review of each customer on each product', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISCOUNT_ONLY_CUSTOMER',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'discount',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Discount options', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISCOUNT_OPTION',
                'type' => 'radios',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 'fixed',
                            'name' => $this->l('Fixed discount code', 'EtsRVDefines')
                        ),
                        array(
                            'id_option' => 'auto',
                            'name' => $this->l('Generate discount code automatically', 'EtsRVDefines')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'default' => 'auto',
                'form_group_class' => ' discount discount_option is_parent1',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Discount code', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISCOUNT_CODE',
                'type' => 'text',
                'col' => '2',
                'required' => true,
                'validate' => 'isCleanHtml',
                'form_group_class' => ' discount discount_option fixed',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Discount prefix', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISCOUNT_PREFIX',
                'type' => 'text',
                'col' => '2',
                'default' => 'REV_',
                'required' => 'true',
                'form_group_class' => ' discount discount_option auto discount_prefix is_parent2',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Free shipping', 'EtsRVDefines'),
                'name' => 'ETS_RV_FREE_SHIPPING',
                'type' => 'switch',
                'values' => $values,
                'default' => 0,
                'form_group_class' => ' discount discount_option auto is_parent2',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Apply a discount', 'EtsRVDefines'),
                'name' => 'ETS_RV_APPLY_DISCOUNT',
                'type' => 'radios',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 'percent',
                            'name' => $this->l('Percentage (%)', 'EtsRVDefines')
                        ),
                        array(
                            'id_option' => 'amount',
                            'name' => $this->l('Amount', 'EtsRVDefines')
                        ),
                        array(
                            'id_option' => 'off',
                            'name' => $this->l('None', 'EtsRVDefines')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'default' => 'percent',
                'form_group_class' => ' discount discount_option auto apply_discount is_parent2',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Discount name', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISCOUNT_NAME',
                'type' => 'text',
                'lang' => true,
                'required' => true,
                'form_group_class' => ' discount discount_option auto',
                'validate' => 'isCleanHtml',
                'default' => $this->l('Review', 'EtsRVDefines'),
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Discount percentage', 'EtsRVDefines'),
                'name' => 'ETS_RV_REDUCTION_PERCENT',
                'type' => 'text',
                'suffix' => '%',
                'col' => '4',
                'required' => true,
                'validate' => 'isPercentage',
                'desc' => $this->l('Does not apply to the shipping costs', 'EtsRVDefines'),
                'form_group_class' => ' discount discount_option auto apply_discount percent',
                'default' => 20,
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Amount', 'EtsRVDefines'),
                'name' => 'ETS_RV_REDUCTION_AMOUNT',
                'type' => 'text',
                'default' => '0',
                'currencies' => Currency::getCurrencies(),
                'tax' => array(
                    array(
                        'id_option' => 0,
                        'name' => $this->l('Tax excluded', 'EtsRVDefines')
                    ),
                    array(
                        'id_option' => 1,
                        'name' => $this->l('Tax included', 'EtsRVDefines')
                    ),
                ),
                'required' => true,
                'validate' => 'isFloat',
                'form_group_class' => ' discount discount_option auto apply_discount amount',
                'tab' => 'discount',
            ),
            array(
                'label' => '',
                'name' => 'ETS_RV_ID_CURRENCY',
                'type' => 'select',
                'options' => array(
                    'query' => Currency::getCurrencies(),
                    'id' => 'id_currency',
                    'name' => 'name',
                ),
                'default' => $this->context->currency->id,
                'form_group_class' => 'discount',
                'tab' => 'discount',
            ),
            array(
                'label' => '',
                'name' => 'ETS_RV_REDUCTION_TAX',
                'type' => 'select',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 0,
                            'name' => $this->l('Tax excluded', 'EtsRVDefines')
                        ),
                        array(
                            'id_option' => 1,
                            'name' => $this->l('Tax included', 'EtsRVDefines')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'default' => '0',
                'form_group_class' => 'discount ',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Discount availability', 'EtsRVDefines'),
                'name' => 'ETS_RV_APPLY_DISCOUNT_IN',
                'type' => 'text',
                'required' => 'true',
                'suffix' => $this->l('days', 'EtsRVDefines'),
                'validate' => 'isUnsignedId',
                'col' => '2',
                'default' => '7',
                'form_group_class' => ' discount discount_option auto apply_discount is_parent2',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Minimum amount', 'EtsRVDefines'),
                'name' => 'ETS_RV_MINIMUM_AMOUNT',
                'type' => 'text',
                'validate' => 'isUnsignedFloat',
                'default' => '0',
                'hint' => $this->l('You can choose a minimum amount for the cart either with or without the taxes and shipping.', 'EtsRVDefines'),
                'form_group_class' => 'discount ',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Currency', 'EtsRVDefines'),
                'name' => 'ETS_RV_MINIMUM_AMOUNT_CURRENCY',
                'type' => 'select',
                'options' => array(
                    'query' => Currency::getCurrencies(),
                    'id' => 'id_currency',
                    'name' => 'name',
                ),
                'default' => $this->context->currency->id,
                'form_group_class' => 'discount ',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Tax', 'EtsRVDefines'),
                'name' => 'ETS_RV_MINIMUM_AMOUNT_TAX',
                'type' => 'select',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 0,
                            'name' => $this->l('Tax excluded', 'EtsRVDefines')
                        ),
                        array(
                            'id_option' => 1,
                            'name' => $this->l('Tax included', 'EtsRVDefines')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'default' => '0',
                'form_group_class' => 'discount ',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Shipping', 'EtsRVDefines'),
                'name' => 'ETS_RV_MINIMUM_AMOUNT_SHIPPING',
                'type' => 'select',
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' => 0,
                            'name' => $this->l('Shipping excluded', 'EtsRVDefines')
                        ),
                        array(
                            'id_option' => 1,
                            'name' => $this->l('Shipping included', 'EtsRVDefines')
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name',
                ),
                'default' => '0',
                'form_group_class' => 'discount ',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Exclude discounted products', 'EtsRVDefines'),
                'name' => 'ETS_RV_REDUCTION_EXCLUDE_SPECIAL',
                'type' => 'switch',
                'values' => $values,
                'default' => 0,
                'hint' => $this->l('If enabled, the voucher will not apply to products already on sale.', 'EtsRVDefines'),
                'form_group_class' => ' discount discount_option auto apply_discount percent',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Highlight', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISCOUNT_HIGHLIGHT',
                'type' => 'switch',
                'values' => $values,
                'default' => 0,
                'hint' => $this->l('If the voucher is not yet in the cart, it will be displayed in the cart summary.', 'EtsRVDefines'),
                'form_group_class' => ' discount discount_option auto',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Can use with other vouchers in the same shopping cart?', 'EtsRVDefines'),
                'name' => 'ETS_RV_USE_OTHER_VOUCHER_SAME_CART',
                'type' => 'switch',
                'values' => $values,
                'default' => 0,
                'form_group_class' => ' discount discount_option auto fixed',
                'tab' => 'discount',
            ),
            array(
                'label' => $this->l('Thank you popup message', 'EtsRVDefines'),
                'name' => 'ETS_RV_DISCOUNT_MESSAGE',
                'type' => 'textarea',
                'autoload_rte' => true,
                'lang' => true,
                'required' => 'true',
                'init_content_file' => true,
                'form_group_class' => 'discount discount_option auto fixed discount_message',
                'tab' => 'discount',
            ),
            // end discount:
            // Tab mail old here:
            // Designs:
            array(
                'label' => $this->l('Color 1', 'EtsRVDefines'),
                'name' => 'ETS_RV_DESIGN_COLOR1',
                'type' => 'color',
                'validate' => 'isColorHex',
                'default' => '#ee9a00',
                'desc' => $this->l('Change color for: rating, filter button background when activated, post review/comment/reply/answer button background', 'EtsRVDefines'),
                'form_group_class' => 'design',
                'tab' => 'design',
            ),
            array(
                'label' => $this->l('Color 2', 'EtsRVDefines'),
                'name' => 'ETS_RV_DESIGN_COLOR2',
                'type' => 'color',
                'validate' => 'isColorHex',
                'default' => '#555555',
                'desc' => $this->l('Change color for: "Write your review" button background, "Ask a question" button background, active tab at "My account/My reviews', 'EtsRVDefines'),
                'form_group_class' => 'design',
                'tab' => 'design',
            ),
            array(
                'label' => $this->l('Color 3', 'EtsRVDefines'),
                'name' => 'ETS_RV_DESIGN_COLOR3',
                'type' => 'color',
                'validate' => 'isColorHex',
                'default' => '#ee9a00',
                'desc' => $this->l('Change color for: Background and button border when hover', 'EtsRVDefines'),
                'form_group_class' => 'design',
                'tab' => 'design',
            ),
            array(
                'label' => $this->l('Color 4', 'EtsRVDefines'),
                'name' => 'ETS_RV_DESIGN_COLOR4',
                'type' => 'color',
                'validate' => 'isColorHex',
                'default' => '#48AF1A',
                'desc' => $this->l('Change color for: "Verified purchase" text', 'EtsRVDefines'),
                'form_group_class' => 'design',
                'tab' => 'design',
            ),
            array(
                'label' => $this->l('Color 5', 'EtsRVDefines'),
                'name' => 'ETS_RV_DESIGN_COLOR5',
                'type' => 'color',
                'validate' => 'isColorHex',
                'default' => '#2fb5d2',
                'desc' => $this->l('Change color for: Customer name', 'EtsRVDefines'),
                'form_group_class' => 'design',
                'tab' => 'design',
            ),
        );
        if ($tab_name) {
            $tab_configs = [];
            foreach ($configs as $config) {
                if (isset($config['tab']) && trim($config['tab']) == trim($tab_name))
                    $tab_configs[] = $config;
            }
            return $tab_configs;
        }
        return $configs;
    }

    public function getConfigTabs()
    {
        return [
            'general' => $this->l('General', 'EtsRVDefines'),
            'review' => $this->l('Reviews', 'EtsRVDefines'),
            'question' => $this->l('Questions & Answers', 'EtsRVDefines'),
            'discount' => $this->l('Voucher settings', 'EtsRVDefines'),
            'design' => $this->l('Design', 'EtsRVDefines'),
        ];
    }

    static $cache_fields_list = array();

    public function getFieldsList($qa = 0, $fo = 0)
    {
        if (!self::$cache_fields_list) {
            self::$cache_fields_list = array_merge(
                array(
                    'id_ets_rv_product_comment' => array(
                        'title' => $this->l('ID', 'EtsRVDefines'),
                        'type' => 'int',
                        'class' => 'ets-rv-id_ets_rv_product_comment id',
                        'align' => 'ets-rv-id_ets_rv_product_comment',
                    ),
                    'title' => array(
                        'title' => $this->l('Title', 'EtsRVDefines'),
                        'type' => 'text',
                        'filter_key' => 'title',
                        'class' => 'ets-rv-title review_title',
                        'havingFilter' => true,
                        'align' => 'ets-rv-title',
                    ),
                    'content' => array(
                        'title' => $qa ? $this->l('Question content', 'EtsRVDefines') : $this->l('Review content', 'EtsRVDefines'),
                        'type' => 'text',
                        'filter_key' => 'content',
                        'class' => 'ets-rv-content review_content',
                        'havingFilter' => true,
                        'callback' => 'getCommentsContentIcon',
                        'align' => 'ets-rv-content',
                    ),
                ),
                $fo ? array() : array(
                    'comments' => array(
                        'title' => $this->l('Comments', 'EtsRVDefines'),
                        'type' => 'text',
                        'filter_key' => 'comments',
                        'havingFilter' => true,
                        'callback' => 'getCommentsNumberAsField',
                        'class' => 'ets-rv-comments ets_comments text-center',
                        'orderby' => false,
                        'search' => false,
                        'align' => 'ets-rv-comments center',
                    ),
                    'replies' => array(
                        'title' => $qa ? $this->l('Answers', 'EtsRVDefines') : $this->l('Replies', 'EtsRVDefines'),
                        'type' => 'text',
                        'filter_key' => 'replies',
                        'havingFilter' => true,
                        'callback' => 'getRepliesNumberAsField',
                        'class' => 'ets-rv-replies ets_replies text-center',
                        'orderby' => false,
                        'search' => false,
                        'align' => 'ets-rv-replies center',
                    ),
                ),
                $qa ? array() : array(
                    'grade' => array(
                        'title' => $this->l('Rating', 'EtsRVDefines'),
                        'type' => 'text',
                        'class' => 'ets-rv-grade ets_rating text-center',
                        'callback' => 'displayGrade',
                        'align' => 'ets-rv-grade',
                    )
                ),
                $fo && (int)Configuration::get('ETS_RV' . ($qa ? '_QA' : '') . '_USEFULNESS') > 0 ? array(
                    'total_like' => array(
                        'title' => $this->l('Like', 'EtsRVDefines'),
                        'type' => 'text',
                        'class' => 'ets_like text-center',
                        'havingFilter' => true,
                        'align' => 'ets-rv-active',
                    ),
                    'total_dislike' => array(
                        'title' => $this->l('Dislike', 'EtsRVDefines'),
                        'type' => 'text',
                        'class' => 'ets-rv-total_dislike ets_dislike text-center',
                        'havingFilter' => true,
                        'align' => 'ets-rv-total_dislike',
                    ),
                ) : array(),
                $fo ? array() : array(
                    'customer_name' => array(
                        'title' => $this->l('Author', 'EtsRVDefines'),
                        'type' => 'text',
                        'havingFilter' => true,
                        'callback' => 'buildFieldCustomerLink',
                        'ref' => 'customer_id',
                        'align' => 'ets-rv-customer_name',
                        'class' => 'ets-rv-customer_name',
                    ),
                ),
                array(
                    'product_name' => array(
                        'title' => $this->l('Product', 'EtsRVDefines'),
                        'type' => 'text',
                        'callback' => 'buildFieldProductLink',
                        'havingFilter' => true,
                        'ref' => 'product_id',
                        'align' => 'ets-rv-product_name',
                        'class' => 'ets-rv-product_name',
                    ),
                ),
                $fo || (int)Configuration::get('ETS_RV_MULTILANG_ENABLED') <= 0 && (int)Configuration::get('ETS_RV_PUBLISH_ALL_LANGUAGE') <= 0 ? array() : array(
                    'publish_lang' => array(
                        'title' => $this->l('Languages to display', 'EtsRVDefines'),
                        'type' => 'text',
                        'orderby' => false,
                        'search' => false,
                        'callback' => 'buildFieldPublishLang',
                        'class' => 'ets-rv-publish_lang language text-center',
                        'align' => 'ets-rv-publish_lang center',
                    ),
                ),
                array(
                    'validate' => array(
                        'title' => $this->l('Status', 'EtsRVDefines'),
                        'type' => 'select',
                        'list' => EtsRVDefines::getInstance()->getReviewStatus(),
                        'filter_key' => 'a!validate',
                        'callback' => 'displayValidate',
                        'class' => 'ets-rv-validate review text-center',
                        'badge_success' => true,
                        'badge_warning' => true,
                        'badge_danger' => true,
                        'badge_reject' => true,
                        'align' => 'ets-rv-validate',
                    ),
                    'date_add' => array(
                        'title' => $this->l('Time of publication', 'EtsRVDefines'),
                        'type' => 'date',
                        'filter_key' => 'a!date_add',
                        'align' => 'ets-rv-date_add',
                        'class' => 'ets-rv-date_add',
                    ),
                )
            );
        }

        return self::$cache_fields_list;
    }

    static $cache_review_status = array();

    public function getReviewStatus($status = null)
    {
        if (!self::$cache_review_status) {
            self::$cache_review_status = array(
                '0' => $this->l('Pending', 'EtsRVDefines'),
                '1' => $this->l('Approved', 'EtsRVDefines'),
                '2' => $this->l('Private', 'EtsRVDefines'),
                '3' => $this->l('Rejected', 'EtsRVDefines'),
            );
        }
        return $status !== null ? isset(self::$cache_review_status[$status]) ? self::$cache_review_status[$status] : null : self::$cache_review_status;
    }

    static $cache_activity_type = array();

    public function getActivityTypes($type = null)
    {
        if (!self::$cache_activity_type) {
            self::$cache_activity_type = array(
                EtsRVActivity::ETS_RV_TYPE_REVIEW => $this->l('Review', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_TYPE_COMMENT => $this->l('Comment', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_TYPE_QUESTION => $this->l('Question', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_TYPE_COMMENT_QUESTION => $this->l('Comment to a question', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_TYPE_ANSWER_QUESTION => $this->l('Answer a question', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_TYPE_COMMENT_ANSWER => $this->l('Comment to an answer', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_TYPE_REPLY_COMMENT => $this->l('Reply to comment', 'EtsRVDefines')
            );
        }
        return $type !== null ? isset(self::$cache_activity_type[$type]) ? self::$cache_activity_type[$type] : null : self::$cache_activity_type;
    }

    static $cache_activity_actions = array();

    public function getActivityActions($action = null)
    {
        if (!self::$cache_activity_actions) {
            self::$cache_activity_actions = array(
                EtsRVActivity::ETS_RV_ACTION_REVIEW => $this->l('Review', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_ACTION_COMMENT => $this->l('Comment', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_ACTION_QUESTION => $this->l('Question', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_ACTION_ANSWER => $this->l('Answer', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_ACTION_REPLY => $this->l('Reply', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_ACTION_LIKE => $this->l('Like', 'EtsRVDefines'),
                EtsRVActivity::ETS_RV_ACTION_DISLIKE => $this->l('Dislike', 'EtsRVDefines'),
            );
        }
        return $action !== null ? isset(self::$cache_activity_actions[$action]) ? self::$cache_activity_actions[$action] : null : self::$cache_activity_actions;
    }
}