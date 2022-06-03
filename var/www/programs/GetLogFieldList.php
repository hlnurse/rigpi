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
if (!empty($_POST['style'])){
	$style=$_POST['style'];
}else{
	$style='General';
}
if (!empty($_POST['target'])){
	$target=$_POST['target'];
}else{
	$target='source';
}
$sql_radio_username="";
$sql_radio_password="";
$sql_radio_database="";
$dRoot='/var/www/html';
require($dRoot."/programs/sqldata.php");
require_once($dRoot."/classes/MysqliDb.php");	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if (!$db) {
	die("Connection failed: " . $db->connect_error);
}

if($target=='Logbook'){  //destinationLogbook
	$data="<ol id='sortable3' class='connectedSortable vertical'>\n";
	if (strlen($style)>0){
		$db->orderBy('OrderValueLog','ASC');
		$db->where('Name',$style);
		$db->where('Logbook',"1");
		$cols = Array ("IDValue");
		$fields = $db->get('LogStyles',null,$cols);
		$i=0;
		foreach ($fields as $field){
			$tField=$field['IDValue'];
			$data.="<li id='".$tField."' data-alias='".$tField."' class='ui-state-highlight list-group-item noselect'><i class='fas fa-arrows-alt-v' id='move'></i><span class='spacer'></span>" . $tField . "<i class='Logbook fas fa-trash-alt fa-lg tr-alt' id='".$tField."'></i></li>\n";
			$i=$i++;
		}
	}else{
		$fields=$db->getOne("Logbook");
		foreach ($fields as $key=>$value){
			$data.="<li data-alias='$key' class='ui-state-highlight list-group-item noselect'>" . $key . "</li>\n";
		}
	}
}elseif($target=='LogEditor'){  //destinationEditor
	$data="<ol id='sortable2' class='connectedSortable vertical'>\n";
	if (strlen($style)>0){
		$db->orderBy('OrderValueEdit','ASC');
		$db->where('Name',$style);
		$db->where('LogEditor',"1");
		$cols = Array ("IDValue");
		$fields = $db->get('LogStyles',null,$cols);
		$i=0;
		foreach ($fields as $field){
			$tField=$field['IDValue'];
			$data.="<li id='".$tField."' data-alias='".$tField."' class='ui-state-highlight list-group-item noselect'><i class='fas fa-arrows-alt-v' id='move'></i><span class='spacer'></span>" .  $tField . "<i class='LogEditor fas fa-trash-alt fa-lg tr-alt' id='".$tField."'></i></li>\n";
			$i=$i++;
		}
	}else{
		$fields=$db->getOne("Logbook");
		foreach ($fields as $key=>$value){
			$data.="<li data-alias='$key' class='ui-state-highlight list-group-item noselect'>" . $key . "</li>\n";
		}
	}
}else{	//source
	$fieldsSource='';
	$data1='';
	if (strlen($style)>0){
		$cols=Array("Field");
		$fields=$db->get("LogFields",null,$cols);
		if ($db->count>0){
			ksort($fields, SORT_NATURAL | SORT_FLAG_CASE);
			$data="<ol id='sortable1' class='connectedSortable vertical'>\n";
			foreach ($fields as $field){
				$tField=$field['Field'];
				$data.="<li id='".$tField."' data-alias='".$tField."' class='ui-state-default list-group-item noselect'><i class='move fas fa-arrows-alt-h' id='move'></i><span class='spacer'></span>" . $tField . "</li>\n";
			}
			$data.='</ol>';
		}else{
			$data='Please enter 1 QSO in log';
		}
	}else{
		$data="<ol id='sortable1' class='connectedSortable vertical noselect'>\n";
		$data.='</ol>';
	}
}
echo $data;


?>