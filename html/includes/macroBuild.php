<?php
for ($i=1;$i<65;$i++){
	
echo "$(document).on('click', '#m$iButton', function() 
                {
                    var tComm=aMCommands[($i-1)];
                    processCommand(tComm);
                });";
                 
                
                
                
                
?>