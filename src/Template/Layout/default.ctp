<?php
/**
 * @var \App\View\AppView $this
 * @var array $ogTags
 */

$title = 'Ether - Thought Repository';
if (isset($titleForLayout) && !isset($title_for_layout)) {
    $title_for_layout = $titleForLayout;
}
if (isset($title_for_layout) && $title_for_layout !== '') {
    $title = 'Ether :: ' . $title_for_layout;
}

$defaultOgTags = [
    'og:title' => $title,
    'og:image' => '/img/og.png',
    'og:image:alt' => 'The logo of Ether, displayed over a cloud of blurry words',
    'og:locale' => 'en_US',
    'og:site_name' => 'Ether',
    'og:type' => 'website',
];
$ogTags = isset($ogTags) ? array_merge($defaultOgTags, $ogTags) : $defaultOgTags;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?= $this->Html->charset() ?>
        <title>
            <?= $title ?>
        </title>
        <link rel="dns-prefetch" href="//ajax.googleapis.com" />
        <link rel="icon" type="image/png" href="/img/favicon.png" />
        <link rel="stylesheet" href="/font-awesome/css/all.min.css" />
        <script src="/js/bootstrap.min.js"></script>
        <?php
            echo $this->Html->css('style');
            echo $this->fetch('meta');
        ?>
        <meta name="title" content="<?= $title ?>" />
        <meta name="description" content="Ether: An experimental freeform thought repository. What's on YOUR mind?" />
        <meta name="author" content="Phantom Watson" />
        <meta name="language" content="en" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php foreach ($ogTags as $property => $content): ?>
            <meta property="<?= $property ?>" content="<?= h($content) ?>" />
        <?php endforeach; ?>
        <?php
            $scriptFiles = [
                '/js/comment.js',
                '/js/flash-message.js',
                '/js/messages.js',
                '/js/profile.js',
                '/js/recent.js',
                '/js/registration.js',
                '/js/scroll.js',
                '/js/search.js',
                '/js/suggested.js',
                '/js/thought.js',
                '/js/thoughtword-index.js',
                '/js/user-index.js'
            ];
            foreach ($scriptFiles as $scriptFile) {
                echo $this->Html->script($scriptFile);
            }
        ?>
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

        <?= $this->element('footer') ?>

        <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/js/jquery-1.9.1.min.js"><\/script>')</script>

        <?= $this->fetch('script') ?>
        <?= $this->element('analytics') ?>

        <script>
            $(document).ready(function () {
                scroll.init();
                <?= $this->fetch('buffered_js') ?>
            });
        </script>
    </body>
</html>
