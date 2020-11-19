<?php
namespace sql;

// Please pay special attention to the sequence.

interface result {
	public function return_result();
}

class sqlInfo {
	const DBserverName = "xxxxxxxx";
	const DBname = "xxxxxxxx";

	protected static $DBusr = "xxxxxxxx";
	protected static $DBpwd = "xxxxxxxx";
	protected static $conn;

	protected $stmt;

	public $DBTable, $DBprepareQuery, $DBparamFormat;

	protected static function connectSQL() {
		self::$conn = mysqli_connect(self::DBserverName, self::$DBusr, self::$DBpwd, self::DBname) or die("Failed to connect.");
		mysqli_set_charset(self::$conn, "utf8");
	}
}

class sqlGeneral extends sqlInfo implements result {
	// UNLESS THERE IS AN EMERGENCY, DO NOT USE THIS.
	// THIS IS NOT A SAFE WAY TO QUERY DATA.
	public function __construct() {
		self::connectSQL();
	}

	public function return_result() {
		if (mysqli_query(self::$conn, $this->DBprepareQuery)) {
			return true;
		} else {
			die("Failed: " . mysqli_error(self::$conn));
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}

class insertAlerts extends sqlInfo implements result {
	public function __construct() {
		self::connectSQL();
	}

	public $IP, $Service, $Details, $Others;

	public function return_result() {
		if ($this->stmt = mysqli_prepare(self::$conn, $this->DBprepareQuery)) {
			mysqli_stmt_bind_param($this->stmt, "ssss", $this->IP, $this->Service, $this->Details, $this->Others);
			mysqli_stmt_execute($this->stmt);
			mysqli_stmt_close($this->stmt);
			return true;
		} else {
			return false;
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}

class select1 extends sqlInfo implements result {
	public function __construct() {
		self::connectSQL();
	}

	public $param;

	protected $retVal, $result, $row;

	public function return_result() {
		$this->result = mysqli_query(self::$conn, $this->DBprepareQuery);
		if (mysqli_num_rows($this->result) > 0) {
			$this->retVal = "";
			while($this->row = mysqli_fetch_assoc($this->result)) {
				$this->retVal .= $this->row[$this->param] . "SM;CL";
			}
			$this->retVal = rtrim($this->retVal, "SM;CL");
			return $this->retVal;
		} else {
			return("Error.");
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}

class select1From1 extends sqlInfo implements result {
	public function __construct() {
		self::connectSQL();
	}

	public $From1, $param1;

	protected $retVal;

	public function return_result() {
		if ($this->stmt = mysqli_prepare(self::$conn, $this->DBprepareQuery)) {
			mysqli_stmt_bind_param($this->stmt, "s", $this->From1);
			mysqli_stmt_execute($this->stmt);
			mysqli_stmt_bind_result($this->stmt, $this->param1);
			$this->retVal = "";
			while (mysqli_stmt_fetch($this->stmt)) {
				$this->retVal .= $this->param1 . "SM;CL";
			}
			$this->retVal = substr($this->retVal, 0, -5);
			mysqli_stmt_close($this->stmt);
			return $this->retVal;
		} else {
			die("Error.");
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}

class select2From1 extends sqlInfo implements result {
	public function __construct() {
		self::connectSQL();
	}

	public $From1, $param1, $param2;

	protected $retVal;

	public function return_result() {
		if ($this->stmt = mysqli_prepare(self::$conn, $this->DBprepareQuery)) {
			mysqli_stmt_bind_param($this->stmt, "s", $this->From1);
			mysqli_stmt_execute($this->stmt);
			mysqli_stmt_bind_result($this->stmt, $this->param1, $this->param2);
			$this->retVal = "";
			while (mysqli_stmt_fetch($this->stmt)) {
				$this->retVal .= $this->param1 . "CO:LN" . $this->param2 . "SM;CL";
			}
			$this->retVal = substr($this->retVal, 0, -5);
			mysqli_stmt_close($this->stmt);
			return $this->retVal;
		} else {
			die("Error.");
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}

class updateMax5 extends sqlInfo implements result {
	public function __construct() {
		self::connectSQL();
	}

	public $From1, $From2, $From3, $From4, $From5;

	public function return_result() {
		if ($this->stmt = mysqli_prepare(self::$conn, $this->DBprepareQuery)) {
			if (!isset($this->From3) || $this->From3 == "") {
				mysqli_stmt_bind_param($this->stmt, $this->DBparamFormat, $this->From1, $this->From2);
			} elseif (!isset($this->From4) || $this->From4 == "") {
				mysqli_stmt_bind_param($this->stmt, $this->DBparamFormat, $this->From1, $this->From2, $this->From3);
			} elseif (!isset($this->From5) || $this->From5 == "") {
				mysqli_stmt_bind_param($this->stmt, $this->DBparamFormat, $this->From1, $this->From2, $this->From3, $this->From4);
			} else {
				mysqli_stmt_bind_param($this->stmt, $this->DBparamFormat, $this->From1, $this->From2, $this->From3, $this->From4, $this->From5);
			}
			mysqli_stmt_execute($this->stmt);
			return true;
		} else {
			die("Error.");
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}

class select23 extends sqlInfo implements result {
	public function __construct() {
		self::connectSQL();
	}

	public $Form1, $From2, $From3;

	protected $retVal, $result, $row;

	public function return_result() {
		$this->result = mysqli_query(self::$conn, $this->DBprepareQuery);
		if (mysqli_num_rows($this->result) > 0) {
			$this->retVal = "";
			if (!isset($this->From3) || $this->From3 == "") {
				while($this->row = mysqli_fetch_assoc($this->result)) {
					$this->retVal .= $this->row[$this->From1] . "CO:LN" . $this->row[$this->From2] . "SM;CL";
				}
			} else {
				while($this->row = mysqli_fetch_assoc($this->result)) {
					$this->retVal .= $this->row[$this->From1] . "CO:LN" . $this->row[$this->From2] . "CO:LN" . $this->row[$this->From3] . "SM;CL";
				}
			}
			$this->retVal = substr($this->retVal, 0, -5);
			return $this->retVal;
		} else {
			die("Error.");
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}


class select5 extends sqlInfo implements result {
	public function __construct() {
		self::connectSQL();
	}

	public $param1, $param2, $param3, $param4, $param5;

	protected $retVal, $result, $row;

	public function return_result() {
		$this->result = mysqli_query(self::$conn, $this->DBprepareQuery);
		if (mysqli_num_rows($this->result) > 0) {
			$this->retVal = "";
			while($this->row = mysqli_fetch_assoc($this->result)) {
				$this->retVal .= $this->row[$this->param1] . "CO:LN" . $this->row[$this->param2] . "CO:LN" . $this->row[$this->param3] . "CO:LN" . $this->row[$this->param4] . "CO:LN" . $this->row[$this->param5] . "SM;CL";
			}
			$this->retVal = ($this->retVal === "") ? "" : substr($this->retVal, 0, -5);
			return $this->retVal;
		} else {
			return "FALSE";
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}

class deleteMax2 extends sqlInfo implements result {
	public function __construct() {
		self::connectSQL();
	}

	public $From1, $From2;

	public function return_result() {
		if ($this->stmt = mysqli_prepare(self::$conn, $this->DBprepareQuery)) {
			if (!isset($this->From2) || $this->From2 == "") {
				mysqli_stmt_bind_param($this->stmt, $this->DBparamFormat, $this->From1);
			} else {
				mysqli_stmt_bind_param($this->stmt, $this->DBparamFormat, $this->From1, $this->From2);
			}
			mysqli_stmt_execute($this->stmt);
			mysqli_stmt_close($this->stmt);
			return true;
		} else {
			return false;
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}

class count extends sqlInfo implements result {
	public function __construct() {
		self::connectSQL();
	}

	public $param;

	protected $retVal, $result, $row, $PARAM, $params;

	public function return_result() {
		$this->result = mysqli_query(self::$conn, $this->DBprepareQuery);
		if (mysqli_num_rows($this->result) > 0) {
			$this->retVal = "";
			$this->row = mysqli_fetch_assoc($this->result);
			$this->params = explode(",", $this->param);
			if (mysqli_num_rows($this->result) === 1) {
				foreach ($this->params as $this->PARAM) {
					$this->retVal .= $this->row[$this->PARAM] . "SM;CL";
				}
			} elseif (sizeof($this->params) === 2) {
				while($this->row = mysqli_fetch_assoc($this->result)) {
					$this->retVal .= $this->row[$this->params[0]] . "CO:LN" . $this->row[$this->params[1]] . "SM;CL";
				}
			}
			$this->retVal = rtrim($this->retVal, "SM;CL");
			return $this->retVal;
		} else {
			return("Error.");
		}
	}

	public function __destruct() {
		mysqli_close(self::$conn);
	}
}

// Now, we all know this is stupid.
// $wasted_hours > 24;
?>
