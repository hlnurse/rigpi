<!--
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.ƒ
	
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
	
		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <https://www.gnu.org/licenses/>.
	-->
<?php
$dRoot = "/var/www/html";//$GLOBALS["htmlPath"];
$tMyRadio=1;
$which=1;
require_once $dRoot . "/classes/MysqliDb.php";
require $dRoot . "/programs/sqldata.php";
require $dRoot . "/programs/GetInterfaceFunc.php";
$f=getRadioData($tMyRadio,"Macros".$which,'RadioInterface');
	$tMacros=$f;//.replace(/\+/g,'%20'));
	$aMacros=explode("~",$tMacros);
	$aMCommands=[];
	for ($i = 0; $i < 32; $i++) {
		$mID='m'. $i . 'Button';
		$tLabel = $aMacros[$i];
		$aLabel=explode("|",$tLabel);
		if (strpos($tLabel,"+")>0){
			$btnLatchColor=substr($tLabel[1],strpos("+",$tLabel[1]))+1;
		}else{
			$btnLatchColor="btn-info";
		}
		$btn =document.getElementById($mID);
		echo $btn;
		$btn=$tLabel[0];
		$arlbtn=$latchBtn[i];
		if ($arlbtn==null || $arlbtn==""){
			$arlbtn = "?";
		};
		echo $arlbtn;
/*		if (arlbtn=="?"){
		$(btn).removeClass(btnLatchColor);
		$(btn).addClass("btn-color");
		}else{
			$(btn).removeClass("btn-color");
			$(btn).addClass(btnLatchColor);
		}
		if (tLabel[0]=="BANK"){
			mBtn=btn;
			mBtn.innerHTML="BANK "+mBank;
		}
		if (tLabel[0].substring(0,6)=="ROTATE" && tLabel[0].indexOf("STOP")==-1){
			tRotateButton=btn;
			tRotateButtonLabel=tLabel[0];
			mBtn=btn;
			mBtn.innerHTML=tRotateButtonLabel + " (" + tCurBeam+")";
		}
		aMCommands.push(tLabel[1]);
*/	}

?>