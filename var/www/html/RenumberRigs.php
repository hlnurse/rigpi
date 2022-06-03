<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
require_once('/var/www/html/programs/sqldata.php');
require_once ('/var/www/html/classes/MysqliDb.php');
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->orderBy("NUMBER","asc");
$cols=Array ("NUMBER","NEWNUM");
$radios=$db->get("Radios", null, $cols);
foreach ($radios as $key=>$radio){
	$r=$radio['NUMBER'];
	if (strlen($r)==3){
		$n=substr($r, 0,1);
		$m=$n."0".substr($r,1);
	}elseif (strlen($r)==4){
		$n=substr($radio['NUMBER'], 0,2);
		$m=$n."0".substr($r,2);
	}
	if (strlen($r)>2){
		$radios[$key]['NEWNUM']=$m;
	}else{
		$radios[$key]['NEWNUM']=$r;
	}
	$db->update('Radios',$radio);
//	echo $radio['NUMBER']."  "."newnum: ".$radio['NEWNUM']."\n";
}
//$db->update('Radios',$radios);
foreach ($radios as $radio){
	$db->where('NUMBER',$radio['NUMBER']);
	$data=Array('NEWNUM'=>$radio['NEWNUM']);
	$db->update('Radios',$data,1);
//	echo $radio["NUMBER"]. "   ".$radio["NEWNUM"]."\n";
}


?>