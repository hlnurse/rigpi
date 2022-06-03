<div class="container-fluid">
<div class="row" style="margin-bottom:10px;">
	<div class="col-12 col-sm4 text-center">
		<span class="label label-success text-white" style="cursor: default; margin-top:10px;">Slider Overrides (User: <?php echo $tUserName; ?>)</span>
	</div>
</div>
<hr>
<span class="label label-primary text-center" style="color: white; background-color: #444; width: 100%; margin-bottom: 30px; margin-top: 50px;" >
		Set maximum for each slider.  Set to 0 to disable.
</span>
<table class="center table-smxy" id="sliderTable" >
	<td  title='AF Level Adjust' id ="AF" width=23%>
		<span class="btn-small-color " id="myAF" style="margin-top: 10px; float:center; font-size:15px;">
		AF
		</span>
		<span class="btn-small-color " id="myAFVal" style="margin-top: 10px; float:center; font-size:15px;">
		0
		</span>
		<div class="slidecontainer">
			  <div id="sliderAF" style="background: black;"></div>
		</div>
	</td>
	<td  title='RF Level Adjust'  id ="RF" width=23%>
		<span class="btn-small-color " id="myRF" style="margin-top: 10px; float:center; font-size:15px;">
		RF
		</span>
		<span class="btn-small-color " id="myRFVal" style="margin-top: 10px; float:center; font-size:15px;">
		0
		</span>
		<div class="slidecontainer">
			  <div id="sliderRF" style="background: black;"></div>
		</div>
	</td>
	<td  title='Power Output Adjust' id ="Pwr" width=23%>
		<span class="btn-small-color " id="myOutputPwr" style="margin-top: 10px; float:center; font-size:15px;">
		Pwr
		</span>
		<span class="btn-small-color " id="myOutputPwrVal" style="margin-top: 10px; float:center; font-size:15px;">
		0
		</span>
		<div class="slidecontainer">
			  <div id="sliderPwrOut" style="background: black;"></div>
		</div>
	</td>
	<td  title='Microphone Level Adjust' id ="Mic" width=23%>
		<span class="btn-small-color " id="myMic" style="margin-top: 10px; float:center; font-size:15px;">
		Mic
		</span>
		<span class="btn-small-color " id="myMicVal" style="margin-top: 10px; float:center; font-size:15px;">
		0
		</span>
		<div class="slidecontainer">
			  <div id="sliderMic" style="background: black;"></div>
		</div>
	</td>
</table>
</div>
