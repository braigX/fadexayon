<?php
/**
 *   AmbJoliSearch Module : Search for prestashop
 *
 *   @author    Ambris Informatique
 *   @copyright Copyright (c) 2013-2023 Ambris Informatique SARL
 *   @license   Licensed under the EUPL-1.2-or-later
 *
 *   @module     Advanced search (AmbJoliSearch)
 *
 *   @file       ambjolisearch.php
 *
 *   @subject    script principal pour gestion du module (install/config/hook)
 *   Support by mail: support@ambris.com
 */

/* Copied from Drupal search module, except for \x{0}-\x{2f} that has been replaced by \x{0}-\x{2c}\x{2e}-\x{2f} in order to keep the char '-'
*/
define(
    'AMB_PREG_CLASS_SEARCH_EXCLUDE',
    '\x{0}-\x{2c}\x{2e}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}\x{7b}-\x{bf}\x{d7}\x{f7}\x{2b0}-' .
    '\x{385}\x{387}\x{3f6}\x{482}-\x{489}\x{559}-\x{55f}\x{589}-\x{5c7}\x{5f3}-' .
    '\x{61f}\x{640}\x{64b}-\x{65e}\x{66a}-\x{66d}\x{670}\x{6d4}\x{6d6}-\x{6ed}' .
    '\x{6fd}\x{6fe}\x{700}-\x{70f}\x{711}\x{730}-\x{74a}\x{7a6}-\x{7b0}\x{901}-' .
    '\x{903}\x{93c}\x{93e}-\x{94d}\x{951}-\x{954}\x{962}-\x{965}\x{970}\x{981}-' .
    '\x{983}\x{9bc}\x{9be}-\x{9cd}\x{9d7}\x{9e2}\x{9e3}\x{9f2}-\x{a03}\x{a3c}-' .
    '\x{a4d}\x{a70}\x{a71}\x{a81}-\x{a83}\x{abc}\x{abe}-\x{acd}\x{ae2}\x{ae3}' .
    '\x{af1}-\x{b03}\x{b3c}\x{b3e}-\x{b57}\x{b70}\x{b82}\x{bbe}-\x{bd7}\x{bf0}-' .
    '\x{c03}\x{c3e}-\x{c56}\x{c82}\x{c83}\x{cbc}\x{cbe}-\x{cd6}\x{d02}\x{d03}' .
    '\x{d3e}-\x{d57}\x{d82}\x{d83}\x{dca}-\x{df4}\x{e31}\x{e34}-\x{e3f}\x{e46}-' .
    '\x{e4f}\x{e5a}\x{e5b}\x{eb1}\x{eb4}-\x{ebc}\x{ec6}-\x{ecd}\x{f01}-\x{f1f}' .
    '\x{f2a}-\x{f3f}\x{f71}-\x{f87}\x{f90}-\x{fd1}\x{102c}-\x{1039}\x{104a}-' .
    '\x{104f}\x{1056}-\x{1059}\x{10fb}\x{10fc}\x{135f}-\x{137c}\x{1390}-\x{1399}' .
    '\x{166d}\x{166e}\x{1680}\x{169b}\x{169c}\x{16eb}-\x{16f0}\x{1712}-\x{1714}' .
    '\x{1732}-\x{1736}\x{1752}\x{1753}\x{1772}\x{1773}\x{17b4}-\x{17db}\x{17dd}' .
    '\x{17f0}-\x{180e}\x{1843}\x{18a9}\x{1920}-\x{1945}\x{19b0}-\x{19c0}\x{19c8}' .
    '\x{19c9}\x{19de}-\x{19ff}\x{1a17}-\x{1a1f}\x{1d2c}-\x{1d61}\x{1d78}\x{1d9b}-' .
    '\x{1dc3}\x{1fbd}\x{1fbf}-\x{1fc1}\x{1fcd}-\x{1fcf}\x{1fdd}-\x{1fdf}\x{1fed}-' .
    '\x{1fef}\x{1ffd}-\x{2070}\x{2074}-\x{207e}\x{2080}-\x{2101}\x{2103}-\x{2106}' .
    '\x{2108}\x{2109}\x{2114}\x{2116}-\x{2118}\x{211e}-\x{2123}\x{2125}\x{2127}' .
    '\x{2129}\x{212e}\x{2132}\x{213a}\x{213b}\x{2140}-\x{2144}\x{214a}-\x{2b13}' .
    '\x{2ce5}-\x{2cff}\x{2d6f}\x{2e00}-\x{3005}\x{3007}-\x{303b}\x{303d}-\x{303f}' .
    '\x{3099}-\x{309e}\x{30a0}\x{30fb}\x{30fd}\x{30fe}\x{3190}-\x{319f}\x{31c0}-' .
    '\x{31cf}\x{3200}-\x{33ff}\x{4dc0}-\x{4dff}\x{a015}\x{a490}-\x{a716}\x{a802}' .
    '\x{e000}-\x{f8ff}\x{fb29}\x{fd3e}-\x{fd3f}\x{fdfc}-\x{fdfd}' .
    '\x{fd3f}\x{fdfc}-\x{fe6b}\x{feff}-\x{ff0f}\x{ff1a}-\x{ff20}\x{ff3b}-\x{ff40}' .
    '\x{ff5b}-\x{ff65}\x{ff70}\x{ff9e}\x{ff9f}\x{ffe0}-\x{fffd}'
);

define(
    'AMB_PREG_CLASS_NUMBERS',
    '\x{30}-\x{39}\x{b2}\x{b3}\x{b9}\x{bc}-\x{be}\x{660}-\x{669}\x{6f0}-\x{6f9}' .
    '\x{966}-\x{96f}\x{9e6}-\x{9ef}\x{9f4}-\x{9f9}\x{a66}-\x{a6f}\x{ae6}-\x{aef}' .
    '\x{b66}-\x{b6f}\x{be7}-\x{bf2}\x{c66}-\x{c6f}\x{ce6}-\x{cef}\x{d66}-\x{d6f}' .
    '\x{e50}-\x{e59}\x{ed0}-\x{ed9}\x{f20}-\x{f33}\x{1040}-\x{1049}\x{1369}-' .
    '\x{137c}\x{16ee}-\x{16f0}\x{17e0}-\x{17e9}\x{17f0}-\x{17f9}\x{1810}-\x{1819}' .
    '\x{1946}-\x{194f}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{2153}-\x{2183}' .
    '\x{2460}-\x{249b}\x{24ea}-\x{24ff}\x{2776}-\x{2793}\x{3007}\x{3021}-\x{3029}' .
    '\x{3038}-\x{303a}\x{3192}-\x{3195}\x{3220}-\x{3229}\x{3251}-\x{325f}\x{3280}-' .
    '\x{3289}\x{32b1}-\x{32bf}\x{ff10}-\x{ff19}'
);

/* We remove '.' \x{2e}, '/' \x{2f}, '_' \x{5f} from Punctuation in order to transform them in '-' for references indexation */
define(
    'AMB_PREG_CLASS_PUNCTUATION',
    '\x{21}-\x{23}\x{25}-\x{2a}\x{2c}-\x{2f}\x{3a}\x{3b}\x{3f}\x{40}\x{5b}-\x{5d}' .
    '\x{5f}\x{7b}\x{7d}\x{a1}\x{ab}\x{b7}\x{bb}\x{bf}\x{37e}\x{387}\x{55a}-\x{55f}' .
    '\x{589}\x{58a}\x{5be}\x{5c0}\x{5c3}\x{5f3}\x{5f4}\x{60c}\x{60d}\x{61b}\x{61f}' .
    '\x{66a}-\x{66d}\x{6d4}\x{700}-\x{70d}\x{964}\x{965}\x{970}\x{df4}\x{e4f}' .
    '\x{e5a}\x{e5b}\x{f04}-\x{f12}\x{f3a}-\x{f3d}\x{f85}\x{104a}-\x{104f}\x{10fb}' .
    '\x{1361}-\x{1368}\x{166d}\x{166e}\x{169b}\x{169c}\x{16eb}-\x{16ed}\x{1735}' .
    '\x{1736}\x{17d4}-\x{17d6}\x{17d8}-\x{17da}\x{1800}-\x{180a}\x{1944}\x{1945}' .
    '\x{2010}-\x{2027}\x{2030}-\x{2043}\x{2045}-\x{2051}\x{2053}\x{2054}\x{2057}' .
    '\x{207d}\x{207e}\x{208d}\x{208e}\x{2329}\x{232a}\x{23b4}-\x{23b6}\x{2768}-' .
    '\x{2775}\x{27e6}-\x{27eb}\x{2983}-\x{2998}\x{29d8}-\x{29db}\x{29fc}\x{29fd}' .
    '\x{3001}-\x{3003}\x{3008}-\x{3011}\x{3014}-\x{301f}\x{3030}\x{303d}\x{30a0}' .
    '\x{30fb}\x{fd3e}\x{fd3f}\x{fe30}-\x{fe52}\x{fe54}-\x{fe61}\x{fe63}\x{fe68}' .
    '\x{fe6a}\x{fe6b}\x{ff01}-\x{ff03}\x{ff05}-\x{ff0a}\x{ff0c}-\x{ff0f}\x{ff1a}' .
    '\x{ff1b}\x{ff1f}\x{ff20}\x{ff3b}-\x{ff3d}\x{ff3f}\x{ff5b}\x{ff5d}\x{ff5f}-' .
    '\x{ff65}'
);

/*
 * Matches all CJK characters that are candidates for auto-splitting
 * (Chinese, Japanese, Korean).
 * Contains kana and BMP ideographs.
 */
define(
    'AMB_PREG_CLASS_CJK',
    '\x{3041}-\x{30ff}\x{31f0}-\x{31ff}\x{3400}-\x{4db5}\x{4e00}-\x{9fbb}\x{f900}-\x{fad9}'
);

class AmbSearch
{
    public $id_lang;
    public $expr;
    public $page_number;
    public $limit;
    public $order_by;
    public $order_way;
    public $in_stock_first = false;
    public $context;
    public $db;
    public $mode = 'normal';
    public $id_customer;
    public $ajax;
    public $search_all_terms = true;

    public $language_ids;

    public $where;
    public $having;
    public $nb = 0;

    public $main_order_by = '';

    protected $results;
    protected $product_ids = [];

    public $words = [];

    public $categories = [];
    public $manufacturers = [];
    public $suppliers = [];

    public $display_manufacturer = false;
    public $display_supplier = false;

    public $search_parameter = 'search_query';

    public function __construct($use_cookie, $context, $module)
    {
        $this->module = $module;
        $this->db = Db::getInstance(_PS_USE_SQL_SLAVE_);

        if ($this->module->ps17) {
            $this->search_parameter = 's';
        }

        if (!$context) {
            $this->context = Context::getContext();
        } else {
            $this->context = $context;
        }

        if ($use_cookie) {
            $this->id_customer = $this->context->customer->id;
        } else {
            $this->id_customer = 0;
        }

        $this->search_all_terms = Configuration::hasKey(AJS_SEARCH_ALL_TERMS) ? (bool) Configuration::get(AJS_SEARCH_ALL_TERMS) : true;
        $this->also_try_or_comparator = Configuration::hasKey(AJS_ALSO_TRY_OR_COMPARATOR) ? (bool) Configuration::get(AJS_ALSO_TRY_OR_COMPARATOR) : true;
        $this->approximation_level = (Configuration::hasKey(AJS_APPROXIMATION_LEVEL) ? Configuration::get(AJS_APPROXIMATION_LEVEL) : (Configuration::get(AJS_APPROXIMATIVE_SEARCH) ? 2 : 0));

        $this->display_manufacturer = Configuration::hasKey(AJS_DISPLAY_MANUFACTURER) ? (bool) Configuration::get(AJS_DISPLAY_MANUFACTURER) : false;
        $this->display_supplier = Configuration::hasKey(AJS_DISPLAY_SUPPLIER) ? (bool) Configuration::get(AJS_DISPLAY_SUPPLIER) : false;
    }

    public function search($id_lang, $expr, $page_number, $limit, $order_by, $order_way, $id_category = null, $id_manufacturer = null, $id_supplier = null)
    {
        static $findCache = [];

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            return;
        }

        $this->id_lang = $id_lang;
        $this->iso_lang = Language::getIsoById($this->id_lang);
        $this->expr = $expr;
        $this->page_number = $page_number;
        $this->limit = ((int) $limit > 0 ? $limit : false);
        $this->order_by = $order_by;
        if (strpos($this->order_by, '.') > 0) {
            $this->order_by = explode('.', $this->order_by);
            $this->order_by = pSQL($this->order_by[0]) . '.`' . pSQL($this->order_by[1]) . '`';
        }
        $this->order_way = $order_way;

        if ((int) Configuration::get(AJS_MULTILANG_SEARCH) == 1) {
            if (version_compare(_PS_VERSION_, '1.6.1', '<')) {
                $languages = Language::getLanguages(true, $this->context->shop->id);
                $this->language_ids = [];
                foreach ($languages as $language) {
                    $this->language_ids[] = $language['id_lang'];
                }
            } else {
                $this->language_ids = Language::getLanguages(true, $this->context->shop->id, true);
            }
        } else {
            $this->language_ids = false;
        }

        $show_only_products_in_stock = Configuration::hasKey(AJS_ONLY_SEARCH_PRODUCTS_IN_STOCK) ? (bool) Configuration::get(AJS_ONLY_SEARCH_PRODUCTS_IN_STOCK) : false;

        // fallback for functions calls from faceted modules (Amazzing Filters & AdvancedSearch4)
        if (is_null($id_category) && Tools::getValue('ajs_cat', false)) {
            $id_category = Tools::getValue('ajs_cat', false);
        }

        if (is_null($id_manufacturer) && Tools::getValue('ajs_man', false)) {
            $id_manufacturer = Tools::getValue('ajs_man', false);
        }

        if (is_null($id_supplier) && Tools::getValue('ajs_sup', false)) {
            $id_supplier = Tools::getValue('ajs_sup', false);
        }

        // use cache only after object attributes are initialized (issue when differents languages are used)
        $cacheKey = sha1(json_encode(func_get_args()));
        if (isset($findCache[$cacheKey])) {
            $this->product_ids = $findCache[$cacheKey];

            return;
        }

        $this->words = self::extractKeyWords($this->expr, $this->id_lang, false, $this->iso_lang);

        if (count($this->words) > 1) {
            $this->words['concat'] = str_replace(' ', '', AmbSearch::sanitize($this->expr, $this->id_lang, false, $this->iso_lang, ' '));
        }

        $alias = '';
        $need_name = false;

        $this->in_stock_first = false;
        if (Configuration::hasKey(AJS_SECONDARY_SORT)) {
            $secondary_order_by = Configuration::get(AJS_SECONDARY_SORT);
            $this->in_stock_first = !$show_only_products_in_stock && (bool) (strpos($secondary_order_by, 'in_stock_first') !== false);
            if ($this->in_stock_first) {
                $secondary_order_by = '';
            }
        } else {
            $secondary_order_by = '';
        }

        if ($this->order_by == 'price') {
            $alias = 'product_shop.';
        }

        if ($this->order_by == 'name') {
            $need_name = true;
            $alias = 'pl.';
        }

        if (in_array($secondary_order_by, ['pl.name DESC', 'pl.name ASC'])) {
            $need_name = true;
        }

        $this->main_order_by = ($this->order_by ? 'ORDER BY  ' . bqSQL($alias . $this->order_by) : '') . (bqSQL($this->order_way) ? ' ' . bqSQL($this->order_way) : '');

        if (!empty($secondary_order_by)) {
            $this->main_order_by .= ',' . bqSQL($secondary_order_by);
        }
        $this->main_order_by = ($this->in_stock_first ? str_replace('ORDER BY ', 'ORDER BY in_stock_first DESC, ', $this->main_order_by) : $this->main_order_by);

        $word_conditions = [];
        $check_terms = [];

        $categories_restriction = '';
        if (!empty($id_category)) {
            $search_in_subcategories = Configuration::get(AJS_SEARCH_IN_SUBCATEGORIES);
            if ($search_in_subcategories) {
                $cat = new Category((int) $id_category);
                if (Validate::isLoadedObject($cat)) {
                    $categories_restriction = 'SELECT id_category FROM ' . _DB_PREFIX_ . 'category WHERE nleft >= ' . (int) $cat->nleft . ' AND nright <= ' . (int) $cat->nright;
                }
            } else {
                $categories_restriction = (int) $id_category;
            }
        }

        $eligible_products_request = '
                SELECT
                DISTINCT cp.`id_product`
                FROM `' . _DB_PREFIX_ . 'category_group` cg
                INNER JOIN `' . _DB_PREFIX_ . 'category_product` cp ON cp.`id_category` = cg.`id_category`
                INNER JOIN `' . _DB_PREFIX_ . 'category` c ON cp.`id_category` = c.`id_category`
                INNER JOIN `' . _DB_PREFIX_ . 'product` p ON cp.`id_product` = p.`id_product`
                ' . Shop::addSqlAssociation('product', 'p', false) . '
                ' . ($show_only_products_in_stock ? Product::sqlStock('p', 0) : '') . '
                WHERE c.`active` = 1
                    AND product_shop.`active` = 1
                    AND product_shop.`visibility` IN ("both", "search")
                    AND product_shop.indexed = 1
                    ' . (!empty($id_category) ? ' AND cg.`id_category` IN (' . $categories_restriction . ')' : '') . '
                    ' . (!empty($id_manufacturer) ? ' AND p.`id_manufacturer` = ' . (int) $id_manufacturer : '') . '
                    ' . (!empty($id_supplier) ? ' AND p.`id_supplier` = ' . (int) $id_supplier : '') . '
                    ' . ($show_only_products_in_stock ? ' AND (stock.quantity IS NOT NULL AND stock.quantity > 0) ' : '') . '
                    AND cg.`id_group` ' . (!$this->id_customer ? '=' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP') : 'IN (
                        SELECT id_group FROM ' . _DB_PREFIX_ . 'customer_group
                        WHERE id_customer = ' . (int) $this->id_customer . ')
                    ');

        $this->module->log($eligible_products_request, __FILE__, __METHOD__, __LINE__, '$eligible_products_request');

        $nb_suitable_words = 0;

        $use_approximative_search = true;
        $use_approximative_on_references = (bool) Configuration::get(AJS_USE_APPROXIMATIVE_FOR_REFERENCES);
        $reference_pattern = '/^([a-z-_]*\d+[a-z-_]*)*$/i';

        $word_max_length = AmbSearch::getWordMaxLength();

        // insert a false condition if no word condition is generated. This protects from a full range search
        // on search_index table to match the having conditions (which, of course, have no results).
        $word_conditions[] = '(0)';
        $matching_conditions = [];

        foreach ($this->words as $key => $word) {
            if (!empty($word) && (Tools::strlen($word) >= (int) Configuration::get('PS_SEARCH_MINWORDLEN') || in_array($this->iso_lang, ['zh', 'tw', 'ja']))) {
                $naked_word = $word;
                $word = str_replace('%', '\\%', $word);
                $word = str_replace('_', '\\_', $word);

                if ((int) Configuration::get(PS_SEARCH_START) == 1) {
                    $my_word = $word[0] == '-'
                    ? '%' . pSQL(Tools::substr($word, 1, $word_max_length)) . '%'
                    : '%' . pSQL(Tools::substr($word, 0, $word_max_length)) . '%';

                    $my_term_word = $word[0] == '-'
                    ? '%' . pSQL(Tools::substr($word, 1, $word_max_length)) . '%'
                    : '%' . pSQL(Tools::substr($word, 0, $word_max_length)) . '%';
                } else {
                    $my_word = $word[0] == '-'
                    ? pSQL(Tools::substr($word, 1, $word_max_length)) . '%'
                    : pSQL(Tools::substr($word, 0, $word_max_length)) . '%';

                    $my_term_word = $word[0] == '-'
                    ? '% ' . pSQL(Tools::substr($word, 1, $word_max_length)) . '%'
                    : '% ' . pSQL(Tools::substr($word, 0, $word_max_length)) . '%';
                }

                if ($use_approximative_search && !in_array($this->iso_lang, ['zh', 'tw', 'ja']) && ($use_approximative_on_references || !(bool) preg_match($reference_pattern, $naked_word, $matches))) {
                    // If we are not in compat mode, we check for synonyms
                    $request = '
                                    SELECT sw.id_word, sw.word
                                    FROM ' . _DB_PREFIX_ . 'search_word sw
                                    WHERE word LIKE "' . $my_word . '"
                                    AND  ' . ($this->language_ids ? 'sw.id_lang IN (' . implode(',', $this->language_ids) . ')' : 'sw.id_lang = ' . (int) $id_lang) . '
                                    AND sw.id_shop = ' . (int) $this->context->shop->id;

                    $results = $this->db->executeS($request);

                    if (($results === false || count($results) == 0) && $key . '' != 'concat') {
                        $synonyms_results = $this->searchSynonyms($my_word);

                        if (count($synonyms_results['ids']) == 0 && $use_approximative_search) {
                            if ($this->applyLevenshtein($my_word, $naked_word, $id_lang)) {
                                $synonyms_results = $this->searchSynonyms($my_word);
                            }
                        }

                        if (count($synonyms_results['ids']) > 0) {
                            $matching_conditions[] = bqSql($word);
                            $word_conditions[] = '
                                (si.id_word IN(' . implode(',', $synonyms_results['ids']) . '))';
                        }
                    } else {
                        if ($results !== false) {
                            $results_ids = [];
                            foreach ($results as $result) {
                                $results_ids[$result['id_word']] = $result;
                            }
                            if (count($results_ids) > 0) {
                                $matching_conditions[] = bqSql($word);
                                $word_conditions[] = '(si.id_word IN(' . implode(
                                    ',',
                                    array_map(
                                        function ($e) {
                                            return $e['id_word'];
                                        },
                                        $results_ids
                                    )
                                ) . '))';
                            }
                        }
                    }
                } else {
                    // If there is no synonym check

                    $fragment_sql = 'SELECT id_word, word FROM ' . _DB_PREFIX_ . 'search_word WHERE word LIKE "' . $my_word . '"
                        AND ' . ($this->language_ids ? 'id_lang IN (' . implode(',', $this->language_ids) . ')' : 'id_lang = ' . (int) $id_lang) . '
                        AND id_shop = ' . (int) $this->context->shop->id;

                    $ids = Db::getInstance()->executeS($fragment_sql);

                    if (!empty($ids)) {
                        $matching_conditions[] = bqSql($word);
                        $word_conditions[] = '(si.id_word IN ( ' . implode(
                            ',',
                            array_map(
                                function ($e) {
                                    return $e['id_word'];
                                },
                                $ids
                            )
                        ) . ' )) ';
                    }
                }

                if ($key . '' != 'concat') {
                    ++$nb_suitable_words;
                    $likes = [];

                    $likes[] = 'terms LIKE "' . $my_term_word . '"';

                    if (isset($synonyms_results['words']) && is_array($synonyms_results['words'])) {
                        foreach ($synonyms_results['words'] as $synonym) {
                            $likes[] = 'terms LIKE "%' . $synonym . '%"';
                        }
                    }

                    $check_terms[] = '(' . implode(' OR ', $likes) . ')';
                }
            }
        }

        if ($nb_suitable_words == 0) {
            $this->context->smarty->assign('no_suitable_words', true);
            $this->context->smarty->assign('min_length', (int) Configuration::get('PS_SEARCH_MINWORDLEN'));

            return;
        }

        $this->where = implode(' OR ', $word_conditions);
        $this->having = (count($check_terms) > 0) ? ' HAVING ' . implode($this->search_all_terms ? ' AND ' : ' OR ', $check_terms) : '';
        $sql_limit = $this->limit > 0 ? ' LIMIT ' . ($this->page_number - 1) * $this->limit . ',' . $this->limit : '';
        $pl = $need_name ? ' INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON si.id_product=pl.id_product AND pl.id_lang=' . (int) $id_lang . ' ' : ' ';

        $weight_variation = '';
        if (count($matching_conditions) > 0) {
            $weight_variation .= 'IF( sw.word IN (\'' . implode('\',\'', $matching_conditions) . '\'), si.weight * 2, si.weight)';
        } else {
            $weight_variation .= 'si.weight';
        }

        $join_manufacturer = '';
        if ($this->display_manufacturer) {
            $join_manufacturer = ' LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`';
        }
        $join_supplier = '';
        if ($this->display_supplier) {
            $join_supplier = ' LEFT JOIN `' . _DB_PREFIX_ . 'supplier` su ON su.`id_supplier` = p.`id_supplier`';
        }

        $main_request = '
                    SELECT
                    SQL_CALC_FOUND_ROWS
                    si.id_product, SUM( ' . $weight_variation . ' ) position, GROUP_CONCAT(CONCAT(\' \', sw.word) SEPARATOR \' \') as terms
                    ' . ($show_only_products_in_stock ? ', IFNULL(stock.quantity, 0) as quantity' : '') . '
                    ' . ($this->in_stock_first ? ', IF(IFNULL(stock.quantity, 0) > 0, 1, 0) as in_stock_first' : '') . '
                    FROM ' . _DB_PREFIX_ . 'search_index si
                    LEFT JOIN ' . _DB_PREFIX_ . 'search_word sw ON sw.id_word = si.id_word
                    LEFT JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product=si.id_product
                    ' . Shop::addSqlAssociation('product', 'si', false)
        . $pl
        . (($show_only_products_in_stock || $this->in_stock_first) ? Product::sqlStock('p', 0) : '') . '
        WHERE 1 ' .
        (Tools::strlen($this->where) > 0 ? 'AND (' . $this->where . ')' : '') . '
                        AND si.id_product IN(' . $eligible_products_request . ')
                    GROUP BY si.id_product '
        . $this->having
        . $this->main_order_by;

        $this->module->log($main_request, __FILE__, __METHOD__, __LINE__, '$main_request');

        $results = $this->db->executeS($main_request);
        $this->nb = is_array($results) ? count($results) : 0;

        if ($this->nb > 0) {
            foreach ($results as $row) {
                $this->full_product_ids[] = $row['id_product'];
            }

            if ($this->limit) {
                $this->product_ids = array_slice($this->full_product_ids, ($this->page_number - 1) * $this->limit, $this->limit);
            } else {
                $this->product_ids = $this->full_product_ids;
            }
        } elseif ($this->also_try_or_comparator && $this->search_all_terms === true) {
            $this->search_all_terms = false;
            $this->search($id_lang, $expr, $page_number, $limit, $order_by, $order_way, $id_category, $id_manufacturer, $id_supplier);
        } else {
        }
        $findCache[$cacheKey] = $this->getResultIds();
    }

    public function getResults($ajax = false, $limit = false)
    {
        if (count($this->product_ids) == 0) {
            return [];
        }

        if ((int) $limit > 0) {
            $product_ids = array_slice($this->product_ids, 0, (int) $limit);
        } else {
            $product_ids = $this->product_ids;
        }

        if ($ajax) {
            if (version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
                $image_join = '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)' .
                Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image`
                        AND il.`id_lang` = ' . (int) $this->id_lang . ')';
            } else {
                $image_join = '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                        ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->context->shop->id . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image`
                        AND il.`id_lang` = ' . (int) $this->id_lang . ')';
            }
            $image_select = 'IFNULL(image_shop.`id_image`, (SELECT i.`id_image` FROM ' . _DB_PREFIX_ . 'image i where i.`id_product`= p.`id_product` ORDER BY i.cover DESC LIMIT 1)) imgid';

            $manufacturer_select = '';
            $manufacturer_join = '';
            if ($this->display_manufacturer) {
                $manufacturer_select = ', m.`name` mname, m.`id_manufacturer` manid';
                $manufacturer_join = ' LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`';
            }

            $supplier_select = '';
            $supplier_join = '';
            if ($this->display_supplier) {
                $supplier_select = ', su.`name` suname, su.`id_supplier` supid';
                $supplier_join = ' LEFT JOIN `' . _DB_PREFIX_ . 'supplier` su ON su.`id_supplier` = p.`id_supplier`';
            }

            $sql = 'SELECT DISTINCT pl.name pname, cl.name cname,
                    cl.link_rewrite crewrite, pl.link_rewrite prewrite, pl.link_rewrite link_rewrite
                    ' . $manufacturer_select . '
                    ' . $supplier_select . '
                    , cs.id_category as catid,
                    p.*,
                    product_shop.*,
                    null as wholesale_price,
                    null as supplier_reference,
                    null as location,
                    ' . $image_select . '
                FROM ' . _DB_PREFIX_ . 'product p
                ' . Shop::addSqlAssociation('product', 'p') . '
                INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                    p.`id_product` = pl.`id_product`
                    AND pl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('pl') . '
                )
                LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON cs.id_category=product_shop.id_category_default
                    AND cs.id_shop=' . $this->context->shop->id . '
                LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (
                    product_shop.`id_category_default` = c.`id_category`
                    AND c.active=1
                )
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (
                    c.`id_category` = cl.`id_category`
                    AND cl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('cl') . '
                )
                ' . $manufacturer_join . '
                ' . $supplier_join . '
                ' . $image_join . '
                WHERE p.`id_product` IN(' . implode(',', $product_ids) . ')';

            $this->module->log($sql, __FILE__, __METHOD__, __LINE__, 'if $ajax $sql');
        } else {
            if (version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
                $image_join = '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)' .
                Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image`
                        AND il.`id_lang` = ' . (int) $this->id_lang . ')';
            } else {
                $image_join = '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                        ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $this->context->shop->id . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image`
                        AND il.`id_lang` = ' . (int) $this->id_lang . ')';
            }
            $image_select = 'IFNULL(image_shop.`id_image`, (SELECT i.`id_image` FROM ' . _DB_PREFIX_ . 'image i where i.`id_product`= p.`id_product` ORDER BY i.cover DESC LIMIT 1)) `id_image`';

            $manufacturer_select = '';
            $manufacturer_join = '';
            if ($this->display_manufacturer) {
                $manufacturer_select = ', m.`name` manufacturer_name';
                $manufacturer_join = ' LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`';
            }

            $supplier_select = '';
            $supplier_join = '';
            if ($this->display_supplier) {
                $supplier_select = ', su.`name` supplier_name';
                $supplier_join = ' LEFT JOIN `' . _DB_PREFIX_ . 'supplier` su ON su.`id_supplier` = p.`id_supplier`';
            }

            $main_order_by = $this->main_order_by;
            $attribute_price_select = '';
            $limit_sql = '';
            if ($this->order_by == 'price') {
                $product_ids = $this->full_product_ids;
                $limit = $this->limit;
                $offset = ((int) $this->page_number - 1) * (int) $this->limit;
                $limit_sql = 'LIMIT ' . $this->limit . ' OFFSET ' . $offset;
                $attribute_price_select = '(product_shop.price + IF(pa.`id_product_attribute` IS NOT NULL OR pa.`id_product_attribute` != 0, pa.`price`, 0)) default_attribute_price ';
                $main_order_by = ' ORDER BY  default_attribute_price ' . ($this->order_way ? ' ' . bqSQL($this->order_way) : '');
                $main_order_by = ($this->in_stock_first ? str_replace('ORDER BY ', 'ORDER BY in_stock_first DESC, ', $main_order_by) : $main_order_by);
            }

            $sql = 'SELECT DISTINCT(p.id_product), p.*, product_shop.*, stock.out_of_stock,
                IFNULL(stock.quantity, 0) as quantity,
                pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
                ' . $image_select . '
                ' . $manufacturer_select . '
                ' . $supplier_select . ',
                il.`legend`,
                IFNULL(pa.`id_product_attribute`, 0) id_product_attribute
                , 1 as position,
                DATEDIFF(
                    p.`date_add`,
                    DATE_SUB(
                        NOW(),
                        INTERVAL '
            . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                    )
                ) > 0 new
                ' . ($this->order_by == 'price' ? ', ' . $attribute_price_select : '') . '
                ' . ($this->in_stock_first ? ', IF(IFNULL(stock.quantity, 0) > 0, 1, 0) as in_stock_first' : '') . '
                FROM ' . _DB_PREFIX_ . 'product p
                ' . Shop::addSqlAssociation('product', 'p') . '
                INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                    p.`id_product` = pl.`id_product`
                    AND pl.`id_lang` = ' . (int) $this->id_lang . Shop::addSqlRestrictionOnLang('pl') . '
                )
                LEFT JOIN (SELECT product_attribute_shop.*
                ' . (version_compare(_PS_VERSION_, '1.6.1', '<') ? ', pa1.id_product ' : ' ') . '
                FROM `' . _DB_PREFIX_ . 'product_attribute` pa1
                ' . Shop::addSqlAssociation('product_attribute', 'pa1', false) . ' WHERE product_attribute_shop.`default_on`=1) pa ON (p.`id_product` = pa.`id_product`)
                ' . Product::sqlStock('p', 0) . '
                ' . $manufacturer_join . '
                ' . $supplier_join . '
                ' . $image_join . '
                WHERE p.`id_product` IN(' . implode(',', $product_ids) . ')
                GROUP BY p.id_product
                ' . $main_order_by . ' ' . $limit_sql;

            $this->module->log($sql, __FILE__, __METHOD__, __LINE__, 'if not $ajax $sql');
        }

        $result_properties = $this->db->executeS($sql);
        $dbresults = [];
        $dbres = [];

        if ($this->order_by == 'position') {
            if (is_array($result_properties)) {
                foreach ($result_properties as $v) {
                    $dbres[$v['id_product']] = $v;
                }
            }

            if (is_array($product_ids)) {
                foreach ($product_ids as $product_id) {
                    if (isset($dbres[$product_id])) {
                        $dbresults[] = $dbres[$product_id];
                    }
                }
            }
        } else {
            $dbresults = $result_properties;
            /* Usefull to order by price when products have specific prices */
            if ($this->order_by == 'price') {
                if (method_exists('Tools', 'orderbyPrice')) {
                    Tools::orderbyPrice($dbresults, $this->order_way);
                } else {
                    self::orderbyPrice($dbresults, $this->order_way);
                }
            }
        }

        $dbresults = Product::getProductsProperties((int) $this->id_lang, $dbresults);
        $this->categories = $this->getCategoriesOfProducts($this->id_lang, $this->context->shop->id, $this->full_product_ids, ['where' => $this->where, 'having' => $this->having], $ajax);
        $this->manufacturers = $this->getManufacturersOfProducts($this->id_lang, $this->context->shop->id, $this->full_product_ids, ['where' => $this->where, 'having' => $this->having], $ajax);
        $this->suppliers = $this->getSuppliersOfProducts($this->id_lang, $this->context->shop->id, $this->full_product_ids, ['where' => $this->where, 'having' => $this->having], $ajax);

        return $dbresults;
    }

    public function getCategories()
    {
        if (isset($this->categories) && count($this->categories) > 0) {
            $allow_filter_results = (bool) Configuration::get(AJS_ALLOW_FILTER_RESULTS);

            foreach ($this->categories as &$row) {
                $row['id_image'] = file_exists(_PS_CAT_IMG_DIR_ . $row['id_category'] . '.jpg') ?
                (int) $row['id_category']
                : Language::getIsoById($this->id_lang) . '-default';
                $row['legend'] = 'no picture';
                $row['image']['legend'] = 'no picture';

                $cat = new Category($row['id_category'], $this->context->language->id);
                $row['image']['large']['url'] = $this->module->getCategoryImage($cat, $this->context->language->id);
                $row['thumb_url'] = $this->getCategoryThumb($cat, $this->context->language->id);
                if ($allow_filter_results) {
                    $row['url'] = $this->context->link->getModuleLink('ambjolisearch', 'jolisearch', [$this->search_parameter => $this->expr, 'ajs_cat' => (int) $row['id_category'], 'fast_search' => 'fs']);
                } else {
                    $row['url'] = $this->context->link->getCategoryLink($cat);
                }
            }
        } else {
            $this->categories = [];
        }

        return $this->categories;
    }

    public function getManufacturers()
    {
        if (isset($this->manufacturers) && count($this->manufacturers) > 0) {
            foreach ($this->manufacturers as &$row) {
                $row['id_image'] = file_exists(_PS_MANU_IMG_DIR_ . $row['id_manufacturer'] . '.jpg') ?
                (int) $row['id_manufacturer']
                : Language::getIsoById($this->id_lang) . '-default';
                $row['legend'] = 'no picture';
                $row['image']['legend'] = 'no picture';

                $cat = new Manufacturer($row['id_manufacturer'], $this->context->language->id);
                $row['image']['large']['url'] = $this->module->getManufacturerImage($cat, $this->context->language->id);
                $row['url'] = $this->context->link->getManufacturerLink($cat);
            }
        } else {
            $this->manufacturers = [];
        }

        return $this->manufacturers;
    }

    public function getSuppliers()
    {
        if (isset($this->suppliers) && count($this->suppliers) > 0) {
            foreach ($this->suppliers as &$row) {
                $row['id_image'] = file_exists(_PS_SUPP_IMG_DIR_ . $row['id_supplier'] . '.jpg') ?
                (int) $row['id_supplier']
                : Language::getIsoById($this->id_lang) . '-default';
                $row['legend'] = 'no picture';
                $row['image']['legend'] = 'no picture';

                $cat = new Supplier($row['id_supplier'], $this->context->language->id);
                $row['image']['large']['url'] = $this->module->getSupplierImage($cat, $this->context->language->id);
                $row['url'] = $this->context->link->getSupplierLink($cat);
            }
        } else {
            $this->suppliers = [];
        }

        return $this->suppliers;
    }

    public function getTotal()
    {
        return $this->nb;
    }

    public function getResultIds()
    {
        return $this->product_ids;
    }

    public function presentForAjaxResponse($show_price = true, $show_features = true, $max_items = null, $allow_filter_results = false)
    {
        if (empty($max_items)) {
            $max_items = [];
            $max_items['all'] = Configuration::get(AJS_MAX_ITEMS_KEY);
            $max_items['manufacturers'] = Configuration::get(AJS_MAX_MANUFACTURERS_KEY);
            $max_items['suppliers'] = Configuration::get(AJS_MAX_SUPPLIERS_KEY);
            $max_items['categories'] = Configuration::get(AJS_MAX_CATEGORIES_KEY);
            $max_items['products'] = Configuration::hasKey(AJS_MAX_PRODUCTS_KEY) ? Configuration::get(AJS_MAX_PRODUCTS_KEY) : 10;
        }

        $search_results = $this->getResults(true, $max_items['products']);
        $total = $this->getTotal();
        $sr_categories = $this->getCategories();
        $sr_manufacturers = $this->getManufacturers();
        $sr_suppliers = $this->getSuppliers();

        if ($total == 0) {
            exit(json_encode([
                [
                    'type' => 'no_results_found',
                ],
            ]));
        }

        $show_parent_category = Configuration::get(AJS_SHOW_PARENT_CATEGORY);
        $filter_on_parent_category = Configuration::get(AJS_FILTER_ON_PARENT_CATEGORY);

        $price_display = Product::getTaxCalculationMethod();
        $show_price = $show_price
            && (!(bool) Configuration::get('PS_CATALOG_MODE') && (bool) Group::getCurrent()->show_prices);

        foreach ($search_results as &$product) {
            $link = $this->context->link->getProductLink(
                $product['id_product'],
                $product['prewrite'],
                $product['crewrite'],
                null,
                null,
                null,
                $product['cache_default_attribute']
            );

            if ($this->module->ps17) {
                $product['link'] = $link . '?fast_search=fs';
            } else {
                $product['link'] = $link . '?' . $this->search_parameter . '=' . $this->expr . '&fast_search=fs';
            }

            $product['img'] = $this->module->getProductImage($product);
            $product['type'] = 'product';

            $feats = [];

            if ($show_features) {
                foreach ($product['features'] as $feature) {
                    $feats[] = $feature['name'] . ': ' . $feature['value'];
                }
            }

            $product['feats'] = implode(', ', $feats);

            if ($show_price && isset($product['show_price']) && $product['show_price']) {
                if (!$price_display) {
                    $product['price_raw'] = $product['price'];
                    $product['price'] = Tools::displayPrice(
                        $product['price'],
                        (int) $this->context->cookie->id_currency
                    );
                } else {
                    $product['price_raw'] = $product['price_tax_exc'];
                    $product['price'] = Tools::displayPrice(
                        $product['price_tax_exc'],
                        (int) $this->context->cookie->id_currency
                    );
                }
            } else {
                $product['price_raw'] = '';
                $product['price'] = '';
            }

            if ($this->module->ps17 && Configuration::get(AJS_SHOW_ADD_TO_CART_BUTTON)) {
                if (method_exists($this->context->link, 'getAddToCartURL') && $this->shouldEnableAddToCartButton($product)) {
                    $product['amb_add_to_cart_url'] = $this->context->link->getAddToCartURL($product['id_product'], $product['cache_default_attribute']);
                }
            }
        }

        $manufacturers = [];
        if (!empty($sr_manufacturers)) {
            foreach ($sr_manufacturers as $manufacturer) {
                $manufacturers[$manufacturer['id_manufacturer']] = $manufacturer;
            }
        }

        $search_manufacturers = [];
        foreach ($manufacturers as $manufacturer) {
            $manu = new Manufacturer();
            $manu->id = $manufacturer['id_manufacturer'];

            $link = '#';
            if ($allow_filter_results) {
                $link = $this->context->link->getModuleLink('ambjolisearch', 'jolisearch', [$this->search_parameter => $this->expr, 'ajs_man' => (int) $manufacturer['id_manufacturer'], 'fast_search' => 'fs']);
            } else {
                if ($this->module->ps17) {
                    $link = $this->context->link->getManufacturerLink($manu, Tools::link_rewrite($manufacturer['name'])) . '?fast_search=fs';
                } else {
                    $link = $this->context->link->getManufacturerLink($manu, Tools::link_rewrite($manufacturer['name'])) . '?' . $this->search_parameter . '=' . $this->expr . '&fast_search=fs';
                }
            }

            $search_manufacturers[] = ['type' => 'manufacturer',
                'man_id' => $manufacturer['id_manufacturer'],
                'man_name' => $manufacturer['name'],
                'img' => $this->module->getManufacturerImage($manu),
                'link' => $link,
                'products_count' => $manufacturer['products_count'],
            ];
        }

        $suppliers = [];
        if (!empty($sr_suppliers)) {
            foreach ($sr_suppliers as $supplier) {
                $suppliers[$supplier['id_supplier']] = $supplier;
            }
        }

        $search_suppliers = [];
        foreach ($suppliers as $supplier) {
            $manu = new Supplier();
            $manu->id = $supplier['id_supplier'];

            $link = '#';
            if ($allow_filter_results) {
                $link = $this->context->link->getModuleLink('ambjolisearch', 'jolisearch', [$this->search_parameter => $this->expr, 'ajs_sup' => (int) $supplier['id_supplier'], 'fast_search' => 'fs']);
            } else {
                if ($this->module->ps17) {
                    $link = $this->context->link->getSupplierLink($manu, Tools::link_rewrite($supplier['name'])) . '?fast_search=fs';
                } else {
                    $link = $this->context->link->getSupplierLink($manu, Tools::link_rewrite($supplier['name'])) . '?' . $this->search_parameter . '=' . $this->expr . '&fast_search=fs';
                }
            }

            $search_suppliers[] = ['type' => 'supplier',
                'sup_id' => $supplier['id_supplier'],
                'sup_name' => $supplier['name'],
                'img' => $this->module->getSupplierImage($manu),
                'link' => $link,
                'products_count' => $supplier['products_count'],
            ];
        }

        $categories = [];
        if (!empty($sr_categories)) {
            foreach ($sr_categories as $category) {
                $categories[$category['id_category']] = $category;
            }
        }

        $search_categories = [];
        foreach ($categories as $category) {
            $cat = new Category($category['id_category'], $this->id_lang);
            $cname = $cat->name;

            if ($filter_on_parent_category) {
                $parent = new Category($cat->id_parent, $this->id_lang);
                if (isset($categories[$parent->id]) || isset($search_categories[$parent->id])) {
                    // parent is already in list or was already done
                    continue;
                } elseif ($parent->level_depth >= 2) {
                    $cat = $parent;
                    $cname = $cat->name;
                    $category['id_category'] = $cat->id;
                }
            }

            if ($show_parent_category) {
                $parent = new Category($cat->id_parent, $this->id_lang);
                if ($parent->level_depth >= 2) {
                    $cname = $parent->name . ' > ' . $cname;
                }
            }

            if ($allow_filter_results) {
                $link = $this->context->link->getModuleLink('ambjolisearch', 'jolisearch', [$this->search_parameter => $this->expr, 'ajs_cat' => (int) $category['id_category'], 'fast_search' => 'fs']);
            } else {
                if ($this->module->ps17) {
                    $link = $this->context->link->getCategoryLink($cat, $cat->link_rewrite, $this->id_lang) . '?fast_search=fs';
                } else {
                    $link = $this->context->link->getCategoryLink($cat, $cat->link_rewrite, $this->id_lang) . '?' . $this->search_parameter . '=' . $this->expr . '&fast_search=fs';
                }
            }

            $search_categories[$category['id_category']] = ['type' => 'category',
                'cat_id' => $category['id_category'],
                'cat_name' => $cname,
                'img' => $this->module->getCategoryImage($cat, $this->id_lang),
                'link' => $link,
                'products_count' => $category['products_count'],
            ];
        }

        $search = [
            'products' => [],
            'manufacturers' => [],
            'suppliers' => [],
            'categories' => [],
        ];
        if (count($search_manufacturers) > 0) {
            if (isset($max_items['manufacturers']) && Tools::strlen($max_items['manufacturers']) > 0) {
                $search['manufacturers'] = array_slice($search_manufacturers, 0, (int) $max_items['manufacturers']);
            }

            foreach ($search['manufacturers'] as &$manufacturer) {
                if ((int) $manufacturer['products_count'] == 0) {
                    unset($search['manufacturers'][$manufacturer['man_id']]);
                    continue;
                }
                $manufacturer['results'] = (int) $manufacturer['products_count'];
                $manufacturer['man_results'] = (int) $manufacturer['products_count'] . ' ' . $this->module->l('products found', 'AmbSearch');
            }
        }

        if (count($search_suppliers) > 0) {
            if (isset($max_items['suppliers']) && Tools::strlen($max_items['suppliers']) > 0) {
                $search['suppliers'] = array_slice($search_suppliers, 0, (int) $max_items['suppliers']);
            }

            foreach ($search['suppliers'] as &$supplier) {
                if ((int) $supplier['products_count'] == 0) {
                    unset($search['suppliers'][$supplier['sup_id']]);
                    continue;
                }
                $supplier['results'] = (int) $supplier['products_count'];
                $supplier['sup_results'] = (int) $supplier['products_count'] . ' ' . $this->module->l('products found', 'AmbSearch');
            }
        }

        if (count($search_categories) > 0) {
            if (isset($max_items['categories']) && Tools::strlen($max_items['categories']) > 0) {
                $search['categories'] = array_slice($search_categories, 0, (int) $max_items['categories']);
            }

            foreach ($search['categories'] as &$category) {
                // do not display categories if there is no results in it
                // (possible if search in subcategories is disabled and show only parent is enabled)
                if ((int) $category['products_count'] == 0) {
                    unset($search['categories'][$category['cat_id']]);
                    continue;
                }

                $category['results'] = (int) $category['products_count'];
                $category['cat_results'] = (int) $category['products_count'] . ' ' . $this->module->l('products found', 'AmbSearch');
            }
        }

        if (count($search_results) > 0) {
            if (isset($max_items['products']) && Tools::strlen($max_items['products']) > 0) {
                $search['products'] = array_slice($search_results, 0, (int) $max_items['products']);
            } else {
                $search['products'] = $search_results;
            }
        }

        return $search;
    }

    private function getCategoriesOfProducts($id_lang, $id_shop, $products, $criteria, $ajax = false)
    {
        $nb_categories = $ajax ? pSQL(Configuration::hasKey(AJS_MAX_CATEGORIES_KEY) ? Configuration::get(AJS_MAX_CATEGORIES_KEY) : 0) : pSQL(Configuration::get(AJS_SHOW_CATEGORIES));

        $category_order = (Configuration::hasKey(AJS_CATEGORIES_ORDER) ? Configuration::get(AJS_CATEGORIES_ORDER) : '');

        $categories_request = '';

        if ($nb_categories > 0) {
            $eligible_categories_request = '
                SELECT c.id_category
                FROM ' . _DB_PREFIX_ . 'category c
                INNER JOIN ' . _DB_PREFIX_ . 'category_shop category_shop ON (category_shop.id_category = c.id_category AND category_shop.id_shop = ' . (int) $id_shop . ')
                WHERE c.is_root_category=0 AND c.active = 1
                     ' . (Configuration::get(AJS_ONLY_LEAF_CATEGORIES) ? ' AND c.nright-c.nleft = 1' : '');

            $categories_limit = ' LIMIT 0,' . $nb_categories;

            $order_by = ' ORDER BY ' . (empty($category_order) ? '' : $category_order . ', ') . 'position DESC';

            $categories_request = '
                        SELECT
                        DISTINCT cp.id_category, pscl.*, SUM(si.weight) position,
                        GROUP_CONCAT(sw.word SEPARATOR \' \') as terms, count(distinct cp.id_product) as products_count,
                        CASE
                            WHEN lower(replace(pscl.name, \' \', \'\')) = :expr THEN 7
                            WHEN lower(replace(pscl.name, \' \', \'\')) LIKE :start_full_expr THEN 6
                            WHEN lower(replace(pscl.name, \' \', \'\')) LIKE :start_like_expr THEN 5
                            WHEN lower(replace(pscl.name, \' \', \'\')) LIKE :full_expr THEN 4
                            WHEN lower(replace(pscl.name, \' \', \'\')) LIKE :like_expr THEN 3
                            WHEN :match_and_expr THEN 2
                            WHEN :match_or_expr THEN 1
                            ELSE 0
                        END cat_position
                        FROM ' . _DB_PREFIX_ . 'search_index si
                        LEFT JOIN ' . _DB_PREFIX_ . 'search_word sw ON sw.id_word = si.id_word

                        LEFT JOIN ' . _DB_PREFIX_ . 'category_product cp ON cp.id_product=si.id_product
                        LEFT JOIN ' . _DB_PREFIX_ . 'category_group cg ON cg.id_category=cp.id_category
                        ' . (Configuration::get(AJS_ONLY_DEFAULT_CATEGORIES) ? Shop::addSqlAssociation('product', 'si', false) : '') . '
                        LEFT JOIN ' . _DB_PREFIX_ . 'category_lang pscl ON pscl.id_category=cp.id_category
                            AND pscl.id_lang=' . (int) $id_lang . '
                            AND pscl.id_shop = ' . (int) $id_shop . '
                        WHERE 1
                            AND (' . $criteria['where'] . ')
                            AND si.id_product IN(' . implode(',', $products) . ')
                            AND cp.`id_category` IN (' . $eligible_categories_request . ')
                            ' . (Configuration::get(AJS_ONLY_DEFAULT_CATEGORIES) ? ' AND cp.id_category = product_shop.id_category_default' : '') . '
                            AND cg.`id_group` ' . (!$this->id_customer ? '=' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP') : 'IN (
                        SELECT id_group FROM ' . _DB_PREFIX_ . 'customer_group
                        WHERE id_customer = ' . (int) $this->id_customer . ')')
                . ' GROUP BY cp.id_category'
                . $criteria['having']
                . $order_by
                . $categories_limit;

            $terms = explode(' ', pSQL($this->expr));
            $strParams = [
                ':expr' => '\'' . pSQL(implode('', $terms)) . '\'',
                ':start_full_expr' => '\'' . pSQL(implode('', $terms)) . '%\'',
                ':start_like_expr' => '\'' . pSQL(implode('%', $terms)) . '%\'',
                ':full_expr' => '\'%' . pSQL(implode('', $terms)) . '%\'',
                ':like_expr' => '\'%' . pSQL(implode('%', $terms)) . '%\'',
                ':match_and_expr' => 'lower(replace(pscl.name, \' \', \'\')) LIKE \'%' . implode('%\' AND lower(replace(pscl.name, \' \', \'\')) LIKE \'%', $terms) . '%\'',
                ':match_or_expr' => 'lower(replace(pscl.name, \' \', \'\')) LIKE \'%' . implode('%\' OR lower(replace(pscl.name, \' \', \'\')) LIKE \'%', $terms) . '%\'',
            ];

            $categories_request = strtr($categories_request, $strParams);

            $categories = Db::getInstance()->ExecuteS($categories_request);

            return $categories;
        } else {
            return [];
        }
    }

    private function getManufacturersOfProducts($id_lang, $id_shop, $products, $criteria, $ajax = false)
    {
        $nb_manufacturers = $ajax ? pSQL(Configuration::hasKey(AJS_MAX_MANUFACTURERS_KEY) ? Configuration::get(AJS_MAX_MANUFACTURERS_KEY) : 0) : 0;

        $manufacturer_order = (Configuration::hasKey(AJS_MANUFACTURERS_ORDER) ? Configuration::get(AJS_MANUFACTURERS_ORDER) : '');

        $manufacturers_request = '';

        if ($nb_manufacturers > 0) {
            $manufacturers_limit = ' LIMIT 0,' . $nb_manufacturers;

            $order_by = ' ORDER BY ' . (empty($manufacturer_order) ? '' : $manufacturer_order . ', ') . 'position DESC';

            $manufacturers_request = '
                         SELECT
                        DISTINCT m.id_manufacturer, m.*, SUM(si.weight) position,
                        GROUP_CONCAT(sw.word SEPARATOR \' \') as terms, count(distinct si.id_product) as products_count,
                        CASE
                            WHEN lower(replace(m.name, \' \', \'\')) = :expr THEN 5
                            WHEN lower(replace(m.name, \' \', \'\')) LIKE :full_expr THEN 4
                            WHEN lower(replace(m.name, \' \', \'\')) LIKE :like_expr THEN 3
                            WHEN :match_and_expr THEN 2
                            WHEN :match_or_expr THEN 1
                            ELSE 0
                        END man_position
                        FROM ' . _DB_PREFIX_ . 'search_index si
                        LEFT JOIN ' . _DB_PREFIX_ . 'search_word sw ON sw.id_word = si.id_word
                        ' . Shop::addSqlAssociation('product', 'si', false) . '
                        INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = si.id_product
                        INNER JOIN ' . _DB_PREFIX_ . 'manufacturer m ON p.id_manufacturer = m.id_manufacturer
                        WHERE 1
                            AND (' . $criteria['where'] . ')
                            AND si.id_product IN(' . implode(',', $products) . ')
                         GROUP BY m.id_manufacturer'
                . $criteria['having']
                . $order_by
                . $manufacturers_limit;

            $terms = explode(' ', pSQL($this->expr));
            $strParams = [
                ':expr' => '\'' . pSQL(implode('', $terms)) . '\'',
                ':full_expr' => '\'%' . pSQL(implode('', $terms)) . '%\'',
                ':like_expr' => '\'%' . pSQL(implode('%', $terms)) . '%\'',
                ':match_and_expr' => 'lower(replace(m.name, \' \', \'\')) LIKE \'%' . implode('%\' AND lower(replace(m.name, \' \', \'\')) LIKE \'%', $terms) . '%\'',
                ':match_or_expr' => 'lower(replace(m.name, \' \', \'\')) LIKE \'%' . implode('%\' OR lower(replace(m.name, \' \', \'\')) LIKE \'%', $terms) . '%\'',
            ];

            $manufacturers_request = strtr($manufacturers_request, $strParams);

            $manufacturers = Db::getInstance()->ExecuteS($manufacturers_request);

            return $manufacturers;
        } else {
            return [];
        }
    }

    private function getSuppliersOfProducts($id_lang, $id_shop, $products, $criteria, $ajax = false)
    {
        $nb_suppliers = $ajax ? pSQL(Configuration::hasKey(AJS_MAX_SUPPLIERS_KEY) ? Configuration::get(AJS_MAX_SUPPLIERS_KEY) : 0) : 0;

        $supplier_order = (Configuration::hasKey(AJS_SUPPLIERS_ORDER) ? Configuration::get(AJS_SUPPLIERS_ORDER) : '');

        $suppliers_request = '';

        if ($nb_suppliers > 0) {
            $suppliers_limit = ' LIMIT 0,' . $nb_suppliers;

            $order_by = ' ORDER BY ' . (empty($supplier_order) ? '' : $supplier_order . ', ') . 'position DESC';

            $suppliers_request = '
                         SELECT
                        DISTINCT su.id_supplier, su.*, SUM(si.weight) position,
                        GROUP_CONCAT(sw.word SEPARATOR \' \') as terms, count(distinct si.id_product) as products_count,
                        CASE
                            WHEN lower(replace(su.name, \' \', \'\')) = :expr THEN 5
                            WHEN lower(replace(su.name, \' \', \'\')) LIKE :full_expr THEN 4
                            WHEN lower(replace(su.name, \' \', \'\')) LIKE :like_expr THEN 3
                            WHEN :match_and_expr THEN 2
                            WHEN :match_or_expr THEN 1
                            ELSE 0
                        END sup_position
                        FROM ' . _DB_PREFIX_ . 'search_index si
                        LEFT JOIN ' . _DB_PREFIX_ . 'search_word sw ON sw.id_word = si.id_word
                        ' . Shop::addSqlAssociation('product', 'si', false) . '
                        INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = si.id_product
                        INNER JOIN ' . _DB_PREFIX_ . 'supplier su ON p.id_supplier = su.id_supplier
                        WHERE 1
                            AND (' . $criteria['where'] . ')
                            AND si.id_product IN(' . implode(',', $products) . ')
                         GROUP BY su.id_supplier'
                . $criteria['having']
                . $order_by
                . $suppliers_limit;

            $terms = explode(' ', pSQL($this->expr));
            $strParams = [
                ':expr' => '\'' . pSQL(implode('', $terms)) . '\'',
                ':full_expr' => '\'%' . pSQL(implode('', $terms)) . '%\'',
                ':like_expr' => '\'%' . pSQL(implode('%', $terms)) . '%\'',
                ':match_and_expr' => 'lower(replace(su.name, \' \', \'\')) LIKE \'%' . implode('%\' AND lower(replace(su.name, \' \', \'\')) LIKE \'%', $terms) . '%\'',
                ':match_or_expr' => 'lower(replace(su.name, \' \', \'\')) LIKE \'%' . implode('%\' OR lower(replace(su.name, \' \', \'\')) LIKE \'%', $terms) . '%\'',
            ];

            $suppliers_request = strtr($suppliers_request, $strParams);

            $suppliers = Db::getInstance()->ExecuteS($suppliers_request);

            return $suppliers;
        } else {
            return [];
        }
    }

    private function searchSynonyms($my_word)
    {
        $request = '
                        SELECT DISTINCT synonyms.id_word, sw.word
                        FROM ' . _DB_PREFIX_ . 'ambjolisearch_synonyms synonyms
                        LEFT JOIN ' . _DB_PREFIX_ . 'search_word sw
                            ON synonyms.id_word = sw.id_word
                        WHERE
                            synonyms.synonym LIKE "' . pSQL($my_word) . '"';

        $synonyms = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($request);
        $return = ['words' => [], 'ids' => []];
        if (is_array($synonyms)) {
            foreach ($synonyms as $synonym) {
                $return['words'][] = $synonym['word'];
                $return['ids'][] = $synonym['id_word'];
            }
        }

        return $return;
    }

    private function applyLevenshtein($my_word, $naked_word, $id_lang)
    {
        if (Tools::strlen($naked_word) <= 2) {
            return false;
        }

        // Levehnstein procedure
        $cuts = [];
        $cutting = '';
        $cutsize = 3;
        $source_word = '__' . $naked_word . '__';

        for ($i = 0, $max = (Tools::strlen($source_word) - $cutsize + 1); $i < $max; ++$i) {
            $cut = '';
            for ($j = 0; $j < $cutsize; ++$j) {
                $cut .= $source_word[$i + $j];
            }

            $cuts[] = $cut;
        }

        foreach ($cuts as $key => &$cut) {
            $cut = '%' . $cut . '%';
            $cut = preg_replace('/(%_{1,2})|(_{1,2}%)/', '', $cut);
        }

        $count = count($cuts);

        $clean_cuts = [];

        for ($i = 0; $i < $count; ++$i) {
            for ($j = $count - 1; $j >= 0; --$j) {
                if (((substr_count($cuts[$i], '%') == 1 && substr_count($cuts[$j], '%') == 1) && Tools::strlen($naked_word) > 5) || $cuts[$i] == $cuts[$j]) {
                    continue;
                } else {
                    if (!isset($clean_cuts[$cuts[$i] . '-' . $cuts[$j]]) && !isset($clean_cuts[$cuts[$j] . '-' . $cuts[$i]])) {
                        $clean_cuts[$cuts[$i] . '-' . $cuts[$j]] = '(sw.word LIKE "' . $cuts[$i] . '" AND sw.word LIKE "' . $cuts[$j] . '")';
                    }
                }
            }
        }

        $cutting = implode(' OR ', $clean_cuts);

        $request = '
            SELECT COUNT(sw.word) as nb_words
            FROM ' . _DB_PREFIX_ . 'search_word sw
            WHERE word="' . $naked_word . '"
                    AND  ' . ($this->language_ids ? 'sw.id_lang IN (' . implode(',', $this->language_ids) . ')' : 'sw.id_lang = ' . (int) $id_lang);

        $existing_words = (int) Db::getInstance()->getValue($request);

        if ($existing_words == 0) {
            $request = '
                SELECT
                DISTINCT sw.id_word, sw.word
                FROM ' . _DB_PREFIX_ . 'search_word sw
                WHERE 1
                    AND  ' . ($this->language_ids ? 'sw.id_lang IN (' . implode(',', $this->language_ids) . ')' : 'sw.id_lang = ' . (int) $id_lang) . '
                    AND sw.id_shop = ' . Context::getContext()->shop->id . '
                    AND (' . $cutting . ')';

            $this->module->log($request, __FILE__, __METHOD__, __LINE__, 'levenhstein $request');
            $filtered_results = Db::getInstance()->executeS($request, false);

            $weighted_results = [];
            while ($row = Db::getInstance()->nextRow($filtered_results)) {
                $lvs = levenshtein($naked_word, $row['word']);
                $weighted_results[$lvs][] = $row;
            }

            $settings = AmbJoliSearch::$approximation_settings[$this->approximation_level];

            $hard_limit = $settings['hard_limit']; // Do not accept a lvs higher than 3
            $span = $settings['span']; // How much distances should be shown
            $minimum_results = isset($settings['minimum_results']) ? Configuration::get(AJS_MAX_PRODUCTS_KEY) : 0; // Keep spanning if less than expected results are displayed

            $selected_results = [];
            for ($i = 0; $i <= $hard_limit; ++$i) {
                if (isset($weighted_results[$i]) && ($span > 0 || count($selected_results) < $minimum_results)) {
                    $selected_results = array_merge($selected_results, $weighted_results[$i]);
                    --$span;
                }
            }

            foreach ($selected_results as $result) {
                try {
                    Db::getInstance()->insert(
                        'ambjolisearch_synonyms',
                        [
                            'synonym' => $naked_word,
                            'id_word' => $result['id_word'],
                        ]
                    );

                    if (Db::getInstance()->getNumberError() == 0) {
                        $got_one = true;
                    }
                } catch (PrestaShopException $e) {
                    continue;
                }
            }
        }

        if (!isset($got_one)) {
            $got_one = false;
        }

        return $got_one;
    }

    private function getCategoryThumb($category, $id_lang)
    {
        $thumb = $category->id . '_thumb.jpg';
        if (file_exists(_PS_CAT_IMG_DIR_ . $thumb)) {
            return _THEME_CAT_DIR_ . $thumb;
        }

        return false;
    }

    public static function getWordMaxLength()
    {
        if (method_exists('Search', 'getWordMaxLength')) {
            $word_max_length = Search::getWordMaxLength();
        } elseif (defined('PS_SEARCH_MAX_WORD_LENGTH')) {
            $word_max_length = PS_SEARCH_MAX_WORD_LENGTH;
        } else {
            $word_max_length = (Configuration::hasKey('PS_SEARCH_MAX_WORD_LENGTH') ?
                Configuration::get('PS_SEARCH_MAX_WORD_LENGTH') : 15);
        }

        return $word_max_length;
    }

    public static function orderbyPrice(&$array, $order_way)
    {
        foreach ($array as &$row) {
            $row['price_tmp'] = Product::getPriceStatic($row['id_product'], true, (isset($row['id_product_attribute']) && !empty($row['id_product_attribute'])) ? (int) $row['id_product_attribute'] : null, 2);
        }

        unset($row);

        if (Tools::strtolower($order_way) == 'desc') {
            uasort($array, ['self', 'cmpPriceDesc']);
        } else {
            uasort($array, ['self', 'cmpPriceAsc']);
        }
        foreach ($array as &$row) {
            unset($row['price_tmp']);
        }
    }

    public function cmpPriceAsc($a, $b)
    {
        if ((float) $a['price_tmp'] < (float) $b['price_tmp']) {
            return -1;
        } elseif ((float) $a['price_tmp'] > (float) $b['price_tmp']) {
            return 1;
        }

        return 0;
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public function cmpPriceDesc($a, $b)
    {
        if ((float) $a['price_tmp'] < (float) $b['price_tmp']) {
            return 1;
        } elseif ((float) $a['price_tmp'] > (float) $b['price_tmp']) {
            return -1;
        }

        return 0;
    }

    protected function shouldEnableAddToCartButton(array $product)
    {
        if ($product['customizable'] == 2 || !empty($product['customization_required'])) {
            $shouldEnable = false;

            if (isset($product['customizations'])) {
                $shouldEnable = true;
                foreach ($product['customizations']['fields'] as $field) {
                    if ($field['required'] && !$field['is_customized']) {
                        $shouldEnable = false;
                    }
                }
            }
        } else {
            $shouldEnable = true;
        }

        $shouldEnable = $shouldEnable && (bool) $product['available_for_order'];

        if (!$product['allow_oosp']
            && ($product['quantity'] <= 0
            || $product['quantity'] < 1)
        ) {
            $shouldEnable = false;
        }

        return $shouldEnable;
    }

    public static function pluck($array, $properties)
    {
        $cleaned = [];
        foreach ($array as $full_item) {
            $item = [];
            foreach ($properties as $property) {
                if (isset($full_item[$property])) {
                    $item[$property] = $full_item[$property];
                }
            }
            if (count($item) > 0) {
                $cleaned[] = $item;
            }
        }

        return $cleaned;
    }

    public static function extractKeyWords($string, $id_lang, $indexation = false, $iso_code = false)
    {
        if (null === $string) {
            return [];
        }

        $sanitizedString = self::sanitize($string, $id_lang, $indexation, $iso_code, ' ');
        $words = explode(' ', $sanitizedString);
        if (preg_match('/[\x{2d}-\x{2f}\x{5f}]/u', $string) !== false) {
            $sanitizedString = self::sanitize($string, $id_lang, $indexation, $iso_code, '');
            $words2 = explode(' ', $sanitizedString);
            // foreach word containing hyphen, we want to index additional word removing the hyphen
            // eg: t-shirt => tshirt
            foreach ($words2 as $word) {
                if (strpos($word, '-') !== false) {
                    $word = str_replace('-', '', $word);
                    if (!empty($word)) {
                        $words[] = $word;
                    }
                }
            }

            $words = array_merge($words, $words2);
        }

        return array_unique($words);
    }

    public static function sanitize($string, $id_lang, $indexation = false, $iso_code = false, $hyphens_replacement = ' ')
    {
        if (null === $string || empty($string = trim($string))) {
            return '';
        }
        $string = Tools::strtolower(strip_tags($string));
        $string = html_entity_decode($string, ENT_NOQUOTES, 'utf-8');
        // $string = preg_replace('/([' . AMB_PREG_CLASS_NUMBERS . ']+)[' . AMB_PREG_CLASS_PUNCTUATION . ']+(?=[' . AMB_PREG_CLASS_NUMBERS . '])/u', '\1', $string);
        $string = preg_replace('/([^' . AMB_PREG_CLASS_PUNCTUATION . ']+)[' . AMB_PREG_CLASS_PUNCTUATION . ']+(?=[^' . AMB_PREG_CLASS_PUNCTUATION . '\s])/u', '\1-', $string);
        $string = preg_replace('/[' . AMB_PREG_CLASS_SEARCH_EXCLUDE . ']+/u', ' ', $string);

        $string = str_replace('-', $hyphens_replacement, $string);

        if (!$indexation) {
            $words = explode(' ', $string);
            $processed_words = [];
            // search for aliases for each word of the query
            $query = '
                SELECT a.alias, a.search
                FROM `' . _DB_PREFIX_ . 'alias` a
                WHERE \'' . pSQL($string) . '\' %s AND `active` = 1
            ';

            // check if we can we use '\b' (faster)
            $useICU = (bool) Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->getValue(
                'SELECT 1 FROM DUAL WHERE \'icu regex\' REGEXP \'\\\\bregex\''
            );
            $aliases = Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->executeS(
                sprintf(
                    $query,
                    $useICU
                        ? 'REGEXP CONCAT(\'\\\\b\', alias, \'\\\\b\')'
                        : 'REGEXP CONCAT(\'(^|[[:space:]]|[[:<:]])\', alias, \'([[:space:]]|[[:>:]]|$)\')'
                )
            );

            foreach ($aliases  as $alias) {
                $processed_words = array_merge($processed_words, explode(' ', $alias['search']));
                // delete words that are being replaced with aliases
                $words = array_diff($words, explode(' ', $alias['alias']));
            }
            $string = implode(' ', array_unique(array_merge($processed_words, $words)));
            $string = str_replace(['.', '_'], '', $string);
            /*
            if (!$keep_hyphens) {
                $string = ltrim(preg_replace('/([^ ])-/', '$1 ', ' ' . $string));
            }
            */
        }

        $blacklist = Tools::strtolower(Configuration::get('PS_SEARCH_BLACKLIST', $id_lang));
        if (!empty($blacklist)) {
            $string = preg_replace('/(?<=\s)(' . $blacklist . ')(?=\s)/Su', '', $string);
            $string = preg_replace('/^(' . $blacklist . ')(?=\s)/Su', '', $string);
            $string = preg_replace('/(?<=\s)(' . $blacklist . ')$/Su', '', $string);
            $string = preg_replace('/^(' . $blacklist . ')$/Su', '', $string);
        }

        // If the language is constituted with symbol and there is no "words", then split every chars
        if (in_array($iso_code, ['zh', 'tw', 'ja'])) {
            // Cut symbols from letters
            $symbols = '';
            $letters = '';
            foreach (explode(' ', $string) as $mb_word) {
                if (strlen(Tools::replaceAccentedChars($mb_word)) == mb_strlen(Tools::replaceAccentedChars($mb_word))) {
                    $letters .= $mb_word . ' ';
                } else {
                    $symbols .= $mb_word . ' ';
                }
            }

            if (preg_match_all('/./u', $symbols, $matches)) {
                $symbols = implode(' ', $matches[0]);
            }

            $string = $letters . $symbols;
        } elseif ($indexation) {
            $minWordLen = (int) Configuration::get('PS_SEARCH_MINWORDLEN');
            if ($minWordLen > 1) {
                --$minWordLen;
                $string = preg_replace('/(?<=\s)[^\s]{1,' . $minWordLen . '}(?=\s)/Su', ' ', $string);
                $string = preg_replace('/^[^\s]{1,' . $minWordLen . '}(?=\s)/Su', '', $string);
                $string = preg_replace('/(?<=\s)[^\s]{1,' . $minWordLen . '}$/Su', '', $string);
                $string = preg_replace('/^[^\s]{1,' . $minWordLen . '}$/Su', '', $string);
            }
        }

        $string = Tools::replaceAccentedChars(trim(preg_replace('/\s+/', ' ', $string)));

        return $string;
    }
}
