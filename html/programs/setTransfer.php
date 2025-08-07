<?php
//this procedure creates a file with the current radio ID and radio name to be used by remote net rigctl
if (isset($_POST['id'])){
	$id=$_POST['id'];
	$rname=$_POST["rn"];
}else{
$id=3073;
$rname="hln";
	
}
$file=fopen('/var/www/html/transfer.txt', 'w');
echo $file . "\n "; 
fwrite($file, $id . "`" . $rname);
fclose($file);
echo print_r(error_get_last()) . " $id $rname\n";
?>
