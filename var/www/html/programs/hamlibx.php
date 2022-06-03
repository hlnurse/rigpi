<?php
	require_once( '/var/www/html/programs/sqldata.php');
	require_once ('/var/www/html/classes/MysqliDb.php');	
	$report= "";
	$tMyRadio='2';//$_POST['radio'];
	$tMyKeyer='rpk';//$_POST['keyer'];
	$tUsername='x';//$_POST['user'];
	$tMyCWPort='';//$_POST['port'];
	$tMyRotorPort='/dev/ttyUSB1';//$_POST['rotorPort'];
	$tMyTCPPort='30001';//$_POST['tcpPort'];
	$tMyUDPPort=2333;
	$tMyTCPPort=$tMyTCPPort;//+($tMyRadio-1)*2; //allows for connection to any account
	$report="Radio: ".$tMyRadio."\n";
	$report.="User: ".$tUsername."\n";
//	echo $report;
	//Get data from MyRadio table
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$db->where ("Radio",$tMyRadio);
	$row = $db->getOne ("MySettings");
	$dradio=$row['Radio'];
	$report.="Radio from settings: ".$dradio."\n";
	$dmodel=$row['Model'];
    $port=4532;//$row['Port'];
	$report.="Port from settings: ".$port."\n";
    $id='2';//$row['ID'];
	$report.="ID from settings: ".$id."\n";
    $baud=$row['Baud'];
    $stop=$row['Stop'];
    $rotorBaud=$row['RotorBaud'];
    $rotorStop=$row['RotorStop'];
    $rotorID=$row['RotorID'];
	// Get the IP address for the target host. 
	$db->where ("Username",$tUsername);
	$row = $db->getOne ("Users");
	$tRigDoPID=$row['rigDoPID'];
	$report.= "rigDoPID from settings: ".$tRigDoPID ."\n";
//	echo $report;
	if ($id==2){
		$service_port=$port;
	}else{
		$service_port=$dradio * 2 + 4530; //$row['rigctldPort'];
	}
	$report.= "service_port: ".$service_port ."\n";
//	echo $report;
	$tR="'[r]adio$tMyRadio'";
	$users=exec("ps aux | grep ".$tR);
	
	$pos=stripos($users,"radio".$tMyRadio);
	if (strlen($pos>0)){
		echo $dmodel." is connected.\n";
		$tR="'[C]WDo.php $tUsername [r]adio$tMyRadio'";
		$users=exec("ps aux | grep ".$tR);
		if (strlen($users)==0)
		{
			echo 'start CW: '.$dradio.' '.$tUsername.' '.$tMyKeyer.' '.$tMyCWPort."\n";
			DoCW($dradio,$tUsername,$tMyKeyer,$tMyCWPort);
		}
		$tR="'[T]CPDo.php $tUsername [r]adio$tMyRadio'";
		$users=exec("ps aux | grep ".$tR);
		if (strlen($users)==0)
		{
			DoTCP($dradio,$tUsername,$tMyTCPPort);
		}
		$tR="'[R]otorDo.php $tUsername [r]otor$tMyRadio'";
		$users=exec("ps aux | grep ".$tR);
		if (strlen($users)==0)
		{
			DoRotor($dradio,$tUsername,$tMyRotorPort);
		}
		$tR="'[U]DPDo.php $tUsername [r]adio$tMyRadio'";
		$users=exec("ps aux | grep ".$tR);
		if (strlen($users)==0)
		{
			DoUDP($dradio,$tUsername,$tMyUDPPort);
		}
		exit; 
	}
///////////posix_kill($tRigDoPID,15);
	if ($id=='1'||$id=='2'){
		$result=shell_exec("rigctld -m $id $service_port > /dev/null 2>&1 &" );
	}else{
		$result=shell_exec("rigctld -m $id -r $port -t $service_port -s $baud -C stop_bits=$stop > /dev/null 2>&1 &" );
	}
	usleep(1000000);
	$address = '127.0.0.1';
	system("stty -F $port -echo");
	system("stty -F $port raw");
	// Create a TCP/IP socket.
	$socket = @socket_create(AF_INET, SOCK_STREAM, 0);
	if ($socket == false) {
    	$report=$report . "socket_create() failed: reason: " . 
         	socket_strerror(socket_last_error()) . "\n";
	}else{
		if ($id=='1'){
			$result=true;
		}else{
			$result = socket_connect($socket, $address, $service_port);
		}
		if ($result === false) {
	    	$report=$report . "socket_connect() failed.\nReason: ($result) " . 
				socket_strerror(socket_last_error($socket)) . "\n";
		}elseif ($id!='1'){
			$in = "f\n";
			$out = '';
			socket_write($socket, $in, strlen($in));
			$reportOut="";
			$OK=true;
			while ($OK){
				$currentByte = socket_read($socket,1);
				if ($currentByte=="\n") {
					$OK=false;
				}
				$reportOut=$reportOut . $currentByte;
			}
		}
	}
	socket_close($socket);

if (strstr($report, "RPRT")){
	echo "Hamlib error: " . strlen($report)."\n\r"."Rigctld timed out waiting for $dmodel.\n";
}elseif (strstr($report,"failed")){
	echo $report." Connection failed, check settings.\n";
}else{
	echo "Congratulations, $dmodel is connected!\n\n";
	echo "VFO A frequency is ".(number_format($reportOut/1000000, 6) . " mHz" )."\n\n";
	echo "Now starting RigPi radio control link.\n";
	usleep(1000000);
	$tRigExec="php /var/www/html/RigDo.php $tUsername radio$dradio $service_port > /dev/null 2>&1 & echo $!";
	$pidRD = exec($tRigExec);
	usleep(1000000);
	echo "Now starting RigPi CW control link.\n";
	DoCW($dradio,$tUsername,$tMyKeyer,$tMyCWPort);
	echo "Now starting RigPi TCP remote control link.\n";
	DoTCP($dradio,$tUsername,$tMyTCPPort);
	echo "Now starting RigPi UDP server.\n";
	DoUDP($dradio,$tUsername,$tMyUDPPort);
	$sp=$service_port+1;
	echo "Now starting RigPi Rotor control link.\n";
	$result=shell_exec("rotctld -m $rotorID -r $tMyRotorPort -t $sp -s $rotorBaud > /dev/null 2>&1 &"); 
	//-r $tMyRotorPort -t $sp -s $rotorBaud -C stop_bits=$rotorStop
//	echo "rotor: ".$result.' '.$rotorID.' '.$tMyRotorPort.' '.$service_port.' '. $rotorBaud . ' '.$rotorStop.'\n';
	usleep(1000000);
	DoRotor($dradio,$tUsername,$tMyRotorPort);
	$con = new mysqli("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if ($con->connect_error) {
		die("Connection failed: " . $con->connect_error);
	}
	$con->query("UPDATE Users SET rigDoPID='$pidRD' where Username='$tUsername'");	
}

function DoCW($whichRadio,$Username,$myKeyer, $myCWPort)
{
	if ($myCWPort="")
	{
		$myCWPort="x";
	}
	echo 'start CW2: '.$whichRadio.' '.$Username.' '.$myKeyer.' '.$myCWPort."\n";
	$tWKExec="php /var/www/html/CWDo.php $Username radio$whichRadio $myKeyer $myCWPort> /dev/null 2>&1 &";
	$pidWK = exec($tWKExec);
	echo 'start CW2 PID: '.$pidWK."\n";
}

function DoTCP($whichRadio,$Username, $port)
{
	$tTCPExec="php /var/www/html/TCPDo.php $Username radio$whichRadio $port> /dev/null 2>&1 &";
	$pidTCP = exec($tTCPExec);
}

function DoUDP($whichRadio,$Username, $udpport)
{
	$tUDPExec="php /var/www/html/UDPDo.php $Username radio$whichRadio $udpport> /dev/null 2>&1 &";
	$pidTCP = exec($tUDPExec);
}

function DoRotor($whichRotor,$Username, $port)///need to add parameters!!!!
{
	$tRotorExec="php /var/www/html/RotorDo.php $Username rotor$whichRotor > /dev/null 2>&1 &";
	$pidRotor = exec($tRotorExec);
}
?>