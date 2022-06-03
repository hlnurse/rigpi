<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine sets desired frequency memory
 * 
 * It must live in the programs folder   
 */
 
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$tMyRadio=$_POST["radio"];
	$tMain=$_POST['main'];
	$tSub=$_POST['sub'];
	$tMode=$_POST['mode'];
	require("GetBand.php");
	require("sqldata.php");
	require_once('../classes/MysqliDb.php');	
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$tMainBand=GetBandFromFrequency($tMain)."L";	
//	echo $tMainBand."\n";
	$data= Array(
		$tMainBand=>ltrim($tMain,"0"),
		$tMainBand."M"=>$tMode
	);
	$db->where("Number",$tMyRadio);
	$db->update("FrequencyMemory",$data);
	echo "OK";	
?>