<?php
$dRoot="/var/www/html";
  require $dRoot . "/programs/sqldata.php";
  $sQuery = "UPDATE $tTable SET $tField = concat($tField, '$tData') WHERE Radio='$tMyRadio'";
  $con = new mysqli(
	"localhost",
	$sql_radio_username,
	$sql_radio_password,
	$sql_radio_database
  );
  $result = $con->query($sQuery);
$ports=shell_exec("ls /dev/ttyUSB*");
$isRadio=shell_exec("ls /dev/radio*");
$data="";
$isSerial=shell_exec("ls /dev/serial/by-id");
$aData=explode("\n",$isSerial);
$cData=count($aData);
for ($n=0;$n<$cData;$n++){
	$portx='-port';
	if (strpos($aData[$n],$portx )){
		$data=$data . "<div class='myport' id='/dev/serial/by-id/$aData[$n]'><li><a class='dropdown-item' href='#'>/dev/serial/by-id/$aData[$n]</a></li></div>\n\r";
	}
}
for ($n=0;$n<10;$n++){
		if (strpos($ports, "USB" . $n)!==FALSE){
			$data=$data . "<div class='myport' id='/dev/ttyUSB$n'><li><a class='dropdown-item' href='#'>/dev/ttyUSB$n</a></li></div>\n\r";                                               
		}
}
$data="<div class='myport' id='portNone'><li><a class='dropdown-item' href='#'>None</a></li></div>\n\r".$data;                                               
echo $data;
?>