<?php
/**
 * @var \App\View\AppView $this
 */
$selectedColor = isset($selectedColor) ? $selectedColor : null;

// Surely there's a less awkward way to do this
$maxCount = 0;
foreach ($colors as $group => $thinker) {
    foreach ($thinker as $color => $thoughtCount) {
        if ($thoughtCount > $maxCount) {
            $maxCount = $thoughtCount;
        }
    }
}
foreach ($colors as $group => $thinker) {
    foreach ($thinker as $color => $thoughtCount) {
        $resizePercent = round(($thoughtCount / $maxCount) * 100);
        $resizePercent = max($resizePercent, 5);
        echo $this->Html->link(
            '',
            ['controller' => 'Users', 'action' => 'view', $color],
            [
                'escape' => false,
                'title' => 'View profile',
                'class' => 'colorbox'.($selectedColor == $color ? ' selected' : ''),
                'data-resize' => $resizePercent,
                'style' => 'background-color: #'.$color.';'
            ]
        );
    }
}