<div id="content_title">
	<h1>
		Thinkers
	</h1>
</div>

<div class="row">
    <div class="col-sm-offset-2 col-sm-8">
        <p>
        	Each of these colors represents a Thinker. Click to view profiles.
        </p>

        <p>
        	Curious about who's most active?
        	<a href="#" id="resize">
        		Resize colorboxes according to number of thoughts thunk.
        	</a>
        </p>

        <div class="users_index">
            <?= $this->element('Users'.DS.'index') ?>
        </div>
    </div>
</div>

<?php $this->append('buffered_js'); ?>
	userIndex.init();
<?php $this->end(); ?>