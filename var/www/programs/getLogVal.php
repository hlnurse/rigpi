<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine gets data in a field in Logbook for given record
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$tField=$_POST['field'];
$tRec=$_POST['record'];
$value=0;
$dRoot='/var/www/html';
require_once ($dRoot.'/programs/sqldata.php');
require_once ($dRoot.'/classes/MysqliDb.php');	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
$db->where('MobileID',$tRec);
$value = $db->getValue('Logbook',$tField);
echo $value;

?>