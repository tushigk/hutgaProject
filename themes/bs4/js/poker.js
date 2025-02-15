document.addEventListener("DOMContentLoaded", function () {
	// lazy load
	setTimeout(function () {
		$(".js-bg").each(function () {
			$(this).css('background-image', 'url(' + $(this).data("bg") + ')');
		});
		$(".js-img").each(function () {
			$(this).attr('src', $(this).data("src"));
		});
	}, 200);
	// loader
	setTimeout(function () {
		$('body').removeClass('loaded');
	}, 400);
});

/* viewport width */
function viewport() {
	var e = window,
		a = 'inner';
	if (!('innerWidth' in window)) {
		a = 'client';
		e = document.documentElement || document.body;
	}
	return { width: e[a + 'Width'], height: e[a + 'Height'] }
};
/* viewport width */

(function () {

	/* components */

	if ($('.poker__chat-messages').length) {
		$(".poker__chat-messages").mCustomScrollbar({
			scrollInertia: 500,
			mouseWheelPixels: 100
		});
		setTimeout(function () {
			$(".poker__chat-messages").mCustomScrollbar("scrollTo", "bottom", { scrollInertia: 0 });
		}, 500);
	};

	if ($('.poker__chat-logs').length) {
		$(".poker__chat-logs").mCustomScrollbar({
			scrollInertia: 500,
			mouseWheelPixels: 100
		});
	};



	if ($('.poker__chat-head-tab').length) {
		$chat_head_tab = $('.poker__chat-head-tab');
		$chat_content_tab = $('.poker__chat-content-tab');

		$('.poker__chat-head-tab[data-tabname="chat"]').addClass('active');
		$('.poker__chat-content-tab[data-tabcontent="chat"]').addClass('active');

		$chat_head_tab.on('click', function () {
			var this_data = $(this).data('tabname');
			if (this_data == 'chat') {
				$chat_head_tab.removeClass('active');
				$chat_content_tab.removeClass('active');
				$chat_head_tab.filter('[data-tabname="chat"]').addClass('active');
				$chat_content_tab.filter('[data-tabcontent="chat"]').addClass('active');
				$(".poker__chat-messages").mCustomScrollbar("scrollTo", "bottom", { scrollInertia: 0 });
			}
			if (this_data == 'logs') {
				$chat_head_tab.removeClass('active');
				$chat_content_tab.removeClass('active');
				$chat_head_tab.filter('[data-tabname="logs"]').addClass('active');
				$chat_content_tab.filter('[data-tabcontent="logs"]').addClass('active');
				if (typeof viewport_wid !== 'undefined' && viewport_wid > 850) {
					$(".poker__chat-logs").mCustomScrollbar({
						scrollInertia: 500,
						mouseWheelPixels: 100
					});
				}
			}
		});
	}


	if ($('.poker__chat-btn').length) {
		var $poker_chat_btn = $('.poker__chat-btn');
		var $poker_chat = $('.poker__chat');
		$poker_chat_btn.on('click', function () {
			if (!$(this).hasClass('active')) {
				$(this).addClass('active');
				$poker_chat.addClass('active');
			} else {
				$(this).removeClass('active');
				$poker_chat.removeClass('active');
			}
		});
		$('.poker__chat-head-close').click(function () {
			$poker_chat_btn.removeClass('active');
			$poker_chat.removeClass('active');
		});
	}


	// Poker Bet/Raise Button
	var $poker_bet_btn = '.poker__bet-btn';
	$(document).on('click', $poker_bet_btn, function () {
		var $poker_betpopup = $('.poker__betpopup');

		if (!$(this).hasClass('active')) {
			$(this).addClass('active');
			$poker_betpopup.addClass('active');
		} else {
			$(this).removeClass('active');
			$poker_betpopup.removeClass('active');
			// temp start
			$('.poker__bet-btn-summa').hide();
			$('.poker__bet-btn-name').html('Bet');
			// temp end
		}
	});





	/*
	if($('.styled').length) {
		$('.styled').styler();
	};
	if($('.fancybox').length) {
		$('.fancybox').fancybox({
			margin  : 10,
			padding  : 10
		});
	};
	if($('.slick-slider').length) {
		$('.slick-slider').slick({
			dots: true,
			infinite: false,
			speed: 300,
			slidesToShow: 4,
			slidesToScroll: 4,
			responsive: [
				{
				  breakpoint: 1024,
				  settings: {
					slidesToShow: 3,
					slidesToScroll: 3,
					infinite: true,
					dots: true
				  }
				},
				{
				  breakpoint: 600,
				  settings: "unslick"
				}
			]
		});
	};
	
	*/

	/* components */

	//отмена перетаскивания картинок
	$("img, a").on("dragstart", function (e) {
		e.preventDefault();
	});

}());

var handler = function () {
	var viewport_wid = viewport().width;
	var viewport_height = viewport().height;

	if (viewport_wid <= 850) {
		$(".poker__chat-logs, .poker__chat-messages").mCustomScrollbar('destroy');
	} else {
		if ($('.poker__chat-messages').length) {
			$(".poker__chat-messages").mCustomScrollbar({
				scrollInertia: 500,
				mouseWheelPixels: 100
			});
			setTimeout(function () {
				$(".poker__chat-messages").mCustomScrollbar("scrollTo", "bottom", { scrollInertia: 0 });
			}, 500);
		};

		if ($('.poker__chat-logs').length) {
			$(".poker__chat-logs").mCustomScrollbar({
				scrollInertia: 500,
				mouseWheelPixels: 100
			});
		};
	}

}

$(window).bind('load', handler);
$(window).bind('resize', handler);
