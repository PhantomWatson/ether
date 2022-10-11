class TTS {
    constructor() {
        const audioContainer = document.getElementById('audio-container');
        const audio = document.getElementById('audio');
        const audioClose = document.getElementById('audio-close');
        const audioSource = document.getElementById('audio-source');
        audioClose.addEventListener('click', (event) => {
            event.preventDefault();
            audioContainer.style.display = 'none';
            audio.pause();
        });

        const openAudio = (filename) => {
            audioContainer.style.display = 'block';
            audioSource.src = '/audio/' + filename;
            audio.load();
        };

        const links = document.querySelectorAll('.listenButton');
        links.forEach((button) => {
            button.addEventListener('click', async (event) => {
                event.preventDefault();
                let filename = button.dataset.tts;
                if (!filename) {
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
                }
                if (filename) {
                    openAudio(filename);
                } else {
                    this.modal(
                        'Sorry, there was an error loading the audio for that thought. '
                        + 'Our text-to-speech service might be temporarily down. Please try again later or '
                        + '<a href="/contact">contact Phantom</a> to let him know something\'s wrong.'
                    );
                }
            });
        });
    }

    modal(msg) {
        const modal = this.createElementFromHTML(document.getElementById('modal-template').innerHTML);
        modal.id = 'tts-modal';
        console.log(modal);
        modal.querySelector('.modal-body p').innerHTML = msg;
        modal.querySelector('.modal-title').innerHTML = 'Whoops';
        document.querySelector('body').appendChild(modal);
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
}
