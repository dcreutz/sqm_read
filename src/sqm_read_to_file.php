#!/usr/bin/env php
<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */

/*	Actually runs the SQM_Device_To_Files based on config.php */
	
if (!function_exists("str_contains")) {
	die("PHP 8 or later is required\n");
}

require_once('load_config.php');

if (isset($full_daemon_info['only_at_night']) && $full_daemon_info['only_at_night']) {
	$now = (new DateTime("now",new DateTimeZone($full_station_info['timezone'])))->format("H:i:s");
	if (($now > $daemon_info['night_end']) && ($now < $daemon_info['night_start'])) {
		exit();
	}
}

require_once('sqm_device.php');
require_once('sqm_device_info.php');
require_once('sqm_device_to_files.php');

if (!isset($file_info['daily_directory']) && !isset($file_info['monthly_directory'])) {
	die("At least one output file location must be specified\n");
}

$sqm_device = null;

switch ($full_device_info['device_type']) {
	case 'SQM LE':
	case 'SQM-LE':
	case 'SQM_LE':
	case 'SQMLE':
		if (!isset($full_device_info['hostname'])) {
			die("Hostname or IP address required for SQM LE\n");
		}
		$sqm_device = new SQM_LE_Device($full_device_info['hostname'],$full_device_info['port'],$full_device_info['wait_time']);
		break;
	case 'SQM LU':
	case 'SQM-LU':
	case 'SQM_LU':
	case 'SQMLU':
		$sqm_device = new SQM_LU_Device();
		break;
}	

if (!$sqm_device) {
	die("Unsupported device type\n");
}

$sqm_device_info = new SQM_Device_Info($full_device_info,$full_station_info);

$sqm_device_to_files = new SQM_Device_To_Files($sqm_device,$sqm_device_info,$full_file_info);

$reading = $sqm_device->reading($full_device_info['tries']);

if ($reading) {
	$sqm_device_to_files->add_reading($reading);
} else {
	die("Could not read SQM device\n");
}
?>