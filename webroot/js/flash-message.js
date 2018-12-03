var flashMessage = {
    fadeDuration: 300,
    init: function () {
        var container = $('#flash_messages');
        container.children('li').fadeIn(this.fadeDuration);
        container.find('a').addClass('alert-link');
    },
    insert: function (message, classname) {
        var li = $('<li role="alert"></li>').addClass('col-sm-offset-2 col-sm-8 alert alert-dismissible alert-'+classname).hide();
        li.append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        li.append(message);
        li.find('a').addClass('alert-link');
        $('#flash_messages').append(li);
        li.fadeIn(this.fadeDuration);
    }
};
