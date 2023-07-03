<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Message[]|\Cake\Collection\CollectionInterface $messages
 */
    $this->Paginator->setTemplates([
        'nextActive' => '<div class="row convo_pagination"><div class="offset-sm-3 col-sm-6"><button class="btn btn-link" data-url="{{url}}">{{text}} <i class="fas fa-spinner fa-spin loading" title="Loading..."></i></button></div></div>',
    ]);

    $hasNext = $this->Paginator->hasNext() && ! ($this->request->is('ajax') && $_GET['dir'] == 'prev');
?>

<?php if ($hasNext): ?>
    <?= $this->Paginator->next('Show older messages') ?>
<?php endif; ?>

<?php foreach ($messages as $message): ?>
    <?= $this->element('Messages/message', [
        'message' => $message
    ]) ?>
<?php endforeach; ?>
