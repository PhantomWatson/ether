<div id="conversation">
    <?php if (empty($messages)): ?>
        <p>

        </p>
    <?php else: ?>
        <ul>
            <?php foreach ($messages as $message): ?>
                <li class="<?php echo $message['sender_id'] == $penpalId ? 'received' : 'sent'; ?>">
                    <div class="message_info">
                        <?php if ($message['sender_id'] == $penpalId): ?>
                            <?= $this->element('colorbox', [
                                'color' => $message['sender']['color']
                            ]) ?>
                        <?php else: ?>
                            you
                        <?php endif; ?>

                        said

                        <?= $this->Time->abbreviatedTimeAgoInWords($message['created']) ?>
                    </div>
                    <p>
                        <?php
                            $formattedMessage = $message['message'];
                            $formattedMessage = stripslashes($formattedMessage);
                            $formattedMessage = $this->Text->autoLink($formattedMessage);
                            echo nl2br($formattedMessage);
                        ?>
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<div id="send_message">
    <?php if ($penpalAcceptsMessages): ?>
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
    <?php else: ?>
        <p>
            This user has opted out of receiving messages.
        </p>
    <?php endif; ?>
</div>