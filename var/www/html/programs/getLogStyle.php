<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns a style row
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
$sql_radio_username="";
$sql_radio_password="";
$sql_radio_database="";
$dRoot='/var/www/html';
require($dRoot."/programs/sqldata.php");
require_once($dRoot."/classes/MysqliDb.php");	

if (!empty($_POST['field'])){
	$field=$_POST['field'];
}else{
	$field='DefaultValue';
}
if (!empty($_POST['style'])){
	$style=$_POST['style'];
}else{
	$style='General';
}
if (!empty($_POST['row'])){
	$tRow=$_POST['row'];
}else{
	$tRow='MyCall';
}
if (!empty($_POST['new'])){
	$tNew=$_POST['new'];
}else{
	$tNew=0;
}
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if (!$db) {
	die("Connection failed: " . $db->connect_error);
}
if ($tNew==0){
	$db->where('Name',$style);
	$db->where('IDValue',$tRow);
	$row="";
	$row = $db->getOne('LogStyles');
	$aRow=array($row['Label'],$row['DefaultValue'],$row['Attribute'],$row['ListContents'],$row['Notes'],$row['ADIFTag'],$row['Prompt']);
}else{
	$db->where('Field',$tRow);
	$row = $db->getOne('LogFields');
	$aRow=array($row['Label'],'',$row['Attribute'],$row['List'],$row['Note'],$row['ADIF'],$row['Prompt']);
}
echo json_encode($aRow);



?>