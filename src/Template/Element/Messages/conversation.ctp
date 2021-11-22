<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Message[]|\Cake\Collection\CollectionInterface $messages
 */
    $this->Paginator->setTemplates([
        'nextActive' => '<div class="row convo_pagination"><div class="offset-sm-2 col-sm-7"><a href="{{url}}&dir=next">{{text}}</a></div></div>',
        'prevActive' => '<div class="row convo_pagination"><div class="offset-sm-2 col-sm-7"><a href="{{url}}&dir=prev">{{text}}</a></div></div>'
    ]);

    /* If this is loaded via AJAX, the result of loading 'next'
     * should only include another link to 'next', and not a link to 'prev' (and vice-versa) */
    $hasNext = $this->Paginator->hasNext() && ! ($this->request->is('ajax') && $_GET['dir'] == 'prev');
    $hasPrev = $this->Paginator->hasPrev() && ! ($this->request->is('ajax') && $_GET['dir'] == 'next');
?>
<div id="conversation">
    <?php if ($hasNext): ?>
        <?= $this->Paginator->next('Show older messages') ?>
    <?php endif; ?>

    <?php foreach ($messages as $message): ?>
        <?= $this->element('Messages/message', [
            'message' => $message
        ]) ?>
    <?php endforeach; ?>

    <?php if ($hasPrev): ?>
        <?= $this->Paginator->prev('Show newer messages') ?>
    <?php endif; ?>
</div>
