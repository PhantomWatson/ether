<!DOCTYPE html>
<html>
	<head>
		<?php echo $this->Html->charset(); ?>
		<title>
			<?php
				$title = 'Ether - Thought Repository';
				$title_for_layout = $this->fetch('title');
				if ($title_for_layout) {
					$title = 'Ether :: '.$title_for_layout;
				}
				echo $title;
			?>
		</title>
		<link rel="dns-prefetch" href="//ajax.googleapis.com" />
		<?php
			if ($debug) {
				//echo '<link rel="stylesheet/less" type="text/css" href="'.$this->Html->assetUrl('style.less', array('pathPrefix' => CSS_URL)).'" />';
				//echo '<script type="text/javascript">less = { env: \'development\' };</script>';
				//echo $this->Html->script('less-1.5.0.min');
			} else {
				echo $this->Html->css('style');
			}
			echo $this->Html->meta('icon');
			echo $this->fetch('meta');
			echo $this->Html->css('base.css');
    		echo $this->Html->css('cake.css');
		?>
		<!--[if !IE 7]>
			<style type="text/css">
				#content_outer {display: table; height: 100%}
			</style>
		<![endif]-->
		<meta name="title" content="<?php echo $title; ?>" />
		<meta name="description" content="Ether: An experimental freeform thought repository. What's on YOUR mind?" />
		<meta name="author" content="Phantom Watson" />
		<meta name="language" content="en" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<?php
			//echo $this->element('flash_messages');
			echo $this->Flash->render();
			//echo $this->element('header');
		?>

		<?php echo $this->fetch('overlay'); ?>

		<div id="content_outer">
			<div id="content">
				<?php echo $this->fetch('content'); ?>
			</div>
		</div>

		<?php //echo $this->element('footer'); ?>

		<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="/js/jquery-1.9.1.min.js"><\/script>')</script>

		<?php
			//echo $this->Html->script('script');
			//echo $this->Html->script('bootstrap.min.js');
			echo $this->fetch('script');
			//$this->Js->buffer("setupOnPopState();");
			//echo $this->Js->writeBuffer();
			//echo $this->element('analytics');
		?>

		<?php if ($this->fetch('buffered_js')): ?>
			<script>
				//<![CDATA[
					$(document).ready(function () {
						<?php echo $this->fetch('buffered_js'); ?>
					});
				//]]>
			</script>
		<?php endif; ?>
	</body>
</html>