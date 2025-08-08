<?php

/**
 * @author Howard Nurse, W6HN.
 * 
 * This routine gets freq from running rigctld
 * 
 * It must live in the programs folder   
 */
 ini_set('max_execution_time', 1);
 if (isset($_POST["radio"])){
     $tMyRadio=$_POST["radio"];
 }else{
     $tMyRadio=2;
 }
 $tPort=4530 + 2 * $tMyRadio;
 $tDelay=1;
$fr="rigctl -m 2 -r localhost:$tPort f"; //rigctl -m 2 -r 127.0.0.1:$tPort f\n";
$output=shell_exec($fr);
echo substr($output,strpos($output, "\n")+1);
?>