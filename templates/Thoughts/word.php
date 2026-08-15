<?php
/**
 * @var \App\Model\Entity\Thought $newThought
 * @var \App\Model\Entity\Thought[] $thoughts
 * @var \App\View\AppView $this
 * @var bool $loggedIn
 * @var int|null $userId
 * @var string $formattingKey
 * @var string $title_for_layout
 * @var string $word
 */
?>
<div class="tw_thoughts">
    <div class="row">
        <div class="offset-sm-2 col-sm-8">
            <h1 class="word_heading">
                <?= $title_for_layout ?>
            </h1>
        </div>
    </div>

    <div class="row word_summary">
        <div class="offset-sm-2 col-sm-8">
            <ul class="unstyled">
                <li>
                    <?php
                        $count = count($thoughts);
                        echo $count.__n(' thought', ' thoughts', $count);
                    ?>
                </li>
                <li>
                    <?php if ($loggedIn): ?>
                        <?= $this->Html->link(
                            'Add a thought',
                            ['controller' => 'Thoughts', 'action' => 'add', 'word' => $word]
                        ) ?>
                    <?php else: ?>
                        <?= $this->Html->link(
                            'Log in to add a thought',
                            ['controller' => 'Users', 'action' => 'login']
                        ) ?>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>

    <?php if (empty($thoughts)): ?>
        <div id="unpopulated-thoughtword">
            <div class="offset-sm-1 col-sm-10">
                <?php if ($loggedIn): ?>
                    <p>
                        No one has yet thought about <strong><?= $word ?></strong>.<br />
                    </p>
                    <p>
                        <?= $this->Html->link(
                            'Would you like to be the first?',
                            [
                                'controller' => 'Thoughts',
                                'action' => 'add',
                                '?' => [
                                    'word' => $word
                                ]
                            ]
                        ) ?>
                    </p>
                <?php else: ?>
                    <p>
                        No one has yet thought about <strong><?= $word ?></strong>.<br />
                        If you were
                        <?= $this->Html->link(
                            'logged in',
                            ['controller' => 'Users', 'action' => 'login']
                        ) ?>, you could be the first.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>

        <?php foreach ($thoughts as $thought): ?>
            <div class="row thoughtrow" id="t<?= $thought->id ?>">
                <div class="offset-sm-1 col-sm-10">
                    <?= $this->element('Thoughts/metadata', compact('thought', 'userId')) ?>
                    <?= $this->element('Thoughts/view', compact('thought')) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?= $this->element('Thoughts/init_js', compact('formattingKey')) ?>
    <?php endif; ?>
</div>

<?= $this->element('Thoughts/init_audio') ?>
