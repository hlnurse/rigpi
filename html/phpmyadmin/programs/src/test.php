<?php
//for ($i=0; $i<5; $i++){
//    echo($i . "\n");
$t=50000;
    $status =exec("/var/www/html/programs/src/dtr_write1 1 > /dev/null 2>&1 &");
    
    usleep($t);
    
    $status = exec("/var/www/html/programs/src/dtr_write1 2 > /dev/null 2>&1 &");
    
    usleep($t);
    $status =exec("/var/www/html/programs/src/dtr_write1 1 > /dev/null 2>&1 &");
    
    usleep($t);
    
    $status = exec("/var/www/html/programs/src/dtr_write1 2 > /dev/null 2>&1 &");
    usleep($t);
    $status =exec("/var/www/html/programs/src/dtr_write1 1 > /dev/null 2>&1 &");
    
    usleep($t);
    $status = exec("/var/www/html/programs/src/dtr_write1 2 > /dev/null 2>&1 &");
  usleep($t);
  $status =exec("/var/www/html/programs/src/dtr_write1 1 > /dev/null 2>&1 &");
  
    usleep(3*$t);
    $status = exec("/var/www/html/programs/src/dtr_write1 2 > /dev/null 2>&1 &");

//}
?> 