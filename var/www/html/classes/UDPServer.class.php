<?php
require_once "/var/www/html/classes/PhpSerialClass.php";
/*!	@class		SocketServer
		@author		Navarr Barnier
		@abstract 	A Framework for creating a multi-client server using the PHP language.
	 */
class UDPServer
{
  protected $config;

  /*!	@var		hooks
			@abstract	Array - a dictionary of hooks and the callbacks attached to them.
		 */
  protected $hooks;

  /*!	@var		master_socket
			@abstract	resource - The master socket used by the server.
		 */
  protected $master_socket;

  /*!	@var		max_clients
			@abstract	unsigned int - The maximum number of clients allowed to connect.
		 */
  public $max_clients = 10;

  /*!	@var		max_read
			@abstract	unsigned int - The maximum number of bytes to read from a socket at a single time.
		 */
  public $max_read = 2048;

  /*!	@var		clients
			@abstract	Array - an array of connected clients.
		 */
  public $clients;
  /*!	@function	__construct
			@abstract	Creates the socket and starts listening to it.
			@param		string	- IP Address to bind to, NULL for default.
			@param		int	- Port to bind to
			@result		void
		 */
  public function __construct($bind_ip, $port, $tRadio, $tUser)
  {
    $bind_ip = "localhost";
    set_time_limit(0);
    $this->hooks = [];
    $this->config["ip"] = $bind_ip;
    $this->config["port"] = intval($port);
    $this->config["radio"] = $tRadio;
    $this->config["user"] = $tUser;
    if ($port != 0) {
      $this->master_socket = socket_create(AF_INET, SOCK_DGRAM, 0);
      socket_bind($this->master_socket, 0, $this->config["port"]) or
        die("Issue Binding");
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
  public function hook($command, $function)
  {
    $command = strtoupper($command);
    if (!isset($this->hooks[$command])) {
      $this->hooks[$command] = [];
    }
    $k = array_search($function, $this->hooks[$command]);
    if ($k === false) {
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
  public function unhook($command = null, $function)
  {
    $command = strtoupper($command);
    if ($command !== null) {
      $k = array_search($function, $this->hooks[$command]);
      if ($k !== false) {
        unset($this->hooks[$command][$k]);
      }
    } else {
      $k = array_search($this->user_funcs, $function);
      if ($k !== false) {
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
    $ii = 0;
    $p = $this->config["port"];
    $r = 4096;
    $i = "localhost";
    $input = socket_recvfrom($this->master_socket, $buf, 4096, 0, $i, $p);
    $this->trigger_hooks("INPUT", $this->clients[$ii], $buf);
    return true;
  }

  /*!	@function	disconnect
			@abstract	Disconnects a client from the server.
			@param		int	- Index of the client to disconnect.
			@param		string	- Message to send to the hooks
			@result		void
		*/
  public function disconnect($client_index, $message = "")
  {
    $i = $client_index;
    SocketServer::debug(
      "Client {$i} from {$this->clients[$i]->ip} Disconnecting"
    );
    $this->trigger_hooks("DISCONNECT", $this->clients[$i], $message);
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
  public function trigger_hooks($command, &$client, $input)
  {
    if (isset($this->hooks[$command])) {
      foreach ($this->hooks[$command] as $function) {
        //SocketServer::debug("Triggering Hook '{$function}' for '{$command}'");
        //$continue = call_user_func($function,$this,&$client,$input);
        $tR = $this->config["radio"];
        $tU = $this->config["user"];
        $continue = call_user_func_array($function, [
          &$this,
          &$client,
          $input,
          $tR,
          $tU,
        ]);
        if ($continue === false) {
          break;
        }
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
    do {
      //				echo "looping\n";
      $test = $this->loop_once();
    } while ($test);
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
    echo "{$text}\r\n";
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
  public static function socket_write_smart(&$sock, $string, $crlf = "\r\n")
  {
    //			SocketServer::debug("<-- {$string}");
    if ($crlf) {
      $string = "{$string}{$crlf}";
    }
    @socket_write($sock, $string, strlen($string));
    return;
  }

  /*!	@function	__get
			@abstract	Magic Method used for allowing the reading of protected variables.
			@discussion	You never need to use this method, simply calling $server->variable works because of this method's existence.
			@param		string	- Variable to retrieve
			@result		mixed	- Returns the reference to the variable called.
		*/
  public function doLog($what)
  {
    if ($this->config["test"] == 1) {
      error_log(
        date("Y-m-d H:i:s", time()) . " " . $what,
        3,
        "/var/log/rigpi-radio.log"
      );
    }
  }

  function &__get($name)
  {
    return $this->{$name};
  }
}

?>
