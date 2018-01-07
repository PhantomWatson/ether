<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<p>
    The following are the best guesses that Ether has regarding the names of each Thinker's color.
    Crowdsourced color names courtesy of the <a href="https://xkcd.com/color/rgb/">XKCD color name survey</a>.
    Click on a color to view that Thinker's profile page.
</p>

<?php foreach ($colors as $color => $name): ?>
    <div class="named-color">
        <?= $this->element('colorbox', ['color' => $color]) ?>
        <br />
        <?= $name ?>
    </div>
<?php endforeach; ?>
