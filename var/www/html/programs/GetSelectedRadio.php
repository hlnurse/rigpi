<?php

/**
 * @author COMMSOFT, Inc.
 * 
 * This routine sets the specified radio data from the MyRadio table in the rigs database
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require_once($dRoot."/classes/MysqliDb.php");	
require($dRoot."/programs/sqldata.php");
$tUserName=$_POST['un'];
$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("Username", $tUserName);
$row = $db->getOne ("Users");
$result = $row['SelectedRadio'];
echo $result;
?>