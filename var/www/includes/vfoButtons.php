<?php
$vfo="<div class='row'>
		<div class='col-6 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' id='connect' type='button'>
				<i class='fas fa-play fa-fw d-none' id='playOK' style='color:green'></i>
				<i class='fas fa-play fa-fw' id='play' style='color:red'></i>
				<i class='fas fa-sync fa-fw d-none' id='spinner'></i>
					Connect Radio
			</button>
		</div>
		<div class='col-6 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' id='disconnect' type='button'>
				<i class='fas fa-stop fa-fw'></i>
					Disconnect Radio
			</button>
		</div>
	</div>
	<div class='row'>
		<div class='col-2 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' id='A2BButton' type='button'>
				A>B
			</button>
		</div>
		<div class='col-2 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' id='A2MButton' type='button'>
				A>M
			</button>
		</div>
		<div class='col-2 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' id='M2AButton' type='button'>
				M>A
			</button>
		</div>
		<div class='col-2 btn-padding'>
			<button class='btn btn-color btn-primary btn-sm btn-block' id='ABButton' type='button'>
				A<>B
			</button>
		</div>
		<div class='col-4 btn-padding'>
			<button class='btn btn-color btn-sm btn-block btn-toggle btn-outline' id='SplitaButton' type='button'>
				<i class='fas fa-columns'></i>
				SPLIT
			</button>
		</div>
	</div>
	<div class='row'>
	</div>";	
echo $vfo;		
?>