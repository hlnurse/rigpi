<?php
/*
 * RigPi
 * Copyright (c) 2025 Howard Nurse, W6HN
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

/*
 * RotorDo.php — RigPi rotor control server launcher.
 * Starts up a TCP server process for rotor communications.
 * This server connects to rotctl or rotctld instances launched elsewhere.
 */

require_once('/var/www/html/classes/RotorServer.class.php');

// Enable full error reporting for debugging
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

// Retrieve command-line arguments or assign defaults
//  $argv[1] = username
//  $argv[2] = rotor name (e.g., rotor2)
//  $argv[3] = IP:port or port
//  $argv[4] = test mode flag (optional)

if (isset($argv[1])) {
	$tUser = $argv[1];
} else {
	$tUser = 'x';  // Default user if none provided
}

if (isset($argv[2])) {
	$tMyRotor = $argv[2];
} else {
	$tMyRotor = 'rotor2';  // Default rotor
}

if (isset($argv[3])) {
	$port = $argv[3];
} else {
	$port = '172.16.0.47:4533';  // Default IP:port
}

if (isset($argv[4])) {
	$test = $argv[4];
} else {
	$test = 1;  // Default test mode enabled
}

// Parse the port string into IP and port number components
if (strpos($port, ":") > 0) {
	$tR = explode(":", $port);
	$tRotorPort = $tR[1];
	$tRotorIP = $tR[0];
} else {
	// If no colon, treat value as port number and default to all IP interfaces
	$tRotorIP = '0.0.0.0';
	$tRotorPort = $port;
}

// Extract rotor number (last character of rotor name, e.g. '2' from 'rotor2')
$tMyRotor = substr($tMyRotor, strlen($tMyRotor) - 1);

// Create a new RotorServer instance with provided connection details
$rotorServer = new RotorServer($tRotorIP, $tRotorPort, $tMyRotor, $test);

// Brief delay to allow server instance to initialize before loop begins
usleep(100000);  // 100ms

// Start the RotorServer infinite loop — this will run indefinitely
$rotorServer->infinite_loop();

?>
