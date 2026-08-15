<?php
/**
 * @param \App\View\AjaxView $this
 * @param \App\Model\Entity\Thought $thoughts
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

    <table class="table">
        <thead>
            <tr>
                <th>Thoughtword</th>
                <th>Anonymous</th>
                <th>Private</th>
                <th>When</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($thoughts as $thought): ?>
                <tr>
                    <td>
                        <?= $this->Html->link($thought->word, $thought->url) ?>
                    </td>
                    <td>
                        <?= $thought->anonymous ? 'Yes' : 'No' ?>
                    </td>
                    <td>
                        <?= $thought->private ? 'Yes' : 'No' ?>
                    </td>
                    <td>
                        <?= $thought->created->format('F j, Y') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>
        You have not thunk any thoughts yet.
        <?= $this->Html->link(
            'Think a thought, forthwith!',
            ['controller' => 'Thoughts', 'action' => 'add'],
        ) ?>
    </p>
<?php endif; ?>
