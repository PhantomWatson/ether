<?php
/**
 * @var \App\View\AppView $this
 */
?>
<!doctype html>
<html lang="en">
    <head>
        <?= $this->Html->charset() ?>
        <title>
            <?php
                $title = 'Ether - Thought Repository';
                if (isset($titleForLayout) && !isset($title_for_layout)) {
                    $title_for_layout = $titleForLayout;
                }
                if (isset($title_for_layout) && $title_for_layout !== '') {
                    $title = 'Ether :: '.$title_for_layout;
                }
                echo $title;
            ?>
        </title>
        <link rel="dns-prefetch" href="//ajax.googleapis.com" />
        <link rel="icon" type="image/png" href="/img/favicon.png" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <?php
            echo $this->Html->css('style');
            echo $this->fetch('meta');
        ?>
        <meta name="title" content="<?= $title ?>" />
        <meta name="description" content="Ether: An experimental freeform thought repository. What's on YOUR mind?" />
        <meta name="author" content="Phantom Watson" />
        <meta name="language" content="en" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    </head>
    <body>
        <?php
            echo $this->element('header');
            echo $this->fetch('overlay');
        ?>

        <div id="content_outer">
            <div id="content">
                <?= $this->element('flash_messages') ?>
                <?= $this->fetch('content') ?>
            </div>
        </div>

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

        <?php //echo $this->element('footer'); ?>

        <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="/js/jquery-3.5.1.slim.min.js"><\/script>')</script>

        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

        <?php
            $scriptFile = $debug ? 'script.concat.js' : 'script.concat.min.js';
            echo $this->Html->script($scriptFile);
            echo $this->fetch('script');
            echo $this->element('analytics');
        ?>

        <?php if ($this->fetch('buffered_js')): ?>
            <script>
                $(document).ready(function () {
                    scroll.init();
                    <?= $this->fetch('buffered_js') ?>
                });
            </script>
        <?php endif; ?>
    </body>
</html>
