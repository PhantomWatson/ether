/**
 * Warns, underneath the thoughtword field, when the entered word does not yet
 * appear in any thought or comment. Checks on page load and, debounced by one
 * second, whenever the field changes.
 */
class ThoughtwordCheck {
    /**
     * @param {object} config
     * @param {number|null} [config.excludeThoughtId] ID of the thought being edited, if any
     */
    constructor(config = {}) {
        this.excludeThoughtId = config.excludeThoughtId ?? null;
        this.minLength = 3;
        this.debounceMs = 1000;
        this.rateLimitRetryMs = 5000;
        this.debounceTimer = null;
        this.lastCheckedWord = null;

        this.input = document.getElementById('input-thought-word');
        if (!this.input) {
            return;
        }

        this.footnote = this.buildFootnote();

        this.input.addEventListener('input', () => {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.check(), this.debounceMs);
        });

        this.check();
    }

    /**
     * Creates the (initially hidden) footnote element and links it to the input
     * for assistive technologies.
     *
     * @returns {HTMLDivElement}
     */
    buildFootnote() {
        const footnote = document.createElement('div');
        footnote.id = 'thoughtword-footnote';
        footnote.className = 'form-text';
        footnote.hidden = true;
        const word = this.getNormalizedWord();
        // footnote.textContent = `That word wasn't found in any thoughts or comments. `
        //     + 'You can still use it, but nothing will link to it. '
        //     + 'Consider using a more common word to make it easier for people to find this thought.';

        const describedBy = (this.input.getAttribute('aria-describedby') || '')
            .split(' ')
            .filter(Boolean);
        if (!describedBy.includes(footnote.id)) {
            describedBy.push(footnote.id);
            this.input.setAttribute('aria-describedby', describedBy.join(' '));
        }

        (this.input.closest('.form-group') || this.input.parentNode).appendChild(footnote);

        return footnote;
    }

    updateNotFoundText() {
        const word = this.getNormalizedWord();
        this.footnote.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> '
            + `The word "${word}" wasn't found in any thoughts or comments. `
            + 'You can still use it, but nothing will link to it. '
            + 'Consider using a more common word to make it easier for people to find this thought.';
    }

    /**
     * @param {number} count
     * @param {string} singular
     * @param {string} plural
     * @returns {string}
     */
    pluralize(count, singular, plural) {
        const formattedCount = new Intl.NumberFormat().format(count);
        return `${formattedCount} ${count === 1 ? singular : plural}`;
    }

    /**
     * @param {{thoughts: number, comments: number}} data
     */
    updateFoundText(data) {
        const thoughts = data.thoughts ? this.pluralize(data.thoughts, 'thought', 'thoughts') : '';
        const comments = data.comments ? this.pluralize(data.comments, 'comment', 'comments') : '';
        const and = (thoughts && comments) ? 'and' : '';
        const word = this.getNormalizedWord();
        this.footnote.innerHTML = '<i class="fa-solid fa-thumbs-up"></i> '
            + `The word "${word}" is used in ${thoughts} ${and} ${comments}.`;
    }

    /**
     * Trims, lowercases, and removes non-alphanumeric characters from the input value
     *
     * @param {string} value
     * @returns {string}
     */
    normalize(value) {
        return value.trim().toLowerCase().replace(/[^a-z0-9]/g, '');
    }

    getNormalizedWord() {
        return this.normalize(this.input.value);
    }

    async check() {
        const word = this.getNormalizedWord();

        if (word.length < this.minLength) {
            this.lastCheckedWord = null;
            this.hide();
            return;
        }

        if (word === this.lastCheckedWord) {
            return;
        }
        this.lastCheckedWord = word;

        const params = new URLSearchParams({ word });
        if (this.excludeThoughtId) {
            params.set('exclude_thought_id', this.excludeThoughtId);
        }

        try {
            const response = await fetch(`/api/words/count?${params.toString()}`, {
                headers: { Accept: 'application/json' },
            });

            if (response.status === 429) {
                // Rate-limited: forget this result so the word is re-checked,
                // and try again once the cooldown has passed.
                this.lastCheckedWord = null;
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => this.check(), this.rateLimitRetryMs);
                return;
            }
            if (!response.ok) {
                return;
            }

            const data = await response.json();

            // Ignore a response that arrived after the field changed again.
            if (this.normalize(this.input.value) !== word) {
                return;
            }

            if (data.total === 0) {
                this.showNotFound();
            } else {
                this.showFound(data);
            }
        } catch (error) {
            console.error('Error checking thoughtword usage:', error);
        }
    }

    showNotFound() {
        this.updateNotFoundText();
        this.footnote.hidden = false;
    }

    showFound(data) {
        this.updateFoundText(data);
        this.footnote.hidden = false;
    }

    hide() {
        this.footnote.hidden = true;
    }
}
