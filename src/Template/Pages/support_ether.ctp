<?php
/**
 * @var string $titleForLayout
 * @var \App\View\AppView $this
 */
?>
<div id="content_title">
    <h1>
        <?= $titleForLayout ?>
    </h1>
</div>

<div class="container">
    <div class="row">
        <div class="col">
            <h2>
                Make a Donation
            </h2>
            <p>

            </p>
        </div>
        <div class="col">
            <h2>
                Become a Patron
            </h2>
            <p>
                <?= $this->Html->link(
                    'Patreon',
                    'https://www.patreon.com/the_ether'
                ) ?>
            </p>
        </div>
    </div>
</div>
