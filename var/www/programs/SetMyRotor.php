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
require_once './sqldata.php';
require_once ('../classes/MysqliDb.php');
$curRadio=$_POST['rx']; //radio number
$manufacturer=$_POST['rm']; //Manufacturer
$model=$_POST['ro']; //rotor model
$port=$_POST['rp']; //tty port
$baud=$_POST['ru']; //baud rate
$bits=$_POST['rb']; //stop bits (
$parity=$_POST['ra']; //parity
$stop=$_POST['rs']; //stop
$id=$_POST['rd']; //hamlib id
$data = Array (
	'RotorManufacturer' => $manufacturer,
	'RotorModel' => $model,
	'RotorPort' => $port,
	'RotorBaud' => $baud,
	'RotorBits' => $bits,
	'RotorParity' => $parity,
	'RotorStop' => $stop,
	'RotorID' => $id
);
$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("Radio", $curRadio);
if ($db->update ('MySettings', $data)){
    echo 'Rotor ' . $manufacturer . ' ' . $model . ' was updated';
}else{
    echo 'update failed: ' . $db->getLastError();
}

?>