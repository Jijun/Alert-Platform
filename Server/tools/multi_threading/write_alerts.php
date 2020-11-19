<?php
include ("/var/www/tools/SQL/SQL.php");
use sql as S;

$CT = new S\insertAlerts;
$CT->DBprepareQuery = "INSERT INTO Alerts (IP, Service, Details, Others) VALUES (?, ?, ?, ?)";
$CT->IP = $argv[1];
$CT->Service = base64_decode($argv[2]);
$CT->Details = base64_decode($argv[3]);
$CT->Others = base64_decode($argv[4]);
$CT->return_result();
unset($CT);
echo "Success!";
?>
