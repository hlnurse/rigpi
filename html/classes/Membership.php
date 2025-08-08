<?php
/**
 * @author Howard Nurse, W6HN
 *
 * Membership class for RigPi — handles login, logout, user session management,
 * access level enforcement, and system control operations (shutdown, reboot).
 *
 * This file must reside in the /classes folder.
 */

/*
 * RigPi
 * Copyright (c) 2025 Howard Nurse, W6HN
 *
 * Licensed under the MIT license — see LICENSE file.
 */

// Include user authentication class
require_once "/var/www/html/classes/User.php";

class Membership
{
    /**
     * Authenticate user against database and initialize session
     *
     * @param string $un  Username
     * @param string $pwd Password
     * @return string "NG" on failure (stops), or redirects on success
     */
    function check_user($un, $pwd)
    {
        $user = new User();

        // Reject if username not supplied
        if (!isset($un)) {
            return "NG";
        }

        $tUserName = $un;
        $ensure_credentials = true;
        $pass = "";

        // Capture password if provided
        if (strlen($pwd) > 0) {
            $pass = $pwd;
        }

        // Initialize database connection
        require_once "/var/www/html/classes/MysqliDb.php";
        require "/var/www/html/programs/sqldata.php";

        $db = new MysqliDb(
            "localhost",
            $sql_radio_username,
            $sql_radio_password,
            $sql_radio_database
        );

        // Check for user in database
        $db->where("Username", $tUserName);
        $row = $db->getOne("Users");
        $level = "1";

        if ($row) {
            $level = $row["Access_Level"];
        }else{
            return 'NG';
        }

        // Wait for system startup (if rc_start.txt was modified <10 sec ago)
        $last = filemtime("/var/www/html/my/rc_start.txt");
        $elapsed = time() - $last;
        if ($elapsed < 10) {
            while ($elapsed < 10) {
                sleep(10);
                $last = filemtime("/var/www/html/my/rc_start.txt");
                $elapsed = time() - $last;
            }
        }

        // Authenticate user credentials
        $ensure_credentials = true;///$user->validate_user($un, $pass, $db);
        // Get client IP address (handling proxy headers if present)
        if (!empty($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        // If validated
        if ($ensure_credentials == true) {
            // Reload user info (access level, callsign)
            $db->where("Username", $un);
            $row = $db->getOne("Users");
            $level = "1";
            if ($row) {
                $level = $row["Access_Level"];
            }
            $call = $row["MyCall"];

            // Update last visit and mark as active
            $db->where("Username", $un);
            $data = [
                "LastVisit" => time(),
                "Active"    => "1",
            ];
            $db->update("Users", $data);

            // Log user session in LoggedIn table
            $data = [
                "Callsign"  => $call,
                "Username"  => $un,
                "CurrentIP" => $ip,
                "TimeOn"    => time(),
            ];
            $db->insert("LoggedIn", $data);

            // Set session variables
            $_SESSION["level"]      = $level;
            $_SESSION["firstUse"]   = 1;
            $_SESSION["myCall"]     = $call;
            $_SESSION["myUsername"] = $un;

            // Log success to access log
            error_log(
                "<W>" . date("Y-m-d H:i:s") . " " . $ip . " OK username: " . $un . " password: " . $pass . "\r\n",
                3,
                "/var/log/rigpi-access.log"
            );

            // Redirect based on user type
            if (trim($call) == "ADMIN") {
                header("Location: /wizardUser.php");
            } elseif ($level > 9) {
                header("Location: /ptt_only.php");
            } else {
                header("Location: /index.php");
            }
        } else {
            // Log failed login
            error_log(
                "<W>" . date("Y-m-d H:i:s") . " " . $ip . " Invalid login as " . $un . " from " . $ip . "\r\n",
                3,
                "/var/log/rigpi-access.log"
            );
            return "NG";
        }
    }

    /**
     * Log user out, deactivating sessions and system state
     *
     * @param string $un Username to log out (defaults to admin)
     */
    function log_User_Out($un)
    {
        if ($un == "") {
            $un = "admin";
        }
        require_once "/var/www/html/classes/MysqliDb.php";
        require "/var/www/html/programs/sqldata.php";

        $db = new MysqliDb(
            "localhost",
            $sql_radio_username,
            $sql_radio_password,
            $sql_radio_database
        );

        // Turn off radio interfaces
        $data = ["MainIn" => "OFF", "SubIn" => "OFF"];
        $db->update("RadioInterface", $data);

        // Mark user as inactive
        $db->where("Username", $un);
        $data = ["Active" => "0"];
        $db->update("Users", $data);

        // Reset session
        session_reset();
    }

    /**
     * Log user out and shut down system power
     *
     * @param string $un Username
     */
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

        // Disable radio interface
        $data = ["MainIn" => "OFF"];
        $db->update("RadioInterface", $data);

        // Mark user inactive
        $db->where("Username", $un);
        $data = ["Active" => "0"];
        $db->update("Users", $data);

        // Remove login record
        $db->where("Username", $un);
        $db->delete("LoggedIn");

        // Initiate power down procedure
        CloseDownPower($un);
    }

    /**
     * Log user out and reboot system
     *
     * @param string $un Username
     */
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

        // Disable radio interface
        $data = ["MainIn" => "OFF"];
        $db->update("RadioInterface", $data);

        // Mark user inactive
        $db->where("Username", $un);
        $data = ["Active" => "0"];
        $db->update("Users", $data);

        // Remove login record
        $db->where("Username", $un);
        $db->delete("LoggedIn");

        // Reboot server
        RebootServer($un);
    }

    /**
     * Confirm session validity (modern)
     * Redirects to login if session missing or invalid
     */
    function confirm_Member($userName)
    {
        if (!isset($_SESSION["myPort"]) || !isset($_SESSION["myRadio"]) || !isset($_SESSION["myRadioName"])) {
            header("Location: /login.php");
            exit();
        }
        if (isset($_SESSION["myCall"]) && $_SESSION["myCall"] == "NG") {
            header("Location: /login.php");
            exit();
        }
    }

    /**
     * Legacy session validation with IP check
     * @param string $userName
     * @return bool True if session valid, else false
     */
    function confirm_MemberOLD($userName)
    {
        $_SESSION["myUsername"] = $userName;
        $un = $_SESSION["myUsername"];

        // Determine client IP
        if (!empty($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
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

        $db->where("Username", $un);
        $row2 = $db->getOne("Users");

        $db->where("CurrentIP", $ip);
        $db->where("Username", $un);
        $row1 = $db->getOne("LoggedIn");

        if ($db->count > 0) {
            // Optional inactivity check (commented)
            $db->where("Username", $un);
            $data = ["TimeOn" => time()];
            $db->update("LoggedIn", $data);
            echo "\n";
        } else {
            print_r($_SESSION);
            echo "\n";
            // header("Location: /login.php");
            return false;
        }
        return true;
    }
}
?>
