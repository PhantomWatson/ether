<div class="comment" data-formatting-key="<?= $comment['formatting_key'] ?>" data-comment-id="<?= $comment['id'] ?>">
    <?= $this->element('colorbox', ['color' => $comment['user']['color']]) ?>
    <div class="body">
        <?= $comment['formatted_comment'] ?>
    </div>
</div>