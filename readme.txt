=== BlackSwan | Block External Request ===
Contributors: blackswanlab, amirhpcom
Donate link: https://amirhp.com/contact/#payment
Tags: external requests, performance, blacklist, whitelist, block http requests
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 5.4
Stable tag: 2.9.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Block unwanted external HTTP requests in WordPress. Blacklist/whitelist management, resource blocking, and one-click pause.

== Description ==

**BlackSwan Block External Request** gives you granular control over every outgoing connection your WordPress site makes — both server-side PHP requests and browser-loaded JS/CSS resources.

WordPress, plugins, and themes constantly send background HTTP requests: update checks, license pings, analytics, font downloads, CDN calls, and more. On slow servers or restricted hosting environments, these requests can add **seconds** to every admin page load.

This plugin lets you **block what you don't need** and **keep what you do**.

= What it does =

**Server-side HTTP Blocking (PHP)**
Intercepts outgoing `wp_remote_get` / `wp_remote_post` calls via the `pre_http_request` filter. Add domains to the blacklist and they'll be blocked before the request is even made. Whitelist specific URL patterns to let essential requests through (e.g. plugin update API).

**Browser-side Resource Blocking (JS/CSS by Domain)**
Deregisters enqueued JavaScript and CSS files loaded from blacklisted external domains. Toggle separately for admin panel and public frontend. Your own site's assets are never touched.

**Block Specific Resources (by URL)**
Block individual JS or CSS files by full URL, partial path, or even just a filename — works for both local and external resources. Per-item backend/frontend toggle. Perfect for removing unwanted plugin assets without editing code.

= Features =

* Blacklist & whitelist with inline edit, delete, and delete-all
* Block external JS/CSS by domain (backend, frontend, or both)
* Block specific resources by URL pattern (local or external)
* One-click pause/resume — instantly disable all blocking
* Safe mode via `?bswan-safe=1` — emergency bypass for any admin page
* Settings page auto-bypasses resource blocking so you never lock yourself out
* Export/import all settings as a single JSON file
* AJAX-powered save — no page reloads
* Query Monitor integration — detect, activate, or install from settings
* All settings stored as a single JSON option with `autoload=no` for performance
* Fully standalone — zero external dependencies (inline Lucide SVG icons)
* Modern liquid glass UI with dot-grid background
* WordPress native postbox layout with collapsible sections
* Translation-ready with full text domain support

= Who is this for? =

* Sites on slow or restricted hosting where external calls cause timeouts
* Developers debugging performance issues
* Agencies managing client sites that don't need update checks
* Anyone who wants a faster wp-admin experience

= Developer Hooks =

Four filters are available for developers to customize blocking behavior programmatically. These run on every page load and merge with the values from the settings page.

**`BlackSwan\block_external_request\block_url_list`**

Filter the array of blocked domain strings. Each entry is matched via `strpos()` against the full request URL.

`add_filter( 'BlackSwan\block_external_request\block_url_list', function( $domains ) {
    $domains[] = 'analytics.example.com';
    return $domains;
});`

**`BlackSwan\block_external_request\whitelist_urls`**

Filter the array of whitelisted URL patterns. If a blocked URL also matches a whitelist pattern (via `strpos()`), the request is allowed through.

`add_filter( 'BlackSwan\block_external_request\whitelist_urls', function( $patterns ) {
    $patterns[] = '//api.example.com/v2/license';
    return $patterns;
});`

Whitelist patterns take priority over blacklist domains. Both filters accept and return a flat array of strings.

**`BlackSwan\block_external_request\blocked_resources`**

Filter the array of specific JS/CSS resources to block. Each entry is an associative array with `url`, `backend`, and `frontend` keys. The `url` is matched via `strpos()` against each registered script/style source.

`add_filter( 'BlackSwan\block_external_request\blocked_resources', function( $resources ) {
    $resources[] = array( 'url' => 'some-plugin/tracking.js', 'backend' => false, 'frontend' => true );
    return $resources;
});`

**`BlackSwan\block_external_request\cdn_replacements`**

Filter the array of CDN replacement rules. Each entry is an associative array with `pattern`, `cdn`, `backend`, and `frontend` keys. When an enqueued asset source contains the `pattern`, it is replaced with the `cdn` URL.

`add_filter( 'BlackSwan\block_external_request\cdn_replacements', function( $replacements ) {
    $replacements[] = array( 'pattern' => '/wp-includes/js/jquery/jquery.min.js', 'cdn' => 'https://cdn.example.com/jquery/3.7.1/jquery.min.js', 'backend' => false, 'frontend' => true );
    return $replacements;
});`

= Links =

* [Developer — AmirhpCom](https://amirhp.com/)
* [BlackSwan](https://blackswandev.com/)
* [Plugin GitHub Page](https://github.com/blackswandevcom/blackswan-block-external-request/)
* [Rate 5-Star](https://wordpress.org/support/plugin/blackswan-block-external-request/reviews/#new-post)
* [Support & Issues](https://wordpress.org/support/plugin/blackswan-block-external-request/)

== Installation ==

1. Upload the `blackswan-block-external-request` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Go to **Settings → Block External Request** to configure

Or install directly from WordPress:

1. Go to **Plugins → Add New**
2. Search for "BlackSwan Block External Request"
3. Click **Install Now**, then **Activate**

The plugin comes pre-configured with a sensible default blacklist and whitelist. You can customize everything from the settings page.

== Frequently Asked Questions ==

= Will this break my site? =

It depends on what you block. The default blacklist blocks common domains that slow down the admin panel (wordpress.org, google.com, yoast.com, etc.). The default whitelist ensures plugin update API calls still work. If something breaks, use the **Pause** button or **Safe Mode** to quickly restore access.

= What's the difference between the three blocking sections? =

1. **Server-side HTTP (PHP)** — Blocks background requests made by WordPress via PHP. These are invisible to the browser (update checks, API calls, license pings).
2. **Browser-side by Domain** — Dequeues JS/CSS files loaded from blacklisted external domains (e.g. Google Fonts, CDN libraries).
3. **Block Specific Resources** — Dequeues individual JS/CSS files by matching any part of their URL. Works for local files too.

= I blocked something and now I can't access wp-admin =

Add `?bswan-safe=1` to any admin URL (e.g. `yoursite.com/wp-admin/?bswan-safe=1`). This bypasses all blocking for that page load. The plugin settings page also automatically skips resource blocking.

= Does this affect the frontend? =

Server-side HTTP blocking applies everywhere. Browser-side resource blocking and specific resource blocking have separate toggles for backend and frontend — you control where each applies.

= Where are settings stored? =

All settings are stored as a single JSON value in `wp_options` with `autoload=no` for optimal performance. No separate database tables.

= Can I export/import settings between sites? =

Yes. The Export/Import panel in the sidebar lets you download all settings as a JSON file and import it on another site.

= How can I contribute? =

We welcome contributions! You can:

* Report bugs or suggest features on [WordPress.org Support](https://wordpress.org/support/plugin/blackswan-block-external-request/) or [GitHub Issues](https://github.com/blackswandevcom/blackswan-block-external-request/issues)
* Submit pull requests on [GitHub](https://github.com/blackswandevcom/blackswan-block-external-request/)
* Translate the plugin via [WordPress.org Translate](https://translate.wordpress.org/projects/wp-plugins/blackswan-block-external-request/)
* Rate the plugin [5 stars](https://wordpress.org/support/plugin/blackswan-block-external-request/reviews/#new-post) if you find it useful

== Screenshots ==

1. Collapsed Settings page [EN]
2. Collapsed Settings page [FA]
3. Expanded Settings page [EN]
4. Expanded Settings page [FA]
5. Initial version with no UI

== Changelog ==

= 2.9.3 =
* Release date: 2026-04-13 | 1405-01-24
* Fixed Translation
* Added `fontawesome` to exclude list
* Added `cdn.jsdelivr.net` to exclude list
* Added `unpkg.com` to exclude list
* Added `googletagmanager.com` to exclude list
* Added `cdnjs.cloudflare.com` to exclude list
* Added `fonts.googleapis.com` to exclude list
* Added `fonts.gstaticn.com` to exclude list
* Set some default options ON by default

= 2.9.0 =
* Added "Disable All Emoji" option: removes WordPress emoji detection script, emoji styles, TinyMCE emoji plugin, and DNS prefetch hints for the emoji CDN — eliminating outgoing requests to s.w.org

= 2.8.0 =
* Expanded the default blacklist with 30+ additional domains commonly responsible for license checks, telemetry, and update pings (e.g. freemius.com, themeforest.com, cloudflare.com, wpbakery.com, xtemos.com, premio.io, nextendweb.com, objectcache.pro, rocketcdn.me, ipinfo.io, paypal.com, and several Iranian plugin/theme vendors)

= 2.7.0 =
* Documented all four developer filters with full examples (`block_url_list`, `whitelist_urls`, `blocked_resources`, `cdn_replacements`)
* Improved readme files for WordPress.org publishing

= 2.6.7 =
* Added at-a-glance overview panel at the top of the settings page with 5 stat cards (HTTP blocking, browser resources, specific resources, CDN replacements, avatars) for non-technical users
* All technical configuration metaboxes now hidden behind a "Configure & Advanced Settings" toggle, collapsed by default, state remembered in localStorage
* Moved "Reset to Defaults" into its own dedicated sidebar metabox with a clear destructive-action warning
* Fixed confirm dialog line breaks (were showing as literal \n on some browsers)

= 2.6.6 =
* Added "Reset to Defaults" button in Export/Import panel with a destructive-action warning notice and two-step confirmation before wiping settings

= 2.6.5 =
* Added new "CDN Resource Replacements" section: replace enqueued JS/CSS with CDN versions by pattern matching, with per-entry backend/frontend toggles, and predefined examples (jQuery, Bootstrap via lib.arvancloud.ir)
* Export/Import support for CDN replacements
* DEV: Added `BlackSwan\block_external_request\cdn_replacements` filter for programmatic replacement rules

= 2.6.4 =
* Added new "Avatars" section in settings: option to disable all WordPress avatars site-wide (default: disabled), preventing outgoing Gravatar requests and removing avatar markup

= 2.6.2 =
* Added pre-defined list of common analytics/tracking domains to the default blacklist (e.g. Google Analytics, Hotjar, Matomo etc.)
* Added pre-defined list of common Iranian payment gateway domains to the default blacklist (e.g. Zarinpal, Pay.ir, IDPay etc.)
* DEV: Added `BlackSwan\block_external_request\blocked_resources` filter to allow programmatic blocking of specific JS/CSS resources by URL pattern

= 2.6.0 =
* New: Modern liquid glass UI with frosted postboxes and dot-grid background
* New: Inline Lucide SVG icons — fully standalone, zero external dependencies
* New: Status badge with animated icons (activity pulse for active, circle-pause for paused)
* New: Query Monitor three-state detection (active / installed but inactive / not installed)
* New: One-click activate button for Query Monitor when installed but not active
* Improved: Gradient badges and softer visual language throughout
* Improved: All buttons use consistent flex layout with icon + text

= 2.5.0 =
* New: Replaced all WordPress dashicons with inline Lucide SVGs
* New: Plugin is now fully standalone — no external CSS, fonts, or icon libraries
* New: SVG icon helper method with 20+ icons for consistent rendering
* Improved: JS-rendered table rows use PHP-generated SVG strings for icons

= 2.4.0 =
* New: Query Monitor integration — detect active, installed, or missing; one-click activate/install
* Fixed: Three-state QM detection (active vs installed-but-inactive vs not-installed)
* Improved: Activate link uses proper `wp_nonce_url` for one-click activation

= 2.3.1 =
* Fixed: Postbox headers now use proper WordPress core markup (`postbox-header`, `handle-actions`, `handlediv`)
* Fixed: Native collapsible postbox behavior via `postboxes.add_postbox_toggles(pagenow)`
* Removed: Custom postbox toggle CSS and JavaScript — WordPress core handles it all

= 2.3.0 =
* New: Safe mode — add `?bswan-safe=1` to any admin URL to bypass all blocking
* New: Settings page automatically skips resource dequeuing (not full safe mode)
* New: Safe Mode sidebar metabox with direct link and usage tips
* New: Collapsible postboxes for main content sections
* Improved: Clear separation between safe mode (full bypass) and settings page (resource-only bypass)

= 2.2.0 =
* New: Block Specific Resources section — block individual JS/CSS by full URL, partial path, or filename
* New: Per-item backend/frontend checkboxes for specific resource blocking
* New: WordPress post-editor style two-column layout with sidebar metaboxes
* New: Sidebar metaboxes for Save/Pause, Tools, Export/Import, and Disclaimer
* New: Global export/import — single JSON file for all settings
* Removed: Individual per-list export/import buttons
* Fixed: Blocked resources checkbox state now persists correctly after save (boolean → integer serialization)

= 2.1.0 =
* New: Browser-side resource blocking — dequeue external JS/CSS from blacklisted domains
* New: Separate toggles for admin panel and public frontend
* New: Resource blocking respects whitelist patterns
* Improved: Settings auto-migrate on upgrade with safe defaults

= 2.0.0 =
* Complete rewrite with settings page
* New: Visual blacklist/whitelist management with inline edit, delete, delete-all
* New: AJAX-powered save — no page reloads
* New: Temporary pause button to disable all blocking without losing rules
* New: JSON export/import for blacklist and whitelist
* New: Query Monitor install link
* New: Disclaimer notice
* New: Settings stored as single JSON option with `autoload=no`
* New: Settings link on plugins list page
* New: Default blacklist and whitelist seeded on first activation

= 1.1.0 =
* Added whitelist support — allow specific URL patterns through the blacklist
* Added `BlackSwan\block_external_request\whitelist_urls` filter
* Default whitelist includes WordPress plugin API and download URLs

= 1.0.0 =
* Initial release
* Block external HTTP requests by domain via `pre_http_request` filter
* Predefined blacklist of common domains
* Filter hook `BlackSwan\block_external_request\block_url_list` for customization

== Upgrade Notice ==

= 2.9.3 =
- Release date: 2026-04-13 | 1405-01-24
- Fixed Translation
- Added `fontawesome` to exclude list
- Added `cdn.jsdelivr.net` to exclude list
- Added `unpkg.com` to exclude list
- Added `googletagmanager.com` to exclude list
- Added `cdnjs.cloudflare.com` to exclude list
- Added `fonts.googleapis.com` to exclude list
- Added `fonts.gstaticn.com` to exclude list
- Set some default options ON by default

= 2.9.0 =
- Release date: 2026-04-09 | 1405-01-20
- New "Disable All Emoji" toggle eliminates outgoing WordPress emoji requests. Safe to upgrade, all settings preserved.

= 2.8.0 =
- Release date: 2026-04-06 | 1405-01-17
- Expanded default blacklist with 30+ additional license/telemetry/update domains. Safe to upgrade, all settings preserved.

For the full changelog, see [GitHub Repository](https://github.com/blackswandevcom/blackswan-block-external-request?tab=readme-ov-file#changelog).

== Copyright ==

BlackSwan Block External Request is free software distributed under the terms of the GNU General Public License v2 or later.

Copyright (c) AmirhpCom — [amirhp.com](https://amirhp.com/)

This plugin is developed and maintained by [BlackSwan Lab](https://blackswandev.com/).

You are free to use, modify, and distribute this plugin under the GPLv2 license. The developers are not responsible for any issues caused by misconfigured blocking rules. Always maintain proper backups before making changes to your site's HTTP request behavior.