<?php
/**
 * 2007-2022 ETS-Soft
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
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2022 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('_PS_VERSION_'))
    exit;

class Ets_crosssell_db
{
    public static $instance;
    public $is17 = true;
    public function __construct()
    {
        $this->is17 = version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_crosssell', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ets_crosssell_db();
        }
        return self::$instance;
    }
    public function searchProduct()
    {
        if (($query = Tools::getValue('q', false)) && Validate::isCleanHtml($query))
        {
            $imageType = ImageType::getFormattedName('cart');
            if ($pos = strpos($query, ' (ref:')) {
                $query = Tools::substr($query, 0, $pos);
            }
            $excludeIds = Tools::getValue('excludeIds', false);
            $excludedProductIds = array();
            if ($excludeIds && $excludeIds != 'NaN' && Validate::isCleanHtml($excludeIds)) {
                $excludeIds = implode(',', array_map(array($this, 'intval'), explode(',', $excludeIds)));
                if($excludeIds && ($ids = explode(',',$excludeIds)) ) {
                    foreach($ids as $id) {
                        $id = explode('-',$id);
                        if(isset($id[0]) && isset($id[1]) && !$id[1]) {
                            $excludedProductIds[] = (int)$id[0];
                        }
                    }
                }
            } else {
                $excludeIds = false;
            }
            $excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', true);
            $exclude_packs = (bool)Tools::getValue('exclude_packs', true);
            if (version_compare(_PS_VERSION_, '1.6.1.0', '<'))
            {
                $imgLeftJoin = ' LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`) '.Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1');
            }
            else
            {
                $imgLeftJoin = ' LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ' AND image_shop.cover = 1) ';
            }
            $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
            		FROM `' . _DB_PREFIX_ . 'product` p
            		' . Shop::addSqlAssociation('product', 'p') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
            		'. pSQL($imgLeftJoin) .' 
            		LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)Context::getContext()->language->id . ')
            		LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (p.`id_product` = ps.`id_product`) 
            		WHERE '.($excludedProductIds ? 'p.`id_product` NOT IN('.pSQL(implode(',',$excludedProductIds)).') AND ' : '').' (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\' OR p.id_product = '.(int)$query.') AND ps.`active` = 1 AND ps.`id_shop` = '.(int)Context::getContext()->shop->id .
                   ($excludeVirtuals ? ' AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
                   ($exclude_packs ? ' AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
                   ($this->is17 ? ' AND p.state=1':'').
                   '  GROUP BY p.id_product';

            if (($items = Db::getInstance()->executeS($sql)))
            {
                $results = array();
                foreach ($items as $item)
                {
                    if(($id_product_attribute = Product::getDefaultAttribute($item['id_product'])) && ($image = self::getCombinationImageById($id_product_attribute,Context::getContext()->language->id)))
                    {
                        $item['id_image'] = $image['id_image'];
                    }
                    $results[] = array(
                        'id_product' => (int)($item['id_product']),
                        'id_product_attribute' => 0,
                        'name' => $item['name'],
                        'attribute' => '',
                        'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' =>$item['id_image'] ?  str_replace('http://', Tools::getShopProtocol(), Context::getContext()->link->getImageLink($item['link_rewrite'], $item['id_image'], $imageType)): self::getNoImageDefault($imageType),
                    );
                }
                if ($results)
                {
                    foreach ($results as &$item)
                        echo trim($item['id_product'] . '|' . (int)($item['id_product_attribute']) . '|' . Tools::ucfirst($item['name']). '|' . $item['attribute'] . '|' . $item['ref'] . '|' . $item['image']) . "\n";
                }
            }
            die;
        }
        die;
    }
    public static function getNoImageDefault($type_image)
    {
        $context = Context::getContext();
        if(file_exists(_PS_PROD_IMG_DIR_.$context->language->iso_code.'-default-'.$type_image.'.jpg'))
            return $context->link->getMediaLink(_PS_PROD_IMG_.$context->language->iso_code.'-default-'.$type_image.'.jpg');
        else
        {
            $langDefault = new Language(Configuration::get('PS_LANG_DEFAULT'));
            if(file_exists(_PS_PROD_IMG_DIR_.$langDefault->iso_code.'-default-'.$type_image.'.jpg'))
                return $context->link->getMediaLink(_PS_PROD_IMG_.$langDefault->iso_code.'-default-'.$type_image.'.jpg');
        }
    }
    public function getBlockProducts($products)
    {
        if (!$products)
            return false;
        if (!is_array($products))
        {
            $IDs = explode(',', $products);
            $products = array();
            foreach ($IDs as $ID) {
                if ($ID &&($tmpIDs = explode('-', $ID))) {
                    $products[] = array(
                        'id_product' => $tmpIDs[0],
                        'id_product_attribute' => !empty($tmpIDs[1])? $tmpIDs[1] : 0,
                    );
                }
            }
        }
        if($products)
        {
            $context = Context::getContext();
            $id_group = isset($context->customer->id) && $context->customer->id? Customer::getDefaultGroupId((int)$context->customer->id) : (int)Group::getCurrent()->id;
            $group = new Group($id_group);
            $useTax = $group->price_display_method? false : true;
            foreach($products as $key=> &$product)
            {
                $p = new Product($product['id_product'], true, Context::getContext()->language->id, Context::getContext()->shop->id);
                if(!Validate::isLoadedObject($p) || !$p->active)
                    unset($products[$key]);
                $product['link_rewrite'] = $p->link_rewrite;
                if(!$product['id_product_attribute'] && $p->hasAttributes())
                {
                    $product['id_product_attribute'] = Product::getDefaultAttribute($p->id);
                }
                $product['price'] = Tools::displayPrice($p->getPrice($useTax,$product['id_product_attribute'] ? $product['id_product_attribute'] : null));
                if(($oldPrice = $p->getPriceWithoutReduct(!$useTax,$product['id_product_attribute'] ? $product['id_product_attribute'] : null)) && $oldPrice!=$product['price'])
                {
                    $product['price_without_reduction'] = Tools::convertPrice($oldPrice);
                }
                if (isset($product['price_without_reduction']) && $product['price_without_reduction'] != $product['price'])
                {
                    $product['specific_prices'] = $p->specificPrice;
                }
                if(isset($product['specific_prices']) && $product['specific_prices'] && $product['specific_prices']['to']!='0000-00-00 00:00:00')
                {
                    $product['specific_prices_to'] = $product['specific_prices']['to'];
                }
                $product['name'] = $p->name;
                $product['description_short'] = $p->description_short;
                $image = ($product['id_product_attribute'] && ($image = self::getCombinationImageById($product['id_product_attribute'],$context->language->id))) ? $image : Product::getCover($product['id_product']);
                $product['link'] = $context->link->getProductLink($product,null,null,null,null,null,$product['id_product_attribute'] ? $product['id_product_attribute'] : 0);
                $product['id_image'] = isset($image['id_image']) && $image['id_image'] ? $image['id_image'] : $context->language->iso_code.'-default';
                if (!$this->is17 || Context::getContext()->controller->controller_type == 'admin')
                {
                    $product['add_to_cart_url'] = isset($context->customer) && $this->is17 ? $context->link->getAddToCartURL((int)$product['id_product'], (int)$product['id_product_attribute']) : '';
                    $imageType = ImageType::getFormattedName('home');
                    $product['image'] = $context->link->getImageLink($p->link_rewrite, isset($image['id_image']) ? $image['id_image'] : $context->language->iso_code.'-default', $imageType);
                    $product['price_tax_exc'] = Product::getPriceStatic( (int)$product['id_product'], false, (int)$product['id_product_attribute'], (!$useTax ? 2 : 6), null, false, true, $p->minimal_quantity);
                    $product['available_for_order'] = $p->available_for_order;
                    if($product['id_product_attribute'])
                    {
                        $p->id_product_attribute = $product['id_product_attribute'];
                        $product['attributes'] = $p->getAttributeCombinationsById((int)$product['id_product_attribute'],$context->language->id);
                    }
                }
                
                if ($this->is17 && Context::getContext()->controller->controller_type != 'admin')
                {
                    $product['image_id'] = $product['id_image'];
                }
                $product['is_available'] = $p->checkQty(1);
                $product['allow_oosp'] = Product::isAvailableWhenOutOfStock($p->out_of_stock);
                $product['show_price'] = $p->show_price;
                if (!$this->is17)
                {
                    $product['out_of_stock'] = $p->out_of_stock;
                    $product['id_category_default'] = $p->id_category_default;
                    $product['ean13'] = $p->ean13;
                }
            }
            unset($context);
        }
        if($products && Context::getContext()->controller->controller_type != 'admin')
        {
            return $this->is17? $this->productsForTemplate($products) : Product::getProductsProperties(Context::getContext()->language->id, $products);
        }
        return $products;
    }
    public static function getCombinationImageById($id_product_attribute, $id_lang)
    {
        if(version_compare(_PS_VERSION_,'1.6.1.0', '>=')) {
            return Product::getCombinationImageById($id_product_attribute, $id_lang);
        }
        else
        {
            if (!Combination::isFeatureActive() || !$id_product_attribute) {
                return false;
            }
            $result = Db::getInstance()->executeS('
                SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
                FROM `'._DB_PREFIX_.'product_attribute_image` pai
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (il.`id_image` = pai.`id_image`)
                LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = pai.`id_image`)
                WHERE pai.`id_product_attribute` = '.(int)$id_product_attribute.' AND il.`id_lang` = '.(int)$id_lang.' ORDER by i.`position` LIMIT 1'
            );
            if (!$result) {
                return false;
            }
            return $result[0];
        }
    }
    public function productsForTemplate($products)
    {
        if (!$products || !is_array($products))
            return array();
        $assembler = new ProductAssembler(Context::getContext());
        $presenterFactory = new ProductPresenterFactory(Context::getContext());
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
            new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
                Context::getContext()->link
            ),
            Context::getContext()->link,
            new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
            new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
            Context::getContext()->getTranslator()
        );
        $products_for_template = array();
        foreach ($products as $item) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($item),
                Context::getContext()->language
            );
        }
        return $products_for_template;
    }
    public static function unInstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'ets_crosssell_product_viewed`');
    }
    public static function installDb()
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_crosssell_product_viewed` ( 
            `id_ets_crosssell_product_viewed` INT(11) NOT NULL AUTO_INCREMENT ,
            `id_product` INT(11) NOT NULL , 
            `viewed` INT(11) NOT NULL , 
            PRIMARY KEY (`id_ets_crosssell_product_viewed`,`id_product`),INDEX(`viewed`)) ENGINE = InnoDB');
    }
    public static function addProductViewed($id_product)
    {
        if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_crosssell_product_viewed` WHERE id_product='.(int)$id_product))
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_crosssell_product_viewed` (id_product,viewed) VALUES('.(int)$id_product.',1)');
        else
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_crosssell_product_viewed` SET viewed=viewed+1 WHERE id_product='.(int)$id_product);
        return true;
    }
    public static function getCategoriesByIDs($id_categories)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'category` c
        INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (c.id_category=cs.id_category AND cs.id_shop="'.(int)Context::getContext()->shop->id.'")
        LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.id_category=cl.id_category AND cl.id_lang="'.(int)Context::getContext()->language->id.'")
        WHERE c.active=1  AND c.id_category IN ('.implode(',',array_map('intval',$id_categories)).') GROUP BY c.id_category');
    }
    public static function getTreeIds($id_root)
    {
        $ids = array();
        $ids[] = $id_root;
        if ($children = Category::getChildren($id_root,Context::getContext()->language->id))
            foreach ($children as $child) {
                $ids = array_merge($ids, self::getTreeIds($child['id_category']));
            }
        $ids[]=$id_root;
        return array_unique($ids);
    }
    public static function getProducts($id_category = false, $page = 0, $per_page = 12, $order_by = 'cp.position', $id_products = false,$not_id_products = false,$excludedOld = false,$includeSub=false,$id_manufacturer=0,$no_in_cart=false)
    {
        $page = (int)$page;
        if ($page <= 0)
            $page = 1;
        $per_page = (int)$per_page;
        if ($per_page <= 0)
            $per_page = 12;
        $active = true;
        $front = true;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        $id_lang = (int)Context::getContext()->language->id;
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        if ($order_by && !in_array($order_by, array('price asc', 'price desc', 'pl.name asc', 'pl.name desc', 'cp.position asc', 'p.id_product desc', 'rand')))
            $order_by = 'cp.position asc';
        if ($order_by == 'price asc') {
            $order_by = 'orderprice asc';
        } elseif ($order_by == 'price desc') {
            $order_by = 'orderprice desc';
        }
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        $sql = 'SELECT DISTINCT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . ($prev_version? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)':' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, IFNULL(image_shop.`id_image`, i.`id_image`) id_image,
    					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
    					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice
                FROM `' . _DB_PREFIX_ . 'product` p'
            .Shop::addSqlAssociation('product', 'p').
            'INNER JOIN `'._DB_PREFIX_.'category_product` cp ON product_shop.`id_product` = cp.`id_product`
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('cl') . ')'.
            (!$prev_version?
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = p.id_product)'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1').'':
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')'
            ).
            (
            Tools::getValue('id_ets_css_sub_category')?' LEFT JOIN `'._DB_PREFIX_.'category_product` cp2 ON (cp2.id_product=p.id_product)':''
            )
            .Product::sqlStock('p', 0, false, Context::getContext()->shop).'
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . ')'.
            (!$prev_version?
                'LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = product_shop.`id_product`)'. Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1') :
                'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = product_shop.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            ).'
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')	
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                '.($excludedOld && (Context::getContext()->customer->isLogged() || isset(Context::getContext()->cookie->id_cart) && Context::getContext()->cookie->id_cart) ?
                ((int)Context::getContext()->customer->isLogged() ? '
                                LEFT JOIN (
                                    SELECT od.product_id as id_product
                                    FROM `'._DB_PREFIX_.'order_detail` od
                                    JOIN `'._DB_PREFIX_.'orders` o ON od.id_order=o.id_order
                                    WHERE o.id_customer='.(int)Context::getContext()->customer->id.'
                                ) od2 on p.id_product=od2.id_product
                            ' : '').(isset(Context::getContext()->cookie->id_cart) && Context::getContext()->cookie->id_cart ? '
                                LEFT JOIN `'._DB_PREFIX_.'cart_product` cap ON cap.id_product=p.id_product AND cap.id_cart='.(int)Context::getContext()->cookie->id_cart
                    : '') : ''
            ).'
                WHERE product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id . '
                '.(version_compare(_PS_VERSION_, '1.7', '>=') ? ' AND p.state=1':'')
            .(($id_product = (int)Tools::getValue('id_product')) ? ' AND p.id_product!="'.(int)$id_product.'"':'')
            .($id_category ? ' AND ' . (!$includeSub ? 'cp.`id_category` = ' . (int)$id_category : 'cp.`id_category` IN(' . implode(',', self::getTreeIds((int)$id_category)) . ')') : '')
            .(Tools::getValue('id_ets_css_sub_category') ? ' AND cp2.id_category="'.(int)Tools::getValue('id_ets_css_sub_category').'"':'')
            . ($active ? ' AND product_shop.`active` = 1' : '')
            . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
            . ($id_products ? ' AND product_shop.`id_product` IN ('.(implode(',',$id_products)).')' : '')
            .($excludedOld ? (((int)Context::getContext()->customer->isLogged() ? ' AND od2.id_product is NULL ' : '')
                .(isset(Context::getContext()->cookie->id_cart) && Context::getContext()->cookie->id_cart ? ' AND cap.id_product is NULL ' : '')) : '')
            . ($not_id_products ? ' AND product_shop.`id_product` NOT IN ('.(implode(',',$not_id_products)).')' : '')
            .($id_manufacturer ? ' AND m.id_manufacturer ="'.(int)$id_manufacturer.'"':'')
            .($no_in_cart && Context::getContext()->cart->id ? ' AND product_shop.id_product NOT IN (SELECT id_product FROM `'._DB_PREFIX_.'cart_product` WHERE id_cart="'.(int)Context::getContext()->cart->id.'")':'')
            . ' GROUP BY product_shop.id_product'
            . (!Configuration::get('ETS_CS_OUT_OF_STOCK')? ' HAVING quantity > 0 ' : '')
            . (Configuration::get('ETS_CS_EXCL_FREE_PRODUCT') ? ' AND product_shop.price >0':'')
            . ( ($order_by) ? ($order_by != 'rand' ? ' ORDER BY ' . pSQL($order_by) : ' ORDER BY '.pSQL(self::getRandomSeed())) : '') . '
                LIMIT ' . (int)($page-1)*$per_page . ',' . (int)$per_page;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, true);
        if (!$products) {
            return array();
        }
        if ($order_by == 'orderprice asc') {
            Tools::orderbyPrice($products, 'asc');
        } elseif ($order_by == 'orderprice desc') {
            Tools::orderbyPrice($products, 'desc');
        }
        $products = Product::getProductsProperties($id_lang, $products);
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $products = Ets_crosssell::productsForTemplate($products);
        }
        return $products;
    }
    public static function getRandomSeed()
    {
        $sorts = Ets_crosssell::getSortOptions();
        $rands_key = array_rand($sorts);
        return $sorts[isset($rands_key[0]) ? $rands_key[0] : 1];
    }
    public static function getMostViewedProducts($perpage=12,$order_by ='pviewed.viewed DESC')
    {
        $is17 = version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $active = true;
        $front = true;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        $sql = 'SELECT DISTINCT p.*,pviewed.viewed as total_viewed, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . ($prev_version? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)':' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, IFNULL(image_shop.`id_image`, i.`id_image`) id_image,
    					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
    					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice
                FROM `' . _DB_PREFIX_ . 'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                INNER JOIN `'._DB_PREFIX_.'category_product` cp ON product_shop.`id_product` = cp.`id_product`
                LEFT JOIN `'._DB_PREFIX_.'ets_crosssell_product_viewed` pviewed ON (pviewed.id_product=product_shop.id_product)
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('cl') . ')'.
            (!$prev_version?
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = product_shop.id_product)'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1').'':
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (product_shop.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')'
            )
            .Product::sqlStock('p', 0, false, Context::getContext()->shop).'
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (product_shop.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')'.
            (!$prev_version?
                'LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = product_shop.`id_product`)'. Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1') :
                'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` =product_shop.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            ).'
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)Context::getContext()->language->id.')	
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                WHERE pviewed.viewed >0 '.($is17 ? ' AND p.state=1':'').' AND product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id
            . ($active ? ' AND product_shop.`active` = 1' : '')
            .(($id_product = (int)Tools::getValue('id_product')) ? ' AND p.id_product!="'.(int)$id_product.'"':'')
            .(Tools::getValue('id_ets_css_sub_category') ? ' AND cp.id_category="'.(int)Tools::getValue('id_ets_css_sub_category').'"':'')
            . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
            . (Configuration::get('ETS_CS_EXCL_FREE_PRODUCT') ? ' AND product_shop.price >0':'')
            . ' GROUP BY product_shop.id_product'
            . (!Configuration::get('ETS_CS_OUT_OF_STOCK')? ' HAVING quantity > 0 ' : '')
            . ( ($order_by) ? ($order_by != 'rand' ? ' ORDER BY ' . pSQL($order_by) : ' ORDER BY '.pSQL(self::getRandomSeed()) ) : '') . '
                LIMIT 0,'.$perpage ;
        $products = Db::getInstance()->executeS($sql);
        if (!$products) {
            return array();
        }
        if ($order_by == 'orderprice asc') {
            Tools::orderbyPrice($products, 'asc');
        } elseif ($order_by == 'orderprice desc') {
            Tools::orderbyPrice($products, 'desc');
        }
        $products = Product::getProductsProperties(Context::getContext()->language->id, $products);
        if ($is17) {
            $products = Ets_crosssell::productsForTemplate($products);
        }
        return $products;
    }
    public static function getProductYouMightAlsoLike($id_product=0,$count_product=8,$order_by ='total_product DESC')
    {
        $is17 = version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $active = true;
        $front = true;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $id_cart = (int)Tools::getValue('id_cart',Context::getContext()->cart->id);
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        $sql = 'SELECT DISTINCT p.*,count(accessory.id_product_2) as total_product, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . ($prev_version? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)':' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, IFNULL(image_shop.`id_image`, i.`id_image`) id_image,
    					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
    					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice
                FROM `' . _DB_PREFIX_ . 'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                INNER JOIN `'._DB_PREFIX_.'category_product` cp ON product_shop.`id_product` = cp.`id_product`
                INNER JOIN `'._DB_PREFIX_.'accessory` accessory ON (accessory.id_product_2=p.id_product '.($id_product ? ' AND accessory.id_product_1="'.(int)$id_product.'"':'').')
                '.(!$id_product ? ' LEFT JOIN `'._DB_PREFIX_.'cart_product` cart_product ON (cart_product.id_product= accessory.id_product_1 AND cart_product.id_cart="'.(int)$id_cart.'")':'').'
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('cl') . ')'.
            (!$prev_version?
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = product_shop.id_product)'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1').'':
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (product_shop.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')'
            )
            .Product::sqlStock('p', 0, false, Context::getContext()->shop).'
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (product_shop.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')'.
            (!$prev_version?
                'LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = product_shop.`id_product`)'. Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1') :
                'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = product_shop.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            ).'
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)Context::getContext()->language->id.')	
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                WHERE product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id
            .($is17 ? ' AND p.state=1':'')
            .(($id_product = (int)Tools::getValue('id_product')) ? ' AND p.id_product!="'.(int)$id_product.'"':'')
            .(!Configuration::get('ETS_CS_OUT_OF_STOCK') ? ' AND stock.quantity>0':'')
            . (Configuration::get('ETS_CS_EXCL_FREE_PRODUCT') ? ' AND product_shop.price >0':'')
            . ($active ? ' AND product_shop.`active` = 1' : '')
            . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
            .(!$id_product ? ' AND cart_product.id_cart="'.(int)$id_cart.'"':'')
            .(Tools::getValue('id_ets_css_sub_category') ? ' AND cp.id_category="'.(int)Tools::getValue('id_ets_css_sub_category').'"':'')
            . ' GROUP BY product_shop.id_product '
            . ( ($order_by) ? ($order_by != 'rand' ? ' ORDER BY ' . pSQL($order_by) : ' ORDER BY '.pSQL(self::getRandomSeed())) : '') . '
                LIMIT 0,'.$count_product ;
        $products = Db::getInstance()->executeS($sql);
        if (!$products) {
            return array();
        }
        if ($order_by == 'orderprice asc') {
            Tools::orderbyPrice($products, 'asc');
        } elseif ($order_by == 'orderprice desc') {
            Tools::orderbyPrice($products, 'desc');
        }
        $products = Product::getProductsProperties(Context::getContext()->language->id, $products);
        if ($is17) {
            $products = Ets_crosssell::productsForTemplate($products);
        }
        return $products;
    }
    public static function getProductPurchasedTogether($id_product=0,$count_product=8,$order_by ='total_cart DESC')
    {
        $is17 = version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $active = true;
        $front = true;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        if(!$id_product)
            $sql_cart = 'SELECT procart.id_product,count(procart.id_product) as total_cart FROM `'._DB_PREFIX_.'cart_product` procart 
            INNER JOIN (
            	select procart2.id_cart FROM `'._DB_PREFIX_.'cart_product` procart2 
                INNER JOIN `'._DB_PREFIX_.'cart_product` procart3 ON (procart2.id_product= procart3.id_product) 
                WHERE procart3.id_cart="'.(int)Context::getContext()->cart->id.'" GROUP BY procart2.id_cart
            ) as procart4 ON (procart4.id_cart= procart.id_cart)
            LEFT JOIN `'._DB_PREFIX_.'cart_product` procart5 on (procart5.id_product = procart.id_product AND procart5.id_cart="'.(int)Context::getContext()->cart->id.'")
            WHERE procart5.id_cart is null
            group by procart.id_product';
        else
            $sql_cart ='SELECT procart.id_product,count(procart.id_product) as total_cart FROM `'._DB_PREFIX_.'cart_product` procart
            INNER JOIN `'._DB_PREFIX_.'cart_product` procart2 on (procart2.id_cart= procart.id_cart)
            WHERE procart2.id_product="'.(int)$id_product.'" AND procart.id_product!="'.(int)$id_product.'"
            GROUP BY procart.id_product';
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        $sql = 'SELECT DISTINCT p.*,cart_product.total_cart, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . ($prev_version? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)':' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, IFNULL(image_shop.`id_image`, i.`id_image`) id_image,
    					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
    					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice
                FROM `' . _DB_PREFIX_ . 'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                INNER JOIN `'._DB_PREFIX_.'category_product` cp ON product_shop.`id_product` = cp.`id_product`
                LEFT JOIN (
                    '.$sql_cart.'
                ) as cart_product ON (cart_product.id_product= p.id_product)
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('cl') . ')'.
            (!$prev_version?
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = product_shop.id_product)'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1').'':
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')'
            )
            .Product::sqlStock('p', 0, false, Context::getContext()->shop).'
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (product_shop.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')'.
            (!$prev_version?
                'LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = product_shop.`id_product`)'. Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1') :
                'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            ).'
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)Context::getContext()->language->id.')	
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                WHERE total_cart>0 '.($is17 ? ' AND p.state=1':'').' AND product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id
            . ($active ? ' AND product_shop.`active` = 1' : '')
            .(($id_product = (int)Tools::getValue('id_product')) ? ' AND p.id_product!="'.(int)$id_product.'"':'')
            .(!Configuration::get('ETS_CS_OUT_OF_STOCK') ? ' AND stock.quantity>0':'')
            . (Configuration::get('ETS_CS_EXCL_FREE_PRODUCT') ? ' AND product_shop.price >0':'')
            .(Context::getContext()->cart->id ? ' AND product_shop.id_product NOT IN (SELECT id_product FROM `'._DB_PREFIX_.'cart_product` WHERE id_cart="'.(int)Context::getContext()->cart->id.'")':'')
            . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
            .(Tools::getValue('id_ets_css_sub_category') ? ' AND cp.id_category="'.(int)Tools::getValue('id_ets_css_sub_category').'"':'')
            . ' GROUP BY product_shop.id_product'
            . ( ($order_by) ? ($order_by != 'rand' ? ' ORDER BY ' . pSQL($order_by) : ' ORDER BY '.pSQl(self::getRandomSeed())) : '') . '
                LIMIT 0,'.$count_product ;
        $products = Db::getInstance()->executeS($sql);
        if (!$products) {
            return array();
        }
        if ($order_by == 'orderprice asc') {
            Tools::orderbyPrice($products, 'asc');
        } elseif ($order_by == 'orderprice desc') {
            Tools::orderbyPrice($products, 'desc');
        }
        $products = Product::getProductsProperties(Context::getContext()->language->id, $products);
        if ($is17) {
            $products = Ets_crosssell::productsForTemplate($products);
        }
        return $products;
    }
    public static function getPricesDrop(
        $id_lang,
        $page_number = 0,
        $nb_products = 10,
        $count = false,
        $order_by = null,
        $order_way = null,
        $beginning = false,
        $ending = false,
        Context $context = null
    ) {
        if (!Validate::isBool($count)) {
            die(Tools::displayError());
        }

        if (!$context) {
            $context = Context::getContext();
        }
        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'price';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        $current_date = date('Y-m-d H:i:00');
        $ids_product = self::_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context);

        $tab_id_product = array();
        foreach ($ids_product as $product) {
            if (is_array($product)) {
                $tab_id_product[] = (int) $product['id_product'];
            } else {
                $tab_id_product[] = (int) $product;
            }
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP')) . ')
                WHERE cp.`id_product` = p.`id_product`)';
        }

        if ($count) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT COUNT(DISTINCT p.`id_product`)
            FROM `' . _DB_PREFIX_ . 'product` p
            ' . Shop::addSqlAssociation('product', 'p') . '
            WHERE product_shop.`active` = 1 '.(version_compare(_PS_VERSION_, '1.7', '>=') ? ' AND p.state=1':'').'
            AND product_shop.`show_price` = 1
            ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
            ' . ((!$beginning && !$ending) ? 'AND p.`id_product` IN(' . ((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0) . ')' : '') . '
            ' . $sql_groups);
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]) . '.`' . pSQL($order_by[1]) . '`';
        }

        $sql = '
        SELECT
            p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`,
            IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute,
            pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`,
            pl.`name`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
            DATEDIFF(
                p.`date_add`,
                DATE_SUB(
                    "' . date('Y-m-d') . ' 00:00:00",
                    INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
                )
            ) > 0 AS new
        FROM `' . _DB_PREFIX_ . 'product` p
        ' . Shop::addSqlAssociation('product', 'p') . '
        LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.id_product=p.id_product)
        LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
            ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
        ' . Product::sqlStock('p', 0, false, $context->shop) . '
        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . '
        )
        LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
        WHERE product_shop.`active` = 1
        AND product_shop.`show_price` = 1'
            .(version_compare(_PS_VERSION_, '1.7', '>=') ? ' AND p.state=1':'')
            .(($id_product = (int)Tools::getValue('id_product')) ? ' AND p.id_product!="'.(int)$id_product.'"':'')
            .(!Configuration::get('ETS_CS_OUT_OF_STOCK') ? ' AND stock.quantity>0':'')
            . (Configuration::get('ETS_CS_EXCL_FREE_PRODUCT') ? ' AND product_shop.price >0':'')
            .(Tools::getValue('id_ets_css_sub_category') ? ' AND cp.id_category="'.(int)Tools::getValue('id_ets_css_sub_category').'"':'').'
        ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
        ' . ((!$beginning && !$ending) ? ' AND p.`id_product` IN (' . ((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0) . ')' : '') . '
        ' . $sql_groups . '
        GROUP BY p.id_product
        ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . pSQL($order_by) . ' ' . pSQL($order_way) . '
        LIMIT ' . (int) (($page_number - 1) * $nb_products) . ', ' . (int) $nb_products;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }

        return Product::getProductsProperties($id_lang, $result);
    }
    protected static function _getProductIdByDate($beginning, $ending, Context $context = null, $with_combination = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        $ids = Address::getCountryAndState($id_address);
        $id_country = $ids && $ids['id_country'] ? (int) $ids['id_country'] : (int) Configuration::get('PS_COUNTRY_DEFAULT');

        return SpecificPrice::getProductIdByDate(
            $context->shop->id,
            $context->currency->id,
            $id_country,
            $context->customer->id_default_group,
            $beginning,
            $ending,
            0,
            $with_combination
        );
    }
    public static function getTopRatedProducts($nbProduct,$order_by ='total_grade DESC')
    {

        if(Module::isInstalled('ets_reviews') || Module::isInstalled('productcomments'))
        {
            $is17 = version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
            $active = true;
            $front = true;
            $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
            if (!Validate::isUnsignedInt($nb_days_new_product)) {
                $nb_days_new_product = 20;
            }
            $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
            $sql = 'SELECT DISTINCT p.*,AVG(comment.grade) as total_grade, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . ($prev_version? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)':' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
        					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, IFNULL(image_shop.`id_image`, i.`id_image`) id_image,
        					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
        					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
        					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice
                    FROM `' . _DB_PREFIX_ . 'product` p
                    '.Shop::addSqlAssociation('product', 'p').'
                    INNER JOIN `'._DB_PREFIX_.'category_product` cp ON product_shop.`id_product` = cp.`id_product`
                    '.(Module::isInstalled('ets_reviews') ? 'LEFT JOIN `'._DB_PREFIX_.'ets_rv_product_comment` comment ON (comment.id_product = p.id_product)':' LEFT JOIN `'._DB_PREFIX_.'product_comment` comment ON (comment.id_product = p.id_product)').'
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('cl') . ')'.
                (!$prev_version?
                    'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = product_shop.id_product)'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1').'':
                    'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')'
                )
                .Product::sqlStock('p', 0, false, Context::getContext()->shop).'
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (product_shop.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')'.
                (!$prev_version?
                    'LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = product_shop.`id_product`)'. Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1') :
                    'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
                ).'
                    LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)Context::getContext()->language->id.')	
                    LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                    WHERE product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id
                .($is17 ? ' AND p.state=1':'')
                .(($id_product = (int)Tools::getValue('id_product')) ? ' AND p.id_product!="'.(int)$id_product.'"':'')
                .(!Configuration::get('ETS_CS_OUT_OF_STOCK') ? ' AND stock.quantity>0':'')
                . ($active ? ' AND product_shop.`active` = 1' : '')
                . (Configuration::get('ETS_CS_EXCL_FREE_PRODUCT') ? ' AND product_shop.price >0':'')
                .(Tools::getValue('id_ets_css_sub_category') ? ' AND cp.id_category="'.(int)Tools::getValue('id_ets_css_sub_category').'"':'')
                . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                . ' GROUP BY product_shop.id_product HAVING total_grade >0'
                . ( $order_by?  ' ORDER BY ' . pSQL($order_by) : ' ORDER BY total_grade DESC') . ' LIMIT 0,'.$nbProduct ;
            $products = Db::getInstance()->executeS($sql);
            if (!$products) {
                return array();
            }
            $products = Product::getProductsProperties(Context::getContext()->language->id, $products);
            if ($is17) {
                $products = Ets_crosssell::productsForTemplate($products);
            }
            return $products;
        }
        else
            return array();
    }
    public static function getListNewProducts($id_lang, $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null, Context $context = null)
    {
        $now = date('Y-m-d') . ' 00:00:00';
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= ' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP')) . ')
                WHERE cp.`id_product` = p.`id_product`)';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }

        $nb_days_new_product = (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT');

        if ($count) {
            $sql = 'SELECT COUNT(p.`id_product`) AS nb
                    FROM `' . _DB_PREFIX_ . 'product` p
                    ' . Shop::addSqlAssociation('product', 'p') . '
                    WHERE product_shop.`active` = 1
                    '.(version_compare(_PS_VERSION_, '1.7', '>=') ? ' AND p.state=1':'').'
                    AND product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . $nb_days_new_product . ' DAY')) . '"
                    ' . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') . '
                    ' . $sql_groups;

            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        $sql = new DbQuery();
        $sql->select(
            'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
            pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
            (DATEDIFF(product_shop.`date_add`,
                DATE_SUB(
                    "' . $now . '",
                    INTERVAL ' . $nb_days_new_product . ' DAY
                )
            ) > 0) as new'
        );

        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin('category_product','cp','cp.id_product=p.id_product');
        $sql->leftJoin(
            'product_lang',
            'pl',
            '
            p.`id_product` = pl.`id_product`
            AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl')
        );
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id);
        $sql->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang);
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        $sql->where('product_shop.`active` = 1');
        if($id_category = (int)Tools::getValue('id_ets_css_sub_category'))
            $sql->where('cp.`id_category` = '.(int)$id_category);
        if(($id_product = (int)Tools::getValue('id_product')))
            $sql->where('p.`id_product` != '.(int)$id_product);
        if ($front) {
            $sql->where('product_shop.`visibility` IN ("both", "catalog")');
        }
        $sql->where('product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . $nb_days_new_product . ' DAY')) . '"');
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $sql->where('p.state=1');
        if(!Configuration::get('ETS_CS_OUT_OF_STOCK'))
            $sql->where('stock.quantity >0');
        if(Configuration::get('ETS_CS_EXCL_FREE_PRODUCT'))
            $sql->where('product_shop.price >0');
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql->where('EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
                JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP')) . ')
                WHERE cp.`id_product` = p.`id_product`)');
        }
        if($order_by=='rand')
        {
            $order_way='';
            $order_by = 'RAND()';
        }
        $sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . pSQL($order_by) . ' ' . pSQL($order_way));
        $sql->limit($nb_products, (int) (($page_number - 1) * $nb_products));

        if (Combination::isFeatureActive()) {
            $sql->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id);
        }
        $sql->join(Product::sqlStock('p', 0));
        $sql->groupBy('p.id_product');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }
        $products_ids = array();
        foreach ($result as $row) {
            $products_ids[] = $row['id_product'];
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheFrontFeatures($products_ids, $id_lang);

        return Product::getProductsProperties((int) $id_lang, $result);
    }
    public static function getBestSalesLight($idLang, $pageNumber = 0, $nbProducts = 10, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if ($pageNumber < 0) {
            $pageNumber = 0;
        }
        if ($nbProducts < 1) {
            $nbProducts = 10;
        }

        // no group by needed : there's only one attribute with default_on=1 for a given id_product + shop
        // same for image with cover=1
        $sql = '
		SELECT
			p.id_product, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
			image_shop.`id_image` id_image, il.`legend`,
			ps.`quantity` AS sales, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
			IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
			product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . (Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY')) . '" as new,
			product_shop.`on_sale`, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity
		FROM `' . _DB_PREFIX_ . 'product_sale` ps
		LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON ps.`id_product` = p.`id_product`
		' . Shop::addSqlAssociation('product', 'p') . '
        '.(
            Tools::getValue('id_ets_css_sub_category')?' LEFT JOIN `'._DB_PREFIX_.'category_product` cp2 ON (cp2.id_product=p.id_product)':''
            ).'
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
			ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int) $context->shop->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (product_attribute_shop.id_product_attribute=pa.id_product_attribute)
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
			ON p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('pl') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
			ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int) $context->shop->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $idLang . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
			ON cl.`id_category` = product_shop.`id_category_default`
			AND cl.`id_lang` = ' . (int) $idLang . Shop::addSqlRestrictionOnLang('cl') . Product::sqlStock('p', 0);

        $sql .= '
		WHERE product_shop.`active` = 1 '
            .(version_compare(_PS_VERSION_, '1.7', '>=') ? ' AND p.state=1':'')
            .(($id_product = (int)Tools::getValue('id_product')) ? ' AND p.id_product!="'.(int)$id_product.'"':'')
            . (!Configuration::get('ETS_CS_OUT_OF_STOCK')? ' AND ps.quantity > 0 ' : '')
            . (Configuration::get('ETS_CS_EXCL_FREE_PRODUCT') ? ' AND product_shop.price >0':'')
            .(Tools::getValue('id_ets_css_sub_category') ? ' AND cp2.id_category="'.(int)Tools::getValue('id_ets_css_sub_category').'"':'').'
		AND p.`visibility` != \'none\'';

        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql .= ' AND EXISTS(SELECT 1 FROM `' . _DB_PREFIX_ . 'category_product` cp
				JOIN `' . _DB_PREFIX_ . 'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '=' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP')) . ')
				WHERE cp.`id_product` = p.`id_product`)';
        }

        $sql .= '
		ORDER BY ps.quantity DESC
		LIMIT ' . (int) ($pageNumber * $nbProducts) . ', ' . (int) $nbProducts;

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return false;
        }
        return $result;
    }
    public static function getTrendingProducts($nbProducts,$day)
    {
        $is17 = version_compare(_PS_VERSION_, '1.7', '>=') ? true : false;
        $date = strtotime("-$day day", strtotime(date('Y-m-d')));
        $active = true;
        $front = true;
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $prev_version = version_compare(_PS_VERSION_, '1.6.1.0', '<');
        $order_by = ' total_sale DESC';
        $sql = 'SELECT DISTINCT p.*,count(DISTINCT od.id_order) as total_sale ,product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity' . ($prev_version? ' ,IFNULL(product_attribute_shop.id_product_attribute, 0)':' ,MAX(product_attribute_shop.id_product_attribute)') . ' id_product_attribute, pl.`description`, pl.`description_short`, pl.`available_now`,
    					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, IFNULL(image_shop.`id_image`, i.`id_image`) id_image,
    					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
    					DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
    					INTERVAL ' . (int)$nb_days_new_product . ' DAY)) > 0 AS new, product_shop.price AS orderprice
                FROM `' . _DB_PREFIX_ . 'category_product` cp
                INNER JOIN `'._DB_PREFIX_.'order_detail` od ON (od.product_id = cp.id_product)
                INNER JOIN `'._DB_PREFIX_.'orders` o ON (od.id_order=o.id_order)
                LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
                '.Shop::addSqlAssociation('product', 'p').
            ' LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('cl') . ')'.
            (!$prev_version?
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = p.id_product)'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on=1').'':
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')'
            ).
            (
            Tools::getValue('id_ets_css_sub_category')?' LEFT JOIN `'._DB_PREFIX_.'category_product` cp2 ON (cp2.id_product=p.id_product)':''
            )
            .Product::sqlStock('p', 0, false, Context::getContext()->shop).'
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')'.
            (!$prev_version?
                'LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`)'. Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1') :
                'LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ')'
            ).'
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)Context::getContext()->language->id.')	
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                WHERE o.date_add >="'.pSQL(date('Y-m-d', $date)).'" AND  product_shop.`id_shop` = ' . (int)Context::getContext()->shop->id
            .($is17 ? ' AND p.state=1':'')
            .(($id_product = (int)Tools::getValue('id_product')) ? ' AND p.id_product!="'.(int)$id_product.'"':'')
            .(!Configuration::get('ETS_CS_OUT_OF_STOCK') ? ' AND stock.quantity>0':'')
            .($active ? ' AND product_shop.`active` = 1' : '')
            . (Configuration::get('ETS_CS_EXCL_FREE_PRODUCT') ? ' AND product_shop.price >0':'')
            .(Tools::getValue('id_ets_css_sub_category') ? ' AND cp2.id_category="'.(int)Tools::getValue('id_ets_css_sub_category').'"':'')
            . ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
            .(Context::getContext()->cart->id ? ' AND product_shop.id_product NOT IN (SELECT id_product FROM `'._DB_PREFIX_.'cart_product` WHERE id_cart="'.(int)Context::getContext()->cart->id.'")':'')
            . ' GROUP BY p.id_product'
            . ( ($order_by) ? ($order_by != 'rand' ? ' ORDER BY ' . pSQL($order_by) : ' ORDER BY '.pSQL(self::getRandomSeed())) : '') . '
                LIMIT  0,' . (int)$nbProducts;
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!$products) {
            return array();
        }
        if($is17)
            return Ets_crosssell::productsForTemplate($products);
        else
            return Product::getProductsProperties(Context::getContext()->language->id, $products);
    }
}