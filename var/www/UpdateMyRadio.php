<?php

/**
 * @author COMMSOFT, Inc.
 * 
 * This routine returns the radio data
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
//echo $sql_radio_password;
function getRadioManufacturers(){
	$sql_radio_username="";
	$sql_radio_password="";
	$sql_radio_database="";

	require "/var/www/html/programs/sqldata.php";
	$str2 = "SELECT DISTINCT MANUFACTURER from Radios ORDER BY MANUFACTURER";
	$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
	$result = $con->query($str2);
	$data="";
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()){
			$man=$row["MANUFACTURER"];
			$data=$data . "<div class='myman' id='$man'><li><a class='dropdown-item' id='$man' href='#'>" . $man . "</a></li></div>\n\r";
		}
	}
	return $data;
}



?>