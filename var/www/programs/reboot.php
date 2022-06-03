<?php
	system("sudo reboot");
    error_log(date("[Y-m-d H:i:s]")."\t[".$level."]\t[".basename(__FILE__)."]\t".$text."\n", 3, 'errorlog.txt');
?>