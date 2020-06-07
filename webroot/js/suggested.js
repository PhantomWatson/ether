var suggestedWords = {
    init: function () {
        $('#suggested-words button').click(function (event) {
            event.preventDefault();
            var word = $(this).html();
            $('#word').val(word);
            $('#thought').focus();
            $('#suggested-words button.selected').removeClass('selected');
            $(this).addClass('selected');
        });
    }
};
