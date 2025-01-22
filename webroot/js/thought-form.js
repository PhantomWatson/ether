/**
 * Requires Toast UI and DOMPurify to be loaded
 */
class ThoughtForm {
    /**
     *
     * @param {object} config Includes toastui, DOMPurify, and markdown
     */
    constructor(config) {
        const purifyOptions = {
            ALLOWED_TAGS: ['p', 'br', 'em', 'strong', 'ul', 'ol', 'li', 'blockquote'],
            ALLOWED_ATTR: [],
            ALLOW_DATA_ATTR: false,
        };
        const Editor = config.toastui.Editor;
        const editor = new Editor({
            el: document.querySelector('#thought-rich-text-editor'),
            height: '300px',
            initialEditType: 'wysiwyg',
            previewStyle: 'vertical',
            toolbarItems: [['bold', 'italic', 'ul', 'ol', 'quote']],
            theme: 'dark',
            hideModeSwitch: true,
        });

        // Sanitize input on change
        editor.addHook('change', () => {
            const html = editor.getHTML();
            let sanitized = config.DOMPurify.sanitize(html, purifyOptions) || ' ';
            sanitized = sanitized.replaceAll(/&nbsp;/ig, ' ');

            // Prevent infinite loop
            if (html !== sanitized) {
                editor.setHTML(sanitized, false);
            }

            // Clear error while typing
            this.hideError();
        });

        // Validate on blur
        editor.addHook('blur', () => {
            const markdown = editor.getMarkdown();
            if (markdown.length > 0) {
                this.validate(markdown, config);
            }
        });

        /* Populate editor
         * Doublespaces before newlines must be removed and then re-added before submitting because this editor
         * for some reason removes all instances of "  \n" */
        let markdown = config.markdown;
        markdown = markdown.replaceAll("\r\n", "\n");
        markdown = markdown.replaceAll("  \n", "\n");
        editor.setMarkdown(markdown, false);
        editor.moveCursorToStart();

        // Prevent native browser validation for hidden input
        const textarea = document.getElementById('input-thought-body');
        textarea.required = false;
        textarea.minLength = null;
        textarea.maxLength = null;

        // Populate hidden textarea when form is submitted
        const form = document.getElementById('ThoughtAddForm');
        form.addEventListener('submit', (event) => {
            let markdown = editor.getMarkdown();

            // Add double-space at end of lines
            markdown = markdown.replaceAll(/[^ \n]\n/g, (match) => {
                return match.replace("\n", "  \n");
            });
            markdown = markdown.replaceAll("\r\n", "\n");

            // Copy value to hidden textarea
            textarea.value = markdown;

            // Validate
            if (!this.validate(markdown, config)) {
                event.preventDefault();
            }
        });
    }

    hideError() {
        const errorMsg = document.getElementById('thought-validation');
        errorMsg.style.display = 'none';
    }

    validate(content, config) {
        this.hideError();
        const errorMsg = document.getElementById('thought-validation');
        const belowMin = content.length < config.minLength;
        const aboveMax = content.length > config.maxLength;
        if (belowMin) {
            errorMsg.innerHTML = 'That thought is way too short! Please enter at least ' + config.minLength + ' characters.';
            errorMsg.style.display = 'block';
            return false;
        }
        if (aboveMax) {
            errorMsg.innerHTML = 'That thought is too long. Please trim that down to ' + config.maxLength + ' characters or split it into multiple thoughts.';
            errorMsg.style.display = 'block';
            return false;
        }
        return true;
    }
}
