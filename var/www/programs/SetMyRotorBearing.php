<?php

/**
 * @author COMMSOFT, Inc.
 * 
 * This routine sets the specified rotor to a given azimuth in the RadioInterface table
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
require_once '/var/www/html/programs/sqldata.php';
require_once ('/var/www/html/classes/MysqliDb.php');
$curRadio=$_POST['i']; //radio number in list
$newAz=$_POST['a'];
$doWhat=$_POST['w'];
$data='';
if ($doWhat=='turn'){
	$data = Array (
		'RotorAzOut' => $newAz,
		'RotorCk' => '1'
	);
	doRotorLog("Az set: $newAz".PHP_EOL);
}elseif ($doWhat=='stop'){
	$data = Array (
		'RotorStop' => '1'
	);
	doRotorLog("Stop set.".PHP_EOL);
}
$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("Radio", $curRadio);
$db->update("RadioInterface",$data);


function doRotorLog($what)
{
//	if ($utest==1){
		error_log(date("Y-m-d H:i:s", time())." ".$what,3,"/var/log/rigpi-rotor.log");
//	};
}


?>