<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine gets desired frequency memory
 * 
 * It must live in the programs folder   
 */
 
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	require_once ('/var/www/html/classes/MysqliDb.php');	
	require ("sqldata.php");
	$tBand=$_POST['band'];
	$tMyRadio=$_POST['radio'];
	$tWhere=strlen($tBand)-1;
	$tMode=substr($tBand, 0,$tWhere)."LM";
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$db->where("Number",$tMyRadio);
	$row = $db->getOne("FrequencyMemory");
	$data=array($row[$tBand],$row[$tMode]);
	echo '["' . implode('", "', $data) . '"]';
?>