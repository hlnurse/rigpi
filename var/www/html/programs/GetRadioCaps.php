<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns the specified radio data from the MyRadio table in the rigs database
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require ($dRoot.'/programs/GetMyRadioFunc.php');
$tMyRadio=$_POST["r"]; //radio
$tInfo=$_POST["q"]; //radio
$service_port=4532+($tMyRadio-1)*2;
require_once ($dRoot.'/classes/MysqliDb.php');	
require_once ($dRoot."/programs/sqldata.php");
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("Radio", $tMyRadio);
$row = $db->getOne ("MySettings");
$resultID = $row["ID"];
if ($tInfo=="radio"){
	$result=shell_exec("rigctl -m $resultID -u" );
}else{
	$result=shell_exec("rigctl -m $resultID -u | grep Serial" );
}
if ($result==""){
	$result="No serial Baud settings found for this radio.";
}
$result="<br>".$result."<br><br>";
$result=str_replace("\n", "<br>", $result);
echo $result;
?>