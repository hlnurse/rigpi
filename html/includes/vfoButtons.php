<?php
$vfo = "<div class='row'>
		<div class='col-6 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' accesskey='b' id='connect' type='button'>
				<i class='fas fa-play fa-fw' id='playOK' style='color:green'></i>
				<i class='fas fa-play fa-fw d-none' id='play' style='color:red'></i>
				<i class='fas fa-sync fa-fw d-none' id='spinner'></i>
					<u>C</u>onnect Radio
			</button>
		</div>
		<div class='col-6 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' id='disconnect' type='button'>
				<i class='fas fa-stop fa-fw'></i>
					<u>D</u>isconnect Radio
			</button>
		</div>
	</div>
	<div class='row'>
		<div class='col-2 btn-padding'>
			<button class='btn btn-color  btn-primary btn-sm btn-block' title='Copy Main VFO to Sub VFO in Split Mode' id='A2BButton' type='button'>
				A>B
			</button>
		</div>
		<div class='col-2 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' title='Copy Main VFO to Memory in Split Mode'id='A2MButton' type='button'>
				A>M
			</button>
		</div>
		<div class='col-2 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' title='Copy Memory to Main VFO in Split Mode'id='M2AButton' type='button'>
				M>A
			</button>
		</div>
		<div class='col-2 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' title='Swap Main VFO with Sub VFO in Split Mode'id='ABButton' type='button'>
				A<>B
			</button>
		</div>
		<div class='col-4 btn-padding'>
			<button class='btn btn-color btn-sm btn-block btn-toggle btn-outline' title='Enter Split Mode'id='SplitaButton' type='button'>
				<i class='fas fa-columns'></i>
				SPL<u>I</u>T
			</button>
		</div>
	</div>
	<div class='row'>
	</div>";
echo $vfo;
?>
