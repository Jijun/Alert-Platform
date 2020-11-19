<?php
require ("/var/www/tools/SQL/SQL.php");
use sql as S;

function selectPINPwd($param, $email) {
	$ReadPP = new S\select1From1;
	$ReadPP->From1 = $email;
	$ReadPP->param1 = $param;
	$ReadPP->DBprepareQuery = "SELECT " . $param . " FROM Login WHERE Usr = ?";
	$ret1 = $ReadPP->return_result();
	unset($ReadPP);
	if ($ret1 === "Error.") {
		return false;
	} else {
		return $ret1;
	}
}

function select1($SQL, $param) {
	$ReadPP = new S\select1;
	$ReadPP->param = $param;
	$ReadPP->DBprepareQuery = $SQL;
	$ret1 = $ReadPP->return_result();
	unset($ReadPP);
	if ($ret1 === "Error.") {
		return false;
	} else {
		return $ret1;
	}
}

function select1Gen($param, $email, $DB, $from1) {
	$ReadPP = new S\select1From1;
	$ReadPP->From1 = $email;
	$ReadPP->param1 = $param;
	$ReadPP->DBprepareQuery = "SELECT " . $param . " FROM " . $DB . " WHERE " . $from1 . " = ?";
	$ret1 = $ReadPP->return_result();
	unset($ReadPP);
	if ($ret1 === "Error.") {
		return false;
	} else {
		return $ret1;
	}
}

function select2F1($SQL, $param1, $param2, $from) {
	$ReadPP = new S\select2From1;
	$ReadPP->From1 = $from;
	$ReadPP->param1 = $param1;
	$ReadPP->param2 = $param2;
	$ReadPP->DBprepareQuery = $SQL;
	$ret1 = $ReadPP->return_result();
	unset($ReadPP);
	if ($ret1 === "Error.") {
		return false;
	} else {
		return $ret1;
	}
}

function updatePINPwd($sql, $format, $param1, $param2, $param3, $param4, $param5) {
	$updtPP = new S\updateMax5;
	$updtPP->From1 = $param1;
	$updtPP->From2 = $param2;
	$updtPP->From3 = $param3;
	$updtPP->From4 = $param4;
	$updtPP->From5 = $param5;
	$updtPP->DBparamFormat = $format;
	$updtPP->DBprepareQuery = $sql;
	$ret1 = $updtPP->return_result();
	unset($ReadPP);
	return $ret1;
}

function select23($sql, $From1, $From2, $From3) {
	$selectInit = new S\select23;
	$selectInit->From1 = $From1;
	$selectInit->From2 = $From2;
	$selectInit->From3 = $From3;
	$selectInit->DBprepareQuery = $sql;
	$ret1 = $selectInit->return_result();
	unset($selectInit);
	return $ret1;
}

function delete2($SQL, $format, $param1, $param2) {
	$deleteInit = new S\deleteMax2;
	$deleteInit->DBprepareQuery = $SQL;
	$deleteInit->DBparamFormat = $format;
	$deleteInit->From1 = $param1;
	$deleteInit->From2 = $param2;
	$ret1 = $deleteInit->return_result();
	unset($deleteInit);
	return $ret1;
}

function counter($SQL, $param) {
	$counter = new S\count;
	$counter->DBprepareQuery = $SQL;
	$counter->param = $param;
	$ret = $counter->return_result();
	unset($counter);
	return $ret;
}

function select5($sql, $From1, $From2, $From3, $From4, $From5) {
	$selectInit = new S\select5;
	$selectInit->param1 = $From1;
	$selectInit->param2 = $From2;
	$selectInit->param3 = $From3;
	$selectInit->param4 = $From4;
	$selectInit->param5 = $From5;
	$selectInit->DBprepareQuery = $sql;
	$ret1 = $selectInit->return_result();
	unset($selectInit);
	return $ret1;
}
?>
