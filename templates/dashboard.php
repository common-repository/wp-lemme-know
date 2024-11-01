<h3><?= sprintf(__('Total count of subscriptions: %d', 'wp-lemme-know'), $settings['email_count']) ?></h3>

<?php if (count($settings['subscribers']) > 0): ?>
    <div class="wp-lemme-know-admin-dashboard-list">
        <table>
            <thead>
                <tr>
                    <th><?= __('Email address', 'wp-lemme-know') ?></th>
                    <th><?= __('Since', 'wp-lemme-know') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($settings['subscribers'] as $index => $subscriber): ?>
                    <tr>
                        <td><a href="mailto:<?= $subscriber['email'] ?>"><?= $subscriber['email'] ?></a></td>
                        <td>
                            <time datetime="<?= mysql2date('c', $subscriber['date']) ?>"><?= sprintf(
                                '%s, %s',
                                mysql2date(get_option('date_format'), $subscriber['date']),
                                mysql2date(get_option('time_format'), $subscriber['date'])
                            ) ?></time>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif ?>
