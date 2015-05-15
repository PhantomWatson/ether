<?= $this->Form->create(
	$thought,
	[
		'url' => [
			'controller' => 'Thoughts',
			'action' => $this->request->action == 'edit' ? 'edit' : 'add'
		],
		'id' => 'ThoughtAddForm'
	]
) ?>

<?= $this->Form->input(
	'word',
	[
		'class' => 'form-control',
		'label' => [
			'class' => 'control-label',
			'text' => 'Thoughtword'
		],
		'placeholder' => 'Enter a word to associated your thought with'
	]
) ?>

<?= $this->Form->input(
	'thought',
	[
		'class' => 'form-control',
		'label' => [
			'class' => 'control-label',
			'text' => 'Thought'
		],
		'type' => 'textarea'
	]
) ?>
<div class="footnote">Allowable HTML: &lt;b&gt; &lt;i&gt;</div>

<div class="options row">
	<div class="form-group col-md-5">
		<?= $this->Form->input(
			'comments_enabled',
			[
				'label' => 'Allow comments',
				'type' => 'checkbox'
			]
		) ?>
	</div>

	<div class="form-group col-md-5">
		<?= $this->Form->input(
			'anonymous',
			[
				'label' => 'Post anonymously',
				'type' => 'checkbox'
			]
		) ?>
	</div>

	<div class="col-md-2">
		<?= $this->Form->submit(
			'Think',
			['class' => 'btn btn-default']
		) ?>
		<?= $this->Form->end(); ?>
	</div>
</div>