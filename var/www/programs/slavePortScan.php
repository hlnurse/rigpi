<?php
$ports=shell_exec("ls /dev/ttyUSB*");
$isRadio=shell_exec("ls /dev/radio*");
$data="";
for ($n=0;$n<10;$n++){
		if (strpos($isRadio, "radio" . $n)!==FALSE){
			$data=$data . "<div class='myslaveport' id='/dev/radio$n'><li><a class='dropdown-item' href='#'>/dev/radio$n</a></li></div>\n\r";                                               
		}
		if (strpos($ports, "USB" . $n)!==FALSE){
			$data=$data . "<div class='myslaveport' id='/dev/ttyUSB$n'><li><a class='dropdown-item' href='#'>/dev/ttyUSB$n</a></li></div>\n\r";                                               
		}
}
$data="<div class='myslaveport' id='portNone'><li><a class='dropdown-item' href='#'>None</a></li></div>\n\r".$data;                                               
echo $data;
?>