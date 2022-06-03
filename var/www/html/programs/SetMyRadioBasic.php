<?php

/**
 * @author COMMSOFT, Inc.
 * 
 * This routine sets the specified radio data for the assigned account
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
require_once './sqldata.php';
require_once ('../classes/MysqliDb.php');
$curRadio=$_POST['i']; //radio number for this acccount
$manufacturer=$_POST['m']; //Manufacturer
$rName=$_POST['n'];//radio name
$model=$_POST['o']; //radio model
$port=$_POST['p']; //tty port
$keyerPort=$_POST['kp']; //tty port
$id=$_POST['d']; //hamlib id
$keyer=$_POST['k'];
$data1 = Array (
	'Selected' => 0
);
if ($rName=="Dummy"){
	$port="None";
}
if ($keyer=="None"||$keyer=="via CAT"){
	$keyerPort="None";
}
$data = Array (
	'Selected' => 1,
	'Manufacturer' => $manufacturer,
	'Model' => $model,
	'Port'=>$port,
	'ID' => $id,
	'RadioName'=> $rName,
	'Keyer'=> $keyer,
	'KeyerPort'=>$keyerPort
);
$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->update("MySettings",$data1);
usleep(100000);
$db->where("Port",$port);
$db->where("Port","",'!=');
$db->where('Radio',$curRadio,'!=');
$db->where('Radio',0,'!=');
$row=$db->getOne('MySettings');
if ($db->count>0){
	if (strtolower($row['Port'])!="none"){
		echo "<br>".$row['Port'] . " is already in use in the account for radio ". $row["RadioName"].".<br><br>";
		exit;
	}
}
$db->where("KeyerPort",$keyerPort);
$db->where('Radio',$curRadio,'!=');
$db->where('KeyerPort','None','!=');
$db->where("KeyerPort","",'!=');
$db->where('Radio','0','!=');
$db->where('Keyer',$keyer);
$row=$db->getOne('MySettings');
if ($db->count>0){
	echo "<br>CW Port " . $port . " is already in use in the account for radio ". $row["RadioName"].".<br><br>";
	exit;
}
$db->where ("Radio", $curRadio);
if ($db->update ('MySettings', $data)){
    echo '<br>Settings for radio ' . $curRadio . ' (' . $manufacturer . ' ' . $model . ') were saved.<br><br>';
}else{
    echo 'update failed: ' . $db->getLastError();
}

?>