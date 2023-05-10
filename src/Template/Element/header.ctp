<?php
/**
 * @var \App\View\AppView $this
 * @var int $newMessages
 */

use Cake\Routing\Router;
$goToUrl = Router::url(['controller' => 'Thoughts', 'action' => 'word']);
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
                                'Question Abstractor',
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
                                    ['controller' => 'Users', 'action' => 'myProfile'],
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

                </li>
            </ul>
            <form class="d-flex" role="search" action="<?= $goToUrl ?>" method="post" id="header-search">
                <input class="form-control" type="search" aria-label="Go to thoughtword..."
                       aria-describedby="basic-addon1" placeholder="Go to thoughtword..." required="required"
                       name="word" id="header-search-input">
                <button class="btn btn-primary" type="submit">
                    Go
                </button>
            </form>
        </div>
    </div>
</nav>
<?php $this->append('buffered_js'); ?>
    search.init();
<?php $this->end(); ?>
