<?php
/**
 * @var \App\View\AppView $this
 */
?>
<nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation" id="header">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
            <span class="sr-only">
                Toggle navigation
            </span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a href="/" id="site_title">
            <h1>
                Ether
            </h1>
        </a>
    </div>

    <div class="collapse navbar-collapse" id="navbar-collapse">
        <ul class="nav navbar-nav">
            <li>
                <?= $this->Html->link(
                    'Thoughts <span class="caret"></span>',
                    ['controller' => 'Thoughts', 'action' => 'index'],
                    [
                       'aria-haspopup' => 'true',
                       'aria-expanded' => 'false',
                       'class' => 'dropdown-toggle',
                       'data-toggle' => 'dropdown',
                       'escape' => false,
                       'role' => 'button'
                   ]
                ) ?>
                <ul class="dropdown-menu">
                    <li>
                        <?= $this->Html->link(
                            'Add a Thought',
                            ['controller' => 'Thoughts', 'action' => 'add']
                        ) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(
                            'Browse',
                            ['controller' => 'Thoughts', 'action' => 'index']
                        ) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(
                            'Random',
                            ['controller' => 'Thoughts', 'action' => 'random'],
                            ['id' => 'random_link']
                        ) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(
                            'Generator',
                            ['controller' => 'Generator', 'action' => 'index']
                        ) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(
                            'Questions',
                            ['controller' => 'Thoughts', 'action' => 'questions']
                        ) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(
                            'Thinkers',
                            ['controller' => 'Users', 'action' => 'index']
                        ) ?>
                    </li>
                </ul>
            </li>

            <?php if (isset($loggedIn) && $loggedIn): ?>

                <li>
                    <a  href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <?php if ($newMessages): ?>
                            <span class="new_messages">
                                <?= $newMessages ?> New Messages
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
                                ['controller' => 'Users', 'action' => 'view', $userColor]
                            ) ?>
                        </li>
                        <li>
                            <?= $this->Html->link(
                                'Settings',
                                ['controller' => 'Users', 'action' => 'settings']
                            ) ?>
                        </li>
                        <li>
                            <?= $this->Html->link(
                                'Messages',
                                ['controller' => 'Messages', 'action' => 'index'],
                                ['class' => $newMessages ? 'new_messages' : '']
                            ) ?>
                        </li>
                        <li>
                            <?= $this->Html->link(
                                'Logout',
                                ['controller' => 'Users', 'action' => 'logout']
                            ) ?>
                        </li>
                    </ul>
                </li>
            <?php else: ?>
                <li>
                    <?= $this->Html->link(
                        'Login',
                        ['controller' => 'Users', 'action' => 'login'],
                        ['id' => 'login_link']
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        'Register',
                        ['controller' => 'Users', 'action' => 'register'],
                        ['id' => 'register_link']
                    ) ?>
                </li>
            <?php endif; ?>
            <li>
                <?= $this->Html->link(
                    'About',
                    ['controller' => 'Pages', 'action' => 'about']
                ) ?>
            </li>
            <li>
                <?= $this->Html->link(
                    'Patreon',
                    'https://www.patreon.com/the_ether'
                ) ?>
            </li>
            <li>
                <?php
                    use Cake\Routing\Router;
                    $goto_url = Router::url(['controller' => 'Thoughts', 'action' => 'word']);
                ?>
                <form class="navbar-form navbar-left" role="search" action="<?= $goto_url ?>" method="post" id="header-search">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    <label for="header-search-input" class="sr-only">Go to thoughtword</label>
                    <input type="search" class="form-control" placeholder="Go to thoughtword..." required="required" name="word" id="header-search-input" />
                </form>
            </li>
        </ul>
    </div>
</nav>
<?php $this->append('buffered_js'); ?>
    search.init();
<?php $this->end(); ?>
