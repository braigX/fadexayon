<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2018 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaWPRSSModuleFrontController extends ModuleFrontController
{
    public $php_self = '';

    public function init()
    {
        if (!$this->module->enable_posts_page) {
            $controller = Controller::getController('PageNotFoundController');
            $controller->run();
            exit;
        }

        parent::init();

        $pswp = Module::getInstanceByName('prestawp');
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;
        $conf_name = $pswp->settings_prefix . 'RSS_GEN_TIME_' . $id_lang;

        // Cached sitemap xml file:
        $xml_file_path = _PS_MODULE_DIR_ . $pswp->name . '/rss-' . (int) $id_shop . '-' . (int) $id_lang . '.xml';

        $last_gen = Configuration::get($conf_name);
        // Check if it's time to regen the sitemap, cache lifetime 10m:
        if (time() - $last_gen < 600 && file_exists($xml_file_path)) {
            // Return the cached sitemap
            $rss = Tools::file_get_contents($xml_file_path);
        } else {
            // Generate the RSS:
            $rss = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
                '<rss version="2.0" ' . PHP_EOL .
                'xmlns:content="http://purl.org/rss/1.0/modules/content/"' . PHP_EOL .
                'xmlns:dc="http://purl.org/dc/elements/1.1/"' . PHP_EOL .
                'xmlns:atom="http://www.w3.org/2005/Atom"' . PHP_EOL .
                'xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"' . PHP_EOL .
                '>' . PHP_EOL;

            $page_title = $this->module->getLangOptionValue('title_page');
            $page_title = ($page_title ? $page_title : $this->module->l('Posts', 'rss'));
            $page_desc = $this->module->getLangOptionValue('desc_page');
            $rss_link = $this->context->link->getModuleLink($this->module->name, 'rss');
            $post_list_link = $this->module->getModuleLink($this->module->name, 'list');
            $rss_limit = ($this->module->rss_limit ? $this->module->rss_limit : 10);

            $posts = $this->module->getPostsFront(1, $rss_limit);
            $last_post_date = '';
            if (is_array($posts) && $posts) {
                $last_post_date = $posts[0]['post_date_gmt'];
                $last_post_date = date('D, d M Y H:i:s +0000', strtotime($last_post_date));
            }

            $rss .= '<channel>' . PHP_EOL .
            '<title>' . $page_title . '</title>' . PHP_EOL .
            '<atom:link href="' . $rss_link . '" rel="self" type="application/rss+xml" />' . PHP_EOL .
            '<link>' . $post_list_link . '</link>' . PHP_EOL .
            '<description>' . $page_desc . '</description>' . PHP_EOL .
            '<lastBuildDate>' . $last_post_date . '</lastBuildDate>' . PHP_EOL .
            '<language>' . $this->context->language->locale . '</language>';

            if (is_array($posts)) {
                foreach ($posts as $post) {
                    $pub_date = $post['post_date_gmt'];
                    $pub_date = date('D, d M Y H:i:s +0000', strtotime($pub_date));

                    $desc = (!empty($post['post_excerpt']) ? $post['post_excerpt'] : strip_tags($post['main_content']));

                    $rss .= '<item>' . PHP_EOL;

                    $rss .= '<title><![CDATA[' . $post['post_title'] . ']]></title>' . PHP_EOL .
                        '<link><![CDATA[' . $post['url'] . ']]></link>' . PHP_EOL .
                        '<pubDate>' . $pub_date . '</pubDate>' . PHP_EOL .
                        '<description>' . $desc . '</description>' . PHP_EOL .
                        '<content:encoded><![CDATA[' . $post['post_content'] . ']]></content:encoded>' . PHP_EOL;

                    $rss .= '</item>' . PHP_EOL;
                }
            }

            $rss .= '</channel></rss>' . PHP_EOL;

            // Cache it:
            @file_put_contents($xml_file_path, $rss);
            Configuration::updateValue($conf_name, time());
        }

        header('Content-Type: text/xml; charset=utf-8');
        exit($rss);
    }
}
