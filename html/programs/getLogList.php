<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns the modes for a list
 * 
 * It must live in the programs folder   
 */
function getLogNames(){
	ini_set('error_reporting', E_ALL);
 	ini_set('display_errors', 1);
	$dRoot='/var/www/html';
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");	
//	$str2 = "SELECT DISTINCT ID,Name from Modes";
	$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
	if (!$db) {
		die("Connection failed: " . $db->connect_error);
	}
	$db->orderBy('Logname','ASC');
//	$db->setQueryOption('DISTINCT');
	$data="";
	$i=0;
	$cols = Array ("Logname");
	$lognames = $db->get('Logbook',null,$cols);
	$data=$data . "<div class='mylog'><li><a class='dropdown-item' id='l$i' href='#'>" . 'ALL Logs' . "</a></li></div>\n";
	foreach($lognames as $logname){
		if ($logname['Logname']!== 'ALL Logs'){
			$data=$data . "<div class='mylog'><li><a class='dropdown-item' id='l$i' href='#'>" . $logname['Logname'] . "</a></li></div>\n";
		}
		$i=$i+1;
	}
	return $data;
}
?>
