<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine updates a QSO in the logbook or adds a new QSO if the id=0.
 * 
 * It must live in the programs folder   
 */
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$dRoot="/var/www/html";
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");
	require_once($dRoot."/programs/GetSettingsFunc.php");
	require_once($dRoot."/programs/GetStyleListFunc.php");
	$data=$_POST['data'];
	$id=$data['MobileID'];
	$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if (!$db){
		die("Connection failed: ".mysqli_connect_error());
	}
	if ($id>0){
		$db->where("MobileID",$id);
		if ($db->update ('Logbook', $data)){
//			echo $data['Callsign']." ($id) updated successfully.";
//		}else{
//			echo $db->getLastError();
		};
		echo $id;
		return;
	}else{
		$data['Sel']=0;
		unset($data['MobileID']);
		$id=$db->insert ('Logbook', $data);
//			echo "1 ".$db->getLastError();
//		echo $data['Callsign']." inserted successfully.";};
		echo $id;
		
	echo error_get_last();
		return;
	}
	echo error_get_last();
?>