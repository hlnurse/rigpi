<?php
	error_reporting(E_ALL);
	ini_set('display_errors','1');
	$dRoot='/var/www/html';
	require_once ($dRoot.'/programs/sqldata.php');
	require_once ($dRoot.'/classes/MysqliDb.php');	
	require_once($dRoot."/classes/SocketServer.class.php");
	/* Open and initialize HamLib.*/
	$report= "";
	/*Get data from MyRadio table*/
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$db->where ("Radio", 1);
	$row = $db->getOne ("MySettings");
    $id=$row['ID'];
	$data=exec("rigctld -m $id -u | grep 'Serial speed'" );
	$baud='34800';
	$cts_rts='0';
	$stop='1';
	$civ='';
	$vers='';	

	if (strstr($data, '115200')){
		$baud='115200';
	}else if (strstr($data,'57600')){
		$baud='57600';
	}else if (strstr($data,'38400')){
		$baud='38400';
	}else if (strstr($data,'19200')){
		$baud='19200';
	}else if (strstr($data,'4800')){
		$baud='4800';
	}else if (strstr($data,'2400')){
		$baud='2400';
	}else if (strstr($data,'1200')){
		$baud='1200';
	}

	if (strstr($data, '8N1')){
		$stop='1';
	}else if (strstr($data,'8N2')){
		$stop='2';
	}
		
	if (strstr($data, 'CTS/RTS')){
		$cts_rts='1';
	}
	
	$data=exec("rigctld -m $id -L q" );
/*	if (strlen($data)>0){
		$civ=substr($data, strstr("Value:"));		
	}
*/	
	$vers=exec("rigctld -V | grep 'Hamlib'" );
				
	echo "Hamlib baud: " . $baud . " stop: " . $stop . " cts/rts: " . $cts_rts . " civ: " . $civ . " version: " . $vers . "\n";
?>