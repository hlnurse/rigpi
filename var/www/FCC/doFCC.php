<?php
//executes hamdb for full FCC database.  Note it is possible to download daily update which are much smaller.  See commented lines in hamdb.
//
//by Howard Nurse, W6HN
shell_exec("sudo -S /usr/local/bin/hamdb populatedb");
//echo error_get_last();
//echo exec("hamdb w6hn");
//echo $result;
echo "<br>The FCC Database has been downloaded and installed.<br><br>";
?>
