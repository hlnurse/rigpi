<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine checks the specified username and password, adding them to db if new.
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$mycallsign=strtoupper($_GET['c1']); //my call
$mypassword=$_GET['p1']; //my password
$find="";
$find = "SELECT MyCall, Password from Personal";
require_once("/var/www/html/programs/sqldata.php");
require_once ('/var/www/html/classes/MysqliDb.php');	
$db = new MysqliDb("localhost", $sql_personal_username, $sql_personal_password, $sql_personal_database);

$row = $db->getOne ("Personal");
if ($row) {
	$tCall=$row["MyCall"];
	if ($tCall=="demo"){
		$data=array('MyCall'=>$mycallsign,'Password'=>md5($mypassword));
		if ($db->update ('Personal', $data)){
			echo "RigPi has added user $mycallsign with password $mypassword.\n";
		}
	}elseif ($mycallsign=="DEMO"){
		echo "DEMO mode";
	}elseif ($tCall==$mycallsign){
		if ($row["Password"]==md5($mypassword)){
			echo "Bingo call and passwork ok.";
		}else{
			echo "Incorrect password";
		}
	}else{
		echo "Call not found.";
	}
}else{
	echo "error!";
}
?>