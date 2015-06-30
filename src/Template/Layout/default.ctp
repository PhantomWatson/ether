<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php
                $title = 'Ether - Thought Repository';
                if (isset($title_for_layout) && $title_for_layout !== '') {
                    $title = 'Ether :: '.$title_for_layout;
                }
                echo $title;
            ?>
        </title>
        <link rel="dns-prefetch" href="//ajax.googleapis.com" />
        <?php echo $this->Html->css('style'); ?>
        <?php
            echo $this->Html->meta('icon');
            echo $this->fetch('meta');
        ?>
        <meta name="title" content="<?php echo $title; ?>" />
        <meta name="description" content="Ether: An experimental freeform thought repository. What's on YOUR mind?" />
        <meta name="author" content="Phantom Watson" />
        <meta name="language" content="en" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <?php
            echo $this->element('flash_messages');
            echo $this->element('header');
        ?>

        <?php echo $this->fetch('overlay'); ?>

        <div id="content_outer">
            <div id="content">
                <?php echo $this->fetch('content'); ?>
            </div>
        </div>

        <?php //echo $this->element('footer'); ?>

        <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/js/jquery-1.9.1.min.js"><\/script>')</script>

        <?php
            $scriptFile = $debug ? 'script.concat.js' : 'script.concat.min.js';
            echo $this->Html->script($scriptFile);
            echo $this->fetch('script');
            //$this->Js->buffer("setupOnPopState();");
            //echo $this->Js->writeBuffer();
            //echo $this->element('analytics');
        ?>

        <?php if ($this->fetch('buffered_js')): ?>
            <script>
                $(document).ready(function () {
                    scroll.init();
                    <?php echo $this->fetch('buffered_js'); ?>
                });
            </script>
        <?php endif; ?>

        <div id="footer">
            <div class="copyright">
                <strong>ETHER</strong> is an experimental thought repository which has collected <?= number_format($thoughtCount) ?> thoughts from <?= number_format($thinkerCount) ?> thinkers since 2006.
                &copy; <?php echo date('Y'); ?> <a href="mailto:graham@phantomwatson.com">Phantom Watson</a>
            </div>
        </div>
    </body>
</html>