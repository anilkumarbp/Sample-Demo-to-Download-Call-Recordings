<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK;

require('vendor/autoload.php');



if (!$argv || !in_array('skipSMS', $argv)) {
	print "Test 1: sms.php\n";
    require(__DIR__ . '/demo/sms.php');
} else {
	print "Test 1: sms.php - skipping...\n";
}

if (!$argv || !in_array('skipCallLog', $argv)) {
	print "Test 2: call_log.php\n";
    require(__DIR__ . '/demo/call_log.php');
} else {
	print "Test 2: call_log.php - skipping...\n";
}

if (!$argv || !in_array('skipRingOut', $argv)) {
	print "Test 3: ringout.php\n";
	print "Ring-Out Initiated\n";
    require(__DIR__ . '/demo/ringout.php');
} else {
	print "Test 3: ringout.php - skipping...\n";
}

if (!$argv || !in_array('skipDownloadS3', $argv)) {
	print "Test 4: callRecording_S3.php\n";
	print "Ring-Out Initiated\n";
    require(__DIR__ . '/demo/callRecording_S3.php.php');
} else {
	print "Test 4: callRecording_S3.php.php - skipping...\n";
}



