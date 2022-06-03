<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns the styles for a list
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
function getLogStyles(){
	$sql_radio_username="";
	$sql_radio_password="";
	$sql_radio_database="";
	$dRoot='/var/www/html';
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");	
	$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
	if (!$db) {
		die("Connection failed: " . $db->connect_error);
	}
	$db->orderBy('Name','ASC');
	$db->setQueryOption('DISTINCT');
	$data="";
	$i=0;
	$cols = Array ("Name");
	$logstyles = $db->get('LogStyles',null,$cols);
	foreach($logstyles as $logstyle){
		$data=$data . "<div class='mystyle'><li><a class='dropdown-item' id='s$i' href='#'>" . $logstyle['Name'] . "</a></li></div>\n";
		$i=$i+1;
	}
	return $data;
}


?>