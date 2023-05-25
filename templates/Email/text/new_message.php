<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Message $message
 */
?>
You have received a new message from Thinker #<?= $senderColor ?>:

"<?= $this->Text->truncate(
	$message->message,
	100,
	[
		'ending' => '...',
		'exact' => false,
		'html' => true
	]
) ?>"

To read this full message, log in at <?= $loginUrl ?> and visit <?= $messageUrl ?>.

If you'd rather not receive notifications when someone sends you a message on Ether, you can disable them by changing your account settings (<?= $accountUrl ?>).
