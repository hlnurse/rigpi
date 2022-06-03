<?php
    header('Content-Type: text/event-stream');
	header('Cache-Control: no-cache');
	function sendMsg($id, $msg){
		echo "id: $id\r\n";
		echo "data: $msg\r\n\r\n";
		flush();
	}
	

?>