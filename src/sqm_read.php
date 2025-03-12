#!/usr/bin/env php
<?php
/*	sqm_read
	(c) 2025 Darren Creutz
	Licensed under the GNU AFFERO GENERAL PUBLIC LICENSE v3 */

/*	Command line tool for reading SQM devices */

if (!function_exists("str_contains")) {
	die("PHP 8 or later is required\n");
}

function usage() {
	return <<<ENDUSAGE
Usage: read_sqm_le.php <hostname or IP> [options]
Options: -p <port>
         -t <tries/attempts>
         -i [requests info]
         -c [requests calibration]
         -f [requests full reading]
         -r [requests raw output only]
         -h [show this usage]
ENDUSAGE;
	// hidden option -w <num> is passed to netcat
}

require_once('sqm_device.php');

if ((count($argv) == 1) || ($argv[1] == "-h") || ($argv[1] == "-help")) {
	die(usage() . "\n");
}

$hostname = $argv[1];
$port = 10001;
$wait_time = 5;
$tries = [1,0];
$type = 'r';
$raw_only = false;

for ($i=2;$i<count($argv);$i++) {
	switch ($argv[$i]) {
		case '-p':
			$port = $argv[$i+1];
			$i++;
			break;
		case '-t':
			$tries = [ intval($argv[$i+1]), 5];
			$i++;
			break;
		case '-w':
			$wait_time = $argv[$i+1];
			$i++;
			break;
		case '-i':
			$type = 'i';
			break;
		case '-c':
			$type = 'c';
			break;
		case '-f':
			$type = 'f';
			break;
		case '-r':
			$raw_only = true;
			break;
	}
}

$sqm_le_device = new SQM_LE_Device($hostname,$port,$wait_time);

$output = "Failed to read SQM-LE\n";

if ($type == 'r') {
	$result = $sqm_le_device->reading($tries);
	if ($result && isset($result['msas'])) {
		$output = $result['msas'] . "\n";
	}
} else {
	switch ($type) {
		case 'i':
			$result = $sqm_le_device->info($tries);
			break;
		case 'c':
			$result = $sqm_le_device->calibration($tries);
			break;
		case 'f':
			$result = $sqm_le_device->reading($tries);
			break;
	}
	if ($result) {
		if ($raw_only) {
			$output = $result['raw'] . "\n";
		} else {
			$output = "";
			foreach ($result as $key => $value) {
				$output .= $key . ": " . $value . "\n";
			}
		}
	}
}

echo($output);
?>