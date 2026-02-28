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

if (!class_exists(EtsSeoStrHelper::class)) {
    require_once __DIR__ . '/classes/utils/EtsSeoStrHelper.php';
}
require_once __DIR__ . '/classes/traits/EtsSeoGetInstanceTrait.php';

/**
 * Class Ets_Seo_Define
 */
class Ets_Seo_Define
{
    use EtsSeoGetInstanceTrait;

    public $context;
    /**
     * @var string[]
     */
    private $hooks = [
        'displayCustomAdminProductsSeoStepBottom',
        'displayBackOfficeHeader',
        'displayAdminAfterHeader',
        'displayHeader',
        'displayOverrideTemplate',
        'actionProductAdd',
        'actionFrontControllerRedirectBefore',
        'actionDispatcherBefore',
        'actionProductSave',
        'actionProductUpdate',
        'actionObjectAddBefore',
        'actionObjectUpdateBefore',
        'actionObjectAddAfter',
        'actionObjectUpdateAfter',
        'actionAdminProductsListingFieldsModifier',
        'actionAdminProductsListingResultsModifier',
        'actionAdminCmsListingResultsModifier',
        'actionAdminCmsCategoriesListingResultsModifier',
        'actionAdminMetaListingResultsModifier',
        'actionAdminCategoriesListingResultsModifier',
        'actionAdminManufacturersListingResultsModifier',
        'actionAdminSuppliersListingResultsModifier',
        'actionCategoryGridQueryBuilderModifier',
        'actionCategoryGridDefinitionModifier',
        'actionProductGridDefinitionModifier',
        'actionCategoryGridDataModifier',
        'actionProductGridDataModifier',
        'actionCmsPageGridQueryBuilderModifier',
        'actionCmsPageGridDefinitionModifier',
        'actionCmsPageGridDataModifier',
        'actionCmsPageCategoryGridQueryBuilderModifier',
        'actionCmsPageCategoryGridDefinitionModifier',
        'actionCmsPageCategoryGridDataModifier',
        'actionMetaGridQueryBuilderModifier',
        'actionMetaGridDefinitionModifier',
        'actionMetaGridDataModifier',
        'actionManufacturerGridQueryBuilderModifier',
        'actionManufacturerGridDefinitionModifier',
        'actionManufacturerGridDataModifier',
        'actionSuppliersGridQueryBuilderModifier',
        'actionSupplierGridDefinitionModifier',
        'actionSupplierGridDataModifier',
        'actionAdminEtsSeoUrlRedirectFormModifier',
        'actionMetaPageSave',
        'actionBeforeCreateCategoryFormHandler',
        'actionBeforeUpdateCategoryFormHandler',
        'actionCategoryFormBuilderModifier',
        'actionBeforeCreateRootCategoryFormHandler',
        'actionBeforeUpdateRootCategoryFormHandler',
        'actionRootCategoryFormBuilderModifier',
        'actionBeforeCreateCmsPageFormHandler',
        'actionBeforeUpdateCmsPageFormHandler',
        'actionCmsPageFormBuilderModifier',
        'actionBeforeCreateCmsPageCategoryFormHandler',
        'actionBeforeUpdateCmsPageCategoryFormHandler',
        'actionCmsPageCategoryFormBuilderModifier',
        'actionBeforeCreateMetaFormHandler',
        'actionBeforeUpdateMetaFormHandler',
        'actionMetaFormBuilderModifier',
        'actionAdminMetaControllerUpdate_optionsBefore',
        'filterProductContent'
    ];
    /**
     * Hooks only run on >= 8.1.0
     *
     * @var string[]
     */
    private $hooks810 = [
        'actionProductFormBuilderModifier',
    ];

    private $gptTemplates = [
        'AdminCmsContent' => [
        ],
        'AdminProducts' => [
            [
                'label' => 'Write a product description',
                'content' => 'Think like an ecommerce merchandising specialist and write a product description to list {product_name} on an ecommerce store {brand}',
            ],
            [
                'label' => 'Generate a page meta title',
                'content' => 'Think like an ecommerce SEO expert and generate a page meta title for {product_name} from the brand \'{brand}\' from the [industry] industry',
            ],
            [
                'label' => 'Generate a page meta description',
                'content' => 'Think like an ecommerce SEO expert and generate a page meta description for {product_name} from brand \'{brand}\' from the [industry] industry',
            ],
        ],
        'AdminCategories' => [
            [
                'label' => 'Write a product category description',
                'content' => 'Think like an ecommerce merchandising specialist and write a product category description to category {category_name} on an ecommerce store [brand]',
            ],
            [
                'label' => 'Generate a page meta title',
                'content' => 'Think like an ecommerce SEO expert and generate a page meta title for {category_name} from the brand \'[brand]\' from the [industry] industry',
            ],
            [
                'label' => 'Generate a page meta description',
                'content' => 'Think like an ecommerce SEO expert and generate a page meta description for {category_name} from brand \'[brand]\' from the [industry] industry',
            ],
        ],
    ];
    /**
     * List controller will be override meta tags.
     *
     * @var string[]
     */
    private $metaOverriddenControllers = [
        'product',
        'category',
        'cms',
        'cms_category',
        'manufacturer',
        'supplier',
    ];

    public function __construct()
    {
        $context = Ets_Seo::getContextStatic();
        $this->context = $context;
    }

    /**
     * l.
     *
     * @param mixed $string
     *
     * @return string
     */
    public function l($string)
    {
        return Translate::getModuleTranslation(_ETS_SEO_MODULE_, $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    /**
     * display.
     *
     * @param mixed $template
     *
     * @return string
     */
    public function display($template)
    {
        $instance = Module::getInstanceByName('ets_seo');
        if (!$instance) {
            return '';
        }

        return $instance->display($instance->getLocalPath(), $template);
    }

    public static function checkEnableOtherShop($id_module)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int) $id_module . ' AND `id_shop` NOT IN(' . implode(', ', Shop::getContextListShopID()) . ')';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @return string[]
     */
    public function getHooks()
    {
        $hooks = $this->hooks;
        if (version_compare(_PS_VERSION_, '8.1.0', '>=')) {
            $hooks = array_merge($hooks, $this->hooks810);
        }

        return $hooks;
    }

    /**
     * @return string[]
     */
    public function getMetaOverriddenControllers()
    {
        return $this->metaOverriddenControllers;
    }

    /**
     * seo_analysis_rules.
     *
     * @return array
     */
    public function seo_analysis_rules($controller = null, $is_cms_category = false)
    {
        $pageTitleLabel = $this->l('Page title');
        switch ($controller) {
            case 'AdminProducts':
                $pageTitleLabel = $this->l('Product name');
                break;
            case 'AdminCategories':
                $pageTitleLabel = $this->l('Category name');
                break;
            case 'AdminCmsContent':
                if ($is_cms_category) {
                    $pageTitleLabel = $this->l('CMS category title');
                } else {
                    $pageTitleLabel = $this->l('CMS title');
                }
                break;
            case 'AdminMeta':
                $pageTitleLabel = $this->l('Page title');
                break;
            case 'AdminManufacturers':
                $pageTitleLabel = $this->l('Brand (Manufacturer) name');
                break;
            case 'AdminSuppliers':
                $pageTitleLabel = $this->l('Supplier name');
                break;
        }

        return [
            'outbound_link' => [
                'error' => [
                    'text' => $this->l('[link_support]: No outbound links appear in this page. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Outbound links'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/outbound-links-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add some'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/outbound-links/',
                        ],
                    ],
                ],
                'all_nofollowed' => [
                    'text' => $this->l('[link_support]: All outbound links on this page are nofollowed. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Outbound links'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/outbound-links-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add some normal links'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/outbound-links/',
                        ],
                    ],
                ],
                'both' => [
                    'text' => $this->l('[link_support]: There are both nofollowed and normal outbound links on this page. Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Outbound links'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/outbound-links-check/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Outbound links'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/outbound-links-check/',
                        ],
                    ],
                ],
            ],
            'internal_link' => [
                'error' => [
                    'text' => $this->l('[link_support]: No internal links appear in this page. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Internal links'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/internal-links-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Make sure to add some'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/site-structure-training/internal-links/',
                        ],
                    ],
                ],
                'all_nofollowed' => [
                    'text' => $this->l('[link_support]: The internal links in this page are all nofollowed. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Internal links'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/internal-links-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add some good internal links'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/site-structure-training/internal-links/',
                        ],
                    ],
                ],
                'both' => [
                    'text' => $this->l('[link_support]: There are both nofollowed and normal internal links on this page. Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Internal links'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/internal-links-check/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: You have enough internal links. Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Internal links'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/internal-links-check/',
                        ],
                    ],
                ],
            ],
            'keyphrase_length' => [
                'error' => [
                    'text' => $this->l('[link_support]: No focus keyphrase was set for this page. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-length-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Set a keyphrase in order to calculate your SEO score'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/keyword-research-training/keyphrase-length/',
                        ],
                    ],
                ],
                'too_long' => [
                    'text' => $this->l('[link_support]: The focus keyphrase is [count_length] words long. That\'s more than the recommended maximum of 4 words. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-length-check/',
                        ],

                        '[link_doc]' => [
                            'text' => $this->l('Make it shorter'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/keyword-research-training/keyphrase-length/',
                        ],
                        '[count_length]' => [
                            'type' => 'number',
                            'number' => '',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-length-check/',
                        ],
                    ],
                ],
            ],
            'keyphrase_in_subheading' => [
                'too_little' => [
                    'text' => $this->l('[link_support]: [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in subheading'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-subheading-check',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Use more keyphrases or synonyms in your higher-level subheadings'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-subheading-check',
                        ],
                    ],
                ],
                'too_much' => [
                    'text' => $this->l('[link_support]: More than 75% of your higher-level subheadings reflect the topic of your copy. That\'s too much. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in subheading'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-subheading-check',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l(' Don\'t over-optimize'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-subheading-check',
                        ],
                    ],
                ],
                'good' => [
                    'text' => $this->l('[link_support]: [count] of your higher-level subheading(s) reflects the topic of your copy. Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in subheading'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-subheading-check/',
                        ],
                        '[count]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]:  Your higher-level subheading reflects the topic of your copy. Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in subheading'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-subheading-check/',
                        ],
                    ],
                ],
            ],
            'keyphrase_in_title' => [
                'error' => [
                    'text' => $this->l('[link_support]: Not all the words from your keyphrase "[keyphrase]" appear in the meta title. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in meta title'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-title-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Try to use the exact match of your focus keyphrase in the meta title'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/keyphrase-in-title/',
                        ],
                        '[keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                    ],
                ],
                'warning' => [
                    'text' => $this->l('[link_support]: The exact match of the focus keyphrase appears in the meta title, but not at the beginning. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in meta title'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-title-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Try to move it to the beginning'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/keyphrase-in-title/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: The exact match of the focus keyphrase appears at the beginning of the meta title. Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in meta title'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-title-check/',
                        ],
                    ],
                ],
            ],
            'keyphrase_in_page_title' => [
                'error' => [
                    'text' => $this->l('[link_support]: Not all the words from your focus keyphrase "[keyphrase]" appear in the ') . $pageTitleLabel . '. [link_doc].',
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in ') . $pageTitleLabel,
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-title-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Try to use the exact match of your focus keyphrase in ') . $pageTitleLabel,
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/keyphrase-in-title/',
                        ],
                        '[keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                    ],
                ],

                'warning' => [
                    'text' => $this->l('[link_support]: The exact match of the focus keyphrase appears in the title, but not at the beginning. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in ') . $pageTitleLabel,
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-title-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Try to move it to the beginning'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/keyphrase-in-title/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: The exact match of the focus keyphrase appears at the beginning of the title. Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in ') . $pageTitleLabel,
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-title-check/',
                        ],
                    ],
                ],
            ],
            'page_title_length' => [
                'too_long' => [
                    'text' => '[link_support]: The ' . $pageTitleLabel . $this->l(' is too long. That\'s over 65 characters. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $pageTitleLabel . $this->l(' length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => '#',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => '#',
                        ],
                    ],
                ],
                'empty' => [
                    'text' => '[link_support]: The ' . $pageTitleLabel . $this->l(' is empty. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $pageTitleLabel . $this->l(' length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => '#',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add some'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => '#',
                        ],
                    ],
                ],
                'success' => [
                    'text' => '[link_support]: ' . $this->l(' Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $pageTitleLabel . $this->l(' length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => '#',
                        ],
                    ],
                ],
            ],
            'keyphrase_in_intro' => [
                'error' => [
                    'text' => $this->l('[link_support]: Your focus keyphrase or its synonyms do not appear in the first paragraph. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in introduction'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-introduction-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Make sure the topic is clear immediately'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/keyphrase-in-introduction/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Well done!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in introduction'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-introduction-check/',
                        ],
                    ],
                ],
            ],
            'keyphrase_density' => [
                'error' => [
                    'text' => $this->l('[link_support]: The focus keyphrase was found [count_word] time. That\'s less than the recommended minimum of [recommended_keyphrase_length] times for a text of this length. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase density'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-density-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Focus on your keyphrase'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/keyphrase-density/',
                        ],
                        '[count_word]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                        '[recommended_keyphrase_length]' => [
                            'type' => 'number',
                            'number' => '2',
                        ],
                    ],
                ],
                'more_than' => [
                    'text' => $this->l('[link_support]: The focus keyphrase was found [count_word] time. That\'s more than the recommended maximum of [recommended_keyphrase_length] times for a text of this length. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase density'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-density-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Focus on your keyphrase'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/keyphrase-density/',
                        ],
                        '[count_word]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                        '[recommended_keyphrase_length]' => [
                            'type' => 'number',
                            'number' => '2',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]:  The focus keyphrase was found [count_word] times. This is great!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase density'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-density-check/',
                        ],
                        '[count_word]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
            ],
            'keyphrase_density_individual' => [
                'error' => [
                    'text' => $this->l('[link_support]: Each single word of the focus keyphrase ("[keyphrase_individual]") should appear at least [recommended_keyphrase_length] time(s) in the content (appearing on focus keyphrase does not count) . [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Individual words of focus keyphrase'),
                            'type' => 'link',
                            'key' => '',
                            'link' => '#',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add some'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => '#',
                        ],
                        '[keyphrase_individual]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                        '[recommended_keyphrase_length]' => [
                            'type' => 'number',
                            'number' => '2',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Great job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Individual words of focus keyphrase'),
                            'type' => 'link',
                            'key' => '',
                            'link' => '#',
                        ],
                    ],
                ],
            ],

            'image_alt_attribute' => [
                'error' => [
                    'text' => $this->l('[link_support]: No images appear on this page. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Image alt attributes'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/image-alt-attributes-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add some'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/image-alt-attributes/',
                        ],
                    ],
                ],
                'no_alt' => [
                    'text' => $this->l('[link_support]: Images on this page do not have alt attributes that reflect the topic of your text. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Image alt attributes'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/image-alt-attributes-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add your focus keyphrase or synonyms to the alt tags of relevant images'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/image-alt-attributes/',
                        ],
                    ],
                ],
                'alt_no_keyphrase' => [
                    'text' => $this->l('[link_support]: Image alt attributes are missing or do contain the focus keyphrase. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Image alt attributes'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/image-alt-attributes-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add your focus keyphrase or synonyms to the alt tags of relevant images'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/image-alt-attributes/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Image alt attributes'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/image-alt-attributes-check/',
                        ],
                    ],
                ],
            ],
            'single_h1' => [
                'error' => [
                    'text' => $this->l('[link_support]: H1s should only be used as [page_title]. Find all H1s in your text that aren\'t your [page_title] and change them to a lower heading level!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Single title'),
                            'type' => 'link',
                            'key' => '',
                            'link' => '#',
                        ],
                        '[page_title]' => [
                            'type' => 'string',
                            'string' => $pageTitleLabel,
                        ],
                    ],
                ],
            ],
            'text_length' => [
                'error' => [
                    'text' => $this->l('[link_support]: The text contains [text_length] words. This is far below the recommended minimum of [min_length] words. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Text length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/text-length-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add more content'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/text-length',
                        ],
                        '[text_length]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                        '[min_length]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: The text contains [text_length] words. Good job'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Text length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/text-length-check/',
                        ],
                        '[text_length]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * seo_analysis_rules_meta.
     *
     * @return array
     */
    public function seo_analysis_rules_meta()
    {
        return [
            'meta_description_length' => [
                'error' => [
                    'text' => $this->l('[link_support]: No meta description has been specified. Search engines will display copy from the page instead. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Meta description length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/meta-description-length-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Make sure to write one'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/meta-description-length/',
                        ],
                    ],
                ],
                'warning' => [
                    'text' => $this->l('[link_support]: The meta description is too short (under 120 characters). Up to 156 characters are available. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Meta description length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/meta-description-length-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Use the space'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/meta-description-length/',
                        ],
                    ],
                ],
                'over_limited' => [
                    'text' => $this->l('[link_support]: The meta description is over 156 characters. To ensure the entire description will be visible, [link_doc]'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Meta description length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/meta-description-length-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('you should reduce the length'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/meta-description-length/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Well done!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Meta description length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/meta-description-length-check/',
                        ],
                    ],
                ],
            ],
            'seo_title_width' => [
                'error' => [
                    'text' => $this->l('[link_support]: [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Meta title length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/seo-title-width-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Please create a meta title'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/seo-title-width/',
                        ],
                    ],
                ],
                'too_long' => [
                    'text' => $this->l('[link_support]: The meta title is wider than the viewable limit. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Meta title length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/seo-title-width-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Try to make it shorter'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/seo-title-width/',
                        ],
                    ],
                ],
                'warning' => [
                    'text' => $this->l('[link_support]: The meta title is too short. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Meta title length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/seo-title-width-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Use the space to add focus keyphrase variations or create compelling call-to-action copy'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/seo-title-width/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Meta title length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/seo-title-width-check/',
                        ],
                    ],
                ],
            ],
            'keyphrase_in_meta_desc' => [
                'error' => [
                    'text' => $this->l('[link_support]: The meta description has been specified, but it does not contain the focus keyphrase. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in meta description'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-meta-description-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/keyphrase-in-meta-description/',
                        ],
                    ],
                ],
                'more_than' => [
                    'text' => $this->l('[link_support]: The meta description contains the focus keyphrase [number] times, which is over the advised maximum of 2 times. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in meta description'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-meta-description-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Limit that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/keyphrase-in-meta-description/',
                        ],
                        '[number]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Focus keyphrase or synonym appear in the meta description. Well done!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in meta description'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-meta-description-check/',
                        ],
                    ],
                ],
            ],

            'keyphrase_in_slug' => [
                'warning' => [
                    'text' => $this->l('[link_support]: (Part of) your focus keyphrase does not appear in the friendly URL. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in friendly URL'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-slug-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Change that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/all-around-seo-training/keyphrase-in-slug/',
                        ],
                    ],
                ],
                'good' => [
                    'text' => $this->l('[link_support]: more than half of your focus keyphrase appears in the friendly URL. That\'s great!!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in friendly URL'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-slug-check/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Great work!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Focus keyphrase in friendly URL'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/keyphrase-in-slug-check/',
                        ],
                    ],
                ],
            ],
            'minor_keyphrase_length' => [
                'too_long' => [
                    'text' => $this->l('[link_support]: The related keyphrase "[minor_keyphrase]" is [count_length] words long. That\'s more than the recommended maximum of 4 words. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => '#',
                        ],

                        '[link_doc]' => [
                            'text' => $this->l('Make it shorter'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => '#',
                        ],
                        '[count_length]' => [
                            'type' => 'number',
                            'number' => '',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Good job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => '#',
                        ],
                    ],
                ],
            ],
            'minor_keyphrase_in_content' => [
                'error' => [
                    'text' => $this->l('[link_support]: The content does not contain the related keyphrases: "[minor_keyphrase]". [link_doc]'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase density'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'string' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                    ],
                ],
                'over_limited' => [
                    'text' => $this->l('[link_support]: The related keyphrase "[minor_keyphrase]" was found [count_word] time. That\'s more than the recommended maximum of [recommended_minor_keyphrase_length] times for a text of this length. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase density'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                        '[count_word]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                        '[recommended_minor_keyphrase_length]' => [
                            'type' => 'number',
                            'number' => '2',
                        ],
                    ],
                ],
                'less_than' => [
                    'text' => $this->l('[link_support]: The related keyphrase "[minor_keyphrase]" was found [count_word] time. That\'s less than the recommended minimum of [recommended_minor_keyphrase_length] times for a text of this length. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase density'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add some'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                        '[count_word]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                        '[recommended_minor_keyphrase_length]' => [
                            'type' => 'number',
                            'number' => '2',
                        ],
                    ],
                ],

                'success' => [
                    'text' => $this->l('[link_support]: Great work!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrases density'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                    ],
                ],
            ],
            'minor_keyphrase_in_content_individual' => [
                'error' => [
                    'text' => $this->l('[link_support]:  Individual word "[keyphrase_individual]" of the related keyphrases should appear at least [recommended_keyphrase_length] times. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Individual words of related keyphrase'),
                            'type' => 'link',
                            'key' => 'individual',
                            'link' => '#',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Add some'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => '#',
                        ],
                        '[keyphrase_individual]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                        '[recommended_keyphrase_length]' => [
                            'type' => 'number',
                            'number' => '2',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]:  Great job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Individual words of related keyphrase'),
                            'type' => 'link',
                            'key' => 'individual',
                            'link' => '#',
                        ],
                    ],
                ],
            ],
            'minor_keyphrase_in_title' => [
                'error' => [
                    'text' => $this->l('[link_support]: The meta title does not contain the related keyphrase: "[minor_keyphrase]". [link_doc]'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrases in meta title'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'string' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                    ],
                ],
                'over_limited' => [
                    'text' => $this->l('[link_support]: The related keyphrase "[minor_keyphrase]" was found [count_word] time. That\'s more than the recommended minimum of 2 times for a text of this length. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrases in meta title'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'string' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[count_word]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                    ],
                ],

                'success' => [
                    'text' => $this->l('[link_support]: Great work!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase in meta title'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                    ],
                ],
            ],
            'minor_keyphrase_in_page_title' => [
                'error' => [
                    'text' => $this->l('[link_support]: The title does not contain the related keyphrase: "[minor_keyphrase]". [link_doc]'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrases in title (name)'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'string' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                    ],
                ],
                'over_limited' => [
                    'text' => $this->l('[link_support]: The related keyphrase "[minor_keyphrase]" was found [count_word] time(s). That\'s more than the recommended minimum of 2 times for a text of this length. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrases in title (name)'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => 'doc',
                            'string' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[count_word]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                    ],
                ],

                'success' => [
                    'text' => $this->l('[link_support]: Great work!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase in title (name)'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                    ],
                ],
            ],
            'minor_keyphrase_in_desc' => [
                'error' => [
                    'text' => $this->l('[link_support]: The meta description does not contain the related keyphrase: "[minor_keyphrase]". [link_doc]'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase in meta description'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => '',
                            'string' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                    ],
                ],
                'over_limited' => [
                    'text' => $this->l('[link_support]: The related keyphrase "[minor_keyphrase]" was found [count_word] time. That\'s more than the recommended minimum of 2 times for a text of this length. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrases in meta description'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Fix that'),
                            'type' => 'link',
                            'key' => '',
                            'string' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                        '[minor_keyphrase]' => [
                            'type' => 'string',
                            'string' => '',
                        ],
                        '[count_word]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Great work!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase in meta description'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                    ],
                ],
            ],
            'minor_keyphrase_acceptance' => [
                'success' => [
                    'text' => $this->l('[link_support]: Great work!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Related keyphrase in title or meta title'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://www.seozoom.co.uk/minor-keywords-how-to-optimize-a-text-with-search-intent-and-correlated-ones/',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * readability_rules.
     *
     * @return array
     */
    public function readability_rules()
    {
        return [
            'not_enough_content' => [
                'error' => [
                    'text' => $this->l('[link_support]: [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Not enough content'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/not-enough-content-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Please add some content to enable a good analysis'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/not-enough-content-check/',
                        ],
                    ],
                ],
            ],
            'sentence_length' => [
                'error' => [
                    'text' => $this->l('[link_support]: [number]% of the sentences contain more than 20 words, which is more than the recommended maximum of 25%. [link_doc]! [link_js]'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Sentence length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/sentence-length-check/',
                        ],
                        '[link_js]' => [
                            'text' => $this->l('Highlight this result'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'javascript:void(0)',
                            'show' => false,
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Try to shorten the sentences'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/sentence-length/',
                        ],
                        '[number]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Great!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Sentence length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/sentence-length-check/',
                        ],
                    ],
                ],
            ],
            'flesch_reading_ease' => [
                'error' => [
                    'text' => $this->l('[link_support]: The copy scores [score] in the test, which is considered difficult to read. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Flesch Reading Ease'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/flesch-reading-ease-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Try to make shorter sentences to improve readability'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/flesch-reading-ease-check/',
                        ],
                        '[score]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
                'warning' => [
                    'text' => $this->l('[link_support]: The copy scores [score] in the test, which is considered fairly difficult to read. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Flesch Reading Ease'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/flesch-reading-ease-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Try to make shorter sentences to improve readability'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/flesch-reading-ease/',
                        ],
                        '[score]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: The copy scores [score] in the test, which is considered ok to read. Good job!.'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Flesch Reading Ease'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/flesch-reading-ease-check/',
                        ],
                        '[score]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
            ],
            'paragraph_length' => [
                'error' => [
                    'text' => $this->l('[link_support]: [number] of the paragraphs contains more than the recommended maximum of 150 words. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Paragraph length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/paragraph-length-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Shorten your paragraphs.'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/paragraph-length/',
                        ],
                        '[number]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
                'warning' => [
                    'text' => $this->l('[link_support]: [number] of the paragraphs contains more than the recommended maximum of 150 words. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Paragraph length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/paragraph-length-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Shorten your paragraphs.'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/paragraph-length/',
                        ],
                        '[number]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],

                'success' => [
                    'text' => $this->l('[link_support]: None of the paragraphs are too long. Great job!.'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Paragraph length'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/paragraph-length-check/',
                        ],
                    ],
                ],
            ],
            'passive_voice' => [
                'success' => [
                    'text' => $this->l('[link_support]: You\'re using enough active voice. That\'s great!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Passive voice'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/passive-voice-check/',
                        ],
                    ],
                ],
                'error' => [
                    'text' => $this->l('[link_support]: [number]% of the sentences contain passive voice, which is more than the recommended maximum of 10% [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Passive voice'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/passive-voice-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Try to use their active counterparts'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/passive-voice/',
                        ],
                        '[number]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
            ],
            'consecutive_sentences' => [
                'error' => [
                    'text' => $this->l('[link_support]: The text contains [number] consecutive sentences starting with the same word. [link_doc]!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Consecutive sentences'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/consecutive-sentences-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l(' Try to mix things up'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/consecutive-sentences-check/',
                        ],
                        '[number]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: There is enough variety in your sentences. That\'s great!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Consecutive sentences'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/consecutive-sentences-check/',
                        ],
                    ],
                ],
            ],
            'subheading_distribution' => [
                'success' => [
                    'text' => $this->l('[link_support]: You are not using any subheadings, but your text is short enough and probably doesn\'t need them'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Subheading distribution'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/subheading-distribution-check/',
                        ],
                    ],
                ],
                'good' => [
                    'text' => $this->l('[link_support]: Great job!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Subheading distribution'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/subheading-distribution-check/',
                        ],
                    ],
                ],
            ],
            'transition_words' => [
                'error' => [
                    'text' => $this->l('[link_support]: None of the sentences contain transition words. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Transition words'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/transition-words-check/',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Use some'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/transition-words/',
                        ],
                    ],
                ],
                'too_little' => [
                    'text' => $this->l('[link_support]: Only [count] of the sentences contain them. This is not enough. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Transition words'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/transition-words-check/',
                        ],
                        '[count]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Use more transition words'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/transition-words/',
                        ],
                    ],
                ],
                'little' => [
                    'text' => $this->l('[link_support]: Only [count] of the sentences contain them. This is not enough. [link_doc].'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Transition words'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/transition-words-check/',
                        ],
                        '[count]' => [
                            'type' => 'number',
                            'number' => '0',
                        ],
                        '[link_doc]' => [
                            'text' => $this->l('Use more transition words'),
                            'type' => 'link',
                            'key' => 'doc',
                            'link' => 'https://yoast.com/academy/seo-copywriting-training/transition-words/',
                        ],
                    ],
                ],
                'success' => [
                    'text' => $this->l('[link_support]: Well done!'),
                    'short_code' => [
                        '[link_support]' => [
                            'text' => $this->l('Transition words'),
                            'type' => 'link',
                            'key' => '',
                            'link' => 'https://yoast.com/wordpress/plugins/seo/transition-words-check/',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * analysis_types.
     *
     * @return array
     */
    public function analysis_types()
    {
        // Should make key the same "type" of each item
        return [
            'error' => [
                'title' => $this->l('Problems'),
                'type' => 'error',
            ],
            'waring' => [
                'title' => $this->l('Implements'),
                'type' => 'warning',
            ],
            'success' => [
                'title' => $this->l('Good results'),
                'type' => 'success',
            ],
        ];
    }

    /**
     * key_phrase_input.
     *
     * @param string $type : cms, product
     * @param int $id
     * @param object $context
     *
     * @return array
     */
    public function key_phrase_input($type, $id = null, $context = null)
    {
        $seo_data = [];
        if ('product' == $type && $id) {
            $seo_data = EtsSeoProduct::getSeoProduct($id, $context);
        } elseif ('cms' == $type && $id) {
            $seo_data = EtsSeoCms::getSeoCms($id, $context);
        } elseif ('meta' == $type && $id) {
            $seo_data = EtsSeoMeta::getSeoMeta($id, $context);
        } elseif ('category' == $type && $id) {
            $seo_data = EtsSeoCategory::getSeoCategory($id, $context);
        } elseif ('cms_category' == $type && $id) {
            $seo_data = EtsSeoCmsCategory::getSeoCmsCategory($id, $context);
        } elseif ('manufacturer' == $type && $id) {
            $seo_data = EtsSeoManufacturer::getSeoManufacturer($id, $context);
        } elseif ('supplier' == $type && $id) {
            $seo_data = EtsSeoSupplier::getSeoSupplier($id, $context);
        }
        $key_phrase = [];
        $minor_key_phrase = [];
        $social_input = [];
        if ($seo_data) {
            foreach ($seo_data as $item) {
                $key_phrase[$item['id_lang']] = $item['key_phrase'];
                $minor_key_phrase[$item['id_lang']] = $item['minor_key_phrase'];
                $social_input['social_title'][$item['id_lang']] = $item['social_title'];
                $social_input['social_desc'][$item['id_lang']] = $item['social_desc'];
                $social_input['social_img'][$item['id_lang']] = $item['social_img'];
            }
        }

        return [
            'focus_keyphrase' => [
                'label' => $this->l('Focus keyphrase (keyword)'),
                'name' => 'key_phrase',
                'id' => 'ets_seo_focus_keyphrase',
                'value' => $key_phrase,
            ],
            'minor_keyphrase' => [
                'label' => $this->l('Related keyphrases (keywords)'),
                'name' => 'minor_keyphrase',
                'id' => 'ets_seo_minor_keyphrase',
                'value' => $minor_key_phrase,
            ],
            'social_title' => [
                'label' => $this->l('Social title'),
                'name' => 'social_title',
                'id' => 'ets_seo_social_title',
                'value' => isset($social_input['social_title']) ? $social_input['social_title'] : [],
            ],
            'social_desc' => [
                'label' => $this->l('Social description'),
                'name' => 'social_desc',
                'id' => 'ets_seo_social_desc',
                'value' => isset($social_input['social_desc']) ? $social_input['social_desc'] : [],
            ],
            'social_img' => [
                'label' => $this->l('Social image'),
                'name' => 'social_img',
                'id' => 'ets_seo_social_img',
                'value' => isset($social_input['social_img']) ? $social_input['social_img'] : [],
            ],
        ];
    }

    /**
     * seo_advanced.
     *
     * @param string $type : product, cms, meta
     * @param int $id
     * @param object $context
     *
     * @return array
     */
    public function seo_advanced($type, $id, $context)
    {
        $seo_data = null;
        $config_allow_search = '';
        $indexLabel = $this->l('Allow search engines to show this Post in search results?');
        $followLabel = $this->l('Should search engines follow links on this Product?');
        $defaultIndexLabel = $this->l('Use default behavior');
        switch ($type) {
            case 'product':
                $indexLabel = $this->l('Allow search engines to show this Product page in search results?');
                $followLabel = $this->l('Should search engines follow links on this Product?');
                $defaultIndexLabel = $this->l('Use default behavior ');
                $config_allow_search = (int) Configuration::get('ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT');
                if ((int) $id) {
                    $seo_data = EtsSeoProduct::getSeoProduct($id, $context);
                }
                break;
            case 'cms':
                $indexLabel = $this->l('Allow search engines to show this CMS page in search results?');
                $followLabel = $this->l('Should search engines follow links on this CMS?');
                $defaultIndexLabel = $this->l('Use default behavior');
                $config_allow_search = (int) Configuration::get('ETS_SEO_CMS_SHOW_IN_SEARCH_RESULT');
                if ((int) $id) {
                    $seo_data = EtsSeoCms::getSeoCms($id, $context);
                }
                break;
            case 'meta':
                $indexLabel = $this->l('Allow search engines to show this Meta page in search results?');
                $followLabel = $this->l('Should search engines follow links on this Meta?');
                $defaultIndexLabel = $this->l('Use default behavior');
                $config_allow_search = (int) Configuration::get('ETS_SEO_META_SHOW_IN_SEARCH_RESULT');
                if ((int) $id) {
                    $seo_data = EtsSeoMeta::getSeoMeta($id, $context);
                }
                break;
            case 'category':
                $indexLabel = $this->l('Allow search engines to show this Category page in search results?');
                $followLabel = $this->l('Should search engines follow links on this Category?');
                $defaultIndexLabel = $this->l('Use default behavior');
                $config_allow_search = (int) Configuration::get('ETS_SEO_CATEGORY_SHOW_IN_SEARCH_RESULT');
                if ((int) $id) {
                    $seo_data = EtsSeoCategory::getSeoCategory($id, $context);
                }
                break;
            case 'cms_category':
                $indexLabel = $this->l('Allow search engines to show this CMS category page in search results?');
                $followLabel = $this->l('Should search engines follow links on this CMS category?');
                $defaultIndexLabel = $this->l('Use default behavior');
                $config_allow_search = (int) Configuration::get('ETS_SEO_CMS_CATE_SHOW_IN_SEARCH_RESULT');
                if ((int) $id) {
                    $seo_data = EtsSeoCmsCategory::getSeoCmsCategory($id, $context);
                }
                break;
            case 'manufacturer':
                $indexLabel = $this->l('Allow search engines to show this Brand page in search results?');
                $followLabel = $this->l('Should search engines follow links on this Brand?');
                $defaultIndexLabel = $this->l('Use default behavior');
                $config_allow_search = (int) Configuration::get('ETS_SEO_MANUFACTURER_SHOW_IN_SEARCH_RESULT');
                if ((int) $id) {
                    $seo_data = EtsSeoManufacturer::getSeoManufacturer($id, $context);
                }
                break;
            case 'supplier':
                $indexLabel = $this->l('Allow search engines to show this Supplier page in search results?');
                $followLabel = $this->l('Should search engines follow links on this Supplier?');
                $defaultIndexLabel = $this->l('Use default behavior');
                $config_allow_search = (int) Configuration::get('ETS_SEO_SUPPLIER_SHOW_IN_SEARCH_RESULT');
                if ((int) $id) {
                    $seo_data = EtsSeoSupplier::getSeoSupplier($id, $context);
                }
                break;
        }
        $data = [
            'allow_search' => [],
            'allow_flw_link' => [],
            'meta_robots_adv' => [],
            'canonical_url' => [],
        ];
        if ($seo_data) {
            foreach ($seo_data as $seo) {
                foreach ($data as $key => $value) {
                    $data[$key][$seo['id_lang']] = $seo[$key];
                }
            }
        }
        $indexOptions = [
            [
                'label' => $defaultIndexLabel,
                'value' => 2,
                'default_option' => true,
                'suffix_label' => $config_allow_search ? $this->l('(Yes)') : $this->l('(No)'),
            ],
            [
                'label' => $this->l('Yes'),
                'value' => 1,
            ],
            [
                'label' => $this->l('No'),
                'value' => 0,
            ],
        ];
        if ('meta' == $type) {
            unset($indexOptions[0]);
        }

        return [
            'allow_search' => [
                'label' => $indexLabel,
                'id' => 'ets_seo_allow_search_engine_show_post',
                'type' => 'select',
                'config_value' => $config_allow_search,
                'selected' => $data['allow_search'],
                'link_default' => $this->context->link->getAdminLink('AdminEtsSeoSearchAppearanceContentType', true),
                'options' => $indexOptions,
            ],
            'allow_flw_link' => [
                'label' => $followLabel,
                'id' => 'ets_seo_allow_search_engine_follow_links',
                'type' => 'radio',
                'checked' => $data['allow_flw_link'],
                'options' => [
                    [
                        'label' => $this->l('Yes'),
                        'value' => 1,
                        'id' => 'ets_seo_allow_search_engine_follow_links_yes',
                    ],
                    [
                        'label' => $this->l('No'),
                        'value' => 0,
                        'id' => 'ets_seo_allow_search_engine_follow_links_no',
                    ],
                ],
            ],
            'meta_robots_adv' => [
                'label' => $this->l('Meta robots advanced'),
                'id' => 'ets_seo_meta_robots_advanced',
                'type' => 'select2',
                'options' => [
                    [
                        'label' => $this->l('Site-wide default'),
                        'value' => '',
                    ],
                    [
                        'label' => $this->l('None'),
                        'value' => 'none',
                    ],
                    [
                        'label' => $this->l('No Image index'),
                        'value' => 'noimageindex',
                    ],
                    [
                        'label' => $this->l('No Archive'),
                        'value' => 'noarchive',
                    ],
                    [
                        'label' => $this->l('No Snippet'),
                        'value' => 'nosnippet',
                    ],
                ],
                'selected' => $data['meta_robots_adv'],
                'multiple' => true,
                'desc' => $this->l('Advanced meta robots settings for this page.'),
            ],
            'canonical_url' => [
                'label' => $this->l('Canonical URL'),
                'id' => 'ets_seo_canonical_url',
                'type' => 'input_text',
                'value' => $data['canonical_url'],
                'desc' => $this->l('The canonical URL that this page should point to. Leave empty to default to current page link. Cross domain canonical (Opens in a new browser tab) supported too..'),
            ],
        ];
    }

    public function transition_words()
    {
        return [
            'en' => [
                'illustration' => 'thus, for example, for instance, namely, to illustrate, in other words, in particular, specifically, such as',
                'contrast' => 'on the contrary, most importantly, contrarily, notwithstanding, but, however, nevertheless, in spite of, in contrast, yet, on one hand, on the other hand, rather, or, nor, conversely, at the same time, while this may be true',
                'addition' => 'and, in addition to, furthermore, moreover, besides, than, too, also, both-and, another, equally important, first, second, etc., again, further, last, finally, not only-but also, as well as, in the second place, next, likewise, similarly, in fact, as a result, consequently, in the same way, for example, for instance, however, thus, therefore, otherwise',
                'time' => 'after, afterward, before, then, once, next, last, at last, at length, first, second, etc., at first, formerly, rarely, usually, another, finally, soon, meanwhile, at the same time, for a minute, hour, day, etc., during the morning, day, week, etc., most important, later, ordinarily, to begin with, afterwards, generally, in order to, subsequently, previously, in the meantime, immediately, eventually, concurrently, simultaneously',
                'space' => 'at the left, at the right, in the center, on the side, along the edge, on top, below, beneath, under, around, above, over, straight ahead, at the top, at the bottom, surrounding, opposite, at the rear, at the front, in front of, beside, behind, next to, nearby, in the distance, beyond, in the forefront, in the foreground, within sight, out of sight, across, under, nearer, adjacent, in the background',
                'concession' => 'although, at any rate, at least, still, thought, even though, granted that, while it may be true, in spite of, of course',
                'similarity_or_comparison' => 'similarly, likewise, in like fashion, in like manner, analogous to',
                'emphasis' => 'above all, indeed, truly, of course, certainly, surely, in fact, really, in truth, again, besides, also, furthermore, in addition',
                'details' => 'specifically, especially, in particular, to explain, to list, to enumerate, in detail, namely, including',
                'examples' => 'for example, for instance, to illustrate, thus, in other words, as an illustration, in particular',
                'consequence_or_result' => 'so that, with the result that, thus, consequently, hence, accordingly, for this reason, therefore, so, because, since, due to, as a result, in other words, then',
                'summary' => 'therefore, finally, consequently, thus, in short, in conclusion, in brief, as a result, accordingly',
                'suggestion' => 'for this purpose, to this end, with this in mind, with this purpose in mind, therefore.',
            ],
        ];
    }

    /**
     * @return array
     *
     * @throws PrestaShopException
     */
    public function get_menus()
    {
        return [
            'AdminEtsSeoGeneralDashboard' => [
                'title' => $this->l('Dashboard'),
                'origin' => 'Dashboard',
                'controller' => 'AdminEtsSeoGeneralDashboard',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoGeneralDashboard', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-dashboard',
                'has_sub' => true,
            ],
            'AdminEtsSeoUrlAndRemoveId' => [
                'title' => $this->l('SEO URLs'),
                'origin' => 'SEO URLs',
                'controller' => 'AdminEtsSeoUrlAndRemoveId',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoUrlAndRemoveId', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-seo-urls',
                'has_sub' => true,
            ],
            'AdminEtsSeoDuplicateUrl' => [
                'title' => $this->l('Check duplicate URLs'),
                'origin' => 'Check duplicate URLs',
                'controller' => 'AdminEtsSeoDuplicateUrl',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoDuplicateUrl', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-duplicate-url',
                'parent_controller' => 'AdminEtsSeoUrlAndRemoveId',
            ],
            'AdminEtsSeoUrlRedirect' => [
                'title' => $this->l('URL redirects'),
                'origin' => 'URL redirects',
                'controller' => 'AdminEtsSeoUrlRedirect',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoUrlRedirect', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-redirect',
                'parent_controller' => 'AdminEtsSeoUrlAndRemoveId',
            ],
            'AdminEtsSeoNotFoundUrl' => [
                'title' => $this->l('404 Monitor'),
                'origin' => '404 Monitor',
                'controller' => 'AdminEtsSeoNotFoundUrl',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoNotFoundUrl', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-not-found',
                'parent_controller' => 'AdminEtsSeoUrlAndRemoveId',
            ],
            'AdminEtsSeoSearchAppearanceSitemap' => [
                'title' => $this->l('Sitemap'),
                'origin' => 'Sitemap',
                'controller' => 'AdminEtsSeoSearchAppearanceSitemap',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSearchAppearanceSitemap', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-sitemap',
                'has_sub' => true,
            ],
            'AdminEtsSeoSearchAppearanceRSS' => [
                'title' => $this->l('RSS'),
                'origin' => 'RSS',
                'controller' => 'AdminEtsSeoSearchAppearanceRSS',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSearchAppearanceRSS', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-rss',
                'has_sub' => true,
            ],
            'AdminEtsSeoFileEditor' => [
                'title' => $this->l('Robots.txt'),
                'origin' => 'Robots.txt',
                'controller' => 'AdminEtsSeoFileEditor',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoFileEditor', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-editor',
                'has_sub' => true,
            ],
            'AdminEtsSeoRatingSnippet' => [
                'title' => $this->l('Rating / snippet'),
                'origin' => 'Rating / snippet',
                'controller' => 'AdminEtsSeoRatingSnippet',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoRatingSnippet', true),
                'icon' => 'star',
                'menu_icon' => 'menu-icon-rating-snippet',
                'has_sub' => true,
            ],

            'AdminEtsSeoRating' => [
                'title' => $this->l('Ratings'),
                'origin' => 'Ratings',
                'controller' => 'AdminEtsSeoRating',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoRating', true),
                'icon' => 'star',
                'menu_icon' => 'menu-icon-rating',
                'parent_controller' => 'AdminEtsSeoRatingSnippet',
            ],
            'AdminEtsSeoBreadcrumb' => [
                'title' => $this->l('Breadcrumbs'),
                'origin' => 'Breadcrumbs',
                'controller' => 'AdminEtsSeoBreadcrumb',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoBreadcrumb', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-breadcrumb',
                'parent_controller' => 'AdminEtsSeoRatingSnippet',
            ],
            'AdminEtsSeoAuthority' => [
                'title' => $this->l('Authority'),
                'origin' => 'Authority',
                'controller' => 'AdminEtsSeoAuthority',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoAuthority', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-authority',
                'parent_controller' => 'AdminEtsSeoRatingSnippet',
            ],
            'AdminEtsSeoSearchAppearanceContentType' => [
                'title' => $this->l('Meta templates'),
                'origin' => 'Meta templates',
                'controller' => 'AdminEtsSeoSearchAppearanceContentType',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSearchAppearanceContentType', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-meta-template',
                'has_sub' => true,
            ],
            'AdminEtsSeoSocial' => [
                'title' => $this->l('Socials'),
                'origin' => 'Socials',
                'controller' => 'AdminEtsSeoSocial',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSocial', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-social',
                'has_sub' => true,
            ],

            'AdminEtsSeoTraffic' => [
                'title' => $this->l('Traffic'),
                'origin' => 'Traffic',
                'controller' => 'AdminEtsSeoTraffic',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoTraffic', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-traffic',
                'has_sub' => true,
            ],
            'AdminEtsSeoSettings' => [
                'title' => $this->l('Settings'),
                'origin' => 'Settings',
                'controller' => 'AdminEtsSeoSettings',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSettings', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-general-setting',
                'has_sub' => true,
            ],
            'AdminEtsSeoSearchAppearanceGeneral' => [
                'title' => $this->l('General'),
                'origin' => 'General',
                'controller' => 'AdminEtsSeoSearchAppearanceGeneral',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSearchAppearanceGeneral', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-setting',
                'parent_controller' => 'AdminEtsSeoSettings',
            ],
            'AdminEtsSeoCronjobSetting' => [
                'title' => $this->l('Cronjob'),
                'origin' => 'Cronjob',
                'controller' => 'AdminEtsSeoCronjobSetting',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoCronjobSetting', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-cronjob',
                'parent_controller' => 'AdminEtsSeoSettings',
            ],
            'AdminEtsSeoImportExport' => [
                'title' => $this->l('Backup'),
                'origin' => 'Backup',
                'controller' => 'AdminEtsSeoImportExport',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoImportExport', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-import-export',
                'parent_controller' => 'AdminEtsSeoSettings',
            ],
            'AdminEtsSeoChatGpt' => [
                'title' => $this->l('ChatGPT'),
                'origin' => 'ChatGPT',
                'controller' => 'AdminEtsSeoChatGpt',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoChatGpt', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-chat-gpt',
                'parent_controller' => 'AdminEtsSeoSettings',
            ],
            'AdminEtsSeoSocialAccount' => [
                'title' => $this->l('Social profiles'),
                'origin' => 'Social profiles',
                'controller' => 'AdminEtsSeoSocialAccount',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSocialAccount', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-account',
                'parent_controller' => 'AdminEtsSeoSocial',
            ],
            'AdminEtsSeoSocialFacebook' => [
                'title' => $this->l('Facebook'),
                'origin' => 'Facebook',
                'controller' => 'AdminEtsSeoSocialFacebook',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSocialFacebook', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-facebook',
                'parent_controller' => 'AdminEtsSeoSocial',
            ],
            'AdminEtsSeoSocialTwitter' => [
                'title' => $this->l('X'),
                'origin' => 'X',
                'controller' => 'AdminEtsSeoSocialTwitter',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSocialTwitter', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-twitter',
                'parent_controller' => 'AdminEtsSeoSocial',
            ],
            'AdminEtsSeoSocialPinterest' => [
                'title' => $this->l('Pinterest'),
                'origin' => 'Pinterest',
                'controller' => 'AdminEtsSeoSocialPinterest',
                'link' => $this->context->link->getAdminLink('AdminEtsSeoSocialPinterest', true),
                'icon' => 'code',
                'menu_icon' => 'menu-icon-pinterest',
                'parent_controller' => 'AdminEtsSeoSocial',
            ],
        ];
    }

    public function restoreTrafficSeoTabs()
    {
        $languages = Language::getLanguages(false);
        // Set tab Traffic seo
        $idTabParentMeta = Tab::getIdFromClassName('AdminParentMeta');

        if ($idTabParentMeta) {
            // Enable meta tab
            $parentMeta = new Tab($idTabParentMeta);
            $parentMeta->active = true;
            $parentMeta->save();

            foreach ($this->traffic_seo_tabs() as $t) {
                $idTabMeta = Tab::getIdFromClassName($t);
                if ($idTabMeta) {
                    $trafficSeo = new Tab($idTabMeta);
                    $trafficSeo->id_parent = $idTabParentMeta;
                    if ('AdminMeta' == $t) {
                        foreach ($languages as $lang) {
                            $oldName = Configuration::get('ETS_SEO_SEO_AND_URL_NAME', $lang['id_lang']);
                            $trafficSeo->name[$lang['id_lang']] = $oldName ? $oldName : $this->l('SEO and URLs');
                        }
                    }

                    $trafficSeo->save();
                }
            }
        }

        return true;
    }

    /**
     * traffic_seo_tabs.
     *
     * @return array
     */
    public function traffic_seo_tabs()
    {
        return ['AdminMeta', 'AdminSearchEngines', 'AdminReferrers'];
    }

    public function fields_config()
    {
        /** @var Ets_Seo $module */
        $module = Module::getInstanceByName('ets_seo');
        return [
            'hidden_configs' => [
                'ETS_SEO_ENABLE_RECORD_404_REQUESTS' => [
                    'type' => 'bool',
                    'default' => 0,
                ],
            ],
            'general_featured' => [
                'ETS_SEO_ENABLE_ANALISYS' => [
                    'title' => $this->l('SEO analysis'),
                    'hint' => $this->l('Hint'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_ENABLE_READABILITY' => [
                    'title' => $this->l('Readability analysis'),
                    'hint' => $this->l('Readability analysis'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],
            ],
            'chat_gpt' => [
                'ETS_SEO_CHAT_GPT_ENABLE' => [
                    'title' => $this->l('Enable ChatGPT'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_CHAT_GPT_MODEL' => [
                    'title' => $this->l('ChatGPT Model'),
                    'validation' => 'isString',
                    'type' => 'select',
                    'required' => true,
                    'default' => 'gpt-4o',
                    'form_group_class' => 'ets-seo-chatgpt-model ets-seo-toggle-parent-enable-chatgpt-model'.(Tools::getValue('ETS_SEO_CHAT_GPT_ENABLE',Configuration::get('ETS_SEO_CHAT_GPT_ENABLE')) ? '': ' hide'),
                    'identifier' => 'value',
                    'list' => $module->getListModels(),
                ],
                'ETS_SEO_CHAT_GPT_API_TOKEN' => [
                    'title' => $this->l('API key'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'default' => '',
                    'desc' => '<p class="help-block"><a target="_blank" rel="noreferrer noopener" href="https://platform.openai.com/account/api-keys">' . $this->l('How to get API key') . '</a></p>',
                    'no_multishop_checkbox' => true,
                ],
            ],
            'ps_extra' => [
                'ETS_SEO_ENABLE_REMOVE_ID_IN_URL' => [
                    'title' => $this->l('Remove ID in URL'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                ],
                'ETS_SEO_ENABLE_REMOVE_LANG_CODE_IN_URL' => [
                    'title' => $this->l('Remove ISO code in URL for default language'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                ],
                'ETS_SEO_ENABLE_REMOVE_ATTR_ALIAS' => [
                    'title' => $this->l('Remove attribute alias in URL'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                ],
                'ETS_SEO_ENABLE_REMOVE_ID_ATTR_ALIAS' => [
                    'title' => $this->l('Remove ID attribute alias in URL'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                ],
                'ETS_SEO_ENABLE_REDRECT_NOTFOUND' => [
                    'title' => $this->l('Redirect to new url'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                ],
                'ETS_SEO_REDIRECT_STATUS_CODE' => [
                    'title' => $this->l('Redirect type'),
                    'validation' => 'isInt',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => [
                        [
                            'name' => $this->l('302 Moved Temporarily (recommended while setting up your store)'),
                            'value' => '302',
                        ],
                        [
                            'name' => $this->l('301 Moved Permanently (recommended once you have gone live)'),
                            'value' => '301',
                        ],
                    ],
                    'default' => '302',
                ],
            ],
            'general_tool' => [
                'ETS_SEO_GOOGLE_VERIFY_CODE' => [
                    'title' => $this->l('Google verification code'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'desc' => $this->l('Get your Google verification code in') . ' ' . $this->getLinkDesc('Google Search Console', 'https://www.google.com/webmasters/verification/verification'),
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_BING_VERIFY_CODE' => [
                    'title' => $this->l('Bing verification code'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'desc' => $this->l('Get your Bing verification code in') . ' ' . $this->getLinkDesc('Bing Webmaster Tools', 'https://www.bing.com/toolbox/webmaster/#/Dashboard/?url=' . $this->context->shop->getBaseURL(true, true)),
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_BAIDU_VERIFY_CODE' => [
                    'title' => $this->l('Baidu verification code'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'desc' => $this->l('Get your Baidu verification code in ') . ' ' . $this->getLinkDesc('Baidu Webmaster Tools', 'https://ziyuan.baidu.com/site/siteadd'),
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_YANDEX_VERIFY_CODE' => [
                    'title' => $this->l('Yandex verification code'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'desc' => $this->l('Get your Yandex verification code in') . ' ' . $this->getLinkDesc('Yandex Webmaster Tools', 'https://webmaster.yandex.com/sites/add/'),
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_VERIFIED_BY_USING_OTHER_METHODS' => [
                    'title' => '',
                    'validation' => 'isInt',
                    'type' => 'checkbox',
                    'choices' => [
                        1 => $this->l('I have verified my website using other verification methods'),
                    ],
                    'no_multishop_checkbox' => true,
                ],
            ],
            'search_general_separator' => [
                'ETS_SEO_TITLE_SEPARATOR' => [
                    'title' => $this->l('Title separator'),
                    'type' => 'radio',
                    'validation' => 'isCleanHtml',
                    'choices' => [
                        '-' => '-',
                        '–' => '–',
                        ':' => ':',
                        '·' => '·',
                        '•' => '•',
                        '*' => '*',
                        '⋆' => '⋆',
                        '|' => '|',
                        '~' => '~',
                        '«' => '«',
                        '»' => '»',
                        '&lt;' => '<',
                        '&gt;' => '>',
                    ],
                    'default' => '|',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_ENABLE_AUTO_ANALYSIS' => [
                    'title' => $this->l('Enable auto analysis'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],
            ],
//            'translation_setting' => [
//                'ETS_SEO_ENABLE_NEW_TRANS' => [
//                    'title' => $this->l('Use new translation system'),
//                    'validation' => 'isBool',
//                    'cast' => 'intval',
//                    'type' => 'bool',
//                    'default' => 1,
//                    'no_multishop_checkbox' => true,
//                ],
//            ],
            'search_general' => [
                'ETS_SEO_SITE_OF_PERSON_OR_COMP' => [
                    'title' => $this->l('Choose whether the site represents an organization or a person'),
                    'validation' => 'isString',
                    'type' => 'select',
                    'value' => 'COMPANY',
                    'default' => 'COMPANY',
                    'identifier' => 'value',
                    'list' => [
                        [
                            'name' => $this->l('Organization'),
                            'value' => 'COMPANY',
                        ],
                        [
                            'name' => $this->l('Person'),
                            'value' => 'PERSON',
                        ],
                    ],
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITE_ORIG_NAME' => [
                    'title' => $this->l('Organization name'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'default' => (string) Configuration::get('PS_SHOP_NAME'),
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITE_ORIG_LOGO' => [
                    'title' => $this->l('Organization logo'),
                    'type' => 'file',
                    'name' => 'ETS_SEO_SITE_ORIG_LOGO',
                    'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %sMb'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITE_PERSON_NAME' => [
                    'title' => $this->l('Name'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITE_PERSON_AVATAR' => [
                    'title' => $this->l('Person logo / avatar'),
                    'type' => 'file',
                    'name' => 'ETS_SEO_SITE_PERSON_AVATAR',
                    'desc' => sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %sMb'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
                    'no_multishop_checkbox' => true,
                ],
            ],

            'search_content_type_product' => [
                'ETS_SEO_PROD_FORCE_USE_META_TEMPLATE' => [
                    'title' => $this->l('Force to use meta template for product pages'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_PROD_SHOW_IN_SEARCH_RESULT' => [
                    'title' => $this->l('Show Products in search results?'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],

                'ETS_SEO_PROD_META_TITLE' => [
                    'title' => $this->l('Meta title'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use product name'),
                ],
                'ETS_SEO_PROD_META_DESC' => [
                    'title' => $this->l('Meta description'),
                    'validation' => 'isString',
                    'type' => 'textareaLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use product short description'),
                ],
                'ETS_SEO_PROD_META_IMG_ALT' => [
                    'title' => $this->l('Images alt content'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use alt text of image itself (set in PrestaShop TinyMCE editor)'),
                ],
            ],
            'search_content_type_cms' => [
                'ETS_SEO_CMS_FORCE_USE_META_TEMPLATE' => [
                    'title' => $this->l('Force to use meta template for cms pages'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_CMS_SHOW_IN_SEARCH_RESULT' => [
                    'title' => $this->l('Show CMS pages in search results?'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],

                'ETS_SEO_CMS_META_TITLE' => [
                    'title' => $this->l('Meta title'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use page title'),
                ],
                'ETS_SEO_CMS_META_DESC' => [
                    'title' => $this->l('Meta description'),
                    'validation' => 'isString',
                    'type' => 'textareaLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use meta description'),
                ],
            ],
            'search_content_type_cms_cate' => [
                'ETS_SEO_CMS_CATE_FORCE_USE_META_TEMPLATE' => [
                    'title' => $this->l('Force to use meta template for CMS category pages'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_CMS_CATE_SHOW_IN_SEARCH_RESULT' => [
                    'title' => $this->l('Show CMS category pages in search results?'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],

                'ETS_SEO_CMS_CATE_META_TITLE' => [
                    'title' => $this->l('Meta title'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use CMS category name'),
                ],
                'ETS_SEO_CMS_CATE_META_DESC' => [
                    'title' => $this->l('Meta description'),
                    'validation' => 'isString',
                    'type' => 'textareaLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use CMS category description'),
                ],
            ],
            'search_content_type_category' => [
                'ETS_SEO_CATEGORY_FORCE_USE_META_TEMPLATE' => [
                    'title' => $this->l('Force to use meta template for category pages'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_CATEGORY_SHOW_IN_SEARCH_RESULT' => [
                    'title' => $this->l('Show product category pages in search results?'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],

                'ETS_SEO_CATEGORY_META_TITLE' => [
                    'title' => $this->l('Meta title'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use category name'),
                ],
                'ETS_SEO_CATEGORY_META_DESC' => [
                    'title' => $this->l('Meta description'),
                    'validation' => 'isString',
                    'type' => 'textareaLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use category description'),
                ],
                'ETS_SEO_CATEGORY_META_IMG_ALT' => [
                    'title' => $this->l('Images alt content'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use product name (Category products list)'),
                ],
            ],
            'search_content_type_manufacturer' => [
                'ETS_SEO_MANUFACTURER_FORCE_USE_META_TEMPLATE' => [
                    'title' => $this->l('Force to use meta template for brand (manufacturer) pages'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_MANUFACTURER_SHOW_IN_SEARCH_RESULT' => [
                    'title' => $this->l('Show brand (manufacturer) pages in search results?'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_MANUFACTURER_META_TITLE' => [
                    'title' => $this->l('Meta title'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use brand name'),
                ],
                'ETS_SEO_MANUFACTURER_META_DESC' => [
                    'title' => $this->l('Meta description'),
                    'validation' => 'isString',
                    'type' => 'textareaLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use brand short description (if brand short description is empty, long description will be used)'),
                ],
                'ETS_SEO_MANUFACTURER_META_IMG_ALT' => [
                    'title' => $this->l('Images alt content'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use product name (Brand products list)'),
                ],
            ],
            'search_content_type_supplier' => [
                'ETS_SEO_SUPPLIER_FORCE_USE_META_TEMPLATE' => [
                    'title' => $this->l('Force to use meta template for supplier pages'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SUPPLIER_SHOW_IN_SEARCH_RESULT' => [
                    'title' => $this->l('Show supplier pages in search results?'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SUPPLIER_META_TITLE' => [
                    'title' => $this->l('Meta title'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use supplier name'),
                ],
                'ETS_SEO_SUPPLIER_META_DESC' => [
                    'title' => $this->l('Meta description'),
                    'validation' => 'isString',
                    'type' => 'textareaLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use supplier description'),
                ],
                'ETS_SEO_SUPPLIER_META_IMG_ALT' => [
                    'title' => $this->l('Images alt content'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => '',
                    'no_multishop_checkbox' => true,
                    'placeholder' => $this->l('Leave blank to use product name (Supplier products list)'),
                ],
            ],

            'breadcrumb_general' => [
                'ETS_SEO_BREADCRUMB_ENABLED' => [
                    'title' => $this->l('Enable breadcrumbs'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_BREADCRUMB_ANCHOR_TEXT_HOME' => [
                    'title' => $this->l('Anchor text for the Homepage'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => 'Home',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_BREADCRUMB_PREFIX_SEARCH' => [
                    'title' => $this->l('Prefix for Search Page breadcrumbs'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => 'Search result for',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_BREADCRUMB_404_PAGE' => [
                    'title' => $this->l('Breadcrumb for 404 Page'),
                    'validation' => 'isString',
                    'type' => 'textLang',
                    'default' => 'Error 404: Page not found',
                    'no_multishop_checkbox' => true,
                ],
            ],
            'breadcrumb_types' => [
                'ETS_SEO_BREADCRUMB_PRODUCT' => [
                    'title' => $this->l('Middle node to product pages'),
                    'validation' => 'isString',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => [
                        [
                            'name' => $this->l('None'),
                            'value' => '',
                        ],
                        [
                            'name' => $this->l('Product category'),
                            'value' => 'category',
                        ],
                    ],
                    'default' => 'category',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_BREADCRUMB_CMS' => [
                    'title' => $this->l('Middle node to CMS pages'),
                    'validation' => 'isString',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => [
                        [
                            'name' => $this->l('None'),
                            'value' => '',
                        ],
                        [
                            'name' => $this->l('CMS category'),
                            'value' => 'category',
                        ],
                    ],
                    'default' => 'category',
                    'no_multishop_checkbox' => true,
                ],
            ],
            'rating' => [
                'ETS_SEO_RATING_PAGES' => [
                    'title' => $this->l('Enable forced ratings for: '),
                    'validation' => 'isString',
                    'type' => 'text',
                    'default' => 'product,cms,meta,category,cms_category,manufacturer,supplier',
                    'no_multishop_checkbox' => true,
                ],
            ],
            'rss_setting' => [
                'ETS_SEO_RSS_ENABLE' => [
                    'title' => $this->l('Enable RSS feed'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'required' => true,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_RSS_OPTION' => [
                    'title' => $this->l('Pages to include in RSS'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'default' => 'product_category,cms_category,all_products,new_products,special_products,popular_products',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_RSS_CONTENT_BEFORE' => [
                    'title' => $this->l('Content to put before each item in the feed'),
                    'validation' => 'isString',
                    'type' => 'textareaLang',
                    'rows' => 5,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_RSS_CONTENT_AFTER' => [
                    'title' => $this->l('Content to put after each item in the feed'),
                    'validation' => 'isString',
                    'type' => 'textareaLang',
                    'rows' => 5,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_RSS_LINK' => [
                    'title' => $this->l('RSS link(s)'),
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_RSS_POST_LIMIT' => [
                    'title' => $this->l('Item limit (the number of latest added items to display)'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'default' => '100',
                    'desc' => $this->l('Leave blank to display all items (not recommended for large catalog)'),
                    'no_multishop_checkbox' => true,
                ],
            ],
            'sitemap_setting' => [
                'ETS_SEO_CRON_SECURE_HTML' => [
                    'type' => 'custom_html',
                    'html' => '<div class="form-horizontal" style="margin-bottom: 25px;">' . '
            <p class="alert alert-info">' . $this->l('Automatic submit sitemap to Google Search Console need cronjob setup.') . ' <a href="https://prestahero.com/help-center/sitemap-configuration/59-sitemap-configuration-google-console"
" target="_blank">' . $this->l('Configuration guide here') . '</a></p>' . '
          </div>',
                ],
                'ETS_SEO_ENABLE_XML_SITEMAP' => [
                    'title' => $this->l('Enable sitemaps'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_PRIMARY' => [
                    'title' => $this->l('Primary sitemap'),
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_LANG' => [
                    'title' => $this->l('Sitemap by languages'),
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_PRIORITY' => [
                    'title' => $this->l('Priority / Change frequency'),
                    'validation' => 'isUnsignedFloat',
                    'type' => 'text',
                    'default' => 0.5,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_OPTION' => [
                    'title' => $this->l('Pages to include in sitemap'),
                    'type' => 'text',
                    'required' => 1,
                    'default' => 'product,category,cms,cms_category,manufacturer,supplier,meta,blog',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_PROD_SITEMAP_LIMIT' => [
                    'title' => $this->l('Number product per page in sitemap pagination'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'default' => '250',
                    'no_multishop_checkbox' => true,
                    'desc' => $this->l('Leave blank to include all products in one sitemap (not recommended for large catalog)'),
                ],
            ],
            'sitemap_value' => [
                'ETS_SEO_SITEMAP_PRIORITY_PRODUCT' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 0.9,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_PRIORITY_CATEGORY' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 0.8,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_PRIORITY_CMS' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 0.1,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_PRIORITY_CMS_CATEGORY' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 0.1,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_PRIORITY_META' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 0.1,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_PRIORITY_SUPPLIER' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 0.1,
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_PRIORITY_MANUFACTURER' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 0.1,
                    'no_multishop_checkbox' => true,
                ],
            ],
            'sitemap_freq' => [
                'ETS_SEO_SITEMAP_FREQ_PRODUCT' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 'weekly',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_FREQ_CATEGORY' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 'weekly',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_FREQ_CMS' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 'weekly',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_FREQ_CMS_CATEGORY' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 'weekly',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_FREQ_META' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 'weekly',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_FREQ_SUPPLIER' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 'weekly',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_SITEMAP_FREQ_MANUFACTURER' => [
                    'title' => '',
                    'type' => 'text',
                    'default' => 'weekly',
                    'no_multishop_checkbox' => true,
                ],
            ],
            'social_account' => [
                'ETS_SEO_URL_FACEBOOK' => [
                    'title' => $this->l('Facebook page URL'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_URL_TWITTER' => [
                    'title' => $this->l('Twitter Username'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_URL_INSTA' => [
                    'title' => $this->l('Instagram URL'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_URL_LINKEDIN' => [
                    'title' => $this->l('LinkedIn URL'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_URL_MYSPACE' => [
                    'title' => $this->l('Myspace URL'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_URL_PINTEREST' => [
                    'title' => $this->l('Pinterest URL'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_URL_YOUTUBE' => [
                    'title' => $this->l('YouTube URL'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_URL_WIKI' => [
                    'title' => $this->l('Wikipedia URL'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
            ],
            'facebook_setting' => [
                'ETS_SEO_FACEBOOK_ENABLE_OG' => [
                    'title' => $this->l('Add Open Graph meta data'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'desc' => $this->l('Enable this feature if you want Facebook and other social media to display a preview with images and a text excerpt when a link to your site is shared.'),
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_FACEBOOK_DEFULT_IMG_URL' => [
                    'title' => $this->l('Image URL'),
                    'type' => 'file',
                    'name' => 'ETS_SEO_FACEBOOK_DEFULT_IMG_URL',
                    'no_multishop_checkbox' => true,
                    'desc' => $this->l('This image is used if the post/page being shared does not contain any images.') . ' ' . sprintf($this->l('Accepted formats: jpg, jpeg, png, gif. Limit: %sMb'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
                ],
            ],

            'twitter_setting' => [
                'ETS_SEO_TWITTER_ENABLE_CARD_META' => [
                    'title' => $this->l('Add X card meta data'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 1,
                    'desc' => $this->l('Enable this feature if you want X to display a preview with images and a text excerpt when a link to your site is shared.'),
                    'no_multishop_checkbox' => true,
                ],
                'ETS_SEO_TWITTER_DEFAULT_CARD_TYPE' => [
                    'title' => $this->l('The default card type to use'),
                    'validation' => 'isString',
                    'type' => 'select',
                    'identifier' => 'value',
                    'default' => 'summary_large_image',
                    'list' => [
                        [
                            'name' => $this->l('Summary'),
                            'value' => 'summary',
                        ],
                        [
                            'name' => $this->l('Summary with large image'),
                            'value' => 'summary_large_image',
                        ],
                    ],
                    'no_multishop_checkbox' => true,
                ],
            ],
            'pinterest_setting' => [
                'ETS_SEO_PINTEREST_CONFIRM' => [
                    'title' => $this->l('Pinterest confirmation'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
            ],
            'search_console' => [
                'ETS_SEO_GOOGLE_AUTH_CODE' => [
                    'title' => $this->l('Enter your Google Authorization Code and press the Authenticate button.'),
                    'validation' => 'isString',
                    'type' => 'text',
                    'no_multishop_checkbox' => true,
                ],
            ],
            'robot_txt' => [
                'ETS_SEO_ROBOT_TXT' => [
                    'title' => $this->l('Edit the content of your robots.txt'),
                    'validation' => 'isString',
                    'type' => 'textarea',
                    'rows' => 20,
                    'cols' => 20,
                    'no_multishop_checkbox' => true,
                ],
            ],
            'url_redirect_setting' => [
                'ETS_SEO_ENABLE_URL_REDIRECT' => [
                    'title' => $this->l('Enabled'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                    'default' => 0,
                    'no_multishop_checkbox' => true,
                ],
            ],
        ];
    }

    public function rating_pages()
    {
        return [
            [
                'title' => $this->l('Product page'),
                'value' => 'product',
                'desc' => $this->l('Do not recommended if "Customer comments" module is installed'),
            ],
            [
                'title' => $this->l('Product category page'),
                'value' => 'category',
            ],
            [
                'title' => $this->l('CMS page'),
                'value' => 'cms',
            ],
            [
                'title' => $this->l('CMS category page'),
                'value' => 'cms_category',
            ],
            [
                'title' => $this->l('Brand (Manufacturer) page'),
                'value' => 'manufacturer',
            ],
            [
                'title' => $this->l('Supplier page'),
                'value' => 'supplier',
            ],
            [
                'title' => $this->l('Other pages'),
                'value' => 'meta',
            ],
        ];
    }

    public function installDb()
    {
        $tbl_seo_product = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_product` (
            `id_ets_seo_product` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_product` INT(10) UNSIGNED NOT NULL,
            `id_shop` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `key_phrase` VARCHAR(191) DEFAULT NULL,
            `minor_key_phrase` VARCHAR(191) DEFAULT NULL,
            `allow_search` INT(1) UNSIGNED DEFAULT 2,
            `allow_flw_link` INT(1) UNSIGNED DEFAULT 1,
            `meta_robots_adv` VARCHAR(191) DEFAULT NULL,
            `meta_keywords` VARCHAR(191) DEFAULT NULL,
            `canonical_url` VARCHAR(191) DEFAULT NULL,
            `seo_score` INT(3) UNSIGNED  DEFAULT NULL,
            `readability_score` INT(3) UNSIGNED DEFAULT NULL,
            `score_analysis` TEXT DEFAULT NULL,
            `content_analysis` TEXT DEFAULT NULL,
            `social_title` VARCHAR(191) DEFAULT NULL,
            `social_desc` TEXT DEFAULT NULL,
            `social_img` VARCHAR(191) DEFAULT NULL,
            PRIMARY KEY (`id_ets_seo_product`),
            UNIQUE KEY `ets_seo_psl` (id_product, id_shop, id_lang)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_seo_category = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_category` (
            `id_ets_seo_category` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_category` INT(10) UNSIGNED NOT NULL,
            `id_shop` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `key_phrase` VARCHAR(191) DEFAULT NULL,
            `minor_key_phrase` VARCHAR(191) DEFAULT NULL,
            `allow_search` INT(1) UNSIGNED DEFAULT 2,
            `allow_flw_link` INT(1) UNSIGNED DEFAULT 1,
            `meta_robots_adv` VARCHAR(191) DEFAULT NULL,
            `meta_keywords` VARCHAR(191) DEFAULT NULL,
            `canonical_url` VARCHAR(191) DEFAULT NULL,
            `seo_score` INT(3) UNSIGNED  DEFAULT NULL,
            `readability_score` INT(3) UNSIGNED DEFAULT NULL,
            `score_analysis` TEXT DEFAULT NULL,
            `content_analysis` TEXT DEFAULT NULL,
            `social_title` VARCHAR(191) DEFAULT NULL,
            `social_desc` TEXT DEFAULT NULL,
            `social_img` VARCHAR(191) DEFAULT NULL,
            PRIMARY KEY (`id_ets_seo_category`),
            UNIQUE KEY `ets_seo_csl` (id_category, id_shop, id_lang)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_seo_cms = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_cms` (
            `id_ets_seo_cms` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_cms` INT(10) UNSIGNED NOT NULL,
            `id_shop` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `key_phrase` VARCHAR(191) DEFAULT NULL,
            `minor_key_phrase` VARCHAR(191) DEFAULT NULL,
            `allow_search` INT(1) UNSIGNED DEFAULT 2,
            `allow_flw_link` INT(1) UNSIGNED DEFAULT 1,
            `meta_robots_adv` VARCHAR(191) DEFAULT NULL,
            `meta_keywords` VARCHAR(191) DEFAULT NULL,
            `canonical_url` VARCHAR(191) DEFAULT NULL,
            `seo_score` INT(3) UNSIGNED DEFAULT NULL,
            `readability_score` INT(3) UNSIGNED DEFAULT NULL,
            `score_analysis` TEXT DEFAULT NULL,
            `content_analysis` TEXT DEFAULT NULL,
            `social_title` VARCHAR(191) DEFAULT NULL,
            `social_desc` TEXT DEFAULT NULL,
            `social_img` VARCHAR(191) DEFAULT NULL,
            PRIMARY KEY (`id_ets_seo_cms`),
            UNIQUE KEY `ets_seo_csl` (id_cms, id_shop, id_lang)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_seo_cms_category = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_cms_category` (
            `id_ets_seo_cms_category` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_cms_category` INT(10) UNSIGNED NOT NULL,
            `id_shop` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `key_phrase` VARCHAR(191) DEFAULT NULL,
            `minor_key_phrase` VARCHAR(191) DEFAULT NULL,
            `allow_search` INT(1) UNSIGNED DEFAULT 2,
            `allow_flw_link` INT(1) UNSIGNED DEFAULT 1,
            `meta_robots_adv` VARCHAR(191) DEFAULT NULL,
            `meta_keywords` VARCHAR(191) DEFAULT NULL,
            `canonical_url` VARCHAR(191) DEFAULT NULL,
            `seo_score` INT(3) UNSIGNED DEFAULT NULL,
            `readability_score` INT(3) UNSIGNED DEFAULT NULL,
            `score_analysis` TEXT DEFAULT NULL,
            `content_analysis` TEXT DEFAULT NULL,
            `social_title` VARCHAR(191) DEFAULT NULL,
            `social_desc` TEXT DEFAULT NULL,
            `social_img` VARCHAR(191) DEFAULT NULL,
            PRIMARY KEY (`id_ets_seo_cms_category`),
            UNIQUE KEY `ets_seo_ccsl` (id_cms_category, id_shop, id_lang)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_seo_meta = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_meta` (
            `id_ets_seo_meta` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_meta` INT(10) UNSIGNED NOT NULL,
            `id_shop` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `key_phrase` VARCHAR(191) DEFAULT NULL,
            `minor_key_phrase` VARCHAR(191) DEFAULT NULL,
            `allow_search` INT(1) UNSIGNED DEFAULT 2,
            `allow_flw_link` INT(1) UNSIGNED DEFAULT 1,
            `meta_robots_adv` VARCHAR(191) DEFAULT NULL,
            `meta_keywords` VARCHAR(191) DEFAULT NULL,
            `canonical_url` VARCHAR(191) DEFAULT NULL,
            `seo_score` INT(3) UNSIGNED DEFAULT NULL,
            `readability_score` INT(3) UNSIGNED DEFAULT NULL,
            `score_analysis` TEXT DEFAULT NULL,
            `content_analysis` TEXT DEFAULT NULL,
            `social_title` VARCHAR(191) DEFAULT NULL,
            `social_desc` TEXT DEFAULT NULL,
            `social_img` VARCHAR(191) DEFAULT NULL,
            PRIMARY KEY (`id_ets_seo_meta`),
            UNIQUE KEY `ets_seo_msl` (id_meta, id_shop, id_lang)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_seo_supplier = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_supplier` (
            `id_ets_seo_supplier` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_supplier` INT(10) UNSIGNED NOT NULL,
            `id_shop` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `key_phrase` VARCHAR(191) DEFAULT NULL,
            `minor_key_phrase` VARCHAR(191) DEFAULT NULL,
            `allow_search` INT(1) UNSIGNED DEFAULT 2,
            `allow_flw_link` INT(1) UNSIGNED DEFAULT 1,
            `meta_robots_adv` VARCHAR(191) DEFAULT NULL,
            `meta_keywords` VARCHAR(191) DEFAULT NULL,
            `canonical_url` VARCHAR(191) DEFAULT NULL,
            `seo_score` INT(3) UNSIGNED DEFAULT NULL,
            `readability_score` INT(3) UNSIGNED DEFAULT NULL,
            `score_analysis` TEXT DEFAULT NULL,
            `content_analysis` TEXT DEFAULT NULL,
            `social_title` VARCHAR(191) DEFAULT NULL,
            `social_desc` TEXT DEFAULT NULL,
            `social_img` VARCHAR(191) DEFAULT NULL,
            PRIMARY KEY (`id_ets_seo_supplier`),
            UNIQUE KEY `ets_seo_ssl` (id_supplier, id_shop, id_lang)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_seo_manufacturer = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_manufacturer` (
            `id_ets_seo_manufacturer` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_manufacturer` INT(10) UNSIGNED NOT NULL,
            `id_shop` INT(11) NOT NULL,
            `id_lang` INT(11) NOT NULL,
            `key_phrase` VARCHAR(191) DEFAULT NULL,
            `minor_key_phrase` VARCHAR(191) DEFAULT NULL,
            `allow_search` INT(1) UNSIGNED DEFAULT 2,
            `allow_flw_link` INT(1) UNSIGNED DEFAULT 1,
            `meta_robots_adv` VARCHAR(191) DEFAULT NULL,
            `meta_keywords` VARCHAR(191) DEFAULT NULL,
            `canonical_url` VARCHAR(191) DEFAULT NULL,
            `seo_score` INT(3) UNSIGNED DEFAULT NULL,
            `readability_score` INT(3) UNSIGNED DEFAULT NULL,
            `score_analysis` TEXT DEFAULT NULL,
            `content_analysis` TEXT DEFAULT NULL,
            `social_title` VARCHAR(191) DEFAULT NULL,
            `social_desc` TEXT DEFAULT NULL,
            `social_img` VARCHAR(191) DEFAULT NULL,
            PRIMARY KEY (`id_ets_seo_manufacturer`),
            UNIQUE KEY `ets_seo_msl` (id_manufacturer, id_shop, id_lang)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_seo_url_redirect = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "ets_seo_redirect` (
            `id_ets_seo_redirect` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(191) DEFAULT NULL,
            `url` VARCHAR(191) NOT NULL,
            `target` VARCHAR(191) NOT NULL,
            `type` ENUM('301', '302', '303', '404') DEFAULT NULL,
            `active` INT(1) DEFAULT 1,
            `id_shop` INT(11) NOT NULL,
            PRIMARY KEY (`id_ets_seo_redirect`),
            UNIQUE KEY `ets_seo_url` (`url`, `id_shop`),
            INDEX (`active`,`id_shop`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_rating = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_rating` (
            `id_ets_seo_rating` INT(11) unsigned NOT NULL AUTO_INCREMENT,
            `page_type` VARCHAR(191) NOT NULL,
            `id_page` INT(10) unsigned NOT NULL,
            `enable` INT(1) NOT NULL,
            `average_rating` DOUBLE(4,2) NOT NULL,
            `best_rating` INT(1) DEFAULT NULL,
            `worst_rating` INT(1) DEFAULT NULL,
            `rating_count` INT(10) NOT NULL,
            `id_shop` INT(11) NOT NULL,
            PRIMARY KEY (`id_ets_seo_rating`),
            INDEX (`id_shop`,`enable`),
            UNIQUE KEY `ets_seo_type_id` (page_type, id_page)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_manu_url = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_manufacturer_url` (
            `id_manufacturer` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `link_rewrite` VARCHAR(191) NOT NULL,
            PRIMARY KEY (`id_manufacturer`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_supplier_url = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_supplier_url` (
            `id_supplier` int(11) unsigned NOT NULL,
            `link_rewrite` VARCHAR(191) NOT NULL,
            PRIMARY KEY (`id_supplier`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        $tbl_not_found_url = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_not_found_url` (
         `id_ets_seo_not_found_url` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
         `id_shop` INT NOT NULL , `url` VARCHAR(255) NOT NULL , 
         `referer` VARCHAR(255) NULL DEFAULT NULL , 
         `visit_count` INT NOT NULL , 
         `last_visited_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
          PRIMARY KEY (`id_ets_seo_not_found_url`),
          INDEX (`id_shop`),
          INDEX (`url`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
        $idx_not_found_url = 'ALTER TABLE `' . _DB_PREFIX_ . 'ets_seo_not_found_url` ADD INDEX(`url`)';
        $tbl_gpt_msg = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_gpt_message` (
         `id_ets_seo_gpt_message` bigint(20) NOT NULL AUTO_INCREMENT,
         `message` text NOT NULL,
         `is_chatgpt` tinyint(4) NOT NULL DEFAULT "0",
         `id_parent` bigint(20) NULL DEFAULT NULL,
         `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
         PRIMARY KEY (`id_ets_seo_gpt_message`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
        $tbl_gpt_tpl = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_gpt_template` (
          `id_ets_seo_gpt_template` INT NOT NULL AUTO_INCREMENT, 
          `position` INT NOT NULL, 
          `display_page` VARCHAR(50) NOT NULL,
          PRIMARY KEY (`id_ets_seo_gpt_template`),
          INDEX(`display_page`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';
        $tbl_gpt_tpl_lang = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ets_seo_gpt_template_lang` (
          `id_ets_seo_gpt_template` INT UNSIGNED NOT NULL, 
          `id_lang` INT UNSIGNED NOT NULL, 
          `label` TEXT NULL DEFAULT NULL, 
          `content` TEXT NULL DEFAULT NULL, 
          PRIMARY KEY (
            `id_ets_seo_gpt_template`, `id_lang`
          )
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

        return Db::getInstance()->execute($tbl_seo_product)
            && Db::getInstance()->execute($tbl_seo_category)
            && Db::getInstance()->execute($tbl_seo_cms)
            && Db::getInstance()->execute($tbl_seo_cms_category)
            && Db::getInstance()->execute($tbl_seo_meta)
            && Db::getInstance()->execute($tbl_seo_manufacturer)
            && Db::getInstance()->execute($tbl_seo_supplier)
            && Db::getInstance()->execute($tbl_seo_url_redirect)
            && Db::getInstance()->execute($tbl_rating)
            && Db::getInstance()->execute($tbl_manu_url)
            && Db::getInstance()->execute($tbl_supplier_url)
            && Db::getInstance()->execute($tbl_not_found_url)
            // Add index might fail on some system with small index size (VARCHAR column <= 191)
            // So we ignored return status code of this statement
            && (Db::getInstance()->execute($idx_not_found_url) ?: true)
            && Db::getInstance()->execute($tbl_gpt_msg)
            && Db::getInstance()->execute($tbl_gpt_tpl)
            && Db::getInstance()->execute($tbl_gpt_tpl_lang)
            && $this->createIndexColumnsData();
    }

    public function uninstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_product`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_category`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_cms`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_cms_category`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_meta`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_manufacturer`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_supplier`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_redirect`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_rating`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_manufacturer_url`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_supplier_url`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_not_found_url`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_gpt_message`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_gpt_template`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ets_seo_gpt_template_lang`')
            && $this->dropIndexColumnsData();
    }

    public function createIndexColumnsData()
    {
        try {
            Db::getInstance()->execute('CREATE INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'product_lang`(link_rewrite)');
        } catch (\Exception $e) {
        }
        try {
            Db::getInstance()->execute('CREATE INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'meta_lang`(url_rewrite)');
        } catch (\Exception $e) {
        }
        try {
            Db::getInstance()->execute('CREATE INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'category_lang`(link_rewrite)');
        } catch (\Exception $e) {
        }
        try {
            Db::getInstance()->execute('CREATE INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'cms_lang`(link_rewrite)');
        } catch (\Exception $e) {
        }
        try {
            Db::getInstance()->execute('CREATE INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'cms_category_lang`(link_rewrite)');
        } catch (\Exception $e) {
        }

        return true;
    }

    public function dropIndexColumnsData()
    {
        if (Db::getInstance()->executeS('SHOW KEYS FROM  `' . _DB_PREFIX_ . "product_lang` WHERE Key_name='ets_seo_rr'")) {
            Db::getInstance()->execute('DROP INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'product_lang`');
        }
        if (Db::getInstance()->executeS('SHOW KEYS FROM  `' . _DB_PREFIX_ . "meta_lang` WHERE Key_name='ets_seo_rr'")) {
            Db::getInstance()->execute('DROP INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'meta_lang`');
        }
        if (Db::getInstance()->executeS('SHOW KEYS FROM  `' . _DB_PREFIX_ . "category_lang` WHERE Key_name='ets_seo_rr'")) {
            Db::getInstance()->execute('DROP INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'category_lang`');
        }

        if (Db::getInstance()->executeS('SHOW KEYS FROM  `' . _DB_PREFIX_ . "cms_lang` WHERE Key_name='ets_seo_rr'")) {
            Db::getInstance()->execute('DROP INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'cms_lang`');
        }
        if (Db::getInstance()->executeS('SHOW KEYS FROM  `' . _DB_PREFIX_ . "cms_category_lang` WHERE Key_name='ets_seo_rr'")) {
            Db::getInstance()->execute('DROP INDEX ets_seo_rr ON `' . _DB_PREFIX_ . 'cms_category_lang`');
        }

        return true;
    }

    /**
     * get_meta_codes.
     *
     * @param string|null $type
     * @param array $params
     *
     * @return array
     */
    public function get_meta_codes($type = null, $params = [])
    {
        $post_title = isset($params['post_title']) ? $params['post_title'] : '';
        if (isset($params['description'])) {
            $params['description'] = strip_tags($params['description']);
        }
        $items = [];
        switch ($type) {
            case 'product':
                $items = [
                    '%product-name%' => [
                        'title' => $this->l('Product name'),
                        'code' => '%product-name%',
                        'type' => 'title',
                        'value' => $post_title,
                    ],
                    '%price%' => [
                        'title' => $this->l('Price'),
                        'code' => '%price%',
                        'type' => 'price',
                        'value' => isset($params['price']) ? $params['price'] : '',
                    ],
                    '%discount-price%' => [
                        'title' => $this->l('Discount price'),
                        'code' => '%discount-price%',
                        'type' => 'discount_price',
                        'value' => isset($params['discount_price']) ? $params['discount_price'] : '',
                    ],
                    '%brand%' => [
                        'title' => $this->l('Brand'),
                        'code' => '%brand%',
                        'type' => 'brand',
                        'value' => isset($params['brand']) ? $params['brand'] : '',
                    ],
                    '%category%' => [
                        'title' => $this->l('Product category'),
                        'code' => '%category%',
                        'type' => 'category',
                        'value' => isset($params['category']) ? $params['category'] : '',
                    ],
                    '%ean13%' => [
                        'title' => $this->l('EAN13'),
                        'code' => '%ean13%',
                        'type' => 'ean13',
                        'value' => isset($params['ean13']) ? $params['ean13'] : '',
                    ],
                ];
                if (!isset($params['is_title']) || !$params['is_title']) {
                    $items['%summary%'] = [
                        'title' => $this->l('Summary'),
                        'code' => '%summary%',
                        'type' => 'desc',
                        'value' => isset($params['description_short']) ? $params['description_short'] : '',
                    ];
                    $items['%description%'] = [
                        'title' => $this->l('Description'),
                        'code' => '%description%',
                        'type' => 'long_desc',
                        'value' => isset($params['description']) ? $params['description'] : '',
                    ];
                }
                break;
            case 'category':
                if (!isset($params['is_title']) || !$params['is_title']) {
                    $items = [
                        '%category-name%' => [
                            'title' => $this->l('Category name'),
                            'code' => '%category-name%',
                            'type' => 'title',
                            'value' => $post_title,
                        ],
                        '%description%' => [
                            'title' => $this->l('Description'),
                            'code' => '%description%',
                            'type' => 'desc',
                            'value' => isset($params['description']) ? $params['description'] : '',
                        ],
                    ];
                } else {
                    $items = [
                        '%category-name%' => [
                            'title' => $this->l('Category name'),
                            'code' => '%category-name%',
                            'type' => 'title',
                            'value' => $post_title,
                        ],
                    ];
                }

                break;
            case 'cms':
                $items = [
                    '%cms-title%' => [
                        'title' => $this->l('Title'),
                        'code' => '%cms-title%',
                        'type' => 'title',
                        'value' => $post_title,
                    ],
                    '%cms-category%' => [
                        'title' => $this->l('CMS category'),
                        'code' => '%cms-category%',
                        'type' => 'category',
                        'value' => isset($params['category']) ? $params['category'] : '',
                    ],
                ];
                break;
            case 'cms_category':
                $items = [
                    '%cms-category-title%' => [
                        'title' => $this->l('Title'),
                        'code' => '%cms-category-title%',
                        'type' => 'title',
                        'value' => $post_title,
                    ],
                ];
                if (!isset($params['is_title']) || !$params['is_title']) {
                    $items['%description%'] = [
                        'title' => $this->l('Description'),
                        'code' => '%description%',
                        'type' => 'desc',
                        'value' => isset($params['description']) ? $params['description'] : '',
                    ];
                }
                break;
            case 'meta':
                $items = [
                    '%title%' => [
                        'title' => $this->l('Title'),
                        'code' => '%title%',
                        'type' => 'title',
                        'value' => $post_title,
                    ],
                ];
                break;
            case 'manufacturer':
                if (!isset($params['is_title']) || !$params['is_title']) {
                    $items = [
                        '%brand-name%' => [
                            'title' => $this->l('Brand  (manufacturer) name'),
                            'code' => '%brand-name%',
                            'type' => 'title',
                            'value' => $post_title,
                        ],
                        '%short-description%' => [
                            'title' => $this->l('Short description'),
                            'code' => '%short-description%',
                            'type' => 'desc',
                            'value' => isset($params['description']) ? $params['description'] : '',
                        ],
                        '%description%' => [
                            'title' => $this->l('Description'),
                            'code' => '%description%',
                            'type' => 'desc2',
                            'value' => isset($params['description2']) ? $params['description2'] : '',
                        ],
                    ];
                } else {
                    $items = [
                        '%brand-name%' => [
                            'title' => $this->l('Brand  (manufacturer) name'),
                            'code' => '%brand-name%',
                            'type' => 'title',
                            'value' => $post_title,
                        ],
                    ];
                }
                break;
            case 'supplier':
                if (!isset($params['is_title']) || !$params['is_title']) {
                    $items = [
                        '%supplier-name%' => [
                            'title' => $this->l('Supplier name'),
                            'code' => '%supplier-name%',
                            'type' => 'title',
                            'value' => $post_title,
                        ],
                        '%description%' => [
                            'title' => $this->l('Description'),
                            'code' => '%description%',
                            'type' => 'desc',
                            'value' => isset($params['description']) ? $params['description'] : '',
                        ],
                    ];
                } else {
                    $items = [
                        '%supplier-name%' => [
                            'title' => $this->l('Supplier name'),
                            'code' => '%supplier-name%',
                            'type' => 'title',
                            'value' => $post_title,
                        ],
                    ];
                }
                break;
        }
        $short_codes = [
            '%shop-name%' => [
                'title' => $this->l('Shop name'),
                'code' => '%shop-name%',
                'value' => Configuration::get('PS_SHOP_NAME'),
            ],
            '%separator%' => [
                'title' => $this->l('Separator'),
                'code' => '%separator%',
                'value' => html_entity_decode((string) Configuration::get('ETS_SEO_TITLE_SEPARATOR')),
            ],
        ];
        if (('meta' == $type || !$type) && (isset($params['is_title']) && $params['is_title'])) {
            return [];
        }
        $short_codes = array_merge($short_codes, $items);

        return $short_codes;
    }

    public function list_separators()
    {
        return [
            'dash' => '-',
            'en_dash' => '–',
            'colon' => ':',
            'middle_dot' => '·',
            'bullet' => '•',
            'star' => '*',
            'big_star' => '⋆',
            'vbar' => '|',
            'tilde' => '~',
            'left_angle' => '«',
            'right_angle' => '»',
            'less_than' => '&lt;',
            'greater_than' => '&gt;',
        ];
    }

    public function url_rules()
    {
        return [
            'category_rule' => [
                'rule' => $this->getConfigRule('category_rule', '{id}-{rewrite}'),
                'new_rule' => $this->getConfigRule('category_rule', '{rewrite}', true),
                'desc_rule' => $this->l('Keywords: id* , rewrite , meta_keywords , meta_title'),
                'desc_new_rule' => $this->l('Keywords: id , rewrite* , meta_keywords, parent_rewrite'),
            ],
            'supplier_rule' => [
                'rule' => $this->getConfigRule('supplier_rule', 'supplier/{id}-{rewrite}'),
                'new_rule' => $this->getConfigRule('supplier_rule', 'supplier/{rewrite}', true),
                'desc_rule' => $this->l('Keywords: id* , rewrite , meta_keywords , meta_title'),
                'desc_new_rule' => $this->l('Keywords: id , rewrite* , meta_keywords'),
            ],
            'manufacturer_rule' => [
                'rule' => $this->getConfigRule('manufacturer_rule', 'brand/{id}-{rewrite}'),
                'new_rule' => $this->getConfigRule('manufacturer_rule', 'brand/{rewrite}', true),
                'desc_rule' => $this->l('Keywords: id* , rewrite , meta_keywords , meta_title'),
                'desc_new_rule' => $this->l('Keywords: id , rewrite* , meta_keywords'),
            ],
            'cms_rule' => [
                'rule' => $this->getConfigRule('cms_rule', 'content/{id}-{rewrite}'),
                'new_rule' => $this->getConfigRule('cms_rule', 'content/{rewrite}', true),
                'desc_rule' => $this->l('Keywords: id* , rewrite , meta_keywords , meta_title'),
                'desc_new_rule' => $this->l('Keywords: id , rewrite* , meta_keywords'),
            ],
            'cms_category_rule' => [
                'rule' => $this->getConfigRule('cms_category_rule', 'content/category/{id}-{rewrite}'),
                'new_rule' => $this->getConfigRule('cms_category_rule', 'content/category/{rewrite}', true),
                'desc_rule' => $this->l('Keywords: id* , rewrite , meta_keywords , meta_title'),
                'desc_new_rule' => $this->l('Keywords: id , rewrite* , meta_keywords'),
            ],
            'module' => [
                'rule' => $this->getConfigRule('module', 'module/{module}{/:controller}'),
                'new_rule' => $this->getConfigRule('module', 'module/{module}{/:controller}', true),
                'desc_rule' => $this->l('Keywords: module* , controller*'),
                'desc_new_rule' => $this->l('Keywords: module* , controller*'),
            ],
            'product_rule' => [
                'rule' => $this->getConfigRule('product_rule', '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}.html'),
                'new_rule' => $this->getConfigRule('product_rule', '{category}/{rewrite}', true),
                'desc_rule' => $this->l('Keywords: id* , id_product_attribute* , rewrite* , ean13 , category , categories , reference , meta_keywords , meta_title , manufacturer , supplier , price , tags'),
                'desc_new_rule' => $this->l('Keywords: id , id_product_attribute , rewrite* , ean13 , category, categories , reference , meta_keywords , manufacturer , supplier , price , tags'),
            ],
            /* Must be after the product and category rules in order to avoid conflict */
            'layered_rule' => [
                'rule' => $this->getConfigRule('layered_rule', '{id}-{rewrite}{/:selected_filters}'),
                'new_rule' => $this->getConfigRule('layered_rule', '{rewrite}/filter/{selected_filters}', true),
                'desc_rule' => $this->l('Keywords: id* , selected_filters* , rewrite , meta_keywords , meta_title'),
                'desc_new_rule' => $this->l('Keywords: id , selected_filters* , rewrite* , meta_keywords'),
            ],
        ];
    }

    public function seo_url_schema_configs()
    {
        return [
            'category_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_CATEGORY_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_CATEGORY_RULE',
                'name' => 'ETS_SEO_URL_CATEGORY_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_CATEGORY_RULE',
                'default' => '{id}-{rewrite}',
            ],
            'supplier_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_SUPPLIER_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_SUPPLIER_RULE',
                'name' => 'ETS_SEO_URL_SUPPLIER_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_SUPPLIER_RULE',
                'default' => 'supplier/{id}-{rewrite}',
            ],
            'manufacturer_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_MANUF_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_MANUF_RULE',
                'name' => 'ETS_SEO_URL_MANUF_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_MANUF_RULE',
                'default' => 'brand/{id}-{rewrite}',
            ],
            'cms_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_CMS_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_CMS_RULE',
                'name' => 'ETS_SEO_URL_CMS_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_CMS_RULE',
                'default' => 'content/{id}-{rewrite}',
            ],
            'cms_category_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_CMS_CATEGORY_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_CMS_CATEGORY_RULE',
                'name' => 'ETS_SEO_URL_CMS_CATEGORY_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_CMS_CATEGORY_RULE',
                'default' => 'content/category/{id}-{rewrite}',
            ],
            'module' => [
                'root_name' => 'ETS_SEO_ROOT_URL_MODULE_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_MODULE_RULE',
                'name' => 'ETS_SEO_URL_MODULE_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_MODULE_RULE',
                'default' => 'module/{module}{/:controller}',
            ],
            'product_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_PRODUCT_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_PRODUCT_RULE',
                'name' => 'ETS_SEO_URL_PRODUCT_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_PRODUCT_RULE',
                'default' => '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}.html',
            ],
            'layered_rule' => [
                'root_name' => 'ETS_SEO_ROOT_URL_LAYERED_RULE',
                'old_name' => 'ETS_SEO_OLD_URL_LAYERED_RULE',
                'name' => 'ETS_SEO_URL_LAYERED_RULE',
                'no_id' => 'ETS_SEO_URL_NOID_LAYERED_RULE',
                'default' => '{id}-{rewrite}{/:selected_filters}',
            ],
        ];
    }

    public function rewrite_noid_rules()
    {
        return [
            'category_rule' => [
                'number_param' => 1,
                'allow' => ['rewrite'],
                'keywords' => ['rewrite', 'id', 'meta_keywords', 'meta_title'],
            ],
            'supplier_rule' => [
                'number_param' => 2,
                'allow' => ['rewrite'],
                'keywords' => ['rewrite', 'id', 'meta_keywords', 'meta_title'],
            ],
            'manufacturer_rule' => [
                'number_param' => 2,
                'allow' => ['rewrite'],
                'keywords' => ['rewrite', 'id', 'meta_keywords', 'meta_title'],
            ],
            'cms_rule' => [
                'number_param' => 2,
                'allow' => ['rewrite'],
                'keywords' => ['rewrite', 'id', 'meta_keywords', 'meta_title'],
            ],
            'cms_category_rule' => [
                'number_param' => 3,
                'allow' => ['rewrite'],
                'keywords' => ['rewrite', 'id', 'meta_keywords', 'meta_title'],
            ],
            'module' => [
                'number_param' => 1,
                'allow' => ['module', 'controller'],
                'keywords' => ['module', 'controller'],
            ],
            'product_rule' => [
                'number_param' => 2,
                'allow' => ['category', 'rewrite'],
                'keywords' => ['id', 'category', 'rewrite', 'id_product_attribute',
                    'categories', 'reference', 'meta_keywords', 'meta_title', 'manufacturer', 'supplier', 'price', 'tags', ],
            ],

            'layered_rule' => [
                'number_param' => 3,
                'allow' => ['rewrite', 'selected_filters'],
                'keywords' => ['rewrite', 'selected_filters', 'meta_keywords', 'meta_title'],
            ],
        ];
    }

    public function getConfigRule($rule, $default = null, $no_id = false)
    {
        $config = $this->seo_url_schema_configs();
        if ($no_id) {
            return ($data = Configuration::get($config[$rule]['no_id'])) && (('module' !== $rule && !preg_match('/\{id\}/', $data)) || 'module' == $rule) ? Configuration::get($config[$rule]['no_id']) : $default;
        }

        if (($nearest = Configuration::get($config[$rule]['name'])) && (('module' !== $rule && preg_match('/\{id\}/', $nearest)) || 'module' == $rule)) {
            return $nearest;
        } elseif (($old = Configuration::get($config[$rule]['old_name'])) && (('module' !== $rule && preg_match('/\{id\}/', $old)) || 'module' == $rule)) {
            return $old;
        } elseif (($root = Configuration::get($config[$rule]['root_name'])) && (('module' !== $rule && preg_match('/\{id\}/', $root)) || 'module' == $rule)) {
            return $root;
        }

        return $default;
    }

    public function translateMessages()
    {
        return [
            'avg_rating_required' => $this->l('The average rating is required.'),
            'avg_rating_decimal' => $this->l('The Average rating must be a decimal.'),
            'avg_rating_invalid' => $this->l('The average rating is invalid.'),
            'best_rating_integer' => $this->l('The Best rating must be an integer.'),
            'best_rating_invalid' => $this->l('The Best rating is invalid.'),
            'best_rating_greater_than_avg' => $this->l('The Best rating must be greater than or equal to the average rating.'),
            'worst_rating_integer' => $this->l('The Worst rating must be an integer.'),
            'worst_rating_invalid' => $this->l('The Worst rating is invalid.'),
            'worst_rating_less_than_avg' => $this->l('The Worst rating must be less than or equal to the average rating.'),
            'rating_count_required' => $this->l('The rating count is required.'),
            'rating_count_integer' => $this->l('The rating count must be an integer.'),
        ];
    }

    public function listControllerAction()
    {
        return [
            'AdminCmsContent',
            'AdminMeta',
            'AdminCategories',
            'AdminCmsCategories',
            'AdminManufacturers',
            'AdminSuppliers',
            'AdminProducts',
            'AdminReferrers',
            'AdminSearchEngines',
        ];
    }

    public function getLinkDesc($title, $link)
    {
        if (!$title || !$link) {
            return '';
        }

        return EtsSeoStrHelper::displayText($title, 'a', ['href' => $link, 'target' => '_blank', 'rel' => 'noreferrer noopener']);
    }

    public function getPlaceholderPage($controller, $isCmsCate = false)
    {
        $title = '';
        $desc = '';
        switch ($controller) {
            case 'AdminProducts':
                $title = $this->l('To have a different title from the product name, enter it here.');
                $desc = $this->l('To have a different description than your product summary in search results pages, write it here.');
                break;
            case 'AdminCategories':
                $title = $this->l('To have a different title from the category name, enter it here.');
                $desc = $this->l('To have a different description than your category description in search results pages, write it here.');
                break;
            case 'AdminCmsContent':
                if ($isCmsCate) {
                    $title = $this->l('To have a different title from the CMS category name, enter it here.');
                    $desc = $this->l('To have a different description than your CMS category description in search results pages, write it here.');
                } else {
                    $title = $this->l('To have a different title from the CMS title, enter it here.');
                    $desc = $this->l('To have a different description than your CMS description in search results pages, write it here.');
                }
                break;
            case 'AdminManufacturers':
                $title = $this->l('To have a different title from the brand name, enter it here.');
                $desc = $this->l('To have a different description than your brand short description in search results pages, write it here.');
                break;
            case 'AdminSuppliers':
                $title = $this->l('To have a different title from the supplier name, enter it here.');
                $desc = $this->l('To have a different description than your supplier description in search results pages, write it here.');
                break;
        }

        return [
            'title' => $title,
            'desc' => $desc,
        ];
    }

    public static function getTextLang($text, $lang, $file_name = '')
    {
        $moduleName = 'ets_seo';
        $text2 = preg_replace("/\\\*'/", "\'", $text);
        if (is_array($lang)) {
            $iso_code = $lang['iso_code'];
        } elseif (is_object($lang)) {
            $iso_code = $lang->iso_code;
        } else {
            $language = new Language($lang);
            $iso_code = $language->iso_code;
        }
        $modulePath = rtrim(_PS_MODULE_DIR_, '/') . '/' . $moduleName;
        $fileTransDir = $modulePath . '/translations/' . $iso_code . '.php';
        if (!@file_exists($fileTransDir)) {
            return '';
        }
        $fileContent = Tools::file_get_contents($fileTransDir);
        $strMd5 = md5($text2);
        $keyMd5 = '<{' . $moduleName . '}prestashop>' . ($file_name ? Tools::strtolower($file_name) : $moduleName) . '_' . $strMd5;
        preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
        if ($matches && isset($matches[2])) {
            return str_replace("\'", "'", $matches[2]);
        }

        return '';
    }
    public function getGptTemplates()
    {
        return $this->gptTemplates;
    }

    public static function activeTab($module_name)
    {
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'tab` SET enabled=1 where module ="' . pSQL($module_name) . '"');
    }
}
