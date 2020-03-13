<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="thought" data-formatting-key="<?= $thought['formatting_key'] ?>" data-thought-id="<?= $thought['id'] ?>">
    <div class="body">
        <?= $thought['formatted_thought'] ?>
    </div>
    <?php if ($thought['comments_enabled']): ?>
        <div class="comments">
            <?php foreach ($thought['comments'] as $comment): ?>
                <?= $this->element('Comments/view', compact('comment')) ?>
            <?php endforeach; ?>
            <div id="newcomment<?= $thought['id'] ?>view"></div>
            <div id="newcomment<?= $thought['id'] ?>add" style="display: none;" class="add_comment">
                <?= $this->element('Comments/add', ['thought' => $thought]) ?>
            </div>
            <div class="post" id="newcomment<?= $thought['id'] ?>button">
                <?php if ($this->request->getSession()->check('Auth.User.id')): ?>
                    <a href="#" class="add_comment" data-thought-id="<?= $thought['id'] ?>">
                        Leave Comment
                    </a>
                <?php else: ?>
                    <?= $this->Html->link(
                        'Log In to Leave Comment',
                        ['controller' => 'Users', 'action' => 'login']
                    ) ?>
                <?php endif; ?>
            </div>
            <br class="clear" />
        </div>
    <?php endif; ?>
</div>
