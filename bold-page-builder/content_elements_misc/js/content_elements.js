(function ($) {
	"use strict";
	// animations
	window.bt_bb_animate_elements = function () {
		var $elems = $('.animate:not(.animated)');
		$elems.each(function () {
			var $elm = $(this);
			if (isOnScreen($elm)) {
				$elm.addClass('animated');
				if ($elm.hasClass('bt_bb_counter')) {
					bt_bb_animate_counter($elm);
				}
				if (typeof window.local_bt_bb_animate_elements == 'function') {
					local_bt_bb_animate_elements($elm);
				}
			}
		});
		bt_bb_lazy_load_images();
		$('.slick-slider .slick-slide:not(.slick-active) .animate').removeClass('animated');
	}

	window.bt_bb_animate_elements_optim = function () {

		const options = {
			root: null,
			rootMargin: "0px",
			treshold: 0,
		};

		const targetElements_anim = document.querySelectorAll(
			".animate:not(.animated)"
		);

		const observer_animation = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					$(entry.target).addClass("animated");
					if ($(entry.target).hasClass("bt_bb_counter")) {
						bt_bb_animate_counter($(entry.target));
					}
					if (typeof window.local_bt_bb_animate_elements == "function") {
						local_bt_bb_animate_elements($(entry.target));
					}

					observer_animation.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements_anim.forEach((element) => {
			observer_animation.observe(element);
		});

		//bt_bb_lazy_load_images();
		$(".slick-slider .slick-slide:not(.slick-active) .animate").removeClass(
			"animated"
		);
	};

	// lazy image load
	window.bt_bb_lazy_load_images = function () {
		var $elems = $('img.btLazyLoadImage:not(.btLazyLoaded)');
		$elems.each(function () {
			var $elm = $(this);
			if (isOnScreen($elm, -200)) {
				$elm.addClass('btLazyLoaded');
				$elm.attr('src', $elm.data('image_src'));
			}
		});
		var $elems = $('image.btLazyLoadImage:not(.btLazyLoaded)');
		$elems.each(function () {
			var $elm = $(this);
			if (isOnScreen($elm, -200)) {
				$elm.addClass('btLazyLoaded');
				$elm.attr('xlink:href', $elm.data('image_src'));
			}
		});
		var $elems = $('.btLazyLoadBackground:not(.btLazyLoaded)');
		$elems.each(function () {
			var $elm = $(this);
			if (isOnScreen($elm, -200)) {
				$elm.addClass('btLazyLoaded');
				$elm.css('background-image', 'url(' + $elm.data('background_image_src') + ')');
			}
		});
	}

	window.bt_bb_lazy_load_images_optim = function () {

		const options = {
			root: null,
			rootMargin: "200px",
			treshold: 0,
		};

		const targetElements1_load = document.querySelectorAll(
			"img.btLazyLoadImage:not(.btLazyLoaded)"
		);

		const observer1_load = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {

					$(entry.target).addClass("btLazyLoaded");
					$(entry.target).attr("src", $(entry.target).data("image_src"));

					observer1_load.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements1_load.forEach((element) => {
			observer1_load.observe(element);
		});

		const targetElements2_load = document.querySelectorAll(
			"image.btLazyLoadImage:not(.btLazyLoaded)"
		);

		const observer2_load = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {

					$(entry.target).addClass("btLazyLoaded");
					$(entry.target).attr("xlink:href", $(entry.target).data("image_src"));

					observer2_load.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements2_load.forEach((element) => {
			observer2_load.observe(element);
		});

		const targetElements3_load = document.querySelectorAll(
			".btLazyLoadBackground:not(.btLazyLoaded)"
		);

		const observer3_load = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {

					$(entry.target).addClass("btLazyLoaded");
					$(entry.target).css(
						"background-image",
						"url(" + $(entry.target).data("background_image_src") + ")"
					);

					observer3_load.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements3_load.forEach((element) => {
			observer3_load.observe(element);
		});
	};

	// css image grid lighbox

	window.bt_bb_init_css_image_grid_lightbox = function () {
		$('.bt_bb_css_image_grid').not('.bt_bb_no_lightbox').each(function () {
			$(this).magnificPopup({
				delegate: '.bt_bb_css_image_grid_item',
				type: 'image',
				gallery: {
					enabled: true
				},
				callbacks: {
					elementParse: function (item) { item.src = item.el.data('src-full'); }
				},
				closeBtnInside: false,
				fixedContentPos: false
			});
		});
	}

	// new startup

	window.bt_bb_init_css_image_grid_lightbox_startup = function () {
		const options = {
			root: null,
			rootMargin: "200px",
			treshold: 0,
		};

		const targetElements_imageGridLightBox = document.querySelectorAll(
			".bt_bb_css_image_grid:not(.bt_bb_no_lightbox)"
		);

		const observer_imageGridLightBox = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {

					$(entry.target).magnificPopup({
						delegate: ".bt_bb_css_image_grid_item",
						type: "image",
						gallery: {
							enabled: true,
						},
						callbacks: {
							elementParse: function (item) {
								item.src = item.el.data("src-full");
							},
						},
						closeBtnInside: false,
						fixedContentPos: false,
					});

					observer_imageGridLightBox.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements_imageGridLightBox.forEach((element) => {
			observer_imageGridLightBox.observe(element);
		});
	};

	// tabs

	window.bt_bb_init_tabs = function () {
		$('.bt_bb_tabs').not('.bt_bb__inited').each(function () {
			$(this).addClass('bt_bb__inited');
			$(this).find('.bt_bb_tabs_header').on('click', 'li', function () {
				$(this).siblings().removeClass('on');
				$(this).addClass('on');
				$(this).closest('.bt_bb_tabs').children('.bt_bb_tabs_tabs').children('.bt_bb_tab_item').removeClass('on');
				$(this).closest('.bt_bb_tabs').children('.bt_bb_tabs_tabs').children('.bt_bb_tab_item').eq($(this).index()).addClass('on');
			});
			$(this).find('li').first().trigger('click');
		});
	}

	// new startup

	window.bt_bb_init_tabs_startup = function () {
		const options = {
			root: null,
			rootMargin: "200px",
			treshold: 0,
		};

		const targetElements_Tabs = document.querySelectorAll(
			".bt_bb_tabs:not(.bt_bb__inited)"
		);

		const observer_Tabs = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {

					$(entry.target).addClass("bt_bb__inited");
					$(entry.target)
						.find(".bt_bb_tabs_header")
						.on("click", "li", function () {
							$(this).siblings().removeClass("on");
							$(this).addClass("on");
							$(this)
								.closest(".bt_bb_tabs")
								.children(".bt_bb_tabs_tabs")
								.children(".bt_bb_tab_item")
								.removeClass("on");
							$(this)
								.closest(".bt_bb_tabs")
								.children(".bt_bb_tabs_tabs")
								.children(".bt_bb_tab_item")
								.eq($(this).index())
								.addClass("on");
						});
					$(entry.target).find("li").first().trigger("click");

					observer_Tabs.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements_Tabs.forEach((element) => {
			observer_Tabs.observe(element);
		});
	};

	// isOnScreen fixed

	function iOSversion() {
		if (/iP(hone|od|ad)/.test(navigator.platform)) {
			// supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
			var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
			return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
		} else {
			return false;
		}
	}

	var ver = iOSversion();

	// isOnScreen

	function isOnScreen(elem, top_offset) {
		if (ver && ver[0] == 13) return true;
		top_offset = (top_offset === undefined) ? 75 : top_offset;
		var element = elem.get(0);
		if (element == undefined) return false;
		var bounds = element.getBoundingClientRect();
		var output = bounds.top + top_offset < window.innerHeight && bounds.bottom > 0;
		// alert(output);
		return output;
	}

	// animate counter

	function bt_bb_animate_counter($elm) {
		var number_length = $elm.data('digit-length');
		for (var i = parseInt(number_length); i > 0; i--) {
			var digit = parseInt($elm.children('.p' + i).data('digit'));
			if (digit == 0) digit = 10;
			if (isNaN(digit)) digit = 10;
			for (var j = 0; j <= digit; j++) {
				$elm.children('.p' + i).css('transform', 'translateY(-' + digit * $elm.outerHeight() + 'px)');
			}
			$elm.addClass('animated');
		}

		return false;
	}

	// paralax helpers 

	window.bt_bb_requestAnimFrame = function () {
		return (
			window.requestAnimationFrame ||
			window.webkitRequestAnimationFrame ||
			window.mozRequestAnimationFrame ||
			window.oRequestAnimationFrame ||
			window.msRequestAnimationFrame ||
			function (callback) {
				window.setTimeout(callback, 1000 / 60);
			}
		);
	}();

	function bt_clamp_number(val, min, max) {
		return val > max ? max : val < min ? min : val;
	}

	var is_edge = navigator.userAgent.indexOf('Edge') > -1;
	var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
	var is_safari = navigator.userAgent.indexOf('Safari') > -1;
	var is_firefox = navigator.userAgent.indexOf('Firefox') > -1;
	if (is_chrome && is_safari) window.is_safari = false;
	var chrome_version = false;
	// alert( navigator.userAgent );
	// if ( is_chrome ) chrome_version = /Chrome\/([0-9]+)/.exec(navigator.userAgent)[1];

	window.bt_bb_raf_loop = function () {
		var win_w = window.innerWidth;
		var win_h = window.innerHeight;
		const SPEED_DELTA = 0.0001;

		$('html.bt_bb_backgroud_fixed_supported .bt_bb_parallax').each(function () {
			if ( window.bt_bb_res !== 'xxl' && window.bt_bb_res !== 'xl') {
				$(this)[0].style.backgroundPosition = 'center center';
				return false;
			} else {
				var bounds = $(this).hasClass('bt_bb_background_image_holder') ? this.parentElement.getBoundingClientRect() : this.getBoundingClientRect();

				if (bounds.top < win_h && bounds.bottom > 0) {
					var speed = parseFloat($(this).attr('data-parallax')) + SPEED_DELTA;
					if (speed == 2 + SPEED_DELTA) {
						// if ( false ) {
						// skip parallax, use fixed background
						$(this)[0].style.backgroundAttachment = 'fixed';
					} else {

						var offset = parseFloat($(this).attr('data-parallax-offset'));
						var ypos = offset - bounds.top * speed / 2;
						ypos = (ypos > -0.5 && ypos < 0.5) ? 0 : ypos;

						$(this)[0].style.backgroundPositionY = ypos + 'px';
					}

					// temp if, to support old sections
					if ($(this).hasClass('bt_bb_background_image_holder')) {

						var bounds_mid = (bounds.top + bounds.bottom) / 2;
						var scroll_distance = (bounds_mid - win_h / 2) / (win_h);
						scroll_distance = bt_clamp_number(scroll_distance, 0, 1);

						var ZOOM_START = parseFloat($(this).attr('data-parallax-zoom-start'));
						var ZOOM_END = parseFloat($(this).attr('data-parallax-zoom-end'));

						if (ZOOM_START >= 0 && ZOOM_END >= 0 && (ZOOM_START != 1 || ZOOM_END != 1)) {
							var zoom = ZOOM_START + (ZOOM_END - ZOOM_START) * (1 - scroll_distance);
							$(this)[0].style.transform = ' scale(' + zoom + ')';
						}

						var BLUR_START = parseFloat($(this).attr('data-parallax-blur-start'));
						var BLUR_END = parseFloat($(this).attr('data-parallax-blur-end'));

						if (BLUR_START >= 0 && BLUR_END >= 0 && (BLUR_START != 0 || BLUR_END != 0)) {
							var blur = BLUR_START + (BLUR_END - BLUR_START) * (1 - scroll_distance);
							$(this)[0].style.filter = ' blur(' + blur + 'px)';
						}

						var OPACITY_START = parseFloat($(this).attr('data-parallax-opacity-start'));
						var OPACITY_END = parseFloat($(this).attr('data-parallax-opacity-end'));

						if (OPACITY_START >= 0 && OPACITY_END >= 0 && (OPACITY_START != 1 || OPACITY_END != 1)) {
							var opacity = OPACITY_START + (OPACITY_END - OPACITY_START) * (1 - scroll_distance);
							$(this)[0].style.opacity = opacity;
						}
					}
				}
			}
		});
		window.bt_bb_raf_lock = false;
	}

	// touch screen detect

	window.bt_detect_touch = function () {
		if (typeof window !== 'undefined') {
			var bt_has_touch = Boolean(
				'ontouchstart' in window ||
				window.navigator.maxTouchPoints > 0 ||
				window.navigator.msMaxTouchPoints > 0 ||
				window.DocumentTouch && document instanceof DocumentTouch
			);
			if (bt_has_touch) $('html').addClass('bt_bb_touch');
			// Mobile device detection
			if (/Mobi|Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				$('html').addClass('bt_bb_mobile');
			}
		}
	}

	// screen detect

	window.bt_bb_get_screen_resolution = function ( not_resize = false ) {
		var width = Math.max( document.documentElement.clientWidth, window.innerWidth || 0 );
		var res = 'xxl';
		if ( width <= 1400 ) res = 'xl';
		if ( width <= 1200 ) res = 'lg';
		if ( width <= 992) res = 'md';
		if ( width <= 768 ) res = 'sm';
		if ( width <= 480 ) res = 'xs';
		
		window.bt_bb_res = res;

		$( 'html' ).attr( 'data-bt_bb_screen_resolution', window.bt_bb_res );

		$( '[data-bt-override-class]' ).each(function() {
			var override_classes = $( this ).data( 'bt-override-class' );
			for ( var prefix in override_classes ) {
				if ( override_classes[ prefix ][ window.bt_bb_res ] !== undefined ) {
					var new_class = prefix + override_classes[ prefix ][ window.bt_bb_res ];
				} else {
					var new_class = prefix + override_classes[ prefix ]['def'];
				}
				$( this ).removeClass( override_classes[ prefix ]['current_class'] );
				var animate = false;
				if ( new_class.includes( ' animate' ) ) {
					animate = true;
					if ( not_resize ) $( this ).css( 'transition', 'none' );
				}
				$( this ).addClass( new_class );
				if ( animate ) {
					setTimeout( () => {
						$( this ).css( 'transition', '' );
					}, 0 );
				}
				override_classes[ prefix ]['current_class'] = new_class;
				$( this ).data( 'override_classes', override_classes );
			};

		});

	}

	// Test fixed background support for iOS devices

	window.bt_bb_check_ios_fixed_background_support = function () {
		return (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) ||
			(/MacIntel/.test(navigator.platform) && $('html').hasClass('bt_bb_touch'));

		/* /MacIntel/... <- checks iPadPro */
	}

	// Test fixed background support

	window.bt_bb_check_fixed_background = function () {
		var el = document.createElement('div');
		try {
			if (!('backgroundAttachment' in el.style) || bt_bb_check_ios_fixed_background_support()) {
				$('html').removeClass('bt_bb_backgroud_fixed_supported');
				return false;
			}
			el.style.backgroundAttachment = 'fixed';
			if (('fixed' === el.style.backgroundAttachment)) {
				$('html').addClass('bt_bb_backgroud_fixed_supported');
				return true;
			}
		}
		catch (e) {
			$('html').removeClass('bt_bb_backgroud_fixed_supported');
			return false;
		}
	}

	// Countdown timer helpers

	window.bt_bb_countdown = function (elem, selector, i, arr, arr_prev) {

		if (arr[i] !== arr_prev[i] || elem.find(selector).children().eq(0).html() == '') {
			elem.find(selector).children().addClass('countdown_anim');
			elem.find(selector).children().eq(0).html(arr[i]);
			elem.find(selector).children().eq(1).html(arr_prev[i]);
			setTimeout(function () {
				elem.find(selector).children().eq(1).html(elem.find(selector).children().eq(0).html());
				elem.find(selector).children().removeClass('countdown_anim');
			}, 300);
		}
	}

	window.bt_bb_countdown_output = function (elem) {

		var s = elem.attr('data-init-seconds');
		if (isNaN(s)) {
			s = 0;
		}
		var delta = s;

		var days = Math.floor(delta / 86400);
		delta -= days * 86400;

		var hours = Math.floor(delta / 3600) % 24;
		delta -= hours * 3600;

		var minutes = Math.floor(delta / 60) % 60;
		delta -= minutes * 60;

		var seconds = delta;

		if (hours < 10) {
			hours = '0' + hours;
		}

		if (minutes < 10) {
			minutes = '0' + minutes;
		}

		if (seconds < 10) {
			seconds = '0' + seconds;
		}

		var seconds_arr_prev = seconds.toString().split('');
		var minutes_arr_prev = minutes.toString().split('');
		var hours_arr_prev = hours.toString().split('');

		s = s - 1;
		if (s < 0) {
			s = 0;
		}

		var delta = s;

		var days = Math.floor(delta / 86400);
		delta -= days * 86400;

		var hours = Math.floor(delta / 3600) % 24;
		delta -= hours * 3600;

		var minutes = Math.floor(delta / 60) % 60;
		delta -= minutes * 60;

		var seconds = delta;

		if (hours < 10) {
			hours = '0' + hours;
		}

		if (minutes < 10) {
			minutes = '0' + minutes;
		}

		if (seconds < 10) {
			seconds = '0' + seconds;
		}

		var seconds_arr = seconds.toString().split('');
		var minutes_arr = minutes.toString().split('');
		var hours_arr = hours.toString().split('');

		for (var i = 0; i <= 1; i++) {
			bt_bb_countdown(elem, '.seconds .n' + i, i, seconds_arr, seconds_arr_prev);
			bt_bb_countdown(elem, '.minutes .n' + i, i, minutes_arr, minutes_arr_prev);
			bt_bb_countdown(elem, '.hours .n' + i, i, hours_arr, hours_arr_prev);
		}

		var days_arr = days.toString().split('');

		var days_html = '';
		for (var i = 0; i < days_arr.length; i++) {
			days_html += '<span>' + days_arr[i] + '</span>';
		}

		// Create a text node instead of inserting raw HTML
		elem.find('.days').html(days_html + '<span class="days_text"><span></span></span>');

		// Then safely set the text content (not HTML) of the inner span
		var textContent = elem.find('.days').data('text');
		elem.find('.days_text span').text(textContent); // .text() escapes HTML automatically

		elem.attr('data-init-seconds', s);

	}

	function bt_bb_fix_slider_heights() {
		$('.bt_bb_content_slider.bt_bb_height_keep-height .slick-slider').each(function (index) {
			$(this).find('.slick-slide').height('auto');
			var slickTrack = $(this).find('.slick-track');
			var slickTrackHeight = $(slickTrack).height();
			$(this).find('.slick-slide').css('height', slickTrackHeight + 'px');
		});
	}

	function bt_bb_init_elements() {
		// get row structure

		const options = {
			root: null,
			rootMargin: "200px",
			treshold: 0,
		};

		// .bt_bb_section .bt_bb_row

		const targetElements_Row = document.querySelectorAll(
			".bt_bb_section .bt_bb_row"
		);

		const observer_Row = new IntersectionObserver(function (entries, observer) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					var data_structure = [];
					$(entry.target).data("structure", "0");
					$(entry.target)
						.find(".bt_bb_column")
						.each(function (index) {
							data_structure.push($(this).data("width"));
						});

					$(entry.target).attr("data-structure", data_structure.join("-"));

					observer_Row.unobserve(entry.target);
				}
			});
		}, options);

		targetElements_Row.forEach((element) => {
			observer_Row.observe(element);
		});

		// .bt_bb_row_inner

		const targetElements_RowInner =
			document.querySelectorAll(".bt_bb_row_inner");

		const observer_RowInner = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					var data_structure = [];
					$(entry.target).data("structure", "0");
					$(entry.target)
						.find(".bt_bb_column_inner")
						.each(function (index) {
							data_structure.push($(this).data("width"));
						});
					$(entry.target).attr("data-structure", data_structure.join("-"));

					observer_RowInner.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements_RowInner.forEach((element) => {
			observer_RowInner.observe(element);
		});

		// .slick-slider

		const targetElements_Slick = document.querySelectorAll(".slick-slider");

		const observer_Slick = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					$(entry.target).slick();

					$(entry.target)
						.find(".slick-prev")
						.on("click", function () {
							$(this).closest(".slick-slider").slick("slickPause");
						});

					$(entry.target)
						.find(".slick-next")
						.on("click", function () {
							$(this).closest(".slick-slider").slick("slickPause");
						});

					$(entry.target)
						.find(".slick-dots li")
						.on("click", function () {
							$(this).closest(".slick-slider").slick("slickPause");
						});

					$(
						".bt_bb_slider.bt_bb_use_lightbox .slick-slider .bt_bb_slider_item"
					).on("click", function () {
						$(this).closest(".slick-slider").slick("slickPause");
					});

					observer_Slick.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements_Slick.forEach((element) => {
			observer_Slick.observe(element);
		});

		// .bt_bb_slider.bt_bb_use_lightbox

		const targetElements_lightBox = document.querySelectorAll(
			".bt_bb_slider.bt_bb_use_lightbox"
		);

		const observer_lightBox = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {

					$(entry.target).magnificPopup({
						delegate: ".bt_bb_slider_item:not(.slick-cloned)",
						type: "image",
						gallery: {
							enabled: true,
						},
						callbacks: {
							elementParse: function (item) {
								item.src = item.el.data("src-full");
							},
						},
						closeBtnInside: false,
						fixedContentPos: false,
					});

					observer_lightBox.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements_lightBox.forEach((element) => {
			observer_lightBox.observe(element);
		});

		// .bt_bb_masonry_image_grid

		const targetElements_masonryImageGrid = document.querySelectorAll(
			".bt_bb_masonry_image_grid:not(.bt_bb_no_lightbox)"
		);

		const observer_masonryImageGrid = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {

					$(entry.target).magnificPopup({
						delegate: ".bt_bb_grid_item",
						type: "image",
						gallery: {
							enabled: true,
						},
						callbacks: {
							elementParse: function (item) {
								item.src = item.el.data("src-full");
							},
						},
						closeBtnInside: false,
						fixedContentPos: false,
					});

					observer_masonryImageGrid.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements_masonryImageGrid.forEach((element) => {
			observer_masonryImageGrid.observe(element);
		});

		// css image grid lighbox

		window.bt_bb_init_css_image_grid_lightbox_startup();

		// image lightbox

		$(document).on("click", ".bt_bb_image.bt_bb_use_lightbox a", function () {
			var url = $(this).attr("href").trim();

			if (url != "" && url != "#" && url != "#lightbox") {
				var contentType =
					url.toLowerCase().includes(".jpg") ||
						url.toLowerCase().includes(".jpeg") ||
						url.toLowerCase().includes(".png")
						? "image"
						: "iframe";
				$.magnificPopup.open({
					type: contentType,
					items: {
						src: url,
						title: $(this).attr("title"),
					},
					closeBtnInside: false,
				});
			} else {
				var url = $(this).find("img").attr("data-full_image_src");
				$.magnificPopup.open({
					type: "image",
					items: {
						src: url,
						title: $(this).attr("title"),
					},
					closeBtnInside: false,
					fixedContentPos: false,
				});
				return false;
			}
			return false;
		});

		// button, icon, service lightbox

		$(document).on(
			"click",
			".bt_bb_button.bt_bb_use_lightbox a, .bt_bb_icon.bt_bb_use_lightbox a, .bt_bb_service.bt_bb_use_lightbox a",
			function () {
				var url = $(this).attr("href");
				var contentType =
					url.toLowerCase().includes(".jpg") ||
						url.toLowerCase().includes(".jpeg") ||
						url.toLowerCase().includes(".png")
						? "image"
						: "iframe";

				$.magnificPopup.open({
					type: contentType,
					items: {
						src: url,
						title: $(this).attr("title"),
					},
					closeBtnInside: false,
				});

				return false;
			}
		);

		// force slider item equal height when keep height is active

		$(".bt_bb_content_slider.bt_bb_height_keep-height .slick-slider").on(
			"setPosition",
			function () {
				bt_bb_fix_slider_heights();
				$(this)
					.find("*[aria-hidden=true] a, *[aria-hidden=true] button")
					.attr("tabindex", -1);
				$(this)
					.find("*[aria-hidden=false] a, *[aria-hidden=false] button")
					.removeAttr("tabindex");
			}
		);
		$(window).on("resize", function (e) {
			bt_bb_fix_slider_heights();
			bt_bb_get_screen_resolution();
		});

		// bt_bb_elements.js resets animated class
		$(".slick-slider").on(
			"beforeChange",
			function (event, slick, currentSlide, nextSlide) {
				$(this).find(".slick-slide .animated").removeClass("animated");
				$(this)
					.find(".slick-slide[data-slick-index=" + nextSlide + "] .animate")
					.addClass("animated");
			}
		);

		// tabs

		window.bt_bb_init_tabs_startup();

		// Detect touch

		setTimeout(() => {
			bt_detect_touch();
		}, 0);

		// parallax

		bt_bb_check_fixed_background();

		if ($("html.bt_bb_backgroud_fixed_supported .bt_bb_parallax").length > 0) {
			window.bt_bb_raf_lock = false;

			$(window).on("mousewheel", function (e) { });

			$(window).on("scroll", function () {
				if (!window.bt_bb_raf_lock) {
					window.bt_bb_raf_lock = true;
					bt_bb_requestAnimFrame(bt_bb_raf_loop);
				}
			});

			//qbt_bb_requestAnimFrame(bt_bb_raf_loop);

			$(window).on("load", function () {
				bt_bb_requestAnimFrame(bt_bb_raf_loop);
			});
		}

		// Countdown

		const options_Countdown = {
			root: null,
			rootMargin: "1000px",
			treshold: 0,
		};

		const targetElements_Countdown =
			document.querySelectorAll(".btCountdownHolder");

		const observer_Countdown = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {

					setInterval(function () {
						bt_bb_countdown_output($(entry.target));
					}, 1000);

					observer_Countdown.unobserve(entry.target);
				}
			});
		},
			options_Countdown);

		targetElements_Countdown.forEach((element) => {
			observer_Countdown.observe(element);
		});

		// Accordion

		$(".bt_bb_wrapper").on("click", ".bt_bb_accordion_item_title", function () {
			var $item = $(this).closest(".bt_bb_accordion_item");
			if (!$item.hasClass("on")) {
				$(this)
					.closest(".bt_bb_accordion")
					.find(".bt_bb_accordion_item.on")
					.removeClass("on");
				$item.addClass("on");
				if (!window.bt_bb_initialaccordion) {
					var top_of_element = $item.offset().top;
					var bottom_of_element =
						$item.offset().top + $("#element").outerHeight();
					var bottom_of_screen =
						$(window).scrollTop() + $(window).innerHeight();
					var top_of_screen = $(window).scrollTop();
					var diff = top_of_screen - top_of_element;
					if (diff > 0) {
						$("html").scrollTop($("html").scrollTop() - diff - 15);
					}
				} else {
					window.bt_bb_initialaccordion = false;
				}
			} else {
				$(this).closest(".bt_bb_accordion_item").removeClass("on");
			}
		});

		const targetElements_Accordion =
			document.querySelectorAll(".bt_bb_accordion");

		const observer_Accordion = new IntersectionObserver(function (
			entries,
			observer
		) {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {

					if ($(entry.target).data("closed") != "closed") {
						window.bt_bb_initialaccordion = true;
						$(entry.target)
							.find(".bt_bb_accordion_item_title")
							.first()
							.click();
					}

					observer_Accordion.unobserve(entry.target);
				}
			});
		},
			options);

		targetElements_Accordion.forEach((element) => {
			observer_Accordion.observe(element);
		});
	}

	// google maps

	window.bt_bb_gmap_init_static_new = function (map_id) {
		var mapElement_jQuery = jQuery('#' + map_id);
		if (mapElement_jQuery.data('init-finished') == true) return false;
		var zoom = (typeof mapElement_jQuery.data('zoom') !== 'undefined') ? mapElement_jQuery.data('zoom') : 8;
		var custom_style = (typeof mapElement_jQuery.data('custom-style') !== 'undefined') ? mapElement_jQuery.data('custom-style').trim() : '';
		var api_key = (typeof mapElement_jQuery.data('api-key') !== 'undefined') ? mapElement_jQuery.data('api-key') : '';
		var height = (typeof mapElement_jQuery.data('height') !== 'undefined') ? mapElement_jQuery.data('height') : 0;
		bt_bb_gmap_init_static(map_id, zoom, custom_style, height, api_key);
		mapElement_jQuery.data('init-finished', true);

	}

	window.bt_bb_gmap_init_new = function (map_id) {
		var mapElement_jQuery = jQuery('#' + map_id);
		if (mapElement_jQuery.data('init-finished') == true) return false;
		var zoom = (typeof mapElement_jQuery.data('zoom') !== 'undefined') ? mapElement_jQuery.data('zoom') : 8;
		var custom_style = (typeof mapElement_jQuery.data('custom-style') !== 'undefined') ? mapElement_jQuery.data('custom-style').trim() : '';
		bt_bb_gmap_init(map_id, zoom, custom_style);
		mapElement_jQuery.data('init-finished', true);
	}

	document.addEventListener('readystatechange', function () {
		if ((document.readyState === 'complete')) {
			if (typeof (bt_bb_init_all_maps) !== 'function') {
				return false;
			}
			window.bt_bb_init_all_maps();
		};
	}, false);

	// callback from gmaps api
	window.bt_bb_init_all_maps = function () {
		jQuery('.bt_bb_google_maps_map').each(function (index) {
			if (jQuery(this).data('map-type') != 'static') {
				bt_bb_gmap_init_new(jQuery(this).attr('id'));
			} else {
				bt_bb_gmap_init_static_new(jQuery(this).attr('id'));
			};
		});
	};

	window.bt_bb_gmap_init_static = function (map_id, zoom, custom_style, height, api_key) {

		if (parseInt(height) <= 0) height = 400;

		var container = jQuery('#' + map_id).parent();
		var googleApiMapId = document.getElementById(map_id).id;

		var locations = container.find('.bt_bb_map_location');

		var center_map = container.data('center');
		if (center_map == 'no') {
			center_map = false;
		} else {
			center_map = true;
		}

		var lat_sum = 0;
		var lng_sum = 0;
		var markerStr = '';
		var n = 0;
		var colors = ['blue', 'green', 'red', 'purple', 'yellow', 'gray', 'orange', 'white', 'black', 'brown'];

		var centerLatLng;

		locations.each(function () {

			var lat = jQuery(this).data('lat');
			var lng = jQuery(this).data('lng');
			var iconStr = jQuery(this).data('icon') != "" ? 'scale:2%7Cicon:' + jQuery(this).data('icon') : 'size:mid%7Ccolor:' + colors[n];

			lat_sum += parseFloat(lat);
			lng_sum += parseFloat(lng);

			markerStr += '&markers=' + iconStr + '%7C' + lat + ',' + lng;

			centerLatLng = [lat, lng];

			n++;
		});

		if (center_map) {
			centerLatLng = [lat_sum / n, lng_sum / n];
		}

		if (atob(custom_style).startsWith('[')) custom_style = '';

		var img_src = '<img src="https://maps.googleapis.com/maps/api/staticmap?center=' + centerLatLng.toString() + '&zoom=' + zoom + markerStr + '&size=640x' + height + '&scale=2&style=' + atob(custom_style) + '&key=' + api_key + '">';
		container.find('.bt_bb_google_maps_map.bt_bb_map_map').append(img_src).on('click', function () { $(this).parent().find('.bt_bb_map_location_show').removeClass('bt_bb_map_location_show').nextOrFirst().addClass('bt_bb_map_location_show') });

		locations.eq(0).addClass('bt_bb_map_location_show');

		jQuery('#' + map_id).data('init-finished', true);

	}

	$.fn.nextOrFirst = function (selector) {
		var next = this.next(selector);
		return (next.length) ? next : this.prevAll(selector).last();
	};

	window.bt_bb_gmap_init = function (map_id, zoom, custom_style) {

		var mapElement = document.getElementById(map_id);

		var myLatLng = new google.maps.LatLng(0, 0);

		var googleApiMapId = mapElement.id;

		var mapOptions = {
			mapId: googleApiMapId,
			zoom: zoom,
			center: myLatLng,
			scrollwheel: false,
			scaleControl: true,
			zoomControl: true,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.SMALL,
				position: google.maps.ControlPosition.RIGHT_CENTER
			},
			streetViewControl: true,
			mapTypeControl: true
		}

		if (mapElement) {
			var map = new google.maps.Map(mapElement, mapOptions);

			if (custom_style != '' && atob(custom_style).startsWith('[')) {

				var style_array = [];

				if (custom_style != '') {
					style_array = JSON.parse(atob(custom_style));
				}

				var customMapType = new google.maps.StyledMapType(style_array, {
					name: 'Custom Style'
				});

				var customMapTypeId = 'custom_style';
				map.mapTypes.set(customMapTypeId, customMapType);
				map.setMapTypeId(customMapTypeId);
			} else {

			}

			var n = 0;

			var container = jQuery('#' + map_id).parent();

			var locations = container.find('.bt_bb_map_location');

			var center_map = container.data('center');
			if (center_map == 'no') {
				center_map = false;
			} else {
				center_map = true;
			}

			var lat_sum = 0;
			var lng_sum = 0;

			locations.each(function () {

				var lat = jQuery(this).data('lat');
				var lng = jQuery(this).data('lng');
				var icon = jQuery(this).data('icon');

				lat_sum += parseFloat(lat);
				lng_sum += parseFloat(lng);

				var myGlyphImg = document.createElement("img");
				myGlyphImg.src = icon;
				myGlyphImg.count = n;
				var myLatLng = new google.maps.LatLng(lat, lng);
				// console.log( myLatLng );
				// var myLatLng = { lat: -25.344, lng: 131.031 };
				// console.log( map );
				// console.log( myLatLng );
				var marker = new google.maps.marker.AdvancedMarkerElement({
					map,
					position: myLatLng,
					content: myGlyphImg,
					// count: n
				});
				marker.count = n;

				if (!center_map && n == 0) {
					map.setCenter(myLatLng);
				}

				locations.eq(0).addClass('bt_bb_map_location_show');

				marker.addListener('click', () => {
					//map.setZoom( zoom );
					//map.setCenter( marker.getPosition() );
					//console.log( marker.count );
					var reload = true;
					if (locations.eq(marker.count).hasClass('bt_bb_map_location_show') && !container.hasClass('bt_bb_map_no_overlay')) reload = false;
					container.removeClass('bt_bb_map_no_overlay');
					locations.removeClass('bt_bb_map_location_show');
					if (reload) locations.eq(marker.count).addClass('bt_bb_map_location_show');
				});

				n++;
			});

			if (center_map) {
				var centerLatLng = new google.maps.LatLng(lat_sum / n, lng_sum / n);
				map.setCenter(centerLatLng);
			}
		}

		jQuery('#' + map_id).data('init-finished', true);

	}

	// leaflet map init

	var map = null;

	window.bt_bb_leaflet_init = function (map_id, zoom, max_zoom, predefined_style, scroll_wheel, custom_style, zoom_control) {
		onImagesLoaded($('#' + map_id).parent(), function () {
			bt_bb_leaflet_init_late(map_id, zoom, max_zoom, predefined_style, scroll_wheel, custom_style, zoom_control);
		});

	}

	window.bt_bb_leaflet_init_late_all = function () {
		jQuery('.bt_bb_leaflet_map_map').not('.leaflet-container').each(function () {
			window.bt_bb_leaflet_init_late(jQuery(this).attr('id'), jQuery(this).data('zoom'), jQuery(this).data('max_zoom'), jQuery(this).data('predefined_style'), jQuery(this).data('custom_style'), jQuery(this).data('scroll_wheel'), jQuery(this).data('zoom_control'));
		});
	}

	window.bt_bb_leaflet_init_late = function (map_id, zoom, max_zoom, predefined_style, custom_style, scroll_wheel, zoom_control) {

		var lat_center = 0;
		var lng_center = 0;

		var container = jQuery('#' + map_id).parent();
		var locations = container.find('.bt_bb_leaflet_map_location');

		var center_map = container.data('center');

		if (center_map == 'no') {
			center_map = false;
		} else {
			center_map = true;
		}

		var markerClusters = L.markerClusterGroup();

		var lat_sum = 0;
		var lng_sum = 0;
		var n = 0;

		locations.each(function () {

			var lat = jQuery(this).data('lat');
			var lng = jQuery(this).data('lng');
			var icon = jQuery(this).data('icon');
			lat_sum += parseFloat(lat);
			lng_sum += parseFloat(lng);

			if (n == 0) {
				lat_center = lat;
				lng_center = lng;
			}
			locations.eq(0).addClass('bt_bb_map_location_show');
			locations.eq(0).addClass('bt_bb_map_location_show');

			var myIcon = L.icon({
				iconUrl: icon,
				iconRetinaUrl: icon,
				iconSize: [45, 58],
				iconAnchor: [22, 58],
				popupAnchor: [0, -14]
			});

			var m = L.marker([lat, lng], { icon: myIcon, id: n, lat: lat, lng: lng }).on("click", markerOnClick);
			markerClusters.addLayer(m);

			n++;
		});

		if (center_map) {
			lat_center = lat_sum / n;
			lng_center = lng_sum / n;
		}

		map = L.map(document.getElementById(map_id)).setView([lat_center, lng_center], zoom);

		var tiles_arr = [];

		if (parseInt(predefined_style) > 0) {
			tiles_arr = tiles_arr.concat(map_leaflet_source_arr[predefined_style]);
		}

		if (custom_style != '') {
			custom_style = atob(custom_style);
			var tmp_arr = custom_style.split('\n');
			$.each(tmp_arr, function (index, tmp) {

				tiles_arr = tiles_arr.concat([tmp.split(',')]);
			});
		}

		$.each(tiles_arr, function (index, tile) {
			L.tileLayer(
				tile[0],
				{
					attribution: tile[1],
					maxZoom: max_zoom,
					subdomains: ['a', 'b', 'c']
				}
			).addTo(map);
		});

		map.addLayer(markerClusters);

		if (scroll_wheel == '') {
			map.scrollWheelZoom.disable();
		}

		map.removeControl(map.zoomControl);

		if (zoom_control) {
			L.control.zoom({
				position: 'topright'
			}).addTo(map);
		}

		function markerOnClick(e) {
			var attributes = e.target.options;
			var id = attributes.id;
			var reload = true;
			if (locations.eq(id).hasClass('bt_bb_leaflet_map_location_show') && !container.hasClass('bt_bb_leaflet_map_no_overlay')) reload = false;
			container.removeClass('bt_bb_leaflet_map_no_overlay');
			locations.removeClass('bt_bb_leaflet_map_location_show');
			if (reload) locations.eq(id).addClass('bt_bb_leaflet_map_location_show');

			if (locations.eq(id).hasClass('bt_bb_map_location_show') && !container.hasClass('bt_bb_map_no_overlay')) reload = false;
			container.removeClass('bt_bb_map_no_overlay');
			locations.removeClass('bt_bb_map_location_show');
			if (reload) locations.eq(id).addClass('bt_bb_map_location_show');
		}
	}

	// do event if all images in container are loaded

	function onImagesLoaded(container, event) {
		var images = container.find('.bt_bb_leaflet_map_content img');
		var loaded = images.length;
		if (loaded > 0) {
			for (var i = 0; i < images.length; i++) {
				if (images[i].complete) {
					loaded--;
					if (loaded == 0) {
						event();
					}
				} else {
					images[i].addEventListener('load', function () {
						loaded--;
						if (loaded == 0) {
							event();
						}
					});
					setTimeout(function () { loaded--; console.log('Image loaded without proof: ' + loaded); if (loaded == 0) { event(); } }, 3000);
				}
			}
		} else {
			event();
		}
	}

	// general init

	var bt_bb_init_finished = false;

	document.addEventListener('readystatechange', function () {
		if (!bt_bb_init_finished && (document.readyState === 'interactive' || document.readyState === 'complete')) {
			bt_bb_init_elements();

			window.addEventListener("load", (event) => {

				if (!$('body').hasClass('bodyPreloader')) {
					bt_bb_animate_elements_optim();
				} else {
					setTimeout(function () {
						bt_bb_animate_elements_optim();
					}, 500);
				}

				bt_bb_lazy_load_images_optim();

				if ($('body').hasClass('logged-in') && $('.bt_bb_wrapper').length) {
					$(window).on('scroll', function () {
						bt_bb_animate_elements();
					});
				}

				$(window).on('resize', function (e) {
					setTimeout(function () {
						var $elems = $('.bt_bb_counter.animated');
						$elems.each(function () {
							var $elm = $(this);
							$elm.removeClass('animated');
							bt_bb_animate_counter($elm);
						});
					}, 1000);
				});
			});

			bt_bb_init_finished = true;

		}

	}, false);

	// magnificPopup swipe

	function mfp_swipe() {
		var touch_start = false;
		var touch_end = false;
		var one_finger = true;
		$(document).on('touchstart', '.mfp-figure figure', function (e) {
			const touches = e.changedTouches;
			if (touches.length == 1) {
				touch_start = touches[0].clientX;
			} else {
				one_finger = false;
			}
		});
		$(document).on('touchmove', '.mfp-figure figure', function (e) {
			const touches = e.changedTouches;
			if (touches.length > 1) {
				one_finger = false;
			}
		});
		$(document).on('touchend', '.mfp-figure figure', function (e) {
			const touches = e.changedTouches;
			if (touches.length == 1 && one_finger) {
				touch_end = touches[0].clientX;
				if (touch_start - touch_end < -50) {
					$('.mfp-arrow-left').click();
				} else if (touch_start - touch_end > 50) {
					$('.mfp-arrow-right').click();
				}
			}
			one_finger = true;
		});
	}
	mfp_swipe();

}(jQuery));