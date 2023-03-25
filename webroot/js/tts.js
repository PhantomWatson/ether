class TTS {
    constructor() {
        this.audioContainer = document.getElementById('audio-container');
        this.audioSource = document.getElementById('audio-source');
        this.audio = document.getElementById('audio');
        this.audioClose = document.getElementById('audio-close');

        this.audioClose.addEventListener('click', (event) => {
            event.preventDefault();
            this.audioContainer.style.display = 'none';
            this.audio.pause();
        });

        const links = document.querySelectorAll('.listenButton');
        links.forEach((button) => {
            button.addEventListener('click', async (event) => {
                event.preventDefault();
                await this.buttonOnClick(event);
            });
        });
    }

    modal(msg) {
        // Add a modal to the DOM
        const modal = this.createElementFromHTML(document.getElementById('modal-template').innerHTML);
        modal.id = 'tts-modal';
        modal.querySelector('.modal-body p').innerHTML = msg;
        modal.querySelector('.modal-title').innerHTML = 'Whoops';
        document.querySelector('body').appendChild(modal);

        // Show it
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    /**
     * https://stackoverflow.com/a/494348/52530
     *
     * @param htmlString
     * @returns {ChildNode}
     */
    createElementFromHTML(htmlString) {
        const div = document.createElement('div');
        div.innerHTML = htmlString.trim();

        // Change this to div.childNodes to support multiple top-level nodes.
        return div.firstChild;
    }

    async buttonOnClick(event) {
        const button = event.target.closest('button');
        let filename = button.dataset.tts ? button.dataset.tts : await this.generateFile(button);
        if (filename) {
            this.openAudio(filename);
            return;
        }
        this.modal(
            'Sorry, there was an error loading the audio for that thought. '
            + 'Our text-to-speech service might be temporarily down. Please try again later or '
            + '<a href="/contact">contact Phantom</a> to let him know something\'s wrong.'
        );
    }

    openAudio(filename) {
        this.audioContainer.style.display = 'block';
        this.audioSource.src = '/audio/' + filename;
        this.audio.load();
    };

    async generateFile(button) {
        let filename = null;

        // Loading
        const loading = document.createElement('i');
        loading.classList.add('fa-solid', 'fa-spinner', 'fa-spin-pulse', 'audio-loading');
        button.appendChild(loading);

        const thoughtId = button.dataset.thoughtId;
        await fetch(`/api/thoughts/tts/${thoughtId}`)
            .then((response) => response.json())
            .then((data) => {
                filename = data.filename;
                button.dataset.tts = filename;
            })
            .catch((error) => {
                console.error('Error:', error);
            });

        loading.remove();

        return filename;
    }
}
