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
            ['id' => 'ThoughtAddForm']
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

        <label class="control-label" for="thought-rich-text-editor">Thought</label>
        <div id="thought-rich-text-editor"></div>
        <textarea id="input-thought-body" name="thought"><?= $thought->thought ?></textarea>

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

<script>
    new ThoughtForm({
        toastui,
        DOMPurify,
        markdown: <?= json_encode($thought->thought ?? '') ?>,
    });
</script>
