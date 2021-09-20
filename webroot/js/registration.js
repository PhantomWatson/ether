var registration = {
    color_avail_request: null,
    color_name_request: null,

    init: function () {
        var myPicker = new jscolor.color(document.getElementById('color_hex'), {
            hash: false
        });

        $('#color_hex').change(function () {
            if (registration.validateColor()) {
                registration.checkColorAvailability();
                registration.getColorName();
            }
        });

        $('#UserRegisterForm').submit(function (event) {
            if (! registration.validateColor()) {
                alert('Please select a valid color.');
                return false;
            }
            return true;
        });

        registration.getColorName();
    },

    validateColor: function () {
        var chosen_color = $('#color_hex').val();

        // Validate color
        var pattern = new RegExp('^[0-9A-F]{6}$', 'i');
        if (! pattern.test(chosen_color)) {
            var error_message = 'Bad news. ' + chosen_color + ' is not a valid hexadecimal color.';
            this.showColorFeedback(error_message, 'error');
            return false;
        }

        return true;
    },

    showColorFeedback: function (message, className) {
        var msgContainer = $('#reg_color_input').find('.evaluation_message');
        if (message === msgContainer.html()) {
            return;
        }
        if (msgContainer.is(':empty')) {
            msgContainer.hide();
            msgContainer.html(message);
            msgContainer.removeClass('success error');
            msgContainer.addClass(className);
            msgContainer.slideDown(500);
        } else {
            msgContainer.fadeOut(100, function () {
                msgContainer.html(message);
                msgContainer.removeClass('success error');
                msgContainer.addClass(className);
                msgContainer.fadeIn(500);
            });
        }
    },

    checkColorAvailability: function () {
        var color = $('#color_hex').val();
        if (this.color_avail_request !== null) {
            this.color_avail_request.abort();
        }
        this.color_avail_request = $.ajax({
            url: '/users/checkColorAvailability/' + color,
            dataType: 'json',
            beforeSend: function () {
                var message = 'Checking to see if that color is available...';
                registration.showColorFeedback(message, null);
            },
            success: function (data) {
                if (! data.hasOwnProperty('available') || typeof(data.available) !== 'boolean') {
                    registration.showColorFeedback('There was an error checking the availability of that color.', null);
                } else if (data.available === true) {
                    registration.showColorFeedback('This color is available! :)', 'success');
                } else if (data.available === false) {
                    registration.showColorFeedback('This color is already taken. :(', 'error');
                }
            },
            error: function () {
                registration.showColorFeedback('There was an error checking the availability of that color.', null);
            },
            complete: function () {
                registration.color_avail_request = null;
            }
        });
    },

    getColorName: function () {
        var color = $('#color_hex').val();
        if (this.color_name_request !== null) {
            this.color_name_request.abort();
        }
        var colorLabel = $('#reg_color_input').find('label');
        this.color_name_request = $.ajax({
            url: '/colors/name/' + color,
            dataType: 'json',
            beforeSend: function () {
                colorLabel.html('Color: <img src="/img/loading_small.gif" class="loading" alt="Loading..." />');
            },
            success: function (data) {
                if (! data.hasOwnProperty('name')) {
                    colorLabel.html('Color');
                    console.log('Error retrieving color name');
                } else {
                    colorLabel.html('Color: "' + data.name + '"');
                }
            },
            error: function () {
                colorLabel.html('Color');
                console.log('Error retrieving color name');
            },
            complete: function () {
                registration.color_name_request = null;
            }
        });
    }
};
