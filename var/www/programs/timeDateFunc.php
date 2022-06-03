<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * These functions are for processing time
 * 
 * It must live in the programs folder   
 */
function convertUnix($unixTime){
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
   $dt = new DateTime("@$unixTime");
   return strToUpper($dt->format('Hi d-M-Y'));
}

function getUnixTime(){
	return $_SERVER['REQUEST_TIME'];
}

function convertStrToUnixTime($sTime){
	date_default_timezone_set('UTC');
	return strtotime($sTime);
}

?>