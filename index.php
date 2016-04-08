<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK;


require('vendor/autoload.php');



$timestamp = strtotime('00:00:00') + 60*60-1;

// echo $timestamp;

$time = date('H:i:s', $timestamp);

$time1 = $time;

// $result = $time->format('H:i:s');

echo $time1;

// echo $time;//11:09

//To parse the .env file
// $dotenv = new Dotenv\Dotenv(getcwd());

// $dotenv->load();

// // Retreive .env variables
// $skipCallLog = $_ENV['RC_SkipCallLog'];
// $skipRingOut = $_ENV['RC_SkipRingOut'];
// $skipDownload = $_ENV['RC_SkipDownload'];
// $skipDownloadS3 = $_ENV['RC_SkipDownloadS3'];
// $skipDownloadDropbox = $_ENV['RC_SkipDownloadDropbox'];


// 	// To authenticate
// 	require(__DIR__ . '/demo/authData.php');


// 	// Call-Logs
// 	if ($skipCallLog!="True" || $skipCallLog=="") {
// 		print "Test 1: call_log.php\n";
// 	    require(__DIR__ . '/demo/call_log.php');
// 	} else {
// 		print "Test 2: call_log.php - skipping...\n";
// 	}

// 	// Ring-out
// 	if ($skipRingOut!="True" || $skipRingOut=="") {
// 		print "Test 2: ringout.php\n";
// 		print "Ring-Out Initiated\n";
// 	    require(__DIR__ . '/demo/ringout.php');
// 	} else {
// 		print "Test 3: ringout.php - skipping...\n";
// 	}

// 	// Recordings-Download
// 	if ($skipDownload!="True" || $skipDownload=="") {
// 		print "Test 3: callRecording.php\n";
// 		print "Downloading Recordings\n";
// 	    require(__DIR__ . '/demo/callRecording.php');
// 	} else {
// 		print "Test 3: callRecording.php - skipping...\n";
// 	}

// 	// Recordings-Download-S3
// 	if ($skipDownloadS3!="True" || $skipDownloadS3=="") {
// 		print "Test 4: callRecording_S3.php\n";
// 		print "Downloading Recordings to S3\n";
// 	    require(__DIR__ . '/demo/callRecording_S3.php');
// 	} else {
// 		print "Test 4: callRecording_S3.php - skipping...\n";
// 	}

// 	// Recordings-Download-DropBox
// 	if ($skipDownloadDropbox!="True" || $skipDownloadDropbox=="") {
// 		print "Test 4: callRecording_S3.php\n";
// 		print "Downloading Recordings to Dropbox\n";
// 	    require(__DIR__ . '/demo/callRecording_Dropbox.php');
// 	} else {
// 		print "Test 4: callRecording_Dropbox.php - skipping...\n";
// 	}
