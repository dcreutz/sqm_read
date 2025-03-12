<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */
	
/*	Executes a query of the SQM device */

abstract class SQM_Query {
	const REQUEST = self::REQUEST;
	const RESPONSE = self::RESPONSE;
	
	protected abstract function parse($response);
	
	private $sqm_device;

	public function __construct($sqm_device) {
		$this->sqm_device = $sqm_device;
	}
	
	public function exec($tries = [1,0]) {
		return $this->do_exec($tries[0],$tries[1]);
	}
	
	private function do_exec($tries,$delay) {
		if ($tries == 0) {
			return null;
		}
		$response = $this->query();
		if ($response) {
			return $this->parse_response($response);
		}
		sleep($delay);
		return $this->do_exec($tries-1,$delay);
	}
	
	private function query() {
		return $this->last_line($this->request($this::REQUEST),$this::RESPONSE);
	}
	
	private function parse_response($response) {
		if ($response) {
			$result = $this->parse($response);
			if (!$result) {
				$result = array();
			}
			$result['raw'] = $response;
			return $result;
		}
		return null;
	}

	private function request($request) {
		$response = shell_exec($this->sqm_device->cmd_for($request));
		return preg_split("/\r\n|\n|\r/", $response);
	}
	
	// return only the latest reading of the specified type
	// SQM LE devices have some sort of cache
	private function last_line($lines,$prefix) {
		if ($lines && is_array($lines) && (count($lines) > 0)) {
			foreach ($lines as $line) {
				if (str_starts_with($line,$prefix)) {
					return $line;
				}
			}
		}
		return null;
	}
}

class SQM_Query_Reading extends SQM_Query {
	const REQUEST = "rx";
	const RESPONSE = "r";
	
	const PARTS = [ 'r', 'msas', 'frequency', 'counts', 'period', 'temperature' ];
	const SUFFIXES = [ "", "m", "Hz", "c", "s", "C" ];
	
	protected function parse($response) {
		$result = array();
		$parts = explode(",",$response);
		for ($i=1;$i<count($parts);$i++) {
			if (isset($this::PARTS[$i])) {
				$result[$this::PARTS[$i]] = rtrim(trim($parts[$i]),$this::SUFFIXES[$i]);
			}
		}
		return $result;
	}
}

class SQM_Query_Info extends SQM_Query {
	const REQUEST = "ix";
	const RESPONSE = "i";
	
	const PARTS = [ 'i', 'protocol_number', 'model_number', 'feature_number', 'serial_number' ];
	
	protected function parse($response) {
		$result = array();
		$parts = explode(",",$response);
		for ($i=1;$i<count($parts);$i++) {
			if (isset($this::PARTS[$i])) {
				$result[$this::PARTS[$i]] = trim($parts[$i]);
			}
		}
		return $result;
	}
}

class SQM_Query_Calibration extends SQM_Query {
	const REQUEST = "cx";
	const RESPONSE = "c";
	
	protected function parse($response) {
		return array();
	}
}
?>