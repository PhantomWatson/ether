<?php
/**
 * @var \App\View\AppView $this
 * @var string $title_for_layout
 * @var \App\Model\Entity\Thought $thought
 * @var int|null $userId
 */
?>

<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<?= $this->element('Thoughts/metadata', compact('thought', 'userId')) ?>
<?= $this->element('Thoughts/view', compact('thought')) ?>
