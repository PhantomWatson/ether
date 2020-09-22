<footer id="footer">
    <div class="col-sm-2 col-sm-offset-2">
        <ul class="list-unstyled">
            <li>
                <?= $this->Html->link(
                    'About',
                    ['controller' => 'Pages', 'action' => 'about']
                ) ?>
            </li>
            <li>
                <?= $this->Html->link(
                    'Contact',
                    ['controller' => 'Pages', 'action' => 'contact']
                ) ?>
            </li>
        </ul>
    </div>
    <div class="col-sm-2 col-sm-offset-4">
        <ul class="list-unstyled">
            <li>
                <a href="https://www.patreon.com/the_ether">
                    Patreon
                </a>
            </li>
            <li>
                <a href="http://www.facebook.com/EtherThoughtRepository">
                    Facebook
                </a>
            </li>
            <li>
                <a href="https://github.com/PhantomWatson/ether">
                    GitHub
                </a>
            </li>
        </ul>
    </div>
    <div class="col-sm-2 col-sm-offset-6">
        <ul class="list-unstyled">
            <li>
                <?= $this->Html->link(
                    'Terms of Use',
                    ['controller' => 'Pages', 'action' => 'terms']
                ) ?>
            </li>
            <li>
                <?= $this->Html->link(
                    'Privacy Policy',
                    ['controller' => 'Pages', 'action' => 'privacy']
                ) ?>
            </li>
        </ul>
    </div>
    <div class="col-sm-2 col-sm-offset-8">
        <ul class="list-unstyled">
            <li>
                <a href="http://PhantomWatson.com">
                    Phantom Watson
                </a>
            </li>
            <li>
                &copy; <?= date('Y') ?> <a href="mailto:graham@phantomwatson.com">Phantom Watson</a>
            </li>
        </ul>
    </div>
</footer>
