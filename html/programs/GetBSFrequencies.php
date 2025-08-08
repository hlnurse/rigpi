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
$band = $_POST["band"];
$freq = $_POST["freq"];
//	$band='20';
$pixStart = 0;
$fStart = 0;
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
    $fStart = 7390;
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
    $pixStart = 90;
    $fStart = 28540;
    break;
  case "6":
    $pixStart = 90;
    $fStart = 50540;
    break;
  case "2":
    $pixStart = 90;
    $fStart = 144540;
    break;
  case "1.25":
    $pixStart = 90;
    $fStart = 222540;
    break;
  case "70":
    $pixStart = 90;
    $fStart = 420540;
    break;
  case "23":
    $pixStart = 90;
    $fStart = 1296340;
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
