<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine gets a row from the Users table for a specified username
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require_once ($dRoot.'/programs/sqldata.php');
require_once ($dRoot.'/classes/MysqliDb.php');
$tUserID=$_POST['uID'];
$tField=$_POST['field'];
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("User", $tUserID);
$row = $db->getOne ("Callbook");
$result = $row[$tField];
echo $result;
?>