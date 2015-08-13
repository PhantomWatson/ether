<?php
    use Cake\Routing\Router;
?>
<div id="content_title">
    <h1>
        <?php echo $title_for_layout; ?>
    </h1>
</div>

<div id="conversations" class="row">
	<?php if (empty($conversations)): ?>
		<p class="no_messages">
			No messages sent or received. :(
		</p>
	<?php else: ?>
		<div id="conversations_index" class="col-sm-offset-2 col-sm-8">
		    <h2>
		        Select a Conversation
	        </h2>
			<ul>
				<?php foreach ($conversations as $other_user_id => $conversation): ?>
					<li>
						<?php
                            $url = Router::url(['action' => 'conversation', $conversation['color']]);
						?>
						<a href="<?= $url ?>" data-color="<?= $conversation['color'] ?>">
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
	<?php endif; ?>
</div>

<?php $this->append('buffered_js'); ?>
	$('#header .new_messages').removeClass('new_messages');
<?php $this->end(); ?>