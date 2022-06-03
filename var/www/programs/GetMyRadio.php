<?php

/**
 * @author COMMSOFT, Inc.
 * 
 * This routine returns the specified radio data from the MyRadio table in the rigs database
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$field=$_GET["f"]; //desired field
$tMyRadio=$_GET["r"]; //radio
$dRoot='/var/www/html';
require_once ($dRoot."/classes/MysqliDb.php");	
require_once ($dRoot."/programs/sqldata.php");
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("Radio", $tMyRadio);
$row = $db->getOne ("MySettings");
$result = $row[$field];
echo $result;
?>