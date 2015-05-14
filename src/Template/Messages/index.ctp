<div id="conversations" class="content_box">
	<?php if (empty($conversations)): ?>
		<p class="no_messages">
			No messages sent or received. :(
		</p>
	<?php else: ?>
		<div id="conversations_index">
			<ul>
				<?php foreach ($conversations as $other_user_id => $conversation): ?>
					<li>
						<a href="#" data-color="<?= $conversation['color'] ?>">
							<span class="penpal">
								<span class="colorbox" style="background-color: #<?= $conversation['color'] ?>"></span>
								#<?= $conversation['color'] ?>
							</span>
							<span class="time">
							    <?= $this->Time->abbreviatedTimeAgoInWords($conversation['time']) ?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div id="selected_conversation_wrapper">
			<p class="select_prompt">
				Select a conversation on the left.
			</p>
		</div>

		<?php $this->append('buffered_js'); ?>

			messagesPage.init();

			<?php if ($selected_user_id): ?>
				$this->Js->buffer("messagesPage.selectConversation($selected_user_id, true);");
			<?php endif; ?>

		<?php $this->end(); ?>

	<?php endif; ?>
</div>

<?php $this->append('buffered_js'); ?>
	$('#header .new_messages').removeClass('new_messages');
<?php $this->end(); ?>