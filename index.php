<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK;

require('vendor/autoload.php');

// require(__DIR__ . '/demo/callRecording.php');

if (!$argv || !in_array('skipSMS', $argv)) {
	print "Test 4: sms.php\n";
    require(__DIR__ . '/demo/sms.php');
} else {
	print "Test 4: sms.php - skipping...\n";
}

if (!$argv || !in_array('skipRingOut', $argv)) {
	print "Test 5: ringout.php\n";
	print "Ring-Out Initiated\n";
    require(__DIR__ . '/demo/ringout.php');
} else {
	print "Test 5: ringout.php - skipping...\n";
}

require(__DIR__ . '/demo/callRecording_S3.php');

// sleep(30);

