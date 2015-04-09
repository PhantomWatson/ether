<div class="user_profile">
	<div id="content_title">
		<h1>
			Thinker:
			<?php echo $this->element('colorbox', ['color' => $user['color']]); ?>
		</h1>
		<p class="subtitle">
			Color: #<?php echo $user['color']; ?>
			<br />
			<?php echo $this->Html->link(
				'View all Thinkers',
				['controller' => 'Users', 'action' => 'index']
			); ?>
		</p>
	</div>

	<div class="content_box introspection">
		<h2>
			Introspection
		</h2>
		<?php if (empty($user['profile'])): ?>
			<em>
				This Thinker has not yet introspected.
			</em>
		<?php else: ?>
			 <?php echo stripslashes($user['profile']); ?>
		<?php endif; ?>
	</div>

	<div class="content_box">
		<h2>
			Thoughts
		</h2>
		<?php if (empty($user['thoughts'])): ?>
			<em>
				This Thinker has not yet thunk.
			</em>
		<?php else: ?>
			<div class="thoughtwords">
				<?php foreach ($user['thoughts'] as $thought): ?>
					<?php echo $this->Html->link(
						$thought['word'],
						['controller' => 'Thoughts', 'action' => 'word', $thought['word'], '#' => 't'.$thought['id']],
						['class' => 'thoughtword']
					); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php /* if ((isset($messagesCount) && $messagesCount) || $user['acceptMessages']): ?>
		<div class="content_box">
			<h2>
				Communication
			</h2>
			<?php if (isset($messagesCount) && $messagesCount): ?>
				<?php echo $this->Html->link(
					"View $messagesCount message".($messagesCount == 1 ? '' : 's')." between you and this Thinker.",
					array(
						'controller' => 'messages',
						'action' => 'with',
						'color' => $color
					)
				); ?>
			<?php endif; ?>
			<?php if ($user['acceptMessages']): ?>
				<div id="profile_send_message">
					<?php echo $this->element('messages/send', array(
						'recipient_id' => $user['id']
					)); ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; */ ?>
</div>

<?php $this->append('buffered_js'); ?>
	setupThoughtwordLinks($('.thoughtwords'));
<?php $this->end(); ?>