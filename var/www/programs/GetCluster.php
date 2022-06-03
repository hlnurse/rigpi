<?php

/**
 * @author COMMSOFT, Inc.
 * 
 * This routine gets Cluster data given ID
 * 
 * It must live in the programs folder   
 */
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$tID=$_POST['id'];
	$tField=$_POST['field'];
	$tTable=$_POST['table'];
	$dRoot='/var/www/html';
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");	
	$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$db->where('ID',$tID);
	$row = $db->getOne($tTable);
	$data=$row[$tField];
	echo $data;


?>