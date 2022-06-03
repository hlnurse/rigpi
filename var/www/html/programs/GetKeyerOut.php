<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine gets the keyer data
 * 
 * It must live in the programs folder   
 */
 
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$tField=$_POST['field'];
	$tRadio=$_POST['radio'];
	require ("/var/www/html/programs/sqldata.php");
	$sQuery = "SELECT $tField from Keyer where Radio='$tRadio'";
	$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
	$result = $con->query($sQuery);
	if ($row = $result->fetch_assoc()){
		$data=$row[$tField];
	}else{
		$data="NG";
	}
	echo $data;
?>