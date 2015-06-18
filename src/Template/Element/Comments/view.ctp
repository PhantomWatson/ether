<div class="comment" data-comment-id="<?= $commentId ?>">
    <?= $this->element('colorbox', compact('color')) ?>
    <div class="body">
        <?= nl2br($comment); ?>
    </div>
</div>