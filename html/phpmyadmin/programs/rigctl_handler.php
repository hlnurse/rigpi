<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Execute the rigctl command\
		if (isset($_POST['port'])){
			$tMyPort=$_POST['port'];
		}else{
			$tMyPort=4532;
		}
		
//		$tMyPort='172.16.0.43:4532';
		$tExec='rigctl -m 2 -r ' . $tMyPort . ' f m t 2>&1';
		
		$output = shell_exec($tExec);
	
		// Check if output exists and return it as JSON
		if ($output !== null) {
			$output=str_replace("\n", "|", $output);
			echo json_encode(['status' => 'success', 'data' => trim($output)]);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Command failed to execute']);
		}
}
?>
