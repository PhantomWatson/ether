<?php
/**
 * @var \App\View\AppView $this
 */
    function letterLink($letter) {
        return '<a href="#abc_thoughts_'.($letter == '#' ? '0' : $letter).'">'.$letter.'</a> ';
    }
    function letterLinks($current, $all) {
        $append = [];
        $foundFirst = false;
        $retval = '';
        foreach ($all as $letter => $words) {
            if ($letter == $current) {
                $foundFirst = true;
                continue;
            }
            if ($foundFirst) {
                $retval .= letterLInk($letter);
            } else {
                $append[] = $letter;
            }
        }
        foreach ($append as $letter) {
            $retval .= letterLInk($letter);
        }
        return $retval;
    }
?>

<div id="alphabetical_words">
    <?php foreach ($categorized as $first => $words): ?>
        <section id="abc_thoughts_<?= $first == '#' ? '0' : $first ?>">
            <div class="header">
                <h2>
                    <?= $first ?>
                </h2>
                <span class="shortcuts">
                    <?= letterLinks($first, $categorized) ?>
                </span>
            </div>
            <ul>
                <?php foreach ($words as $word): ?>
                    <li>
                        <?= $this->Html->link(
                            $word,
                            ['controller' => 'thoughts', 'action' => 'word', $word],
                            ['class' => 'thoughtword']
                        ) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <br class="clear" />
        </section>
    <?php endforeach; ?>
</div>

<?php $this->append('buffered_js'); ?>
    thoughtwordIndex.init();
<?php $this->end(); ?>