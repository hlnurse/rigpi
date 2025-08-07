<?php
if (isset($argv[1])){
	$tPort=$argv[1];
}else{
	$tPort="172.16.0.12:4532|b|e";
}
//	$tPort="172.16.0.12:4534|b|e";
//$tPort=$argv[1];
$tData=explode("|", $tPort);
echo $tPort . "\n";
/*	if (isset($argv[1])){
		$tPort=$argv[1];
	}else{
		$tPort="4534";
	}
	if (isset($argv[2])){
		$tCommand=$argv[2];
	}else{
		$tCommand="L";
	}
	if (isset($argv[3])){
		$toSend=$argv[3];
	}else{
		$toSend=" KEYSPD 5";
	}
	if (isset($argv[4])){
		$tCWIP=$argv[4];
	}else{
		$tCWIP="172.16.0.12";
	}
	$snd='';
*/
/*		$tPort="4534";
		$tCommand="L";
		$toSend="KEYSPD 35";
		$tCWIP="172.16.0.12";
*/	
	if ($tData[1]=="L")
	{
//		$snd="rigctl -m 2 -r $tCWIP:$tPort $tCommand $toSend \n";
		$snd="rigctl -m 2 -r $tData[0] $tData[1] $tData[2]";
		echo $snd . "\n";
		$sent=exec($snd);
	}else{
		$snd="rigctl -m 2 -r $tData[0] $tData[1] '$tData[2]'";//$tData[1] '$tData[2]' \n";
//		$snd="rigctl -m 2 -r $tCWIP:$tPort $tCommand " . "'$toSend'" . "\n";  //' required to send spaces
		echo "to send: " . $snd;
		 $sent=exec($snd);
	}
//	echo $sent . ' snd: ' . $snd;
//	file_put_contents('/var/www/html/tlog13.txt','cwCAT'."|".$snd."\n", FILE_APPEND);
?>