function toggleBGFade(fade_interval) {
	if (! fade_interval) {
		fade_interval = 1000;
	}
	if (fading_interval_id) {
		clearInterval(fading_interval_id);
		fading_interval_id = 0;
	} else {
		fading_interval_id = setInterval("adjustBackground()", fade_interval);
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
	if (Math.round(Math.random()) == 0) {
		var adjustment = -1;
	} else {
		var adjustment = 1;
	}
	if (rand_color == 0) {
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
	if (rand_color == 0) {
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

function add_comment(thought_id) {
	var form_container = $('newcomment' + thought_id + 'add');
	if (form_container.visible()) {
		return false;
	}
	var button = $('newcomment' + thought_id + 'button');
	Effect.SlideUp(button, {
		duration: 0.2,
		queue: {position: 'end', limit: 1, scope: 'addcomment_button'},
	});
	Effect.SlideDown(form_container, {
		duration: 0.2,
		queue: {position: 'end', limit: 1, scope: 'addcomment'},
		afterFinish: function() {
			Effect.ScrollTo(form_container, {duration: '0.2', offset: -40});
			form_container.down('textarea').focus();
		}
	});
	return false;
}

function cancel_comment(thought_id) {
	var form_container = $('newcomment' + thought_id + 'add');
	if (! form_container.visible()) {
		return false;
	}
	var button = $('newcomment' + thought_id + 'button');
	Effect.SlideDown(button, {
		duration: 0.2,
		queue: {position: 'end', limit: 1, scope: 'addcomment_button'},
	});
	Effect.SlideUp(form_container, {
		duration: 0.2,
		queue: {position: 'end', limit: 1, scope: 'addcomment'},
	});
	return false;
}

function insert_comment(thought_id) {
	$('newcomment' + thought_id + 'view').insert({'bottom': $('comment_just_added')});
	$('comment_just_added').id = '';
	$('newcomment' + thought_id + 'add').down('textarea').update();
	cancel_comment(thought_id);
}

function add_thought(thought_id) {
	var form_container = $('newthoughtadd');
	if (form_container.visible()) {
		return false;
	}
	Effect.SlideDown(form_container, {
		duration: 0.2,
		queue: {position: 'end', limit: 1, scope: 'addthought'},
		afterFinish: function() {
			form_container.down('textarea').focus();
		}
	});
	$('newthoughtbutton').hide();
	return false;
}

function cancel_thought() {
	var form_container = $('newthoughtadd');
	if (! form_container.visible()) {
		return false;
	}
	Effect.SlideUp(form_container, {
		duration: 0.2,
		queue: {position: 'end', limit: 1, scope: 'addthought'}
	});
	$('newthoughtbutton').show();
	return false;
}

function insert_thought() {
	$('newthoughtview').insert({'top': $('thought_just_added')});
	$('thought_just_added').id = '';
	$('newthoughtadd_form').down('textarea').update();
	cancel_thought();
}

function dontwannathink() {
	$('#wannathink_choices').slideUp(500, function () {
		$('#wannathink_rejection').slideDown(500);
	});
}

function d2h(d) {
	return d.toString(16);
}

function h2d(h) {
	return parseInt(h,16);
}

var registrationForm = {
	color_avail_request: null,
		
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
		if (this.color_avail_request != null) {
			this.color_avail_request.abort();
		}
		this.color_avail_request = $.ajax({
			url: '/users/check_color_availability/'+color,
			beforeSend: function () {
				var message = '<img src="/img/loading4.gif" /> Checking to see if #'+color+' is available...';
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

function showFlashMessages() {
	var messages = $('#flash_messages');
	if (! messages.is(':visible')) {
		messages.fadeIn(500);
	}
}

function hideFlashMessages() {
	var messages = $('#flash_messages');
	if (messages.is(':visible')) {
		messages.fadeOut(500, function() {
			$('#flash_messages ul').empty();
		});
	}
}

function insertFlashMessage(message, classname) {
	var msgLi = $(document.createElement('li'))
		.addClass(classname)
		.append('<p>'+message+'</p>')
		.hide()
		.fadeIn(500);
	$('#flash_messages ul').append(msgLi);
	if (! $('#flash_messages').is(':visible')) {
		showFlashMessages();
	}
}

function setupThoughtwordLinks(container) {
	container.find('a.thoughtword').click(function (event) {
		event.preventDefault();
		overlayContent({
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

function overlayContent(options) {
	if (! options.hasOwnProperty('push_history_state')) {
		options.push_history_state = true;
	}
	
	// If url is this page's original url and a popup is open, just close the popup 
	var origin_url = $('#overlaid').data('originUrl');
	if (options.url == origin_url && popupIsOpen()) {
		closePopup(options.push_history_state);
		return;
	}
	
	if ($('#overlaid').length == 0) {
		$('body').append($('<div id=\"overlaid_bg\" title="Click to close popup"></div>'));
		$('body').append($('<div id=\"overlaid\" data-origin-url=\"'+window.location.pathname+'\"></div>'));
		setupOverlayCloser();
	}
	var container = $('#overlaid');
	var bg = $('#overlaid_bg');
	var async_load = function (url, bg, container) {
		// If this popup has already been loaded, show it
		var existing_popup = container.children('div[data-popup-url="'+url+'"]');
		if (existing_popup.length > 0) {
			if (options.push_history_state) {
				pushHistoryState(url);
			}
			existing_popup.show();
			container.fadeIn(300);
			return;
		}
		
		bg.addClass('loading');
		$.ajax({
			url: url,
			beforeSend: function () {
			},
			success: function (data) {
				if (options.hasOwnProperty('success')) {
					options.success();
				}
				
				var inner_container = $('<div></div>');
				inner_container.html(data);
				container.append(inner_container);
				
				container.fadeIn(300);
				var scroll_to = $('#overlaid').offset().top - $('#header').height();
				$('html,body').animate({
					scrollTop: scroll_to
				}, 1000);
				var thought_containers = container.find('> div > div.thought');
				if (thought_containers.length > 0) {
					setupThoughtwordLinks(thought_containers);
				}
				
				/* Opening a popup for /random should function the same as
				 * opening a popup for whatever word /random picks. */
				if (url == '/random') {
					var thoughtword = inner_container
						.find('h1')
						.first()
						.html()
						.trim()
						.toLowerCase();
					var displayed_url = '/t/'+thoughtword;
				} else {
					var displayed_url = url;
				}
				
				if (options.push_history_state) {
					pushHistoryState(displayed_url);
				}
				
				/* Give the popup a wrapper that identifies the content by URL
				 * so it can be found later via history navigation. */
				inner_container.attr('data-popup-url', displayed_url);
			},
			error: function () {
			},
			complete: function () {
				bg.removeClass('loading');
			}
		});
	};
	if (popupIsOpen()) {
		container.fadeOut(300, function () {
			container.children('div').hide();
			async_load(options.url, bg, container);
		});
	} else {
		bg.fadeIn(300, function () {
			async_load(options.url, bg, container);
		});
	}
}

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

function setupOverlayCloser() {
	$('#overlaid_bg').click(function (event) {
		closePopup();
	});
	$('html').keydown(function (event) {
		// Close when esc key is pressed
		if (event.which == 27) {
			closePopup();
		}
	});
	
}

function closePopup(push_history_state) {
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
		});
	});
}

function popupIsOpen() {
	return $('#overlaid > div:visible').length > 0;
}

function setupOnPopState() {
	window.onpopstate = function (event) {
		if (event.state) { 
			overlayContent({
				url: event.state.url,
				push_history_state: false
			});
		} else {
			closePopup(false);
		}
	};
}

function setupHeaderLinks() {
	$('#random_link, #login_link, #register_link').click(function (event) {
		event.preventDefault();
		overlayContent({
			url: $(this).attr('href')
		});
	});
}

function setupThoughtwordIndex() {
	$('body').scrollspy({
		target: '.abc_thoughts_shortcuts',
		offset: 140
	});
	$('.abc_thoughts_shortcuts a').click(function (event) {
		$('html,body').animate({
			scrollTop: $(this.hash).offset().top
		}, 1000);
	});
	$('#alphabetical_words').find('a.thoughtword').click(function (event) {
		event.preventDefault();
		var top_offset = $(this).parents('section').children('h2').offset().top;
		overlayContent({
			url: $(this).attr('href'),
			success: function () {
				$('#overlaid').css({
					top: top_offset+'px',
					position: 'absolute'
				});
			}
		});
	});
}

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

var messagesPage = {
	currentRequest: null,
	init: function () {
		$('#conversations_index a').click(function (event) {
			event.preventDefault();
			var user_id = $(this).data('userId');
			messagesPage.selectConversation(user_id);
		});
	},
	cancelCurrentRequest: function () {
		if (this.currentRequest) {
			this.currentRequest.abort();
		}
		$('#conversations_index .loading').removeClass('loading');
	},
	selectConversation: function (user_id, scroll_to_selection) {
		var conv_index = $('#conversations_index');
		var conv_link = conv_index.find('a[data-user-id='+user_id+']');
		this.cancelCurrentRequest();
		conv_link.addClass('loading');
		this.currentRequest = $.ajax({
			url: '/messages/view_conversation/'+user_id,
			complete: function () {
				conv_link.removeClass('loading');
			},
			success: function (data) {
				conv_index.find('a.selected').removeClass('selected');
				conv_link.addClass('selected');
				var outer_container = $('#selected_conversation_wrapper');
				var inner_container = $('#conversation');
				
				if (inner_container.length > 0) {
					inner_container.fadeOut(150, function () {
						outer_container.html(data);
						inner_container = $('#conversation');
						inner_container.fadeIn(150);
						inner_container.scrollTop(inner_container.prop('scrollHeight'));
					});
				} else {
					outer_container.fadeOut(150, function () {
						outer_container.html(data);
						outer_container.fadeIn(150);
						inner_container = $('#conversation');
						inner_container.scrollTop(inner_container.prop('scrollHeight'));
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
	}
};

var profilePage = {
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
					var loading_indicator = $('<img src="/img/loading4.gif" class="loading" alt="Loading..." />');
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
}