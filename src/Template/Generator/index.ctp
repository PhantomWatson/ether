<?php
/**
 * @var \App\View\AppView $this
 * @var string $title_for_layout
 */
?>
<div id="content_title">
    <h1>
        <?= $title_for_layout ?>
    </h1>
</div>

<div id="generator-intro">
    <p>
        The Ether Thought Generator generates text based on thoughts that have been shared on Ether. You can set the
        speed and the randomness and let'r rip.
    </p>

    <em class="signature">Love,&nbsp;Phantom</em>
</div>

<div class="row" id="generator-interface">
    <section class="col-sm-4">
        <h2>
            Controls
        </h2>
        <div class="generator-well">
            <div class="form-group row">
                <label for="speed" class="col-sm-4">
                    Speed:
                </label>
                <div class="col-sm-6">
                    <select id="speed" class="form-control">
                        <option value="0.5">
                            Very slow
                        </option>
                        <option value="1">
                            Slow
                        </option>
                        <option value="2" selected="selected">
                            Normal
                        </option>
                        <option value="4">
                            Fast
                        </option>
                        <option value="6">
                            Faster
                        </option>
                        <option value="8">
                            Fasterer
                        </option>
                        <option value="10">
                            Ludicrous speed
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="blockLength" class="col-sm-4">
                    Random&shy;ness:
                    <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                       data-bs-content="The <em>Randomness</em> score shows you how often your text encountered a fork in the road where it could switch from one source thought to another."></i>
                </label>
                <div class="col-sm-6">
                    <select id="blockLength" class="form-control">
                        <option value="4">
                            Mostly exact quotes
                        </option>
                        <option value="3">
                            Slightly weird
                        </option>
                        <option value="2" selected="selected">
                            Moderately chaotic
                        </option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="limit" class="col-sm-4">
                    Stop after:
                </label>
                <div class="col-sm-6">
                    <select id="limit" class="form-control">
                        <option value="10">
                            10 words
                        </option>
                        <option value="100">
                            100 words
                        </option>
                        <option value="250">
                            250 words
                        </option>
                        <option value="500">
                            500 words
                        </option>
                        <option value="1000">
                            1000 words
                        </option>
                        <option value="" selected="selected">
                            Never
                        </option>
                    </select>
                </div>
            </div>
            <br />
            <button id="stop" class="btn btn-primary" style="display: none;">Stop</button>
            <button id="start" class="btn btn-primary">Start</button>
        </div>
    </section>

    <section class="col-sm-4 offset-sm-1">
        <h2>
            Thinkin'
            <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
               data-bs-content="The <em>Thinkin'</em> section shows you how the process takes a block of N words and searches for a random word that someone, somewhere once wrote <em>after</em> that."></i>
        </h2>
        <div class="generator-well">
            <div id="markovOptions"></div>
        </div>
    </section>
</div>

<section id="generator-results">
    <h2>Results</h2>
    <span id="randomness-container">
        <span id="randomness-label">Randomness:</span>
        <span class="progress" id="entropyScore">
            <span class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                <span></span>
            </span>
        </span>
    </span>
    <p id="markovResults" class="generator-well"></p>
</section>

<?php $this->Html->script('ether-markov', ['block' => 'script']); ?>
<?php $this->append('buffered_js'); ?>
    EtherMarkov.init('/generator/getSource');
<?php $this->end(); ?>
