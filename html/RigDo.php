<?php
/*
 * RigPi RigDo Server Launcher
 * Copyright (c) 2025 Howard Nurse, W6HN
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Description:
 *  This script launches a RigServer instance for RigPi, acting as a TCP server 
 *  for rig control communications. It connects to Hamlib rigctl/rigctld instances
 *  typically launched by h.php, and provides a network interface to manage them.
 */

// --- Load Required Libraries and Config Files ---

require_once "/var/www/html/programs/sqldata.php";
require_once "/var/www/html//classes/MysqliDb.php";
require_once "/var/www/html/classes/RigServer.class.php";
require_once "/var/www/html/programs/GetSettingsFunc.php";

// --- Enable Error Reporting for Debugging ---

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);

// --- Parse Command-Line Arguments ---

// Username (for logging/tracking); defaults to 'admin'
if (isset($argv[1])) {
  $tUser = $argv[1];
} else {
  $tUser = "admin";
}

// Radio identifier (like 'radio1', 'radio2'); defaults to 'radio1'
if (isset($argv[2])) {
  $tMyRadio = $argv[2];
} else {
  $tMyRadio = "radio1";
}

// Extract numeric radio ID (last character)
$tMyRadio = substr($tMyRadio, strlen($tMyRadio) - 1);

// --- Look Up Radio Port from MySettings Database Table ---

// Use helper function to retrieve 'Port' field for this radio
$port = GetField($tMyRadio, "Port", "MySettings");
echo $port . "\n";

// --- Determine IP and Port for Rig Server ---

if (strstr($port, ":")) {
  // If port contains an IP:Port string
  $p = explode(":", $port);
  $port = $p[1];
  $ip = $p[0];
} elseif (strstr($port, "/dev")) {
  // If it's a device path (like /dev/ttyUSB0), assign unique port per radio
  $port = $tMyRadio * 2 + 4530;
  $ip = "127.0.0.1";
} elseif (strstr($port, "45")) {
  // If it already looks like a TCP port number, keep it
  $port = $port;
  $ip = "127.0.0.1";
} else {
  // Otherwise default to calculated port number based on radio ID
  $port = 4530 + 2 * $tMyRadio;
  $ip = '127.0.0.1';
}

// --- Additional Arguments ---

// Test mode flag (1 = on, 0 = off)
if (isset($argv[4])) {
  $test = $argv[4];
} else {
  $test = 1;
}

// Default VFO (like 'VFOA', 'VFOB')
if (isset($argv[5])) {
  $vfo = $argv[5];
} else {
  $vfo = "VFOA";
}

// VFO Mode flag (0 = off, 1 = split/dual)
if (isset($argv[6])) {
  $vfoMode = $argv[6];
} else {
  $vfoMode = 0;
}

// Supports USB Audio flag (1 = yes, 0 = no)
if (isset($argv[7])) {
  $supportsUSB = $argv[7];
} else {
  $supportsUSB = 1;
}

// Instance ID (999 = default if not specified)
if (isset($argv[8])) {
  $instance = $argv[8];
} else {
  $instance = 999;
}

// --- Diagnostic Output ---
echo $port . " " . $tMyRadio . " " . $test . " " . $vfo . " " . $vfoMode . " " . $supportsUSB . "\n";

// --- Create and Launch Rig Server ---

// Instantiate a RigServer object with parameters: IP, Port, Radio ID, Test flag, VFO, VFO Mode, USB Support
$rigServer = new RigServer($ip, $port, $tMyRadio, $test, $vfo, $vfoMode, $supportsUSB);

// Small delay before starting server loop to allow system readiness
usleep(500000);  // 500 milliseconds

// Enter the server's main command loop (blocks here)
$rigServer->infinite_loop();

?>
