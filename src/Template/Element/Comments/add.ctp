<?= $this->Form->create(
    null,
    ['url' => ['controller' => 'Comments', 'action' => 'add']]
) ?>

<?= $this->Form->input(
    'comment',
    [
       'label' => false,
       'type' => 'textarea'
   ]
) ?>

<?= $this->Form->hidden(
    'thought_id',
    ['value' => $thoughtId]
) ?>

<?php if (isset($error_duplicate) && $error_duplicate): ?>
    <div class="error-message">
        It looks like you're trying to submit the same comment multiple times, so we stopped you.
        This sometimes happens if you click the submit button multiple times. If you refresh the
        page where this comment is supposed to show up, you should see it there.
    </div>
<?php endif; ?>

<?= $this->Form->submit(
    'Comment',
    ['class' => 'btn btn-default']
) ?>

<?= $this->Form->end() ?>

<a href="#" id="cancel_comment_t<?= $thoughtId ?>">
    Cancel
</a>

<?php $this->append('buffered_js'); ?>
    $('#cancel_comment_t<?= $thoughtId ?>').click(function (event) {
        event.preventDefault();
        comment.cancel(<?= $thoughtId ?>);
    });
<?php $this->end(); ?>