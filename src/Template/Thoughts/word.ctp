<div id="content_title">
    <h1>
        <?php echo $title_for_layout; ?>
    </h1>
    <?php $count = count($thoughts); ?>
    <?php if ($count): ?>
        <p class="subtitle">
            <?php echo $count.__n(' thought', ' thoughts', $count); ?>
        </p>
    <?php endif; ?>
</div>

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
            <div class="content_box" id="newthoughtadd_form">
                <?php echo $this->element('Thoughts/form', compact('thought')); ?>
            </div>
            <div class="newthoughtbutton">
                <a href="#" id="cancel_thought">
                    Cancel
                </a>
            </div>
        </div>
    </div>
    <div id="newthoughtbutton" class="newthoughtbutton">
        <?php if ($this->request->session()->check('Auth.User.id')): ?>
            <a href="#" id="add_thought">
                Add a Thought
            </a>
        <?php else: ?>
            <?php echo $this->Html->link(
                'Log In to Add a Thought',
                ['controller' => 'Users', 'action' => 'login']
            ); ?>
        <?php endif; ?>
    </div>
    <div id="newthoughtview"></div>
    <?php foreach ($thoughts as $thought): ?>
        <?php echo $this->element('Thoughts/view', compact('thought')); ?>
    <?php endforeach; ?>

    <?php $this->append('buffered_js'); ?>
       thought.init({
           formattingKey: '<?= $formattingKey ?>'
       });
    <?php $this->end(); ?>
<?php endif; ?>