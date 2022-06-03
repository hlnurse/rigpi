<?php
/*
RigPi can control its own RigPi Keyer, or an external K1EL WinKeyer connected through a USB port.

RigPi Keyer is controlled through /dev/ttyS0.  For external keyers, the USB port will be /dev/ttyUSBn
where n can be 0-3 (assuming the RPi standard USB ports).  The RPi uses USB hot pluging so the actual port
number will vary.

The the only dependable way to connect to an external keyer is by using unique information about the connection
to symbolically link to the correct port.  The vendor id, product id, and serial number usually uniquely 
identify each plugged in USB cable.

The following code assumes a file named keyer.rules has been created in /etc/udev/rules.d/ that provides the necessary
information to the operating system when it boots.  If the symbolic link is not found, the code falls back to 
identifying all possible active USB connections.
*/

$ports=shell_exec("ls /dev/ttyUSB*");  //find all active USB ports
$isKeyer=shell_exec("ls /dev/keyer*"); //find all keyer symbolic links
$data="";
for ($n=0;$n<10;$n++){
	if (strpos($isKeyer, "keyer")!==FALSE){
		if (strpos($isKeyer, "keyer" . $n)!==FALSE){
			$data=$data . "<div class='myKeyerPort' id='/dev/keyer$n'><li><a class='dropdown-item' href='#'>'/dev/keyer$n'</a></li></div>\n";                                               
		}
	}else{
		if (strpos($ports, "USB" . $n)!==FALSE){
			$data=$data . "<div class='myKeyerPort' id='/dev/ttyUSB$n'><li><a class='dropdown-item' href='#'>'/dev/ttyUSB$n'</a></li></div>\n";                                               
		}
	}
}
echo $data;
?>