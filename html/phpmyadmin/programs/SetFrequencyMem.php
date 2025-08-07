<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine sets desired frequency memory
 * 
 * It must live in the programs folder   
 */
 //return;
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$tMyRadio=$_POST["radio"];
	$tMain=$_POST['main'];
	$tSub='';//$_POST['sub'];
	$tMode=$_POST['mode'];
	$tBW=$_POST['bw'];
	$dRoot="/var/www/html";
	require_once($dRoot . "/programs/GetBand.php");
	require_once($dRoot . "/programs/sqldata.php");
	require_once($dRoot . '/classes/MysqliDb.php');
	if (strpos($tMyRadio, "UNK")==-1 || strpos($tMode,"UNK")== -1 || strpos($tMain,"UNK")== -1 || strpos($tSub, "UNK")==-1 || strpos($tBW, "UNK")== -1){
		exit;
	};
		
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$tMainBand=GetBandFromFrequency($tMain)."L";	
//	echo $tMainBand."\n";
	$data= Array(
		$tMainBand=>ltrim($tMain,"0"),
		$tMainBand."M"=>$tMode,
		$tMainBand."BW"=>$tBW
	);
	$db->where("Number",$tMyRadio);
	$db->update("FrequencyMemory",$data);
	echo "OK";	
?>