<?php

/**
 * @author COMMSOFT, Inc.
 * 
 * This routine sets the specified radio data from the MyRadio table in the rigs database
 * 
 * It must live in the programs folder   
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
require_once './sqldata.php';
require_once ('../classes/MysqliDb.php');	

$winkey_mode_register=$_POST['mode'];
$winkey_Speed=$_POST['speed'];
$winkey_Sidetone_Frequency=$_POST['sidetone'];
$winkey_Weight=$_POST['weight'];
$winkey_Leadin_Time=$_POST['leadin'];
$winkey_Tail_Time=$_POST['tail'];
$winkey_Minimum_WPM=$_POST['minimum'];
$winkey_WPM_Range=$_POST['range'];
$winkey_X2_Mode=$_POST['x2mode'];
$winkey_Key_Compensation=$_POST['comp'];
$winkey_Farnsworth_WPM=$_POST['farnsworth'];
$winkey_Paddle_Setpoint=$_POST['paddle'];
$winkey_DitDah_Ratio=$_POST['ditdah'];
$winkey_Pin_Configuration=$_POST['pin'];
$winkey_X1_Mode=$_POST['x1mode'];

$data = Array (
	'WKMode' => $winkey_mode_register
	'WKSpeed' => $winkey_Speed,
	'WKSidetone' => $winkey_Sidetone_Frequency,
	'WKWeight' => $winkey_Weight,
	'WKLeadin' => $winkey_Leadin_Time,
	'WKTail' => $winkey_Tail_Time,
	'WKMinWPM' => $winkey_Minimum_WPM,
	'WKWPMRange' => $winkey_WPM_Range,
	'WKX2Mode' => $winkey_X2_Mode,
	'WKKeyComp' => $winkey_Key_Compensation,
	'WKFarnsworth' => $winkey_Farnsworth_WPM,
	'WKPaddleSet' => $winkey_Paddle_Setpoint,
	'WKDitDahRatio' => $winkey_DitDah_Ratio,
	'WKPinConf' => $winkey_Pin_Configuration,
	'WKX1Mode' => $winkey_X1_Mode
);
$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("Radio", 1);
if ($db->update ('Keyer', $data)){
    return $db->count . ' records were updated';
}else{
    return 'update failed: ' . $db->getLastError();
}
?>