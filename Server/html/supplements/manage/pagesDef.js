/*
 * @author: Mike
 * Copyright 2019 Mike. All rights reserved.
 * This file is licensed under the MIT License.
 */

function initCookies(INPUT) {
	var cooks = INPUT.split(" "), cooksI;
	for (cooksI = 0; cooksI < cooks.length; cooksI++) {
		setCookie(cooks[cooksI], "", "/");
	}
}

function delCookies(INPUT) {
	var cooks = INPUT.split(" "), cooksI;
	for (cooksI = 0; cooksI < cooks.length; cooksI++) {
		deleteCookie(cooks[cooksI]);
	}
}

function shakeError1(INPUT, COLOR) {
	var shakeElement = INPUT.split(";"), shakeI;
	for (shakeI = 0; shakeI < shakeElement.length; shakeI++) {
		$(shakeElement[shakeI]).css({"color":COLOR});
		$(shakeElement[shakeI]).effect("shake");
	}
}

/* Select2 */
(function ($) {
	"use strict";
	$(".input100").each(function(){
		$(this).on("blur", function(){
			if($(this).val().trim() !== "") {
				$(this).addClass("has-val");
			}
			else {
				$(this).removeClass("has-val");
			}
		})
	})
})(jQuery);

/* Mobile Menu */
$("span[class^='mobile-menu']").click(function () {
	$(this).toggleClass("toggle");
	$("nav").toggleClass("mobile-nav");

	/* If menu is open when resizing, fix it. */
	$(window).resize(function () {
		if ($(window).width() >= 992) {
			$("nav").removeClass("mobile-nav");
			$("span[class^='mobile-menu']").removeClass("toggle");
		}
	});
});

/* Scroll Navigation */
$(window).scroll(function () {
	var scrollTop = 0;
	scrollTop = $(window).scrollTop();
	if (scrollTop >= 100) {
		$("header").addClass("scrolled");
	} else if (scrollTop < 100) {
		$("header").removeClass("scrolled");
	}
});

/* Illegal Char */
function illegalChar(INPUT) {
	if (INPUT.includes("CO:LN") || INPUT.includes("SM;CL")) {
		return false;
	} else {
		return true;
	}
}

/* Initiate the Process */
var patternName, currentDomain = "https://" + window.location.host + "/", Patterns = "";

function setNewPatternCookie(INPUT, COOK) {
	INPUT = Base64.encode(INPUT.substring(0, INPUT.length - 5));
	setCookie(COOK, INPUT, "/");
}
function comparePattern(INPUT, COOK) {
	var cook = Base64.decode(getCookie(COOK)), cookI;
	cook = cook.split("SM;CL");
	for (cookI = 0; cookI < cook.length; cookI++) {
		if (INPUT === cook[cookI]) {
			return false;
		}
	}
	return true;
}
