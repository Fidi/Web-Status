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
		
		private $colors = array('#2e89f9', '#ee2e22', '#fed105', '#f48026', '#31e618', '#97015e');
		
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
		
		
		private function getUpdateInterval() {
			$int = $this->json->graph->refreshEveryNSeconds;
			if (($int == "") && ($int != 0)) {
				return self::VALUE_NOT_FOUND;
			}	
			return $int*1000;
		}
		
		
		
		private function getYAxisMin() {
			$min = $this->json->graph->yAxis->minValue;
			if (($min == "") && ($min != 0)) {
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
		
		private function getDatapointValue($sequence, $index) {
			return $this->json->graph->datasequences[$sequence]->datapoints[$index]->value;
		}
		
		
		
		
		private function getMaxValue() {
			$max = 0;
			for ($i = 0; $i < $this->getSequenceCount(); $i++) {
				for ($j = 0; $j < $this->getSequenceLength(); $j++) {
					$val = $this->getDatapointValue($i, $j);
					if ($val > $max) { $max = $val; }
				}		
			}
			return $max;
		}
		
		private function getMinValue() {
			$min = 0;
			for ($i = 0; $i < $this->getSequenceCount(); $i++) {
				for ($j = 0; $j < $this->getSequenceLength(); $j++) {
					$val = $this->getDatapointValue($i, $j);
					if ($val < $min) { $min = $val; }
				}		
			}
			return $min;
		}
		
		private function getRange() {
			$maxValue = $this->getYAxisMax();
			if ($maxValue == self::VALUE_NOT_FOUND) {
				$maxValue = $this->getMaxValue();
			}
			
			$minValue = $this->getYAxisMin();
			if ($minValue == self::VALUE_NOT_FOUND) {
				$minValue = $this->getMinValue();	
			}
			
			return $maxValue - $minValue;
		}
		
		private function getStepSize() {
			$min = $this->getYAxisMin();
			$max = $this->getYAxisMax();
			$range = $this->getRange();
			if (($min != self::VALUE_NOT_FOUND) && ($max != self::VALUE_NOT_FOUND)) {
				return	($range/10);
			}
			if ($range <= 5) { return 0.5; }
			else if ($range <= 10) { return 1; }
			else { return ceil($range / 10) * 10; }	
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
			$entr = array_rand($this->colors, $this->getSequenceCount());
			
			echo '	<canvas class="chart" id="chart' . $rand . '">
						Your browser does not support the HTML5 canvas tag.
					</canvas>
					
					<script type="text/javascript">
						var chart' . $rand . ' = new Chart("chart' . $rand . '", "' . $this->input . '");
						chart' . $rand . '.drawOutline();
						chart' . $rand . '.drawValues(true);
						$("#chart' . $rand . '").parent().on( "resize", function( event, ui ) { chart' . $rand . '.drawOutline(); chart' . $rand . '.drawValues(false); } );';
			if ($this->getUpdateInterval() != self::VALUE_NOT_FOUND) {
				echo '	setInterval(function(){ chart' . $rand . '.drawOutline(); chart' . $rand . '.drawValues(false); }, ' . $this->getUpdateInterval() . ');';
			}
			echo ' </script>
					';
		}
	}
?>