<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns the modes for a list. Mode list comes from LogStyles database, comma separated.
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
function getLogFields($style,$target){
	$sql_radio_username="";
	$sql_radio_password="";
	$sql_radio_database="";
	$dRoot='/var/www/html';
	require_once($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");	
	$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
	if (!$db) {
		die("Connection failed: " . $db->connect_error);
	}
	if($target!='source'){
		$data="<ul id='sortable2' class='connectedSortable'>\n";
		if (strlen($style)>0){
			$db->orderBy('OrderValue','ASC');
			$db->where('Name',$style);
			$cols = Array ("IDValue");
			$fields = $db->get('LogStyles',null,$cols);
			foreach ($fields as $field){
				$data.="<li data-alias='".$field['IDValue']."' class='ui-state-highlight list-group-item'>" . $field['IDValue'] . "</li>\n";
			}
		}else{
			$fields=$db->getOne("Logbook");
			foreach ($fields as $key=>$value){
				$data.="<li data-alias='$key' class='ui-state-highlight list-group-item'>" . $key . "</li>\n";
			}
		}
	}else{
		$fieldsDestination='';
		$data1='';
		if (strlen($style)>0){
			$db->orderBy('OrderValue','ASC');
			$db->where('Name',$style);
			$cols = Array ("IDValue");
			$fieldsDestination = $db->get('LogStyles',null,$cols);
			foreach ($fieldsDestination as $field){
				$data1.=$field['IDValue'].'+';
			}
			
		}else{
			$fieldsDestination=$db->getOne("Logbook");
			foreach ($fieldsDestination as $key=>$value){
				$data1.=$key.'+';
			}
		}
		if (strlen($style)>0){
			$fields=$db->getOne("Logbook");
			$data="<ul id='sortable1' class='connectedSortable'>\n";
			foreach ($fields as $key=>$value){
				if (strpos($data1, $key)==0) {
					$data.="<li data-alias='$key' class='ui-state-default list-group-item'>" . $key . "</li>\n";
				}
			}
//			echo "$data: " . $data.'\n';
		}else{
			$data="<ul id='sortable1' class='connectedSortable'>\n";
		}
	}
	$data.='</ul>';
	return $data;
}

?>