<?php
    $pagingUsed = $this->Paginator->hasNext() || ($this->Paginator->hasPrev() && ! $this->request->is('ajax'));
    $this->Paginator->templates([
        'nextActive' => '<a href="{{url}}" class="prev">{{text}}</a>',
        'prevActive' => '<a href="{{url}}" class="next">{{text}}</a>'
    ]);
    if ($pagingUsed) {
        echo '<p class="paging"><a href="?full">Show full conversation</a></p>';
    }
?>
<div id="conversation">
    <?php if ($this->Paginator->hasNext()): ?>
        <div class="row convo_pagination">
            <div class="col-sm-offset-2 col-sm-7">
                <?= $this->Paginator->next('Show older messages') ?>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach ($messages as $message): ?>
        <?= $this->element('Messages/message', [
            'message' => $message
        ]) ?>
    <?php endforeach; ?>

    <?php if ($this->Paginator->hasPrev()): ?>
        <div class="row convo_pagination">
            <div class="col-sm-offset-2 col-sm-7">
                <?= $this->Paginator->prev('Show newer messages') ?>
            </div>
        </div>
    <?php endif; ?>
</div>