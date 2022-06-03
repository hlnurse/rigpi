<?php
/**
 * @author Howard Nurse, W6HN
 *
 * This is login processor
 *
 * It must live in the classes folder
 */

require_once "/var/www/html/classes/User.php";
class Membership
{
  function check_user($un, $pwd)
  {
    $user = new User();
    $tUserName = $un;
    $ensure_credentials = true;
    $pass = "";
    if (strlen($pwd) > 0) {
      $pass = md5($pwd);
    }
    require_once "/var/www/html/classes/MysqliDb.php";
    require "/var/www/html/programs/sqldata.php";
    $db = new MysqliDb(
      "localhost",
      $sql_radio_username,
      $sql_radio_password,
      $sql_radio_database
    );
    $db->where("Username", $tUserName);
    $row = $db->getOne("Users");
    $level = "10";
    if ($row) {
      $level = $row["Access_Level"];
    }
    $last = filemtime("/var/www/html/my/rc_start.txt");
    $elapsed = time() - $last;
    if ($elapsed < 10) {
      while ($elapsed < 10) {
        sleep(10);
        $last = filemtime("/var/www/html/my/rc_start.txt");
        $elapsed = time() - $last;
      }
    }
    $ensure_credentials = $user->validate_user($un, $pass, $db);
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
      //check ip from share internet
      $ip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
      //to check ip is pass from proxy
      $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
      $ip = $_SERVER["REMOTE_ADDR"];
    }
    if ($ensure_credentials) {
      $db->where("Username", $un);
      $row = $db->getOne("Users");
      $level = "10";
      if ($row) {
        $level = $row["Access_Level"];
      }
      $call = $row["MyCall"];
      $db->where("Username", $un);
      $data = [
        "LastVisit" => time(),
        "Active" => "1",
      ];
      $db->update("Users", $data);
      $data = [
        "Callsign" => $call,
        "Username" => $un,
        "CurrentIP" => $ip,
        "TimeOn" => time(),
      ];
      $db->insert("LoggedIn", $data);
      $_SESSION["firstUse"] = 1;
      error_log(
        "<W>" .
          date("Y-m-d H:i:s", time()) .
          " " .
          $ip .
          " OK username: " .
          $un .
          " password: " .
          $pass .
          "\r\n",
        3,
        "/var/log/rigpi-access.log"
      );
      if ($call == "ADMIN") {
        header("Location: wizardUser.php?id=1&what=edit&c=$call&x=" . $un);
      } elseif ($level > 9) {
        header("Location: ptt_only.php?c=$call&x=" . $un); //strtolower $un
      } else {
        header("Location: index.php?c=$call&x=" . $un); //strtolower $un
      }
    } else {
      error_log(
        "<W>" .
          date("Y-m-d H:i:s", time()) .
          " " .
          $ip .
          " Invalid login as " .
          $un .
          " from " .
          $ip .
          "\r\n",
        3,
        "/var/log/rigpi-access.log"
      );
      return "NG";
    }
  }

  function log_User_Out($un)
  {
    require_once "/var/www/html/classes/MysqliDb.php";
    require "/var/www/html/programs/sqldata.php";
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
      //check ip from share internet
      $ip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
      //to check ip is pass from proxy
      $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
      $ip = $_SERVER["REMOTE_ADDR"];
    }
    $db = new MysqliDb(
      "localhost",
      $sql_radio_username,
      $sql_radio_password,
      $sql_radio_database
    );
    $data = [
      "MainIn" => "OFF",
    ];
    $db->update("RadioInterface", $data);
    $db->where("Username", $un);
    $data = [
      "Active" => "0",
    ];
    $db->update("Users", $data);

    $db->where("CurrentIP", $ip);
    $db->where("Username", $un);
    $db->delete("LoggedIn");
  }

  function PowerDown_User_Out($un)
  {
    require "/var/www/html/programs/shutdownFunc.php";
    require_once "/var/www/html/classes/MysqliDb.php";
    require "/var/www/html/programs/sqldata.php";
    $db = new MysqliDb(
      "localhost",
      $sql_radio_username,
      $sql_radio_password,
      $sql_radio_database
    );
    $data = [
      "MainIn" => "OFF",
    ];
    $db->update("RadioInterface", $data);
    $db->where("Username", $un);
    $data = [
      "Active" => "0",
    ];
    $db->update("Users", $data);
    $db->where("Username", $un);
    $db->delete("LoggedIn");
    CloseDownPower($un);
  }

  function Reboot_User($un)
  {
    require "/var/www/html/programs/rebootFunc.php";
    require_once "/var/www/html/classes/MysqliDb.php";
    require "/var/www/html/programs/sqldata.php";
    $db = new MysqliDb(
      "localhost",
      $sql_radio_username,
      $sql_radio_password,
      $sql_radio_database
    );
    $data = [
      "MainIn" => "OFF",
    ];
    $db->update("RadioInterface", $data);
    $db->where("Username", $un);
    $data = [
      "Active" => "0",
    ];
    $db->update("Users", $data);
    $db->where("Username", $un);
    $db->delete("LoggedIn");
    RebootServer($un);
  }

  function confirm_Member($un)
  {
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
      //check ip from share internet
      $ip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
      //to check ip is pass from proxy
      $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
      $ip = $_SERVER["REMOTE_ADDR"];
    }
    require_once "/var/www/html/classes/MysqliDb.php";
    require "/var/www/html/programs/sqldata.php";
    require_once "/var/www/html/programs/GetUserFieldFunc.php";
    $db = new MysqliDb(
      "localhost",
      $sql_radio_username,
      $sql_radio_password,
      $sql_radio_database
    );
    $db->where("CurrentIP", $ip);
    $db->where("Username", $un);
    $row1 = $db->getOne("LoggedIn");
    if ($db->count > 0) {
      //the following if clause can be used to require a user to log back in after some period of inactivity
      /*				$pwd=getUserField($un,"Password");
        if ((time()-$to)>600 && strlen($pwd)>0){
          $db->where('Username', $un);
          $db->delete('LoggedIn');
          header("location:login.php");
          return;
        };
*/
      $db->where("Username", $un);
      $data = [
        "TimeOn" => time(),
      ];
      $db->update("LoggedIn", $data);
    } else {
      header("location: login.php");
    }
    return true;
  }
}

?>
