<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK\SDK;

echo "\n";
echo "------------Get Call Logs----------------";
echo "\n";


try {

        // To parse the .env
        $dotenv = new Dotenv\Dotenv(getcwd());

        $dotenv->load();

        // constants
            // Count of Pages
            $pageCount = 1;
            $recordCountPerPage = 100;
            $timePerCallLogRequest = 10;
            $flag = True;

        // Create SDK instance

        $rcsdk = new SDK($_ENV['RC_AppKey'], $_ENV['RC_AppSecret'], $_ENV['RC_Server'], 'Demo', '1.0.0');

        $platform = $rcsdk->platform();

        // Authorize

        $platform->login($_ENV['RC_Username'], $_ENV['RC_Extension'], $_ENV['RC_Password'], true);
    
        // Writing the call-log response to json file
        $dir = $_ENV['RC_dateFrom'];
        $callLogDir = getcwd() . '/Call-Logs/' . $dir;

        //Create the Directory
        if (!file_exists($callLogDir)) {
            mkdir($callLogDir, 0777, true);
          }

        // dateFrom and dateTo paramteres
        $timeFrom = '00:00:00';
        $timeTo = '23:59:59';

        while($flag) {

            // Start Time
            $start = microtime(true);
                
            $apiResponse = $platform->get('/account/~/extension/~/call-log', array(
            'dateFrom' => $_ENV['RC_dateFrom'] . 'T' . $timeFrom,
            'dateTo' => $_ENV['RC_dateTo'] . 'T' . $timeTo,
            'perPage' => 300
            ));

            // ApiResponse as jsonArray()
            $apiResponseJSONArray = $apiResponse -> jsonArray();

            // Write the contents to .json file
            file_put_contents("${callLogDir}/call_log_${'dir'}.json", $apiResponse->text());

            $end=microtime(true);

            print 'Page ' . $pageCount . 'retreived with ' . $recordCountPerPage . 'records' . PHP_EOL;

            // Check if the recording completed wihtin 10 seconds.
                $time = ($end*1000 - $start*1000) / 1000;

            // Check if 'nextPage' exists
            if(isset($apiResponseJSONArray["navigation"]["nextPage"])) {  

                if($time < $timePerCallLogRequest) {
                    print 'Sleeping for :' . $timePerCallLogRequest - $time . PHP_EOL;
                    sleep($timePerCallLogRequest-$time);

                    $pageCount = $pageCount + 1;
                }
            }
            else {
                // set the flag equals false
                  $flag = False;
                
            }
    }

} catch (HttpException $e) {

        $message = $e->getMessage() . ' (from backend) at URL ' . $e->apiResponse()->request()->getUri()->__toString();

        print 'Expected HTTP Error: ' . $message . PHP_EOL;

}
    