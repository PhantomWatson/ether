<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $thought
 * @var string $title_for_layout
 */
?>
<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<?= $this->element('Thoughts/form', compact('thought')) ?>