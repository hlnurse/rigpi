<?php
function disRadio($tMyRadio, $tUsername, $tMyRotor)
{
  $tMyRadio = 2;
  $tUsername = "ptt";
  $tMyRotor = "";
  $tTable = "RadioInterface";
  $tField = "CWIn";
  $tData = "<18><00><02><00>";
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  $dRoot = "/var/www/html";
  require $dRoot . "/programs/sqldata.php";
  //sets cw PTT off
  $sQuery = "UPDATE $tTable SET $tField = concat($tField, '$tData') WHERE Radio='$tMyRadio'";
  $con = new mysqli(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  $result = $con->query($sQuery);
  $sQuery = "UPDATE $tTable SET MainIn = 'OFF' WHERE Radio='$tMyRadio'";
  $result = $con->query($sQuery);
  usleep(100000);
  $tR = "'[T]CPDo.php $tUsername [r]adio$tMyRadio'";
  $users = exec("ps aux | grep -i " . $tR);
  $tTCPDoPID = substr($users, 9, 5);
  $tR = "'[R]igDo.php $tUsername [r]adio$tMyRadio'";
  $users = exec("ps aux | grep -i " . $tR);
  $tRigDoPID = substr($users, 9, 5);
  $tR = "'[R]igDo_PTT.php $tUsername [r]adio$tMyRadio'";
  $users = exec("ps aux | grep -i " . $tR);
  $tRigDoPID_PTT = substr($users, 9, 5);
  $tR = "'[C]WDo.php $tUsername [r]adio$tMyRadio'";
  $users = exec("ps aux | grep -i " . $tR);
  $tCWDoPID = substr($users, 9, 5);
  $tR = "'[G]PIOInt1.php'";
  $users = exec("ps aux | grep -i " . $tR);
  $tGPIOPID = substr($users, 9, 5);
  $tR = "'[U]DPDo.php $tUsername [r]adio$tMyRadio'";
  $users = exec("ps aux | grep -i " . $tR);
  $tUDPDoPID = substr($users, 9, 5);
  $tR = "'[R]otorDo.php $tUsername [r]otor$tMyRadio'";
  $users = exec("ps aux | grep -i " . $tR);
  $tRotorDoPID = substr($users, 9, 5);
  $tRPort = $tMyRadio * 2 + 4530;
  $tR = "'t\s$tRPort'"; //this closes rigctld for this radio by using port number
  $users = exec("ps aux | grep -i " . $tR);
  $trigctldPID = substr($users, 9, 5);
  $users1 = exec("ps aux | grep -i -m 1 'sudo rigctld'");
  $trigctldPID1 = substr($users1, 9, 5);
  $tRoPort = $tRPort + 1;
  $tR = "'t\s$tRoPort'"; //this closes rotctld for this radio by using port number
  $users = exec("ps aux | grep -i " . $tR);
  $trotctldPID = substr($users, 9, 5);

  if ($tTCPDoPID > 0) {
    posix_kill(trim($tTCPDoPID), 15);
  }
  if ($tRigDoPID > 0) {
    posix_kill(trim($tRigDoPID), 15);
  }
  if ($tRigDoPID_PTT > 0) {
    posix_kill(trim($tRigDoPID_PTT), 15);
  }
  if ($tCWDoPID > 0) {
    posix_kill(trim($tCWDoPID), 15);
  }
  if ($tGPIOPID > 0) {
    $tK = "/usr/share/rigpi/nocw.sh " . $tGPIOPID;
    exec($tK);
  }
  if ($tUDPDoPID > 0) {
    posix_kill(trim($tUDPDoPID), 15);
  }
  if ($tRotorDoPID > 0) {
    posix_kill(trim($tRotorDoPID), 15);
  }
  if ($trigctldPID > 0) {
    $tK = "/usr/share/rigpi/nocw.sh " . $trigctldPID;
    exec($tK);
  }
  if ($trigctldPID1 > 0) {
    $tK = "/usr/share/rigpi/nocw.sh " . $trigctldPID1;
    exec($tK);
  }
  if ($trotctldPID > 0) {
    posix_kill(trim($trotctldPID), 15);
  }
  /*	if ($tRigDoPID>0){
    echo "<br>Radio ".$tMyRadio." (for ".$tUsername." account) is now disconnected.<br><br>";
  }else{
    echo "<br>Radio connection ".$tMyRadio." was already disconnected.<br><br>";
  }
*/
}
?>
