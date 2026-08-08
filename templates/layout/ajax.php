<?php
/**
 * @var \App\View\AppView $this
 * @var string|null $titleForLayout
 */
$urlBase = \Cake\Routing\Router::url(null, true);
$path = $this->request->getUri()->getPath();
?>
<?= $this->fetch('content') ?>

<script>
    $(document).ready(function () {
        <?= $this->fetch('buffered_js') ?>
        gtag('event', 'page_view', {
            page_title: <?= json_encode('AJAX - ' . ($titleForLayout ?? $path)) ?>,
            page_location: <?= json_encode($urlBase) ?>,
        });
    });
</script>
