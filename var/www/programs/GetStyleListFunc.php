<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns the log styles as a list
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
function GetLogStyles($tStyle){
	$dRoot='/var/www/html';
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");	
	$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
	if (!$db) {
		die("Connection failed: " . $db->connect_error);
	}
	$db->orderBy('Name','ASC');
	$db->setQueryOption('DISTINCT');
	$db->where('Name',$tStyle);
	$db->where('LogEditor','1');
	$data="";
	$cols = Array ("IDValue");
	$logstyles = $db->get('LogStyles',null,$cols);
	foreach($logstyles as $logstyle){
		$data=$data.'+'.$logstyle['IDValue'];
	}
	echo $data;
}



?>