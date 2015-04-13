<div id="content_title">
	<h1>
		<?php echo $title_for_layout; ?>
	</h1>
</div>

<div class="content_box">
	<?php echo $this->Form->create(
		$thought,
		[
			'url' => ['controller' => 'Thoughts', 'action' => 'add'],
			'id' => 'ThoughtAddForm'
		]
	); ?>

	<?php
		echo $this->Form->input(
			'word',
			[
				'class' => 'control-label form-control',
				'label' => [
					'class' => 'control-label',
					'text' => 'Thoughtword'
				],
				'placeholder' => 'Enter a word to associated your thought with',
				'value' => isset($word) ? $word : null
			]
		);
	?>

	<?php
		echo $this->Form->input(
			'thought',
			[
				'class' => 'form-control',
				'label' => [
					'class' => 'control-label',
					'text' => 'Thought'
				],
				'type' => 'textarea'
			]
		);
	?>
	<div class="footnote">Allowable HTML: &lt;b&gt; &lt;i&gt;</div>

	<div class="options row">
		<div class="form-group col-md-5">
			<?php echo $this->Form->input(
				'comments_enabled',
				[
					'label' => 'Allow comments',
					'type' => 'checkbox'
				]
			); ?>
		</div>

		<div class="form-group col-md-5">
			<?php echo $this->Form->input(
				'anonymous',
				[
					'label' => 'Post anonymously',
					'type' => 'checkbox'
				]
			); ?>
		</div>

		<div class="col-md-2">
			<?php
				echo $this->Form->submit(
					'Think',
					['class' => 'btn btn-default']
				);
				echo $this->Form->end();
			?>
		</div>
	</div>
</div>