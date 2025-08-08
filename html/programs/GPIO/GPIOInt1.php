<?php
$sock;
require_once('/var/www/html/programs/GPIO/GPIO.php');
require_once('/var/www/html/programs/GPIO/GPIOInterface.php');
require_once('/var/www/html/programs/GPIO/InputPinInterface.php');
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	echo "Error caught: [$errno] $errstr - $errfile:$errline\n";
	// Custom handling logic
	return true; // Prevents the PHP error handler from running
});
if ($argc<2){
	$tMyPort=30040;
	$tMyIP='172.16.0.5';
	$tInvert=0;
}else{
	$tMyPort=$argv[2];//$_POST['port'];
	$tMyIP=$argv[1];//$_POST['IP'];
	$tInvert=$argv[3];//invert
}
// Create a GPIO object
$gpio = new GPIO();
echo print_r($gpio);
	echo "GLOBAL1 port: " . $tMyPort . "\n";

//echo "getting input pin\n";
$pin = $gpio->getInputPin(10);
//exit;
$interruptWatcher=$gpio->createWatcher();
$pin->setEdge(InputPinInterface::EDGE_BOTH);
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
//	echo "hello " . $GLOBALS["sock"] . "\n";
echo "starting interrupt watcher\n";
$interruptWatcher->register($pin, function(InputPinInterface $pin, $value) {
	$tG=explode(" ",$value);
	if ($GLOBALS["tInvert"]==0) {
		if($tG[0]==1){
			$tG[0]=0;
		}else{
			$tG[0]=1;
		}
	};
//	echo "GLOBAL port: " . $GLOBALS['tMyRadio'] . "\n";
	$numsg=$tG[0]." ".floor(1000*(microtime(true)));
	$t=socket_sendto($GLOBALS['sock'], $numsg, strlen($numsg) , 0, $GLOBALS['tMyIP'] , $GLOBALS['tMyPort']);
	$t=socket_sendto($GLOBALS['sock'], $numsg, strlen($numsg) , 0, $GLOBALS['tMyIP'] , $GLOBALS['tMyPort']);
//	$t=socket_sendto($sock, "hello", 40 , 0, '172.16.0.5' , 30040);
	echo $t . " " . $numsg . "\n";
});
echo "in loop\n";
while ($interruptWatcher->watch(1000))
?>