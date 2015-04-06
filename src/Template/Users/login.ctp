<div id="content_title">
	<h1>
		Log in
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
			'password',
			[
				'class' => 'form-control',
				'placeholder' => 'Enter your password',
				'div' => [
					'class' => 'form-group'
				]
			]
		);
		echo $this->Form->input(
			'remember_me',
			[
				'type' => 'checkbox',
				'label' => [
					'text' => ' Remember me'
				]
			]
		);
		echo $this->Form->end([
			'label' => 'Log in',
			'class' => 'btn btn-default'
		]);
		echo $this->Html->link(
			'Forgot password?',
			[
				'controller' => 'users',
				'action' => 'forgot_password'
			]
		);
	?>
</div>