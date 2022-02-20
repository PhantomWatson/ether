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
        });

        /* Populate editor
         * Doublespaces before newlines must be removed and then re-added before submitting because this editor
         * for some reason removes all instances of "  \n" */
        let markdown = config.markdown;
        markdown = markdown.replaceAll("\r\n", "\n");
        markdown = markdown.replaceAll("  \n", "\n");
        editor.setMarkdown(markdown, false);
        editor.moveCursorToStart();

        // Populate hidden textarea when form is submitted
        const form = document.getElementById('ThoughtAddForm');
        form.addEventListener('submit', () => {
            let markdown = editor.getMarkdown();

            // Add double-space at end of lines
            markdown = markdown.replaceAll(/[^ \n]\n/g, (match) => {
                return match.replace("\n", "  \n");
            });
            markdown = markdown.replaceAll("\r\n", "\n");

            document.getElementById('input-thought-body').value = markdown;
        });
    }
}
