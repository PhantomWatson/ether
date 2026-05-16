<?php
/**
 * @var \App\View\AppView $this
 */
$animate = true;
$interval = 30; // seconds
?>

<style>
    :root {
        --slideshow-controls-bottom: 2rem;
        --slideshow-controls-height: 3.25rem;
        --slideshow-controls-gap: 1rem;
    }

    #slideshow-stage {
        position: fixed;
        inset: 0 0 calc(var(--slideshow-controls-bottom) + var(--slideshow-controls-height) + var(--slideshow-controls-gap)) 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        z-index: 20;
        pointer-events: none;
    }

    #slideshow-thought-container {
        background: #000;
        border-radius: 12px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.5);
        height: min(680px, 80vh, calc(100vh - var(--slideshow-controls-bottom) - var(--slideshow-controls-height) - var(--slideshow-controls-gap) - 2rem));
        overflow: hidden;
        padding: 1.25rem;
        position: relative;
        pointer-events: auto;
        width: 92vw;
    }

    #slideshow-thought-content {
        position: relative;
        height: 100%;
    }

    .slideshow-pane {
        position: absolute;
        inset: 0;
        overflow-y: auto;
        opacity: 0;
        transition: opacity 700ms ease;
        pointer-events: none;
    }

    .slideshow-pane.is-active {
        opacity: 1;
        pointer-events: auto;
    }

    .slideshow-thought-meta {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        text-align: center;
    }

    .slideshow-thought-meta a {
        font-weight: 600;
    }

    #slideshow-controls {
        position: fixed;
        bottom: var(--slideshow-controls-bottom);
        left: 50%;
        transform: translateX(-50%);
        z-index: 21;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .slideshow-control-button,
    #slideshow-timer {
        font-size: 1.5rem;
        font-weight: 700;
        color: rgba(0, 0, 0, 0.7);
        background: rgba(255, 255, 255, 0.9);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        min-width: 4rem;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .slideshow-control-button {
        border: 0;
        cursor: pointer;
    }

    .slideshow-control-button:hover,
    .slideshow-control-button:focus {
        background: rgba(255, 255, 255, 1);
        color: rgba(0, 0, 0, 0.85);
    }

    .slideshow-control-button:focus {
        outline: 2px solid rgba(0, 0, 0, 0.35);
        outline-offset: 2px;
    }

    .thought .body {
        font-size: 2rem;
    }
</style>

<div class="cloud <?= $animate ? 'animate_hide' : '' ?>" id="frontpage_cloud">
    <?php if (empty($cloud)): ?>
        <p>
            Sorry, we couldn't find any thoughts in the database.
            <br />That's probably a bad sign. :(
        </p>
    <?php else: ?>
        <?= $this->element('cloud', ['words' => $cloud, 'animate' => $animate]) ?>
    <?php endif; ?>
</div>

<div id="slideshow-stage">
    <div id="slideshow-thought-container">
        <div id="slideshow-thought-content">
            <div class="slideshow-pane is-active" data-pane="0">
                <p>Loading thought...</p>
            </div>
            <div class="slideshow-pane" data-pane="1" aria-hidden="true"></div>
        </div>
    </div>
</div>

<div id="slideshow-controls">
    <button id="slideshow-pause" class="slideshow-control-button" type="button" aria-pressed="false">
        <i class="fas fa-pause"></i>
    </button>
    <div id="slideshow-timer" aria-live="polite"><?= $interval ?></div>
    <button id="slideshow-next" class="slideshow-control-button" type="button">
        <i class="fas fa-arrow-right"></i>
    </button>
</div>

<script>
    (() => {
        const refreshIntervalMs = <?= $interval ?>000;
        const thoughtApiUrl = '/api/thoughts/slideshow';
        const panes = Array.from(document.querySelectorAll('.slideshow-pane'));
        const timerDisplay = document.getElementById('slideshow-timer');
        const pauseButton = document.getElementById('slideshow-pause');
        const nextButton = document.getElementById('slideshow-next');
        const refreshIntervalSeconds = refreshIntervalMs / 1000;
        let activePaneIndex = 0;
        let isLoading = false;
        let isPaused = false;
        let secondsRemaining = refreshIntervalSeconds;
        let timerIntervalId = null;

        function updateTimerDisplay() {
            timerDisplay.textContent = `${Math.max(0, secondsRemaining)}`;
        }

        function stopTimer() {
            if (timerIntervalId !== null) {
                clearInterval(timerIntervalId);
                timerIntervalId = null;
            }
        }

        function startTimer() {
            stopTimer();

            if (isPaused) {
                return;
            }

            timerIntervalId = setInterval(() => {
                secondsRemaining = Math.max(0, secondsRemaining - 1);
                updateTimerDisplay();

                if (secondsRemaining <= 0) {
                    stopTimer();
                    fetchThought();
                }
            }, 1000);
        }

        function resetTimer() {
            secondsRemaining = refreshIntervalSeconds;
            updateTimerDisplay();
            startTimer();
        }

        function updatePauseButton() {
            pauseButton.innerHTML = isPaused ? '<i class="fas fa-play"></i>' : '<i class="fas fa-pause"></i>';
            pauseButton.setAttribute('aria-pressed', isPaused ? 'true' : 'false');
        }

        function escapeHtml(text) {
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildThoughtHtml(thought) {
            if (!thought) {
                return '<p>Could not load a thought right now.</p>';
            }

            const word = thought.word || '';
            const thoughtUrl = '/thoughts/word/' + encodeURIComponent(word) + '#t' + thought.id;
            const formattedThought = thought.formatted_thought;
            const color = thought.user.color;
            let colorBox;
            if (thought.anonymous) {
                colorBox = '<div class="colorbox anonymous_colorbox" title="Contributed anonymously"></div>';
            } else if (thought.user.color === 'phanto') {
                colorBox = '<div class="colorbox" style="text-align: center;" title="Phantom">P</div>';
            } else {
                colorBox = `<div class="colorbox" style="background-color: #${color};" title="Thinker #${color}"></div>`;
            }
            const date = new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'long', day: 'numeric' }).format(new Date(thought.created));

            return `
                <div class="slideshow-thought-meta">
                    ${colorBox}
                    thought on
                    ${date}
                    about
                    <a href="${thoughtUrl}">${escapeHtml(word)}</a>
                </div>
                <div class="thought" data-formatting-key="${escapeHtml(thought.formatting_key || '')}" data-thought-id="${escapeHtml(thought.id)}">
                    <div class="body">${formattedThought}</div>
                </div>
            `;
        }

        function crossfadeTo(html) {
            const nextPaneIndex = activePaneIndex === 0 ? 1 : 0;
            const activePane = panes[activePaneIndex];
            const nextPane = panes[nextPaneIndex];

            nextPane.innerHTML = html;
            nextPane.scrollTop = 0;
            nextPane.classList.add('is-active');
            nextPane.removeAttribute('aria-hidden');

            activePane.classList.remove('is-active');
            activePane.setAttribute('aria-hidden', 'true');

            activePaneIndex = nextPaneIndex;
            resetTimer();
        }

        async function fetchThought() {
            if (isLoading) {
                return;
            }

            isLoading = true;
            stopTimer();
            try {
                const response = await fetch(thoughtApiUrl, {
                    headers: {
                        Accept: 'application/json'
                    },
                    cache: 'no-store'
                });

                if (!response.ok) {
                    crossfadeTo('<p>There was an error loading the next thought.</p>');
                    return;
                }

                const payload = await response.json();
                crossfadeTo(buildThoughtHtml(payload.thought || null));
            } catch (error) {
                crossfadeTo('<p>There was an error loading the next thought.</p>');
            } finally {
                isLoading = false;
            }
        }

        pauseButton.addEventListener('click', () => {
            isPaused = !isPaused;
            updatePauseButton();

            if (isPaused) {
                stopTimer();
                return;
            }

            startTimer();
        });

        nextButton.addEventListener('click', () => {
            secondsRemaining = refreshIntervalSeconds;
            updateTimerDisplay();
            fetchThought();
        });

        if (<?= $animate ? 'true' : 'false' ?>) {
            setTimeout(() => {
                const cloud = document.getElementById('frontpage_cloud');
                cloud.className = 'cloud animate_show';
            }, 100);
        }

        updateTimerDisplay();
        updatePauseButton();
        fetchThought();
    })();
</script>
