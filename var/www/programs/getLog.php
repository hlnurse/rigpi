<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns table header and rows with logged Q's
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
require_once ("/var/www/html/programs/sqldata.php");
require_once ("/var/www/html/classes/MysqliDb.php");	

if (!empty($_POST['field'])){
	$field=$_POST['field'];
}else{
	$field='Logname';
}
if (!empty($_POST['value'])){
	$tValue=$_POST['value'];
}else{
	$tValue='ALL Logs';
}
//$tValue="Test";
if (!empty($_POST['call'])){
	$call=$_POST['call'];
}else{
	$call='';
}
$call1=$call;
if (!empty($_POST['page'])){
	$tPage=$_POST['page'];
}else{
	$tPage=1;
}
if (!empty($_POST['order'])){
	$tOrder=$_POST['order'];
}else{
	$tOrder='Callsign';
}
if (!empty($_POST['direction'])){
	$tDir=$_POST['direction'];
}else{
	$tDir='ASC';
}
if (!empty($_POST['style'])){
	$tStyle=$_POST['style'];
}else{
	$tStyle='General';
}
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
$dbStyle = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);

if ($tValue=="ALL Logs"){
}else{
	if (strlen($tValue)>0){
		$db->where($field,$tValue);
	}
}

$call1=$call;
if (strpos($call1,"=")>0){
	$db->where("$call1");
}else{
	if (strlen($call1)==0){
	}elseif (strpos($call1,"*")>0){
		$call1=str_replace("*", "%", $call1);
		$db->where('Callsign',$call1,'LIKE');
	}else{
		$db->where('Callsign',$call1);
	}	
}
$cols=Array("Sel");
$tRowsCount=$db->getValue('Logbook','count(*)');
if ($tValue=="ALL Logs"){
}else{
	if (strlen($tValue)>0){
		$db->where($field,$tValue);
	}
}
$call1=$call;
if (strpos($call1,"=")>0){
	$db->where("$call1");
}else{
	if (strlen($call1)==0){
	}elseif (strpos($call1,"*")>0){
		$call1=str_replace("*", "%", $call1);
		$db->where('Callsign',$call1,'LIKE');
	}else{
		$db->where('Callsign',$call1);
	}	
}

if (!strlen($tOrder)==0){
	$db->orderBy($tOrder,$tDir);
}
if (!($tOrder=='Time_Start')){
	$db->orderBy('Time_Start','DESC');
}
$row=$db->paginate('Logbook',$tPage);
$dbStyle->where('Logbook','1');
$dbStyle->orderBy('OrderValueLog','ASC');
$dbStyle->where('Name',$tStyle);
$styles=$dbStyle->get('LogStyles');

$j=0;
$tPages= $db->totalPages;
$tTable="";
$tTable.="<tp$tPages><tq$tRowsCount>";
$tTable.="<div class='container-fluid'>";
$tTable.="<table class='table table-striped table-sm ' onselectstart='return false' id='logt'>";
$tTable=$tTable."<thead>";
$tTable=$tTable."<tr class='sortable'>";
$thisID='';
while ($j< $dbStyle->count){
	$tRowStyle=$styles[$j];
	if ($tRowStyle['IDValue']=='Time_Start_Plain'){
		$thisID='Time_Start';
	}elseif ($tRowStyle['IDValue']=='Time_End_Plain'){
		$thisID='Time_End';
	}else{
		$thisID=$tRowStyle['IDValue'];
	}
	if ($tRowStyle['IDValue']=='Sel'){
		$tTable=$tTable."<th style='text-align:center; vertical-align:middle'><input id='selAll' name='selAll' type='checkbox'>";
		$tTable=$tTable."<i class='fas fa-sort fa-fw hClk'></i></th>";
	}else{
		$tTable=$tTable."<th class='hClk' style='text-align:center;' id='".$thisID."'>".$tRowStyle['Label'];
		$tTable=$tTable."<i class='fas fa-sort fa-fw hClk select' id='".$thisID."'></i></th>";
	}
	$j=$j+1;
}
$tTable=$tTable."<th class='null' style='text-align:center;'></th>";

$tTable=$tTable."</tr>";
$tTable=$tTable."</thead>";

$i=0;
while ($i< $db->count){
	$tRow=$row[$i];
	$tID=$tRow['MobileID'];
	$tCall=$tRow['Callsign'];
	$tChk=$tRow['Sel'];
	$tVal="";
	if ($tChk==1){
		 $tVal="checked";
	}
	$j=0;
	$tTable=$tTable . "<tr class='clickme' id='$tCall'>";
	while ($j< $dbStyle->count){
		$tRowStyle=$styles[$j];
		if ($tRowStyle['IDValue']=='Sel'){
		    $tTable.="<td><input class='checkbox selCk' style='vertical-align:middle' id='c".$tID."' type='checkbox' name='Sel' " . $tVal . "></td>";
		}else{
			$val='';
			if ($tRowStyle['Attribute']=='Add Periods' && $tRow[$tRowStyle['Field']]>0){
				$val=number_format($tRow[$tRowStyle['Field']],0,".",".");
			}else{
				$val=$tRow[$tRowStyle['Field']];
			};
		    $tTable=$tTable."<td>" . $val. "</td>";

		}
		$j=$j+1;
	}
	
    $tTable=$tTable."<td>" . "<button class='btn btn-success btn-sm logButton' id='e".$tID."' type='button'>".
		"<i class='fas fa-pencil-alt fa-fw'></i>".
		"Edit".
		"</button>" ." ".
		"<button class='btn btn-danger btn-sm logButton' id='b".$tID."' type='button'>".
		"<i class='fas fa-trash-alt fa-fw'></i>".
		"Delete".
		"</button></td>" .
		"</tr>";
  $i=$i+1;
}
$tTable.="</table>";
$tTable.="</div>";

echo $tTable;
?>