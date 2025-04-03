<?php
    $gangsta = [];
    $capslock = [];
    $fullRandomImgs = range(1, 17);
    $randomImgs = $fullRandomImgs;
    $fullRandomTitles = [
        'Is your computer ready for Y2K?',
        'So are you more of a Geocities, Angelfire, or Tripod kinda person?',
        'Sorry, I\'m going to be browsing usenet all weekend.',
        'Check out the number my hitcounter is up to!',
        'Be sure to sign my guestbook!',
        'This MIDI music loads up so fast on my 56k.',
        'UNDER CONSTRUCTION',
        'You\'ll have to install Realplayer Shockwave Quicktime to view this site.'
    ];
    $randomTitles = $fullRandomTitles;
?>
<div class="container patrons">
    <div class="row">
        <h2 class="col">
            Patreon Supporters
        </h2>
    </div>
    <div class="row gx-5">
        <div class="col patron-category" id="gangstacaps-patrons">
            <div>
                <h3>
                    <img src="/img/gangsta/flame.gif" alt="HELLA RADICAL FYRE" class="d-none d-md-inline"
                         title="Watch out, this website might be too EDGY for you" />
                    gAnGsTa CaPs sUpPoRtErS
                    <img src="/img/gangsta/flame.gif" alt="MORE HELLA RADICAL FYRE" class="d-none d-md-inline"
                         title="If you're not too scared, join my radical webring" />
                </h3>
                <ul class="unstyled">
                    <?php foreach ($gangsta as $supporter): ?>
                        <?php
                        if (!$randomImgs) {
                            $randomImgs = $fullRandomImgs;
                        }
                        $key = array_rand($randomImgs);
                        $imgNum = $randomImgs[$key];
                        unset($randomImgs[$key]);

                        if (!$randomTitles) {
                            $randomTitles = $fullRandomTitles;
                        }
                        $key = array_rand($randomTitles);
                        $title = $randomTitles[$key];
                        unset($randomTitles[$key]);
                        ?>
                        <li>
                            <?= $supporter ?>
                            <img src="/img/gangsta/random/<?= $imgNum ?>.gif" alt="IS HELLA RADICAL" title="<?= $title ?>" />
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col patron-category" id="capslock-patrons">
            <div>
                <h3>
                    CAPSLOCK SUPPORTERS
                </h3>
                <ul class="unstyled">
                    <?php foreach ($capslock as $supporter): ?>
                        <li>
                            <?= $supporter ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <p id="patreon-info">
        Support Ether by
        <a href="https://www.patreon.com/the_ether">
            becoming a Patreon supporter
        </a>
        at the lowercase, Capitalized, CAPSLOCK, or <strong>gAnGsTa CaPs level</strong>.
    </p>
</div>

<footer id="footer" class="container">
    <div class="row">
        <div class="col-sm-2 offset-sm-2">
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
        <div class="col-sm-2">
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
        <div class="col-sm-2">
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
        <div class="col-sm-2">
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
