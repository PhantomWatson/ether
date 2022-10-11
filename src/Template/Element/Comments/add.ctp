<?php
/**
 * @var \App\View\AppView $this
 * @var array $thought
 * @var int $userId
 */
// Modify the output of FormHelper::radio() so that <label>s appear adjacent to radio buttons instead of around them
$this->Form->setTemplates([
    'nestingLabel' => '{{hidden}}{{input}}<label{{attrs}}>{{text}}</label><br />',
    'formGroup' => '{{input}}{{label}}',
]);
?>
<?= $this->Form->create(
    null,
    ['url' => ['controller' => 'Comments', 'action' => 'add']]
) ?>

<?= $this->Form->control(
    'comment',
    [
       'class' => 'form-control',
       'id' => false,
       'label' => false,
       'type' => 'textarea',
       'placeholder' => 'Write a comment...'
   ]
) ?>

<?= $this->Form->hidden(
    'thought_id',
    ['value' => $thought['id']]
) ?>

<?php if (isset($error_duplicate) && $error_duplicate): ?>
    <div class="error-message">
        It looks like you're trying to submit the same comment multiple times, so we stopped you.
        This sometimes happens if you click the submit button multiple times. If you refresh the
        page where this comment is supposed to show up, you should see it there.
    </div>
<?php endif; ?>

<div class="comment-form-actions">
    <?php if ($thought['anonymous'] && $thought['user_id'] === $userId): ?>
        <div class="anonymous-comment-toggler">
            <label for="thought-<?= $thought['id'] ?>-comment-anonymous" title="Comment anonymously vs. with your color">
                <input type="checkbox" value="1" name="anonymous" id="thought-<?= $thought['id'] ?>-comment-anonymous" />
                Comment anonymously
            </label>
        </div>
    <?php endif; ?>
    <?= $this->Form->submit(
        'Post comment',
        ['class' => 'btn btn-primary', 'div' => false]
    ) ?>
    <a href="#" class="btn btn-link cancel_comment" data-thought-id="<?= $thought['id'] ?>">
        Cancel
    </a>
</div>

<?= $this->Form->end() ?>
