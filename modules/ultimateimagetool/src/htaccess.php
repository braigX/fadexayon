<?php
/***
 * Advanced plugins
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please office@advancedplugins.com
 *
 * @author    Advanced Plugins <office@advancedplugins.com>
 * @copyright Advanced Plugins
 * @license   commercial
 */


if (!defined('_PS_VERSION_')) { exit; }


class Htaccess
{
    private $separator    = '# Ultimate Image Tools - Do not edit';
    private $content      = array();
    private $domains      = array();
    private $mediaDomains = '';

    public function __construct()
    {
        $folder_prefix = '/';
        if(!empty(__PS_BASE_URI__))
            $folder_prefix = __PS_BASE_URI__;

        foreach (\ShopUrl::getShopUrls() as $shop_url) {
            /*** @var \ShopUrl $shop_url */
            if (!isset($this->domains[$shop_url->domain])) {
                $this->domains[$shop_url->domain] = array();
            }

            $this->domains[$shop_url->domain][] = array(
                'physical' => $shop_url->physical_uri,
                'virtual' => $shop_url->virtual_uri,
                'id_shop' => $shop_url->id_shop,
            );

            if ($shop_url->domain === $shop_url->domain_ssl) {
                continue;
            }

            if (!isset($this->domains[$shop_url->domain_ssl])) {
                $this->domains[$shop_url->domain_ssl] = array();
            }

            $this->domains[$shop_url->domain_ssl][] = array(
                'physical' => $shop_url->physical_uri,
                'virtual' => $shop_url->virtual_uri,
                'id_shop' => $shop_url->id_shop,
            );

            

        }
        if(Tools::strlen($folder_prefix) <= 1)
            $folder_prefix = '/';

        $medias = array();
        if (\Configuration::getMultiShopValues('PS_MEDIA_SERVER_1')
            && \Configuration::getMultiShopValues('PS_MEDIA_SERVER_2')
            && \Configuration::getMultiShopValues('PS_MEDIA_SERVER_3')
        ) {
            $medias = array(
                \Configuration::getMultiShopValues('PS_MEDIA_SERVER_1'),
                \Configuration::getMultiShopValues('PS_MEDIA_SERVER_2'),
                \Configuration::getMultiShopValues('PS_MEDIA_SERVER_3'),
            );
        }
        foreach ($medias as $media) {
            foreach ($media as $media_url) {
                if ($media_url) {
                    $this->mediaDomains .= 'RewriteCond %{HTTP_HOST} ^' . $media_url . '$ [OR]'.PHP_EOL;
                }
            }
        }


        $this->add_line('RewriteRule . - [E=REWRITEBASE:'. $folder_prefix .']');
        $this->add_line('');
        $this->add_line('# Apache 2.2');
        $this->add_line('<IfModule !mod_authz_core.c>');
        $this->add_line('    <Files ~ "(?i)^.*\.(webp)$">');
        $this->add_line('        Allow from all');
        $this->add_line('    </Files>');
        $this->add_line('</IfModule>');
        $this->add_line('# Apache 2.4');

        $this->add_line('<IfModule mod_authz_core.c>');
        $this->add_line('    <Files ~ "(?i)^.*\.(webp)$">');
        $this->add_line('        Require all granted');
        $this->add_line('        allow from all');
        $this->add_line('    </Files>');
        $this->add_line('</IfModule>');
        $this->add_line('');

        $this->add_line('');
        $this->add_line('<IfModule mod_setenvif.c>');
        $this->add_line('SetEnvIf Request_URI "\.(jpe?g|png)$" REQUEST_image');
        $this->add_line('</IfModule>');
               
        //Check browser compatibility from .htacces
        if((int)Configuration::get('uit_use_webp')  > 0 && (int)Configuration::get('uit_use_webp_termination') == 0 )
        {
            $this->add_line('');
            $this->add_line('<IfModule mod_rewrite.c>');
            $this->add_line('RewriteEngine On');
            $this->add_line('RewriteBase '.$folder_prefix);
            $this->add_line('RewriteCond %{HTTP_ACCEPT} image/webp');
            $this->add_line('RewriteCond %{REQUEST_FILENAME} (.*)\.(jpe?g|png)$');
            $this->add_line('RewriteCond %1\.webp -f');
            $this->add_line('RewriteRule (.+)\.(jpe?g|png)$ $1.webp [T=image/webp,E=accept:1]');
            $this->add_line('</IfModule>');

            $this->add_line('');
            $this->add_line('<IfModule mod_rewrite.c>');
            $this->add_line('RewriteEngine On');
            $this->add_line('RewriteCond %{HTTP_ACCEPT} image/webp');
            $this->add_line('RewriteCond %{DOCUMENT_ROOT}/$1.webp -f');
            $this->add_line('RewriteRule (.+)\.(jpe?g|png)$ $1.webp [T=image/webp]');
            $this->add_line('</IfModule>');
        }

        $this->add_line('');
        $this->add_line('<IfModule mod_headers.c>');
        $this->add_line('Header append Vary Accept env=REDIRECT_accept');
        $this->add_line('</IfModule>');
        $this->add_line('');

        $this->add_line('<IfModule mod_mime.c>');
        $this->add_line('AddType image/webp .webp');
        $this->add_line('</IfModule>');
    }

    public function generate_htaccess_content()
    {
        if(Configuration::get('uit_enable_gzip'))
        {
            $this->add_line('');
            $this->add_line('<IfModule mod_expires.c>');
                $this->add_line('ExpiresActive On');
                $this->add_line('ExpiresByType image/gif "access plus 1 year"');
                $this->add_line('ExpiresByType image/jpeg "access plus 1 year"');
                $this->add_line('ExpiresByType image/png "access plus 1 year"');
                $this->add_line('ExpiresByType image/webp "access plus 1 year"');
                $this->add_line('ExpiresByType text/css "access plus 1 week"');
                $this->add_line('ExpiresByType text/javascript "access plus 1 week"');
                $this->add_line('ExpiresByType application/javascript "access plus 1 week"');
                $this->add_line('ExpiresByType application/x-javascript "access plus 1 week"');
                $this->add_line('ExpiresByType image/x-icon "access plus 1 year"');
                $this->add_line('ExpiresByType image/svg+xml "access plus 1 year"');
                $this->add_line('ExpiresByType image/vnd.microsoft.icon "access plus 1 year"');
                $this->add_line('ExpiresByType application/font-woff "access plus 1 year"');
                $this->add_line('ExpiresByType application/x-font-woff "access plus 1 year"');
                $this->add_line('ExpiresByType application/vnd.ms-fontobject "access plus 1 year"');
                $this->add_line('ExpiresByType font/opentype "access plus 1 year"');
                $this->add_line('ExpiresByType font/ttf "access plus 1 year"');
                $this->add_line('ExpiresByType font/otf "access plus 1 year"');
                $this->add_line('ExpiresByType application/x-font-ttf "access plus 1 year"');
                $this->add_line('ExpiresByType application/x-font-otf "access plus 1 year"');
                $this->add_line('ExpiresByType text/xml "access plus 1 seconds"');
                $this->add_line('ExpiresByType text/plain "access plus 1 seconds"');
                $this->add_line('ExpiresByType application/xml "access plus 1 seconds"');
                $this->add_line('ExpiresByType application/rss+xml "access plus 1 seconds"');
                $this->add_line('ExpiresByType application/json "access plus 1 seconds"');
                $this->add_line('</IfModule>');

              $this->add_line('');
              $this->add_line('<IfModule mod_deflate.c>');
              $this->add_line('AddOutputFilterByType DEFLATE application/javascript');
              $this->add_line('AddOutputFilterByType DEFLATE application/rss+xml');
              $this->add_line('AddOutputFilterByType DEFLATE application/vnd.ms-fontobject');
              $this->add_line('AddOutputFilterByType DEFLATE application/x-font');
              $this->add_line('AddOutputFilterByType DEFLATE application/x-font-opentype');
              $this->add_line('AddOutputFilterByType DEFLATE application/x-font-otf');
              $this->add_line('AddOutputFilterByType DEFLATE application/x-font-truetype');
              $this->add_line('AddOutputFilterByType DEFLATE application/x-font-ttf');
              $this->add_line('AddOutputFilterByType DEFLATE application/x-javascript');
              $this->add_line('AddOutputFilterByType DEFLATE application/xhtml+xml');
              $this->add_line('AddOutputFilterByType DEFLATE application/xml');
              $this->add_line('AddOutputFilterByType DEFLATE font/opentype');
              $this->add_line('AddOutputFilterByType DEFLATE font/otf');
              $this->add_line('AddOutputFilterByType DEFLATE font/ttf');
              $this->add_line('AddOutputFilterByType DEFLATE image/svg+xml');
              $this->add_line('AddOutputFilterByType DEFLATE image/x-icon');
              $this->add_line('AddOutputFilterByType DEFLATE text/css');
              $this->add_line('AddOutputFilterByType DEFLATE text/html');
              $this->add_line('AddOutputFilterByType DEFLATE text/javascript');
              $this->add_line('AddOutputFilterByType DEFLATE text/plain');
              $this->add_line('AddOutputFilterByType DEFLATE text/xml');
              $this->add_line('</IfModule>');
              $this->add_line('');
        }

        foreach ($this->domains as $domain => $list_uri) {
            foreach ($list_uri as $uri) {
                /*** @noinspection DisconnectedForeachInstructionInspection */
                $this->add_line('#Domain: ' . $domain);
                $rewrite_settings = (int)\Configuration::get('PS_REWRITING_SETTINGS', null, null, (int)$uri['id_shop']);
                if ($rewrite_settings) {
                    $domain_rewrite_cond = 'RewriteCond %{HTTP_HOST} ^' . $domain . '$'.PHP_EOL;
                    // Rewrite product images < 100 millions
                    for ($i = 1; $i <= 8; $i++) {
                        $img_path = $img_name = '';
                        for ($j = 1; $j <= $i; $j++) {
                            $img_path .= '$' . $j . '/';
                            $img_name .= '$' . $j;
                        }
                        $img_name .= '$' . $j;
                        $this->add_line($this->mediaDomains);
                        $this->add_line($domain_rewrite_cond);
                        $this->add_line(
                            'RewriteRule ^' .
                            str_repeat('([0-9])', $i) .
                            '(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/' .
                            $img_path .
                            $img_name .
                            '$' .
                            ($j + 1) .
                            '.webp [L]'
                        );                        
                    }
                    
                    $this->add_line($this->mediaDomains);
                    $this->add_line($domain_rewrite_cond);
                    $this->add_line(
                        'RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)' .
                        '(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/c/$1$2$3.webp [L]'
                    );
                    $this->add_line($this->mediaDomains);
                    $this->add_line($domain_rewrite_cond);
                    $this->add_line(
                        'RewriteRule ^c/([a-zA-Z_-]+)(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/c/$1$2.webp [L]'
                    );
                }
            }
        }
    }

    protected function add_line($content)
    {
        $this->content[] = $content;
    }

    public function __toString()
    {
        return implode(PHP_EOL, array($this->separator, implode(PHP_EOL, $this->content), $this->separator));
    }

    public function add_to_htaccess()
    {
        $out = \Tools::file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
        $regex = '#' . preg_quote($this->separator, '#')
            . '(.*?)'
            . preg_quote($this->separator, '#')
            . '#'
            . 's';
        return file_put_contents(
            _PS_ROOT_DIR_ . '/.htaccess',
            $this . PHP_EOL . preg_replace($regex, '', $out)
        );
    }
}
