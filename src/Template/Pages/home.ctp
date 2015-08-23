<div class="row" id="welcome">
    <div class="col-sm-2 col-sm-offset-3">
        <h2>
            Ether
        </h2>
        <h3>
            Thought Repository
        </h3>
    </div>
    <div class="col-sm-4">
        <?= $this->Html->link(
            'Add a Thought',
            ['controller' => 'Thoughts', 'action' => 'add'],
            ['class' => 'btn btn-default']
        ) ?>
        <?= $this->Html->link(
            'About Ether',
            ['controller' => 'Pages', 'action' => 'about'],
            ['class' => 'btn btn-link']
        ) ?>
        <a href="http://www.facebook.com/EtherThoughtRepository" class="btn btn-link social_icon">
            <i class="fa fa-facebook-official" title="Facebook"></i>
        </a>
        <a href="https://github.com/PhantomWatson/ether3" class="btn btn-link social_icon">
            <i class="fa  fa-github" title="GitHub"></i>
        </a>
    </div>
</div>

<?php if ($randomThought): ?>
    <hr />

    <div class="row" id="frontpage_random_thought">
        <div class="col-sm-6 col-sm-offset-3">
            <h3>
                Random Thought:
            </h3>
            <span class="word">
                <?= $this->Html->link(
                    $randomThought->word,
                    [
                        'controller' => 'Thoughts',
                        'action' => 'word',
                        $randomThought->word,
                        '#' => 't'.$randomThought['id']
                    ]
                ) ?>
            </span>
            <br />
            <span class="thought_excerpt"><?= $randomThought->formatted_thought ?></span>
            <span class="byline">
                <?php if ($randomThought->anonymous): ?>
                    thought anonymously
                <?php else: ?>
                    thought by
                    <?= $this->element('colorbox', [
                        'color' => $randomThought->user['color'],
                        'anonymous' => $randomThought->anonymous
                    ]) ?>
                <?php endif; ?>
            </span>

        </div>
    </div>
<?php endif; ?>

<hr />

<div id="recent">
    <h2>
        Recent
    </h2>
	<?= $this->element('Thoughts'.DS.'recent') ?>
</div>
<div class="cloud">
    <?php if (empty($cloud)): ?>
        <p>
            Sorry, we couldn't find any thoughts in the database.
            <br />That's probably a bad sign. :(
        </p>
    <?php else: ?>
	   <?= $this->element('cloud', ['words' => $cloud]) ?>
	<?php endif; ?>
</div>