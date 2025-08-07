<?php
$adif_file = $_POST["file"];
$logname = $_POST["logname"];
$uid=$_POST['uid'];
$mycall = "";
$reg = "";
$num = "";
$recNumber=0;
set_time_limit(120);
$count = "0";
$missed = "0";
require "/var/www/html/programs/sqldata.php";
require_once "/var/www/html/classes/MysqliDb.php";
$db = mysqli_connect("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
$db1 = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if (!$db) {
    die('Could not connect: ' . mysqli_error());
}

$Amplifier = "";
$ARRL_Section="";
$Band = "";
$BeamHeading = "";
$Callsign = "";
$Comment="";
$County="";
$CQZone = "";
$dend = "";
$dstart = "";
$DXCC = "";
$His_Age="";
$His_Continent = "";
$His_Country = "";
$His_County = "";
$His_Email = "";
$His_Grid = "";
$His_Name = "";
$His_Power = "";
$His_QSL_Address="";
$His_QTH = "";
$His_RST = "";
$His_URL = "";
$His_State = "";
$ID=0;
$IOTA = "";
$ITUZone = "";
//$logname="";
$LoTWSent="";
$LOTW_LastQSL="";
$MobileID="";
$Mode = "";
$My_Call = "";
$My_Grid = "";
$My_RST = "";
$Note = "";
$Propagation_Mode = "";
$QSL_Note="";
$QSL_Received = "N";
$QSL_Received_Date = "N";
$QSL_Sent = "N";
$QSL_Sent_Date = "N";
$QSL_Via="";
$Receiver = "";
$RXAntenna = "";
$RX_Frequency = "";
$SatName = "";
$SatMode = "";
$SerialReceived = "";
$SerialSent = "";
$TenTen = "";
$Time_Start = "";
$Time_End = "";
$Transmitter = "";
$TX_Frequency = "";
$WPX_Prefix = "";
$tend = "";
$TenTen="";
$Time_End = "";
$Time_End_Plain = "";
$Time_Start_Plain = "";
$Time_Start = "";
$tstart = "";
$TX_Power = "";
$TXPower = "";
$TXAntenna = "";
$VEProvince="";
$WPX_Prefix="";
//if (!file_exists("/var/www/html/my/uploads/" . $adif_file)){
if (!file_exists("/var/www/fi/" . $adif_file)){
	////echo "$adif_file not found in uploads folder.";
	return;
}
$file = fopen("/var/www/fi/" . $adif_file, "r");
if (!$file){
	////echo "$adif_file not found.";
	return;
}
$line = fgets($file);
////echo $line."\n";
$lA = strpos(strtolower($line), "<eoh>");
while ($lA == 0) {
    $line .= fgets($file);
    $lA = strpos(strtolower($line), "<eoh>");
    if (strpos(strtolower($line), "<eoh>") > 0) {
        //comment to log?
    }
    if (strpos(strtolower($line), "<eor>") > 0) {
        $lA = 1;
    }
}
if (strpos(strtolower($line), "<eoh>") > 0) {
    $line = substr($line, $lA + 5);
}
while (feof($file) === false) {
//	////echo 'feof'.feof($file);	    
    if (strpos(strtolower($line), "<eor>") === false) {
        $line .= Read_Record($file);
    //}else{
		   
    }
    ////echo feof($file)."\n";
    $dend="";
    $line = str_replace(chr(92), " ", $line);
    $line = str_replace(chr(10), "", $line);
    $line = str_replace(chr(13), "", $line);
    $line = str_replace("'", "`", $line);
    $line = str_replace('"', '`', $line);
    ////echo $line."\n";
    while (strpos($line, "<") !== false) {
        $pos = strpos($line, "<");
        if ($pos !== false) {
            $line = substr($line, $pos);
            if (strpos($line, ":") > 0) {
                $stemp = substr($line, 1, (strpos($line, ":") - 1));
            } else {
                $stemp = substr($line, 1);
            }
            if (strtolower(substr($stemp, 0, 3)) == "eor") {
                $line = substr($line, strpos($line, ">") + 1);
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
                if (!strlen($His_County>1)){
	                $tComma=strstr($His_State,",");
	                if ($tComma){
						$His_County=substr($His_State,3);
						$His_State=substr($His_State,0,2);
	                }
                }
                //save to table
                $find = "INSERT INTO Logbook " . 
                	"(Amplifier,Callsign,Mode,Band,My_RST,His_RST,His_Name,Time_Start,Time_End,MyCall,Note,Tx_Frequency,Rx_Frequency,QSL_S,QSL_R,His_QTH," . 					"His_Email,His_URL,His_County,His_State,His_Grid,His_Country,His_Continent,His_Power,Propagation_Mode,LoTWSent,LOTW_LastQSL," .
                    "Receiver,DXCC,CQZone,ITUZone,WPX_Prefix,IOTA,TX_Power,Sat_Name,Sat_Mode," . 					"Serial_Sent,Serial_Received,Ten_Ten,Transmitter,Time_Start_Plain,Time_End_Plain,Logname,ID,TX_Antenna,VE_Province) " .
                    "VALUES" . 					
                    " ('$Amplifier','$Callsign','$Mode','$Band','$My_RST','$His_RST','$His_Name','$Time_Start','$Time_End','$My_Call','$Comment'," .
					"'$TX_Frequency','$RX_Frequency','$QSL_Sent','$QSL_Received','$His_QTH','$His_Email','$His_URL','$His_County','$His_State','$His_Grid'," . 
					"'$His_Country','$His_Continent','$His_Power','$Propagation_Mode','$LoTWSent','$LOTW_LastQSL','$Receiver'," . 
					"'$DXCC','$CQZone','$ITUZone','$WPX_Prefix','$IOTA','$TX_Power','$SatName','$SatMode','$SerialSent','$SerialReceived','$TenTen','$Transmitter'," . 					"'$Time_Start_Plain','$Time_End_Plain','$logname','$ID','$TXAntenna','$VEProvince')";

				if (mysqli_query($db, $find)){
					$recNumber=$recNumber+1;
					$db1->where('uID',$uid);
					$tData=Array('Count'=>$recNumber);
					$db1->update('Users',$tData);
//			    ////echo "sent2 ".$recNumber;
				}else{
                    $missed = $missed + 1;
                    ////echo "Error adding record: " . mysqli_error($db) . "\n";
					
				}
				$Amplifier = "";
				$ARRL_Section="";
				$Band = "";
				$BeamHeading = "";
				$Callsign = "";
				$Comment="";
				$County="";
				$CQZone = "";
				$dend = "";
				$dstart = "";
				$DXCC = "";
				$His_Age="";
				$His_Continent = "";
				$His_Country = "";
				$His_County = "";
				$His_Email = "";
				$His_Grid = "";
				$His_Name = "";
				$His_Power = "";
				$His_QSL_Address="";
				$His_QTH = "";
				$His_RST = "";
				$His_URL = "";
				$His_State = "";
				$ID=0;
				$IOTA = "";
				$ITUZone = "";
//				$logname="";
				$LoTWSent="";
				$LOTW_LastQSL="";
				$MobileID="";
				$Mode = "";
				$My_Call = "";
				$My_Grid = "";
				$My_RST = "";
				$Note = "";
				$Propagation_Mode = "";
				$QSL_Note="";
				$QSL_Received = "N";
				$QSL_Received_Date = "N";
				$QSL_Sent = "N";
				$QSL_Sent_Date = "N";
				$QSL_Via="";
				$Receiver = "";
				$RXAntenna = "";
				$RX_Frequency = "";
				$SatName = "";
				$SatMode = "";
				$SerialReceived = "";
				$SerialSent = "";
				$TenTen = "";
				$Time_Start = "";
				$Time_End = "";
				$Transmitter = "";
				$TX_Frequency = "";
				$WPX_Prefix = "";
				$tend = "";
				$TenTen="";
				$Time_End = "";
				$Time_End_Plain = "";
				$Time_Start_Plain = "";
				$Time_Start = "";
				$tstart = "";
				$TX_Power = "";
				$TXAntenna = "";
				$VEProvince="";
				$WPX_Prefix="";

                $count = $count + 1;
                //echo $count."\n";
            } else {
                $stemp = Find_Field($stemp);
                if (strlen($stemp) > 0) {
                    $len = intval(substr($line, strpos($line, ":") + 1));
                    $stemp1 = substr($line, strpos($line, ">") + 1, $len);
                    //data includes <, bad len
                    if (strpos($stemp1, "<") > 0) {
                        $len = substr($stemp1, "<") - 1;
                        $stemp1 = substr($stemp1, 0, $len);
                    }
                    //no spaces
                    $stemp1 = trim($stemp1);
                }
                switch ($stemp) {
                    case "Callsign":
                        $Callsign = $stemp1;
                        break;
                    case "Mode":
                        $Mode = $stemp1;
                        break;
                    case "Band":
                        $Band = $stemp1;
                        break;
                    case "My_RST":
                        $My_RST = $stemp1;
                        break;
                    case "His_RST":
                        $His_RST = $stemp1;
                        break;
                    case "His_Name":
                        $His_Name = $stemp1;
                        break;
                    case "tend":
                        $tend = $stemp1;
                        break;
                    case "tstart":
                        $tstart = $stemp1;
                        break;
                    case "dend":
                        $dend = $stemp1;
                        break;
                    case "dstart":
                        $dstart = $stemp1;
                        break;
                    case "My_Call":
                        $My_Call = $stemp1;
                        break;
                    case "Note":
                        $Comment = $stemp1;
                        break;
                    case "Propagation_Mode":
                        $Propagation_Mode = $stemp1;
                        break;
                    case "TX_Frequency":
                        $aF = explode(".", $stemp1);
                        $aF[1] = str_pad($aF[1], 6, "0");
                        $TX_Frequency = $aF[0] . $aF[1];
                        break;
                    case "RX_Frequency":
                        $aF = explode(".", $stemp1);
                        $aF[1] = str_pad($aF[1], 6, "0");
                        $RX_Frequency = $aF[0] . $aF[1];
                        break;
                    case "QSL_Sent":
                        $QSL_Sent = $stemp1;
                        break;
                    case "QSL_Received":
                        $QSL_Received = $stemp1;
                        break;
                    case "His_QTH":
                        $His_QTH = $stemp1;
                        break;
                    case "His_Email":
                        $His_Email = $stemp1;
                        break;
                    case "His_URL":
                        $His_URL = $stemp1;
                        break;
                    case "His_County":
                        if (strpos($stemp1, ",") > 0) {
                            $stemp1 = substr($stemp1, strpos($stemp1, ",") + 1);
                        }
                        $County = $stemp1;
                        break;
                    case "His_State":
                        $His_State = $stemp1;
                        break;
                    case "His_Country":
                        $His_Country = $stemp1;
                        break;
                    case "His_Continent":
                        $His_Continent = $stemp1;
                        break;
                    case "His_Grid":
                        $His_Grid = $stemp1;
                        break;
                    case "DXCC":
                        $DXCC = $stemp1;
                        break;
                    case "CQZone":
                        $CQZone = $stemp1;
                        break;
                    case "ITUZone":
                        $ITUZone = $stemp1;
                        break;
                    case "WPX_Prefix":
                        $WPX_Prefix = $stemp1;
                        break;
                    case "IOTA":
                        $IOTA = $stemp1;
                        break;
                    case "QSL_Address":
                        $QSL_Address = $stemp1;
                        break;
                    case "TX_Power":
                        $TX_Power = $stemp1;
                        break;
                    case "Logname":
                        $logname = $stemp1;
                        break;
                    case "MobileID":
                        $MobileID = $stemp1;
                        break;
                    case "ID":
                        $ID = $stemp1;
                        break;
                }
                $line = substr($line, strpos($line, ">") + 1);
            }
        }

    }
}
fclose($file);
//sendMsg(time(),'Done');
if ($count-$missed==0){
	//echo " NO records were imported.";
}elseif ($count-$missed==1){
	//echo "ONE record was imported.";
}else{
	$added=$count-$missed;
	if ($missed==1){
		
	//echo "$added records were imported, one was skipped (dupe).";
	}else{
	
	//echo "$added records were imported,  $missed were skipped (dupes).";	
	}
}


//FUNCTIONS

function Read_Record($file)
{
	$line1="";
	$nextline="";
    $xint = 0;
    while (strpos(strtolower($line1), "eor>") == 0) {
        $xint = $xint + 1;
        $line1 = fgets($file);
        $nextline = $nextline . $line1;
        if ((strlen($line1) == 0 && feof($file)) || $xint > 100) {
            return $nextline;
        }
    }
    $nextline = str_replace(chr(0), "", $nextline);
    $nextline = str_replace(chr(16), "", $nextline);
    $nextline = str_replace(chr(10), "", $nextline);
    $nextline = str_replace(chr(13), "", $nextline);
    return $nextline;
}

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
            $stemp1 = "";
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
