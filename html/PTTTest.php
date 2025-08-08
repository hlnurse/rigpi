<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require( '/var/www/html/programs/sqldata.php');
require_once ('/var/www/html/classes/MysqliDb.php');	
require_once "/var/www/html/programs/SetSettingsFunc.php";
$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where('Radio',"1");
$t=microtime(true);
for ($i=1;$i<=10;$i++){
	usleep(1000000);
	$data = Array (
		'PTTOut' =>'1',
		'PTTOutCk' =>'1'
	);
	$db->update ('RadioInterface', $data);
		echo "\n on: ".(microtime(true)-$t);
	$t=time();

	usleep(1000000);
	$data = Array (
		'PTTOut' =>'0',
		'PTTOutCk' =>'1'
	);
	$db->update ('RadioInterface', $data);
	echo "     off: ".(microtime(true)-$t);
	$t=time();
};
echo "\n";
?>