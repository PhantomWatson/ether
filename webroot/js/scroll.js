// Fixes how the fixed navbar hides content targeted by #hashlinks
var scroll = {
    init: function () {
        if (this.hashTargets('comment')) {
            this.toComment();
        }
        $(window).on('hashchange', function (event) {
            event.preventDefault();
            scroll.toComment();
        });
    },
    hashTargets: function (targetType) {
        if (targetType === 'comment') {
            return location.hash.match(/^#c\d+$/);
        }
        return false;
    },
    toComment: function () {
        var commentId = location.hash.replace('#', '').replace('c', '');
        this.to('[data-comment-id='+commentId+']');
    },
    to: function (selector) {
        var target = $(selector);
        if (target.length === 0) {
            return;
        }
        $(window).scrollTo(target, 1000, {
            interrupt: true,
            offset: -100,
        });
    }
};
