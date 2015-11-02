<div id="content_title">
    <h1>
        <?= $titleForLayout ?>
    </h1>
</div>
<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back to conversations',
        ['action' => 'index'],
        ['escape' => false]
    ) ?>
</p>

<div id="conversation">
    <?php if (empty($messages)): ?>
        <p>

        </p>
    <?php else: ?>
        <?php foreach ($messages as $message): ?>
            <?= $this->element('Messages/message', [
                'message' => $message
            ]) ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

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