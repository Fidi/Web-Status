<?php	 

	/**
	* Chart Class
	*
	* This class creates a chart from a submitted
	* json file and displays it.
	*
	* @author Kevin Fiedler <kevinfiedler93f@gmail.com>
	* @copyright 2015 Kevin Fiedler
	* @license Check license file in this repo
	*/
	class chart {
		
		const VALUE_NOT_FOUND = -424242;
		
		private $colors = array('#00b1b0', '#ee2e22', '#fed105', '#f48026', '#31e618', '#97015e');
		
		// private section
		private $input = "";
		private $json = "";
		private $error = "";
		
		/**
	   	* Sets $foo to a new value upon class instantiation
	   	*
	   	* @param string $val a value required for the class
	   	* @return void
	   	*/
		private function decodeJSON() {
			$this->debug_to_console('Decoding JSON file.');
			$this->json = json_decode(utf8_encode(file_get_contents($this->input)), false, 512, JSON_BIGINT_AS_STRING);
			$this->error = json_last_error();
		}
		
		private function getGraphTitle() {
			return $this->json->graph->title;
		}
		
		private function getGraphType() {
			return $this->json->graph->type;
		}
		
		private function getYAxisMin() {
			$min = $this->json->graph->yAxis->minValue;
			if ($min == "") {
				return self::VALUE_NOT_FOUND;
			}
			return $min;
		}
		
		private function getYAxisMax() {
			$max = $this->json->graph->yAxis->maxValue;
			if ($max == "") {
				return self::VALUE_NOT_FOUND;
			}
			return $max;
		}
		
		private function getSequenceCount() {
			return count($this->json->graph->datasequences);
		}
		
		private function getSequenceLength() {
			return count($this->json->graph->datasequences[0]->datapoints);
		}
		
		private function getSequenceTitle($sequence) {
			return $this->json->graph->datasequences[$sequence]->title;
		}
		
		private function getDatapoint($sequence, $index) {
			return $this->json->graph->datasequences[$sequence]->datapoints[$index];
		}
		
		function debug_to_console($data) {
			if ( is_array( $data ) )
				$output = "<script>console.log('Chart PHP: " . implode( ',', $data) . "');</script>";
			else
				$output = "<script>console.log('Chart PHP: " . $data . "');</script>";
	
			echo $output;
		}
	
		
		
		// public section
		public function __construct($filename) {
			if (!isset($filename)) {
				$this->debug_to_console("The class could not be created. No filename submitted.");
			} else {
				$this->debug_to_console("The class was initiated with parameter \"" . $filename .  "\".");
				$this->input = $filename;
				$this->decodeJSON();
				$this->debug_to_console($this->getError());
			}
  		}	
		
		public function __destruct() {
      		$this->debug_to_console('The class was destroyed.');
  		}
		
		public function __toString() {
      		return __CLASS__ . '"' . $this->input . '"<br />';
  		}
		
		
		public function getError() {
			$result = "Error: ";
			switch($error) {
        		case JSON_ERROR_NONE:
					$result = $result . 'No errors'; break;
				case JSON_ERROR_DEPTH:
					$result = $result . 'Depth error'; break;
				case JSON_ERROR_STATE_MISMATCH:
					$result = $result . 'Invalid JSON'; break;
				case JSON_ERROR_CTRL_CHAR:
					$result = $result . 'Control character'; break;
				case JSON_ERROR_SYNTAX:
					$result = $result . 'Syntax error'; break;
				case JSON_ERROR_UTF8:
					$result = $result . 'UTF-8 error'; break;
				default:
					$result = $result . 'Unknown error'; break;
			}
			return $result;
		}
		
		
		public function drawChart() {
			$rand = rand();
			echo '	<canvas class="chart" id="chart' . $rand . '">
						Your browser does not support the HTML5 canvas tag.
					</canvas><br/>';
					
			// init
			echo ' 	<script type="text/javascript">
						function drawChart' . $rand . '() {
							var c = document.getElementById("chart' . $rand . '");
			  				var ctx = c.getContext("2d");
							
							var canvasWidth = $("#chart' . $rand . '").css("width").replace(/[^-\d\.]/g, "");
							var canvasHeight = $("#chart' . $rand . '").css("height").replace(/[^-\d\.]/g, "");
							c.setAttribute("width", canvasWidth);
							c.setAttribute("height", canvasHeight);

 
							drawOutline();
 							
				
							// This draws all the captions and
							// borders of the chart				
							function drawOutline() {
								
								// draw the caption of the graph
								ctx.fillStyle = "#FFF";
								ctx.font = "bold 20px Helvetica";
								ctx.fillText("' . $this->getGraphTitle() . '", 5, 25);
								
								var rightdistance = 5;
								
								// draw sequence titles
								';
								
			$entr = array_rand($this->colors, $this->getSequenceCount());
			for ($i = $this->getSequenceCount()-1; $i >= 0; $i--) {
				echo '			ctx.fillStyle = "' . $this->colors[$entr[$i]] . '";
								ctx.font = "bold 15px Helvetica";
								ctx.textAlign = "end"; 
								ctx.fillText("' . $this->getSequenceTitle($i) . '", canvasWidth - rightdistance, 25);
								rightdistance += (ctx.measureText("' . $this->getSequenceTitle($i) . '").width + 10);
								';
			}
			
			echo '
							}
						};
						
						drawChart' . $rand . '();
					</script>';
		/*
			echo '	<script>
						var c = document.getElementById("chart' . $rand . '");
						cWidth = $("#chart' . $rand . '").css("width").replace(/[^-\d\.]/g, "");
						cHeight = $("#chart' . $rand . '").css("height").replace(/[^-\d\.]/g, "");
						c.setAttribute("width", cWidth);
						c.setAttribute("height", cHeight);
						var ctx = c.getContext("2d");';
			
			// graph title			
			echo '		ctx.fillStyle = "#FFF";
						ctx.font = "bold 20px Helvetica";
						ctx.fillText("' . $this->getGraphTitle() . '", 5, 25);';
					
			// sequence titles
			$text = '';
			echo '		var rightdistance = 5;';
			for ($i = $this->getSequenceCount()-1; $i >= 0; $i--) {
				//$text = $text . $this->getSequenceTitle($i) . " ";	
				echo '	ctx.fillStyle = "' . $this->colors[$i] . '";
						ctx.font = "bold 15px Helvetica";
						ctx.textAlign = "end"; 
						ctx.fillText("' . $this->getSequenceTitle($i) . '", cWidth - rightdistance, 25);
						rightdistance += (ctx.measureText("' . $this->getSequenceTitle($i) . '").width + 10);';
			}
			echo '	
					</script>';
					*/
		}
	}
?>