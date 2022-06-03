<?php
/**
 * @author Howard Nurse, W6HN
 * 
 * This function returns the county name 
 * given a zipcode
 * 
 * It must live in the programs folder   
 */
function getCountyInfo($Zip) 
{
require("/var/www/html/programs/sqldata.php");
require_once("/var/www/html/classes/MysqliDb.php");	
$Zip = substr($Zip, 0, 5);
$county="";
$latitude="";
$longitude="";
$cqz='';
$ituz='';
$dxcc='';
$section='';

$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if (!$db) {
	die("Connection failed: " . $db->connect_error);
};
$db->where("ZIP_CODE",$Zip);
$rowZip = $db->getOne("ZipCode");
if ($rowZip) {
    $FIPS = $rowZip["STATE"] . $rowZip["COUNTY"];
    $FIPS=ltrim($FIPS, '0');
    $latitude = $rowZip["LATITUDE"];
    $longitude = $rowZip["LONGITUDE"];
    $cont = $rowZip["Continent"];
    $country = $rowZip["Country"];
    $cFIPS=ltrim($rowZip["COUNTY"], '0');
    $db->where("FIPS",$FIPS);
    $row = $db->getOne("Counties");
    if ($row){
	    $county=$row['County'];
    }
    $sFIPS=ltrim($rowZip["STATE"], '0');
    $db->where("ID",$sFIPS);
    $row = $db->getOne("States");
     if ($row) {
        $cqz = $row["CQZ"];
        $ituz = $row["ITUZ"];
        $dxcc = $row["DXCC"];
        $section = $row["Section"];
    };
    $grid=GetGridData($latitude,$longitude);
};
if (strlen($county)>0){
	return $county . "|" . $latitude . "|" . $longitude . "|" . $grid . "|" . $FIPS . "|" . $country . "|" . $cont . "|" . $cqz . "|" . $ituz . "|" . $dxcc . "|" . $section . "|";
}else{
return '';
}
}

function GetGridData($latitude, $longitude) 
{
$TD = $latitude;    
if ($TD < 0) {
	$TD = -1 * (abs(intval($TD))) + 0.0001;
}else{
	$TD = (abs(intval($TD))) + 0.0001;
}
$TL = 60 * (abs($latitude) - abs($TD));
$TM = intval($TL/2.5) * 2.5 + .0001;

$GD=$longitude;

if ($GD < 0) {
	$GD = -1 * (abs(intval($GD)) + 0.0001);
}else{
	$GD = (abs(intval($GD))) + 0.0001;
}

$GL = intval(60 * (abs($longitude) - abs($GD))) + .0001;

$GM = intval($GL) + .0001;

$GE = $GD + 180;
$TE = $TD + 90;
$G1 = intval($GE / 20);
$G2 = intval($TE / 10);
$sG1 = chr($G1 + 65);
$sG2 = chr($G2 + 65);

$G3 = intval(intval($GE / 2) - intval($GE / 20) * 10);

$G4 = intval(intval($TE) - intval($TE / 10) * 10);
$sG3 = chr($G3 + 48);
$sG4 = chr($G4 + 48);

$GN = $GM;
if ($longitude < 0) {
	$GN = 60 - $GN;
}
$TN = $TM;
if ($latitude < 0) {
	$TN = 60 - $TN;
}
 
$G5 = intval($GN / 5) + 12 * intval($GE - 2 * intval($GE / 2));
$G6 = intval($TN / 2.5);
//echo $GE . " " . $GN;
$sG5 = strtolower(chr($G5 + 65));
$sG6 = strtolower(Chr($G6 + 65));

//echo $sG1 . $sG2 . $sG3 . $sG4 . $sG5 . $sG6 ;
return $sG1 . $sG2 . $sG3 . $sG4 . $sG5 . $sG6 ;
}
?>