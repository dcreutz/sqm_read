<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */

/*	Asks the SQM_Device for a reading then sends it to the SQM_Data_Files */

require_once('sqm_device.php');
require_once('sqm_data_file.php');

class SQM_Device_To_Files {
	private $sqm_device;
	private $sqm_device_info;
	private $file_info;
	
	private $daily_files;
	private $monthly_files;
	
	public function __construct($sqm_device,$sqm_device_info,$file_info) {
		$this->sqm_device = $sqm_device;
		$this->sqm_device_info = $sqm_device_info;
		$this->file_info = $file_info;
		
		$this->daily_files = array();
		$this->monthly_files = array();
	}
	
	public function add_reading($reading) {
		if (!$reading || !isset($reading['msas']) || !is_numeric($reading['msas'])) {
			return null;
		}
		$reading['local_datetime'] = new DateTimeImmutable("now",$this->sqm_device_info->timezone);
		$reading['utc_datetime']   = new DateTimeImmutable("now",$this->sqm_device_info->utc);

		if (isset($this->file_info['daily_directory']) && $this->file_info['daily_directory']) {
			$date_for = $this->date_for($reading['local_datetime'],$this->file_info['day_start'],$this->file_info['date_format']);
			if (!isset($this->daily_files[$date_for])) {
				$daily_file = SQM_Data_File::create(
					$this->file_info['daily_directory'],
					$this->file_info['daily_name_prefix'] . $date_for . $this->file_info['file_extension'],
					$this->sqm_device_info, $this->sqm_device
				);
				if ($daily_file) {
					$this->daily_files[$date_for] = $daily_file;
				}
			}
			if (isset($this->daily_files[$date_for])) {
				$this->daily_files[$date_for]->add_reading($reading);
			}
		}

		if (isset($this->file_info['monthly_directory']) && $this->file_info['monthly_directory']) {
			$month_for = $this->month_for($reading['local_datetime'],$this->file_info['month_format']);
			if (!isset($this->monthly_files[$month_for])) {
				$monthly_file = SQM_Data_File::create(
					$this->file_info['monthly_directory'],
					$this->file_info['monthly_name_prefix'] . $month_for . $this->file_info['file_extension'],
					$this->sqm_device_info, $this->sqm_device
				);
				if ($monthly_file) {
					$this->monthly_files[$month_for] = $monthly_file;
				}
			}
			if (isset($this->monthly_files[$month_for])) {
				$this->monthly_files[$month_for]->add_reading($reading);
			}
		}
	}
	
	private function date_for($datetime,$day_start,$format) {
		if ($datetime->format("H:i:s") >= $day_start) {
			return $datetime->format($format);
		} else {
			return $datetime->modify("-1 day")->format($format);
		}
	}
	
	private function month_for($datetime,$format) {
		return $datetime->format($format);
	}
}
?>