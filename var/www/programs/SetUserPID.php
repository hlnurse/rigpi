<?php

/**
 * @author COMMSOFT, Inc.
 * 
 * This routine sets the specified radio data from the MyRadio table in the rigs database
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
require_once '/var/www/html/programs/sqldata.php';
require_once ('/var/www/html//classes/MysqliDb.php');
$tPID=$_POST['pid'];
$tCall=$_POST['call'];
$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
if ($con->connect_error) {
	die("Connection failed: " . $con->connect_error);
}
$result = $con->query("UPDATE Users SET rigDoPID='$tPID' where MyCall='$tCall'");
echo "OK";
?>

