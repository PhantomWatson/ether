<div class="abc_thoughts_shortcuts navbar-fixed-top">
    <ul class="nav nav-tabs">
        <?php foreach ($categorized as $first => $words): ?>
            <li>
                <a href="#abc_thoughts_<?php echo $first == '#' ? '0' : $first; ?>">
                    <?php echo $first; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div id="alphabetical_words">
    <?php foreach ($categorized as $first => $words): ?>
        <section id="abc_thoughts_<?php echo $first == '#' ? '0' : $first; ?>">
            <a></a>
            <h2>
                <?php echo $first; ?>
            </h2>
            <ul>
                <?php foreach ($words as $word): ?>
                    <li>
                        <?php echo $this->Html->link(
                            $word,
                            ['controller' => 'thoughts', 'action' => 'word', $word],
                            ['class' => 'thoughtword']
                        ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <br class="clear" />
        </section>
    <?php endforeach; ?>
</div>

<?php $this->append('buffered_js'); ?>
    setupThoughtwordIndex();
<?php $this->end(); ?>