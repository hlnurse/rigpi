<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine gets a row from the Users table given a userID
 * 
 * It must live in the programs folder   
 */
function getUserField($tID,$tField){
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 'on');
	$dRoot='/var/www/html';
	require_once ($dRoot.'/programs/sqldata.php');
	require_once ($dRoot.'/classes/MysqliDb.php');
	require ($dRoot."/programs/sqldata.php");
	$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$db->where ("radio", $tID);
	$row = $db->getOne ("MySettings");
	$result = $row[$tField];
	return $result;
}
?>