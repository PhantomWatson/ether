<div id="recent">
	<?php echo $this->element('Thoughts'.DS.'recent'); ?>
</div>
<div class="cloud">
    <?php if (empty($topCloud)): ?>
        <p>
            Sorry, we couldn't find any thoughts in the database.
            <br />That's probably a bad sign. :(
        </p>
    <?php else: ?>
	   <?= $this->element('cloud', ['words' => $topCloud]) ?>
	<?php endif; ?>
</div>