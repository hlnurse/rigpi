<?php
function relayOn($dec){
$dec=1;
	switch ($dec){
	case 0:
		$dec=0;
		break;
	case 1:
		$dec=1;
		break;
	case 2:
		$dec=2;
		break;
	case 3:
		$dec=4;
		break;
	case 4:
		$dec=8;
		break;
	case 5:
		$dec=16;
		break;
	case 6:
		$dec=32;
		break;
	case 7:
		$dec=64;
		break;
	case 8:
		$dec=128;
		break;
}
exec("sudo /usr/share/rigpi/bitmode $dec");

}
?> 
