<a name="t<?php echo $thought['id']; ?>"></a>
<div class="thought">
    <?php echo $this->element('colorbox', [
        'color' => $thought['user']['color'],
        'anonymous' => $thought['anonymous']
    ]); ?>
    <?php if ($userId == $thought['user']['id']): ?>
        <div class="controls">
            <?php echo $this->Html->link(
                'Edit',
                ['controller' => 'Thoughts', 'action' => 'edit', $thought['id']]
            ); ?>
            |
            <?php echo $this->Form->postLink(
                'Delete',
                ['controller' => 'Thoughts', 'action' => 'delete', $thought['id']],
                ['confirm' => 'Are you sure that you want to remove this thought?']
            ); ?>
        </div>
    <?php endif; ?>
    <div class="info">
        <?php echo $this->Time->abbreviatedTimeAgoInWords($thought['created']); ?>
        |
        <?php echo $this->Html->link(
            'Link',
            ['controller' => 'Thoughts', 'action' => 'word', $word, '#' => 't'.$thought['id']]
        ); ?>
    </div>
    <div class="body">
        <?php
            echo $thought['parsedTextCache'];
            /*
            echo $this->element('thoughts/formatted_thought', array(
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
                <?= $this->element('comments/view', [
                    'comment' => $comment['comment'],
                    'color' => $comment['user']['color'],
                    'commentId' => $comment['id']
                ]) ?>
            <?php endforeach; ?>
            <div id="newcomment<?php echo $thought['id']; ?>view"></div>
            <div id="newcomment<?php echo $thought['id']; ?>add" style="display: none;" class="add_comment">
                <?= $this->element('comments/add', ['thoughtId' => $thought['id']]) ?>
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