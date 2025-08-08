<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine sets the selected radio data from the Users table
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require ($dRoot."/programs/sqldata.php");
require_once ($dRoot."/classes/MysqliDb.php");
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("Username", $tUserName);
$row = $db->getOne ("Users");
if ($row){
    $result = $row['SelectedRadio'];
}else{
    $result=1;
}
return $result;
?>