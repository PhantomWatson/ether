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
					['controller' => 'Thoughts', 'action' => 'index']
				); ?>
			</li>
			<li>
				<?php echo $this->Html->link(
					'Random',
					['controller' => 'Thoughts', 'action' => 'random'],
					['id' => 'random_link']
				); ?>
			</li>
			<li>
				<?php echo $this->Html->link(
					'About',
					['controller' => 'Pages', 'action' => 'about']
				); ?>
			</li>

			<?php if (isset($loggedIn) && $loggedIn): ?>
				<li>
					<?php echo $this->Html->link(
						'Think',
						['controller' => 'Thoughts', 'action' => 'add']
					); ?>
				</li>
				<li>
					<?php echo $this->Html->link(
						'Messages',
						['controller' => 'Messages', 'action' => 'index'],
						['class' => $hasNewMessages ? 'new_messages' : '']
					); ?>
				</li>
				<li>
					<?php echo $this->Html->link(
						'Account',
						['controller' => 'Users', 'action' => 'account']
					); ?>
				</li>
				<li>
					<?php echo $this->Html->link(
						'Logout',
						['controller' => 'Users', 'action' => 'logout']
					); ?>
				</li>
			<?php else: ?>
				<li>
					<?php echo $this->Html->link(
						'Login',
						['controller' => 'Users', 'action' => 'login'],
						['id' => 'login_link']
					); ?>
				</li>
				<li>
					<?php echo $this->Html->link(
						'Register',
						['controller' => 'Users', 'action' => 'register'],
						['id' => 'register_link']
					); ?>
				</li>
			<?php endif; ?>
			<li>
				<a href="http://www.facebook.com/EtherThoughtRepository">
					<i class="fa fa-facebook-official" title="Facebook"></i>
				</a>
			</li>
		</ul>
		<?php
			use Cake\Routing\Router;
			$goto_url = Router::url(['controller' => 'Thoughts', 'action' => 'word']);
		?>
		<form class="navbar-form navbar-left" role="search" action="<?php echo $goto_url; ?>" method="post">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Go to thoughtword..." required="required" name="data[Thought][word]" />
			</div>
		</form>
	</div>
</nav>
<?php //$this->Js->buffer("setupHeaderLinks();"); ?>