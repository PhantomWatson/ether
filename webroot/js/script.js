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
    	var refresh = function (model, formattingKey) { 
        	$('div.'+model).each(function () {
        		var post = $(this);
        		if (post.data('formatting-key') == formattingKey) {
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
            url: '/colors/get-name/'+color,
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
    scrollToLastMsg: function () {
        var lastMsg = $('#conversation div.row:last-child');
        if (lastMsg.length === 0) {
            return;
        }
        $(window).scrollTo(lastMsg, 1000, {
            interrupt: true,
            offset: -100,
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
    },
    setupPagination: function () {
        var links = $('.convo_pagination a');
        var loadingIndicator = $('<img src="/img/loading_small.gif" class="loading" alt="Loading..."/>');
        loadingIndicator.hide();
        links.append(loadingIndicator);
        links.click(function (event) {
            event.preventDefault();
            var link = $(this);
            var url = link.attr('href').split('?');
            $.ajax({
                data: url[1],
                beforeSend: function () {
                    var loading = link.children('.loading');
                    if (loading.is(':visible')) {
                        return false;
                    }
                    loading.show();
                },
                success: function (data) {
                    var row = link.parents('.row');
                    row.after(data);
                    row.remove();
                    messages.setupPagination();
                },
                error: function () {
                    alert('There was an error loading more messages.');
                },
                complete: function () {
                    link.children('.loading').hide();
                }
            });
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

var search = {
    init: function () {
        $('#header-search input').on('input', function () {
            // Remove spaces
            var input = $(this);
            var original = input.val();
            var spaceless = original.replace(' ', '');
            if (original != spaceless) {
                input.val(spaceless);
            }

            search.filterCloud(original);
        });
    },
    filterCloud: function (searchTerm) {
        var cloud = $('#frontpage_cloud');

        // Skip if not on front page
        if (cloud.length === 0) {
            return;
        }

        // Show all words if search term is empty
        if (searchTerm === '') {
            cloud.find('a').show();
            return;
        }

        cloud.find('> a.thoughtword').each(function () {
            var link = $(this);
            var word = $(link).html().trim();
            if (word.search(searchTerm) === -1) {
                link.hide();
            } else {
                link.show();
            }
        });
    }
};

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
