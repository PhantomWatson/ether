<?php
/**
 * @var \App\View\AppView $this
 * @var array[] $flashMessages
 */
/* This creates the hidden #flash_messages container and fills it with
 * flash messages and displayed via a javascript animation if there are
 * messages to display. Regardless, the container is put onto the page
 * so that asyncronous activity can load messages into it as needed. */
 if (! isset($flashMessages)) {
     $flashMessages = [];
 }
?>

<?php $this->append('buffered_js'); ?>
    flashMessage.init();
<?php $this->end(); ?>

<div id="flash_messages" class="row">
    <?php foreach ($flashMessages as $msg): ?>
        <div class="offset-sm-2 col-sm-8 alert alert-<?= $msg['class'] ?> alert-dismissible" role="alert" style="display: none;">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <?= $msg['message'] ?>
        </div>
    <?php endforeach; ?>
</div>
