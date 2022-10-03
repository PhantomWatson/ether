<?php
/**
 * @var \App\Model\Entity\Thought $newThought
 * @var \App\Model\Entity\Thought[]|\Cake\Collection\CollectionInterface $thoughts
 * @var \App\View\AppView $this
 * @var bool $loggedIn
 * @var int|null $userId
 * @var string $formattingKey
 * @var string $title_for_layout
 * @var string $word
 */
?>
<div class="tw_thoughts">
    <div class="row">
        <div class="offset-sm-2 col-sm-8">
            <h1 class="word_heading">
                <?= $title_for_layout ?>
            </h1>
        </div>
    </div>

    <div class="row word_summary">
        <div class="offset-sm-2 col-sm-8">
            <ul class="unstyled">
                <li>
                    <?php
                        $count = count($thoughts);
                        echo $count.__n(' thought', ' thoughts', $count);
                    ?>
                </li>
                <li>
                    <?php if ($this->request->getSession()->check('Auth.User.id')): ?>
                        <?= $this->Html->link(
                            'Add a thought',
                            ['controller' => 'Thoughts', 'action' => 'add', 'word' => $word]
                        ) ?>
                    <?php else: ?>
                        <?= $this->Html->link(
                            'Log in to add a thought',
                            ['controller' => 'Users', 'action' => 'login']
                        ) ?>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>

    <?php if (empty($thoughts)): ?>
        <div id="unpopulated-thoughtword">
            <div class="offset-sm-1 col-sm-10">
                <?php if ($loggedIn): ?>
                    <p>
                        No one has yet thought about <strong><?= $word ?></strong>.<br />
                    </p>
                    <p>
                        <?= $this->Html->link(
                            'Would you like to be the first?',
                            [
                                'controller' => 'Thoughts',
                                'action' => 'add',
                                '?' => [
                                    'word' => $word
                                ]
                            ]
                        ) ?>
                    </p>
                <?php else: ?>
                    <p>
                        No one has yet thought about <strong><?= $word ?></strong>.<br />
                        If you were
                        <?= $this->Html->link(
                            'logged in',
                            ['controller' => 'Users', 'action' => 'login']
                        ) ?>, you could be the first.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>

        <?php foreach ($thoughts as $thought): ?>
            <div class="row thoughtrow" id="t<?= $thought['id'] ?>">
                <div class="col-sm-1 offset-sm-1 meta">
                    <div class="visible-xs-inline-block visible-sm visible-md visible-lg">
                        <?= $this->element('colorbox', [
                            'color' => $thought['user']['color'],
                            'anonymous' => $thought['anonymous']
                        ]) ?>
                    </div>
                    <div class="visible-xs-inline-block visible-sm visible-md visible-lg">
                        <?php
                            $timeAgo = $this->Time->abbreviatedTimeAgoInWords($thought['created']);
                            echo str_replace(',', ',<br class="hidden-xs" />', $timeAgo);
                        ?>
                        <br />
                        <?= $this->Html->link(
                            'Link <span class="visually-hidden">to this thought</span>',
                            [
                                'controller' => 'Thoughts',
                                'action' => 'word',
                                $word,
                                '#' => 't' . $thought['id']
                            ],
                            ['escape' => false]
                        ) ?>
                        <br />
                        <a data-tts="<?= $thought['tts'] ?>" data-thought-id="<?= $thought['id'] ?>" href="#" class="listenButton">
                            Listen
                        </a>
                        <?php if ($userId == $thought['user']['id']): ?>
                            <br />
                            <?= $this->Html->link(
                                'Edit',
                                ['controller' => 'Thoughts', 'action' => 'edit', $thought['id']]
                            ) ?>
                            <br />
                            <?= $this->Form->postLink(
                                'Delete',
                                ['controller' => 'Thoughts', 'action' => 'delete', $thought['id']],
                                ['confirm' => 'Are you sure that you want to remove this thought?']
                            ) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-8">
                    <?= $this->element('Thoughts/view', compact('thought')) ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php $this->append('buffered_js'); ?>
           thought.init({
               formattingKey: '<?= $formattingKey ?>'
           });
        <?php $this->end(); ?>
    <?php endif; ?>
</div>

<div id="audio-container" style="display: none;">
    <audio autoplay controls id="audio">
        <source src="" type="audio/mpeg" id="audio-source">
    </audio>
    <button id="audio-close">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>

<script>
    const audioContainer = document.getElementById('audio-container');
    const audio = document.getElementById('audio');
    const audioClose = document.getElementById('audio-close');
    const audioSource = document.getElementById('audio-source');
    audioClose.addEventListener('click', (event) => {
        event.preventDefault();
        audioContainer.style.display = 'none';
        audio.pause();
    });

    const openAudio = (filename) => {
        audioContainer.style.display = 'block';
        audioSource.src = '/audio/' + filename;
        audio.load();
    };

    const links = document.querySelectorAll('.listenButton');
    links.forEach((button) => {
        button.addEventListener('click', async (event) => {
            event.preventDefault();
            let filename = button.dataset.tts;
            if (!filename) {
                // Loading
                const loading = document.createElement('i');
                loading.classList.add('fa-solid', 'fa-spinner', 'fa-spin-pulse', 'audio-loading');
                button.appendChild(loading);

                const thoughtId = button.dataset.thoughtId;
                await fetch(`/api/thoughts/tts/${thoughtId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        filename = data.filename;
                        button.dataset.tts = filename;
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });

                loading.remove();
            }
            if (filename) {
                openAudio(filename);
            } else {
                alert('Sorry, there was an error loading the audio for that thought. :(')
            }
        });
    });
</script>
