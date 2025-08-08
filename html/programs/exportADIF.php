<?php
    // generate ADIF formatted report for export

//function export(){

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require_once($dRoot."/programs/zipMe.php");
require ($dRoot."/programs/sqldata.php");
require_once ($dRoot."/classes/MysqliDb.php");
ini_set('max_execution_time', 0);	
$whichLog=$_POST['log'];
$myCall=$_POST['call'];
$sel=$_POST['sel'];
$type=$_POST['type'];
$uid=$_POST['uid'];
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
$db1 = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);

if ($sel==1){
	$db->where('Sel',1);
}

$db->orderBy('Time_Start','ASC');

$whichLog1=$whichLog;
if ($whichLog!='x'){
	$db->where('Logname',$whichLog);
}else{
	$whichLog1="All_Logs";
}

//$db->where("LoTWSent","1","<>"); //confusing when repeating downloads

$qs=$db->paginate("Logbook",1);
$pages=$db->totalPages;
if ($pages==0){
	echo "0";
	exit;
}
$recNumber=0;	

$adif_file="/dev/shm/rigpi".$myCall.str_replace(" ", "_", $whichLog1).".adi";
$adif_zip="/dev/shm/rigpi".$myCall.str_replace(" ", "_", $whichLog1).".zip";
$i=0;
$adifstr = "";
$adifstr = "RigPi Log ADIF Export\n";
$adifstr .= "Created " . date('l jS \of F Y h:i:s A') . " by $myCall\n";
$adifstr .="QSO info from ".$whichLog." log.\n";
$adifstr .= "<PROGRAMID:5>RigPi\n";
$adifstr .= "<PROGRAMVERSION:1>4\n";

$adifstr .= "<EOH>\n\n";
$gotOne=false;
for ($p=1;$p<=$pages;$p++){
if ($sel==1){
	$db->where('Sel',1);
}
$db->orderBy('Time_Start','ASC');
if ($whichLog!='x'){
	$db->where('Logname',$whichLog);
}
//$db->where("LoTWSent","1","<>"); confusing for multiple downloads

$qs=$db->paginate("Logbook",$p);
if ($type=='full'){
	foreach($qs as $row => $innerArray){
		foreach($innerArray as $innerRow => $value){
	        if ($gotOne==true){
		        $adifstr.="   ";
	        }
			$tHis_County="";
			$tHis_State="";
			$tMy_County="";
			$tMy_State="";
			$gotOne=false;
			$col_name=$innerRow;
			$val=$value;
			if (!($col_name == "")) {
		        if ($col_name == "Callsign") {
		            $adifstr .= "<CALL:" . strlen($val) . ">" . $val;
		            $gotOne=true;
		        }elseif ($col_name == "MyCall") {
		            $len = strlen($val);
		            $adifstr .= "<OPERATOR:" . $len . ">" . $val;
		            $gotOne=true;
		        }elseif ($col_name == "Band") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<BAND:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        }elseif ($col_name == "Mode") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $temp=$val;
		                if ($temp == "USB" || $temp == "LSB")
		                {
		                    $temp="SSB";
		                }
		                $adifstr .= "<MODE:" . $len . ">" . $temp;
						$gotOne=true;
		            }
		     	}elseif ($col_name == "Time_Start_Plain") {
			     		if (strlen($val)==16)
			     		{
			                $len = 4;
			                $ti = substr($val, 0, 4);
			                $adifstr .= "<TIME_ON:" . $len . ">" . $ti;
			                $len = 8;
			                $mo = strtolower(substr($val, 8, 3));
			                $mon = (strpos("janfebmaraprmayjunjulaugsepoctnovdec", $mo) / 3) + 1;
			                $mon = "0" . $mon;
			                $day = substr($val, 5, 2);
			                $yr = substr($val, 12, 4);
			                $mon = substr($mon, strlen($mon) - 2);
			                $adifstr .= "\n   <QSO_DATE:" . $len . ">" . $yr . $mon . $day;
			                $gotOne=true;
			     		}else{
				     		$gotOne=false;
			     		}
		        } elseif ($col_name == "QSL_S") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<QSL_SENT:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "QSL_R") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<QSL_RCVD:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "DXCC") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<DXCC:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "His_Continent") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<CONT:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "His_Country") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<COUNTRY:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "CQZone") {
		            $len = strlen($val);
		            if ($len > 0) {
		                if ($len==1) {
		                    $stemp = str_pad($val,2,"0");
		                    $len = 2;
		                }else{
		                    $stemp=$val;
		                }
		                $adifstr .= "<CQZ:" . $len . ">" . $stemp;
						$gotOne=true;
		            }
		        } elseif ($col_name == "ITUZone") {
		            $len = strlen($val);
		            if ($len > 0) {
		                if ($len==1) {
		                    $stemp = str_pad($val,2,"0");
		                    $len = 2;
		                }else{
		                    $stemp=$val;
		                }
		                $adifstr .= "<ITUZ:" . $len . ">" . $stemp;
						$gotOne=true;
		            }
		        } elseif ($col_name == "IOTA") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<IOTA:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "His_Grid") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<GRIDSQUARE:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "His_County") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $tHis_County=$val;
		                if (strlen($tHis_State)>0){
		                    $tSt=$tHis_State.",".$tHis_County;
		                    $adifstr .= "<STATE:" . strlen($tSt) . ">" . $tSt;
							$gotOne=true;
		                }
		            }
		        } elseif ($col_name == "His_State") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $tHis_State=$val;
		                if (strlen($tHis_County)>0){
		                	$tSt=$tHis_State.",".$tHis_County;
							$adifstr .= "<STATE:" . strlen($tSt) . ">" . $tSt;
							$gotOne=true;
		            	}
		            }
		        } elseif ($col_name == "His_Name") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<NAME:" . $len . ">" . $val;
						$gotOne=true;
		            }
		
		        } elseif ($col_name == "Rx_Frequency") {
		            $fr = $val;
		            $len = strlen($fr);
		            if ($len > 3) {
		                $fr = substr($fr, 0, $len - 6) . "." . substr($fr, $len - 6);
		                $adifstr .= "<FREQ:" . ($len + 1) . ">" . $fr;
						$gotOne=true;
		            }
		
		        } elseif ($col_name == "Tx_Frequency") {
		            $fr = $val;
		            $len = strlen($fr);
		            if ($len > 3) {
		                $fr = substr($fr, 0, $len - 6) . "." . substr($fr, $len - 6);
		                $adifstr .= "<FREQ_RX:" . ($len + 1) . ">" . $fr;
						$gotOne=true;
		            }
		        } elseif ($col_name == "My_Latitude") {
		            $la = ($val);
		            $len = strlen($la);
		            if ($len > 3) {
		                if (substr($la, 0, 1) == "-") {
		                    $pre = "S";
		                    $la = substr($la, 1);
		                } else {
		                    $pre = "N";
		                    if (substr($la, 0, 1) == "+") {
		                        $la = substr($la, 1);
		                    }
		                }
		                $deg = intval($la);
		                $pt1 = strlen($deg);
		                switch ($pt1) {
		                    case 0:
		                        $deg = "000" . $deg;
		                        break;
		                    case 1:
		                        $deg = "00" . $deg;
		                        break;
		                    case 2:
		                        $deg = "0" . $deg;
		                        break;
		                }
		                $min = ($la - $deg) * 60;
		                $pt = strpos($min, ".");
		                switch ($pt) {
		                    case 0:
		                        if ($min == "0") {
		                            $min = "00.0";
		                        } elseif (strlen($min == 1)) {
		                            $min = "0" . $min . ".0";
		                        } else {
		                            $min = $min . ".0";
		                        }
		                        break;
		                    case 1:
		                        $min = "0" . $min;
		                        if (strlen($min) == 3) {
		                            $min = $min . "0";
		                        }
		                        break;
		                    case 2:
		                        if (strlen($min) == 3) {
		                            $min = $min . "0";
		                        }
		                        break;
		                }
		                $lat = $pre . $deg . " " . $min;
		                $len = strlen($lat);
		                $adifstr .= "<MY_LAT:" . $len . ">" . $lat;
						$gotOne=true;
		            }
		        } elseif ($col_name == "My_Longitude") {
		            $la = ($val);
		            $len = strlen($la);
		            if ($len > 3) {
		                if (substr($la, 0, 1) == "-") {
		                    $pre = "W";
		                    $la = substr($la, 1);
		                } else {
		                    $pre = "E";
		                    if (substr($la, 0, 1) == "+") {
		                        $la = substr($la, 1);
		                    }
		                }
		                $deg = intval($la);
		                $pt1 = strlen($deg);
		                switch ($pt1) {
		                    case 0:
		                        $deg = "000" . $deg;
		                        break;
		                    case 1:
		                        $deg = "00" . $deg;
		                        break;
		                    case 2:
		                        $deg = "0" . $deg;
		                        break;
		                }
		                $min = ($la - $deg) * 60;
		                $pt = strpos($min, ".");
		                switch ($pt) {
		                    case 0:
		                        if ($min == "0") {
		                            $min = "00.0";
		                        } elseif (strlen($min == 1)) {
		                            $min = "0" . $min . ".0";
		                        } else {
		                            $min = $min . ".0";
		                        }
		                        break;
		                    case 1:
		                        $min = "0" . $min;
		                        if (strlen($min) == 3) {
		                            $min = $min . "0";
		                        }
		                        break;
		                    case 2:
		                        if (strlen($min) == 3) {
		                            $min = $min . "0";
		                        }
		                        break;
		                }
		                $lat = $pre . $deg . " " . $min;
		                $len = strlen($lat);
		                $adifstr .= "<MY_LON:" . $len . ">" . $lat;
						$gotOne=true;
		            }
		        } elseif ($col_name == "My_Grid") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<MY_GRIDSQUARE:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "His_RST") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<RST_SENT:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "My_RST") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<RST_RCVD:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Serial_Sent") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<STX:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Serial_Received") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<SRX:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "His_QTH") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<QTH:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "MyCity") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<MY_CITY:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "MyCnty") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $tMy_County=$val;
		                if (strlen($tMy_State)>0){
		                    $tSt=$tMy_State.",".$tMy_County;
		                    $adifstr .= "<MY_STATE:" . strlen($tSt) . ">" . $tSt;
							$gotOne=true;
		                }
		            }
		        } elseif ($col_name == "MyState") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $tMy_State=$val;
		                if (strlen($tMy_County)>0){
		                	$tSt=$tMy_State.",".$tMy_County;
							$adifstr .= "<MY_STATE:" . strlen($tSt) . ">" . $tSt;
							$gotOne=true;
		            	}
		            }
		         } elseif ($col_name == "MyCountry") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<MY_COUNTRY:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "His_Email") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<EMAIL:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Propagation_Mode") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<PROP_MODE:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Sat_Name") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<SAT_NAME:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Sat_Mode") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<SAT_MODE:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Note") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<COMMENT:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Time_End_Plain") {
		            if (strlen($val)==16){
		                $len = 4;
		                $ti = substr($val, 0, 4);
		                $adifstr .= "<TIME_OFF:" . $len . ">" . $ti;
		                $len = 8;
		                $mo = strtolower(substr($val, 8, 3));
		                $mon = (strpos("janfebmaraprmayjunjulaugsepoctnovdec", $mo) / 3) + 1;
		                $mon = "0" . $mon;
		                $day = substr($val, 5, 2);
		                $yr = substr($val, 12, 4);
		                $mon = substr($mon, strlen($mon) - 2);
		                $adifstr .= "\n   <QSO_DATE_OFF:" . $len . ">" . $yr . $mon . $day;
						$gotOne=true;
		            }else{
			            $gotOne=false;
		            }
		        } elseif ($col_name == "Logname") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<APP_RIGPI_LOGNAME:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "MobileID") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<APP_RIGPI_MOBILEID:" . $len . ">" . $val;
						$gotOne=true;
						$db->where("MobileID",$val);
						$tA=array("LoTWSent"=>1);
						$db->Update("Logbook",$tA);
		            }
		        } elseif ($col_name == "ID") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<APP_RIGPI_ID:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        }elseif ($col_name == "LOTW_LastQSL") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<APP_LOTW_LASTQSL:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        }
			}
	        if ($gotOne==true){
				$adifstr .= "\n";
	        }
		}
		$adifstr .= "<EOR>\n\n";
	    $i=$i+1;
		$db1->where('uID',$uid);
		$tData=Array('Count'=>$i);
		$db1->update('Users',$tData);
	}
}elseif ($type=="lotw"){
	foreach($qs as $row => $innerArray){
		foreach($innerArray as $innerRow => $value){
	        if ($gotOne==true){
		        $adifstr.="   ";
	        }
			$tHis_County="";
			$tHis_State="";
			$tMy_County="";
			$tMy_State="";
			$gotOne=false;
			$col_name=$innerRow;
			$val=$value;
			if (!($col_name == "")) {
		        if ($col_name == "Callsign") {
		            $adifstr .= "<CALL:" . strlen($val) . ">" . $val;
		            $gotOne=true;
		        }elseif ($col_name == "Band") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<BAND:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        }elseif ($col_name == "Mode") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $temp=$val;
		                if ($temp == "USB" || $temp == "LSB")
		                {
		                    $temp="SSB";
		                }
		                $adifstr .= "<MODE:" . $len . ">" . $temp;
						$gotOne=true;
		            }
		     	}elseif ($col_name == "Time_Start_Plain") {
			     		if (strlen($val)==16)
			     		{
			                $len = 4;
			                $ti = substr($val, 0, 4);
			                $adifstr .= "<TIME_ON:" . $len . ">" . $ti;
			                $len = 8;
			                $mo = strtolower(substr($val, 8, 3));
			                $mon = (strpos("janfebmaraprmayjunjulaugsepoctnovdec", $mo) / 3) + 1;
			                $mon = "0" . $mon;
			                $day = substr($val, 5, 2);
			                $yr = substr($val, 12, 4);
			                $mon = substr($mon, strlen($mon) - 2);
			                $adifstr .= "\n   <QSO_DATE:" . $len . ">" . $yr . $mon . $day;
			                $gotOne=true;
			     		}else{
				     		$gotOne=false;
			     		}
		        } elseif ($col_name == "DXCC") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<DXCC:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "His_Continent") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<CONT:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "His_Country") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<COUNTRY:" . $len . ">" . $val;
						$gotOne=true;
		            }

		        } elseif ($col_name == "His_RST") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<RST_SENT:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "My_RST") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<RST_RCVD:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "MobileID") {
		            $len = strlen($val);
		            if ($len > 0) {
						$db->where("MobileID",$val);
						$tA=array("LoTWSent"=>1);
						$db->Update("Logbook",$tA);
					}
		        } elseif ($col_name == "Propagation_Mode") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<PROP_MODE:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Sat_Name") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<SAT_NAME:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Sat_Mode") {
		            $len = strlen($val);
		            if ($len > 0) {
		                $adifstr .= "<SAT_MODE:" . $len . ">" . $val;
						$gotOne=true;
		            }
		        } elseif ($col_name == "Rx_Frequency") {
		            $fr = $val;
		            $len = strlen($fr);
		            if ($len > 3) {
		                $fr = substr($fr, 0, $len - 6) . "." . substr($fr, $len - 6);
		                $adifstr .= "<FREQ:" . ($len + 1) . ">" . $fr;
						$gotOne=true;
		            }
		
		        } elseif ($col_name == "Tx_Frequency") {
		            $fr = $val;
		            $len = strlen($fr);
		            if ($len > 3) {
		                $fr = substr($fr, 0, $len - 6) . "." . substr($fr, $len - 6);
		                $adifstr .= "<FREQ_RX:" . ($len + 1) . ">" . $fr;
						$gotOne=true;
		            }
				}
		    }
	        if ($gotOne==true){
				$adifstr .= "\n";
	        }
		}
		$adifstr .= "<EOR>\n\n";
	    $i=$i+1;
		$db1->where('uID',$uid);
		$tData=Array('Count'=>$i);
		$db1->update('Users',$tData);
	}
}
}
if (file_exists($adif_file)) {
    unlink($adif_file);
} 

$file = fopen($adif_file, "wa+") or die("Unable to open file!");
fwrite($file,$adifstr);
fclose($file);
if (file_exists($adif_zip)) {
    unlink($adif_zip);
} 

$files_to_zip = array(
	$adif_file
);

//if true, good; if false, zip creation failed
$result = create_zip($files_to_zip,$adif_zip);

$fileName = basename($adif_zip);
$new_path=$dRoot."/my/downloads/".$fileName;
copy ('/dev/shm/'.$fileName,'/var/www/fi/'.$fileName);
exec('sudo chown www-data:www-data '.$dRoot.'/my/downloads');
exec('sudo chown www-data:www-data '.$new_path);
if (file_exists($new_path)){
	unlink($new_path);
}
copy ('/var/www/fi/'.$fileName,$new_path);
///exec('sudo chown pi:pi '.$new_path);
//exec('sudo chown pi:pi '.$dRoot.'/my/downloads');
echo $fileName;	

?>
