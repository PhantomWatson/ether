<?php
/**
 * @var \App\View\AppView $this
 * @var string $titleForLayout
 * @var string $penpalColor
 * @var boolean $penpalAcceptsMessages
 * @var \App\Model\Entity\Message $messageEntity
 */

$pagingUsed = $this->Paginator->hasNext() || ($this->Paginator->hasPrev() && ! $this->request->is('ajax'));
?>
<div id="content_title">
    <h1>
        Messages with
        <?= $this->Html->link(
            "Thinker #$penpalColor",
            [
                'controller' => 'Users',
                'action' => 'view',
                $penpalColor
            ]
        ) ?>

    </h1>
</div>
<ul class="list-unstyled">
    <li>
        <?= $this->Html->link(
            '<i class="fas fa-arrow-left"></i> Back to conversations',
            ['action' => 'index'],
            ['escape' => false]
        ) ?>
    </li>
</ul>

<?php if (empty($messages)): ?>
    <p>
        You have not exchanged any messages with this Thinker yet.
    </p>
<?php else: ?>
    <div id="conversation">
        <?= $this->element('Messages' . DS . 'conversation') ?>
    </div>
    <?php $this->append('buffered_js'); ?>
        messages.init();
    <?php $this->end(); ?>
<?php endif; ?>

<div id="send_message" class="row">
    <?php if ($penpalAcceptsMessages): ?>
        <div class="offset-sm-3 col-sm-6">
            <?php
                echo $this->Form->create(
                    $messageEntity,
                    [
                        'url' => ['controller' => 'Messages', 'action' => 'send']
                    ]
                );
                echo $this->Form->control(
                    'message',
                    [
                        'class' => 'form-control',
                        'div' => ['class' => 'form-group'],
                        'label' => false,
                        'placeholder' => 'Send a message'
                    ]
                );
                echo $this->Form->control(
                    'recipient',
                    [
                        'type' => 'hidden',
                        'value' => $penpalColor
                    ]
                );
                echo $this->Form->submit(
                    'Send',
                    ['class' => 'btn btn-primary']
                );
                echo $this->Form->end();
            ?>
        </div>
    <?php else: ?>
        <div class="offset-sm-2 col-sm-8">
            This user has opted out of receiving messages.
        </div>
    <?php endif; ?>
</div>
