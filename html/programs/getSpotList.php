<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns table header and rows with spotted Q's
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require_once ($dRoot."/programs/sqldata.php");
require_once ($dRoot."/classes/MysqliDb.php");
require($dRoot."/programs/GetSettingsFunc.php");

if (!empty($_POST['radio'])){
	$tMyRadio=$_POST['radio'];
}else{
	$tMyRadio='1';
}

if (!empty($_POST['folder'])){
	$tFolder=$_POST['folder'];
}else{
	$tFolder='Inbox';
}

if (!empty($_POST['order'])){
	$tOrder=$_POST['order'];
}else{
	$tOrder='Webdate';
}

if (!empty($_POST['direction'])){
	$tDir=$_POST['direction'];
}else{
	$tDir='ASC';
}

if (!empty($_POST['band'])){
	$tBand=$_POST['band'];
}else{
	$tBand='1=1';
}

if (!empty($_POST['need'])){
	$tNeed=urldecode($_POST['need']);
}else{
	$tNeed='';
}

if (!empty($_POST['mode'])){
	$tMode=urldecode($_POST['mode']);
}else{
	$tMode='';
}

if (!empty($_POST['sort'])){
	$tSort=urldecode($_POST['sort']);
}else{
	$tSort='DX';
}
//$tSort="Spotter";
if (!empty($_POST['direction'])){
	$tSortDir=urldecode($_POST['direction']);
}else{
	$tSortDir='desc';
}
//$tSortDir="ASC";
$tMode=strtoupper($tMode);
if ($tMode=='ALL'){
	$tMode='';
}
if ($tMode=='DIGITAL'){
	$tMode='RTTY';
}

$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
$dbLog = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if ($tFolder!="Inbox" && strlen($tFolder)>0){
	$db->where($field,$tFolder);
}
$logName=GetField($tMyRadio,'LogName','MySettings');
$db->where('Radio',$tMyRadio);
$db->where($tBand);
if (strlen($tMode)>0){
	if ($tMode=='PHONE'){
		$db->where("(Mode = 'USB' or Mode = 'LSB')");
	}else{
		$db->where('Mode',"$tMode");
	}
};

$db->orderBy("$tSort","$tSortDir");
$rows=$db->get('Spots');
//echo $db->getLastQuery();
$j=0;
$tCount= $db->count;
$tTable = "";
$tTable.="<tp 'TCOUNT: $tCount'>";

$tTable .= "<div id='logt1' style='height: 800px' tabindex='0'>";
$tTable .= "<table class='table table-sm striped' onselectstart='return false' id='logt'>";
$tTable .= "<thead><tr>";

// Header columns
$headers = [
	['DX', '10%'],
	['Frequency', '15%'],
	['From', '10%', 'Spotter'],
	['Time', '10%', 'Webdate'],
	['Note', '10%'],
	['Entity', '10%', 'Country'],
	['DXCC', '10%'],
	['Sp Cont', '15%', 'SpotterContinent'],
	['Mode', '15%']
];

foreach ($headers as $h) {
	$label = $h[0];
	$width = $h[1];
	$id = isset($h[2]) ? $h[2] : $label;
	$tTable .= "<th class='hClk' style='text-align:center; width: $width;' id='$id'>$label";
	$tTable .= "<i class='fas fa-sort fa-fw select' data-col='$id'></i></th>";
}

// Empty header for delete button
$tTable .= "<th class='null' style='text-align:center;'></th>";

$tTable .= "</tr></thead><tbody id='myTableBody'>";

// Table rows
$i = 0;
while ($i < $tCount) {
	$tRow = $rows[$i];
	$tID = $tRow['id'];
	$tDX = $tRow['DX'];
	$tDXCC = $tRow['DXCC'];
	$tFrequency = $tRow['Frequency'];
	$tBand = $tRow['Band'];
	$tBandM = $tBand . "M";
	$tMode = $tRow['Mode'];

	switch ($tNeed){
		case "callWorked":
			$dbLog->where("Callsign",$tDX);
			break;
		case "callWorkedBand":
			$dbLog->where("Callsign",$tDX);
			$dbLog->where("(Band = '$tBand' or Band = '$tBandM')");
			break;
		case "callConfirmed":
			$dbLog->where("Callsign",$tDX);
			$dbLog->where('QSL_R','Y');
			$dbLog->where("(Band = '$tBand' or Band = '$tBandM')");
			break;
		case "callConfirmedBand":
			$dbLog->where("Callsign",$tDX);
			$dbLog->where('QSL_R','Y');
			break;
		case "entityWorked":
			$dbLog->where("DXCC",$tDXCC);
			break;
		case "entityWorkedBand":
			$dbLog->where("DXCC",$tDXCC);
			$dbLog->where("(Band = '$tBand' or Band = '$tBandM')");
			break;
		case "entityConfirmed":
			$dbLog->where("DXCC",$tDXCC);
			$dbLog->where('QSL_R','Y');
			break;
		case "entityConfirmedBand":
			$dbLog->where("DXCC",$tDXCC);
			$dbLog->where("(QSL_R = 'Y')");
			$dbLog->where("(Band = '$tBand' or Band = '$tBandM')");
			break;
		default:
			break;
	}
	if ($logName!='ALL Logs'){
		$dbLog->where('Logname',$logName);
	}
	$tRowLog=$dbLog->getOne('Logbook');
	$tColor='';
	if ($dbLog->count==1 && $tNeed=='callWorked'){
		$tColor="class='bg-info text-white' title='Station worked'";
	}
	
	if ($dbLog->count==1 && $tNeed=='callConfirmed'){
		$tColor="class='bg-success text-white' title='Station confirmed'";
	}
	
	if ($dbLog->count==1 && $tNeed=='callWorkedBand'){
		$tColor="class='bg-warning text-white' title='Station worked on this band'";
	}
	
	if ($dbLog->count==1 && $tNeed=='callConfirmedBand'){
		$tColor="class='bg-danger text-white' title='Station confirmed on this band'";
	}
	
	$tColorB='';
	if ($dbLog->count==0 && $tNeed=='entityWorked'){
		$tColorB="class='bg-info text-white' title='This entity NOT worked'";
	}
	
	if ($dbLog->count==0 && $tNeed=='entityConfirmed'){
		$tColorB="class='bg-success text-white' title='This entity NOT confirmed'";
	}
	
	if ($dbLog->count==0 && $tNeed=='entityWorkedBand'){
		$tColorB="class='bg-warning text-white' title='Entity NOT worked on this band'";
	}
	
	if ($dbLog->count==0 && $tNeed=='entityConfirmedBand'){
		$tColorB="class='bg-danger text-white' title='Entity NOT confirmed on this band'";
	}
	
	
	$tColorC='';

	$tDecF = addPeriods($tFrequency);

	$tTable .= "<tr class='clickme' Frequency='$tFrequency' Band='$tBand' Mode='$tMode' call='$tDX' id='$tID' title='Frequency=$tFrequency Band=$tBand Mode=$tMode DX Call=$tDX ID=$tID'>";
	$tTable .= "<td $tColor>{$tDX}</td>";
	$tTable .= "<td>$tDecF</td>";
	$tTable .= "<td>{$tRow['Spotter']}</td>";
	$tTable .= "<td>{$tRow['Webtime']}</td>";
	$tTable .= "<td>{$tRow['Note']}</td>";
	$tTable .= "<td $tColorB>{$tRow['Country']}</td>";
	$tTable .= "<td>{$tRow['DXCC']}</td>";
	$tTable .= "<td $tColorC>{$tRow['SpotterContinent']}</td>";

	$displayMode = (stripos($tRow['Note'], 'FT8') !== false || stripos($tDecF, '74') !== false) ? "USB-D" : $tMode;
	$tTable .= "<td>$displayMode</td>";

	$tTable .= "<td><button class='btn btn-warning btn-sm BSdelete' title='Delete this spot' id='b$tID' type='button'>"
			. "<i class='fas fa-trash-alt fa-fw'></i> Delete</button></td>";

	$tTable .= "</tr>";
	$i++;
}

$tTable .= "</tbody></table></div>"; // close table & wrapper

echo $tTable;

function addPeriods($tF){
	return number_format($tF,0,".",".");
}
?>