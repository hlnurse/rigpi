<div class="container-fluid">
<div class="row" style="margin-bottom:10px;">
	<div class="col-12 col-sm4 text-center">
		<span class="label label-success text-white" style="cursor: default; margin-top:10px;">Band Filters (User: <?php echo $tUserName; ?>)</span>
	</div>
</div>
<hr>
		<div class="row">
			<span class="label label-primary text-center" style="color: white; background-color: #444; width: 100%; margin-bottom: 30px; margin-top: 50px;" >
				Click to preset a group of bands.
			</span>
		</div>
		<div class="text-center">
			<div class="d-inline-block">
				<div class="btn-toolbar mx-auto">
					<button class="btn btn-primary btn-padding btn-sm bBtnA" style="margin-right:14px; width: 100px;" id="RButton">
						Reset
					</button>
					<button class="btn btn-primary btn-padding btn-sm bBtnA" style="margin-right:4px; width: 100px;" id="AButton">
						HF
						</button>
					<button class="btn btn-primary btn-padding btn-sm bBtnA" style="margin-right:4px; width: 100px;" id="BButton">
					WARC
					</button>
					<button class="btn btn-primary btn-padding btn-sm bBtnA" style="margin-right:4px; width: 100px;" id="CButton">
					LOW HF
					</button>
					<button class="btn btn-primary btn-padding btn-sm bBtnA" style="margin-right:4px; width: 100px;" id="DButton">
					HIGH HF
					</button>
					<button class="btn btn-primary btn-padding btn-sm bBtnA" style="width: 100px;" id="EButton">
					VHF
					</button>
				</div>
			</div>
		</div>
	<span class="label label-primary text-center" style="color: white; background-color: #444; width: 100%; margin-bottom: 30px; margin-top: 50px;" >
			Click bands below you want to enable or disable.
	</span>
	<table class="center table-smxy" id="bandTable" >
		<tr>
			<td  width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm bBtn0" id="160Button">
				160
				</button>
			</td>
			<td width=6%>
				<button	class="btn btn-block btn-success btn-pointer btn-sm bBtn1" id="80Button">
					80
				</button>
			</td>
			<td width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm bBtn2" id="60Button">
					60
				</button>
			</td>
			<td width=6%>
				<button	class="btn btn-block btn-success btn-pointer btn-sm bBtn3" id="40Button">
					40
				</button>
			</td>
			<td width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm bBtn4 " id="30Button">
					30
				</button>
			</td>
			<td  width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm  bBtn5" id="20Button">
					20
				</button>
			</td>
			<td  width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm bBtn6 " id="17Button">
					17
				</button>
			</td>
			<td width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm bBtn7 "	id="15Button">
					15
				</button>
			</td>
			<td width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm bBtn8 " id="12Button" >
					12
				</button>
			</td>
			<td width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm bBtn9 "	id="10Button">
					10
				</button>
			</td>
			<td width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm bBtn10 "  id="6Button">
					6
				</button>
			</td>
			<td width=6%>
				<button class="btn btn-block btn-success btn-pointer btn-sm bBtn11" id="2Button">
					2
				</button>
			</td>
		</tr>
	</table>
</div>
