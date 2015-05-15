// JavaScript Document

function displayAnalogClock(canvasID) {
	var c = document.getElementById(canvasID);

	var canvasWidth = $("#" + canvasID).css("width").replace(/[^-\d\.]/g, '');
	var canvasHeight = $("#" + canvasID).css("height").replace(/[^-\d\.]/g, '');

	var angle;
	var distance = (Math.min(canvasWidth, canvasHeight) / 2) - 5;
	
	// update canvas in case of size change
	c.setAttribute("width", canvasWidth);
	c.setAttribute("height", canvasHeight);
	
	var ctx = c.getContext('2d');
	
	// clear and repaint
	ctx.clearRect(0, 0, canvasWidth, canvasHeight);   
	
	
	// get times
	var date = new Date;
	var hour = date.getHours();
	var min = date.getMinutes();
	var sec = date.getSeconds();
	var ampm = hour >= 12 ? 'PM' : 'AM';
	
	
	// hour marks
	for (var i = 0; i < 12; i++) {
		angle = (i - 3) * (Math.PI * 2) / 12;
		distance = Math.sqrt(1/(Math.pow(Math.sin(angle) / (canvasHeight/2), 2) + Math.pow(Math.cos(angle) / (canvasWidth/2), 2))) - 5;
		ctx.lineWidth = 1.2; 
		ctx.beginPath();
		var x1 = (canvasWidth / 2) + Math.cos(angle) * (distance);
		var y1 = (canvasHeight / 2) + Math.sin(angle) * (distance);
		var x2 = (canvasWidth / 2) + Math.cos(angle) * (distance - (distance / 12));
		var y2 = (canvasHeight / 2) + Math.sin(angle) * (distance - (distance / 12));
		ctx.moveTo(x1, y1);
		ctx.lineTo(x2, y2);
		ctx.strokeStyle = '#CCC';
		ctx.stroke();
	}
	
	
	// minute marks (if size allows it
	if (Math.min(canvasHeight, canvasWidth) > 200) {
		for (var i = 0; i < 60; i++) {
			angle = (i - 3) * (Math.PI * 2) / 60;
			distance = Math.sqrt(1/(Math.pow(Math.sin(angle) / (canvasHeight/2), 2) + Math.pow(Math.cos(angle) / (canvasWidth/2), 2))) - 5;
			ctx.lineWidth = 1; 
			ctx.beginPath();
			var x1 = (canvasWidth / 2) + Math.cos(angle) * (distance);
			var y1 = (canvasHeight / 2) + Math.sin(angle) * (distance);
			var x2 = (canvasWidth / 2) + Math.cos(angle) * (distance - (distance / 30));
			var y2 = (canvasHeight / 2) + Math.sin(angle) * (distance - (distance / 30));
			ctx.moveTo(x1, y1);
			ctx.lineTo(x2, y2);
			ctx.strokeStyle = '#CCC';
			ctx.stroke();
		}
	}
	
	// set the hand length to minimum height/width
	distance = (Math.min(canvasWidth, canvasHeight) / 2) - 5;
	
	// AM/PM
	if (Math.min(canvasHeight, canvasWidth) > 200) {
		ctx.textAlign = "center";     
		ctx.fillStyle = "#fc6b00";
		ctx.font = "bold 15px Helvetica";
		ctx.fillText(ampm, canvasWidth/2, canvasHeight/4);  
	}
	
	// add some awesome logo "\uF8FF"
	if (Math.min(canvasHeight, canvasWidth) > 200) {
		ctx.textAlign = "center";     
		ctx.fillStyle = "rgba(50, 50, 50, 0.3)";
		ctx.font = "bold 25px Helvetica";
		ctx.fillText("\uF8FF", canvasWidth/2, canvasHeight*3/4);  
	}
	
	// Minutes
	angle = ((Math.PI * 2) * (min / 60)) - ((Math.PI * 2) / 4);
	ctx.lineWidth = 5;
	ctx.beginPath();
	ctx.moveTo(canvasWidth / 2, canvasHeight / 2);
	ctx.lineTo((canvasWidth / 2 + Math.cos(angle) * distance / 1.1), canvasHeight / 2 + Math.sin(angle) * distance / 1.1);
	ctx.strokeStyle = '#CCC';
	ctx.lineCap = 'round';
	ctx.shadowBlur = 0;
	ctx.shadowOffsetX = 0;
	ctx.shadowOffsetY = 0;
	ctx.stroke();

	// Hours
	angle = ((Math.PI * 2) * ((hour * 5 + (min / 60) * 5) / 60)) - ((Math.PI * 2) / 4);
	ctx.lineWidth = 5;
	ctx.beginPath();
	ctx.moveTo(canvasWidth / 2, canvasHeight / 2); 
	ctx.lineTo((canvasWidth / 2 + Math.cos(angle) * distance / 1.5), canvasHeight / 2 + Math.sin(angle) * distance / 1.5);
	ctx.strokeStyle = '#CCC';
	ctx.lineCap = 'round';
	ctx.shadowColor = '#000';
	ctx.shadowBlur = 2;
	ctx.shadowOffsetX = 1;
	ctx.shadowOffsetY = 1;
	ctx.stroke();			
	
	// seconds
	angle = ((Math.PI * 2) * (sec / 60)) - ((Math.PI * 2) / 4);
	ctx.lineWidth = 1.5;
	ctx.beginPath();
	ctx.moveTo(canvasWidth / 2, canvasHeight / 2);
	ctx.lineTo((canvasWidth / 2 + Math.cos(angle) * distance), canvasHeight / 2 + Math.sin(angle) * distance);
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


function displayDigitalClock(containerClass) {
	var currentTime = new Date();
	var currentHours = currentTime.getHours();
	var currentMinutes = currentTime.getMinutes();
	var currentSeconds = currentTime.getSeconds();
	currentHours = (currentHours < 10 ? "0" : "") + currentHours;
	currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
	currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
	$("." + containerClass).html(currentHours + ":" + currentMinutes);
}