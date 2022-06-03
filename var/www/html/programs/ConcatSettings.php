<?php

/**
 * @author Howard Nurse, W6HN
 *
 * This routine concatenates data in a field with additional data
 *
 * It must live in the programs folder
 */
$tField = $_POST["field"];
$tRadio = $_POST["radio"];
$tData = $_POST["data"];
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
require "/var/www/html/programs/sqldata.php";
$con = new mysqli(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
// echo "tdata: " . $tData . "\n";
$con->query("LOCK TABLE RadioInterface WRITE;");
$sQuery = "UPDATE RadioInterface SET $tField = CONCAT($tField, '$tData') WHERE Radio='$tRadio'";
//$sQuery1 = "UPDATE RadioInterface SET CWInUncleared = CONCAT(CWInUncleared, '$tData') WHERE Radio='$tRadio'";
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}
$con->query($sQuery);
//$con->query($sQuery1);
echo $sQuery; //  $result = $con->query($sQuery1);
$con->query("UNLOCK TABLES;");
/*	$tField=$_POST['field'];
	$tRadio=$_POST['radio'];
	$tData=$_POST['data'];
	require ("sqldata.php");
	$sQuery="update RadioInterface set $tField = concat($tField, '$tData') where Radio='$tRadio'";
	$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$result = $con->query($sQuery);
	echo $sQuery;
*/
?>
