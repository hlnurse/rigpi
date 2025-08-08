<?php
require_once "/var/www/html/classes/PhpSerialClass.php";
require_once "/var/www/html/programs/SetSettingsFunc.php";
	/*!	@class		SocketServer
		@author		Navarr Barnier
		@abstract 	A Framework for creating a multi-client server using the PHP language.
	 */
	 

class CWUDPServer
{
	protected $serialRTS;
	protected $valueOld;
	protected $config;

	/*!	@var		hooks
		@abstract	Array - a dictionary of hooks and the callbacks attached to them.
	 */
	protected $hooks;

	/*!	@var		master_socket
		@abstract	resource - The master socket used by the server.
	 */
	protected $masterSocket;
	
	/*!	@var		max_clients
		@abstract	unsigned int - The maximum number of clients allowed to connect.
	 */
	public $max_clients = 10;

	/*!	@var		max_read
		@abstract	unsigned int - The maximum number of bytes to read from a socket at a single time.
	 */
	public $max_read = 20;

	/*!	@var		clients
		@abstract	Array - an array of connected clients.
	 */
	 protected array $clients = [];
	/*!	@function	__construct
		@abstract	Creates the socket and starts listening to it.
		@param		string	- IP Address to bind to, NULL for default.
		@param		int	- Port to bind to
		@result		void
	 */
	public function setRigInterface($r,$f,$d)
	{
		SetInterface($r,$f,$d);
	}

	public function __construct($bind_ip,$port, $tRadio, $tUser, $tRTS, $tAdaptor)
	{
		$bind_ip='localhost';
		set_time_limit(0);
		$this->hooks = array();
		$this->config["ip"] = $bind_ip;
		$this->config["port"] = intval($port);
		$this->config["radio"]=$tRadio;
		$this->config["user"]=$tUser;
		$this->config["gottit"]=0;
		$this->config["deadman"]=0;
		$this->config["valueOld"]=0;
		$this->config["keyOld"]=0;
		$this->config["serialRTS"]=0;
//			$tRTS=0;   //
		echo "tRTS: " . $tRTS . "\n";
		$this->config["CTS-RTS"]=$tRTS; //are we in external adaptor mode? (use CW USB via RTS Interface = 1, UDP if 0)  
///		$this->config["adaptorUSB"]="/dev/serial/by-id/usb-1a86_USB_Serial-if00-port0";//$tAdaptor; //serial port for CW RTS/CTS adaptor
		$this->config["adaptorUSB"]=$tAdaptor; //serial port for CW RTS/CTS adaptor
		echo "port in class: " . $this->config["port"] . "\n";
		if ($port!=0)
		{
			$this->masterSocket = socket_create(AF_INET, SOCK_DGRAM, 0);
			socket_set_option($this->masterSocket, SOL_SOCKET, SO_REUSEADDR, 1);
			socket_bind($this->masterSocket,"0.0.0.0",$this->config["port"]) or die("Issue Binding");
			if ($this->masterSocket===false){
				echo "error creating socket\n";
			}else{
				echo "socket created\n";
			}
		}
	}
	

	/*!	@function	hook
		@abstract	Adds a function to be called whenever a certain action happens.  Can be extended in your implementation.
		@param		string	- Command
		@param		callback- Function to Call.
		@see		unhook
		@see		trigger_hooks
		@result		void
	 */
	public function hook($command,$function)
	{
		$command = strtoupper($command);
		if(!isset($this->hooks[$command])) { $this->hooks[$command] = array(); }
		$k = array_search($function,$this->hooks[$command]);
		if($k === FALSE)
		{
			$this->hooks[$command][] = $function;
		}
	}

	/*!	@function	unhook
		@abstract	Deletes a function from the call list for a certain action.  Can be extended in your implementation.
		@param		string	- Command
		@param		callback- Function to Delete from Call List
		@see		hook
		@see		trigger_hooks
		@result		void
	 */
	public function unhook($command = NULL,$function)
	{
		$command = strtoupper($command);
		if($command !== NULL)
		{
			$k = array_search($function,$this->hooks[$command]);
			if($k !== FALSE)
			{
				unset($this->hooks[$command][$k]);
			}
		} else {
			$k = array_search($this->user_funcs,$function);
			if($k !== FALSE)
			{
				unset($this->user_funcs[$k]);
			}
		}
	}
	
	
	/*!	@function	loop_once
		@abstract	Runs the class's actions once.
		@discussion	Should only be used if you want to run additional checks during server operation.  Otherwise, use infinite_loop()
		@param		void
		@see		infinite_loop
		@result 	bool	- True
	*/
	public function loop_once()
	{
		$stopFile = '/tmp/stop_flag';
		$ii=0;
		$p=$this->config['port'];
		$r=15;
		$i="0.0.0.0";
//		$this->config["CTS-RTS"]=1;
		if($this->config["CTS-RTS"]==1){
			printf("\nCTS running\n");
			// Array to hold received packets
			$packets = [];
			
			// Number of packets to receive
			$packetCount = 200; // Change this as needed
			$count=1;
			$descriptorspec = array(
				0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
				1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
				2 => array("pipe", "w")   // stderr is a pipe that the child will write to
			);
			$serialPort = $this->config["adaptorUSB"];
			echo "running\n";
			$cmd="/var/www/html/programs/src/y $serialPort";  //y takes argument serialport
			echo $serialPort . "\n";
			$process = proc_open($cmd, $descriptorspec, $pipes);
//			stream_set_blocking($pipes[1], false);
//			stream_set_blocking($pipes[2], false);
			echo "process from y: " . $process . "\n";
			if (is_resource($process)) {
				stream_set_blocking($pipes[1], true);
				stream_set_blocking($pipes[2], true);
				$output="";
				$error="";
				$status=0;
				$tWait=1;
				while (true) {
					$read = [];
					if (!feof($pipes[1])){
						$read[]=$pipes[1];
					}
					if (!feof($pipes[2])){
						$read[]=$pipes[2];
					}
					if (empty($read)){
						break;
					}
					$write = null;
					$except = null;
					$timeout=200000;
					$num_changed_streams = 	stream_select($read, $write, $except, 0, $timeout);
					if ($num_changed_streams === false){
						break;
					}
						// Read from stdout
						foreach ($read as $pipe){
							$data=fread($pipe, 8192);
							if ($pipe === $pipes[1]) {
								$output .= $data;
								echo "OUTPUT: " . $output . "\n";
							}elseif ($pipe === $pipes[2]){
								$error .= $data;
							}
//							echo "ouput in server before strip: " . $output . " " . bin2hex($data) . "\n";
							$output1=explode("\x0A", $output);
							$output2=$output1[1];
//							echo "ouput in server after strip: $output2 " . bin2hex($output2) . "\n";
							$output = $output2 . " 1234";
							if ($tWait=0){  //done to prevent transmit on first loop
								$this->trigger_hooks("INPUT",$this->clients[$ii],$output);
							}
							$tWait=0;
							$output='';
						}
						$status = proc_get_status($process);
						if (!$status["running"] && feof($pipes[1]) && feof($pipes[2])){
							break;
						}

					}
					}
				echo("done, output=$output\n");
				fclose($pipes[0]);
				fclose($pipes[1]);
				fclose($pipes[2]);
				proc_close($process);
			
					
		}else{  //normally using port 30040
			$i='';
			$p=0;
			echo "CTS=0, inputting $r 0 $i $p\n";
			$input = socket_recvfrom($this->masterSocket, $buf,$r,0,$i,$p);  //sock,buf,#chars,flags,fromIP,fromPort
			echo "input from $i: " . $buf . "\n";
			$tdat=explode(" ",$buf);
			echo "incoming: gottit last: " . $this->config['gottit'] . " tdat $buf \n";
			if ($tdat[1]==0){
				$tdat[1]=1;
			}else{
				$tdat[1]=0;
			}
			if ($this->config["gottit"]!=$buf){
				$tW=$tdat[0]-$this->config["valueOld"];
				Echo "TW: $tW\n";
				$this->config["valueOld"]=$tdat[0];
				echo "trigger: " . $tdat[0] . " " . $tdat[1] . "\n";
				$this->trigger_hooks("INPUT",$this->clients[$ii],$tdat[0]. " " . $tdat[1]);
				$this->config["gottit"]=$buf;
			}
			usleep(50);
			return true;
		}
	}		
	/*!	@function	disconnect
		@abstract	Disconnects a client from the server.
		@param		int	- Index of the client to disconnect.
		@param		string	- Message to send to the hooks
		@result		void
	*/
	public function disconnect($client_index,$message = "")
	{
		$i = $client_index;
		CWUDPServer::debug("Client {$i} from {$this->clients[$i]->ip} Disconnecting");
		$this->trigger_hooks("DISCONNECT",$this->clients[$i],$message);
		$this->clients[$i]->destroy();
		unset($this->clients[$i]);
	}

	/*!	@function	trigger_hooks
		@abstract	Triggers Hooks for a certain command.
		@param		string	- Command who's hooks you want to trigger.
		@param		object	- The client who activated this command.
		@param		string	- The input from the client, or a message to be sent to the hooks.
		@result		void
	*/
	public function trigger_hooks($command,&$client,$input)
	{
		if(isset($this->hooks[$command]))
		{
			foreach($this->hooks[$command] as $function)
			{
				//SocketServer::debug("Triggering Hook '{$function}' for '{$command}'");
				//$continue = call_user_func($function,$this,&$client,$input);
				$tR=$this->config["radio"];
				$tU=$this->config["user"];
				$continue = call_user_func_array($function, array(&$this,&$client,$input,$tR,$tU)); 
				if($continue === FALSE) { break; }
			}
		}
	}

	/*!	@function	infinite_loop
		@abstract	Runs the server code until the server is shut down.
		@see		loop_once
		@param		void
		@result		void
	*/
	public function infinite_loop()
	{
		$test = true;
		do
		{
			echo "loop";
			$test = $this->loop_once();
			echo "in infinite test: " . $test==true . "\n";
		}
		while($test);
		echo "ALL DONE";
	}

	/*!	@function	debug
		@static
		@abstract	Outputs Text directly.
		@discussion	Yeah, should probably make a way to turn this off.
		@param		string	- Text to Output
		@result		void
	*/
	public static function debug($text)
	{
		echo("{$text}\r\n");
	}

	/*!	@function	socket_write_smart
		@static
		@abstract	Writes data to the socket, including the length of the data, and ends it with a CRLF unless specified.
		@discussion	It is perfectly valid for socket_write_smart to return zero which means no bytes have been written. Be sure to use the === operator to check for FALSE in case of an error. 
		@param		resource- Socket Instance
		@param		string	- Data to write to the socket.
		@param		string	- Data to end the line with.  Specify a "" if you don't want a line end sent.
		@result		mixed	- Returns the number of bytes successfully written to the socket or FALSE on failure. The error code can be retrieved with socket_last_error(). This code may be passed to socket_strerror() to get a textual explanation of the error.
	*/
	protected function read($socket )
	{
//			if (is_resource($socket)){
			$ii=0;
			$p=30040;//$this->config['port'];
			$r=15;
			$i="0.0.0.0";
			$input = socket_recvfrom($this->masterSocket, $buf,$r,0,$i,$p);  //sock,buf,#chars,flags,fromIP,fromPort
			$tdat=explode(" ",$buf);
//				if ($this->config["gottit"]!=$tdat[0]){
				$this->trigger_hooks("INPUT",$this->clients[$ii],$buf);
				$this->config["gottit"]=$tdat[0];
//				}

/*				if (false === ($buf = socket_read($master_socket[0], 2048, PHP_NORMAL_READ))) {
				echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($socket)) . "\n";
				return 2;
			}
			if (!$buf = trim($buf)) {
//				continue;
			}
			if ($buf == 'quit') {
				return;
			}
			if ($buf == 'shutdown') {
				socket_close($msgsock);
				return 2;
			}
			return $buf;
*///			}
	}

	public static function socket_write_smart(&$sock,$string,$crlf = "\r\n")
	{
//			SocketServer::debug("<-- {$string}");
		if($crlf) { $string = "{$string}{$crlf}"; }
		echo("ws: " . $string . "\n");

		@socket_write($sock,$string,strlen($string));
		return;
	}

	/*!	@function	__get
		@abstract	Magic Method used for allowing the reading of protected variables.
		@discussion	You never need to use this method, simply calling $server->variable works because of this methostence.
		@param		string	- Variable to retrieve
		@result		mixed	- Returns the reference to the variable called.
	*/

	function &__get($name)
	{
		return $this->{$name};
	}
}

	
?>