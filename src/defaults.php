<?php

/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_LemmeKnowDefaults
{
    const WP_LEMME_KNOW_TAB_GENERAL = null;
    const WP_LEMME_KNOW_TAB_MAIL_SETTINGS = 'mail-settings';
    const WP_LEMME_KNOW_TAB_NOTIFICATIONS = 'notifications';

    /** @var WP_LemmeKnowDefaults */
    private static $instance;

    /** @var array */
    private $options = [];

    private function __construct()
    {
        ob_start();
        require_once __DIR__.'/../templates/email_body.php';
        $mailBody = ob_get_contents();
        ob_end_clean();

        $this->options = [
            'embed_css' => '1',
            'mail_title' => __('New post has just been published', 'wp-lemme-know'),
            'mail_from' => '',
            'mail_from_name' => __('Your postman', 'wp-lemme-know'),
            'mail_body' => $mailBody,
            'mailer_type' => 'default',
            'smtp_host' => '',
            'smtp_port' => '25',
            'smtp_auth_mode' => 'LOGIN',
            'smtp_encryption' => '',
            'smtp_user' => '',
            'smtp_pass' => '',
            'notifications_subscribe' => '1',
            'notifications_unsubscribe' => '1',
        ];
    }

    public static function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns all options with defaults or values provided by the user.
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [];

        foreach ($this->options as $name => $value) {
            $options[$name] = $this->getOption($name);
        }

        return $options;
    }

    /**
     * Returns default option value.
     *
     * @param string $name
     * @param null|string $value
     *
     * @return string
     */
    public function getOption($name, $value = null)
    {
        if (is_null($value) && isset($this->options[$name]))
        {
            $value = $this->options[$name];
        }

        $options = get_option('wp_lemme_know_options', [
            $name => $value
        ]);

        if (isset($options[$name])) {
            return $options[$name];
        }

        return '';
    }

    /**
     * Update (or create if not exists) plugin settings.
     */
    public function updateSettings()
    {
        global $wpdb;

        $options = [];
        foreach ($this->options as $name => $value) {
            $options[$name] = $this->getOption($name);
        }

        $tableName = $wpdb->prefix.'options';
        $wpdb->replace($tableName, [
            'option_name' => 'wp_lemme_know_options',
            'option_value' => serialize($options)
        ], [
            '%s',
            '%s'
        ]);
    }
}
