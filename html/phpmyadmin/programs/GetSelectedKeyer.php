<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine gets the selected keyer data from the MySettings table
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require_once ($dRoot.'/programs/sqldata.php');
require_once ($dRoot.'/classes/MysqliDb.php');
$tCall=$_POST['call'];
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("MyCall", $tCall);
$row = $db->getOne ("MySettings");
$result = $row['Keyer'];
echo $result;
?>