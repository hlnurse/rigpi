<?php
$ports = shell_exec("ls /dev/ttyUSB*");
$isRadio = shell_exec("ls /dev/radio*");
$data = "";
$isSerial = shell_exec("ls /dev/serial/by-id");
$aData = explode("\n", $isSerial);
$cData = count($aData) - 1;
for ($n = 0; $n < $cData; $n++) {
  $portx='-port';
  if (strpos($aData[$n],$portx )){
    $data =
      $data .
      "<div class='myCWPort' id='/dev/serial/by-id/$aData[$n]'><li><a class='dropdown-item' href='#'>/dev/serial/by-id/$aData[$n]</a></li></div>\n\r";
  }
}
for ($n = 0; $n < 10; $n++) {
  if (strpos($ports, "USB" . $n) !== false) {
    if (strpos($ports, "USB" . $n) !== false) {
      $data =
        $data .
        "<div class='myCWPort' id='/dev/ttyUSB$n'><li><a class='dropdown-item' href='#'>/dev/ttyUSB$n</a></li></div>\n\r";
    }
  }
}
$data =
  "<div class='myCWPort' id='/dev/ttyS0'><li><a class='dropdown-item' href='#'>/dev/ttyS0</a></li></div>\n\r" .
  $data;
$data =
  "<div class='myCWPort' id='cwPortNone'><li><a class='dropdown-item' href='#'>None</a></li></div>\n\r" .
  $data;
echo $data;
?>
