<div id="content_title">
	<h1>
		<?php echo $title_for_layout; ?>
	</h1>
</div>

<div class="content_box">
	<?php
		echo $this->Form->create('User');
		echo $this->Form->input(
			'email',
			[
				'class' => 'form-control',
				'placeholder' => 'Enter your email address',
				'div' => [
					'class' => 'form-group'
				]
			]
		);
		echo $this->Form->input(
			'new_password',
			[
				'label' => 'Password',
				'type' => 'password',
				'class' => 'form-control',
				'placeholder' => 'Enter your password',
				'div' => [
					'class' => 'form-group'
				]
			]
		);
		echo $this->Form->input(
			'confirm_password',
			[
				'type' => 'password',
				'class' => 'form-control',
				'placeholder' => 'Enter your password',
				'div' => [
					'class' => 'form-group'
				]
			]
		);
	?>

	<div class="input form-group" id="reg_color_input">
		<label>
			Color
		</label>
		<div class="ajax_message"></div>
		<input type="text" size="7" maxlength="7" name="data[color]" id="color_hex" value="<?php echo $this->request->data['color']; ?>" class="form-control color" />
		<div class="error-message">
			<?php if (false && isset($this->validationErrors['User']['color'])): ?>
				<?php echo implode('<br />', $this->validationErrors['User']['color']); ?>
			<?php endif; ?>
		</div>
		<?php if (isset($random_color)): ?>
			<div class="footnote">
				We've pre-selected a random color for you, but feel free to change it.
			</div>
		<?php endif; ?>
	</div>

	<div class="input">
		<label>
			Human?
		</label>
		<?php echo $this->Recaptcha->display(); ?>
		<?php if (isset($recaptcha_error)): ?>
			<div class="error-message">
				Invalid CAPTCHA response. Please try again.
			</div>
		<?php endif; ?>
	</div>

	<?php
		echo $this->Form->submit(
			'Register',
			['class' => 'btn btn-default']
		);
		echo $this->Form->end();
	?>
</div>

<?php $this->Html->script('/jscolor/jscolor.js', ['block' => true]); ?>
<?php $this->append('buffered_js'); ?>
	registrationForm.init();
<?php $this->end(); ?>