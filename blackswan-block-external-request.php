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
 Version: 2.6.0
 Stable tag: 2.6.0
 Requires PHP: 5.4
 Tested up to: 6.8
 Requires at least: 5.0
 Text Domain: blackswan-block-external-request
 Domain Path: /languages
 Copyright: (c) amirhp.com, All rights reserved.
 License: GPLv2 or later
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * @Last modified by: amirhp-com <its@amirhp.com>
 * @Last modified time: 2026/03/07 17:23:35
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
            "w.org",
            "w.com",
            "wp.org",
            "wp.com",
            "wincher.com",
            "yoa.st",
            "yoast.com",
            "wordpress.org",
            "wordpress.com",
            "woocommerce.com",
            "reduxframework.com",
            "wp-rocket.me",
            "easydigitaldownloads.com",
            "github.com",
            "google.com",
        );
        private static $default_whitelist = array(
            "//api.wordpress.org/plugins/",
            "//downloads.wordpress.org/",
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
            );
            if (!isset($icons[$name])) return '';
            return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $s . '" height="' . $s . '" viewBox="0 0 24 24" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;flex-shrink:0;">' . $icons[$name] . '</svg>';
        }

        public function __construct() {
            $this->td      = "blackswan-block-external-request";
            $this->version = "2.5.0";
            load_plugin_textdomain($this->td, false, dirname(plugin_basename(__FILE__)) . "/languages/");
            $this->title = __("Block External Request", $this->td);

            $this->load_settings();

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
                add_filter("plugin_action_links_" . plugin_basename(__FILE__), array($this, "plugin_action_links"));
            }
        }

        private function is_safe_mode() {
            if (!is_admin()) return false;
            return (isset($_GET['bswan-safe']) && $_GET['bswan-safe'] === '1');
        }

        private function load_settings() {
            $defaults = array('blacklist' => self::$default_blacklist, 'whitelist' => self::$default_whitelist, 'paused' => false, 'block_resources_backend' => false, 'block_resources_frontend' => false, 'blocked_resources' => array());
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
            $blocked_resources = array();
            if (!empty($_POST['blocked_resources']) && is_array($_POST['blocked_resources'])) {
                foreach ($_POST['blocked_resources'] as $item) {
                    $url = sanitize_text_field(isset($item['url']) ? $item['url'] : '');
                    if (empty($url)) continue;
                    $blocked_resources[] = array('url' => $url, 'backend' => !empty($item['backend']), 'frontend' => !empty($item['frontend']));
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
            wp_send_json_success(array('paused' => $this->settings['paused'], 'message' => $this->settings['paused'] ? __('Plugin is now paused. All blocking is disabled.', $this->td) : __('Plugin is now active. Blocking rules are enforced.', $this->td)));
        }

        public function block_external_request($preempt, $parsed_args, $url) {
            foreach ($this->block_url_list as $bu) {
                if (strpos($url, $bu) !== false) {
                    foreach ($this->whitelist_urls as $wu) {
                        if (strpos($url, $wu) !== false) return $preempt;
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
            $qm_url = $qm_installed ? wp_nonce_url(admin_url('plugins.php?action=activate&plugin=query-monitor/query-monitor.php'), 'activate-plugin_query-monitor/query-monitor.php') : admin_url('plugin-install.php?s=query+monitor&tab=search&type=term');
            $safe_mode_url = admin_url('options-general.php?page=bswan-ber-settings&bswan-safe=1');
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

                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">

                            <!-- Section 1 -->
                            <div id="bswan-section-http" class="postbox bswan-glass">
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
                                                                <th style="width:90px;text-align:right;"><?php _e('Actions', $this->td); ?></th>
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
                                                                <th style="width:90px;text-align:right;"><?php _e('Actions', $this->td); ?></th>
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
                            <div id="bswan-section-domain" class="postbox bswan-glass">
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
                            <div id="bswan-section-specific" class="postbox bswan-glass">
                                <div class="postbox-header">
                                    <h2 class="hndle"><?php echo $i['ban']; ?> <span><?php _e('Block Specific Resources (by URL)', $this->td); ?></span></h2>
                                    <div class="handle-actions"><button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text"><?php _e('Toggle panel', $this->td); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button></div>
                                </div>
                                <div class="inside">
                                    <p class="description"><?php _e('Block individual JS or CSS files by matching against their enqueued URL. Full URL, partial path, or even just a filename with extension — anything that appears in the resource URL will match. Works for both local and external resources.', $this->td); ?></p>
                                    <p class="description" style="margin-top:4px;"><?php _e('Examples: <code>/persian-woocommerce/assets/fonts/admin-font.css</code> · <code>admin-font.css</code> · <code>https://cdn.example.com/lib.js</code>', $this->td); ?></p>
                                    <div class="bswan-input-row" style="margin:10px 0 8px;">
                                        <input type="text" id="bswan-br-input" class="regular-text" placeholder="<?php esc_attr_e('Full URL, partial path, or filename.ext', $this->td); ?>">

                                        <div class="checkbox-wrapper">
                                            <input type="checkbox" class="switch" id="bswan-br-backend" checked>
                                            <label for="bswan-br-backend"><span class="switch-x-text"><?php _e('Backend', $this->td); ?></span></label>
                                        </div>

                                        <div class="checkbox-wrapper">
                                            <input type="checkbox" class="switch" id="bswan-br-frontend">
                                            <label for="bswan-br-frontend"><span class="switch-x-text"><?php _e('Frontend', $this->td); ?></span></label>
                                        </div>

                                        <button type="button" class="button button-primary" onclick="bswanAddResource()"><?php _e('Add', $this->td); ?></button>
                                    </div>
                                    <div class="bswan-table-scroll">
                                        <table class="widefat striped" id="bswan-br-table">
                                            <thead>
                                                <tr>
                                                    <th><?php _e('URL Pattern', $this->td); ?></th>
                                                    <th><?php _e('Conditions', $this->td); ?></th>
                                                    <th style="width:90px;text-align:right;"><?php _e('Actions', $this->td); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div style="margin-top:8px;"><button type="button" class="button bswan-btn-danger" onclick="bswanDeleteAllRes()"><?php echo $i['trash']; ?> <?php _e('Delete All', $this->td); ?></button></div>
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

                            <div class="postbox bswan-glass" style="border-left:3px solid rgba(34,113,177,0.6);">
                                <div class="postbox-header">
                                    <h2 class="hndle"><span><?php _e('Safe Mode', $this->td); ?></span></h2>
                                </div>
                                <div class="inside">
                                    <p class="description" style="margin:0 0 8px;"><?php _e('Temporarily disable <strong>all</strong> blocking for a page load. Resource dequeuing is already skipped on this settings page — safe mode is for other broken admin pages.', $this->td); ?></p>
                                    <a href="<?php echo esc_url($safe_mode_url); ?>" class="button bswan-full-btn"><?php echo $i['shldchk']; ?> <?php _e('Open in Safe Mode', $this->td); ?></a>
                                    <p class="description" style="margin:8px 0 0;opacity:0.7;"><?php _e('Tip: Add <code>&bswan-safe=1</code> to any admin URL.', $this->td); ?></p>
                                </div>
                            </div>

                            <div class="postbox bswan-glass">
                                <div class="postbox-header">
                                    <h2 class="hndle"><span><?php _e('Tools', $this->td); ?></span></h2>
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

                            <div class="postbox bswan-glass">
                                <div class="postbox-header">
                                    <h2 class="hndle"><span><?php _e('Export / Import', $this->td); ?></span></h2>
                                </div>
                                <div class="inside">
                                    <p class="description" style="margin:0 0 8px;"><?php _e('All plugin settings as a single JSON file.', $this->td); ?></p>
                                    <button type="button" id="bswan-export-all" class="button bswan-full-btn" style="margin-bottom:6px;"><?php echo $i['download']; ?> <?php _e('Export All Settings', $this->td); ?></button>
                                    <button type="button" id="bswan-import-all-btn" class="button bswan-full-btn"><?php echo $i['upload']; ?> <?php _e('Import Settings', $this->td); ?></button>
                                    <input type="file" id="bswan-import-all" accept=".json" style="display:none;">
                                    <span id="bswan-import-msg" class="bswan-msg"></span>
                                </div>
                            </div>

                            <div class="postbox bswan-glass bswan-status-paused" style="border-left:3px solid rgba(219,166,23,0.6);">
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
            </div>

            <style>
                .checkbox-wrapper-flex {
                    display: flex;
                    gap: 8px;
                    align-items: center;
                }

                .checkbox-wrapper .switch {
                    display: none;
                }

                .checkbox-wrapper .switch+label {
                    -webkit-box-align: center;
                    -webkit-align-items: center;
                    -ms-flex-align: center;
                    align-items: center;
                    color: #78768d;
                    cursor: pointer;
                    display: -webkit-box;
                    display: -webkit-flex;
                    display: -ms-flexbox;
                    display: flex;
                    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                    font-size: 12px;
                    line-height: 15px;
                    position: relative;
                    -webkit-user-select: none;
                    -moz-user-select: none;
                    -ms-user-select: none;
                    user-select: none;
                    gap: 5px;
                }

                .checkbox-wrapper .switch+label::before,
                .checkbox-wrapper .switch+label::after {
                    content: '';
                    display: block;
                }

                .checkbox-wrapper .switch+label::before {
                    background-color: #05012c;
                    border-radius: 500px;
                    height: 16px;
                    -webkit-transition: background-color 0.125s ease-out;
                    transition: background-color 0.125s ease-out;
                    width: 30px;
                }

                .checkbox-wrapper .switch+label::after {
                    background-color: #fff;
                    border-radius: 13px;
                    box-shadow: 0 3px 1px 0 rgba(37, 34, 71, 0.05), 0 2px 2px 0 rgba(37, 34, 71, 0.1), 0 3px 3px 0 rgba(37, 34, 71, 0.05);
                    height: 12px;
                    left: 2px;
                    position: absolute;
                    top: auto;
                    -webkit-transition: -webkit-transform 0.125s ease-out;
                    transition: -webkit-transform 0.125s ease-out;
                    transition: transform 0.125s ease-out;
                    transition: transform 0.125s ease-out, -webkit-transform 0.125s ease-out;
                    width: 12px;
                }

                .checkbox-wrapper .switch+label .switch-x-text {
                    display: block;
                    margin-right: .3em;
                }

                .checkbox-wrapper .switch+label .switch-x-text>svg {
                    width: 14px;
                    height: 14px;
                    margin: -2px 4px 0 0;
                }

                .checkbox-wrapper .switch+label .switch-x-toggletext {
                    display: block;
                    font-weight: bold;
                    height: 15px;
                    overflow: hidden;
                    position: relative;
                    width: 25px;
                }

                .checkbox-wrapper .switch+label .switch-x-unchecked,
                .checkbox-wrapper .switch+label .switch-x-checked {
                    left: 0;
                    position: absolute;
                    top: 0;
                    -webkit-transition: opacity 0.125s ease-out, -webkit-transform 0.125s ease-out;
                    transition: opacity 0.125s ease-out, -webkit-transform 0.125s ease-out;
                    transition: transform 0.125s ease-out, opacity 0.125s ease-out;
                    transition: transform 0.125s ease-out, opacity 0.125s ease-out, -webkit-transform 0.125s ease-out;
                }

                .checkbox-wrapper .switch+label .switch-x-unchecked {
                    opacity: 1;
                    -webkit-transform: none;
                    transform: none;
                }

                .checkbox-wrapper .switch+label .switch-x-checked {
                    opacity: 0;
                    -webkit-transform: translate3d(0, 100%, 0);
                    transform: translate3d(0, 100%, 0);
                }

                .checkbox-wrapper .switch+label .switch-x-hiddenlabel {
                    position: absolute;
                    visibility: hidden;
                }

                .checkbox-wrapper .switch:checked+label::before {
                    background-color: #08bc02;
                }

                .checkbox-wrapper .switch:checked+label::after {
                    -webkit-transform: translate3d(14px, 0, 0);
                    transform: translate3d(14px, 0, 0);
                }

                .checkbox-wrapper .switch:checked+label .switch-x-unchecked {
                    opacity: 0;
                    -webkit-transform: translate3d(0, -100%, 0);
                    transform: translate3d(0, -100%, 0);
                }

                .checkbox-wrapper .switch:checked+label .switch-x-checked {
                    opacity: 1;
                    -webkit-transform: none;
                    transform: none;
                }

                /* ── Liquid Glass Design System ── */
                .bswan-wrap {
                    position: relative;
                    padding: 20px 0;
                }

                .bswan-wrap::before {
                    content: '';
                    position: fixed;
                    inset: 0;
                    z-index: -1;
                    background:
                        radial-gradient(ellipse at 20% 50%, rgba(34, 113, 177, 0.06) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(130, 110, 180, 0.06) 0%, transparent 50%),
                        radial-gradient(ellipse at 60% 80%, rgba(0, 163, 42, 0.04) 0%, transparent 50%),
                        radial-gradient(circle at 1px 1px, rgba(0, 0, 0, 0.07) 1px, transparent 0);
                    background-size: 100% 100%, 100% 100%, 100% 100%, 20px 20px;
                }

                .bswan-title {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-weight: 900 !important;
                    letter-spacing: -0.01em;
                }

                .bswan-title sup {
                    background: linear-gradient(135deg, #2271b1, #135e96);
                    color: #fff;
                    padding: 4px 10px;
                    font-size: 11px;
                    border-radius: 20px;
                    font-weight: 600;
                    letter-spacing: 0.03em;
                    line-height: 1;
                    position: relative;
                    top: -0.7rem;
                }

                /* Glass postboxes */
                .bswan-glass {
                    background: rgba(255, 255, 255, 0.65) !important;
                    backdrop-filter: blur(16px) saturate(1.4);
                    -webkit-backdrop-filter: blur(16px) saturate(1.4);
                    border: 1px solid rgba(255, 255, 255, 0.5) !important;
                    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.7) !important;
                    border-radius: 14px !important;
                    overflow: hidden;
                }

                .bswan-glass .postbox-header {
                    background: rgba(255, 255, 255, 0.4) !important;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.06) !important;
                }

                .bswan-glass .inside {
                    padding: 14px 16px !important;
                }

                #post-body-content .postbox-header .hndle {
                    justify-content: flex-start;
                    gap: 6px;
                }

                .postbox-container .hndle {
                    pointer-events: none;
                    user-select: auto;
                }

                /* Notice */
                .bswan-notice {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 12px 16px;
                    border-radius: 10px;
                    margin-bottom: 16px;
                    font-size: 13px;
                    backdrop-filter: blur(8px);
                }

                .bswan-notice-info {
                    background: rgba(34, 113, 177, 0.08);
                    border: 1px solid rgba(34, 113, 177, 0.2);
                    color: #135e96;
                }

                /* Cards inside Section 1 */
                #bswan-columns {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 16px;
                    margin-top: 12px;
                }

                .bswan-card {
                    background: rgba(255, 255, 255, 0.5);
                    border: 1px solid rgba(0, 0, 0, 0.06);
                    border-radius: 10px;
                    overflow: hidden;
                }

                .bswan-card-header {
                    padding: 10px 14px;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    background: rgba(255, 255, 255, 0.3);
                }

                .bswan-card-header h3 {
                    margin: 0;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-size: 13px;
                    font-weight: 600;
                }

                .bswan-card-body {
                    display: flex;
                    gap: 8px;
                    flex-direction: column;
                    padding: 10px 14px;
                }

                /* Badges */
                .bswan-badge {
                    padding: 2px 9px;
                    border-radius: 10px;
                    font-size: 11px;
                    font-weight: 700;
                    color: #fff;
                    line-height: 1.4;
                }

                .bswan-badge-red {
                    background: linear-gradient(135deg, #d63638, #b32d2e);
                }

                .bswan-badge-green {
                    background: linear-gradient(135deg, #00a32a, #008a20);
                }

                /* Status badge */
                .bswan-status {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px 12px;
                    border-radius: 8px;
                    font-size: 12px;
                    font-weight: 600;
                    margin-top: 10px;
                    transition: all 0.3s ease;
                }

                .bswan-status-active {
                    background: rgba(0, 163, 42, 0.08);
                    border: 1px solid rgba(0, 163, 42, 0.2);
                    color: #006b1a;
                }

                .bswan-status-paused {
                    background: rgba(219, 166, 23, 0.08) !important;
                    border: 1px solid rgba(219, 166, 23, 0.25) !important;
                    color: #8c6a00 !important;
                }

                /* Table scroll */
                .bswan-table-scroll {
                    max-height: 300px;
                    overflow-y: auto;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    border-radius: 8px;
                }

                .bswan-table-scroll>.widefat {
                    border: 0;
                }

                .bswan-table-scroll>.widefat thead th {
                    position: sticky;
                    top: 0;
                    background: rgba(240, 240, 241, 0.9);
                    backdrop-filter: blur(4px);
                    z-index: 1;
                    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.08);
                }

                #bswan-br-table td.bswan-br-scope {
                    text-align: center;
                }

                /* Inputs */
                .bswan-input-row {
                    display: flex;
                    gap: 6px;
                    align-items: center;
                }

                .bswan-input-row input[type="text"] {
                    flex: 1;
                }

                .bswan-inline-label {
                    display: inline-flex;
                    align-items: center;
                    gap: 3px;
                    white-space: nowrap;
                    font-size: 12px;
                }

                .bswan-checkbox-row {
                    display: flex;
                    gap: 24px;
                    flex-wrap: wrap;
                    margin-top: 8px;
                }

                /* Buttons */
                .bswan-full-btn {
                    width: 100%;
                    display: flex !important;
                    align-items: center;
                    justify-content: center;
                    gap: 6px;
                }

                .bswan-btn-danger {
                    color: #d63638 !important;
                    border-color: rgba(214, 54, 56, 0.4) !important;
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                }

                .bswan-row-btn {
                    padding: 3px 7px !important;
                    min-height: 0 !important;
                    line-height: 1 !important;
                    display: inline-flex !important;
                    align-items: center;
                    border-radius: 6px !important;
                }

                .bswan-msg {
                    display: none;
                    font-weight: 600;
                    text-align: center;
                    font-size: 12px;
                    margin-top: 12px;
                }

                #bswan-columns .widefat td.bswan-actions {
                    text-align: right;
                    white-space: nowrap;
                }

                #bswan-columns .widefat .bswan-edit-input,
                #bswan-br-table .bswan-edit-input {
                    width: calc(100% - 10px);
                }

                @media(max-width:782px) {
                    #bswan-columns {
                        grid-template-columns: 1fr !important;
                    }
                }
            </style>

            <script>
                jQuery(function($) {
                    postboxes.add_postbox_toggles(pagenow);

                    var bl = <?php echo $blacklist_json; ?>,
                        wl = <?php echo $whitelist_json; ?>,
                        br = <?php echo $blocked_res_json; ?>;
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
                            blocked_resources: br
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
                                renderTable('bl');
                                renderTable('wl');
                                renderResTable();
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
                        $.post(ajaxurl, {
                            action: 'bswan_ber_save',
                            _nonce: nonce,
                            blacklist: bl,
                            whitelist: wl,
                            block_resources_backend: $('#bswan-res-backend').is(':checked') ? 1 : 0,
                            block_resources_frontend: $('#bswan-res-frontend').is(':checked') ? 1 : 0,
                            blocked_resources: bs
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