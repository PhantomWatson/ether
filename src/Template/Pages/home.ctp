<div class="row">
    <div class="col-sm-6 col-sm-offset-3">
        <h3>
            Welcome to Ether, an experimental thought repository.
        </h3>
        <p>
            Here, each Thought is found under a single word that it relates to, and each Thinker is identified by only a unique color. <span class="aside">*</span>
        </p>
        <p>
            Ether has collected <?= number_format($thoughtCount) ?> Thoughts from <?= number_format($thinkerCount) ?> Thinkers since 2006.
        </p>
        <p>
            <?php echo $this->Html->link(
                'Add a Thought',
                ['controller' => 'Thoughts', 'action' => 'add'],
                ['class' => 'btn btn-default']
            ); ?>
            <?php echo $this->Html->link(
                'Create an Account',
                ['controller' => 'Users', 'action' => 'register'],
                ['class' => 'btn btn-default']
            ); ?>
            <?php echo $this->Html->link(
                'About Ether',
                ['controller' => 'Pages', 'action' => 'about'],
                ['class' => 'btn btn-link']
            ); ?>
            <a href="http://www.facebook.com/EtherThoughtRepository" class="btn btn-link social_icon">
                <i class="fa fa-facebook-official" title="Facebook"></i>
            </a>
            <a href="https://github.com/PhantomWatson/ether3" class="btn btn-link social_icon">
                <i class="fa  fa-github" title="GitHub"></i>
            </a>
        </p>
        <p>
            <span class="aside">* Would we sound pretentious if we used the word "chromonymous"?</span>
        </p>
    </div>
</div>

<hr />

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