<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */

/*	Encapsulates a connection to an SQM device */

require_once('sqm_query.php');

abstract class SQM_Device {
	public function reading($tries = [1,0]) {
		return (new SQM_Query_Reading($this))->exec($tries);
	}
	
	public function info($tries = [1,0]) {
		return (new SQM_Query_Info($this))->exec($tries);
	}
	
	public function calibration($tries = [1,0]) {
		return (new SQM_Query_Calibration($this))->exec($tries);
	}

	public abstract function cmd_for($request);
}

class SQM_LE_Device extends SQM_Device {
	private $hostname;
	private $port;	
	private $wait_time;

	public function __construct($hostname, $port = 10001, $wait_time = 5) {
		$this->hostname = $hostname;
		$this->port = $port;
		$this->wait_time = $wait_time;
	}
	
	public function cmd_for($request) {
		return 'echo "' . $request . '" | nc -w ' . $this->wait_time . ' ' . $this->hostname . ' ' . $this->port;
	}
}

class SQM_LU_Device extends SQM_Device {
	public function __construct() {
		die("SQM LU is not currently supported\n");	
	}
	
	public function cmd_for($request) {
	
	}
}
?>