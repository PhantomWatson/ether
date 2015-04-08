<?php $this->Paginator->options([
	'model' => 'Thought',
	'url' => [
		'controller' => 'Thoughts',
		'action' => 'recent'
	]
]); ?>

<div class="pagination">
	<?php if ($this->Paginator->hasPrev()): ?>
		<?php echo $this->Paginator->prev(
			'&uarr; &uarr; &uarr;',
			['escape' => false]
		); ?>
	<?php endif; ?>
</div>

<ul>
	<?php foreach ($recentActivity as $action): ?>
		<li>
			<?php
				if ($action->comment_id) {
					$target = 'c'.$action->comment_id;
				} else {
					$target = 't'.$action->thought_id;
				}
				echo $this->Html->link(
					$action->thought_word,
					[
						'controller' => 'Thoughts',
						'action' => 'word',
						$action->thought_word,
						'#' => $target
					],
					['class' => 'thoughtword']
				);
			?>
			<div class="info">
				<?php echo $this->element('colorbox', [
					'color' => $action->user['color'],
					'anonymous' => $action->thought_anonymous && ! $action->comment_id
				]); ?>
				<?php if ($action->comment_id): ?>
					commented
				<?php else: ?>
					<strong>
						thought
					</strong>
				<?php endif; ?>
				<?php
					$timeAgo = $this->Time->timeAgoInWords(
						$action->created,
						['end' => '+10 years']
					);
					if (stripos($timeAgo, ',') !== false) {
						$timeAgo = substr($timeAgo, 0, strpos($timeAgo, ','));
						if (stripos($timeAgo, 'ago') === false) {
							$timeAgo .= ' ago';
						}
					}
					echo $timeAgo;
				?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>

<div class="pagination">
	<?php if ($this->Paginator->hasNext()): ?>
		<?php echo $this->Paginator->next(
			'&darr; &darr; &darr;',
			['escape' => false]
		); ?>
	<?php endif; ?>
</div>

<?php $this->append('buffered_js'); ?>
	recentActivity.init();
<?php $this->end(); ?>