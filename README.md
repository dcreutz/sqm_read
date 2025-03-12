# sqm_read

Open source minimal program for reading Unihedron SQM (Sky Quality Meter) devices and creating data files in the 'International Dark Sky Association (IDA) NSBM Community Standards for Reporting Skyglow Observations' [format](https://darksky.org/app/uploads/bsk-pdf-manager/47_SKYGLOW_DEFINITIONS.PDF).

sqm_read is free and open-source, provided as-is and licensed under the GNU Affero General Public License version 3, or (at your option) any later version.  The sofware was designed and developed by Darren Creutz.

Currently only SQM-LE devices are supported.

## Reading the SQM

The sqm_read.php script reads an SQM-LE device:

```./read_sqm.php 192.168.1.100```

will output the msas reading of the SQM-LE device with IP address 192.168.1.100 (hostnames and IP addresses are allowed).  See the [sqm_read.php usage](sqm_read_usage.md) for options.

## Collecting data

The sqm_read_to_file.php script reads an SQM device and stores the reading in a data file formatted according to the international standard (it will create the file if it does not exist, complete with the required headers).  It can be run directly from the command line but is intended to be automated via crom or systemd.

## Installation

1. [Download sqm_read](https://github.com/dcreutz/SQM-Visualizer/releases/download/v0.1alpha/sqm_read.tar.gz).

2. Extract the .tar.gz file.

3. Edit config.php to enter information about your SQM device.  At the minimum, enter the hostname or IP address.

4. [Optional] Create a cron job to peridoically take readings.  Run the command

```crontab -e``` 

and add the following line

```
*/5 * * * * cd sqm_read; ./sqm_read_to_file.php >> log/sqm_read.log 2&>1
```
If you would prefer the readings be more or less frequent than every 5 minutes, change the 5 to the desired frewuency.  (Alternatively use /etc/cron.d or systemd).

5. [Optional] Edit config.php to customize your installation.  See the [configuration instructions](config.md) for options.

## sqm_read.php only

If you just want the command line program to read the device, download [sqm_read.php.gz](https://github.com/dcreutz/SQM-Visualizer/releases/download/v0.1alpha/sqm_read.php.gz).

There is no configuration required, simply (g)unzip it.