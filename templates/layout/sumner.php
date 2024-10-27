<?php
    const POSTER_PHANTOM = 'Hub Bub';
    const POSTER_NERD = '@thesumnerforgepodcast';
    const POSTER_ANGIE = 'Morning Glory';
    const POSTER_SKADI = '_~darkXelf~_';
    const POSTER_HOLE = 'A Strange Hole';
    const POSTER_HANNAH = 'Squirrely Rose';

    function getPoster($name) {
        return match($name) {
             POSTER_PHANTOM => [
                'img' => 'poster-phantom.jpg',
                'title' => '<strong>Moderator</strong>',
            ],
            POSTER_NERD => [
                'img' => 'poster-nerd.jpg',
                'title' => 'Veteran Poster',
            ],
            POSTER_ANGIE => [
                'img' => 'poster-angie.png',
                'title' => 'Regular Poster',
            ],
            POSTER_SKADI => [
                'img' => 'poster-skadi.png',
                'title' => 'Junior Poster',
            ],
            POSTER_HOLE => [
                'img' => 'poster-hole.png',
                'title' => 'Landmark',
            ],
            POSTER_HANNAH => [
                'img' => 'poster-hannah.jpg',
                'title' => 'New Arrival',
            ],
        };
    }

    function post($posterContent, $postContent) {
        return sprintf(
            '<div class="post-wrapper">
                    <div class="poster">
                        %s
                    </div>
                    <div class="post-main">
                        %s
                        <div class="post-footer">
                            <button class="button">Quote</button>
                            <button class="button">Report Abuse</button>
                        </div>
                    </div>
                </div>',
            $posterContent,
            $postContent
        );
    }

    function getPosterContent($name) {
        $poster = getPoster($name);
        return sprintf(
            '
                <img src="/img/sumner/%s" style="height: 100px; width: 100px;">
                <br />
                <a href="#" style="word-break: break-word;">%s</a>
                <br />
                %s
            ',
            $poster['img'],
            $name,
            $poster['title'],
        );
    }

    $posts = [];
    $posts[] = [POSTER_HANNAH => "Does anyone know the rules for the upcoming 5K? Last year there was a lot of confusion."];
    $posts[] = [POSTER_NERD => "I did a show about last year's run and interviewed Judy and that tangible clot of whispers that was the acting deputy mayor. <a href=\"#\">Click here</a> to download an MP3."];
    $posts[] = [POSTER_PHANTOM => "What are you confused about? I think if you sign up to run, they send you all the rules.<br />Maybe call City Hall? 938-786-3674"];
    $posts[] = [POSTER_ANGIE => "I ran last year and they didn't tell me about how we'd have to lean really far forward and kind of climb up the ground because normal running stopped working."];
    $posts[] = [POSTER_SKADI => "i watched it and like 1 out of 10 ppl was QWOPing around like they forgot how legs worked. tryna skoot forward and landing on the back of thr heads n shit. embarasing. y did that only happen to some ppl and not others?"];
    $posts[] = [POSTER_HOLE => "IT'S DANGEROUS TO RUN TOO CLOSE TO ANY MYSTERIOUS PIT IN THE OUTSKIRTS OF TOWN. ANY CITIZEN FED TO THE HOLE WILL NOT BE RETURNED. DO NOT ATTEMPT A RESCUE."];
    $posts[] = [POSTER_HANNAH => "Yeah, I'm mostly confused about how during the race some people seem to be kind of swimming through the air and others are crawling really fast and others basically teleport. Nothing about that makes sense to me.<br /><br />I listened to that podcast episode and it was mostly just ads about that company that ships you memories of meals you ate as a child. In the interview, Judy was really dodgy and didn't explain anything about how the physics of the 5K work or what the really tall people handing out buckets of broken eyeglasses were for."];
?>
<!DOCTYPE HTML>
<html>

<head>
    <title>SumnerHub</title>
    <meta http-equiv="content-type" content="text/html; charset=windows-1252" />
    <meta http-equiv="robots" content="noindex, nofollow" />
    <link rel="stylesheet" type="text/css" href="/css/sumner.css" title="style" />
</head>

<body>
<div id="main">
    <div id="header">
        <div id="logo">
            <img src="/img/sumner/sumner%20logo.bad.png" style="float: right; width: 100px; height: 100px;" />
            <div id="logo_text">
                <h1>
                    Sumner<span style="background-color: white; color: var(--sumner-blue);">Hub</span>
                </h1>
                <h2>An online gathering place for the residents of Sumner Forge, DR</h2>
            </div>
        </div>
        <div id="menubar">
            <ul id="menu">
                <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
                <li><a href="index.html">Home</a></li>
                <li class="selected"><a href="another_page.html">Forum</a></li>
                <li><a href="examples.html">Events</a></li>
                <li><a href="page.html">About</a></li>
                <li><a href="page.html">Links</a></li>
            </ul>
        </div>
    </div>
    <div id="site_content">
        <!-- div class="sidebar">
            <h3>Latest News</h3>
            <h4>New Website Launched</h4>
            <h5>January 1st, 2010</h5>
            <p>2010 sees the redesign of our website. Take a look around and let us know what you think.<br /><a href="#">Read more</a></p>
            <p></p>
            <h4>New Website Launched</h4>
            <h5>January 1st, 2010</h5>
            <p>2010 sees the redesign of our website. Take a look around and let us know what you think.<br /><a href="#">Read more</a></p>
            <h3>Useful Links</h3>
            <ul>
                <li><a href="#">link 1</a></li>
                <li><a href="#">link 2</a></li>
                <li><a href="#">link 3</a></li>
                <li><a href="#">link 4</a></li>
            </ul>
            <h3>Search</h3>
            <form method="post" action="#" id="search_form">
                <p>
                    <input class="search" type="text" name="search_field" value="Enter keywords....." />
                    <input name="search" type="image" style="border: 0; margin: 0 0 -9px 5px;" src="style/search.png" alt="Search" title="Search" />
                </p>
            </form>
        </div -->
        <div id="content">
            <p style="background-color: white; padding: 0 1em; margin-bottom: 1em;">
                <a href="#">
                    Forums
                </a>
                >
                <a href="#">
                    Local Events
                </a>
                > anyone understand the rules for the 5K SumnerRun
            </p>
            <h1 style="background-color: var(--sumner-blue); color: white; padding: 0.5em;">
                anyone understand the rules for the 5K SumnerRun
            </h1>
            <?php ob_start(); ?>
            <div>
                <button class="button">
                    + New Message
                </button>

                <input type="search" placeholder="Search thread..." style="float: right; padding: 2px;" />
            </div>
            <?php
                $actions = ob_get_contents();
                ob_end_flush();
            ?>
            <?php ob_start(); ?>
            <div style="background-color: white; padding: 0.5em; margin-top: 1em; margin-bottom: 0.5em; font-weight: bold;">
                Page:
                <span style="border: 1px solid transparent; text-align: center; display: inline-block; margin-right: 0.5em; padding: 0 2px;">
                    1
                </span>
                <?php for ($n = 2; $n <= 5; $n++): ?>
                    <a href="#" style="border: 1px solid black; text-align: center; display: inline-block; margin-right: 0.5em; padding: 0 2px; text-decoration: none;">
                        <?= $n ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php
                $pagination = ob_get_contents();
                ob_end_flush();
            ?>
            <?php foreach ($posts as $post): ?>
                <?php foreach ($post as $posterName => $postContent): ?>
                    <?= post(getPosterContent($posterName), $postContent) ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?= $pagination ?>
            <?= $actions ?>
        </div>
    </div>
    <div id="content_footer"></div>
    <div id="footer">
        Copyright &copy; SumnerHub | <a href="#">Terms of Service</a> | <a href="#">Privacy</a> | <a href="#">Dream Contact Opt-out</a>
    </div>
</div>
</body>
</html>
