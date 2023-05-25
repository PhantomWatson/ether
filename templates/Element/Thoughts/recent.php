<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Thought[]|\App\Model\Entity\Comment[] $recentActivity
 */

use App\View\AppView;

/**
 * @param \App\View\AppView $appView AppView object
 * @param \App\Model\Entity\Thought|\App\Model\Entity\Comment $action Either a thought or a comment
 * @return string
 */
function getInfo(AppView $appView, $action): string
{
    $info = $appView->element('colorbox', [
        'color' => $action->user['color'],
        'anonymous' => $action->thought_anonymous && ! $action->comment_id,
        'noLink' => true,
    ]);
    $info .= $action->comment_id ? ' commented ' : ' thought ';
    $timeAgo = $appView->Time->timeAgoInWords(
        $action->created,
        ['end' => '+100 years']
    );
    if (stripos($timeAgo, ',') !== false) {
        $timeAgo = substr($timeAgo, 0, strpos($timeAgo, ','));
        if (stripos($timeAgo, 'ago') === false) {
            $timeAgo .= ' ago';
        }
    }
    $info .= $timeAgo;

    return '<div class="info">' . $info . '</div>';
}

?>
<?php $this->Paginator->options([
    'model' => 'Thought',
    'url' => [
        'controller' => 'Thoughts',
        'action' => 'recent'
    ]
]); ?>

<?php if ($this->Paginator->hasPrev()): ?>
    <ul class="nav">
        <?= $this->Paginator->prev(
            '&uarr; &uarr; &uarr;',
            ['escape' => false]
        ) ?>
    </ul>
<?php endif; ?>

<ul>
    <?php foreach ($recentActivity as $action): ?>
        <li>
            <?= $this->Html->link(
                '<span class="word">' . $action->thought_word . '</span>' . getInfo($this, $action),
                [
                    'controller' => 'Thoughts',
                    'action' => 'word',
                    $action->thought_word,
                    '#' => $action->comment_id ? 'c' . $action->comment_id : 't' . $action->thought_id,
                ],
                [
                    'class' => 'thoughtword',
                    'escape' => false,
                ]
            ) ?>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ($this->Paginator->hasNext()): ?>
    <ul class="nav">
        <?= $this->Paginator->next(
            '&darr; &darr; &darr;',
            ['escape' => false]
        ) ?>
    </ul>
<?php endif; ?>

<?php $this->append('buffered_js'); ?>
    recentActivity.init();
<?php $this->end(); ?>
