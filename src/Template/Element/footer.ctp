<div class="container patrons">
    <div class="row" id="gangstacaps-patrons">
        <div class="col-sm-8 col-sm-offset-2">
            <h3>
                <img src="/img/gangsta/flame.gif" alt="HELLA RADICAL FYRE" title="Watch out, this website might be too EDGY for you" />
                Patreon gAnGsTa CaPs sUpPoRtErS:
                <img src="/img/gangsta/flame.gif" alt="MORE HELLA RADICAL FYRE" title="If you're not too scared, join my radical webring" />
            </h3>
            <ul class="unstyled">
                <li>
                    <img src="/img/gangsta/hot-dog.gif" alt="Waggly wiener" title="Is it supposed to be wiggling like that?" style="height: 40px;" />
                    Bitey_Chicken
                    <img src="/img/gangsta/baby.gif" alt="Danson Bay Bee" title="Why was this the most memorable thing from Ally McBeal" style="height: 70px;" />
                </li>
            </ul>
        </div>
    </div>
    <div class="row" id="capslock-patrons">
        <div class="col-sm-8 col-sm-offset-2">
            <h3>
                Patreon CAPSLOCK SUPPORTERS:
            </h3>
            <?php
                $patrons = [
                    'Luna Kay',
                    'Wocket',
                ];
            ?>
            <ul class="unstyled">
                <?php foreach ($patrons as $patron): ?>
                    <li><?= $patron ?></li>
                <?php endforeach; ?>
            </ul>
            <p>
                <a href="https://www.patreon.com/the_ether">Support Ether by becoming a Patreon supporter</a>
            </p>
        </div>
    </div>
</div>

<footer id="footer" class="container">
    <div class="row">
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
    </div>
</footer>
