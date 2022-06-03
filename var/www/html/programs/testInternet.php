<?php
$ip ="8.8.8.8";
exec("timeout 2.0 ping -c 1 -n -q $ip > /dev/null 2>&1",$output,$status);
print_r($status);
?>