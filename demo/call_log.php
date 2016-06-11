<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK\SDK;
date_default_timezone_set ('UTC');

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
        $timeTo = '00:59:59';

        // Array to push the call-logs to a file
        $callLogs = array();

        // $dateFrom = new DateTime($_ENV['RC_dateFrom']);

            // $dateTo = $_ENV['RC_dateTo'] . 'T' . $timeTo;
        // $dateTo = new DateTime($_ENV['RC_dateTo']);

        while($flag) {

            // Start Time
            $start = microtime(true);
            $dateFrom = $_ENV['RC_dateFrom'] . 'T' . $timeFrom;
            // $dateFrom = new DateTime($_ENV['RC_dateFrom']);

            $dateTo = $_ENV['RC_dateTo'] . 'T' . $timeTo;
            // $dateTo = new DateTime($_ENV['RC_dateTo']);
                
            $apiResponse = $platform->get('/account/~/extension/~/call-log', array(
            'dateFrom' => $dateFrom,
            'withRecording' => 'True',
            'dateTo' => $dateTo,
            'type' => 'Voice',
            'perPage' => 500,
            'page' => $pageCount
            ));

            // print "The api response is :" . $apiResponse . PHP_EOL;
            // print dateFrom and dateTo
            // print 'DateFrom : ' . $dateFrom->format(DateTime::ISO8601) . PHP_EOL;
            // print 'DateTo :' . $dateTo->format(DateTime::ISO8601) . PHP_EOL;
            // // ApiResponse as jsonArray()
            // $apiResponseJSONArray = $apiResponse -> jsonArray();

            $apiResponseArray = $apiResponse->json()->records;

            $apiResponse->json()->records;

            foreach ($apiResponseArray as $value) {
                // json_encode($callLogs, FILE_APPEND, JSON_PRETTY_PRINT)
                array_push($callLogs, $value);
                // file_put_contents("${callLogDir}/call_log_${'dir'}.json", json_encode($value), FILE_APPEND);
            } 

            // array_push($callLogs, $apiResponse->json()->records);   
            // Write the contents to .json file
            // file_put_contents("${callLogDir}/call_log_${'dir'}.json", json_encode($callLogs, FILE_APPEND, JSON_PRETTY_PRINT));

            $end=microtime(true);

            print 'Page ' . $pageCount . 'retreived with ' . $recordCountPerPage . 'records' . PHP_EOL;

            // Check if the recording completed wihtin 10 seconds.
                $time = ($end*1000 - $start*1000) / 1000;

            // Check if 'nextPage' exists
            if(isset($apiResponseJSONArray["navigation"]["nextPage"])) {  

                if($time < $timePerCallLogRequest) {
                    print 'Sleeping for :' . $timePerCallLogRequest - $time . PHP_EOL;
                    sleep($timePerCallLogRequest-$time);
                    sleep(5);

                    $pageCount = $pageCount + 1;
                }
            }
            
            else if($dateTo != $_ENV['RC_dateTo'] . 'T' . '23:59:59') {
            // set the flag equals false
                // print "the next day's date is :" . date('Y-m-d', strtotime($_ENV['RC_dateTo'] . ' +1 day')) . 'T' . '00:00:00+0000' . PHP_EOL;

                sleep(5);    
                $pageCount = 1;
                $timeFrom = $timeTo;
                print "The new Time From is :" . $timeFrom . PHP_EOL;
                $timestamp = strtotime($timeTo) + 60*60;
                $timeTo = date('H:i:s', $timestamp);
                print "The new Time To is :" . $timeTo . PHP_EOL;
            }
            
            else {

                    print_r ($callLogs);
                    file_put_contents("${callLogDir}/call_log_${'dir'}.json", json_encode($callLogs));
                    $flag = False;  
                    unset($callLogs);
            }
        }

} catch (HttpException $e) {

            $message = $e->getMessage();

            print 'Expected HTTP Error: ' . $message . PHP_EOL;

            $apiResponse = $e->apiResponse();
            print 'The Request is :' . PHP_EOL;
            print_r($apiResponse->request());
            print PHP_EOL; 
            print 'The Response is :' . PHP_EOL;
            print_r($apiResponse->response());
            print PHP_EOL; 
            
            // Another way to get message, but keep in mind, that there could be no response if request has failed completely
            print '  Message: ' . $e->apiResponse->response()->error() . PHP_EOL;

}
    