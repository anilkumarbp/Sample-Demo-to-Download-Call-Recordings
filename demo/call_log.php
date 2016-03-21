<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK\SDK;

echo "\n";
echo "------------Get Call Logs----------------";
echo "\n";


try {

      
        $credentials_file = count($argv) > 1 
        ? $argv[1] : __DIR__ . '/../config.json';


        

        $credentials = json_decode(file_get_contents($credentials_file), true);

        // constants
            // Count of Pages
            $pageCount = 1;
            $recordCountPerPage = 100;
            $timePerCallLogRequest = 10;
            $flag = True;

        // Create SDK instance

        $rcsdk = new SDK($credentials['appKey'], $credentials['appSecret'], $credentials['server'], 'Demo', '1.0.0');

        $platform = $rcsdk->platform();

        // Authorize

        $platform->login($credentials['username'], $credentials['extension'], $credentials['password'], true);
    
        // Writing the call-log response to json file
        $dir = $credentials['dateFrom'];
        $callLogDir = __DIR__ . '/../Call-Logs/' . $dir;

        //Create the Directory
        if (!file_exists($callLogDir)) {
            mkdir($callLogDir, 0777, true);
          }

        // dateFrom and dateTo paramteres
        $timeFrom = '00:00:00';
        $timeTo = '00:29:59';

        while($flag) {

            // Start Time
            $start = microtime(true);
                
            $apiResponse = $platform->get('/account/~/extension/~/call-log', array(
            'dateFrom' => $credentials['dateFrom'],
            'dateTo' => $credentials['dateFrom'],
            'perPage' => 300
            ));

            // ApiResponse as jsonArray()
            $apiResponseJSONArray = $apiResponse -> jsonArray();

            // Write the contents to .json file
            file_put_contents("phar://RC_CallRecordings_Download.phar/${callLogDir}/call_log_${'dir'}.json", $apiResponse->text());

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
    