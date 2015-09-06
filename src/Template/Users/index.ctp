<div id="content_title">
	<h1>
		Thinkers
	</h1>
</div>

<div class="row">
    <div class="col-sm-offset-2 col-sm-8">
        <p>
        	Each of these colors represents a Thinker. Click to view profile.
        </p>

        <p>
        	Curious about who's most active?
        	<a href="#" id="resize">
        		Resize colorboxes according to number of thoughts thunk.
        	</a>
        </p>

        <div class="users_index">
        	<?php
        		// Surely there's a less awkward way to do this
        		$maxCount = 0;
        		foreach ($colors as $group => $thinker) {
        			foreach ($thinker as $color => $thoughtCount) {
        				if ($thoughtCount > $maxCount) {
        					$maxCount = $thoughtCount;
        				}
        			}
        		}

        		foreach ($colors as $group => $thinker) {
        			foreach ($thinker as $color => $thoughtCount) {
        				$resizePercent = round(($thoughtCount / $maxCount) * 100);
        				$resizePercent = max($resizePercent, 5);
        				echo $this->Html->link(
        					'',
        					['controller' => 'Users', 'action' => 'view', $color],
        					[
        						'escape' => false,
        						'title' => 'View profile',
        						'class' => 'colorbox',
        						'data-resize' => $resizePercent,
        						'style' => 'background-color: #'.$color.';'
        					]
        				);
        			}
        		}
        	?>
        </div>
    </div>
</div>

<?php $this->append('buffered_js'); ?>
	userIndex.init();
<?php $this->end(); ?>