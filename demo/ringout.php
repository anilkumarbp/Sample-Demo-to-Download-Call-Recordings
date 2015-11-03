<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK\SDK;

require_once(__DIR__ . '/_bootstrap.php');

// Constants
$timePerRingOut = 60

$credentials_file = count($argv) > 1 
  ? $argv[1] : __DIR__ . '/demo/_credentials.json';

$credentials = json_decode(file_get_contents($credentials_file), true);

// Create SDK instance

$rcsdk = new SDK($credentials['appKey'], $credentials['appSecret'], $credentials['server'], 'Demo', '1.0.0');

$platform = $rcsdk->platform();

// Authorize

$platform->login($credentials['username'], $credentials['extension'], $credentials['password']);

// Make a call
try {
		print 'Generating Call Recordings: ' . PHP_EOL;

		$count = $credentials['callRecordingsCount'];
		
		for($i = 1; $i <= $count; $i++) {

			print 'Generating Call Recordings:' . $i . PHP_EOL;
        	$start = microtime(true);
        	print 'Start Time :' . $start . PHP_EOL;
			$response = $platform->post('/account/~/extension/~/ringout', array(
			    'from' => array('phoneNumber' => $credentials['fromPhoneNumber']),
			    'to'   => array('phoneNumber' => $credentials['toPhoneNumber'])
			));

			$json = $response->json();

			$lastStatus = $json->status->callStatus;

			// Poll for call status updates

			while ($lastStatus == 'InProgress') {

			    $current = $platform->get($json->uri);
			    $currentJson = $current->json();
			    $lastStatus = $currentJson->status->callStatus;
			    print 'Status: ' . json_encode($currentJson->status) . PHP_EOL;

			    sleep(2);

			}

			// call announcement queue is active for 45 sec's
			sleep(45);

			print 'Call Recording:${i} generated.' . PHP_EOL;

	        $end = microtime(true);
	        print 'End Time :' . $end . PHP_EOL;
	        $time = ($end*1000 - $start * 1000) / 1000;
	        print 'Recording completed in :' . $time . PHP_EOL;
	        if($time < $timePerRingOut){
	            $sleepTime = round($timePerRingOut - $time,0);
	            print 'Sleeping for :' . $sleepTime . PHP_EOL;
	            sleep($sleepTime);
	        }

		}

		print 'Total call recordings completed: ' . $count . PHP_EOL;


	} catch (HttpException $e) {

        $message = $e->getMessage() . ' (from backend) at URL ' . $e->apiResponse()->request()->getUri()->__toString();

        print 'Expected HTTP Error: ' . $message . PHP_EOL;

}
