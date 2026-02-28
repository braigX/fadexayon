<?php
/**
 * Cron execution for Sitemap Generation
 * Usage: php modules/steseositemap/cron.php token=SECURE_TOKEN
 */

include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/src/Core/SteSitemapGenerator.php');

$token = Tools::getValue('token');
$moduleToken = Configuration::get('STE_SEO_CRON_TOKEN');

// Auto-generate token if not exists
if (!$moduleToken) {
    $moduleToken = Tools::passwdGen(16);
    Configuration::updateValue('STE_SEO_CRON_TOKEN', $moduleToken);
}

// Security Check
if ($token !== $moduleToken && php_sapi_name() !== 'cli') {
    die('Invalid Token');
}

$generator = new SteSitemapGenerator();
$shops = Shop::getShops();
$basePath = _PS_ROOT_DIR_;

echo "Starting Sitemap Generation...\n";

foreach ($shops as $shop) {
    if (Configuration::get('STE_SKIP_DEFAULT') && $shop['id_shop'] == Configuration::get('PS_SHOP_DEFAULT')) {
        echo "Skipping Default Shop ID {$shop['id_shop']}\n";
        continue;
    }

    echo "Processing Shop: " . $shop['name'] . " (ID: " . $shop['id_shop'] . ")...\n";
    $res = $generator->generateForShop($shop['id_shop'], $basePath);
    
    if ($res['success']) {
        echo " - Success: {$res['filename']} generated with {$res['count']} URLs.\n";
    } else {
        echo " - Error: {$res['message']}\n";
    }
}

echo "Done.\n";
