<?php

require ("/var/www/tools/SQL/SQL_minified.php");

$execSQL = delete2(base64_decode($argv[1]), base64_decode($argv[2]), base64_decode($argv[3]), base64_decode($argv[4]));
if ($execSQL != false) {
	echo "Success!";
} else {
	echo "Failed.";
}

?>
