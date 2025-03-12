<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */

/*	Encapsulates a data file in the standard SQM format */
	
class SQM_Data_File {
	const DATE_FORMAT = "Y-m-d\TH:i:s.v";
	const DATA_DELIMITER = ";";

	public static function create($filepath,$filename,$sqm_device_info,$sqm_device) {
		if (file_exists($filepath) && is_dir($filepath) && is_writeable($filepath)) {
			if (file_exists($filepath . DIRECTORY_SEPARATOR . $filename)) {
				return new SQM_Data_File($filepath . DIRECTORY_SEPARATOR . $filename);
			}
			$header = SQM_Data_File_Header::build($sqm_device,$sqm_device_info);
			file_put_contents($filepath . DIRECTORY_SEPARATOR . $filename,$header);
			return new SQM_Data_File($filepath . DIRECTORY_SEPARATOR . $filename);
		} else {
			die("Directory " . $filepath . " is not writeable\n");
		}
	}
	
	public function add_reading($reading) {
		fwrite($this->filehandle,$this->data_line($reading) . "\n");
	}

	private $filename;
	private $filehandle;

	private function __construct($filename) {
		$this->filename = $filename;
		$this->filehandle = fopen($this->filename, "a") or die("Unable to open file " . $filename . "\n");
	}
	
	public function __destruct() {
		fclose($this->filehandle);
	}

	private function data_line($reading) {
		$msas = number_format(floatval($reading['msas']),3,'.','');
		$period = floatval($reading['period']);
		$frequency = floatval($reading['frequency']);
		if (($frequency < 30) && ($period > 0)) {
			$frequency = 1.0/$period;
		}
		$frequency = number_format($frequency,3,'.','');
		$counts = number_format(floatval($reading['counts']),3,'.','');
		$temp = number_format(floatval($reading['temperature']),2,'.','');
	
		return implode(self::DATA_DELIMITER, array(
			$reading['utc_datetime']->format(self::DATE_FORMAT),
			$reading['local_datetime']->format(self::DATE_FORMAT),
			$temp, $counts, $frequency, $msas
		));
	}
}

class SQM_Data_File_Header {
	public static function build($sqm_device,$sqm_device_info) {
		$info = $sqm_device->info($sqm_device_info->tries);
		$reading = $sqm_device->reading($sqm_device_info->tries);
		$calibration = $sqm_device->calibration();
		if ($info && $reading && $calibration) {
			$header = new SQM_Data_File_Header();
			$header->set_attr('$SERIAL_NUMBER',$info['serial_number']);
			$header->set_attr('$FEATURE_NUMBER',$info['feature_number']);
			$header->set_attr('PROTOCOL_NUMBER',$info['protocol_number']);
			$header->set_attr('$MODEL_NUMBER',$info['model_number']);
			$header->set_attr('$IXREADOUT',$info['raw']);
			$header->set_attr('$RXREADOUT',$reading['raw']);
			$header->set_attr('$CXREADOUT',$calibration['raw']);
			
			$header->set_attr('$FULL_TIMEZONE',$sqm_device_info->timezone->getName());
			$utc_offset = floatval($sqm_device_info->timezone->getOffset(new DateTime())) / 3600.0;
			if ($utc_offset == 0) {
				$value = "UTC";
			}
			if ($utc_offset > 0) {
				$value = "UTC+" . $utc_offset;
			}
			if ($utc_offset < 0) {
				$value = "UTC-" . abs($utc_offset);
			}
			$header->set_attr('$TIMEZONE',$value);
			
			foreach ($sqm_device_info->attrs as $key => $value) {
				$header->set_attr($key,$value);
			}
			
			return $header->text;
		} else {
			return null;
		}
	}
	
	private function raw_header_content() {
		return '# Definition of the community standard for skyglow observations 1.0
# URL: http://www.darksky.org/NSBM/sdf1.0.pdf
# Number of header lines: 35
# This data is released under the following license: ODbL 1.0 http://opendatacommons.org/licenses/odbl/summary/
# Device type: $DEVICE_TYPE
# Instrument ID: $DEVICE_ID
# Data supplier: $DATA_SUPPLIER
# Location name: $LOCATION_NAME
# Position: $LATITUDE, $LONGITUDE, $ELEVATION
# Local timezone: $TIMEZONE
# Time Synchronization: NTP
# Moving / Stationary position: STATIONARY
# Moving / Fixed look direction: FIXED
# Number of channels: 1
# Filters per channel: HOYA CM-500
# Measurement direction per channel: 0., 0.
# Field of view: 20
# Number of fields per line: 6
# SQM serial number: $SERIAL_NUMBER
# SQM firmware version: $FEATURE_NUMBER
# SQM cover offset value: $CALIBRATION_OFFSET
# SQM readout test ix: $IXREADOUT
# SQM readout test rx: $RXREADOUT
# SQM readout test cx: $CXREADOUT
# Comment:
# Comment:
# Comment: Timezone: $FULL_TIMEZONE
# Comment: 
# Comment: Capture program: sqm_read
# blank line 30
# blank line 31
# blank line 32
# UTC Date & Time, Local Date & Time, Temperature, Counts, Frequency, MSAS
# YYYY-MM-DDTHH:mm:ss.fff;YYYY-MM-DDTHH:mm:ss.fff;Celsius;number;Hz;mag/arcsec^2
# END OF HEADER
';
	}
	
	private $text;
	private $attrs;
	
	protected function __construct() {
		$this->text = $this->raw_header_content();
		$this->attrs = array();
	}
	
	private function set_attr($attr,$value) {
		$this->attrs[$attr] = $value;
		$this->text = str_replace($attr,$value,$this->text);
	}
}
?>