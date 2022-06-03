<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine updates data for a field in Logbook
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$tField=$_POST['field'];
$tData=$_POST['data'];
$tRec=$_POST['record'];
$tFil=$_POST['filter'];
//$tRadio=$_POST['radio'];
require_once '/var/www/html/programs/sqldata.php';
require_once '/var/www/html/classes/MysqliDb.php';	
require_once '/var/www/html/programs/GetSettingsFunc.php';	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
$find="";
if ($tRec>0){
	$db->where('MobileID',$tRec);
}
if ($tFil){
	$db->where('Callsign',$tFil);
}
//$tLogName=getField($tUserNumber,'LogName','MySettings');
//$db->where('Logname',$tLogName);
$data = Array (
	$tField => $tData
);
if ($db->update ('Logbook', $data)){
    echo $db->count . ' records were updated';
}else{
    echo 'update failed: ' . $db->getLastError();
}

?>