<?php
date_default_timezone_set('Asia/Kolkata');

$mysql_hostname = "";
$mysql_user = "";
$mysql_password = "";
$mysql_database = ""; 

$dbh = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password,$mysql_database) or die("Opps some thing went wrong");

?>