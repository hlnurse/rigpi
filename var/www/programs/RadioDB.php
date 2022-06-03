<?php

/**
 * @author Howard Nurse W6HN
 * 
 * This routine returns the radio data
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
$manufacturer=$_POST['m'];
require_once '/var/www/html/programs/sqldata.php';
require_once ('/var/www/html/classes/MysqliDb.php');
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where('MANUFACTURER',$manufacturer);
$db->orderBy('MODEL','ASC');
$db->setQueryOption('DISTINCT');
$row = $db->get('Radios');
$data="";
if ($db->count > 0) {
	foreach ($row as $line){
		$rad=$line["MODEL"];
		$data=$data . "<div class='myrad' id='$rad'><li><a class='dropdown-item radios' id='$rad' href='#'>" . $rad . "</a></li></div>\n\r";
	}
}else{
	$rad="Man first";
	$data="<div class='myrad' id='$rad'><li><a class='dropdown-item radios' id='$rad' href='#'>Not found. Select Man(ufacturer) first</a></li></div>\n\r";
}
echo $data;

?>