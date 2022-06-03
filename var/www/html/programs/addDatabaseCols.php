<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine checks to see if table has required fields.  If not they are added.
 * 
 * It must live in the programs folder   
 */
 ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
require_once("/var/www/html/programs/sqldata.php");
require_once ('/var/www/html/classes/MysqliDb.php');	
$sql="ALTER TABLE MySettings ADD COLUMN PTTCAT VARCHAR(1) NOT NULL AFTER RTS, ADD COLUMN PTTMode VARCHAR(1) NOT NULL DEFAULT 0 AFTER RTS, ADD COLUMN PTTCmd VARCHAR(20) NOT NULL AFTER RTS";
$test="SHOW COLUMNS FROM `MySettings` LIKE 'PTTCAT'";
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
$result = $db->rawQuery($test);
$exists = ($db->count)?TRUE:FALSE;
if ($exists==FALSE){
	$temp=$db->rawQuery($sql);
}else{
	echo "Field PTTCAT already intalled.\n";
}
$sql="ALTER TABLE Keyer ADD COLUMN WKFunction VARCHAR(4) NOT NULL DEFAULT 0, ADD COLUMN WKRemotePort VARCHAR(6) NOT NULL DEFAULT 0, ADD COLUMN WKRemoteIP VARCHAR(50) NOT NULL";
$test="SHOW COLUMNS FROM `Keyer` LIKE 'WKFunction'";
$result = $db->rawQuery($test);
$exists = ($db->count)?TRUE:FALSE;
if ($exists==FALSE){
	$temp=$db->rawQuery($sql);
}else{
	echo "Field WKFunction already intalled.\n";
}
$sql="ALTER TABLE RadioInterface ADD COLUMN Transmit VARCHAR(1) NOT NULL DEFAULT 0";
$test="SHOW COLUMNS FROM `RadioInterface` LIKE 'Transmit'";
$result = $db->rawQuery($test);
$exists = ($db->count)?TRUE:FALSE;
if ($exists==FALSE){
	$temp=$db->rawQuery($sql);
}else{
	echo "Field Transmit already intalled.\n";
}
?>