<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Thought[]|\Cake\Collection\CollectionInterface $thoughts
 */
?>
<div class="tw_thoughts">
    <div class="row">
        <div class="col-sm-offset-2 col-sm-8">
            <h1 class="word_heading">
                <?= $title_for_layout ?>
            </h1>
        </div>
    </div>

    <div class="row word_summary">
        <div class="col-sm-offset-2 col-sm-8">
            <ul class="unstyled">
                <li>
                    <?php
                        $count = count($thoughts);
                        echo $count.__n(' thought', ' thoughts', $count);
                    ?>
                </li>
                <li>
                    <?php if ($this->request->session()->check('Auth.User.id')): ?>
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
            <div class="col-sm-offset-1 col-sm-10">
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
        <div class="row">
            <div id="newthoughtadd" class="col-sm-12" style="display: none;">
                <div>
                    <div class="content_box">
                        <?= $this->element('Thoughts/form', compact('thought')) ?>
                    </div>
                    <div class="newthoughtbutton">
                        <a href="#" id="cancel_thought">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="newthoughtview"></div>


        <?php foreach ($thoughts as $thought): ?>
            <div class="row thoughtrow" id="t<?= $thought['id'] ?>">
                <div class="col-sm-1 col-sm-offset-1 meta">
                    <div class="visible-xs-inline-block visible-sm visible-md visible-lg">
                        <?= $this->element('colorbox', [
                            'color' => $thought['user']['color'],
                            'anonymous' => $thought['anonymous']
                        ]) ?>
                    </div>
                    <div class="visible-xs-inline-block visible-sm visible-md visible-lg">
                        <?php
                            $timeAgo = $this->Time->abbreviatedTimeAgoInWords($thought['created']);
                            echo str_replace(',', ',<br class="hidden-xs" />', $timeAgo);
                        ?>
                        <br />
                        <?= $this->Html->link(
                            'Link',
                            ['controller' => 'Thoughts', 'action' => 'word', $word, '#' => 't'.$thought['id']]
                        ) ?>
                        <?php if ($userId == $thought['user']['id']): ?>
                            <br />
                            <?= $this->Html->link(
                                'Edit',
                                ['controller' => 'Thoughts', 'action' => 'edit', $thought['id']]
                            ) ?>
                            <br />
                            <?= $this->Form->postLink(
                                'Delete',
                                ['controller' => 'Thoughts', 'action' => 'delete', $thought['id']],
                                ['confirm' => 'Are you sure that you want to remove this thought?']
                            ) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-8">
                    <?= $this->element('Thoughts/view', compact('thought')) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php $this->append('buffered_js'); ?>
           thought.init({
               formattingKey: '<?= $formattingKey ?>'
           });
        <?php $this->end(); ?>
    <?php endif; ?>
</div>