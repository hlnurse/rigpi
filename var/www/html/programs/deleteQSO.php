<?php

/**
 * @author Howard Nurse W6HN
 * 
 * This routine deletes all or selected contacts from the logbook
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$id=$_POST['id'];
$whichLog=$_POST['which'];
$log=$_POST['log'];
//$id=0;
require_once ("/var/www/html/programs/sqldata.php");
require_once("/var/www/html/classes/MysqliDb.php");	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if (!$db){
	die("Connection failed: ".mysqli_connect_error());
}
if ($log!='x'){
	$db->where('Logname',$log);
}
if ($whichLog=='selected'){
	if ($id==0){
		$db->where('Sel',1);
	}else{
		$db->where('MobileID',$id);
	}
}
if ($db->delete('Logbook')){
	echo "Record(s) deleted successfully.";
}else{
	echo "Delete attempt failed: ".$id;
}

?>