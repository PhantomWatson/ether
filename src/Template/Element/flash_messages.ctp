<?php
	/* This creates the hidden #flash_messages container and fills it with
	 * flash messages and displayed via a javascript animation if there are
	 * messages to display. Regardless, the container is put onto the page
	 * so that asyncronous activity can load messages into it as needed. */
?>

<?php $this->append('buffered_js'); ?>
	flashMessage.init();
<?php $this->end(); ?>

<ul id="flash_messages" class="row">
	<?php foreach ($flashMessages as $msg): ?>
		<li class="<?= $msg['class'] ?> col-sm-offset-2 col-sm-8" style="display: none;">
            <a href="#" class="close">
                close
            </a>
			<?= $msg['message'] ?>
		</li>
	<?php endforeach; ?>
</ul>