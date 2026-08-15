<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="audio-container" style="display: none;">
    <audio autoplay controls id="audio">
        <source src="" type="audio/mpeg" id="audio-source">
    </audio>
    <button id="audio-close">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>

<?= $this->Html->script('tts') ?>

<?php $this->append('buffered_js'); ?>
    new TTS();
<?php $this->end(); ?>
