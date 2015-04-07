<?php if (isset($anonymous) && $anonymous): ?>
	<div class="colorbox anonymous_colorbox" title="Contributed anonymously"></div>
<?php elseif ($color == 'phanto'): ?>
	<div class="colorbox" style="text-align: center;" title="Phantom">
		P
	</div>
<?php elseif (! $color): ?>
	<div class="colorbox anonymous_colorbox" title="(Thinker not found)"></div>
<?php else: ?>
	<?php echo $this->Html->link(
		'',
		array(
			'controller' => 'Users',
			'action' => 'view',
			$color
		),
		array(
			'escape' => false,
			'title' => 'View Thinker #'.$color.'\'s profile',
			'id' => (isset($colorboxId) ? $colorboxId : ''),
			'class' => 'colorbox'.(isset($class) ? " $class" : ''),
			'style' => 'background-color: #'.$color.';'
		)
	); ?>
<?php endif; ?>