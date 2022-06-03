<?php
/**
 * @author Howard Nurse W6HN
 *
 * This function returns the call, first/last name,
 * city, state, country zip for a given callsign
 *
 * It must live in the RigPi programs folder
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require_once ("/var/www/html/programs/sqldata.php");
require_once ("/var/www/html/programs/GetCallbookFunc.php");
require_once ("/var/www/html/classes/MysqliDb.php");
//if (isset($_POST['call']))
//{
$call =$_GET['call'];
$getWhat =$_GET['what'];
$getMe=$getWhat;
$getWhat='QRZdata';
$user=2;
$result=getCallbook($call,$getWhat);
$db = new MysqliDb ('localhost', $sql_log_username, $sql_log_password, $sql_log_database);
$db->where ('User', $user);
$row = $db->getOne ("Callbook");
if ($getMe=='QRZdata'){
	if ($row) {
		$street = ucwords(strtolower($row['His_Street']));
		$city = ucwords(strtolower($row['His_City']));
		$state = $row['His_State'];
		$country = $row['His_Country'];
		$zip = $row['His_Zip'];
		$name = ucwords(strtolower($row['His_Name']));
		$data= "<b>".$call . "</b>" . "<br>" . $name . "<br>" . $street . "<br>" . $city . ", " . $state . " " . $zip . "<br>" . strtoupper($country)."\n";
		$data .= "<hr>";
		echo $data;
	} else {
		echo "$Callsign not found in Callbook.";
	}
}elseif ($getMe=='QRZpix'){
	$imageURL=$row['ImageURL'];
	echo $imageURL;
}
///}else{
//	echo "Call not specified.";
//}
?>