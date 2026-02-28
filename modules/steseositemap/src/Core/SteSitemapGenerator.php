<?php

class SteSitemapGenerator
{
    private $db;
    private $context;

    public function __construct()
    {
        $this->db = Db::getInstance();
        $this->context = Context::getContext();
    }

    /**
     * Generate sitemap for a specific shop
     * 
     * @param int $idShop
     * @param string $basePath Directory to save sitemaps
     * @return array Result status
     */
    public function generateForShop($idShop, $basePath)
    {
        // Switch Context
        $previousContextShop = $this->context->shop->id;
        $shop = new Shop($idShop);
        if (!Validate::isLoadedObject($shop)) {
           return ['success' => false, 'message' => "Shop $idShop not found"];
        }

        Shop::setContext(Shop::CONTEXT_SHOP, $shop->id);
        $this->context->shop = $shop;
        
        // SYNC: Update the SEO Index Database first
        $this->syncIndex($shop->id, $shop->id_lang);

        // FETCH: Get all indexable URLs from our table
        $urls = $this->db->executeS('
            SELECT loc, priority, changefreq, image
            FROM ' . _DB_PREFIX_ . 'ste_seo_index
            WHERE id_shop = ' . (int)$shop->id . ' AND is_indexable = 1
        ');

        // Write to XML
        $filename = 'sitemap-' . $idShop . '.xml';
        $fullPath = $basePath . DIRECTORY_SEPARATOR . $filename;
        
        $xml = $this->buildXml($urls);
        
        $success = file_put_contents($fullPath, $xml);

        // Restore Context
        if ($previousContextShop) {
             Shop::setContext(Shop::CONTEXT_SHOP, $previousContextShop);
             $this->context->shop = new Shop($previousContextShop);
        }
        
        if ($success) {
            return ['success' => true, 'filename' => $filename, 'count' => count($urls)];
        }
        return ['success' => false, 'message' => "Could not write to $fullPath"];
    }

    /**
     * Syncs the live catalog with the local SEO index table
     */
    private function syncIndex($idShop, $idLang)
    {
        $link = new Link('http://', 'http://');

        // 1. Sync Categories
        $categories = $this->getCategories($idShop, $idLang);
        foreach ($categories as $cat) {
            $url = $link->getCategoryLink($cat['id_category'], $cat['link_rewrite'], $idLang, null, $idShop);
            $this->upsertIndex($idShop, 'category', $cat['id_category'], $url, '0.8', 'weekly');
        }

        // 2. Sync Products
        $products = $this->getProducts($idShop, $idLang);
        foreach ($products as $prod) {
            $url = $link->getProductLink($prod['id_product'], $prod['link_rewrite'], $prod['category'], $prod['ean13'], $idLang, $idShop);
            $imageUrl = '';
            if (!empty($prod['id_image'])) {
                $imageType = ImageType::getFormattedName('large');
                if (!$imageType) {
                    $imageType = ImageType::getFormattedName('home');
                }
                if (!$imageType) {
                    $imageType = 'large_default';
                }
                
                try {
                    $imageUrl = $link->getImageLink($prod['link_rewrite'], $prod['id_image'], $imageType);
                } catch (Exception $e) {
                    $imageUrl = ''; // Skip image if link generation fails
                }
            }
            $this->upsertIndex($idShop, 'product', $prod['id_product'], $url, '0.9', 'daily', $imageUrl);
        }

        // 3. Sync CMS
        $cmsPages = $this->getCms($idShop, $idLang);
        foreach ($cmsPages as $cms) {
            $url = $link->getCMSLink($cms['id_cms'], $cms['link_rewrite'], null, $idLang, $idShop);
            $this->upsertIndex($idShop, 'cms', $cms['id_cms'], $url, '0.5', 'monthly');
        }
        
        // 4. Cleanup/Mark Removed
        // (Optional: Logic to set is_indexable=0 for IDs not found in current sync)
    }

    private function upsertIndex($idShop, $type, $idEntity, $url, $priority, $freq, $image = null)
    {
        // Simple UPSERT simulation
        $exists = $this->db->getValue('SELECT id_seo_index FROM ' . _DB_PREFIX_ . 'ste_seo_index WHERE id_shop=' . (int)$idShop . ' AND entity_type="' . pSQL($type) . '" AND id_entity=' . (int)$idEntity);
        
        if ($exists) {
            // Update Url if changed
            $this->db->execute('UPDATE ' . _DB_PREFIX_ . 'ste_seo_index SET loc = "'.pSQL($url).'", image = "'.pSQL($image).'", last_mod = NOW() WHERE id_seo_index = '.(int)$exists);
        } else {
            $this->db->execute('INSERT INTO ' . _DB_PREFIX_ . 'ste_seo_index (id_shop, entity_type, id_entity, loc, last_mod, changefreq, priority, is_indexable, image)
            VALUES ('.(int)$idShop.', "'.pSQL($type).'", '.(int)$idEntity.', "'.pSQL($url).'", NOW(), "'.pSQL($freq).'", '.(float)$priority.', 1, "'.pSQL($image).'")');
        }
    }

    private function getCategories($idShop, $idLang)
    {
        return $this->db->executeS('
            SELECT c.id_category, cl.link_rewrite 
            FROM ' . _DB_PREFIX_ . 'category c
            INNER JOIN ' . _DB_PREFIX_ . 'category_lang cl ON c.id_category = cl.id_category AND cl.id_shop = ' . (int)$idShop . '
            INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs ON c.id_category = cs.id_category AND cs.id_shop = ' . (int)$idShop . '
            WHERE c.active = 1 AND cl.id_lang = ' . (int)$idLang . '
        ');
    }

    private function getProducts($idShop, $idLang)
    {
        return $this->db->executeS('
            SELECT p.id_product, pl.link_rewrite, p.ean13, cl.link_rewrite as category, i.id_image
            FROM ' . _DB_PREFIX_ . 'product p
            INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product AND pl.id_shop = ' . (int)$idShop . '
            INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product AND ps.id_shop = ' . (int)$idShop . '
            LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON p.id_category_default = cl.id_category AND cl.id_shop = ' . (int)$idShop . ' AND cl.id_lang = '.(int)$idLang.' 
            LEFT JOIN ' . _DB_PREFIX_ . 'image i ON (p.id_product = i.id_product AND i.cover = 1)
            WHERE p.active = 1 AND ps.visibility IN ("both", "catalog") AND pl.id_lang = ' . (int)$idLang . '
        ');
    }

    private function getCms($idShop, $idLang)
    {
        return $this->db->executeS('
            SELECT c.id_cms, cl.link_rewrite
            FROM ' . _DB_PREFIX_ . 'cms c
            INNER JOIN ' . _DB_PREFIX_ . 'cms_lang cl ON c.id_cms = cl.id_cms AND cl.id_shop = ' . (int)$idShop . '
            INNER JOIN ' . _DB_PREFIX_ . 'cms_shop cs ON c.id_cms = cs.id_cms AND cs.id_shop = ' . (int)$idShop . '
            WHERE c.active = 1 AND cl.id_lang = ' . (int)$idLang . '
        ');
    }

    private function buildXml($urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
        
        foreach ($urls as $data) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($data['loc']) . '</loc>';
            $xml .= '<priority>' . $data['priority'] . '</priority>';
            $xml .= '<changefreq>' . $data['changefreq'] . '</changefreq>';
            if (!empty($data['image'])) {
                $xml .= '<image:image>';
                $xml .= '<image:loc>' . htmlspecialchars($data['image']) . '</image:loc>';
                $xml .= '</image:image>';
            }
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        return $xml;
    }
}
