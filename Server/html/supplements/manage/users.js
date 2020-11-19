/*
 * @author: Mike
 * Copyright 2019 Mike. All rights reserved.
 * This file is licensed under the MIT License.
 */

/* Cookies */
initCookies("ClickDIS2 Delete_P2 Modify_P2 Patterns2 newBtnDisable2");

function validEmail(email) {
	/* We DO allow UTF-8 characters in the email address, so it is faster to do this way than regex. */
	if (email.length > 5 && (email.match(/@/g) || []).length === 1 && email.includes(".")) {
		if (email.includes("@.") || email.includes(".@") || email.includes("..")) {
			return false;
		} else if (email.charAt(0) === "@") {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

function isValidPermission(INPUT, MIN, MAX) {
	var VALID = (/^\+?(0|[1-9]\d*)$/.test(INPUT) ? "YES" : "NO");
	if (VALID === "NO") {
		return false;
	} else {
		VALID = Number(INPUT);
		if (VALID > (MIN - 1) && (MAX + 1) > VALID) {
			return true;
		} else {
			return false;
		}
	}
}

/* Loading the page. */
var tableCol1 = '<div class="row" id="', admin = 0;
var tableCol2 = '"><div class="cell content" data-title="User">';
var tableCol3 = '</div><div class="cell content" data-title="Permission">';
var tableCol4 = '</div><div class="cell content" data-title="Operation"><i class="fa fa-edit" id="';
var tableSel1 = '"></i>&nbsp;&nbsp;&nbsp;<i class="fa fa-trash" id="';
var tableSel2 = '"></i></div></div>';

function insertTable(retStr) {
	var splitRes = retStr.split("SM;CL"), tableCol = "", I;
	for (I = 0; I < splitRes.length; I++) {
		var splitres = splitRes[I].split("CO:LN"), i, splitresType;
		for (i = 0; i < splitres.length; i++) {
			if (i == 0) tableCol += tableCol1 + Base64.encode(splitres[0]) + tableCol2 + splitres[0] + tableCol3;
			if (i == 1) {
				if (splitres[1] === "0") splitresType = "ADMIN";
				if (splitres[1] === "0") admin++;
				if (splitres[1] === "1") splitresType = "MEMBER";
				tableCol += splitresType + tableCol4 + Base64.encode(splitRes[I]) + tableSel1 + "DEL" + Base64.encode(splitres[0] + splitres[1]) + tableSel2;
			}
			if (i == 0) Patterns += splitres[0] + "SM;CL";
		}
	}
	document.getElementById("Table1").innerHTML = tableCol;
	setNewPatternCookie(Patterns, "Patterns2");
	setCookie("ClickDIS2", "jFidcxkwCJ", "/");
}

function loadPage() {
	$.ajax({
		url: "ajax.php",
		type: "POST",
		data: "requestContent=ALLUSERS",
		success: function(data) {
			insertTable(data);
			return false;
		}
	});
}

function sendData(ACT, DATA) {
	$.ajax({
		url: "ajax.php",
		type: "POST",
		data: "table=Users&act=" + ACT + "&data=" + DATA,
		success: function(data) {
			if (data === "REDOK") {
				window.location.replace("/manage/");
			} else if (data.substring(0, 3) === "NEW") {
				if (data.substring(3) === "OK") {
					var insertData = Base64.decode(DATA).slice(0, -1), insertDataType = Base64.decode(DATA).slice(-1);
					setCookie("Patterns2", Base64.encode(insertData + "SM;CL" + Base64.decode(getCookie("Patterns2"))), "/");
					"0"===insertDataType&&(insertDataType="ADMIN",++admin);
					if (insertDataType === "1") insertDataType = "MEMBER";
					$("label[for='newUser']").text("WHAT'S THE EMAIL?");
					$("#newUser").val("");
					$("#Table1").prepend(tableCol1 + Base64.encode(insertData) + tableCol2 + insertData + tableCol3 + insertDataType + tableCol4 + Base64.encode(insertData + "CO:LN" + Base64.decode(DATA).slice(-1)) + tableSel1 + "DEL" + Base64.encode(insertData + Base64.decode(DATA).slice(-1)) + tableSel2);
				} else {
					shakeError1("#newUser","#FF0000");
				}
				setCookie("newBtnDisable2", "HskC8&slC3", "/");
				$("#newUser").prop("disabled", false);
			} else if (data.substring(0, 3) === "UPD") {
				if (data.substring(3) === "OK") {
					$("#modalBody").removeClass("Show");
					var insertData = Base64.decode(DATA).slice(0, -2), insertDataType = Base64.decode(DATA).slice(-2, -1), oG = Base64.decode(DATA).slice(-1);
					if (insertDataType === "0") insertDataType = "ADMIN";
					if (insertDataType === "1") insertDataType = "MEMBER";
					if (oG == "0" && insertDataType == "MEMBER") admin--;
					if (oG == "1" && insertDataType == "ADMIN") admin++;
					document.getElementById(Base64.encode(insertData)).remove();
					$("#Table1").prepend(tableCol1 + Base64.encode(insertData) + tableCol2 + insertData + tableCol3 + insertDataType + tableCol4 + Base64.encode(insertData + "CO:LN" + Base64.decode(DATA).slice(-2, -1)) + tableSel1 + "DEL" + Base64.encode(insertData + Base64.decode(DATA).slice(-2, -1)) + tableSel2);
				} else {
					shakeError1(".selection","#FF0000");
				}
				setCookie("ClickDIS2", "jFidcxkwCJ", "/");
				$("#submitBTN_Modify").on("click");
				$("#sel2").prop("disabled", false);
			} else {
				if (data.substring(3) === "OK") {
					var newPtn = Base64.decode(getCookie("Patterns2")).split("SM;CL"), newPtnI, newPattern;
					for (newPtnI = 0; newPtnI < newPtn.length; newPtnI++) {
						if (newPtn[newPtnI] !== Base64.decode(DATA).slice(0, -1)) {
							newPattern += newPtn[newPtnI] + "SM;CL";
						}
					}
					if (Base64.decode(DATA).slice(-1) == "0") admin--;
					setCookie("Patterns2", Base64.encode(newPattern.substring(0, newPattern.length - 5)), "/");
					document.getElementById(Base64.encode(Base64.decode(DATA).slice(0, -1))).remove();
					$("#modalBody").removeClass("Show");
				} else {
					shakeError1("#ModalCTNT","#FF0000");
				}
				setCookie("ClickDIS2", "jFidcxkwCJ", "/");
				$("#submitBTN_Delete").on("click");
			}
			if (data.substring(3) !== "OK") console.log("ERROR Code: " + data.substring(3) + " \nPlease visit " + currentDomain + "manage/Error.php?code=" + data.substring(3) + " for more details.");
		}
	});
}

/* Custom Select */
$(".custom-select").each(function () {
	var classes = $(this).attr("class"),
	id = $(this).attr("id"),
	name = $(this).attr("name");
	var template = '<div class="' + classes + '">';
	template += '<span class="custom-select-trigger">' + $(this).attr("placeholder") + '</span>';
	template += '<div class="custom-options">';
	$(this).find("option").each(function () {
		template += '<span class="custom-option ' + $(this).attr("class") + '" data-value="' + $(this).attr("value") + '">' + $(this).html() + '</span>';
	});
	template += '</div></div>';

	$(this).wrap('<div class="custom-select-wrapper"></div>');
	$(this).hide();
	$(this).after(template);
});
$(".custom-option:first-of-type").hover(function () {
	$(this).parents(".custom-options").addClass("option-hover");
}, function () {
	$(this).parents(".custom-options").removeClass("option-hover");
});
$(".custom-select-trigger").on("click", function () {
	var newBtnDisable22 = getCookie("newBtnDisable2");
	if (newBtnDisable22 !== "HskC8&slw3") {
		$('html').one('click', function () {
			$(".custom-select").removeClass("opened");
		});
		$(this).parents(".custom-select").toggleClass("opened");
		event.stopPropagation();
	}
});
$(".custom-option").on("click", function () {
	$(this).parents(".custom-select-wrapper").find("select").val($(this).data("value"));
	$(this).parents(".custom-options").find(".custom-option").removeClass("selection");
	$(this).addClass("selection");
	$(this).parents(".custom-select").removeClass("opened");
	$(this).parents(".custom-select").find(".custom-select-trigger").text($(this).text());
});

/* Load Page */
$(document).ready(function(){loadPage();})

$(document).ajaxComplete(function() {
	document.body.classList.add("show");
	setCookie("newBtnDisable2", "HskC8&slC3", "/");
	$(".cell").on("click", ".fa.fa-edit", function() {
		$("#ModalTitle").text("MODIFY");
		$("#ModalCTNT").html('<div class="wrap-contact100"><form class="contact100-form"><div class="wrap-input100"><span class="label-input100">User</span><input class="input100" type="text" required id="user1"><span class="focus-input100"></span></div><div class="wrap-input100 input100-select"><span class="label-input100">Permission</span><div><select class="selection-2" id="sel2" required><option value="1">MEMBER</option><option value="0">ADMIN</option></select></div><span class="focus-input100"></span></div></form></div><div id="dropDownSelect1"></div>');
		$(".selection-2").select2({	minimumResultsForSearch: 20, dropdownParent: $("#dropDownSelect1") });
		var patternName = $(this).attr("id");
		var patternNames = Base64.decode(patternName).split("CO:LN");
		$("#sel2 option[value='" + patternNames[1] + "']").prop("selected", true).change();
		$("#user1").val(patternNames[0]);
		setCookie("Modify_P2", Base64.encode(patternNames[0]) + patternNames[1], "/", 0);
		$("#user1").prop('disabled', true);
		if ($("#submitBTN_Modify").length == 0) $("#submitBTN_Delete").attr("id", "submitBTN_Modify");
		$("#modalBody").addClass("Show");
	});
	$("#modalBody").on("click", ".closeModal", function() {
		if (getCookie("ClickDIS2") !== "jFidc&kwCJ") {
			$("#modalBody").removeClass("Show");
		}
	});
	$(".cell").on("click", ".fa.fa-trash", function() {
		$("#ModalTitle").html("<span class='FONT_COLOR_RED'>DELETE</span>");
		var patternName = Base64.decode($(this).attr("id").substring(3)).slice(0, -1);
		setCookie("Delete_P2", $(this).attr("id").substring(3), "/", 0);
		$("#ModalCTNT").html('<br><br><p>Are you sure to <strong><br>DELETE: "<span class="FONT_COLOR_RED">' + patternName + '</span>" @User</strong>?</p>');
		if ($("#submitBTN_Delete").length == 0) $("#submitBTN_Modify").attr("id","submitBTN_Delete");
		$("#modalBody").addClass("Show");
	});
});

window.onclick = function(modalCloseEvent) {
	if (modalCloseEvent.target == document.getElementById("modalBody") && getCookie("ClickDIS2") !== "jFidc&kwCJ") {
		$("#modalBody").toggleClass("Show");
	}
}

window.setInterval(function(){
	newBtnDisable2 = getCookie("newBtnDisable2");
	if ($("#newUser").val() != "" && newBtnDisable2 !== "HskC8&slw3") {
		$("#newRow1").prop("disabled", false);
		$("#newRow1").removeClass("NEW1");
		$("#newRow1").addClass("NEW2");
	} else {
		$("#newRow1").prop("disabled", true);
		$("#newRow1").removeClass("NEW2");
		$("#newRow1").addClass("NEW1");
	}
	if ($("#user1").val() == "") {
		$("#submitBTN_Modify").prop("disabled", true);
		$("#submitBTN_Modify").removeClass("w_hover");
	} else {
		$("#submitBTN_Modify").prop("disabled", false);
		$("#submitBTN_Modify").addClass("w_hover");
	}
}, 512);
$(document).on("click", "#newRow1", function() {
	$("#newRow1").prop("disabled", true);
	$("#newUser").prop("disabled", true);
	$("label[for='newUser']").text("");
	var ERR = "NO";
	setCookie("newBtnDisable2", "HskC8&slw3", "/");
	if ($("#newUser").val() != "" && illegalChar($("#newUser").val()) && validEmail($("#newUser").val())) {
		if (isValidPermission($("#newPermission").val(), 0, 1)) {
			if (comparePattern($("#newUser").val(), "Patterns2") === true) {
				sendData("NEW", Base64.encode($("#newUser").val() + $("#newPermission").val()));
			} else {
				ERR = "YES";
				console.log("ERROR Code: 100202 \nPlease visit " + currentDomain + "manage/Error.php?code=100202 for more details.");
			}
		} else {
			ERR = "YES";
			console.log("ERROR Code: 100201 \nPlease visit " + currentDomain + "manage/Error.php?code=100201 for more details.");
		}
	} else {
		ERR = "YES";
		console.log("ERROR Code: 100205 \nPlease visit " + currentDomain + "manage/Error.php?code=100205 for more details.");
	}
	if (ERR === "YES") {
		shakeError1("#newUser","#FF0000");
		setCookie("newBtnDisable2", "HskC8&slC3", "/");
		$("#newRow1").prop("disabled", false);
		$("#newUser").prop("disabled", false);
	}
});
$(document).on("click", "#submitBTN_Modify", function() {
	$("#sel2").prop("disabled", true);
	$("#user1").prop("disabled", true);
	$("#submitBTN_Modify").off("click");
	setCookie("ClickDIS2", "jFidc&kwCJ", "/");
	var ERR = "NO";
	if ($("#user1").val() !== "" && illegalChar($("#user1").val()) && validEmail($("#user1").val())) {
		if (Base64.decode(getCookie("Modify_P2").slice(0, -1)) !== $("#user1").val() || (comparePattern($("#user1").val(), "Patterns2") === true)) {
			shakeError1(".selection","#FF0000");
			ERR = "YES";
			console.log("ERROR Code: 100221 \nPlease visit " + currentDomain + "manage/Error.php?code=100221 for more details.");
		} else if ($("#sel2").val() == getCookie("Modify_P2").slice(-1)) {
			ERR = "YES";
		} else if (getCookie("Modify_P2").slice(-1) != "0" || admin > 1) {
			sendData("UPD", Base64.encode($("#user1").val() + $("#sel2").val() + getCookie("Modify_P2").slice(-1)));
		} else {
			shakeError1(".selection","#FF0000");
			ERR = "YES";
			console.log("ERROR Code: 100222 \nPlease visit " + currentDomain + "manage/Error.php?code=100222 for more details.");
		}
	} else {
		shakeError1("#user1","#FF0000");
		ERR = "YES";
		console.log("ERROR Code: 100205 \nPlease visit " + currentDomain + "manage/Error.php?code=100205 for more details.");
	}
	if (ERR === "YES") {
		setCookie("ClickDIS2", "jFidcxkwCJ", "/");
		$("#submitBTN_Modify").on("click");
		$("#sel2").prop("disabled", false);
	}
});
$(document).on("click", "#submitBTN_Delete", function() {
	setCookie("ClickDIS2", "jFidc&kwCJ", "/");
	$("#submitBTN_Delete").off("click");
	if (comparePattern(Base64.decode(getCookie("Delete_P2")).slice(0, -1), "Patterns2") !== true) {
		if (admin > 1 || Base64.decode(getCookie("Delete_P2")).slice(-1) != "0") {
			sendData("DEL", getCookie("Delete_P2"));
		} else {
			shakeError1("#ModalCTNT", "#FF0000");
			setCookie("ClickDIS2", "jFidcxkwCJ", "/");
			$("#submitBTN_Delete").on("click");
			console.log("ERROR Code: 100222 \nPlease visit " + currentDomain + "manage/Error.php?code=100222 for more details.");
		}
	} else {
		shakeError1("#ModalCTNT", "#FF0000");
		setCookie("ClickDIS2", "jFidcxkwCJ", "/");
		$("#submitBTN_Delete").on("click");
		console.log("ERROR Code: 100204 \nPlease visit " + currentDomain + "manage/Error.php?code=100204 for more details.");
	}
});

$(window).on("beforeunload", function () {
	delCookies("ClickDIS2 Delete_P2 Modify_P2 Patterns2 newBtnDisable2");
});
