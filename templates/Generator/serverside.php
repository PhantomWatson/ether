<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="content_title">
    <h1>
        <?= $titleForLayout ?>
    </h1>
</div>

<h2>
    Your Randomly-Generated Thought:
</h2>

<p class="well">
    <?= $result ?>
</p>

<p>
    Refresh this page for a new randomly-generated thought. <a href="https://github.com/PhantomWatson/ether-markov">View the PhantomWatson/ether-markov project on GitHub.</a>
</p>