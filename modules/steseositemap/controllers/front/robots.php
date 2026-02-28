<?php
/**
 * STE SEO Sitemap & Robots Module
 * Front Controller for dynamic robots.txt
 */

class SteSeoSitemapRobotsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        // Set header to plain text
        header('Content-Type: text/plain; charset=utf-8');
        
        $idShop = (int)$this->context->shop->id;
        
        // Fetch custom content for this shop
        $content = Db::getInstance()->getValue('
            SELECT content FROM ' . _DB_PREFIX_ . 'ste_robots 
            WHERE id_shop = ' . $idShop
        );

        if (!$content) {
            // Default robots content if none set
            $content = "User-agent: *\nDisallow: /admin/\nDisallow: /track/\nDisallow: /cart\nDisallow: /checkout";
        }

        // Always append the shop-specific sitemap
        $sitemapUrl = $this->context->shop->getBaseURL(true) . 'sitemap-' . $idShop . '.xml';
        
        echo "# Dynamic Robots.txt by STE SEO Master\n";
        echo $content . "\n";
        
        // Append sitemap only if not already present in content
        if (strpos($content, 'Sitemap:') === false) {
            echo "Sitemap: " . $sitemapUrl . "\n";
        }
        
        exit; // Prevent further output from PS
    }
}
