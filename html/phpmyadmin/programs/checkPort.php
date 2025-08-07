<?php
session_start();
$tClusterPort=$_SESSION['clusterPort'];
header('Content-Type: text/plain');
$tClusterPort=$_POST['clusterPort'];
// Run the ss command and capture the output
$output = shell_exec("ss -tn | grep $tClusterPort");

// Return the result, or a message if no connection found
if (empty($output)) {
	echo "";//No connection found on port $tClusterPort.";
} else {
	echo $output;
}
?>
