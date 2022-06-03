<?php
require_once '/var/www/html/programs/GPIO/vendor/autoload.php';
use PiPHP\GPIO\GPIO;
use PiPHP\GPIO\Pin\InputPinInterface;
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
$charHold="";
$didDown=0;
$last=0;
// Create a GPIO object
$gpio = new GPIO();

// Retrieve pin 10 and configure it as an input pin
$pin = $gpio->getInputPin(10);
$pin->setEdge(InputPinInterface::EDGE_BOTH);
$interruptWatcher=$gpio->createWatcher();
$interruptWatcher->register($pin, function(InputPinInterface $pin, $value) {
	$msg = $pin->getValue();
	$t1=strval(1000*(microtime(true)));
	$t2=explode(".",$t1);
	$numsg=$msg." ".$t2[0];
	$sock1=$GLOBALS['sock'];
	socket_sendto($sock1, $numsg,strlen($numsg) , 0, '10.0.0.158', 12060);
});

while ($interruptWatcher->watch(10000));
?>