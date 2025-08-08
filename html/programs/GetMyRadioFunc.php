<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns the radio data
 * 
 * It must live in the programs folder   
 */
 
function myRadio($radio, $field){
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$dRoot="/var/www/html";
	require ($dRoot."/programs/sqldata.php");
	$str2 = "SELECT $field from MySettings where Radio = '$radio'";
	
	$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
	$result = $con->query($str2);
	$data="";
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()){
			$data=$row["$field"];
		}
	}
	return $data;
}

function GetIn($radio) {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$dRoot="/var/www/html";
	$tRadio=$radio;
	require ($dRoot."/programs/sqldata.php");
	$sQuery = "SELECT * from RadioInterface where Radio='$tRadio'";
	$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
	$result = $con->query($sQuery);
	echo mysqli_error($con);
	if ($row = $result->fetch_assoc()){
			$data=$row['MainIn'] . "+" . $row['SubIn'] . "+" . $row['ModeIn'] . "+" . $row['SplitIn'] . "+" . $row['SMeterIn'] . "+" . $row['PTTIn'];
		}else{
			$data="NG";
		}
//		echo $data[1];
	return $data;
	}

function GetRadioField($radio,$field) {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$dRoot="/var/www/html";
	echo $dRoot;
	$tRadio=$radio;
	require ($dRoot."/programs/sqldata.php");
	$sQuery = "SELECT $field from RadioInterface where Radio='$tRadio'";
	$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
	$result = $con->query($sQuery);
	echo mysqli_error($con);
	if ($row = $result->fetch_assoc()){
			$data=$row["$field"];
		}else{
			$data="NG";
		}
	return $data;
	}

function setPHPVar($var){
	$tMyRadioModel=$var;
}

?>