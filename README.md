# RingCentral-Call-Generator-Recordings-Downloader
Sample PHP command line application to : Generate Call Records ( with / without Recordings ) , download call logs , upload recordings to Amazon S3 Bucket.

![APP screenshots](docs/Recordings.png) 
 

# Requirements

- PHP 5.3.29+

# Installation

## PHAR with bundled dependencies

1. Download [PHAR file](https://github.com/anilkumarbp/RingCentral-Call-Generator-Recordings-Downloader/releases/tag/0.1.0)

```bash
$ wget https://github.com/anilkumarbp/RingCentral-Call-Generator-Recordings-Downloader/releases/download/0.1.0/RC_CallRecordings_Download.phar
```

2. Create a `config.json` file with the contents as shown below with your details:


```php
{
	"appKey": "", 			 	// app key       
	"appSecret": "",			// app secret
	"server": "",             		// sandbox :  https://platform.devtest.ringcentral.com  production : https://platform.ringcentral.com
	"username": "",				// username
	"extension": "",			// extension
	"password": "",				// password
	"fromPhoneNumber": "",			// Announcements only extension
	"toPhoneNumber": "",			// Announcements only extension
	"dateFrom": "YYYY-MM-DD",		// dateFrom {single Day}
	"callRecordingsCount": "",  		// No of call recordings generator 
	"amazonAccessKey": "",			// Amazon Access Key
	"amazonSecretKey": "",			// Amazon Secret Key
	"skipCallLog": "",			// True to skip call-log
	"skipRingOut": "",			// True to skip Ring-Out
	"skipDownload": "",			// True to skip Download
	"skipDownloadS3": ""			// True to skip Upload to S3
}
```

3. CD into the terminal and type:

```php
$ php RC_CallRecordings_Download.phar
```

## Clone the Repo

1. Clone the repo.
2. Open a terminal and cd into the repo
3. Make sure to add the appKey and appSecret and the extension credentials and dateFrom within the Configuration file ( credentials.json )
   The file could be found within the demo folder ( ./demo/_credentials.json )


# Basic Usage

## Generate Call Recordings

```php
require(__DIR__ . '/demo/ringout.php');
```

Aim : To generate sample call recordings you would need to use the ringout.php file. 
Pre-requisite : Before you initiate a RingOut, make sure to add a "Announcement-Only-Extension" and associate a digital line / direct line attached to it.
                Make sure to use this number as both the "fromPhoneNumber" and "toPhoneNumber" within the configuration file ( /demo/_credentials.json )
                
## Pull Down Call-Logs

```php
require(__DIR__ . '/demo/call_log.php');
```
Aim : To pull down call-logs in cycles of one business day ( 24 hours ) and save them as .json file
Pre-requisite : Before you initiate the call_log.php make sure to pass the "dateFrom" filter in the configuration file ( ./demo/_credentials.json )
Note : The call-log is designed to fetch 100 records per page.

## Save the call-recordings to Local File System

```php
require(__DIR__ . '/demo/callRecording.php');
```
Aim : Save the call-recordings to your local file system using file stream writer. Creates a directory called "Recordings" and the recordings are stored as .mp3 / .wav format.
Pre-requisite : Before you initiate the callRecording.php make sure to pass the "dateFrom" filter in the configuration file ( ./demo/_credentials.json )

## Save the call-recordings to Amazon S3 Bucket

```php
require(__DIR__ . '/demo/callRecording_S3.php');
```
Aim : Save the call-recordings to your Amazon S3 Buckets using amazon stream writer. Creates a directory called "Recordings" and the recordings are stored as .mp3 / .wav format.
Pre-requisite : Before you initiate the callRecording.php make sure to pass the "dateFrom" filter in the configuration file ( ./demo/_credentials.json )
                Make sure to pass in the "amazonAccessKey" and "amazonSecretKey"

## Save the call-recordings to DropBox

```php
require(__DIR__ . '/demo/callRecording_Dropbox.php');
```
Aim : Save the call-recordings to your Dropbox Application. Creates a directory called "Recordings" and the recordings are stored as .mp3 / .wav format.
Pre-requisite : Before you initiate the callRecording_Dropbox.php make sure to pass the "key" and "secret" within the configuration file ( ./demo/_credentialsSample.json )



# How to demo?

Clone the repo and update the file `demo/_credentials.json`:

```php
{
	"appKey": "", 			 	// app key       
	"appSecret": "",			// app secret
	"server": "",             		// sandbox :  https://platform.devtest.ringcentral.com  production : https://platform.ringcentral.com
	"username": "",				// username
	"extension": "",			// extension
	"password": "",				// password
	"fromPhoneNumber": "",			// Announcements only extension
	"toPhoneNumber": "",			// Announcements only extension
	"dateFrom": "xxxx-xx-xx",		// dateFrom {single Day}
	"callRecordingsCount": "",  		// No of call recordings generator 
	"amazonAccessKey": "",			// Amazon Access Key
	"amazonSecretKey": "",			// Amazon Secret Key
	"skipCallLog": "",			// True to skip call-log
	"skipRingOut": "",			// True to skip Ring-Out
	"skipDownload": "",			// True to skip Download
	"skipDownloadS3": ""			// True to skip Upload to S3
}
```

Then execute:

```sh
$ php index.php ./demo/_credentials.json *
```
* skipRingOut - to skip initiating RingOut
* skipCallLog - to skip downloading of call-logs
* skipDownloadS3 - to skip uploading the recordings to S3 Bucket

Please take a look in `demo` folder to see all the demo scripts.

