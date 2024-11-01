<?php

/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('transition_post_status', 'wp_lemme_know_publish_callback', 10, 3);

function wp_lemme_know_publish_callback($newStatus, $oldStatus, $post)
{
    // we do not accept updates other than `post` type
    if ('post' !== get_post_type($post)) {
        return;
    }

    // only `publish` status is accepted
    if ('publish' !== $newStatus
        || 'publish' === $oldStatus) {
        return;
    }

    wp_lemme_know_send(
        wp_lemme_know_get_subscribers(),
        $post
    );
}

/**
 * Sends e-mails.
 *
 * @param array $subscribers
 * @param WP_Post $post
 */
function wp_lemme_know_send($subscribers, $post)
{
    $options = WP_LemmeKnowDefaults::getInstance();

    if (empty($options->getOption('mail_from'))) {
        return;
    }

    $sender = new WP_LemmeKnowNotificationSender(
        $options->getOption('mailer_type') === 'smtp',
        $options->getOption('smtp_host'),
        $options->getOption('smtp_port'),
        $options->getOption('smtp_user'),
        $options->getOption('smtp_pass'),
        $options->getOption('smtp_encryption'),
        $options->getOption('smtp_auth_mode')
    );

    $sender
        ->setFrom(
            $options->getOption('mail_from'),
            $options->getOption('mail_from_name')
        )
        ->setSubject($options->getOption('mail_title'));

    foreach ($subscribers as $item) {
        $sender
            ->setAddress($item['email'])
            ->setBody(wp_lemme_know_parse_body(
                $options->getOption('mail_body'),
                $post,
                $item['hash']
            ))
            ->send();
    }
}

/**
 * Retrieves subscribers.
 *
 * @return array
 */
function wp_lemme_know_get_subscribers()
{
    global $wpdb;

    $tableName = $wpdb->prefix.'lemme_know_subscribers';

    $queryResults = $wpdb->get_results(sprintf(
        "SELECT `s_email`, `s_hash` FROM `%s` WHERE `s_confirmed`='yes'", $tableName
    ));

    $results = [];
    foreach ($queryResults as $subscriber) {
        $results[] = [
            'email' => $subscriber->s_email,
            'hash' => $subscriber->s_hash
        ];
    }

    return $results;
}

/**
 * Parses body template by injecting $post details.
 *
 * @param string $body
 * @param WP_Post|null $post
 * @param string|null $hash
 *
 * @return string
 */
function wp_lemme_know_parse_body($body, $post = null, $hash = null)
{
    return str_replace([
        '{{post_title}}',
        '{{post_body}}',
        '{{post_excerpt}}',
        '{{post_date}}',
        '{{post_author}}',
        '{{post_url}}',
        '{{unsubscribe_url}}',
    ], [
        $post ? $post->post_title : __('Example title', 'wp-lemme-know'),
        $post ? nl2br(trim($post->post_content)) : __('Example content', 'wp-lemme-know'),
        $post ? nl2br(trim($post->post_excerpt)) : __('Example excerpt', 'wp-lemme-know'),
        $post ? $post->post_date : __('Example post date', 'wp-lemme-know'),
        $post ? $post->post_author : __('Example post author', 'wp-lemme-know'),
        $post ? get_permalink($post) : '#',
        sprintf('%s/lemme_know/unsubscribe/%s/', get_site_url(), $hash ? $hash : __('real-hash-will-be-placed-here', 'wp-lemme-know'))
    ], $body);
}
