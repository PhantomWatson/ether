<?php
/**
 * @var string $formattingKey
 * @var \App\View\AppView $this
 */
?>
<?php $this->append('buffered_js'); ?>
    thought.init({
        formattingKey: <?= json_encode($formattingKey) ?>,
    });
<?php $this->end(); ?>
