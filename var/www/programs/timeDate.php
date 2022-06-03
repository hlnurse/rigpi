<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * These functions are for processing time
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
$sTime =strtoupper($_POST['time']);
date_default_timezone_set('UTC');
echo strtotime($sTime);


?>