<?php
/**
 * @var \App\View\AppView $this
 */

$noLink = $noLink ?? false;
?>
<?php if (isset($anonymous) && $anonymous): ?>
    <div class="colorbox anonymous_colorbox" title="Contributed anonymously"></div>
<?php elseif ($color == 'phanto'): ?>
    <div class="colorbox" style="text-align: center;" title="Phantom">
        P
    </div>
<?php elseif (! $color): ?>
    <div class="colorbox anonymous_colorbox" title="(Thinker not found)"></div>
<?php elseif ($noLink): ?>
    <div class="colorbox <?= $class ?? null ?>" style="background-color: #<?= $color ?>;"
         title="Thinker #<?= $color ?>"></div>
<?php else: ?>
    <?= $this->Html->link(
        '<span class="visually-hidden">View Thinker #' . $color . '\'s profile</span>',
        [
            'controller' => 'Users',
            'action' => 'view',
            $color
        ],
        [
            'escape' => false,
            'title' => 'View Thinker #' . $color . '\'s profile',
            'id' => (isset($colorboxId) ? $colorboxId : ''),
            'class' => 'colorbox' . (isset($class) ? " $class" : ''),
            'style' => 'background-color: #' . $color . ';'
        ]
    ) ?>
<?php endif; ?>
