<?php
/**
 * @var \App\View\AppView $this
 * @var string|null $titleForLayout
 */
?>
<?= $this->fetch('content') ?>

<script>
    $(document).ready(function () {
        <?= $this->fetch('buffered_js') ?>
        ga('send', 'pageview', {
            'page': <?= json_encode($this->request->getUri()->getPath()) ?>,
            'title': <?= json_encode($titleForLayout ?? '') ?>,
        });
    });
</script>
