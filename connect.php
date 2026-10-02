<?php

$db_hostname = "localhost";
$db_dbname = "3ohda";
$user = "root";
$password = "";

$db_server = mysql_connect($db_hostname, $user, $password);
mysql_set_charset('utf8', $db_server);
if (!$db_server) {
    die("Unable to connect to MySQL: " . mysql_error());
}
mysql_select_db($db_dbname) or die("Unable to select database: " . mysql_error());

if (session_id() == '') {
    @session_start();
}

$activity_user = isset($_SESSION['3ohda_username']) ? $_SESSION['3ohda_username'] : 'guest';
$activity_action = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'CLI';
$activity_file = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : 'unknown';
$activity_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$activity_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

$activity_user = mysql_real_escape_string($activity_user, $db_server);
$activity_action = mysql_real_escape_string($activity_action, $db_server);
$activity_file = mysql_real_escape_string($activity_file, $db_server);
$activity_uri = mysql_real_escape_string($activity_uri, $db_server);
$activity_ip = mysql_real_escape_string($activity_ip, $db_server);

@mysql_query("INSERT INTO activity_log
    (username, action, file_name, request_uri, ip_address)
    VALUES ('$activity_user', '$activity_action', '$activity_file', '$activity_uri', '$activity_ip')", $db_server);




// $options = array(
// PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
// );
// try{
// 	$db = new PDO($dsn,$user,$password,$options);	
// 	echo "martyr DB Successfuly connected";
// }
// catch(PDOException $e){
// 	echo "failed" . $e->getMessage();
// }
