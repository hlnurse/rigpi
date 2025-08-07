<?php
//	$tCall=$_POST['c'];
//	$tCall=strtoupper($_GET["c"]);
function CloseDownPower($un){
	$tUserName=$un;
//	$tUserName=$_GET["x"];
	require_once('/var/www/html/classes/Membership.php');
	$membership = New Membership();
	$membership->confirm_Member($tUserName);
	exec('sudo /sbin/halt');
}	
?>