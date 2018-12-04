<?php
/**
 * @var \App\View\AppView $this
 * @var array $words
 * @var bool $animate
 */

use Cake\Cache\Cache;

echo Cache::remember('thoughtwordCloudRendered', function () use ($words, $animate) {
    // Will be rendered as percents
    $maxFontSize = 400;
    $minFontSize = 50;

    // This is the darkest shade of gray allowed
    $darkestColor = hexdec(89);

    /* To draw the colors higher up on the dark-to-light scale,
     * we pretend that the highest color is brighter than possible,
     * then just cap all of the colors off at white. */
    $lightestColor = hexdec('ff') * 1.5;

    $ceiling = max($words);

    $cloud = '';
    foreach($words as $word => $count) {
        $scale = $count / $ceiling;

        $size = round(($maxFontSize - $minFontSize) * $scale) + $minFontSize;

        $color = round(($lightestColor - $darkestColor) * $scale) + $darkestColor;
        if ($color > hexdec('ff')) {
            $color = hexdec('ff');
        }
        $color = dechex($color);
        while(strlen($color) < 2) {
            $color = "0$color";
        }
        $color .= $color.$color;

        if (isset($animate)) {
            $animationClass = ' anim'.rand(1, 10);
        } else {
            $animationClass = '';
        }

        $cloud .= $this->Html->link(
            '<span>' . $word . '</span>',
            [
                'controller' => 'Thoughts',
                'action' => 'word',
                $word
            ],
            [
                'class' => 'thoughtword'.$animationClass,
                'escape' => false,
                'style' => "font-size: $size%; color: #$color;"
            ]
        );
        $cloud .= "\n";
    }

    return $cloud;
}, 'long');
