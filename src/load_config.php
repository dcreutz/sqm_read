<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */

/*	Sensible default configuration options */

$full_station_info = [
	'display_name' => '',
	'data_supplier' => '',
	'location_name' => '',
	'position' => [ 0.0, -0.0, "0" ],
	'timezone' => date_default_timezone_get()
];

$full_file_info = [
	'day_start' => "12:00",
	
	'daily_name_prefix' => "SQM_",
	'date_format' => 'Y-m-d',
	
	'monthly_name_prefix' => "SQM_",
	'month_format' => 'Y-m',
	
	'file_extension' => '.dat'
];

$full_device_info = [
	'device_type' => 'SQM-LE',
	
	'port' => 10001,
	'wait_time' => 5, // passed to netcat
	'tries' => [5, 2],
];

$full_daemon_info = [
	'only_at_night' => false,
	'night_start' => "16:00",
	'night_end' => "09:00",
];

include('..' . DIRECTORY_SEPARATOR . 'config.php');

foreach ($station_info as $key => $value) {
	$full_station_info[$key] = $value;
}
foreach ($file_info as $key => $value) {
	$full_file_info[$key] = $value;
}
foreach ($device_info as $key => $value) {
	$full_device_info[$key] = $value;
}
foreach ($daemon_info as $key => $value) {
	$full_daemon_info[$key] = $value;
}
if ($full_daemon_info['night_start'][1] == ':') {
	$full_daemon_info['night_start'] = '0' . $full_daemon_info['night_start'];
}
if ($full_daemon_info['night_end'][1] == ':') {
	$full_daemon_info['night_end'] = '0' . $full_daemon_info['night_end'];
}
?>