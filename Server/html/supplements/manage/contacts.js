/*
 * @author: Mike
 * Copyright 2019 Mike. All rights reserved.
 * This file is licensed under the MIT License.
 */

/* Cookies */
initCookies("ClickDIS Delete_P Modify_P Patterns PD PD_D");

function SpecificIllegalChar(INPUT, TYPE) {
	if (TYPE === "Phone") {
		if (INPUT.length != 11 || INPUT.charAt(0) != "1" || (/^\d+$/.test(INPUT)) == false) {
			return false;
		} else {
			return true;
		}
	} else if (TYPE === "Group") {
		if (INPUT.length != 64 || (/[a-f0-9]{64}/.test(INPUT)) == false) {
			return false;
		} else {
			return true;
		}
	} else {
		if (INPUT.indexOf(',') > -1) { return false; }
		if (INPUT.indexOf(',') == -1) { return true; }
	}
}

/* Loading the page. */
var tableCol1 = '<div class="row" id="'
var tableCol2 = '"><div class="cell content" data-title="Name">';
var tableCol3 = '</div><div class="cell content" data-title="Phone">';
var tableCol4 = '</div><div class="cell content p" data-title="DDGroup">';
var tableCol5 = '</div><div class="cell content" data-title="Operation"><i class="fa fa-edit" id="';
var tableSel1 = '"></i>&nbsp;&nbsp;&nbsp;<i class="fa fa-trash" id="';
var tableSel2 = '"></i></div></div>';

function insertTable(retStr) {
	var splitRes = retStr.split("SM;CL"), tableCol = "", I, PDVal = "";
	for (I = 0; I < splitRes.length; I++) {
		var splitres = splitRes[I].split("CO:LN"), splitresType;
		tableCol += tableCol1 + Base64.encode(splitres[0]) + tableCol2 + splitres[0] + tableCol3;
		tableCol += splitres[1] + tableCol4;
		Patterns += splitres[0] + "SM;CL"
		tableCol += splitres[2] + tableCol5 + Base64.encode(splitRes[I]) + tableSel1 + Base64.encode(splitres[0]) + "DEL" + tableSel2;
		PDVal += splitres[1] + "CO:LN" + splitres[2] + "SM;CL";
	}
	document.getElementById("Table1").innerHTML = tableCol;
	setCookie("PD", btoa(PDVal), "/");
	setNewPatternCookie(Patterns, "Patterns");
	setCookie("ClickDIS", "jFidcxkwCJ", "/");
}

function loadPage() {
	$.ajax({
		url: "ajax.php",
		type: "POST",
		data: "requestContent=ALLCONTACTS",
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
		data: "table=Contacts&act=" + ACT + "&data=" + DATA,
		success: function(data) {
			if (data === "REDOK") {
				window.location.replace("/manage/");
			} else if (data.substring(0, 3) === "NEW") {
				if (data.substring(3) === "OK") {
					var insertData = Base64.decode(DATA).split("CO:LN");
					setCookie("Patterns", Base64.encode(insertData[0] + "SM;CL" + Base64.decode(getCookie("Patterns"))), "/");
					setCookie("PD", btoa(insertData[1] + "CO:LN" + insertData[2] + "SM;CL" + atob(getCookie("PD"))), "/");
					$("label[for='newName']").text("NAME?");
					$("label[for='newPhone']").text("PHONE NUMBER?");
					$("label[for='newDtGroup']").text("WHICH DINGTALK GROUP?");
					$("#newName").val("");
					$("#newPhone").val("");
					$("#newDtGroup").val("");
					$("#Table1").prepend(tableCol1 + Base64.encode(insertData[0]) + tableCol2 + insertData[0] + tableCol3 + insertData[1] + tableCol4 + insertData[2] + tableCol5 + DATA + tableSel1 + Base64.encode(insertData[0]) + "DEL" + tableSel2);
				} else {
					shakeError1("#newName;#newDtGroup;#newPhone", "#FF0000");
				}
				$("#newName").prop("disabled", false);
				$("#newPhone").prop("disabled", false);
				$("#newDtGroup").prop("disabled", false);
			} else if (data.substring(0, 3) === "UPD") {
				if (data.substring(3) === "OK") {
					var insertData = Base64.decode(DATA).split("CO:LN");
					var newPtn = Base64.decode(getCookie("Patterns")).split("SM;CL"), newPtnI, newPattern;
					for (newPtnI = 0; newPtnI < newPtn.length; newPtnI++) {
						if (newPtn[newPtnI] === insertData[0]) {
							newPattern += insertData[0] + "SM;CL";
						} else {
							newPattern += newPtn[newPtnI] + "SM;CL";
						}
					}
					setCookie("Patterns", Base64.encode(newPattern.substring(0, newPattern.length - 5)), "/");
					newPtn = atob(getCookie("PD")).split("SM;CL"), newPtnI, newPattern = "";
					for (newPtnI = 0; newPtnI < newPtn.length; newPtnI++) {
						if (newPtn[newPtnI] + "SM;CL" === atob(getCookie("PD_D"))) {
							newPattern += insertData[1] + "CO:LN" + insertData[2] + "SM;CL";
						} else {
							newPattern += newPtn[newPtnI] + "SM;CL";
						}
					}
					setCookie("PD", btoa(newPattern.substring(0, newPattern.length - 5)), "/");
					document.getElementById(Base64.encode(insertData[0])).remove();
					$("#Table1").prepend(tableCol1 + Base64.encode(insertData[0]) + tableCol2 + insertData[0] + tableCol3 + insertData[1] + tableCol4 + insertData[2] + tableCol5 + DATA + tableSel1 + Base64.encode(insertData[0]) + "DEL" + tableSel2);
					$("#modalBody").removeClass("Show");
				} else {
					shakeError1("#phone1;#dtgroup1", "#FF0000");
				}
			} else {
				if (data.substring(3) === "OK") {
					var newPtn = Base64.decode(getCookie("Patterns")).split("SM;CL"), newPtnI, newPattern;
					for (newPtnI = 0; newPtnI < newPtn.length; newPtnI++) {
						if (newPtn[newPtnI] !== Base64.decode(DATA)) {
							newPattern += newPtn[newPtnI] + "SM;CL";
						}
					}
					setCookie("Patterns", Base64.encode(newPattern.substring(0, newPattern.length - 5)), "/");
					var newPtn = atob(getCookie("PD")).split("SM;CL"), newPtnI, newPattern = "";
					for (newPtnI = 0; newPtnI < newPtn.length; newPtnI++) {
						if (newPtn[newPtnI] + "SM;CL" !== atob(getCookie("PD_D"))) {
							newPattern += newPtn[newPtnI] + "SM;CL";
						}
					}
					setCookie("PD", btoa(newPattern.substring(0, newPattern.length - 5)), "/");
					document.getElementById(Base64.encode(Base64.decode(DATA))).remove();
					$("#modalBody").removeClass("Show");
				} else {
					shakeError1("#ModalCTNT","#FF0000");
				}
				setCookie("ClickDIS", "jFidcxkwCJ", "/");
				$("#submitBTN_Delete").on("click");
			}
			if (data.substring(3) !== "OK") console.log("ERROR Code: " + data.substring(3) + " \nPlease visit " + currentDomain + "manage/Error.php?code=" + data.substring(3) + " for more details.");
		}
	});
}

/* Load Page */
$(document).ready(function(){loadPage();})

$(document).ajaxComplete(function() {
	document.body.classList.add("show");
	$(".cell.p").addClass("fonts");
	$(".cell").on("click", ".fa.fa-edit", function() {
		$("#ModalTitle").text("MODIFY");
		$("#ModalCTNT").html('<div class="wrap-contact100"><form class="contact100-form"><div class="wrap-input100"><span class="label-input100">Name</span><input class="input100" type="text" required id="name1"><span class="focus-input100"></span></div><div class="wrap-input100"><span class="label-input100">Phone</span><input class="input100" type="number" required id="phone1" pattern="[1][0-9]{10}"><span class="focus-input100"></span></div><div class="wrap-input100"><span class="label-input100">Dingtalk Group</span><input class="input100" type="text" id="dtgroup1" pattern="[0-9a-f]{64}" required><span class="focus-input100"></span></div></form></div><div id="dropDownSelect1"></div>');
		patternName = Base64.decode($(this).attr("id"));
		setCookie("PD_D", btoa(patternName.substring(patternName.indexOf("CO:LN") + 5) + "SM;CL"), "/");
		var patternNames = patternName.split("CO:LN");
		$("#name1").val(patternNames[0]);
		$("#name1").prop("disabled", true);
		setCookie("Modify_P", Base64.encode(patternNames[0]), "/", 0);
		$("#phone1").val(patternNames[1]);
		$("#dtgroup1").val(patternNames[2]);
		if ($("#submitBTN_Modify").length == 0) $("#submitBTN_Delete").attr("id", "submitBTN_Modify");
		$("#modalBody").addClass("Show");
	});
	$("#modalBody").on("click", ".closeModal", function() {
		if (getCookie("ClickDIS") !== "jFidc&kwCJ") {
			$("#modalBody").removeClass("Show");
		}
	});
	$(".cell").on("click", ".fa.fa-trash", function() {
		var existPD = Base64.decode($(this).prev().attr("id"));
		setCookie("PD_D", btoa(existPD.substring(existPD.indexOf("CO:LN") + 5) + "SM;CL"), "/");
		$("#ModalTitle").html("<span class='FONT_COLOR_RED'>DELETE</span>");
		patternName = $(this).attr("id");
		setCookie("Delete_P", Base64.encode(Base64.decode(patternName.substring(0, patternName.length - 3))), "/", 0);
		patternName = Base64.decode(patternName.substring(0, patternName.length - 3));
		$("#ModalCTNT").html('<br><br><p>Are you sure to <strong><br>DELETE: "<span class="FONT_COLOR_RED">' + patternName + '</span>" @Name</strong>?</p>');
		if ($("#submitBTN_Delete").length == 0) $("#submitBTN_Modify").attr("id","submitBTN_Delete");
		$("#modalBody").addClass("Show");
	});
});

window.onclick = function(modalCloseEvent) {
	if (modalCloseEvent.target == document.getElementById("modalBody") && getCookie("ClickDIS") !== "jFidc&kwCJ") {
		$("#modalBody").toggleClass("Show");
	}
}

$(document).on("keydown", "#name1", function() { $(this).css("color", ""); });
$(document).on("keydown", "#phone1", function() { $(this).css("color", ""); });
$(document).on("keydown", "#dtgroup1", function() { $(this).css("color", ""); });
$("#newPhone").keypress(function() { $(this).css("color", ""); });
$("#newName").keypress(function() { $(this).css("color", ""); });
$("#newDtGroup").keypress(function() { $(this).css("color", ""); });

window.setInterval(function(){
	if ($("#newName").val() != "" && $("#newPhone").val() != "" && $("#newDtGroup").val() != "") {
		$("#newRow1").prop("disabled", false);
		$("#newRow1").removeClass("NEW1");
		$("#newRow1").addClass("NEW2");
	} else {
		$("#newRow1").prop("disabled", true);
		$("#newRow1").removeClass("NEW2");
		$("#newRow1").addClass("NEW1");
	}
	if ($("#name1").val() == "" || $("#phone1").val() == "" || $("#dtgroup1").val() == "") {
		$("#submitBTN_Modify").prop("disabled", true);
		$("#submitBTN_Modify").removeClass("w_hover");
	} else {
		$("#submitBTN_Modify").prop("disabled", false);
		$("#submitBTN_Modify").addClass("w_hover");
	}
}, 512);
$(document).on("click", "#newRow1", function() {
	var ERR = "NO";
	$("#newRow1").prop("disabled", true);
	$("#newName").prop("disabled", true);
	$("label[for='newName']").text("");
	$("#newPhone").prop("disabled", true);
	$("label[for='newPhone']").text("");
	$("#newDtGroup").prop("disabled", true);
	$("label[for='newDtGroup']").text("");
	if ($("#newDtGroup").val() != "" && $("#newPhone").val() != "" && $("#newName").val() != "") {
		if (illegalChar($("#newDtGroup").val()) === false || illegalChar($("#newPhone").val()) === false || illegalChar($("#newName").val()) === false) {
			shakeError1("#newName;#newDtGroup;#newPhone", "#FF0000");
			ERR = "YES";
			console.log("ERROR Code: 100205 \nPlease visit " + currentDomain + "manage/Error.php?code=100205 for more details.");
		} else if (SpecificIllegalChar($("#newDtGroup").val(), "Group") === false || SpecificIllegalChar($("#newPhone").val(), "Phone") === false || SpecificIllegalChar($("#newName").val(), "Name") === false) {
			shakeError1("#newDtGroup;#newPhone", "#FF0000");
			ERR = "YES";
			console.log("ERROR Code: 100212 \nPlease visit " + currentDomain + "manage/Error.php?code=100212 for more details.");
		} else {
			if (Base64.decode(getCookie("PD")).includes($("#newPhone").val() + "CO:LN" + $("#newDtGroup").val() + "SM;CL")) {
				shakeError1("#newName", "#FF0000");
				ERR = "YES";
				console.log("ERROR Code: 100214 \nPlease visit " + currentDomain + "manage/Error.php?code=100214 for more details.");
			} else if (comparePattern($("#newName").val(), "Patterns") === true) {
				sendData("NEW", Base64.encode($("#newName").val() + "CO:LN" + $("#newPhone").val() + "CO:LN" + $("#newDtGroup").val()));
			} else {
				shakeError1("#newName", "#FF0000");
				ERR = "YES";
				console.log("ERROR Code: 100203 \nPlease visit " + currentDomain + "manage/Error.php?code=100203 for more details.");
			}
		}
	} else {
		shakeError1("#newName;#newDtGroup;#newPhone","#FF0000");
		ERR = "YES";
		console.log("ERROR Code: 100211 \nPlease visit " + currentDomain + "manage/Error.php?code=100211 for more details.");
	}
	if (ERR === "YES") {
		$("#newName").prop("disabled", false);
		$("#newPhone").prop("disabled", false);
		$("#newDtGroup").prop("disabled", false);
	}
});
$(document).on("click", "#submitBTN_Modify", function() {
	var ERR = "NO";
	$("#dtgroup1").prop("disabled", true);
	$("#name1").prop("disabled", true);
	$("#phone1").prop("disabled", true);
	$("#submitBTN_Modify").off("click");
	setCookie("ClickDIS", "jFidc&kwCJ", "/");
	if (illegalChar($("#name1").val()) === false || illegalChar($("#phone1").val()) === false || illegalChar($("#dtgroup1").val()) === false) {
		shakeError1("#phone1;#dtgroup1", "#FF0000");
		ERR = "YES";
		console.log("ERROR Code: 100205 \nPlease visit " + currentDomain + "manage/Error.php?code=100205 for more details.");
	} else if (SpecificIllegalChar($("#dtgroup1").val(), "Group") === false || SpecificIllegalChar($("#name1").val(), "Name") === false || SpecificIllegalChar($("#phone1").val(), "Phone") === false) {
		shakeError1("#phone1;#dtgroup1", "#FF0000");
		ERR = "YES";
		console.log("ERROR Code: 100212 \nPlease visit " + currentDomain + "manage/Error.php?code=100212 for more details.");
	} else {
		if (Base64.decode(getCookie("Modify_P")) !== $("#name1").val()) {
			shakeError1("#phone1;#dtgroup1", "#FF0000");
			ERR = "YES";
			console.log("ERROR Code: 100204 \nPlease visit " + currentDomain + "manage/Error.php?code=100204 for more details.");
		} else if (Base64.decode(getCookie("PD")).includes($("#phone1").val() + "CO:LN" + $("#dtgroup1").val() + "SM;CL")) {
			if (atob(getCookie("PD_D")) !== $("#phone1").val() + "CO:LN" + $("#dtgroup1").val() + "SM;CL") {
				shakeError1("#newName", "#FF0000");
				console.log("ERROR Code: 100214 \nPlease visit " + currentDomain + "manage/Error.php?code=100214 for more details.");
			}
			ERR = "YES";
		} else {
			sendData("UPD", Base64.encode($("#name1").val() + "CO:LN" + $("#phone1").val() + "CO:LN" + $("#dtgroup1").val()));
		}
	}
	if (ERR === "YES") {
		setCookie("ClickDIS", "jFidcxkwCJ", "/");
		$("#submitBTN_Modify").on("click");
		$("#phone1").prop("disabled", false);
		$("#dtgroup1").prop("disabled", false);
	}
});
$(document).on("click", "#submitBTN_Delete", function() {
	setCookie("ClickDIS", "jFidc&kwCJ", "/");
	$("#submitBTN_Delete").off("click");
	if (comparePattern(Base64.decode(getCookie("Delete_P")), "Patterns") !== true) {
		sendData("DEL", getCookie("Delete_P"));
	} else {
		shakeError1("#ModalCTNT", "#FF0000");
		setCookie("ClickDIS", "jFidcxkwCJ", "/");
		$("#submitBTN_Delete").on("click");
		console.log("ERROR Code: 100204 \nPlease visit " + currentDomain + "manage/Error.php?code=100204 for more details.");
	}
});

$(window).on("beforeunload", function () {
	delCookies("ClickDIS Delete_P Modify_P Patterns PD_D");
});
