<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine deletes one user from the user database
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$id=$_POST['id'];
$un=$_POST['un'];
require_once("/var/www/html/programs/sqldata.php");
require_once("/var/www/html/classes/MysqliDb.php");	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if (!$db){
	die("Connection failed: ".mysqli_connect_error());
}

$db->where('radio',$id);
$data = Array (
	'Manufacturer' =>'Hamlib',
	'RadioName'=>'Dummy'.$id,
	'Model' =>'Dummy',
	'Port' =>'None',
	'ID'=>'1',
	'DX'=>'W1AW',
	'Keyer'=>'None',
	'KeyerPort'=>'None',
	'ClusterID'=>'378',
	'RotorManufacturer'=>'Hamlib',
	'RotorModel'=>'Dummy'
);
$db->update ('MySettings', $data);


$db->where('uID',$id);


if ($db->delete('Users')){
	echo "User with username " . $un . " deleted successfully.";

}else{
	echo "Delete user attempt failed.";
}

?>