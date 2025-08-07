<?php
/*
 * RigPi
 * Copyright (c) 2025 Howard Nurse, W6HN
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * CWUDPDo.php
 * 
 * Purpose:
 * Launches a CW UDP Server for transmitting CW keying signals over UDP.
 * Listens for UDP connections on a specific port, manages keying via GPIO,
 * and handles connected clients. Runs as a long-running daemon.
 */

global $queue;

// Required class and function dependencies
require_once("/var/www/html/classes/CWUDPServer.class.php");
require_once("/var/www/html/classes/queue.class.php");
require_once("/var/www/html/programs/sqldata.php");
require_once("/var/www/html/programs/SetLogFunc.php");
require_once("/var/www/html/programs/GetSettingsFunc.php");
require_once("/var/www/html/classes/MysqliDb.php");
require_once("/var/www/html/programs/GetUserFieldFunc.php");
require_once("/var/www/html/programs/SetSettingsFunc.php");

// Get command-line arguments if provided; otherwise use defaults
if ($argc > 1) {
	$tUser     = $argv[1];
	$tMyRadio  = $argv[2];
	$tMyPort   = $argv[3];
	$tMySerial = $argv[4];
	$test      = $argv[5];
} else {
	$tUser     = 'admin';
	$tMyRadio  = 'radio1';
	$tMyPort   = '30040';
	$tMySerial = "/dev/ttyS0";  // Default serial port device
	$test      = 1;
}

// Create a new queue for handling incoming UDP keying data
$queue = new \Ds\Queue();
echo print_r("queue: " . $queue . "\n");
$queue->push("hello");  // Test push

// Log entry point
doLog($test, "Entering CWUDPDo with $tUser $tMyRadio $tMyPort $tMySerial $test");

// Fixed local IP (only localhost listens)
$tMyIP = "127.0.0.1";

// Extract radio number (last character of 'radio1', 'radio2' etc.)
$tMyRadio = substr($tMyRadio, strlen($tMyRadio) - 1);

// Initialize runtime variables
$arKey    = array();
$tAvTime  = 0;
$tAvCount = 0;

// Set GPIO pin 13 for keying
require('/var/www/html/programs/vendor/autoload.php');
system("gpio mode 13 out");
system("gpio write 13 1");  // Key up (idle)

// Retrieve keying method: 'External CTS' or other
$tRTSKey = GetField($tMyRadio, "Keyer", "MySettings");
doLog($test, "tRTSKey: " . $tRTSKey);
$tRTSKey = ($tRTSKey == "External CTS") ? 1 : 0;

// Start UDP server instance
$tUDP = "$tMyIP, $tMyPort, $tMyRadio, $tUser, $tRTSKey";
echo "starting new CWUDPServer with " . $tUDP . "\n";

// Instantiate UDP server with parameters
$UDPServer = new CWUDPServer($tMyIP, $tMyPort, $tMyRadio, $tUser, $tRTSKey, $tMySerial);

// Limit max concurrent connections
$UDPServer->max_clients = 10;

// Register event hooks:
$UDPServer->hook("CONNECT", "handle_connect");  // When a new client connects
$UDPServer->hook("INPUT",   "handle_input");    // When data arrives from a client
$UDPServer->hook("CLOCK",   "handle_clock");    // On internal clock tick (if used)

// Start the UDP server infinite event loop
$UDPServer->infinite_loop();

/**
 * Called when a client connects to the UDP server
 *
 * @param object $server Server object
 * @param object $client Client object
 * @param string $input  Received input data
 * @param string $call   Call sign (if passed)
 * @param string $radio  Radio number
 *
 * @return void
 */
function handle_connect(&$server, &$client, $input, $call, $radio)
{
	echo "CW connected!" . "\n";
	system("gpio write 13 1");  // Key up
}

/**
 * Called on every clock tick (if supported by server loop)
 * Currently unused.
 */
function handle_clock(&$server, &$client, $input, $call, $radio)
{
}

/**
 * Called when data is received from a client
 *
 * @param object $server Server object
 * @param object $client Client object
 * @param string $input  Input string from client
 * @param string $radio  Radio number
 * @param string $user   Username
 *
 * @return void
 */
function handle_input(&$server, &$client, $input, $radio, $user)
{
	// Create a local queue for this function scope
	$queue = new \Ds\Queue();
	echo "INPUT: " . $input . "\n";

	// Parse input string (expects space-separated)
	$input1 = explode("\x20", $input);

	// Sanity check: if 2nd value is over 1000 ms, reset it to 0
	if ($input1[1] > 1000) {
		$input1[1] = 0;
	}

	// Add to queue, immediately pop, then clear queue
	$queue->push($input);
	$input2 = $queue->pop();
	$queue->clear();

	// Split processed input again
	$input1 = explode("\x20", $input2);
	echo "input1: " . $input1[1] . "\n";

	// If value is 0: key down, else key up
	if ($input1[1] === "0") {
		echo "INPUTx: " . $input1[0] . " key down\n";
		system("gpio write 13 0");  // Key down
	} else {
		echo "INPUTy: " . $input1[0] . " key up\n";
		system("gpio write 13 1");  // Key up
	}
}

/**
 * Logging utility
 *
 * @param int    $utest  1 = enable logging, 0 = disable
 * @param string $what   Log message content
 *
 * @return void
 */
function doLog($utest, $what)
{
	if ($utest == 1) {
		error_log(
			date("Y-m-d H:i:s", time()) . " " . $what . PHP_EOL,
			3,
			"/var/log/rigpi-radio.log"
		);
	}
}

?>
