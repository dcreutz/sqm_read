# sqm_read_to_file Configuration Instructions

The options available are all specified in ```config.php```.

The mandatory options are ```device_type``` and ```hostname``` but position is highly recommended.

## Sation Information

```$station_info = [```

Station information configuration.

```	'display_name' => 'SQM Name',```

The display name to report in the headers of the data files, defaults to empty.

```	'data_supplier' => 'Data Supplier',```

The data supplier to report in the headers, defaults to empty.

```	'location_name' => 'Location',```

The location to report in the headers, defaults to empty.

```	'position' => [ 0.0, -0.0, "0" ],````

The position to report in the headers, defaults to [ 0.0, -0.0, "0" ].  The values are latitude, longitude, and elevation.

```	'timezone' => 'America/Los_Angeles',```

The timezone to use for local time specified in the IANA format, defaults to computer timezone.

```];```

## File Information

```$file_info = [```

Where to store the data files and how to name and organize them.

```	'day_start' => "12:00",```

What time to consider the start of the day for daily data files, defaults to "12:00".

```	'file_extension' => '.dat',```

What extension to use for the data files.

```
	'daily_directory' => 'daily_data',
	'daily_name_prefix' => "SQM_",
	'date_format' => 'Y-m-d',
```

Where to store daily data files and how to name them.  Files will be named with the date formatted as specified and the given prefix.  Set 'daily_directory' to false (or comment it out) to disable storing daily data files.

```
	'monthly_directory' => "data",
	'monthly_name_prefix' => "SQM_",
	'month_format' => 'Y-m',
```

Where to store monthly data files and how to name them.  Files will be named with the month formatted as specified and the given prefix.  Set 'monthly_directory' to false (or comment it out) to disable storing monthly data files.

```];```

## Device Information

```$device_info = [```

Information about the SQM device.

```	'device_type' => 'SQM-LE',```

The device type, currently only SQM-LE is supported.  This option is mandatory.

```	'hostname' => "127.0.0.1",```

The hostname or IP address of the SQM-LE device.  This option is mandatory.

```	'port' => 10001,```

The port to access the SQM-LE, defaults to the standard 10001.

```	'tries' => [5, 2],```

How many attempts to make before giving up and how long between them (in seconds).

```];```

## Daemon Information

```$daemon_info = [```

When to collect date.

```	'only_at_night' => true,```

Only take readings at night (useful if using a cron job or systemd), default is false.

```	'night_start' => "16:00",```

What time to start taking readings (if only_at_night is set).

```	'night_end' => "09:00",```

What time to stop taking readings (if only_at_night is set).

```];```