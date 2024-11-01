<?php

/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'admin_enqueue_scripts',
    'wp_lemme_know_admin_enqueue_script'
);

function wp_lemme_know_admin_enqueue_script()
{
    global $pluginData;

    wp_register_style(
        'wp-lemme-know-admin-style',
        plugin_dir_url(__FILE__).'../assets/css/style-admin.css',
        false,
        $pluginData['Version']
    );
    wp_enqueue_style('wp-lemme-know-admin-style');

    wp_enqueue_script(
        'wp-lemme-know-admin-javascript',
        plugin_dir_url(__FILE__).'../assets/js/lemme-know-admin.js',
        false,
        $pluginData['Version'],
        false
    );
}

add_action(
    'admin_menu',
    function() {
        return add_options_page(
            'Lemme Know',
            'Lemme Know',
            'manage_options',
            'wp-lemme-know',
            'wp_lemme_know_options_page'
        );
    }
);

function wp_lemme_know_options_page()
{
    require_once __DIR__.'/../templates/settings.php';
}

add_action(
    'admin_init',
    'wp_lemme_know_admin_init'
);

function wp_lemme_know_admin_init()
{
    register_setting(
        'wp_lemme_know_options',
        'wp_lemme_know_options',
        'wp_lemme_know_validate_callback'
    );

    $tab = isset($_GET['tab']) ? $_GET['tab'] : null;

    if ($tab === WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_GENERAL) {
        add_settings_section(
            'wp_lemme_know_options_general',
            null,
            'wp_lemme_know_general_callback',
            'wp_lemme_know_plugin'
        );
        add_settings_field(
            'styling',
            __('Styling the widget', 'wp-lemme-know'),
            'wp_lemme_know_styling_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_general'
        );
    }

    if ($tab === WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_MAIL_SETTINGS) {
        add_settings_section(
            'wp_lemme_know_options_mail',
            null,
            'wp_lemme_know_mail_callback',
            'wp_lemme_know_plugin'
        );
        add_settings_field(
            'mail_title',
            __('E-mail title', 'wp-lemme-know'),
            'wp_lemme_know_mail_title_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_mail'
        );
        add_settings_field(
            'mail_from',
            __('E-mail from address', 'wp-lemme-know'),
            'wp_lemme_know_mail_from_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_mail'
        );
        add_settings_field(
            'mail_from_name',
            __('E-mail from name', 'wp-lemme-know'),
            'wp_lemme_know_mail_from_name_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_mail'
        );
        add_settings_field(
            'mail_body',
            __('E-mail body (html)', 'wp-lemme-know'),
            'wp_lemme_know_mail_body_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_mail'
        );
        add_settings_field(
            'mailer',
            __('Mailer type', 'wp-lemme-know'),
            'wp_lemme_know_mailer_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_mail'
        );

        // SMTP settings
        add_settings_section(
            'wp_lemme_know_options_smtp',
            __('SMTP settings', 'wp-lemme-know'),
            'wp_lemme_know_smtp_callback',
            'wp_lemme_know_plugin'
        );
        add_settings_field(
            'smtp_host',
            __('Hostname', 'wp-lemme-know'),
            'wp_lemme_know_smtp_host_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_smtp'
        );
        add_settings_field(
            'smtp_port',
            __('Port number', 'wp-lemme-know'),
            'wp_lemme_know_smtp_port_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_smtp'
        );
        add_settings_field(
            'smtp_auth_mode',
            __('Authentication', 'wp-lemme-know'),
            'wp_lemme_know_smtp_auth_mode_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_smtp'
        );
        add_settings_field(
            'smtp_encryption',
            __('Encryption', 'wp-lemme-know'),
            'wp_lemme_know_smtp_encryption_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_smtp'
        );
        add_settings_field(
            'smtp_user',
            __('Username', 'wp-lemme-know'),
            'wp_lemme_know_smtp_user_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_smtp'
        );
        add_settings_field(
            'smtp_pass',
            __('Password', 'wp-lemme-know'),
            'wp_lemme_know_smtp_pass_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_smtp'
        );

        // tests
        add_settings_section(
            'wp_lemme_know_options_tests',
            __('Tests', 'wp-lemme-know'),
            'wp_lemme_know_tests_callback',
            'wp_lemme_know_plugin'
        );
        add_settings_field(
            'test_email',
            __('Provide an e-mail address to send an example notification', 'wp-lemme-know'),
            'wp_lemme_know_test_email_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_tests'
        );
    }

    if ($tab === WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_NOTIFICATIONS) {
        add_settings_section(
            'wp_lemme_know_options_notifications',
            null,
            'wp_lemme_know_notifications_callback',
            'wp_lemme_know_plugin'
        );
        add_settings_field(
            'mail_notify',
            __('New subscriptions', 'wp-lemme-know'),
            'wp_lemme_know_mail_notify_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_notifications'
        );
        add_settings_field(
            'mail_unsubscribe_notify',
            __('Unsubscribe', 'wp-lemme-know'),
            'wp_lemme_know_mail_unsubscribe_notify_callback',
            'wp_lemme_know_plugin',
            'wp_lemme_know_options_notifications'
        );
    }
};

function wp_lemme_know_validate_callback($input)
{
    $options = WP_LemmeKnowDefaults::getInstance()->getAllOptions();

    foreach ($options as $name => $v) {
        if (isset($input[$name])) {
            $options[$name] = sanitize_text_field($input[$name]);
        }
    }

    return $options;
}

function wp_lemme_know_general_callback()
{
    printf(
        '<p>%s</p>',
        __('Those settings have impact on all created widgets.', 'wp-lemme-know')
    );
}

function wp_lemme_know_styling_callback()
{
    printf(
        '<label for="wp-lemme-know-options-embed-css"><input type="checkbox" id="wp-lemme-know-options-embed-css" name="wp_lemme_know_options[embed_css]" value="1" %s /> %s</label>',
        checked(1, WP_LemmeKnowDefaults::getInstance()->getOption('embed_css'), false),
        __('Embed default CSS provided with this plugin (disable if you want to style the widgets by yourself)', 'wp-lemme-know')
    );
}

function wp_lemme_know_mail_callback()
{
    printf(
        '<p>%s</p>',
        __('Essential settings required for sending e-mail notifications.', 'wp-lemme-know')
    );
}

function wp_lemme_know_mail_title_callback()
{
    printf(
        '<input type="text" id="wp-lemme-know-options-mail-title" name="wp_lemme_know_options[mail_title]" value="%s" class="regular-text ltr" /><p class="description">%s</p>',
        WP_LemmeKnowDefaults::getInstance()->getOption('mail_title'),
        __('text will be used as a title for e-mail notifications', 'wp-lemme-know')
    );
}

function wp_lemme_know_mail_from_callback()
{
    printf(
        '<input type="text" id="wp-lemme-know-options-mail-from" name="wp_lemme_know_options[mail_from]" value="%s" class="regular-text ltr" /><p class="description">%s</p>',
        WP_LemmeKnowDefaults::getInstance()->getOption('mail_from'),
        __('if empty then no messages will be sent (useful if you want to temporary disable e-mail sending)', 'wp-lemme-know')
    );
}

function wp_lemme_know_mail_from_name_callback()
{
    printf(
        '<input type="text" id="wp-lemme-know-options-mail-from-name" name="wp_lemme_know_options[mail_from_name]" value="%s" class="regular-text ltr" />',
        WP_LemmeKnowDefaults::getInstance()->getOption('mail_from_name')
    );
}

function wp_lemme_know_mail_body_callback()
{
    printf(
        '<textarea id="wp-lemme-know-options-mail-body" name="wp_lemme_know_options[mail_body]" class="large-text" rows="10" cols="50">%s</textarea><p class="description">%s</p>',
        WP_LemmeKnowDefaults::getInstance()->getOption('mail_body'),
        __('available short codes are: {{post_title}}, {{post_body}}, {{post_excerpt}}, {{post_date}}, {{post_author}}, {{post_url}} and {{unsubscribe_url}}', 'wp-lemme-know')
    );
}

function wp_lemme_know_mailer_callback()
{
    printf(
        '<label for="wp-lemme-know-options-mailer-default"><input type="radio" id="wp-lemme-know-options-mailer-default" name="wp_lemme_know_options[mailer_type]" value="default" %s /> %s</label>',
        checked('default', WP_LemmeKnowDefaults::getInstance()->getOption('mailer_type'), false),
        __('Use built-in mail() function', 'wp-lemme-know')
    );

    echo '<br />';

    printf(
        '<label for="wp-lemme-know-options-mailer-smtp"><input type="radio" id="wp-lemme-know-options-mailer-smtp" name="wp_lemme_know_options[mailer_type]" value="smtp" %s /> %s</label><p class="description">%s</p>',
        checked('smtp', WP_LemmeKnowDefaults::getInstance()->getOption('mailer_type'), false),
        __('Use external SMTP server', 'wp-lemme-know'),
        __('recommended but requires additional SMTP parameters described below', 'wp-lemme-know')
    );
}

function wp_lemme_know_smtp_callback()
{
    printf(
        '<p>%s</p>',
        __('Additional parameters required for using external SMTP server.', 'wp-lemme-know')
    );
}


function wp_lemme_know_smtp_host_callback()
{
    printf(
        '<input type="text" id="wp-lemme-know-options-smtp-host" name="wp_lemme_know_options[smtp_host]" value="%s" class="regular-text ltr" /><p class="description">%s</p>',
        WP_LemmeKnowDefaults::getInstance()->getOption('smtp_host'),
        __('eg. mail.example.com', 'wp-lemme-know')
    );
}

function wp_lemme_know_smtp_port_callback()
{
    printf(
        '<input type="number" id="wp-lemme-know-options-smtp-port" name="wp_lemme_know_options[smtp_port]" value="%s" class="regular-text ltr" /><p class="description">%s</p>',
        WP_LemmeKnowDefaults::getInstance()->getOption('smtp_port'),
        __('eg. 25, 587 (TLS) or 467 (SSL)', 'wp-lemme-know')
    );
}

function wp_lemme_know_smtp_auth_mode_callback()
{
    printf('<select id="wp-lemme-know-options-smtp-auth-mode" name="wp_lemme_know_options[smtp_auth_mode]"><option value="PLAIN" %s>%s</option><option value="LOGIN" %s>%s</option><option value="CRAM-MD5" %s>%s</option></select>',
        selected(WP_LemmeKnowDefaults::getInstance()->getOption('smtp_auth_mode'), 'PLAIN', false),
        'PLAIN',
        selected(WP_LemmeKnowDefaults::getInstance()->getOption('smtp_auth_mode'), 'LOGIN', false),
        'LOGIN',
        selected(WP_LemmeKnowDefaults::getInstance()->getOption('smtp_auth_mode'), 'CRAM-MD5', false),
        'CRAM-MD5'
    );
}

function wp_lemme_know_smtp_encryption_callback()
{
    printf('<select id="wp-lemme-know-options-smtp-encryption" name="wp_lemme_know_options[smtp_encryption]"><option value="" %s>%s</option>><option value="tls" %s>%s</option><option value="ssl" %s>%s</option></select>',
        selected(WP_LemmeKnowDefaults::getInstance()->getOption('smtp_encryption'), '', false),
        __('none', 'wp-lemme-know'),
        selected(WP_LemmeKnowDefaults::getInstance()->getOption('smtp_encryption'), 'tls', false),
        'TLS',
        selected(WP_LemmeKnowDefaults::getInstance()->getOption('smtp_encryption'), 'ssl', false),
        'SSL'
    );
}

function wp_lemme_know_smtp_user_callback()
{
    printf(
        '<input type="text" id="wp-lemme-know-options-smtp-user" name="wp_lemme_know_options[smtp_user]" value="%s" class="regular-text ltr" />',
        WP_LemmeKnowDefaults::getInstance()->getOption('smtp_user')
    );
}

function wp_lemme_know_smtp_pass_callback()
{
    printf(
        '<input type="password" id="wp-lemme-know-options-smtp-pass" name="wp_lemme_know_options[smtp_pass]" value="%s" class="regular-text ltr" />',
        WP_LemmeKnowDefaults::getInstance()->getOption('smtp_pass')
    );
}

function wp_lemme_know_tests_callback()
{
    printf(
        '<p>%s</p>',
        __('Use this option to test provided configuration, an example e-mail message will be sent. Be aware that the current on-screen configuration will be used (not the saved one). Remember also that this tool allows you only to check if SMTP configuration is correct. In case of a mail() function, you will not be able to know if message was sent correctly, check your e-mail inbox instead.', 'wp-lemme-know')
    );
}

function wp_lemme_know_test_email_callback()
{
    printf(
        '<input type="text" id="wp-lemme-know-admin-test-email" class="regular-text ltr" placeholder="%s" />
        <br /><br />
        <button type="button" id="wp-lemme-know-admin-test-send" class="button button-primary">%s</button>
        <div id="wp-lemme-know-admin-test-results" class="wp-lemme-know-admin-test-results">Results</div>
        <script>
            (function() {
                new clash82.LemmeKnowAdmin({
                    adminAjaxUrl: "%sadmin-ajax.php",
                    sendingMsg: "%s",
                    successMsg: "%s",
                    errorMsg: "%s",
                    internalErrorMsg: "%s"
                });
            }) ();
        </script>',
        __('email@example.com', 'wp-lemme-know'),
        __('Send e-mail notification now', 'wp-lemme-know'),
        get_admin_url(),
        __('Sending test message, please wait...', 'wp-lemme-know'),
        __('Congratulations! test e-mail was sent, configuration is correct', 'wp-lemme-know'),
        __('ERROR', 'wp-lemme-know').': '.__("couldn't send an email using current settings", 'wp-lemme-know'),
        __('ERROR', 'wp-lemme-know').': '.__('internal error occurred', 'wp-lemme-know')
    );
}

function wp_lemme_know_notifications_callback()
{
    printf(
        '<p>%s</p>',
        __('Internal e-mail notifications.', 'wp-lemme-know')
    );
}

function wp_lemme_know_mail_notify_callback()
{
    printf(
        '<label for="wp-lemme-know-options-notifications-subscribe"><input type="checkbox" id="wp-lemme-know-options-notifications-subscribe" name="wp_lemme_know_options[notifications_subscribe]" value="1" %s /> %s</label>',
        checked(1, WP_LemmeKnowDefaults::getInstance()->getOption('notifications_subscribe'), false),
        __('Notify Administrator about the new subscriptions', 'wp-lemme-know')
    );
}

function wp_lemme_know_mail_unsubscribe_notify_callback()
{
    printf(
        '<label for="wp-lemme-know-options-notifications-unsubscribe"><input type="checkbox" id="wp-lemme-know-options-notifications-unsubscribe" name="wp_lemme_know_options[notifications_unsubscribe]" value="1" %s /> %s</label>',
        checked(1, WP_LemmeKnowDefaults::getInstance()->getOption('notifications_unsubscribe'), false),
        __('Notify Administrator when user unsubscribe', 'wp-lemme-know')
    );
}
