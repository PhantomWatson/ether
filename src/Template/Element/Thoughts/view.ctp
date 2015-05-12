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
			<?php echo $this->Html->link(
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
				<a name="c<?php echo $comment['id']; ?>"></a>
				<?php
					/* echo $this->element('comments/view', [
						'comment' => $comment['comment'],
						'color' => $comment['user']['color'],
						'id' => $comment['id'],
						'thoughtwords' => $thoughtwords
					]); */
				?>
			<?php endforeach; ?>
			<div id="newcomment<?php echo $thought['id']; ?>view"></div>
			<div id="newcomment<?php echo $thought['id']; ?>add" style="display: none;" class="add_comment">
				<?php //echo $this->element('comments/add', compact('thoughtId'))); ?>
			</div>
			<div class="post" id="newcomment<?php echo $thought['id']; ?>button">
				<?php if ($this->request->session()->check('Auth.User.id')): ?>
					<a href="#">
						Leave Comment
					</a>
					<?php $this->append('buffered_js'); ?>
						$('#newcomment<?= $thought['id'] ?>button').click(function (event) {
							event.preventDefault();
							add_comment(<?= $thought['id'] ?>);
						});
					<?php $this->end(); ?>
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