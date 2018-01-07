<?php
/**
 * @var \App\View\AppView $this
 */
?>
<style>
    #test-words {
        position: relative;
    }
    #test-words a {
        display: inline-block;
        z-index: 1;
    }
    #test-words > span {
        display: inline-block;
        position: absolute;
        z-index: 2;
    }
</style>

<div id="test-words">
    <?php foreach ($words as $word): ?>
        <a href="#" data-word="<?= $word ?>"><?= $word ?></a>
    <?php endforeach; ?>
</div>

<script>
<?php $this->append('buffered_js'); ?>
    wordCoalesceTest.init();
<?php $this->end(); ?>
</script>
