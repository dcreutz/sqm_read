# sqm_read.php

Command line tool to read from an SQM device.

Standard usage:
```
./sqm_read.php <IP or Hostname> [options]
```

By default, it will return the msas reading.

## Options

```-p <port>```
This specifies the port the SQM is listening on (default is 10001).

```-t <tries/attempts>```
How many times to attempt a reading before giving up.

```-i```
Reads the information from the SQM device rather than taking a reading.

```-c```
Reads the calibration information from the SQM device.

```-f```
Returns the complete reading, not just the msas.

```-r```
Only the raw response from the SQM is returned.