<div class="user_profile">
    <div id="content_title">
        <h1>
            Thinker:
            <?= $this->element('colorbox', ['color' => $user['color']]) ?>
        </h1>
        <p class="subtitle">
            Color: #<?= $user['color'] ?>
            <br />
            <?= $this->Html->link(
                'View all Thinkers',
                ['controller' => 'Users', 'action' => 'index']
            ) ?>
        </p>
    </div>

    <div class="content_box introspection">
        <h2>
            Introspection
        </h2>
        <?php if (empty($user['profile'])): ?>
            <em>
                This Thinker has not yet introspected.
            </em>
        <?php else: ?>
             <?= stripslashes($user['profile']) ?>
        <?php endif; ?>
    </div>

    <div class="content_box">
        <h2>
            Thoughts
        </h2>
        <?php if (empty($user['thoughts'])): ?>
            <em>
                This Thinker has not yet thunk.
            </em>
        <?php else: ?>
            <div class="thoughtwords">
                <?php foreach ($user['thoughts'] as $thought): ?>
                    <?= $this->Html->link(
                        $thought['word'],
                        ['controller' => 'Thoughts', 'action' => 'word', $thought['word'], '#' => 't'.$thought['id']],
                        ['class' => 'thoughtword']
                    ) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ((isset($messagesCount) && $messagesCount) || $user['acceptMessages']): ?>
        <div class="content_box">
            <h2>
                Communication
            </h2>
            <?php if (isset($messagesCount) && $messagesCount): ?>
                <?= $this->Html->link(
                    "View $messagesCount message".($messagesCount == 1 ? '' : 's')." between you and this Thinker.",
                    [
                        'controller' => 'Messages',
                        'action' => 'conversation',
                        $user['color']
                    ]
                ) ?>
            <?php endif; ?>
            <?php if ($user['acceptMessages']): ?>
                <div id="profile_send_message">
                    <?php
                        echo $this->Form->create(
                            $messageEntity,
                            [
                                'url' => ['controller' => 'Messages', 'action' => 'send']
                            ]
                        );
                        echo $this->Form->input(
                            'message',
                            [
                                'class' => 'form-control',
                                'div' => ['class' => 'form-group'],
                                'label' => false,
                                'placeholder' => 'Send a message'
                            ]
                        );
                        echo $this->Form->input(
                            'recipient',
                            [
                                'type' => 'hidden',
                                'value' => $user['color']
                            ]
                        );
                        echo $this->Form->submit(
                            'Send',
                            ['class' => 'btn btn-default']
                        );
                        echo $this->Form->end();
                    ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php $this->append('buffered_js'); ?>
    setupThoughtwordLinks($('.thoughtwords'));
<?php $this->end(); ?>