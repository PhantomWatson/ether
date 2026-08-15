<?php
/**
 * @param \App\View\AjaxView $this
 * @param \App\Model\Entity\Thought $thoughts
 * @var \App\View\AppView $this
 * @var string $title_for_layout
 * @var \App\Model\Entity\Thought[]|\Cake\Collection\CollectionInterface $thoughts
 */
$count = count($thoughts);
?>

<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<?php if ($thoughts): ?>
    <p>
        <?php if ($count > 1): ?>
            You have thunk <?= number_format($count) ?> thoughts.
        <?php else: ?>
            Here's the only thought that you've thunk. It looks lonely, though. Want to
            <?= $this->Html->link(
                'post more',
                ['controller' => 'Thoughts', 'action' => 'add'],
            ) ?>?
        <?php endif; ?>
    </p>

    <table class="table my-thoughts sortable">
        <thead>
            <tr>
                <th>Thoughtword</th>
                <th class="short-content">Comments</th>
                <th class="short-content">Anonymous</th>
                <th class="short-content">Private</th>
                <th>When</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($thoughts as $thought): ?>
                <tr>
                    <td class="word">
                        <?= $this->Html->link($thought->word, ['action' => 'view', $thought->id]) ?>
                    </td>
                    <td class="short-content" data-sort="<?= $thought->total_comments ?: 0 ?>">
                        <?= $thought->total_comments ?: '' ?>
                    </td>
                    <td class="short-content" data-sort="<?= $thought->anonymous ? 1 : 0 ?>">
                        <?= $thought->anonymous ? '<i class="fa-solid fa-user-secret"></i>' : '' ?>
                    </td>
                    <td class="short-content" data-sort="<?= $thought->hidden ? 1 : 0 ?>">
                        <?= $thought->hidden ? '<i class="fa-solid fa-eye-slash"></i>' : '' ?>
                    </td>
                    <td class="when" data-sort="<?= $thought->created->getTimestamp() ?>">
                        <?= $thought->created->format('F j, Y') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/gh/tofsjonas/sortable@latest/dist/sortable.min.js"></script>
<?php else: ?>
    <p>
        You have not thunk any thoughts yet.
        <?= $this->Html->link(
            'Think a thought, forthwith!',
            ['controller' => 'Thoughts', 'action' => 'add'],
        ) ?>
    </p>
<?php endif; ?>
