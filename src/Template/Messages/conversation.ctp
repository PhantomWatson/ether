<div id="content_title">
    <h1>
        <?php echo $titleForLayout; ?>
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
            <div class="row">
                <div class="col-sm-offset-2 col-sm-1 sender">
                    <?= $this->element('colorbox', [
                        'color' => $message['sender']['color']
                    ]) ?>
                </div>
                <div class="col-sm-6">
                    <?php
                        $formattedMessage = $message['message'];
                        $formattedMessage = stripslashes($formattedMessage);
                        $formattedMessage = $this->Text->autoLink($formattedMessage);
                        $formattedMessage = nl2br($formattedMessage);
                        echo $formattedMessage;
                    ?>
                    <div class="message_info">
                        <?php if ($message['received'] == 0): ?>
                            <span class="new">
                                New
                            </span>
                        <?php endif; ?>
                        <?= ($message['sender_id'] == $penpalId) ? 'received' : 'sent' ?>
                        <?= $this->Time->abbreviatedTimeAgoInWords($message['created']) ?>
                    </div>
                </div>
            </div>
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