<?php
/*
  Plugin Name: bbPress Product Tab
  Plugin URI: https://wordpress.org/plugins/product-tab-bbpress-woocommerce/
  Description: Add Tabs in Product Settings Page. Show Forum Topics in Relative Product Page.
  Author: wpfloor
  Author URI: http://www.wpfloor.com/
  Version:2.0.3
  Text Domain: eh_bb_product_tab
  WC Tested up to: 3.3.1
 */

if (!defined('ABSPATH')) {
    return;
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_notices', 'eh_wc_admin_notices', 99);
    deactivate_plugins(plugin_basename(__FILE__));

    function eh_wc_admin_notices() {
        is_admin() && add_filter('gettext', function($translated_text, $untranslated_text, $domain) {
                    $old = array(
                        "Plugin <strong>activated</strong>.",
                        "Selected plugins <strong>activated</strong>."
                    );
                    //Error Text for Version Identification
                    $error_text = "bbPress Product Tab requires Woocommerce to be installed!";
                    $new = "<span style='color:red'>" . $error_text . "</span>";
                    if (in_array($untranslated_text, $old, true)) {
                        $translated_text = $new;
                    }
                    return $translated_text;
                }, 99, 3);
    }

    return;
}

if (!in_array('bbpress/bbpress.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_notices', 'eh_wc_admin_notices', 99);
    deactivate_plugins(plugin_basename(__FILE__));

    function eh_wc_admin_notices() {
        is_admin() && add_filter('gettext', function($translated_text, $untranslated_text, $domain) {
                    $old = array(
                        "Plugin <strong>activated</strong>.",
                        "Selected plugins <strong>activated</strong>."
                    );
                    //Error Text for Version Identification
                    $error_text = "bbPress Product Tab requires bbPress to be installed!";
                    $new = "<span style='color:red'>" . $error_text . "</span>";
                    if (in_array($untranslated_text, $old, true)) {
                        $translated_text = $new;
                    }
                    return $translated_text;
                }, 99, 3);
    }

    return;
}

if (!defined('ABSPATH')) {
    return;
}

    register_deactivation_hook(__FILE__, 'bb_product_tab_bb_deactivate_work');

    // Enter your plugin unique option name below update_option function
    function bb_product_tab_bb_deactivate_work() {
        update_option('bb_product_tab_option', '');
    }

    if (!class_exists('EH_BB_Product_Tab_Main')) {

        /**
         * Main CSV Import class
         */
        class EH_BB_Product_Tab_Main {

            /**
             * Constructor
             */
            public function __construct() {

                if (is_admin()) {
                    require_once ( 'includes/eh_bbPress_settings.php' );
                    require_once ( 'includes/eh_bb_tab_settings_add.php' );
                }

                add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'eh_bbPress_Product_tab_plugin_link'));
                add_filter('woocommerce_product_tabs', array($this, 'new_product_tab'));
            }

            public function eh_bbPress_Product_tab_plugin_link($links) {
                $setting_link = admin_url('admin.php?page=wc-settings&tab=bb_settings_tab');
                $plugin_links = array(
                    '<a href="' . $setting_link . '">' . __('Settings', 'eh_bb_product_tab') . '</a>',
                    '<a href="http://www.wpfloor.com/setting-product-tab-bbpress/" target="_blank">' . __('Documentation', 'eh_bb_product_tab') . '</a>',
                    '<a href="https://wordpress.org/support/plugin/product-tab-bbpress-woocommerce" target="_blank">' . __('Support', 'eh_bb_product_tab') . '</a>',
                    '<a href="http://www.wpfloor.com/" target="_blank">' . __('wpfloor', 'hf_bb_import_export') . '</a>'
                );
                return array_merge($plugin_links, $links);
            }

            public function new_product_tab($tabs) {
                /* Adds the new tab */
                global $woocommerce, $post, $wpdb;
                $post_id = get_the_ID();

                $input_global_get = (int) get_post_meta($post_id, '_eh_bb_tab_global', true);
                $input_global_set = !empty($input_global_get) ? $input_global_get : true; // set default value

                $my_selector_get = (int) get_post_meta($post_id, '_eh_bb_tab_forum', true);
                $my_selector_data = !empty($my_selector_get) ? $my_selector_get : 0; // set default value

                $input_post_count_get = get_option('wc_bb_settings_tab_view_post_count');
                $input_post_count_set = !empty($input_post_count_get) ? $input_post_count_get : 'yes'; // set default value


                if ($my_selector_data === 0) {
                    return;
                }

                if ($input_global_set === 1) {
                    $input_text_get = get_option('wc_bb_settings_tab_title');
                    $input_text_data = !empty($input_text_get) ? $input_text_get : 'Comments'; // set default value

                    $input_priority_get = (int) get_option('wc_bb_settings_tab_priority');
                    $input_priority = !empty($input_priority_get) ? $input_priority_get : '50'; // set default value
                } else {
                    $input_text_get = get_post_meta($post_id, '_eh_bb_tab_title', true);
                    $input_text_data = !empty($input_text_get) ? $input_text_get : 'Comments'; // set default value

                    $input_priority_get = (int) get_post_meta($post_id, '_eh_bb_tab_priority', true);
                    $input_priority = !empty($input_priority_get) ? $input_priority_get : '50'; // set default value
                }
                $query = "SELECT count(*) FROM $wpdb->postmeta pm INNER JOIN $wpdb->posts p ON p.id=pm.post_id WHERE pm.meta_key='_bbp_forum_id' and pm.meta_value = '" . $my_selector_data . "' and p.post_status='publish' ";
                $total_count = $wpdb->get_var($query);

                if ($input_post_count_set === 'yes') {
                    $input_text_data .= ' (' . $total_count . ')';
                }

                $tabs['test_tab'] = array(
                    'title' => __($input_text_data, 'eh_bb_product_tab'),
                    'priority' => $input_priority,
                    'callback' => array($this, 'new_product_tab_content')
                );
                return $tabs;  /* Return all  tabs including the new New Custom Product Tab  to display */
            }

            public function new_product_tab_content() {
                /* The new tab content */
                global $woocommerce, $post;
                $post_id = get_the_ID();
                include_once ( 'includes/views/html-eh-bb-product-discussion-fetch.php' );
            }

        }

    }

    new EH_BB_Product_Tab_Main();