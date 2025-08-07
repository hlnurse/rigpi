<?php
//$ip='172.16.0.37';//$_POST['ip'];
$ip=$_POST['ip'];
$w="wget '$ip'/transfer.txt -O /var/www/html/transferIn.txt";
exec($w);
$t=fopen('/var/www/html/transferIn.txt', 'r');
$t1=fread($t, 100);
echo $t1 . "\n";
fclose($t);
?>
