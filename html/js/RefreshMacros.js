var tMyRadio=1;
var which=1;
var tAccessLevel=1;
$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'Macros'+which}, function(response)
{
	var tMacros=decodeURIComponent((response+'').replace(/\+/g,'%20'));
	aMacros=tMacros.split('~');
	var mBtn;
	aMCommands=[];
	for (i = 0; i < 32; i++) {
		var mID='m'+i+'Button';
		var tLabel = aMacros[i];
		tLabel=tLabel.split('|');
		if (tLabel[1].indexOf("+")>0){
			btnLatchColor=tLabel[1].substr(tLabel[1].indexOf("+")+1);
		}else{
			btnLatchColor="btn-info";
		}
		var btn =document.getElementById(mID);
		btn.innerHTML=tLabel[0];
		var arlbtn=latchBtn[i];
		if (arlbtn==null || arlbtn==""){
			arlbtn="?";
		}
		if (arlbtn=="?"){
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
		aMCommands.push(tLabel[1]);
	}
})