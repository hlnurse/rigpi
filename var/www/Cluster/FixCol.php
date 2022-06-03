<?php

/**
 * @author Howard Nurse, W6HN.
 * @copyright 2018
 */

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
require_once ("/var/www/html/programs/sqldata.php");
require_once ("/var/www/html/classes/MysqliDb.php");	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
$rows=$db->get('Clusters');

foreach ($rows as $row){
	$ID=$row['ID'];
	$node=$row['IP'];
	$node1=strstr($node,":");
	if (strlen($node1)>0){
		$node1=str_replace(":","",$node1);
		$node1=trim($node1);
		$row['Port']=$node1;
		$node1 = substr( $node, 0, strpos( $node, ":" ) );	
		$row['IP']=trim($node1);
		$db->where('ID',$ID);
		$db->update ('Clusters', $row);
	}

/*	$val=$row['Grid'];
	$val=str_replace(0x0A, '', $val);
	$row['IP']=trim($val);
		$db->where('ID',$ID);
		$db->update ('Clusters', $row);
*/}
?>