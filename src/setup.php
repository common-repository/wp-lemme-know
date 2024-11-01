<?php

/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

if (!defined('ABSPATH')) {
    exit;
}

function wp_lemme_know_settings_link($links)
{
    $settingsLink = sprintf('<a href="options-general.php?page=wp-lemme-know">%s</a>', __('Settings', 'wp-lemme-know'));
    array_unshift($links, $settingsLink);

    return $links;
}

function wp_lemme_know_row_meta($meta, $file)
{
    if (strpos($file, 'plugin.php') !== false) {
        $meta = array_merge($meta, [
            sprintf('<a href="https://github.com/clash82/wp-lemme-know" target="_blank">%s</a>', __('Contribute', 'wp-lemme-know'))
        ]);
    }

    return $meta;
}

function wp_lemme_know_activation_callback()
{
    global $wpdb;
    $tableName = $wpdb->prefix . 'lemme_know_subscribers';

    // create additional table
    if ($wpdb->get_var(sprintf("SHOW TABLES LIKE '%s'", $tableName)) !== $tableName) {
        $charsetCollate = $wpdb->get_charset_collate();

        $sql = sprintf("CREATE TABLE %s (
                  s_id int(16) NOT NULL AUTO_INCREMENT,
                  s_email text NOT NULL,
                  s_hash varchar(32) NOT NULL,
                  s_confirmed enum('yes','no') NOT NULL DEFAULT 'no',
                  s_optin enum('single','double', 'manual') NOT NULL DEFAULT 'manual',
                  s_ip int(16) NOT NULL,
                  s_date timestamp DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY  (s_id)
                ) %s;",
            $tableName,
            $charsetCollate
        );

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    // save/update plugin options
    WP_LemmeKnowDefaults::getInstance()->updateSettings();
}

function wp_lemme_know_uninstall_callback()
{
    global $wpdb, $wp_rewrite;
    $tableName = $wpdb->prefix . 'lemme_know_subscribers';

    $wpdb->query(
        sprintf('DROP TABLE IF EXISTS `%s`', $tableName)
    );

    delete_option('wp_lemme_know_options');

    add_filter('rewrite_rules_array', 'wp_lemme_know_remove_rewrites');
    $wp_rewrite->flush_rules();
}

function wp_lemme_know_remove_rewrites($rules)
{
    foreach ($rules as $rule => $rewrite) {
        if (preg_match('lemme_know/unsubscribe/([a-z0-9]{32})/?$', $rule)) {
            unset($rules[$rule]);
        }
    }

    return $rules;
}
