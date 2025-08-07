<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine checks for a new Fldigi log record and, if found, imports it
 * 
 * It must live in the programs folder   
 */
 
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	$tLog='';//"/home/pi/.fldigi/temp/log.adif";
	$dRoot='/var/www/html';
	require_once ($dRoot."/programs/SetLogFunc.php");
	require_once($dRoot."/programs/GetSettingsFunc.php");
	require_once($dRoot."/programs/GetBand.php");
	$tUserName=$_SESSION['name'];
	$tMyCall=$_SESSION['call'];
	$tMyRadio=include($dRoot."/GetSelectedRadioInc.php");
	$tLogName=GetField($tMyRadio,'LogName','MySettings');
	if (file_exists($tLog)){
		$trim = trim(file_get_contents($tLog));
		if ($trim===false){
			exit;
		}
		$comms1=$trim;
		$comms=explode("<",$comms1);
		$tArray=array();
		foreach ($comms as $comm)
		{
			if (strlen($comm)>0)
			{
				$tag=strtolower(substr($comm, 0, strpos($comm, ":")));
				$dbField=Find_Field($tag);
				$data=trim(substr($comm, strpos($comm,">")+1));
				if ($dbField=='dstart'){
					$dstart=$data;
					$data='';
				}
				if ($dbField=='dend'){
					$dend=$data;
					$data='';
				}
				if ($dbField=='tstart'){
					$tstart=$data;
					$data='';
				}
				if ($dbField=='tend'){
					$tend=$data;
					$data='';
				}
				if ($dbField=='RX_Frequency'||$dbField=='TX_Frequency'){
					$data=str_replace(".", "", $data);
				}
				if (strlen($data)>0){
					$tArray[$dbField]=$data;
				}
			}
		}
	    if (strlen($dstart) > 0 && strlen($tstart) > 0) {
	        $yr = substr($dstart, 0, 4);
	        $da = substr($dstart, 6);
	        $mo = substr($dstart, 4, 2);
	        $mo = ($mo - 1) * 3;
	        $mon = substr("JANFEBMARAPRMAYJUNJULAUGSEPOCTNOVDEC", $mo, 3);
	        $tstart = substr($tstart, 0, 4);
	        $Time_Start_Plain = $tstart . " " . $da . "-" . $mon . "-" . $yr;
	        $Time_Start = strtotime($Time_Start_Plain . " GMT");
	    } else {
	        $Time_Start_Plain = "";
	        $Time_Start = "";
	    }
	    if (strlen($dend) > 1 && strlen($tend) > 1) {
	        $yr = substr($dend, 0, 4);
	        $da = substr($dend, 6);
	        $mo = substr($dend, 4, 2);
	        $mo = ($mo - 1) * 3;
	        $mon = substr("JANFEBMARAPRMAYJUNJULAUGSEPOCTNOVDEC", $mo, 3);
	        $tend = substr($tend, 0, 4);
	        $Time_End_Plain = $tend . " " . $da . "-" . $mon . "-" . $yr;
	        $Time_End = strtotime($Time_End_Plain . " GMT");
	    } else {
	        $tend = "";
	        $dend = "";
	        $Time_End_Plain = "";
	        $Time_End = "";
	    }
	    $tArray['Time_Start_Plain']=$Time_Start_Plain;
	    $tArray['Time_Start']=$Time_Start;
	    $tArray['Time_End_Plain']=$Time_End_Plain;
	    $tArray['Time_End']=$Time_End;
	    if ($tLogName==''){
		    $tLogName='fldigi';
	    }
	    $tArray['Logname']=$tLogName;
	    $tArray['MyCall']=$tMyCall;
	    $tArray['StationCall']=$tMyCall;
	    $tArray['Band']=GetBandFromFrequency($tArray['RX_Frequency']).'M';
	    unlink($tLog);
		echo addLogRecord('0',$tArray);
	}
	exit;

function Find_Field($adi)
{
    switch (strtolower($adi)) {
        case "address":
            $stemp1 = "QSL_Address";
            break;
        case "age":
            $stemp1 = "His_Age";
            break;
        case "amplifier":
            $stemp1 = "Amplifier";
            break;
        case "app_commcat_id";
            $stemp1 = "ID";
            break;
        case "app_commcat_logname":
            $stemp1 = "Logname";
            break;
        case "app_commcat_mobileid";
            $stemp1 = "MobileID";
            break;
        case "app_lotw_lastqsl":
            $stemp1 = "LOTW_LastQSL";
            break;
        case "app_lotw_sent":
            $stemp1 = "LoTW_Sent";
            break;
        case "arrl_sect":
            $stemp1 = "ARRL_Section";
            break;
        case "band":
            $stemp1 = "Band";
            break;
        case "call":
            $stemp1 = "Callsign";
            break;
        case "cnty":
            $stemp1 = "His_County";
            break;
        case "comment":
            $stemp1 = "Note";
            break;
        case "cont":
            $stemp1 = "His_Continent";
            break;
        case "country":
            $stemp1 = "His_Country";
            break;
        case "contest_id":
            $stemp1 = "Contest_ID";
            break;
        case "cqz":
            $stemp1 = "CQZone";
            break;
        case "dxcc":
            $stemp1 = "DXCC";
            break;
        case "email":
            $stemp1 = "His_Email";
            break;
        case "freq":
            $stemp1 = "RX_Frequency";
            break;
        case "freq_tx":
            $stemp1 = "TX_Frequency";
            break;
        case "radio":
            $stemp1 = "Transmitter";
            break;
        case "gridsquare":
            $stemp1 = "His_Grid";
            break;
        case "iota":
            $stemp1 = "IOTA";
            break;
        case "ituz":
            $stemp1 = "ITUZone";
            break;
        case "mode":
            $stemp1 = "Mode";
            break;
        case "my_gridsquare":
        	$stemp1 = "My_Grid";
        	break;
        case "name":
            $stemp1 = "His_Name";
            break;
        case "operator":
            $stemp1 = "My_Call";
            break;
        case "prop_mode":
            $stemp1 = "Propagation_Mode";
            break;
        case "qslmsg":
            $stemp1 = "Qsl_Note";
            break;
        case "qslrdate":
            $stemp1 = "Qsl_Received_Date";
            break;
        case "qslsdate":
            $stemp1 = "Qsl_Sent_Date";
            break;
        case "qsl_rcvd":
            $stemp1 = "QSL_Received";
            break;
        case "qsl_sent":
            $stemp1 = "QSL_Sent";
            break;
        case "qsl_via":
            $stemp1 = "Qsl_Via";
            break;
        case "qso_date":
            $stemp1 = "dstart";
            break;
        case "qso_date_off":
            $stemp1 = "dend";
            break;
        case "qth":
            $stemp1 = "His_QTH";
            break;
        case "rst_rcvd":
            $stemp1 = "My_RST";
            break;
        case "rst_sent":
            $stemp1 = "His_RST";
            break;
        case "rx_pwr":
            $stemp1 = "His_Power";
            break;
        case "sat_mode":
            $stemp1 = "Sat_Mode";
            break;
        case "sat_name":
            $stemp1 = "Sat_Name";
            break;
        case "srx":
            $stemp1 = "Serial_Received";
            break;
        case "state":
            $stemp1 = "His_State";
            break;
        case "station_callsign":
        	$stemp1 = "StationCall";
        	break;
        case "stx":
            $stemp1 = "Serial_Sent";
            break;
        case "ten_ten":
            $stemp1 = "Ten_Ten";
            break;
        case "time_off":
            $stemp1 = "tend";
            break;
        case "time_on":
            $stemp1 = "tstart";
            break;
        case "tx_pwr":
            $stemp1 = "TX_Power";
            break;
        case "ve_prov":
            $stemp1 = "VE_Province";
            break;
        case "web":
            $stemp1 = "His_URL";
            break;
        case "wpx":
            $stemp1 = "WPX_Prefix";
            break;
        default:
            $stemp1 = "";
    }
    return $stemp1;
}
?>