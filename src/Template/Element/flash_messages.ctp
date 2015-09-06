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
		<li class="col-sm-offset-2 col-sm-8 alert alert-<?= $msg['class'] ?> alert-dismissible" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?= $msg['message'] ?>
		</li>
	<?php endforeach; ?>
</ul>