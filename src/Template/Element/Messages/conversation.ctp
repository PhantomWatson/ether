<?php
    $linkTemplate = '<div class="row convo_pagination"><div class="col-sm-offset-2 col-sm-7"><a href="{{url}}">{{text}}</a></div></div>';
    $this->Paginator->templates([
        'nextActive' => $linkTemplate,
        'prevActive' => $linkTemplate
    ]);
?>
<div id="conversation">
    <?php if ($this->Paginator->hasNext()): ?>
        <?= $this->Paginator->next('Show older messages') ?>
    <?php endif; ?>

    <?php foreach ($messages as $message): ?>
        <?= $this->element('Messages/message', [
            'message' => $message
        ]) ?>
    <?php endforeach; ?>

    <?php if ($this->Paginator->hasPrev()): ?>
        <?= $this->Paginator->prev('Show newer messages') ?>
    <?php endif; ?>
</div>