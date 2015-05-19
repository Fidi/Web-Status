<?php	

	function debug_to_console($data) {
		if ( is_array( $data ) )
			$output = "<script>console.log('" . implode( ',', $data) . "');</script>";
		else
			$output = "<script>console.log('" . $data . "');</script>";

		echo $output;
	}
	
	
	function decodeJSON($filename) {
		return json_decode(utf8_encode(file_get_contents($filename)), false, 512, JSON_BIGINT_AS_STRING);
	}
	

	/**
	* Config Class
	*
	* This class parses the configuration file and returns
	* its json key values.
	*
	* @author Kevin Fiedler <kevinfiedler93f@gmail.com>
	* @copyright 2015 Kevin Fiedler
	* @license Check license file in this repo
	*/
	class config {
		
		// private section
		private $input = "";
		private $json = "";

			
		// public section
		public function __construct($filename) {
			if (!isset($filename)) {
				debug_to_console("The class could not be created. No filename submitted.");
			} else {
				debug_to_console("The class was initiated with parameter \"" . $filename .  "\".");
				$this->input = $filename;
				$this->json = decodeJSON($this->input);
			}
  		}	
		
		public function __destruct() {
      		debug_to_console('The class was destroyed.');
  		}
		
		public function __toString() {
      		return __CLASS__ . '"' . $this->input . '"<br />';
  		}
		
		public function getAnimationStatus() {
			return $this->json->animation;	
		}
		
		public function getRatio() {
			return $this->json->ratio;	
		}
		
		public function getBoxLength() {
			return count($this->json->box);
		}
		
		public function getBoxType($index) {
			return $this->json->box[$index]->type;
		}
		
		public function getBoxDetails($index) {
			return $this->json->box[$index]->details;
		}
		
		public function getBoxCol($index) {
			return $this->json->box[$index]->col;
		}
		
		public function getBoxRow($index) {
			return $this->json->box[$index]->row;
		}
		
		public function getBoxWidth($index) {
			return $this->json->box[$index]->width;
		}
		
		public function getBoxHeight($index) {
			return $this->json->box[$index]->height;
		}
	}

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
	
		// private section
		private $input = "";
		private $json = "";
		
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
		
		private function downloadRemoteFile($url) {
			$fp = fopen ("data/" . basename($url), 'w+');
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
			curl_setopt( $ch, CURLOPT_FILE, $fp );
			curl_exec( $ch );
			curl_close( $ch );
			fclose( $fp );
		}
		
		
		
		// public section
		public function __construct($filename) {
			if (!isset($filename)) {
				debug_to_console("The class could not be created. No filename submitted.");
			} else {
				debug_to_console("The class was initiated with parameter \"" . $filename .  "\".");
				if (filter_var($filename, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
					debug_to_console("Download required");
					$this->downloadRemoteFile($filename);
					$this->input = "data/" . basename($filename);
					debug_to_console($this->input);
				} else {
					$this->input = $filename;
				}
				$this->json = decodeJSON($this->input);
			}
  		}	
		
		public function __destruct() {
      		debug_to_console('The class was destroyed.');
  		}
		
		public function __toString() {
      		return __CLASS__ . '"' . $this->input . '"<br />';
  		}
		
		
		
		public function drawChart($animated) {
			$rand = rand();
			
			echo '	<canvas class="chart" id="chart' . $rand . '">
						Your browser does not support the HTML5 canvas tag.
					</canvas>
					
					<script type="text/javascript">
						var chart' . $rand . ' = new Chart("chart' . $rand . '", "' . $this->input . '");
						chart' . $rand . '.drawOutline();';
			if ($animated == true) {
				echo ' chart' . $rand . '.drawValues(true);';
			} else {
				echo ' chart' . $rand . '.drawValues(false);';
			}
			
			//echo '			$("#chart' . $rand . '").parent().on( "resize", function( event, ui ) { chart' . $rand . '.drawOutline(); chart' . $rand . '.drawValues(false); } );';
			echo '			$("window").on( "resize", function() { updateCharts(); chart' . $rand . '.drawOutline(); chart' . $rand . '.drawValues(false); } );';
			if ($this->getUpdateInterval() != self::VALUE_NOT_FOUND) {
				echo '	setInterval(function(){ chart' . $rand . '.drawOutline(); chart' . $rand . '.drawValues(false); }, ' . $this->getUpdateInterval() . ');';
			}
			echo ' </script>
					';
		}
	}
?>