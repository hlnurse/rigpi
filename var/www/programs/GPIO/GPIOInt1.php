<?php
require_once('/var/www/html/programs/GPIO/GPIO.php');
require_once('/var/www/html/programs/GPIO/GPIOInterface.php');
require_once('/var/www/html/programs/GPIO/InputPinInterface.php');

$tMyPort=$argv[2];//$_POST['port'];
$tMyIP=$argv[1];//$_POST['IP'];
$tInvert=$argv[3];//invert
// Create a GPIO object
$gpio = new GPIO();
$pin = $gpio->getInputPin(10);
$interruptWatcher=$gpio->createWatcher();
$pin->setEdge(InputPinInterface::EDGE_BOTH);
$sock = socket_create(AF_INET, SOCK_DGRAM, 0);
$interruptWatcher->register($pin, function(InputPinInterface $pin, $value) {
	$tG=explode(" ",$value);
	if ($GLOBALS['tInvert']==1) {
		if($tG[0]==1){
			$tG[0]=0;
		}else{
			$tG[0]=1;
		}
	};

	$numsg=$tG[0]." ".floor(1000*(microtime(true)));
	$t=socket_sendto($GLOBALS['sock'], $numsg,strlen($numsg) , 0, $GLOBALS['tMyIP'] , $GLOBALS['tMyPort']);
});
while ($interruptWatcher->watch(1000))
?>