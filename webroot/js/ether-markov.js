/**
 * A Markov-chain text generator developed for theEther.com 
 */
var EtherMarkov = {
    blockCount: 0,
    blockLength: null,
    currentBlock: null,
    entropyScore: 0,
    interval: null,
    limit: null,
    loop: null,
    processingContainer: null,
    resultsContainer: null,
    seed: null,
    sourceUrl: null,

    /**
     * Initialization method
     *
     * sourceUrl must return a JSON object that includes {source: '...'}
     *
     * @param {string} sourceUrl
     */
    init: function (sourceUrl) {
        var blockLength = $('#blockLength');
        var speed = $('#speed');
        var limit = $('#limit');

        this.blockLength = blockLength.val();
        this.interval = 1000 / speed.val();
        this.limit = limit.val();
        this.resultsContainer = $('#markovResults');
        this.processingContainer = $('#markovOptions');
        this.entropyScoreContainer = $('#entropyScore');
        this.sourceUrl = sourceUrl;
        
        $('#start').click(function () {
            EtherMarkov.start();
        });
        $('#stop').click(function () {
            EtherMarkov.stop();
        }).hide();
        blockLength.change(function () {
            EtherMarkov.blockLength = $(this).val();
        });
        speed.change(function () {
            EtherMarkov.interval = 1000 / $(this).val();
        });
        limit.change(function () {
            EtherMarkov.limit = $(this).val();
        });
        
        this.setup();
    },
    
    setup: function () {
        var start = $('#start');
        start.prop('disabled', true).hide();
        
        start.after('<p id="loadingSource">Loading source text... <img src="/img/loading_small.gif" alt="Loading..." /></p>');
        this.getSeed(function () {
            start.prop('disabled', false).show();
            $('#loadingSource').hide();
        });
    },
    
    getSeed: function (successCallback) {
        var displayError = function () {
            alert('AHFUCK SOMETHING WENT WRONG');
        };
        $.ajax({
            url: this.sourceUrl,
            dataType: 'json',
            beforeSend: function () {
            },
            success: function (data) {
                if (data.hasOwnProperty('source') && data.source) {
                    EtherMarkov.seed = data.source;
                    successCallback();
                } else {
                    displayError();
                }
            },
            error: function () {
                displayError();
            }
        });
    },
    
    start: function () {
        $('#generator-interface').find('hr').show();
        $('#randomness-label').show();
        this.entropyScoreContainer.css({display: 'inline-block'});
        $('#start').hide();
        $('#stop').show();
        $('#blockLength').prop('disabled', true);
        $('#speed').prop('disabled', true);
        $('#limit').prop('disabled', true);
        if (this.currentBlock === null) {
            var start = this.getRandomBlock(this.seed, this.blockLength);
            this.currentBlock = start.split(' ');
            this.displayInResults(this.currentBlock.join(' '));
            this.blockCount += this.blockLength;
        }
        this.loop = setInterval(function () {EtherMarkov.addBlock();}, this.interval);
    },
    
    addBlock: function () {
        var word = this.getNextWord();
        this.currentBlock.shift();
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
    
    displayInResults: function (message) {
        var text = $('<span> ' + message + '</span>');
        text.css('opacity', 0);
        this.resultsContainer.append(text);
        var duration = 500 + (Math.round(Math.random()) ? -1 : 1) * (Math.random() * 400);
        text.animate({opacity: 1}, duration);
    },
    
    stop: function () {
        $('#stop').hide();
        $('#start').show();
        $('#speed').prop('disabled', false);
        $('#limit').prop('disabled', false);
        clearInterval(this.loop);
    },
    
    getNextWord: function () {
        var seed = this.seed;
        var prevBlock = this.currentBlock.join(' ') + ' ';
        var blockLength = this.blockLength;
        var wordCandidates = [];
        var searchStart = 0;
        this.processingContainer.html(prevBlock+'<ul></ul>');
        while (true) {
            var matchPos = seed.indexOf(prevBlock, searchStart);
            if (matchPos === -1) {
                break;
            }
            var wordStart = matchPos + prevBlock.length;
            var wordEnd = seed.indexOf(' ', wordStart + 1);
            var wordLength = wordEnd - wordStart;
            var word = seed.substr(wordStart, wordLength);
            wordCandidates.push(word);
            
            var safeWord = encodeURI(word);
            var displayedWord = this.processingContainer.find('li[data-word="'+safeWord+'"]');
            if (displayedWord.length === 0) {
                this.processingContainer.find('ul').append('<li data-word="'+word+'">'+word+'</li>');
            } else {
                var countElement = displayedWord.find('span.count');
                if (countElement.length === 0) {
                    displayedWord.append(' <span class="count">2</span>');
                } else {
                    var count = parseInt(countElement.html());
                    countElement.html(count + 1);
                }
            }
            
            searchStart = wordEnd + 1;
        }
        if (wordCandidates.length > 0) {
            if (wordCandidates.length > 1) {
                this.entropyScore++;
            }
            var key = Math.floor(Math.random() * wordCandidates.length);
            this.processingContainer.find('li:nth-child('+(key + 1)+')').addClass('selected');
            return wordCandidates[key];
        }
        return this.getRandomBlock(seed, blockLength);
    },
    
    getRandomBlock: function (seed, blockLength) {
        while (true) {
            var startPos = Math.floor(Math.random() * seed.length);
            var blockStart = seed.indexOf(' ', startPos) + 1;
            var blockEnd = blockStart;
            for (var i = 1; i <= blockLength; i++) {
                blockEnd = seed.indexOf(' ', blockEnd + 1);
            }
            if (blockStart > -1 && blockEnd > -1) {
                return seed.substr(blockStart, (blockEnd - blockStart));
            }
        }
    },
    
    updateDisplayedEntropyScore: function () {
        var percent = Math.round((this.entropyScore / this.blockCount) * 100);
        this.entropyScoreContainer.find('span').html(percent + '%');
        this.entropyScoreContainer.find('.progress-bar').css({width: percent + '%'})
    }
};
