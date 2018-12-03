var thoughtwordIndex = {
    init: function () {
        $('.shortcuts a').click(function (event) {
            event.preventDefault();
            $('html,body').animate({
                scrollTop: $(this.hash).offset().top
            }, 1000);
        });
    }
};
