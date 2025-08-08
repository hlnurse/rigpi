<?php
//function scanFor($radioModel){
	$radioModel = $_POST['model'];
	$dRoot="/var/www/html";
	require_once $dRoot . "/classes/MysqliDb.php";
	require $dRoot . "/programs/sqldata.php";
	$sQuery = "SELECT Radio from MySettings WHERE CHAR_LENGTH(Port)>4";
	$con = new mysqli(
	"localhost",
	$sql_radio_username,
	$sql_radio_password,
	$sql_radio_database
  );
	  $tMyRadio = strtolower( $radioModel);
//	  echo $tMyRadio;
	switch ($tMyRadio){
		case "none":  //none
			break;
		case "dummy":
			$data=doNone();
			break;
		case "dummy no vfo":
			$data=doNone();
			break;
			
		case "rigctl":
			$data=doNetRigctl();
			break;
		case "net rigctl":
			$data=doNetRigctl3();
			break;
		default:
			$data=doNormal();
			break;
	};
	$data="<div class='myport' id='portNone'><li><a class='dropdown-item' href='#'>None</a></li></div>".$data; 
	echo $data;
//};

function doNone(){
	return "";
}

function doRigctl(){
	$ports=shell_exec("ls /dev/ttyUSB*");
	$isRadio=shell_exec("ls /dev/radio*");
	$data="";
	$isSerial=shell_exec("ls /dev/serial/by-id");
	$aData=explode("\n",$isSerial);
	$cData=count($aData);
	for ($n=0;$n<$cData;$n++){
		$portx='-port';
		if (strpos($aData[$n],$portx )){
			$data=$data . "<div class='myport' id='/dev/serial/by-id/$aData[$n]'><li><a class='dropdown-item' href='#'>/dev/serial/by-id/$aData[$n]</a></li></div>";
		};
	};

	for ($n=0;$n<10;$n++){
		if (strpos($ports, "USB" . $n)!==FALSE){
			$data=$data . "<div class='myport' id='/dev/ttyUSB$n'><li><a class='dropdown-item' href='#'>/dev/ttyUSB$n</a></li></div>";                                          
		};
	};
	return $data;
	
};

function doNetRigctl(){
	$data="";
$dRoot="/var/www/html";
require $dRoot . "/programs/sqldata.php";
$sQuery = "SELECT Radio from MySettings WHERE CHAR_LENGTH(Port)>4";
$con = new mysqli(
"localhost",
$sql_radio_username,
$sql_radio_password,
$sql_radio_database);
  $rows = $con->query($sQuery);
	$rowcount=mysqli_num_rows($rows);
	for ($i=0;$i<$rowcount;$i++){
		  $row=$rows->fetch_assoc();
		  $result = $row['Radio'];
		$n=($result * 2) + 4530;
		$data=$data .  "<div class='cMyType' id='myport'><li><a class='dropdown-item' href='#'>$n</a></li></div>"; 
	};
	return $data;                                           	
};

function doNetRigctl3(){
	$data="";
	$dRoot="/var/www/html";
	require $dRoot . "/programs/sqldata.php";
	$sQuery = "SELECT Radio from MySettings WHERE CHAR_LENGTH(Port)>4";
	$con = new mysqli(
	"localhost",
	$sql_radio_username,
	$sql_radio_password,
	$sql_radio_database);
	$rows = $con->query($sQuery);
	$rowcount=mysqli_num_rows($rows);
	if ($rowcount>0){
		$data=$data .  "<div class='myport' id='myport'><li><a class='dropdown-item' href='#'>4532</a></li></div>";
	  for ($i=0;$i<$rowcount;$i++){
		  $row=$rows->fetch_assoc();
		  $result = $row['Radio'];
		  $n=($result * 2) + 4530;
	  		if ($n!=4532){
		  	$data=$data .  "<div class='myport' id='myport'><li><a class='dropdown-item' href='#'>$n</a></li></div>";
	  		};
		  };
	  }else{
		  $data=$data .  "<div class='myport' id='myport'><li><a class='dropdown-item' href='#'>4532</a></li></div>"; 
	  };
	  return $data;                                           	
};

function doNormal()
{
	$ports=shell_exec("ls /dev/ttyUSB*");
	$isRadio=shell_exec("ls /dev/radio*");
	$data="";
	$isSerial=shell_exec("ls /dev/serial/by-id");
	$aData=explode("\n",$isSerial);
	$cData=count($aData);
	for ($n=0;$n<$cData;$n++){
		$portx='-port';
		if (strpos($aData[$n],$portx )){
			$data=$data . "<div class='myport' id='/dev/serial/by-id/$aData[$n]'><li><a class='dropdown-item' href='#'>/dev/serial/by-id/$aData[$n]</a></li></div>";
		}
	}
	for ($n=0;$n<10;$n++){
		if (strpos($ports, "USB" . $n)!==FALSE){
			$data=$data . "<div class='myport' id='/dev/ttyUSB$n'><li><a class='dropdown-item' href='#'>/dev/ttyUSB$n</a></li></div>";                                               
		}
	}
	return $data;
}
?>