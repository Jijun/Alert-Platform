/*
 * @author: Mike
 * Copyright 2019 Mike. All rights reserved.
 * This file is licensed under the MIT License.
 */

document.getElementsByClassName("close1")[0].style.display = 'none';

function shakeError1(Frame, Icon, Input, color) {
	$(Icon).css({"color":color});
	$(Input).css({"color":color});
	$(Frame).effect("shake");
}
function resetColor(Icon, Input) {
	$(Icon).css({'color':''});
	$(Input).css({'color':''});
}

$(document).ready(function() {
	$("body").hide().fadeIn(1000);
	$("#resetpass").on("click", function (e) {
		if ($(this).attr('href') == '#') {
			e.preventDefault();
		}
		var text = $(this).text();
		$(".signin").toggleClass("moveup");
		$(".resetpass").toggleClass("moveup");
		$(this).text(text == "Forgot Password?" ? "sign in" : "Forgot Password?").toggleClass("active");
	});
	$("#usr_password").keypress(function() { resetColor("#usr_password", "#usr_password_icon"); });
	$("#usr_email").keypress(function() { resetColor("#usr_email", "#usr_email_icon"); });
	$("#resetEmail").keypress(function() { resetColor("#resetEmail", "#resetEmailIcon"); $(".submit1").text("Get PIN"); $(".button1").attr("class", "button1").prop("disabled", false); });
	$("#thePIN").keypress(function() { resetColor("#PINIcon", "#thePIN"); $(".submit1").text("VERIFY"); $(".button1").attr("class", "button1").prop("disabled", false); });
	$("#resetPass1").keypress(function() { resetColor("#resetPass1", "#resetPassIcon1"); });
	$("#resetPass2").keypress(function() { resetColor("#resetPass2", "#resetPassIcon2"); });
});

/* Login: Success/Failed */
function LOGIN_TF(value) {
	if (value === "200OK") {
		window.location.replace(document.getElementById("defaultjs").src.split("redirect=")[1]);
	} else if (value == "ERROR") {
		document.getElementById("usr_password").value = "";
		shakeError1("#usr_email_group", "#usr_email_icon", "#usr_email", "#ffe819");
		shakeError1("#usr_password_group", "#usr_password_icon", "#usr_password", "#ffe819");
		document.getElementById("usr_password").disabled = false;
		document.getElementById("usr_email").disabled = false;
		document.getElementById("login").disabled = false;
		console.log("ERROR: Please check your Email and Password!");
	} else {
		document.getElementById("usr_email_group").value = "TOO MANY FAILED ATTEMPTS!";
		document.getElementById("usr_password").value = "PLEASE SET A NEW PASSWORD!";
		document.getElementById('resetpass').click();
		console.log("ERROR: You are required to set a new password.");
	}
}
function loginPost(value) {
	$.ajax({
		data: value,
		method: 'POST',
		success: function(msg) {
			LOGIN_TF(msg.substring(0, 5));
		}
	});
}

/* Reset Password: Success/Failed */
function sendPost_BTN(value) {
	if (document.getElementById("resetPass1") && document.getElementById("resetPass2") && document.getElementById("resetPass1").value && document.getElementById("resetPass2").value) {
		if (value == "200OK") {
			window.location.replace(document.getElementById("defaultjs").src.split("redirect=")[1]);
		} else if (value == "ERROR") {
			shakeError1("#resetPassGroup1","#resetPassIcon1","#resetPass1", "#FF0000");
			shakeError1("#resetPassGroup2","#resetPassIcon2","#resetPass2", "#FF0000");
			document.getElementById("resetPass1").disabled = false;
			document.getElementById("resetPass2").disabled = false;
			document.getElementById("resetPass1").value = "";
			document.getElementById("resetPass2").value = "";
			document.getElementById("resetPwd").disabled = false;
			console.log("ERROR: Please check your passwords! They are not the same.");
		} else {
			document.getElementById("thePIN").value = "";
			document.getElementById("resetPass1").value = "";
			document.getElementById("resetPass2").value = "";
			document.getElementById("resetEmail").disabled = false;
			document.getElementById("thePIN").disabled = true;
			document.getElementsByClassName("button1")[0].disabled = false;
			shakeError1("#resetEmailGroup","#resetEmailIcon","#resetEmail", "#FF0000");
			shakeError1("#PINGroup","#PINIcon","#thePIN", "#FF0000");
			console.log("ERROR: You need to restart the whole process.");
		}
	} else if (!document.getElementById("thePIN").disabled) {
		document.getElementsByClassName("button1")[0].disabled = true;
		if (value == "200OK") {
			document.getElementsByClassName("button1")[0].classList.add("finished1");
			document.getElementById("thePIN").disabled = true;
			document.getElementById("resetPass1").disabled = false;
			document.getElementById("resetPass2").disabled = false;
			document.getElementById("resetPwd").disabled = false;
		} else if (value == "ERRRR") {
			document.getElementsByClassName("button1")[0].classList.add("finished2");
			document.getElementsByClassName("submit1")[0].value = "Get PIN";
			document.getElementById("thePIN").value = "";
			document.getElementById("thePIN").disabled = true;
			document.getElementById("resetEmail").disabled = false;
			shakeError1("#PINGroup","#PINIcon","#thePIN", "#FF0000");
			console.log("ERROR: Please restart the process.");
		} else {
			document.getElementsByClassName("button1")[0].classList.add("finished2");
			shakeError1("#PINGroup","#PINIcon","#thePIN", "#FF0000");
			console.log("ERROR: Wrong PIN.");
		}
	} else {
		if (value == "200OK") {
			document.getElementsByClassName("button1")[0].classList.add("finished1");
			document.getElementById("thePIN").disabled = false;
			document.getElementById("resetEmail").disabled = true;
		} else {
			document.getElementsByClassName("button1")[0].classList.add("finished2");
			shakeError1("#resetEmailGroup","#resetEmailIcon","#resetEmail", "#FF0000");
			document.getElementsByClassName("button1")[0].disabled = true;
			console.log("ERROR: Please check your email address!");
		}
	}
}
function sendPost(value) {
	$.ajax({
		data: value,
		method: 'POST',
		success: function(msg) {
			sendPost_BTN(msg.substring(0, 5));
		}
	});
}

/* Get PIN/VERIFY Button Animation */
const button = document.querySelector(".button1");
const submit = document.querySelector(".submit1");
function toggleClass() {
	this.classList.toggle("active1");
}
function addClass() {
	if (document.getElementById("resetEmail").value.includes("@") && document.getElementById("resetEmail").value.includes(".")) {
		if (document.getElementById("thePIN").disabled) {
			sendPost("ACC=" + Base64.encode(document.getElementById("resetEmail").value.toString().toLowerCase()));
		} else {
			sendPost("PIN=" + Base64.encode(document.getElementById("thePIN").value.toString()) + "&ACC=" + Base64.encode(document.getElementById("resetEmail").value.toString().toLowerCase()));
		}
	} else {
		this.classList.add("finished2");
		shakeError1("#resetEmailGroup","#resetEmailIcon","#resetEmail", "#FF0000");
	}
}
button.addEventListener("click", toggleClass);
button.addEventListener("transitionend", toggleClass);
button.addEventListener("transitionend", addClass);

/* Action After Click the Login Button */
$("#login").click(function(event) {
	event.preventDefault();
	if (document.getElementById("usr_password").value.length > 5 && document.getElementById("usr_email").value.length > 3) {
		document.getElementById("usr_password").disabled = true;
		document.getElementById("usr_email").disabled = true;
		document.getElementById("login").disabled = true;
		loginPost("usr=" + Base64.encode(document.getElementById("usr_email").value.toString().toLowerCase()) + "&pwd=" + Base64.encode(document.getElementById("usr_password").value.toString()));
	} else {
		document.getElementById("usr_password").value = "";
		document.getElementById("usr_email").value = "";
		shakeError1("#usr_email_group", "#usr_email_icon", "#usr_email", "#FF0000");
		shakeError1("#usr_password_group", "#usr_password_icon", "#usr_password", "#FF0000");
	}
});

/* Action After Click the Reset Password Button */
$("#resetPwd").click(function(event) {
	event.preventDefault();
	if (document.getElementById("resetPass1").value.length > 5 && document.getElementById("resetPass1").value == document.getElementById("resetPass2").value) {
		document.getElementById("resetPass1").disabled = true;
		document.getElementById("resetPass2").disabled = true;
		document.getElementById("resetPwd").disabled = true;
		sendPost("A=" + Base64.encode(document.getElementById("resetEmail").value.toString().toLowerCase()) + "&P=" + Base64.encode(document.getElementById("thePIN").value.toString()) + "&P1=" + Base64.encode(document.getElementById("resetPass1").value.toString()) + "&P2=" + Base64.encode(document.getElementById("resetPass2").value.toString()));
	} else {
		document.getElementById("resetPass1").value = "";
		document.getElementById("resetPass2").value = "";
		shakeError1("#resetPassGroup1", "#resetPassIcon1", "#resetPass1", "#FF0000");
		shakeError1("#resetPassGroup2", "#resetPassIcon2", "#resetPass2", "#FF0000");
	}
});
