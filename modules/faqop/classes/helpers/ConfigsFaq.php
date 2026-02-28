<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class ConfigsFaq
{
    public const BLOCK_TYPES = [
        'list',
        'page',
    ];

    public const SHORTCODE_NAME = 'opfaqblock';

    public const BLOCKS_COOKIE = 'faq_main_grid';

    public const ITEMS_COOKIE = 'faq_items_grid';

    public const ITEMS_PARENT_COOKIE = 'faq_items_parent';

    public const PAGE = 'module-faqop-page';

    public const PAGE_URL = 'faq-page';

    public const PAGE_HOOK = 'displayOnFaqPage';

    public const DEMO_MODE = false;
}
