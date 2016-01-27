<p>
    You have received a new message from
    <span style="border: 1px solid black; background-color: #<?= $senderColor ?>; display: inline-block; height: 1em; vertical-align: middle; width: 1em;"></span>
    (Thinker #<?= $senderColor ?>):
</p>

<blockquote>
    <?= nl2br($this->Text->truncate(
        $message->message,
        100,
        [
            'ending' => '...',
            'exact' => false,
            'html' => true
        ]
    )) ?>
</blockquote>

<p>
    To read this full message,
    <?= $this->Html->link('log in', $loginUrl) ?>
    and view your
    <?= $this->Html->link('messages', $messageUrl) ?>.
</p>

<p>
    If you'd rather not receive notifications when someone sends you a message on Ether, you can disable them by changing your
    <?= $this->Html->link('account settings', $accountUrl) ?>.
</p>
