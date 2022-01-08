const flashMessage = {
    fadeDuration: 300,
    init: function () {
        const container = $('#flash_messages');
        container.children('div').fadeIn(this.fadeDuration);
        container.find('a').addClass('alert-link');
    },
    insert: function (message, classname) {
        const alert = $('<div role="alert"></div>')
            .addClass('col-sm-offset-2 col-sm-8 alert alert-dismissible alert-' + classname)
            .hide();
        alert.append(message);
        alert.append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        alert.find('a').addClass('alert-link');
        $('#flash_messages').append(alert);
        alert.fadeIn(this.fadeDuration);
    }
};
