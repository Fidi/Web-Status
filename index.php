<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link href="http://code.jquery.com/ui/1.10.4/themes/dark-hive/jquery-ui.css" rel="stylesheet" />
	<script src="http://code.jquery.com/jquery-1.11.3.js"></script>
	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    
    <link href="src/style.css" rel="stylesheet" />
    <script src="src/clock.js"></script>
  
    
    <?php 
        header ('Content-type: text/html; charset=utf-8'); 
        require 'src/chart.php';
    ?>
    
    <script type="text/javascript">
		$(document).ready(function() {
			function updateClock() {
				displayAnalogClock("analog_clock");
				displayDigitalClock("digital_clock");	
			}
			
			window.onload = updateClock();
			setInterval(function() {
				updateClock();
			}, 1000);	
			
			$("#settings-triangle").on("click", enterSettingsMenu);
			$("#settings-add").on("click", addBox);	
		}); 
	</script>
	
	<script type="text/javascript">
		
		// this defines the resizing behaviour of each box
		var resObj = {
               grid: [142,142],
			   minHeight: 273,
			   minWidth: 273,
			   maxHeight: 567,
			   maxWidth: 851,
			   start: function (event, ui)
               {
                  	$("body").css("background-image", "repeating-linear-gradient(0deg,transparent,transparent 70px,#444 70px,#444 71px),repeating-linear-gradient(-90deg,transparent,transparent 70px,#444 70px,#444 71px)");
               },
			   resize: function (event, ui)
               {
                  	var json_file;		
					var sender = $(this).attr("id");
						
					// read JSON file and modify it   	
					$.getJSON('config.json', function(response){
						json_file = response;
						json_file[sender].width = ui.size.width; 
						json_file[sender].height = ui.size.height;
						
						var json_str = JSON.stringify(json_file);
					
						$.post("src/writeJSON.php", {json : json_str});  
					});
               },
			   stop: function (event, ui)
               {
					$("body").css("background-image", "");
					var json_file;		
					var sender = $(this).attr("id");
						
					// read JSON file and modify it   	
					$.getJSON('config.json', function(response){
						json_file = response;
						json_file[sender].width = ui.size.width; 
						json_file[sender].height = ui.size.height;
						
						var json_str = JSON.stringify(json_file);
					
						$.post("src/writeJSON.php", {json : json_str});  
					});
               }
            };
		
		// this defines the dragging behaviour of each box	
		var dragObj = {
				grid: [142, 142],
				start: function (event, ui)
               	{
                  	$("body").css("background-image", "repeating-linear-gradient(0deg,transparent,transparent 70px,#444 70px,#444 71px),repeating-linear-gradient(-90deg,transparent,transparent 70px,#444 70px,#444 71px)");
               	},
			   	drag: function( event, ui )
				{
					var json_file;		
					var sender = $(this).attr("id");
						
					// read JSON file and modify it   	
					$.getJSON('config.json', function(response){
						json_file = response;
						json_file[sender].left = ui.position.left; 
						json_file[sender].top = ui.position.top;
						
						var json_str = JSON.stringify(json_file);
					
						$.post("src/writeJSON.php", {json : json_str});  
					});
				},
			   	stop: function (event, ui)
               	{
                  	$("body").css("background-image", "");
					var json_file;		
					var sender = $(this).attr("id");
						
					// read JSON file and modify it   	
					$.getJSON('config.json', function(response){
						json_file = response;
						json_file[sender].left = ui.position.left; 
						json_file[sender].top = ui.position.top;
						
						var json_str = JSON.stringify(json_file);
					
						$.post("src/writeJSON.php", {json : json_str});  
					});
               	}
			};
			
			 
		
        $(function() {
            $( ".box" ).resizable(resObj);
         });
		 
		 $(function() {
			$( ".box" ).draggable(dragObj);
		 });
		 
		 
		 function enterSettingsMenu() {
				$('#settings-menu').animate({
					'margin-right': '0px'
				}, 500);
				//$('#settings').click(leaveSettingsMenu());
				$("#settings-triangle").unbind("click");
				$("#settings-triangle").on("click", leaveSettingsMenu);
		};
		
		function leaveSettingsMenu() {
				$('#settings-menu').animate({
					'margin-right': '-72px'
				}, 500);
				//$('#settings').click(enterSettingsMenu());
				$("#settings-triangle").unbind("click");
				$("#settings-triangle").on("click", enterSettingsMenu);
		};
		
		function addBox() {
			var json_file;

			$.getJSON('config.json', function(response){
				json_file = response;
				var cnt = json_file.count;
				json_file.count = parseInt(cnt) + 1;
				
				var tmp = "box" + cnt;
				var newEntry = {"type":"none","details":"none","top":1,"left":1,"width":273,"height":273}
				json_file[tmp] = newEntry;
				
				var json_str = JSON.stringify(json_file);
			
				$.post("src/writeJSON.php", {json : json_str});  
				
				
				var element = $("<div style='position: absolute; background-color: #333; top: 1px; left: 1px; width: 273px; height: 273px;' class='box' id='box" + cnt + "'> </div>").text('').draggable(dragObj).resizable(resObj);
				$("body").append(element);
			});
		}
		
		
		
	
	</script>
    
	<title>WebStatus</title>

</head>

<body>

	<div id="settings-triangle">
    	<img src="icons/settings.png" id="settings" />
    </div>
    
    <div id="settings-menu">
    	<center>
        	<br />
        	<img src="icons/add.png" style="height: 32px; width: 32px; margin: 20px;" id="settings-add" />
        </center>
    </div>

	<?php
		$json = json_decode(file_get_contents("config.json"));
		for ($x = 0; $x < $json->count; $x++) {
			echo '<div class="box" id="box' . $x . '" style="top: ' . $json->{"box$x"}->top . 'px; left: ' . $json->{"box$x"}->left . 'px; width: ' . $json->{"box$x"}->width . 'px; height: ' . $json->{"box$x"}->height . 'px;">';
			
			switch ($json->{"box$x"}->type) {
				case "time": 	if ($json->{"box$x"}->details == "analog") {
									echo ' 	<div class="analog_clock">
												<canvas id="analog_clock" style="width: 100%; height: 100%;"></canvas>
											</div>';
								} else {
									echo '<div class="digital_clock"> </div>'; 
								}
								break;
								
				case "json": 	$obj = new chart($json->{"box$x"}->details);
								echo $obj->drawChart();
								break;
			}
			
			echo "</div>";
		} 
	?>
</body>
</html>