CHANGELOG:
===========================
v1.0.1 (December 28, 2024) - Requires Amazzing Filter v3.3.0
===========================
- [+] Optional trailing slash in SEO Page URLs
- [*] Fixed dynamic price parameter in URLs
- [*] Optimized bulk page actions
- [*] Misc bug fixes and optimizations

Files modified
-----
- /af_seopages.php
- /classes/BulkGenerator.php
- /views/css/back.css
- /views/css/bulk-generate.css
- /views/js/back.js
- /views/js/bulk-generate.js
- /views/js/front.js
- /views/templates/admin/bulk-generate.tpl
- /views/templates/admin/center-panel.tpl
- /views/templates/admin/side-panel.tpl

Files added
-----
- /upgrade/install-1.0.1.php

===========================
v1.0.0 (July 23, 2024) - Requires Amazzing Filter v3.2.9
===========================
- [+] Optionally exclude main SEO Page from URLs
- [*] Misc bug fixes and optimizations

Files modified
-----
- /af_seopages.php
- /classes/BulkGenerator.php
- /classes/SiteMap.php
- /controllers/front/seopage.php
- /upgrade/install-0.1.2.php
- /upgrade/install-0.2.0.php
- /views/css/back.css
- /views/templates/admin/center-panel.tpl
- /views/templates/admin/side-panel.tpl
- /views/templates/admin/sp-item-duplicates.tpl
- /views/templates/admin/sp-items.tpl
- /views/templates/front/seopage-16.tpl

Files added
-----
- /.htaccess
- /readme_en.pdf
- /upgrade/install-1.0.0.php

===========================
v0.2.7 (May 1, 2024) - Requires Amazzing Filter v3.2.8
===========================
- [*] Misc fixes and optimizations

Files modified
-----
- /af_seopages.php
- /views/css/back.css
- /views/js/back.js
- /views/templates/admin/center-panel.tpl
- /views/templates/admin/qs-results.tpl

===========================
v0.2.6 (August 22, 2023) - Requires Amazzing Filter v3.2.5
===========================
- [*] Allow slashes in bulk-generated URLs
- [*] Misc bug fixes and optimizations

Files modified
-----
- /af_seopages.php
- /classes/BulkGenerator.php
- /controllers/front/seopage.php
- /upgrade/install-0.1.2.php
- /upgrade/install-0.2.0.php
- /views/css/back.css
- /views/js/back.js

===========================
v0.2.5 (May 19, 2023) - Requires Amazzing Filter v3.2.4
===========================
- [*] Compatibility with PS 8.0+
- [*] Skip empty pages during bulk generation
- [*] Misc bug fixes and optimizations

Files modified
-----
- /af_seopages.php
- /classes/BulkGenerator.php
- /classes/SiteMap.php
- /controllers/front/seopage.php
- /views/css/bulk-generate.css
- /views/js/bulk-generate.js
- /views/templates/admin/bulk-generate.tpl

===========================
v0.2.4 (February 7, 2023) - Requires Amazzing Filter v3.2.3
===========================
- [+] Bulk add/update/delete SEO Pages
- [+] Quick search for SEO Pages in BackOffice
- [+] Active/inactive status for SEO pages
- [+] Possibility to use emoji in Title, Header, Description and other fields
- [*] Fixed page number in URLs
- [*] Improved compatibility with Warehouse theme in PS 1.6
- [*] Show the language of tags that are used as criteria for SEO pages
- [*] Misc fixes and optimizations

Files modified
-----
- /af_seopages.php
- /classes/SiteMap.php
- /controllers/front/seopage.php
- /upgrade/install-0.2.2.php
- /views/css/back.css
- /views/js/back.js
- /views/js/front.js
- /views/js/front-extra.js
- /views/templates/admin/center-panel.tpl
- /views/templates/admin/side-panel.tpl
- /views/templates/admin/sp-items.tpl

Files added
-----
- /classes/BulkGenerator.php
- /upgrade/install-0.2.4.php
- /views/css/bulk-generate.css
- /views/js/bulk-generate.js
- /views/templates/admin/bulk-generate.tpl

===========================
v0.2.3 (November 2, 2022) - Requires Amazzing Filter v3.2.1
===========================
- [+] Auto-fill empty header and meta_title on saving
- [*] Dynamically update URL to canonical of the matched SEO Page after applying filters on native category pages
- [*] Fixed selecting criteria in Firefox
- [*] Misc fixes and optimizations

Files modified
-----
- /af_seopages.php
- /controllers/front/seopage.php
- /views/css/back.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/center-panel.tpl
- /views/templates/admin/sp-item-form.tpl

Files added
-----
- /upgrade/install-0.2.3.php
- /views/js/front-extra-16.js
- /views/js/front-extra.js

===========================
v0.2.2 (February 12, 2022)
===========================
- [*] Moved sitemaps from module directory to root directory
- [*] Include page number in meta_title
- [*] Improved compatibility with Warehouse theme in PS 1.7
- [*] Improved compatibility with PS 1.7.8
- [*] Misc fixes and optimizations

Files modified
-----
- /af_seopages.php
- /classes/SiteMap.php
- /controllers/front/seopage.php
- /views/css/back.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/sp-item-duplicates.tpl
- /views/templates/admin/sp-item-form.tpl
- /views/templates/admin/sp-items.tpl

Files added
-----
- /upgrade/install-0.2.2.php
- /views/templates/admin/qs-results.tpl

Directories removed:
-----
- /sitemap/

===========================
v0.2.1 (July 3, 2021)
===========================
- [*] Compatibility with AF 3.1.6
- [*] Minor fixes

Files modified
-----
- /af_seopages.php
- /controllers/front/seopage.php

===========================
v0.2.0 (May 15, 2021)
===========================
- [+] Compatibility with PS 1.6
- [+] Configurable left/right columns on SEO Pages
- [+] Possibility to duplicate SEO Pages
- [+] Possibility to add multiple SEO Pages with same criteria but different meta fields
- [*] Minor fixes

Files modified
-----
- /af_seopages.php
- /controllers/front/seopage.php
- /views/css/back.css
- /views/js/back.js
- /views/js/front.js
- /views/templates/admin/center-panel.tpl
- /views/templates/admin/sp-item-form.tpl
- /views/templates/admin/sp-items.tpl

Files added
-----
- /upgrade/install-0.2.0.php
- /views/templates/admin/sp-item-duplicates.tpl
- /views/templates/front/breadcrumb-16.tpl
- /views/templates/front/seopage-16.tpl

===========================
v0.1.2 (September 14, 2020)
===========================
- [+] XML Sitemaps
- [+] Custom description below product list
- [+] Basic breadcrumbs on custom pages
- [+] Compatibility with Google sitemap module by Prestashop
- [*] Minor fixes

Files modified
-----
- /af_seopages.php
- /controllers/front/seopage.php
- /views/css/back.css
- /views/js/front.js
- /views/templates/front/seopage.tpl
- /translations/pl.php

Files added
-----
- /classes/SiteMap.php
- /upgrade/install-0.1.2.php

===========================
v0.1.1 (June 8, 2020)
===========================
- [+] Added sitemaps

Files modified
-----
- /af_seopages.php
- /views/js/back.js
- /views/templates/admin/center-panel.tpl
- /views/templates/admin/side-panel.tpl

Directories added
-----
- /sitemap/

===========================
v0.1.0 (May, 2020)
===========================
Initial release
