<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine gets the rotor data coming from rotor
 * 
 * It must live in the programs folder   
 */
 
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$tRotor=$_POST['rotor'];
	require_once('GetBand.php');
	require ("sqldata.php");
	$sQuery = "SELECT RotorAzIn,RotorElIn from RadioInterface where Radio='$tRotor'";
	$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
	$result = $con->query($sQuery);
	if ($row = $result->fetch_assoc()){
			$data=$row['RotorAzIn'];
			if ($data==''){
				$data='--';
			}
		}else{
			$data="--";
		}
	echo $data;
?>