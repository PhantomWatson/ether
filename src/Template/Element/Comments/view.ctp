<?php
/**
 * @var \App\View\AppView $this
 * @var array $comment
 */
?>
<div class="comment" data-formatting-key="<?= $comment['formatting_key'] ?>" data-comment-id="<?= $comment['id'] ?>">
    <?= $this->element(
        'colorbox',
        [
            'color' => $comment['user']['color'],
            'anonymous' => $comment['anonymous']
        ]
    ) ?>
    <div class="body">
        <?= $comment['formatted_comment'] ?>
    </div>
</div>
