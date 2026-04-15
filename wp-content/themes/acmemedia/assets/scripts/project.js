'use strict';

/**
 * File comment-toggle.js
 *
 * Handles toggling of the Disqus comment section on single posts.
 */
window.wdsCommentToggler = {};
(function (window, $, app) {

	// Store comment block id globally for easy re-use.
	var commentBlockID = '';

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			toggleButton: $('.comment-toggle'),
			window: $(window)
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.toggleButton.on('click', app.toggleComments);
		//app.$c.window.on( 'transitionend', app.commentEndTransition );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.toggleButton.length;
	};

	// Toggle the comment block, and make sure the aria-expanded attribute gets updated.
	app.toggleComments = function () {

		// Get the height of the disqus block (or whatever element data-height is set to)
		// And use that to smoothly expand/collapse the comment block with CSS transitions.
		var commentHeight = app.getBlockHeight('#' + $(this).data('height'));
		var styles = {
			'height': commentHeight,
			'min-height': commentHeight
		};

		commentBlockID = $(this).data('target');

		app.$c.commentBlock = $('#' + commentBlockID);
		app.$c.commentBlock[0].addEventListener('transitionend', app.commentEndTransition);

		// DOM attributes on jQuery objects are strings.
		if ('true' === $(this).attr('aria-expanded')) {

			// Reset the height from auto to an actual value.
			app.$c.commentBlock.css(styles);

			// Then wait a bit before removing the rest for a smoother height transition.
			setTimeout(function () {

				// Remove the "style" attribute (which sets the height)
				// The CSS transition will take over and hide all the things.
				app.$c.commentBlock.removeAttr('style').removeClass('open');
			}, 50);

			$(this).attr('aria-expanded', false);
		} else {

			app.$c.commentBlock.css(styles).addClass('open');
			$(this).attr('aria-expanded', true);
		}
	};

	// Get the height of the element that's passed, or fall back to default comment block if there is no height.
	app.getBlockHeight = function (heightID) {
		var returnHeight = $(heightID).height();

		// Fall back to the default comment form if we can't get a height from the requested element.
		if (!returnHeight) {
			returnHeight = $('#comments').height();
			commentBlockID = 'comments';
		}

		// 25 is to account for the padding on the comment wrap.
		return returnHeight + 25;
	};

	// transitionend event handler, add the opened class if the target's id matches that of our comment block.
	app.commentEndTransition = function (e) {

		if (e.propertyName.includes('height')) {
			// Only add the opened class when it already has the open class (meaning it did, in fact, open and not close).
			if (app.$c.commentBlock.hasClass('open')) {
				app.$c.commentBlock.height('auto');
			}
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsCommentToggler);
'use strict';

/**
 * File fixed-header.js
 *
 * Fix the header to top and shrink on scroll.
 */
window.wdsScrollToFixed = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things, but mostly the header.
	app.cache = function () {
		app.$c = {
			window: $(window),
			body: $('body'),
			siteHeader: $('.site-header')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.window.on('scroll', app.toggleFixedHeader);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.siteHeader.length;
	};

	// Toggle the fixed version of the header.
	app.toggleFixedHeader = function () {
		var headerHeight = app.$c.siteHeader.height() / 2;

		if (app.$c.window.scrollTop() > headerHeight) {
			app.$c.body.addClass('fixed-header');
		} else {
			app.$c.body.removeClass('fixed-header');
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsScrollToFixed);
'use strict';

/**
 * File hide-tapbar.js
 *
 * Hide the tapbar when scrolling down, unhide it when scrolling up.
 */
window.wdsHideTapbar = {};
(function (window, $, app) {

	// Variable to keep track of whether we're scrolling up or down.
	var lastScrollTop = 0;

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			mobileNavWrap: $('#mobile-nav-menu'), // the nav wrapper, using this to  check if the nav is open of not.
			siteHeader: $('.site-header')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		//app.$c.window.on('scroll', app.handleScroll);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.siteHeader.length;
	};

	// (Un)hide the tapbar / header
	app.handleScroll = function () {

		// Bail early if the window isn't showing the tapbar.
		if (1024 <= app.$c.window.width()) {
			return;
		}

		// Also bail early if the menu is open and (somehow) the scroll event is triggered
		if (app.$c.mobileNavWrap.hasClass('more')) {
			return;
		}

		var scrollTop = $(this).scrollTop();

		if (scrollTop > lastScrollTop) {
			app.$c.siteHeader.addClass('tapbar-hidden');
		} else {
			app.$c.siteHeader.removeClass('tapbar-hidden');
		}

		lastScrollTop = scrollTop;
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsHideTapbar);
'use strict';

/**
 * File input-focus-fix.js
 *
 * Ensures inputs with text in them don't shrink when they lose focus.
 * For example, when focus is shifted to the submit button.
 */
window.wdsFixedHeaderInputsFix = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			inputs: $('.site-header .search-form input[type="text"]')
		};
	};

	// Combine all events.
	app.bindEvents = function () {

		// We only need to check for a value when the focus is moved away from the input.
		app.$c.inputs.on('focusout', app.fixInput);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.inputs.length;
	};

	// Set a class on the input when it has a value.
	app.fixInput = function () {
		var value = $(this).val();

		if ('' !== value) {
			$(this).addClass('not-empty');
		} else {
			$(this).removeClass('not-empty');
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsFixedHeaderInputsFix);
'use strict';

/**
 * File js-enabled.js
 *
 * If Javascript is enabled, replace the <body> class "no-js".
 */
document.body.className = document.body.className.replace('no-js', 'js');
'use strict';

/**
 * File list-toggle.js
 *
 * Toggle a list with a button.
 */
window.wdsListToggle = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			body: $('body'),
			toggleButton: $('.dropdown-toggle')
		};
	};

	// Combine all events.
	app.bindEvents = function () {

		// Toggle the list to open/close on button cick.
		app.$c.toggleButton.on('click', app.toggleList);

		// Allow the user to close the list by hitting the esc key.
		app.$c.body.on('keydown', app.escKeyClose);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.toggleButton.length;
	};

	// Toggle the ist.
	app.toggleList = function () {

		// Figure out which list we're opening and store the object.
		var $list = $($(this).siblings('.dropdown-list')),
		    location = 'bottom';

		// Figure out where if we need to change the list position..
		if ($(this).data('location')) {
			location = $(this).data('location');
		}

		if ($list.hasClass('open')) {
			app.closeList($list, true);
		} else {
			app.closeList($('.dropdown-list.open'), false);
			app.openList($list, location);
		}
	};

	// Close the open list.
	app.closeList = function ($list, moveFocus) {
		$list.removeClass('open');
		$list.siblings('.dropdown-toggle').attr('aria-expanded', false);

		if (moveFocus) {
			$list.siblings('.dropdown-toggle').focus();
		}
	};

	// Open a list.
	app.openList = function ($list, position) {

		// Add a class top when we want it to open on top, instead of below the button
		if ('top' === position) {
			$list.addClass('top');
		}

		$list.addClass('open');
		$list.siblings('.dropdown-toggle').attr('aria-expanded', true);
	};

	// Close if "esc" key is pressed.
	app.escKeyClose = function (e) {
		if (27 === e.keyCode) {
			app.closeList($('.dropdown-list.open'), true);
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsListToggle);
'use strict';

/**
 * File modal.js
 *
 * Deal with multiple modals and their media.
 */
window.wdsModal = {};

(function (window, $, app) {
	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			'body': $('body')
		};
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return $('.modal-trigger').length;
	};

	// Combine all events.
	app.bindEvents = function () {
		// Trigger a modal to open.
		app.$c.body.on('click touchstart', '.modal-trigger', app.openModal);

		// Trigger the close button to close the modal.
		app.$c.body.on('click touchstart', '.close', app.closeModal);

		// Allow the user to close the modal by hitting the esc key.
		app.$c.body.on('keydown', app.escKeyClose);

		// Allow the user to close the modal by clicking outside of the modal.
		app.$c.body.on('click touchstart', 'div.modal-open', app.closeModalByClick);
	};

	// Open the modal.
	app.openModal = function () {
		// Figure out which modal we're opening and store the object.
		var $modal = $($(this).data('target'));

		// Display the modal.
		$modal.addClass('modal-open');

		// Add body class.
		app.$c.body.addClass('modal-open');
	};

	// Close the modal.
	app.closeModal = function () {
		// Figure the opened modal we're closing and store the object.
		var $modal = $($('div.modal-open .close').data('target'));

		// Find the iframe in the $modal object.
		var $iframe = $modal.find('iframe');

		// Get the iframe src URL.
		var url = $iframe.attr('src');

		// Remove the source URL, then add it back, so the video can be played again later.
		$iframe.attr('src', '').attr('src', url);

		// Finally, hide the modal.
		$modal.removeClass('modal-open');

		// Remove the body class.
		app.$c.body.removeClass('modal-open');
	};

	// Close if "esc" key is pressed.
	app.escKeyClose = function (event) {
		if (27 === event.keyCode) {
			app.closeModal();
		}
	};

	// Close if the user clicks outside of the modal
	app.closeModalByClick = function (event) {
		// If the parent container is NOT the modal dialog container, close the modal
		if (!$(event.target).parents('div').hasClass('modal-dialog')) {
			app.closeModal();
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsModal);
'use strict';

/**
 * File scale-embeds.js
 *
 * Automatically re-scale iframe embeds.
 */
window.wdsScaleEmbeds = {};
(function (window, $, app) {

	var resizeTimer = 0;

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			iframes: $('.site-main .entry-content iframe')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.window.on('load', app.checkIframes);
		app.$c.window.on('resize', app.rescaleIframes);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.iframes.length;
	};

	// Check iframe size.
	// Only do something when the iFrame's width attribute is set wider than it actually is rendering.
	app.checkIframes = function () {

		app.$c.iframes.each(function () {
			var width = $(this).attr('width'),
			    outerWidth = $(this).outerWidth();

			if (width > outerWidth) {
				app.scaleFrame($(this));
			}
		});
	};

	// Re-run the size check when the window resizes.
	// Using a timer to ensure it only fires after we can reasonably conclude the resizing is done.
	app.rescaleIframes = function () {

		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(app.checkIframes, 500);
	};

	// Scale the iFrame width.
	app.scaleFrame = function ($iframe) {

		// Get the height/width of the current iframe, calculate what the new height should be.
		var width = $iframe.attr('width'),
		    height = $iframe.attr('height'),
		    scale = height / width,
		    newHeight = $iframe.outerWidth() * scale;

		$iframe.outerHeight(newHeight);
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsScaleEmbeds);
'use strict';

/**
 * File scroll-indicator.js
 *
 * Checks the hidden mobile nav for height and displays the scroll indicator if it's larger than the screen.
 */
window.mobileMenuScrollIndicator = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();
		app.bindEvents();
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window)
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.window.on('load', app.scaffolding);
	};

	// We're relying on elements that are added after document.ready,
	// which means we'll need to initiate cache / events later.
	app.scaffolding = function () {

		// Cache hidden menu element.
		app.$c.menuWrap = $('.mobile-nav-menu-hidden');

		// Bind our events.
		app.$c.menuWrap.on('scroll', app.checkScrollPosition);
		app.$c.window.on('resize', app.checkForScroll);

		// Initiate, part 2!
		app.checkForScroll();
	};

	// Check if the element can be scrolled.
	app.checkForScroll = function () {

		// Remove the scroll class, in case it's still there from before resizing.
		app.removeScrollClass();

		// If the height is larger than the window height, unhide the icon.
		if (app.$c.menuWrap[0].scrollHeight - 70 > app.$c.window.height() - 70) {
			app.addScrollClass();
		}
	};

	app.checkScrollPosition = function () {

		var scrollPosition = $(this).scrollTop() + app.$c.menuWrap.height();

		// If the scrollPosition and srollHeight are equal, we've reached the bottom.
		if (scrollPosition === app.$c.menuWrap[0].scrollHeight) {
			app.removeScrollClass();
		} else {

			// If not, and it doesn't have the class - we'll need to re-add it.
			// For example, when we've reached the bottom and then scroll back up.
			if (!$(this).hasClass('scroll')) {
				app.addScrollClass();
			}
		}
	};

	// Add scroll class.
	app.addScrollClass = function () {
		app.$c.menuWrap.addClass('scroll');
	};

	// Remove scroll class.
	app.removeScrollClass = function () {
		app.$c.menuWrap.removeClass('scroll');
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.mobileMenuScrollIndicator);
'use strict';

/**
 * File skip-link-focus-fix.js.
 *
 * Helps with accessibility for keyboard only users.
 *
 * Learn more: https://git.io/vWdr2
 */
(function () {
	var isWebkit = navigator.userAgent.toLowerCase().indexOf('webkit') > -1,
	    isOpera = navigator.userAgent.toLowerCase().indexOf('opera') > -1,
	    isIe = navigator.userAgent.toLowerCase().indexOf('msie') > -1;

	if ((isWebkit || isOpera || isIe) && document.getElementById && window.addEventListener) {
		window.addEventListener('hashchange', function () {
			var id = location.hash.substring(1),
			    element;

			if (!/^[A-z0-9_-]+$/.test(id)) {
				return;
			}

			element = document.getElementById(id);

			if (element) {
				if (!/^(?:a|select|input|button|textarea)$/i.test(element.tagName)) {
					element.tabIndex = -1;
				}

				element.focus();
			}
		}, false);
	}
})();
'use strict';

/**
 * File smooth-scroll.js
 *
 * Smooth scroll functionality, specifically for #main-content type links (post pagination).
 */
window.smoothScroll = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();
		app.bindEvents();
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			document: $(document),
			page: $('html, body'),
			content: $('.site-main')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.document.on('ready', app.handleScroll);
	};

	// Scroll to content
	app.handleScroll = function () {

		// Bail if the hash is not what we're looking for.
		if ('#main-content' !== window.location.hash) {
			return;
		}

		// Use animate to scroll down to the content portion of the page more smoothly.
		app.$c.page.animate({
			scrollTop: app.$c.content.offset().top + 'px'
		}, 1000, 'swing');
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.smoothScroll);
'use strict';

/**
 * File submenu-toggle.js
 *
 * Allow submenus to be toggled by keyboard, without breaking mouse hover.
 */
window.subMenuToggler = {};
(function (window, $, app) {

	// Constructor.
	app.init = function () {
		app.cache();

		if (app.meetsRequirements()) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function () {
		app.$c = {
			window: $(window),
			parentMenuItems: $('nav:not(.mobile-nav-menu) .menu-item-has-children')
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.parentMenuItems.on('click keydown', 'a', app.handleToggle);
		app.$c.parentMenuItems.on('mouseleave', app.classToggleLeave);
		app.$c.parentMenuItems.on('mouseenter', app.classToggleEnter);
	};

	// Do we meet the requirements?
	app.meetsRequirements = function () {
		return app.$c.parentMenuItems.length;
	};

	// Toggling logic.
	// - Toggle immediately if the href is simply #
	// - Allow the second interaction to load the link
	app.handleToggle = function (e) {

		// Bail if there is no submenu.
		if (!$(this).parent().hasClass('menu-item-has-children')) {
			return;
		}

		// Bail if it is neither space or enter.
		if (32 !== e.keyCode && 13 !== e.keyCode && 'keydown' === e.type) {
			return;
		}

		var link = $(this).attr('href'),
		    hasClass = $(this).hasClass('open-link');

		// If the link is just a hash, we can safely toggle the menu.
		// Do the same thing if it does not have the class open-link.
		if ('#' === link || !hasClass) {
			$(this).parent().toggleClass('focus');

			// Make sure to mark legit links with a class to allow them to work on a second activation.
			if ('#' !== link) {
				$(this).addClass('open-link');
			}

			return false;
		}
	};

	// Removes the focus class. Triggered on mouseleave, in case people mouse-click on links and then move on.
	app.classToggleLeave = function () {

		if ($(this).hasClass('focus')) {
			$(this).removeClass('focus');
		}

		if ($(this).children('a').hasClass('open-link')) {
			$(this).children('a').removeClass('open-link');
		}
	};

	// Add the open-link class when the mouse enters the link - this way, legit links will just work
	// while the hover takes care of showing the submenu.
	app.classToggleEnter = function () {

		var $link = $(this).children('a');

		if ('#' !== $($link[0]).attr('href')) {
			$($link[0]).addClass('open-link');
		}
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.subMenuToggler);
'use strict';

/**
 * File window-ready.js
 *
 * Add a "ready" class to <body> when window is ready.
 */
window.wdsWindowReady = {};
(function (window, $, app) {
	// Constructor.
	app.init = function () {
		app.cache();
		app.bindEvents();
	};

	// Cache document elements.
	app.cache = function () {
		app.$c = {
			'window': $(window),
			'body': $(document.body)
		};
	};

	// Combine all events.
	app.bindEvents = function () {
		app.$c.window.load(app.addBodyClass);
	};

	// Add a class to <body>.
	app.addBodyClass = function () {
		app.$c.body.addClass('ready');
	};

	// Engage!
	$(app.init);
})(window, jQuery, window.wdsWindowReady);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1lbnQtdG9nZ2xlLmpzIiwiZml4ZWQtaGVhZGVyLmpzIiwiaGlkZS10YXBiYXIuanMiLCJpbnB1dC1mb2N1cy1maXguanMiLCJqcy1lbmFibGVkLmpzIiwibGlzdC10b2dnbGUuanMiLCJtb2RhbC5qcyIsInNjYWxlLWVtYmVkcy5qcyIsInNjcm9sbC1pbmRpY2F0b3IuanMiLCJza2lwLWxpbmstZm9jdXMtZml4LmpzIiwic21vb3RoLXNjcm9sbC5qcyIsInN1Ym1lbnUtdG9nZ2xlLmpzIiwid2luZG93LXJlYWR5LmpzIl0sIm5hbWVzIjpbIndpbmRvdyIsIndkc0NvbW1lbnRUb2dnbGVyIiwiJCIsImFwcCIsImNvbW1lbnRCbG9ja0lEIiwiaW5pdCIsImNhY2hlIiwibWVldHNSZXF1aXJlbWVudHMiLCJiaW5kRXZlbnRzIiwiJGMiLCJ0b2dnbGVCdXR0b24iLCJvbiIsInRvZ2dsZUNvbW1lbnRzIiwibGVuZ3RoIiwiY29tbWVudEhlaWdodCIsImdldEJsb2NrSGVpZ2h0IiwiZGF0YSIsInN0eWxlcyIsImNvbW1lbnRCbG9jayIsImFkZEV2ZW50TGlzdGVuZXIiLCJjb21tZW50RW5kVHJhbnNpdGlvbiIsImF0dHIiLCJjc3MiLCJzZXRUaW1lb3V0IiwicmVtb3ZlQXR0ciIsInJlbW92ZUNsYXNzIiwiYWRkQ2xhc3MiLCJoZWlnaHRJRCIsInJldHVybkhlaWdodCIsImhlaWdodCIsImUiLCJwcm9wZXJ0eU5hbWUiLCJpbmNsdWRlcyIsImhhc0NsYXNzIiwialF1ZXJ5Iiwid2RzU2Nyb2xsVG9GaXhlZCIsImJvZHkiLCJzaXRlSGVhZGVyIiwidG9nZ2xlRml4ZWRIZWFkZXIiLCJoZWFkZXJIZWlnaHQiLCJzY3JvbGxUb3AiLCJ3ZHNIaWRlVGFwYmFyIiwibGFzdFNjcm9sbFRvcCIsIm1vYmlsZU5hdldyYXAiLCJoYW5kbGVTY3JvbGwiLCJ3aWR0aCIsIndkc0ZpeGVkSGVhZGVySW5wdXRzRml4IiwiaW5wdXRzIiwiZml4SW5wdXQiLCJ2YWx1ZSIsInZhbCIsImRvY3VtZW50IiwiY2xhc3NOYW1lIiwicmVwbGFjZSIsIndkc0xpc3RUb2dnbGUiLCJ0b2dnbGVMaXN0IiwiZXNjS2V5Q2xvc2UiLCIkbGlzdCIsInNpYmxpbmdzIiwibG9jYXRpb24iLCJjbG9zZUxpc3QiLCJvcGVuTGlzdCIsIm1vdmVGb2N1cyIsImZvY3VzIiwicG9zaXRpb24iLCJrZXlDb2RlIiwid2RzTW9kYWwiLCJvcGVuTW9kYWwiLCJjbG9zZU1vZGFsIiwiY2xvc2VNb2RhbEJ5Q2xpY2siLCIkbW9kYWwiLCIkaWZyYW1lIiwiZmluZCIsInVybCIsImV2ZW50IiwidGFyZ2V0IiwicGFyZW50cyIsIndkc1NjYWxlRW1iZWRzIiwicmVzaXplVGltZXIiLCJpZnJhbWVzIiwiY2hlY2tJZnJhbWVzIiwicmVzY2FsZUlmcmFtZXMiLCJlYWNoIiwib3V0ZXJXaWR0aCIsInNjYWxlRnJhbWUiLCJjbGVhclRpbWVvdXQiLCJzY2FsZSIsIm5ld0hlaWdodCIsIm91dGVySGVpZ2h0IiwibW9iaWxlTWVudVNjcm9sbEluZGljYXRvciIsInNjYWZmb2xkaW5nIiwibWVudVdyYXAiLCJjaGVja1Njcm9sbFBvc2l0aW9uIiwiY2hlY2tGb3JTY3JvbGwiLCJyZW1vdmVTY3JvbGxDbGFzcyIsInNjcm9sbEhlaWdodCIsImFkZFNjcm9sbENsYXNzIiwic2Nyb2xsUG9zaXRpb24iLCJpc1dlYmtpdCIsIm5hdmlnYXRvciIsInVzZXJBZ2VudCIsInRvTG93ZXJDYXNlIiwiaW5kZXhPZiIsImlzT3BlcmEiLCJpc0llIiwiZ2V0RWxlbWVudEJ5SWQiLCJpZCIsImhhc2giLCJzdWJzdHJpbmciLCJlbGVtZW50IiwidGVzdCIsInRhZ05hbWUiLCJ0YWJJbmRleCIsInNtb290aFNjcm9sbCIsInBhZ2UiLCJjb250ZW50IiwiYW5pbWF0ZSIsIm9mZnNldCIsInRvcCIsInN1Yk1lbnVUb2dnbGVyIiwicGFyZW50TWVudUl0ZW1zIiwiaGFuZGxlVG9nZ2xlIiwiY2xhc3NUb2dnbGVMZWF2ZSIsImNsYXNzVG9nZ2xlRW50ZXIiLCJwYXJlbnQiLCJ0eXBlIiwibGluayIsInRvZ2dsZUNsYXNzIiwiY2hpbGRyZW4iLCIkbGluayIsIndkc1dpbmRvd1JlYWR5IiwibG9hZCIsImFkZEJvZHlDbGFzcyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7QUFLQUEsT0FBT0MsaUJBQVAsR0FBMkIsRUFBM0I7QUFDQSxDQUFFLFVBQVVELE1BQVYsRUFBa0JFLENBQWxCLEVBQXFCQyxHQUFyQixFQUEyQjs7QUFFNUI7QUFDQSxLQUFJQyxpQkFBaUIsRUFBckI7O0FBRUE7QUFDQUQsS0FBSUUsSUFBSixHQUFXLFlBQVc7QUFDckJGLE1BQUlHLEtBQUo7O0FBRUEsTUFBS0gsSUFBSUksaUJBQUosRUFBTCxFQUErQjtBQUM5QkosT0FBSUssVUFBSjtBQUNBO0FBQ0QsRUFORDs7QUFRQTtBQUNBTCxLQUFJRyxLQUFKLEdBQVksWUFBVztBQUN0QkgsTUFBSU0sRUFBSixHQUFTO0FBQ1JDLGlCQUFjUixFQUFHLGlCQUFILENBRE47QUFFUkYsV0FBUUUsRUFBR0YsTUFBSDtBQUZBLEdBQVQ7QUFJQSxFQUxEOztBQU9BO0FBQ0FHLEtBQUlLLFVBQUosR0FBaUIsWUFBVztBQUMzQkwsTUFBSU0sRUFBSixDQUFPQyxZQUFQLENBQW9CQyxFQUFwQixDQUF3QixPQUF4QixFQUFpQ1IsSUFBSVMsY0FBckM7QUFDQTtBQUNBLEVBSEQ7O0FBS0E7QUFDQVQsS0FBSUksaUJBQUosR0FBd0IsWUFBVztBQUNsQyxTQUFPSixJQUFJTSxFQUFKLENBQU9DLFlBQVAsQ0FBb0JHLE1BQTNCO0FBQ0EsRUFGRDs7QUFJQTtBQUNBVixLQUFJUyxjQUFKLEdBQXFCLFlBQVc7O0FBRS9CO0FBQ0E7QUFDQSxNQUFNRSxnQkFBZ0JYLElBQUlZLGNBQUosQ0FBb0IsTUFBTWIsRUFBRyxJQUFILEVBQVVjLElBQVYsQ0FBZ0IsUUFBaEIsQ0FBMUIsQ0FBdEI7QUFDQSxNQUFNQyxTQUFTO0FBQ2QsYUFBVUgsYUFESTtBQUVkLGlCQUFjQTtBQUZBLEdBQWY7O0FBS0FWLG1CQUFpQkYs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ICogRmlsZSBjb21tZW50LXRvZ2dsZS5qc1xuICpcbiAqIEhhbmRsZXMgdG9nZ2xpbmcgb2YgdGhlIERpc3F1cyBjb21tZW50IHNlY3Rpb24gb24gc2luZ2xlIHBvc3RzLlxuICovXG53aW5kb3cud2RzQ29tbWVudFRvZ2dsZXIgPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIFN0b3JlIGNvbW1lbnQgYmxvY2sgaWQgZ2xvYmFsbHkgZm9yIGVhc3kgcmUtdXNlLlxuXHR2YXIgY29tbWVudEJsb2NrSUQgPSAnJztcblxuXHQvLyBDb25zdHJ1Y3Rvci5cblx0YXBwLmluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuY2FjaGUoKTtcblxuXHRcdGlmICggYXBwLm1lZXRzUmVxdWlyZW1lbnRzKCkgKSB7XG5cdFx0XHRhcHAuYmluZEV2ZW50cygpO1xuXHRcdH1cblx0fTtcblxuXHQvLyBDYWNoZSBhbGwgdGhlIHRoaW5ncy5cblx0YXBwLmNhY2hlID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjID0ge1xuXHRcdFx0dG9nZ2xlQnV0dG9uOiAkKCAnLmNvbW1lbnQtdG9nZ2xlJyApLFxuXHRcdFx0d2luZG93OiAkKCB3aW5kb3cgKVxuXHRcdH07XG5cdH07XG5cblx0Ly8gQ29tYmluZSBhbGwgZXZlbnRzLlxuXHRhcHAuYmluZEV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYy50b2dnbGVCdXR0b24ub24oICdjbGljaycsIGFwcC50b2dnbGVDb21tZW50cyApO1xuXHRcdC8vYXBwLiRjLndpbmRvdy5vbiggJ3RyYW5zaXRpb25lbmQnLCBhcHAuY29tbWVudEVuZFRyYW5zaXRpb24gKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMudG9nZ2xlQnV0dG9uLmxlbmd0aDtcblx0fTtcblxuXHQvLyBUb2dnbGUgdGhlIGNvbW1lbnQgYmxvY2ssIGFuZCBtYWtlIHN1cmUgdGhlIGFyaWEtZXhwYW5kZWQgYXR0cmlidXRlIGdldHMgdXBkYXRlZC5cblx0YXBwLnRvZ2dsZUNvbW1lbnRzID0gZnVuY3Rpb24oKSB7XG5cblx0XHQvLyBHZXQgdGhlIGhlaWdodCBvZiB0aGUgZGlzcXVzIGJsb2NrIChvciB3aGF0ZXZlciBlbGVtZW50IGRhdGEtaGVpZ2h0IGlzIHNldCB0bylcblx0XHQvLyBBbmQgdXNlIHRoYXQgdG8gc21vb3RobHkgZXhwYW5kL2NvbGxhcHNlIHRoZSBjb21tZW50IGJsb2NrIHdpdGggQ1NTIHRyYW5zaXRpb25zLlxuXHRcdGNvbnN0IGNvbW1lbnRIZWlnaHQgPSBhcHAuZ2V0QmxvY2tIZWlnaHQoICcjJyArICQoIHRoaXMgKS5kYXRhKCAnaGVpZ2h0JyApICk7XG5cdFx0Y29uc3Qgc3R5bGVzID0ge1xuXHRcdFx0J2hlaWdodCc6IGNvbW1lbnRIZWlnaHQsXG5cdFx0XHQnbWluLWhlaWdodCc6IGNvbW1lbnRIZWlnaHRcblx0XHR9XG5cblx0XHRjb21tZW50QmxvY2tJRCA9ICQoIHRoaXMgKS5kYXRhKCAndGFyZ2V0JyApO1xuXG5cdFx0YXBwLiRjLmNvbW1lbnRCbG9jayA9ICQoICcjJyArIGNvbW1lbnRCbG9ja0lEICk7XG5cdFx0YXBwLiRjLmNvbW1lbnRCbG9ja1swXS5hZGRFdmVudExpc3RlbmVyKCAndHJhbnNpdGlvbmVuZCcsIGFwcC5jb21tZW50RW5kVHJhbnNpdGlvbiApO1xuXG5cdFx0Ly8gRE9NIGF0dHJpYnV0ZXMgb24galF1ZXJ5IG9iamVjdHMgYXJlIHN0cmluZ3MuXG5cdFx0aWYgKCAndHJ1ZScgPT09ICQoIHRoaXMgKS5hdHRyKCAnYXJpYS1leHBhbmRlZCcgKSApIHtcblxuXHRcdFx0Ly8gUmVzZXQgdGhlIGhlaWdodCBmcm9tIGF1dG8gdG8gYW4gYWN0dWFsIHZhbHVlLlxuXHRcdFx0YXBwLiRjLmNvbW1lbnRCbG9jay5jc3MoIHN0eWxlcyApO1xuXG5cdFx0XHQvLyBUaGVuIHdhaXQgYSBiaXQgYmVmb3JlIHJlbW92aW5nIHRoZSByZXN0IGZvciBhIHNtb290aGVyIGhlaWdodCB0cmFuc2l0aW9uLlxuXHRcdFx0c2V0VGltZW91dCggZnVuY3Rpb24oKSB7XG5cblx0XHRcdFx0Ly8gUmVtb3ZlIHRoZSBcInN0eWxlXCIgYXR0cmlidXRlICh3aGljaCBzZXRzIHRoZSBoZWlnaHQpXG5cdFx0XHRcdC8vIFRoZSBDU1MgdHJhbnNpdGlvbiB3aWxsIHRha2Ugb3ZlciBhbmQgaGlkZSBhbGwgdGhlIHRoaW5ncy5cblx0XHRcdFx0YXBwLiRjLmNvbW1lbnRCbG9jay5yZW1vdmVBdHRyKCAnc3R5bGUnICkucmVtb3ZlQ2xhc3MoICdvcGVuJyApO1xuXG5cdFx0XHR9LCA1MCApO1xuXG5cdFx0XHQkKCB0aGlzICkuYXR0ciggJ2FyaWEtZXhwYW5kZWQnLCBmYWxzZSApO1xuXG5cdFx0fSBlbHNlIHtcblxuXHRcdFx0YXBwLiRjLmNvbW1lbnRCbG9jay5jc3MoIHN0eWxlcyApLmFkZENsYXNzKCAnb3BlbicgKTtcblx0XHRcdCQoIHRoaXMgKS5hdHRyKCAnYXJpYS1leHBhbmRlZCcsIHRydWUgKTtcblxuXHRcdH1cblx0fTtcblxuXHQvLyBHZXQgdGhlIGhlaWdodCBvZiB0aGUgZWxlbWVudCB0aGF0J3MgcGFzc2VkLCBvciBmYWxsIGJhY2sgdG8gZGVmYXVsdCBjb21tZW50IGJsb2NrIGlmIHRoZXJlIGlzIG5vIGhlaWdodC5cblx0YXBwLmdldEJsb2NrSGVpZ2h0ID0gZnVuY3Rpb24oIGhlaWdodElEICkge1xuXHRcdHZhciByZXR1cm5IZWlnaHQgPSAkKCBoZWlnaHRJRCApLmhlaWdodCgpO1xuXG5cdFx0Ly8gRmFsbCBiYWNrIHRvIHRoZSBkZWZhdWx0IGNvbW1lbnQgZm9ybSBpZiB3ZSBjYW4ndCBnZXQgYSBoZWlnaHQgZnJvbSB0aGUgcmVxdWVzdGVkIGVsZW1lbnQuXG5cdFx0aWYgKCAhIHJldHVybkhlaWdodCApIHtcblx0XHRcdHJldHVybkhlaWdodCA9ICQoICcjY29tbWVudHMnICkuaGVpZ2h0KCk7XG5cdFx0XHRjb21tZW50QmxvY2tJRCA9ICdjb21tZW50cyc7XG5cdFx0fVxuXG5cdFx0Ly8gMjUgaXMgdG8gYWNjb3VudCBmb3IgdGhlIHBhZGRpbmcgb24gdGhlIGNvbW1lbnQgd3JhcC5cblx0XHRyZXR1cm4gcmV0dXJuSGVpZ2h0ICsgMjU7XG5cdH07XG5cblx0Ly8gdHJhbnNpdGlvbmVuZCBldmVudCBoYW5kbGVyLCBhZGQgdGhlIG9wZW5lZCBjbGFzcyBpZiB0aGUgdGFyZ2V0J3MgaWQgbWF0Y2hlcyB0aGF0IG9mIG91ciBjb21tZW50IGJsb2NrLlxuXHRhcHAuY29tbWVudEVuZFRyYW5zaXRpb24gPSBmdW5jdGlvbiggZSApIHtcblxuXHRcdGlmICggZS5wcm9wZXJ0eU5hbWUuaW5jbHVkZXMoICdoZWlnaHQnICkgKSB7XG5cdFx0XHQvLyBPbmx5IGFkZCB0aGUgb3BlbmVkIGNsYXNzIHdoZW4gaXQgYWxyZWFkeSBoYXMgdGhlIG9wZW4gY2xhc3MgKG1lYW5pbmcgaXQgZGlkLCBpbiBmYWN0LCBvcGVuIGFuZCBub3QgY2xvc2UpLlxuXHRcdFx0aWYgKCBhcHAuJGMuY29tbWVudEJsb2NrLmhhc0NsYXNzKCAnb3BlbicgKSApIHtcblx0XHRcdFx0YXBwLiRjLmNvbW1lbnRCbG9jay5oZWlnaHQoICdhdXRvJyApO1xuXHRcdFx0fVxuXHRcdH1cblxuXHR9O1xuXG5cdC8vIEVuZ2FnZSFcblx0JCggYXBwLmluaXQgKTtcblxufSkoIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cud2RzQ29tbWVudFRvZ2dsZXIgKTtcbiIsIi8qKlxuICogRmlsZSBmaXhlZC1oZWFkZXIuanNcbiAqXG4gKiBGaXggdGhlIGhlYWRlciB0byB0b3AgYW5kIHNocmluayBvbiBzY3JvbGwuXG4gKi9cbndpbmRvdy53ZHNTY3JvbGxUb0ZpeGVkID0ge307XG4oIGZ1bmN0aW9uKCB3aW5kb3csICQsIGFwcCApIHtcblxuXHQvLyBDb25zdHJ1Y3Rvci5cblx0YXBwLmluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuY2FjaGUoKTtcblxuXHRcdGlmICggYXBwLm1lZXRzUmVxdWlyZW1lbnRzKCkgKSB7XG5cdFx0XHRhcHAuYmluZEV2ZW50cygpO1xuXHRcdH1cblx0fTtcblxuXHQvLyBDYWNoZSBhbGwgdGhlIHRoaW5ncywgYnV0IG1vc3RseSB0aGUgaGVhZGVyLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHR3aW5kb3c6ICQoIHdpbmRvdyApLFxuXHRcdFx0Ym9keTogJCggJ2JvZHknICksXG5cdFx0XHRzaXRlSGVhZGVyOiAkKCAnLnNpdGUtaGVhZGVyJyApXG5cdFx0fTtcblx0fTtcblxuXHQvLyBDb21iaW5lIGFsbCBldmVudHMuXG5cdGFwcC5iaW5kRXZlbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjLndpbmRvdy5vbiggJ3Njcm9sbCcsIGFwcC50b2dnbGVGaXhlZEhlYWRlciApO1xuXHR9O1xuXG5cdC8vIERvIHdlIG1lZXQgdGhlIHJlcXVpcmVtZW50cz9cblx0YXBwLm1lZXRzUmVxdWlyZW1lbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0cmV0dXJuIGFwcC4kYy5zaXRlSGVhZGVyLmxlbmd0aDtcblx0fTtcblxuXHQvLyBUb2dnbGUgdGhlIGZpeGVkIHZlcnNpb24gb2YgdGhlIGhlYWRlci5cblx0YXBwLnRvZ2dsZUZpeGVkSGVhZGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0dmFyIGhlYWRlckhlaWdodCA9IGFwcC4kYy5zaXRlSGVhZGVyLmhlaWdodCgpIC8gMjtcblxuXHRcdGlmICggYXBwLiRjLndpbmRvdy5zY3JvbGxUb3AoKSA+IGhlYWRlckhlaWdodCApIHtcblx0XHRcdGFwcC4kYy5ib2R5LmFkZENsYXNzKCAnZml4ZWQtaGVhZGVyJyApO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHRhcHAuJGMuYm9keS5yZW1vdmVDbGFzcyggJ2ZpeGVkLWhlYWRlcicgKTtcblx0XHR9XG5cdH07XG5cblx0Ly8gRW5nYWdlIVxuXHQkKCBhcHAuaW5pdCApO1xuXG59KSggd2luZG93LCBqUXVlcnksIHdpbmRvdy53ZHNTY3JvbGxUb0ZpeGVkICk7XG4iLCIvKipcbiAqIEZpbGUgaGlkZS10YXBiYXIuanNcbiAqXG4gKiBIaWRlIHRoZSB0YXBiYXIgd2hlbiBzY3JvbGxpbmcgZG93biwgdW5oaWRlIGl0IHdoZW4gc2Nyb2xsaW5nIHVwLlxuICovXG53aW5kb3cud2RzSGlkZVRhcGJhciA9IHt9O1xuKCBmdW5jdGlvbiggd2luZG93LCAkLCBhcHAgKSB7XG5cblx0Ly8gVmFyaWFibGUgdG8ga2VlcCB0cmFjayBvZiB3aGV0aGVyIHdlJ3JlIHNjcm9sbGluZyB1cCBvciBkb3duLlxuXHRsZXQgbGFzdFNjcm9sbFRvcCA9IDA7XG5cblx0Ly8gQ29uc3RydWN0b3IuXG5cdGFwcC5pbml0ID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLmNhY2hlKCk7XG5cblx0XHRpZiAoIGFwcC5tZWV0c1JlcXVpcmVtZW50cygpICkge1xuXHRcdFx0YXBwLmJpbmRFdmVudHMoKTtcblx0XHR9XG5cdH07XG5cblx0Ly8gQ2FjaGUgYWxsIHRoZSB0aGluZ3MuXG5cdGFwcC5jYWNoZSA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYyA9IHtcblx0XHRcdHdpbmRvdzogJCggd2luZG93ICksXG5cdFx0XHRtb2JpbGVOYXZXcmFwOiAkKCAnI21vYmlsZS1uYXYtbWVudScgKSwgLy8gdGhlIG5hdiB3cmFwcGVyLCB1c2luZyB0aGlzIHRvICBjaGVjayBpZiB0aGUgbmF2IGlzIG9wZW4gb2Ygbm90LlxuXHRcdFx0c2l0ZUhlYWRlcjogJCggJy5zaXRlLWhlYWRlcicgKVxuXHRcdH07XG5cdH07XG5cblx0Ly8gQ29tYmluZSBhbGwgZXZlbnRzLlxuXHRhcHAuYmluZEV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYy53aW5kb3cub24oICdzY3JvbGwnLCBhcHAuaGFuZGxlU2Nyb2xsICk7XG5cdH07XG5cblx0Ly8gRG8gd2UgbWVldCB0aGUgcmVxdWlyZW1lbnRzP1xuXHRhcHAubWVldHNSZXF1aXJlbWVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRyZXR1cm4gYXBwLiRjLnNpdGVIZWFkZXIubGVuZ3RoO1xuXHR9O1xuXG5cdC8vIChVbiloaWRlIHRoZSB0YXBiYXIgLyBoZWFkZXJcblx0YXBwLmhhbmRsZVNjcm9sbCA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0Ly8gQmFpbCBlYXJseSBpZiB0aGUgd2luZG93IGlzbid0IHNob3dpbmcgdGhlIHRhcGJhci5cblx0XHRpZiAoIDEwMjQgPD0gYXBwLiRjLndpbmRvdy53aWR0aCgpICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdC8vIEFsc28gYmFpbCBlYXJseSBpZiB0aGUgbWVudSBpcyBvcGVuIGFuZCAoc29tZWhvdykgdGhlIHNjcm9sbCBldmVudCBpcyB0cmlnZ2VyZWRcblx0XHRpZiAoIGFwcC4kYy5tb2JpbGVOYXZXcmFwLmhhc0NsYXNzKCAnbW9yZScgKSApIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cblx0XHRjb25zdCBzY3JvbGxUb3AgPSAkKCB0aGlzICkuc2Nyb2xsVG9wKCk7XG5cblx0XHRpZiAoIHNjcm9sbFRvcCA+IGxhc3RTY3JvbGxUb3AgKSB7XG5cdFx0XHRhcHAuJGMuc2l0ZUhlYWRlci5hZGRDbGFzcyggJ3RhcGJhci1oaWRkZW4nICk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGFwcC4kYy5zaXRlSGVhZGVyLnJlbW92ZUNsYXNzKCAndGFwYmFyLWhpZGRlbicgKTtcblx0XHR9XG5cblx0XHRsYXN0U2Nyb2xsVG9wID0gc2Nyb2xsVG9wO1xuXG5cdH07XG5cblx0Ly8gRW5nYWdlIVxuXHQkKCBhcHAuaW5pdCApO1xuXG59KSggd2luZG93LCBqUXVlcnksIHdpbmRvdy53ZHNIaWRlVGFwYmFyICk7XG4iLCIvKipcbiAqIEZpbGUgaW5wdXQtZm9jdXMtZml4LmpzXG4gKlxuICogRW5zdXJlcyBpbnB1dHMgd2l0aCB0ZXh0IGluIHRoZW0gZG9uJ3Qgc2hyaW5rIHdoZW4gdGhleSBsb3NlIGZvY3VzLlxuICogRm9yIGV4YW1wbGUsIHdoZW4gZm9jdXMgaXMgc2hpZnRlZCB0byB0aGUgc3VibWl0IGJ1dHRvbi5cbiAqL1xud2luZG93Lndkc0ZpeGVkSGVhZGVySW5wdXRzRml4ID0ge307XG4oIGZ1bmN0aW9uKCB3aW5kb3csICQsIGFwcCApIHtcblxuXHQvLyBDb25zdHJ1Y3Rvci5cblx0YXBwLmluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuY2FjaGUoKTtcblxuXHRcdGlmICggYXBwLm1lZXRzUmVxdWlyZW1lbnRzKCkgKSB7XG5cdFx0XHRhcHAuYmluZEV2ZW50cygpO1xuXHRcdH1cblx0fTtcblxuXHQvLyBDYWNoZSBhbGwgdGhlIHRoaW5ncy5cblx0YXBwLmNhY2hlID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjID0ge1xuXHRcdFx0d2luZG93OiAkKCB3aW5kb3cgKSxcblx0XHRcdGlucHV0czogJCggJy5zaXRlLWhlYWRlciAuc2VhcmNoLWZvcm0gaW5wdXRbdHlwZT1cInRleHRcIl0nIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblxuXHRcdC8vIFdlIG9ubHkgbmVlZCB0byBjaGVjayBmb3IgYSB2YWx1ZSB3aGVuIHRoZSBmb2N1cyBpcyBtb3ZlZCBhd2F5IGZyb20gdGhlIGlucHV0LlxuXHRcdGFwcC4kYy5pbnB1dHMub24oICdmb2N1c291dCcsIGFwcC5maXhJbnB1dCApO1xuXHR9O1xuXG5cdC8vIERvIHdlIG1lZXQgdGhlIHJlcXVpcmVtZW50cz9cblx0YXBwLm1lZXRzUmVxdWlyZW1lbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0cmV0dXJuIGFwcC4kYy5pbnB1dHMubGVuZ3RoO1xuXHR9O1xuXG5cdC8vIFNldCBhIGNsYXNzIG9uIHRoZSBpbnB1dCB3aGVuIGl0IGhhcyBhIHZhbHVlLlxuXHRhcHAuZml4SW5wdXQgPSBmdW5jdGlvbigpIHtcblx0XHR2YXIgdmFsdWUgPSAkKCB0aGlzICkudmFsKCk7XG5cblx0XHRpZiAoICcnICE9PSB2YWx1ZSApIHtcblx0XHRcdCQoIHRoaXMgKS5hZGRDbGFzcyggJ25vdC1lbXB0eScgKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0JCggdGhpcyApLnJlbW92ZUNsYXNzKCAnbm90LWVtcHR5JyApO1xuXHRcdH1cblx0fTtcblxuXHQvLyBFbmdhZ2UhXG5cdCQoIGFwcC5pbml0ICk7XG5cbn0pKCB3aW5kb3csIGpRdWVyeSwgd2luZG93Lndkc0ZpeGVkSGVhZGVySW5wdXRzRml4ICk7XG4iLCIvKipcbiAqIEZpbGUganMtZW5hYmxlZC5qc1xuICpcbiAqIElmIEphdmFzY3JpcHQgaXMgZW5hYmxlZCwgcmVwbGFjZSB0aGUgPGJvZHk+IGNsYXNzIFwibm8tanNcIi5cbiAqL1xuZG9jdW1lbnQuYm9keS5jbGFzc05hbWUgPSBkb2N1bWVudC5ib2R5LmNsYXNzTmFtZS5yZXBsYWNlKCAnbm8tanMnLCAnanMnICk7XG4iLCIvKipcbiAqIEZpbGUgbGlzdC10b2dnbGUuanNcbiAqXG4gKiBUb2dnbGUgYSBsaXN0IHdpdGggYSBidXR0b24uXG4gKi9cbndpbmRvdy53ZHNMaXN0VG9nZ2xlID0ge307XG4oIGZ1bmN0aW9uKCB3aW5kb3csICQsIGFwcCApIHtcblxuXHQvLyBDb25zdHJ1Y3Rvci5cblx0YXBwLmluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuY2FjaGUoKTtcblxuXHRcdGlmICggYXBwLm1lZXRzUmVxdWlyZW1lbnRzKCkgKSB7XG5cdFx0XHRhcHAuYmluZEV2ZW50cygpO1xuXHRcdH1cblx0fTtcblxuXHQvLyBDYWNoZSBhbGwgdGhlIHRoaW5ncy5cblx0YXBwLmNhY2hlID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjID0ge1xuXHRcdFx0Ym9keTogJCggJ2JvZHknICksXG5cdFx0XHR0b2dnbGVCdXR0b246ICQoICcuZHJvcGRvd24tdG9nZ2xlJyApXG5cdFx0fTtcblx0fTtcblxuXHQvLyBDb21iaW5lIGFsbCBldmVudHMuXG5cdGFwcC5iaW5kRXZlbnRzID0gZnVuY3Rpb24oKSB7XG5cblx0XHQvLyBUb2dnbGUgdGhlIGxpc3QgdG8gb3Blbi9jbG9zZSBvbiBidXR0b24gY2ljay5cblx0XHRhcHAuJGMudG9nZ2xlQnV0dG9uLm9uKCAnY2xpY2snLCBhcHAudG9nZ2xlTGlzdCApO1xuXG5cdFx0Ly8gQWxsb3cgdGhlIHVzZXIgdG8gY2xvc2UgdGhlIGxpc3QgYnkgaGl0dGluZyB0aGUgZXNjIGtleS5cblx0XHRhcHAuJGMuYm9keS5vbiggJ2tleWRvd24nLCBhcHAuZXNjS2V5Q2xvc2UgKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMudG9nZ2xlQnV0dG9uLmxlbmd0aDtcblx0fTtcblxuXHQvLyBUb2dnbGUgdGhlIGlzdC5cblx0YXBwLnRvZ2dsZUxpc3QgPSBmdW5jdGlvbigpIHtcblxuICAgIC8vIEZpZ3VyZSBvdXQgd2hpY2ggbGlzdCB3ZSdyZSBvcGVuaW5nIGFuZCBzdG9yZSB0aGUgb2JqZWN0LlxuXHRcdHZhciAkbGlzdCA9ICQoICQoIHRoaXMgKS5zaWJsaW5ncyggJy5kcm9wZG93bi1saXN0JyApICksXG5cdFx0XHRcdGxvY2F0aW9uID0gJ2JvdHRvbSc7XG5cblx0XHQvLyBGaWd1cmUgb3V0IHdoZXJlIGlmIHdlIG5lZWQgdG8gY2hhbmdlIHRoZSBsaXN0IHBvc2l0aW9uLi5cblx0XHRpZiAoICQoIHRoaXMgKS5kYXRhKCAnbG9jYXRpb24nICkgKSB7XG5cdFx0XHRsb2NhdGlvbiA9ICQoIHRoaXMgKS5kYXRhKCAnbG9jYXRpb24nICk7XG5cdFx0fVxuXG4gICAgaWYgKCAkbGlzdC5oYXNDbGFzcyggJ29wZW4nICkgKSB7XG4gICAgICBhcHAuY2xvc2VMaXN0KCAkbGlzdCwgdHJ1ZSApO1xuICAgIH0gZWxzZSB7XG4gICAgICBhcHAuY2xvc2VMaXN0KCAkKCAnLmRyb3Bkb3duLWxpc3Qub3BlbicgKSwgZmFsc2UgKTtcbiAgICAgIGFwcC5vcGVuTGlzdCggJGxpc3QsIGxvY2F0aW9uICk7XG4gICAgfVxuXHR9O1xuXG5cdC8vIENsb3NlIHRoZSBvcGVuIGxpc3QuXG5cdGFwcC5jbG9zZUxpc3QgPSBmdW5jdGlvbiggJGxpc3QsIG1vdmVGb2N1cyApIHtcbiAgICAkbGlzdC5yZW1vdmVDbGFzcyggJ29wZW4nICk7XG4gICAgJGxpc3Quc2libGluZ3MoICcuZHJvcGRvd24tdG9nZ2xlJyApLmF0dHIoICdhcmlhLWV4cGFuZGVkJywgZmFsc2UgKTtcblxuICAgIGlmICggbW92ZUZvY3VzICkge1xuICAgICAgJGxpc3Quc2libGluZ3MoICcuZHJvcGRvd24tdG9nZ2xlJyApLmZvY3VzKCk7XG4gICAgfVxuXHR9O1xuXG4gIC8vIE9wZW4gYSBsaXN0LlxuICBhcHAub3Blbkxpc3QgPSBmdW5jdGlvbiggJGxpc3QsIHBvc2l0aW9uICkge1xuXG5cdFx0Ly8gQWRkIGEgY2xhc3MgdG9wIHdoZW4gd2Ugd2FudCBpdCB0byBvcGVuIG9uIHRvcCwgaW5zdGVhZCBvZiBiZWxvdyB0aGUgYnV0dG9uXG5cdFx0aWYgKCAndG9wJyA9PT0gcG9zaXRpb24gKSB7XG5cdFx0XHQkbGlzdC5hZGRDbGFzcyggJ3RvcCcgKTtcblx0XHR9XG5cbiAgICAkbGlzdC5hZGRDbGFzcyggJ29wZW4nICk7XG4gICAgJGxpc3Quc2libGluZ3MoICcuZHJvcGRvd24tdG9nZ2xlJyApLmF0dHIoICdhcmlhLWV4cGFuZGVkJywgdHJ1ZSApO1xuICB9O1xuXG5cdC8vIENsb3NlIGlmIFwiZXNjXCIga2V5IGlzIHByZXNzZWQuXG5cdGFwcC5lc2NLZXlDbG9zZSA9IGZ1bmN0aW9uKCBlICkge1xuXHRcdGlmICggMjcgPT09IGUua2V5Q29kZSApIHtcblx0XHRcdGFwcC5jbG9zZUxpc3QoICQoICcuZHJvcGRvd24tbGlzdC5vcGVuJyApLCB0cnVlICk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIEVuZ2FnZSFcblx0JCggYXBwLmluaXQgKTtcblxufSkoIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cud2RzTGlzdFRvZ2dsZSApO1xuIiwiLyoqXG4gKiBGaWxlIG1vZGFsLmpzXG4gKlxuICogRGVhbCB3aXRoIG11bHRpcGxlIG1vZGFscyBhbmQgdGhlaXIgbWVkaWEuXG4gKi9cbndpbmRvdy53ZHNNb2RhbCA9IHt9O1xuXG4oIGZ1bmN0aW9uICggd2luZG93LCAkLCBhcHAgKSB7XG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uICgpIHtcblx0XHRhcHAuY2FjaGUoKTtcblxuXHRcdGlmICggYXBwLm1lZXRzUmVxdWlyZW1lbnRzKCkgKSB7XG5cdFx0XHRhcHAuYmluZEV2ZW50cygpO1xuXHRcdH1cblx0fTtcblxuXHQvLyBDYWNoZSBhbGwgdGhlIHRoaW5ncy5cblx0YXBwLmNhY2hlID0gZnVuY3Rpb24gKCkge1xuXHRcdGFwcC4kYyA9IHtcblx0XHRcdCdib2R5JzogJCggJ2JvZHknIClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIERvIHdlIG1lZXQgdGhlIHJlcXVpcmVtZW50cz9cblx0YXBwLm1lZXRzUmVxdWlyZW1lbnRzID0gZnVuY3Rpb24gKCkge1xuXHRcdHJldHVybiAkKCAnLm1vZGFsLXRyaWdnZXInICkubGVuZ3RoO1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbiAoKSB7XG5cdFx0Ly8gVHJpZ2dlciBhIG1vZGFsIHRvIG9wZW4uXG5cdFx0YXBwLiRjLmJvZHkub24oICdjbGljayB0b3VjaHN0YXJ0JywgJy5tb2RhbC10cmlnZ2VyJywgYXBwLm9wZW5Nb2RhbCApO1xuXG5cdFx0Ly8gVHJpZ2dlciB0aGUgY2xvc2UgYnV0dG9uIHRvIGNsb3NlIHRoZSBtb2RhbC5cblx0XHRhcHAuJGMuYm9keS5vbiggJ2NsaWNrIHRvdWNoc3RhcnQnLCAnLmNsb3NlJywgYXBwLmNsb3NlTW9kYWwgKTtcblxuXHRcdC8vIEFsbG93IHRoZSB1c2VyIHRvIGNsb3NlIHRoZSBtb2RhbCBieSBoaXR0aW5nIHRoZSBlc2Mga2V5LlxuXHRcdGFwcC4kYy5ib2R5Lm9uKCAna2V5ZG93bicsIGFwcC5lc2NLZXlDbG9zZSApO1xuXG5cdFx0Ly8gQWxsb3cgdGhlIHVzZXIgdG8gY2xvc2UgdGhlIG1vZGFsIGJ5IGNsaWNraW5nIG91dHNpZGUgb2YgdGhlIG1vZGFsLlxuXHRcdGFwcC4kYy5ib2R5Lm9uKCAnY2xpY2sgdG91Y2hzdGFydCcsICdkaXYubW9kYWwtb3BlbicsIGFwcC5jbG9zZU1vZGFsQnlDbGljayApO1xuXHR9O1xuXG5cdC8vIE9wZW4gdGhlIG1vZGFsLlxuXHRhcHAub3Blbk1vZGFsID0gZnVuY3Rpb24gKCkge1xuXHRcdC8vIEZpZ3VyZSBvdXQgd2hpY2ggbW9kYWwgd2UncmUgb3BlbmluZyBhbmQgc3RvcmUgdGhlIG9iamVjdC5cblx0XHR2YXIgJG1vZGFsID0gJCggJCggdGhpcyApLmRhdGEoICd0YXJnZXQnICkgKTtcblxuXHRcdC8vIERpc3BsYXkgdGhlIG1vZGFsLlxuXHRcdCRtb2RhbC5hZGRDbGFzcyggJ21vZGFsLW9wZW4nICk7XG5cblx0XHQvLyBBZGQgYm9keSBjbGFzcy5cblx0XHRhcHAuJGMuYm9keS5hZGRDbGFzcyggJ21vZGFsLW9wZW4nICk7XG5cdH07XG5cblx0Ly8gQ2xvc2UgdGhlIG1vZGFsLlxuXHRhcHAuY2xvc2VNb2RhbCA9IGZ1bmN0aW9uICgpIHtcblx0XHQvLyBGaWd1cmUgdGhlIG9wZW5lZCBtb2RhbCB3ZSdyZSBjbG9zaW5nIGFuZCBzdG9yZSB0aGUgb2JqZWN0LlxuXHRcdHZhciAkbW9kYWwgPSAkKCAkKCAnZGl2Lm1vZGFsLW9wZW4gLmNsb3NlJyApLmRhdGEoICd0YXJnZXQnICkgKTtcblxuXHRcdC8vIEZpbmQgdGhlIGlmcmFtZSBpbiB0aGUgJG1vZGFsIG9iamVjdC5cblx0XHR2YXIgJGlmcmFtZSA9ICRtb2RhbC5maW5kKCAnaWZyYW1lJyApO1xuXG5cdFx0Ly8gR2V0IHRoZSBpZnJhbWUgc3JjIFVSTC5cblx0XHR2YXIgdXJsID0gJGlmcmFtZS5hdHRyKCAnc3JjJyApO1xuXG5cdFx0Ly8gUmVtb3ZlIHRoZSBzb3VyY2UgVVJMLCB0aGVuIGFkZCBpdCBiYWNrLCBzbyB0aGUgdmlkZW8gY2FuIGJlIHBsYXllZCBhZ2FpbiBsYXRlci5cblx0XHQkaWZyYW1lLmF0dHIoICdzcmMnLCAnJyApLmF0dHIoICdzcmMnLCB1cmwgKTtcblxuXHRcdC8vIEZpbmFsbHksIGhpZGUgdGhlIG1vZGFsLlxuXHRcdCRtb2RhbC5yZW1vdmVDbGFzcyggJ21vZGFsLW9wZW4nICk7XG5cblx0XHQvLyBSZW1vdmUgdGhlIGJvZHkgY2xhc3MuXG5cdFx0YXBwLiRjLmJvZHkucmVtb3ZlQ2xhc3MoICdtb2RhbC1vcGVuJyApO1xuXHR9O1xuXG5cdC8vIENsb3NlIGlmIFwiZXNjXCIga2V5IGlzIHByZXNzZWQuXG5cdGFwcC5lc2NLZXlDbG9zZSA9IGZ1bmN0aW9uICggZXZlbnQgKSB7XG5cdFx0aWYgKCAyNyA9PT0gZXZlbnQua2V5Q29kZSApIHtcblx0XHRcdGFwcC5jbG9zZU1vZGFsKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENsb3NlIGlmIHRoZSB1c2VyIGNsaWNrcyBvdXRzaWRlIG9mIHRoZSBtb2RhbFxuXHRhcHAuY2xvc2VNb2RhbEJ5Q2xpY2sgPSBmdW5jdGlvbiAoIGV2ZW50ICkge1xuXHRcdC8vIElmIHRoZSBwYXJlbnQgY29udGFpbmVyIGlzIE5PVCB0aGUgbW9kYWwgZGlhbG9nIGNvbnRhaW5lciwgY2xvc2UgdGhlIG1vZGFsXG5cdFx0aWYgKCAhJCggZXZlbnQudGFyZ2V0ICkucGFyZW50cyggJ2RpdicgKS5oYXNDbGFzcyggJ21vZGFsLWRpYWxvZycgKSApIHtcblx0XHRcdGFwcC5jbG9zZU1vZGFsKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIEVuZ2FnZSFcblx0JCggYXBwLmluaXQgKTtcbn0gKSggd2luZG93LCBqUXVlcnksIHdpbmRvdy53ZHNNb2RhbCApO1xuIiwiLyoqXG4gKiBGaWxlIHNjYWxlLWVtYmVkcy5qc1xuICpcbiAqIEF1dG9tYXRpY2FsbHkgcmUtc2NhbGUgaWZyYW1lIGVtYmVkcy5cbiAqL1xud2luZG93Lndkc1NjYWxlRW1iZWRzID0ge307XG4oIGZ1bmN0aW9uKCB3aW5kb3csICQsIGFwcCApIHtcblxuXHR2YXIgcmVzaXplVGltZXIgPSAwO1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHR3aW5kb3c6ICQoIHdpbmRvdyApLFxuXHRcdFx0aWZyYW1lczogJCggJy5zaXRlLW1haW4gLmVudHJ5LWNvbnRlbnQgaWZyYW1lJyApXG5cdFx0fTtcblx0fTtcblxuXHQvLyBDb21iaW5lIGFsbCBldmVudHMuXG5cdGFwcC5iaW5kRXZlbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjLndpbmRvdy5vbiggJ2xvYWQnLCBhcHAuY2hlY2tJZnJhbWVzICk7XG5cdFx0YXBwLiRjLndpbmRvdy5vbiggJ3Jlc2l6ZScsIGFwcC5yZXNjYWxlSWZyYW1lcyApO1xuXHR9O1xuXG5cdC8vIERvIHdlIG1lZXQgdGhlIHJlcXVpcmVtZW50cz9cblx0YXBwLm1lZXRzUmVxdWlyZW1lbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0cmV0dXJuIGFwcC4kYy5pZnJhbWVzLmxlbmd0aDtcblx0fTtcblxuXHQvLyBDaGVjayBpZnJhbWUgc2l6ZS5cblx0Ly8gT25seSBkbyBzb21ldGhpbmcgd2hlbiB0aGUgaUZyYW1lJ3Mgd2lkdGggYXR0cmlidXRlIGlzIHNldCB3aWRlciB0aGFuIGl0IGFjdHVhbGx5IGlzIHJlbmRlcmluZy5cblx0YXBwLmNoZWNrSWZyYW1lcyA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0YXBwLiRjLmlmcmFtZXMuZWFjaCggZnVuY3Rpb24oKSB7XG5cdFx0XHRjb25zdCB3aWR0aCA9ICQoIHRoaXMgKS5hdHRyKCAnd2lkdGgnICksXG5cdFx0XHRcdG91dGVyV2lkdGggPSAkKCB0aGlzICkub3V0ZXJXaWR0aCgpO1xuXG5cdFx0XHRpZiAoIHdpZHRoID4gb3V0ZXJXaWR0aCApIHtcblx0XHRcdFx0YXBwLnNjYWxlRnJhbWUoICQoIHRoaXMgKSApO1xuXHRcdFx0fVxuXHRcdH0pO1xuXG5cdH07XG5cblx0Ly8gUmUtcnVuIHRoZSBzaXplIGNoZWNrIHdoZW4gdGhlIHdpbmRvdyByZXNpemVzLlxuXHQvLyBVc2luZyBhIHRpbWVyIHRvIGVuc3VyZSBpdCBvbmx5IGZpcmVzIGFmdGVyIHdlIGNhbiByZWFzb25hYmx5IGNvbmNsdWRlIHRoZSByZXNpemluZyBpcyBkb25lLlxuXHRhcHAucmVzY2FsZUlmcmFtZXMgPSBmdW5jdGlvbigpIHtcblxuXHRcdGNsZWFyVGltZW91dCggcmVzaXplVGltZXIgKTtcblx0XHRyZXNpemVUaW1lciA9IHNldFRpbWVvdXQoIGFwcC5jaGVja0lmcmFtZXMsIDUwMCApO1xuXG5cdH07XG5cblx0Ly8gU2NhbGUgdGhlIGlGcmFtZSB3aWR0aC5cblx0YXBwLnNjYWxlRnJhbWUgPSBmdW5jdGlvbiggJGlmcmFtZSApIHtcblxuXHRcdC8vIEdldCB0aGUgaGVpZ2h0L3dpZHRoIG9mIHRoZSBjdXJyZW50IGlmcmFtZSwgY2FsY3VsYXRlIHdoYXQgdGhlIG5ldyBoZWlnaHQgc2hvdWxkIGJlLlxuXHRcdGNvbnN0IHdpZHRoID0gJGlmcmFtZS5hdHRyKCAnd2lkdGgnICksXG5cdFx0XHRoZWlnaHQgPSAkaWZyYW1lLmF0dHIoICdoZWlnaHQnICksXG5cdFx0XHRzY2FsZSA9IGhlaWdodCAvIHdpZHRoLFxuXHRcdFx0bmV3SGVpZ2h0ID0gJGlmcmFtZS5vdXRlcldpZHRoKCkgKiBzY2FsZTtcblxuXHRcdCRpZnJhbWUub3V0ZXJIZWlnaHQoIG5ld0hlaWdodCApO1xuXG5cdH07XG5cblx0Ly8gRW5nYWdlIVxuXHQkKCBhcHAuaW5pdCApO1xuXG59KSggd2luZG93LCBqUXVlcnksIHdpbmRvdy53ZHNTY2FsZUVtYmVkcyApO1xuIiwiLyoqXG4gKiBGaWxlIHNjcm9sbC1pbmRpY2F0b3IuanNcbiAqXG4gKiBDaGVja3MgdGhlIGhpZGRlbiBtb2JpbGUgbmF2IGZvciBoZWlnaHQgYW5kIGRpc3BsYXlzIHRoZSBzY3JvbGwgaW5kaWNhdG9yIGlmIGl0J3MgbGFyZ2VyIHRoYW4gdGhlIHNjcmVlbi5cbiAqL1xud2luZG93Lm1vYmlsZU1lbnVTY3JvbGxJbmRpY2F0b3IgPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdH07XG5cblx0Ly8gQ2FjaGUgYWxsIHRoZSB0aGluZ3MuXG5cdGFwcC5jYWNoZSA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYyA9IHtcblx0XHRcdHdpbmRvdzogJCggd2luZG93IClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMud2luZG93Lm9uKCAnbG9hZCcsIGFwcC5zY2FmZm9sZGluZyApO1xuXHR9O1xuXG5cdC8vIFdlJ3JlIHJlbHlpbmcgb24gZWxlbWVudHMgdGhhdCBhcmUgYWRkZWQgYWZ0ZXIgZG9jdW1lbnQucmVhZHksXG5cdC8vIHdoaWNoIG1lYW5zIHdlJ2xsIG5lZWQgdG8gaW5pdGlhdGUgY2FjaGUgLyBldmVudHMgbGF0ZXIuXG5cdGFwcC5zY2FmZm9sZGluZyA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0Ly8gQ2FjaGUgaGlkZGVuIG1lbnUgZWxlbWVudC5cblx0XHRhcHAuJGMubWVudVdyYXAgPSAkKCAnLm1vYmlsZS1uYXYtbWVudS1oaWRkZW4nICk7XG5cblx0XHQvLyBCaW5kIG91ciBldmVudHMuXG5cdFx0YXBwLiRjLm1lbnVXcmFwLm9uKCAnc2Nyb2xsJywgYXBwLmNoZWNrU2Nyb2xsUG9zaXRpb24gKTtcblx0XHRhcHAuJGMud2luZG93Lm9uKCAncmVzaXplJywgYXBwLmNoZWNrRm9yU2Nyb2xsICk7XG5cblx0XHQvLyBJbml0aWF0ZSwgcGFydCAyIVxuXHRcdGFwcC5jaGVja0ZvclNjcm9sbCgpO1xuXHR9O1xuXG5cdC8vIENoZWNrIGlmIHRoZSBlbGVtZW50IGNhbiBiZSBzY3JvbGxlZC5cblx0YXBwLmNoZWNrRm9yU2Nyb2xsID0gZnVuY3Rpb24oKSB7XG5cblx0XHQvLyBSZW1vdmUgdGhlIHNjcm9sbCBjbGFzcywgaW4gY2FzZSBpdCdzIHN0aWxsIHRoZXJlIGZyb20gYmVmb3JlIHJlc2l6aW5nLlxuXHRcdGFwcC5yZW1vdmVTY3JvbGxDbGFzcygpO1xuXG5cdFx0Ly8gSWYgdGhlIGhlaWdodCBpcyBsYXJnZXIgdGhhbiB0aGUgd2luZG93IGhlaWdodCwgdW5oaWRlIHRoZSBpY29uLlxuXHRcdGlmICggYXBwLiRjLm1lbnVXcmFwWzBdLnNjcm9sbEhlaWdodCAtIDcwID4gYXBwLiRjLndpbmRvdy5oZWlnaHQoKSAtIDcwICkge1xuXHRcdFx0YXBwLmFkZFNjcm9sbENsYXNzKCk7XG5cdFx0fVxuXG5cdH07XG5cblx0YXBwLmNoZWNrU2Nyb2xsUG9zaXRpb24gPSBmdW5jdGlvbigpIHtcblxuXHRcdGNvbnN0IHNjcm9sbFBvc2l0aW9uID0gJCggdGhpcyApLnNjcm9sbFRvcCgpICsgYXBwLiRjLm1lbnVXcmFwLmhlaWdodCgpO1xuXG5cdFx0Ly8gSWYgdGhlIHNjcm9sbFBvc2l0aW9uIGFuZCBzcm9sbEhlaWdodCBhcmUgZXF1YWwsIHdlJ3ZlIHJlYWNoZWQgdGhlIGJvdHRvbS5cblx0XHRpZiAoIHNjcm9sbFBvc2l0aW9uID09PSBhcHAuJGMubWVudVdyYXBbMF0uc2Nyb2xsSGVpZ2h0ICkge1xuXHRcdFx0YXBwLnJlbW92ZVNjcm9sbENsYXNzKCk7XG5cdFx0fSBlbHNlIHtcblxuXHRcdFx0Ly8gSWYgbm90LCBhbmQgaXQgZG9lc24ndCBoYXZlIHRoZSBjbGFzcyAtIHdlJ2xsIG5lZWQgdG8gcmUtYWRkIGl0LlxuXHRcdFx0Ly8gRm9yIGV4YW1wbGUsIHdoZW4gd2UndmUgcmVhY2hlZCB0aGUgYm90dG9tIGFuZCB0aGVuIHNjcm9sbCBiYWNrIHVwLlxuXHRcdFx0aWYgKCAhICQoIHRoaXMgKS5oYXNDbGFzcyggJ3Njcm9sbCcgKSApIHtcblx0XHRcdFx0YXBwLmFkZFNjcm9sbENsYXNzKCk7XG5cdFx0XHR9XG5cblx0XHR9XG5cblx0fTtcblxuXHQvLyBBZGQgc2Nyb2xsIGNsYXNzLlxuXHRhcHAuYWRkU2Nyb2xsQ2xhc3MgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMubWVudVdyYXAuYWRkQ2xhc3MoICdzY3JvbGwnICk7XG5cdH07XG5cblx0Ly8gUmVtb3ZlIHNjcm9sbCBjbGFzcy5cblx0YXBwLnJlbW92ZVNjcm9sbENsYXNzID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjLm1lbnVXcmFwLnJlbW92ZUNsYXNzKCAnc2Nyb2xsJyApO1xuXHR9O1xuXG5cdC8vIEVuZ2FnZSFcblx0JCggYXBwLmluaXQgKTtcblxufSkoIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cubW9iaWxlTWVudVNjcm9sbEluZGljYXRvciApO1xuIiwiLyoqXG4gKiBGaWxlIHNraXAtbGluay1mb2N1cy1maXguanMuXG4gKlxuICogSGVscHMgd2l0aCBhY2Nlc3NpYmlsaXR5IGZvciBrZXlib2FyZCBvbmx5IHVzZXJzLlxuICpcbiAqIExlYXJuIG1vcmU6IGh0dHBzOi8vZ2l0LmlvL3ZXZHIyXG4gKi9cbiggZnVuY3Rpb24gKCkge1xuXHR2YXIgaXNXZWJraXQgPSBuYXZpZ2F0b3IudXNlckFnZW50LnRvTG93ZXJDYXNlKCkuaW5kZXhPZiggJ3dlYmtpdCcgKSA+IC0xLFxuXHRcdGlzT3BlcmEgPSBuYXZpZ2F0b3IudXNlckFnZW50LnRvTG93ZXJDYXNlKCkuaW5kZXhPZiggJ29wZXJhJyApID4gLTEsXG5cdFx0aXNJZSA9IG5hdmlnYXRvci51c2VyQWdlbnQudG9Mb3dlckNhc2UoKS5pbmRleE9mKCAnbXNpZScgKSA+IC0xO1xuXG5cdGlmICggKCBpc1dlYmtpdCB8fCBpc09wZXJhIHx8IGlzSWUgKSAmJiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCAmJiB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lciApIHtcblx0XHR3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lciggJ2hhc2hjaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XG5cdFx0XHR2YXIgaWQgPSBsb2NhdGlvbi5oYXNoLnN1YnN0cmluZyggMSApLFxuXHRcdFx0XHRlbGVtZW50O1xuXG5cdFx0XHRpZiAoICEoIC9eW0EtejAtOV8tXSskLyApLnRlc3QoIGlkICkgKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0ZWxlbWVudCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCBpZCApO1xuXG5cdFx0XHRpZiAoIGVsZW1lbnQgKSB7XG5cdFx0XHRcdGlmICggISggL14oPzphfHNlbGVjdHxpbnB1dHxidXR0b258dGV4dGFyZWEpJC9pICkudGVzdCggZWxlbWVudC50YWdOYW1lICkgKSB7XG5cdFx0XHRcdFx0ZWxlbWVudC50YWJJbmRleCA9IC0xO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0ZWxlbWVudC5mb2N1cygpO1xuXHRcdFx0fVxuXHRcdH0sIGZhbHNlICk7XG5cdH1cbn0gKSgpO1xuIiwiLyoqXG4gKiBGaWxlIHNtb290aC1zY3JvbGwuanNcbiAqXG4gKiBTbW9vdGggc2Nyb2xsIGZ1bmN0aW9uYWxpdHksIHNwZWNpZmljYWxseSBmb3IgI21haW4tY29udGVudCB0eXBlIGxpbmtzIChwb3N0IHBhZ2luYXRpb24pLlxuICovXG53aW5kb3cuc21vb3RoU2Nyb2xsID0ge307XG4oIGZ1bmN0aW9uKCB3aW5kb3csICQsIGFwcCApIHtcblxuXHQvLyBDb25zdHJ1Y3Rvci5cblx0YXBwLmluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuY2FjaGUoKTtcblx0XHRhcHAuYmluZEV2ZW50cygpO1xuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHR3aW5kb3c6ICQoIHdpbmRvdyApLFxuXHRcdFx0ZG9jdW1lbnQ6ICQoIGRvY3VtZW50ICksXG5cdFx0XHRwYWdlOiAkKCAnaHRtbCwgYm9keScgKSxcblx0XHRcdGNvbnRlbnQ6ICQoICcuc2l0ZS1tYWluJyApXG5cdFx0fTtcblx0fTtcblxuXHQvLyBDb21iaW5lIGFsbCBldmVudHMuXG5cdGFwcC5iaW5kRXZlbnRzID0gZnVuY3Rpb24oKSB7XG5cdFx0YXBwLiRjLmRvY3VtZW50Lm9uKCAncmVhZHknLCBhcHAuaGFuZGxlU2Nyb2xsICk7XG5cdH07XG5cblx0Ly8gU2Nyb2xsIHRvIGNvbnRlbnRcblx0YXBwLmhhbmRsZVNjcm9sbCA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0Ly8gQmFpbCBpZiB0aGUgaGFzaCBpcyBub3Qgd2hhdCB3ZSdyZSBsb29raW5nIGZvci5cblx0XHRpZiAoICcjbWFpbi1jb250ZW50JyAhPT0gd2luZG93LmxvY2F0aW9uLmhhc2ggKSB7XG5cdFx0XHRyZXR1cm47XG5cdFx0fVxuXG5cdFx0Ly8gVXNlIGFuaW1hdGUgdG8gc2Nyb2xsIGRvd24gdG8gdGhlIGNvbnRlbnQgcG9ydGlvbiBvZiB0aGUgcGFnZSBtb3JlIHNtb290aGx5LlxuXHRcdGFwcC4kYy5wYWdlLmFuaW1hdGUoe1xuXHRcdFx0c2Nyb2xsVG9wOiBhcHAuJGMuY29udGVudC5vZmZzZXQoKS50b3AgKyAncHgnXG5cdFx0fSwgMTAwMCwgJ3N3aW5nJyApO1xuXHR9O1xuXG5cdC8vIEVuZ2FnZSFcblx0JCggYXBwLmluaXQgKTtcblxufSkoIHdpbmRvdywgalF1ZXJ5LCB3aW5kb3cuc21vb3RoU2Nyb2xsICk7XG4iLCIvKipcbiAqIEZpbGUgc3VibWVudS10b2dnbGUuanNcbiAqXG4gKiBBbGxvdyBzdWJtZW51cyB0byBiZSB0b2dnbGVkIGJ5IGtleWJvYXJkLCB3aXRob3V0IGJyZWFraW5nIG1vdXNlIGhvdmVyLlxuICovXG53aW5kb3cuc3ViTWVudVRvZ2dsZXIgPSB7fTtcbiggZnVuY3Rpb24oIHdpbmRvdywgJCwgYXBwICkge1xuXG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC5jYWNoZSgpO1xuXG5cdFx0aWYgKCBhcHAubWVldHNSZXF1aXJlbWVudHMoKSApIHtcblx0XHRcdGFwcC5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdC8vIENhY2hlIGFsbCB0aGUgdGhpbmdzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbigpIHtcblx0XHRhcHAuJGMgPSB7XG5cdFx0XHR3aW5kb3c6ICQoIHdpbmRvdyApLFxuXHRcdFx0cGFyZW50TWVudUl0ZW1zOiAkKCAnbmF2Om5vdCgubW9iaWxlLW5hdi1tZW51KSAubWVudS1pdGVtLWhhcy1jaGlsZHJlbicgKVxuXHRcdH07XG5cdH07XG5cblx0Ly8gQ29tYmluZSBhbGwgZXZlbnRzLlxuXHRhcHAuYmluZEV2ZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdGFwcC4kYy5wYXJlbnRNZW51SXRlbXMub24oICdjbGljayBrZXlkb3duJywgJ2EnLCBhcHAuaGFuZGxlVG9nZ2xlICk7XG5cdFx0YXBwLiRjLnBhcmVudE1lbnVJdGVtcy5vbiggJ21vdXNlbGVhdmUnLCBhcHAuY2xhc3NUb2dnbGVMZWF2ZSApO1xuXHRcdGFwcC4kYy5wYXJlbnRNZW51SXRlbXMub24oICdtb3VzZWVudGVyJywgYXBwLmNsYXNzVG9nZ2xlRW50ZXIgKTtcblx0fTtcblxuXHQvLyBEbyB3ZSBtZWV0IHRoZSByZXF1aXJlbWVudHM/XG5cdGFwcC5tZWV0c1JlcXVpcmVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdHJldHVybiBhcHAuJGMucGFyZW50TWVudUl0ZW1zLmxlbmd0aDtcblx0fTtcblxuXHQvLyBUb2dnbGluZyBsb2dpYy5cblx0Ly8gLSBUb2dnbGUgaW1tZWRpYXRlbHkgaWYgdGhlIGhyZWYgaXMgc2ltcGx5ICNcblx0Ly8gLSBBbGxvdyB0aGUgc2Vjb25kIGludGVyYWN0aW9uIHRvIGxvYWQgdGhlIGxpbmtcblx0YXBwLmhhbmRsZVRvZ2dsZSA9IGZ1bmN0aW9uKCBlICkge1xuXG5cdFx0Ly8gQmFpbCBpZiB0aGVyZSBpcyBubyBzdWJtZW51LlxuXHRcdGlmICggISAkKCB0aGlzICkucGFyZW50KCkuaGFzQ2xhc3MoICdtZW51LWl0ZW0taGFzLWNoaWxkcmVuJyApICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdC8vIEJhaWwgaWYgaXQgaXMgbmVpdGhlciBzcGFjZSBvciBlbnRlci5cblx0XHRpZiAoIDMyICE9PSBlLmtleUNvZGUgJiYgMTMgIT09IGUua2V5Q29kZSAmJiAna2V5ZG93bicgPT09IGUudHlwZSApIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cblx0XHRjb25zdCBsaW5rID0gJCggdGhpcyApLmF0dHIoICdocmVmJyApLFxuXHRcdFx0XHRcdGhhc0NsYXNzID0gJCggdGhpcyApLmhhc0NsYXNzKCAnb3Blbi1saW5rJyApO1xuXG5cdFx0Ly8gSWYgdGhlIGxpbmsgaXMganVzdCBhIGhhc2gsIHdlIGNhbiBzYWZlbHkgdG9nZ2xlIHRoZSBtZW51LlxuXHRcdC8vIERvIHRoZSBzYW1lIHRoaW5nIGlmIGl0IGRvZXMgbm90IGhhdmUgdGhlIGNsYXNzIG9wZW4tbGluay5cblx0XHRpZiAoICcjJyA9PT0gbGluayB8fCAhIGhhc0NsYXNzICkge1xuXHRcdFx0JCggdGhpcyApLnBhcmVudCgpLnRvZ2dsZUNsYXNzKCAnZm9jdXMnICk7XG5cblx0XHRcdC8vIE1ha2Ugc3VyZSB0byBtYXJrIGxlZ2l0IGxpbmtzIHdpdGggYSBjbGFzcyB0byBhbGxvdyB0aGVtIHRvIHdvcmsgb24gYSBzZWNvbmQgYWN0aXZhdGlvbi5cblx0XHRcdGlmICggJyMnICE9PSBsaW5rICkge1xuXHRcdFx0XHQkKCB0aGlzICkuYWRkQ2xhc3MoICdvcGVuLWxpbmsnICk7XG5cdFx0XHR9XG5cblx0XHRcdHJldHVybiBmYWxzZTtcblx0XHR9XG5cdH07XG5cblx0Ly8gUmVtb3ZlcyB0aGUgZm9jdXMgY2xhc3MuIFRyaWdnZXJlZCBvbiBtb3VzZWxlYXZlLCBpbiBjYXNlIHBlb3BsZSBtb3VzZS1jbGljayBvbiBsaW5rcyBhbmQgdGhlbiBtb3ZlIG9uLlxuXHRhcHAuY2xhc3NUb2dnbGVMZWF2ZSA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0aWYgKCAkKCB0aGlzICkuaGFzQ2xhc3MoICdmb2N1cycgKSApIHtcblx0XHRcdCQoIHRoaXMgKS5yZW1vdmVDbGFzcyggJ2ZvY3VzJyApIDtcblx0XHR9XG5cblx0XHRpZiAoICQoIHRoaXMgKS5jaGlsZHJlbiggJ2EnICkuaGFzQ2xhc3MoICdvcGVuLWxpbmsnICkgKSB7XG5cdFx0XHQkKCB0aGlzICkuY2hpbGRyZW4oICdhJyApLnJlbW92ZUNsYXNzKCAnb3Blbi1saW5rJyApO1xuXHRcdH1cblxuXHR9O1xuXG5cdC8vIEFkZCB0aGUgb3Blbi1saW5rIGNsYXNzIHdoZW4gdGhlIG1vdXNlIGVudGVycyB0aGUgbGluayAtIHRoaXMgd2F5LCBsZWdpdCBsaW5rcyB3aWxsIGp1c3Qgd29ya1xuXHQvLyB3aGlsZSB0aGUgaG92ZXIgdGFrZXMgY2FyZSBvZiBzaG93aW5nIHRoZSBzdWJtZW51LlxuXHRhcHAuY2xhc3NUb2dnbGVFbnRlciA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0Y29uc3QgJGxpbmsgPSAkKCB0aGlzICkuY2hpbGRyZW4oICdhJyApO1xuXG5cdFx0aWYgKCAnIycgIT09ICQoICRsaW5rWzBdICkuYXR0ciggJ2hyZWYnICkgKSB7XG5cdFx0XHQkKCAkbGlua1swXSApLmFkZENsYXNzKCAnb3Blbi1saW5rJyApO1xuXHRcdH1cblx0fTtcblxuXHQvLyBFbmdhZ2UhXG5cdCQoIGFwcC5pbml0ICk7XG5cbn0pKCB3aW5kb3csIGpRdWVyeSwgd2luZG93LnN1Yk1lbnVUb2dnbGVyICk7XG4iLCIvKipcbiAqIEZpbGUgd2luZG93LXJlYWR5LmpzXG4gKlxuICogQWRkIGEgXCJyZWFkeVwiIGNsYXNzIHRvIDxib2R5PiB3aGVuIHdpbmRvdyBpcyByZWFkeS5cbiAqL1xud2luZG93Lndkc1dpbmRvd1JlYWR5ID0ge307XG4oIGZ1bmN0aW9uICggd2luZG93LCAkLCBhcHAgKSB7XG5cdC8vIENvbnN0cnVjdG9yLlxuXHRhcHAuaW5pdCA9IGZ1bmN0aW9uICgpIHtcblx0XHRhcHAuY2FjaGUoKTtcblx0XHRhcHAuYmluZEV2ZW50cygpO1xuXHR9O1xuXG5cdC8vIENhY2hlIGRvY3VtZW50IGVsZW1lbnRzLlxuXHRhcHAuY2FjaGUgPSBmdW5jdGlvbiAoKSB7XG5cdFx0YXBwLiRjID0ge1xuXHRcdFx0J3dpbmRvdyc6ICQoIHdpbmRvdyApLFxuXHRcdFx0J2JvZHknOiAkKCBkb2N1bWVudC5ib2R5IClcblx0XHR9O1xuXHR9O1xuXG5cdC8vIENvbWJpbmUgYWxsIGV2ZW50cy5cblx0YXBwLmJpbmRFdmVudHMgPSBmdW5jdGlvbiAoKSB7XG5cdFx0YXBwLiRjLndpbmRvdy5sb2FkKCBhcHAuYWRkQm9keUNsYXNzICk7XG5cdH07XG5cblx0Ly8gQWRkIGEgY2xhc3MgdG8gPGJvZHk+LlxuXHRhcHAuYWRkQm9keUNsYXNzID0gZnVuY3Rpb24gKCkge1xuXHRcdGFwcC4kYy5ib2R5LmFkZENsYXNzKCAncmVhZHknICk7XG5cdH07XG5cblx0Ly8gRW5nYWdlIVxuXHQkKCBhcHAuaW5pdCApO1xufSApKCB3aW5kb3csIGpRdWVyeSwgd2luZG93Lndkc1dpbmRvd1JlYWR5ICk7XG4iXX0=
