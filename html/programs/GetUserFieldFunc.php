<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine gets the specified field from the Users table given a username
 * 
 * It must live in the programs folder   
 */
function getUserField($tUser,$tField){
//	$tUser="admin";
//	$tField='MyCall';
	$dRoot="/var/www/html";
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 'on');
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");
	$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$db->where ("Username", $tUser);
	$row = $db->getOne ("Users");
	if ($row){
		$result = $row[$tField];
//		echo $result;
		return $result;
	}else{
		return "";
	}
}
?>
