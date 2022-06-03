<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine retruns count of Q's in logbook.
 * 
 * It must live in the programs folder   
 */
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$dRoot='/var/www/html';
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");
	$tLog=$_POST['Logname'];
	$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if (!$db){
		die("Connection failed: ".mysqli_connect_error());
	}
	if ($tLog=="ALL Logs"){
	}else{
		if (strlen($tLog)>0){
			$db->where('Logname',$tLog);
		}
	}
	$tRowsCount=$db->getValue('Logbook','count(*)');
	echo $tRowsCount;
?>