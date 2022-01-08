/**
 * A Markov-chain text generator developed for theEther.com
 */
const EtherMarkov = {
    blockCount: 0,
    blockLength: null,
    buttonStart: document.getElementById('start'),
    buttonStop: document.getElementById('stop'),
    currentBlock: null,
    entropyScore: 0,
    entropyScoreContainer: document.getElementById('entropyScore'),
    interval: null,
    limit: null,
    loop: null,
    processingContainer: document.getElementById('markovOptions'),
    resultsContainer: document.getElementById('markovResults'),
    seed: null,
    selectorBlockLength: document.getElementById('blockLength'),
    selectorLimit: document.getElementById('limit'),
    selectorSpeed: document.getElementById('speed'),
    sourceUrl: null,

    /**
     * Initialization method
     *
     * sourceUrl must return a JSON object that includes {source: '...'}
     *
     * @param {string} sourceUrl
     */
    init: function (sourceUrl) {
        const selectVal = (select) => select.querySelector('option:checked').value;
        this.blockLength = selectVal(this.selectorBlockLength);
        this.interval = 1000 / selectVal(this.selectorSpeed);
        this.limit = selectVal(this.selectorLimit);
        this.sourceUrl = sourceUrl;
        this.buttonStart.addEventListener('click', () => EtherMarkov.start());
        this.buttonStop.addEventListener('click', () => EtherMarkov.stop());
        this.buttonStop.style.display = 'none';
        this.selectorBlockLength.addEventListener(
            'change',
            event => this.blockLength = selectVal(event.target)
        );
        this.selectorSpeed.addEventListener(
            'change',
            event => this.interval = 1000 / selectVal(event.target)
        );
        this.selectorLimit.addEventListener(
            'change',
            event => this.limit = selectVal(event.target)
        );

        this.setup();
    },

    setup: function () {
        this.buttonStart.disabled = true;
        this.buttonStart.style.display = 'none';

        const loading = document.createElement('p');
        loading.id = 'loadingSource';
        loading.innerHTML = 'Loading source text... <img src="/img/loading_small.gif" alt="Loading..." />';

        this.buttonStart.after(loading);
        this.getSeed(() => {
            this.buttonStart.disabled = false;
            this.buttonStart.style.display = 'inline';
            loading.remove();
        });
    },

    getSeed: function (successCallback) {
        const displayError = () => alert('AHFUCK SOMETHING WENT WRONG');
        $.ajax({
            url: this.sourceUrl,
            dataType: 'json',
            beforeSend: () => {},
            success: (data) => {
                if (data.hasOwnProperty('source') && data.source) {
                    this.seed = data.source;
                    successCallback();
                } else {
                    displayError();
                }
            },
            error: () => displayError()
        });
    },

    start: function () {
        document.getElementById('randomness-label').style.display = 'inline';
        this.entropyScoreContainer.style.display = 'inline-block';
        this.buttonStart.style.display = 'none';
        this.buttonStop.style.display = 'inline';
        this.selectorBlockLength.disabled = true;
        this.selectorSpeed.disabled = true;
        this.selectorLimit.disabled = true;
        if (this.currentBlock === null) {
            this.currentBlock = this.getRandomBlock(this.seed).split(' ');
            this.displayInResults(this.currentBlock.join(' '));
            this.blockCount += this.blockLength;
        }
        this.loop = setInterval(() => this.addWord(), this.interval);
    },

    addWord: function () {
        const word = this.getNextWord();

        /* Remove as many words as is needed from the current block so that after adding another word, it will be
         * this.blockLength words long. This accommodates the block length possibly being changed after text generation
         * starts. */
        while (this.currentBlock.length + 1 > this.blockLength) {
            this.currentBlock.shift();
        }

        this.currentBlock.push(word);

        this.displayInResults(word);
        this.blockCount++;
        if (this.limit && this.blockCount >= this.limit) {
            this.stop();
            this.entropyScore = 0;
            this.blockCount = 0;
            return;
        }
        this.updateDisplayedEntropyScore();
    },

    displayInResults: function (word) {
        const duration = 1500 + (Math.round(Math.random()) ? -1 : 1) * (Math.random() * 400);
        let text = document.createElement('span');
        text.innerHTML = ' ' + word;
        this.resultsContainer.appendChild(text);

        /* Delaying by 10ms prevents a problem where opacity will occasionally be set before the text appears,
         * resulting in the CSS transition not being applied */
        setTimeout(() => text.style.opacity = '1', 10);
    },

    stop: function () {
        this.buttonStop.style.display = 'none';
        this.buttonStart.style.display = 'inline';
        this.selectorBlockLength.disabled = false;
        this.selectorLimit.disabled = false;
        this.selectorSpeed.disabled = false;
        clearInterval(this.loop);
    },

    getNextWord: function () {
        const prevBlock = this.currentBlock.join(' ') + ' ';
        this.processingContainer.innerHTML = prevBlock + '<ul></ul>';
        const wordList = this.processingContainer.querySelector('ul');

        // Build the array of words that are candidates for appending to the chain
        const wordCandidates = [];
        let searchStart = 0;
        while (true) {
            const matchPos = this.seed.indexOf(prevBlock, searchStart);
            if (matchPos === -1) {
                break;
            }

            const wordStart = matchPos + prevBlock.length;
            const wordEnd = this.seed.indexOf(' ', wordStart + 1);
            const wordLength = wordEnd - wordStart;
            const word = this.seed.substr(wordStart, wordLength);
            wordCandidates.push(word);

            searchStart = wordEnd + 1;
        }

        if (wordCandidates.length === 0) {
            return this.getRandomBlock();
        }

        // Add all candidates to the "thinkin" container
        wordCandidates.forEach(word => this.addWordToProcessingContainer(wordList, word));

        // Advance entropy score if there's more than one choice
        if (wordCandidates.length > 1) {
            this.entropyScore++;
        }

        // Mark the chosen word as selected and return it
        const chosenWord = this.getRandomArrayElement(wordCandidates);
        const words = Array.from(wordList.querySelectorAll('li'));
        for (let i = 0; i < words.length; i++) {
            if (words[i].innerText === chosenWord) {
                words[i].classList.add('selected');
            }
        }
        return chosenWord;
    },

    getRandomBlock: function () {
        while (true) {
            const startPos = Math.floor(Math.random() * this.seed.length);
            const blockStart = this.seed.indexOf(' ', startPos) + 1;
            let blockEnd = blockStart;
            for (let i = 1; i <= this.blockLength; i++) {
                blockEnd = this.seed.indexOf(' ', blockEnd + 1);
            }
            if (blockStart > -1 && blockEnd > -1) {
                return this.seed.substr(blockStart, blockEnd - blockStart);
            }
        }
    },

    updateDisplayedEntropyScore: function () {
        const percent = Math.round((this.entropyScore / this.blockCount) * 100);
        this.entropyScoreContainer.querySelector('span').innerText = percent + '%';
        this.entropyScoreContainer.querySelector('.progress-bar').style.width = percent + '%';
    },

    /**
     * Either display the candidate word or increment its ×2, ×3, etc. counter
     *
     * @param {HTMLUListElement} wordList
     * @param {string} word
     */
    addWordToProcessingContainer(wordList, word) {
        let safeWord = encodeURI(word);
        const displayedWord = this.processingContainer.querySelector(`li[data-word="${safeWord}"]`);
        if (displayedWord) {
            const countElement = displayedWord.querySelectorAll('span.count');
            if (countElement.length) {
                countElement.innerHtml = parseInt(countElement.innerHtml) + 1;
            } else {
                displayedWord.innerHTML += ' <span class="count">2</span>';
            }
        } else {
            let wordListItem = document.createElement('li');
            wordListItem.innerText = word;
            wordListItem.dataset.word = safeWord;
            wordList.appendChild(wordListItem);
        }
    },

    /**
     * Returns a random element from the provided array
     *
     * @param {Array} array
     * @returns {*}
     */
    getRandomArrayElement(array) {
        const key = Math.floor(Math.random() * array.length);
        return array[key];
    }
};
