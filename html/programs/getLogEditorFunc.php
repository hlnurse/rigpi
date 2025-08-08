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
function getLogFields($tMyRadio,$id,$tStyle,$doLookup,$UserName){
	$dRoot='/var/www/html';
	require($dRoot."/programs/sqldata.php");
	require_once($dRoot."/classes/MysqliDb.php");
	include_once($dRoot."/programs/GetCallbookFunc.php");
	include_once($dRoot."/programs/GetUserFieldFunc.php");
	$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
	$tUName=$UserName;
	$tUser=getUserField($tUName,'uID');
	$rowBook=array();
	if ($id>0){
		$db->where ("MobileID", $id);
		$rowLog = $db->getOne ("Logbook");
		if ($rowLog['Logname']=='ALL Logs' || $rowLog['Logname']==''){
			$db->where('MobileID',$id);
			$data = Array('Logname'=>'Main');
			$db->update('Logbook',$data);
			$db->where ("MobileID", $id);
			$rowLog = $db->getOne ("Logbook");
		}
		$call = $rowLog['Callsign'];
		if ($doLookup=='1'){
			getCallbookFunc($call,'1','QRZdata');	
			$db->where('User',$tUser);
			$rowBook=$db->getOne('Callbook');
		}
	}
	$dbStyle = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
	
	$dbStyle->orderBy('OrderValueEdit','ASC');
	$dbStyle->where('LogEditor','1');
	$dbStyle->where('Name',$tStyle);
	$styles=$dbStyle->get('LogStyles');
	$k=0;
	$clockButtonStart="<span class='input-group-btn'>".
				  "<button class='btn btn-sm btn-color' id='startButton' type='button'><i class='fas fa-clock fa-lg'></i></button>".
				   "</span>";
	$clockButtonEnd="<span class='input-group-btn'>".
				  "<button class='btn btn-sm btn-color time' id='xxxxxx' type='button'><i class='fas fa-clock fa-lg'></i></button>".
				   "</span>";
	$output="";
	$size=$dbStyle->count;
	for ($j=0; $j<$size; $j=$j+3){
		$output.="<div class='row'>";
		for ($k=0; $k<3; $k++){
			if ($k+$j<$size){
				$curVal='';
//					echo $styles[$k+$j]['IDValue']."\n";		
				$curID= $styles[$k+$j]['IDValue'];
				$curLabel=$styles[$k+$j]['Label'];
				$curAttr=$styles[$k+$j]['Attribute'];
				if ($doLookup==1){
					$curDefault=$styles[$k+$j]['DefaultValue'];
				}else{
					$curDefault='';
				}
				$curPrompt=$styles[$k+$j]['Prompt'];
				if (isset($rowLog)){
					$curVal=$rowLog[$curID];
				}else{
					$curVal='';
				}
				if ($doLookup==1){
					if (array_key_exists($curID,$rowBook)){
						if (strlen($curVal)==0 && strlen($rowBook[$curID])>0){
							$curVal=$rowBook[$curID];
							$defaultAdded='default-added';
						}
					}
				}
				$defaultAdded='';
				if (strlen($curVal)==0 && strlen($curDefault)>0){
					$curVal=$curDefault;
					$defaultAdded='default-added';
				}
				$curList=$styles[$k+$j]['ListContents'];
				$listHTML='';
				$listItself='';
				if (strlen($curList)>0){	//this allows any field to have a list, including modes
					$modes=explode(",",$curList);
					$i=0;
					for ($i=0;$i<sizeof($modes);$i++){
						$listItself.="<div class='mymode'><li><a class='dropdown-item' id='".$curID."ID' href='#'>" . $modes[$i] . "</a></li></div>\n";
					}
					$listHTML="<span class='input-group-btn'>";
					$listHTML.="<div class='dropdown'>";
					$listHTML.="<button class='btn btn-primary dropdown-toggle' id='$curID' data-size='3' type='button'  title='Select' data-toggle='dropdown'><i class='fas fa-list-alt fa-lg'></i>";
					$listHTML.="</button>";
					$listHTML.="<ul class='dropdown-menu dropdown-menu-right menu-scroll' id='$curID'.'List'>";
					$listHTML.=	$listItself;
					$listHTML.="</ul>";
					$listHTML.="</div>";
					$listHTML.="</span>";
				}elseif ($curID=='Mode') { //but if no default list in modes, then add the entire list
					$cols = Array ("Name");
					$db->orderBy('Name','ASC');
					$modes = $db->get('Modes',null,$cols);
					foreach ($modes as $mode){
						$listItself.="<div class='mymode'><li><a class='dropdown-item' id='".$curID."ID' href='#'>" . $mode['Name'] . "</a></li></div>\n";
					}
					$listHTML="<span class='input-group-btn'>";
					$listHTML.="<div class='dropdown'>";
					$listHTML.="<button class='btn btn-primary dropdown-toggle' id='$curID' data-size='3' type='button'  title='Select' data-toggle='dropdown'><i class='fas fa-list-alt fa-lg'></i>";
					$listHTML.="</button>";
					$listHTML.="<ul class='dropdown-menu menu-scroll' id='$curID'.'List'>";
					$listHTML.=	$listItself;
					$listHTML.="</ul>";
					$listHTML.="</div>";
					$listHTML.="</span>";
				}

				if ($curAttr=="Add Periods" && $curVal>0){
					$curVal=number_format($curVal,0,".",".");
				}
				$noEdit="class='form-control'";
				if ($curAttr=="No Edit"){
					$noEdit="class='form-control noedit b-red' readonly ";
				}
				$output.="<div class='col-sm-4 text-spacer'>";
				$output.="<div class='input-group'>";
				$output.="<div class='input-group-prepend'>";
				$output.="<span class='input-group-text input-sm  $defaultAdded'>$curLabel</span>";
				$output.="</div>";
				$output.="<input type='text' $noEdit  placeholder='$curPrompt' value='$curVal' id='$curID' aria-lable='$curID-label' aria-describedby='$curID-addon'>";
				if($curAttr=='Set Time'){
					if ($curID=='Time_Start_Plain'){
						$output.=$clockButtonStart;
					}else{
						if ($curID=='Time_End_Plain'){
							$clockButtonEnd=str_replace('xxxxxx', 'endButton', $clockButtonEnd);
						}else{
							$clockButtonEnd=str_replace('xxxxxx', $curID, $clockButtonEnd);
						}
						$output.=$clockButtonEnd;
					}
				}
				$output.=$listHTML;
				$output.='</input>';
				$output.="</div>";
				$output.="</div>";
			}
		}
		$output.="</div>";
	}
	return json_encode($output);
}
?>