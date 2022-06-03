<?php
$macro_file = $_POST["file"];
$tRadio=$_POST['radio'];
require_once "/var/www/html/programs/SetSettingsFunc.php";
if (!file_exists("/var/www/fi/" . $macro_file)){
	echo "$macro_file not found in uploads folder.";
	return;
}
$file = fopen("/var/www/fi/" . $macro_file, "r");
if (!$file){
	echo "$macro_file not found.";
	return;
}
$line = fgets($file);
//echo $tRadio."     ".$line;

	
/*$line="WAIT%201%20(AS)%7C%24%5B~MY%20CALL%7C%24*~5NN%7C%245NN%20TU~ESC%7C!ESC~ERROR%20(...)%7C%24%3C1b%3EHH~TUNE%7C!TUNE~T%2FR%7C!T%2FR~CQ%7C%24CQ%20CQ%20CQ%20DE%20*%20K~QRZ%7C%24QRZ~HIS%20DE%20MINE%7C%24X%20DE%20*~CANCEL%7C!ESC~PWR%20ON%7C*PS1%3B~PWR%20OFF%7C*PS0%3B~HAMLIB%20TEST%20(F)%7C*F%2021025000~ROTATE%7C!ROTATE~ROTATE%20STOP%7C!RTR%20STOP~BK%7C%24%3C1b%3EBK~KNWD%20w%20TEST%7C*0w%20FA00014030777%3B%20~SWITCH%20OFF%7C%24%3C18%3E%3C00%3E~SWITCH%20ON%7C%24%3C18%3E%3C01%3E~MACRO%2021%7CMACRO21~MACRO%2022%7CMACRO22~MACRO%2023%7CMACRO23~MACRO%2024%7CMACRO24~MACRO%2025%7CMACRO25~MACRO%2026%7CMACRO26~MACRO%2027%7CMACRO27~MACRO%2028%7CMACRO28~MACRO%2029%7CMACRO29~MACRO%2030%7CMACRO30~Pwr%20ON%7C*%5Cset_powerstat%201~Pwr%20OFF%7C*%5Cset_powerstat%200~";
*/
	require "/var/www/html/programs/sqldata.php";
  	$sQuery = "UPDATE RadioInterface SET Macros='" . $line . "' where Radio='$tRadio'";
	$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
	$result = $con->query($sQuery);
//SetInterfacePartial($tRadio,"Macros",$line);
//////fclose($file);
echo $line;

?>
