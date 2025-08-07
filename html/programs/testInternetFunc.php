<?php
function getInternetOK(){
	$ip ="8.8.8.8";
	exec("timeout 1.0 ping -c 1 -n -q $ip > /dev/null 2>&1",$output,$status);
	return $status;
	
}
?>