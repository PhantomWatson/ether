<nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation" id="header">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
			<span class="sr-only">
				Toggle navigation
			</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a href="/" id="site_title">
			<h1>
				Ether
			</h1>
			<span class="subtitle">
				Thought Repository
			</span>
		</a>
	</div>

	<div class="collapse navbar-collapse" id="navbar-collapse">
		<ul class="nav navbar-nav">
			<li>
				<?php echo $this->Html->link(
					'Thoughts',
					array(
						'controller' => 'Thoughts',
						'action' => 'index'
					)
				); ?>
			</li>
			<li>
				<?php echo $this->Html->link(
					'Random',
					array(
						'controller' => 'Thoughts',
						'action' => 'random'
					),
					array(
						'id' => 'random_link'
					)
				); ?>
			</li>
			<li>
				<?php echo $this->Html->link(
					'About',
					array(
						'controller' => 'Pages',
						'action' => 'about'
					)
				); ?>
			</li>

			<?php if (isset($logged_in) && $logged_in): ?>
				<li>
					<?php echo $this->Html->link(
						'Think',
						array(
							'controller' => 'Thoughts',
							'action' => 'add'
						)
					); ?>
				</li>
				<li>
					<?php echo $this->Html->link(
						'Messages',
						array(
							'controller' => 'Messages',
							'action' => 'index'
						),
						array(
							'class' => $new_messages ? 'new_messages' : ''
						)
					); ?>
				</li>
				<li>
					<?php echo $this->Html->link(
						'Account',
						array(
							'controller' => 'Users',
							'action' => 'account'
						)
					); ?>
				</li>
				<li>
					<?php echo $this->Html->link(
						'Logout',
						array(
							'controller' => 'Users',
							'action' => 'logout'
						)
					); ?>
				</li>
			<?php else: ?>
				<li>
					<?php echo $this->Html->link(
						'Login',
						array(
							'controller' => 'Users',
							'action' => 'login'
						),
						array(
							'id' => 'login_link'
						)
					); ?>
				</li>
				<li>
					<?php echo $this->Html->link(
						'Register',
						array(
							'controller' => 'Users',
							'action' => 'register'
						),
						array(
							'id' => 'register_link'
						)
					); ?>
				</li>
			<?php endif; ?>
			<li>
				<a href="http://www.facebook.com/EtherThoughtRepository">
					<i class="icon-facebook-sign" title="Facebook"></i>
				</a>
			</li>
		</ul>
		<?php
			use Cake\Routing\Router;
			$goto_url = Router::url(array(
				'controller' => 'Thoughts',
				'action' => 'word'
			));
		?>
		<form class="navbar-form navbar-left" role="search" action="<?php echo $goto_url; ?>" method="post">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Go to thoughtword..." required="required" name="data[Thought][word]" />
			</div>
		</form>
	</div>
</nav>
<?php //$this->Js->buffer("setupHeaderLinks();"); ?>