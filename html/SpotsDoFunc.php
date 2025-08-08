<?php
/**
 * @author Howard Nurse, W6HN
 * 
 * This routine gets spots from the cluster, loads them into db
 * 
 * It must live in the html folder   
 */
function SpotsDo($call,$radio){
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$dRoot='/var/www/html';
	require_once($dRoot.'/classes/phpTelnet.php');
	require_once($dRoot.'/programs/GetSettingsFunc.php');
	require_once($dRoot.'/programs/SetSettingsFunc.php');
	require_once($dRoot.'/programs/GetClusterFunc.php');
	require_once($dRoot.'/programs/GetDXCC.php');
	require_once($dRoot.'/programs/getModeFromFrequency.php');
	require($dRoot.'/programs/sqldata.php');
	require_once($dRoot.'/classes/MysqliDb.php');
	$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	
//	use IDCT\Net\PhpTelnet;
	$tMyCall=$call;
	$tMyRadio=$radio;
	$tMyRadio=substr($tMyRadio, strlen($tMyRadio)-1);
	$aDist=['0|0|0'];
	
	$clusterID=GetField($tMyRadio,'ClusterID','MySettings');
	$clusterRetain=GetField($tMyRadio,'RetainTime','MySettings');
	$clusterIP=GetCluster($clusterID,'IP','Clusters');
	$clusterPort=GetCluster($clusterID,'Port','Clusters');
	setField($tMyRadio,'ClusterConnected',0);

	$telnet=new PhpTelnet();
	$keepGoing=1;
	if (!empty($_POST['openClose'])){
		$tOpenClose=$_POST['openClose'];
		if ($tOpenClose=='open'){
			$keepGoing=1;
			$telnet->connect($clusterIP, $clusterPort);
			echo $telnet->getlastErrorDescription();
		}else{
			$keepGoing=0;
			$telnet->write('QUIT');
			echo $telnet->getlastErrorDescription();
			return;
		}
	}else{
//			$telnet = new PhpTelnet();
			$keepGoing=1;
			$telnet->connect($clusterIP, $clusterPort);
			echo $telnet->getlastErrorDescription();
	}
	
	while($keepGoing==1)
	{
		if (!$telnet->isOK){
			exit;
		}
		$cmdResult = $telnet->read();
		if (strpos($cmdResult,"login:")>0 || strpos($cmdResult,"call:")>0){
			echo "here-call";
			$telnet->writeln($tMyCall);
		};
			setField($tMyRadio,'ClusterConnected',1);
	//	echo $cmdResult."\n";
		if (strpos($cmdResult,"DX de")===0){
			echo $cmdResult;
			$tFrom=substr($cmdResult, strpos($cmdResult,"de")+3);
			$tFrom=substr($tFrom, 0, strpos($tFrom,":"));
			$tFreq=substr($cmdResult, strpos($cmdResult,":")+2);
			$tFreq1=trim(substr($tFreq, 0, strpos($tFreq,".")+2));
			$tFreq1=str_replace(".", "", $tFreq1)."00";
			$tDX=ltrim(substr($cmdResult, strpos($cmdResult,".")+3));
			$tDX=trim(substr($tDX, 0, strpos($tDX," ")));
		$tDX="y".$tDX;
			$tNote=substr($cmdResult, strpos($cmdResult,".")+13);
			$tNote=trim(substr($tNote, 0, 16));
			$tNote=str_replace("<", "&lt", $tNote);
			$tNote=str_replace(">", "&gt", $tNote);
			$tTime=substr($cmdResult, strpos($cmdResult,".")+33);
			$tTime=trim(substr($tTime, 0, 4));
			$tBand=GetBandFromFrequency($tFreq1);
			$tDXCC=GetLocationData($tDX);
			$aDXCC=explode("|", $tDXCC);
			$tDist=getDXDistance($aDXCC[3],$aDXCC[4]);
			$aDist=explode('|',$tDist);
	//		print_r($tDist);
			$tSpotter=GetLocationData($tFrom);
	//		print_r($tSpotter);
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
//					$db->query($sql);
//			$db->where(time().'-Webdate>'.$clusterRetain*60);
//			$db->delete('Spots');
//			echo "here2";
		}else{
	//		return "error";
		}
		
	}
	return "OK";
}

function getDXDistance($hisLat,$hisLon){
	$dRoot="/var/www/html";
	require_once($dRoot.'/programs/GetDistanceFunc.php');
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");	
	$user=1;
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$db->where('User',"1");
	$rowDist=$db->getOne('Callbook');
	$dxlat=$hisLat;
	$dxlon=$hisLon;
	$db->where('uID',$user);	
	$rowDist=$db->getOne('Users');
	if ($rowDist){
		$mylat=$rowDist['My_Latitude'];
		$mylon=$rowDist['My_Longitude'];
		$dist=getDistance($dxlat,$dxlon,$mylat,$mylon);
//		print_r($dist);
		return $dist;
	}else{
		return ['0|0|0'];
	}
}


?>