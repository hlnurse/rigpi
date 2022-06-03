<?php

	$tPort=$argv[1];
	$tCommand=$argv[2];
	$tosend=$argv[3];
	$snd='';
	if ($tCommand=="L")
	{
		$snd="rigctl -m 2 -r 127.0.0.1:$tPort $tCommand  $tosend\n";
		exec($snd);
	}else{
		$snd="rigctl -m 2 -r 127.0.0.1:$tPort $tCommand '$tosend'\n";  //' required to send spaces
		exec($snd);
	}
//	echo 'snd: ' . $snd;
//	file_put_contents('/var/www/html/tlog13.txt','cwCAT'."|".$snd."\n", FILE_APPEND);
?>