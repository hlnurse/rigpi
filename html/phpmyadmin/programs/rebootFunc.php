<?php
function RebootServer($un){
	$tUserName=$un;
//	$tUserName=$_GET["x"];
	require_once('/var/www/html/classes/Membership.php');
	$membership = New Membership();
	$membership->confirm_Member($tUserName);
	header("location: /login.php?c=&x=");
	system("sudo reboot");

}
 //   error_log(date("[Y-m-d H:i:s]")."\t[".$level."]\t[".basename(__FILE__)."]\t".$text."\n", 3, 'errorlog.txt');
?>