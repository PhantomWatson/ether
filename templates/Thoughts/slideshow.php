<?php
/**
 * @var \App\View\AppView $this
 */
$animate = true;
?>

<style>
    #slideshow-thought-container {
        background: #000;
        border-radius: 12px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.5);
        height: min(680px, 80vh);
        left: 50%;
        overflow: hidden;
        padding: 1.25rem;
        position: fixed;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 92vw;
        z-index: 20;
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

<div id="slideshow-thought-container">
    <div id="slideshow-thought-content">
        <div class="slideshow-pane is-active" data-pane="0">
            <p>Loading thought...</p>
        </div>
        <div class="slideshow-pane" data-pane="1" aria-hidden="true"></div>
    </div>
</div>

<script>
    (() => {
        const refreshIntervalMs = 30000;
        const thoughtApiUrl = '/api/thoughts/slideshow';
        const panes = Array.from(document.querySelectorAll('.slideshow-pane'));
        let activePaneIndex = 0;
        let isLoading = false;

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
            // return `
            //     <div class="thought">
            //         <div class="body">
            //             ${formattedThought}
            //         </div>
            //     </div>
            // `;
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
        }

        async function fetchThought() {
            if (isLoading) {
                return;
            }

            isLoading = true;
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

        if (<?= $animate ? 'true' : 'false' ?>) {
            setTimeout(() => {
                const cloud = document.getElementById('frontpage_cloud');
                cloud.className = 'cloud animate_show';
            }, 100);
        }

        fetchThought();
        setInterval(fetchThought, refreshIntervalMs);
    })();
</script>
