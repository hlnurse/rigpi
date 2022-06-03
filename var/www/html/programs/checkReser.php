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
$tEcho3 = "";
$tEcho2 = "";
$tEcho1 = "";
$sQuery = $sQuery =
  "SELECT DISTINCT RadioName FROM MySettings WHERE Model != 'Net rigctl' AND Model != 'Dummy'";

$result = $con->query($sQuery);
$i = 0;
//look for connected physical radios
while ($row = mysqli_fetch_array($result)) {
  $tN = $row["RadioName"];
  $tEcho1 = $tEcho1 . $tN . " is found<br>";
  $j = $i;
  $sQuery = "SELECT title, callsign, RadioName, allDay, UNIX_TIMESTAMP() as a, UNIX_TIMESTAMP(start_event) as b, UNIX_TIMESTAMP(end_event) as c, start_event as y, UNIX_TIMESTAMP(end_event) as p,  UNIX_TIMESTAMP(start_event) - UNIX_TIMESTAMP()  as z, UNIX_TIMESTAMP(end_event) - UNIX_TIMESTAMP() as q from Events WHERE start_event< NOW() AND RadioName='$tN' ORDER BY start_event DESC;";
  //    echo $sQuery;
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
  if ($row["allDay"] == true) {
    $tEcho3 =
      $tEcho3 .
      "<div style='color: red'>" .
      $tN .
      " in use all day " .
      " by " .
      $row["callsign"] .
      "</div>";
  } else {
    if ($datapz != 0) {
      $tD = intval(abs($datapz / 60));
      //      echo $tD;
      //    echo "start times: " . $tD / 60 . "" . $un . "<br>";
      //$tEcho3 = "";
      $tCall = $row["callsign"];
      //      echo "datapx: " . $datapx;

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
          if ($datapz > 0) {
            $tEcho3 =
              $tEcho3 .
              "<br>" .
              $tN .
              " is available " .
              intval($datapz / 60) .
              " mins";
          } else {
            $tEcho3 = $tEcho3 . "<br>" . $tN . " is available";
          }

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
    //  }
    //  $tEcho1 = ""; //"Previous reservation: " . date("Y-m-d H:m", $datap1) . "\n";
    /*   } else {
      if ($tD < 60) {
        $tEcho3 =
          "<div style='color: red'>" .
          $tD .
          " mins ago (radio is in use by " .
          $row["title"] .
          ")" .
          "</div>\n";
      } else {
        $tEcho3 = $tD / 60 . " hours ago" . "<br>\n";
      }
      $tEcho1 = "Previous reservation: " . date("Y-m-d H:m", $datap1) . "\n";
*/

    //}
    $sQuery =
      "SELECT title, callsign, allDay, RadioName, UNIX_TIMESTAMP() as a, UNIX_TIMESTAMP(start_event) as b, start_event as y, UNIX_TIMESTAMP(start_event) - UNIX_TIMESTAMP()  as z, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(start_event) as x from Events WHERE RadioName='" .
      $tN .
      "' AND start_event> NOW() ORDER BY start_event ASC;";
    $con = new mysqli(
      "localhost",
      $sql_radio_username,
      $sql_radio_password,
      $sql_radio_database
    );
    if ($con->connect_error) {
      die("Connection failed: " . $con->connect_error);
    }
    $result2 = $con->query($sQuery);
    if ($row = $result2->fetch_assoc()) {
      $datan = $row["a"];
      $datan1 = $row["b"];
      $datanx = $row["x"];
      $datany = $row["y"];
      $datanz = $row["z"];
    } else {
      $datan = 0;
      $datan1 = 0;
      $datanx = 0;
      $datany = 0;
      $datanz = 0;
    }
    if ($datany != 0) {
      if ($row["allDay"] == true) {
        $tEcho2 =
          $tEcho2 .
          $tN .
          " reserved all day on " .
          date("d M Y", $datan1) .
          " by " .
          $row["callsign"] .
          "<br>";
      } else {
        $tEcho2 =
          $tEcho2 .
          $tN .
          " reserved " .
          date("d M Y H:i", $datan1) .
          " by " .
          $row["callsign"] .
          "<br>";
      }
    }
  }
}
echo $tEcho1;
echo $tEcho2 . "<br>";
echo $tEcho3;
//print_r($aRadios);
//echo $tEcho1 . "<br>" . $tEcho3 . $tEcho2;
//echo $tEcho3 . "<br>";

/*echo $data -
    $data1 .
    "  " .
    $data .
    "  " .
    $data1 .
    " " .
    date("Y-m-d", $data1) .
    " " .
    intval($datax / (3600 * 24)) .
    " " .
    $datax .
    "\n";
  */
?>
