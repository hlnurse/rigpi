<?php

/**
 * @author Howard Nurse, W6HN
 * 
 * This routine adds, updates, or removes a style in the styles database from the log designer.
 * 
 * It must live in the programs folder   
 */
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
//	require($_SERVER['DOCUMENT_ROOT']."/programs/sqldata.php");
//	require_once($_SERVER['DOCUMENT_ROOT']."/classes/MysqliDb.php");
//	require_once($_SERVER['DOCUMENT_ROOT']."/programs/GetSettingsFunc.php");
//	require_once($_SERVER['DOCUMENT_ROOT']."/programs/GetStyleListFunc.php");
	require("sqldata.php");
	require_once("../classes/MysqliDb.php");
	require_once("GetSettingsFunc.php");
	require_once("GetStyleListFunc.php");
	$fields=$_POST['fields'];
	$value=$_POST['value'];
	$style=$_POST['style']; //style name to modify
	$operation=$_POST['operation']; //N=ignore, D=delete, SA=saveas, F=field edit
	$orderList=$_POST['orderList']; //either order for log, or order for editor
	$from=$_POST['from']; //either log editor or log
	$for=$_POST['for']; //field to be updated or saveas style

	$db = new MysqliDb("localhost", $sql_radio_username, $sql_radio_password, $sql_radio_database);
	if (!$db){
		die("Connection failed: ".mysqli_connect_error());
	}
	$success="OK";
	$editField='N';
	$delete='N';
	if ($operation=='F'){ //F means field edit, before was checking len of value, but that doesn't allow delete
		$editField='Y';
		$delete='N';
	}
	if ($operation=='N'){
		if (strlen($style)>0){
			if ($editField=='Y'){
				$j=1;
			}else{
				$j=count($fields)-1;	//takes care of bogus elenment at end
			}
			for ($i=0;$i<$j;$i++){
				if ($editField=='Y'){
					$tArray=array($fields=>$value);
				}else{
					$tArray=array($orderList=>$i+1, $from=>'1');
				}
//echo $fields[$i].'++';
 				$db->where("Name",$style);
	 			if ($editField!='Y'){
 					$db->where("IDValue",$fields[$i]);
 				}else{
	 				$db->where("IDValue",$for);
 				}
				$row=$db->getone('LogStyles');
				if ($db->count==0){
					$tArray=array($orderList=>$i+1,'Name'=>$style,'Field'=>$fields[$i], 'IDValue'=>$fields[$i],'Length'=>'L', $from=>'1');
					$db->insert('LogStyles',$tArray);
				}else{
	 				$db->where("Name",$style);
	 				if ($editField!='Y'){
		 				$db->where("IDValue",$fields[$i]);
	 				}else{
		 				$db->where("IDValue",$for);
	 				}
					$success=$db->update('LogStyles',$tArray);
				};
			};
		};
	}elseif ($operation=='SA'){
		if (strlen($style)>0){
			$db->where("Name",$for);
			$k=$db->getValue('LogStyles','count(*)');
			if ($k==0){
				$db->where("Name",$style);
				$j=$db->getValue('LogStyles','count(*)');
				$db->where("Name",$style);
				$rows=$db->get('LogStyles');
				for ($i=0;$i<$j;$i++){
					$row=$rows[$i];
					$row['Name']=$for;
					unset($row['ID']);
					$success=$db->insert('LogStyles',$row);
				};
				echo "Style $for saved.";
			}else{
				echo "Style $for is already used.";
			}
		};
	}elseif ($operation=='F'){
		$db->where("IDValue",$for);
		$db->where("Name",$style);
		$row=$db->getone('LogStyles');
		if ($db->count==0){
			$db->insert('LogStyles',array("Name"=>$style,"IDValue"=>$for,"Field"=>$for,$fields=>$value));
		}else{
			$db->where("IDValue",$for);
			$db->where("Name",$style);
			$db->update('LogStyles',array($fields=>$value));
		}
	}else{
		$db->where("IDValue",$fields);
		$db->where("Name",$style);
		$db->update('LogStyles',array($from=>"0"));
	}
	
	//echo $db->GetLastQuery();
?>