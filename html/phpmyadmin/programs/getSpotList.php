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
if ($tSort=='DXCC'){
//	$tSort=(int)$tSort;
}
$db->orderBy("$tSort","$tSortDir");
$rows=$db->get('Spots');
//echo $db->getLastQuery();
$j=0;
$tCount= $db->count;
$tTable="";
//$tTable.= "xxx".$db->getLastQuery();
$tTable.="<tc$tCount>";
$tTable.="<div class='container-fluid'>";
$tTable.="<table class='table table-sm table-striped' onselectstart='return false' id='logt'>";
$tTable=$tTable."<thead>";
$tTable=$tTable."<tr class='sortable' >";

		$tTable=$tTable."<th class='hClk' style='text-align:center; width: 10%;' id='DX'>DX";
		if ($tSort=="DX"){
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:red;' id='DX'></i></th>";			
		}else{
			$tTable=$tTable."<i class='fas fa-sort fa-fw select'  style='color:black;' id='DX'></i></th>";
		}
		$tTable=$tTable."<th class='hClk' style='text-align:center; width: 10%;' id='Frequency'>Frequency";
		if ($tSort=="Frequency"){
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:red;' id='Frequency'></i></th>";
		}else{
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:black;' id='Frequency'></i></th>";			
		}
		$tTable=$tTable."<th class='hClk' style='text-align:center; width: 10%;' id='Spotter'>From";
		if ($tSort=="Spotter"){
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:red;'id='Spotter'></i></th>";
		}else{
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:black;' id='Spotter'></i></th>";			
		}
		$tTable=$tTable."<th class='hClk' style='text-align:center; width: 10%;' id='Webtime'>Time";
		if ($tSort=="Webtime"){
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:red;'id='Webtime'></i></th>";
		}else{
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:black;' id='Webtime'></i></th>";			
		}
		$tTable=$tTable."<th class='hClk' style='text-align:center; width: 20%;' id='Note'>Note";
		if ($tSort=="Note"){
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:red;'id='Note'></i></th>";
		}else{
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:black;' id='Note'></i></th>";			
		}
		$tTable=$tTable."<th class='hClk' style='text-align:center; width: 10%;' id='Country'>Entity";
		if ($tSort=="Country"){
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:red;'id='Country'></i></th>";
		}else{
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:black;' id='Country'></i></th>";			
		}
		$tTable=$tTable."<th class='hClk' style='text-align:center; width: 10%;' id='DXCC'>DXCC";
		if ($tSort=="DXCC"){
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:red;'id='DXCC'></i></th>";
		}else{
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:black;' id='DXCC'></i></th>";			
		}
		$tTable=$tTable."<th class='hClk' style='text-align:center; width: 10%;' id='SpotterContinent'>Cont";
		if ($tSort=="SpotterContinent"){
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:red;'id='SpotterContinent'></i></th>";
		}else{
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:black;' id='SpotterContinent'></i></th>";			
		}
		$tTable=$tTable."<th class='hClk' style='text-align:center; width: 10%;' id='Mode'>Mode";
		if ($tSort=="Mode"){
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:red;'id='Mode'></i></th>";
		}else{
			$tTable=$tTable."<i class='fas fa-sort fa-fw select' style='color:black;' id='Mode'></i></th>";			
		}
			$tTable=$tTable."<th style='z-index:100;' id='delete'></th>";			

$tTable=$tTable."<th style='text-align:center;'></th>";

$tTable=$tTable."</tr>";
$tTable=$tTable."</thead>";
//echo "TCOUNT: ".$tCount . PHP_EOL;
$i=0;
while ($i< $tCount){
	$tRow=$rows[$i];
	$tID=$tRow['id'];
	$tDX=$tRow['DX'];
	$tDXCC=$tRow['DXCC'];
	$tFrequency=$tRow['Frequency'];
	$tBand=$tRow['Band'];
	$tBandM=$tBand."M";
	$tMode=$tRow['Mode'];
	$tNote=$tRow['Note'];

/*	switch ($tMode){
		case "all":
			break;
		case "cw":
			$db->where("Mode","CW");
			break;
		case "phone":
			$db->where("(Mode = 'USB' or Mode = 'LSB')");
			break;
		case "digital":
			$db->where("Mode","RTTY");
			break;
		default:
			break;
	};	
*/
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
//	$tColor='';
//	$tColorB='';

//	if ($tRow['SpotterContinent']=='NA'){
//		$tColorC="class='bg-primary text-white' title='Spotter is from NA'";
//	}
//	$tVal="";
//	$tColor='tdo';
//	if ($i % 2 == 0) {
//		$tColor='tde';
//	}
	$j=0;
	$tDecF=addPeriods($tFrequency);
	$tTable=$tTable . "<tr class='clickme' frequency='$tFrequency' band='$tBand' note='$tNote' mode='$tMode' call='$tDX' id='$tID'>";
	$val=$tRow['DX'];
	$tTable=$tTable."<td $tColor style='text-align:center;'>" . $val. "</td>";
	$val=$tDecF;
	$tTable=$tTable."<td style='text-align:center;'>" . $val. "</td>";
	$val=$tRow['Spotter'];
	$tTable=$tTable."<td style='text-align:center;'>" . $val. "</td>";
	$val=$tRow['Webtime'];
	$tTable=$tTable."<td style='text-align:center;'>" . $val. "</td>";
	$note=$tRow['Note'];
	$tTable=$tTable."<td style='text-align:center;'>" . $note. "</td>";
	$val=$tRow['Country'];
	$tTable=$tTable."<td $tColorB style='text-align:center;'>" . $val. "</td>";
	$val=$tRow['DXCC'];
	$tTable=$tTable."<td style='text-align:center;'>" . $val. "</td>";
	$val=$tRow['SpotterContinent'];
	$tTable=$tTable."<td $tColorC style='text-align:center;'>" . $val. "</td>";
	$freqs = "14074000|7074000|21074000|1840000|3573000|5357000|10136000|18100000|24915000|28074000|50313000";

	if (strstr(strtolower($note),"ft8")>-1 ){
		$val="USB-D";
	}else if (strstr(trim($tFrequency),$freqs)>-1){
			   $val='USB-D';
	}else{
		$val=$tRow['Mode'];
	};
	$tTable=$tTable."<td style='text-align:center;'>" . $val. "</td>";

	
	$tTable=$tTable."<td>" . "<button class='btn btn-warning btn-sm BSdelete' title='Delete this spot' style='position:relative; top: -1px;
height:30px; width:80px;' id='b".$tID."' type='button'>".
			"<i class='fas fa-trash-alt fa-fw'></i>".
		"Delete".
		"</button></td>" .
		"</tr>";
  $i=$i+1;
}
$tTable.="</table>";
$tTable.="</div>";

echo $tTable;

function addPeriods($tF){
	return number_format($tF,0,".",".");
}
?>