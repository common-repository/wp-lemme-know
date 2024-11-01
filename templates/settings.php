<div class="wrap">
    <h1><?= __('Lemme Know settings', 'wp-lemme-know'); ?></h1>
    <p><?= __('Notify users every time when new posts are published.', 'wp-lemme-know'); ?></p>

    <?php $tab = isset($_GET['tab']) ? $_GET['tab'] : null; ?>

    <form action="options.php" method="post">
        <div class="wrap">
            <nav class="nav-tab-wrapper">
                <a href="?page=<?= $_GET['page'] ?>" class="nav-tab <?php if ($tab === WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_GENERAL): ?>nav-tab-active<?php endif; ?>"><?= __('General', 'wp-lemme-know') ?></a>
                <a href="?page=<?= $_GET['page'] ?>&tab=<?= WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_MAIL_SETTINGS ?>" class="nav-tab <?php if($tab === WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_MAIL_SETTINGS):?>nav-tab-active<?php endif; ?>"><?= __('Mail settings', 'wp-lemme-know') ?></a>
                <a href="?page=<?= $_GET['page'] ?>&tab=<?= WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_NOTIFICATIONS ?>" class="nav-tab <?php if ($tab === WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_NOTIFICATIONS): ?>nav-tab-active<?php endif; ?>"><?= __('Notifications', 'wp-lemme-know') ?></a>
            </nav>

            <div class="tab-content">
                <?php
                    /**
                     * Hard way to preserve checkbox options when checkbox is unchecked and submitted.
                     */
                    function wp_lemme_know_print_default_checkbox_value(array $checkboxes = [])
                    {
                        foreach ($checkboxes as $name) {
                            printf('<input type="hidden" name="%s" value="0" />', $name);
                        }
                    }

                    if ($tab === WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_GENERAL) {
                        wp_lemme_know_print_default_checkbox_value([
                            'wp_lemme_know_options[embed_css]',
                        ]);
                    }

                    if ($tab === WP_LemmeKnowDefaults::WP_LEMME_KNOW_TAB_NOTIFICATIONS) {
                        wp_lemme_know_print_default_checkbox_value([
                            'wp_lemme_know_options[notifications_subscribe]',
                            'wp_lemme_know_options[notifications_unsubscribe]',
                        ]);
                    }
                ?>
                <?php settings_fields('wp_lemme_know_options'); ?>
                <?php do_settings_sections('wp_lemme_know_plugin'); ?>
            </div>
        </div>

        <input name="submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
</div>
