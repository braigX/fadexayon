# PSBoost – PageSpeed 100 Optimizer
### PrestaShop 8.x Module

Achieve **100/100 Core Web Vitals** on Google PageSpeed Insights by installing and activating this module. All optimizations are toggleable from the back-office dashboard.

---

## 📦 Installation

1. **Download** the `psboost.zip` (or the `psboost/` folder)
2. In your PrestaShop back-office, go to **Modules → Module Manager**
3. Click **Upload a module** and select `psboost.zip`
4. Click **Install**
5. The module auto-configures itself — you're done!

---

## ⚙️ Configuration

Go to **Modules → PSBoost** (or **Module Manager → Configure**) to access the dashboard.

You'll see a live **PageSpeed score ring** that updates in real-time as you toggle features on/off.

### Optimization Groups

#### ⚡ CSS & JavaScript
| Feature | What it does |
|---|---|
| Minify CSS | Strips whitespace and comments from CSS |
| Minify JS | Compresses JavaScript files |
| Defer JavaScript | Adds `defer` to non-critical scripts |
| Remove Render-Blocking | Loads CSS asynchronously via media trick |
| Remove Unused CSS | Strips unused CSS rules (experimental) |

#### 🖼️ Images
| Feature | What it does |
|---|---|
| Lazy Load Images | Adds `loading="lazy"` to below-fold images |
| Serve WebP Images | Auto-converts and serves WebP to supported browsers |

#### 🎨 Critical CSS & Fonts
| Feature | What it does |
|---|---|
| Enable Critical CSS | Extracts and inlines above-the-fold CSS |
| Inline Critical Resources | Inlines small CSS/JS directly in HTML |
| Preload Web Fonts | Adds `<link rel="preload">` for fonts |

#### 🌐 Caching & Network
| Feature | What it does |
|---|---|
| Browser Caching | Sets 1-year cache headers for static assets |
| GZIP Compression | Enables GZIP + Brotli via .htaccess |
| Preconnect Origins | `<link rel="preconnect">` for Google Fonts etc. |
| DNS Prefetch | Early DNS resolution for external domains |
| Resource Hints | Auto-preloads detected critical resources |

---

## 🔧 Server Requirements

- **PrestaShop**: 8.0.0+
- **PHP**: 7.4+ (8.x recommended)
- **Apache**: mod_deflate, mod_expires, mod_headers, mod_rewrite enabled
- **GD or Imagick**: For WebP conversion
- **Writable `.htaccess`**: For caching and GZIP rules

---

## 💡 Extra Steps for 100/100

These are server/theme-level changes that complement the module:

1. **Enable CCC in PrestaShop**  
   *Advanced Parameters → Performance → CCC → Enable for CSS and JS*

2. **Mark your LCP image**  
   Add `fetchpriority="high" loading="eager"` to your hero/banner image in your theme.

3. **Use Cloudflare (free)**  
   CDN + caching + HTTP/2 push for free. Huge TTFB improvement.

4. **Enable PHP OPcache**  
   Ask your host or set in `php.ini`: `opcache.enable=1`

5. **Self-host fonts**  
   Download Google Fonts and serve locally with `font-display: swap`.

6. **Set explicit image dimensions**  
   Add `width` and `height` attributes to all `<img>` tags in your theme to prevent CLS.

---

## 🛠️ How WebP Conversion Works

When **Serve WebP Images** is enabled:

1. The module checks if a `.webp` version of each image exists on disk
2. If not, it generates one using PHP GD or Imagick (85% quality)
3. In the HTML it wraps images in `<picture>` with a `<source type="image/webp">`
4. In `.htaccess` it adds a `RewriteRule` to serve `.webp` transparently when the browser supports it

---

## 🗂️ File Structure

```
psboost/
├── psboost.php                        # Main module class
├── config.xml                         # Module metadata
├── index.php                          # Security
├── controllers/
│   └── admin/
│       └── AdminPsBoostController.php
└── views/
    ├── admin/
    │   └── templates/
    │       └── configure.tpl          # Back-office dashboard
    ├── css/
    │   └── admin.css                  # Dashboard styles
    └── js/
        └── admin.js                   # Live score ring + toggles
```

---

## 📄 License

MIT License — free to use, modify, and distribute.
