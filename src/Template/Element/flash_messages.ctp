<?php
	/* This creates the hidden #flash_messages container and fills it with
	 * flash messages and displayed via a javascript animation if there are
	 * messages to display. Regardless, the container is put onto the page
	 * so that asyncronous activity can load messages into it as needed. */
?>

<?php $this->append('buffered_js'); ?>
	flashMessage.init();
<?php $this->end(); ?>

<div id="flash_messages" style="display: none;">
	<div class="close">
		<a href="#" id="close_flash_msg">
			Close
		</a>
	</div>
	<div class="messages_wrapper">
		<ul>
			<?php foreach ($flashMessages as $msg): ?>
				<li class="<?php echo $msg['class']; ?>">
					<?php echo $msg['message']; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>