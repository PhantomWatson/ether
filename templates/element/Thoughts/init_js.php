<?php
/**
 * @var string $formattingKey
 */
?>
<?php $this->append('buffered_js'); ?>
    thought.init({
        formattingKey: <?= json_encode($formattingKey) ?>,
    });
<?php $this->end(); ?>
