var messages = {
    currentRequest: null,
    init: function () {
        $('#conversations_index a').click(function (event) {
            event.preventDefault();
            var color = $(this).data('color');
            messages.selectConversation(color);
        });
    },
    scrollToLastMsg: function () {
        var lastMsg = $('#conversation div.row:last-child');
        if (lastMsg.length === 0) {
            return;
        }
        $(window).scrollTo(lastMsg, 1000, {
            interrupt: true,
            offset: -100,
        });
    },
    cancelCurrentRequest: function () {
        if (this.currentRequest) {
            this.currentRequest.abort();
        }
        $('#conversations_index .loading').removeClass('loading');
    },
    selectConversation: function (color, scroll_to_selection) {
        var conv_index = $('#conversations_index');
        var conv_link = conv_index.find('a[data-color='+color+']');
        this.cancelCurrentRequest();
        conv_link.addClass('loading');
        this.currentRequest = $.ajax({
            url: '/messages/conversation/'+color,
            complete: function () {
                conv_link.removeClass('loading');
            },
            success: function (data) {
                conv_index.find('a.selected').removeClass('selected');
                conv_link.addClass('selected');
                var inner_container = $('#conversation');

                // Fade out previous conversation
                if (inner_container.length > 0) {
                    inner_container.fadeOut(150, function () {
                        messages.fadeInConversation(data);
                    });

                } else {
                    $('#selected_conversation_wrapper').fadeOut(150, function () {
                        messages.fadeInConversation(data);
                    });
                }

                if (scroll_to_selection) {
                    var scroll_to = conv_index.scrollTop() + conv_link.position().top;
                    conv_index.animate({
                        scrollTop: scroll_to
                    }, 1000);
                }
            }
        });
    },
    fadeInConversation: function (data) {
        var outer_container = $('#selected_conversation_wrapper');
        var inner_container = $('#conversation');
        outer_container.html(data);
        inner_container = $('#conversation');
        inner_container.fadeIn(150);
        if (outer_container.is(':visible')) {
            inner_container.scrollTop(inner_container.prop('scrollHeight'));
        } else {
            outer_container.fadeIn(150, function () {
                inner_container.scrollTop(inner_container.prop('scrollHeight'));
            });
        }
        outer_container.find('form').submit(function (event) {
            event.preventDefault();
            messages.send();
        });
    },
    send: function () {
        var outer_container = $('#selected_conversation_wrapper');
        var recipientColor = outer_container.find('input[name=recipient]').val();
        var data = {
            message: outer_container.find('textarea').val(),
            recipient: recipientColor
        };
        var button = outer_container.find('input[type=submit]');
        $.ajax({
            type: 'POST',
            url: '/messages/send',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                button.prop('disabled', true);
                $('<img src="/img/loading_small.gif" class="loading" alt="Loading..." />').insertBefore(button);
            },
            success: function (data) {
                if (data.error) {
                    flashMessage.insert(data.error, 'error');
                    button.prop('disabled', false);
                    button.siblings('img.loading').remove();
                }
                if (data.success) {
                    messages.selectConversation(recipientColor);
                }
            },
            error: function () {
                flashMessage.insert('There was an error sending that message. Please try again.', 'error');
                button.prop('disabled', false);
                button.siblings('img.loading').remove();
            }
        });
    },
    setupPagination: function () {
        var links = $('.convo_pagination a');
        var loadingIndicator = $('<img src="/img/loading_small.gif" class="loading" alt="Loading..."/>');
        loadingIndicator.hide();
        links.append(loadingIndicator);
        links.click(function (event) {
            event.preventDefault();
            var link = $(this);
            var url = link.attr('href').split('?');
            $.ajax({
                data: url[1],
                beforeSend: function () {
                    var loading = link.children('.loading');
                    if (loading.is(':visible')) {
                        return false;
                    }
                    loading.show();
                },
                success: function (data) {
                    var row = link.parents('.row');
                    row.after(data);
                    row.remove();
                    messages.setupPagination();
                },
                error: function () {
                    alert('There was an error loading more messages.');
                },
                complete: function () {
                    link.children('.loading').hide();
                }
            });
        });
    }
};
