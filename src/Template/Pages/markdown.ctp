<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<p>
    Thoughts, comments, and messages can be styled using <a href="https://daringfireball.net/projects/markdown/">Markdown</a>
    (specifically a subset of the <a href="http://spec.commonmark.org/">CommonMark spec</a>, if you're curious). Here's how!
</p>

<?php
    $markdownHelper = $this->loadHelper('Gourmet/CommonMark.CommonMark');
    $examples = [
        'Italics and Bold' => "This is *italics*. \nSo is _this_.\nAnd both **this** and __this__ is bold.\n\nIf you want to mix bold and italics, *you can do it __like this__*.",
        'Line Breaks' => "Single line breaks\nare normally ignored.\n\nBut double line breaks aren't.\n\nIf you need a single line break, (two spaces go here -->)  \nend a line with two spaces before hitting return.",
        'Blockquotes' => "Need a blockquote? Well, as Mahatma Gandhi famously said,\n> Do it\n> like this, \n> ya dinglefuck.",
        'Unordered Lists' => "- Start lines\n- With dashes\n- For simple lists\n\nOh, you need line breaks inside of list items?\n- Make sure  \n  Them shits  \n  Is indented  \n  As fuck.\n- And remember the thing about ending a line with two spaces if you want a single line break after it.",
        'Ordered Lists' => "Can't have lists running amok, all unordered.\n1. Here's how\n2. to make\n3. an ordered list."
    ];
?>

<ul>
    <?php foreach ($examples as $header => $example): ?>
        <li>
            <a href="#section-<?= strtolower(str_replace(' ', '-', $header)) ?>">
                <?= $header ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<?php foreach ($examples as $header => $example): ?>
    <section class="markdown-example row" id="section-<?= strtolower(str_replace(' ', '-', $header)) ?>">
        <div class="col-sm-offset-2 col-sm-8">
            <h2>
                <?= $header ?>
            </h2>
            <pre><?= $example ?></pre>
            becomes...
            <div>
                <?= $markdownHelper->convertToHtml($example) ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>