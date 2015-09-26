function toggleBGFade(fade_interval) {
    if (! fade_interval) {
        fade_interval = 1000;
    }
    if (fading_interval_id) {
        clearInterval(fading_interval_id);
        fading_interval_id = 0;
    } else {
        fading_interval_id = setInterval(function() {adjustBackground();}, fade_interval);
    }
}

function adjustBackground() {
    if (fading_mouseover_pause) {
        return;
    }
    var upper_limit = 255;
    var lower_limit = 0;
    var body_tag = document.getElementById('body_tag');
    var color = new RGBColor(body_tag.style.backgroundColor);
    var rand_color = Math.floor(Math.random()*3);
    var adjustment = Math.round(Math.random()) === 0 ? -1 : 1;
    if (rand_color === 0) {
        target_color = color.r;
    } else if (rand_color == 1) {
        target_color = color.g;
    } else if (rand_color == 2) {
        target_color = color.b;
    }
    if (adjustment == 1 && target_color >= upper_limit) {
        adjustment = -1;
    } else if (adjustment == -1 && target_color <= lower_limit) {
        adjustment = 1;
    }
    if (rand_color === 0) {
        color.r += adjustment;
    } else if (rand_color == 1) {
        color.g += adjustment;
    } else if (rand_color == 2) {
        color.b += adjustment;
    }
    document.getElementById('toggleBGFade').innerHTML = color.toHex();
    document.getElementById('toggleBGFade').style.color = color.toHex();
    body_tag.style.backgroundColor = color.toHex();
}

var comment = {
    add: function (thoughtId) {
        var formContainer = $('#newcomment' + thoughtId + 'add');
        if (formContainer.is(':visible')) {
            return;
        }
        var button = $('#newcomment' + thoughtId + 'button');
        button.slideUp(200);
        formContainer.slideDown(200);
        formContainer.find('textarea').focus();
    },
    
    cancel: function (thoughtId) {
        var formContainer = $('#newcomment' + thoughtId + 'add');
        if (formContainer.is(':visible')) {
            $('#newcomment' + thoughtId + 'button').slideDown(200);
            formContainer.slideUp(200);
        }
    },
    
    insert: function (thoughtId) {
        $('#newcomment' + thoughtId + 'view').append($('#comment_just_added'));
        $('#comment_just_added').attr('id', '');
        $('#newcomment' + thoughtId + 'add').find('textarea').html('');
        this.cancel(thoughtId);
    }
};

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
    	$('div.thought').each(function () {
    		var thought = $(this);
    		if (thought.data('formatting-key') == formattingKey) {
    			return;
    		}
    		
    		var body = thought.children('.body');
    		$.ajax({
    			url: '/thoughts/refreshFormatting/'+thought.data('thought-id'),
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
    }
};

function d2h(d) {
    return d.toString(16);
}

function h2d(h) {
    return parseInt(h,16);
}

var registration = {
    color_avail_request: null,
    
    init: function () {
        var myPicker = new jscolor.color(document.getElementById('color_hex'), {
            hash: true
        });

        $('#color_hex').change(function () {
            if (registrationForm.validateColor()) {
                registrationForm.checkColorAvailability();
            } else {
                registrationForm.showColorAjaxMessage('', null);
            }
        });

        $('#UserRegisterForm').submit(function (event) {
            if (! registrationForm.validateColor()) {
                alert('Please select a valid color.');
                return false;
            }
            return true;
        });
    },
    
    validateColor: function () {
        var chosen_color = $('#color_hex').val();
        
        // Correct missing hash
        if (chosen_color.length < 7 && chosen_color.charAt(0) != '#') {
            chosen_color = '#'+chosen_color;
            $('#color_hex').val(chosen_color); 
        }
        
        // Validate color
        var pattern = new RegExp('^#[0-9A-F]{6}$', 'i');
        if (! pattern.test(chosen_color)) {
            var error_message = 'Bad news.' + chosen_color + ' is not a valid hexadecimal color.';
            this.showColorError(error_message);
            return false;
        }
        
        return true;
    },
    
    showColorError: function (error_message) {
        var error_container = $('#reg_color_input .error-message');
        if (error_message == error_container.html()) {
            return;
        }
        if (error_container.is(':empty')) {
            error_container.html(error_message);
            error_container.slideDown(500);
        } else {
            error_container.fadeOut(500, function () {
                error_container.html(error_message);
                error_container.fadeIn(500);
            });
        }
    },
    
    showColorAjaxMessage: function (message, class_name) {
        var container = $('#reg_color_input .ajax_message');
        if (container.is(':empty')) {
            container.html(message);
            container.removeClass('success error');
            container.addClass(class_name);
            container.fadeIn(500);
        } else {
            container.fadeOut(500, function () {
                container.html(message);
                container.removeClass('success error');
                container.addClass(class_name);
                container.fadeIn(500);
            });
        }
    },
    
    checkColorAvailability: function () {
        var color = $('#color_hex').val();
        color = color.replace('#', '');
        if (this.color_avail_request !== null) {
            this.color_avail_request.abort();
        }
        this.color_avail_request = $.ajax({
            url: '/users/checkColorAvailability/'+color,
            beforeSend: function () {
                var message = '<img src="/img/loading_small.gif" /> Checking to see if #'+color+' is available...';
                registrationForm.showColorAjaxMessage(message, null);
            },
            success: function (data) {
                if (data === '1') {
                    registrationForm.showColorAjaxMessage('#'+color+' is available! :)', 'success');
                } else if (data === '0') {
                    registrationForm.showColorAjaxMessage('#'+color+' is already taken. :(', 'error');
                } else {
                    registrationForm.showColorAjaxMessage('There was an error checking the availability of #'+color+'.', null);
                }
            },
            error: function () {
                registrationForm.showColorAjaxMessage('There was an error checking the availability of #'+color+'.', null);
            },
            complete: function () {
                this.color_avail_request = null;
            }
        });
    }
};

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

function setupThoughtwordLinks(container) {
    container.find('a.thoughtword').click(function (event) {
        event.preventDefault();
        popup.open({
            url: $(this).attr('href')
        });
    });
}

function setupCloudElaboration() {
    $('#full_cloud_link').click(function (event) {
        event.preventDefault();
        $.ajax({
            url: '/thoughts/cloud',
            beforeSend: function () {
                $('#cloud_loading').show();
            },
            success: function (data) {
                $('#cloud_loading').hide();
                $('#cloud_elaborator').fadeOut(500);
                var container = $('#cloud_container');
                container.fadeOut(500, function() {
                    container.html(data);
                    setupThoughtwordLinks(container);
                    container.fadeIn(500);
                });
            }
        });
    });
}

var popup = {
	init: function () {
		var background = $('<div id=\"overlaid_bg\" title="Click to close popup"></div>');
		background.click(function (event) {
            popup.close();
        });
		$('html').keydown(function (event) {
            // Close when esc key is pressed
            if (event.which == 27) {
                popup.close();
            }
        });
		var popupContainer = $('<div id=\"overlaid\" data-origin-url=\"'+window.location.pathname+'\"></div>');
		$('body').append(background).append(popupContainer);
	},
	
	open: function (options) {
		if (! options.hasOwnProperty('push_history_state')) {
	        options.push_history_state = true;
	    }
	    
	    // If url is this page's original url and a popup is open, just close the popup 
	    var origin_url = $('#overlaid').data('originUrl');
	    if (options.url == origin_url && this.isOpen()) {
	        this.close(options.push_history_state);
	        return;
	    }
	    
	    // Initialize popup container(s)
	    if ($('#overlaid').length === 0) {
	        this.init();
	    }
	    
	    // Remember scroll position of background content so it can be preserved
	    var scrollTop = $(window).scrollTop();
	    
	    // Tell the body tag what's up
	    $('body').addClass('popup_active');
	    
	    if (this.isOpen()) {
	    	var container = $('#overlaid');
	        container.fadeOut(300, function () {
	            container.children('div').hide();
	            popup.ajaxLoad(options);
	        });
	    } else {
	    	var bg = $('#overlaid_bg');
	    	$('#content_outer').scrollTop(scrollTop);
	        bg.fadeIn(300, function () {
	            popup.ajaxLoad(options);
	        });
	    }
	},
	
	ajaxLoad: function (options) {
		var container = $('#overlaid');
	    var bg = $('#overlaid_bg');
		
        // If this popup has already been loaded, show it
        var existing_popup = container.children('div[data-popup-url="'+options.url+'"]');
        if (existing_popup.length > 0) {
            if (options.push_history_state) {
                pushHistoryState(options.url);
            }
            existing_popup.show();
            container.fadeIn(300);
            return;
        }
        
        bg.addClass('loading');
        $.ajax({
            url: options.url,
            success: function (data) {
            	// Callbacks passed through options
                if (options.hasOwnProperty('success')) {
                    options.success();
                }
                
                var inner_container = $('<div>'+data+'</div>');
                container.append(inner_container).fadeIn(300);
                var thought_containers = container.find('> div > div.thought');
                if (thought_containers.length > 0) {
                    setupThoughtwordLinks(thought_containers);
                }
                
                var displayed_url = popup.getDisplayedUrl(options.url);
                
                if (options.push_history_state) {
                    pushHistoryState(displayed_url);
                }
                
                /* Give the popup a wrapper that identifies the content by URL
                 * so it can be found later via history navigation. */
                inner_container.attr('data-popup-url', displayed_url);
            },
            complete: function () {
                bg.removeClass('loading');
            }
        });
    },
    
    isOpen: function () {
    	return $('#overlaid > div:visible').length > 0;
    },
    
    close: function (push_history_state) {
        if (typeof push_history_state == 'undefined') {
            push_history_state = true;
        }
        
        $('#overlaid').fadeOut(600, function () {
            $('#overlaid_bg').fadeOut(1000, function () {
                if (push_history_state) {
                    var origin_url = $('#overlaid').data('originUrl');
                    pushHistoryState(origin_url);
                }
                $('#overlaid > div').hide();
                var scrollTop = $('#content_outer').scrollTop();
                $('body.popup_active').removeClass('popup_active');
                $(window).scrollTop(scrollTop);
            });
        });
    },
    
    getDisplayedUrl: function (url) {
    	/* Opening a popup for /random should push a URL 
         * specific to the result to the browser's history */
    	if (url == '/random') {
    		var thoughtword = $('#overlaid')
                .find('h1')
                .first()
                .html()
                .trim()
                .toLowerCase();
    		return '/t/'+thoughtword;
    	}
    	return url;
    }
};

function pushHistoryState(url) {
    // Don't push state if the URL isn't actually changing
    if (url == window.location.pathname) {
        return;
    }
    
    history.pushState(
        {
            url: url
        },
        'Ether :: '+url,
        url
    );
}

function setupOnPopState() {
    window.onpopstate = function (event) {
        if (event.state) { 
            popup.open({
                url: event.state.url,
                push_history_state: false
            });
        } else {
            popup.close(false);
        }
    };
}

function setupHeaderLinks() {
    $('#random_link, #login_link, #register_link').click(function (event) {
        event.preventDefault();
        popup.open({
            url: $(this).attr('href')
        });
    });
}

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

var userIndex = {
    init: function () {
        $('#resize').click(function (event) {
            event.preventDefault();
            var container = $('.users_index');
            if (container.hasClass('resized')) {
                container.removeClass('resized');
                container.children('.colorbox').css({
                    height: '',
                    width: ''
                });
            } else {
                userIndex.resizeColorboxes();
            }
        });
    },
    resizeColorboxes: function () {
        $('.users_index').addClass('resized');
        $('.users_index .colorbox').each(function () {
            var scale = $(this).data('resize');
            var size = 100 * (scale / 100);
            $(this).css({
                height: size+'px',
                width: size+'px'
            });
                
        });
    }
};

var messages = {
    currentRequest: null,
    init: function () {
        $('#conversations_index a').click(function (event) {
            event.preventDefault();
            var color = $(this).data('color');
            messages.selectConversation(color);
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
    }
};

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

var recentActivity = {
    init: function () {
        var container = $('#recent');
        container.find('.nav a').click(function (event) {
            event.preventDefault();
            var link = $(this);
            $.ajax({
                url: link.attr('href'),
                beforeSend: function () {
                    container.fadeTo(200, 0.5);
                },
                success: function (data) {
                    container.fadeOut(100, function () {
                        container.html(data);
                        container.fadeTo(100, 1);
                    });
                },
                error: function () {
                    container.fadeTo(200, 1);
                    alert('Whoops. There was an error. Please try again.');
                }
            });
        });
    }
};

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
		if (targetType == 'comment') {
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