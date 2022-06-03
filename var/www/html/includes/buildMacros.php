<?php
for ($i = 0; $i < 32; $i++) {
  $t = $i;
  echo "\n$(document).on('click', '#m" .
    $i .
    "Button', function() 
	{
		var tComm=aMCommands[$t];
		processCommand(tComm,'#m" .
    $i .
    "Button');
	});\n";
}
?>
