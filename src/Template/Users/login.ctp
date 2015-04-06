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
			array(
				'class' => 'form-control',
				'placeholder' => 'Enter your email address',
				'div' => array(
					'class' => 'form-group'
				)
			)
		);
		echo $this->Form->input(
			'password', 
			array(
				'class' => 'form-control',
				'placeholder' => 'Enter your password',
				'div' => array(
					'class' => 'form-group'
				)
			)
		);
		echo $this->Form->input(
			'remember_me', 
			array(
				'type' => 'checkbox', 
				'label' => array(
					'text' => ' Remember me'
				)
			)
		);
		echo $this->Form->end(array(
			'label' => 'Log in',
			'class' => 'btn btn-default'
		));
	?>
	<?php echo $this->Html->link(
		'Forgot password?',
		array(
			'controller' => 'users', 
			'action' => 'forgot_password'
		)
	); ?>
</div>