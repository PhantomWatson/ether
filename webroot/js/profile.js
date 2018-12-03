var profile = {
    init: function () {
        var profile_message_form = $('#profile_message_form');
        var textarea = profile_message_form.find('textarea');
        var submit_button = profile_message_form.find("input[type='submit']");
        var submit_container = profile_message_form.find('div.submit');
        profile_message_form.submit(function (event) {
            event.preventDefault();
            var postData = $(this).serializeArray();
            var url = $(this).attr('action');
            $.ajax({
                url : url,
                type: 'POST',
                data : postData,
                beforeSend: function () {
                    submit_button.prop('disabled', true);
                    textarea.prop('disabled', true);
                    var loading_indicator = $('<img src="/img/loading_small.gif" class="loading" alt="Loading..." />');
                    submit_container.prepend(loading_indicator);
                    submit_container.find('.result').fadeOut(200, function () {
                        $(this).remove();
                    });
                },
                success: function (data, textStatus, jqXHR) {
                    submit_container.find('.result').remove();
                    var result = $(data);
                    result.hide();
                    submit_container.prepend(result);
                    result.fadeIn(500);
                    textarea.val('');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('There was an error sending that message. ('+textStatus+')');
                },
                complete: function () {
                    submit_button.prop('disabled', false);
                    textarea.prop('disabled', false);
                    submit_container.find('.loading').remove();
                }
            });
        });
    }
};
