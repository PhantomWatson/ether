<div class="comment" id="c<?= $commentId ?>">
    <?= $this->element('colorbox', compact('color')) ?>
    <div class="body">
        <?= nl2br($comment); ?>
    </div>
</div>