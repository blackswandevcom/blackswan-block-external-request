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
 Version: 2.4.0
 Stable tag: 2.4.0
 Requires PHP: 5.4
 Tested up to: 6.8
 Requires at least: 5.0
 Text Domain: blackswan-block-external-request
 Domain Path: /languages
 Copyright: (c) amirhp.com, All rights reserved.
 License: GPLv2 or later
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * @Last modified by:   amirhp-com <its@amirhp.com>
 * @Last modified time: 2026/03/07 15:44:01
*/

namespace BlackSwan;

defined("ABSPATH") or die("<h2>Unauthorized Access!</h2><hr><small>BlackSwan | Block External Request Plugin :: Developed by Amirhp-com (<a href='https://amirhp.com/'>https://amirhp.com/</a>)</small>");

if (!class_exists("\BlackSwan\blockExternalRequest")) {
    class blockExternalRequest {
        public $td;
        public $version;
        public $title;
        protected $block_url_list;
        protected $whitelist_urls;
        private $option_key = 'bswan_ber_settings';
        private $settings = null;

        private static $default_blacklist = array(
            "w.org", "w.com", "wp.org", "wp.com", "wincher.com",
            "yoa.st", "yoast.com", "wordpress.org", "wordpress.com",
            "woocommerce.com", "reduxframework.com", "wp-rocket.me",
            "easydigitaldownloads.com", "github.com", "google.com",
        );

        private static $default_whitelist = array(
            "//api.wordpress.org/plugins/",
            "//downloads.wordpress.org/",
        );

        // ── Inline Lucide SVG icons (18x18, stroke-width 2) ──
        private static function icon($name, $color = 'currentColor', $size = 18) {
            $s = $size;
            $icons = array(
                // cloud
                'cloud'          => '<path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>',
                // shield
                'shield'         => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>',
                // shield-check
                'shield-check'   => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
                // circle-check (whitelist)
                'circle-check'   => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
                // globe
                'globe'          => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>',
                // settings (gear)
                'settings'       => '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
                // monitor (frontend)
                'monitor'        => '<rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/>',
                // ban (block specific)
                'ban'            => '<circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/>',
                // trash
                'trash'          => '<path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/>',
                // pencil (edit)
                'pencil'         => '<path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>',
                // x (delete/close)
                'x'              => '<path d="M18 6 6 18"/><path d="m6 6 12 12"/>',
                // save
                'save'           => '<path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/>',
                // pause
                'pause'          => '<rect x="14" y="4" width="4" height="16" rx="1"/><rect x="6" y="4" width="4" height="16" rx="1"/>',
                // play
                'play'           => '<polygon points="6 3 20 12 6 21 6 3"/>',
                // search
                'search'         => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
                // check
                'check'          => '<path d="M20 6 9 17l-5-5"/>',
                // download
                'download'       => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>',
                // upload
                'upload'         => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/>',
            );
            if (!isset($icons[$name])) return '';
            return '<svg xmlns="http://www.w3.org/2000/svg" width="'.$s.'" height="'.$s.'" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;flex-shrink:0;">'.$icons[$name].'</svg>';
        }

        public function __construct() {
            $this->td      = "blackswan-block-external-request";
            $this->version = "2.4.0";
            load_plugin_textdomain($this->td, false, dirname(plugin_basename(__FILE__)) . "/languages/");
            $this->title = __("Block External Request", $this->td);

            $this->load_settings();

            $this->block_url_list = apply_filters("BlackSwan\block_external_request\block_url_list", $this->settings['blacklist']);
            $this->whitelist_urls = apply_filters("BlackSwan\block_external_request\whitelist_urls", $this->settings['whitelist']);

            $is_safe   = $this->is_safe_mode();
            $is_paused = !empty($this->settings['paused']);
            $is_own_page = (is_admin() && isset($_GET['page']) && $_GET['page'] === 'bswan-ber-settings');

            if (!$is_safe && !$is_paused) {
                add_filter("pre_http_request", array($this, "block_external_request"), 10, 3);

                if (!$is_own_page) {
                    if (!empty($this->settings['block_resources_backend'])) {
                        add_action("admin_enqueue_scripts", array($this, "dequeue_blocked_resources"), 9999);
                    }
                    if (!empty($this->settings['block_resources_frontend'])) {
                        add_action("wp_enqueue_scripts", array($this, "dequeue_blocked_resources"), 9999);
                    }

                    $block_res = !empty($this->settings['blocked_resources']) ? $this->settings['blocked_resources'] : array();
                    if (!empty($block_res)) {
                        $has_backend = $has_frontend = false;
                        foreach ($block_res as $r) {
                            if (!empty($r['backend']))  $has_backend  = true;
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
                add_filter("plugin_action_links_" . plugin_basename(__FILE__), array($this, "plugin_action_links"));
            }
        }

        private function is_safe_mode() {
            if (!is_admin()) return false;
            return (isset($_GET['bswan-safe']) && $_GET['bswan-safe'] === '1');
        }

        private function load_settings() {
            $defaults = array(
                'blacklist'                => self::$default_blacklist,
                'whitelist'                => self::$default_whitelist,
                'paused'                   => false,
                'block_resources_backend'  => false,
                'block_resources_frontend' => false,
                'blocked_resources'        => array(),
            );
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
            add_options_page(
                $this->title . ' — ' . __('Settings', $this->td),
                $this->title,
                'manage_options',
                'bswan-ber-settings',
                array($this, 'render_settings_page')
            );
        }

        public function ajax_save_settings() {
            check_ajax_referer('bswan_ber_nonce', '_nonce');
            if (!current_user_can('manage_options')) wp_send_json_error(__('Permission denied.', $this->td));

            $blacklist = isset($_POST['blacklist']) ? $_POST['blacklist'] : array();
            $whitelist = isset($_POST['whitelist']) ? $_POST['whitelist'] : array();
            $this->settings['blacklist'] = array_values(array_unique(array_filter(array_map('sanitize_text_field', (array) $blacklist))));
            $this->settings['whitelist'] = array_values(array_unique(array_filter(array_map('sanitize_text_field', (array) $whitelist))));
            $this->settings['block_resources_backend']  = !empty($_POST['block_resources_backend']);
            $this->settings['block_resources_frontend'] = !empty($_POST['block_resources_frontend']);

            $blocked_resources = array();
            if (!empty($_POST['blocked_resources']) && is_array($_POST['blocked_resources'])) {
                foreach ($_POST['blocked_resources'] as $item) {
                    $url = sanitize_text_field(isset($item['url']) ? $item['url'] : '');
                    if (empty($url)) continue;
                    $blocked_resources[] = array(
                        'url'      => $url,
                        'backend'  => !empty($item['backend']),
                        'frontend' => !empty($item['frontend']),
                    );
                }
            }
            $this->settings['blocked_resources'] = $blocked_resources;

            $this->save_settings();
            wp_send_json_success(__('Settings saved successfully.', $this->td));
        }

        public function ajax_toggle_pause() {
            check_ajax_referer('bswan_ber_nonce', '_nonce');
            if (!current_user_can('manage_options')) wp_send_json_error(__('Permission denied.', $this->td));
            $this->settings['paused'] = !$this->settings['paused'];
            $this->save_settings();
            wp_send_json_success(array(
                'paused'  => $this->settings['paused'],
                'message' => $this->settings['paused']
                    ? __('Plugin is now paused. All blocking is disabled.', $this->td)
                    : __('Plugin is now active. Blocking rules are enforced.', $this->td),
            ));
        }

        public function block_external_request($preempt, $parsed_args, $url) {
            foreach ($this->block_url_list as $block_url) {
                if (strpos($url, $block_url) !== false) {
                    foreach ($this->whitelist_urls as $unblock_url) {
                        if (strpos($url, $unblock_url) !== false) return $preempt;
                    }
                    return new \WP_Error('http_request_block', __("This request is not allowed", $this->td) . "\n:: {$url}", $url);
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
            $registry = ($type === 'scripts') ? $wp_scripts : $wp_styles;
            if (empty($registry) || empty($registry->registered)) return;
            $site_host = parse_url(home_url(), PHP_URL_HOST);
            foreach ($registry->registered as $handle => $dep) {
                if (empty($dep->src)) continue;
                $src = $dep->src;
                if (strpos($src, '//') === false) continue;
                $parsed = parse_url($src);
                if (empty($parsed['host']) || $parsed['host'] === $site_host) continue;
                $is_blocked = false;
                foreach ($this->block_url_list as $block_domain) {
                    if (strpos($parsed['host'], $block_domain) !== false) {
                        $is_whitelisted = false;
                        foreach ($this->whitelist_urls as $ap) {
                            if (strpos($src, $ap) !== false) { $is_whitelisted = true; break; }
                        }
                        if (!$is_whitelisted) { $is_blocked = true; break; }
                    }
                }
                if ($is_blocked) {
                    if ($type === 'scripts') wp_deregister_script($handle);
                    else wp_deregister_style($handle);
                }
            }
        }

        public function dequeue_specific_resources() {
            $is_admin = is_admin();
            $blocked  = !empty($this->settings['blocked_resources']) ? $this->settings['blocked_resources'] : array();
            if (empty($blocked)) return;
            $active = array();
            foreach ($blocked as $item) {
                if ($is_admin && !empty($item['backend']))  $active[] = $item['url'];
                if (!$is_admin && !empty($item['frontend'])) $active[] = $item['url'];
            }
            if (empty($active)) return;
            $this->dequeue_specific_by_type('scripts', $active);
            $this->dequeue_specific_by_type('styles', $active);
        }

        private function dequeue_specific_by_type($type, $patterns) {
            global $wp_scripts, $wp_styles;
            $registry = ($type === 'scripts') ? $wp_scripts : $wp_styles;
            if (empty($registry) || empty($registry->registered)) return;
            foreach ($registry->registered as $handle => $dep) {
                if (empty($dep->src)) continue;
                foreach ($patterns as $pattern) {
                    if (strpos($dep->src, $pattern) !== false) {
                        if ($type === 'scripts') wp_deregister_script($handle);
                        else wp_deregister_style($handle);
                        break;
                    }
                }
            }
        }

        // ─────────────────────────────────────────────
        // SETTINGS PAGE
        // ─────────────────────────────────────────────
        public function render_settings_page() {
            wp_enqueue_script('postbox');

            $is_paused        = !empty($this->settings['paused']);
            $is_safe          = $this->is_safe_mode();
            $res_backend      = !empty($this->settings['block_resources_backend']);
            $res_frontend     = !empty($this->settings['block_resources_frontend']);
            $blacklist_json   = wp_json_encode(array_values($this->settings['blacklist']));
            $whitelist_json   = wp_json_encode(array_values($this->settings['whitelist']));
            $blocked_res_json = wp_json_encode(array_values(!empty($this->settings['blocked_resources']) ? $this->settings['blocked_resources'] : array()));
            $nonce            = wp_create_nonce('bswan_ber_nonce');
            $ajax_url         = admin_url('admin-ajax.php');
            $qm_active        = is_plugin_active('query-monitor/query-monitor.php');
            $qm_installed     = file_exists(WP_PLUGIN_DIR . '/query-monitor/query-monitor.php');
            $qm_url           = $qm_installed
                ? wp_nonce_url(admin_url('plugins.php?action=activate&plugin=query-monitor/query-monitor.php'), 'activate-plugin_query-monitor/query-monitor.php')
                : admin_url('plugin-install.php?s=query+monitor&tab=search&type=term');
            $safe_mode_url    = admin_url('options-general.php?page=bswan-ber-settings&bswan-safe=1');

            // Icon shortcuts
            $i_cloud     = self::icon('cloud', '#2271b1');
            $i_shield    = self::icon('shield', '#d63638');
            $i_circlechk = self::icon('circle-check', '#00a32a');
            $i_globe     = self::icon('globe', '#826eb4');
            $i_gear      = self::icon('settings', '#826eb4');
            $i_monitor   = self::icon('monitor', '#826eb4');
            $i_ban       = self::icon('ban', '#dba617');
            $i_trash     = self::icon('trash', '#d63638', 16);
            $i_pencil    = self::icon('pencil', 'currentColor', 14);
            $i_x         = self::icon('x', '#d63638', 14);
            $i_save      = self::icon('save', '#fff');
            $i_pause     = self::icon('pause', 'currentColor');
            $i_play      = self::icon('play', 'currentColor');
            $i_search    = self::icon('search', 'currentColor');
            $i_check     = self::icon('check', '#00a32a');
            $i_download  = self::icon('download', 'currentColor');
            $i_upload    = self::icon('upload', 'currentColor');
            $i_shieldchk = self::icon('shield-check', '#2271b1');

            add_filter("admin_footer_text", function() {
                return sprintf(
                    __('Developed by %s — Another Free & Open-source project by %s', $this->td),
                    '<a href="https://amirhp.com/" target="_blank">AmirhpCom</a>',
                    '<a href="https://blackswandev.com/" target="_blank">BlackSwan</a>'
                );
            }, 9999);
            ?>
            <div class="wrap">
                <h1><?php echo esc_html($this->title); ?> <sup style="background:#2271b1;color:#fff;padding:4px 8px;font-size:small;border-radius:20px;font-family:initial;line-height:1;">v<?php echo esc_html($this->version); ?></sup></h1>

                <?php if ($is_safe): ?>
                <div class="notice notice-info"><p><?php echo $i_shieldchk; ?> <strong><?php _e('Safe Mode is active.', $this->td); ?></strong> <?php _e('All blocking rules are temporarily bypassed on this page load.', $this->td); ?></p></div>
                <?php endif; ?>

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">

                        <!-- ════════════ MAIN CONTENT ════════════ -->
                        <div id="post-body-content">

                            <!-- Section 1 -->
                            <div id="bswan-section-http" class="postbox">
                                <div class="postbox-header">
                                    <h2 class="hndle"><?php echo $i_cloud; ?> <span><?php _e('Server-side HTTP Requests (PHP)', $this->td); ?></span></h2>
                                    <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                </div>
                                <div class="inside">
                                    <p class="description"><?php _e('Background requests made by WordPress, plugins, and themes via PHP (e.g. update checks, license verification, REST API calls, analytics pings). They happen on the server and are invisible to the browser. Blocking these speeds up admin page loads but may disable updates and remote features.', $this->td); ?></p>
                                    <div id="bswan-columns" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:12px;">

                                        <!-- Blacklist -->
                                        <div class="card" style="max-width:none;padding:0;margin:0;">
                                            <div style="padding:10px 14px;border-bottom:1px solid #c3c4c7;display:flex;align-items:center;justify-content:space-between;">
                                                <h3 style="margin:0;display:flex;align-items:center;gap:6px;"><?php echo $i_shield; ?> <?php _e('Blacklist (Blocked Domains)', $this->td); ?></h3>
                                                <span class="bswan-count-bl" style="background:#d63638;color:#fff;padding:2px 8px;border-radius:10px;font-size:12px;">0</span>
                                            </div>
                                            <div style="padding:10px 14px;">
                                                <div style="display:flex;gap:6px;margin-bottom:8px;">
                                                    <input type="text" id="bswan-bl-input" class="regular-text" placeholder="<?php esc_attr_e('e.g. example.com', $this->td); ?>" style="flex:1;">
                                                    <button type="button" class="button button-primary" onclick="bswanAddItem('bl')"><?php _e('Add', $this->td); ?></button>
                                                </div>
                                                <div class="bswan-table-scroll"><table class="widefat striped" id="bswan-bl-table"><thead><tr><th><?php _e('Domain / URL Pattern', $this->td); ?></th><th style="width:90px;text-align:right;"><?php _e('Actions', $this->td); ?></th></tr></thead><tbody></tbody></table></div>
                                                <div style="margin-top:8px;"><button type="button" class="button bswan-btn-danger" onclick="bswanDeleteAll('bl')"><?php echo $i_trash; ?> <?php _e('Delete All', $this->td); ?></button></div>
                                            </div>
                                        </div>

                                        <!-- Whitelist -->
                                        <div class="card" style="max-width:none;padding:0;margin:0;">
                                            <div style="padding:10px 14px;border-bottom:1px solid #c3c4c7;display:flex;align-items:center;justify-content:space-between;">
                                                <h3 style="margin:0;display:flex;align-items:center;gap:6px;"><?php echo $i_circlechk; ?> <?php _e('Whitelist (Allowed URL Patterns)', $this->td); ?></h3>
                                                <span class="bswan-count-wl" style="background:#00a32a;color:#fff;padding:2px 8px;border-radius:10px;font-size:12px;">0</span>
                                            </div>
                                            <div style="padding:10px 14px;">
                                                <div style="display:flex;gap:6px;margin-bottom:8px;">
                                                    <input type="text" id="bswan-wl-input" class="regular-text" placeholder="<?php esc_attr_e('e.g. //api.wordpress.org/plugins/', $this->td); ?>" style="flex:1;">
                                                    <button type="button" class="button button-primary" onclick="bswanAddItem('wl')"><?php _e('Add', $this->td); ?></button>
                                                </div>
                                                <div class="bswan-table-scroll"><table class="widefat striped" id="bswan-wl-table"><thead><tr><th><?php _e('URL Pattern (whitelisted)', $this->td); ?></th><th style="width:90px;text-align:right;"><?php _e('Actions', $this->td); ?></th></tr></thead><tbody></tbody></table></div>
                                                <div style="margin-top:8px;"><button type="button" class="button bswan-btn-danger" onclick="bswanDeleteAll('wl')"><?php echo $i_trash; ?> <?php _e('Delete All', $this->td); ?></button></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Section 2 -->
                            <div id="bswan-section-domain" class="postbox">
                                <div class="postbox-header">
                                    <h2 class="hndle"><?php echo $i_globe; ?> <span><?php _e('Browser-side Resources by Domain (JS / CSS)', $this->td); ?></span></h2>
                                    <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                </div>
                                <div class="inside">
                                    <p class="description"><?php _e('When enabled, any enqueued JavaScript or CSS file loaded from a blacklisted domain (Section 1) will be deregistered before the page renders. This only affects external resources — files on your own server are never touched. Whitelisted URL patterns are still respected.', $this->td); ?></p>
                                    <fieldset style="display:flex;gap:24px;flex-wrap:wrap;margin-top:8px;">
                                        <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
                                            <input type="checkbox" id="bswan-res-backend" <?php checked($res_backend); ?>>
                                            <?php echo $i_gear; ?> <?php _e('Admin Panel (wp-admin)', $this->td); ?>
                                        </label>
                                        <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
                                            <input type="checkbox" id="bswan-res-frontend" <?php checked($res_frontend); ?>>
                                            <?php echo $i_monitor; ?> <?php _e('Public Frontend', $this->td); ?>
                                        </label>
                                    </fieldset>
                                </div>
                            </div>

                            <!-- Section 3 -->
                            <div id="bswan-section-specific" class="postbox">
                                <div class="postbox-header">
                                    <h2 class="hndle"><?php echo $i_ban; ?> <span><?php _e('Block Specific Resources (by URL)', $this->td); ?></span></h2>
                                    <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                </div>
                                <div class="inside">
                                    <p class="description"><?php _e('Block individual JS or CSS files by matching against their enqueued URL. You can enter a full URL, a partial path, or even just a filename with extension — anything that appears in the resource URL will match.', $this->td); ?></p>
                                    <p class="description" style="margin-top:4px;"><?php _e('Examples: <code>/persian-woocommerce/assets/fonts/admin-font.css</code> or <code>admin-font.css</code> or <code>https://cdn.example.com/lib.js</code>. Works for both local and external resources. Choose whether to block on backend, frontend, or both.', $this->td); ?></p>
                                    <div style="display:flex;gap:6px;margin:10px 0 8px;align-items:center;">
                                        <input type="text" id="bswan-br-input" class="regular-text" placeholder="<?php esc_attr_e('Full URL, partial path, or filename.ext', $this->td); ?>" style="flex:1;">
                                        <label style="display:inline-flex;align-items:center;gap:3px;white-space:nowrap;"><input type="checkbox" id="bswan-br-backend" checked> <?php _e('Backend', $this->td); ?></label>
                                        <label style="display:inline-flex;align-items:center;gap:3px;white-space:nowrap;"><input type="checkbox" id="bswan-br-frontend"> <?php _e('Frontend', $this->td); ?></label>
                                        <button type="button" class="button button-primary" onclick="bswanAddResource()"><?php _e('Add', $this->td); ?></button>
                                    </div>
                                    <div class="bswan-table-scroll"><table class="widefat striped" id="bswan-br-table"><thead><tr><th><?php _e('URL Pattern', $this->td); ?></th><th style="width:80px;text-align:center;"><?php _e('Backend', $this->td); ?></th><th style="width:80px;text-align:center;"><?php _e('Frontend', $this->td); ?></th><th style="width:90px;text-align:right;"><?php _e('Actions', $this->td); ?></th></tr></thead><tbody></tbody></table></div>
                                    <div style="margin-top:8px;"><button type="button" class="button bswan-btn-danger" onclick="bswanDeleteAllRes()"><?php echo $i_trash; ?> <?php _e('Delete All', $this->td); ?></button></div>
                                </div>
                            </div>

                        </div><!-- /post-body-content -->

                        <!-- ════════════ SIDEBAR ════════════ -->
                        <div id="postbox-container-1" class="postbox-container">

                            <!-- Save & Pause -->
                            <div class="postbox">
                                <div class="postbox-header"><h2 class="hndle"><span><?php _e('Actions', $this->td); ?></span></h2></div>
                                <div class="inside">
                                    <div style="display:flex;flex-direction:column;gap:10px;">
                                        <button type="button" id="bswan-save-btn" class="button button-primary button-large" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
                                            <?php echo $i_save; ?> <?php _e('Save All Settings', $this->td); ?>
                                        </button>
                                        <span id="bswan-save-msg" style="display:none;font-weight:600;text-align:center;"></span>
                                        <hr style="margin:4px 0;">
                                        <div style="display:flex;align-items:center;gap:8px;justify-content:space-between;">
                                            <button type="button" id="bswan-pause-btn" class="button <?php echo $is_paused ? 'button-primary' : ''; ?>" style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;">
                                                <span class="bswan-pause-icon"><?php echo $is_paused ? $i_play : $i_pause; ?></span>
                                                <span class="bswan-pause-label"><?php echo $is_paused ? __('Resume', $this->td) : __('Pause', $this->td); ?></span>
                                            </button>
                                            <span id="bswan-status-badge" class="<?php echo $is_paused ? 'notice-warning' : 'notice-success'; ?>" style="flex:1;display:inline-block;padding:4px 10px;font-weight:600;border-left:4px solid;background:#fff;font-size:12px;">
                                                <?php echo $is_paused ? __('⏸ Paused', $this->td) : __('✔ Active', $this->td); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Safe Mode -->
                            <div class="postbox" style="border-left:4px solid #2271b1;">
                                <div class="postbox-header"><h2 class="hndle"><span><?php _e('Safe Mode', $this->td); ?></span></h2></div>
                                <div class="inside">
                                    <p class="description" style="margin:0 0 8px;"><?php _e('If blocking rules break your admin panel, use safe mode to temporarily disable <strong>all</strong> blocking (HTTP requests, domain-based resources, and specific resources) for that page load so you can fix your settings.', $this->td); ?></p>
                                    <p class="description" style="margin:0 0 8px;"><?php _e('Note: Resource dequeuing (JS/CSS) is already skipped on this settings page automatically — safe mode is for when other admin pages are broken.', $this->td); ?></p>
                                    <a href="<?php echo esc_url($safe_mode_url); ?>" class="button" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
                                        <?php echo $i_shieldchk; ?> <?php _e('Open Settings in Safe Mode', $this->td); ?>
                                    </a>
                                    <p class="description" style="margin:8px 0 0;font-size:11px;color:#666;"><?php _e('Tip: Add <code>&bswan-safe=1</code> to any admin URL to fully bypass all blocking rules on that page load.', $this->td); ?></p>
                                </div>
                            </div>

                            <!-- Tools -->
                            <div class="postbox">
                                <div class="postbox-header"><h2 class="hndle"><span><?php _e('Tools', $this->td); ?></span></h2></div>
                                <div class="inside">
                                    <?php if ($qm_active): ?>
                                        <p class="description" style="display:flex;align-items:center;gap:6px;"><?php echo $i_check; ?> <?php _e('Query Monitor is active.', $this->td); ?></p>
                                    <?php elseif ($qm_installed): ?>
                                        <a href="<?php echo esc_url($qm_url); ?>" class="button" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:6px;">
                                            <?php echo $i_play; ?> <?php _e('Activate Query Monitor', $this->td); ?>
                                        </a>
                                        <p class="description"><?php _e('Query Monitor is installed but not active. Activate it to inspect all HTTP requests and enqueued resources.', $this->td); ?></p>
                                    <?php else: ?>
                                        <a href="<?php echo esc_url($qm_url); ?>" class="button" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:6px;">
                                            <?php echo $i_search; ?> <?php _e('Install Query Monitor', $this->td); ?>
                                        </a>
                                        <p class="description"><?php _e('Query Monitor helps you see every HTTP request and enqueued resource on your site. Highly recommended for debugging.', $this->td); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Export / Import -->
                            <div class="postbox">
                                <div class="postbox-header"><h2 class="hndle"><span><?php _e('Export / Import', $this->td); ?></span></h2></div>
                                <div class="inside">
                                    <p class="description" style="margin:0 0 8px;"><?php _e('Export or import all plugin settings as a single JSON file — includes blacklist, whitelist, blocked resources, and all toggles.', $this->td); ?></p>
                                    <button type="button" id="bswan-export-all" class="button" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:6px;">
                                        <?php echo $i_download; ?> <?php _e('Export All Settings', $this->td); ?>
                                    </button>
                                    <button type="button" id="bswan-import-all-btn" class="button" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
                                        <?php echo $i_upload; ?> <?php _e('Import Settings', $this->td); ?>
                                    </button>
                                    <input type="file" id="bswan-import-all" accept=".json" style="display:none;">
                                    <span id="bswan-import-msg" style="display:none;font-weight:600;text-align:center;margin-top:6px;font-size:12px;"></span>
                                </div>
                            </div>

                            <!-- Disclaimer -->
                            <div class="postbox" style="border-left:4px solid #dba617;">
                                <div class="postbox-header"><h2 class="hndle"><span><?php _e('Disclaimer', $this->td); ?></span></h2></div>
                                <div class="inside">
                                    <p class="description" style="margin:0;"><?php _e('This plugin blocks outgoing HTTP requests and browser-loaded resources. Blocking certain domains may prevent plugin/theme updates, license verification, REST API calls, Google Fonts, CDN libraries, and other essential functionality.', $this->td); ?></p>
                                    <p class="description" style="margin:8px 0 0;"><?php _e('Use at your own risk and always maintain proper backups. The developers are not responsible for any issues caused by misconfigured blocking rules.', $this->td); ?></p>
                                </div>
                            </div>

                        </div><!-- /postbox-container-1 -->

                    </div><!-- /post-body -->
                </div><!-- /poststuff -->
            </div><!-- /wrap -->

            <style>
            #bswan-columns .card .widefat td.bswan-actions{text-align:right;white-space:nowrap;}
            #bswan-columns .card .widefat .bswan-edit-input,
            #bswan-br-table .bswan-edit-input{width:calc(100% - 10px);}
            .wrap a.button, .wrap button.button { justify-content: flex-start !important; }
            .bswan-table-scroll{max-height:300px;overflow-y:auto;border:1px solid #c3c4c7;border-radius:3px;}
            .bswan-table-scroll>.widefat{border:0;}
            .bswan-table-scroll>.widefat thead th{position:sticky;top:0;background:#f0f0f1;z-index:1;box-shadow:inset 0 -1px 0 #c3c4c7;}
            #bswan-br-table td.bswan-br-scope{text-align:center;}
            .bswan-btn-danger{color:#d63638 !important;border-color:#d63638 !important;display:inline-flex;align-items:center;gap:4px;}
            .bswan-row-btn{padding:2px 6px !important;min-height:0 !important;line-height:1 !important;display:inline-flex !important;align-items:center;}
            #post-body-content .postbox-header .hndle{justify-content:flex-start;gap:6px;}
            .postbox-container .hndle{pointer-events:none;-webkit-user-select:auto;user-select:auto;}
            @media(max-width:782px){#bswan-columns{grid-template-columns:1fr !important;}}
            </style>

            <script>
            jQuery(function($){
                postboxes.add_postbox_toggles(pagenow);

                var bl = <?php echo $blacklist_json; ?>;
                var wl = <?php echo $whitelist_json; ?>;
                var br = <?php echo $blocked_res_json; ?>;
                var nonce = '<?php echo $nonce; ?>';
                var ajaxurl = '<?php echo $ajax_url; ?>';
                var isPaused = <?php echo $is_paused ? 'true' : 'false'; ?>;

                // SVG icon strings for JS-rendered buttons
                var svgEdit = '<?php echo addslashes(self::icon('pencil', 'currentColor', 14)); ?>';
                var svgX    = '<?php echo addslashes(self::icon('x', '#d63638', 14)); ?>';
                var svgPlay = '<?php echo addslashes(self::icon('play', 'currentColor')); ?>';
                var svgPause= '<?php echo addslashes(self::icon('pause', 'currentColor')); ?>';

                function renderTable(type){
                    var list = type==='bl' ? bl : wl;
                    var $tb = $('#bswan-'+type+'-table tbody');
                    $tb.empty();
                    if(!list.length){
                        $tb.append('<tr><td colspan="2" style="text-align:center;color:#999;"><?php _e('No items.', $this->td); ?></td></tr>');
                    } else {
                        $.each(list, function(i,v){
                            $tb.append(
                                '<tr data-i="'+i+'">'+
                                '<td><span class="bswan-val">'+$('<span>').text(v).html()+'</span><input type="text" class="bswan-edit-input regular-text" value="'+$('<span>').text(v).html()+'" style="display:none;"></td>'+
                                '<td class="bswan-actions">'+
                                '<button type="button" class="button bswan-row-btn bswan-edit-btn" title="<?php esc_attr_e('Edit', $this->td); ?>">'+svgEdit+'</button> '+
                                '<button type="button" class="button bswan-row-btn bswan-del-btn" title="<?php esc_attr_e('Delete', $this->td); ?>" style="color:#d63638;border-color:#d63638;">'+svgX+'</button>'+
                                '</td></tr>'
                            );
                        });
                    }
                    $('.bswan-count-'+type).text(list.length);
                }

                $(document).on('click','.bswan-edit-btn',function(){
                    var $tr=$(this).closest('tr'),$val=$tr.find('.bswan-val'),$inp=$tr.find('.bswan-edit-input');
                    if($inp.is(':visible')){
                        var type=$tr.closest('table').attr('id').indexOf('-bl-')>-1?'bl':'wl';
                        var list=type==='bl'?bl:wl,idx=$tr.data('i'),nv=$.trim($inp.val());
                        if(nv) list[idx]=nv;
                        renderTable(type);
                    } else { $val.hide();$inp.show().focus().select(); }
                });
                $(document).on('keydown','.bswan-edit-input',function(e){
                    if($(this).closest('#bswan-br-table').length) return;
                    if(e.key==='Enter') $(this).closest('tr').find('.bswan-edit-btn').click();
                    if(e.key==='Escape'){ var type=$(this).closest('table').attr('id').indexOf('-bl-')>-1?'bl':'wl'; renderTable(type); }
                });
                $(document).on('click','.bswan-del-btn',function(){
                    var $tr=$(this).closest('tr'),type=$tr.closest('table').attr('id').indexOf('-bl-')>-1?'bl':'wl';
                    (type==='bl'?bl:wl).splice($tr.data('i'),1);
                    renderTable(type);
                });

                window.bswanAddItem=function(type){
                    var $inp=$('#bswan-'+type+'-input'),v=$.trim($inp.val());
                    if(!v) return;
                    var list=type==='bl'?bl:wl;
                    if(list.indexOf(v)===-1) list.push(v);
                    $inp.val('');
                    renderTable(type);
                };
                $('#bswan-bl-input,#bswan-wl-input').on('keydown',function(e){
                    if(e.key==='Enter'){ e.preventDefault(); bswanAddItem($(this).attr('id').indexOf('-bl-')>-1?'bl':'wl'); }
                });

                window.bswanDeleteAll=function(type){
                    if(!confirm('<?php _e('Are you sure you want to delete all items?', $this->td); ?>')) return;
                    if(type==='bl') bl=[]; else wl=[];
                    renderTable(type);
                };

                // ── Export / Import ──
                $('#bswan-export-all').on('click',function(){
                    var s = {blacklist:bl,whitelist:wl,block_resources_backend:$('#bswan-res-backend').is(':checked'),block_resources_frontend:$('#bswan-res-frontend').is(':checked'),blocked_resources:br};
                    var a = document.createElement('a');
                    a.href = URL.createObjectURL(new Blob([JSON.stringify(s,null,2)],{type:'application/json'}));
                    a.download = 'bswan-all-settings-export.json';
                    a.click();
                });
                $('#bswan-import-all-btn').on('click',function(){ $('#bswan-import-all').click(); });
                $('#bswan-import-all').on('change',function(){
                    var file=this.files[0],$msg=$('#bswan-import-msg');
                    if(!file) return;
                    var reader=new FileReader();
                    reader.onload=function(e){
                        try {
                            var d=JSON.parse(e.target.result);
                            if(typeof d!=='object'||Array.isArray(d)){$msg.text('<?php _e('Invalid JSON: expected a settings object.', $this->td); ?>').css('color','#d63638').show();return;}
                            if(Array.isArray(d.blacklist)) bl=d.blacklist;
                            if(Array.isArray(d.whitelist)) wl=d.whitelist;
                            if(Array.isArray(d.blocked_resources)) br=d.blocked_resources;
                            if(typeof d.block_resources_backend!=='undefined') $('#bswan-res-backend').prop('checked',!!d.block_resources_backend);
                            if(typeof d.block_resources_frontend!=='undefined') $('#bswan-res-frontend').prop('checked',!!d.block_resources_frontend);
                            renderTable('bl');renderTable('wl');renderResTable();
                            $msg.text('<?php _e('Settings imported. Click "Save All Settings" to apply.', $this->td); ?>').css('color','#2271b1').show();
                        } catch(err){$msg.text('<?php _e('Failed to parse JSON file.', $this->td); ?>').css('color','#d63638').show();}
                    };
                    reader.readAsText(file);
                    $(this).val('');
                });

                // ── Specific Resources ──
                function renderResTable(){
                    var $tb=$('#bswan-br-table tbody');
                    $tb.empty();
                    if(!br.length){
                        $tb.append('<tr><td colspan="4" style="text-align:center;color:#999;"><?php _e('No items.', $this->td); ?></td></tr>');
                    } else {
                        $.each(br, function(i,item){
                            $tb.append(
                                '<tr data-i="'+i+'">'+
                                '<td><span class="bswan-val">'+$('<span>').text(item.url).html()+'</span><input type="text" class="bswan-edit-input regular-text" value="'+$('<span>').text(item.url).html()+'" style="display:none;width:calc(100% - 10px);"></td>'+
                                '<td class="bswan-br-scope"><input type="checkbox" class="bswan-br-cb-be" '+(item.backend?'checked':'')+' title="<?php esc_attr_e('Backend', $this->td); ?>"></td>'+
                                '<td class="bswan-br-scope"><input type="checkbox" class="bswan-br-cb-fe" '+(item.frontend?'checked':'')+' title="<?php esc_attr_e('Frontend', $this->td); ?>"></td>'+
                                '<td class="bswan-actions">'+
                                '<button type="button" class="button bswan-row-btn bswan-br-edit-btn" title="<?php esc_attr_e('Edit', $this->td); ?>">'+svgEdit+'</button> '+
                                '<button type="button" class="button bswan-row-btn bswan-br-del-btn" title="<?php esc_attr_e('Delete', $this->td); ?>" style="color:#d63638;border-color:#d63638;">'+svgX+'</button>'+
                                '</td></tr>'
                            );
                        });
                    }
                }

                $(document).on('change','.bswan-br-cb-be,.bswan-br-cb-fe',function(){
                    var $tr=$(this).closest('tr'),i=$tr.data('i');
                    br[i].backend=$tr.find('.bswan-br-cb-be').is(':checked');
                    br[i].frontend=$tr.find('.bswan-br-cb-fe').is(':checked');
                });
                $(document).on('click','.bswan-br-edit-btn',function(){
                    var $tr=$(this).closest('tr'),$val=$tr.find('.bswan-val'),$inp=$tr.find('.bswan-edit-input');
                    if($inp.is(':visible')){var nv=$.trim($inp.val()),i=$tr.data('i');if(nv) br[i].url=nv;renderResTable();}
                    else {$val.hide();$inp.show().focus().select();}
                });
                $(document).on('keydown','#bswan-br-table .bswan-edit-input',function(e){
                    if(e.key==='Enter') $(this).closest('tr').find('.bswan-br-edit-btn').click();
                    if(e.key==='Escape') renderResTable();
                });
                $(document).on('click','.bswan-br-del-btn',function(){
                    br.splice($(this).closest('tr').data('i'),1);renderResTable();
                });

                window.bswanAddResource=function(){
                    var v=$.trim($('#bswan-br-input').val());
                    if(!v) return;
                    br.push({url:v,backend:$('#bswan-br-backend').is(':checked'),frontend:$('#bswan-br-frontend').is(':checked')});
                    $('#bswan-br-input').val('');
                    renderResTable();
                };
                $('#bswan-br-input').on('keydown',function(e){if(e.key==='Enter'){e.preventDefault();bswanAddResource();}});

                window.bswanDeleteAllRes=function(){
                    if(!confirm('<?php _e('Are you sure you want to delete all items?', $this->td); ?>')) return;
                    br=[];renderResTable();
                };

                // ── Save ──
                $('#bswan-save-btn').on('click',function(){
                    var $btn=$(this).prop('disabled',true),$msg=$('#bswan-save-msg');
                    var brSend=[];
                    for(var i=0;i<br.length;i++) brSend.push({url:br[i].url,backend:br[i].backend?1:0,frontend:br[i].frontend?1:0});
                    $.post(ajaxurl,{
                        action:'bswan_ber_save',_nonce:nonce,blacklist:bl,whitelist:wl,
                        block_resources_backend:$('#bswan-res-backend').is(':checked')?1:0,
                        block_resources_frontend:$('#bswan-res-frontend').is(':checked')?1:0,
                        blocked_resources:brSend
                    },function(res){
                        $btn.prop('disabled',false);
                        $msg.text(res.success?res.data:'<?php _e('Error saving.', $this->td); ?>').css('color',res.success?'#00a32a':'#d63638').fadeIn().delay(3000).fadeOut();
                    }).fail(function(){$btn.prop('disabled',false);$msg.text('<?php _e('Request failed.', $this->td); ?>').css('color','#d63638').fadeIn().delay(3000).fadeOut();});
                });

                // ── Pause ──
                $('#bswan-pause-btn').on('click',function(){
                    var $btn=$(this).prop('disabled',true);
                    $.post(ajaxurl,{action:'bswan_ber_toggle_pause',_nonce:nonce},function(res){
                        $btn.prop('disabled',false);
                        if(res.success){
                            isPaused=res.data.paused;
                            var $icon=$btn.find('.bswan-pause-icon'),$label=$btn.find('.bswan-pause-label'),$badge=$('#bswan-status-badge');
                            if(isPaused){
                                $btn.addClass('button-primary');
                                $icon.html(svgPlay);
                                $label.text('<?php _e('Resume', $this->td); ?>');
                                $badge.removeClass('notice-success').addClass('notice-warning').html('<?php _e('⏸ Paused', $this->td); ?>');
                            } else {
                                $btn.removeClass('button-primary');
                                $icon.html(svgPause);
                                $label.text('<?php _e('Pause', $this->td); ?>');
                                $badge.removeClass('notice-warning').addClass('notice-success').html('<?php _e('✔ Active', $this->td); ?>');
                            }
                            $('#bswan-save-msg').text(res.data.message).css('color','#2271b1').fadeIn().delay(3000).fadeOut();
                        }
                    }).fail(function(){$btn.prop('disabled',false);});
                });

                renderTable('bl');
                renderTable('wl');
                renderResTable();
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