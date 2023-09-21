<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Thought $randomThought
 */

$features = [
    [
        'title' => '<i class="fa-solid fa-book"></i> Sponsoring Darkness Dreaming',
        'body' => '
            Ether is the proud sponsor of the third issue of
            <a href="http://darknessdreaming.com/">Darkness Dreaming</a>,
            a magazine dedicated to strange and dark writing and visual art, created by the
            talented <a href="http://lydiaburris.com/">Lydia Burris</a>, and available in print
            or as a digital download. Check it out!
        ',
    ],
    [
        'title' => '<i class="fa-solid fa-volume-high"></i> Listen',
        'body' => 'Look for the new
            <strong class="listenButton"><i class="fa-solid fa-play thought-action-icon"></i> Listen</strong>
            button. It\'ll read each thought to you so you can rest your eyes or use them to look at other things, like
            <a href="https://imgur.com/gallery/PZmQcma">this lovely picture</a> that I took outside of my house.'
    ],
    [
        'title' => $this->Html->link(
            '<i class="fa-solid fa-robot"></i> Thought Generator',
            ['controller' => 'Generator', 'action' => 'index'],
            ['escape' => false],
        ),
        'body' =>
            $this->Html->link(
                'Use the new Thought Generator',
                ['controller' => 'Generator', 'action' => 'index'],
                ['escape' => false],
            ) . ' to get a profound, confusing, and grammatically-questionable stream-of-consciousness based on existing Thoughts!
        ',
    ],
    [
        'title' =>  $this->Html->link(
            '<i class="fa-solid fa-circle-question"></i> Question Abstractor',
            ['controller' => 'Thoughts', 'action' => 'questions'],
            ['escape' => false],
        ),
        'body' =>
            'What are Thinkers asking about? Do you wonder the same thing? Do you know the answers? Behold, ' .
            $this->Html->link(
                'the Question Abstractor',
                ['controller' => 'Thoughts', 'action' => 'questions'],
                ['escape' => false],
            ) . '!',
    ],
];

?>
<div class="container mb-4">
    <div class="row align-items-center" id="welcome">
        <div class="col-sm-4">
            <h2>
                Ether
            </h2>
            <h3>
                is a thought repository
            </h3>
        </div>
        <div class="col-sm-8 card" id="frontpage_random_thought">
            <?php if ($randomThought): ?>
                <h3>
                    Random Thought:
                    <span class="word">
                        <?= $this->Html->link(
                            $randomThought->word,
                            [
                                'controller' => 'Thoughts',
                                'action' => 'word',
                                $randomThought->word,
                                '#' => 't' . $randomThought['id']
                            ]
                        ) ?>
                    </span>
                </h3>
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
            <?php endif; ?>
        </div>
    </div>

    <div class="row" id="features">
        <?php foreach ($features as $n => $feature): ?>
            <div class="col <?= $n ? 'visually-hidden' : '' ?>" data-feature-key="<?= $n ?>">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?= $feature['title'] ?>
                        </h5>
                        <p class="card-text">
                            <?= $feature['body'] ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="row" id="features-controls">
        <div class="col text-end">
            New stuff:
            <?php foreach ($features as $n => $feature): ?>
                <button data-feature-key="<?= $n ?>" class="btn btn-link btn-sm">
                    <?= $n + 1 ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div id="recent">
    <h2>
        Recent
    </h2>
    <?= $this->element('Thoughts' . DS . 'recent') ?>
</div>
<div class="cloud <?= isset($_GET['animate']) ? 'animate_hide' : '' ?>" id="frontpage_cloud">
    <?php if (empty($cloud)): ?>
        <p>
            Sorry, we couldn't find any thoughts in the database.
            <br />That's probably a bad sign. :(
        </p>
    <?php else: ?>
        <?= $this->element('cloud', ['words' => $cloud, 'animate' => isset($_GET['animate'])]) ?>
        <?php if (isset($_GET['animate'])): ?>
            <script>
                let cloud = document.getElementById('frontpage_cloud');
                cloud.className = 'cloud animate_show';
            </script>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    const buttons = document.querySelectorAll('#features-controls button');
    buttons.forEach((button) => {
        button.addEventListener('click', (event) => {
            // Hide all
            const shown = document.querySelector('#features .col:not(.visually-hidden)');
            shown.classList.add('visually-hidden');

            // Show selected
            const key = event.target.dataset.featureKey;
            const selected = document.querySelector('#features .col[data-feature-key="' + key + '"]');
            selected.classList.remove('visually-hidden');
        });
    });
</script>
