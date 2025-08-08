<?php
//NOT USED ANYWHERER?
require_once "/var/www/html/programs/GetInterfaceOut.php";
require_once "/var/www/html/programs/SetSettingsFunc.php";
//function doTCPWrite($tMyRadio, $client){
	$tMyRadio=argcv[1];
	$client=argcv[2];
	echo "connecting to: " . print_r($client) . "\n";
	   // Prepare arrays for socket_select
			$read = [$client];
			$write = null;
			$except = null;
	$tUser='admin';
	$hooks='';
			// Set timeout to 5 seconds
			$timeout = 5;
	
			// Check for incoming connections
			$num_changed_sockets = socket_select($read, $write, $except, $timeout);
	
			if ($num_changed_sockets === false) {
				echo "Socket select failed: " . socket_strerror(socket_last_error($client)) . "\n";
				return;
			} elseif ($num_changed_sockets > 0) {
				// There is an incoming connection, accept it
				echo "incoming socket\n";
				$client_socket = socket_accept($client);
				if ($client_socket === false) {
					echo "Socket accept failed: " . socket_strerror(socket_last_error($client_socket)) . "\n";
					return;
				}
	
				// Read from and write to the client socket
				$input = socket_read($client_socket, 1024);
				if (strlen($input) > 2) {
				  $aComm = explode(";", $input);
				  for ($i = 0; $i < count($aComm); $i++) {
					if (strpos($aComm[$i], "KR") !== false) {
					  $bComm = str_replace("KR", "", $aComm[$i]);
					  if (strlen($bComm) > 0) {
						for ($j = 0; $j < strlen($bComm); $j++) {
						  echo "trigger ECHO: " .
							$aComm[$i] .
							" " .
							$i .
							" " .
							bin2hex($aComm[$i]) .
							"\n";
				
						  trigger_hooks(
							"ECHO",
							$client_socket,
							$aComm[$i] . ";",
							$tUser,
							$tMyRadio
						  );
						}
					  }
					} elseif (strpos($aComm[$i], "KS") !== false) {
					  if (ord(substr($aComm[$i], 2)) > 0) {
						echo "trigger SPEED: " .
						  $aComm[$i] .
						  " " .
						  bin2hex($aComm[$i]) .
						  "\n";
						trigger_hooks(
						  "SPEED",
						  $client_socket,
						  $aComm[$i] . ";",
						  $tUser,
						  $tMyRadio
						);
					  }
					} else {
					  trigger_hooks(
						"INPUT",
						$client_socket,
						$aComm[$i] . ";",
						$tUser,
						$tMyRadio
					  );
					}
				  }
				
				}

				echo "Received from client: $input\n";
	
				socket_close($client_socket);
			} else {
				// Timeout occurred, no incoming connection
				echo "No incoming connections within timeout period\n";
			}
//		}
/*	}	$client_socket = socket_accept($client);
	if ($client_socket === false) {
		echo "Socket accept failed: " . socket_strerror(socket_last_error($client_socket)) . "\n";
		return;
	}
	echo "connected to: $client_socket" . "\n";
		
	while (true){
		$data = GetWKOut($tMyRadio);  //note CWPortServer does not send TCP data, this is to send to another rigpi
		echo "data: $data\n";
		$data = explode(chr(96), $data);
		if ($data[0] == 1) {
			echo("from db: " . $data[1]);
			echo is_resource($client_socket) . "\n";
//			foreach ($clients as $client) {
				$cl=$client_socket;
				fwrite($cl, $data[1]);
				echo "wrote $data[1] to socket\n";
		 	    SetInterface($tMyRadio, "CWInWKCk", 0);
				SetInterface($tMyRadio, "CWInWK", "");
//			}
		}
		usleep(1000);
	};
}
*/
function trigger_hooks($command, &$client, $input)
  {
	if (isset($hooks[$command])) {
	  $tMyRadio = $this->config["radio"];
	  $tUser = 'admin';//$this->config["user"];
	  foreach ($hooks[$command] as $function) {
		$continue = call_user_func_array($function, [
		  &$this,
		  &$client,
		  $input,
		  $tUser,
		  1,
		]);
		if ($continue === false) {
		  break;
		}
	  }
	}
  }


?>