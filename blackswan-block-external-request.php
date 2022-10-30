<?php
/*
Plugin Name: BlackSwan Block External Request
Description: Block External Requests made by WordPress in backend so admin-area will load much faster BUT you will lose some features that require sending external request like Getting latest Updates and ...
Author: BlackSwan
Author URI: https://amirhp.com
Plugin URI: https://blackswanlab.ir
Contributors: blackswanlab, amirhpcom
Donate link: https://amirhp.com/contact/#payment
Tags: blackswan, amirhp, woocommerce, attribute
Version: 1.1.0
Stable tag: 1.1.0
Requires PHP: 5.4
Tested up to: 6.0.3
Requires at least: 5.0
Text Domain: blackswan-block-external-request
Domain Path: /languages
Copyright: (c) amirhp.com, All rights reserved.
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
# @Last modified by:   amirhp-com <its@amirhp.com>
# @Last modified time: 2022/10/24 23:17:47
*/

namespace BlackSwan\WordPress;

defined("ABSPATH") or die("<h2>Unauthorized Access!</h2><hr><small>BlackSwan Block External Request Plugin :: Developed by Amirhp-com (<a href='https://amirhp.com/'>https://amirhp.com/</a>)</small>");
if (!class_exists("\BlackSwan\WordPress\blockExternalRequest")) {
    class blockExternalRequest
    {
        public $td;
        public $version;
        public $title;
        protected $block_url_list;
        public function __construct()
        {
            $this->td              = "blackswan-block-external-request";
            $this->version         = "1.1.0";
            load_plugin_textdomain($this->td, false, dirname(plugin_basename(__FILE__))."/languages/");
            $this->title          = __("Block External Request", "blackswan-block-external-request");
            $this->block_url_list = apply_filters( "BlackSwan/WordPress/blockExternalRequest/block_url_list", array(
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
            ));
			$this->whitelist_urls = apply_filters( "BlackSwan/WordPress/blockExternalRequest/whitelist_urls", array(
              "//api.wordpress.org/plugins/",
              "//downloads.wordpress.org/",
            ));
            add_filter("pre_http_request", array($this, "block_external_request"), 10, 3);
        }
        public function block_external_request($preempt, $parsed_args, $url)
        {
            foreach ($this->block_url_list as $block_url) {
              if (strpos($url, $block_url) !== false) {
				  
					foreach ($this->whitelist_urls as $unblock_url) {
						if (strpos($url, $unblock_url) !== false) {
						  return $preempt;
						}
					}
				  
					return new \WP_Error('http_request_block', __("This request is not allowed", "blackswan-block-external-request") . "\n:: {$url}", $url);
              }
            }

            return $preempt;
        }
    }
    add_action("plugins_loaded", function () {
        new \BlackSwan\WordPress\blockExternalRequest();
    });
}
/*##################################################
Lead Developer: [amirhp-com](https://amirhp.com/)
##################################################*/
