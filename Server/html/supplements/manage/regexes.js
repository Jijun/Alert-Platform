/*
 * @author: Mike
 * Copyright 2019 Mike. All rights reserved.
 * This file is licensed under the MIT License.
 */

/* Cookies */
initCookies("ClickDIS1 Delete_P1 Modify_P1 Patterns1 newBtnDisable1");

function isKeyExist(INPUT) {
	var cook = Base64.decode(getCookie("Patterns1")), cookI;
	cook = cook.split("SM;CL");
	for (cookI = 0; cookI < cook.length; cookI++) {
		if (INPUT === cook[cookI].split("CO:LN")[1]) {
			return true;
		}
	}
	return false;
}

/* Loading the page. */
var tableCol1 = '<div class="row" id="'
var tableCol2 = '"><div class="cell content" data-title="Type">';
var tableCol3 = '</div><div class="cell content" data-title="Pattern">';
var tableCol4 = '</div><div class="cell content" data-title="Notify">';
var tableCol5 = '</div><div class="cell content" data-title="Operation"><i class="fa fa-edit" id="';
var tableSel1 = '"></i>&nbsp;&nbsp;&nbsp;<i class="fa fa-trash" id="';
var tableSel2 = '"></i></div></div>';

function insertTable(retStr) {
	var splitRes = retStr.split("SM;CL"), tableCol = "", I;
	for (I = 0; I < splitRes.length; I++) {
		var splitres = splitRes[I].split("CO:LN"), i, splitresType;
		for (i = 0; i < splitres.length; i++) {
			if (i == 0) {
				if (splitres[0] === "A") splitresType = "IP";
				if (splitres[0] === "B") splitresType = "Service";
				if (splitres[0] === "C") splitresType = "Details";
				if (splitres[0] === "D") splitresType = "Others";
				tableCol += tableCol1 + Base64.encode(splitres[0] + "CO:LN" + splitres[1]) + tableCol2 + splitresType + tableCol3;
			}
			if (i == 1) tableCol += splitres[1] + tableCol4;
			if (i == 1) Patterns += splitres[0] + "CO:LN" + splitres[1] + "SM;CL"
			if (i == 2) tableCol += splitres[2] + tableCol5 + Base64.encode(splitRes[I]) + tableSel1 + splitres[0] + Base64.encode(splitres[1]) + "DEL" + tableSel2;
		}
	}
	document.getElementById("Table1").innerHTML = tableCol;
	setNewPatternCookie(Patterns, "Patterns1");
	setCookie("ClickDIS1", "jFidcxkwCJ", "/");
}

function loadPage() {
	$.ajax({
		url: "ajax.php",
		type: "POST",
		data: "requestContent=ALLREGEXES",
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
		data: "table=Regexes&act=" + ACT + "&data=" + DATA,
		success: function(data) {
			if (data === "REDOK") {
				window.location.replace("/manage/");
			} else if (data.substring(0, 3) === "NEW") {
				if (data.substring(3) === "OK") {
					var insertData = Base64.decode(DATA).split("CO:LN"), insertDataType;
					setCookie("Patterns1", Base64.encode(insertData[0] + "CO:LN" + insertData[1] + "SM;CL" + Base64.decode(getCookie("Patterns1"))), "/");
					if (insertData[0] === "A") insertDataType = "IP";
					if (insertData[0] === "B") insertDataType = "Service";
					if (insertData[0] === "C") insertDataType = "Details";
					if (insertData[0] === "D") insertDataType = "Others";
					$("label[for='newPattern']").text("WHAT IS THE PATTERN?");
					$("label[for='newNotify']").text("NOTIFY WHOM?");
					$("#newPattern").val("");
					$("#newNotify").val("");
					$("#Table1").prepend(tableCol1 + Base64.encode(insertData[0] + "CO:LN" + insertData[1]) + tableCol2 + insertDataType + tableCol3 + insertData[1] + tableCol4 + insertData[2] + tableCol5 + DATA + tableSel1 + insertData[0] + Base64.encode(insertData[1]) + "DEL" + tableSel2);
				} else {
					shakeError1("#newPattern;#newNotify","#FF0000");
				}
				setCookie("newBtnDisable1", "HskC8&slC3", "/");
				$("#newNotify").prop("disabled", false);
				$("#newPattern").prop("disabled", false);
			} else if (data.substring(0, 3) === "UPD") {
				if (data.substring(3) === "OK") {
					$("#modalBody").removeClass("Show");
					var insertData = Base64.decode(DATA).split("CO:LN"), insertDataType;
					if (insertData[0] === "A") insertDataType = "IP";
					if (insertData[0] === "B") insertDataType = "Service";
					if (insertData[0] === "C") insertDataType = "Details";
					if (insertData[0] === "D") insertDataType = "Others";
					var newPtn = Base64.decode(getCookie("Patterns1")).split("SM;CL"), newPtnI, newPattern;
					for (newPtnI = 0; newPtnI < newPtn.length; newPtnI++) {
						if (newPtn[newPtnI] === insertData[0] + "CO:LN" + insertData[1]) {
							newPattern += insertData[0] + "CO:LN" + insertData[1] + "SM;CL";
						} else {
							newPattern += newPtn[newPtnI] + "SM;CL";
						}
					}
					setCookie("Patterns1", Base64.encode(newPattern.substring(0, newPattern.length - 5)), "/");
					document.getElementById(Base64.encode(insertData[3] + "CO:LN" + insertData[1])).remove();
					$("#Table1").prepend(tableCol1 + Base64.encode(insertData[0] + "CO:LN" + insertData[1]) + tableCol2 + insertDataType + tableCol3 + insertData[1] + tableCol4 + insertData[2] + tableCol5 + DATA + tableSel1 + insertData[0] + Base64.encode(insertData[1]) + "DEL" + tableSel2);
				} else {
					shakeError1("#notify1","#FF0000");
				}
				setCookie("ClickDIS1", "jFidcxkwCJ", "/");
				$("#submitBTN_Modify").on("click");
				$("#sel2").prop("disabled", false);
				$("#notify1").prop("disabled", false);
			} else {
				if (data.substring(3) === "OK") {
					var newPtn = Base64.decode(getCookie("Patterns1")).split("SM;CL"), newPtnI, newPattern;
					for (newPtnI = 0; newPtnI < newPtn.length; newPtnI++) {
						if (newPtn[newPtnI] !== Base64.decode(DATA)) {
							newPattern += newPtn[newPtnI] + "SM;CL";
						}
					}
					setCookie("Patterns1", Base64.encode(newPattern.substring(0, newPattern.length - 5)), "/");
					document.getElementById(Base64.encode(Base64.decode(DATA))).remove();
					$("#modalBody").removeClass("Show");
				} else {
					shakeError1("#ModalCTNT","#FF0000");
				}
				setCookie("ClickDIS1", "jFidcxkwCJ", "/");
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
	var newBtnDisable12 = getCookie("newBtnDisable1");
	if (newBtnDisable12 !== "HskC8&slw3") {
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
	setCookie("newBtnDisable1", "HskC8&slC3", "/");
	$(".cell").on("click", ".fa.fa-edit", function() {
		$("#ModalTitle").text("MODIFY");
		$("#ModalCTNT").html('<div class="wrap-contact100"><form class="contact100-form"><div class="wrap-input100 input100-select"><span class="label-input100">Type</span><div><select class="selection-2" id="sel2" required><option value="A">IP</option><option value="B">Services</option><option value="C">Details</option><option value="D">Others</option></select></div><span class="focus-input100"></span></div><div class="wrap-input100"><span class="label-input100">Pattern</span><input class="input100" type="text" required id="pattern1" name="pattern"><span class="focus-input100"></span></div><div class="wrap-input100"><span class="label-input100">Notify</span><input class="input100" type="text" id="notify1" required name="notify"><span class="focus-input100"></span></div></form></div><div id="dropDownSelect1"></div>');
		$(".selection-2").select2({	minimumResultsForSearch: 20, dropdownParent: $("#dropDownSelect1") });
		var patternName = $(this).attr("id");
		var patternNames = Base64.decode(patternName).split("CO:LN");
		$("#sel2 option[value='" + patternNames[0] + "']").prop("selected", true).change();
		$("#pattern1").val(patternNames[1]);
		setCookie("Modify_P1", Base64.encode(patternNames[0] + patternNames[1]), "/", 0);
		$("#pattern1").prop('disabled', true);
		$("#notify1").val(patternNames[2]);
		if ($("#submitBTN_Modify").length == 0) $("#submitBTN_Delete").attr("id", "submitBTN_Modify");
		$("#modalBody").addClass("Show");
	});
	$("#modalBody").on("click", ".closeModal", function() {
		if (getCookie("ClickDIS1") !== "jFidc&kwCJ") {
			$("#modalBody").removeClass("Show");
		}
	});
	$(".cell").on("click", ".fa.fa-trash", function() {
		$("#ModalTitle").html("<span class='FONT_COLOR_RED'>DELETE</span>");
		var patternName = $(this).attr("id");
		var patternNames = Base64.decode(patternName.substring(1, patternName.length - 3));
		setCookie("Delete_P1", Base64.encode(patternName.substring(0, 1) + "CO:LN" + patternNames, "/", 0));
		$("#ModalCTNT").html('<br><br><p>Are you sure to <strong><br>DELETE: "<span class="FONT_COLOR_RED">' + patternNames + '</span>" @Pattern</strong>?</p>');
		if ($("#submitBTN_Delete").length == 0) $("#submitBTN_Modify").attr("id","submitBTN_Delete");
		$("#modalBody").addClass("Show");
	});
});

window.onclick = function(modalCloseEvent) {
	if (modalCloseEvent.target == document.getElementById("modalBody") && getCookie("ClickDIS1") !== "jFidc&kwCJ") {
		$("#modalBody").toggleClass("Show");
	}
}

$(document).on("keydown", "#notify1", function() { $(this).css("color", ""); });
$("#newPattern").keypress(function() { $(this).css("color", ""); });
$("#newNotify").keypress(function() { $(this).css("color", ""); });

window.setInterval(function(){
	newBtnDisable1 = getCookie("newBtnDisable1");
	if ($("#newPattern").val() != "" && $("#newNotify").val() != "" && newBtnDisable1 !== "HskC8&slw3") {
		$("#newRow1").prop("disabled", false);
		$("#newRow1").removeClass("NEW1");
		$("#newRow1").addClass("NEW2");
	} else {
		$("#newRow1").prop("disabled", true);
		$("#newRow1").removeClass("NEW2");
		$("#newRow1").addClass("NEW1");
	}
	if ($("#notify1").val() == "" || $("#pattern1").val() == "") {
		$("#submitBTN_Modify").prop("disabled", true);
		$("#submitBTN_Modify").removeClass("w_hover");
	} else {
		$("#submitBTN_Modify").prop("disabled", false);
		$("#submitBTN_Modify").addClass("w_hover");
	}
}, 512);
$(document).on("click", "#newRow1", function() {
	$("#newRow1").prop("disabled", true);
	$("#newPattern").prop("disabled", true);
	$("label[for='newPattern']").text("");
	$("#newNotify").prop("disabled", true);
	$("label[for='newNotify']").text("");
	var ERR = "NO";
	setCookie("newBtnDisable1", "HskC8&slw3", "/");
	if ($("#newPattern").val() != "" && $("#newNotify").val() != "" && illegalChar($("#newNotify").val()) && illegalChar($("#newPattern").val())) {
		if (($("#newType").val() == "A" || $("#newType").val() == "B" || $("#newType").val() == "C" || $("#newType").val() == "D")) {
			if (comparePattern($("#newType").val() + "CO:LN" + $("#newPattern").val(), "Patterns1") === true) {
				sendData("NEW", Base64.encode($("#newType").val() + "CO:LN" + $("#newPattern").val() + "CO:LN" + $("#newNotify").val() + "CO:LN" + "DNE"));
			} else {
				shakeError1("#newPattern;#newNotify","#FF0000");
				ERR = "YES";
				console.log("ERROR Code: 100202 \nPlease visit " + currentDomain + "manage/Error.php?code=100202 for more details.");
			}
		} else {
			shakeError1("#newPattern;#newNotify","#FF0000");
			ERR = "YES";
			console.log("ERROR Code: 100201 \nPlease visit " + currentDomain + "manage/Error.php?code=100201 for more details.");
		}
	} else {
		shakeError1("#newPattern;#newNotify","#FF0000");
		ERR = "YES";
		console.log("ERROR Code: 100205 \nPlease visit " + currentDomain + "manage/Error.php?code=100205 for more details.");
	}
	if (ERR === "YES") {
		setCookie("newBtnDisable1", "HskC8&slC3", "/");
		$("#newNotify").prop("disabled", false);
		$("#newPattern").prop("disabled", false);
	}
});
$(document).on("click", "#submitBTN_Modify", function() {
	$("#sel2").prop("disabled", true);
	$("#notify1").prop("disabled", true);
	$("#pattern1").prop("disabled", true);
	$("#submitBTN_Modify").off("click");
	setCookie("ClickDIS1", "jFidc&kwCJ", "/");
	var ERR = "NO";
	if ($("#notify1").val() != "" && $("#pattern1").val() != "" && illegalChar($("#pattern1").val()) && illegalChar($("#notify1").val())) {
		if (Base64.decode(getCookie("Modify_P1")).substr(1) !== $("#pattern1").val() || (comparePattern($("#sel2").val() + "CO:LN" + $("#pattern1").val(), "Patterns1") !== true && $("#sel2").val() != Base64.decode(getCookie("Modify_P1")).substr(0, 1))) {
			shakeError1("#notify1","#FF0000");
			ERR = "YES";
			console.log("ERROR Code: 100203 \nPlease visit " + currentDomain + "manage/Error.php?code=100203 for more details.");
		} else if (isKeyExist($("#pattern1").val())) {
			sendData("UPD", Base64.encode($("#sel2").val() + "CO:LN" + $("#pattern1").val() + "CO:LN" + $("#notify1").val() + "CO:LN" + Base64.decode(getCookie("Modify_P1")).substr(0, 1)));
		} else {
			shakeError1("#notify1","#FF0000");
			ERR = "YES";
			console.log("ERROR Code: 100204 \nPlease visit " + currentDomain + "manage/Error.php?code=100204 for more details.");
		}
	} else {
		shakeError1("#notify1","#FF0000");
		ERR = "YES";
		console.log("ERROR Code: 100205 \nPlease visit " + currentDomain + "manage/Error.php?code=100205 for more details.");
	}
	if (ERR === "YES") {
		setCookie("ClickDIS1", "jFidcxkwCJ", "/");
		$("#submitBTN_Modify").on("click");
		$("#sel2").prop("disabled", false);
		$("#notify1").prop("disabled", false);
	}
});
$(document).on("click", "#submitBTN_Delete", function() {
	setCookie("ClickDIS1", "jFidc&kwCJ", "/");
	$("#submitBTN_Delete").off("click");
	if (comparePattern(Base64.decode(getCookie("Delete_P1")), "Patterns1") !== true) {
		sendData("DEL", getCookie("Delete_P1"));
	} else {
		shakeError1("#ModalCTNT", "#FF0000");
		setCookie("ClickDIS1", "jFidcxkwCJ", "/");
		$("#submitBTN_Delete").on("click");
		console.log("ERROR Code: 100204 \nPlease visit " + currentDomain + "manage/Error.php?code=100204 for more details.");
	}
});

$(window).on("beforeunload", function () {
	delCookies("ClickDIS1 Delete_P1 Modify_P1 Patterns1 newBtnDisable1");
});
