<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine returns the clusters from the cluster table
 * 
 * It must live in the programs folder   
 */
 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
$dRoot='/var/www/html';
require_once($dRoot.'/programs/sqldata.php');
require_once($dRoot.'/programs/GetUserFieldFunc.php');
require_once($dRoot.'/classes/MysqliDb.php');
	
$tUserName=$_POST['username'];
if (!empty($_POST['filter'])){
	$tFilter=$_POST['filter'];
}else{
	$tFilter='all';
}
$tST=getUserField($tUserName,'MyState');
if (strlen($tST)>2){
	$tST='';
}else if($tFilter=='state'&&strlen($tST)==0){
	echo "<td class='space'>State not set for this Account to use filter 'My US State.'</td></tr>";
	return;
}
$tGrid=getUserField($tUserName,'My_Grid');
if(strlen($tGrid)>1){
	$tGrid=substr($tGrid, 0,2);
}else if($tFilter=='grid'&&strlen($tGrid)==0){
	echo "<td class='space'>Grid not set for this Account to use filter 'Closest to me.'</td></tr>";
	return;
}
$db = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
if ($tFilter=='grid'){
	$db->where("Grid","%".$tGrid."%","like");
}elseif ($tFilter=='state'){
	$db->where("Location","%, ".$tST,"like");
}
$db->where("Port","0",">");
$db->where("NodeCall","%CW Skimmer%", "not like");
$row=$db->get('Clusters');
$i=0;
$tTable="";
while ($i< $db->count){
	$tRow=$row[$i];
	$tID=$tRow['ID'];
    $tTable=$tTable . "<tr class='clickRow' id='$tID'>".
	    "<td>" . $tRow["NodeCall"]. "</td>".
	    "<td>" . $tRow["IP"] ."</td>".
	    "<td>" . $tRow["Port"] ."</td>".
	    "<td>" . $tRow["Location"] ."</td>".
	    "<td>" . $tRow["Notes"] ."</td>".
	    "</tr>";
  $i=$i+1;
}

echo $tTable;
?>