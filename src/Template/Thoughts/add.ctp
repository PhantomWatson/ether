<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<?= $this->element('Thoughts/form', compact('thought')) ?>