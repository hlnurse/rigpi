<?php
/**
 * @author Howard Nurse, W6HN
 * 
 * This routine gets spots from the cluster, loads them into db via SpotsDo.php
 * 
 * It must live in the html folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
$dRoot="/var/www/html";
require($dRoot.'/programs/sqldata.php');
 require_once($dRoot.'/classes/MysqliDb.php');
require_once($dRoot.'/programs/SetSettingsFunc.php');

$tMyCall=$_POST['call'];
$tMyRadio=$_POST['radio'];
$tAction=$_POST['action'];
$tS="'SpotsDo.php $tMyCall [r]adio$tMyRadio'";
$users=exec("ps aux | grep ".$tS);
//echo "users: $tS".$users."\n";

$pos=stripos($users,"radio".$tMyRadio);
if (strlen($pos>0)){
	if ($tAction=='start')
	{
		if (strlen($users)>0){
			$tSpotsDoPID=trim(substr($users,9,5));
			if ($tSpotsDoPID){
				posix_kill($tSpotsDoPID,15);
			}
		}
//		$SPKExec="php /var/www/html/SpotsDo.php $tMyCall radio$tMyRadio;";
		$SPKExec="php /var/www/html/SpotsDo.php $tMyCall radio$tMyRadio > /dev/null 2>&1 & echo $!;";
		$pidSP = exec($SPKExec);
		sleep(1);
		$users=exec("ps aux | grep ".$tS);
		if (strlen($users)>0){
			echo $users . " " . "Now connected.\n";
			SetField($tMyRadio,"ClusterConnected",1);
		}else{
			echo "Not connected.\n";
			SetField($tMyRadio,"ClusterConnected",0);
		}
		exit;
	}elseif($tAction=='delete'){
		$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
		$db->delete('Spots');
	}else{ //disconnect
		SetField($tMyRadio,"ClusterConnected",0);
		if (strlen($users)>0){
			$tSpotsDoPID=trim(substr($users,9,5));
			posix_kill($tSpotsDoPID,15);
		}
		echo "Not connected.\n";
	}
	exit; 
}else{
	if ($tAction=='start'){
		$SPKExec='';
		$SPKExec="php /var/www/html/SpotsDo.php $tMyCall radio$tMyRadio > /dev/null 2>&1 & echo $!;";
		$pidSP = exec($SPKExec);
		sleep(1);
		$users=exec("ps aux | grep ".$tS);
		if (strlen($users)>0){
			echo $users . " " . "Now connected.\n";
			SetField($tMyRadio,"ClusterConnected",1);
		}else{
			echo "Not connected.\n";
			SetField($tMyRadio,"ClusterConnected",0);
		}
	}else{
		SetField($tMyRadio,"ClusterConnected",0);
		echo "Not connected.\n";
	}
}
?>