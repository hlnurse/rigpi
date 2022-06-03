<?php

/**
 * @author COMMSOFT, Inc.
 * @copyright 2008
 * 
 * This function returns the call, DXCC num, 
 * Country name, lat, lon, and time offset given a callsign
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
	
function GetLocationData($callsign1)
{
	$sql_radio_username="";
	$sql_radio_password="";
	$sql_radio_database="";
	$dRoot='/var/www/html';
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");	
	
	$Callsign = strtoupper($callsign1);
	
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$Callsign = strtoupper($Callsign);
	$i = strlen($Callsign);
	$link=0;
	$found=false;
	$prefix='';
	while ($found === false && $i > 0) {
	    $prefix = substr($Callsign, 0, $i);
		$db->where ("Prefix", $prefix);
		$row = $db->getOne ("PLink");
		if($row){
			$link=$row["plink"];
			$found=true;
		}
		$i--;
	}
	
	$db->where ("ID", $link);
	$row = $db->getOne ("Prefixes");
	//$str1 = "SELECT * FROM Prefixes Where ID = '" . $link . "'";
	//$result = mysql_query($str1);
	if ($row) {
	    $DXCC = $row["DXCC"];
	    $country = $row["Country"];
	    if (strpos($country,":")>0) {
	    	$country=substr($country,0,strpos($country,":"));
	    }
	    $latitude = $row["Latitude"];
	    $longitude = $row["Longitude"];
	    $time_offset = $row["Time_Offset"];
	    $time_offset = -1 * $time_offset;
	    $cont = $row["Continent"];
	    $abbr = $row["Abbreviation"];
	    $itu = $row["ITU_Zone"];
	    $cq = $row["CQ_Zone"];
	}else{
	    $DXCC = "";        
	    $country = "";
	    $latitude = 0;
	    $longitude = 0;
	    $time_offset = 0;
	    $cont = "";
	    $abbr = "";
	    $itu = "";
	    $cq = "";
	}
	
	
	return $Callsign . "|" . $DXCC . "|" . $country . "|" . $latitude . "|" . $longitude . "|" . $time_offset . "|" . $cont . "|" . $abbr . "|" . $itu . "|" . $cq . "|";
}
?>