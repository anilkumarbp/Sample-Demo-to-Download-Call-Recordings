<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK\SDK;

echo "\n";
echo "------------ Initiate RingOut ----------------";
echo "\n";

try {


		// Constants
		$timePerRingOut = 6;

        // To parse the .env
        $dotenv = new Dotenv\Dotenv(getcwd());

        $dotenv->load();


		// Create SDK instance

		$rcsdk = new SDK($_ENV['RC_AppKey'], $_ENV['RC_AppSecret'], $_ENV['RC_Server'], 'Demo', '1.0.0');

		$platform = $rcsdk->platform();

		// Authorize

		$platform->login($_ENV['RC_Username'], $_ENV['RC_Extension'], $_ENV['RC_Password']);

		print 'Generating Call Recordings: ' . PHP_EOL;

		$count = $_ENV['RC_callRecordingsCount'];
		
		for($i = 1; $i <= $count; $i++) {

			print 'Generating Call Recordings:' . $i . PHP_EOL;
        	$start = microtime(true);
        	print 'Start Time :' . $start . PHP_EOL;
			$response = $platform->post('/account/~/extension/~/ringout', array(
			    'from' => array('phoneNumber' => $_ENV['RC_fromPhoneNumber']),
			    'to'   => array('phoneNumber' => $_ENV['RC_toPhoneNumber'])
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
			sleep(2);

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
