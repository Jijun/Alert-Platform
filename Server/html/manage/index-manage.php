<?php
require ("../../tools/small_tools.php");
require ("../../tools/SQL/SQL_minified.php");
use Tools as T;

if (strpos($_SERVER["REQUEST_URI"], "index-manage.php") !== false) {
	header("Location: https://www.eqxiu.com" . $_SERVER["REQUEST_URI"]);
	exit;
}

session_start();

if (!isset($_SESSION["_SLP"])) {
	setcookie("Ref.", "/manage/", 0, "/", $_SERVER["HTTP_HOST"], TRUE, TRUE);
	header("Location: /");
	exit();
} else {
	$SLP = $_SESSION['_SLP'];
	if (substr($SLP, 0, 1) === "0") {
		$usrPage = "<li><a href=\"Users.php\">USERS</a></li>";
	} else {
		$usrPage = "";
	}
}

if ($_SERVER["QUERY_STRING"] == "Logout=True" && isset($_SESSION["_SLP"])) {
	session_destroy();
	header("Location /");
	setcookie("Ref.", "/manage/", 0, "/", $_SERVER["HTTP_HOST"], TRUE, TRUE);
	exit();
}

/* Line Chart Setup */
function date_range($first, $last, $step = "+1 day", $output_format = "Y-m-d" ) {
	$dates = array();
	$current = strtotime($first);
	$last = strtotime($last);
	while ($current <= $last) {
		$dates[] = date($output_format, $current);
		$current = strtotime($step, $current);
	}
	return $dates;
}
$yesterday = date("Y-m-d", strtotime("-1 day"));
$twoWeeksAgo = date("Y-m-d", strtotime("-14 day"));
$allDates = date_range($twoWeeksAgo, $yesterday);
$returnDates = "";
$returnDateValues = "";
foreach ($allDates as $allDate) {
	$returnDates .= "'" . date("M. j", strtotime($allDate)) . "',";
	$returnDateValues .= select1("SELECT COUNT(*) AS C FROM Alerts WHERE Date LIKE '{$allDate}%'", "C") . ",";
}

$localCSP = "Content-Security-Policy: ".
	"default-src 'self'; ".
	"style-src 'self' tpicloud.github.io picloud.xyz pi-314159.github.io; ".
	"script-src 'self' 'sha256-4whvhppAdn0ouiCkD/BRqdVMwJV9UITpcML2Cuso26A=' tpicloud.github.io cdn1.tian.science pi-314159.github.io; ".
	"font-src 'self' tpicloud.github.io pi-314159.github.io; ".
	"img-src 'self' tpicloud.github.io";
header($localCSP);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description" content="Alert Platform's regexes management page.">
<meta name="keywords" content="alert platform regex,alerts regex">
<meta name="author" content="Mike">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="https://tpicloud.github.io/Image/Favicon/Alert%20Platform.ico">
<title>Manage | Alert Platform</title>
<link rel="stylesheet" type="text/css" href="https://pi-314159.github.io/CSS/select2/select2-4.0.7.min.css">
<link rel="stylesheet" type="text/css" href="https://pi-314159.github.io/jQuery/UI/1.12.1.min.css">
<link rel="stylesheet" type="text/css" href="/supplements/manage/manage.css">
<link rel="stylesheet" href="https://pi-314159.github.io/CSS/flatpickr-4.2.3.css">
</head>
<body>
<header>
	<div class="logo">
		<a href="/manage/">
			<img src="/supplements/Alert%20Platform.png" alt="Alert Platform">
		</a>
	</div>
	<span class="mobile-menu">
		<span class="line-1"></span>
		<span class="line-2"></span>
		<span class="line-3"></span>
	</span>

	<nav>
		<ul>
			<li><a href="Regexes.php">REGEXES</a></li>
			<li><a href="Contacts.php">CONTACTS</a></li>
			<?php echo $usrPage; ?>
			<li><a href="https://picloud.xyz/Others/Contact/?subject=Alert%20Platform%20Error">SUPPORT</a></li>
			<li><a href="/manage/?Logout=True"><i class="fa fa-sign-out"></i></a></li>
		</ul>
	</nav>
</header>

<div class="container3">
	<div class="grid">
		<div class="number-table basic">
			<div class="number-head">
				<h2>ALERTS</h2>
			</div>
			<div class="number-content">
				<ul>
					<li class="counter">0</li>
				</ul>
			</div>
		</div>
		<div class="number-table">
			<div class="number-head">
				<h2>PATTERNS</h2>
			</div>
			<div class="number-content">
				<ul>
					<li class="counter">0</li>
				</ul>
			</div>
		</div>
		<div class="number-table">
			<div class="number-head">
				<h2>CONTACTS</h2>
			</div>
			<div class="number-content">
				<ul>
					<li class="counter">0</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="canvasWrap">
		<canvas class="CHARTS myChartLine" id="LineC1"></canvas>
	</div>
	<div class="canvasWrap">
		<canvas class="CHARTS myChartPie" id="PieC1"></canvas>
	</div>
	<div class="tableWrap">
		<form>
			<input required type="text" id="rangeDate" placeholder="MAX Number of Rows: 500" value="" data-input>
			<button class="input-button" type="button" id="search">SEARCH</button>
		</form>
	</div>
	<div id="theAlertsTable"></div>
</div>
<script src="https://pi-314159.github.io/jQuery/jQuery-3.1.1.min.js"></script>
<script src="https://pi-314159.github.io/jQuery/UI/1.12.1.min.js"></script>
<script src="https://cdn1.tian.science/select2/select2-4.0.7.min.js"></script>
<script src="https://cdn1.tian.science/utf8_base64.js"></script>
<script src="https://tpicloud.github.io/JavaScript/Cookie.js"></script>
<script src="https://cdn1.tian.science/Chart-2.2.2.min.js"></script>
<script src="https://cdn1.tian.science/flatpickr-4.2.3.js"></script>
<script src="/supplements/manage/pagesDef.js?dates=<?php echo $returnDates; ?>&values=<?php echo $returnDateValues; ?>" id="defaultjs"></script>
<script>
document.body.classList.add("show");
var anchor1 = "";

$("#rangeDate").flatpickr({
	mode: "range",
	dateFormat: "Y-m-d",
});

function isValidDate(s) {
	var bits = s.split("-");
	var y = bits[0], m = bits[1], d = bits[2];
	var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
	if ((!(y % 4) && y % 100) || !(y % 400)) {
		daysInMonth[1] = 29;
	}
	return !(/\D/.test(String(d))) && d > 0 && d <= daysInMonth[--m]
}

function animateNum(numTo) {
	var numToI = 0, numTo = numTo.split("SM;CL");
	$(".counter").each(function (_, self) {
		jQuery({
			Counter: $(self).text()
		}).animate({
			Counter: numTo[numToI]
		}, {
			duration: 1777,
			easing: "swing",
			step: function () {
				$(self).text(Math.ceil(this.Counter));
			}
		});
		$(self).text(numTo[numToI]);
		numToI++;
	});
}
function threeNumbers() {
	$.ajax({
		url: "ajax.php",
		type: "POST",
		data: "requestPage=index&content=threeNumbers",
		success: function(data) {
			animateNum(data);
		}
	});
	setTimeout(threeNumbers, 22000);
}

threeNumbers();

Chart.defaults.global.defaultFontColor = "#ffff16";
Chart.defaults.global.defaultFontSize = 14;
Chart.defaults.global.defaultFontFamily = "Times New Roman, Times";

var returnValues = decodeURI(document.getElementById("defaultjs").src.split("dates=")[1]).split(",&values=");
var returnDates = returnValues[0].split(",");
var returnVal = returnValues[1].slice(0, -1).split(",");
function LINE() {
	var ctx1 = document.getElementById("LineC1").getContext("2d");
	var myChart1 = new Chart(ctx1, {
		type: "line",
		data: {
			labels: returnDates,
			datasets: [{
				label: "Alerts",
				data: returnVal,
				backgroundColor: "rgba(153,255,51,0.6)",
				borderColor: "#aa0841",
				fill: false,
			}]
		},
		options: {
			legend: {
				labels: {
					fontColor: "#f0f0f0"
				 }
			},
			title: {
				display: true,
				fontColor: "#edbdef",
				fontStyle: "bold",
				fontSize: 22,
				text: "Frequency of Alerts"
			},
			scales: {
				yAxes: [{
					scaleLabel: {
						display: true,
						labelString: "NUMBER OF ERRORS"
					}
				}],
				xAxes: [{
					scaleLabel: {
						display: true,
						labelString: "DATE"
					}
				}]
			}
		}
	});
}

function PIE(VAL) {
	var ctx2 = document.getElementById("PieC1").getContext("2d");
	var myChart2 = new Chart(ctx2, {
		type: "pie",
		data: {
			labels: ["IP", "Service", "Details", "Others"],
			datasets: [{
				backgroundColor: [
					"#4fff66",
					"#16d8ff",
					"#e7ff14",
					"#ff72f7"
				],
				data: VAL.split("SM;CL"),
			}]
		},
		options: {
			legend: {
				labels: {
					fontColor: "#f0f0f0"
				}
			},
			title: {
				display: true,
				fontColor: "#fff",
				fontSize: 22,
				fontStyle: "bold",
				text: "Pattern Types"
			}
		}
	});
}
function PieFour() {
	$.ajax({
		url: "ajax.php",
		type: "POST",
		data: "requestPage=index&content=PieFour",
		success: function(data) {
			if (data !== anchor1) {
				anchor1 = data;
				PIE(data);
			}
		}
	});
	setTimeout(PieFour, 22000);
}

var show1 = 0, show2 = 0;
$(window).scroll(function() {
	var scrollTop = $(window).scrollTop() + window.innerHeight, offsetTop1 = $("#LineC1").offset().top, offsetTop2 = $("#PieC1").offset().top;
	if (scrollTop > offsetTop1 && show1 === 0) {
		LINE();
		++show1;
	}
	if (scrollTop > offsetTop2 && show2 === 0) {
		PieFour();
		++show2;
	}
});

function insertTable(INPUT) {
	var rowHeader = '<div class="container-table100"><div class="wrap-table100"><div class="row header" disabled><div class="cell">IP</div><div class="cell">Service</div><div class="cell">Details</div><div class="cell">Others</div></div>';
	var col0 = '<div class="row"><div class="cell content" data-title="IP"><div class="tooltip">';
	var col1 = '<span class="tooltiptext">';
	var col2 = '</span></div></div><div class="cell content" data-title="Service">';
	var col3 = '</div><div class="cell content" data-title="Notify">';
	var col4 = '</div><div class="cell content" data-title="Details">';
	var fCol = '</div></div>';
	var INPUTS = INPUT.split("SM;CL"), I, retVal = "", i;
	for (I = 0; I < INPUTS.length; I++) {
		var inputs = INPUTS[I].split("CO:LN");
		retVal += col0 + inputs[1] + col1 + inputs[0] + col2 + inputs[2] + col3 + inputs[3] + col4 + inputs[4] + fCol;
	}
	document.getElementById("theAlertsTable").innerHTML = rowHeader + retVal + "</div></div>";
	$("#search").prop("disabled", false);
	$("#rangeDate").prop("disabled", false);
	$("#rangeDate").val("");
}

$("#search").click(function() {
	$("#search").prop("disabled", true);
	$("#rangeDate").prop("disabled", true);
	if ($("#rangeDate").val() !== "") {
		if (isValidDate($("#rangeDate").val().split(" to ")[0]) && isValidDate($("#rangeDate").val().split(" to ")[1])) {
			$.ajax({
				url: "ajax.php",
				type: "POST",
				data: "requestPage=index&date1=" + $("#rangeDate").val().split(" to ")[0] + "&date2=" + $("#rangeDate").val().split(" to ")[1],
				success: function(data) {
					if (data === "FALSE" || data === "") {
						removeDisabled();
						console.log("ERROR Code: 200207 \nPlease visit " + currentDomain + "manage/Error.php?code=200207 for more details.");
					} else {
						insertTable(data);
					}
				}
			});
		} else {
			removeDisabled();
			console.log("ERROR Code: 100206 \nPlease visit " + currentDomain + "manage/Error.php?code=100206 for more details.");
		}
	} else {
		removeDisabled();
		console.log("ERROR Code: 100206 \nPlease visit " + currentDomain + "manage/Error.php?code=100206 for more details.");
	}
	function removeDisabled() {
		document.getElementById("theAlertsTable").innerHTML = "";
		shakeError1("#rangeDate", "");
		$("#search").prop("disabled", false);
		$("#rangeDate").prop("disabled", false);
	}
});
</script>
</body>
</html>
