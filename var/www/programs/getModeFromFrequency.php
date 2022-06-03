<?php

/**
 * @author Howard Nurse, W6HN
 * @copyright 2008
 * 
 * This function returns the mode given a frequency
 * 
 * It must live in the programs folder   
 */
//$frequency = $_GET["f"];

function GetMode($frequency)
{
	$dRoot='/var/www/html';
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");	
		
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	
	$db->where('Frequency',$frequency,'<');
	$db->orderBy('Frequency','DESC');
	$row=$db->getOne('Bandplan');
	if ($row) 
	{
	    $mode = $row["Mode"];
	    return $mode;
	}else{
	    return "UNK";
	}
}
?>