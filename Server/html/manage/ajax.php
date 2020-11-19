<?php
include ("../../tools/SQL/SQL_minified.php");
include ("../../tools/small_tools.php");
include ("../../tools/email/lib.php");

use Tools as T;

session_start();

if (!isset($_SESSION["_SLP"])) {
	setcookie("Ref.", "/manage/", 0, "/", $_SERVER['HTTP_HOST'], TRUE, TRUE);
	echo "REDOK";
	exit;
} else {
	$SLP = $_SESSION["_SLP"];
	$Permission = substr($SLP, 0, 1);
	$Assignee = substr($SLP, 1);
}

if (isset($_POST["requestPage"]) && $_POST["requestPage"] === "index") {
	if (isset($_POST["content"]) && $_POST["content"] === "threeNumbers") {
		echo counter("SELECT (SELECT count(*) FROM Alerts) AS ALT, (SELECT count(*) FROM Regex) AS REG, (SELECT count(*) FROM Tel) AS TEL","ALT,REG,TEL");
	} elseif (isset($_POST["content"]) && $_POST["content"] === "PieFour") {
		echo counter("SELECT COUNT(IF(Type='A',1,null)) AS A, COUNT(IF(Type='B',1,null)) AS B, COUNT(IF(Type='C',1,null)) AS C, COUNT(IF(Type='D',1,null)) AS D FROM Regex","A,B,C,D");
	} elseif (isset($_POST["date1"]) && isset($_POST["date2"])) {
		$date1 = explode("-", $_POST["date1"]);
		$date2 = explode("-", $_POST["date2"]);
		$DATE1 = $_POST["date1"];
		$DATE2 = $_POST["date2"];
		if (sizeof($date1) === 3 && sizeof($date2) === 3 && checkdate($date1[1], $date1[2], $date1[0]) && checkdate($date2[1], $date2[2], $date2[0])) {
			echo select5("SELECT Date, IP, Service, Details, Others FROM Alerts WHERE Date BETWEEN '{$DATE1}' AND '{$DATE2}' LIMIT 500", "Date", "IP", "Service", "Details", "Others");
		} else {
			echo "FALSE"; /* Error: 200207 */
		}
	} else {
		header("Location: https://www.eqxiu.com" . $_SERVER['REQUEST_URI']);
		exit;
	}
} elseif (isset($_POST["requestContent"])) {
	if ($_POST["requestContent"] === "ALLREGEXES") {
		echo select23("SELECT Type, Regex, Send FROM Regex", "Type", "Regex", "Send");
	} elseif ($_POST["requestContent"] === "ALLCONTACTS") {
		echo select23("SELECT Name, Phone, DDGroup FROM Tel", "Name", "Phone", "DDGroup");
	} elseif ($_POST["requestContent"] === "ALLUSERS") {
		if ($Permission === "0") {
			echo select23("SELECT Usr, Permission FROM Login", "Usr", "Permission", "");
		} else {
			header("Location: https://www.eqxiu.com" . $_SERVER['REQUEST_URI']);
			exit;
		}
	}
} elseif ($_POST["table"] && $_POST["table"] === "Regexes" && $_POST["act"] && $_POST["act"] !== "" && $_POST["data"] && $_POST["data"] !== "") {
	$regexData = explode("CO:LN", base64_decode($_POST["data"]));
	$act = $_POST["act"];
	if ($act === "NEW" || $act === "UPD") {
		$retType = select1Gen("Type", $regexData[1], "Regex", "Regex");
		if ($retType !== false) {
			$retType = explode("SM;CL", $retType);
			foreach ($retType as $retTypeI) {
				if ($retTypeI === $regexData[0] && $retTypeI !== $regexData[3]) {
					echo $act . "200202";
					exit;
				}
			}
		}
		if ($regexData[0] !== "A" && $regexData[0] !== "B" && $regexData[0] !== "C" && $regexData[0] !== "D") {
			echo $act . "200203";
		} else {
			$regexContact = explode(",", $regexData[2]);
			foreach ($regexContact as $regexContactI) {
				$contactLegal = select1Gen("Phone", $regexContactI, "Tel", "Name");
				if ($contactLegal === false && $regexContactI !== "ALL") {
					echo $act . "200204";
					exit;
				}
			}
			if ($act === "NEW") {
				$SQL = updatePINPwd("INSERT INTO Regex (Type, Regex, Send, Assignee) VALUES (?, ?, ?, ?)", "ssss", $regexData[0], $regexData[1], $regexData[2], $Assignee, "");
			} else {
				$SQL = updatePINPwd("Update Regex SET Type=?, Send=?, Assignee=? WHERE Type=? AND Regex=?", "sssss", $regexData[0], $regexData[2], $Assignee, $regexData[3], $regexData[1]);
			}
			if ($SQL) {
				echo $act . "OK";
			} else {
				echo $act . "200201";
			}
		}
	} else {
		$retType = select1Gen("Type", $regexData[1], "Regex", "Regex");
		if ($retType !== false) {
			$retType = explode("SM;CL", $retType);
			$retTypeNum = 0;
			foreach ($retType as $retTypeI) {
				if ($retTypeI === $regexData[0]) {
					++$retTypeNum;
				}
			}
			if ($retTypeNum === 1) {
				if (delete2("DELETE FROM Regex WHERE Type=? AND Regex=?", "ss", $regexData[0], $regexData[1])) {
					echo "DELOK";
				} else {
					echo "DEL200201";
				}
			} else {
				echo "DEL200206";
			}
		} else {
			echo "DEL200205";
		}
	}
} elseif ($_POST["table"] && $_POST["table"] === "Contacts" && $_POST["act"] && $_POST["act"] !== "" && $_POST["data"] && $_POST["data"] !== "") {
	$regexData = explode("CO:LN", base64_decode($_POST["data"]));
	$act = $_POST["act"];
	if ($act === "NEW" || $act === "UPD") {
		if (!strpos($regexData[0], ",") || strlen($regexData[1]) === 11 || strlen($regexData[2]) === 64 || preg_match("/^[1][0-9]{10}/", $regexData[1]) || preg_match("/^[0-9a-f]{64}/", $regexData[2])) {
			$retType2 = 0;
			$retType = explode("SM;CL", select23("SELECT Name, Phone, DDGroup FROM Tel", "Name", "Phone", "DDGroup"));
			foreach ($retType as $retTypes) {
				if (explode("CO:LN", $retTypes, 2)[1] === $regexData[1] . "CO:LN" . $regexData[2]) {
					echo $act . "200214";
					exit;
				} else {
					if (explode("CO:LN", $retTypes, 2)[0] == $regexData[0]) {
						++$retType2;
					}
				}
			}
			if ($retType2 === 1 && $act === "UPD") {
				$SQL = updatePINPwd("UPDATE Tel SET Phone=?, DDGroup=?, Assignee=? WHERE Name=?", "ssss", $regexData[1], $regexData[2], $Assignee, $regexData[0], "");
			} elseif ($retType2 === 0) {
				$SQL = updatePINPwd("INSERT INTO Tel (Name, Phone, DDGroup, Assignee) VALUES (?, ?, ?, ?)", "ssss", $regexData[0], $regexData[1], $regexData[2], $Assignee, "");
			} else {
				echo $act . "200202";
				exit;
			}
			if ($SQL) {
				echo $act . "OK";
			} else {
				echo $act . "200201";
			}
		} else {
			echo $act . "200211";
		}
	} else {
		$retType = select1Gen("Phone", $regexData[0], "Tel", "Name");
		if ($retType !== false) {
			if (1 === sizeof(explode("SM;CL", $retType))) {
				if (delete2("DELETE FROM Tel WHERE Name=?", "s", $regexData[0], "") && delete2("DELETE FROM Regex WHERE Send=?", "s", $regexData[0], "")) {
					$retRS = explode("SM;CL", select2F1("SELECT Regex, Send FROM Regex WHERE Send LIKE ?", "Regex", "Send", "%{$regexData[0]}%"));
					if (sizeof($retRS) > 1) {
						foreach ($retRS as $retRSs) {
							$retrss = explode("CO:LN", $retRSs);
							$newRetRSs = str_replace("," . $regexData[0], "", str_replace($regexData[0] . ",", "", $retrss[1]));
							if (!updatePINPwd("UPDATE Regex SET Send=? WHERE Regex=?", "ss", $newRetRSs, $retrss[0], "", "", "")) {
								echo "DEL200201";
								exit;
							}
						}
					}
					echo "DELOK";
				} else {
					echo "DEL200201";
				}
			} else {
				echo "DEL200206";
			}
		} else {
			 echo "DEL200212";
		}
	}
} elseif ($_POST["table"] && $_POST["table"] === "Users" && $_POST["act"] && $_POST["act"] !== "" && $_POST["data"] && $_POST["data"] !== "" && $Permission === "0") {
	$Data = base64_decode($_POST["data"]);
	$act = $_POST["act"];
	if ($act === "NEW") {
		list($permission, $email) = array(substr($Data, -1), substr($Data, 0, -1));
		$retType = select1Gen("Permission", $email, "Login", "Usr");
		if ($retType !== false) {
			echo "NEW200202";
		} else {
			if ($permission !== "0" && $permission !== "1") {
				echo "NEW200221";
			} else {
				if (substr_count($email, "@") !== 1 || substr_count($email, ".") === 0 || strlen($email) < 5 || substr_count($email, "..") !== 0 || substr_count($email, ".@") !== 0 || substr_count($email, "@.") !== 0 || T\emailDomains::punycode($email, "YES") === "False") {
					echo "NEW200222";
				} else {
					$newPIN = shell_exec("/var/www/tools/randNum 1000 9999") . "1";
					$p1 = base64_encode("INSERT INTO Login(Usr, Pwd, PIN, Permission, Assignee) VALUES (?,?,?,?,?)");
					$p2 = base64_encode("ssiis");
					$p3 = base64_encode($email);
					$p4 = base64_encode("ERROR::NEED TO RESET PASSWORD");
					$p5 = base64_encode($newPIN);
					$p6 = base64_encode($permission);
					$p7 = base64_encode($Assignee);
					$PHPexec = "\"php /var/www/tools/SQL/exec/updatePP.php '" . $p1 . "' '" . $p2 . "' '" . $p3 . "' '" . $p4 . "' '" . $p5 . "' '" . $p6 . "' '" . $p7 . "'\"";
					$EMAILexec = "\"/var/www/tools/email/sendEmail.py '" . $p3 . "' '" . base64_encode('NEW ACCOUNT—Alert Platform') . "' '" . newUsr($email) . "'\"";
					$preEXEC = "/var/www/tools/multi_threading/main " . $EMAILexec . " " . $PHPexec;
					shell_exec($preEXEC);
					echo "NEWOK";
				}
			}
		}
	} elseif ($act === "UPD") {
		list($permission, $email, $oldP) = array(substr($Data, -2, -1), substr($Data, 0, -2), substr($Data, -1));
		$retType = select1Gen("Permission", $email, "Login", "Usr");
		if ($retType !== false && $retType === $oldP) {
			if ($permission !== "0" && $permission !== "1") {
				echo "UPD200221";
			} else {
				if (substr_count($email, "@") !== 1 || substr_count($email, ".") === 0 || strlen($email) < 5 || substr_count($email, "..") !== 0 || substr_count($email, ".@") !== 0 || substr_count($email, "@.") !== 0 || T\emailDomains::punycode($email, "YES") === "False") {
					echo "UPD200222";
				} else {
					if ($permission === "0" || $oldP === "1") {
						if (updatePINPwd("UPDATE Login SET Permission=?, Assignee=? WHERE Usr=?", "iss", $permission, $Assignee, $email, "", "")) {
							echo "UPDOK";
						} else {
							echo "UPD200201";
						}
					} else {
						$perm = 0;
						$Perm = explode("SM;CL", select1("SELECT Permission FROM Login", "Permission"));
						foreach ($Perm as $perms) {
							if ($perms === "0") {
								++$perm;
							}
						}
						if ($perm > 1) {
							if (updatePINPwd("UPDATE Login SET Permission=?, Assignee=? WHERE Usr=?", "iss", $permission, $Assignee, $email, "", "")) {
								if ($Assignee === $email) {
									$_SESSION["_SLP"] = "1" . $Assignee;
									echo "REDOK";
								} else {
									echo "UPDOK";
								}
							} else {
								echo "UPD200201";
							}
						} else {
							echo "UPD200225";
						}
					}
				}
			}
		} else {
			 echo "UPD200224";
		}
	} else {
		list($permission, $email) = array(substr($Data, -1), substr($Data, 0, -1));
		$retType = select1Gen("Permission", $email, "Login", "Usr");
		if ($retType === $permission) {
			if ($permission === "1") {
				$p1 = base64_encode("DELETE FROM Login WHERE Usr=? AND Permission=?");
				$p2 = base64_encode("si");
				$p3 = base64_encode($email);
				$p4 = base64_encode($permission);
				$PHPexec = "\"php /var/www/tools/SQL/exec/delete.php '" . $p1 . "' '" . $p2 . "' '" . $p3 . "' '" . $p4 . "'\"";
				$EMAILexec = "\"/var/www/tools/email/sendEmail.py '" . $p3 . "' '" . base64_encode('ACCOUNT NOTIFICATION—Alert Platform') . "' '" . delUsr($email) . "'\"";
				$EXEC = "/var/www/tools/multi_threading/main " . $EMAILexec . " " . $PHPexec;
				$ret = shell_exec($EXEC);
				if ($ret === "Success!") {
					echo "DELOK";
				} else {
					echo "DEL200201";
				}
			} elseif ($permission === "0") {
				$perm = 0;
				$Perm = explode("SM;CL", select1("SELECT Permission FROM Login", "Permission"));
				foreach ($Perm as $perms) {
					if ($perms === "0") {
						++$perm;
					}
				}
				if ($perm > 1) {
					$p1 = base64_encode("DELETE FROM Login WHERE Usr=? AND Permission=?");
					$p2 = base64_encode("si");
					$p3 = base64_encode($email);
					$p4 = base64_encode($permission);
					$PHPexec = "\"php /var/www/tools/SQL/exec/delete.php '" . $p1 . "' '" . $p2 . "' '" . $p3 . "' '" . $p4 . "'\"";
					$EMAILexec = "\"/var/www/tools/email/sendEmail.py '" . $p3 . "' '" . base64_encode('ACCOUNT NOTIFICATION—Alert Platform') . "' '" . delUsr($email) . "'\"";
					$EXEC = "/var/www/tools/multi_threading/main " . $EMAILexec . " " . $PHPexec;
					$ret = shell_exec($EXEC);
					if ($ret === "Success!") {
						if ($Assignee === $email) {
							unset($_SESSION["_SLP"]);
							echo "REDOK";
						} else {
							echo "DELOK";
						}
					} else {
						echo "DEL200201";
					}
				} else {
					echo "DEL200225";
				}
			} else {
				echo "DEL200206";
			}
		} else {
			echo "DEL200224";
		}
	}
} else {
	header("Location: https://www.eqxiu.com" . $_SERVER['REQUEST_URI']);
	exit;
}
?>
