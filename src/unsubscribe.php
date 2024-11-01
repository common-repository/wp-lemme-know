<?php

/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

if (!defined('ABSPATH')) {
    exit;
}

// adding a new rule
add_filter('rewrite_rules_array', 'wp_lemme_know_insert_rewrite_rules');

function wp_lemme_know_insert_rewrite_rules($rules)
{
    $newRules = [];
    $newRules['lemme_know/unsubscribe/([a-z0-9]{32})/?$'] = 'index.php?lemme-know=unsubscribe&hash=$matches[1]';

    return $newRules + $rules;
}

// adding the hash var so that WP recognizes it
add_filter('query_vars', 'wp_lemme_know_insert_query_vars');

function wp_lemme_know_insert_query_vars($vars)
{
    array_push($vars, 'lemme-know', 'hash');

    return $vars;
}

// flush_rules() if rules are not yet included
add_action('wp_loaded', 'wp_lemme_know_flush_rules');

function wp_lemme_know_flush_rules()
{
    $rules = get_option('rewrite_rules');

    if (!isset($rules['lemme_know/unsubscribe/([a-z0-9]{32})/?$'])) {
        global $wp_rewrite;

        $wp_rewrite->flush_rules();
    }
}

// registers plugin endpoint (perform dirty job)
add_action('parse_request', 'wp_lemme_know_parse_request');

function wp_lemme_know_parse_request($wp)
{
    global $wpdb;

    if (array_key_exists('lemme-know', $wp->query_vars)
        && $wp->query_vars['lemme-know'] === 'unsubscribe'
        && array_key_exists('hash', $wp->query_vars)
        && strlen($wp->query_vars['hash']) === 32) {

        $tableName = $wpdb->prefix . 'lemme_know_subscribers';
        $hash = $wp->query_vars['hash'];

        $user = $wpdb->get_results(sprintf(
            "SELECT * FROM `%s` WHERE `s_hash`='%s'",
            $tableName,
            $hash
        ));

        if (empty($wpdb->num_rows)) {
            wp_die(
                __('This address has been already removed from our subscription list.', 'wp-lemme-know'),
                __('Unsubscribe', 'wp-lemme-know')
            );
        }

        $wpdb->delete($tableName, ['s_hash' => $hash]);

        // sends e-mail notification
        if (WP_LemmeKnowDefaults::getInstance()->getOption('notifications_unsubscribe') === '1') {
            $adminEmail = get_option('admin_email');
            wp_mail(
                $adminEmail,
                sprintf(__('[Lemme Know] %s: Unsubscription', 'wp-lemme-know'), get_bloginfo('name')),
                sprintf(__('Existing e-mail has just been removed: %s', 'wp-lemme-know'), $user[0]->s_email)
            );
        }

        wp_die(
            __('You have been successfully removed from our subscription list.', 'wp-lemme-know'),
            __('Unsubscribe', 'wp-lemme-know')
        );
    }
}
