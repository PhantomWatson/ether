<?php
/**
 * @var \Gourmet\CommonMark\View\Helper\CommonMarkHelper $markdownHelper
 * @var \App\View\AppView $this
 */
?>
<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<p>
    The Question Abstractor pulls a random selection of questions (or things that look like questions) from Thoughts.
    Read, marvel, enjoy, and consider following the question to its Thought of origin and leaving a comment with an
    answer.
</p>

<?php $markdownHelper = $this->loadHelper('Gourmet/CommonMark.CommonMark'); ?>

<ul id="questions">
    <?php foreach ($questions as $question): ?>
        <li>
            <span class="question">
                <?php
                    $formattedQuestion = $markdownHelper->convertToHtml($question['question']);
                    echo str_replace(['<p>', '</p>'], '', $formattedQuestion);
                ?>
            </span>
            <span class="author">
                <?= $this->Html->link(
                    $question['color'] ?
                        'asked in a thought about <strong>' . $question['word'] . '</strong> by' :
                        'asked anonymously in a thought about <strong>' . $question['word'] . '</strong>',
                    [
                        'controller' => 'Thoughts',
                        'action' => 'word',
                        $question['word'],
                        '#' => 't'.$question['thoughtId']
                    ],
                    ['escape' => false]
                ) ?>
                <?php if ($question['color']): ?>
                    <?= $this->element('colorbox', [
                        'color' => $question['color'],
                        'anonymous' => false
                    ]) ?>
                <?php endif; ?>
            </span>
        </li>
    <?php endforeach; ?>
</ul>
