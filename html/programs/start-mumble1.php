<?php
//$audio = $_POST["audioState"];
$audio = "ON";
echo "OK";
/*if ($audio == "ON") {
  exec(
    "sudo /home/pi/mumble/build/mumble --window-title-ext 'Radio 1' -m -n mumble://radio1:7388@rigpi3.local"
  );
}
*/ /*$users = exec("ps aux | grep -E 'window-title' | grep -v 'grep'");
$tMumblePID = substr($users, 9, 5);
echo "users: " . $tMumblePID;
if (strlen($tMumblePID > 0)) {
  $iD = "exec sudo kill " . $tMumblePID;
  shell_exec($iD);
  echo $iD . "\n";
  if ($iD == true) {
    echo "deleted";
  }
}
$out = exec(
  "/home/pi/Desktop/M1"
  //  "sh sudo /usr/share/rigpi/start-mumble1.sh 2>/dev/null >dev/null &"
);
echo "done: " . "<pre>$out</pre>";
*/
/*exec(
  "su pi mumble --window-title-ext 'Radio 1' -m -n mumble://radio1:7388@rigpi3.local"
);
*/

shell_exec("/usr/share/rigpi/x.php");
sleep(1);
shell_exec("sudo /usr/share/rigpi/x1.sh");
?>
