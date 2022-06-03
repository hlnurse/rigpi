<?php

/**
 * @author Howard Nurse W6HN
 * 
 * This routine deletes all or selected contacts from the logbook
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$whichLogName=$_POST['name'];
require_once ("/var/www/html/programs/sqldata.php");
require_once("/var/www/html/classes/MysqliDb.php");	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if (!$db){
	die("Connection failed: ".mysqli_connect_error());
}
$db->where('Logname',$whichLogName);
$db->where('ID',-1);
$db->delete('Logbook');
echo "OK";
?>