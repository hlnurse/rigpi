<?php
//set PTT via GPIO throughb keyer and audio boards
require ('/var/www/html/programs/vendor/autoload.php');
use PhpGpio\Gpio;
function doPTT($on1){
//	$on1="on";
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	system ( "gpio mode 0 out");
	if ($on1=="on"){
		system ( "gpio write 0 1");
	}else{
		system ( "gpio write 0 0");
	}	
//	system ( "gpio unexportall"); note this messes up remote cw
	return "OK";
}
?>

