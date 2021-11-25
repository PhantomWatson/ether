<?php
/**
 * @var \App\View\AppView $this
 * @var int $newMessages
 * @var string $userColor
 */
?>
<nav class="navbar fixed-top navbar-dark navbar-expand-md" id="header">
    <div class="container-fluid">
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbar-collapse"
                aria-controls="navbar-collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a href="/" id="site_title">
            <h1>
                Ether
            </h1>
        </a>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <?= $this->Html->link(
                        'Thoughts <span class="caret"></span>',
                        ['controller' => 'Thoughts', 'action' => 'index'],
                        [
                            'aria-haspopup' => 'true',
                            'aria-expanded' => 'false',
                            'class' => 'nav-link dropdown-toggle',
                            'data-bs-toggle' => 'dropdown',
                            'escape' => false,
                            'role' => 'button',
                        ]
                    ) ?>
                    <ul class="dropdown-menu">
                        <li>
                            <?= $this->Html->link(
                                'Add a Thought',
                                ['controller' => 'Thoughts', 'action' => 'add'],
                                ['class' => 'dropdown-item']
                            ) ?>
                        </li>
                        <li>
                            <?= $this->Html->link(
                                'Browse',
                                ['controller' => 'Thoughts', 'action' => 'index'],
                                ['class' => 'dropdown-item']
                            ) ?>
                        </li>
                        <li>
                            <?= $this->Html->link(
                                'Random',
                                ['controller' => 'Thoughts', 'action' => 'random'],
                                ['id' => 'random_link', 'class' => 'dropdown-item']
                            ) ?>
                        </li>
                        <li>
                            <?= $this->Html->link(
                                'Generator',
                                ['controller' => 'Generator', 'action' => 'index'],
                                ['class' => 'dropdown-item']
                            ) ?>
                        </li>
                        <li>
                            <?= $this->Html->link(
                                'Questions',
                                ['controller' => 'Thoughts', 'action' => 'questions'],
                                ['class' => 'dropdown-item']
                            ) ?>
                        </li>
                        <li>
                            <?= $this->Html->link(
                                'Thinkers',
                                ['controller' => 'Users', 'action' => 'index'],
                                ['class' => 'dropdown-item']
                            ) ?>
                        </li>
                    </ul>
                </li>

                <?php if (isset($loggedIn) && $loggedIn): ?>

                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
                           aria-expanded="false">
                            <?php if ($newMessages): ?>
                                <span class="new_messages">
                                <?= $newMessages ?> New <?= __n('Message', 'Messages', $newMessages) ?>
                            </span>
                            <?php else: ?>
                                Logged in
                            <?php endif; ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <?= $this->Html->link(
                                    'My Profile',
                                    ['controller' => 'Users', 'action' => 'view', $userColor],
                                    ['class' => 'dropdown-item']
                                ) ?>
                            </li>
                            <li>
                                <?= $this->Html->link(
                                    'Settings',
                                    ['controller' => 'Users', 'action' => 'settings'],
                                    ['class' => 'dropdown-item']
                                ) ?>
                            </li>
                            <li>
                                <?= $this->Html->link(
                                    'Messages',
                                    ['controller' => 'Messages', 'action' => 'index'],
                                    ['class' => 'dropdown-item' . ($newMessages ? ' new_messages' : '')]
                                ) ?>
                            </li>
                            <li>
                                <?= $this->Html->link(
                                    'Logout',
                                    ['controller' => 'Users', 'action' => 'logout'],
                                    ['class' => 'dropdown-item']
                                ) ?>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <?= $this->Html->link(
                            'Login',
                            ['controller' => 'Users', 'action' => 'login'],
                            ['id' => 'login_link', 'class' => 'nav-link']
                        ) ?>
                    </li>
                    <li class="nav-item">
                        <?= $this->Html->link(
                            'Register',
                            ['controller' => 'Users', 'action' => 'register'],
                            ['id' => 'register_link', 'class' => 'nav-link']
                        ) ?>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <?= $this->Html->link(
                        'About',
                        ['controller' => 'Pages', 'action' => 'about'],
                        ['class' => 'nav-link']
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        'Patreon',
                        'https://www.patreon.com/the_ether',
                        ['class' => 'nav-link']
                    ) ?>
                </li>
                <li class="nav-item">
                    <?php
                    use Cake\Routing\Router;
                    $goto_url = Router::url(['controller' => 'Thoughts', 'action' => 'word']);
                    ?>
                    <form class="navbar-form navbar-left" role="search" action="<?= $goto_url ?>" method="post" id="header-search">
                        <label for="header-search-input">
                            <i class="fas fa-search" aria-label="Go to thoughtword" title="Go to thoughtword"></i>
                        </label>
                        <input type="search" class="form-control" placeholder="Go to thoughtword..." required="required"
                               name="word" id="header-search-input" />
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php $this->append('buffered_js'); ?>
    search.init();
<?php $this->end(); ?>
