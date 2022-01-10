const messages = {
    convo: null,
    convoIndex: null,
    currentRequest: null,
    selectedConvoWrapper: null,

    init: function () {
        this.convo = document.getElementById('conversation');
        this.convoIndex = document.getElementById('');
        this.selectedConvoWrapper = document.getElementById('selected_conversation_wrapper');
    },

    scrollToLastMsg: function () {
        const lastMsg = this.convo.querySelector('div.row:last-child');
        if (lastMsg.length === 0) {
            return;
        }

        // This should have a vertical offset to make up for the nav bar
        //lastMsg.scrollIntoView();
    },

    cancelCurrentRequest: function () {
        if (this.currentRequest) {
            this.currentRequest.abort();
        }
        const loading = this.convoIndex.querySelector('.loading');
        loading.classList.remove('loading');
    },

    fadeInConversation: function (data) {
        const innerContainer = this.convo;
        this.selectedConvoWrapper.innerHTML = data;
        innerContainer.fadeIn(150);
        if (this.selectedConvoWrapper.is(':visible')) {
            innerContainer.scrollTop(innerContainer.prop('scrollHeight'));
        } else {
            this.selectedConvoWrapper.fadeIn(150, function () {
                innerContainer.scrollTop(innerContainer.prop('scrollHeight'));
            });
        }
        this.selectedConvoWrapper.find('form').submit(function (event) {
            event.preventDefault();
            messages.send();
        });
    },

    send: function () {
        const recipientColor = this.selectedConvoWrapper.querySelector('input[name=recipient]').value;
        const data = {
            message: this.selectedConvoWrapper.querySelector('textarea').value,
            recipient: recipientColor
        };
        const button = this.selectedConvoWrapper.querySelector('input[type=submit]');
        $.ajax({
            type: 'POST',
            url: '/messages/send',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                button.disabled = true;
                button.parentNode.insertBefore(this.createLoadingIndicator(), button);
            },
            success: function (data) {
                if (data.error) {
                    flashMessage.insert(data.error, 'error');
                    button.disabled = false;
                    button.parentNode.querySelector('img.loading').remove();
                }
                if (data.success) {
                    window.location = '/messages/conversation/' + recipientColor;
                }
            },
            error: function () {
                flashMessage.insert('There was an error sending that message. Please try again.', 'error');
                button.disabled = false;
                button.parentNode.querySelector('img.loading').remove();
            },
        });
    },

    createLoadingIndicator: function () {
        const loadingIndicator = document.createElement('i');
        loadingIndicator.title = 'Loading...';
        loadingIndicator.className = 'fas fa-spinner fa-spin loading';
        return loadingIndicator;
    },

    setupPagination: function () {
        const paginationContainer = document.querySelector('.convo_pagination');
        if (paginationContainer === null) {
            return;
        }

        const paginationButton = paginationContainer.querySelector('button');
        const loadingIndicator = paginationButton.querySelector('.loading');
        paginationButton.addEventListener('click', (event) => {
            event.preventDefault();
            paginationButton.append(loadingIndicator);
            paginationButton.disabled = true;
            loadingIndicator.style.visibility = 'visible';
            fetch(paginationButton.dataset.url).then((response) => {
                return response.text();
            }).then((html) => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const content = doc.querySelector('body');
                const container = document.createElement('div');
                container.innerHTML = content.innerHTML
                this.convo.prepend(container);
                paginationContainer.remove();
                this.setupPagination();
            }).catch(() => {
                alert('There was an error loading more messages.');
            })
            .finally(() => {
                loadingIndicator.style.visibility = 'hidden';
            });
        });
    }
};
