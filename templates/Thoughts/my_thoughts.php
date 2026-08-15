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

    <table class="table my-thoughts">
        <thead>
            <tr>
                <th>Thoughtword</th>
                <th>Comments</th>
                <th class="short-content">Anonymous</th>
                <th class="short-content">Private</th>
                <th>When</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($thoughts as $thought): ?>
                <tr>
                    <td class="word">
                        <?= $this->Html->link($thought->word, $thought->url) ?>
                    </td>
                    <td class="short-content">
                        <?= $thought->total_comments ?: '' ?>
                    </td>
                    <td class="short-content">
                        <?= $thought->anonymous ? '<i class="fa-solid fa-user-secret"></i>' : '' ?>
                    </td>
                    <td class="short-content">
                        <?= $thought->hidden ? '<i class="fa-solid fa-eye-slash"></i>' : '' ?>
                    </td>
                    <td class="when">
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
