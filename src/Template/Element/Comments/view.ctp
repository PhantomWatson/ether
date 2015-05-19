<div class="comment">
    <?= $this->element('colorbox', compact('color')) ?>
    <div class="body">
        <?= $this->element('thoughts/formatted_thought', [
            'input' => $comment,
            'id' => $id,
            'type' => 'comment',
            'thoughtwords' => isset($thoughtwords) ? $thoughtwords : null
        ]) ?>
    </div>
</div>