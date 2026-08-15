<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Thought $thought
 * @var int $userId
 */
?>

<div class="thought-meta row">
    <div class="col thought-meta-color">
        <?= $this->element('colorbox', [
            'color' => $thought->user->color,
            'anonymous' => $thought->anonymous,
        ]) ?>
        thought
        <?= $this->Time->abbreviatedTimeAgoInWords($thought->created) ?>...
    </div>
    <div class="col thought-actions">
        <ul class="list-unstyled">
            <?php if (!$thought->hidden): ?>
                <li>
                    <?= $this->Html->link(
                        '<i class="fa-solid fa-link thought-action-icon"></i> Link <span class="visually-hidden">to this thought</span>',
                        $thought->url,
                        [
                            'escape' => false,
                            'class' => 'btn btn-link',
                        ]
                    ) ?>
                </li>
            <?php endif; ?>
            <li>
                <button data-tts="<?= $thought->tts ?>" data-thought-id="<?= $thought->id ?>" class="listenButton btn btn-link">
                    <i class="fa-solid fa-play thought-action-icon"></i> Listen
                </button>
            </li>
            <?php if ($userId == $thought->user_id): ?>
                <li>
                    <?= $this->Html->link(
                        '<i class="fa-solid fa-pencil thought-action-icon"></i> Edit',
                        ['controller' => 'Thoughts', 'action' => 'edit', $thought->id],
                        [
                            'escape' => false,
                            'class' => 'btn btn-link',
                        ]
                    ) ?>
                </li>
                <li>
                    <?= $this->Form->postLink(
                        '<i class="fa-solid fa-trash-can thought-action-icon"></i> Delete',
                        ['controller' => 'Thoughts', 'action' => 'delete', $thought->id],
                        [
                            'confirm' => 'Are you sure that you want to remove this thought?',
                            'escape' => false,
                            'class' => 'btn btn-link',
                        ]
                    ) ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
