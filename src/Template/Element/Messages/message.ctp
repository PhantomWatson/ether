<?php
/**
 * @var \App\View\AppView $this
 */
?>
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