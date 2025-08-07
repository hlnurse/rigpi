<?php

/**
 * @author COMMSOFT, Inc.
 * 
 * This routine returns the specified radio data from the MyRadio table in the rigs database
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
if (!empty($_POST['call'])){
	$call=$_POST['call'];
}else{
	$call='';
}
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
$dRoot='/var/www/html';
require_once($dRoot.'/programs/sqldata.php');
require_once($dRoot.'/classes/MysqliDb.php');	
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if ($call==""){
}elseif (strstr($call,"*")){
	$call=str_replace("*", "%", $call);
	$db->where('Callsign',$call,'LIKE');
}else{
	$db->where('Callsign',$call);
}
$db->pagelimit=25;
if (!strlen($tOrder)==0){
	$db->orderBy($tOrder,$tDir);
}
if (!($tOrder=='Time_Start')){
	$db->orderBy('Time_Start','DESC');
}
$row=$db->paginate('Logbook',$tPage);
$i=0;
$tPages= $db->totalPages;
$tTable="";
$tTable="<tp$tPages>";
while ($i< $db->count){
	$tRow=$row[$i];
	$tChk=$tRow['Sel'];
	$tVal="";
	$tColor='tdo';
	if ($i % 2 == 0) {
		$tColor='tde';
	}
	if ($tChk==1){
		 $tVal="checked";
	}
    $tTable=$tTable . "<tr class='$tColor'>".
	    "<td><input class='checkbox' style='vertical-align:middle' type='checkbox' name='Sel' " . $tVal . "></td>" .
	    "<td class='$tColor'>" . $tRow["Time_Start_Plain"]. "</td>".
	    "<td class='$tColor'>" . $tRow["Callsign"] ."</td>".
	    "<td class='$tColor'>" . $tRow["Band"] ."</td>".
	    "<td class='$tColor'>" . $tRow["Mode"] ."</td>".
	    "<td class='$tColor'>" . $tRow["His_RST"] ."</td>".
	    "<td class='$tColor'>" . $tRow["My_RST"] ."</td>".
	    "<td class='$tColor'>" . $tRow["His_Name"] ."</td>".
	    "<td class='$tColor'>" . $tRow["His_Country"] ."</td>".
	    "<td class='$tColor'>" . "<button class='btn btn-success btn-sm logButton' id='e".$tRow['MobileID']."' type='button'>".
			"<i class='fas fa-pencil-alt fa-fw'></i>".
			"Edit".
			"</button>" .
			"<button class='btn btn-danger btn-sm logButton' id='b".$tRow['MobileID']."' type='button'>".
			"<i class='fas fa-trash-alt fa-fw'></i>".
			"Delete".
			"</button></td>" .
			"</tr>";
  $i=$i+1;
}

echo $tTable;
?>