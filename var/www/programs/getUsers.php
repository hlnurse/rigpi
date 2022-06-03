<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns a list, html, of users from the Users table
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require_once($dRoot.'/programs/sqldata.php');
require_once ($dRoot.'/classes/MysqliDb.php');	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
$db->orderBy('uID','ASC');
$row=$db->get('Users');
$i=0;
$tTable="";
while ($i< $db->count){
	$tRow=$row[$i];
	$ts=$tRow["LastVisit"];
	$date = new DateTime("@$ts");
	$date = $date->format('Y-m-d H:i:s').'z';
	$delButton='';
	if ($tRow['uID']!=1){
		$delButton="<button class='btn btn-danger btn-sm logButton' id='b".$tRow['uID']."' type='button'>".
			"<i class='fas fa-trash-alt fa-fw'></i>".
				"Delete".
			"</button></td>";
	}
	$on=$tRow["Active"];
	if ($on==1){
		$on="Y";
	}else{
		$on="";
	}
    $tTable=$tTable . 
    	"<tr>".
	    "<td style='cursor: default'>" . $on . "</td>".
	    "<td style='cursor: default'>" . $tRow["MyCall"]. "</td>".
	    "<td style='cursor: default'>" . $tRow["Access_Level"] ."</td>".
	    "<td style='cursor: default'>" . $tRow["Username"] ."</td>".
	    "<td style='cursor: default'>" . $tRow["FirstName"] ."</td>".
	    "<td style='cursor: default'>" . $tRow["LastName"] ."</td>".
	    "<td style='cursor: default'>" . $tRow["QTH"] ."</td>".
	    "<td style='cursor: default'>" . $date ."</td>".
	    "<td>" . 
	    	"<button class='btn btn-success btn-sm logButton' id='e".$tRow['uID']."' type='button'>".
			"<i class='fas fa-pencil-alt fa-fw'></i>".
				"Edit".
			"</button>" ." ".
			$delButton.
			"</tr>";
  $i=$i+1;
}
echo $tTable;
?>