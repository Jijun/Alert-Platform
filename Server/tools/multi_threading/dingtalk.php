<?php
// Source: https://bit.ly/3155375
//
// I am not responsible of this code.
// I know nothing about the Dingtalk.
// They made me write it, against my will.
//
// Also, I'd like to take a moment to talk about the Dingtalk.
// It is not good, it is even not bad. Calling it such would be an insult to other bad softwares, such as WeChat and Alipay.
// The API document is a mess, and the response is not formatted in the correct Content-Type.
// Oh, a kind reminder, when you are trying to send message in Markdown type, the whole thing will blow up.
// The software itself tries to retrieve as much information as possible from you and put it on the Internet.
// Working with this garbage... FOR GOD'S SAKE!!!
//
// Maybe I need to find a better job.
//
// Good luck.

include ("/var/www/tools/small_tools.php");
include ("/var/www/tools/SQL/SQL.php");

use sql as S;
use Tools as T;

class Dingtalk {
	public $message, $dest, $mobile, $all, $title;

	private $ch, $destKey, $keyVal, $data_string, $result, $messages, $messageTail;

	protected $data, $curlURL;

	private function curlReq($remote_server, $post_string) {
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_URL, $remote_server);
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json;charset=utf-8']);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		$this->data = curl_exec($this->ch);
		curl_close($this->ch);
	}

	private function sendMsg() {
		// Please refer to Line 57.
		$this->messageTail = "";
		if ($this->all === false) {
			foreach ($this->mobile as $this->messages) {
				$this->messageTail .= " @" . $this->messages;
			}
		}

		$this->data = [
			'msgtype' => 'markdown',
			'markdown' => [
				'title'=> $this->title, /* explode(',', $this->message,2)[0] === "" ? "!!Alert!!" : explode(',', $this->message,2)[0], */
				// YOU DO NOT HAVE TO ADD @ MANUALLY IF YOU ARE SENDING MESSAGE IN TEXT TYPE.
				'text' => $this->message . $this->messageTail /* explode(',', $this->message,2)[1], */
			],
			'at' => [
				'atMobiles' => $this->mobile,
				'isAtAll' => $this->all
			],
		];
		$this->data_string = json_encode($this->data);
		$this->result = $this->curlReq($this->curlURL, $this->data_string);
	}

	public function sendMsgMain() {
		foreach ($this->dest as $this->destKey => $this->keyVal) {
			$this->curlURL = "https://oapi.dingtalk.com/robot/send?access_token=" . $this->destKey;
			if (gettype($this->keyVal) === "string" && $this->keyVal === "ALL") {
				$this->mobile = "";
				$this->all = true;
			} else {
				$this->mobile = $this->keyVal;
				$this->all = false;
			}
			$this->sendMsg();
		}
	}
}

$args = array($argv[1], T\argDecode($argv[2]), T\argDecode($argv[3]), T\argDecode($argv[4]));

$ReadSQL = new S\select2From1;
$ReadSQL->param1 = "Regex";
$ReadSQL->param2 = "Send";

$mobiles = "";
foreach (array("A1","B2","C3","D4") as $type) {
	$type = str_split($type, 1);
	$ReadSQL->From1 = $type[0];
	$ReadSQL->DBprepareQuery = "SELECT Regex, Send FROM Regex WHERE Type = ?";
	$ret1 = $ReadSQL->return_result();
	$ret1Array = explode("SM;CL", $ret1);
	$matcher = new T\strMatch;
	$matcher->ogString = $args[intval($type[1]) - 1];

	foreach ($ret1Array as $ret1Arr) {
		$ret1Arr_1 = substr($ret1Arr, 0, strpos($ret1Arr, 'CO:LN'));
		$ret1Arr_2 = substr($ret1Arr, strpos($ret1Arr, 'CO:LN') + 5);

		$retArr_1Array = explode(",", $ret1Arr_1);
		foreach ($retArr_1Array as $retArr_1Arr) {
			$matcher->regexPattern = $retArr_1Arr;
			if ($matcher->check_match()) {
				if ($ret1Arr_2 === "ALL") {
					$mobiles = "ALL";
					unset($ret1Arr_1);
					unset($ret1Arr_2);
					break;
				} else {
					$mobiles .= $ret1Arr_2 . ",";
				}
			}
		}
	}
	unset($matcher);
	unset($ret1Array);
	unset($type);
	unset($ret1);
}

unset($ReadSQL);

$finalMobiles = array();
if ($mobiles !== "ALL") {
	$ReadSQL = new S\select2From1;
	$ReadSQL->param1 = "DDGroup";
	$ReadSQL->param2 = "Phone";
	$mobiles = substr($mobiles, 0, -1);
	$newMobiles = array_keys(array_flip(explode(",", $mobiles)));

	foreach ($newMobiles as $mobile) {
		$ReadSQL->DBprepareQuery = "SELECT DDGroup, Phone FROM Tel WHERE Name = ?";
		$ReadSQL->From1 = $mobile;
		$ret2 = $ReadSQL->return_result();
		$ret2Array = explode("SM;CL", $ret2);
		foreach ($ret2Array as $ret2Arr) {
			$ret2Arr_1 = substr($ret2Arr, 0, strpos($ret2Arr, 'CO:LN'));
			$ret2Arr_2 = substr($ret2Arr, strpos($ret2Arr, 'CO:LN') + 5);
			if (array_key_exists($ret2Arr_1, $finalMobiles)) {
				array_push($finalMobiles[$ret2Arr_1], $ret2Arr_2);
			} else {
				$finalMobiles[$ret2Arr_1] = array($ret2Arr_2);
			}
		}
		unset($ret2);
	}
	unset($ret2Arr_1);
	unset($ret2Arr_2);
	unset($newMobiles);
} else {
	$ReadSQL = new S\select1;
	$ReadSQL->param = "DDGroup";
	$ReadSQL->DBprepareQuery = "SELECT DDGroup FROM Tel";
	$ret2 = $ReadSQL->return_result();
	$ret2Array = explode("SM;CL", $ret2);
	foreach ($ret2Array as $ret2Arr) {
		if (!array_key_exists($ret2Arr, $finalMobiles)) {
			$finalMobiles[$ret2Arr] = "ALL";
		}
	}
	unset($ret2);
}

unset($mobiles);
unset($ReadSQL);
unset($ret2Array);

$DT = new Dingtalk;
$DT->title = $args[1];
$DT->message = T\argDecode($argv[5]);
$DT->dest = $finalMobiles;
unset($args);
unset($finalMobiles);

$DT->sendMsgMain();
unset($DT);

echo "Success!";
// I don't know why this works.
?>
