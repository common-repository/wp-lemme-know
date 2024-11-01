<?php

/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_test_email', 'wp_lemme_know_ajax_test_email_callback');
add_action('wp_ajax_subscribe', 'wp_lemme_know_ajax_subscribe_callback');
add_action('wp_ajax_nopriv_subscribe', 'wp_lemme_know_ajax_subscribe_callback');

function wp_lemme_know_ajax_test_email_callback()
{
    if (empty($_POST['mailFrom'])) {
        die (json_encode([
            'status' => 1, // e-mail from address is missing
            'results' => __('Please provide E-mail from address', 'wp-lemme-know'),
        ]));
    }

    $sender = new WP_LemmeKnowNotificationSender(
        $_POST['mailerType'] === 'default' ? false : true,
        $_POST['hostname'],
        $_POST['port'],
        $_POST['user'],
        $_POST['pass'],
        $_POST['encryption'],
        $_POST['authMode']
    );

    $body = wp_lemme_know_parse_body(stripcslashes($_POST['mailBody']));

    $sender
        ->setSubject($_POST['mailTitle'])
        ->setFrom($_POST['mailFrom'], $_POST['mailFromName'])
        ->setAddress($_POST['email'])
        ->setBody($body)
        ->setDebug(true);

    if ($sender->send() === true) {
        die (json_encode([
            'status' => 0, // e-mail was successfully sent
        ]));
    }

    die (json_encode([
        'status' => 1, // there were some issues when sending e-mail
        'results' => $sender->getDebugDetails(),
    ]));
}

function wp_lemme_know_ajax_subscribe_callback()
{
    global $wpdb;

    $email = esc_html(strtolower($_POST['email']));
    $tableName = $wpdb->prefix . 'lemme_know_subscribers';

    header('Content-Type: application/json');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die (json_encode([
            'status' => 1 // e-mail address is invalid
        ]));
    }

    $wpdb->get_results(sprintf(
        "SELECT * FROM `%s` WHERE `s_email`='%s'",
        $tableName,
        $email
    ));

    if ($wpdb->num_rows > 0) {
        die (json_encode([
            'status' => 2 // e-mail already exists
        ]));
    }

    $insertData = [
        's_email' => $email,
        's_ip' => ip2long(wp_lemme_know_get_ip()),
        's_confirmed' => 1,
        's_optin' => 'single',
        's_hash' => wp_lemme_know_get_hash($email)
    ];

    if ($wpdb->insert($tableName, $insertData) === false) {
        die (json_encode([
            'status' => 3 // issues when inserting e-mail address
        ]));
    }

    // sends e-mail notification
    if (WP_LemmeKnowDefaults::getInstance()->getOption('notifications_subscribe') === '1') {
        $adminEmail = get_option('admin_email');
        wp_mail(
            $adminEmail,
            sprintf(__('[Lemme Know] %s: New e-mail subscription', 'wp-lemme-know'), get_bloginfo('name')),
            sprintf(__('New e-mail has just been added: %s', 'wp-lemme-know'), $email)
        );
    }

    die (json_encode([
        'status' => 0, // e-mail was successfully added
    ]));
}

/**
 * Retrieves client IP using available methods.
 *
 * @return string
 */
function wp_lemme_know_get_ip() {
    foreach ([
         'HTTP_CLIENT_IP',
         'HTTP_X_FORWARDED_FOR',
         'HTTP_X_FORWARDED',
         'HTTP_X_CLUSTER_CLIENT_IP',
         'HTTP_FORWARDED_FOR',
         'HTTP_FORWARDED',
         'REMOTE_ADDR'] as $key){
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);

                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }

    return '';
}

/**
 * Generates hash based on the given $text.
 *
 * @param string $text
 *
 * @return string
 */
function wp_lemme_know_get_hash($text)
{
    return md5('S5d#2_!'.$text.'fi*d+@A');
}
