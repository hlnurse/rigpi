<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine gets the current update from the download site 
 * 
 * It must live in the programs folder   
 */
$which=$_POST['what'];
switch (strtolower($which)){
	case 'ipadr':
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', 1);
		$lanIP1=trim(str_replace("\n", '', shell_exec("hostname -I")));
		$lan1="";
		$lan2="";
		if (strpos($lanIP1," ")>0){
			$lan1=substr($lanIP1, 0, strpos($lanIP1," "));
			$lan2=substr($lanIP1,strpos($lanIP1," "));
		}else{
			$lan1=$lanIP1;
			$lan2="not found";
		}
		$lan3=shell_exec("curl ifconfig.me");
		
		
		
		echo trim($lan1)."+".trim($lan2)."+".trim($lan3);
		break;
	case 'temp':
		$temp=shell_exec("/opt/vc/bin/vcgencmd measure_temp");
		echo $temp;
		break;	
	case 'stats':
		$temp=shell_exec("uptime");
		echo "<br>".$temp."<br><br>";
		break;	
}
?>