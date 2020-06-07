<?php
/**
 * @var \App\View\AppView $this
 */
?>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark" id="header">
    <a href="/" id="site_title">
        <h1>
            Ether
        </h1>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbar-collapse">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <?= $this->Html->link(
                    'Thoughts',
                    [
                        'controller' => 'Thoughts',
                        'action' => 'index',
                    ],
                    [
                        'aria-expanded' => 'false',
                        'aria-haspopup' => 'true',
                        'class' => 'nav-link dropdown-toggle',
                        'data-toggle' => 'dropdown',
                        'id' => 'header-dropdown-thoughts',
                        'role' => 'button',
                    ]
                ) ?>
                <div class="dropdown-menu" aria-labelledby="header-dropdown-thoughts">
                    <?= $this->Html->link(
                        'Browse',
                        ['controller' => 'Thoughts', 'action' => 'index'],
                        ['class' => 'dropdown-item']
                    ) ?>
                    <?= $this->Html->link(
                        'Random',
                        ['controller' => 'Thoughts', 'action' => 'random'],
                        ['class' => 'dropdown-item']
                    ) ?>
                    <?= $this->Html->link(
                        'Generator',
                        ['controller' => 'Generator', 'action' => 'index'],
                        ['class' => 'dropdown-item']
                    ) ?>
                    <?= $this->Html->link(
                        'Questions',
                        ['controller' => 'Thoughts', 'action' => 'questions'],
                        ['class' => 'dropdown-item']
                    ) ?>
                    <?= $this->Html->link(
                        'Add a Thought',
                        ['controller' => 'Thoughts', 'action' => 'add'],
                        ['class' => 'dropdown-item']
                    ) ?>
                    <?= $this->Html->link(
                        'Thinkers',
                        ['controller' => 'Users', 'action' => 'index'],
                        ['class' => 'dropdown-item']
                    ) ?>
                </div>
            </li>

            <?php if (isset($loggedIn) && $loggedIn): ?>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="header-dropdown-logged-in">
                        <?php if ($newMessages): ?>
                            <span class="new_messages">
                                <?= $newMessages ?> New Messages
                            </span>
                        <?php else: ?>
                            Logged in
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="header-dropdown-logged-in">
                        <?= $this->Html->link(
                            'My Profile',
                            ['controller' => 'Users', 'action' => 'view', $userColor],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Settings',
                            ['controller' => 'Users', 'action' => 'settings'],
                            ['class' => 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Messages',
                            ['controller' => 'Messages', 'action' => 'index'],
                            ['class' => $newMessages ? 'dropdown-item new_messages' : 'dropdown-item']
                        ) ?>
                        <?= $this->Html->link(
                            'Logout',
                            ['controller' => 'Users', 'action' => 'logout'],
                            ['class' => 'dropdown-item']
                        ) ?>
                    </div>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <?= $this->Html->link(
                        'Login',
                        ['controller' => 'Users', 'action' => 'login'],
                        ['class' => 'nav-link']
                    ) ?>
                </li>
                <li class="nav-item">
                    <?= $this->Html->link(
                        'Register',
                        ['controller' => 'Users', 'action' => 'register'],
                        ['class' => 'nav-link']
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
                <form class="form-inline my-2 mt-lg-2 ml-lg-2" role="search" action="<?= $goto_url ?>" method="post" id="header-search">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <label for="header-search-input" class="sr-only">Go to thoughtword</label>
                    <input type="search" class="form-control mr-sm-2" placeholder="Go to thoughtword..." required="required" name="word" id="header-search-input" />
                </form>
            </li>
        </ul>
    </div>
</nav>
<?php $this->append('buffered_js'); ?>
    search.init();
<?php $this->end(); ?>
