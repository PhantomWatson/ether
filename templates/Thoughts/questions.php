<?php
/**
 * @var \App\View\AppView $this
 * @var string $title_for_layout
 * @var array $questions
 *
 */

/** @var \App\Model\Table\ThoughtsTable $thoughtsTable */
$thoughtsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Thoughts');
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

<ul id="questions">
    <?php foreach ($questions as $question): ?>
        <li>
            <span class="question">
                <?php
                    $formattedQuestion = $thoughtsTable->parseMarkdown($question['question']);
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
