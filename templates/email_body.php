<?= __('New post has just been published:', 'wp-lemme-know'); ?>
<br><br>

<a href="{{post_url}}" target="_blank">{{post_title}}</a>
<br>
<p>{{post_excerpt}}</p>
<br><br>

<a href="{{post_url}}" target="_blank"><?= __('Read more', 'wp-lemme-know'); ?> &raquo;</a>
<br><br>

<?= sprintf(__("If you don't want to receive messages like this in the future, please %s.", 'wp-lemme-know'),
    sprintf('<a href="{{unsubscribe_url}}" target="_blank">%s</a>', __('unsubscribe', 'wp-lemme-know'))); ?>
