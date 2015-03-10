<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('New Thought'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Comments'), ['controller' => 'Comments', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Comment'), ['controller' => 'Comments', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="thoughts index large-10 medium-9 columns">
    <table cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('id') ?></th>
            <th><?= $this->Paginator->sort('user_id') ?></th>
            <th><?= $this->Paginator->sort('word') ?></th>
            <th><?= $this->Paginator->sort('color') ?></th>
            <th><?= $this->Paginator->sort('time') ?></th>
            <th><?= $this->Paginator->sort('edited') ?></th>
            <th><?= $this->Paginator->sort('comments_enabled') ?></th>
            <th class="actions"><?= __('Actions') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($thoughts as $thought): ?>
        <tr>
            <td><?= $this->Number->format($thought->id) ?></td>
            <td>
                <?= $thought->has('user') ? $this->Html->link($thought->user->id, ['controller' => 'Users', 'action' => 'view', $thought->user->id]) : '' ?>
            </td>
            <td><?= h($thought->word) ?></td>
            <td><?= h($thought->color) ?></td>
            <td><?= $this->Number->format($thought->time) ?></td>
            <td><?= $this->Number->format($thought->edited) ?></td>
            <td><?= $this->Number->format($thought->comments_enabled) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['action' => 'view', $thought->id]) ?>
                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $thought->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $thought->id], ['confirm' => __('Are you sure you want to delete # {0}?', $thought->id)]) ?>
            </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
