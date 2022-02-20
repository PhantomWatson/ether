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
        <meta name="title" content="<?= $title ?>" />
        <meta name="description" content="Ether: An experimental freeform thought repository. What's on YOUR mind?" />
        <meta name="author" content="Phantom Watson" />
        <meta name="language" content="en" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php foreach ($ogTags as $property => $content): ?>
            <meta property="<?= $property ?>" content="<?= h($content) ?>" />
        <?php endforeach; ?>
        <?= $this->fetch('meta') ?>
        <link rel="dns-prefetch" href="//ajax.googleapis.com" />
        <link rel="icon" type="image/png" href="/img/favicon.png" />
        <?php
            $cssFiles = [
                '/font-awesome/css/all.min.css',
                'toastui-editor-dark.min.css',
                'toastui-editor-only.min.css',
            ];
            echo $this->Html->css($cssFiles);
            echo $this->Html->css('style');
            $scriptFiles = [
                '/js/popper.min.js',
                '/js/bootstrap.min.js',
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
                '/js/user-index.js',
                '/js/toastui-editor-all.min.js',
            ];
            echo $this->Html->script($scriptFiles);
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
                let popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                let popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl)
                })
            });
        </script>
    </body>
</html>
