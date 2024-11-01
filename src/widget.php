<?php

/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_LemmeKnowWidget extends WP_Widget
{
    /** @var array */
    private $fields = [];

    public function __construct()
    {
        parent::__construct('LemmeKnowWidget', 'Lemme Know', [
            'classname' => 'LemmeKnowWidget',
            'description' => __('Displays subscription form used for sending notification when new post is published.', 'wp-lemme-know')
        ]);

        $this->fields = [
            'title' => [
                'type' => 'input',
                'caption' => __('Title', 'wp-lemme-know'),
                'value' => __('E-mail notifications', 'wp-lemme-know'),
            ],
            'description' => [
                'type' => 'text',
                'caption' => __('Description', 'wp-lemme-know'),
                'value' => __('Leave your e-mail here to receive notification every time when new post will be published. You can resign at any time.', 'wp-lemme-know'),
            ],
            'email' => [
                'type' => 'input',
                'caption' => __('E-mail input field title', 'wp-lemme-know'),
                'value' => __('E-mail:', 'wp-lemme-know'),
            ],
            'submit' => [
                'type' => 'input',
                'caption' => __('Subscribe button title', 'wp-lemme-know'),
                'value' => __('Subscribe', 'wp-lemme-know'),
            ],
            'success_msg' => [
                'type' => 'input',
                'caption' => __('Success message', 'wp-lemme-know'),
                'value' => __('Added, thank you!', 'wp-lemme-know'),
            ],
            'error_msg' => [
                'type' => 'input',
                'caption' => __('Error message', 'wp-lemme-know'),
                'value' => __('Issue occurs when submitting e-mail address, please try again later', 'wp-lemme-know'),
            ],
            'exists_msg' => [
                'type' => 'input',
                'caption' => __('E-mail exists message', 'wp-lemme-know'),
                'value' => __('This e-mail is already added', 'wp-lemme-know'),
            ],
            'invalid_msg' => [
                'type' => 'input',
                'caption' => __('Invalid e-mail message', 'wp-lemme-know'),
                'value' => __('E-mail address is incorrect', 'wp-lemme-know'),
            ],
        ];
    }

    public function form($instance)
    {
        $args = [];
        foreach ($this->fields as $fieldName => $data) {
            $args[$fieldName] = $data['value'];
        }
        $instance = wp_parse_args((array) $instance, $args);

        foreach ($this->fields as $fieldName => $data) {
            $fieldPattern = '';

            if ($data['type'] == 'input') {
                $fieldPattern = '<input class="widefat" id="%1$s" name="%2$s" type="text" value="%3$s" />';
            }

            if ($data['type'] == 'text') {
                $fieldPattern = '<textarea class="widefat" id="%1$s" name="%2$s" rows="10" cols="20">%3$s</textarea>';
            }

            $fieldLine = sprintf(
                $fieldPattern,
                $this->get_field_id($fieldName),
                $this->get_field_name($fieldName),
                $instance[$fieldName]
            );

            echo sprintf(
                '<p><label for="%2$s">%1$s:</label>%3$s</p>',
                $data['caption'],
                $this->get_field_id($fieldName),
                $fieldLine
            );
        }
    }

    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    public function widget($args, $instance)
    {
        $fieldSettings = [
            'widget_id' => 'lemme-know-'.substr(md5($this->id), 0, 6)
        ];

        foreach ($this->fields as $fieldName => $data) {
            $value = $instance[$fieldName];
            if ($fieldName == 'title') {
                $value = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
            }

            $fieldSettings[$fieldName] = $value;
        }

        require_once __DIR__.'/../templates/widget.php';
    }
}

add_action(
    'widgets_init',
    function() {
        register_widget('WP_LemmeKnowWidget');
    }
);
