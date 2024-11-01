<div class="wp-lemme-know-widget" id="<?= $fieldSettings['widget_id']; ?>">
    <div class="wp-lemme-know-widget-description">
        <?= $fieldSettings['description']; ?>
    </div>

    <div class="wp-lemme-know-widget-status">
    </div>

    <fieldset>
        <label for="<?= $fieldSettings['widget_id']; ?>-email">
            <?= $fieldSettings['email']; ?>
        </label>
        <input type="email" id="<?= $fieldSettings['widget_id']; ?>-email" />

        <button type="button">
            <?= $fieldSettings['submit']; ?>
        </button>
    </fieldset>
</div>

<script>
    (function() {
        new clash82.LemmeKnow({
            adminAjaxUrl: '<?= sprintf('%sadmin-ajax.php', get_admin_url()); ?>',
            widgetId: '<?= $fieldSettings['widget_id']; ?>',
            errorMsg: '<?= $fieldSettings['error_msg']; ?>',
            existsMsg: '<?= $fieldSettings['exists_msg']; ?>',
            successMsg: '<?= $fieldSettings['success_msg']; ?>',
            invalidMsg: '<?= $fieldSettings['invalid_msg']; ?>'
        });
    }) ();
</script>
