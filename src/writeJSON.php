<?php
	$json = $_POST['json'];

	if (json_decode($json) != null) { 
		$file = fopen('../config.json','w+');
		fwrite($file, $json);
		fclose($file);
	} else {
		// handle error 
	}
?>