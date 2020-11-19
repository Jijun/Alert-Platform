<?php
namespace Tools;

// You are the man, who is selected by God, to help me fix this rusty code.

function post_test($param) {
	if (isset($param) && !empty($param)) {
		return true;
	} else {
		return false;
	}
}

function argDecode($param) {
	$param = base64_decode($param);
	return str_replace("\\n", "\n", $param);
}

class emailDomains {
	public static function validDomain($domain) {
		if (checkdnsrr($domain, "MX") || checkdnsrr($domain, "A") || checkdnsrr($domain, "AAAA")) {
			return true;
		} else {
			return false;
		}
	}

	public static function punycode($EMAIL, $VALID) {
		if (substr_count($EMAIL, "@") === 1) {
			$EMAIL = explode("@", $EMAIL);
			if ($VALID === "YES") {
				if (self::validDomain(idn_to_ascii($EMAIL[1])) && filter_var(idn_to_ascii($EMAIL[0]) . "@" . idn_to_ascii($EMAIL[1]), FILTER_VALIDATE_EMAIL)) {
					return idn_to_ascii($EMAIL[0]) . "@" . idn_to_ascii($EMAIL[1]);
				} else {
					return "False";
				}
			} else {
				return idn_to_ascii($EMAIL[0]) . "@" . idn_to_ascii($EMAIL[1]);
			}
		} else {
			if ($VALID === "YES") {
				if (self::validDomain(idn_to_ascii($EMAIL))) {
					return idn_to_ascii($EMAIL);
				} else {
					return "False";
				}
			} else {
				return idn_to_ascii($EMAIL);
			}
		}
	}
}

class IPTools {
	public static function isPublicIP($client_IP) {
		if (filter_var($client_IP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return true;
		} else {
			return false;
		}
	}

	public static function isValidIP($client_IP) {
		if (filter_var($client_IP, FILTER_VALIDATE_IP)) {
			return true;
		} else {
			return false;
		}
	}
}

class strMatch {
	public $regexPattern, $ogString;
	private $finalRegex;

	public function check_match() {
		$this->finalRegex = '|^'. str_replace('\*', '.*', preg_quote($this->regexPattern)) .'$|is';
		return preg_match($this->finalRegex, $this->ogString);
	}
}
?>
