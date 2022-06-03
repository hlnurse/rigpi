<?php

/**
 * @author Howard Nurse W6HN
 * 
 * This routine deletes all or selected spots from the spotlist
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$id=$_POST['id'];
//$id=14;
require_once("/var/www/html/programs/sqldata.php");
require_once ("/var/www/html/classes/MysqliDb.php");	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
//$db = mysqli_connect("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if (!$db){
	die("Connection failed: ".nysqli_connect_error());
}
$db->where('id',$id);

if ($db->delete('Spots')){
	echo "<br>Spot deleted successfully.<br><br>";
}else{
	echo "<br>Spot delete attempt failed: ".$id ."<br><br>";
}

?>