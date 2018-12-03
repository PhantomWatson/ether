var suggestedWords = {
    init: function () {
        $('#suggested-words button').click(function (event) {
            event.preventDefault();
            var word = $(this).html();
            $('#word').val(word);
            $('#suggested-words').slideUp();
            $('#thought').focus();
        });
    }
};
