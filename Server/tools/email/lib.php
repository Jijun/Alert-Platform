<?php

function newUsr($ACC) {
	$emailContent = base64_encode("<p>Hello,</p><br><p style='text-align:center;font-size:1.6em;font-weight:bold;color:#911572'>CONGRATULATIONS!</p><p>The administrator invited you to join this platform. Here is your login information: </p><p style='font-size:1.2em;color:#0f7026'>Account: <strong>" . $ACC . "</strong></p><p>If this is your first time logging in, or if you have forgotten your username or password, you will need to request a new password.</p><p>If you have any questions, please ask your administrator for help.</p><br><p style='text-align:left'>Platform Operator<br>Mike</p>");
	return $emailContent;
}

function delUsr($ACC) {
	$emailContent = base64_encode("<p>Hello,</p><br><p style='text-align:center;font-size:1.6em;font-weight:bold;color:#911572'>THANK YOU FOR USING THIS PLATFORM!</p><p>Your administrator has deleted your account: </p><p style='font-size:1.2em;color:#0f7026'>Account: <strong>" . $ACC . "</strong></p><p>If you have any questions, please ask your administrator for help.</p><br><p style='text-align:left'>Platform Operator<br>Mike</p>");
	return $emailContent;
}

function wrongPwd($IP) {
	$emailContent = base64_encode('<p>Hello,</p><br><p>We have detected the <span style="font-weight:bold;color:#FF0000">SUSPICIOUS</span> activity on your account. </p><br><p>Details: Someone repeatedly enters the wrong password.</p><p>IP: ' . $IP . ';</p><p>Date: ' . date('Y M d H:i:s', time()) . ';</p><br><p>Your password has <strong>EXPIRED</strong> and you need to set a new password by clicking the "FORGOT PASSWORD?" on the sign-in page.</p><br><p style="text-align:left">Trust &amp; Safety<br>Mike</p>');
	return $emailContent;
}

function newPIN($PIN) {
	$emailContent = base64_encode("<p>Hello,</p><br><p>The PIN to reset your password is: </p><p style='text-align:center;font-size:2.2em;font-weight:bold;color:#0f7026'>" . $PIN . "</p><p>Please note that this PIN will expire if you fail to properly authenticate after three attempts.</p><p>If you didn't ask to reset your password, you can ignore this email and no action will be taken; your account will remain secure.</p><br><p style='text-align:left'>Trust &amp; Safety<br>Mike</p>");
	return $emailContent;
}

function wrongPIN($IP) {
	$emailContent = base64_encode("<p>Hello,</p><br><p>We have detected the <span style='font-weight:bold;color:#FF0000'>SUSPICIOUS</span> activity on your account. </p><br><p>Details: Someone repeatedly types the wrong PIN when attempting to change the password.</p><p>IP: " . $IP . ";</p><p>Date: " . date('Y M d H:i:s', time()) . ";</p><br><p>If the activity was you, you can request a replacement PIN. If the activity was NOT you, you can ignore this email and no action will be taken; your account will remain secure.</p><p>If you receive a second email saying that your password was reset, you should immediately secure your account.</p><br><p style='text-align:left'>Trust &amp; Safety<br>Mike</p>");
	return $emailContent;
}

function newPwd($IP) {
	$emailContent = base64_encode("<p>Hello,</p><br><p>We are confirming that…</p><p style='text-align:center;font-size:1.6em;font-weight:bold;color:#911572'>You have changed your password successfully!<br><span style='font-size:10px;font-weight:normal'>" . $IP . " on " . date('Y M d H:i:s', time()) . "</span></p><p>If you didn't ask to reset your password, you should immediately secure your account and notify your administrator.</p><br><p style='text-align:left'>Trust &amp; Safety<br>Mike</p>");
	return $emailContent;
}

?>
