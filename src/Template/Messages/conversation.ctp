<div id="content_title">
    <h1>
        <?= $titleForLayout ?>
    </h1>
</div>
<ul class="list-unstyled">
    <li>
        <?= $this->Html->link(
            '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back to conversations',
            ['action' => 'index'],
            ['escape' => false]
        ) ?>
    </li>
    <li>
        <?= $this->Html->link(
            'View Thinker\'s profile',
            [
                'controller' => 'Users',
                'action' => 'view',
                $penpalColor
            ]
        ) ?>
    </li>
    <?php $pagingUsed = $this->Paginator->hasNext() || ($this->Paginator->hasPrev() && ! $this->request->is('ajax')); ?>
    <?php if ($pagingUsed): ?>
        <li>
            <a href="?full">
                Show full conversation
            </a>
        </li>
    <?php endif; ?>
</ul>

<?php if (empty($messages)): ?>
    <p>
        You have not exchanged any messages with this Thinker yet.
    </p>
<?php else: ?>
    <?= $this->element('Messages'.DS.'conversation') ?>
    <?php $this->append('buffered_js'); ?>
        messages.scrollToLastMsg();
        messages.setupPagination();
    <?php $this->end(); ?>
<?php endif; ?>

<div id="send_message" class="row">
    <?php if ($penpalAcceptsMessages): ?>
        <div class="col-sm-offset-3 col-sm-6">
            <?php
                echo $this->Form->create(
                    $messageEntity,
                    [
                        'url' => ['controller' => 'Messages', 'action' => 'send']
                    ]
                );
                echo $this->Form->input(
                    'message',
                    [
                        'class' => 'form-control',
                        'div' => ['class' => 'form-group'],
                        'label' => false,
                        'placeholder' => 'Send a message'
                    ]
                );
                echo $this->Form->input(
                    'recipient',
                    [
                        'type' => 'hidden',
                        'value' => $penpalColor
                    ]
                );
                echo $this->Form->submit(
                    'Send',
                    ['class' => 'btn btn-default']
                );
                echo $this->Form->end();
            ?>
        </div>
    <?php else: ?>
        <div class="col-sm-offset-2 col-sm-8">
            This user has opted out of receiving messages.
        </div>
    <?php endif; ?>
</div>