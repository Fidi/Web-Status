<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link href="http://code.jquery.com/ui/1.10.4/themes/dark-hive/jquery-ui.css" rel="stylesheet" />
	<script src="http://code.jquery.com/jquery-1.11.3.js"></script>
	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    
    <link href="src/style.css" rel="stylesheet" />
  
    
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
		
		
		function showClock() {
            var c = document.getElementById('analog_clock');

			var canvasWidth = $("#analog_clock").css("width").replace(/[^-\d\.]/g, '');
			var canvasHeight = $("#analog_clock").css("height").replace(/[^-\d\.]/g, '');
			
			var date = new Date;
            var angle;
            var secHandLength = (Math.min(canvasWidth, canvasHeight) / 2) - 5;
			
			// update canvas in case of size change
			c.setAttribute("width", canvasWidth);
			c.setAttribute("height", canvasHeight);
			
			var ctx = c.getContext('2d');
			
            // clear and repaint
            ctx.clearRect(0, 0, canvasWidth, canvasHeight);   
			
			
			// get times
			var hour = date.getHours();
			var min = date.getMinutes();
			var sec = date.getSeconds();
			var ampm = hour >= 12 ? 'PM' : 'AM';
			
			
			// hour marks
			for (var i = 0; i < 12; i++) {
				angle = (i - 3) * (Math.PI * 2) / 12;
				ctx.lineWidth = 1.2; 
				ctx.beginPath();
				var x1 = (canvasWidth / 2) + Math.cos(angle) * (secHandLength);
				var y1 = (canvasHeight / 2) + Math.sin(angle) * (secHandLength);
				var x2 = (canvasWidth / 2) + Math.cos(angle) * (secHandLength - (secHandLength / 12));
				var y2 = (canvasHeight / 2) + Math.sin(angle) * (secHandLength - (secHandLength / 12));
				ctx.moveTo(x1, y1);
				ctx.lineTo(x2, y2);
				ctx.strokeStyle = '#CCC';
				ctx.stroke();
			}
			
			
			// second marks
			for (var i = 0; i < 60; i++) {
				angle = (i - 3) * (Math.PI * 2) / 60;
				ctx.lineWidth = 1; 
				ctx.beginPath();
				var x1 = (canvasWidth / 2) + Math.cos(angle) * (secHandLength);
				var y1 = (canvasHeight / 2) + Math.sin(angle) * (secHandLength);
				var x2 = (canvasWidth / 2) + Math.cos(angle) * (secHandLength - (secHandLength / 30));
				var y2 = (canvasHeight / 2) + Math.sin(angle) * (secHandLength - (secHandLength / 30));
				ctx.moveTo(x1, y1);
				ctx.lineTo(x2, y2);
				ctx.strokeStyle = '#CCC';
				ctx.stroke();
			}
			
			
			
			// Hours
			if (Math.min(canvasHeight, canvasWidth) > 200) {
				ctx.textAlign = "center";     
				ctx.fillStyle = "#fc6b00";
				ctx.font = "bold 15px Helvetica";
				ctx.fillText(ampm, canvasWidth/2, secHandLength*3/5);  
			}

			angle = ((Math.PI * 2) * ((hour * 5 + (min / 60) * 5) / 60)) - ((Math.PI * 2) / 4);
			ctx.lineWidth = 5;
			ctx.beginPath();
			ctx.moveTo(canvasWidth / 2, canvasHeight / 2); 
			ctx.lineTo((canvasWidth / 2 + Math.cos(angle) * secHandLength / 1.5), canvasHeight / 2 + Math.sin(angle) * secHandLength / 1.5);
			ctx.strokeStyle = '#CCC';
			ctx.lineCap = 'round';
			ctx.shadowColor = '#000';
      		ctx.shadowBlur = 2;
      		ctx.shadowOffsetX = 1;
      		ctx.shadowOffsetY = 1;
			ctx.stroke();
			
			
			// Minutes
			angle = ((Math.PI * 2) * (min / 60)) - ((Math.PI * 2) / 4);
			ctx.lineWidth = 5;
			ctx.beginPath();
			ctx.moveTo(canvasWidth / 2, canvasHeight / 2);
			ctx.lineTo((canvasWidth / 2 + Math.cos(angle) * secHandLength / 1.1), canvasHeight / 2 + Math.sin(angle) * secHandLength / 1.1);
			ctx.strokeStyle = '#CCC';
			ctx.lineCap = 'round';
			ctx.shadowBlur = 0;
			ctx.shadowOffsetX = 0;
      		ctx.shadowOffsetY = 0;
			ctx.stroke();
			
			// seconds
			angle = ((Math.PI * 2) * (sec / 60)) - ((Math.PI * 2) / 4);
			ctx.lineWidth = 1.5;
			ctx.beginPath();
			ctx.moveTo(canvasWidth / 2, canvasHeight / 2);
			ctx.lineTo((canvasWidth / 2 + Math.cos(angle) * secHandLength), canvasHeight / 2 + Math.sin(angle) * secHandLength);
			ctx.strokeStyle = '#fc6b00';
			ctx.stroke();		    


			// center dot
			ctx.beginPath();
			ctx.arc(canvasWidth / 2, canvasHeight / 2, 2, 0, Math.PI * 2);
			ctx.lineWidth = 8;
			ctx.fillStyle = '#fc6b00';
			ctx.strokeStyle = '#fc6b00';
			ctx.stroke();
        }
	
	</script>
    
	<title>WebStatus</title>

</head>

<body>

	<div id="settings-triangle">
    	<img src="icons/settings.png" style="position: absolute; top: -62px; left: 10px; width: 25px; height: 25px;" id="settings" />
    </div>
    
    <div id="settings-menu">
    	<center>
        	<img src="icons/add.png" style="height: 32px; width: 32px; margin: 20px;" id="settings-add" />
        </center>
    </div>

	<?php
		$json = json_decode(file_get_contents("config.json"));
		for ($x = 0; $x < $json->count; $x++) {
			echo '<div class="box" id="box' . $x . '" style="top: ' . $json->{"box$x"}->top . 'px; left: ' . $json->{"box$x"}->left . 'px; width: ' . $json->{"box$x"}->width . 'px; height: ' . $json->{"box$x"}->height . 'px;">';
			
			switch ($json->{"box$x"}->type) {
				case "time": 	if ($json->{"box$x"}->details == "analog") {
									echo ' 	<div style="margin: 10%; width: 80%; height: 80%; border-radius: 50%; -webkit-box-shadow: inset 0px 0px 10px 1px rgba(0,0,0,0.75); -moz-box-shadow: inset 0px 0px 10px 1px rgba(0,0,0,0.75); box-shadow: inset 0px 0px 10px 1px rgba(0,0,0,0.75);">
												<canvas id="analog_clock" style="width: 100%; height: 100%;"></canvas>
											</div>';
								} else {
									echo '<div class="clock_in_checkout"> </div>'; 
								}
								break;
				case "json": 	$obj = new chart("sample/load.json");
								echo $obj->drawChart();
								//echo $obj;
								break;
			}
			
			echo "</div>";
		} 
	?>
    
    <!--
    <div class="box" style="position: absolute; bottom: 0px; left: 0px; height: 400px; width: 400px; background-color: #333;">
    	<div style="margin: 10%; width: 80%; height: 80%; border-radius: 50%; -webkit-box-shadow: inset 0px 0px 10px 1px rgba(0,0,0,0.75); -moz-box-shadow: inset 0px 0px 10px 1px rgba(0,0,0,0.75); box-shadow: inset 0px 0px 10px 1px rgba(0,0,0,0.75);">
    		<canvas id="watch" style="width: 100%; height: 100%;"></canvas>
        </div>
    </div>
    -->
    
    
    <script type="text/javascript">
		$("#settings-triangle").on("click", enterSettingsMenu);
		$("#settings-add").on("click", addBox);
	</script>
    
    <script>
		showClock();
        setInterval(showClock, 1000);
	</script>


</body>
</html>