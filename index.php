<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link href="http://code.jquery.com/ui/1.10.4/themes/dark-hive/jquery-ui.css" rel="stylesheet" />
	<script src="http://code.jquery.com/jquery-1.11.3.js"></script>
	<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    
    <link href="src/style.css" rel="stylesheet" />
    <script src="src/clock.js"></script>
    <script src="src/chart.js"></script>
  
    
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
					
						//$.post("src/writeJSON.php", {json : json_str});  
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
					
						//$.post("src/writeJSON.php", {json : json_str});  
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
					
						//$.post("src/writeJSON.php", {json : json_str});  
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
					
						//$.post("src/writeJSON.php", {json : json_str});  
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
    	<img src="icons/fullscreen_on.png" id="fullscreen" title="Toggle fullscreen" />
        <script type="text/javascript">
			$(document).ready(function() {
				if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
					$("#fullscreen").attr("src", "icons/fullscreen_on.png");
				} else {
					$("#fullscreen").attr("src", "icons/fullscreen_off.png");
				}
			});
		</script>
    </div>

	<?php
		//$json = json_decode(file_get_contents("config2.json"));
		$conf = new config("config.json");
		
		
		// print all the boxes
		for ($x = 0; $x < $conf->getBoxLength(); $x++) {
			echo '<div class="box" id="box' . $x . '">';
			
			// this adjusts the position and size at first draw
			echo '<script type="text/javascript">';
			if ($conf->getRatio() == "16:9") {
			echo '	var unit = Math.min(window.innerHeight/9, window.innerWidth/16);
					';
			} else {
			echo '	var unit = Math.min(window.innerHeight/3, window.innerWidth/4);
					';	
			}
			echo '	// set width/height
					var w = unit * ' . $conf->getBoxWidth($x) . ';
					var h = unit * ' . $conf->getBoxHeight($x) . ';
					$("#box' . $x . '").css("width", w);
					$("#box' . $x . '").css("height", h);
					
					// set left/top
					var l = unit * ' . ($conf->getBoxCol($x)-1) . ';
					var t = unit * ' . ($conf->getBoxRow($x)-1) . ';
					$("#box' . $x . '").css("left", l);
					$("#box' . $x . '").css("top", t);
					';
			echo '</script>';
			
			// draw the chart
			switch ($conf->getBoxType($x)) {
				case "clock": 	if ($conf->getBoxDetails($x) == "analog") {
									echo ' 	<div class="analog_clock">
												<canvas id="analog_clock" style="width: 100%; height: 100%;"></canvas>
											</div>';
								} else {
									echo '<div class="digital_clock"> </div>'; 
								}
								break;
								
				case "json": 	$obj = new chart($conf->getBoxDetails($x));
								echo $obj->drawChart($conf->getAnimationStatus());
								break;
			}
			
			echo "</div>";
		}
				
		
		// now create global updating js script:
		echo '<script type="text/javascript">
				$(window).on("resize", function() { updateCharts(); });
				
				function updateCharts(){
		  		';
		
		// calculate the size of one unit:
		if ($conf->getRatio() == "16:9") {
		echo '	var unit = Math.min(window.innerHeight/9, window.innerWidth/16);
				';
		} else {
		echo '	var unit = Math.min(window.innerHeight/3, window.innerWidth/4);
				';	
		}
		
		for ($x = 0; $x < $conf->getBoxLength(); $x++) {
			echo '	// set width/height
					var w = unit * ' . $conf->getBoxWidth($x) . ';
					var h = unit * ' . $conf->getBoxHeight($x) . ';
					$("#box' . $x . '").css("width", w);
					$("#box' . $x . '").css("height", h);
					
					// set left/top
					var l = unit * ' . ($conf->getBoxCol($x)-1) . ';
					var t = unit * ' . ($conf->getBoxRow($x)-1) . ';
					$("#box' . $x . '").css("left", l);
					$("#box' . $x . '").css("top", t);
					';
		}
			
		echo ' 	};
				</script>';
	?>
    
    <script type="text/javascript">

		function toggleFullScreenMode() {
			if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
				if (document.documentElement.requestFullscreen) {
					document.documentElement.requestFullscreen();
				} else if (document.documentElement.mozRequestFullScreen) {
					document.documentElement.mozRequestFullScreen();
				} else if (document.documentElement.webkitRequestFullscreen) {
					document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
				}
				
				$("#fullscreen").attr("src", "icons/fullscreen_off.png");
			} else {
				if (document.cancelFullScreen) {
					document.cancelFullScreen();
				} else if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen();
				} else if (document.webkitCancelFullScreen) {
					document.webkitCancelFullScreen();
				}
				
				$("#fullscreen").attr("src", "icons/fullscreen_on.png");
			}
		}
		
    	$("#fullscreen").on('click', function() { toggleFullScreenMode(); updateCharts(); });
    </script>
</body>
</html>