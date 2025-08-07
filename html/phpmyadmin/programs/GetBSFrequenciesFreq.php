<?php

/**
 * @author Howard Nurse, W6HN
 *
 * This routine gets frequency scale for the Band Spotter panel
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
$freq = $_POST["freq"];
$band = $_POST["band"];
//	$band='20';
//$pixStart = 0;
$fStart = 0;
$pixStart = 90;
//$fStart = ($freq + 370000) / 1000;
switch ($band) {
  case "160":
    $pixStart = 90;
    $fStart = 2090;
    break;
  case "80":
    $pixStart = 90;
    $fStart = 4040;
    break;
  case "60":
    $pixStart = 90;
    $fStart = 5490;
    break;
  case "40":
    $pixStart = 90;
    $fStart = 7390; //($freq + 365000) / 1000;
    //    $fStart = 7390;
    break;
  case "30":
    $pixStart = 90;
    $fStart = 10240;
    break;
  case "20":
    $pixStart = 90;
    $fStart = 14440;
    break;
  case "17":
    $pixStart = 90;
    $fStart = 18260;
    break;
  case "15":
    $pixStart = 90;
    $fStart = 21490;
    break;
  case "12":
    $pixStart = 90;
    $fStart = 25080;
    break;
  case "10":
    switch ($freq) {
      case $freq >= 28000000 and $freq <= 28500000:
        $fStart = 28540;
        break;
      case $freq >= 28500000 and $freq <= 29000000:
        $fStart = 29040;
        break;
      case $freq >= 29000000 and $freq <= 29500000:
        $fStart = 29540;
        break;
      case $freq >= 29500000 and $freq <= 30000000:
        $fStart = 30040;
        break;
    }
    break;
  case "6":
    $freq = $freq - 50000000;
    $freq = intval($freq / 500000);
    $fStart = $freq * 500 + 540 + 50000;
    $pixStart = 90;
    break;
  case "2":
    $freq = $freq - 144000000;
    $freq = intval($freq / 500000);
    $fStart = $freq * 500 + 540 + 144000;
    $pixStart = 90;
    break;
  case "1.25":
    $freq = $freq - 222000000;
    $freq = intval($freq / 500000);
    $fStart = $freq * 500 + 540 + 222000;
    $pixStart = 90;
    break;
  case "70":
    $freq = $freq - 420000000;
    $freq = intval($freq / 500000);
    $fStart = $freq * 500 + 540 + 420000;
    $pixStart = 90;
    break;
  case "23":
    $freq = $freq - 1240000000;
    $freq = intval($freq / 500000);
    $fStart = $freq * 500 + 540 + 1240000;
    $pixStart = 90;
    break;
}

$tOut = "";
for ($i = 0; $i < 59; $i++) {
  $tPix = $i * 100 + $pixStart;
  $tFreq = $fStart - $i * 10;
  $tFreq = number_format($tFreq, 0, ".", ".");
  $tOut .= "<h3><div class='BSFrequency' style='top:" . $tPix . "px'>";
  if (strlen($tFreq) == 9) {
    $tFreq = join("", explode(".", $tFreq, 2));
  }
  $tOut .= $tFreq;
  $tOut .= "</div></h3>";
}
echo $tOut;

?>
