<a name="t<?php echo $thought['id']; ?>"></a>
<div class="thought" data-formatting-key="<?= $thought['formatting_key'] ?>" data-thought-id="<?= $thought['id'] ?>">
    <div class="body">
        <?php
            echo $thought['formatted_thought'];
            /*
            echo $this->element('Thoughts/formatted_thought', array(
                'input' => $thought,
                'id' => $thought['id'],
                'type' => 'thought',
                'thoughtwords' => $thoughtwords
            ));
            */
        ?>
    </div>
    <?php if ($thought['comments_enabled']): ?>
        <div class="comments">
            <?php foreach ($thought['comments'] as $comment): ?>
                <?= $this->element('Comments/view', [
                    'comment' => $comment['comment'],
                    'color' => $comment['user']['color'],
                    'commentId' => $comment['id']
                ]) ?>
            <?php endforeach; ?>
            <div id="newcomment<?php echo $thought['id']; ?>view"></div>
            <div id="newcomment<?php echo $thought['id']; ?>add" style="display: none;" class="add_comment">
                <?= $this->element('Comments/add', ['thoughtId' => $thought['id']]) ?>
            </div>
            <div class="post" id="newcomment<?php echo $thought['id']; ?>button">
                <?php if ($this->request->session()->check('Auth.User.id')): ?>
                    <a href="#" class="add_comment" data-thought-id="<?= $thought['id'] ?>">
                        Leave Comment
                    </a>
                <?php else: ?>
                    <?php echo $this->Html->link(
                        'Log In to Leave Comment',
                        ['controller' => 'Users', 'action' => 'login']
                    ); ?>
                <?php endif; ?>
            </div>
            <br class="clear" />
        </div>
    <?php endif; ?>
</div>