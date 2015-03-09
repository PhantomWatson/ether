<ul>
	<?php foreach ($results as $result): ?>
		<li>
			<?php if ($result->thought_anonymous && ! $result->comment_id): ?>
				An anonymous user
			<?php else: ?>
				User #<?php echo $result->user['color']; ?>
			<?php endif; ?>

			<?php if ($result->comment_id): ?>
				commented on
			<?php else: ?>
				posted
			<?php endif; ?>

			thought #<?php echo $result->thought_id; ?> (<?php echo $result->thought_word; ?>)

			at <?php echo $result->created; ?>
		</li>
	<?php endforeach; ?>
</ul>