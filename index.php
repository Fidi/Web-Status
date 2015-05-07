<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link href="http://code.jquery.com/ui/1.10.4/themes/dark-hive/jquery-ui.css" rel="stylesheet">
	<script src="http://code.jquery.com/jquery-1.11.3.js"></script>
	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  
    
    <?php 
        header ('Content-type: text/html; charset=utf-8'); 
        require 'src/chart.php';
    ?>
    
    <script type="text/javascript">
		$(document).ready(function() {
			//custom wrote clock
			function updateClock() {
				var currentTime = new Date();
				var currentHours = currentTime.getHours();
				var currentMinutes = currentTime.getMinutes();
				var currentSeconds = currentTime.getSeconds();
				currentHours = (currentHours < 10 ? "0" : "") + currentHours;
				currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
				currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
				$(".clock_in_checkout").html(currentHours + ":" + currentMinutes);
			}
			
			window.onload = updateClock();
			setInterval(function() {
				updateClock();
			}, 1000);		
		}); 
	</script>
	
	<script type="text/javascript">
		
		// this defines the resizing behaviour of each box
		var resObj = {
               grid: [142,142],
			   minHeight: 131,
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
				$('#footer').animate({
					'margin-bottom': '0px'
				}, 500);
				//$('#settings').click(leaveSettingsMenu());
				$("#settings-triangle").unbind("click");
				$("#settings-triangle").on("click", leaveSettingsMenu);
		};
		
		function leaveSettingsMenu() {
				$('#footer').animate({
					'margin-bottom': '-72px'
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
    
    
	<style>
		body {
			background-color: #333;
    		background-size: 71px 71px;
			color: #000;
			overflow: hidden;
			font-family: Helvetica, Arial, sans-serif;
		}
		
		.box {
			position: absolute; 
			float: left;
			
			top: 1px; 
			left: 1px;
			
			width: 273px; 
			height: 273px;
			
			background: #45484d;
			background: -moz-linear-gradient(top, #45484d 0%, #000000 100%);
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#45484d), color-stop(100%,#000000));
			background: -webkit-linear-gradient(top, #45484d 0%,#000000 100%);
			background: -o-linear-gradient(top, #45484d 0%,#000000 100%);
			background: -ms-linear-gradient(top, #45484d 0%,#000000 100%);
			background: linear-gradient(to bottom, #45484d 0%,#000000 100%);
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#45484d', endColorstr='#000000',GradientType=0 );
	
			border-radius: 10px;
			
			color: #FFF;
			
			padding: 5px;
		}
		
		#settings-triangle {
			position: absolute;
			left: 0px;
			top: 0px;
			width: 0;
			height: 0;
			border-top: 72px solid #111;
			border-right: 72px solid transparent;
			z-index: 100;
			opacity: 0.9;
		}
		
		#settings-triangle img {
			cursor: pointer;	
		}
		
		.clock_in_checkout {
			position: absolute; 
			top: 50%; 
			left: 50%; 
			-moz-transform: translateX(-50%) translateY(-50%); 
			-webkit-transform: translateX(-50%) translateY(-50%); 
			transform: translateX(-50%) translateY(-50%); 
			color: #FFF; 
			font-size: 80px;
			border: 2px solid #222;
			padding-left: 10px;	
			padding-right: 10px;
		}
		
		#footer {
			position: absolute;
			bottom: 0px;
			left: 0px;
			right: 0px;
			height: 72px;
			margin-bottom: -72px;
			background-color: #111;
			z-index: 100;
			opacity: 0.9;
		}
	</style>
    
	<title>WebStatus</title>

</head>

<body>

	<div id="settings-triangle">
    	<img src="icons/settings.png" style="position: absolute; top: -62px; left: 10px; width: 25px; height: 25px;" id="settings" />
    </div>
    
    <div id="footer">
    	<center>
        	<img src="icons/add.png" style="height: 32px; width: 32px; margin: 20px;" id="add" />
        </center>
    </div>

	<?php
		$json = json_decode(file_get_contents("config.json"));
		for ($x = 0; $x < $json->count; $x++) {
			echo '<div class="box" id="box' . $x . '" style="top: ' . $json->{"box$x"}->top . 'px; left: ' . $json->{"box$x"}->left . 'px; width: ' . $json->{"box$x"}->width . 'px; height: ' . $json->{"box$x"}->height . 'px;">';
			
			switch ($json->{"box$x"}->type) {
				case "time": 	echo '<div class="clock_in_checkout"> </div>'; 
								break;
				case "json": 	$obj = new chart("sample/load.json");
								echo $obj->drawChart();
								echo $obj;
								break;
			}
			
			echo "</div>";
		} 
	?>
    
    
    <script type="text/javascript">
		$("#settings-triangle").on("click", enterSettingsMenu);
		$("#add").on("click", addBox);
	</script>


</body>
</html>