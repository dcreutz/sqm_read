<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */

/*	sqm_read configuration options */

$station_info = [
/*	The display name to report in the headers of the data files, defaults to '' */
	'display_name' => 'SQM Name',

/*	The data supplier to report in the headers, defaults to '' */
	'data_supplier' => 'Data Supplier',

/*	The location to report in the headers, defaults to '' */
	'location_name' => 'Location',

/*	The position to report in the headers, defaults to [ 0.0, -0.0, "0 meters" ]
	[ Latitude, Longitude, Elevation ]
	While technically optional, the data is not very useful without location info
	Values should be numbers not string */
	'position' => [ 0.0, -0.0, 0 ],

/*	The timezone to use for local time, defaults to computer timezone */
	'timezone' => 'America/Los_Angeles',
];

$file_info = [
/*	What time to consider the start of the day for daily data files, defaults to "12:00" */
	'day_start' => "12:00",

/*	Where to store daily data files and how to name them
	set 'daily_directory' to false (or comment it out) to disable daily data files */
	'daily_directory' => 'daily_data',
	'daily_name_prefix' => "SQM_",
	'date_format' => 'Y-m-d',

/*	Where to store monthly data files and how to name them
	set 'monthly_directory' to false (or comment it out) to disable monthly data files */
	'monthly_directory' => "data",
	'monthly_name_prefix' => "SQM_",
	'month_format' => 'Y-m',
	
	'file_extension' => '.dat',
];

$device_info = [
/*	The device type, currently only SQM-LE is supported */
	'device_type' => 'SQM-LE',		// mandatory

/*	The hostname or IP address of the SQM-LE */
	'hostname' => "127.0.0.1",		// mandatory
	
/*	The port to access the SQM-LE, defaults to the standard 10001 */
	'port' => 10001,

/*	How many attempts to make before giving up and how long between them (in seconds) */
	'tries' => [5, 2], // number of tries, duration of sleep
];

$daemon_info = [
/*	Only take readings at night (useful if using a cron job or systemd), default is false */
	'only_at_night' => true,

/*	What time to start taking readings, default is 4pm */
	'night_start' => "16:00",

/*	What time to stop taking readings, default is 9am */
	'night_end' => "09:00",
];
?>