<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine updates a QSO in the logbook or adds a new QSO if the id=0.
 * 
 * It must live in the programs folder   
 */
function addLogRecord($id, $data){
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	require("/var/www/html/programs/sqldata.php");
	require_once("/var/www/html/classes/MysqliDb.php");
	$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if (!$db){
		die("Connection failed: ".mysqli_connect_error());
	}
	if ($id>0 && $data['Callsign']!=null){
		$db->where("MobileID",$id);
		if ($db->update ('Logbook', Array($data))){echo $data['Callsign']." updated successfully.";};
	}else{
		$tData=$data;
		if ($db->insert ('Logbook', $tData)){echo $data['Callsign']." inserted successfully.";};
	}
}

?>