<?php

/*
Plugin Name: Lemme Know
Plugin URI:  https://github.com/clash82/wp-lemme-know
Description: Sends e-mail notification for subscribers when a new post is published.
Version:     0.10.2
Author:      Rafał Toborek
Author URI:  https://kontakt.toborek.info
License:     GPLv2
License URI: https://opensource.org/licenses/GPL-2.0
*/

if (!defined('ABSPATH')) {
    exit;
}

register_activation_hook(__FILE__, 'wp_lemme_know_activation_callback');
register_uninstall_hook(__FILE__, 'wp_lemme_know_uninstall_callback');

add_filter(
    sprintf('plugin_action_links_%s', plugin_basename(__FILE__)),
    'wp_lemme_know_settings_link'
);

add_filter('plugin_row_meta', 'wp_lemme_know_row_meta', 10, 2);

if (!function_exists('get_plugins')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$plugin_folder = get_plugins('/' . plugin_basename(dirname(__FILE__)));
$plugin_file = basename((__FILE__));
$pluginData['Version'] =  $plugin_folder[$plugin_file]['Version'];

require_once 'src/defaults.php';

function wp_lemme_know_enqueue_script()
{
    global $pluginData;

    wp_enqueue_script(
        'wp-lemme-know-javascript',
        plugin_dir_url(__FILE__).'assets/js/lemme-know.js',
        false,
        $pluginData['Version'],
        false
    );

    if (WP_LemmeKnowDefaults::getInstance()->getOption('embed_css') === '1') {
        wp_enqueue_style(
            'wp-lemme-know-style',
            plugin_dir_url(__FILE__) . 'assets/css/style.css',
            false,
            $pluginData['Version'],
            'all'
        );
    }
}
add_action('wp_enqueue_scripts', 'wp_lemme_know_enqueue_script');

if (is_admin()) {
    require_once 'src/setup.php';
    require_once 'src/settings.php';
    require_once 'src/dashboard.php';
}

require_once 'src/unsubscribe.php';
require_once 'src/widget.php';
require_once 'src/publish.php';
require_once 'src/ajax.php';
require_once 'src/sender.php';

/**
 * Jezus żyje! 🧡
 */
