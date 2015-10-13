<div id="content_title">
	<h1>
		<?= $title_for_layout ?>
	</h1>
</div>

<div class="content_box">
	<?= $this->element('Thoughts/form', compact('thought')) ?>
</div>