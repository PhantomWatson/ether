<div class="tw_thoughts">
    <div class="row">
        <div class="col-sm-offset-2 col-sm-8">
            <h1 class="word_heading">
                <?= $title_for_layout ?>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-2">
            <p>
                <?php
                    $count = count($thoughts);
                    echo $count.__n(' thought', ' thoughts', $count);
                ?>
            </p>
            <p>
                <?php if ($this->request->session()->check('Auth.User.id')): ?>
                    <?= $this->Html->link(
                        'Add a thought',
                        ['controller' => 'Thoughts', 'action' => 'add', 'word' => $word]
                    ) ?>
                <?php else: ?>
                    <?= $this->Html->link(
                        'Log In to add a thought',
                        ['controller' => 'Users', 'action' => 'login']
                    ) ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="col-sm-10">
            <?php if (empty($thoughts)): ?>
                <div class="content_box">
                    <div id="wannathink_choices">
                        <div>
                            <?php if ($loggedIn): ?>
                                <p>
                                    No one has yet thought about <strong><?php echo $word; ?></strong>.<br />
                                    Would you like to?
                                </p>
                                <p>
                                    <?php echo $this->Html->link(
                                        'Yes',
                                        ['controller' => 'Thoughts', 'action' => 'add', $word]
                                    ); ?>
                                    <br />
                                    <a href="#" id="dontwannathink">No</a>
                                </p>
                            <?php else: ?>
                                <p>
                                    No one has yet thought about <strong><?php echo $word; ?></strong>.<br />
                                    If you were
                                    <?php echo $this->Html->link(
                                        'logged in',
                                        ['controller' => 'Users', 'action' => 'login']
                                    ); ?>, you could be the first.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div id="wannathink_rejection" style="display: none;">
                        <div>
                            Well, fine. Be that way.
                        </div>
                    </div>
                </div>
                <div id="newthoughtview"></div>
            <?php else: ?>
                <div id="newthoughtadd" style="display: none;">
                    <div>
                        <div class="content_box">
                            <?php echo $this->element('Thoughts/form', compact('thought')); ?>
                        </div>
                        <div class="newthoughtbutton">
                            <a href="#" id="cancel_thought">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
                <div id="newthoughtview"></div>
                <div class="row">
                    <?php foreach ($thoughts as $thought): ?>
                        <div class="row">
                            <div class="col-sm-3 meta">
                                <div class="visible-xs-inline-block visible-sm visible-md visible-lg">
                                    <?= $this->element('colorbox', [
                                        'color' => $thought['user']['color'],
                                        'anonymous' => $thought['anonymous']
                                    ]) ?>
                                </div>
                                <div class="visible-xs-inline-block visible-sm visible-md visible-lg">
                                    <?= $this->Time->abbreviatedTimeAgoInWords($thought['created']) ?>
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
                            <div class="col-sm-9">
                                <?php echo $this->element('Thoughts/view', compact('thought')); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php $this->append('buffered_js'); ?>
                   thought.init({
                       formattingKey: '<?= $formattingKey ?>'
                   });
                <?php $this->end(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>