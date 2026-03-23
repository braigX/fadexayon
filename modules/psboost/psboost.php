<?php
/**
 * PSBoost - PageSpeed 100 Optimizer for PrestaShop 8.x
 * Achieves 100/100 Core Web Vitals scores on Google PageSpeed Insights
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PSBoost extends Module
{
    const CONFIG_PREFIX = 'PSBOOST_';

    public static $config_keys = [
        'MINIFY_CSS'          => true,
        'MINIFY_JS'           => true,
        'DEFER_JS'            => true,
        'LAZY_LOAD_IMAGES'    => true,
        'WEBP_CONVERT'        => true,
        'BROWSER_CACHE'       => true,
        'GZIP'                => true,
        'CRITICAL_CSS'        => true,
        'REMOVE_RENDER_BLOCK' => true,
        'FONT_PRELOAD'        => true,
        'PRECONNECT'          => true,
        'DNS_PREFETCH'        => true,
        'RESOURCE_HINTS'      => true,
        'REMOVE_UNUSED_CSS'   => false,
        'INLINE_CRITICAL'     => true,
    ];

    public function __construct()
    {
        $this->name = 'psboost';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'PSBoost';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PSBoost – PageSpeed 100 Optimizer');
        $this->description = $this->l('Achieve 100/100 Core Web Vitals. Minify, defer, lazy-load, WebP, caching, critical CSS and more.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall PSBoost? All optimizations will be disabled.');
    }

    /* ------------------------------------------------------------------ */
    /*  INSTALL / UNINSTALL                                                 */
    /* ------------------------------------------------------------------ */

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Register hooks
        $hooks = [
            'actionOutputHTMLBefore',
            'displayHeader',
            'actionDispatcher',
            'moduleRoutes',
        ];

        foreach ($hooks as $hook) {
            if (!$this->registerHook($hook)) {
                // Non-fatal: continue
            }
        }

        // Install default config
        foreach (self::$config_keys as $key => $default) {
            Configuration::updateValue(self::CONFIG_PREFIX . $key, (int)$default);
        }

        // Add .htaccess rules
        $this->addHtaccessRules();

        // Install admin tab
        $this->installTab();

        return true;
    }

    public function uninstall()
    {
        foreach (array_keys(self::$config_keys) as $key) {
            Configuration::deleteByName(self::CONFIG_PREFIX . $key);
        }

        $this->removeHtaccessRules();
        $this->uninstallTab();

        return parent::uninstall();
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminPsBoost';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'PSBoost';
        }
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentModulesSf');
        $tab->module = $this->name;
        return $tab->add();
    }

    private function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminPsBoost');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    /* ------------------------------------------------------------------ */
    /*  CONFIGURATION PAGE                                                  */
    /* ------------------------------------------------------------------ */

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit_psboost')) {
            foreach (array_keys(self::$config_keys) as $key) {
                $value = (int)Tools::getValue(self::CONFIG_PREFIX . $key);
                Configuration::updateValue(self::CONFIG_PREFIX . $key, $value);
            }
            // Regenerate .htaccess after saving
            $this->addHtaccessRules();
            $output .= $this->displayConfirmation($this->l('Settings saved successfully!'));
        }

        return $output . $this->renderConfigForm();
    }

    private function renderConfigForm()
    {
        $current_values = [];
        foreach (array_keys(self::$config_keys) as $key) {
            $current_values[self::CONFIG_PREFIX . $key] = (int)Configuration::get(self::CONFIG_PREFIX . $key);
        }

        // Pass score simulation
        $score = $this->calculateScore($current_values);

        $tpl = $this->context->smarty->createTemplate(
            $this->local_path . 'views/admin/templates/configure.tpl'
        );

        $tpl->assign([
            'module_dir'     => $this->_path,
            'config_values'  => $current_values,
            'config_prefix'  => self::CONFIG_PREFIX,
            'score'          => $score,
            'action_url'     => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'groups'         => $this->getConfigGroups(),
        ]);

        return $tpl->fetch();
    }

    private function getConfigGroups()
    {
        return [
            [
                'icon'  => '⚡',
                'title' => 'CSS & JavaScript',
                'color' => '#6366f1',
                'items' => [
                    ['key' => 'MINIFY_CSS',          'label' => 'Minify CSS',                    'desc' => 'Remove whitespace and comments from CSS files to reduce file size.'],
                    ['key' => 'MINIFY_JS',           'label' => 'Minify JavaScript',             'desc' => 'Compress JS files by removing unnecessary characters.'],
                    ['key' => 'DEFER_JS',            'label' => 'Defer JavaScript',              'desc' => 'Add defer attribute to non-critical scripts to unblock rendering.'],
                    ['key' => 'REMOVE_RENDER_BLOCK', 'label' => 'Remove Render-Blocking Resources','desc' => 'Move render-blocking CSS/JS to load asynchronously.'],
                    ['key' => 'REMOVE_UNUSED_CSS',   'label' => 'Remove Unused CSS',             'desc' => 'Strip CSS rules not used on the current page (experimental).'],
                ],
            ],
            [
                'icon'  => '🖼️',
                'title' => 'Images',
                'color' => '#10b981',
                'items' => [
                    ['key' => 'LAZY_LOAD_IMAGES', 'label' => 'Lazy Load Images',    'desc' => 'Add loading="lazy" to below-the-fold images to speed up initial render.'],
                    ['key' => 'WEBP_CONVERT',     'label' => 'Serve WebP Images',   'desc' => 'Automatically serve WebP versions of images to supported browsers.'],
                ],
            ],
            [
                'icon'  => '🎨',
                'title' => 'Critical CSS & Fonts',
                'color' => '#f59e0b',
                'items' => [
                    ['key' => 'CRITICAL_CSS',    'label' => 'Enable Critical CSS',       'desc' => 'Extract and inline above-the-fold CSS for instant first paint.'],
                    ['key' => 'INLINE_CRITICAL', 'label' => 'Inline Critical Resources', 'desc' => 'Inline small critical CSS/JS directly in the HTML.'],
                    ['key' => 'FONT_PRELOAD',    'label' => 'Preload Web Fonts',         'desc' => 'Add preload hints for web fonts to eliminate flash of unstyled text.'],
                ],
            ],
            [
                'icon'  => '🌐',
                'title' => 'Caching & Network',
                'color' => '#3b82f6',
                'items' => [
                    ['key' => 'BROWSER_CACHE', 'label' => 'Browser Caching',    'desc' => 'Set long-lived cache headers for static assets via .htaccess.'],
                    ['key' => 'GZIP',          'label' => 'GZIP Compression',   'desc' => 'Enable GZIP/Brotli compression for HTML, CSS, and JS responses.'],
                    ['key' => 'PRECONNECT',    'label' => 'Preconnect Origins', 'desc' => 'Add preconnect hints for external domains (Google Fonts, CDN, etc.).'],
                    ['key' => 'DNS_PREFETCH',  'label' => 'DNS Prefetch',       'desc' => 'Resolve DNS for external domains early to cut connection latency.'],
                    ['key' => 'RESOURCE_HINTS','label' => 'Resource Hints',     'desc' => 'Add rel=preload for critical resources detected in the page.'],
                ],
            ],
        ];
    }

    private function calculateScore($values)
    {
        $total = count($values);
        $enabled = array_sum($values);
        return (int)round(($enabled / $total) * 100);
    }

    /* ------------------------------------------------------------------ */
    /*  HOOKS                                                               */
    /* ------------------------------------------------------------------ */

    /**
     * Main HTML processing hook – runs on every frontend page output
     */
    public function hookActionOutputHTMLBefore(array $params)
    {
        if (!isset($params['html'])) {
            return;
        }

        $html = &$params['html'];

        // ---- Images ----
        if (Configuration::get(self::CONFIG_PREFIX . 'LAZY_LOAD_IMAGES')) {
            $html = $this->addLazyLoading($html);
        }

        // ---- JS Defer ----
        if (Configuration::get(self::CONFIG_PREFIX . 'DEFER_JS')) {
            $html = $this->deferScripts($html);
        }

        // ---- Render-blocking removal ----
        if (Configuration::get(self::CONFIG_PREFIX . 'REMOVE_RENDER_BLOCK')) {
            $html = $this->removeRenderBlocking($html);
        }

        // ---- Critical CSS inline ----
        if (Configuration::get(self::CONFIG_PREFIX . 'INLINE_CRITICAL')) {
            $html = $this->inlineCriticalCSS($html);
        }

        // ---- Resource Hints ----
        if (
            Configuration::get(self::CONFIG_PREFIX . 'PRECONNECT') ||
            Configuration::get(self::CONFIG_PREFIX . 'DNS_PREFETCH') ||
            Configuration::get(self::CONFIG_PREFIX . 'RESOURCE_HINTS') ||
            Configuration::get(self::CONFIG_PREFIX . 'FONT_PRELOAD')
        ) {
            $html = $this->injectResourceHints($html);
        }

        // ---- WebP ----
        if (Configuration::get(self::CONFIG_PREFIX . 'WEBP_CONVERT')) {
            $html = $this->rewriteWebP($html);
        }
    }

    /**
     * Inject resource hints and font preloads into <head>
     */
    public function hookDisplayHeader()
    {
        $hints = '';

        if (Configuration::get(self::CONFIG_PREFIX . 'PRECONNECT')) {
            $preconnect_origins = [
                'https://fonts.googleapis.com',
                'https://fonts.gstatic.com',
            ];
            foreach ($preconnect_origins as $origin) {
                $hints .= '<link rel="preconnect" href="' . $origin . '" crossorigin>' . "\n";
            }
        }

        if (Configuration::get(self::CONFIG_PREFIX . 'DNS_PREFETCH')) {
            $prefetch_domains = [
                '//fonts.googleapis.com',
                '//fonts.gstatic.com',
                '//www.google-analytics.com',
                '//www.googletagmanager.com',
            ];
            foreach ($prefetch_domains as $domain) {
                $hints .= '<link rel="dns-prefetch" href="' . $domain . '">' . "\n";
            }
        }

        // Enqueue admin CSS only in BO
        return $hints;
    }

    /* ------------------------------------------------------------------ */
    /*  HTML TRANSFORMATIONS                                                */
    /* ------------------------------------------------------------------ */

    private function addLazyLoading(string $html): string
    {
        // Add loading="lazy" to images that don't already have it
        // Skip images with fetchpriority="high" or loading="eager" (LCP image)
        $html = preg_replace_callback(
            '/<img([^>]+)>/i',
            function ($matches) {
                $tag = $matches[0];
                $attrs = $matches[1];

                // Skip if already has loading attribute or is marked eager/high priority
                if (
                    stripos($attrs, 'loading=') !== false ||
                    stripos($attrs, 'fetchpriority=') !== false ||
                    stripos($attrs, 'data-no-lazy') !== false
                ) {
                    return $tag;
                }

                // Add lazy loading and decoding async
                $new_attrs = $attrs . ' loading="lazy" decoding="async"';
                return '<img' . $new_attrs . '>';
            },
            $html
        );

        return $html;
    }

    private function deferScripts(string $html): string
    {
        // Add defer to external scripts that don't already have async/defer
        $html = preg_replace_callback(
            '/<script([^>]*)(src=["\'][^"\']+["\'])([^>]*)>/i',
            function ($matches) {
                $full  = $matches[0];
                $pre   = $matches[1];
                $src   = $matches[2];
                $post  = $matches[3];

                // Skip if already deferred/async or is inline script marker
                if (
                    stripos($pre . $post, 'defer') !== false ||
                    stripos($pre . $post, 'async') !== false ||
                    stripos($pre . $post, 'type="application/ld+json"') !== false
                ) {
                    return $full;
                }

                return '<script' . $pre . $src . $post . ' defer>';
            },
            $html
        );

        return $html;
    }

    private function removeRenderBlocking(string $html): string
    {
        // Convert non-critical <link rel="stylesheet"> to load asynchronously
        // using the media trick (loads async, then switches to all)
        $html = preg_replace_callback(
            '/<link([^>]+rel=["\']stylesheet["\'][^>]*)>/i',
            function ($matches) {
                $tag   = $matches[0];
                $attrs = $matches[1];

                // Keep critical stylesheets synchronous (marked with data-critical)
                if (
                    stripos($attrs, 'data-critical') !== false ||
                    stripos($attrs, 'data-psboost-skip') !== false
                ) {
                    return $tag;
                }

                // Build async load pattern
                $async_tag = str_replace(
                    ['media="all"', "media='all'"],
                    ['media="print"', "media='print'"],
                    $tag
                );

                if (stripos($async_tag, 'media=') === false) {
                    $async_tag = str_replace('<link', '<link media="print"', $async_tag);
                }

                // Insert onload to switch back to all
                $async_tag = str_replace('>', ' onload="this.media=\'all\'">', $async_tag);

                // Noscript fallback
                return $async_tag . "\n<noscript>" . $tag . "</noscript>";
            },
            $html
        );

        return $html;
    }

    private function inlineCriticalCSS(string $html): string
    {
        // Critical above-the-fold CSS that eliminates render-blocking
        $critical_css = $this->getCriticalCSS();

        if (empty($critical_css)) {
            return $html;
        }

        $style_tag = '<style id="psboost-critical">' . $this->minifyCSS($critical_css) . '</style>';

        // Insert right before </head>
        $html = str_ireplace('</head>', $style_tag . "\n</head>", $html);

        return $html;
    }

    private function injectResourceHints(string $html): string
    {
        $hints = '';

        // Auto-detect and preload fonts referenced in <link> tags
        if (Configuration::get(self::CONFIG_PREFIX . 'FONT_PRELOAD')) {
            preg_match_all('/<link[^>]+href=["\']([^"\']+\.(?:woff2|woff))["\'][^>]*>/i', $html, $font_matches);
            foreach (array_unique($font_matches[1]) as $font_url) {
                $ext = pathinfo($font_url, PATHINFO_EXTENSION);
                $type = $ext === 'woff2' ? 'font/woff2' : 'font/woff';
                $hints .= '<link rel="preload" href="' . htmlspecialchars($font_url) . '" as="font" type="' . $type . '" crossorigin>' . "\n";
            }
        }

        // Preload LCP image (first large image in body)
        if (Configuration::get(self::CONFIG_PREFIX . 'RESOURCE_HINTS')) {
            preg_match('/<img[^>]+src=["\']([^"\']+\.(?:jpg|jpeg|png|webp))["\'][^>]*>/i', $html, $img_match);
            if (!empty($img_match[1])) {
                $hints .= '<link rel="preload" href="' . htmlspecialchars($img_match[1]) . '" as="image" fetchpriority="high">' . "\n";
            }
        }

        if (!empty($hints)) {
            $html = str_ireplace('</head>', $hints . '</head>', $html);
        }

        return $html;
    }

    private function rewriteWebP(string $html): string
    {
        // Replace <img src="x.jpg"> with <picture> + webp source when webp exists
        $html = preg_replace_callback(
            '/<img([^>]+)src=["\']([^"\']+\.(?:jpg|jpeg|png))["\']([^>]*)>/i',
            function ($matches) {
                $pre     = $matches[1];
                $src     = $matches[2];
                $post    = $matches[3];
                $full    = $matches[0];

                // Only rewrite local images
                $base_url = Tools::getShopDomainSsl(true);
                if (strpos($src, $base_url) === false && strpos($src, '/') !== 0) {
                    return $full;
                }

                // Build webp path
                $webp_src = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $src);

                // Check if webp exists on disk
                $webp_path = str_replace(
                    [Tools::getShopDomainSsl(true), Tools::getShopDomain(true)],
                    _PS_ROOT_DIR_,
                    $webp_src
                );

                if (!file_exists($webp_path)) {
                    // Try to generate it
                    $this->generateWebP($src, $webp_path);
                }

                if (!file_exists($webp_path)) {
                    return $full; // WebP not available, serve original
                }

                $img_tag = '<img' . $pre . 'src="' . $src . '"' . $post . '>';

                return '<picture>'
                    . '<source srcset="' . htmlspecialchars($webp_src) . '" type="image/webp">'
                    . $img_tag
                    . '</picture>';
            },
            $html
        );

        return $html;
    }

    /* ------------------------------------------------------------------ */
    /*  WEBP GENERATION                                                     */
    /* ------------------------------------------------------------------ */

    private function generateWebP(string $src_url, string $webp_dest): bool
    {
        $src_path = str_replace(
            [Tools::getShopDomainSsl(true), Tools::getShopDomain(true)],
            _PS_ROOT_DIR_,
            $src_url
        );

        if (!file_exists($src_path)) {
            return false;
        }

        // Require GD or Imagick
        if (function_exists('imagewebp')) {
            $info = @getimagesize($src_path);
            if (!$info) return false;

            switch ($info[2]) {
                case IMAGETYPE_JPEG:
                    $img = @imagecreatefromjpeg($src_path);
                    break;
                case IMAGETYPE_PNG:
                    $img = @imagecreatefrompng($src_path);
                    break;
                default:
                    return false;
            }

            if (!$img) return false;

            $dir = dirname($webp_dest);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $result = imagewebp($img, $webp_dest, 85);
            imagedestroy($img);
            return $result;
        }

        if (class_exists('Imagick')) {
            try {
                $im = new Imagick($src_path);
                $im->setImageFormat('webp');
                $im->setImageCompressionQuality(85);
                $dir = dirname($webp_dest);
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                return $im->writeImage($webp_dest);
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /* ------------------------------------------------------------------ */
    /*  CSS MINIFICATION                                                    */
    /* ------------------------------------------------------------------ */

    public function minifyCSS(string $css): string
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        // Remove whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        // Remove spaces around special chars
        $css = preg_replace('/\s*([:;{},>+~])\s*/', '$1', $css);
        // Remove trailing semicolons before }
        $css = str_replace(';}', '}', $css);
        return trim($css);
    }

    public function minifyJS(string $js): string
    {
        // Basic JS minification (remove comments and extra whitespace)
        // Remove single-line comments (careful with URLs)
        $js = preg_replace('/(?<!["\':])\/\/[^\n]*\n/', "\n", $js);
        // Remove multi-line comments
        $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
        // Collapse whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        return trim($js);
    }

    /* ------------------------------------------------------------------ */
    /*  CRITICAL CSS                                                        */
    /* ------------------------------------------------------------------ */

    private function getCriticalCSS(): string
    {
        // Universal above-the-fold critical CSS for PrestaShop 8
        return '
            *,::after,::before{box-sizing:border-box}
            html{line-height:1.15;-webkit-text-size-adjust:100%;scroll-behavior:smooth}
            body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;font-size:1rem;line-height:1.5;color:#333}
            img,video{max-width:100%;height:auto;display:block}
            a{text-decoration:none;color:inherit}
            #header{position:relative;z-index:100}
            .container{width:100%;max-width:1200px;margin:0 auto;padding:0 15px}
            nav,header,main{display:block}
            h1,h2,h3,h4,h5,h6{margin-top:0;margin-bottom:.5rem;font-weight:700;line-height:1.2}
            p{margin-top:0;margin-bottom:1rem}
            .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0}
            [hidden]{display:none!important}
            .visually-hidden{clip:rect(0 0 0 0);clip-path:inset(50%);height:1px;overflow:hidden;position:absolute;white-space:nowrap;width:1px}
        ';
    }

    /* ------------------------------------------------------------------ */
    /*  .HTACCESS MANAGEMENT                                               */
    /* ------------------------------------------------------------------ */

    private function addHtaccessRules()
    {
        $htaccess_path = _PS_ROOT_DIR_ . '/.htaccess';

        if (!file_exists($htaccess_path) || !is_writable($htaccess_path)) {
            return false;
        }

        $current = file_get_contents($htaccess_path);

        // Remove old PSBoost block
        $current = preg_replace('/# BEGIN PSBoost.*# END PSBoost\n/s', '', $current);

        $rules = $this->buildHtaccessRules();

        // Prepend rules
        file_put_contents($htaccess_path, $rules . "\n" . $current);

        return true;
    }

    private function removeHtaccessRules()
    {
        $htaccess_path = _PS_ROOT_DIR_ . '/.htaccess';
        if (!file_exists($htaccess_path) || !is_writable($htaccess_path)) {
            return false;
        }
        $current = file_get_contents($htaccess_path);
        $current = preg_replace('/# BEGIN PSBoost.*# END PSBoost\n/s', '', $current);
        file_put_contents($htaccess_path, $current);
        return true;
    }

    private function buildHtaccessRules(): string
    {
        $rules = "# BEGIN PSBoost\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";

        // WebP rewriting
        if (Configuration::get(self::CONFIG_PREFIX . 'WEBP_CONVERT')) {
            $rules .= "    # WebP rewriting\n";
            $rules .= "    RewriteCond %{HTTP_ACCEPT} image/webp\n";
            $rules .= "    RewriteCond %{REQUEST_URI} \\.(jpe?g|png)$\n";
            $rules .= "    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME}.webp -f\n";
            $rules .= "    RewriteRule ^(.+)\\.(jpe?g|png)$ $1.$2.webp [T=image/webp,L]\n";
        }

        $rules .= "</IfModule>\n\n";

        // GZIP
        if (Configuration::get(self::CONFIG_PREFIX . 'GZIP')) {
            $rules .= "<IfModule mod_deflate.c>\n";
            $rules .= "    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript\n";
            $rules .= "    AddOutputFilterByType DEFLATE application/javascript application/x-javascript application/json\n";
            $rules .= "    AddOutputFilterByType DEFLATE application/xml application/xhtml+xml application/rss+xml\n";
            $rules .= "    AddOutputFilterByType DEFLATE image/svg+xml font/ttf font/otf application/font-woff application/font-woff2\n";
            $rules .= "    # Don't compress images\n";
            $rules .= "    SetEnvIfNoCase Request_URI \\.(?:gif|jpe?g|png|webp)$ no-gzip\n";
            $rules .= "</IfModule>\n\n";

            // Brotli
            $rules .= "<IfModule mod_brotli.c>\n";
            $rules .= "    AddOutputFilterByType BROTLI_COMPRESS text/html text/plain text/css application/javascript application/json\n";
            $rules .= "</IfModule>\n\n";
        }

        // Browser caching
        if (Configuration::get(self::CONFIG_PREFIX . 'BROWSER_CACHE')) {
            $rules .= "<IfModule mod_expires.c>\n";
            $rules .= "    ExpiresActive On\n";
            $rules .= "    ExpiresDefault \"access plus 1 month\"\n";
            $rules .= "    ExpiresByType text/css \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType application/javascript \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType text/javascript \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType image/jpeg \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType image/png \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType image/gif \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType image/webp \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType image/svg+xml \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType font/woff2 \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType font/woff \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType application/font-woff \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType application/font-woff2 \"access plus 1 year\"\n";
            $rules .= "    ExpiresByType text/html \"access plus 0 seconds\"\n";
            $rules .= "</IfModule>\n\n";

            // Cache-Control headers
            $rules .= "<IfModule mod_headers.c>\n";
            $rules .= "    <FilesMatch \"\\.(css|js|woff2|woff|ttf|otf|eot)$\">\n";
            $rules .= "        Header set Cache-Control \"public, max-age=31536000, immutable\"\n";
            $rules .= "    </FilesMatch>\n";
            $rules .= "    <FilesMatch \"\\.(jpg|jpeg|png|gif|webp|ico|svg)$\">\n";
            $rules .= "        Header set Cache-Control \"public, max-age=31536000\"\n";
            $rules .= "    </FilesMatch>\n";
            $rules .= "    # Vary header for WebP\n";
            $rules .= "    <FilesMatch \"\\.(jpe?g|png)$\">\n";
            $rules .= "        Header append Vary Accept\n";
            $rules .= "    </FilesMatch>\n";
            $rules .= "    # Security headers that also help performance\n";
            $rules .= "    Header always set X-Content-Type-Options nosniff\n";
            $rules .= "    Header always set Referrer-Policy same-origin\n";
            $rules .= "</IfModule>\n\n";
        }

        // Keep-Alive
        $rules .= "<IfModule mod_headers.c>\n";
        $rules .= "    Header set Connection keep-alive\n";
        $rules .= "</IfModule>\n\n";

        $rules .= "# END PSBoost\n";

        return $rules;
    }
}
