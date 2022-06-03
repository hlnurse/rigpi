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
require_once '/var/www/html/programs/sqldata.php';
require_once ('/var/www/html/classes/MysqliDb.php');
$tField=$_POST['field'];
$tData=$_POST['data'];
$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ('Callsign', 'ADMIN');
$data = Array (
	$tField => $tData 
);
if ($db->update ('LoggedIn', $data)){echo "OK";};
?>

