<?php
function isAuthorAvailable() {
	$author = array("name"=>"Mike", "contact"=>"bit.ly/2MoSi40");
	echo "Author: " . $author["name"] . "\nConact: " . $author["contact"] . "\nI AM NOT AVAILABLE!!!\n";
	unset($author);
	return false;
}

if (strpos($_SERVER["REQUEST_URI"], "default_eqxiu.php") !== false) {
	header("Location: https://www.eqxiu.com" . $_SERVER['REQUEST_URI']);
	exit;
} elseif (strpos($_SERVER["REQUEST_URI"], "resetopcache=true") !== false) {
	opcache_reset();
	header("Location: /");
	exit;
}

session_start();

if (isset($_SESSION["_SLP"])) {
	header("Location: /manage/");
	exit;
}


require ("../tools/small_tools.php");

use Tools as T;

/*
 * Following Codes Are Designed for the Login Page
 */

include ("../tools/email/lib.php");
include ("../tools/SQL/SQL_minified.php");

if(isset($_COOKIE["Ref."])) {
	$successRedirect = $_COOKIE["Ref."];
} else {
	$successRedirect = "/manage/";
}

function sendUpdate($SQL, $ptn, $PIN, $ACC, $subj, $emailMsg, $others) {
	$updateSQL = base64_encode($SQL);
	$updatePtn = base64_encode($ptn);
	$updateParam1 = base64_encode($PIN);
	$updateParam2 = base64_encode($ACC);
	$updateParam3 = base64_encode($others);
	$emailTo = base64_encode($ACC);
	if ($others != "" && $others != " ") {
		$emailTo = base64_encode($others);
	}
	$PHPexec = "\"php /var/www/tools/SQL/exec/updatePP.php '" . $updateSQL . "' '" . $updatePtn . "' '" . $updateParam1 . "' '" . $updateParam2 . "' '" . $updateParam3 . "' '' ''\"";
	$EMAILexec = "\"/var/www/tools/email/sendEmail.py '" . $emailTo . "' '" . base64_encode($subj) . "' '" . $emailMsg . "'\"";
	$preEXEC = "/var/www/tools/multi_threading/main " . $EMAILexec . " " . $PHPexec;
	shell_exec($preEXEC);
}

if (isset($_POST["usr"]) && isset($_POST["pwd"])) {
	$retPwd = selectPINPwd("Pwd", base64_decode($_POST["usr"]));
	if ($retPwd === false) {
		echo "ERROR";
	} else {
		if ($retPwd === "ERROR::NEED TO RESET PASSWORD") {
			echo "ERRRR";
		} elseif (intval(substr($retPwd, 40, 1)) > 8) {
			echo "ERRRR";
			shell_exec("/var/www/tools/email/sendEmail.py '" . $_POST["usr"] . "' '" . base64_encode("SECURITY ALERT (PASSWORD)—Alert Platform") . "' '" . wrongPwd($_SERVER['REMOTE_ADDR']) . "'");
		} elseif (intval(substr($retPwd, 40, 1)) == 8) {
			if  (substr($retPwd, 0, 40) == sha1($_POST["pwd"])) {
				updatePINPwd("UPDATE Login SET Pwd = ? WHERE Usr = ?", "ss", substr($retPwd, 0, 40) . "0", base64_decode($_POST["usr"]), "", "", "");
				$retPms = selectPINPwd("Permission", base64_decode($_POST["usr"]));
				$_SESSION['_SLP'] = $retPms . base64_decode($_POST["usr"]);
				echo "200OK";
			} else {
				echo "ERRRR";
				sendUpdate("UPDATE Login SET Pwd = ? WHERE Usr = ?", "ss", "ERROR::NEED TO RESET PASSWORD", base64_decode($_POST["usr"]), "SECURITY ALERT (PASSWORD)—Alert Platform", wrongPwd($_SERVER['REMOTE_ADDR']), "");
			}
		} elseif (sha1($_POST["pwd"]) === substr($retPwd, 0, 40)) {
			$retPms = selectPINPwd("Permission", base64_decode($_POST["usr"]));
			$_SESSION['_SLP'] = $retPms . base64_decode($_POST["usr"]);
			updatePINPwd("UPDATE Login SET Pwd = ? WHERE Usr = ?", "ss", substr($retPwd, 0, 40) . "0", base64_decode($_POST["usr"]), "", "", "");
			echo "200OK";
		} else {
			echo "ERROR";
			updatePINPwd("UPDATE Login SET Pwd = ? WHERE Usr = ?", "ss", substr($retPwd, 0, 40) . strval(intval(substr($retPwd, 40, 1)) + 1), base64_decode($_POST["usr"]), "", "", "");
		}
	}
	exit;
}

if (isset($_POST["ACC"]) && !isset($_POST["PIN"])) {
	$retPIN = selectPINPwd("PIN", base64_decode($_POST["ACC"]));
	if ($retPIN === false) {
		echo "ERROR";
	} else {
		$newPIN = shell_exec("/var/www/tools/randNum 1000 9999");
		$updatePIN = $newPIN . "1";
		sendUpdate("UPDATE Login SET PIN = ? WHERE Usr = ?", "is", $updatePIN, base64_decode($_POST["ACC"]), "Password Reset PIN—Alert Platform", newPIN($newPIN), "");
		echo "200OK";
	}
	exit;
}

if (isset($_POST["ACC"]) && isset($_POST["PIN"])) {
	$retPIN = selectPINPwd("PIN", base64_decode($_POST["ACC"]));
	if ($retPIN === false) {
		echo "ERRRR";
	} else {
		if (intval(substr($retPIN, 4, 1)) > 3) {
			echo "ERRRR";
			shell_exec("/var/www/tools/email/sendEmail.py '" . $_POST["ACC"] . "' '" . base64_encode("SECURITY ALERT (PIN ERROR)—Alert Platform") . "' '" . wrongPIN($_SERVER['REMOTE_ADDR']) . "'");
		} elseif (intval(substr($retPIN, 4, 1)) == 3) {
			if  (substr($retPIN, 0, 4) == base64_decode($_POST["PIN"])) {
				echo "200OK";
			} else {
				echo "ERRRR";
				$updatePIN = substr($retPIN, 0, 4) . strval(intval(substr($retPIN, 4, 1)) + 1);
				sendUpdate("UPDATE Login SET PIN = ? WHERE Usr = ?", "is", $updatePIN, base64_decode($_POST["ACC"]), "SECURITY ALERT (PIN ERROR)—Alert Platform", wrongPIN($_SERVER['REMOTE_ADDR']), "");
			}
		} elseif (substr($retPIN, 0, 4) == base64_decode($_POST["PIN"])) {
			echo "200OK";
		} else {
			echo "ERROR";
			$updatePIN = substr($retPIN, 0, 4) . strval(intval(substr($retPIN, 4, 1)) + 1);
			updatePINPwd("UPDATE Login SET PIN = ? WHERE Usr = ?", "is", $updatePIN, base64_decode($_POST["ACC"]), "", "", "");
		}
	}
	exit;
}

if (isset($_POST["A"]) && isset($_POST["P"]) && isset($_POST["P1"]) && isset($_POST["P2"])) {
	$retPIN = selectPINPwd("PIN", base64_decode($_POST["A"]));
	if ($retPIN === false || intval(substr($retPIN, 4, 1)) > 3 || substr($retPIN, 0, 4) != base64_decode($_POST["P"])) {
		echo "ERRRR";
	} elseif  ($_POST["P1"] == $_POST["P2"]) {
		$newPIN = shell_exec("/var/www/tools/randNum 1000 9999") . "1";
		$newPWD = sha1($_POST["P1"]) . "0";
		sendUpdate("UPDATE Login SET PIN = ?, Pwd=? WHERE Usr = ?", "iss", $newPIN, $newPWD, "Password Update—Alert Platform", newPwd($_SERVER['REMOTE_ADDR']), base64_decode($_POST["A"]));
		$retPms = selectPINPwd("Permission", base64_decode($_POST["A"]));
		$_SESSION['_SLP'] = $retPms . base64_decode($_POST["A"]);
		echo "200OK";
	} else {
		echo "ERROR";
	}
	exit;
}

/*
 * END
 */

interface originVerification {
	public function oV_result();
}

class IPVerification implements originVerification {
	public $client_IP = "8.8.8.8";

	public static function Redirect() {
		header("Location: http://www.eqxiu.com/?from=alert_platform", true, 302);
		exit();
	}

	public function oV_result() {
		if (T\IPTools::isPublicIP($this->client_IP)) { // Who cares?
			self::Redirect(); // Yes, I do.
		} else {
			return true;
		}
	}
}

class UAVerification implements originVerification {
	public $Browser = "Uh-oh";
	private $UA = "Mozilla/5.0 EQXIUAlertPlatform/1.0.0 (KHTML, like Gecko) Author/Mike";

	public function oV_result() {
		if ($Browser !== $UA) {
			IPVerification::Redirect();
		} else {
			return true;
		}
	}
}

setlocale(LC_CTYPE, "en_US.UTF-8");

define("Multi_Threading", "/var/www/tools/multi_threading/main ");
define("DingDing", '"php /var/www/tools/multi_threading/dingtalk.php" ');
define("WriteAlerts", '"php /var/www/tools/multi_threading/write_alerts.php" "');

$UA = "";
$UA = $_SERVER['HTTP_USER_AGENT'];
if (isset($_POST["Service"]) && T\post_test($_POST["Service"]) && T\post_test($_POST["IP"]) && T\post_test($_POST["Details"]) && T\post_test($_POST["Others"]) && T\post_test($_POST["MSG"])) { // Yes, I know this is stupid because `quality = salary / number of lines`.
	$UAverify = new UAVerification;
	$UAverify->Browser = $UA;
	unset($UAverify);

	if (!T\IPTools::isValidIP($_POST["IP"])) {
		IPVerification::Redirect();
	}

	list($alertIP, $alertService, $alertDetails, $alertOthers, $alertMSG) = array($_POST["IP"], base64_encode($_POST["Service"]), base64_encode($_POST["Details"]), base64_encode($_POST["Others"]), base64_encode($_POST["MSG"]));

	$retVal = shell_exec(Multi_Threading . DingDing . WriteAlerts . $alertIP . '" "' . $alertService . '" "' . $alertDetails . '" "' . $alertOthers . '" "' . $alertMSG . '"');

	if ($retVal === "Success!") {
		echo "ok";
		exit();
	} else {
		echo "error";
		exit();
	}
} else {
	$IPverify = new IPVerification;
	$IPverify->client_IP = $_SERVER['REMOTE_ADDR'];
	unset($IPverify);
	$LocalCSP = "Content-Security-Policy: " .
		"default-src 'self'; " .
		"style-src 'self' tpicloud.github.io picloud.xyz pi-314159.github.io; " .
		"script-src 'self' tpicloud.github.io cdn1.tian.science pi-314159.github.io; " .
		"font-src 'self' tpicloud.github.io pi-314159.github.io; " .
		"img-src 'self' tpicloud.github.io";
	header($LocalCSP);
	echo '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description" content="Login to the alert platform.">
<meta name="keywords" content="alerts,alert,alert platform">
<meta name="author" content="Mike">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="https://tpicloud.github.io/Image/Favicon/Alert%20Platform.ico">
<title>Log In | Alert Platform</title>
<link rel="stylesheet" href="https://pi-314159.github.io/Font-Awesome/CSS/font-awesome.min.css">
<link rel="stylesheet" href="https://pi-314159.github.io/jQuery/UI/1.12.1.min.css">
<link rel="stylesheet" href="/supplements/default.css">
</head>
<body>
<div class="formContainer">
	<div class="signin">
		<form>
			<div class="logo">
				<i class="fa fa-user-circle-o"></i>
			</div>
			<div class="input-group" id="usr_email_group">
				<i class="fa fa-user" id="usr_email_icon"></i>
				<input type="email" id="usr_email" placeholder="Email" required>
			</div>
			<div class="input-group" id="usr_password_group">
				<i class="fa fa-unlock-alt" id="usr_password_icon"></i>
				<input type="password" id="usr_password" placeholder="Password" required>
			</div>
			<button id="login" type="submit">Log In</button>
		</form>
		<a href="#" id="resetpass">Forgot Password?</a>
	</div>
	<div class="resetpass">
		<form>
			<div class="logo">
				<i class="fa fa-user-circle-o"></i>
			</div>
			<div class="input-group" id="resetEmailGroup">
				<i class="fa fa-user" id="resetEmailIcon"></i>
				<input id="resetEmail" type="email" placeholder="Email" required>
			</div>
			<div class="input-group" id="PINGroup">
				<i id="PINIcon" class="fa fa-key"></i>
				<input id="thePIN" pattern="[0-9]{4}" type="number" placeholder="PIN" required disabled>
				<button type="button" class="button1" disabled>
					<span class="submit1">Get PIN</span>
					<span class="loading1"><i class="fa fa-refresh"></i></span>
					<span class="check1"><i class="fa fa-check"></i></span>
					<span class="close1"><i class="fa fa-close"></i></span>
				</button>
			</div>
			<div class="input-group" id="resetPassGroup1">
				<i id="resetPassIcon1" class="fa fa-unlock-alt"></i>
				<input id="resetPass1" type="password" placeholder="Password" required disabled>
			</div>
			<div class="input-group" id="resetPassGroup2">
				<i id="resetPassIcon2" class="fa fa-unlock-alt"></i>
				<input id="resetPass2" type="password" placeholder="Retype Password" required disabled>
			</div>
			<button type="submit" id="resetPwd" disabled>Reset</button>
		</form>
	</div>
</div>
<script src="https://pi-314159.github.io/jQuery/jQuery-3.1.1.min.js"></script>
<script src="https://pi-314159.github.io/jQuery/UI/1.12.1.min.js"></script>
<script src="https://cdn1.tian.science/utf8_base64.js"></script>
<script src="/supplements/default.js?redirect=' . $successRedirect . '" id="defaultjs"></script>
</body>
</html>
';

}

?>
