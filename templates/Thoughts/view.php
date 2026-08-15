<?php
/**
 * @var \App\View\AppView $this
 * @var string $title_for_layout
 * @var \App\Model\Entity\Thought $thought
 * @var int|null $userId
 * @var string $formattingKey
 */
?>

<div class="tw_thoughts">
    <div class="row">
        <div class="offset-sm-2 col-sm-8">
            <h1 class="word_heading">
                <?= $this->Html->link(ucfirst($thought->word), $thought->url) ?>
            </h1>
        </div>
    </div>
    <div class="row word_summary">
        <div class="offset-sm-2 col-sm-8">
            <h2>
                Thought #<?= $thought->id ?>
            </h2>
            <?php if ($thought->hidden): ?>
                <p>
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <strong>Private thought</strong> (only you can see this)
                </p>
            <?php endif; ?>
        </div>
    </div>
    <div class="row thoughtrow" id="t<?= $thought->id ?>">
        <div class="offset-sm-1 col-sm-10">
            <?= $this->element('Thoughts/metadata', compact('thought', 'userId')) ?>
            <?= $this->element('Thoughts/view', compact('thought')) ?>
        </div>
    </div>
</div>

<?= $this->element('Thoughts/init_js', compact('formattingKey')) ?>
<?= $this->element('Thoughts/init_audio') ?>
