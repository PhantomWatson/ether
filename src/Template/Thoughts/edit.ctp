<div id="content_title">
	<h1>
		<?php echo $title_for_layout; ?>
	</h1>
</div>

<div class="content_box">
	<?php echo $this->element('Thoughts/form', compact('thought')); ?>
</div>