<?php

/**
 * @author COMMSOFT, Inc.
 * @copyright 2009
 */

//include "GetGrid.php";
include "GetBand.php";
include "GetDXCC.php";
include "/var/www/html/programs/sqldata.php";

$myLon = "0";
$myLat = "0";

$mycall = strtoupper(trim($_GET["mycall"]));
if ($mycall=="DEMO"){
	exit;
}
$id = $_GET["id"];
$mid = $_GET["mid"];
if ($mid=="(null)"){
	$mid=0;
}
//echo $mid;
$logname = trim($_GET["logname"]);
$callsign = strtoupper(trim($_GET["call"]));
$reg = strtoupper(trim($_GET["reg"]));
$time_start = $_GET["stime"];
$time_end = $_GET["etime"];
$mode = $_GET["mode"];
$tx_frequency = $_GET["tx"];
$rx_frequency = $_GET["rx"];
$tx_frequency = $tx_frequency . "0";
$rx_frequency = $rx_frequency . "0";
$rx_frequencyBand = substr($rx_frequency,0,strlen($rx_frequency)-3);
$band = GetBandFromFrequency($rx_frequencyBand);
$band=strtoupper($band);
if (!stristr($band,"M")){
	$band=$band . "M";
}
$his_rst = $_GET["hrst"];
$my_rst = $_GET["mrst"];
$dxcc = $_GET["dxcc"];
$qth = $_GET["qth"];
$qth = str_replace("'","",$qth);
$dxgrid = $_GET["gr"];
$dxcounty = $_GET["county"];
$dxstate = $_GET["state"];
$dxcont = $_GET["cont"];
$dxcountry = $_GET["country"];

if ($dxgrid=="(null)"){
	$dxgrid="";
}
$dxemail = $_GET["dxemail"];
$name = $_GET["name"];
$name = str_replace("'","",$name);
$notes = $_GET["notes"];
$notes = str_replace("^^","\'",$notes);
$qsl_s = $_GET["qsls"];
$qsl_r = $_GET["qslr"];
$power = $_GET["pwr"];
$mytx = $_GET["mRig"];
$myant = $_GET["mAnt"];
$myLat = $_GET["mLat"];
$myLon = $_GET["mLon"];
$mycity = $_GET["mCit"];
$mycounty = $_GET["mCnty"];
$mystate = $_GET["mSt"];
$mycountry = $_GET["mCoun"];
$sendTweet = $_GET["tweet"];
$changed = $_GET["changed"];
$cqzone = $_GET["cqzone"];
$ituzone = $_GET["ituzone"];
$wpx = $_GET["wpx"];
$ten_ten = $_GET["ten_ten"];
$iota = $_GET["iota"];
$lotw = $_GET["lotw"];
$eqsl = $_GET["eqsl"];
$mqsl = $_GET["mqsl"];
if ($qsl_s==""){
	$qsl_s="N";
}
if ($qsl_r==""){
	$qsl_r="N";
}
if ($lotw==""){
	$lotw="?";
}
if ($eqsl==""){
	$eqsl="?";
}
if ($mqsl==""){
	$mqsl="?";
}
//echo date('m d Y',$time_start);
//echo $mycall;
if ($myLon=="0" && $myLat=="0"){
	$mGrid="unk";
}else{
	$mGrid = GetGridData($myLat,$myLon);
}
$location=GetLocationData($callsign);
$arrayDX = explode("|", $location);
if ($cqzone==""){
	$cq = $arrayDX[9];
}else{
	$cq = $cqzone;
}
if ($ituzone==""){
	$itu = $arrayDX[8];
}else{
	$itu = $ituzone;
}
if ($dxcountry==""){
	$dxCountry = $arrayDX[2];
}else{
	$dxCountry=$dxcountry;
}
if ($dxcont==""){
	$dxContinent = $arrayDX[6];
}else{
	$dxContinent = $dxcont;
}
$start_plain=gmdate("Hi d-M-Y", $time_start);
$end_plain=gmdate("Hi d-M-Y", $time_end) ;
if ($mid == 0) {
	$data = Array (
		'ID'=>$tCall,
		'Logname'=>$_POST['Access_Level'],
		'Callsign'=>$_POST['FirstName'],
		'MyCall'=>$_POST['LastName'],
		'Time_Start'=>$tPWD,
		'Time_End'=>$_POST['QTH'],
		'Mode'=>$_POST['My_Country'],
		'TX_Frequency'=>$_POST['My_State'],
		'RX_Frequency'=>$_POST['My_County'],
		'His_RST'=>$_POST['My_City'],
		'My_RST'=>$_POST['My_Email'],
		'DXCC'=>$_POST['My_Phone'],
		'His_QTH'=>$_POST['My_Lat'],
		'His_Name'=>$_POST['My_Lon'],
		'Note'=>$_POST['My_Grid'],
		'QSL_S'=>$_POST['Mobile_Lat'],
		'QSL-R'=>$_POST['Mobile_Lon'],
		'My_Latitude'=>$_POST['Mobile_Grid'],
		'My_Logitude'=>$_POST['Mobile_Grid'],
		'MyCity'=>$_POST['Mobile_Grid'],
		'MyCounty'=>$_POST['Mobile_Grid'],
		'MyState'=>$_POST['Mobile_Grid'],
		'MyCountry'=>$_POST['Mobile_Grid'],
		'Transmitter'=>$_POST['Mobile_Grid'],
		'TX_Antenna'=>$_POST['Mobile_Grid'],
		'My_Grid'=>$_POST['Mobile_Grid'],
		'Band'=>$_POST['Mobile_Grid'],
		'His_Grid'=>$_POST['Mobile_Grid'],
		'His_Country'=>$_POST['Mobile_Grid'],
		'His_Continent'=>$_POST['Mobile_Grid'],
		'His_URL'=>$_POST['Mobile_Grid'],
		'CQZone'=>$_POST['Mobile_Grid'],
		'ITUZone'=>$_POST['Mobile_Grid'],
		'His_Email'=>$_POST['Mobile_Grid'],
		'Time_start_Plain'=>$_POST['Mobile_Grid'],
		'Time_End_Plain'=>$_POST['Mobile_Grid'],
		'Mobile_Change'=>$_POST['Mobile_Grid'],
		'Remote_Changed'=>$_POST['Mobile_Grid'],
		'LoTW'=>$_POST['Mobile_Grid'],
		'LoTW_Last_QSL'=>$_POST['Mobile_Grid'],
		'eQSL'=>$_POST['Mobile_Grid'],
		'mQSL'=>$_POST['Mobile_Grid'],
		'His_County'=>$_POST['Mobile_Grid'],
		'His_State'=>$_POST['Mobile_Grid'],
		'His_Section'=>$_POST['Mobile_Grid'],
		'His_Class'=>$_POST['Mobile_Grid'],
		'IOTA'=>$_POST['Mobile_Grid'],
		'Ten_Ten'=>$_POST['Mobile_Grid'],
		'WPX_Prefix'=>$_POST['Mobile_Grid'],
		'VE_Province'=>$_POST['Mobile_Grid'],
		'TX_Power'=>$_POST['Mobile_Grid'],
		'TX_Antenna'=>$_POST['Mobile_Grid'],
		'RX_Antenna'=>$_POST['Mobile_Grid'],
		'His_Power'=>$_POST['Mobile_Grid'],
		'Serial_Sent'=>$_POST['Mobile_Grid'],
		'Serial_Received'=>$_POST['Mobile_Grid'],
		'Contest_ID'=>$_POST['Mobile_Grid'],
		'Receiver'=>$_POST['Mobile_Grid'],
		'Transmitter'=>$_POST['Mobile_Grid'],
		'Amplifier'=>$_POST['Mobile_Grid'],
		'Beam_Heading'=>$_POST['Mobile_Grid'],
		'Propagation_Mode'=>$_POST['Mobile_Grid'],
		'Sat_Name'=>$_POST['Mobile_Grid'],
		'Sat_Mode'=>$_POST['Mobile_Grid']
	);
	$str1 = "INSERT INTO " . $mycall .
		" (ID, Logname, Callsign, MyCall, Time_Start, Time_End, Mode, Tx_Frequency, Rx_Frequency,
        His_RST, My_RST, DXCC, His_QTH, His_Name, Note, QSL_S, QSL_R, TX_Power, My_Latitude, My_Longitude,
        MyCity, MyCounty, MyState, MyCountry, Transmitter, TX_Antenna, My_Grid, Band, His_Grid, His_Country,
        His_Continent, CQZone, ITUZone, His_Email, Time_Start_Plain, Time_End_Plain, Mobile_Change, Remote_Changed, LoTW, eQSL,
        mQSL, His_County, His_State, IOTA, Ten_Ten, WPX_Prefix) VALUES ('" .
		$id . "', '" . $logname . "', '" . $callsign . "', '" . strtoupper($mycall) .
		"', '" . $time_start . "', '" . $time_end . "', '" . $mode . "', '" . $tx_frequency .
		"', '" . $rx_frequency . "', '" . $his_rst . "', '" . $my_rst . "', '" . $dxcc .
		"', '" . $qth . "', '" . $name . "', '" . $notes . "', '" . $qsl_s . "', '" . $qsl_r .
		"', '" . $power . "', '" . $myLat . "', '" . $myLon . "', '" . $mycity .
		"', '" . $mycounty . "', '" . $mystate . "', '" . $mycountry . "', '" . $mytx . "', '" . $myant .
		"', '" . $mGrid . "', '" . $band . "', '" . $dxgrid . "', '" . $dxCountry . "', '" . $dxContinent .
		"', '" . $cq . "', '" . $itu . "', '" . $dxemail . "', '" . $start_plain . "', '" . $end_plain . "', '" . $changed .
		"', '" . $changed . "', '" . $lotw . "', '" . $eqsl . "', '" . $mqsl . "', '" . $dxcounty . "', '" . $dxstate .
		"', '" . $iota . "', '" . $ten_ten . "', '" . $wpx . "')";
	//    echo $str1;
} else {
	$str1 = "UPDATE " . $mycall . " SET Logname = '" . $logname . "', Callsign = '" . $callsign .
		"', Time_Start = '" . $time_start . "', Time_End = '" . $time_end .
		"', Mode = '" . $mode . "', Tx_Frequency = '" . $tx_frequency .
		"', Rx_Frequency = '" . $rx_frequency . "', His_RST = '" . $his_rst .
		"', My_RST = '" . $my_rst . "', DXCC = '" . $dxcc . "', His_QTH = '" . $qth .
		"', His_Name = '" . $name . "', Note = '" . $notes . "', QSL_S = '" . $qsl_s .
		"', QSL_R = '" . $qsl_r . "', Tx_Power = '" . $power . "', Time_Start_Plain = '" . $start_plain .
		"', Remote_Changed = '" . $changed . "', Time_End_Plain = '" . $end_plain . "', Mobile_Change = '" . $changed .
		"', lotw = '" . $lotw . "', eqsl = '" . $eqsl . "', mqsl = '" . $mqsl .
		"', CQZone = '" . $cq . "', ITUZone = '" . $itu . "', His_Email = '" . $dxemail .
		"', His_County = '" . $dxcounty . "', His_State = '" . $dxstate .
		"', His_Country = '" . $dxcountry . "', IOTA = '" . $iota .
		"', Ten_Ten = '" . $ten_ten . "', WPX_Prefix = '" . $wpx .
		"' WHERE MobileID = '" . $mid . "'";
}
//echo $str1;
$con = mysql_connect("localhost", $mycall, $reg);
if (!$con) {
	die('Could not connect: ' . mysql_error());
}
$db_selected = @mysql_select_db("logbook", $con);
if (!$db_selected) {
	die("Can\'t use: " . mysql_error());
}
if (mysql_query($str1)) {
	echo mysql_insert_id();
} else {
	echo "N";
}

mysqli_close($con);
?>