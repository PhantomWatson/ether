<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Thought $thought
 */
?>
<div class="row">
    <div class="offset-sm-2 col-sm-8">
        <?= $this->Form->create(
            $thought,
            [
                'url' => [
                    'controller' => 'Thoughts',
                    'action' => $this->request->getParam('action') == 'edit' ? 'edit' : 'add'
                ],
                'id' => 'ThoughtAddForm'
            ]
        ) ?>

        <?php if (isset($suggestedThoughtwords)): ?>
            <div id="suggested-words" class="card">
                <div class="card-body">
                    <p>
                        Not sure what to write about?
                        How about one of these commonly-used words that no one has written about yet:
                    </p>
                    <ul>
                        <?php foreach ($suggestedThoughtwords as $word): ?>
                            <li>
                                <button><?= $word ?></button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <?php $this->append('buffered_js'); ?>
                new SuggestedWords();
            <?php $this->end(); ?>
        <?php endif; ?>

        <?= $this->Form->control(
            'word',
            [
                'class' => 'form-control',
                'label' => [
                    'class' => 'control-label',
                    'text' => 'Thoughtword'
                ],
                'placeholder' => 'Enter a word to associate your thought with',
                'id' => 'input-thought-word'
            ]
        ) ?>

        <?= $this->Form->control(
            'thought',
            [
                'class' => 'form-control',
                'label' => [
                    'class' => 'control-label',
                    'text' => 'Thought'
                ],
                'type' => 'textarea',
                'placeholder' => 'What\'s on your mind?',
                'id' => 'input-thought-body'
            ]
        ) ?>

        <p>
            Styles like *<em>italics</em>* and **<strong>bold</strong>** can be applied with Markdown. For a full list of supported styles, consult the
            <?= $this->Html->link('Markdown styling guide',
                [
                    'controller' => 'Pages',
                    'action' => 'markdown'
                ],
                ['target' => '_blank']
            ) ?>
        </p>

        <div>
            <p id="options-header">
                Options
            </p>
            <?= $this->Form->control(
                'comments_enabled',
                [
                    'label' => 'Allow comments',
                    'type' => 'checkbox'
                ]
            ) ?>
            <?= $this->Form->control(
                'anonymous',
                [
                    'label' => 'Post anonymously',
                    'type' => 'checkbox'
                ]
            ) ?>
        </div>

        <?= $this->Form->submit(
            'Think',
            ['class' => 'btn btn-primary btn-block']
        ) ?>
        <?= $this->Form->end(); ?>

    </div>
</div>
