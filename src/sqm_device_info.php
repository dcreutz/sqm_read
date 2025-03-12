<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */

/*	Holds the information about the device needed for the header of the files */
	
class SQM_Device_Info {
	public $attrs;
	public $timezone;
	public $utc;
	public $tries;
	
	public function __construct($device_info,$station_info) {
		$this->attrs = array(
			'$DEVICE_TYPE'			=> $device_info['device_type'],
			'$DEVICE_ID'			=> $device_info['device_type'] . " " . $station_info['display_name'],
			'$DATA_SUPPLIER'		=> $station_info['data_supplier'],
			'$LOCATION_NAME'		=> $station_info['location_name'],
			'$LATITUDE'				=> $station_info['position'][0],
			'$LONGITUDE'			=> $station_info['position'][1],
			'$ELEVATION'			=> $station_info['position'][2],
			// possible feature to add, requires trusting the user to calibrate the SQM
			'$CALIBRATION_OFFSET'	=> "N/A"
		);
		$this->timezone = new DateTimeZone($station_info['timezone']);
		$this->utc = new DateTimeZone("UTC");
		$this->tries = $device_info['tries'];
	}
}
?>