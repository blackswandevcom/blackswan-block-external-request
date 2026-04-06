<?php
/*
 Plugin Name: BlackSwan | Block External Request
 Description: Take control of outgoing HTTP requests in WordPress. Block unwanted external calls to speed up your admin panel, with full blacklist/whitelist management and one-click pause.
 Author: AmirhpCom
 Author URI: https://amirhp.com/
 Plugin URI: https://github.com/blackswandevcom/blackswan-block-external-request/
 Contributors: blackswanlab, amirhpcom
 Donate link: https://amirhp.com/contact/#payment
 Tags: external requests, performance, blacklist, whitelist, block http requests
 Version: 2.8.0
 Stable tag: 2.8.0
 Requires PHP: 5.4
 Tested up to: 6.8
 Requires at least: 5.0
 Text Domain: blackswan-block-external-request
 Domain Path: /languages
 Copyright: (c) amirhp.com, All rights reserved.
 License: GPLv2 or later
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * @Last modified by: amirhp-com <its@amirhp.com>
 * @Last modified time: 2026/04/06 23:35:07
*/

namespace BlackSwan;

defined("ABSPATH") or die("<h2>Unauthorized Access!</h2><hr><small>BlackSwan | Block External Request Plugin :: Developed by Amirhp-com (<a href='https://amirhp.com/'>https://amirhp.com/</a>)</small>");
if (!class_exists("\BlackSwan\blockExternalRequest")) {
    class blockExternalRequest {
        public $td = "blackswan-block-external-request";
        public $version = "2.8.0";
        public $title = "Block External Request";
        protected $block_url_list;
        protected $whitelist_urls;
        private $option_key = 'bswan_ber_settings';
        private $settings = null;
        private static $default_blacklist = array(
            "w.org",
            "w.com",
            "wp.org",
            "wp.com",
            "wincher.com",
            "yoa.st",
            "bitbucket.org",
            "us-themes.com",
            "duplicator.com",
            "yoast.com",
            "wordpress.org",
            "wordpress.com",
            "woocommerce.com",
            "reduxframework.com",
            "wp-rocket.me",
            "easydigitaldownloads.com",
            "github.com",
            "google.com",
            "rtl-theme.com",
            "zhaket",
            "premio.io",
            "nextendweb.com",
            "objectcache.pro",
            "themeforest.com",
            "wpbakery.com",
            "cloudflare.com",
            "xtemos.com",
            "themehigh.com",
            "gravityapi.com",
            "wpcode.com",
            "wpmailsmtpapi.com",
            "freemius.com",
            "translationspress.com",
            "wpcodeusage.com",
            "gpltimes.com",
            "sigmaplugin.com",
            "rocketcdn.me",
            "gravityforms.ir",
            "woocommerce.ir",
            "woosupport.ir",
            "wpmailsmtp.com",
            "wpmailsmtpusage.com",
            "wpnovin.com",
            "ipinfo.io",
            "woohash.ir",
            "woonotice.ir",
            "paypal.com",
            "ip-api.co",
            "gravatar.com",
            "googleapis",
            "fonts.googleapis.com",
            "my.elementor.com"
        );
        private static $default_whitelist = array(
            // "//api.wordpress.org/plugins/",
            // "//downloads.wordpress.org/",
            'zarinpal.com',
            'idpay.ir',
            'pay.ir',
            'nextpay.org',
            'zibal.ir',
            'vandar.io',
            'payping.ir',
            'payping.io',
            'bitpay.ir',
            'paystar.ir',
            'irandargah.com',
            'aqayepardakht.ir',
            'jibit.ir',
            'digipay.ir',
            'mydigipay.com',
            'shaparak.ir',
            'sep.ir',
            'pec.ir',
            'pep.co.ir',
            'sadadpsp.ir',
            'behpardakht.com',
            'irankish.com',
            'fanavacard.ir',
            'ecd-co.ir',
            'pna.co.ir',
            'persianswitch.com',
            'asanpardakht.ir',
            'sepehrpay.com',
            'snapppay.ir',
            'snapp.ir',
            'sms.ir',
            'ippanel.com',
            'melipayamak.com',
            'payamak-panel.com',
            'farazsms.com',
            'kavenegar.com',
            'ghasedak.me',
            'magfa.com',
            'parsgreen.com',
            'payamito.com',
            'webone-sms.com',
            'raygansms.com',
            'niksms.com',
            'post.ir',
            'tipaxco.com',
            'alopeyk.com',
            'tapin.ir',
            'postex.ir',
            'chapar.co',
            'torob.com',
            'torob.ir',
            'emalls.ir',
        );
        private static $default_resources = array(
            ['url' => 'persian-woocommerce/assets/fonts/admin-font.css', 'backend' => true, 'frontend' => true],
            ['url' => 'googletagmanager.com', 'backend' => true, 'frontend' => true],
            ['url' => 'google-analytics.com', 'backend' => true, 'frontend' => true],
            ['url' => 'analytics.js', 'backend' => true, 'frontend' => true],
            ['url' => 'gtag/js', 'backend' => true, 'frontend' => true],
            ['url' => 'tagmanager', 'backend' => true, 'frontend' => true],
            ['url' => 'googlesyndication.com', 'backend' => true, 'frontend' => true],
            ['url' => 'doubleclick.net', 'backend' => true, 'frontend' => true],
            ['url' => 'googleadservices.com', 'backend' => true, 'frontend' => true],
            ['url' => 'adsbygoogle', 'backend' => true, 'frontend' => true],
            ['url' => 'pagead2.googlesyndication.com', 'backend' => true, 'frontend' => true],
            ['url' => 'yektanet', 'backend' => true, 'frontend' => true],
            ['url' => 'cdn.yektanet.com', 'backend' => true, 'frontend' => true],
            ['url' => 'rg.complete.js', 'backend' => true, 'frontend' => true],
            ['url' => 'tapsell', 'backend' => true, 'frontend' => true],
            ['url' => 'adivery', 'backend' => true, 'frontend' => true],
            ['url' => 'mediaad', 'backend' => true, 'frontend' => true],
            ['url' => 'adtrace', 'backend' => true, 'frontend' => true],
            ['url' => 'cafebazaar', 'backend' => true, 'frontend' => true],
            ['url' => 'connect.facebook.net', 'backend' => true, 'frontend' => true],
            ['url' => 'clarity.ms', 'backend' => true, 'frontend' => true],
            ['url' => 'hotjar', 'backend' => true, 'frontend' => true],
            ['url' => 'tiktok', 'backend' => true, 'frontend' => true],
            ['url' => 'snap.licdn.com', 'backend' => true, 'frontend' => true],
            ['url' => 'linkedin', 'backend' => true, 'frontend' => true],
            ['url' => 'matomo', 'backend' => true, 'frontend' => true],
            ['url' => 'plausible', 'backend' => true, 'frontend' => true],
            ['url' => 'umami', 'backend' => true, 'frontend' => true],
            ['url' => 'mixpanel', 'backend' => true, 'frontend' => true],
            ['url' => 'segment.com', 'backend' => true, 'frontend' => true],
            ['url' => 'sentry.io', 'backend' => true, 'frontend' => true],
            ['url' => 'rtl-theme.com', 'backend' => true, 'frontend' => true],
            ['url' => 'zhaket', 'backend' => true, 'frontend' => true],
            ['url' => 'gravatar.com', 'backend' => true, 'frontend' => true],
            ['url' => 'googleapis', 'backend' => true, 'frontend' => true],
            ['url' => 'fonts.googleapis.com', 'backend' => true, 'frontend' => true],
            ['url' => 'my.elementor.com', 'backend' => true, 'frontend' => true],
        );
        private static $default_cdn_replacements = array(
            ['pattern' => 'jquery.js',    'cdn' => 'https://lib.arvancloud.ir/jquery/3.6.3/jquery.js',                                    'backend' => false, 'frontend' => false],
            ['pattern' => 'bootstrap',    'cdn' => 'https://lib.arvancloud.ir/bootstrap/5.3.0-alpha1/css/bootstrap-grid.css',              'backend' => false, 'frontend' => false],
        );
        private static function icon($name, $color = 'currentColor', $size = 18) {
            $s = $size;
            $icons = array(
                'cloud'          => '<path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>',
                'shield'         => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>',
                'shield-check'   => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
                'circle-check'   => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
                'globe'          => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>',
                'settings'       => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
                'monitor'        => '<rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/>',
                'ban'            => '<circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/>',
                'trash'          => '<path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/>',
                'pencil'         => '<path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>',
                'x'              => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
                'save'           => '<path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/>',
                'pause'          => '<rect x="14" y="4" width="4" height="16" rx="1"/><rect x="6" y="4" width="4" height="16" rx="1"/>',
                'play'           => '<polygon points="6 3 20 12 6 21 6 3"/>',
                'search'         => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
                'check'          => '<path d="M20 6 9 17l-5-5"/>',
                'download'       => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>',
                'upload'         => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/>',
                'circle-pause'   => '<circle cx="12" cy="12" r="10"/><line x1="10" x2="10" y1="15" y2="9"/><line x1="14" x2="14" y1="15" y2="9"/>',
                'circle-play'    => '<circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/>',
                'activity'       => '<path d="M22 12h-2.48a2 2 0 0 0-1.93 1.46l-2.35 8.36a.25.25 0 0 1-.48 0L9.24 2.18a.25.25 0 0 0-.48 0l-2.35 8.36A2 2 0 0 1 4.49 12H2"/>',
                'user'           => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
                'repeat'         => '<path d="m17 2 4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="m7 22-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>',
                'rotate-ccw'     => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>',
            );
            if (!isset($icons[$name])) return '';
            return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;flex-shrink:0;">' . $icons[$name] . '</svg>';
        }
        public function __construct() {

            load_plugin_textdomain($this->td, false, dirname(plugin_basename(__FILE__)) . "/languages/");

            add_action("init", function () {
                $this->title = __("Block External Request", $this->td);
            });

            $this->load_settings();

            if (!empty($this->settings['disable_avatars'])) {
                add_filter('pre_option_show_avatars', '__return_zero');
                add_filter('get_avatar', '__return_empty_string');
            }

            if (!empty($this->settings['cdn_replacements'])) {
                add_filter('script_loader_src', array($this, 'replace_cdn_resource'), 10, 1);
                add_filter('style_loader_src',  array($this, 'replace_cdn_resource'), 10, 1);
            }

            $this->block_url_list = apply_filters("BlackSwan\block_external_request\block_url_list", $this->settings['blacklist']);
            $this->whitelist_urls = apply_filters("BlackSwan\block_external_request\whitelist_urls", $this->settings['whitelist']);

            $is_safe     = $this->is_safe_mode();
            $is_paused   = !empty($this->settings['paused']);
            $is_own_page = (is_admin() && isset($_GET['page']) && $_GET['page'] === 'bswan-ber-settings');

            if (!$is_safe && !$is_paused) {
                add_filter("pre_http_request", array($this, "block_external_request"), 10, 3);
                if (!$is_own_page) {
                    if (!empty($this->settings['block_resources_backend'])) add_action("admin_enqueue_scripts", array($this, "dequeue_blocked_resources"), 9999);
                    if (!empty($this->settings['block_resources_frontend'])) add_action("wp_enqueue_scripts", array($this, "dequeue_blocked_resources"), 9999);
                    $block_res = !empty($this->settings['blocked_resources']) ? $this->settings['blocked_resources'] : array();
                    if (!empty($block_res)) {
                        $has_backend = $has_frontend = false;
                        foreach ($block_res as $r) {
                            if (!empty($r['backend'])) $has_backend = true;
                            if (!empty($r['frontend'])) $has_frontend = true;
                        }
                        if ($has_backend)  add_action("admin_enqueue_scripts", array($this, "dequeue_specific_resources"), 9999);
                        if ($has_frontend) add_action("wp_enqueue_scripts", array($this, "dequeue_specific_resources"), 9999);
                    }
                }
            }

            if (is_admin()) {
                add_action("admin_menu", array($this, "add_admin_menu"));
                add_action("wp_ajax_bswan_ber_save", array($this, "ajax_save_settings"));
                add_action("wp_ajax_bswan_ber_toggle_pause", array($this, "ajax_toggle_pause"));
                add_action("wp_ajax_bswan_ber_reset", array($this, "ajax_reset_settings"));
                add_filter("plugin_action_links_" . plugin_basename(__FILE__), array($this, "plugin_action_links"));
            }
        }
        private function is_safe_mode() {
            if (!is_admin()) return false;
            return (isset($_GET['bswan-safe']) && $_GET['bswan-safe'] === '1');
        }
        private function load_settings() {
            $defaults = array('blacklist' => self::$default_blacklist, 'whitelist' => self::$default_whitelist, 'paused' => false, 'block_resources_backend' => false, 'block_resources_frontend' => false, 'blocked_resources' => self::$default_resources, 'disable_avatars' => true, 'cdn_replacements' => self::$default_cdn_replacements);
            $raw = get_option($this->option_key, false);
            if ($raw === false) {
                $this->settings = $defaults;
                add_option($this->option_key, wp_json_encode($this->settings), '', 'no');
            } else {
                $decoded = json_decode($raw, true);
                $this->settings = is_array($decoded) ? array_merge($defaults, $decoded) : $defaults;
            }
        }
        private function save_settings() {
            update_option($this->option_key, wp_json_encode($this->settings), 'no');
        }
        public function plugin_action_links($links) {
            $url = admin_url('options-general.php?page=bswan-ber-settings');
            array_unshift($links, '<a href="' . esc_url($url) . '">' . __('Settings', $this->td) . '</a>');
            return $links;
        }
        public function add_admin_menu() {
            add_options_page(sprintf('%s — %s', $this->title, __('Settings', $this->td)), $this->title, 'manage_options', 'bswan-ber-settings', array($this, 'render_settings_page'));
        }
        public function ajax_save_settings() {
            check_ajax_referer('bswan_ber_nonce', '_nonce');
            if (!current_user_can('manage_options')) wp_send_json_error(__('Permission denied.', $this->td));
            $this->settings['blacklist'] = array_values(array_unique(array_filter(array_map('sanitize_text_field', (array)(isset($_POST['blacklist']) ? $_POST['blacklist'] : array())))));
            $this->settings['whitelist'] = array_values(array_unique(array_filter(array_map('sanitize_text_field', (array)(isset($_POST['whitelist']) ? $_POST['whitelist'] : array())))));
            $this->settings['block_resources_backend']  = !empty($_POST['block_resources_backend']);
            $this->settings['block_resources_frontend'] = !empty($_POST['block_resources_frontend']);
            $this->settings['disable_avatars']          = !empty($_POST['disable_avatars']);
            $blocked_resources = array();
            if (!empty($_POST['blocked_resources']) && is_array($_POST['blocked_resources'])) {
                foreach ($_POST['blocked_resources'] as $item) {
                    $url = sanitize_text_field(isset($item['url']) ? $item['url'] : '');
                    if (empty($url)) continue;
                    $blocked_resources[] = array('url' => $url, 'backend' => !empty($item['backend']), 'frontend' => !empty($item['frontend']));
                }
            }
            $this->settings['blocked_resources'] = $blocked_resources;
            $cdn_replacements = array();
            if (!empty($_POST['cdn_replacements']) && is_array($_POST['cdn_replacements'])) {
                foreach ($_POST['cdn_replacements'] as $item) {
                    $pattern = sanitize_text_field(isset($item['pattern']) ? $item['pattern'] : '');
                    $cdn     = esc_url_raw(isset($item['cdn']) ? $item['cdn'] : '');
                    if (empty($pattern) || empty($cdn)) continue;
                    $cdn_replacements[] = array('pattern' => $pattern, 'cdn' => $cdn, 'backend' => !empty($item['backend']), 'frontend' => !empty($item['frontend']));
                }
            }
            $this->settings['cdn_replacements'] = $cdn_replacements;
            $this->save_settings();
            wp_send_json_success(__('Settings saved successfully.', $this->td));
        }
        public function ajax_toggle_pause() {
            check_ajax_referer('bswan_ber_nonce', '_nonce');
            if (!current_user_can('manage_options')) wp_send_json_error(__('Permission denied.', $this->td));
            $this->settings['paused'] = !$this->settings['paused'];
            $this->save_settings();
            wp_send_json_success(array('paused' => $this->settings['paused'], 'message' => $this->settings['paused'] ? __('Plugin is now paused. All blocking is disabled.', $this->td) : __('Plugin is now active. Blocking rules are enforced.', $this->td)));
        }
        public function ajax_reset_settings() {
            check_ajax_referer('bswan_ber_nonce', '_nonce');
            if (!current_user_can('manage_options')) {
                wp_send_json_error(__('Permission denied.', $this->td));
                return;
            }
            delete_option($this->option_key);
            $this->load_settings();
            wp_send_json_success(__('All settings have been reset to defaults.', $this->td));
        }
        public function block_external_request($preempt, $parsed_args, $url) {
            foreach ($this->block_url_list as $bu) {
                if (strpos($url, $bu) !== false) {
                    foreach ($this->whitelist_urls as $wu) {
                        if (strpos($url, $wu) !== false) return $preempt;
                    }
                    return new \WP_Error('http_request_block', __("This request is blocked by 'BlackSwan - Block External Requests' plugin", $this->td) . "\n:: {$url}", $url);
                }
            }
            return $preempt;
        }
        public function dequeue_blocked_resources() {
            $this->dequeue_by_domain('scripts');
            $this->dequeue_by_domain('styles');
        }
        private function dequeue_by_domain($type) {
            global $wp_scripts, $wp_styles;
            $reg = ($type === 'scripts') ? $wp_scripts : $wp_styles;
            if (empty($reg) || empty($reg->registered)) return;
            $host = parse_url(home_url(), PHP_URL_HOST);
            foreach ($reg->registered as $h => $d) {
                if (empty($d->src)) continue;
                $src = $d->src;
                if (strpos($src, '//') === false) continue;
                $p = parse_url($src);
                if (empty($p['host']) || $p['host'] === $host) continue;
                $blocked = false;
                foreach ($this->block_url_list as $bd) {
                    if (strpos($p['host'], $bd) !== false) {
                        $wl = false;
                        foreach ($this->whitelist_urls as $ap) {
                            if (strpos($src, $ap) !== false) {
                                $wl = true;
                                break;
                            }
                        }
                        if (!$wl) {
                            $blocked = true;
                            break;
                        }
                    }
                }
                if ($blocked) {
                    if ($type === 'scripts') wp_deregister_script($h);
                    else wp_deregister_style($h);
                }
            }
        }
        public function dequeue_specific_resources() {
            $ia = is_admin();
            $bl = !empty($this->settings['blocked_resources']) ? $this->settings['blocked_resources'] : array();
            $bl = apply_filters("BlackSwan\block_external_request\blocked_resources", $bl);
            if (empty($bl)) return;
            $act = array();
            foreach ($bl as $it) {
                if ($ia && !empty($it['backend'])) $act[] = $it['url'];
                if (!$ia && !empty($it['frontend'])) $act[] = $it['url'];
            }
            if (empty($act)) return;
            $this->dequeue_specific_by_type('scripts', $act);
            $this->dequeue_specific_by_type('styles', $act);
        }
        private function dequeue_specific_by_type($type, $patterns) {
            global $wp_scripts, $wp_styles;
            $reg = ($type === 'scripts') ? $wp_scripts : $wp_styles;
            if (empty($reg) || empty($reg->registered)) return;
            foreach ($reg->registered as $h => $d) {
                if (empty($d->src)) continue;
                foreach ($patterns as $pt) {
                    if (strpos($d->src, $pt) !== false) {
                        if ($type === 'scripts') wp_deregister_script($h);
                        else wp_deregister_style($h);
                        break;
                    }
                }
            }
        }
        public function replace_cdn_resource($src) {
            $replacements = apply_filters("BlackSwan\block_external_request\cdn_replacements", !empty($this->settings['cdn_replacements']) ? $this->settings['cdn_replacements'] : array());
            if (empty($replacements)) return $src;
            $ia = is_admin();
            foreach ($replacements as $r) {
                if (empty($r['pattern']) || empty($r['cdn'])) continue;
                if ($ia && empty($r['backend']))  continue;
                if (!$ia && empty($r['frontend'])) continue;
                if (strpos($src, $r['pattern']) !== false) {
                    return esc_url_raw($r['cdn']);
                }
            }
            return $src;
        }
        // ─────────────────────────────────────────────
        // SETTINGS PAGE
        // ─────────────────────────────────────────────
        public function render_settings_page() {
            wp_enqueue_script('postbox');
            wp_enqueue_style($this->td, plugins_url("assets/admin-style.css", __FILE__), [], current_time("timestamp"), "all");
            $is_paused        = !empty($this->settings['paused']);
            $is_safe          = $this->is_safe_mode();
            $res_backend      = !empty($this->settings['block_resources_backend']);
            $res_frontend     = !empty($this->settings['block_resources_frontend']);
            $disable_avatars  = !empty($this->settings['disable_avatars']);
            $br_list      = !empty($this->settings['blocked_resources']) ? $this->settings['blocked_resources'] : array();
            $cdn_list     = !empty($this->settings['cdn_replacements'])  ? $this->settings['cdn_replacements']  : array();
            $bl_count     = count($this->settings['blacklist']);
            $wl_count     = count($this->settings['whitelist']);
            $br_count     = count($br_list);
            $br_be_count  = count(array_filter($br_list,  function ($r) {
                return !empty($r['backend']);
            }));
            $br_fe_count  = count(array_filter($br_list,  function ($r) {
                return !empty($r['frontend']);
            }));
            $cdn_count    = count($cdn_list);
            $cdn_be_count = count(array_filter($cdn_list, function ($r) {
                return !empty($r['backend']);
            }));
            $cdn_fe_count = count(array_filter($cdn_list, function ($r) {
                return !empty($r['frontend']);
            }));
            $blacklist_json   = wp_json_encode(array_values($this->settings['blacklist']));
            $whitelist_json   = wp_json_encode(array_values($this->settings['whitelist']));
            $blocked_res_json = wp_json_encode(array_values(!empty($this->settings['blocked_resources']) ? $this->settings['blocked_resources'] : array()));
            $cdn_replace_json = wp_json_encode(array_values(!empty($this->settings['cdn_replacements']) ? $this->settings['cdn_replacements'] : array()));
            $nonce            = wp_create_nonce('bswan_ber_nonce');
            $ajax_url         = admin_url('admin-ajax.php');
            $qm_active        = is_plugin_active('query-monitor/query-monitor.php');
            $qm_installed     = file_exists(WP_PLUGIN_DIR . '/query-monitor/query-monitor.php');
            $qm_url           = $qm_installed ? wp_nonce_url(admin_url('plugins.php?action=activate&plugin=query-monitor/query-monitor.php'), 'activate-plugin_query-monitor/query-monitor.php') : admin_url('plugin-install.php?s=query+monitor&tab=search&type=term');
            $safe_mode_url    = admin_url('options-general.php?page=bswan-ber-settings&bswan-safe=1');
            $i = array(
                'cloud'    => self::icon('cloud', '#2271b1'),
                'shield'   => self::icon('shield', '#d63638'),
                'circhk'   => self::icon('circle-check', '#00a32a'),
                'globe'    => self::icon('globe', '#826eb4'),
                'gear'     => self::icon('settings', '#826eb4'),
                'monitor'  => self::icon('monitor', '#826eb4'),
                'ban'      => self::icon('ban', '#dba617'),
                'trash'    => self::icon('trash', '#d63638', 16),
                'save'     => self::icon('save', '#fff'),
                'pause'    => self::icon('pause', 'currentColor'),
                'play'     => self::icon('play', 'currentColor'),
                'search'   => self::icon('search', 'currentColor'),
                'check'    => self::icon('check', '#00a32a'),
                'download' => self::icon('download', 'currentColor'),
                'upload'   => self::icon('upload', 'currentColor'),
                'shldchk'  => self::icon('shield-check', '#2271b1'),
                'cpause'   => self::icon('circle-pause', '#dba617', 16),
                'cplay'    => self::icon('circle-play', '#00a32a', 16),
                'activity' => self::icon('activity', '#00a32a', 16),
                'user'     => self::icon('user', '#826eb4'),
                'repeat'   => self::icon('repeat', '#826eb4'),
                'reset'    => self::icon('rotate-ccw', '#d63638'),
            );
            add_filter("admin_footer_text", function () {
                return sprintf(__('Developed by %s — Another Free & Open-source project by %s', $this->td), '<a href="https://amirhp.com/" target="_blank">AmirhpCom</a>', '<a href="https://blackswandev.com/" target="_blank">BlackSwan</a>');
            }, 9999);
            ?>
            <div class="wrap bswan-wrap">
                <h1 class="bswan-title"><?php echo esc_html__("BlackSwan - Block External Request", $this->td); ?> <sup><?php echo esc_html($this->version); ?></sup></h1>

                <?php if ($is_safe): ?>
                    <div class="bswan-notice bswan-notice-info"><?php echo $i['shldchk']; ?> <strong><?php _e('Safe Mode is active.', $this->td); ?></strong> <?php _e('All blocking rules are temporarily bypassed on this page load.', $this->td); ?></div>
                <?php endif; ?>

                <!-- ── Overview ── -->
                <div id="bswan-overview">
                    <div class="bswan-ov-status <?php echo $is_paused ? 'bswan-status-paused' : 'bswan-status-active'; ?>">
                        <span><?php echo $is_paused ? $i['cpause'] : $i['activity']; ?></span>
                        <div>
                            <strong><?php echo $is_paused ? esc_html__('Plugin is Paused', $this->td) : esc_html__('Plugin is Active', $this->td); ?></strong>
                            <?php echo $is_paused ? esc_html__('All blocking is temporarily suspended — no requests or resources are being filtered.', $this->td) : esc_html__('All configured rules are actively enforced on this site.', $this->td); ?>
                        </div>
                    </div>

                    <div class="bswan-ov-grid">

                        <div class="bswan-ov-card">
                            <div class="bswan-ov-card-icon" style="background:rgba(34,113,177,.1);"><?php echo self::icon('cloud', '#2271b1', 20); ?></div>
                            <div class="bswan-ov-card-body">
                                <div class="bswan-ov-card-title"><?php esc_html_e('HTTP Request Blocking', $this->td); ?></div>
                                <?php if ($bl_count === 0): ?>
                                    <div class="bswan-ov-card-value bswan-ov-dim"><?php esc_html_e('No domains configured', $this->td); ?></div>
                                <?php else: ?>
                                    <div class="bswan-ov-card-value"><?php echo esc_html(sprintf(_n('%d domain blocked', '%d domains blocked', $bl_count, $this->td), $bl_count)); ?></div>
                                <?php endif; ?>
                                <div class="bswan-ov-card-detail"><?php echo $wl_count > 0 ? esc_html(sprintf(_n('%d domain always allowed', '%d domains always allowed', $wl_count, $this->td), $wl_count)) : esc_html__('No whitelist exceptions', $this->td); ?></div>
                            </div>
                        </div>

                        <div class="bswan-ov-card">
                            <div class="bswan-ov-card-icon" style="background:rgba(130,110,180,.1);"><?php echo self::icon('globe', '#826eb4', 20); ?></div>
                            <div class="bswan-ov-card-body">
                                <div class="bswan-ov-card-title"><?php esc_html_e('Script & Style Blocking', $this->td); ?></div>
                                <?php if (!$res_backend && !$res_frontend): ?>
                                    <div class="bswan-ov-card-value bswan-ov-dim"><?php esc_html_e('Off', $this->td); ?></div>
                                    <div class="bswan-ov-card-detail"><?php esc_html_e('Not blocking external scripts or styles', $this->td); ?></div>
                                <?php else: ?>
                                    <div class="bswan-ov-card-value bswan-ov-on"><?php esc_html_e('On', $this->td); ?></div>
                                    <div class="bswan-ov-card-detail"><?php echo esc_html(implode(' · ', array_filter(array($res_backend ? __('Admin panel', $this->td) : '', $res_frontend ? __('Website', $this->td) : '')))); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bswan-ov-card">
                            <div class="bswan-ov-card-icon" style="background:rgba(219,166,23,.1);"><?php echo self::icon('ban', '#dba617', 20); ?></div>
                            <div class="bswan-ov-card-body">
                                <div class="bswan-ov-card-title"><?php esc_html_e('Specific Resource Blocking', $this->td); ?></div>
                                <?php if ($br_count === 0): ?>
                                    <div class="bswan-ov-card-value bswan-ov-dim"><?php esc_html_e('None configured', $this->td); ?></div>
                                    <div class="bswan-ov-card-detail"><?php esc_html_e('No individual JS/CSS rules set up', $this->td); ?></div>
                                <?php else: ?>
                                    <div class="bswan-ov-card-value"><?php echo esc_html(sprintf(_n('%d rule', '%d rules', $br_count, $this->td), $br_count)); ?></div>
                                    <div class="bswan-ov-card-detail"><?php echo esc_html(sprintf(__('Admin: %d · Website: %d', $this->td), $br_be_count, $br_fe_count)); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bswan-ov-card">
                            <div class="bswan-ov-card-icon" style="background:rgba(130,110,180,.1);"><?php echo self::icon('repeat', '#826eb4', 20); ?></div>
                            <div class="bswan-ov-card-body">
                                <div class="bswan-ov-card-title"><?php esc_html_e('CDN Replacements', $this->td); ?></div>
                                <?php if ($cdn_count === 0 || ($cdn_be_count === 0 && $cdn_fe_count === 0)): ?>
                                    <div class="bswan-ov-card-value bswan-ov-dim"><?php echo $cdn_count === 0 ? esc_html__('None configured', $this->td) : esc_html(sprintf(_n('%d rule (none active)', '%d rules (none active)', $cdn_count, $this->td), $cdn_count)); ?></div>
                                    <div class="bswan-ov-card-detail"><?php esc_html_e('Resources load from their original source', $this->td); ?></div>
                                <?php else: ?>
                                    <div class="bswan-ov-card-value bswan-ov-on"><?php echo esc_html(sprintf(_n('%d rule active', '%d rules active', max($cdn_be_count, $cdn_fe_count), $this->td), max($cdn_be_count, $cdn_fe_count))); ?></div>
                                    <div class="bswan-ov-card-detail"><?php echo esc_html(sprintf(__('Admin: %d · Website: %d', $this->td), $cdn_be_count, $cdn_fe_count)); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bswan-ov-card">
                            <div class="bswan-ov-card-icon" style="background:<?php echo $disable_avatars ? 'rgba(0,163,42,.1)' : 'rgba(130,110,180,.08)'; ?>;"><?php echo self::icon('user', $disable_avatars ? '#00a32a' : '#826eb4', 20); ?></div>
                            <div class="bswan-ov-card-body">
                                <div class="bswan-ov-card-title"><?php esc_html_e('User Avatars', $this->td); ?></div>
                                <?php if ($disable_avatars): ?>
                                    <div class="bswan-ov-card-value bswan-ov-on"><?php esc_html_e('Avatars disabled', $this->td); ?></div>
                                    <div class="bswan-ov-card-detail"><?php esc_html_e('No Gravatar requests — faster & more private', $this->td); ?></div>
                                <?php else: ?>
                                    <div class="bswan-ov-card-value bswan-ov-dim"><?php esc_html_e('Avatars shown', $this->td); ?></div>
                                    <div class="bswan-ov-card-detail"><?php esc_html_e('WordPress loads Gravatars normally', $this->td); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="bswan-advanced-row">
                    <button type="button" id="bswan-advanced-toggle" class="button bswan-advanced-btn">
                        <?php echo self::icon('settings', 'currentColor', 15); ?>
                        <span><?php esc_html_e('Configure &amp; Advanced Settings', $this->td); ?></span>
                        <span id="bswan-advanced-arrow" class="bswan-advanced-arrow">&#9660;</span>
                    </button>
                </div>

                <div id="bswan-advanced-wrap">
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">

                                <!-- Section 1 -->
                                <div id="bswan-section-http" class="postbox bswan-glass closed">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><?php echo $i['cloud']; ?> <span><?php _e('Server-side HTTP Requests (PHP)', $this->td); ?></span></h2>
                                        <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                    </div>
                                    <div class="inside">
                                        <p class="description"><?php _e('Background requests made by WordPress, plugins, and themes via PHP (e.g. update checks, license verification, REST API calls). They happen on the server and are invisible to the browser.', $this->td); ?></p>
                                        <div id="bswan-columns">

                                            <div class="bswan-card">
                                                <div class="bswan-card-header">
                                                    <h3><?php echo $i['shield']; ?> <?php _e('Blacklist (Blocked Domains)', $this->td); ?></h3>
                                                    <span class="bswan-count-bl bswan-badge bswan-badge-red">0</span>
                                                </div>
                                                <div class="bswan-card-body">
                                                    <div class="bswan-input-row">
                                                        <input type="text" id="bswan-bl-input" class="regular-text" placeholder="<?php esc_attr_e('e.g. example.com', $this->td); ?>">
                                                        <button type="button" class="button button-primary" onclick="bswanAddItem('bl')"><?php _e('Add', $this->td); ?></button>
                                                    </div>
                                                    <div class="bswan-table-scroll">
                                                        <table class="widefat striped" id="bswan-bl-table">
                                                            <thead>
                                                                <tr>
                                                                    <th><?php _e('Domain / URL Pattern', $this->td); ?></th>
                                                                    <th style="width:65px;text-align:end;"><?php _e('Actions', $this->td); ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                    <div><button type="button" class="button bswan-btn-danger" onclick="bswanDeleteAll('bl')"><?php echo $i['trash']; ?> <?php _e('Delete All', $this->td); ?></button></div>
                                                </div>
                                            </div>

                                            <div class="bswan-card">
                                                <div class="bswan-card-header">
                                                    <h3><?php echo $i['circhk']; ?> <?php _e('Whitelist (Allowed URL Patterns)', $this->td); ?></h3>
                                                    <span class="bswan-count-wl bswan-badge bswan-badge-green">0</span>
                                                </div>
                                                <div class="bswan-card-body">
                                                    <div class="bswan-input-row">
                                                        <input type="text" id="bswan-wl-input" class="regular-text" placeholder="<?php esc_attr_e('e.g. //api.wordpress.org/plugins/', $this->td); ?>">
                                                        <button type="button" class="button button-primary" onclick="bswanAddItem('wl')"><?php _e('Add', $this->td); ?></button>
                                                    </div>
                                                    <div class="bswan-table-scroll">
                                                        <table class="widefat striped" id="bswan-wl-table">
                                                            <thead>
                                                                <tr>
                                                                    <th><?php _e('URL Pattern (whitelisted)', $this->td); ?></th>
                                                                    <th style="width:65px;text-align:end;"><?php _e('Actions', $this->td); ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                    <div><button type="button" class="button bswan-btn-danger" onclick="bswanDeleteAll('wl')"><?php echo $i['trash']; ?> <?php _e('Delete All', $this->td); ?></button></div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2 -->
                                <div id="bswan-section-domain" class="postbox bswan-glass closed">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><?php echo $i['globe']; ?> <span><?php _e('Browser-side Resources by Domain (JS / CSS)', $this->td); ?></span></h2>
                                        <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                    </div>
                                    <div class="inside">
                                        <p class="description"><?php _e('When enabled, enqueued JS/CSS from blacklisted domains will be deregistered. Only external resources are affected. Whitelisted patterns are respected.', $this->td); ?></p>
                                        <fieldset class="bswan-checkbox-row">

                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" class="switch" id="bswan-res-backend" <?php checked($res_backend); ?> />
                                                <label for="bswan-res-backend"><span class="switch-x-text"><?php echo $i['gear']; ?><?php _e('Admin Panel (wp-admin)', $this->td); ?></span><span class="switch-x-toggletext"><span class="switch-x-unchecked">Off</span><span class="switch-x-checked">On</span></span></label>
                                            </div>

                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" class="switch" id="bswan-res-frontend" <?php checked($res_frontend); ?> />
                                                <label for="bswan-res-frontend"><span class="switch-x-text"><?php echo $i['monitor']; ?><?php _e('Public Frontend', $this->td); ?></span><span class="switch-x-toggletext"><span class="switch-x-unchecked">Off</span><span class="switch-x-checked">On</span></span></label>
                                            </div>

                                        </fieldset>
                                    </div>
                                </div>

                                <!-- Section 3 -->
                                <div id="bswan-section-avatars" class="postbox bswan-glass closed">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><?php echo $i['user']; ?> <span><?php _e('Avatars', $this->td); ?></span></h2>
                                        <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                    </div>
                                    <div class="inside">
                                        <p class="description"><?php _e('Control WordPress avatar display. When disabled, all avatars (Gravatar and local) are hidden site-wide — preventing outgoing requests to gravatar.com and removing avatar markup from comments, profiles, and admin areas.', $this->td); ?></p>
                                        <fieldset class="bswan-checkbox-row" style="margin-top:10px;">
                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" class="switch" id="bswan-disable-avatars" <?php checked($disable_avatars); ?> />
                                                <label for="bswan-disable-avatars"><span class="switch-x-text"><?php echo $i['user']; ?><?php _e('Disable All Avatars', $this->td); ?></span><span class="switch-x-toggletext"><span class="switch-x-unchecked">Off</span><span class="switch-x-checked">On</span></span></label>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <!-- Section 4 -->
                                <div id="bswan-section-specific" class="postbox bswan-glass closed">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><?php echo $i['ban']; ?> <span><?php _e('Block Specific Resources (by URL)', $this->td); ?></span></h2>
                                        <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                    </div>
                                    <div class="inside">
                                        <p class="description"><?php _e('Block individual JS or CSS files by matching against their enqueued URL. Full URL, partial path, or even just a filename with extension — anything that appears in the resource URL will match. Works for both local and external resources.', $this->td); ?></p>
                                        <p class="description" style="margin-top:4px;"><?php printf(__('Examples: %s · %s · %s', $this->td), '<code>/persian-woocommerce/assets/fonts/admin-font.css</code>', '<code>admin-font.css</code>', '<code>https://cdn.example.com/lib.js</code>'); ?></p>
                                        <div class="bswan-input-row bg-round" style="margin: 12px 0;gap: 12px;">
                                            <input type="text" id="bswan-br-input" class="regular-text" placeholder="<?php esc_attr_e('Full URL, partial path, or filename.ext', $this->td); ?>">

                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" class="switch" id="bswan-br-backend" checked>
                                                <label for="bswan-br-backend"><span class="switch-x-text"><?php _e('Backend', $this->td); ?></span></label>
                                            </div>

                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" class="switch" id="bswan-br-frontend">
                                                <label for="bswan-br-frontend"><span class="switch-x-text"><?php _e('Frontend', $this->td); ?></span></label>
                                            </div>

                                            <button type="button" style="min-width: 100px;" class="button button-primary" onclick="bswanAddResource()"><?php _e('Add', $this->td); ?></button>
                                        </div>
                                        <div class="bswan-table-scroll">
                                            <table class="widefat striped" id="bswan-br-table">
                                                <thead>
                                                    <tr>
                                                        <th><?php _e('URL Pattern', $this->td); ?></th>
                                                        <th><?php _e('Conditions', $this->td); ?></th>
                                                        <th style="width:65px;text-align:end;"><?php _e('Actions', $this->td); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div style="margin-top:8px;"><button type="button" class="button bswan-btn-danger" onclick="bswanDeleteAllRes()"><?php echo $i['trash']; ?> <?php _e('Delete All', $this->td); ?></button></div>
                                    </div>
                                </div>

                                <!-- Section 5 -->
                                <div id="bswan-section-cdn" class="postbox bswan-glass closed">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><?php echo $i['repeat']; ?> <span><?php _e('CDN Resource Replacements (JS / CSS)', $this->td); ?></span></h2>
                                        <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                    </div>
                                    <div class="inside">
                                        <p class="description"><?php _e('Replace enqueued JS or CSS resources with versions served from a CDN. Any enqueued resource whose URL contains the pattern will have its source swapped for the CDN URL. Useful for serving popular libraries from a local or faster CDN — for example:', $this->td); ?> <i><a href="https://lib.arvancloud.ir/" target="_blank">https://lib.arvancloud.ir/</a></i></p>
                                        <div class="bswan-input-row bg-round" style="margin: 12px 0; gap: 12px; flex-wrap: wrap;">
                                            <input type="text" id="bswan-cdn-pattern" class="regular-text" style="flex:1;min-width:120px;" placeholder="<?php esc_attr_e('Pattern (e.g. jquery.js)', $this->td); ?>">
                                            <input type="url" id="bswan-cdn-url" class="regular-text" style="flex:2;min-width:200px;" placeholder="<?php printf(esc_attr__('CDN URL (e.g. %s)', $this->td), "https://lib.arvancloud.ir/jquery/3.6.3/jquery.js"); ?>">

                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" class="switch" id="bswan-cdn-backend">
                                                <label for="bswan-cdn-backend"><span class="switch-x-text"><?php _e('Backend', $this->td); ?></span></label>
                                            </div>

                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" class="switch" id="bswan-cdn-frontend">
                                                <label for="bswan-cdn-frontend"><span class="switch-x-text"><?php _e('Frontend', $this->td); ?></span></label>
                                            </div>

                                            <button type="button" style="min-width: 100px;" class="button button-primary" onclick="bswanAddCdn()"><?php _e('Add', $this->td); ?></button>
                                        </div>
                                        <div class="bswan-table-scroll">
                                            <table class="widefat striped" id="bswan-cdn-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:22%;"><?php _e('Pattern', $this->td); ?></th>
                                                        <th><?php _e('CDN URL', $this->td); ?></th>
                                                        <th style="width:160px;"><?php _e('Conditions', $this->td); ?></th>
                                                        <th style="width:65px;text-align:end;"><?php _e('Actions', $this->td); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div style="margin-top:8px;"><button type="button" class="button bswan-btn-danger" onclick="bswanDeleteAllCdn()"><?php echo $i['trash']; ?> <?php _e('Delete All', $this->td); ?></button></div>
                                    </div>
                                </div>

                            </div>

                            <!-- ════════════ SIDEBAR ════════════ -->
                            <div id="postbox-container-1" class="postbox-container">

                                <div class="postbox bswan-glass">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><span><?php _e('Actions', $this->td); ?></span></h2>
                                    </div>
                                    <div class="inside">
                                        <button type="button" id="bswan-save-btn" class="button button-primary button-large bswan-full-btn"><?php echo $i['save']; ?> <?php _e('Save All Settings', $this->td); ?></button>
                                        <span id="bswan-save-msg" class="bswan-msg"></span>
                                        <hr style="margin:10px 0;">
                                        <div class="bswan-pause-row">
                                            <button type="button" id="bswan-pause-btn" class="button <?php echo $is_paused ? 'button-primary' : ''; ?> bswan-full-btn">
                                                <span class="bswan-pause-icon"><?php echo $is_paused ? $i['play'] : $i['pause']; ?></span>
                                                <span class="bswan-pause-label"><?php echo $is_paused ? __('Resume', $this->td) : __('Pause', $this->td); ?></span>
                                            </button>
                                        </div>
                                        <div id="bswan-status-badge" class="bswan-status <?php echo $is_paused ? 'bswan-status-paused' : 'bswan-status-active'; ?>">
                                            <span class="bswan-status-icon"><?php echo $is_paused ? $i['cpause'] : $i['activity']; ?></span>
                                            <span class="bswan-status-text"><?php echo $is_paused ? __('Paused — all blocking disabled', $this->td) : __('Active — blocking rules enforced', $this->td); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="postbox bswan-glass closed">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><span><?php _e('Safe Mode', $this->td); ?></span></h2>
                                        <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                    </div>
                                    <div class="inside">
                                        <p class="description" style="margin:0 0 8px;"><?php _e('Temporarily disable <strong>all</strong> blocking for a page load. Resource dequeuing is already skipped on this settings page — safe mode is for other broken admin pages.', $this->td); ?></p>
                                        <a href="<?php echo esc_url($safe_mode_url); ?>" class="button bswan-full-btn"><?php echo $i['shldchk']; ?> <?php _e('Open in Safe Mode', $this->td); ?></a>
                                        <p class="description" style="margin:8px 0 0;opacity:0.7;"><?php _e('Tip: Add <code>&bswan-safe=1</code> to any admin URL.', $this->td); ?></p>
                                    </div>
                                </div>

                                <div class="postbox bswan-glass closed">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><span><?php _e('Tools', $this->td); ?></span></h2>
                                        <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                    </div>
                                    <div class="inside">
                                        <?php if ($qm_active): ?>
                                            <p class="description" style="display:flex;align-items:center;gap:6px;"><?php echo $i['check']; ?> <?php _e('Query Monitor is active.', $this->td); ?></p>
                                        <?php elseif ($qm_installed): ?>
                                            <a href="<?php echo esc_url($qm_url); ?>" class="button bswan-full-btn" style="margin-bottom:6px;"><?php echo $i['play']; ?> <?php _e('Activate Query Monitor', $this->td); ?></a>
                                            <p class="description"><?php _e('Installed but not active. Activate to inspect HTTP requests and enqueued resources.', $this->td); ?></p>
                                        <?php else: ?>
                                            <a href="<?php echo esc_url($qm_url); ?>" class="button bswan-full-btn" style="margin-bottom:6px;"><?php echo $i['search']; ?> <?php _e('Install Query Monitor', $this->td); ?></a>
                                            <p class="description"><?php _e('See every HTTP request and enqueued resource. Highly recommended.', $this->td); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="postbox bswan-glass closed">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><span><?php _e('Export / Import', $this->td); ?></span></h2>
                                        <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                    </div>
                                    <div class="inside">
                                        <p class="description" style="margin:0 0 8px;"><?php _e('All plugin settings as a single JSON file.', $this->td); ?></p>
                                        <button type="button" id="bswan-export-all" class="button bswan-full-btn" style="margin-bottom:6px;"><?php echo $i['download']; ?> <?php _e('Export All Settings', $this->td); ?></button>
                                        <button type="button" id="bswan-import-all-btn" class="button bswan-full-btn"><?php echo $i['upload']; ?> <?php _e('Import Settings', $this->td); ?></button>
                                        <input type="file" id="bswan-import-all" accept=".json" style="display:none;">
                                        <span id="bswan-import-msg" class="bswan-msg"></span>
                                    </div>
                                </div>

                                <div class="postbox bswan-glass closed" style="border-color: rgba(214,54,56,0.6) !important;background:rgba(214,54,56,0.04) !important;">
                                    <div class="postbox-header" style="background:rgba(214,54,56,0.06) !important;">
                                        <h2 class="hndle"><span><?php _e('Reset Settings', $this->td); ?></span></h2>
                                        <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                    </div>
                                    <div class="inside">
                                        <p class="description" style="margin:0 0 10px;"><?php _e('Restore all plugin settings to their original defaults. This will permanently erase your blacklist, whitelist, blocked resources, CDN replacements, and all toggle settings. <strong>This cannot be undone.</strong>', $this->td); ?></p>
                                        <button type="button" id="bswan-reset-btn" class="button bswan-full-btn bswan-btn-danger"><?php echo $i['reset']; ?> <?php _e('Reset to Defaults', $this->td); ?></button>
                                        <span id="bswan-reset-msg" class="bswan-msg"></span>
                                    </div>
                                </div>

                                <div class="postbox bswan-glass bswan-status-paused" style="border-color:rgba(219,166,23,0.6) !important;">
                                    <div class="postbox-header">
                                        <h2 class="hndle"><span><?php _e('Disclaimer', $this->td); ?></span></h2>
                                    </div>
                                    <div class="inside">
                                        <p class="description" style="margin:0;"><?php _e('This plugin blocks outgoing HTTP requests and browser resources. May prevent updates, license checks, fonts, CDN libraries, etc. Use at your own risk. Always maintain backups.', $this->td); ?></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div><!-- /#bswan-advanced-wrap -->
            </div>
            <script>
                jQuery(function($) {
                    postboxes.add_postbox_toggles(pagenow);

                    // ── Advanced settings toggle ──
                    var advOpen = localStorage.getItem('bswan_advanced') === '1';

                    function bswanSetAdvanced(open) {
                        advOpen = open;
                        localStorage.setItem('bswan_advanced', open ? '1' : '0');
                        if (open) {
                            $('#bswan-advanced-wrap').slideDown(220);
                            $('#bswan-advanced-arrow').addClass('bswan-arrow-open');
                        } else {
                            $('#bswan-advanced-wrap').slideUp(180);
                            $('#bswan-advanced-arrow').removeClass('bswan-arrow-open');
                        }
                    }
                    if (advOpen) {
                        $('#bswan-advanced-wrap').show();
                        $('#bswan-advanced-arrow').addClass('bswan-arrow-open');
                    }
                    $('#bswan-advanced-toggle').on('click', function() {
                        bswanSetAdvanced(!advOpen);
                    });

                    var bl = <?php echo $blacklist_json; ?>,
                        wl = <?php echo $whitelist_json; ?>,
                        br = <?php echo $blocked_res_json; ?>,
                        cr = <?php echo $cdn_replace_json; ?>;
                    var nonce = '<?php echo $nonce; ?>',
                        ajaxurl = '<?php echo $ajax_url; ?>',
                        isPaused = <?php echo $is_paused ? 'true' : 'false'; ?>;
                    var svgEdit = '<?php echo addslashes(self::icon('pencil', 'currentColor', 14)); ?>';
                    var svgX = '<?php echo addslashes(self::icon('x', '#d63638', 14)); ?>';
                    var svgPlay = '<?php echo addslashes(self::icon('play', 'currentColor')); ?>';
                    var svgPause = '<?php echo addslashes(self::icon('pause', 'currentColor')); ?>';
                    var svgCPause = '<?php echo addslashes(self::icon('circle-pause', '#dba617', 16)); ?>';
                    var svgActivity = '<?php echo addslashes(self::icon('activity', '#00a32a', 16)); ?>';

                    function renderTable(type) {
                        var list = type === 'bl' ? bl : wl,
                            $tb = $('#bswan-' + type + '-table tbody');
                        $tb.empty();
                        if (!list.length) {
                            $tb.append('<tr><td colspan="2" style="text-align:center;color:#999;"><?php _e('No items.', $this->td); ?></td></tr>');
                        } else {
                            $.each(list, function(idx, v) {
                                $tb.append('<tr data-i="' + idx + '"><td><span class="bswan-val">' + $('<span>').text(v).html() + '</span><input type="text" class="bswan-edit-input regular-text" value="' + $('<span>').text(v).html() + '" style="display:none;"></td><td class="bswan-actions"><button type="button" class="button bswan-row-btn bswan-edit-btn" title="<?php esc_attr_e('Edit', $this->td); ?>">' + svgEdit + '</button> <button type="button" class="button bswan-row-btn bswan-del-btn" title="<?php esc_attr_e('Delete', $this->td); ?>" style="color:#d63638;border-color:rgba(214,54,56,0.4);">' + svgX + '</button></td></tr>');
                            });
                        }
                        $('.bswan-count-' + type).text(list.length);
                    }
                    $(document).on('click', '.bswan-edit-btn', function() {
                        var $tr = $(this).closest('tr'),
                            $v = $tr.find('.bswan-val'),
                            $i = $tr.find('.bswan-edit-input');
                        if ($i.is(':visible')) {
                            var t = $tr.closest('table').attr('id').indexOf('-bl-') > -1 ? 'bl' : 'wl',
                                l = t === 'bl' ? bl : wl,
                                nv = $.trim($i.val());
                            if (nv) l[$tr.data('i')] = nv;
                            renderTable(t);
                        } else {
                            $v.hide();
                            $i.show().focus().select();
                        }
                    });
                    $(document).on('keydown', '.bswan-edit-input', function(e) {
                        if ($(this).closest('#bswan-br-table').length) return;
                        if (e.key === 'Enter') $(this).closest('tr').find('.bswan-edit-btn').click();
                        if (e.key === 'Escape') {
                            var t = $(this).closest('table').attr('id').indexOf('-bl-') > -1 ? 'bl' : 'wl';
                            renderTable(t);
                        }
                    });
                    $(document).on('click', '.bswan-del-btn', function() {
                        var $tr = $(this).closest('tr'),
                            t = $tr.closest('table').attr('id').indexOf('-bl-') > -1 ? 'bl' : 'wl';
                        (t === 'bl' ? bl : wl).splice($tr.data('i'), 1);
                        renderTable(t);
                    });
                    window.bswanAddItem = function(t) {
                        var $i = $('#bswan-' + t + '-input'),
                            v = $.trim($i.val());
                        if (!v) return;
                        var l = t === 'bl' ? bl : wl;
                        if (l.indexOf(v) === -1) l.push(v);
                        $i.val('');
                        renderTable(t);
                    };
                    $('#bswan-bl-input,#bswan-wl-input').on('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            bswanAddItem($(this).attr('id').indexOf('-bl-') > -1 ? 'bl' : 'wl');
                        }
                    });
                    window.bswanDeleteAll = function(t) {
                        if (!confirm('<?php _e('Are you sure you want to delete all items?', $this->td); ?>')) return;
                        if (t === 'bl') bl = [];
                        else wl = [];
                        renderTable(t);
                    };

                    $('#bswan-export-all').on('click', function() {
                        var s = {
                            blacklist: bl,
                            whitelist: wl,
                            block_resources_backend: $('#bswan-res-backend').is(':checked'),
                            block_resources_frontend: $('#bswan-res-frontend').is(':checked'),
                            blocked_resources: br,
                            disable_avatars: $('#bswan-disable-avatars').is(':checked'),
                            cdn_replacements: cr
                        };
                        var a = document.createElement('a');
                        a.href = URL.createObjectURL(new Blob([JSON.stringify(s, null, 2)], {
                            type: 'application/json'
                        }));
                        a.download = 'bswan-settings-export.json';
                        a.click();
                    });
                    $('#bswan-import-all-btn').on('click', function() {
                        $('#bswan-import-all').click();
                    });
                    $('#bswan-import-all').on('change', function() {
                        var f = this.files[0],
                            $m = $('#bswan-import-msg');
                        if (!f) return;
                        var r = new FileReader();
                        r.onload = function(e) {
                            try {
                                var d = JSON.parse(e.target.result);
                                if (typeof d !== 'object' || Array.isArray(d)) {
                                    $m.text('<?php _e('Invalid JSON.', $this->td); ?>').css('color', '#d63638').show();
                                    return;
                                }
                                if (Array.isArray(d.blacklist)) bl = d.blacklist;
                                if (Array.isArray(d.whitelist)) wl = d.whitelist;
                                if (Array.isArray(d.blocked_resources)) br = d.blocked_resources;
                                if (typeof d.block_resources_backend !== 'undefined') $('#bswan-res-backend').prop('checked', !!d.block_resources_backend);
                                if (typeof d.block_resources_frontend !== 'undefined') $('#bswan-res-frontend').prop('checked', !!d.block_resources_frontend);
                                if (typeof d.disable_avatars !== 'undefined') $('#bswan-disable-avatars').prop('checked', !!d.disable_avatars);
                                if (Array.isArray(d.cdn_replacements)) cr = d.cdn_replacements;
                                renderTable('bl');
                                renderTable('wl');
                                renderResTable();
                                renderCdnTable();
                                $m.text('<?php _e('Imported. Save to apply.', $this->td); ?>').css('color', '#2271b1').show();
                            } catch (err) {
                                $m.text('<?php _e('Failed to parse JSON.', $this->td); ?>').css('color', '#d63638').show();
                            }
                        };
                        r.readAsText(f);
                        $(this).val('');
                    });

                    function renderResTable() {
                        var $tb = $('#bswan-br-table tbody');
                        $tb.empty();
                        if (!br.length) {
                            $tb.append('<tr><td colspan="2" style="text-align:center;color:#999;"><?php _e('No items.', $this->td); ?></td></tr>');
                        } else {
                            $.each(br, function(idx, it) {
                                $tb.append(
                                    `<tr data-i="${idx}">
                                    <td>
                                        <span class="bswan-val">` + $('<span>').text(it.url).html() + `</span>
                                        <input type="text" class="bswan-edit-input regular-text" value="` + $('<span>').text(it.url).html() + `" style="display:none;width:calc(100% - 10px);">
                                    </td>
                                    <td class="bswan-conditions">
                                        <div class="checkbox-wrapper-flex">
                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" id="bswan-br-cb-be-${idx}" class="switch bswan-br-cb-be" ` + (it.backend ? 'checked' : '') + ` title="<?php esc_attr_e('Backend', $this->td); ?>">
                                                <label for="bswan-br-cb-be-${idx}"><span class="switch-x-text"><?php _e('Backend', $this->td); ?></span></label>
                                            </div>
                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" id="bswan-br-cb-fe-${idx}" class="switch bswan-br-cb-fe" ` + (it.frontend ? 'checked' : '') + ` title="<?php esc_attr_e('Frontend', $this->td); ?>">
                                                <label for="bswan-br-cb-fe-${idx}"><span class="switch-x-text"><?php _e('Frontend', $this->td); ?></span></label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="bswan-actions">
                                        <button type="button" class="button bswan-row-btn bswan-br-edit-btn" title="<?php esc_attr_e('Edit', $this->td); ?>">${svgEdit}</button>
                                        <button type="button" class="button bswan-row-btn bswan-br-del-btn" title="<?php esc_attr_e('Delete', $this->td); ?>" style="color:#d63638;border-color:rgba(214,54,56,0.4);">${svgX}</button>
                                    </td>
                                </tr>`);
                            });
                        }
                    }
                    $(document).on('change', '.bswan-br-cb-be,.bswan-br-cb-fe', function() {
                        var $tr = $(this).closest('tr'),
                            idx = $tr.data('i');
                        br[idx].backend = $tr.find('.bswan-br-cb-be').is(':checked');
                        br[idx].frontend = $tr.find('.bswan-br-cb-fe').is(':checked');
                    });
                    $(document).on('click', '.bswan-br-edit-btn', function() {
                        var $tr = $(this).closest('tr'),
                            $v = $tr.find('.bswan-val'),
                            $i = $tr.find('.bswan-edit-input');
                        if ($i.is(':visible')) {
                            var nv = $.trim($i.val()),
                                idx = $tr.data('i');
                            if (nv) br[idx].url = nv;
                            renderResTable();
                        } else {
                            $v.hide();
                            $i.show().focus().select();
                        }
                    });
                    $(document).on('keydown', '#bswan-br-table .bswan-edit-input', function(e) {
                        if (e.key === 'Enter') $(this).closest('tr').find('.bswan-br-edit-btn').click();
                        if (e.key === 'Escape') renderResTable();
                    });
                    $(document).on('click', '.bswan-br-del-btn', function() {
                        br.splice($(this).closest('tr').data('i'), 1);
                        renderResTable();
                    });
                    window.bswanAddResource = function() {
                        var v = $.trim($('#bswan-br-input').val());
                        if (!v) return;
                        br.push({
                            url: v,
                            backend: $('#bswan-br-backend').is(':checked'),
                            frontend: $('#bswan-br-frontend').is(':checked')
                        });
                        $('#bswan-br-input').val('');
                        renderResTable();
                    };
                    $('#bswan-br-input').on('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            bswanAddResource();
                        }
                    });
                    window.bswanDeleteAllRes = function() {
                        if (!confirm('<?php _e('Are you sure?', $this->td); ?>')) return;
                        br = [];
                        renderResTable();
                    };

                    $('#bswan-save-btn').on('click', function() {
                        var $b = $(this).prop('disabled', true),
                            $m = $('#bswan-save-msg'),
                            bs = [];
                        for (var j = 0; j < br.length; j++) bs.push({
                            url: br[j].url,
                            backend: br[j].backend ? 1 : 0,
                            frontend: br[j].frontend ? 1 : 0
                        });
                        var cs = [];
                        for (var k = 0; k < cr.length; k++) cs.push({
                            pattern: cr[k].pattern,
                            cdn: cr[k].cdn,
                            backend: cr[k].backend ? 1 : 0,
                            frontend: cr[k].frontend ? 1 : 0
                        });
                        $.post(ajaxurl, {
                            action: 'bswan_ber_save',
                            _nonce: nonce,
                            blacklist: bl,
                            whitelist: wl,
                            block_resources_backend: $('#bswan-res-backend').is(':checked') ? 1 : 0,
                            block_resources_frontend: $('#bswan-res-frontend').is(':checked') ? 1 : 0,
                            blocked_resources: bs,
                            disable_avatars: $('#bswan-disable-avatars').is(':checked') ? 1 : 0,
                            cdn_replacements: cs
                        }, function(r) {
                            $b.prop('disabled', false);
                            $m.text(r.success ? r.data : '<?php _e('Error.', $this->td); ?>').css('color', r.success ? '#00a32a' : '#d63638').fadeIn().delay(3000).fadeOut();
                        }).fail(function() {
                            $b.prop('disabled', false);
                            $m.text('<?php _e('Request failed.', $this->td); ?>').css('color', '#d63638').fadeIn().delay(3000).fadeOut();
                        });
                    });

                    $('#bswan-pause-btn').on('click', function() {
                        var $b = $(this).prop('disabled', true);
                        $.post(ajaxurl, {
                            action: 'bswan_ber_toggle_pause',
                            _nonce: nonce
                        }, function(r) {
                            $b.prop('disabled', false);
                            if (r.success) {
                                isPaused = r.data.paused;
                                var $ic = $b.find('.bswan-pause-icon'),
                                    $lb = $b.find('.bswan-pause-label'),
                                    $bd = $('#bswan-status-badge');
                                if (isPaused) {
                                    $b.addClass('button-primary');
                                    $ic.html(svgPlay);
                                    $lb.text('<?php _e('Resume', $this->td); ?>');
                                    $bd.removeClass('bswan-status-active').addClass('bswan-status-paused');
                                    $bd.find('.bswan-status-icon').html(svgCPause);
                                    $bd.find('.bswan-status-text').text('<?php _e('Paused — all blocking disabled', $this->td); ?>');
                                } else {
                                    $b.removeClass('button-primary');
                                    $ic.html(svgPause);
                                    $lb.text('<?php _e('Pause', $this->td); ?>');
                                    $bd.removeClass('bswan-status-paused').addClass('bswan-status-active');
                                    $bd.find('.bswan-status-icon').html(svgActivity);
                                    $bd.find('.bswan-status-text').text('<?php _e('Active — blocking rules enforced', $this->td); ?>');
                                }
                                $('#bswan-save-msg').text(r.data.message).css('color', '#2271b1').fadeIn().delay(3000).fadeOut();
                            }
                        }).fail(function() {
                            $b.prop('disabled', false);
                        });
                    });

                    $('#bswan-reset-btn').on('click', function() {
                        if (!confirm('<?php echo esc_js(__("Reset ALL settings to plugin defaults?\n\nThis will permanently erase your blacklist, whitelist, blocked resources, CDN replacements, and all toggle settings.\n\nThis action cannot be undone.", $this->td)); ?>')) return;
                        var $b = $(this).prop('disabled', true),
                            $m = $('#bswan-reset-msg');
                        $.post(ajaxurl, {
                            action: 'bswan_ber_reset',
                            _nonce: nonce
                        }, function(r) {
                            if (r.success) {
                                $m.text(r.data).css('color', '#00a32a').fadeIn();
                                setTimeout(function() {
                                    location.reload();
                                }, 1200);
                            } else {
                                $b.prop('disabled', false);
                                $m.text(r.data || '<?php echo esc_js(__('Error.', $this->td)); ?>').css('color', '#d63638').fadeIn().delay(3000).fadeOut();
                            }
                        }).fail(function() {
                            $b.prop('disabled', false);
                            $m.text('<?php echo esc_js(__('Request failed.', $this->td)); ?>').css('color', '#d63638').fadeIn().delay(3000).fadeOut();
                        });
                    });

                    function renderCdnTable() {
                        var $tb = $('#bswan-cdn-table tbody');
                        $tb.empty();
                        if (!cr.length) {
                            $tb.append('<tr><td colspan="4" style="text-align:center;color:#999;"><?php _e('No items.', $this->td); ?></td></tr>');
                        } else {
                            $.each(cr, function(idx, it) {
                                $tb.append(
                                    `<tr data-i="${idx}">
                                    <td>
                                        <span class="bswan-val">` + $('<span>').text(it.pattern).html() + `</span>
                                        <input type="text" class="bswan-edit-input regular-text bswan-cdn-edit-pattern" value="` + $('<span>').text(it.pattern).html() + `" style="display:none;width:calc(100% - 10px);">
                                    </td>
                                    <td>
                                        <span class="bswan-val bswan-cdn-val-url" style="word-break:break-all;font-size:12px;">` + $('<span>').text(it.cdn).html() + `</span>
                                        <input type="url" class="bswan-edit-input regular-text bswan-cdn-edit-url" value="` + $('<span>').text(it.cdn).html() + `" style="display:none;width:calc(100% - 10px);">
                                    </td>
                                    <td class="bswan-conditions">
                                        <div class="checkbox-wrapper-flex">
                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" id="bswan-cdn-cb-be-${idx}" class="switch bswan-cdn-cb-be" ` + (it.backend ? 'checked' : '') + ` title="<?php esc_attr_e('Backend', $this->td); ?>">
                                                <label for="bswan-cdn-cb-be-${idx}"><span class="switch-x-text"><?php _e('Backend', $this->td); ?></span></label>
                                            </div>
                                            <div class="checkbox-wrapper">
                                                <input type="checkbox" id="bswan-cdn-cb-fe-${idx}" class="switch bswan-cdn-cb-fe" ` + (it.frontend ? 'checked' : '') + ` title="<?php esc_attr_e('Frontend', $this->td); ?>">
                                                <label for="bswan-cdn-cb-fe-${idx}"><span class="switch-x-text"><?php _e('Frontend', $this->td); ?></span></label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="bswan-actions">
                                        <button type="button" class="button bswan-row-btn bswan-cdn-edit-btn" title="<?php esc_attr_e('Edit', $this->td); ?>">${svgEdit}</button>
                                        <button type="button" class="button bswan-row-btn bswan-cdn-del-btn" title="<?php esc_attr_e('Delete', $this->td); ?>" style="color:#d63638;border-color:rgba(214,54,56,0.4);">${svgX}</button>
                                    </td>
                                </tr>`);
                            });
                        }
                    }
                    $(document).on('change', '.bswan-cdn-cb-be,.bswan-cdn-cb-fe', function() {
                        var $tr = $(this).closest('tr'),
                            idx = $tr.data('i');
                        cr[idx].backend = $tr.find('.bswan-cdn-cb-be').is(':checked');
                        cr[idx].frontend = $tr.find('.bswan-cdn-cb-fe').is(':checked');
                    });
                    $(document).on('click', '.bswan-cdn-edit-btn', function() {
                        var $tr = $(this).closest('tr'),
                            $vp = $tr.find('.bswan-val').first(),
                            $vu = $tr.find('.bswan-cdn-val-url'),
                            $ip = $tr.find('.bswan-cdn-edit-pattern'),
                            $iu = $tr.find('.bswan-cdn-edit-url');
                        if ($ip.is(':visible')) {
                            var idx = $tr.data('i'),
                                np = $.trim($ip.val()),
                                nu = $.trim($iu.val());
                            if (np) cr[idx].pattern = np;
                            if (nu) cr[idx].cdn = nu;
                            renderCdnTable();
                        } else {
                            $vp.hide();
                            $vu.hide();
                            $ip.show().focus().select();
                            $iu.show();
                        }
                    });
                    $(document).on('keydown', '#bswan-cdn-table .bswan-edit-input', function(e) {
                        if (e.key === 'Enter') $(this).closest('tr').find('.bswan-cdn-edit-btn').click();
                        if (e.key === 'Escape') renderCdnTable();
                    });
                    $(document).on('click', '.bswan-cdn-del-btn', function() {
                        cr.splice($(this).closest('tr').data('i'), 1);
                        renderCdnTable();
                    });
                    window.bswanAddCdn = function() {
                        var p = $.trim($('#bswan-cdn-pattern').val()),
                            u = $.trim($('#bswan-cdn-url').val());
                        if (!p || !u) return;
                        cr.push({
                            pattern: p,
                            cdn: u,
                            backend: $('#bswan-cdn-backend').is(':checked'),
                            frontend: $('#bswan-cdn-frontend').is(':checked')
                        });
                        $('#bswan-cdn-pattern').val('');
                        $('#bswan-cdn-url').val('');
                        renderCdnTable();
                    };
                    $('#bswan-cdn-pattern,#bswan-cdn-url').on('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            bswanAddCdn();
                        }
                    });
                    window.bswanDeleteAllCdn = function() {
                        if (!confirm('<?php _e('Are you sure?', $this->td); ?>')) return;
                        cr = [];
                        renderCdnTable();
                    };

                    renderTable('bl');
                    renderTable('wl');
                    renderResTable();
                    renderCdnTable();
                });
            </script>
            <?php
        }
    }
    add_action("plugins_loaded", function () {
        return (new blockExternalRequest);
    });
}
/*##################################################
Lead Developer: [amirhp-com](https://amirhp.com/)
##################################################*/