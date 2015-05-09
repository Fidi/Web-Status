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
			$this->json = json_decode(file_get_contents($this->input));
			$this->error = json_last_error();
		}
		
		private function getGraphTitle() {
			return $this->json->graph->title;
		}
		
		private function getGraphType() {
			return $this->json->graph->type;
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
		
		
		public function drawChart($width, $height) {
			$rand = rand();
			echo '	<canvas class="chart" id="chart' . $rand . '" >
						Your browser does not support the HTML5 canvas tag.
					</canvas><br/>';
					
			// caption
			echo '	<script>
						var c = document.getElementById("chart' . $rand . '");
						c.setAttribute("width", $("#chart' . $rand . '").css("width"));
						c.setAttribute("height", $("#chart' . $rand . '").css("height"));
						var ctx = c.getContext("2d");
						ctx.fillStyle = "#FFF";
						ctx.font = "20px Helvetica";
						ctx.fillText("' . $this->getGraphTitle() . '", 5, 25);
					</script>';
			//echo $this->getGraphType()	. '<br />';
		}
	}
?>