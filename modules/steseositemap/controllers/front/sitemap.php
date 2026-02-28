<?php
/**
 * STE SEO Sitemap & Robots Module
 * Front Controller for dynamic sitemap.xml
 */

class SteSeoSitemapSitemapModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        // Set header to XML
        header('Content-Type: application/xml; charset=utf-8');
        
        $idShop = (int)$this->context->shop->id;
        $filename = 'sitemap-' . $idShop . '.xml';
        $fullPath = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($fullPath)) {
            readfile($fullPath);
        } else {
            // If file doesn't exist, try to generate it on the fly or show empty sitemap
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
        }
        
        exit;
    }
}
