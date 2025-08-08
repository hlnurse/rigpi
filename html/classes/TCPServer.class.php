<?php
define("DB_SERVER", "localhost");
define("DB_USER", "ham");
define("DB_PWD", "7388");
define("DB_NAME", "station");
require_once "/var/www/html/programs/GetSettingsFunc.php";
require_once "/var/www/html/programs/SetSettingsFunc.php";
require_once "/var/www/html/programs/GetInterfaceOut.php";
/*!	@class		SocketServer
    @author		Navarr Barnier
    @abstract 	A Framework for creating a multi-client server using the PHP language.
   */
class TCPServer
{
  private static $user;
  public $keyerPort;
  /*!	@var		config
      @abstract	Array - an array of configuration information used by the server.
     */
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
  protected $clients;
  /*!	@function	__construct
      @abstract	Creates the socket and starts listening to it.
      @param		string	- IP Address to bind to, NULL for default.
      @param		int	- Port to bind to
      @result		void
     */
  public function __construct($bind_ip, $port, $user, $radio)
  {
    set_time_limit(0);
    $this->hooks = [];

    $this->config["ip"] = $bind_ip;
    $this->config["port"] = $port;
    $this->config["user"] = $user;
    $this->config["radio"] = $radio;
    if ($port != 0) {
      $this->master_socket = socket_create(AF_INET, SOCK_STREAM, 0);
      socket_bind(
        $this->master_socket,
        $this->config["ip"],
        $this->config["port"]
      ) or die("Issue Binding");
      socket_getsockname($this->master_socket, $bind_ip, $port);
      socket_listen($this->master_socket);
      $this->debug("master socket created");
    }
    echo "finished construct\n";
  }

  public function setRigData($radio, $field, $data)
  {
    setRigInterface($radio, $field, $data);
  }

  public function getRigData($radio, $field, $data)
  {
    return getField($radio, $field, $data);
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
       $this->debug("looping...\n");
    $tMyRadio = $this->config["radio"];
    $tUser = $this->config["user"];
    $read = [];
    //    echo "for radio: " . $tMyRadio . "\n";
    // Setup Clients Listen Socket For Reading
    $read[0] = $this->master_socket;
    for ($i = 0; $i < $this->max_clients; $i++) {
      if (isset($this->clients[$i])) {
        $read[$i + 1] = $this->clients[$i]->socket;
                echo "new read: " . $read[$i + 1] . "\n";
      }
    }
    //    echo "after new array " . count($read) . " " . var_dump($read) . "\n";

    // Set up a blocking call to socket_select
    $except = null;
    $write = null;
    if (socket_select($read, $write, $except, $tv_sec = 5) < 1) {
      //      SocketServer::debug("Problem blocking socket_select?");
      return true;
    }

    // Handle new Connections
    if (in_array($this->master_socket, $read)) {
      for ($i = 0; $i < $this->max_clients; $i++) {
        if (empty($this->clients[$i])) {
                   echo "new client: " . $i . "\n";
          $temp_sock = $this->master_socket;
          $this->clients[$i] = new SocketServerClient($this->master_socket, $i);
          $this->trigger_hooks(
            "CONNECT",
            $this->clients[$i],
            "",
            $tUser,
            $tMyRadio
          );
          break;
        } elseif ($i == $this->max_clients - 1) {
          $this->debug("Too many clients... :( ");
        }
      }
    }

    //    echo "before handle " . print_r($read) . "\n";
    // Handle \
    //    $i = 0;
    $input = "";
    for ($i = 0; $i < $this->max_clients; $i++) {
      //     echo "i0: " . $i . "\n";
      if (isset($this->clients[$i])) {
        //        echo "i: " . $i . "\n";
        //        echo "i in array: " .
//        in_array($this->clients[$i]->socket, $read) . "\n";
        if (in_array($this->clients[$i]->socket, $read)) {
          //          echo "i1: " . $i . "\n";
          $input = socket_read($this->clients[$i]->socket, $this->max_read);
          echo "input: $input \n";
          if ($input == null || $input == "") {
            //            return true;
            $this->disconnect($i);
            //          } else {
          }
        }
      }
    }
//        echo "strlen: " . strlen($input) . " " . $input . " " . bin2hex($input) . "\n";
    if (strlen($input) > 0) {
      $aComm = explode(";", $input);
      for ($i = 0; $i < count($aComm); $i++) {
        echo "tcp input number: " .
        $i .
        " " .
        $aComm[$i] .
        " " .
        bin2hex($aComm[$i]) .
        " " .
        strpos($aComm[$i], "KS") .
        "\n";

        if (strpos($aComm[$i], "KR") !== false) {
          $bComm = str_replace("KR", "", $aComm[$i]);
          if (strlen($bComm) > 0) {
            for ($j = 0; $j < strlen($bComm); $j++) {
  //           SocketServer::debug("{$i}@{$this->clients[$i]->ip} --> {$input}");
              //            $tMyRadio = $this->config["radio"];
              //            $tUser = $this->config["user"];
              //            echo $input;
              echo "trigger ECHO: " .
                $aComm[$j] .
                " " .
                $i .
                " " .
                bin2hex($aComm[$j]) .
                "\n";

              $this->trigger_hooks(
                "ECHO",
                $this->clients,
                $aComm[$i] . ";",
                $tUser,
                $tMyRadio
              );
            }
          }
        } elseif ((strpos(str_replace(" ", "", $aComm[$i]),"KS"))> -1) {
          $s=$aComm[$i];
          if (strlen($s)>0) {
            echo "S: $s\n";
              $t=str_replace(" ", "", $s);
              $t=substr($t,2);
              $t=str_replace(";", "", $t);
              echo "trigger SPEED: " .
              $t .
              " " .
              $t .
              "\n";
            $this->trigger_hooks(
              "SPEED",
              $this->clients,
//              bin2hex($t),
                $t,
              $tUser,
              $tMyRadio
            );
          }
        } else {
          echo "aComm: $aComm[$i] of len " . strlen($aComm[$i]) . " and hex: " . bin2hex($aComm[$i]) . "\n";
          if (strlen($aComm[$i])>0){
            $t=$aComm[$i] . ";";
             echo "trigger INPUT: " .
                $t .
                " " .
                bin2hex($t) .
                "\n";
            $this->trigger_hooks(
              "INPUT",
              $this->clients,
              $t,
              $tUser,
              $tMyRadio
            );
           }
        }
      }

      //            }
      //          }
      //        }
      //      }
/*            $data = GetWKOut($tMyRadio);
      $this->debug("from db: " . $data);
      $data = explode(chr(96), $data);
      if ($data[0] == 1) {
        $this->debug("to ip: " . "KY " . $data[1] . ";");
        SetInterface($tMyRadio, "CWOutWKCk", 0);
        socket_write($this->clients[$i]->socket, $data[1] . ";");
      }
*/
    }
    //    echo "leaving..." . "\n";
    return true;
  }

  /*!	@function	ect
      @abstract	Disconnects a client from the server.
      @param		int	- Index of the client to disconnect.
      @param		string	- Message to send to the hooks
      @result		void
    */
  public function disconnect($client_index, $message = "")
  {
    $tMyRadio = $this->config["radio"];
    $tUser = $this->config["user"];
    $i = $client_index;
    $this->debug("Client {$i} from {$this->clients[$i]->ip} Disconnecting");
    $this->trigger_hooks(
      "DISCONNECT",
      $this->clients[$i],
      $message,
      $tUser,
      $tMyRadio
    );
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
    //    echo $input;
    if (isset($this->hooks[$command])) {
      $tMyRadio = $this->config["radio"];
      $tUser = $this->config["user"];
      foreach ($this->hooks[$command] as $function) {
        //SocketServer::debug("Triggering Hook '{$function}' for '{$command}'");
        //$continue = call_user_func($function,$this,&$client,$input);
        $continue = call_user_func_array($function, [
          &$this,
          &$client,
          $input,
          $tUser,
          $tMyRadio,
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
    $this->debug("infinite loop started\n");
    $test = true;
    do {
      //      echo "loop\n";
      $test = $this->loop_once();
    } while ($test);
    echo "ALL DONE";
  }

  /*  public function read_loop()
  {
    $this->debug("read loop started\n");
    $test = true;
    do {
      echo "read loop\n";
      usleep(100000);
    } while ($test);
    echo "ALL DONE";
  }
*/

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
    //    echo "TCP class write... " . $sock . " " . $string . "\n";
    if ($crlf) {
      $string = "{$string}{$crlf}";
    }
    //    for ($i = 0; $i < count($clients); $i++) {
//    if (file_exists($sock)){
  echo "string: $string\n";
      socket_write($sock, $string);
//    }
    return;
  }

  /*!	@function	__get
      @abstract	Magic Method used for allowing the reading of protected variables.
      @discussion	You never need to use this method, simply calling $server->variable works because of this method's existence.
      @param		string	- Variable to retrieve
      @result		mixed	- Returns the reference to the variable called.
    */
  public static function rig_socket_write_smart(&$sock, $string, $crlf = "")
  {
    //   SocketServer::debug("<-- {$string}");
    if ($crlf) {
      $string = "{$string}{$crlf}";
    }
    //    SocketServer::debug("write rig socket: ".$string);
    if (is_resource($sock)){
      return fwrite($sock, $string);
    }
  }

  public static function rig_socket_read(&$sock, $count)
  {
    $read = fread($sock, $count);
    SocketServer::debug("read: " . $read);
    return $read;

    /*			if($input == null)
      {
        $this->disconnect($i);
      }
      else
      {
*/
    //       SocketServer::debug("{$i}@{$this->clients[$i]->ip} --> {$input}");
    //    $this->trigger_hooks("INPUT",$this->clients[$i],$input);
    //   }
  }
  function &__get($name)
  {
    return $this->{$name};
  }
}

/*!XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
/*!XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    @class		SocketServerClient
    @author		Navarr Barnier
    @abstract	A Client Instance for use with SocketServer
   */
class SocketServerClient
{
  /*!	@var		socket
      @abstract	resource - The client's socket resource, for sending and receiving data with.
     */
  protected $socket;

  /*!	@var		ip
      @abstract	string - The client's IP address, as seen by the server.
     */
  protected $ip;

  /*!	@var		hostname
      @abstract	string - The client's hostname, as seen by the server.
      @discussion	This variable is only set after calling lookup_hostname, as hostname lookups can take up a decent amount of time.
      @see		lookup_hostname
     */
  protected $hostname;

  /*!	@var		server_clients_index
      @abstract	int - The index of this client in the SocketServer's client array.
     */
  protected $server_clients_index;

  /*!	@function	__construct
      @param		resource- The resource of the socket the client is connecting by, generally the master socket.
      @param		int	- The Index in the Server's client array.
      @result		void
     */
  public function __construct(&$socket, $i)
  {
    $this->server_clients_index = $i;
    ($this->socket = socket_accept($socket)) or die("Failed to Accept");
    TCPServer::debug("New Client Connected: " . $this->socket);
    socket_getpeername($this->socket, $ip);
    $this->ip = $ip;
  }

  /*!	@function	lookup_hostname
      @abstract	Searches for the user's hostname and stores the result to hostname.
      @see		hostname
      @param		void
      @result		string	- The hostname on success or the IP address on failure.
     */
  public function lookup_hostname()
  {
    $this->hostname = gethostbyaddr($this->ip);
    return $this->hostname;
  }

  /*!	@function	destroy
      @abstract	Closes the socket.  Thats pretty much it.
      @param		void
      @result		void
     */
  public function destroy()
  {
    socket_close($this->socket);
  }

  function &__get($name)
  {
    return $this->{$name};
  }

  function __isset($name)
  {
    return isset($this->{$name});
  }
}

?>
