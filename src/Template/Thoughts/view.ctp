<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Thought'), ['action' => 'edit', $thought->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Thought'), ['action' => 'delete', $thought->id], ['confirm' => __('Are you sure you want to delete # {0}?', $thought->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Thoughts'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Thought'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Comments'), ['controller' => 'Comments', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Comment'), ['controller' => 'Comments', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="thoughts view large-10 medium-9 columns">
    <h2><?= h($thought->id) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('User') ?></h6>
            <p><?= $thought->has('user') ? $this->Html->link($thought->user->id, ['controller' => 'Users', 'action' => 'view', $thought->user->id]) : '' ?></p>
            <h6 class="subheader"><?= __('Word') ?></h6>
            <p><?= h($thought->word) ?></p>
            <h6 class="subheader"><?= __('Color') ?></h6>
            <p><?= h($thought->color) ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($thought->id) ?></p>
            <h6 class="subheader"><?= __('Time') ?></h6>
            <p><?= $this->Number->format($thought->time) ?></p>
            <h6 class="subheader"><?= __('Edited') ?></h6>
            <p><?= $this->Number->format($thought->edited) ?></p>
            <h6 class="subheader"><?= __('Comments Enabled') ?></h6>
            <p><?= $this->Number->format($thought->comments_enabled) ?></p>
            <h6 class="subheader"><?= __('CacheTimestamp') ?></h6>
            <p><?= $this->Number->format($thought->cacheTimestamp) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($thought->created) ?></p>
            <h6 class="subheader"><?= __('Modified') ?></h6>
            <p><?= h($thought->modified) ?></p>
        </div>
        <div class="large-2 columns booleans end">
            <h6 class="subheader"><?= __('Anonymous') ?></h6>
            <p><?= $thought->anonymous ? __('Yes') : __('No'); ?></p>
        </div>
    </div>
    <div class="row texts">
        <div class="columns large-9">
            <h6 class="subheader"><?= __('Thought') ?></h6>
            <?= $this->Text->autoParagraph(h($thought->thought)); ?>

        </div>
    </div>
    <div class="row texts">
        <div class="columns large-9">
            <h6 class="subheader"><?= __('ParsedTextCache') ?></h6>
            <?= $this->Text->autoParagraph(h($thought->parsedTextCache)); ?>

        </div>
    </div>
</div>
<div class="related row">
    <div class="column large-12">
    <h4 class="subheader"><?= __('Related Comments') ?></h4>
    <?php if (!empty($thought->comments)): ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?= __('Id') ?></th>
            <th><?= __('Thought Id') ?></th>
            <th><?= __('User Id') ?></th>
            <th><?= __('Color') ?></th>
            <th><?= __('Comment') ?></th>
            <th><?= __('Time') ?></th>
            <th><?= __('ParsedTextCache') ?></th>
            <th><?= __('CacheTimestamp') ?></th>
            <th><?= __('Created') ?></th>
            <th><?= __('Modified') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($thought->comments as $comments): ?>
        <tr>
            <td><?= h($comments->id) ?></td>
            <td><?= h($comments->thought_id) ?></td>
            <td><?= h($comments->user_id) ?></td>
            <td><?= h($comments->color) ?></td>
            <td><?= h($comments->comment) ?></td>
            <td><?= h($comments->time) ?></td>
            <td><?= h($comments->parsedTextCache) ?></td>
            <td><?= h($comments->cacheTimestamp) ?></td>
            <td><?= h($comments->created) ?></td>
            <td><?= h($comments->modified) ?></td>

            <td class="actions">
                <?= $this->Html->link(__('View'), ['controller' => 'Comments', 'action' => 'view', $comments->id]) ?>

                <?= $this->Html->link(__('Edit'), ['controller' => 'Comments', 'action' => 'edit', $comments->id]) ?>

                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Comments', 'action' => 'delete', $comments->id], ['confirm' => __('Are you sure you want to delete # {0}?', $comments->id)]) ?>

            </td>
        </tr>

        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    </div>
</div>
