<?php
    // generate ADIF formatted report for export

//function export(){

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
$whichRadio=$_POST['radio'];
$myID=$_POST['uid'];
$myMacro=$_POST['macro'];
$mBank=$_POST['bank'];
require_once($dRoot."/programs/zipMe.php");

/////$macroFile="/dev/shm/rigpiMacroBank".$mBank."_".$myID."_".$whichRadio."_".time().".mac";
$macroFile="/my/Downloads/rigpiMacroBank".$mBank."_".$myID."_".$whichRadio."_".time().".mac";
//$macroFile="/dev/shm/1234.txt";
$macroZipFile="/dev/shm/rigpiMacro_".$myID."_".$whichRadio."_".time().".zip";
if (file_exists($macroFile)) {
    unlink($macroFile);
} 

$file = fopen($macroFile, "w") or die("Unable to open file!");
fwrite($file,$myMacro);
fclose($file);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($macroFile));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($macroFile));
    readfile($macroFile);
//	echo basename($macroFile);	
    exit;


//echo $fileName;
$files_to_zip = array(
	$macroFile
);

//if true, good; if false, zip creation failed
/////$result = create_zip($files_to_zip,$macroZipFile);

/////$fileName = basename($macroZipFile);
$fileName = basename($macroFile);
$new_path=$dRoot."/my/downloads/".$fileName;
copy ('/dev/shm/'.$fileName,'/var/www/fi/'.$fileName);
if (file_exists($new_path)){
	unlink($new_path);
}
copy ('/var/www/fi/'.$fileName,$new_path);
echo $fileName;	

?>
