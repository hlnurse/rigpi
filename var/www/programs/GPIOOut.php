<?php
//set PTT via GPIO throughb keyer and audio boards
require ('/var/www/html/programs/vendor/autoload.php');
use PhpGpio\Gpio;
//function doCW($on1){
//	$on1="on";
	$on=1;
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	system ( "gpio mode 13 out");
	if ($on==1){
		system ( "gpio write 13 1");
	}else{
		system ( "gpio write 13 0");
	}	
	system ( "gpio unexportall");
	return "OK";
//}
?>

