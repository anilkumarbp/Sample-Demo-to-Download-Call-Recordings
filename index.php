<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK;


require('vendor/autoload.php');


// check if a config.ini file exists 
// $config = "";
// $skipCallLog = "";
// $skipRingOut = "";
// $skipDownloadS3 = "";
// $skipDownload = "";

// $filename = './config.json';

// // $skipCallLog = 

// if (file_exists($filename)) {
// 	$config = json_decode(file_get_contents($filename), true);
// 	$skipCallLog = $config['skipCallLog'];
// 	$skipRingOut = $config['skipRingOut'];
// 	$skipDownload = $config['skipDownload'];
// 	$skipDownloadS3 = $config['skipDownloadS3'];
// 	$skipDownloadDropbox = $config['skipDownloadDropbox'];

//To parse the .env file
$dotenv = new Dotenv\Dotenv(__DIR__);

$dotenv->load();

// Retreive .env variables
$skipCallLog = $_ENV['RC_SkipCallLog'];
$skipRingOut = $_ENV['RC_SkipRingOut'];
$skipDownload = $_ENV['RC_SkipDownload'];
$skipDownloadS3 = $_ENV['RC_SkipDownloadS3'];
$skipDownloadDropbox = $_ENV['RC_SkipDownloadDropbox'];


	// To authenticate
	require(__DIR__ . '/demo/authData.php');


	// Call-Logs
	if ($skipCallLog!="True" || $skipCallLog=="") {
		print "Test 1: call_log.php\n";
	    require(__DIR__ . '/demo/call_log.php');
	} else {
		print "Test 2: call_log.php - skipping...\n";
	}

	// Ring-out
	if ($skipRingOut!="True" || $skipRingOut=="") {
		print "Test 2: ringout.php\n";
		print "Ring-Out Initiated\n";
	    require(__DIR__ . '/demo/ringout.php');
	} else {
		print "Test 3: ringout.php - skipping...\n";
	}

	// Recordings-Download
	if ($skipDownload!="True" || $skipDownload=="") {
		print "Test 3: callRecording.php\n";
		print "Downloading Recordings\n";
	    require(__DIR__ . '/demo/callRecording.php');
	} else {
		print "Test 3: callRecording.php - skipping...\n";
	}

	// Recordings-Download-S3
	if ($skipDownloadS3!="True" || $skipDownloadS3=="") {
		print "Test 4: callRecording_S3.php\n";
		print "Downloading Recordings to S3\n";
	    require(__DIR__ . '/demo/callRecording_S3.php');
	} else {
		print "Test 4: callRecording_S3.php - skipping...\n";
	}

	// Recordings-Download-DropBox
	if ($skipDownloadDropbox!="True" || $skipDownloadDropbox=="") {
		print "Test 4: callRecording_S3.php\n";
		print "Downloading Recordings to Dropbox\n";
	    require(__DIR__ . '/demo/callRecording_Dropbox.php');
	} else {
		print "Test 4: callRecording_Dropbox.php - skipping...\n";
	}

// }

// else {

// 	print "Kindly include a config.json in the folder\n";
// }
// // require(__DIR__ . '/demo/call_log.php');

// require(__DIR__ . '/demo/ringout.php');

// require(__DIR__ . '/demo/callRecording.php');

// require(__DIR__ . '/demo/callRecording_S3.php');

// require(__DIR__ . '/demo/callRecording_Dropbox.php');

