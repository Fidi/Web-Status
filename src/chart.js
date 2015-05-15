// JS Chart library


(function() {
  var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
                              window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
  window.requestAnimationFrame = requestAnimationFrame;
})();


var colors = ["#2e89f9", "#ee2e22", "#fed105", "#31e618", "#f48026", "#97015e"];


// class wrapper
var Class = function(methods) {   
    var c = function() {    
        this.initialize.apply(this, arguments);          
    };  
    
    for (var property in methods) { 
       c.prototype[property] = methods[property];
    }
          
    if (!c.prototype.initialize) c.prototype.initialize = function(){};      
    
    return c;    
};


// chart class
var Chart = Class({
    initialize: function(canvasID, jsonPath) {
        this.canvasID = canvasID;
        this.jsonPath  = jsonPath;
		this.json_o = this.getJSON();
    },
	
	callbackFuncWithData: function(data){
 // do some thing with data 
	this.json_o = data;
},
	
	getJSON: function() {
		var result = null;
		$.ajax({
			url: this.jsonPath,
			async: false,
			dataType: "json",
			success: function(data){
				result = data;
			}
		});
		return result;
	},
	
	
	
	getChartTitle: function() {
		return this.json_o.graph.title;
	},
	
	getGraphType: function() {
		return this.json_o.graph.type;
	},
	
	
	
	getYAxisMin: function() {
		if (!(this.json_o.graph.hasOwnProperty("yAxis"))) {
			return -424242;	
		} else {
			return this.json_o.graph.yAxis.minValue;
		}
	},
	
	getYAxisMax: function() {
		if (!(this.json_o.graph.hasOwnProperty("yAxis"))) {
			return -424242;	
		} else {
			return this.json_o.graph.yAxis.maxValue;
		}
	},
	
	
	
	getSequenceCount: function() {
		return this.json_o.graph.datasequences.length;
	},
	
	getSequenceLength: function() {
		return this.json_o.graph.datasequences[0].datapoints.length;
	},
	
	getSequenceTitle: function(sequence) {
		return this.json_o.graph.datasequences[sequence].title;
	},
		
	getDatapointValue: function(sequence, index) {
		return this.json_o.graph.datasequences[sequence].datapoints[index].value;
	},
	
	getDatapointTitle: function(sequence, index) {
		return this.json_o.graph.datasequences[sequence].datapoints[index].title;
	},
	
	
	
	getMinValue: function() {
		var mi = Number.MAX_VALUE;
		for (var i = 0; i < this.getSequenceCount(); i++) {
			for (var j = 0; j < this.getSequenceLength()-1; j++) {
				if (mi > this.getDatapointValue(i, j)) {
					mi = this.getDatapointValue(i, j);
				}
			}
		}
		return mi;
	},
	
	getMaxValue: function() {
		var ma = Number.MIN_VALUE;
		for (var i = 0; i < this.getSequenceCount(); i++) {
			for (var j = 0; j < this.getSequenceLength()-1; j++) {
				if (ma < this.getDatapointValue(i, j)) {
					ma = this.getDatapointValue(i, j);
				}
			}
		}
		return ma;
	},
	
	getRange: function() {
		var ma = this.getYAxisMax();
		if (ma == -424242) {
			ma = this.getMaxValue();
		}
			
		mi = this.getYAxisMin();
		if (mi == -424242) {
			mi = this.getMinValue();	
		}
		mi = Math.min(mi, 0);
			
		return ma - mi;
	},
	
	getStepSize: function() {
		var mi = this.getYAxisMin();
		var ma = this.getYAxisMax();
		if ((mi != -424242) && (ma != -424242)) { return ((ma-mi)/10); }
		if (mi == -424242) { mi = 0; }
		if (ma == -424242) { ma = this.getMaxValue(); }
		
		var range = ma - mi;
		return (Math.ceil(range/10) * 10);
	},
	
	
	
	drawOutline: function() {
		var c = document.getElementById(this.canvasID);
		var ctx = c.getContext("2d");
							
		var canvasWidth = $("#" + this.canvasID).css("width").replace(/[^-\d\.]/g, "");
		var canvasHeight = $("#" + this.canvasID).css("height").replace(/[^-\d\.]/g, "");
		c.setAttribute("width", canvasWidth);
		c.setAttribute("height", canvasHeight);
		
		
		// draw the caption of the graph
		ctx.fillStyle = "#FFF";
		ctx.font = "bold 20px Helvetica";
		ctx.fillText(this.getChartTitle().toUpperCase(), 10, 25);
		
		// draw sequence titles
		var rightDistance = 10;
		for (var i = this.getSequenceCount()-1; i >= 0; i--) {
			ctx.fillStyle = colors[i];
			ctx.font = "bold 15px Helvetica";
			ctx.textAlign = "end"; 
			ctx.fillText(this.getSequenceTitle(i), canvasWidth - rightDistance, 25);
			rightDistance += (ctx.measureText(this.getSequenceTitle(i)).width + 10);
		}
		
		// if line or bar chart: draw some reference lines:
		if ((this.getGraphType() == "line") || (this.getGraphType() == "bar")) {
			
			// draw one bottom line 
			var bottomDistance = 25;					
			ctx.strokeStyle = "#FFF";
			ctx.lineWidth = 2;
			ctx.beginPath();
			ctx.moveTo(10, canvasHeight-bottomDistance);
			ctx.lineTo(canvasWidth-10,canvasHeight-bottomDistance);
			ctx.stroke();
				
			// draw reference lines				
			var lineDiff = (canvasHeight - bottomDistance - 60)/10;
			for (var i = 1; i <= 10; i++) {
				
				// reference line
				ctx.strokeStyle = "#666";
				ctx.lineWidth = 0.3;
				ctx.beginPath();
				ctx.moveTo(50, canvasHeight - bottomDistance - (i * lineDiff));
				ctx.lineTo(canvasWidth-10, canvasHeight - bottomDistance - (i * lineDiff));
				ctx.stroke();
				
				// and its descriptive caption
				var s = this.getStepSize();
				var number = Math.min(this.getMinValue(), 0) + (i * s);
				if (s < 1) {
					number = number.toFixed(2);
				} else if (s < 10) {
					number = number.toFixed(1);	
				}	else if (s < 1000) {
					number = number.toFixed(0);
				} else if (s < 1000000) {
					number = parseInt((number/1000).toFixed(0)) + "k";
				} else if (s < 10000000000) {
					number = parseInt((number/1000000).toFixed(0)) + "M";
				} else {
					number = parseInt((number/10000000000).toFixed(0)) + "G";
				}
				ctx.fillStyle = "#666";
				ctx.font = "bold 10px Helvetica";
				ctx.textAlign = "right"; 
				ctx.fillText(number, 40, canvasHeight - bottomDistance - (i * lineDiff) + 4);
			}
			
			// add footer captions
			var totalTitleWidth = 0;
			for (var i = 0; i < this.getSequenceLength()-1; i++) {
				totalTitleWidth += ctx.measureText(this.getDatapointTitle(0, i)).width;
			}
			var diffValue = 1;
			while (totalTitleWidth >= (canvasWidth - 60)-50) {
				totalTitleWidth = totalTitleWidth/2;
				diffValue = diffValue * 2;
			}
			var stepCounts = this.getSequenceLength()/diffValue;
			var titleMargin = (canvasWidth - 60 - totalTitleWidth)/stepCounts;

			for (var i = 0; i < stepCounts; i++){
				ctx.fillStyle = "#666";
				ctx.font = "bold 10px Helvetica";
				ctx.textAlign = "left"; 
				var text = this.getDatapointTitle(0, i*diffValue);
				ctx.fillText(text, 50 + (i * (ctx.measureText(text).width + titleMargin)), canvasHeight - 10);
			}
		}
	},
	
	
	drawValues: function(animated) {
		
		var c = document.getElementById(this.canvasID);
		var ctx = c.getContext("2d");
							
		var canvasWidth = $("#" + this.canvasID).css("width").replace(/[^-\d\.]/g, "");
		var canvasHeight = $("#" + this.canvasID).css("height").replace(/[^-\d\.]/g, "");
		
		// now draw the chart according to its style
		switch (this.getGraphType()) {

			case "line": 	var drawWidth = canvasWidth - 40;
							var widthDiff = drawWidth/this.getSequenceLength();
							
							// create an array
							var s = [];
							for (var i = this.getSequenceCount()-1;  i >= 0; i--) {
								var startLeft = 50;
								for (var j = 0; j < this.getSequenceLength()-1; j++) {
									var x = {
										left: startLeft,
										value :  50 + ((((this.getRange() - this.getDatapointValue(i, j)) - Math.min(this.getMinValue(), 0)) * (canvasHeight - 80)/(this.getRange()))),
										next : 50 + ((((this.getRange() - this.getDatapointValue(i, j+1)) - Math.min(this.getMinValue(), 0)) * (canvasHeight - 80)/(this.getRange()))),
										color : colors[i]
									};								
									s.push(x);
									
									startLeft += widthDiff;
								}
							}	
							
							var curNum = 0;
							ctx.lineWidth = 3;
							
							// draw the lines
							function animateLines(c) {
								ctx.strokeStyle = s[curNum].color;
								ctx.beginPath();
								ctx.moveTo(s[curNum].left, s[curNum].value);
								ctx.lineTo(s[curNum].left + widthDiff, s[curNum].next);
								ctx.stroke();
								curNum++;
								startLeft += widthDiff;
								if (c < s.length-1) {
									if (animated) {
										requestAnimationFrame(function () {
											animateLines(curNum);
										});
									} else {
										animateLines(curNum);
									}
								}
							}
							
							animateLines(0);
								
							break;
							
			case "bar": 	var drawWidth = canvasWidth - 60;
							
							// create an array
							var s = [];
							for (var i = 0;  i < this.getSequenceLength(); i++) {
								for (var j = 0; j < this.getSequenceCount(); j++) {								
									var x = {
										value :  50 + ((canvasHeight - 80) - ((this.getDatapointValue(j, i)/this.getRange()) * (canvasHeight - 80))),
										color : colors[j]
									};								
									s.push(x);
								}
							}
							
							var curNum = 0;
							var endNum = 100;
							var stepHeight = (canvasHeight - 80)/100;
							var startBottom = canvasHeight - 25;
							var barWidth = drawWidth/(this.getSequenceLength() * this.getSequenceCount());
							ctx.lineWidth = barWidth-1;
							
							// draw the bars
							function animateBars(c) {
								var startLeft = 50 + barWidth/2;
								for (var i = 0; i < s.length; i++) {
									if (s[i].value < (canvasHeight - 25 - (curNum * stepHeight))) {
										ctx.strokeStyle = s[i].color;
										ctx.beginPath();
										ctx.moveTo(startLeft, startBottom);						
										ctx.lineTo(startLeft, startBottom-(stepHeight+1)); 
										ctx.stroke();
									}
									startLeft += barWidth;
								}
								startBottom -= stepHeight;
								curNum++;
								if (c < endNum) {
									if (animated) {
										requestAnimationFrame(function () {
											animateBars(curNum);
										});
									} else {
										animateBars(curNum);
									}
								}
							}
							
							animateBars(0);
								
							break;	
							
			case "pie": 	var radius = Math.min(canvasHeight, canvasWidth)/4;
							var lineWidth = radius-20;
							
							// calculate total number
							var total = 0;
							for (var i = 0; i < this.getSequenceCount(); i++) {
								total += this.getDatapointValue(i, 0);
							}
							
							// store angles in array to access them inside animate function
							var s = [];
							for (var i = 0; i < this.getSequenceCount(); i++) {
								var x = {
									percent :  (this.getDatapointValue(i, 0)*100/total),
									color : colors[i]
								};
								s.push(x);
							}

							
							var endPercent = 101;
							var curPerc = 0;
							ctx.lineWidth = lineWidth;
							
							// and draw the pie with or without animating it
							function animatePie(c) {
								var l = 0;
								for (var i = 0; i < s.length; i++) {
									l += Math.round(s[i].percent);
									if (c*100 < l) {
										ctx.strokeStyle = s[i].color;
										break;
									}
								}
								ctx.beginPath();
								ctx.arc(canvasWidth / 2, canvasHeight / 2, radius, (Math.PI * 2)*(c-0.012) - (Math.PI/2), (Math.PI * 2 * c) - (Math.PI/2), false);
								ctx.stroke();
								curPerc++;
								if (curPerc < endPercent) {
									if (animated) {
										requestAnimationFrame(function () {
											animatePie(curPerc / 100);
										});
									} else {
										animatePie(curPerc / 100);
									}
								}
							}
							
							animatePie();
							
							break;
		}
	},
	
	
    toString: function() {
        return "Chart class " + this.getChartTitle();
    }
}); 