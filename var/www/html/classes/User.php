<?php
/**
 * @author Howard Nurse, W6HN
 * 
 * This is part of thee login processor
 * 
 * It must live in the classes folder   
 */
	
	class User
	 {
		private $stmt="";
		function __construct(){
		}

		function validate_user($un,$pwd,$db){
			require('/var/www/html/programs/sqldata.php');
			require_once('/var/www/html/classes/MysqliDb.php');
			$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
			$db->where('Username',$un);
			$row=$db->getOne("Users");
			$u=$row['Username'];
			if (strtolower($u)==strtolower($un)){
				if ($row){
					if (strlen($row['Password'])>0 && ($pwd==$row['Password'])){
						return true;
					}elseif (strlen($row['Password'])==0){
						return true;
					}else{
						return false;
					}
	
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
	}

?>