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
                    alert('Sorry, there was an error loading the audio for that thought. :(')
                }
            });
        });
    }
}
