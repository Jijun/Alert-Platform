<?php
session_start();

if (isset($_SESSION['_SLP'])) {
	$SLP = $_SESSION['_SLP'];
	if (substr($SLP, 0, 1) === "0") {
		$usrPage = "<li><a href=\"Users.php\">USERS</a></li>";
	} else {
		$usrPage = "";
	}
} else {
	setcookie("Ref.", "/manage/Regexes.php", 0, "/", $_SERVER['HTTP_HOST'], TRUE, TRUE);
	header("Location: /");
	exit;
}

// 1* errors are errors detected by the client.
// 2* errors are errors detected by the server.

$ERRORCODES_100 = "'a100201', 'a100202', 'a100203', 'a100204', 'a100205', 'a100206', 'a100211', 'a100212', 'a100214', 'a100221', 'a100222'";
$ERRORCODES_200 = "'a200201', 'a200202', 'a200203', 'a200204', 'a200205', 'a200206', 'a200207', 'a200211', 'a200212', 'a200214', 'a200221', 'a200222', 'a200223', 'a200224', 'a200225'";

$a100201e = "Please check the selected value and fill in the textbox.";
$a100201z = "请检查您的选择并且在文本框内输入。";

$a100202e = "This pattern already exists.";
$a100202z = "此匹配项已经存在。";

$a100203e = "The input value is duplicated; delete all cookies and refresh the page may help.";
$a100203z = "输入的值已经存在，删除所有cookie并刷新页面可能会有帮助。";

$a100204e = "The corresponding value does not exist; delete all cookies and refresh the page may help.";
$a100204z = "对应的值不存在，删除所有cookie并刷新页面可能会有帮助。";

$a100205e = "The input contains illegal characters.";
$a100205z = "输入的值包括非法字符。";

$a100206e = "Please check the selected value.";
$a100206z = "请检查您的选择。";

$a100211e = "Please fill in the textbox.";
$a100211z = "请在文本框内输入。";

$a100212e = "Please check the input Name, Phone, and Dingtalk Group.";
$a100212z = "请检查输入的姓名、电话和钉钉群组。";

$a100214e = &$a200214e;
$a100214z = &$a200214z;

$a100222e = &$a200225e;
$a100222z = &$a200225z;

$a100221e = "Please check the user Email.";
$a100221z = "请检查用户邮箱。";

$a200201e = "Failed to connect the database or failed to execute the SQL statement; please contact the database administrator.";
$a200201z = "无法连接数据库或不能执行SQL命令，请联系数据库管理员。";

$a200202e = "The database includes duplicate values.";
$a200202z = "数据库包含重复的值。";

$a200203e = "Unrecognized input type.";
$a200203z = "输入类型不能识别。";

$a200204e = "The contact does not exist; please add the contact first.";
$a200204z = "联系用户不存在，请首先添加联系用户。";

$a200205e = "The pattern does not exist.";
$a200205z = "匹配项不存在。";

$a200206e = "Syntax error caused by illegal values in the database; please contact the database administrator.";
$a200206z = "数据库中的非法值导致运行错误，请联系数据库管理员。";

$a200207e = "Invalid date.";
$a200207z = "非法日期。";

$a200211e = "Illegal Name, Phone, or Dingtalk Group.";
$a200211z = "非法的姓名、电话或钉钉群组。";

$a200212e = "The name does not exist.";
$a200212z = "姓名不存在。";

$a200214e = "This suit, which includes the Phone and the Dingtalk Group already exists; please choose another one."; // 100214
$a200214z = "此电话和钉钉群组搭配已经存在，请选择其他搭配。";

$a200221e = "Invalid permission.";
$a200221z = "非法权限。";

$a200222e = "Invalid Email address.";
$a200222z = "非法邮箱地址。";

$a200223e = "Failed to delete the selected value.";
$a200223z = "无法删除被选中的值。";

$a200224e = "This user does not exist.";
$a200224z = "用户不存在。";

$a200225e = "The system must have at least one administrator."; // 100222
$a200225z = "系统必须包含至少一个管理员。";

$localCSP = "Content-Security-Policy: ".
	"default-src 'self'; ".
	"style-src 'self' 'sha256-/koqFnS11cn2I66jGd6KI7qTGz+3DRixoQzZReDq7Uc=' picloud.xyz; ".
	"script-src 'self' 'sha256-0S+knJYx4IUAvS80I8a3JH45ibcNVw0H0vSyZofdzv4=' pi-314159.github.io; ".
	"font-src 'self' tpicloud.github.io rawcdn.githack.com; ".
	"img-src 'self' tpicloud.github.io";
header($localCSP);

if (isset($_POST["code"]) && $_POST["code"] !== null) {
	if (strpos($ERRORCODES_100 . $ERRORCODES_200, "'a" . $_POST["code"] . "'") === false) {
		echo "ERROR,THE ERROR CODE DOES NOT EXIST!";
	} else {
		$ERRORe = "a" . $_POST["code"] . "e";
		$ERRORz = "a" . $_POST["code"] . "z";
		echo "ERROR: " . $_POST["code"] . "," . $$ERRORe . "</p></p>" . $$ERRORz;
	}
	exit;
}
?>
<html>
<head>
<meta charset="UTF-8">
<meta name="description" content="Alert Platform's error codes.">
<meta name="keywords" content="alert platform errors,alerts errors">
<meta name="author" content="Mike">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="https://tpicloud.github.io/Image/Favicon/Alert%20Platform.ico">
<title>ERROR | Manage | Alert Platform</title>
<style>
@import url("https://picloud.xyz/Fonts/?Montserrat,400,500,600,700");

@font-face {
	font-family: "Ovo";
	font-style: normal;
	font-weight: 400;
	src: local("Ovo"), url(https://rawcdn.githack.com/tpicloud/Fonts/caa9517c0cc2faf3ddf5f62d718767267a354b6b/Others/Ovo-400.woff2) format("woff2");
	unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
@font-face {
	font-family: "楷体";
	font-style: normal;
	font-weight: 400;
	src: local("STKaiti"), url(https://rawcdn.githack.com/tpicloud/Fonts/bdd75a7bed885800c6561d37f5b9db85f6ccd4f9/FONT-4dea2719c566e670e00f592faf92cac29df8fdfb-1561344720.woff2) format("woff2");
}

body {
	background: #2cb5e8;
	background-size: cover;
	font-family: "Montserrat", sans-serif;
	opacity: 0;
	transition: 500ms opacity;
}
body.show {
	opacity: 1;
}

.ec :focus {
	outline: none;
}
.ec div {
	font-size: 100%;
	width: 18em;
	height: 4em;
	border-radius: 2em;
	margin: 10% auto 0 auto;
	background: linear-gradient(top, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0) 50%, rgba(0, 0, 0, 0.025) 51%, rgba(0, 0, 0, 0.15) 100%);
}
.ec div input {
	width: 86%;
	height: 2.5em;
	margin: 0.75em;
	border-radius: 2em;
	border: none;
	background: rgba(255, 255, 255, 0.9);
	box-shadow: inset 0 0.1em 0.1em rgba(0, 0, 0, 0.8);
	padding-left: 1em;
	color: rgba(0, 0, 0, 0.65);
	font-size: 100%;
}
.ec div input.error {
	box-shadow: inset 0 0.05em 0.1em rgba(0, 0, 0, 0.8), 0 0 0.2em rgba(150, 0, 0, 0.5);
	color: rgba(150, 0, 0, 0.5);
}
.ec div input.correct {
	box-shadow: inset 0 0.05em 0.1em rgba(0, 0, 0, 0.8), 0 0 0.2em rgba(0, 200, 0, 0.8);
	color: rgba(0, 70, 0, 0.8);
}
.ec label {
	height: 2em;
	width: 16em;
	height: 2.5em;
	border-radius: 0.25em;
	background: rgba(255, 255, 255, 0.5);
	display: block;
	line-height: 2.7em;
	text-align: center;
	margin: 10% auto -10%;
	border: 0.25em solid rgba(150, 0, 0, 0.5);
	border-radius: 0.5em;
	font-weight: bold;
	color: rgba(150, 0, 0, 0.75);
	z-index: 100;
	opacity: 0;
}
.ec label:after {
	border: solid 0.75em transparent;
	border-bottom: 0;
	border-top-color: rgba(150, 0, 0, 0.5);
	content: " ";
	display: block;
	margin: 0.05em 0 0 3em;
	overflow: hidden;
	width: 0;
	z-index: 101;
}
.ec a {
	width: 7em;
	height: 1.75em;
	margin: 1em auto;
	display: block;
	background-color: #f3e6e8;
	background-image: linear-gradient(315deg, #f3e6e8 0%, #d5d0e5 74%);
	box-shadow: 0 0.05em 0.05em rgba(0, 0, 0, 0.4);
	text-shadow: 0 0.05em rgba(255, 255, 255, 0.5);
	font-size: 150%;
	border-radius: 1.25em;
	text-align: center;
	line-height: 1.9em;
	text-decoration: none;
	color: #E61D8C;
}
.ec a:hover {
	color: #21ce43;
}
.ec a:active {
	box-shadow: inset 0 0.05em 0.05em rgba(0, 0, 0, 0.4);
	line-height: 2.1em;
}
.ec a.noEvent {
	pointer-events: none;
	color: rgba(0, 0, 0, 0.4);
}

.block {
	border: 2px solid #FF0000;
	padding: 12px 16px;
	border-radius: 25px;
	width: 72%;
	margin: 2em auto;
	text-align: justify;
	font-size: 1em;
	background-color: rgba(242, 234, 14, 0.72);
	font-family: "Ovo", "楷体", serif;
	background-image: linear-gradient(315deg, rgba(242, 234, 14, 0.72) 0%, rgba(73, 202, 209, 0.4) 74%);
}
.block .center {
	text-align: center;
	font-family: "Montserrat", sans-serif;
	color: #FF0000;
	font-size: 2em;
	font-weight: bold;
}
</style>
</head>
<body>
<div class="ec">
	<label>Failed to Validate!</label>
	<div>
		<input type="text" placeholder="Enter Your Error Code!" />
	</div>
	<a href="#" class="noEvent">SUBMIT!</a>
</div>
<p></p>
<div id="errorDetails"></div>
<script src="https://pi-314159.github.io/jQuery/jQuery-3.1.1.min.js"></script>
<script>
document.body.classList.add("show");

var ERRORCODES = [<?php echo $ERRORCODES_100 . ", " .$ERRORCODES_200 ?>];
if ($(location).attr("href").includes("?code=")) {
	var refError = $(location).attr("href").slice(-6);
	if (ERRORCODES.includes("a" + refError) !== false) {
		$("input[type='text']").val(refError);
		var ele = $("input[type='text']");
		validate(refError, ele);
	}
}

function validate(input, ele) {
	if (ERRORCODES.includes("a" + input) === false) {
		$(ele).removeClass("correct").addClass("error");
		$(".ec a").addClass("noEvent");
		showLabel();
	} else {
		$(ele).prop("disabled", true);
		$(".ec a").addClass("noEvent");
		$(ele).removeClass("error").addClass("correct");
		var postCtnt = "code=" + input;
		$.ajax({
			type: "POST",
			data: postCtnt,
			success: function(d) {
				$("#errorDetails").html("<div class='block'><p class='center'>" + d.substr(0, d.indexOf(",")) + "</p><p>" + d.substr(d.indexOf(',')+1) + "</p></div>");
				$(ele).prop("disabled", false);
			}
		});
	}
}

function showLabel() {
	$("label").animate(
	{
		opacity: 1
	}, 250);
}

function hideLabel() {
	$("label").animate(
	{
		opacity: 0
	}, 250);
}

$("input[type='text']").keyup(function () {
	if ($("input[type='text']").val().length === 6) {
		$(".ec a").removeClass("noEvent");
		$("input[type='text']").removeClass("error");
		hideLabel();
	}
});

$(".ec a").click(function (e) {
	e.preventDefault();
	var input = $("input[type='text']").val();
	var ele = $("input[type='text']");
	validate(input, ele);
});
</script>
</body>
</html>
