<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine sets the specified radio control port from the MyRadio table in the rigs database
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require_once ($dRoot.'/programs/sqldata.php');
require_once ($dRoot.'/classes/MysqliDb.php');
$tCall=$_GET['call'];
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("MyCall", $tCall);
$row = $db->getOne ("Users");
$result = $row['rigctldPort'];
echo $result;
?>