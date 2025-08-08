<?php
/**
 * @author Howard Nurse, W6HN
 * 
 * This routine gets spots from the cluster, loads them into db
 * 
 * It must live in the html folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
$dRoot='/var/www/html';
require_once('/var/www/html/classes/phpTelnet.php');
require_once($dRoot.'/programs/GetBand.php');
require_once($dRoot.'/programs/GetSettingsFunc.php');
require_once($dRoot.'/programs/GetClusterFunc.php');
require_once($dRoot.'/programs/GetDXCC.php');
require_once($dRoot.'/programs/getModeFromFrequency.php');
require($dRoot.'/programs/sqldata.php');
require_once($dRoot.'/classes/MysqliDb.php');
$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);

use IDCT\Net\PhpTelnet;
$tMyCall=$argv[1];
$tMyRadio=$argv[2];
$tMyRadio=substr($tMyRadio, strlen($tMyRadio)-1);
$tOpenClose=$_POST['openClose'];
$clusterID=GetField($tMyRadio,'ClusterID','MySettings');
$clusterRetain=GetField($tMyRadio,'RetainTime','MySettings');
$clusterIP=GetCluster($clusterID,'IP','Clusters');
$clusterPort=GetCluster($clusterID,'Port','Clusters');
$telnet='';
$keepGoing=1;
if (strlen($tOpenClose)>0){
	if ($tOpenClose=='open'){
		$telnet = new PhpTelnet();
		$keepGoing=1;
		$telnet->connect($clusterIP, $clusterPort);
//		echo $telnet->getlastErrorDescription();
	}else{
		$keepGoing=0;
		$telnet->write('QUIT');
//		echo $telnet->getlastErrorDescription();
		return;
	}
}else{
	$keepGoing=1;
		$telnet = new PhpTelnet();
		$keepGoing=1;
		$telnet->connect($clusterIP, $clusterPort);
//		echo $telnet->getlastErrorDescription();
}

while($keepGoing==1)
{
	$cmdResult = $telnet->read();
	if ($cmdResult=="login:" || strpos($cmdResult,"call:")>0){
		$telnet->writeln($tMyCall);
	}
//	$s="DX de EA3AVQ: 10489520.0  YB5QZ        QO 100 CW                      1330Z"
//	$s="DX de TA2NC:    144174.0  G4DCV        <ES> FT8 -13 dB 2014 Hz        1326Z";
//	echo $cmdResult."\n";
	if (strpos($cmdResult,"DX de")===0){
		$tFrom=substr($cmdResult, strpos($cmdResult,"de")+3);
		$tFrom=substr($tFrom, 0, strpos($tFrom,":"));
		$tFreq=substr($cmdResult, strpos($cmdResult,":")+1);
		$tFreq1=trim(substr($tFreq, 0, strpos($tFreq,".")+2));
		$tFreq1=str_replace(".", "", $tFreq1)."00";
		$tDX=trim(substr($cmdResult, strpos($cmdResult,".")+3));
		$tDX=trim(substr($tDX, 0, strpos($tDX," ")));
//		$tDX="x".$tDX;
		$tNote="";
		$tNote=substr($cmdResult, strpos($cmdResult,".")+13);
		$tNote=trim(substr($tNote, 0, 16));
		$tNote=str_replace("<", "&lt", $tNote);
		$tNote=str_replace(">", "&gt", $tNote);
		$tTime=substr($cmdResult, strpos($cmdResult,".")+48);
		$tTime=trim(substr($tTime, 0, 4));
		$tBand=GetBandFromFrequency($tFreq1);
		$tDXCC=GetLocationData($tDX);
		$aDXCC=explode("|", $tDXCC);
		$tDist=getDXDistance($aDXCC[3],$aDXCC[4]);
		$aDist=explode('|',$tDist);
		$tSpotter=GetLocationData($tFrom);
		$aSpotter=explode("|",$tSpotter);
		$tSDist=getDXDistance($aSpotter[3],$aSpotter[4]);
		$aSDist=explode("|",$tSDist);
		if ($aDXCC[1]==291){
			
		}

		$data = Array (
			'Radio'=>$tMyRadio,
			'Folder'=>'Inbox',
			'Spotter'=>$tFrom,
			'Frequency'=>$tFreq1,
			'Band'=>$tBand,
			'DX'=>$tDX,
			'Webtime'=>$tTime,
			'Webdate'=>time(),
			'Note'=>$tNote,
			'Source'=>'Telnet',
			'Longitude'=>$aDXCC[4],
			'Latitude'=>$aDXCC[3],
			'Email'=>'0',
			'Push'=>'0',
			'Hide'=>'0',
			'Tune'=>'0',
			'County'=>'',
			'State'=>'',
			'Country'=>$aDXCC[2],
			'Continent'=>$aDXCC[6],
			'DXCC'=>$aDXCC[1],
			'Mode'=>GetMode($tFreq1),
			'DXBearing'=>$aDist[3],
			'DXDistance'=>$aDist[1],
			'SpotterContinent'=>'',
			'Usecolor'=>'0',
			'Backcolor'=>0xfff,
			'Forecolor'=>0x0,
			'SpotterContinent'=>$aSpotter[6],
			'SpotterDistance'=>$aSDist[1]
		);	
		$db->insert ('Spots', $data);
		
		$now = time();
					
					// Build the DELETE query
		$sql = "DELETE FROM Spots WHERE Webdate <= " . ($now - 60 * $clusterRetain);
		$db->query($sql);
	}
	
}

function getDXDistance($hisLat,$hisLon){
	$dRoot="/var/www/html";
	require_once($dRoot.'/programs/GetDistanceFunc.php');
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");	
	$user=2;
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$db->where('User',"2");
	$rowDist=$db->getOne('Callbook');
	$dxlat=$hisLat;
	$dxlon=$hisLon;
	$db->where('uID',$user);	
	$rowDist=$db->getOne('Users');
	$mylat=$rowDist['My_Latitude'];
	$mylon=$rowDist['My_Longitude'];
	$dist=getDistance($dxlat,$dxlon,$mylat,$mylon);
	return $dist;
}


?>