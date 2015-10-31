<div class="comment" data-comment-id="<?= $comment['id'] ?>">
    <?= $this->element('colorbox', ['color' => $comment['user']['color']]) ?>
    <div class="body">
        <?= $comment['formatted_comment'] ?>
    </div>
</div>