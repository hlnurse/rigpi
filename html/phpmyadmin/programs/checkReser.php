<?php
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
require_once "/var/www/html/programs/sqldata.php";
$con = new mysqli(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}
$aRadios = [];
$aRigctl = [];
$tEcho4 = "";
$tEcho3 = "";
$tEcho2 = "";
$tEcho1 = "";
$sQuery =
  "SELECT DISTINCT RadioName FROM MySettings WHERE Model != 'Net rigctl' AND Model != 'Dummy'";

$resultRad = $con->query($sQuery);
$i = 0;
//look for connected physical radios
while ($row = mysqli_fetch_array($resultRad)) {
  $tN = $row["RadioName"];
  if (strlen($tEcho1)==0){
    $tEcho1 = $tEcho1 . $tN . " is found";
  }else{
    $tEcho1 = $tEcho1 . "<br>" . $tN . " is found";
  }
 $j = $i;
  $sQuery = "SELECT title, callsign, RadioName, allDay, UNIX_TIMESTAMP() as a, UNIX_TIMESTAMP(start_event) as b, UNIX_TIMESTAMP(end_event) as c, start_event as y, UNIX_TIMESTAMP(end_event) as p,  UNIX_TIMESTAMP(start_event) - UNIX_TIMESTAMP()  as z, UNIX_TIMESTAMP(end_event) - UNIX_TIMESTAMP() as q from Events WHERE start_event< NOW() AND RadioName='$tN' ORDER BY start_event DESC;";
  $result1 = $con->query($sQuery);
  if ($row = $result1->fetch_assoc()) {
    $datap = $row["a"];  
    $datap1 = $row["b"]; 
    $datap2 = $row["c"]; 
    $datapx = $row["q"]; 
    $datapy = $row["y"]; 
    $datapz = $row["z"]; 
    $datapp = $row["p"]; 
  } else {
    $datap = 0;
    $datap1 = 0;
    $datap2 = 0;
    $datapx = 0;
    $datapy = 0;
    $datapz = 0;
    $datapp = 0;
    //      exit();
  }
// echo 'px ' . intval($datapz) . "<br>";
  if (!is_null($row)) {
    if ($row["allDay"] == 1){
      $dif=intval(($datap - intval($datap1))/3600) . "<br>";
      if ($dif < 24){
      $tEcho3 =
        "<br>" . $tEcho3 .
        "<div style='color: red'>" .
        $tN .
        " is in use all day " .
        " by " .
        $row["callsign"] .
        "</div>";
      }elseif (intval($datap2)<0){
//        $tEcho3="<br>$tN is available";
      }else{
//        $tEcho3="<br>$tN is available";
      }
      
      if ($datapz != 0) {
        $tD = intval(abs($datapz / 60));
        $tCall = $row["callsign"];
        if ($datapx > 0) {
          //      echo "end timex: " . intval($tD / 60) . "\n";
          $tD = abs(intval($datapx / 60));
          //      if ($tD < 60) {
          $tEcho3 =
            $tEcho3 .
            "<div style='color: red'>" .
            $tN .
            " in use for " .
            $tD .
            " mins by " .
            $tCall .
            "</div>";
          //      } else {
          //        $tEcho3 = intval($tD / 60) . " hours ago" . "<br>\n";
          //      }
        } else {
          if ($tD > 60) {
            $sQuery = "SELECT title, callsign, RadioName, UNIX_TIMESTAMP() as a, UNIX_TIMESTAMP(start_event) as b, UNIX_TIMESTAMP(end_event) as c, start_event as y, UNIX_TIMESTAMP(end_event) as p,  UNIX_TIMESTAMP(start_event) - UNIX_TIMESTAMP()  as z, UNIX_TIMESTAMP(end_event) - UNIX_TIMESTAMP() as q from Events WHERE start_event> NOW() AND RadioName='$tN' ORDER BY start_event DESC;";
            $resultB = $con->query($sQuery);
            if ($row = $resultB->fetch_assoc()) {
              $datapz = $row["z"];
            }
//            echo $datapz;
            if ($datapz > 0) {
/*              $tEcho3 =
                $tEcho3 .
                "<br>" .
                $tN .
                " is available " .
                intval($datapz / 60) .
                " mins";
*/            }

            //        echo $tEcho3 . "\n";
          } else {
            $tEcho3 =
              $tEcho3 .
              "<div style='color: red'>" .
              $tN .
              " in use for " .
              $tD .
              " mins by " .
              $row["callsign"] .
              "</div>";
          }
        }
      }
    }
  }
}

$sQuery =
  "SELECT callsign, RadioName, title, allDay, UNIX_TIMESTAMP() as a, UNIX_TIMESTAMP(start_event)as b, UNIX_TIMESTAMP(end_event) as c from Events";
$con = new mysqli(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}
$result4 = $con->query($sQuery);
$tEcho4 = "";
$row1="";
while($row1 = $result4->fetch_assoc()){
  if ($row1){
    $dif=intval($row1["b"] - $row1["a"])/60;
    $difend=intval($row1["c"] - $row1["a"])/60;
    $aD=$row1["allDay"];
    $callsign=$row1["callsign"];
    $rname=$row1["RadioName"];
    if ($aD==1){
    }elseif ($dif < 0 && $difend < 0 && $aD==0){
        $tEcho4= $tEcho4 . "<div style='color: white'>$rname is available</div>";
    }elseif ($dif < 0 && $difend>0){
        $tEcho4 =$tEcho4 . "<div style='color: red'>" .
          $rname ." is in use by $callsign for " . intval($difend) . ' mins</div>';
    }elseif (strlen($tEcho4)>0){
      if (intval($dif)>120){
        $dif=intval($dif/60);
        if ($dif< 48){
          $tEcho4 = $tEcho4 . "<div style='color: yellow'>Upcoming reservation for $callsign with $rname in " . $dif . " hours</div>";
        };
      }else{
        $tEcho4 = $tEcho4 . "<div style='color: yellow'>Upcoming reservation for $callsign with $rname in " . intval($dif) . " mins</div>";
      }
    }else{
      if (intval($dif)>120){
        $dif=intval($dif/60);
      }
      $tEcho4 = $tEcho4 . "<div style='color: yellow'>Upcoming reservation for $callsign with $rname in " . intval($dif) . " hours</div>";
    }
  }else{
    $tEcho4=$tEcho4 . "<div style='color: white'>$rname is available</div>";
  }
}


echo $tEcho1;
echo $tEcho2;
echo $tEcho3 ;
echo $tEcho4;
?>
