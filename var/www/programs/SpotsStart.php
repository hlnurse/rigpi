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
$tMyCall=$_POST['call'];
$tMyRadio=$_POST['radio'];
$tAction=$_POST['action'];
$tS="'SpotsDo.php $tMyCall [r]adio$tMyRadio'";
$users=exec("ps aux | grep ".$tS);
//echo "users: $tS".$users."\n";

$pos=stripos($users,"radio".$tMyRadio);
if (strlen($pos>0)){
	if ($tAction=='start'){
		$tSpotsDoPID=trim(substr($users,9,5));
		posix_kill($tSpotsDoPID,15);
		$SPKExec="php /var/www/html/SpotsDo.php $tMyCall radio$tMyRadio > /dev/null 2>&1 & echo $!;";
		$pidSP = exec($SPKExec);
		echo "Now connected.\n";
	}else{
		$tSpotsDoPID=trim(substr($users,9,5));
		posix_kill($tSpotsDoPID,15);
		echo "Now disconnected.\n";
	}
	exit; 
}else{
	if ($tAction=='start'){
		$SPKExec="php /var/www/html/SpotsDo.php $tMyCall radio$tMyRadio > /dev/null 2>&1 & echo $!;";
		$pidSP = exec($SPKExec);
		echo "Now connected.\n";
	}else{
		echo "Not connected.\n";
	}
}

?>