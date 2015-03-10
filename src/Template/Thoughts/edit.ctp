<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $thought->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $thought->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Thoughts'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Comments'), ['controller' => 'Comments', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Comment'), ['controller' => 'Comments', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="thoughts form large-10 medium-9 columns">
    <?= $this->Form->create($thought); ?>
    <fieldset>
        <legend><?= __('Edit Thought') ?></legend>
        <?php
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('word');
            echo $this->Form->input('thought');
            echo $this->Form->input('color');
            echo $this->Form->input('time');
            echo $this->Form->input('edited');
            echo $this->Form->input('comments_enabled');
            echo $this->Form->input('parsedTextCache');
            echo $this->Form->input('cacheTimestamp');
            echo $this->Form->input('anonymous');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
