<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div id="content_title">
    <h1>
        Thinkers
    </h1>
</div>

<div class="row">
    <div class="offset-sm-2 col-sm-8">
        <p>
            Each of these colors represents a Thinker who has contributed thoughts to Ether. Click a color to view that Thinker's profile.
        </p>

        <p>
            Curious about who's most active?
            <a href="#" id="resize">
                Resize colorboxes according to number of thoughts thunk.
            </a>
        </p>

        <p>
            <span class="glyphicon glyphicon-info-sign text-info"></span>
            <strong>New:</strong> Check out
            <?= $this->Html->link('the names of these colors', [
                'controller' => 'Colors',
                'action' => 'colorNames'
            ]) ?>
        </p>

        <div class="users_index">
            <?= $this->element('Users'.DS.'index') ?>
        </div>
    </div>
</div>

<?php $this->append('buffered_js'); ?>
    userIndex.init();
<?php $this->end(); ?>
