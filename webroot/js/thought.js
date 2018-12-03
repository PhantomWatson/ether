var thought = {
    init: function (params) {
        $('a.add_comment').click(function (event) {
            event.preventDefault();
            var thoughtId = $(this).data('thoughtId');
            comment.add(thoughtId);
        });
        $('a.cancel_comment').click(function (event) {
            event.preventDefault();
            var thoughtId = $(this).data('thoughtId');
            comment.cancel(thoughtId);
        });
        $('#add_thought').click(function (event) {
            event.preventDefault();
            thought.add();
        });
        $('#cancel_thought').click(function (event) {
            event.preventDefault();
            thought.cancel();
        });
        $('#dontwannathink').click(function (event) {
            event.preventDefault();
            thought.dontWannaThink();
        });
        this.refreshFormatting(params.formattingKey);
    },

    add: function () {
        var formContainer = $('#newthoughtadd');
        if (formContainer.is(':visible')) {
            return;
        }
        formContainer.slideDown(200, function() {
            formContainer.find('textarea').focus();
        });
        $('#newthoughtbutton').hide();
    },

    cancel: function () {
        var formContainer = $('#newthoughtadd');
        if (! formContainer.is(':visible')) {
            return;
        }
        formContainer.slideUp(200);
        $('#newthoughtbutton').show();
    },

    dontWannaThink: function () {
        $('#wannathink_choices').slideUp(500, function () {
            $('#wannathink_rejection').slideDown(500);
        });
    },

    refreshFormatting: function (formattingKey) {
        if (formattingKey === '') {
            return;
        }
        var refresh = function (model, formattingKey) {
            $('div.'+model).each(function () {
                var post = $(this);
                if (post.data('formatting-key') === formattingKey) {
                    return;
                }
                var body = post.children('.body');
                $.ajax({
                    url: '/'+model+'s/refreshFormatting/'+post.data(model+'-id'),
                    dataType: 'json',
                    beforeSend: function () {
                        if (body.html().trim() === '') {
                            body.html('<img src="/img/loading_small.gif" alt="Loading..." />');
                        }
                    },
                    success: function (data) {
                        if (data.success && data.update) {
                            body.html(data.formattedThought);
                        }
                    }
                });
            });
        };
        refresh('thought', formattingKey);
        refresh('comment', formattingKey);
    }
};
