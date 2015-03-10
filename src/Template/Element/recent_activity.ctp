<ul>
	<?php foreach ($actions as $action): ?>
		<li>
			<?php if ($action->thought_anonymous && ! $action->comment_id): ?>
				An anonymous user
			<?php else: ?>
				User #<?php echo $action->user['color']; ?>
			<?php endif; ?>

			<?php if ($action->comment_id): ?>
				commented on
			<?php else: ?>
				posted
			<?php endif; ?>

			thought #<?php echo $action->thought_id; ?> (<?php echo $action->thought_word; ?>)

			at <?php echo $action->created; ?>
		</li>
	<?php endforeach; ?>
</ul>