<?php
require_once "/var/www/html/classes/MysqliDb.php";
require "/var/www/html/programs/sqldata.php";
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Username", $tUserName);
$row = $db->getOne("Users");
$level = "10";
if ($row) {
  $level = $row["Access_Level"];
}
?>	
<script>
	$('.nav-item > li').on('click', function(e) {
		$('.nav-item > li').removeClass('active');
		$(this).addClass('active');
	});   
</script>


<nav class="navbar navbar-expand-lg navbar-light nav-pills sticky-top id="myTabs" style="width:101%; background-color: #e3f2fd;">
	<div class="container-fluid">
	<div class="padding2">
	<a target="_blank" rel="noopener noreferrer" id="rigpi" href="https://www.rigpi.com" title="Go to https://www.rigpi.com">
		<img src="./Images/RigPiW.png"\ alt="RigPi" title="Go to https://www.rigpi.com" style="width:30px;height:30px;">
	</a>	
	</div>
	<a target="_blank" rel="noopener noreferrer" class="navbar-brand" href="https://www.rigpi.com" title="Go to https://www.rigpi.com" style="color:red;">RigPi</a>
	
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse navbar-toggleable-lg" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
<li class="nav-item">
	<a class="nav-link" id="tunerB" href="./index.php"><u>T</u>UNER<span class="sr-only">(current)</span></a>
</li>
			<li class="nav-item">
	  <?php if ($level < 4) {
	 echo "<a class='nav-link' id='keyer' href='/keyer.php'><u>K</u>EYER<span class='sr-only'></span></h2></a>";
   } ?>
			</li>
<li class="nav-item">
	<a class="nav-link" id='log' href="./log.php"><u>L</u>OG<span class="sr-only">(current)</span></a>
</li>

<li class="nav-item">
	<a class="nav-link" id='spots' href="./spots.php"><u>S</u>POTS<span class="sr-only">(current)</span></a>
</li>
<li class="nav-item">
	<a class="nav-link active1" id="calendar_main" href="./calendar_main.php">C<u>A</u>LENDAR<span class="sr-only">(current)</span></a>
</li>
<li class="nav-item">
	<a class="nav-link" id='web' href="./web.php"><u>W</u>EB<span class="sr-only">(current)</span></a>
</li>
		<li class="nav-item dropdown">
				<?php if ($level < 3) {
	  echo "<a class='nav-link dropdown-toggle' data-toggle='dropdown' id='Preview' href='#' role='button' aria-haspopup='true' aria-expanded='false'>S<u>E</u>TTINGS</a>";
	} ?>
		<ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
				<?php if ($level == 1) {
	  echo "<li><a class='dropdown-item' href='./users.php'>Accounts</a></li>";
	} ?>

	<li><a class="dropdown-item" href="./settings.php">Basic Radio</a></li>
	<li><a class="dropdown-item" href="./settingsAdvanced.php">Advanced Radio</a></li>
	<li><a class="dropdown-item" href="./rotorSettings.php">Rotor</a></li>
	<li><a class="dropdown-item" href="./keyerSettings.php">Keyer</a></li>
	<li><a class="dropdown-item" href="./logDesigner.php">Log Designer</a></li>
	<li><a class="dropdown-item" href="./spotsSettings.php">Spots</a></li>
	<li><a class="dropdown-item" href="./bandFilter.php">Band Filter</a></li>
	<li><a class="dropdown-item" href="./modeFilter.php">Mode Filter</a></li>
	<li><a class="dropdown-item" href="./calendar.php">Scheduler</a></li>
	<li><a class="dropdown-item" href="./sliderOverride.php">Slider Overrides</a></li>
	<li><a class="dropdown-item" href="./macros.php">Macros</a></li>
	<?php if ($level == 1) {
   echo "<li><a class='dropdown-item' href='./system.php'>System</a></li>";
 } ?>
		</ul>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" data-toggle="dropdown" id="help" href="#" role="button" aria-haspopup="true" aria-expanded="false"><u>H</u>ELP</a>	
				<div class="dropdown-menu nav-item" aria-labelledby="Preview">
				<a class="dropdown-item" href="./help.php" target="_blank">RigPi Help</a>
				<a class="dropdown-item" href="https://rigpi.groups.io" target="_blank">RigPi Forum</a>
				<a class="dropdown-item" href="https://rigpi.com" target="_blank">RigPi Home Page</a>
				<a class="dropdown-item" href="https://mfjenterprises.com" target="_blank">MFJ Home Page</a>
				<a class="dropdown-item" href="./license.php">RSS License</a>
				<a class="dropdown-item" href="./acknowledgments.php"Legal Acknowledgments</a>
				<a class="dropdown-item" href="./about.php">About</a>
			</li>
		</ul>
							  <form class="form-inline my-2 my-lg-0">
			<input class="form-control mr-sm-2" style="width:100px;" onfocus="this.select();" data-index ="1" id="searchText" type="text"  title="Show info for this call" placeholder="Search for Call" aria-label="SEARCH">
			<button class="btn btn-outline-success my-2 my-sm-0" id="searchButton"  data-index="2" title="Show call info" type="button">
				<i class='fas fa-search fa-fw fa-lg'></i>
			</button>
			&nbsp;
			<button class="btn btn-outline-normal my-2 my-sm-0" name='logout' title="Log Out from RigPi" id="logoutButton" type="button">
				<i class='fas fa-sign-out-alt fa-fw fa-lg'></i>
			</button>
		</form>
	</div>
	</div>
</nav>
<script>
function showSettings(){
		var set=document.getElementById('Preview');
		set.click();
	}
	
	function showHelp(){
		var set=document.getElementById('help');
		set.click();
	}
	
	function showTuner(){
		var set=document.getElementById('tunerB');
		set.click();
	}
	
	function showKeyer(){
		var set=document.getElementById('keyer');
		set.click();
	}
	
	function showLog(){
		var set=document.getElementById('log');
		set.click();
	}
	
	function showWeb(){
		var set=document.getElementById('web');
		set.click();
	}

	function showCalendar(){
		var set=document.getElementById('calendar_main');
		set.click();
	}
	
	function showSpots(){
		var set=document.getElementById('spots');
		set.click();
	}
</script>