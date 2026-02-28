<?php

class SteSeoAnalyzer
{
    private $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * Run full diagnostics
     * @return array List of issues ['level' => 'critical|warning|info', 'msg' => '...']
     */
    public function analyzeGlobal()
    {
        $issues = [];

        // Check 1: index.php exposure
        $countIndexPhp = $this->db->getValue('
            SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'ste_seo_index 
            WHERE loc LIKE "%/index.php%" AND is_indexable = 1
        ');
        if ($countIndexPhp > 0) {
            $issues[] = [
                'level' => 'critical',
                'msg' => $countIndexPhp . ' URLs contain "index.php" - Friendly URLs might be disabled or broken.'
            ];
        }

        // Check 2: HTTP vs HTTPS
        $countHttp = $this->db->getValue('
            SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'ste_seo_index 
            WHERE loc LIKE "http://%" AND is_indexable = 1
        ');
        if ($countHttp > 0 && Configuration::get('PS_SSL_ENABLED')) {
            $issues[] = [
                'level' => 'warning',
                'msg' => $countHttp . ' URLs are using HTTP instead of HTTPS.'
            ];
        }

        // Check 3: Duplicates
        $duplicates = $this->db->executeS('
            SELECT loc, COUNT(*) as c FROM ' . _DB_PREFIX_ . 'ste_seo_index 
            WHERE is_indexable = 1
            GROUP BY loc HAVING c > 1
        ');
        if (!empty($duplicates)) {
            $issues[] = [
                'level' => 'critical',
                'msg' => count($duplicates) . ' URLs are duplicated across entities or shops.'
            ];
        }
        
        // Check 4: Cross-shop Domain Check (Simple heuristic)
        // Check if Shop A URLs contain Shop B domain? 
        // We can check if `loc` matches the Shop Base URL
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
             $s = new Shop($shop['id_shop']);
             $baseUrl = $s->getBaseURL(true);
             $domain = parse_url($baseUrl, PHP_URL_HOST);
             
             // Count URLs for this shop that DO NOT contain the domain
             $badCount = $this->db->getValue('
                SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'ste_seo_index 
                WHERE id_shop = ' . (int)$shop['id_shop'] . ' 
                AND loc NOT LIKE "%' . pSQL($domain) . '%"
             ');
             
             if ($badCount > 0) {
                 $issues[] = [
                     'level' => 'critical',
                     'msg' => "Shop '{$shop['name']}' has $badCount URLs that do not match its domain context ($domain)."
                 ];
             }
        }

        return $issues;
    }
}
